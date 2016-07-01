<?php 
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_huanlegu.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = false;
}

assign_template();

$action = isset($_REQUEST['action']) ? addslashes_deep($_REQUEST['action']) : '' ;
$smarty->assign( 'action', $action);

if ($action == 'saveOrder')
{
    
    $scenicId   = isset($_REQUEST['scenicId']) ? addslashes($_REQUEST['scenicId']) : null ;
    $date = isset($_REQUEST['date']) ? addslashes($_REQUEST['date']) : null ;
    $goodsId  = isset($_REQUEST['goodsId']) ? addslashes($_REQUEST['goodsId']) : null ;    
    $number  = isset($_REQUEST['number']) ? addslashes($_REQUEST['number']) : null ;
    
    $secret     = isset($_REQUEST['secret']) ? $_REQUEST['secret'].md5( implode('', array($scenicId,$date,$goodsId))) : null ;    
    $checkArray = array(
        'scenic_id'     => $scenicId,
        'goods_id'      => $goodsId,
        'date'          => $date,
        'number'        => $number
    );
    
    // 数据验证
    $checkResult = luyouEmpty($checkArray);
    
    if ($checkResult['state'] == 1)
    {
        show_wap_message( $checkResult['message'] );
    }
    
    // 产品信息
    $good = array();
    $sceneryGoods = sceneryGoods($scenicId);
    if (count($sceneryGoods) == 1)
    {
        $good = $sceneryGoods[0];
    }
    else {
        foreach ($sceneryGoods as $goods)
        {
            if ($goods['GoodsId'] == $goodsId)
            {
                $good = $goods;
            }
        }
    }
    
    if (empty($good))
    {
        show_wap_message('商品不可购买！');
    }
    
    // 当前下单的时间，是否超过了最晚下单时间
    if (!empty($good['CurrentDayLastBuyTime']))
    {
        $lastBuyTime = $good['CurrentDayLastBuyTime'];
        $currentTime = (local_date('H') * 60) + local_date('i');
        if ($lastBuyTime < $currentTime)
        {
            show_wap_message('今天不能购买了，请明天再来订购');
        }        
    }
    
    // 最大购买的商品数量判断
    if ($number > $good['MaxQuantityEachOrder'])
    {
        show_wap_message('您最多可以购买 '.$good['MaxQuantityEachOrder'].' 张！');
    }
   
    // 商品有效期提示信息
    $timeMesg = '';
    
    // 有效期判断
    switch ($good['ValidityType'])
    {
        //  指定出行当天有效
        case 1:
           
            $strtotime = local_strtotime($date);

            // 出行日期判断
            $selectTime = array();
            $optionalDay = json_decode($good['GoodsTimeInfo']['OptionalDay'], true);
            $selectTime = searchData($optionalDay, $strtotime); 
            if ( !in_array(date('z',$strtotime), $selectTime) )
            {
                show_wap_message('您选择的日期无效，从新选择一个吧！');
            }
            $timeMesg = '商品仅限于 '.$date.' 当天使用！';
            break;
        // 固定有效期
        case 2:
            /* $timeMesg = '有效期为 ';
            if ($good['GoodsTimeInfo']['ValidityStart'] && $good['GoodsTimeInfo']['ValidityEnd'])
            {
                $timeMesg .= $good['GoodsTimeInfo']['ValidityStart'].' - '.$good['GoodsTimeInfo']['ValidityEnd'];
            }
            else {
                $timeMesg .= '不限';
            }
            
            $timeMesg .=''; */
        // 购买后生效
        case 3:
            
            break;
    }
    $createData = array('goods'=>$good, 'order'=>$checkArray);      
    
    $orderId = hasOnly( $secret );    
    if ($orderId == false)
    {
        // 创建订单
        $default = array(
            'is_pay'    => 0,
            'state'     => 0,
            'add_time'  => local_gettime(),
            'number'    => $createData['order']['number'],
            'name'      => $createData['goods']['GoodsName'],
            'link_man'  => '',
            'link_phone'=> '',
            'date'      => $createData['order']['date'],
            'order_sn'  => get_order_sn(),
            'username'  => $_SESSION['user_name'],
            'user_id'   => $_SESSION['user_id'],
            
            'money'     => $createData['goods']['showPrice'] * $number,
            'unit_price'=> $createData['goods']['showPrice'],
            'goods_id'  => $createData['order']['goods_id'],
            'secret'    => $secret,
            'pay_type'  => $createData['goods']['PayType'],
            'scenic_id' => $createData['goods']['ScenicId'],
            'source'    => 1
        );
            
        $cols = array_keys($default);
        $GLOBALS['db']->query(' INSERT INTO '.$GLOBALS['ecs']->table('huanlegu_order')." ( ".implode(',', $cols)." ) VALUES ('".implode("','", $default)."')");
        $orderId = $GLOBALS['db']->insert_id();
        
        $orders = findData('huanlegu_order', 'id='.$orderId);
        $order = $orders[0];
    }
    else 
    {
        $orders = findData('huanlegu_order', 'id='.$orderId);
        $order = $orders[0];
    }
    
    $smarty->assign( 'order', $order);
    
    $smarty->assign('header', get_header('欢乐谷预订',true,true));
    
}

else if($action == 'pay')
{
    $ajaxMessge = array('error'=>0, 'message'=>'');
    $password = isset($_REQUEST['password']) ? intval($_REQUEST['password']) : 0 ;
    $orderId = isset($_REQUEST['orderid']) ? intval($_REQUEST['orderid']) : 0;
    $link_man = isset($_REQUEST['link_man']) ? addslashes($_REQUEST['link_man']) : 0;
    $link_zhengjian = isset($_REQUEST['link_zhengjian']) ? addslashes($_REQUEST['link_zhengjian']) : 0;
    $link_phone = isset($_REQUEST['link_phone']) ? addslashes($_REQUEST['link_phone']) : 0;
     
    if ( empty($password) || empty($orderId) || empty($link_man) || empty($link_phone) || empty($link_zhengjian))
    {
        $ajaxMessge['error'] = 1;
        $ajaxMessge['message'] = '参数不全！';
        exit(json_encode($ajaxMessge));
    }
    
    $arr_param = array(	'CardInfo'=>array('CardNo'  => $_SESSION['username'], 'CardPwd' => $password) );
	if ($cardPay->action($arr_param, 8) == 1)
	{
	    $ajaxMessge['error'] = 1;
	    $ajaxMessge['message'] = '密码错误！';
	    exit(json_encode($ajaxMessge));
	}
    
	$orders = findData('huanlegu_order', 'id='.$orderId);
	$order = $orders[0];
	
    // 是否已经支付的判断
    if ($order['is_pay'] == 1 && $order['state'] == 1)
    {
        $ajaxMessge['error'] = 1;
        $ajaxMessge['message'] = '已经支付过了！';
        exit(json_encode($ajaxMessge));
    }    
/*     $ajaxMessge['error'] = 1;
        $ajaxMessge['message'] = $order['id'];
        exit(json_encode($ajaxMessge));
         */
    // 支付操作 
    /** TODO 支付 （双卡版） */
    $param = array(
        'CardInfo' => array( 'CardNo'=> $_SESSION['user_name'], 'CardPwd' => $password),
        'TransationInfo' => array( 'TransRequestPoints'=>$order['money'], 'TransSupplier'=>setCharset('欢乐谷'))
    );
    
    if ( $order['is_pay'] == 1)
    {
        $state = 0;
        $cardResult = $order['api_card_id'];
    }
    else
    {
        $state = $cardPay->action($param, 1, $order['order_sn']);
        if ($state == 0){
            $cardResult = $cardPay->getResult();
            $_SESSION['BalanceCash'] -= $order['money']; //重新计算用户卡余额
            $GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('users')." SET card_money = card_money - (".$order['money'].") WHERE user_id = '".intval($_SESSION['user_id'])."'");
            // 更新订单的支付状态
            update( 'is_pay=1, pay_time="'.local_gettime().'",api_card_id="'.$cardResult.'"', 'id='.$orderId, 'huanlegu_order');
        }
    }        
    
    if ( $state == 0)
    {
        $OutOrderId = $order['order_sn'];
        $param = array( 
            'OutOrderId'=> $OutOrderId, 
            'Name'      => $link_man, 
            'Quantity'  => $order['number'],
            'Mobile'    => $link_phone,
            'GoodsId'   => $order['goods_id'],
            'SalePrice' => $order['unit_price'],
            'SmsSendMode'=> 3,
            'CertificateType'=>'1',
            'CertificateNum' => $link_zhengjian,
            'AppointTripDate'=> $order['date']
        );
        $result = getHQCapi($param, 'confirm');
        
        if (is_array($result))
        {
            $vouchers = json_encode($result['Vouchers']);
            // 订单信息
            update( 'state=1, link_man="'.$link_man.'", link_zhengjian="'.$link_zhengjian.'", link_phone = "'.$link_phone.'", api_order_id="'.$result['OtmOrderId'].'", vouchers=\''.$vouchers.'\'', 'id='.$orderId, 'huanlegu_order');
        }
        else{
            $messages = explode('|', $result);
            $ajaxMessge['error'] = 1;
            $ajaxMessge['message'] = $messages[1];
            exit(json_encode($ajaxMessge));
        }
    }
    else {
        $ajaxMessge['error'] = 1;
        $ajaxMessge['message'] = $cardPay->getMessage();
        exit(json_encode($ajaxMessge));
    }
    
    exit(json_encode($ajaxMessge));
}

else if($action == 'respond') {
    
    $position = assign_ur_here(0,       '生活服务 <code>&gt;</code> 旅游产品');
    $smarty->assign('page_title',       '旅游产品_生活服务_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
}


// 参数完整性验证
function luyouEmpty( $params )
{
    $returnMessage = array( 'state'=>0, 'message'=>'');
    
    if ( empty($params['scenic_id']) || empty($params['date']) || empty($params['goods_id']) || empty($params['number']) )
    {
        $returnMessage['state'] = 1;
        $returnMessage['message'] = '非法请求';
    }
     
    return $returnMessage;
}

/* 检查数据是否存在 */
function hasOnly( $secret )
{
    $secrets = findData( 'huanlegu_order', 'state=0 AND user_id = "'.$_SESSION['user_id'].'" AND secret="'.$secret.'"' , 'id');
    if ($secrets[0]['id'])
        return $secrets[0]['id'];
    else
        return false;
}

$smarty->assign('action', $action);
$smarty->display('huanlegu_goods_order.html');
