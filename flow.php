<?php

/**
 * ECSHOP 购物流程
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: douqinghua $
 * $Id: flow.php 17218 2011-01-24 04:10:41Z douqinghua $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_basic.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

if (!isset($_REQUEST['step']))
{
    $_REQUEST['step'] = "cart";
}

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

assign_template();
assign_dynamic('flow');
$position = assign_ur_here(0, $_LANG['shopping_flow']);
$smarty->assign('page_title',       $position['title']);    // 页面标题
$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

$smarty->assign('categories',       get_categories_tree()); // 分类树
$smarty->assign('helps',            get_shop_help());       // 网店帮助
$smarty->assign('lang',             $_LANG);
$smarty->assign('show_marketprice', $_CFG['show_marketprice']);
$smarty->assign('data_dir',    DATA_DIR);       // 数据目录

/*------------------------------------------------------ */
//-- 添加商品到购物车
/*------------------------------------------------------ */
if ($_REQUEST['step'] == 'add_to_cart')
{
    include_once('includes/cls_json.php');
    $_POST['goods']=strip_tags(urldecode($_POST['goods']));
    $_POST['goods'] = json_str_iconv($_POST['goods']);

    if (!empty($_REQUEST['goods_id']) && empty($_POST['goods']))
    {
        if (!is_numeric($_REQUEST['goods_id']) || intval($_REQUEST['goods_id']) <= 0)
        {
            ecs_header("Location:./\n");
        }
        $goods_id = intval($_REQUEST['goods_id']);
        exit;
    }

    $result = array('error' => 0, 'message' => '', 'content' => '', 'goods_id' => '');
    $json  = new JSON;

    if (empty($_POST['goods']))
    {
         $result['error'] = 1;
        die($json->encode($result));
    }

    $goods = $json->decode($_POST['goods']);
    $specArray = get_show_specs($goods->goods_id);
	if (empty($specArray))
	{
		$result['error'] = 1;
		$result['message'] = '此商品暂时无法购买';
		die($json->encode($result));
	}
	$result['carttype']  = $goods->carttype;

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

			//直接默认第一个为选中
            //$result['error']   = ERR_NEED_SELECT_ATTR;
            //$result['goods_id'] = $goods->goods_id;
            //$result['parent'] = $goods->parent;
            //$result['message'] = $spe_array;
            //die($json->encode($result));
        }
    }

    /* 更新：如果是一步购物，先清空购物车 */
    if ($goods->carttype == '5')
    {
        clear_cart(5);
        $_SESSION['flow_type'] = 5;
    }else{
        unset($_SESSION['flow_type']);
    }

    /* error_log(var_export($goods,true),"3",'error.log');
     exit; */
    /* 检查：商品数量是否合法 */
    if (!is_numeric($goods->number) || intval($goods->number) <= 0)
    {
        $result['error']   = 1;
        $result['message'] = $_LANG['invalid_number'];
    }
    /* 更新：购物车 */
    else
    {
        // 更新：添加到购物车
        if (addto_cart($goods->goods_id, $goods->number, $goods->spec, $goods->parent))
        {
            if ($_CFG['cart_confirm'] > 2)
            {
                $result['message'] = '';
            }
            else
            {
                $result['message'] = $_CFG['cart_confirm'] == 1 ? $_LANG['addto_cart_success_1'] : $_LANG['addto_cart_success_2'];
            }

            $result['content'] = insert_cart_info();
            $result['one_step_buy'] = $_CFG['one_step_buy'];
        }
        else
        {
            $result['message']  = $err->last_message();
            $result['error']    = $err->error_no;
            $result['goods_id'] = stripslashes($goods->goods_id);
            if (is_array($goods->spec))
            {
                $result['product_spec'] = implode(',', $goods->spec);
            }
            else
            {
                $result['product_spec'] = $goods->spec;
            }
        }
    }
	
    $result['confirm_type'] = !empty($_CFG['cart_confirm']) ? $_CFG['cart_confirm'] : 2;
    die($json->encode($result));
}
// 查看配送范围
elseif ($_REQUEST['step'] == 'showYunfei')
{
    $id = intval($_GET['id']);
    $yunfei = array();
    $map = findData('peisongmap',"gongyingshang_id=$id AND isTime = 1 AND cityid=".$_SESSION['cityid']);
    foreach ((array)$map as $key=>$val){
        $yunfei[$key] = array(
            'yunfei'=>$val['jiage'],
            'color' =>$val['yanse']
        );
    }
    $smarty->assign('yunfei', $yunfei);
    $smarty->assign('id', $id);
    exit($smarty->fetch('flow/row/yunfeiMap.dwt'));
}

// 供应商运费计算
elseif ($_REQUEST['step'] == 'yunfei')
{
    $returnAjax = array( 'error'=>0, 'message'=>'', 'html'=>'');  
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : null;
    
    // 供应商id为空，返回错误消息
    if ($id == null) 
    {
        $returnAjax = array(
            'error'=>1,
            'message'=>'供应商不存在！'
        );
        
        exit(json_encode($returnAjax));
    }
    
    include_once('includes/lib_transaction.php');   
    
    
    // 如果当前供应商单独设置了配送地址，就以单独设置的为准
    if ( isset($_SESSION['supplier']) && !empty($_SESSION['supplier'][$id]))
    {
        $consignee = $_SESSION['supplier'][$id];
        $consignee['country_cn']  = get_add_cn($consignee['country']);
        $consignee['province_cn'] = get_add_cn($consignee['province']);
        $consignee['city_cn']     = get_add_cn($consignee['city']);
        $consignee['district_cn'] = get_add_cn($consignee['district']);
    }
    else
    {
        $consignee = get_consignee($_SESSION['user_id']);
        if (!empty($consignee)){
            $consignee['country_cn']  = get_add_cn($consignee['country']);
            $consignee['province_cn'] = get_add_cn($consignee['province']);
            $consignee['city_cn']     = get_add_cn($consignee['city']);
            $consignee['district_cn'] = get_add_cn($consignee['district']);
        }
    }
    
    // 供应商信息
    $supplierDetail = findData('supplier', "supplier_id = $id");
    $detail = current($supplierDetail);
    
    $smarty->assign('detail',          $detail);
    $smarty->assign('consignee',       $consignee);
    $returnAjax['html'] = $smarty->fetch('flow/row/yunfeiAddress.dwt');
    exit(json_encode($returnAjax));    
}

// 提交订单之前，删除某个供应商的所有商品
elseif ($_REQUEST['step'] == 'drop_supplier_goods')
{    
    $supplier_id = intval($_GET['id']);

    $sql =  "SELECT c.rec_id, g.goods_id,g.supplier_id FROM ".$GLOBALS['ecs']->table('cart')." AS c ".
            "LEFT JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON g.goods_id = c.goods_id "." ".
            "WHERE c.session_id='" . SESS_ID . "'";
    $result = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($result))
    {
        if ($row['supplier_id'] == $supplier_id)
        {
            flow_drop_cart_goods($row['rec_id']);
        }
    }
    
    ecs_header("Location:flow.php?step=checkout");
    
}
// 添加新的收货地址（供应商单独指定）
elseif ($_REQUEST['step'] == 'newAddress')
{
    $smarty->assign('suppier_id',$_REQUEST['id']);
    $smarty->display('flow/row/newAddress.dwt');
}
elseif ($_REQUEST['step'] == 'checkout')
{
    /*------------------------------------------------------ */
    //-- 订单确认
    /*------------------------------------------------------ */
    unset($_SESSION['flow_type']);
    if ( isset($_REQUEST['flowtype']))
    {
        $_SESSION['flow_type'] = $_REQUEST['flowtype'];
    }
    
    /* 取得购物类型 */
    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

    /* 团购标志 */
    if ($flow_type == CART_GROUP_BUY_GOODS)
    {
        $smarty->assign('is_group_buy', 1);
    }
    /* 积分兑换商品 */
    elseif ($flow_type == CART_EXCHANGE_GOODS)
    {
        $smarty->assign('is_exchange_goods', 1);
    }
    else
    {
        //正常购物流程  清空其他购物流程情况
        $_SESSION['flow_order']['extension_code'] = '';
    }

    /* 检查购物车中是否有商品 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('cart') .
        " WHERE session_id = '" . SESS_ID . "' " .
        "AND parent_id = 0 AND is_gift = 0 AND rec_type = '$flow_type'";

    if ($db->getOne($sql) == 0)
    {
        show_message($_LANG['no_goods_in_cart'], '', '', 'warning');
    }

    if ( check_cart_price() === false)
    {
    	show_message('价格检查不通过，请删除购物车中价格小于等于0的商品', '', '', 'error');
    }
    /*
     * 检查用户是否已经登录
     * 如果用户已经登录了则检查是否有默认的收货地址
     * 如果没有登录则跳转到登录和注册页面
     */
    if (empty($_SESSION['direct_shopping']) && $_SESSION['user_id'] == 0)
    {
        /* 用户没有登录且没有选定匿名购物，转向到登录页面 */
        //ecs_header("Location: flow.php?step=login\n");
		ecs_header("Location: user.php\n");
        exit;
    }

    $consignee = get_consignee($_SESSION['user_id']);
	if (!empty($consignee)){
		$consignee['country_cn']  = get_add_cn($consignee['country']);
		$consignee['province_cn'] = get_add_cn($consignee['province']);
		$consignee['city_cn']     = get_add_cn($consignee['city']);
		$consignee['district_cn'] = get_add_cn($consignee['district']);
	}

    /* 检查收货人信息是否完整 */
    if (!check_consignee_info($consignee, $flow_type))
    {
		$smarty->assign('checkconsignee', 0);
    }else{
		$smarty->assign('checkconsignee', 1);
	}

    $_SESSION['flow_consignee'] = $consignee;
    $smarty->assign('consignee', $consignee);

    /* 对商品信息赋值 */
    $cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计
	//$smarty->assign('goods_list', $cart_goods);

    //分供货商显示商品
    $cart_goods_new = array();
	$is_21cake = 0;
	$arr_supplier = array();//购物车商品都有哪些供货商
    if(count($cart_goods)>0){
    	foreach($cart_goods as $key => $val){
			if ($val['supplier_id'] == 12){
				$is_21cake = 1;
			}
			$arr_supplier[$val['supplier_id']] = $val['supplier_id'];
    		$cart_goods_new[$val['supplier_id']][] = $val;
    	}
    }
            
    $smarty->assign('goods_list', $cart_goods_new);
	$smarty->assign('is_21cake', $is_21cake);
	$smarty->assign('arr_supplier', $arr_supplier);

    /* 对是否允许修改购物车赋值 */
    if ($flow_type != CART_GENERAL_GOODS || $_CFG['one_step_buy'] == '1')
    {
        $smarty->assign('allow_edit_cart', 0);
    }
    else
    {
        $smarty->assign('allow_edit_cart', 1);
    }

    /*
     * 取得购物流程设置
     */
    $smarty->assign('config', $_CFG);
    /*
     * 取得订单信息
     */
    $order = flow_order_info();
	
    $smarty->assign('order', $order);

    /* 计算折扣 */
    if ($flow_type != CART_EXCHANGE_GOODS && $flow_type != CART_GROUP_BUY_GOODS)
    {
        $discount = compute_discount();
        $smarty->assign('discount', $discount['discount']);
        $favour_name = empty($discount['name']) ? '' : join(',', $discount['name']);
        $smarty->assign('your_discount', sprintf($_LANG['your_discount'], $favour_name, price_format($discount['discount'])));
    }

	/* 如果使用红包，取得用户可以使用的红包及用户选择的红包 */
	if ((!isset($_CFG['use_bonus']) || $_CFG['use_bonus'] == '1')
		&& ($flow_type != CART_GROUP_BUY_GOODS && $flow_type != CART_EXCHANGE_GOODS)){
		// 取得用户可用红包
		$user_bonus = user_bonus($_SESSION['user_id'], $total['goods_price']);
		//获取供货商红包
		$arr_bonusSupplier = $user_bonus['supplier'];
		$arr_bonusSuppid   = array_keys($arr_bonusSupplier);//获取红包供货商id
		$arr_bonusShop      = array();//本网站红包
	
		foreach ($arr_bonusSuppid as $var){
			if (!empty($var) && !in_array($var, $arr_supplier)){
				unset($arr_bonusSupplier[$var]);
			}
		}
		$arr_bonusShop = array_shift($arr_bonusSupplier);
		if (!empty($arr_bonusShop)){
			foreach ($arr_bonusShop['bonus'] as $key=>$var){
				$var['enddate']              = local_date('Y-m-d H:i:s', $var['endtime']);
				$var['bonus_money_formated'] = price_format($var['type_money'], true);
				$arr_bonusShop['bonus'][$key] = $var;
			}
			$smarty->assign('bonusShop', $arr_bonusShop['bonus']);
		}
		if (!empty($arr_bonusSupplier)){
			foreach ($arr_bonusSupplier as $key=>$var){
				foreach ($var['bonus'] as $k=>$v){
					$v['enddate']              = local_date('Y-m-d H:i:s', $v['endtime']);
					$v['bonus_money_formated'] = price_format($v['type_money'], true);
					$var['bonus'][$k] = $v;
				}
				$arr_bonusSupplier[$key] = $var;
			}
			$smarty->assign('bonusSupplier', $arr_bonusSupplier);
		}

		// 能使用红包
		$smarty->assign('allow_use_bonus', 1);
	}

	if (!empty($order['supplier_bonus'])){
		//如果修改购物车商品，重新设置供货商红包使用情况
		foreach ($order['supplier_bonus'] as $key=>$var){
			if (!empty($var) && !empty($key) && !in_array($key, $arr_supplier)){
				unset($order['supplier_bonus'][$key]);
			}
		}
	}
	
	// 刷新页面删除SESSION中的配送费用，主要是迎合开启了配送时间的
	unset($_SESSION['flow_shipping']);
    /*
     * 计算订单的费用
     */
    $total = order_fee($order, $cart_goods, $consignee);
	/** TODO 去掉配送费用为0的  
	 *  @author guoyunpeng
	 */
    foreach($total['supplier_shipping'] as $_k=>$_v){
    	if($_v['formated_shipping_fee'] == '0'){
    		unset($total['supplier_shipping'][$_k]);
    	}
    }
    
    $smarty->assign('total', $total);
    $smarty->assign('shopping_money', sprintf($_LANG['shopping_money'], $total['formated_goods_price']));
    $smarty->assign('market_price_desc', sprintf($_LANG['than_market_price'], $total['formated_market_price'], $total['formated_saving'], $total['save_rate']));

    /* 取得配送列表 */
    $region            = array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district']);
    $shipping_list     = available_shipping_list($region);
    $cart_weight_price = cart_weight_price($flow_type);
    $insure_disabled   = true;
    $cod_disabled      = true;

    // 查看购物车中是否全为免运费商品，若是则把运费赋为零
    $sql = 'SELECT count(*) FROM ' . $ecs->table('cart') . " WHERE `session_id` = '" . SESS_ID. "' AND `extension_code` != 'package_buy' AND `is_shipping` = 0";
    $shipping_count = $db->getOne($sql);

    foreach ($shipping_list AS $key => $val)
    {
        $shipping_cfg = unserialize_config($val['configure']);
        $shipping_fee = ($shipping_count == 0 AND $cart_weight_price['free_shipping'] == 1) ? 0 : shipping_fee($val['shipping_code'], unserialize($val['configure']),
        $cart_weight_price['weight'], $cart_weight_price['amount'], $cart_weight_price['number']);

        $shipping_list[$key]['format_shipping_fee'] = price_format($shipping_fee, false);
        $shipping_list[$key]['shipping_fee']        = $shipping_fee;
        $shipping_list[$key]['free_money']          = price_format($shipping_cfg['free_money'], false);
        $shipping_list[$key]['insure_formated']     = strpos($val['insure'], '%') === false ?
            price_format($val['insure'], false) : $val['insure'];

        /* 当前的配送方式是否支持保价 */
        if ($val['shipping_id'] == $order['shipping_id'])
        {
            $insure_disabled = ($val['insure'] == 0);
            $cod_disabled    = ($val['support_cod'] == 0);
        }
    }

    $smarty->assign('shipping_list',   $shipping_list);
    $smarty->assign('insure_disabled', $insure_disabled);
    $smarty->assign('cod_disabled',    $cod_disabled);

	$order['shipping_id'] = 1;

    /* 取得支付列表 */
    if ($order['shipping_id'] == 0)
    {
        $cod        = true;
        $cod_fee    = 0;
    }
    else
    {
        //$shipping = shipping_info($order['shipping_id']);
		$shipping = shipping_area_info($order['shipping_id'], $region);
		if ( $shipping){
			$shipping['shipping_id'] = $order['shipping_id'];
			$cod = $shipping['support_cod'];
			$shipping_cfg = unserialize_config($shipping['configure']);
			$shipping_fee = ($shipping_count == 0 AND $cart_weight_price['free_shipping'] == 1) ? 0 : shipping_fee($shipping['shipping_code'], unserialize($shipping['configure']),
			$cart_weight_price['weight'], $cart_weight_price['amount'], $cart_weight_price['number']);
			$shipping['format_shipping_fee'] = price_format($shipping_fee, false);
			$shipping['shipping_fee']        = $shipping_fee;
			$shipping['free_money']          = price_format($shipping_cfg['free_money'], false);
		}
		$cod = $shipping['support_cod'];

        if ($cod)
        {
            /* 如果是团购，且保证金大于0，不能使用货到付款 */
            if ($flow_type == CART_GROUP_BUY_GOODS)
            {
                $group_buy_id = $_SESSION['extension_id'];
                if ($group_buy_id <= 0)
                {
                    show_message('error group_buy_id');
                }
                $group_buy = group_buy_info($group_buy_id);
                if (empty($group_buy))
                {
                    show_message('group buy not exists: ' . $group_buy_id);
                }

                if ($group_buy['deposit'] > 0)
                {
                    $cod = false;
                    $cod_fee = 0;

                    /* 赋值保证金 */
                    $smarty->assign('gb_deposit', $group_buy['deposit']);
                }
            }

            if ($cod)
            {
                $shipping_area_info = shipping_area_info($order['shipping_id'], $region);
                $cod_fee            = $shipping_area_info['pay_fee'];
            }
        }
        else
        {
            $cod_fee = 0;
        }
    }

    // 给货到付款的手续费加<span id>，以便改变配送的时候动态显示
    $payment_list = available_payment_list(1, $cod_fee);
    if(isset($payment_list))
    {
        foreach ($payment_list as $key => $payment)
        {
            if ($payment['is_cod'] == '1')
            {
                $payment_list[$key]['format_pay_fee'] = '<span id="ECS_CODFEE">' . $payment['format_pay_fee'] . '</span>';
            }
            /* 如果有易宝神州行支付 如果订单金额大于300 则不显示 */
            if ($payment['pay_code'] == 'yeepayszx' && $total['amount'] > 300)
            {
                unset($payment_list[$key]);
            }
            /* 如果有余额支付 */
            if ($payment['pay_code'] == 'balance')
            {
                /* 如果未登录，不显示 */
                if ($_SESSION['user_id'] == 0)
                {
                    unset($payment_list[$key]);
                }
                else
                {
                    if ($_SESSION['flow_order']['pay_id'] == $payment['pay_id'])
                    {
                        $smarty->assign('disable_surplus', 1);
                    }
                }
            }
        }
    }
    $smarty->assign('payment_list', $payment_list);

	//配送方式
	$smarty->assign('shipping_info',   $shipping);
	//支付方式
	$smarty->assign('payment_info', payment_info($order['pay_id']));

	//判断是否设置了支付方式，配送方式，最佳送货时间
	if ($order['shipping_id'] && $order['pay_id']){
		$smarty->assign('checkpayshipping', 1);
	}else{
		$smarty->assign('checkpayshipping', 0);
	}

    /* 取得包装与贺卡 */
    if ($total['real_goods_count'] > 0)
    {
        /* 只有有实体商品,才要判断包装和贺卡 */
        if (!isset($_CFG['use_package']) || $_CFG['use_package'] == '1')
        {
            /* 如果使用包装，取得包装列表及用户选择的包装 */
            $smarty->assign('pack_list', pack_list());
        }

        /* 如果使用贺卡，取得贺卡列表及用户选择的贺卡 */
        if (!isset($_CFG['use_card']) || $_CFG['use_card'] == '1')
        {
            $smarty->assign('card_list', card_list());
        }
    }

    $user_info = user_info($_SESSION['user_id']);

    /* 如果使用余额，取得用户余额 */
    if ((!isset($_CFG['use_surplus']) || $_CFG['use_surplus'] == '1')
        && $_SESSION['user_id'] > 0
        && $user_info['user_money'] > 0)
    {
        // 能使用余额
        $smarty->assign('allow_use_surplus', 1);
        $smarty->assign('your_surplus', $user_info['user_money']);
    }

    /* 如果使用积分，取得用户可用积分及本订单最多可以使用的积分 */
    if ((!isset($_CFG['use_integral']) || $_CFG['use_integral'] == '1')
        && $_SESSION['user_id'] > 0
        && $user_info['pay_points'] > 0
        && ($flow_type != CART_GROUP_BUY_GOODS && $flow_type != CART_EXCHANGE_GOODS))
    {
        // 能使用积分
        $smarty->assign('allow_use_integral', 1);
        $smarty->assign('order_max_integral', flow_available_points());  // 可用积分
        $smarty->assign('your_integral',      $user_info['pay_points']); // 用户积分
    }

    /* 如果使用缺货处理，取得缺货处理列表 */
    if (!isset($_CFG['use_how_oos']) || $_CFG['use_how_oos'] == '1')
    {
        if (is_array($GLOBALS['_LANG']['oos']) && !empty($GLOBALS['_LANG']['oos']))
        {
            $smarty->assign('how_oos_list', $GLOBALS['_LANG']['oos']);
        }
    }

    /* 如果能开发票，取得发票内容列表 */
    if ((!isset($_CFG['can_invoice']) || $_CFG['can_invoice'] == '1')
        && isset($_CFG['invoice_content'])
        && trim($_CFG['invoice_content']) != '' && $flow_type != CART_EXCHANGE_GOODS)
    {
        $inv_content_list = explode("\n", str_replace("\r", '', $_CFG['invoice_content']));

        $smarty->assign('inv_content_list', $inv_content_list);

        $inv_type_list = array();
        foreach ($_CFG['invoice_type']['type'] as $key => $type)
        {
            if (!empty($type))
            {
                //$inv_type_list[$type] = $type . ' [' . floatval($_CFG['invoice_type']['rate'][$key]) . '%]';
				$inv_type_list[$type] = floatval($_CFG['invoice_type']['rate'][$key]);
            }
        }
        $smarty->assign('inv_type_list', $inv_type_list);
		$invs = $_COOKIE['ECS']['flow_inv'];
		$smarty->assign('invs', $invs);
    }

	$smarty->assign('province_list',    get_regions(1, $int_cityId));

    /* 保存 session */
    $_SESSION['flow_order'] = $order;
    
    $smarty->display('flow/checkout.dwt');
    
}
elseif ($_REQUEST['step'] == 'select_shipping')
{
    /*------------------------------------------------------ */
    //-- 改变配送方式
    /*------------------------------------------------------ */
    include_once('includes/cls_json.php');
    $json = new JSON;
    $result = array('error' => '', 'content' => '', 'need_insure' => 0);

    /* 取得购物类型 */
    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

    /* 获得收货人信息 */
    $consignee = get_consignee($_SESSION['user_id']);
	
    /* 对商品信息赋值 */
    $cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计

    if (empty($cart_goods) || !check_consignee_info($consignee, $flow_type))
    {
        $result['error'] = $_LANG['no_goods_in_cart'];
    }
    else
    {
        /* 取得购物流程设置 */
        $smarty->assign('config', $_CFG);

        /* 取得订单信息 */
        $order = flow_order_info();

        $order['shipping_id'] = intval($_REQUEST['shipping']);
        $regions = array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district']);
        $shipping_info = shipping_area_info($order['shipping_id'], $regions);

        /* 计算订单的费用 */
        $total = order_fee($order, $cart_goods, $consignee);
        $smarty->assign('total', $total);

        /* 取得可以得到的积分和红包 */
        $smarty->assign('total_integral', cart_amount(false, $flow_type) - $total['bonus'] - $total['integral_money']);
        $smarty->assign('total_bonus',    price_format(get_total_bonus(), false));

        /* 团购标志 */
        if ($flow_type == CART_GROUP_BUY_GOODS)
        {
            $smarty->assign('is_group_buy', 1);
        }

        $result['cod_fee']     = $shipping_info['pay_fee'];
        if (strpos($result['cod_fee'], '%') === false)
        {
            $result['cod_fee'] = price_format($result['cod_fee'], false);
        }
        $result['need_insure'] = ($shipping_info['insure'] > 0 && !empty($order['need_insure'])) ? 1 : 0;
        $result['content']     = $smarty->fetch('library/order_total.lbi');
    }

    echo $json->encode($result);
    exit;
}
elseif ($_REQUEST['step'] == 'select_insure')
{
    /*------------------------------------------------------ */
    //-- 选定/取消配送的保价
    /*------------------------------------------------------ */

    include_once('includes/cls_json.php');
    $json = new JSON;
    $result = array('error' => '', 'content' => '', 'need_insure' => 0);

    /* 取得购物类型 */
    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

    /* 获得收货人信息 */
    $consignee = get_consignee($_SESSION['user_id']);

    /* 对商品信息赋值 */
    $cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计

    if (empty($cart_goods) || !check_consignee_info($consignee, $flow_type))
    {
        $result['error'] = $_LANG['no_goods_in_cart'];
    }
    else
    {
        /* 取得购物流程设置 */
        $smarty->assign('config', $_CFG);

        /* 取得订单信息 */
        $order = flow_order_info();

        $order['need_insure'] = intval($_REQUEST['insure']);

        /* 保存 session */
        $_SESSION['flow_order'] = $order;

        /* 计算订单的费用 */
        $total = order_fee($order, $cart_goods, $consignee);
        $smarty->assign('total', $total);

        /* 取得可以得到的积分和红包 */
        $smarty->assign('total_integral', cart_amount(false, $flow_type) - $total['bonus'] - $total['integral_money']);
        $smarty->assign('total_bonus',    price_format(get_total_bonus(), false));

        /* 团购标志 */
        if ($flow_type == CART_GROUP_BUY_GOODS)
        {
            $smarty->assign('is_group_buy', 1);
        }

        $result['content'] = $smarty->fetch('library/order_total.lbi');
    }

    echo $json->encode($result);
    exit;
}
elseif ($_REQUEST['step'] == 'select_payment')
{
    /*------------------------------------------------------ */
    //-- 改变支付方式
    /*------------------------------------------------------ */

    include_once('includes/cls_json.php');
    $json = new JSON;
    $result = array('error' => '', 'content' => '', 'need_insure' => 0, 'payment' => 1);

    /* 取得购物类型 */
    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

    /* 获得收货人信息 */
    $consignee = get_consignee($_SESSION['user_id']);

    /* 对商品信息赋值 */
    $cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计

    if (empty($cart_goods) || !check_consignee_info($consignee, $flow_type))
    {
        $result['error'] = $_LANG['no_goods_in_cart'];
    }
    else
    {
        /* 取得购物流程设置 */
        $smarty->assign('config', $_CFG);

        /* 取得订单信息 */
        $order = flow_order_info();

        $order['pay_id'] = intval($_REQUEST['payment']);
        $payment_info = payment_info($order['pay_id']);
        $result['pay_code'] = $payment_info['pay_code'];

        /* 保存 session */
        $_SESSION['flow_order'] = $order;

        /* 计算订单的费用 */
        $total = order_fee($order, $cart_goods, $consignee);
        $smarty->assign('total', $total);

        /* 取得可以得到的积分和红包 */
        $smarty->assign('total_integral', cart_amount(false, $flow_type) - $total['bonus'] - $total['integral_money']);
        $smarty->assign('total_bonus',    price_format(get_total_bonus(), false));

        /* 团购标志 */
        if ($flow_type == CART_GROUP_BUY_GOODS)
        {
            $smarty->assign('is_group_buy', 1);
        }

        $result['content'] = $smarty->fetch('library/order_total.lbi');
    }

    echo $json->encode($result);
    exit;
}
elseif ($_REQUEST['step'] == 'select_pack')
{
    /*------------------------------------------------------ */
    //-- 改变商品包装
    /*------------------------------------------------------ */

    include_once('includes/cls_json.php');
    $json = new JSON;
    $result = array('error' => '', 'content' => '', 'need_insure' => 0);

    /* 取得购物类型 */
    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

    /* 获得收货人信息 */
    $consignee = get_consignee($_SESSION['user_id']);

    /* 对商品信息赋值 */
    $cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计

    if (empty($cart_goods) || !check_consignee_info($consignee, $flow_type))
    {
        $result['error'] = $_LANG['no_goods_in_cart'];
    }
    else
    {
        /* 取得购物流程设置 */
        $smarty->assign('config', $_CFG);

        /* 取得订单信息 */
        $order = flow_order_info();

        $order['pack_id'] = intval($_REQUEST['pack']);

        /* 保存 session */
        $_SESSION['flow_order'] = $order;

        /* 计算订单的费用 */
        $total = order_fee($order, $cart_goods, $consignee);
        $smarty->assign('total', $total);

        /* 取得可以得到的积分和红包 */
        $smarty->assign('total_integral', cart_amount(false, $flow_type) - $total['bonus'] - $total['integral_money']);
        $smarty->assign('total_bonus',    price_format(get_total_bonus(), false));

        /* 团购标志 */
        if ($flow_type == CART_GROUP_BUY_GOODS)
        {
            $smarty->assign('is_group_buy', 1);
        }

        $result['content'] = $smarty->fetch('library/order_total.lbi');
    }

    echo $json->encode($result);
    exit;
}
elseif ($_REQUEST['step'] == 'select_card')
{
    /*------------------------------------------------------ */
    //-- 改变贺卡
    /*------------------------------------------------------ */

    include_once('includes/cls_json.php');
    $json = new JSON;
    $result = array('error' => '', 'content' => '', 'need_insure' => 0);

    /* 取得购物类型 */
    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

    /* 获得收货人信息 */
    $consignee = get_consignee($_SESSION['user_id']);

    /* 对商品信息赋值 */
    $cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计

    if (empty($cart_goods) || !check_consignee_info($consignee, $flow_type))
    {
        $result['error'] = $_LANG['no_goods_in_cart'];
    }
    else
    {
        /* 取得购物流程设置 */
        $smarty->assign('config', $_CFG);

        /* 取得订单信息 */
        $order = flow_order_info();

        $order['card_id'] = intval($_REQUEST['card']);

        /* 保存 session */
        $_SESSION['flow_order'] = $order;

        /* 计算订单的费用 */
        $total = order_fee($order, $cart_goods, $consignee);
        $smarty->assign('total', $total);

        /* 取得可以得到的积分和红包 */
        $smarty->assign('total_integral', cart_amount(false, $flow_type) - $order['bonus'] - $total['integral_money']);
        $smarty->assign('total_bonus',    price_format(get_total_bonus(), false));

        /* 团购标志 */
        if ($flow_type == CART_GROUP_BUY_GOODS)
        {
            $smarty->assign('is_group_buy', 1);
        }

        $result['content'] = $smarty->fetch('library/order_total.lbi');
    }

    echo $json->encode($result);
    exit;
}
elseif ($_REQUEST['step'] == 'change_surplus')
{
    /*------------------------------------------------------ */
    //-- 改变余额
    /*------------------------------------------------------ */
    include_once('includes/cls_json.php');

    $surplus   = floatval($_GET['surplus']);
    $user_info = user_info($_SESSION['user_id']);

    if ($user_info['user_money'] + $user_info['credit_line'] < $surplus)
    {
        $result['error'] = $_LANG['surplus_not_enough'];
    }
    else
    {
        /* 取得购物类型 */
        $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

        /* 取得购物流程设置 */
        $smarty->assign('config', $_CFG);

        /* 获得收货人信息 */
        $consignee = get_consignee($_SESSION['user_id']);

        /* 对商品信息赋值 */
        $cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计

        if (empty($cart_goods) || !check_consignee_info($consignee, $flow_type))
        {
            $result['error'] = $_LANG['no_goods_in_cart'];
        }
        else
        {
            /* 取得订单信息 */
            $order = flow_order_info();
            $order['surplus'] = $surplus;

            /* 计算订单的费用 */
            $total = order_fee($order, $cart_goods, $consignee);
            $smarty->assign('total', $total);

            /* 团购标志 */
            if ($flow_type == CART_GROUP_BUY_GOODS)
            {
                $smarty->assign('is_group_buy', 1);
            }

            $result['content'] = $smarty->fetch('library/order_total.lbi');
        }
    }

    $json = new JSON();
    die($json->encode($result));
}
elseif ($_REQUEST['step'] == 'change_integral')
{
    /*------------------------------------------------------ */
    //-- 改变积分
    /*------------------------------------------------------ */
    include_once('includes/cls_json.php');

    $points    = floatval($_GET['points']);
    $user_info = user_info($_SESSION['user_id']);

    /* 取得订单信息 */
    $order = flow_order_info();

    $flow_points = flow_available_points();  // 该订单允许使用的积分
    $user_points = $user_info['pay_points']; // 用户的积分总数

    if ($points > $user_points)
    {
        $result['error'] = $_LANG['integral_not_enough'];
    }
    elseif ($points > $flow_points)
    {
        $result['error'] = sprintf($_LANG['integral_too_much'], $flow_points);
    }
    else
    {
        /* 取得购物类型 */
        $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

        $order['integral'] = $points;

        /* 获得收货人信息 */
        $consignee = get_consignee($_SESSION['user_id']);

        /* 对商品信息赋值 */
        $cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计

        if (empty($cart_goods) || !check_consignee_info($consignee, $flow_type))
        {
            $result['error'] = $_LANG['no_goods_in_cart'];
        }
        else
        {
            /* 计算订单的费用 */
            $total = order_fee($order, $cart_goods, $consignee);
            $smarty->assign('total',  $total);
            $smarty->assign('config', $_CFG);

            /* 团购标志 */
            if ($flow_type == CART_GROUP_BUY_GOODS)
            {
                $smarty->assign('is_group_buy', 1);
            }

            $result['content'] = $smarty->fetch('library/order_total.lbi');
            $result['error'] = '';
        }
    }

    $json = new JSON();
    die($json->encode($result));
}
elseif ($_REQUEST['step'] == 'change_bonus')
{
    /*------------------------------------------------------ */
    //-- 改变红包
    /*------------------------------------------------------ */
    include_once('includes/cls_json.php');
    $result = array('error' => '', 'content' => '');

    /* 取得购物类型 */
    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

    /* 获得收货人信息 */
    $consignee = get_consignee($_SESSION['user_id']);

    /* 对商品信息赋值 */
    $cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计

    if (empty($cart_goods) || !check_consignee_info($consignee, $flow_type))
    {
        $result['error'] = $_LANG['no_goods_in_cart'];
    }
    else
    {
        /* 取得购物流程设置 */
        $smarty->assign('config', $_CFG);

        /* 取得订单信息 */
        $order = flow_order_info();
		$is_supplierBonus = 0;
		if (!empty($order['supplier_bonus'])){
			foreach ($order['supplier_bonus'] as $key=>$var){
				if (!empty($var)){
					$is_supplierBonus = 1;
					break;
				}
			}
		}
		if (!empty($is_supplierBonus)){
			$result['error'] = '本网站红包不能和供货商红包一同使用';
		}else{
			$bonus = bonus_info(intval($_GET['bonus']));

			if ((!empty($bonus) && $bonus['user_id'] == $_SESSION['user_id']) || $_GET['bonus'] == 0)
			{
				$order['bonus_id'] = intval($_GET['bonus']);
			}
			else
			{
				$order['bonus_id'] = 0;
				$result['error'] = $_LANG['invalid_bonus'];
			}

			/* 计算订单的费用 */
			$total = order_fee($order, $cart_goods, $consignee);
			$smarty->assign('total', $total);

			/* 团购标志 */
			if ($flow_type == CART_GROUP_BUY_GOODS)
			{
				$smarty->assign('is_group_buy', 1);
			}
			$result['bonus'] = $order['bonus_id'];
			$result['content'] = $smarty->fetch('library/order_total.lbi');
		}
    }

    $json = new JSON();
    die($json->encode($result));
}
//使用供货商红包
else if ($_REQUEST['step'] == 'change_supplier_bonus'){
	/*------------------------------------------------------ */
    //-- 改变红包
    /*------------------------------------------------------ */
    include_once('includes/cls_json.php');
    $result = array('error' => '', 'content' => '');

    /* 取得购物类型 */
    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

    /* 获得收货人信息 */
    $consignee = get_consignee($_SESSION['user_id']);

    /* 对商品信息赋值 */
    $cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计

    if (empty($cart_goods) || !check_consignee_info($consignee, $flow_type))
    {
        $result['error'] = $_LANG['no_goods_in_cart'];
    }
    else
    {
        /* 取得购物流程设置 */
        $smarty->assign('config', $_CFG);

        /* 取得订单信息 */
        $order = flow_order_info();

		if (!empty($order['bonus_id'])){
			$result['error'] = '本网站红包不能和供货商红包一同使用';
		}else{
			$bonus = bonus_info(intval($_GET['bonus']));
			if ((!empty($bonus) && $bonus['user_id'] == $_SESSION['user_id']) || $_GET['bonus'] == 0)
			{
				$order['supplier_bonus'][intval($_GET['supplier'])] = intval($_GET['bonus']);
			}
			else
			{
				$order['supplier_bonus'][intval($_GET['supplier'])] = 0;
				$result['error'] = $_LANG['invalid_bonus'];
			}

			/* 计算订单的费用 */
			$total = order_fee($order, $cart_goods, $consignee);
			$smarty->assign('total', $total);

			/* 团购标志 */
			if ($flow_type == CART_GROUP_BUY_GOODS)
			{
				$smarty->assign('is_group_buy', 1);
			}
			$result['bonus'] = $total['supplier_bonus'];
			$result['content'] = $smarty->fetch('library/order_total.lbi');
		}
	}
    $json = new JSON();
    die($json->encode($result));
}
elseif ($_REQUEST['step'] == 'change_needinv')
{
    /*------------------------------------------------------ */
    //-- 改变发票的设置
    /*------------------------------------------------------ */
    include_once('includes/cls_json.php');
    $result = array('error' => '', 'content' => '');
    $json = new JSON();
    $_GET['inv_type'] = !empty($_GET['inv_type']) ? json_str_iconv(urldecode($_GET['inv_type'])) : '';
    $_GET['invPayee'] = !empty($_GET['invPayee']) ? json_str_iconv(urldecode($_GET['invPayee'])) : '';
    $_GET['inv_content'] = !empty($_GET['inv_content']) ? json_str_iconv(urldecode($_GET['inv_content'])) : '';

    /* 取得购物类型 */
    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

    /* 获得收货人信息 */
    $consignee = get_consignee($_SESSION['user_id']);

    /* 对商品信息赋值 */
    $cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计

    if (empty($cart_goods) || !check_consignee_info($consignee, $flow_type))
    {
        $result['error'] = $_LANG['no_goods_in_cart'];
        die($json->encode($result));
    }
    else
    {
        /* 取得购物流程设置 */
        $smarty->assign('config', $_CFG);

        /* 取得订单信息 */
        $order = flow_order_info();

        if (isset($_GET['need_inv']) && intval($_GET['need_inv']) == 1)
        {
            $order['need_inv']    = 1;
            $order['inv_type']    = trim(stripslashes($_GET['inv_type']));
            $order['inv_payee']   = trim(stripslashes($_GET['inv_payee']));
            $order['inv_content'] = trim(stripslashes($_GET['inv_content']));
        }
        else
        {
            $order['need_inv']    = 0;
            $order['inv_type']    = '';
            $order['inv_payee']   = '';
            $order['inv_content'] = '';
        }

        /* 计算订单的费用 */
        $total = order_fee($order, $cart_goods, $consignee);
        $smarty->assign('total', $total);

        /* 团购标志 */
        if ($flow_type == CART_GROUP_BUY_GOODS)
        {
            $smarty->assign('is_group_buy', 1);
        }

        die($smarty->fetch('library/order_total.lbi'));
    }
}
elseif ($_REQUEST['step'] == 'change_oos')
{
    /*------------------------------------------------------ */
    //-- 改变缺货处理时的方式
    /*------------------------------------------------------ */

    /* 取得订单信息 */
    $order = flow_order_info();

    $order['how_oos'] = intval($_GET['oos']);

    /* 保存 session */
    $_SESSION['flow_order'] = $order;
}
elseif ($_REQUEST['step'] == 'check_surplus')
{
    /*------------------------------------------------------ */
    //-- 检查用户输入的余额
    /*------------------------------------------------------ */
    $surplus   = floatval($_GET['surplus']);
    $user_info = user_info($_SESSION['user_id']);

    if (($user_info['user_money'] + $user_info['credit_line'] < $surplus))
    {
        die($_LANG['surplus_not_enough']);
    }

    exit;
}
elseif ($_REQUEST['step'] == 'check_integral')
{
    /*------------------------------------------------------ */
    //-- 检查用户输入的余额
    /*------------------------------------------------------ */
    $points      = floatval($_GET['integral']);
    $user_info   = user_info($_SESSION['user_id']);
    $flow_points = flow_available_points();  // 该订单允许使用的积分
    $user_points = $user_info['pay_points']; // 用户的积分总数

    if ($points > $user_points)
    {
        die($_LANG['integral_not_enough']);
    }

    if ($points > $flow_points)
    {
        die(sprintf($_LANG['integral_too_much'], $flow_points));
    }

    exit;
}
/*------------------------------------------------------ */
//-- 完成所有订单操作，提交到数据库
/*------------------------------------------------------ */
elseif ($_REQUEST['step'] == 'done')
{
    /* Array
    (
        [riqi] => Array
        (
            [3] => 2016-05-31
            [2] => 2016-05-31
        )
    
        [time] => Array
        (
            [3] => 配送时间
            [2] => 19:00-19:30
        )
    
        [sup_3] => 10
        [sup_2] => 0
        [shipping] => 1
        [payment] => 2
        [payshipping_check] => 1
        [step] => done
    )
     */
	
    include_once('includes/lib_clips.php');
    include_once('includes/lib_payment.php');
    /* 取得购物类型 */
    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

    /* 检查购物车中是否有商品 */
    $sql = "SELECT COUNT(*) FROM " . $ecs->table('cart') .
        " WHERE session_id = '" . SESS_ID . "' " .
        "AND parent_id = 0 AND is_gift = 0 AND rec_type = '$flow_type'";
    if ($db->getOne($sql) == 0)
    {
        show_message($_LANG['no_goods_in_cart'], '', '', 'warning');
    }
	
	

    /* 检查商品库存 */
    /* 如果使用库存，且下订单时减库存，则减少库存 */
    if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)
    {
        $cart_goods_stock = get_cart_goods();
        $_cart_goods_stock = array();
        foreach ($cart_goods_stock['goods_list'] as $value)
        {
            $_cart_goods_stock[$value['rec_id']] = $value['goods_number'];
        }
        flow_cart_stock($_cart_goods_stock);
        unset($cart_goods_stock, $_cart_goods_stock);
    }

    /*
     * 检查用户是否已经登录
     * 如果用户已经登录了则检查是否有默认的收货地址
     * 如果没有登录则跳转到登录和注册页面
     */
    if (empty($_SESSION['direct_shopping']) && $_SESSION['user_id'] == 0)
    {
        /* 用户没有登录且没有选定匿名购物，转向到登录页面 */
        ecs_header("Location: user.php\n");
        exit;
    }

    $consignee = get_consignee($_SESSION['user_id']);

    $_POST['how_oos'] = isset($_POST['how_oos']) ? intval($_POST['how_oos']) : 0;
    $_POST['card_message'] = isset($_POST['card_message']) ? compile_str($_POST['card_message']) : '';
    $_POST['inv_type'] = !empty($_POST['inv_type']) ? compile_str($_POST['inv_type']) : '';
    $_POST['inv_payee'] = isset($_POST['inv_payee']) ? compile_str($_POST['inv_payee']) : '';
    $_POST['inv_content'] = isset($_POST['inv_content']) ? compile_str($_POST['inv_content']) : '';
    $_POST['postscript'] = isset($_POST['postscript']) ? compile_str($_POST['postscript']) : '';

	$arr_supplierBonus = $_POST['supplier_bonus'];

    $order = array(
        'shipping_id'     => intval($_POST['shipping']),
        'pay_id'          => intval($_POST['payment']),
        'pack_id'         => isset($_POST['pack']) ? intval($_POST['pack']) : 0,
        'card_id'         => isset($_POST['card']) ? intval($_POST['card']) : 0,
        'card_message'    => trim($_POST['card_message']),
        'surplus'         => isset($_POST['surplus']) ? floatval($_POST['surplus']) : 0.00,
        'integral'        => isset($_POST['integral']) ? intval($_POST['integral']) : 0,
        'bonus_id'        => isset($_POST['bonus']) ? intval($_POST['bonus']) : 0,
        'need_inv'        => empty($_POST['need_inv']) ? 0 : 1,
        'inv_type'        => $_POST['inv_type'],
        'inv_payee'       => trim($_POST['inv_payee']),
        'inv_content'     => $_POST['inv_content'],
        'postscript'      => trim($_POST['postscript']),
        'how_oos'         => isset($_LANG['oos'][$_POST['how_oos']]) ? addslashes($_LANG['oos'][$_POST['how_oos']]) : '',
        'need_insure'     => isset($_POST['need_insure']) ? intval($_POST['need_insure']) : 0,
        'user_id'         => $_SESSION['user_id'],
        'add_time'        => gmtime(),
        'order_status'    => OS_UNCONFIRMED,
        'shipping_status' => SS_UNSHIPPED,
        'pay_status'      => PS_UNPAYED,
        'agency_id'       => get_agency_by_regions(array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district']))
        );

    /* 扩展信息 */
    if (isset($_SESSION['flow_type']) && intval($_SESSION['flow_type']) != CART_GENERAL_GOODS)
    {
        $order['extension_code'] = $_SESSION['extension_code'];
        $order['extension_id'] = $_SESSION['extension_id'];
    }
    else
    {
        $order['extension_code'] = '';
        $order['extension_id'] = 0;
    }

    /* 检查积分余额是否合法 */
    $user_id = $_SESSION['user_id'];
    if ($user_id > 0)
    {
        $user_info = user_info($user_id);

        $order['surplus'] = min($order['surplus'], $user_info['user_money'] + $user_info['credit_line']);
        if ($order['surplus'] < 0)
        {
            $order['surplus'] = 0;
        }

        // 查询用户有多少积分
        $flow_points = flow_available_points();  // 该订单允许使用的积分
        $user_points = $user_info['pay_points']; // 用户的积分总数

        $order['integral'] = min($order['integral'], $user_points, $flow_points);
        if ($order['integral'] < 0)
        {
            $order['integral'] = 0;
        }
    }
    else
    {
        $order['surplus']  = 0;
        $order['integral'] = 0;
    }

    /* 检查红包是否存在 */
    if ($order['bonus_id'] > 0)
    {
        $bonus = bonus_info($order['bonus_id']);

        if (empty($bonus) || $bonus['user_id'] != $user_id || $bonus['order_id'] > 0 || $bonus['min_goods_amount'] > cart_amount(true, $flow_type))
        {
            $order['bonus_id'] = 0;
        }
    }
    elseif (isset($_POST['bonus_sn']))
    {
        $bonus_sn = trim($_POST['bonus_sn']);
        $bonus = bonus_info(0, $bonus_sn);
        $now = gmtime();
        if (empty($bonus) || $bonus['user_id'] > 0 || $bonus['order_id'] > 0 || $bonus['min_goods_amount'] > cart_amount(true, $flow_type) || $now > $bonus['use_end_date'])
        {
        }
        else
        {
            if ($user_id > 0)
            {
                $sql = "UPDATE " . $ecs->table('user_bonus') . " SET user_id = '$user_id', 'add_time' = '$now' WHERE bonus_id = '$bonus[bonus_id]' LIMIT 1";
                $db->query($sql);
            }
            $order['bonus_id'] = $bonus['bonus_id'];
            $order['bonus_sn'] = $bonus_sn;
        }
    }else if ($arr_supplierBonus){
		$order['supplier_bonus'] = array();
		$is_supplierBonus = 0;
		foreach ($arr_supplierBonus as $key=>$var){
			if (!empty($var)){
				$bonus = bonus_info($var);
				if (empty($bonus) || $bonus['user_id'] != $user_id || $bonus['order_id'] > 0 || $bonus['min_goods_amount'] > cart_amount(true, $flow_type)){
					continue;
				}else{
					$is_supplierBonus = 1;
					$order['supplier_bonus'][$key] = $var;
				}
			}
		}
	}



    /* 订单中的商品 */
    $cart_goods = cart_goods($flow_type);
	$int_cardCount = count($cart_goods);
	$smarty->assign('cardCount', $int_cardCount);

    if (empty($cart_goods))
    {
        show_message($_LANG['no_goods_in_cart'], $_LANG['back_home'], './', 'warning');
    }

    /* 检查商品总额是否达到最低限购金额 */
    if ($flow_type == CART_GENERAL_GOODS && cart_amount(true, CART_GENERAL_GOODS) < $_CFG['min_goods_amount'])
    {
        show_message(sprintf($_LANG['goods_amount_not_enough'], price_format($_CFG['min_goods_amount'], false)));
    }

    /* 收货人信息 */
    foreach ($consignee as $key => $value)
    {
        $order[$key] = addslashes($value);
    }
	
    /* 处理配货时间*/
    $rctArray = array();
	foreach( array('riqi','time') as $rct){
		foreach($_POST[$rct] as $sid=>$stime){			
			$rctArray[$sid][$rct] = $stime; 
		}
	}
	
	$order['best_time'] = serialize($rctArray);



   /* 判断是不是实体商品 */
    foreach ($cart_goods AS $val)
    {
        /* 统计实体商品的个数 */
        if ($val['is_real'])
        {
            $is_real_good=1;
        }
    }
    if(isset($is_real_good))
    {
        $sql="SELECT shipping_id FROM " . $ecs->table('shipping') . " WHERE shipping_id=".$order['shipping_id'] ." AND enabled =1"; 
        if(!$db->getOne($sql))
        {
           show_message($_LANG['flow_no_shipping']);
        }
    }
    /* 订单中的总额 */
    $total = order_fee($order, $cart_goods, $consignee);
    
    // 计算运费
    $shipping_total_fee = 0;
    if (!empty($_POST['sup']))
    {
        $shipping_total_fee = array_sum($_POST['sup']);
    }    
    $total['shipping_fee'] = $shipping_total_fee;
    // 总价 = 商品总价 + 运费总价
    $total['amount'] = $total['amount'] + $shipping_total_fee;
 
    $order['bonus']        = 0;
    $order['goods_amount'] = 0;
    $order['discount']     = 0;
    $order['surplus']      = 0;
    $order['tax']          = 0;

    // 购物车中的商品能享受红包支付的总额
    $discount_amout = compute_discount_amount();
    // 红包和积分最多能支付的金额为商品总额
    $temp_amout = $order['goods_amount'] - $discount_amout;
    if ($temp_amout <= 0)
    {
        $order['bonus_id'] = 0;
		$order['supplier_bonus_info'] = '';
    }

    /* 配送方式 */
    if ($order['shipping_id'] > 0)
    {
        $shipping = shipping_info($order['shipping_id']);
        $order['shipping_name'] = addslashes($shipping['shipping_name']);
    }
    $order['shipping_fee'] = $total['shipping_fee'];
    $order['insure_fee']   = 0;

    /* 支付方式 */
    if ($order['pay_id'] > 0)
    {
        $payment = payment_info($order['pay_id']);
        $order['pay_name'] = addslashes($payment['pay_name']);
    }
    $order['pay_fee'] = 0;
    $order['cod_fee'] = 0;

    /* 商品包装 */
    if ($order['pack_id'] > 0)
    {
        $pack               = pack_info($order['pack_id']);
        $order['pack_name'] = addslashes($pack['pack_name']);
    }
    $order['pack_fee'] = 0;

    /* 祝福贺卡 */
    if ($order['card_id'] > 0)
    {
        $card               = card_info($order['card_id']);
        $order['card_name'] = addslashes($card['card_name']);
    }
    $order['card_fee']      = 0;

    $order['order_amount']  = number_format($total['amount'], 2, '.', '');

    /* 如果全部使用余额支付，检查余额是否足够 */
    if ($payment['pay_code'] == 'balance' && $order['order_amount'] > 0)
    {
        if($order['surplus'] >0) //余额支付里如果输入了一个金额
        {
            $order['order_amount'] = $order['order_amount'] + $order['surplus'];
            $order['surplus'] = 0;
        }
        if ($order['order_amount'] > ($user_info['user_money'] + $user_info['credit_line']))
        {
            show_message($_LANG['balance_not_enough']);
        }
        else
        {
            $order['surplus'] = $order['order_amount'];
            $order['order_amount'] = 0;
        }
    }

    /* 如果订单金额为0（使用余额或积分或红包支付），修改订单状态为已确认、已付款 */
    if ($order['order_amount'] <= 0)
    {
        $order['order_status'] = OS_CONFIRMED;
        $order['confirm_time'] = gmtime();
        $order['pay_status']   = PS_PAYED;
        $order['pay_time']     = gmtime();
        $order['order_amount'] = 0;
    }

    $order['integral_money']   = 0;
    $order['integral']         = 0;

    if ($order['extension_code'] == 'exchange_goods')
    {
        $order['integral_money']   = 0;
        $order['integral']         = 0;
    }

    $order['from_ad']          = !empty($_SESSION['from_ad']) ? $_SESSION['from_ad'] : '0';
    $order['referer']          = !empty($_SESSION['referer']) ? addslashes($_SESSION['referer']) : '';

    /* 记录扩展信息 */
    if ($flow_type != CART_GENERAL_GOODS)
    {
        $order['extension_code'] = $_SESSION['extension_code'];
        $order['extension_id'] = $_SESSION['extension_id'];
    }

    $affiliate = unserialize($_CFG['affiliate']);
    if(isset($affiliate['on']) && $affiliate['on'] == 1 && $affiliate['config']['separate_by'] == 1)
    {
        //推荐订单分成
        $parent_id = get_affiliate();
        if($user_id == $parent_id)
        {
            $parent_id = 0;
        }
    }
    elseif(isset($affiliate['on']) && $affiliate['on'] == 1 && $affiliate['config']['separate_by'] == 0)
    {
        //推荐注册分成
        $parent_id = 0;
    }
    else
    {
        //分成功能关闭
        $parent_id = 0;
    }
    $order['parent_id'] = $parent_id;
    
    // 设置供应商单独的配送地址
    $supplierIds = array();
    $supplierAddress = $_SESSION['supplier'];
    foreach ((array) $supplierAddress as $supk=>$supv) { $supplierIds[] = $supk;}    
    if (count($_POST['sup']) != count($_SESSION['supplier']))
    {
        foreach ($_POST['sup'] as $supid=>$supyun)
        {
            if (!in_array($supid, $supplierIds))
            {
                $supplierAddress[$supid] = $consignee;
            }
        }
    }
    $order['supplier_address'] = serialize($supplierAddress);
    // 运费    
    $order['supplier_shipping'] = serialize($_POST['sup']);
    
    /* 插入订单表 */
    $error_no = 0;
    do
    {
        $order['order_sn'] = get_order_sn(); //获取新订单号
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('order_info'), $order, 'INSERT');

        $error_no = $GLOBALS['db']->errno();

        if ($error_no > 0 && $error_no != 1062)
        {
            die($GLOBALS['db']->errorMsg());
        }
    }
    
    while ($error_no == 1062); //如果是订单号重复则重新提交数据
    

    $new_order_id = $db->insert_id();
    
    $order['order_id'] = $new_order_id;

    /* 插入订单商品 */
    $sql = "INSERT INTO " . $ecs->table('order_goods') . "( " .
                "order_id, goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
                "goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id) ".
            " SELECT '$new_order_id', goods_id, goods_name, goods_sn, product_id, goods_number, market_price, ".
                "goods_price, goods_attr, is_real, extension_code, parent_id, is_gift, goods_attr_id".
            " FROM " .$ecs->table('cart') .
            " WHERE session_id = '".SESS_ID."' AND rec_type = '$flow_type'";
    $db->query($sql);
    /* 修改拍卖活动状态 */
    if ($order['extension_code']=='auction')
    {
        $sql = "UPDATE ". $ecs->table('goods_activity') ." SET is_finished='2' WHERE act_id=".$order['extension_id'];
        $db->query($sql);
    }

    /* 处理余额、积分、红包 */
    if ($order['user_id'] > 0 && $order['surplus'] > 0)
    {
        log_account_change($order['user_id'], $order['surplus'] * (-1), 0, 0, 0, sprintf($_LANG['pay_order'], $order['order_sn']));
    }
    if ($order['user_id'] > 0 && $order['integral'] > 0)
    {
        log_account_change($order['user_id'], 0, 0, 0, $order['integral'] * (-1), sprintf($_LANG['pay_order'], $order['order_sn']));
    }


    if ($order['bonus_id'] > 0 && $temp_amout > 0)
    {
        use_bonus($order['bonus_id'], $new_order_id);
    }else if ($total['supplier_bonus_info']){
		foreach ($total['supplier_bonus_info'] as $key=>$var){
			if (!empty($var['bonus_id'])){
				//标记红包已使用
				use_bonus($var['bonus_id'], $new_order_id);
			}
		}
	}

    /* 如果使用库存，且下订单时减库存，则减少库存 */
    if ($_CFG['use_storage'] == '1' && $_CFG['stock_dec_time'] == SDT_PLACE)
    {
        change_order_goods_storage($order['order_id'], true, SDT_PLACE);
    }

    /* 给商家发邮件 */
    /* 增加是否给客服发送邮件选项 */
    if ($_CFG['send_service_email'] && $_CFG['service_email'] != '')
    {
        $tpl = get_mail_template('remind_of_new_order');
        $smarty->assign('order', $order);
        $smarty->assign('goods_list', $cart_goods);
        $smarty->assign('shop_name', $_CFG['shop_name']);
        $smarty->assign('send_date', date($_CFG['time_format']));
        $content = $smarty->fetch('str:' . $tpl['template_content']);
        send_mail($_CFG['shop_name'], $_CFG['service_email'], $tpl['template_subject'], $content, $tpl['is_html']);
    }

    /* 如果需要，发短信 */
    if ($_CFG['sms_order_placed'] == '1' && $_CFG['sms_shop_mobile'] != '')
    {
        include_once('includes/cls_sms.php');
        $sms = new sms();
        $msg = $order['pay_status'] == PS_UNPAYED ?
            $_LANG['order_placed_sms'] : $_LANG['order_placed_sms'] . '[' . $_LANG['sms_paid'] . ']';
        $sms->send($_CFG['sms_shop_mobile'], sprintf($msg, $order['consignee'], $order['tel']),'', 13,1);
    }

    /* 如果订单金额为0 处理虚拟卡 */
    if ($order['order_amount'] <= 0)
    {
        $sql = "SELECT goods_id, goods_name, goods_number AS num FROM ".
               $GLOBALS['ecs']->table('cart') .
                " WHERE is_real = 0 AND extension_code = 'virtual_card'".
                " AND session_id = '".SESS_ID."' AND rec_type = '$flow_type'";

        $res = $GLOBALS['db']->getAll($sql);

        $virtual_goods = array();
        foreach ($res AS $row)
        {
            $virtual_goods['virtual_card'][] = array('goods_id' => $row['goods_id'], 'goods_name' => $row['goods_name'], 'num' => $row['num']);
        }

        if ($virtual_goods AND $flow_type != CART_GROUP_BUY_GOODS)
        {
            /* 虚拟卡发货 */
            if (virtual_goods_ship($virtual_goods,$msg, $order['order_sn'], true))
            {
                /* 如果没有实体商品，修改发货状态，送积分和红包 */
                $sql = "SELECT COUNT(*)" .
                        " FROM " . $ecs->table('order_goods') .
                        " WHERE order_id = '$order[order_id]' " .
                        " AND is_real = 1";
                if ($db->getOne($sql) <= 0)
                {
                    /* 修改订单状态 */
                    update_order($order['order_id'], array('shipping_status' => SS_SHIPPED, 'shipping_time' => gmtime()));

                    /* 如果订单用户不为空，计算积分，并发给用户；发红包 */
                    if ($order['user_id'] > 0)
                    {
                        /* 取得用户信息 */
                        $user = user_info($order['user_id']);

                        /* 计算并发放积分 */
                        $integral = integral_to_give($order);
                        log_account_change($order['user_id'], 0, 0, intval($integral['rank_points']), intval($integral['custom_points']), sprintf($_LANG['order_gift_integral'], $order['order_sn']));

                        /* 发放红包 */
                        send_order_bonus($order['order_id']);
                    }
                }
            }
        }

    }

    /* 清空购物车 */
    clear_cart($flow_type);
    /* 清除缓存，否则买了商品，但是前台页面读取缓存，商品数量不减少 */
    clear_all_files();

    /* 插入支付日志 */
    //$order['log_id'] = insert_pay_log($new_order_id, $order['order_amount'], PAY_ORDER);

    /* 取得支付信息，生成支付代码 */
    if ($order['order_amount'] > 0)
    {
        $payment = payment_info($order['pay_id']);

        include_once('includes/modules/payment/' . $payment['pay_code'] . '.php');

        $pay_obj    = new $payment['pay_code'];

        $pay_online = $pay_obj->get_code($order, unserialize_config($payment['pay_config']));

        $order['pay_desc'] = $payment['pay_desc'];

        $smarty->assign('pay_online', $pay_online);
    }
    if(!empty($order['shipping_name']))
    {
        $order['shipping_name']=trim(stripcslashes($order['shipping_name']));
    }

	//分单处理
	$split_order = split_order($new_order_id);
	
	// 分单后插入支付日志，并记录支付日志id
    if(count($split_order['suborder_list']) > 0){
		foreach($split_order['suborder_list'] as $key => $val){
			/* 插入支付日志 */
			insert_pay_log($val['order_id'], $val['order_amount'], PAY_ORDER);
		}
	}
    /* 订单信息 */
    $smarty->assign('order',      $order);
    $smarty->assign('total',      $total);
    $smarty->assign('goods_list', $cart_goods);
    $smarty->assign('order_submit_back', sprintf($_LANG['order_submit_back'], $_LANG['back_home'], $_LANG['goto_user_center'])); // 返回提示

    unset($_SESSION['flow_consignee']); // 清除session中保存的收货人信息
    unset($_SESSION['flow_order']);
    unset($_SESSION['direct_shopping']);
    unset($_SESSION['supplier']);
    ecs_header("Location: flow.php?step=pay&order_id=".$new_order_id);
}
// 支付页面
elseif ($_REQUEST['step'] == 'pay')
{
    $orderid = intval($_GET['order_id']);
    if ($orderid < 1)
    {
        ecs_header("Location: user.php\n");
    }    
    $orderInfo = findData('order_info',"order_id=$orderid");
    
    $smarty->assign('orders',current($orderInfo));
    $smarty->display('flow/pay.dwt');
}
//支付成功页面
else if ($_REQUEST['step'] == 'respond'){
	$order_id = intval($_REQUEST['id']);
	$smarty->display('flow/respond.dwt');
}

//操作支付页面
else if ($_REQUEST['step'] == 'act_pay')
{    
    include_once(ROOT_PATH . 'includes/lib_payment.php');
    
	$str_password = !empty($_POST['password']) ? $_POST['password'] : '';	
	$order_id     = intval($_POST['order_id']);

	$arr_result = array('error' => 0, 'message' => '', 'content' => '');
	
	if (empty($order_id)){
    	$arr_result['error'] = 1;
    	$arr_result['message'] = '非法操作！';
    	die(json_encode($arr_result));
	}
	
	// 主订单信息 
	$orderInfo = findData('order_info',"order_id=$order_id");
	$order = current($orderInfo);
	
	if (empty($str_password)){
		$arr_result['error'] = 1;
		$arr_result['message'] = '卡密码不能为空';
		die(json_encode($arr_result));
	}
	
	if ($order['order_amount'] > $_SESSION['BalanceCash']){
		$arr_result['error'] = 1;
		$arr_result['message'] = '卡余额不足';
		die(json_encode($arr_result));
	} 

	// 得到订单的所有支付日志
	$orderList = findData('order_info',"parent_order_id = '".$order['order_id']."'");	
	// 如果没有子订单，就把主订单加入到 $orderList 中
	if ( empty($orderList))
	{
	    $orderList = $orderInfo;
	}
	
	
	// 支付状态，如果有三个子订单的话，需要单独去支付，但在支付的时候，有个别订单已经支付过了，就不在支付，
	// 将支付过的订单号，放入is_pay 中，
	// 当 is_pay 数量等于订单数量的时候，说明次订单已经全都付完款了，返回提示给用户。
	$is_pay = array();
	
	foreach ($orderList as $key=>$value)
	{
	    $arr_payLog = $db->getRow('SELECT * FROM '.$ecs->table('pay_log')." WHERE order_id = '".$value['order_id']."'");
	    // 如果支付过了，就跳过这笔支付
	    if (!empty($arr_payLog['is_paid'])){
	        $is_pay[] = $arr_payLog['order_id'];
	        continue;
	    }
	    // 如果订单状态是无效，终止付款操作
	    if ($arr_payLog['order_status'] == 2)
	    {
	        $arr_result['error']   = 1;
	        $arr_result['message'] = '订单无效，不可支付！';
	        die(json_encode($arr_result));
	    }
	    
	    // 支付总金额
	    $order_amount = $arr_payLog['order_amount'];
	    
	    // 供应商名称
	    $supplierName = $db->getOne('SELECT supplier_name FROM '.$ecs->table('supplier')." WHERE supplier_id = '".$value['supplier_id']."'");
	    /** TODO 订单支付，双卡版 */
	    $arr_param = array(
	        'CardInfo' => array( 'CardNo'=> $_SESSION['user_name'], 'CardPwd' => $str_password),
	        'TransationInfo' => array( 'TransRequestPoints'=>1, 'TransSupplier'=>setCharset($supplierName))
	    );
	    
	    //if (true)
	    if ($cardPay->action($arr_param, 1, $value['order_sn']) == 0)
	    {
	        //支付成功修改订单状态
	        $cardResult = $cardPay->getResult();
	        if ($cardPay->getCardType() == 1)
	            $api_order_id = $cardResult;
	        else 
	            $api_order_id = 0;
	        
	        order_paid($arr_payLog['log_id'], 2, '','', $api_order_id);
	    }
	    
	    // 卡系统消费失败，返回错误消息，一般是密码错误
	    else {
	        $arr_result['error']   = 1;
	        $arr_result['message'] = $cardPay->getMessage();
	        die(json_encode($arr_result));
	    }
	}
	
	// 如果已经支付订单数量 = 订单总数量，返回提示信息
	if (count($is_pay) == count($orderList))
	{
	    $arr_result['error'] = 1;
	    $arr_result['message'] = '订单已经支付，请不要重复支付';
	    die(json_encode($arr_result));
	}
	
	//重新计算用户卡余额
	$_SESSION['BalanceCash'] -= $order['order_amount']; 	
	$db->query('UPDATE '.$ecs->table('users')." SET card_money = card_money - ('$order[order_amount]') WHERE user_id = '".intval($_SESSION['user_id'])."'");
		
	die(json_encode($arr_result));
}

/*------------------------------------------------------ */
//-- 更新购物车
/*------------------------------------------------------ */

elseif ($_REQUEST['step'] == 'update_cart')
{
    if (isset($_POST['goods_number']) && is_array($_POST['goods_number']))
    {
        flow_update_cart($_POST['goods_number']);
    }

    //show_message($_LANG['update_cart_notice'], $_LANG['back_to_cart'], 'flow.php');
	ecs_header("Location: flow.php\n");
    exit;
}

/*------------------------------------------------------ */
//-- 删除购物车中的商品
/*------------------------------------------------------ */

elseif ($_REQUEST['step'] == 'drop_goods')
{
    $rec_id = intval($_GET['id']);
    flow_drop_cart_goods($rec_id);

    ecs_header("Location: flow.php\n");
    exit;
}

/* 把优惠活动加入购物车 */
elseif ($_REQUEST['step'] == 'add_favourable')
{
    /* 取得优惠活动信息 */
    $act_id = intval($_POST['act_id']);
    $favourable = favourable_info($act_id);
    if (empty($favourable))
    {
        show_message($_LANG['favourable_not_exist']);
    }

    /* 判断用户能否享受该优惠 */
    if (!favourable_available($favourable))
    {
        show_message($_LANG['favourable_not_available']);
    }

    /* 检查购物车中是否已有该优惠 */
    $cart_favourable = cart_favourable();
    if (favourable_used($favourable, $cart_favourable))
    {
        show_message($_LANG['favourable_used']);
    }

    /* 赠品（特惠品）优惠 */
    if ($favourable['act_type'] == FAT_GOODS)
    {
        /* 检查是否选择了赠品 */
        if (empty($_POST['gift']))
        {
            show_message($_LANG['pls_select_gift']);
        }

        /* 检查是否已在购物车 */
        $sql = "SELECT goods_name" .
                " FROM " . $ecs->table('cart') .
                " WHERE session_id = '" . SESS_ID . "'" .
                " AND rec_type = '" . CART_GENERAL_GOODS . "'" .
                " AND is_gift = '$act_id'" .
                " AND goods_id " . db_create_in($_POST['gift']);
        $gift_name = $db->getCol($sql);
        if (!empty($gift_name))
        {
            show_message(sprintf($_LANG['gift_in_cart'], join(',', $gift_name)));
        }

        
        /* 检查数量是否超过上限 */
        $count = isset($cart_favourable[$act_id]) ? $cart_favourable[$act_id] : 0;
        if ($favourable['act_type_ext'] > 0 && $count + count($_POST['gift']) > $favourable['act_type_ext'])
        {
            show_message($_LANG['gift_count_exceed']);
        }

        /* 添加赠品到购物车 */
        foreach ($favourable['gift'] as $gift)
        {
            if (in_array($gift['id'], $_POST['gift']))
            {
                add_gift_to_cart($act_id, $gift['id'], $gift['price']);
            }
        }
    }
    elseif ($favourable['act_type'] == FAT_DISCOUNT)
    {
        add_favourable_to_cart($act_id, $favourable['act_name'], cart_favourable_amount($favourable) * (100 - $favourable['act_type_ext']) / 100);
    }
    elseif ($favourable['act_type'] == FAT_PRICE)
    {
        add_favourable_to_cart($act_id, $favourable['act_name'], $favourable['act_type_ext']);
    }

    /* 刷新购物车 */
    ecs_header("Location: flow.php\n");
    exit;
}
elseif ($_REQUEST['step'] == 'clear')
{
    $sql = "DELETE FROM " . $ecs->table('cart') . " WHERE session_id='" . SESS_ID . "'";
    $db->query($sql);

    ecs_header("Location:./\n");
}
elseif ($_REQUEST['step'] == 'drop_to_collect')
{
    if ($_SESSION['user_id'] > 0)
    {
        $rec_id = intval($_GET['id']);
        $goods_id = $db->getOne("SELECT  goods_id FROM " .$ecs->table('cart'). " WHERE rec_id = '$rec_id' AND session_id = '" . SESS_ID . "' ");
        $count = $db->getOne("SELECT goods_id FROM " . $ecs->table('collect_goods') . " WHERE user_id = '$_SESSION[user_id]' AND goods_id = '$goods_id'");
        if (empty($count))
        {
            $time = gmtime();
            $sql = "INSERT INTO " .$GLOBALS['ecs']->table('collect_goods'). " (user_id, goods_id, add_time)" .
                    "VALUES ('$_SESSION[user_id]', '$goods_id', '$time')";
            $db->query($sql);
        }
        flow_drop_cart_goods($rec_id);
    }
    ecs_header("Location: flow.php\n");
    exit;
}

/* 验证红包序列号 */
elseif ($_REQUEST['step'] == 'validate_bonus')
{
    $bonus_sn = trim($_REQUEST['bonus_sn']);
    if (is_numeric($bonus_sn))
    {
        $bonus = bonus_info(0, $bonus_sn);
    }
    else
    {
        $bonus = array();
    }

//    if (empty($bonus) || $bonus['user_id'] > 0 || $bonus['order_id'] > 0)
//    {
//        die($_LANG['bonus_sn_error']);
//    }
//    if ($bonus['min_goods_amount'] > cart_amount())
//    {
//        die(sprintf($_LANG['bonus_min_amount_error'], price_format($bonus['min_goods_amount'], false)));
//    }
//    die(sprintf($_LANG['bonus_is_ok'], price_format($bonus['type_money'], false)));
    $bonus_kill = price_format($bonus['type_money'], false);

    include_once('includes/cls_json.php');
    $result = array('error' => '', 'content' => '');

    /* 取得购物类型 */
    $flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;

    /* 获得收货人信息 */
    $consignee = get_consignee($_SESSION['user_id']);

    /* 对商品信息赋值 */
    $cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计

    if (empty($cart_goods) || !check_consignee_info($consignee, $flow_type))
    {
        $result['error'] = $_LANG['no_goods_in_cart'];
    }
    else
    {
        /* 取得购物流程设置 */
        $smarty->assign('config', $_CFG);

        /* 取得订单信息 */
        $order = flow_order_info();


        if (((!empty($bonus) && $bonus['user_id'] == $_SESSION['user_id']) || ($bonus['type_money'] > 0 && empty($bonus['user_id']))) && $bonus['order_id'] <= 0)
        {
            //$order['bonus_kill'] = $bonus['type_money'];
            $now = gmtime();
            if ($now > $bonus['use_end_date'])
            {
                $order['bonus_id'] = '';
                $result['error']=$_LANG['bonus_use_expire'];
            }
            else
            {
                $order['bonus_id'] = $bonus['bonus_id'];
                $order['bonus_sn'] = $bonus_sn;
            }
        }
        else
        {
            //$order['bonus_kill'] = 0;
            $order['bonus_id'] = '';
			$order['bonus_sn'] = '';
            $result['error'] = $_LANG['invalid_bonus'];
        }

        /* 计算订单的费用 */
        $total = order_fee($order, $cart_goods, $consignee);

        if($total['goods_price']<$bonus['min_goods_amount'])
        {
         $order['bonus_id'] = '';
         /* 重新计算订单 */
         $total = order_fee($order, $cart_goods, $consignee);
         $result['error'] = sprintf($_LANG['bonus_min_amount_error'], price_format($bonus['min_goods_amount'], false));
        }

        $smarty->assign('total', $total);

        /* 团购标志 */
        if ($flow_type == CART_GROUP_BUY_GOODS)
        {
            $smarty->assign('is_group_buy', 1);
        }

        $result['content'] = $smarty->fetch('library/order_total.lbi');
    }
    $json = new JSON();
    die($json->encode($result));
}
/*------------------------------------------------------ */
//-- 添加礼包到购物车
/*------------------------------------------------------ */
elseif ($_REQUEST['step'] == 'add_package_to_cart')
{
    include_once('includes/cls_json.php');
    $_POST['package_info'] = json_str_iconv($_POST['package_info']);

    $result = array('error' => 0, 'message' => '', 'content' => '', 'package_id' => '');
    $json  = new JSON;

    if (empty($_POST['package_info']))
    {
        $result['error'] = 1;
        die($json->encode($result));
    }

    $package = $json->decode($_POST['package_info']);

    /* 如果是一步购物，先清空购物车 */
    if ($_CFG['one_step_buy'] == '1')
    {
        clear_cart();
    }

    /* 商品数量是否合法 */
    if (!is_numeric($package->number) || intval($package->number) <= 0)
    {
        $result['error']   = 1;
        $result['message'] = $_LANG['invalid_number'];
    }
    else
    {
        /* 添加到购物车 */
        if (add_package_to_cart($package->package_id, $package->number))
        {
            if ($_CFG['cart_confirm'] > 2)
            {
                $result['message'] = '';
            }
            else
            {
                $result['message'] = $_CFG['cart_confirm'] == 1 ? $_LANG['addto_cart_success_1'] : $_LANG['addto_cart_success_2'];
            }

            $result['content'] = insert_cart_info();
            $result['one_step_buy'] = $_CFG['one_step_buy'];
        }
        else
        {
            $result['message']    = $err->last_message();
            $result['error']      = $err->error_no;
            $result['package_id'] = stripslashes($package->package_id);
        }
    }
    $result['confirm_type'] = !empty($_CFG['cart_confirm']) ? $_CFG['cart_confirm'] : 2;
    die($json->encode($result));
}

/*------------------------------------------------------ */
//-- 删除购物车中选中的商品
/*------------------------------------------------------ */
elseif($_REQUEST['step'] == 'drop_allgoods'){
	foreach($_POST['sel_cartgoods'] as $rec_id){
		flow_drop_cart_goods($rec_id);
	}
	ecs_header("Location: flow.php\n");
	exit;
}

//修改购买商品数量
else if ($_REQUEST['step'] == 'attr_goods_num'){
	include('includes/cls_json.php');
	$json   = new JSON;
	$rec_id = (isset($_REQUEST['rec_id'])) ? $_REQUEST['rec_id'] : 0;
	$number = (isset($_REQUEST['number'])) ? intval($_REQUEST['number']) : 1;
	$result = ajax_flow_update_cart($rec_id, $number);//下面自己写的
	$sql = "SELECT `goods_price`, `goods_number` FROM" .$GLOBALS['ecs']->table('cart').
	" WHERE rec_id='$rec_id' AND session_id='" . SESS_ID . "'";
	$goods = $GLOBALS['db']->getRow($sql);

	$count_cart = $db->getOne('SELECT SUM(goods_number) FROM '.$ecs->table('cart')." WHERE session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GENERAL_GOODS . "'");
	$result['count_cart'] = $count_cart;

	$result['subtotal'] = price_format($goods['goods_price'] * $goods['goods_number'], false);
	$cart_goods = get_cart_goods();
	$result['shopping_money'] = $cart_goods['total']['goods_price'];
	$result['rec_id'] = $rec_id;
	$result['number'] = $number;
	die($json->encode($result));
}


//收货人信息ajax操作
//删除收货人信息
elseif ($_REQUEST['step'] == 'consignee_ajax_drop'){
	include_once('includes/cls_json.php');
	include_once(ROOT_PATH . 'includes/lib_transaction.php');
	$address_id = $_REQUEST['address_id']?$_REQUEST['address_id']:0;
	$result = array();
	$result['id'] = $address_id;
	if($address_id){
		$result['check'] = drop_consignee($address_id);
	}
	$json = new JSON();
	die($json->encode($result));
}
//编辑收货人信息
elseif ($_REQUEST['step'] == 'consignee_ajax_edit'){
	include_once('includes/cls_json.php');
	include_once(ROOT_PATH . 'includes/lib_transaction.php');
	$address_id = $_REQUEST['address_id'] ? $_REQUEST['address_id'] : 0;
	$from = $_REQUEST['from'] ? $_REQUEST['from'] : 'flow';

	/* 获得用户所有的收货人信息 */
	$consignee_list = get_consignee_list($_SESSION['user_id']);
	if($consignee_list){
		$consignees = $consignee_list[$address_id];
		$smarty->assign('province_list',       get_regions(1, $consignees['country']));
		$smarty->assign('city_list',           get_regions(2, $consignees['province']));
		$smarty->assign('district_list',       get_regions(3, $consignees['city']));
		$smarty->assign('consignee_list', $consignee_list);
	}
	$consigness['country'] = $int_cityId;
	if(empty($address_id)){
		$smarty->assign('province_list',    get_regions(1, $_CFG['shop_country']));
	}

	$smarty->assign('consignees', $consignees);
	$smarty->assign('address_id', $address_id);

	$smarty->assign('shop_country',       $int_cityId);
	
	if ($from == 'user'){
		$result['content'] = $smarty->fetch('library/user_consignee.lbi');
	}else{
		$result['content'] = $smarty->fetch('library/consignee.lbi');
	}
	$json = new JSON();
	die($json->encode($result));
}
//更新收货人信息
elseif ($_REQUEST['step'] == 'consignee_ajax_update'){
	$check = $_REQUEST['check'] ? $_REQUEST['check'] : 0;
	$from  = $_REQUEST['from'] ? $_REQUEST['from'] : 'flow';//来自哪里的提交，flow默认订单页，dealers为推广商添加订单页
	$address_id = $db->getOne("SELECT address_id FROM ".$ecs->table('users')." WHERE user_id = '$_SESSION[user_id]'");
	$address_id = $_REQUEST['address_id'] ? $_REQUEST['address_id'] : $address_id;
	include_once('includes/cls_json.php');
	if ($from == 'dealers'){
		$consignee = get_consignee(0);//获取默认收货人信息
	}else{
		//$consignee = $_SESSION['flow_consignee'] ? $_SESSION['flow_consignee'] : get_consignee($_SESSION['user_id']);
		$consignee = get_consignee($_SESSION['user_id']);
	}

	if($check == 1 && !empty($consignee)){
		$consignee['country_cn']  = get_add_cn($consignee['country']);
		$consignee['province_cn'] = get_add_cn($consignee['province']);
		$consignee['city_cn']     = get_add_cn($consignee['city']);
		$consignee['district_cn'] = get_add_cn($consignee['district']);
		$smarty->assign('consignee', $consignee);

		$result['content'] = $from == 'dealers' ? $smarty->fetch('library/dealers_my_consignee.lbi') : $smarty->fetch('library/my_consignee.lbi');
	}else{
		include_once('includes/lib_transaction.php');
		//取得国家列表、商店所在国家、商店所在国家的省列表
		$smarty->assign('shop_country',       $_CFG['shop_country']);
		$smarty->assign('country_list',       get_regions());
		$smarty->assign('province_list',      get_regions(1, $int_cityId));

		//获得用户所有的收货人信息
		$consignee_list = get_consignee_list($_SESSION['user_id']);
		$smarty->assign('consignee_list', $consignee_list);
		
		
		//获取用户默认收货信息
		//$consigness = $_SESSION['flow_consignee'] ? $_SESSION['flow_consignee'] : get_consignee($_SESSION['user_id']);
		if($consignee){
			$smarty->assign('province_list',       get_regions(1, $consignee['country']));
			$smarty->assign('city_list',           get_regions(2, $consignee['province']));
			$smarty->assign('district_list',       get_regions(3, $consignee['city']));
		}else{
			$mobile = $db->getOne('select mobile_phone from '.$ecs->table('users')." where user_id = '".$_SESSION['user_id']."'");
			$smarty->assign('mobile', $mobile);
		}
		$smarty->assign('consignees', $consignee);
		$smarty->assign('address_id', $address_id);
		
		$result['content'] = $from == 'dealers' ? $smarty->fetch('library/dealers_consignee.lbi') : $smarty->fetch('library/consignee.lbi');
	}
	$json = new JSON();
	die($json->encode($result));

}
//保存收货人信息
elseif ($_REQUEST['step'] == 'consignee_ajax_save'){
	$address_id = $_REQUEST['address_id'] ? $_REQUEST['address_id'] : 0;
	$from = $_REQUEST['from'] ? $_REQUEST['from'] : 'flow';//来自哪里的提交，flow默认订单页，dealers为推广商添加订单页

	include_once('includes/cls_json.php');
	include_once('includes/lib_transaction.php');
	$json = new JSON();
	/* 保存为用户的默认收货地址 */
	if (!empty($address_id)){
		$address_id = (int)$db->getOne("SELECT address_id FROM ".$ecs->table('user_address')." WHERE address_id = '$address_id' AND user_id = '".$_SESSION['user_id']."'");
	}
	$consignee = array(
		'address_id'    => $address_id,
		'user_id'       => $_SESSION['user_id'],
		'consignee'     => empty($_REQUEST['consignee'])  ? '' : trim($_REQUEST['consignee']),
		'country'       => empty($_REQUEST['country'])    ? '' : $_REQUEST['country'],
		'province'      => empty($_REQUEST['province'])   ? '' : $_REQUEST['province'],
		'city'          => empty($_REQUEST['city'])       ? '' : $_REQUEST['city'],
		'district'      => empty($_REQUEST['district'])   ? '' : $_REQUEST['district'],
		'address'       => empty($_REQUEST['address'])    ? '' : $_REQUEST['address'],
		'tel'           => empty($_REQUEST['tel'])        ? '' : make_semiangle(trim($_REQUEST['tel'])),
		'mobile'        => empty($_REQUEST['mobile'])     ? '' : make_semiangle(trim($_REQUEST['mobile'])),
		'email'         => empty($_REQUEST['email'])     ? '' : trim($_REQUEST['email'])
	);
	if (!empty($_SESSION['user_id']) && $from != 'dealers'){
		$address_id = update_address($consignee);
		$db->query("UPDATE " . $GLOBALS['ecs']->table('users') ." SET address_id = '$address_id' WHERE user_id = '$_SESSION[user_id]'");
		if($address_id){
			$_SESSION['flow_consignee'] = stripslashes_deep($consignee);
			$result['result'] = 'true';
		}else{
			$result['result'] = 'false';
		}
	}else{
		/* 保存到session */
		$_SESSION['flow_consignee'] = stripslashes_deep($consignee);
		$result['result'] = 'true';
	}
	
	$result['from'] = $from;
	die($json->encode($result));
}

//支付及配送ajax操作
elseif ($_REQUEST['step'] == 'payshipping_ajax_save'){
	include_once('includes/cls_json.php');
	$json = new JSON;
	$result = array('content' => '', 'type' => 0);
	$from = $_REQUEST['from'] ? $_REQUEST['from'] : 'flow';//来自哪里的提交，flow默认订单页，dealers为推广商添加订单页

	/* 取得订单信息 */
	//$order = flow_order_info();
	$order['pay_id']      = intval($_REQUEST['payment']);
	$order['shipping_id'] = intval($_REQUEST['shipping']);
	/* 保存 session */
	$_SESSION['flow_order'] = $order;
	/* 取得订单信息 */
	$order = flow_order_info();
	$consignee = get_consignee($_SESSION['user_id']);
	$result['type'] = 2;
	if ($order['pay_id'] && $order['shipping_id']){
		$result['address_id'] = $consignee['address_id'];
		$smarty->assign('payment_info',		payment_info($order['pay_id']));
        $shipping_info = shipping_info($order['shipping_id']);
        if($shipping_info['shipping_name'] == '申通快递')
            $shipping_info['shipping_name'] = '普通快递';
		$result['content'] = $from == 'dealers' ? $smarty->fetch('library/dealers_my_payshipping.lbi') : $smarty->fetch('library/my_payshipping.lbi');
	}else{
		$result['content'] = $from == 'dealers' ? $smarty->fetch('library/dealers_payshipping.lbi') : $smarty->fetch('library/payshipping.lbi');
	}
	die($json->encode($result));
}
//支付及配送方式修改
elseif ($_REQUEST['step'] == 'payshipping_ajax_update'){
	include_once('includes/cls_json.php');
	$json = new JSON;
	$result = array('content' => '');
	$check = $_REQUEST['check'] ? $_REQUEST['check'] : 0;
	$type  = $_REQUEST['type']  ? $_REQUEST['type']  : 0;//0:自身修改，1:修改收货人信息时重新修改支付及配送方式，2:保存支付及配送方式时重新刷新页面
	$from  = $_REQUEST['from'] ? $_REQUEST['from'] : 'flow';//来自哪里的提交，flow默认订单页，dealers为推广商添加订单页
	if($check == 1){
		/* 取得订单信息 */
		$order = flow_order_info();
		$smarty->assign('payment_info',		payment_info($order['pay_id']));
		$smarty->assign('shipping_info',	shipping_info($order['shipping_id']));
		$result['content'] = $from == 'dealers' ? $smarty->fetch('library/dealers_my_payshipping.lbi') : $smarty->fetch('library/my_payshipping.lbi');
	}else{
		$consignee = get_consignee($_SESSION['user_id']);
		/* 对商品信息赋值 */
		$cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计
		/*
		* 取得购物流程设置
		*/
		$smarty->assign('config', $_CFG);
		/*
		 * 取得订单信息
		 */
		$order = flow_order_info();
		$smarty->assign('order', $order);

		/* 取得配送列表 */
		$region            = array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district']);
		$shipping_list     = available_shipping_list($region);
        foreach($shipping_list as $k => $shipping){
            if($shipping['shipping_name'] == '申通快递')
                $shipping_list[$k]['shipping_name'] = '普通快递';
        }
		$cart_weight_price = cart_weight_price($flow_type);
		$insure_disabled   = true;
		$cod_disabled      = true;

		/*
		 * 计算订单的费用
		 */
		$total = order_fee($order, $cart_goods, $consignee);
		$smarty->assign('total', $total);
		// 查看购物车中是否全为免运费商品，若是则把运费赋为零
		$sql = 'SELECT count(*) FROM ' . $ecs->table('cart') . " WHERE `session_id` = '" . SESS_ID. "' AND `extension_code` != 'package_buy' AND `is_shipping` = 0";
		$shipping_count = $db->getOne($sql);
		foreach ($shipping_list AS $key => $val){
			$shipping_cfg = unserialize_config($val['configure']);
			$shipping_fee = ($shipping_count == 0 AND $cart_weight_price['free_shipping'] == 1) ? 0 : shipping_fee($val['shipping_code'], unserialize($val['configure']),
			$cart_weight_price['weight'], $cart_weight_price['amount'], $cart_weight_price['number']);

			$shipping_list[$key]['format_shipping_fee'] = price_format($shipping_fee, false);
			$shipping_list[$key]['shipping_fee']        = $shipping_fee;
			$shipping_list[$key]['free_money']          = price_format($shipping_cfg['free_money'], false);
			$shipping_list[$key]['insure_formated']     = strpos($val['insure'], '%') === false ?
				price_format($val['insure'], false) : $val['insure'];

			/* 当前的配送方式是否支持保价 */
			if ($val['shipping_id'] == $order['shipping_id'])
			{
				$insure_disabled = ($val['insure'] == 0);
				$cod_disabled    = ($val['support_cod'] == 0);
			}
		}

		$smarty->assign('shipping_list',   $shipping_list);
		$smarty->assign('insure_disabled', $insure_disabled);
		$smarty->assign('cod_disabled',    $cod_disabled);

		/* 取得支付列表 */
		if ($order['shipping_id'] == 0){
			$cod        = true;
			$cod_fee    = 0;
		}else{
			$shipping = shipping_info($order['shipping_id']);
			$cod = $shipping['support_cod'];

			if ($cod){
				/* 如果是团购，且保证金大于0，不能使用货到付款 */
				if ($flow_type == CART_GROUP_BUY_GOODS){
					$group_buy_id = $_SESSION['extension_id'];
					if ($group_buy_id <= 0){
						show_message('error group_buy_id');
					}
					$group_buy = group_buy_info($group_buy_id);
					if (empty($group_buy)){
						show_message('group buy not exists: ' . $group_buy_id);
					}

					if ($group_buy['deposit'] > 0){
						$cod = false;
						$cod_fee = 0;

						/* 赋值保证金 */
						$smarty->assign('gb_deposit', $group_buy['deposit']);
					}
				}

				if ($cod){
					$shipping_area_info = shipping_area_info($order['shipping_id'], $region);
					$cod_fee            = $shipping_area_info['pay_fee'];
				}
			}else{
				$cod_fee = 0;
			}
		}

		// 给货到付款的手续费加<span id>，以便改变配送的时候动态显示
		$payment_list = available_payment_list(1, $cod_fee);
		if(isset($payment_list)){
			foreach ($payment_list as $key => $payment){
				if ($payment['is_cod'] == '1'){
					$payment_list[$key]['format_pay_fee'] = '<span id="ECS_CODFEE">' . $payment['format_pay_fee'] . '</span>';
				}
				/* 如果有易宝神州行支付 如果订单金额大于300 则不显示 */
				if ($payment['pay_code'] == 'yeepayszx' && $total['amount'] > 300){
					unset($payment_list[$key]);
				}
				/* 如果有余额支付 */
				if ($payment['pay_code'] == 'balance'){
					/* 如果未登录，不显示 */
					if ($_SESSION['user_id'] == 0){
						unset($payment_list[$key]);
					}else{
						if ($_SESSION['flow_order']['pay_id'] == $payment['pay_id']){
							$smarty->assign('disable_surplus', 1);
						}
					}
				}
			}
		}
		$smarty->assign('type', $type);
		$smarty->assign('payment_list', $payment_list);
		$result['type'] = $type;
		$result['content'] = $from == 'dealers' ? $smarty->fetch('library/dealers_payshipping.lbi') : $smarty->fetch('library/payshipping.lbi');
	}
	die($json->encode($result));
}

//保存常用发票
elseif ($_REQUEST['step'] == 'inv_ajax_save'){
	include_once('includes/cls_json.php');

	$inv_id = isset($_REQUEST['inv_id']) ? intval($_REQUEST['inv_id']) : 0;
	$from   = $_GET['from'] ? $_GET['from'] : 'flow';

	$invs = array(
		'inv_type'     => empty($_REQUEST['inv_type'])    ? '' : trim($_REQUEST['inv_type']),
		'inv_content'  => empty($_REQUEST['inv_content']) ? '' : trim($_REQUEST['inv_content']),
		'inv_payee'    => empty($_REQUEST['inv_payee'])   ? '' : trim($_REQUEST['inv_payee']),
		'inv_tax'      => empty($_REQUEST['inv_tax'])     ? '' : trim($_REQUEST['inv_tax'])
	);

	/*if ($_SESSION['user_id'] > 0 && $from != 'dealers'){
		$invs['user_id'] = $_SESSION['user_id'];
		if (!empty($inv_id)){
			$invs['inv_id'] = $inv_id;
			$inv_id = update_inv($invs);
		}else{
			$inv_id = save_inv($invs);
		}
	}*/

	setcookie('ECS[flow_inv][inv_type]', $invs['inv_type'], gmtime() + 3600 * 24 * 30);
	setcookie('ECS[flow_inv][inv_content]', $invs['inv_content'], gmtime() + 3600 * 24 * 30);
	setcookie('ECS[flow_inv][inv_payee]', $invs['inv_payee'], gmtime() + 3600 * 24 * 30);
	setcookie('ECS[flow_inv][inv_tax]', $invs['inv_tax'], gmtime() + 3600 * 24 * 30);
	$_SESSION['flow_inv'] = stripslashes_deep($invs);//把发票信息存入session

	$smarty->assign('invs', $invs);

	//取得购物类型
	$flow_type = isset($_SESSION['flow_type']) ? intval($_SESSION['flow_type']) : CART_GENERAL_GOODS;
	//获得收货人信息
	$consignee = get_consignee($_SESSION['user_id']);
	//对商品信息赋值
	$cart_goods = cart_goods($flow_type); // 取得商品列表，计算合计
	if (empty($cart_goods) || !check_consignee_info($consignee, $flow_type)){
		$result['error'] = $_LANG['no_goods_in_cart'];
		die($json->encode($result));
	}else{
		//取得购物流程设置
		$smarty->assign('config', $_CFG);

		//取得订单信息
		$order = flow_order_info();
		
		if ($invs['inv_type'] && $invs['inv_content']){
			$order['need_inv']    = 1;
			$order['inv_type']    = trim(stripslashes($invs['inv_type']));
			$order['inv_payee']   = trim(stripslashes($invs['inv_payee']));
			$order['inv_content'] = trim(stripslashes($invs['inv_content']));
		}else{
			$order['need_inv']    = 0;
			$order['inv_type']    = '';
			$order['inv_payee']   = '';
			$order['inv_content'] = '';
		}

		//计算订单的费用
		$total = order_fee($order, $cart_goods, $consignee);
		$smarty->assign('total', $total);
		$smarty->assign('order', $order);

		//团购标志
		if ($flow_type == CART_GROUP_BUY_GOODS){
			$smarty->assign('is_group_buy', 1);
		}
		$result['inv']     = $smarty->fetch('library/my_inv.lbi');
		$result['content'] = $smarty->fetch('library/order_total.lbi');
	}

	$json = new JSON();
	die($json->encode($result));
}
//修改常用发票
elseif ($_REQUEST['step'] == 'inv_ajax_update'){
	include_once('includes/cls_json.php');

	$result = array('content' => '');
	$check  = $_REQUEST['check'] ? $_REQUEST['check'] : 0;
	
	
	$invs = array();
	if ($type != 'new'){
		$invs = $_COOKIE['ECS']['flow_inv'];//用户默认发票信息
	}
	$smarty->assign('invs', $invs);

	$inv_content_list = explode("\n", str_replace("\r", '', $_CFG['invoice_content']));
	$smarty->assign('inv_content_list', $inv_content_list);
	$inv_type_list = array();
	foreach ($_CFG['invoice_type']['type'] as $key => $type){
		if (!empty($type)){
			$inv_type_list[$type] = floatval($_CFG['invoice_type']['rate'][$key]);
		}
	}
	$smarty->assign('inv_type_list', $inv_type_list);
	if ($from == 'user'){
		$result['content'] = $smarty->fetch('library/user_inv.lbi');
	}else{
		$result['content'] = $smarty->fetch('library/inv.lbi');
	}
		


	$json = new JSON();
	die($json->encode($result));
}

//根据指定日期获取时间段
else if ($_REQUEST['step'] == 'getTime'){
	$str_date = $_REQUEST['date'];
	$arr_result = array('error'=>0, 'message'=>'', 'content'=>'');
	
	$arr_times = array();
	if ($str_date == local_date('Y-m-d')){
		$int_hours   = local_date('G');//当前是几点
		$int_minutes = local_date('m');//当前分钟
		if ($int_hours > 21){
		$int_startTime = 17;
		}else if($int_hours < 9 ){
		 $int_startTime = 17;
		}else{
		$int_startTime = $int_hours + 8;
		}		
		//$int_startTime = $int_startTime < 9 ? 9 : $int_startTime;
		$int_endTime   = 22;

		if ($int_startTime >= $int_endTime){
			$arr_result['error']   = 1;
			$arr_result['message'] = '抱歉，今天已经打烊了，请选择明天或其他日期';
		}

		$int_index = 0;
		for ($i = $int_startTime; $i < $int_endTime; $i++){
			$count = $i == 9 ? 1 : 2;
			if ($i == 9){
				if ($int_hours + 6 < $int_startTime){
					$arr_times[$int_index] = sprintf("%02d", $i).':30-'.sprintf("%02d", $i+1).':00';
					$int_index++;
				}
			}else{
				for ($j = 0; $j < $count; $j++){
					if ($int_index == 0){
						if ($int_hours + 6 < $int_startTime){
							$arr_times[$int_index] = $j == 0 ? sprintf("%02d", $i).':00-'.sprintf("%02d", $i).':30' : sprintf("%02d", $i).':30-'.sprintf("%02d", $i+1).':00';
						}
					}else{
						$arr_times[$int_index] = $j == 0 ? sprintf("%02d", $i).':00-'.sprintf("%02d", $i).':30' : sprintf("%02d", $i).':30-'.sprintf("%02d", $i+1).':00';
					}
					$int_index++;
				}
			}
		}
	}else{
		$int_startTime = 9;
		$int_endTime   = 22;

		$int_index = 0;
		for ($i = $int_startTime; $i < $int_endTime; $i++){
			$count = $i == 9 ? 1 : 2;
			if ($i == 9){
				if ($int_hours + 8 < $int_startTime){
					$arr_times[$int_index] = sprintf("%02d", $i).':30-'.sprintf("%02d", $i+1).':00';
					$int_index++;
				}
			}else{
				for ($j = 0; $j < $count; $j++){
					if ($int_index == 0){
						if ($int_hours + 8 < $int_startTime){
							$arr_times[$int_index] = $j == 0 ? sprintf("%02d", $i).':00-'.sprintf("%02d", $i).':30' : sprintf("%02d", $i).':30-'.sprintf("%02d", $i+1).':00';
						}
					}else{
						$arr_times[$int_index] = $j == 0 ? sprintf("%02d", $i).':00-'.sprintf("%02d", $i).':30' : sprintf("%02d", $i).':30-'.sprintf("%02d", $i+1).':00';
					}
					$int_index++;
				}
			}
		}
	}
	sort($arr_times);
	$arr_result['content'] = $arr_times;
	die(json_encode($arr_result));
}

/**
 * TODO 供应商配送时间
 *
 * 思路：
 *
 * 		根据供应商的配送地区，找到他的配送的最早时间和最晚时间。
 *
 * 		在得到当前的下单时间（小时）+6，如果在配送时间范围内，显示6小时后的配送时间，不在则显示不在配送时间范围内，
 *
 * 		可以选择明天的配送时间。
 *
 * 		当前时间 + 6 小时，这个6小时是后台给供应商配送城市的下单到配送时间。
 */
else if($_REQUEST['step'] == 'shippingTime')
{
    // 时间
    $str_date = $_REQUEST['date'];
    // 供应商id
    $supplier_id = isset($_REQUEST['sid']) ? intval($_REQUEST['sid']) : 0 ;
    // 最早配送时间
    $shipping_start = isset($_REQUEST['shipping_start']) ? trim($_REQUEST['shipping_start']) : 0 ;
    // 最晚配送时间
    $shipping_end = isset($_REQUEST['shipping_end']) ? trim($_REQUEST['shipping_end']) : 0 ;
    // 提前预订时间
    $shipping_waiting = isset($_REQUEST['shipping_waiting']) ? trim($_REQUEST['shipping_waiting']) : 0 ;
    // 间隔时间段
    $shipping_booking = isset($_REQUEST['shipping_booking']) ? trim($_REQUEST['shipping_booking']) : 0 ;
    // 返回数据格式
    $arr_result = array('error'=>0,'supplier'=>array(), 'message'=>'', 'content'=>'');
    
    // 配送时间信息、供应商id 不能我空
    if( empty($supplier_id) || empty($shipping_start) || empty($shipping_end) || empty($shipping_waiting) || empty($shipping_booking))
    {
        $arr_result['error'] = 1;
        $arr_result['message'] = '非常规操作！';
        die(json_encode($arr_result));
        exit;
    }
    
    
    $currentTime = strtotime(local_date('Y-m-d'));
    $currentPostTime = strtotime($str_date);
    
    $shipping_times = array(
                            'shipping_start'    =>$shipping_start, 
                            'shipping_end'      =>$shipping_end,
                            'shipping_waiting'  =>$shipping_waiting,
                            'shipping_booking'  =>$shipping_booking
                        );
    
    //日期是今天
    if($currentTime == $currentPostTime)
    {
        $timesArray = getShippingTimes($shipping_times,'today',$supplier_id);
    
    // 明天以后的日期
    }
    else if ($currentPostTime > $currentTime){
        $timesArray = getShippingTimes($shipping_times,'tomorrow',$supplier_id);
    }
    else{
        $arr_result['error'] 	= 1;
        $arr_result['message'] 	= '此时间不能配送，请选择其他时间！';
        die(json_encode($arr_result));
        exit;
    }
    
    
    //是否有可选的配送时间，如果没有，请选择明天
    if(empty($timesArray))
    {
        $arr_result['error'] 	= 1;
        $arr_result['message'] 	= '此时间不能配送，请选择其他时间段！';
        die(json_encode($arr_result));
        exit;
    }
    
    // 处理错误消息
    if($timesArray['error'] == 1)
    {
        $arr_result['error'] 	= 1;
        $arr_result['message'] 	= $timesArray['message'];
        die(json_encode($arr_result));
        exit;
    }
    
    
    //供应商名称   
    $arr_result['content']  = $timesArray;
    die(json_encode($arr_result));
    exit;
}

else
{	
    /* 标记购物流程为普通商品 */
    $_SESSION['flow_type'] = CART_GENERAL_GOODS;
    
    /* 取得商品列表，计算合计 */
    $cart_goods = get_cart_goods();
    $smarty->assign('goods_list', $cart_goods['goods_list']);
    $smarty->assign('total', $cart_goods['total']);
	$smarty->assign('cart_count', $cart_goods['total']['real_goods_count'] + $cart_goods['total']['virtual_goods_count']);

    //购物车的描述的格式化
    $smarty->assign('shopping_money',         sprintf($_LANG['shopping_money'], $cart_goods['total']['goods_price']));
    $smarty->assign('market_price_desc',      sprintf($_LANG['than_market_price'],
        $cart_goods['total']['market_price'], $cart_goods['total']['saving'], $cart_goods['total']['save_rate']));

    // 显示收藏夹内的商品
    if ($_SESSION['user_id'] > 0)
    {
        require_once(ROOT_PATH . 'includes/lib_clips.php');
        $collection_goods = get_collection_goods($_SESSION['user_id']);
        $smarty->assign('collection_goods', $collection_goods);
    }

    /* 取得优惠活动 */
    $favourable_list = favourable_list($_SESSION['user_rank']);
    usort($favourable_list, 'cmp_favourable');

    $smarty->assign('favourable_list', $favourable_list);

    /* 计算折扣 */
    $discount = compute_discount();
    $smarty->assign('discount', $discount['discount']);
    $favour_name = empty($discount['name']) ? '' : join(',', $discount['name']);
    $smarty->assign('your_discount', sprintf($_LANG['your_discount'], $favour_name, price_format($discount['discount'])));

    /* 增加是否在购物车里显示商品图 */
    $smarty->assign('show_goods_thumb', $GLOBALS['_CFG']['show_goods_in_cart']);

    /* 增加是否在购物车里显示商品属性 */
    $smarty->assign('show_goods_attribute', $GLOBALS['_CFG']['show_attr_in_cart']);

    /* 购物车中商品配件列表 */
    //取得购物车中基本件ID
    $sql = "SELECT goods_id " .
            "FROM " . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id = '" . SESS_ID . "' " .
            "AND rec_type = '" . CART_GENERAL_GOODS . "' " .
            "AND is_gift = 0 " .
            "AND extension_code <> 'package_buy' " .
            "AND parent_id = 0 ";
    $parent_list = $GLOBALS['db']->getCol($sql);

    $fittings_list = get_goods_fittings($parent_list);

    $smarty->assign('fittings_list', $fittings_list);
    $smarty->assign('currency_format', $_CFG['currency_format']);
    $smarty->assign('integral_scale',  $_CFG['integral_scale']);
    assign_dynamic('shopping_flow');
    
    $smarty->display('flow/cart.dwt');
}



/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */

/**
 * 获得用户的可用积分
 *
 * @access  private
 * @return  integral
 */
function flow_available_points()
{
    $sql = "SELECT SUM(g.integral * c.goods_number) ".
            "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " . $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE c.session_id = '" . SESS_ID . "' AND c.goods_id = g.goods_id AND c.is_gift = 0 AND g.integral > 0 " .
            "AND c.rec_type = '" . CART_GENERAL_GOODS . "'";

    $val = intval($GLOBALS['db']->getOne($sql));

    return integral_of_value($val);
}

/**
 * 更新购物车中的商品数量
 *
 * @access  public
 * @param   array   $arr
 * @return  void
 */
function flow_update_cart($arr)
{
    /* 处理 */
    foreach ($arr AS $key => $val)
    {
        $val = intval(make_semiangle($val));
        if ($val <= 0 || !is_numeric($key))
        {
            continue;
        }

        //查询：
        $sql = "SELECT `goods_id`, `goods_attr_id`, `product_id`, `extension_code` FROM" .$GLOBALS['ecs']->table('cart').
               " WHERE rec_id='$key' AND session_id='" . SESS_ID . "'";
        $goods = $GLOBALS['db']->getRow($sql);

        $sql = "SELECT g.goods_name, g.goods_number ".
                "FROM " .$GLOBALS['ecs']->table('goods'). " AS g, ".
                    $GLOBALS['ecs']->table('cart'). " AS c ".
                "WHERE g.goods_id = c.goods_id AND c.rec_id = '$key'";
        $row = $GLOBALS['db']->getRow($sql);

        //查询：系统启用了库存，检查输入的商品数量是否有效
        if (intval($GLOBALS['_CFG']['use_storage']) > 0 && $goods['extension_code'] != 'package_buy')
        {
            if ($row['goods_number'] < $val)
            {
                show_message(sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],
                $row['goods_number'], $row['goods_number']));
                exit;
            }
            /* 是货品 */
            $goods['product_id'] = trim($goods['product_id']);
            if (!empty($goods['product_id']))
            {
                $sql = "SELECT product_number FROM " .$GLOBALS['ecs']->table('products'). " WHERE goods_id = '" . $goods['goods_id'] . "' AND product_id = '" . $goods['product_id'] . "'";

                $product_number = $GLOBALS['db']->getOne($sql);
                if ($product_number < $val)
                {
                    show_message(sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],
                    $product_number['product_number'], $product_number['product_number']));
                    exit;
                }
            }
        }
        elseif (intval($GLOBALS['_CFG']['use_storage']) > 0 && $goods['extension_code'] == 'package_buy')
        {
            if (judge_package_stock($goods['goods_id'], $val))
            {
                show_message($GLOBALS['_LANG']['package_stock_insufficiency']);
                exit;
            }
        }

        /* 查询：检查该项是否为基本件 以及是否存在配件 */
        /* 此处配件是指添加商品时附加的并且是设置了优惠价格的配件 此类配件都有parent_id goods_number为1 */
        $sql = "SELECT b.goods_number, b.rec_id
                FROM " .$GLOBALS['ecs']->table('cart') . " a, " .$GLOBALS['ecs']->table('cart') . " b
                WHERE a.rec_id = '$key'
                AND a.session_id = '" . SESS_ID . "'
                AND a.extension_code <> 'package_buy'
                AND b.parent_id = a.goods_id
                AND b.session_id = '" . SESS_ID . "'";

        $offers_accessories_res = $GLOBALS['db']->query($sql);

        //订货数量大于0
        if ($val > 0)
        {
            /* 判断是否为超出数量的优惠价格的配件 删除*/
            $row_num = 1;
            while ($offers_accessories_row = $GLOBALS['db']->fetchRow($offers_accessories_res))
            {
                if ($row_num > $val)
                {
                    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
                            " WHERE session_id = '" . SESS_ID . "' " .
                            "AND rec_id = '" . $offers_accessories_row['rec_id'] ."' LIMIT 1";
                    $GLOBALS['db']->query($sql);
                }

                $row_num ++;
            }

            /* 处理超值礼包 */
            if ($goods['extension_code'] == 'package_buy')
            {
                //更新购物车中的商品数量
                $sql = "UPDATE " .$GLOBALS['ecs']->table('cart').
                        " SET goods_number = '$val' WHERE rec_id='$key' AND session_id='" . SESS_ID . "'";
            }
            /* 处理普通商品或非优惠的配件 */
            else
            {
                $attr_id    = empty($goods['goods_attr_id']) ? array() : explode(',', $goods['goods_attr_id']);
                // TODO 更新购物车数量的同时，更新价格
                $spec_nember = '';
                foreach ($attr_id as $attr_k=>$attr_v)
                {
                	if (strpos($attr_v, 'S_') !==false)
                	{
                		$spec_nember = substr($attr_v, 2);
                	}
                }
                $spec_array 			= array('spec_nember'=> $spec_nember, 'goods_id'=>$goods['goods_id']);
                $spec_price2 			= get_spec_ratio_price($spec_array);
                $shop_price  			= get_final_price($goods['goods_id'], $val, true, $attr_id);
                $goods_price 			= $spec_price2+$shop_price;
                
                //$goods_price = get_final_price($goods['goods_id'], $val, true, $attr_id);

                //更新购物车中的商品数量
                $sql = "UPDATE " .$GLOBALS['ecs']->table('cart').
                        " SET goods_number = '$val', goods_price = '$goods_price' WHERE rec_id='$key' AND session_id='" . SESS_ID . "'";
            }
        }
        //订货数量等于0
        else
        {
            /* 如果是基本件并且有优惠价格的配件则删除优惠价格的配件 */
            while ($offers_accessories_row = $GLOBALS['db']->fetchRow($offers_accessories_res))
            {
                $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
                        " WHERE session_id = '" . SESS_ID . "' " .
                        "AND rec_id = '" . $offers_accessories_row['rec_id'] ."' LIMIT 1";
                $GLOBALS['db']->query($sql);
            }

            $sql = "DELETE FROM " .$GLOBALS['ecs']->table('cart').
                " WHERE rec_id='$key' AND session_id='" .SESS_ID. "'";
        }

        $GLOBALS['db']->query($sql);
    }

    /* 删除所有赠品 */
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') . " WHERE session_id = '" .SESS_ID. "' AND is_gift <> 0";
    $GLOBALS['db']->query($sql);
}

/**
 * 检查订单中商品库存
 *
 * @access  public
 * @param   array   $arr
 *
 * @return  void
 */
function flow_cart_stock($arr)
{
    foreach ($arr AS $key => $val)
    {
        $val = intval(make_semiangle($val));
        if ($val <= 0 || !is_numeric($key))
        {
            continue;
        }

        $sql = "SELECT `goods_id`, `goods_attr_id`, `extension_code` FROM" .$GLOBALS['ecs']->table('cart').
               " WHERE rec_id='$key' AND session_id='" . SESS_ID . "'";
        $goods = $GLOBALS['db']->getRow($sql);

        $sql = "SELECT g.goods_name, g.goods_number, c.product_id ".
                "FROM " .$GLOBALS['ecs']->table('goods'). " AS g, ".
                    $GLOBALS['ecs']->table('cart'). " AS c ".
                "WHERE g.goods_id = c.goods_id AND c.rec_id = '$key'";
        $row = $GLOBALS['db']->getRow($sql);

        //系统启用了库存，检查输入的商品数量是否有效
        if (intval($GLOBALS['_CFG']['use_storage']) > 0 && $goods['extension_code'] != 'package_buy')
        {
            if ($row['goods_number'] < $val)
            {
                show_message(sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],
                $row['goods_number'], $row['goods_number']));
                exit;
            }

            /* 是货品 */
            $row['product_id'] = trim($row['product_id']);
            if (!empty($row['product_id']))
            {
                $sql = "SELECT product_number FROM " .$GLOBALS['ecs']->table('products'). " WHERE goods_id = '" . $goods['goods_id'] . "' AND product_id = '" . $row['product_id'] . "'";
                $product_number = $GLOBALS['db']->getOne($sql);
                if ($product_number < $val)
                {
                    show_message(sprintf($GLOBALS['_LANG']['stock_insufficiency'], $row['goods_name'],
                    $row['goods_number'], $row['goods_number']));
                    exit;
                }
            }
        }
        elseif (intval($GLOBALS['_CFG']['use_storage']) > 0 && $goods['extension_code'] == 'package_buy')
        {
            if (judge_package_stock($goods['goods_id'], $val))
            {
                show_message($GLOBALS['_LANG']['package_stock_insufficiency']);
                exit;
            }
        }
    }

}

/**
 * 删除购物车中的商品
 *
 * @access  public
 * @param   integer $id
 * @return  void
 */
function flow_drop_cart_goods($id)
{
    /* 取得商品id */
    $sql = "SELECT * FROM " .$GLOBALS['ecs']->table('cart'). " WHERE rec_id = '$id'";
    $row = $GLOBALS['db']->getRow($sql);
    if ($row)
    {
        //如果是超值礼包
        if ($row['extension_code'] == 'package_buy')
        {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
                    " WHERE session_id = '" . SESS_ID . "' " .
                    "AND rec_id = '$id' LIMIT 1";
        }

        //如果是普通商品，同时删除所有赠品及其配件
        elseif ($row['parent_id'] == 0 && $row['is_gift'] == 0)
        {
            /* 检查购物车中该普通商品的不可单独销售的配件并删除 */
            $sql = "SELECT c.rec_id
                    FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " . $GLOBALS['ecs']->table('group_goods') . " AS gg, " . $GLOBALS['ecs']->table('goods'). " AS g
                    WHERE gg.parent_id = '" . $row['goods_id'] . "'
                    AND c.goods_id = gg.goods_id
                    AND c.parent_id = '" . $row['goods_id'] . "'
                    AND c.extension_code <> 'package_buy'
                    AND gg.goods_id = g.goods_id
                    AND g.is_alone_sale = 0";
            $res = $GLOBALS['db']->query($sql);
            $_del_str = $id . ',';
            while ($id_alone_sale_goods = $GLOBALS['db']->fetchRow($res))
            {
                $_del_str .= $id_alone_sale_goods['rec_id'] . ',';
            }
            $_del_str = trim($_del_str, ',');

            $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
                    " WHERE session_id = '" . SESS_ID . "' " .
                    "AND (rec_id IN ($_del_str) OR parent_id = '$row[goods_id]' OR is_gift <> 0)";
        }

        //如果不是普通商品，只删除该商品即可
        else
        {
            $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') .
                    " WHERE session_id = '" . SESS_ID . "' " .
                    "AND rec_id = '$id' LIMIT 1";
        }

        $GLOBALS['db']->query($sql);
    }

    flow_clear_cart_alone();
}

/**
 * 删除购物车中不能单独销售的商品
 *
 * @access  public
 * @return  void
 */
function flow_clear_cart_alone()
{
    /* 查询：购物车中所有不可以单独销售的配件 */
    $sql = "SELECT c.rec_id, gg.parent_id
            FROM " . $GLOBALS['ecs']->table('cart') . " AS c
                LEFT JOIN " . $GLOBALS['ecs']->table('group_goods') . " AS gg ON c.goods_id = gg.goods_id
                LEFT JOIN" . $GLOBALS['ecs']->table('goods') . " AS g ON c.goods_id = g.goods_id
            WHERE c.session_id = '" . SESS_ID . "'
            AND c.extension_code <> 'package_buy'
            AND gg.parent_id > 0
            AND g.is_alone_sale = 0";
    $res = $GLOBALS['db']->query($sql);
    $rec_id = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $rec_id[$row['rec_id']][] = $row['parent_id'];
    }

    if (empty($rec_id))
    {
        return;
    }

    /* 查询：购物车中所有商品 */
    $sql = "SELECT DISTINCT goods_id
            FROM " . $GLOBALS['ecs']->table('cart') . "
            WHERE session_id = '" . SESS_ID . "'
            AND extension_code <> 'package_buy'";
    $res = $GLOBALS['db']->query($sql);
    $cart_good = array();
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $cart_good[] = $row['goods_id'];
    }

    if (empty($cart_good))
    {
        return;
    }

    /* 如果购物车中不可以单独销售配件的基本件不存在则删除该配件 */
    $del_rec_id = '';
    foreach ($rec_id as $key => $value)
    {
        foreach ($value as $v)
        {
            if (in_array($v, $cart_good))
            {
                continue 2;
            }
        }

        $del_rec_id = $key . ',';
    }
    $del_rec_id = trim($del_rec_id, ',');

    if ($del_rec_id == '')
    {
        return;
    }

    /* 删除 */
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('cart') ."
            WHERE session_id = '" . SESS_ID . "'
            AND rec_id IN ($del_rec_id)";
    $GLOBALS['db']->query($sql);
}

/**
 * 比较优惠活动的函数，用于排序（把可用的排在前面）
 * @param   array   $a      优惠活动a
 * @param   array   $b      优惠活动b
 * @return  int     相等返回0，小于返回-1，大于返回1
 */
function cmp_favourable($a, $b)
{
    if ($a['available'] == $b['available'])
    {
        if ($a['sort_order'] == $b['sort_order'])
        {
            return 0;
        }
        else
        {
            return $a['sort_order'] < $b['sort_order'] ? -1 : 1;
        }
    }
    else
    {
        return $a['available'] ? -1 : 1;
    }
}

/**
 * 取得某用户等级当前时间可以享受的优惠活动
 * @param   int     $user_rank      用户等级id，0表示非会员
 * @return  array
 */
function favourable_list($user_rank)
{
    /* 购物车中已有的优惠活动及数量 */
    $used_list = cart_favourable();

    /* 当前用户可享受的优惠活动 */
    $favourable_list = array();
    $user_rank = ',' . $user_rank . ',';
    $now = gmtime();
    $sql = "SELECT * " .
            "FROM " . $GLOBALS['ecs']->table('favourable_activity') .
            " WHERE CONCAT(',', user_rank, ',') LIKE '%" . $user_rank . "%'" .
            " AND start_time <= '$now' AND end_time >= '$now'" .
            " AND act_type = '" . FAT_GOODS . "'" .
            " ORDER BY sort_order";
    $res = $GLOBALS['db']->query($sql);
    while ($favourable = $GLOBALS['db']->fetchRow($res))
    {
        $favourable['start_time'] = local_date($GLOBALS['_CFG']['time_format'], $favourable['start_time']);
        $favourable['end_time']   = local_date($GLOBALS['_CFG']['time_format'], $favourable['end_time']);
        $favourable['formated_min_amount'] = price_format($favourable['min_amount'], false);
        $favourable['formated_max_amount'] = price_format($favourable['max_amount'], false);
        $favourable['gift']       = unserialize($favourable['gift']);

        foreach ($favourable['gift'] as $key => $value)
        {
            $favourable['gift'][$key]['formated_price'] = price_format($value['price'], false);
            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('goods') . " WHERE is_on_sale = 1 AND goods_id = ".$value['id'];
            $is_sale = $GLOBALS['db']->getOne($sql);
            if(!$is_sale)
            {
                unset($favourable['gift'][$key]);
            }
        }

        $favourable['act_range_desc'] = act_range_desc($favourable);
        $favourable['act_type_desc'] = sprintf($GLOBALS['_LANG']['fat_ext'][$favourable['act_type']], $favourable['act_type_ext']);

        /* 是否能享受 */
        $favourable['available'] = favourable_available($favourable);
        if ($favourable['available'])
        {
            /* 是否尚未享受 */
            $favourable['available'] = !favourable_used($favourable, $used_list);
        }

        $favourable_list[] = $favourable;
    }

    return $favourable_list;
}

/**
 * 根据购物车判断是否可以享受某优惠活动
 * @param   array   $favourable     优惠活动信息
 * @return  bool
 */
function favourable_available($favourable)
{
    /* 会员等级是否符合 */
    $user_rank = $_SESSION['user_rank'];
    if (strpos(',' . $favourable['user_rank'] . ',', ',' . $user_rank . ',') === false)
    {
        return false;
    }

    /* 优惠范围内的商品总额 */
    $amount = cart_favourable_amount($favourable);

    /* 金额上限为0表示没有上限 */
    return $amount >= $favourable['min_amount'] &&
        ($amount <= $favourable['max_amount'] || $favourable['max_amount'] == 0);
}

/**
 * 取得优惠范围描述
 * @param   array   $favourable     优惠活动
 * @return  string
 */
function act_range_desc($favourable)
{
    if ($favourable['act_range'] == FAR_BRAND)
    {
        $sql = "SELECT brand_name FROM " . $GLOBALS['ecs']->table('brand') .
                " WHERE brand_id " . db_create_in($favourable['act_range_ext']);
        return join(',', $GLOBALS['db']->getCol($sql));
    }
    elseif ($favourable['act_range'] == FAR_CATEGORY)
    {
        $sql = "SELECT cat_name FROM " . $GLOBALS['ecs']->table('category') .
                " WHERE cat_id " . db_create_in($favourable['act_range_ext']);
        return join(',', $GLOBALS['db']->getCol($sql));
    }
    elseif ($favourable['act_range'] == FAR_GOODS)
    {
        $sql = "SELECT goods_name FROM " . $GLOBALS['ecs']->table('goods') .
                " WHERE goods_id " . db_create_in($favourable['act_range_ext']);
        return join(',', $GLOBALS['db']->getCol($sql));
    }
    else
    {
        return '';
    }
}

/**
 * 取得购物车中已有的优惠活动及数量
 * @return  array
 */
function cart_favourable()
{
    $list = array();
    $sql = "SELECT is_gift, COUNT(*) AS num " .
            "FROM " . $GLOBALS['ecs']->table('cart') .
            " WHERE session_id = '" . SESS_ID . "'" .
            " AND rec_type = '" . CART_GENERAL_GOODS . "'" .
            " AND is_gift > 0" .
            " GROUP BY is_gift";
    $res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $list[$row['is_gift']] = $row['num'];
    }

    return $list;
}

/**
 * 购物车中是否已经有某优惠
 * @param   array   $favourable     优惠活动
 * @param   array   $cart_favourable购物车中已有的优惠活动及数量
 */
function favourable_used($favourable, $cart_favourable)
{
    if ($favourable['act_type'] == FAT_GOODS)
    {
        return isset($cart_favourable[$favourable['act_id']]) &&
            $cart_favourable[$favourable['act_id']] >= $favourable['act_type_ext'] &&
            $favourable['act_type_ext'] > 0;
    }
    else
    {
        return isset($cart_favourable[$favourable['act_id']]);
    }
}

/**
 * 添加优惠活动（赠品）到购物车
 * @param   int     $act_id     优惠活动id
 * @param   int     $id         赠品id
 * @param   float   $price      赠品价格
 */
function add_gift_to_cart($act_id, $id, $price)
{
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('cart') . " (" .
                "user_id, session_id, goods_id, goods_sn, goods_name, market_price, goods_price, ".
                "goods_number, is_real, extension_code, parent_id, is_gift, rec_type ) ".
            "SELECT '$_SESSION[user_id]', '" . SESS_ID . "', goods_id, goods_sn, goods_name, market_price, ".
                "'$price', 1, is_real, extension_code, 0, '$act_id', '" . CART_GENERAL_GOODS . "' " .
            "FROM " . $GLOBALS['ecs']->table('goods') .
            " WHERE goods_id = '$id'";
    $GLOBALS['db']->query($sql);
}

/**
 * 添加优惠活动（非赠品）到购物车
 * @param   int     $act_id     优惠活动id
 * @param   string  $act_name   优惠活动name
 * @param   float   $amount     优惠金额
 */
function add_favourable_to_cart($act_id, $act_name, $amount)
{
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('cart') . "(" .
                "user_id, session_id, goods_id, goods_sn, goods_name, market_price, goods_price, ".
                "goods_number, is_real, extension_code, parent_id, is_gift, rec_type ) ".
            "VALUES('$_SESSION[user_id]', '" . SESS_ID . "', 0, '', '$act_name', 0, ".
                "'" . (-1) * $amount . "', 1, 0, '', 0, '$act_id', '" . CART_GENERAL_GOODS . "')";
    $GLOBALS['db']->query($sql);
}

/**
 * 取得购物车中某优惠活动范围内的总金额
 * @param   array   $favourable     优惠活动
 * @return  float
 */
function cart_favourable_amount($favourable)
{
    /* 查询优惠范围内商品总额的sql */
    $sql = "SELECT SUM(c.goods_price * c.goods_number) " .
            "FROM " . $GLOBALS['ecs']->table('cart') . " AS c, " . $GLOBALS['ecs']->table('goods') . " AS g " .
            "WHERE c.goods_id = g.goods_id " .
            "AND c.session_id = '" . SESS_ID . "' " .
            "AND c.rec_type = '" . CART_GENERAL_GOODS . "' " .
            "AND c.is_gift = 0 " .
            "AND c.goods_id > 0 ";

    /* 根据优惠范围修正sql */
    if ($favourable['act_range'] == FAR_ALL)
    {
        // sql do not change
    }
    elseif ($favourable['act_range'] == FAR_CATEGORY)
    {
        /* 取得优惠范围分类的所有下级分类 */
        $id_list = array();
        $cat_list = explode(',', $favourable['act_range_ext']);
        foreach ($cat_list as $id)
        {
            $id_list = array_merge($id_list, array_keys(cat_list(intval($id), 0, false)));
        }

        $sql .= "AND g.cat_id " . db_create_in($id_list);
    }
    elseif ($favourable['act_range'] == FAR_BRAND)
    {
        $id_list = explode(',', $favourable['act_range_ext']);

        $sql .= "AND g.brand_id " . db_create_in($id_list);
    }
    else
    {
        $id_list = explode(',', $favourable['act_range_ext']);

        $sql .= "AND g.goods_id " . db_create_in($id_list);
    }

    /* 优惠范围内的商品总额 */
    return $GLOBALS['db']->getOne($sql);
}

/**
 * 处理配送时间段
 * @param unknown_type $config			配送时间配置信息
 * @param unknown_type $day				天数标示， today(今天)  tomorrow (明天以后)
 * @param int		   $sid				供应商id
 */
function getShippingTimes($config,$day,$sid){
	
	$times = $timeConfig = array();	
	
	//提前预订的毫秒数, 最早配送时间的毫秒数, 最晚配送的毫秒数
	foreach( array('shipping_start', 'shipping_end', 'shipping_booking') as $shipping)
	{
		if(strpos($config[$shipping], ":") !==false)
		{
			$booking = explode(':',$config[$shipping]);
			$hours   = $booking[0];
			$minutes = isset($booking[1]) ? intval($booking[1]) : 0 ;
			$timeConfig[$shipping] = ($hours*60*60)+($minutes*60);
		}
		else{
			$timeConfig[$shipping] = $config[$shipping]*60*60;
		}
	}
	// 时间处理后的开始时间和结束时间
	$shipping_start = $shipping_end = '';
	
	// 当前年月日的时间戳
	$yearMonthDay = local_date('Y-m-d');
	// 客服今天下班的时间
	$afterTime = strtotime($yearMonthDay.' 21:00');
	// 客服上班的时间
	$workTime  = strtotime($yearMonthDay.' 09:00');
	
	
	switch($day)
	{
		case "today":
			
			//用户可选的最早配送时间 = 当前下单时间 + 提前预订时间
			$shipping_start = strtotime(local_date('Y-m-d H:i:s'))+$timeConfig['shipping_booking'];			
			//用户可选的最晚配送时间 = 今天0点时间戳 + 最晚配送时间的时间戳
			$shipping_end   =  strtotime($yearMonthDay)+$timeConfig['shipping_end'];
			
			/**
			 *  针对每个供应商的配置信息，配置文件在 ./includes/modules/shippingTime/供应商.php
			 */
			$fileName = get_supplier_file_name($sid);
			$file = dirname(__FILE__).'/includes/modules/shippingTime/'.$fileName.'.php';
			if(is_file($file))
			{
				include_once $file;
				$extensionInfo = get_extension_info($timeConfig, 'today');
				
				if($extensionInfo['shipping_start'] !== 0)
				{
					$shipping_start = $extensionInfo['shipping_start'];
				}
			}
			
			// 如果当前时间加提前预订时间，超过了客服下班的时间,
			// 或者最早下单，超过了客服下班的时间
			if(strtotime(local_date('Y-m-d H:i:s'))+$timeConfig['shipping_booking'] > $afterTime
					|| $shipping_start > $afterTime)
			{
				$mesage = array( 'error'=>1, 'message'=>'今天不能预订，请选择明天或其他时间段！');
				return $mesage;
			}
			
			//如果下单时间早于客服上班的时间  最早配送时间 = 当天最早的配送时间 + 提前预订时间。
			//或者最早下单时间早过客服上班的时间。
			if(strtotime(local_date('Y-m-d H:i:s')) < $workTime 
					&& $shipping_start < $afterTime)
			{
				$shipping_start = strtotime($yearMonthDay)+$timeConfig['shipping_start']+$timeConfig['shipping_booking'];
			}
			
			break;
		case "tomorrow":
			
			$shipping_start = strtotime(local_date('Y-m-d'))+$timeConfig['shipping_start'];
			$shipping_end 	= strtotime(local_date('Y-m-d'))+$timeConfig['shipping_end'];			
			
			// 如果当前客户下单时间，超过了客服下班的时间，最早配送时间 = 当天最早的配送时间 + 提前预订时间。			
			if(strtotime(local_date('Y-m-d H:i:s')) > $afterTime)
			{
				$shipping_start = strtotime(local_date('Y-m-d'))+$timeConfig['shipping_start']+$timeConfig['shipping_booking'];
			}
			
			/**
			 *  针对每个供应商的配置信息，配置文件在 ./includes/modules/shippingTime/供应商.php
			 */
			$fileName = get_supplier_file_name($sid);
			$file = dirname(__FILE__).'/includes/modules/shippingTime/'.$fileName.'.php';
			if(is_file($file))
			{
				include_once $file;
				$extensionInfo = get_extension_info($timeConfig, 'tomorrow');
					
				if($extensionInfo['shipping_start'] !== 0)
				{
					$shipping_start = $extensionInfo['shipping_start'];
					$shipping_end = $extensionInfo['shipping_end'];
				}
			}
			
			break;
	}
	//error_log(var_export(array($shipping_start, $shipping_end, $config, $timeConfig['shipping_start']),true),'3','error.log');
	return generate_shipping_times($shipping_start, $shipping_end, $config, $timeConfig['shipping_start']);
}
/**
 * 	生成配送时间函数
 */
function generate_shipping_times($start, $end, $config, $shipping_start){
	
	// 配送时间间隔的毫秒数  
	$seconds = $config['shipping_waiting']*60*60;
	// 下单的分钟是30分钟内，就按30分钟算， 超过了30分钟，就按整点算
	$minutes = 30;
	if(date('i', $start) > 30)
	{
		$minutes = 60;
	}
	$start = strtotime(date('Y-m-d H',$start).':00:00')+$minutes*60;
	// 最早配送小时等于配送设置里的最早配送小时	
	$configStart = strtotime(date('Y-m-d',$start).' 00:00:00')+$shipping_start;
	if(date('H',$configStart) == date('H',$start))
	{
		$start = $configStart;
	}
	// 开始和结束之间的毫秒数
	$deffSeconds = $end - $start;
	// 计算几个时间段
	$row = ceil($deffSeconds/$seconds);
	$tmp_seconds = 0;
	$times = array();
	for($i=0; $i<$row; $i++){
		if($tmp_seconds == 0)
		{
			$tmp_seconds = $start;
		}
		$first = date('H:i',$tmp_seconds);
		
		$prevs = date('H:i',$tmp_seconds+$seconds);
		$tmp_seconds = $tmp_seconds+$seconds;
		$times[$first.'-'.$prevs] = $first.'-'.$prevs; 
	}
	
	return $times;
	
	
	
	
	/* // 开始的小时
	$startHours 	= date("H", $start);
	if(date('i', $start) > 30)
	{
		$startHours +=1;
	}
	
	// 开始的分钟
	$tmp = explode(':',$config['shipping_start']);
	$startMinutes = 00;
	if(isset($tmp[1]))
	{
		$startMinutes = $tmp[1]; 
	}
	
	// 结束的小时
	$endHours 		= date("H", $end);
	// 结束的分钟
	$endMinutes		= date('i', $end);
	
	
	
	$times = array();
	
	
	for($i=$startHours; $i<$endHours; $i+=$config['shipping_waiting'])
	{
		$time = $i.':'.$startMinutes.'-'.($i+1).':'.$startMinutes;
		
		if($i+1 == $endHours)
		{
			$time = $i.':'.$startMinutes.'-'.($i+1).':'.$endMinutes;
			$times[$time] = $time;
		}
		else{
			$times[$time] = $time;
		}
	}
	
	
	return $times; */	
}

/** 
 * 得到供应商文件名。
 * 新供应商在现有的规则无法完成，想要的功能的时候，需要扩展供应商放在这里。
 */
function get_supplier_file_name($sid=0){
	$fileName = array(
		// 诺心
		'2'=>'nuoxin'
		
	);
	
	if(isset($fileName[$sid]))
	{
		return $fileName[$sid];
	}
	else{
		return false;
	}
}
/**
 *  获取供应商列表信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function suppliers_list()
{
	/* 查询 */
	$sql = "SELECT supplier_id,u.user_name, rank_id, open_time, cost_ratio, shop_ratio, supplier_name, tel, system_fee, supplier_bond, supplier_rebate, supplier_remark,  ".
			"status ".
			"FROM " . $GLOBALS['ecs']->table("supplier") . " as s left join " . $GLOBALS['ecs']->table("users") . " as u on s.user_id = u.user_id ";

	$list=array();
	$res = $GLOBALS['db']->query($sql);
	while ($row = $GLOBALS['db']->fetchRow($res))
	{

		$row['status_name'] = $row['status']=='1' ? '通过' : ($row['status']=='0' ? "未审核" : "未通过");
		$list[]=$row;
	}
 
	return $list;
}

/**
 *  检查购物车中商品的价格
 */
function check_cart_price()
{
	$return = true;
	$sql = "SELECT goods_id, goods_price " .
			"FROM " . $GLOBALS['ecs']->table('cart') .
			" WHERE session_id = '" . SESS_ID . "' " .
			"AND rec_type = '" . CART_GENERAL_GOODS . "' " .
			"AND is_gift = 0 " .
			"AND extension_code <> 'package_buy' " .
			"AND parent_id = 0 ";
	$parent_list = $GLOBALS['db']->getAll($sql);
	foreach ($parent_list as $list)
	{
		if ($list['goods_price'] <=0)
		{
			$return = false;
		}
	}
	
	return $return;
}
?>