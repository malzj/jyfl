<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_basic.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');

assign_template();

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

if($_REQUEST['step'] == 'confirm_order'){
    include_once('includes/cls_json.php');
    $_REQUEST['goods']=strip_tags(urldecode($_REQUEST['goods']));
    $_REQUEST['goods'] = json_str_iconv($_REQUEST['goods']);

    if (!empty($_REQUEST['goods_id']) && empty($_REQUEST['goods']))
    {
        if (!is_numeric($_REQUEST['goods_id']) || intval($_REQUEST['goods_id']) <= 0)
        {
            $jsonArray['state'] = 'false';
            $jsonArray['data']['go'] = -1;
            $jsonArray['message'] = '非法操作，请重新选择商品！';
        }
        JsonpEncode($jsonArray);
    }

    $json  = new JSON;

    if (empty($_REQUEST['goods']))
    {
        $jsonArray['state'] = 'false';
        $jsonArray['data']['go'] = -1;
        $jsonArray['message'] = '非法操作，请重新选择商品！';
        JsonpEncode($jsonArray);
    }

    $goods = $json->decode($_REQUEST['goods']);
    $specArray = get_show_specs($goods->goods_id);

    if (empty($specArray))
    {
        $jsonArray['state'] = 'false';
        $jsonArray['data']['go'] = -1;
        $jsonArray['message'] = '此商品暂时无法购买';
        JsonpEncode($jsonArray);
    }

    /* 检查：如果商品有规格，而post的数据没有规格，把商品的规格属性通过JSON传到前台 */
    if (empty($goods->spec) AND empty($goods->quick))
    {
        $sql = "SELECT a.attr_id, a.attr_name, a.attr_type, ".
            "g.goods_attr_id, g.attr_value, g.attr_price " .
            'FROM ' . $GLOBALS['ecs']->table('goods_attr') . ' AS g ' .
            'LEFT JOIN ' . $GLOBALS['ecs']->table('attribute') . ' AS a ON a.attr_id = g.attr_id ' .
            "WHERE a.attr_type != 0 AND g.goods_id = '" . $goods->goods_id . "' " .
            'ORDER BY a.sort_order, g.attr_price, g.goods_attr_id';

        $res = $GLOBALS['db']->getAll($sql);

        if (!empty($res))
        {
            $spe_arr = array();
            foreach ($res AS $row)
            {
                $spe_arr[$row['attr_id']]['attr_type'] = $row['attr_type'];
                $spe_arr[$row['attr_id']]['name']     = $row['attr_name'];
                $spe_arr[$row['attr_id']]['attr_id']     = $row['attr_id'];
                $spe_arr[$row['attr_id']]['values'][] = array(
                    'label'        => $row['attr_value'],
                    'price'        => $row['attr_price'],
                    'format_price' => price_format($row['attr_price'], false),
                    'id'           => $row['goods_attr_id']);
            }
            $i = 0;
            $spe_array = array();
            $goods->spec = array();
            foreach ($spe_arr AS $row)
            {
                $spe_array[]=$row;
                //直接默认第一个为选中
                $goods->spec[] = $row['values'][0]['id'];
            }
        }
    }

    /* 检查：商品数量是否合法 */
    if (!is_numeric($goods->number) || intval($goods->number) <= 0)
    {
        $jsonArray['state'] = 'false';
        $jsonArray['data']['go'] = -1;
        $jsonArray['message'] = $_LANG['invalid_number'];
        JsonpEncode($jsonArray);
    }else{
        $goods_info = check_goods($goods->goods_id,$goods->number, $goods->spec);
    }

    //判断商品码剩余量
    $goods_sql = "SELECT supplier_id FROM ".$GLOBALS['ecs'] -> table('goods')." WHERE goods_id =".$goods->goods_id;
    $supplier_id = $GLOBALS['db'] -> getOne($goods_sql);
    
    $code_sql = "SELECT COUNT(*) AS count FROM ".$GLOBALS['ecs'] -> table('code')." WHERE supplier_id=".$supplier_id." AND status = 0 AND price=".$goods_info['market_price']." ORDER BY id ASC";
    $code_count = $db -> getOne($code_sql);

    $code_sql = "SELECT id FROM ".$GLOBALS['ecs'] -> table('code')." WHERE supplier_id=".$supplier_id." AND status = 0 AND price=".$goods_info['market_price']." ORDER BY id ASC LIMIT ".$goods->number;
    $code_info = $db -> getAll($code_sql);
    $code_ids = array();
    foreach ($code_info as $code){
        $code_ids[] = $code['id'];
    }

    $str_code_id = implode(',',$code_ids);

    if($code_count<$goods->number){
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '商品数量不足！';
        JsonpEncode($jsonArray);
    }

    //订单初始化
    $order = array(
        'user_id'   =>  $_SESSION['user_id'],
        'user_name' =>  $_SESSION['user_name'],
        'code_id'   =>  $str_code_id,
        'price'     =>  $goods_info['market_price'],
        'sjprice'   =>  $goods_info['goods_price'],
        'pay_id'    =>  2,
        'send_msg'  =>  0,
        'goods_id'  =>  $goods_info['goods_id'],
        'goods_name'  =>  $goods_info['goods_name'],
        'goods_attr'=>  $goods_info['goods_attr'],
        'goods_attr_id'=>  $goods_info['goods_attr_id'],
        'goods_number'=>  $goods_info['goods_number'],
        'add_time'        => gmtime(),
        'order_status'    => 1,
        'supplier_id'    => $supplier_id,
    );

    /*商品总价*/
    $total['goods_price']  = $goods_info['goods_price'] * $goods_info['goods_number'];
    $order['goods_amount'] = $total['goods_price'];
    $order['discount']     = 0;

    $order['order_amount']  = number_format($total['goods_price'], 2, '.', '');
    /* 支付方式 */
    if ($order['pay_id'] > 0)
    {
        $payment = payment_info($order['pay_id']);
        $order['pay_name'] = addslashes($payment['pay_name']);
    }

//    获取商城售比、卡规则比例、浮比、单价比
    $customSpec = null;
    $specAttr = strpos($goods_info['goods_attr_id'], ',') !== false ? explode(',', $goods_info['goods_attr_id']) : array($goods_info['goods_attr_id']);
    foreach ($specAttr as $spec) {
        if (strpos($spec, 'S_') !== false)
        {
            $customSpec = substr($spec, 2);
        }
    }
    $ratios = get_spec_ratio_price( array('spec_nember'=>$customSpec, 'goods_id'=>$goods_info['goods_id']) , true);
    $order['shop_ratio'] = $ratios['shop_ratio'];
    $order['card_ratio'] = $ratios['card_ratio'];
    $order['unit_ratio'] = $ratios['unit_ratio'];
    $order['raise'] = $ratios['raise'];
    $order['ext'] = $ratios['ext'];
    /* 插入订单表 */
    $error_no = 0;
    do
    {
        $order['order_sn'] = get_order_sn(); //获取新订单号
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('code_order'), $order, 'INSERT');

        $error_no = $GLOBALS['db']->errno();

        if ($error_no > 0 && $error_no != 1062)
        {
            die($GLOBALS['db']->errorMsg());
        }
    }

    while ($error_no == 1062); //如果是订单号重复则重新提交数据

    $new_order_id = $db->insert_id();

    //修改商品码状态为已选
    foreach ($code_ids as $code_id) {
        $sql = "UPDATE " . $GLOBALS['ecs']->table('code') . " SET status = 1,order_sn=".$order['order_sn']." WHERE id=" . $code_id;
        $db->query($sql);
    }

    $jsonArray['data']['order_id'] = $new_order_id;
    $jsonArray['state'] = 'true';
    $jsonArray['message'] = '购买成功！';
    JsonpEncode($jsonArray);
}
// 支付页面
elseif ($_REQUEST['step'] == 'pay')
{
    $orderid = intval($_REQUEST['order_id']);
    if ($orderid < 1)
    {
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '没有该订单，请重新下单！';
        JsonpEncode($jsonArray);
    }

    $sql = "SELECT o.*,g.goods_name,g.goods_thumb FROM ".$GLOBALS['ecs']->table('code_order')."AS o LEFT JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON o.goods_id = g.goods_id WHERE o.id = ".$orderid;
    $orders = $db->getRow($sql);

    //过滤支付过的订单
    if($orders['order_status'] !=1){
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '订单已经支付过了，请选择其他商品吧！';
        JsonpEncode($jsonArray);
    }

    //支付倒计时
    $int_endPayTime = $orders['add_time'] + 15 * 60;
    if ($int_endPayTime < gmtime()){
        $db->query('UPDATE '.$ecs->table('code_order')." SET order_status=2 WHERE id = '$orderid'");
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '订单超时，请选择其他商品吧！';
        JsonpEncode($jsonArray);
    }
    //获取用户绑定手机号
    $mobile = $db -> getOne("SELECT mobile_phone FROM ".$ecs->table('users')." WHERE user_id = ".$_SESSION['user_id']);
    $jsonArray['state'] = 'true';
    $jsonArray['data']['endPayTime'] = local_date('M d, Y H:i:s',$int_endPayTime);
    $jsonArray['data']['orders'] = $orders;
    $jsonArray['data']['mobile_phone'] = $mobile;
    JsonpEncode($jsonArray);
}
//订单支付
elseif($_REQUEST['step'] == 'act_pay')
{
    $ajaxArray = array( 'error'=>0, 'message'=>'' );
    $orderid = intval($_REQUEST['order_id']);
    if($orderid < 1){
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '没有该订单，请重新下单！';
        JsonpEncode($jsonArray);
    }

    $mobile = $_REQUEST['mobile'];

    if(empty($mobile)){
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '请填写手机号码！';
        JsonpEncode($jsonArray);
    }
    if(!matchMobile($mobile)){
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '请填写正确的手机号码！';
        JsonpEncode($jsonArray);
    }
    $password = !empty($_REQUEST['password']) ? $_REQUEST['password'] : '';
    if(empty($_REQUEST['password'])){
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '请输入密码！';
        JsonpEncode($jsonArray);
    }
    //获取订单信息
    $order_sql = "SELECT * FROM ".$ecs->table('code_order')." WHERE id = ".$orderid;
    $order_info = $db -> getRow($order_sql);

    if (empty($order_info)){
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '抱歉，您提交的支付信息不存在';
        JsonpEncode($jsonArray);
    }
    // 检查订单是否在支付中，如果在支付中返回错误消息
    if ($order_info['card_pay'] > 0){
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '订单已经支付';
        JsonpEncode($jsonArray);
    }

    //获取电子码信息
    $code_sql = "SELECT c.*,s.supplier_name,m.content FROM ".$ecs->table('code')." AS c , ".$ecs->table('supplier')." AS s LEFT JOIN ".$ecs->table('supplier_message')." AS m ON s.supplier_id = m.supplier_id WHERE c.supplier_id = s.supplier_id AND c.id IN(".$order_info['code_id'].")";
    $code_info = $db->getAll($code_sql);

    //应支付点数
    $card_price = number_format(round($order_info['order_amount'],1),2,'.','');

    // 卡订单号
    $cardOrderId = local_date('ymdHis').mt_rand(1,1000);

    /** TODO 支付 （双卡版） */
    $arr_param = array(
        'CardInfo' => array( 'CardNo'=> $_SESSION['user_name'], 'CardPwd' => $password),
        'TransationInfo' => array( 'TransRequestPoints'=>$card_price, 'TransSupplier'=>setCharset('聚优电子码'))
    );
    $state = $cardPay->action($arr_param, 1);

    if ($state == 0){
        $cardResult = $cardPay->getResult();

        //更新订单加入手机号
        $sql = "UPDATE ".$ecs->table('code_order')." SET mobile = ".$mobile." WHERE id = ".$orderid;
        $db -> query($sql);

        $_SESSION['BalanceCash'] -= $card_price; //重新计算用户卡余额
        //更新卡金额
        $GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('users')." SET card_money = card_money - ('$card_price') WHERE user_id = '".intval($_SESSION['user_id'])."'");
        //更新卡支付状态,支付成功，更新订单状态
        $GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('code_order')." SET order_status = '3', pay_time = '".gmtime()."',money_paid = '".$card_price."', card_pay = '1', api_order_id = '".$cardResult."', card_order_id= '".$cardOrderId."' WHERE id = $orderid");
        //修改商品码状态为已付款
        $db->query("UPDATE ".$ecs->table('code')." SET status = 2 WHERE id IN (".$order_info['code_id'].")");

        //支付成功短信发放电子码
        $userInfo = $db -> getRow("SELECT nickname FROM ".$ecs->table('users')." WHERE user_name = ".$_SESSION['user_name']);
        $msgInfo = array(
            'nickname'=>$userInfo['nickname'],
            'mobile'=>$mobile
        );

        require(ROOT_PATH . 'includes/lib_smsvrerify.php');
        $Smsvrerify = new smsvrerifyApi();
        $error = 0;
        foreach($code_info as $code){
            $code['content'] = empty($code['content'])?'尊敬的聚优客户您好，您在我司官网订购的{$supplier_name}电子码券号：{$account}密码：{$password}请持电子码到合作的门店使用，谢谢！':$code['content'];
            $msgInfo = array_merge($msgInfo,$code);
            $smarty->assign($msgInfo);
            $message = $smarty->fetch("str:" . $code['content']);
            $result = $Smsvrerify->smsvrerify($msgInfo['mobile'],$message,'','聚优福利');
            if($result != 0)
                $error ++;
        }
        //修改商品码信息为已发送
        if($error == 0)
            $db->query("UPDATE ".$ecs->table('code_order')." SET send_msg = 1 WHERE id = ".$orderid);

        $jsonArray['state'] = 'true';
        $jsonArray['message'] = '支付成功';
        JsonpEncode($jsonArray);

    }else{
        $GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('code_order')." SET card_order_id= '0' WHERE id = '$orderid'");
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = $cardPay->getMessage();
        JsonpEncode($jsonArray);

    }
}
elseif($_REQUEST['step'] == 'respond'){
    $smarty->display('code/codeRespond.dwt');
}

//取消订单
elseif($_REQUEST['step'] == 'delorder')
{
    $orderid = $_REQUEST['order_id'];
    //获取已超时的订单
    $order = $db->getRow("SELECT * FROM ".$ecs->table('code_order')." WHERE id = '".$orderid."' AND add_time+".(15*60-10)."<unix_timestamp(now())");
    //取消未付款已超时的订单，解锁已锁定商品码
    if(!empty($order)) {
        $db->query("UPDATE " . $ecs->table('code_order') . "SET order_status = 2 WHERE id = " . $order['id']);
        $db->query("UPDATE " . $ecs->table('code') . "SET status = 0,order_sn='' WHERE id IN (" . $order['code_id'].")");
    }
    $jsonArray['state'] = 'true';
    $jsonArray['message'] = '订单已删除';
    JsonpEncode($jsonArray);
}

function check_goods($goods_id, $num = 1, $spec = array()){

    // 得到规格编号
    $spec_nember = '';
    foreach ($spec as $attr_k=>$attr_v)
    {
        if (strpos($attr_v, 'S_') !==false)
        {
            $spec_nember = substr($attr_v, 2);
        }
    }
    // 如果规格编号是空取第一个规格
    if (empty($spec_nember))
    {
        $spec_array = get_show_specs($goods_id);
        reset($spec_array);
        $firstSpece = current($spec_array);
        $spec_nember = $firstSpece['spec_nember'];
        $spec[] = "S_".$firstSpece['spec_nember'];
    }

    /* 取得商品信息 */
    $sql = "SELECT g.goods_name, g.goods_sn, g.is_on_sale, g.is_real, ".
        "g.market_price, g.shop_price AS org_price, g.promote_price, g.promote_start_date, ".
        "g.promote_end_date, g.goods_weight, g.integral, g.extension_code, ".
        "g.goods_number, g.is_alone_sale, g.is_shipping,".
        "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price ".
        " FROM " .$GLOBALS['ecs']->table('goods'). " AS g ".
        " LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
        "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
        " WHERE g.goods_id = '$goods_id'" .
        " AND g.is_delete = 0";
    $goods = $GLOBALS['db']->getRow($sql);

    if (empty($goods))
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['goods_not_exists'], ERR_NOT_EXISTS);

        return false;
    }

    /* 是否正在销售 */
    if ($goods['is_on_sale'] == 0)
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['not_on_sale'], ERR_NOT_ON_SALE);

        return false;
    }

    /* 不是配件时检查是否允许单独销售 */
    if ($goods['is_alone_sale'] == 0)
    {
        $GLOBALS['err']->add($GLOBALS['_LANG']['cannt_alone_sale'], ERR_CANNT_ALONE_SALE);

        return false;
    }

    /* 如果商品有规格则取规格商品信息 配件除外 */
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('products'). " WHERE goods_id = '$goods_id' LIMIT 0, 1";
    $prod = $GLOBALS['db']->getRow($sql);

    if (is_spec($spec) && !empty($prod))
    {
        $product_info = get_products_info($goods_id, $spec);
    }
    if (empty($product_info))
    {
        $product_info = array('product_number' => '', 'product_id' => 0);
    }

    /* 计算商品的促销价格 */
    //$spec_price             = spec_price($spec);
    // TODO 产品购买最总价格
    $spec_array 			= array('spec_nember'=> $spec_nember, 'goods_id'=>$goods_id);
    $spec_price2 			= get_spec_ratio_price($spec_array);
    $shop_price  			= get_final_price($goods_id, $num, true, $spec);
    $goods_price 			= $spec_price2+$shop_price;
    $goods_spec 			= get_goods_spec_info($spec_nember,$spec_price2);

    $goods_attrs            = get_goods_attr_info($spec);
    $goods_attr				= $goods_spec.$goods_attrs;

    // 规格价格
    $spec_money = $GLOBALS['db']->getOne("SELECT spec_price	FROM ".$GLOBALS['ecs']->table('goods_spec')." WHERE spec_nember='".$spec_nember."' AND goods_id = ".$goods_id);
    $goods['market_price'] = $spec_money;
    $goods_attr_id          = join(',', $spec);

    /* 初始化要插入购物车的基本件数据 */
    $parent = array(
        'user_id'       => $_SESSION['user_id'],
        'goods_id'      => $goods_id,
        'goods_sn'      => $spec_nember,//addslashes(getGoodsSn($goods_id)),
        'product_id'    => $product_info['product_id'],
        'goods_name'    => addslashes($goods['goods_name']),
        'market_price'  => $goods['market_price'],
        'goods_attr'    => addslashes($goods_attr),
        'goods_attr_id' => $goods_attr_id,
        'is_real'       => $goods['is_real'],
        'extension_code'=> $goods['extension_code'],
        'is_gift'       => 0,
        'is_shipping'   => $goods['is_shipping'],
    );

    $parent['goods_price']  = max($goods_price, 0);
    $parent['goods_number'] = $num;
    $parent['parent_id']    = 0;

    return $parent;
}

/**
 * 判断手机格式
 * @param $mobile
 * @return bool
 */
function matchMobile($mobile){
    $preg = "/^1[3,4,5,7,8]\d{9}$/";
    if(preg_match($preg,$mobile)){
        return true;
    }else{
        return false;
    }
}
