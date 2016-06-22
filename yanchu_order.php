<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'mobile/includes/lib_yanchu.php');
if ((DEBUG_MODE & 2) != 2)
{
	$smarty->caching = true;
}

assign_template();

//根据城市id获取影院区域编号
$int_areaNo = getAreaNo(0, 'yanchu');
$int_itemId = intval($_REQUEST['id']);
$int_cateId = intval($_REQUEST['cateid']);

$str_action = $_REQUEST['act'];
$smarty->assign('action', $str_action);

$arr_catName = array(
		'1217' => '演唱会',
		'1220' => '话剧',
		'1218' => '音乐会',
		'1211' => '体育赛事',
		'1227' => '亲子儿童',
		'1224' => '戏曲综艺',
);

$arr_statusZN = array(
		'1'=>'提供预订',
		'2'=>'正在销售',
		'3'=>'免费',
		'4'=>'已经包场',
		'5'=>'仅供资讯',
		'6'=>'门售方式',
		'7'=>'票已售完'
);

// 下单演出票
if($_REQUEST['act'] == 'order')
{
 
	//获取已选中的订单信息
	$arr_orderInfo = array(
			'itemId'      => $int_itemId,
			'cateId'      => $int_cateId,
			'catename'    => $arr_catName[$int_cateId],
			'best_time'   => local_date('Y-m-d H:i', intval($_POST['time'])),
			'price'       => $_POST['price'],
			'specid'      => intval($_POST['specid']),
			'number'      => intval($_POST['number']),
			'goods_amount' => $_POST['price'] * intval($_POST['number']),
			'status'      => intval($_POST['status']),
			'statusZn'    => $arr_statusZN[$_POST['status']],
			'storeId'     => intval($_POST['storeId']),
			'storename'   => $_POST['storeName'],
			'market_price'=> $_POST['market_price']
	);

	if ($arr_orderInfo['status'] > 3){
		show_message('抱歉，该演出不能购买');
	}
	if (empty($arr_orderInfo['best_time'])){
		show_message('抱歉，请选择时间');
	}
	if (empty($arr_orderInfo['specid'])){
		show_message('抱歉，请选择价格');
	}
	if (empty($arr_orderInfo['number'])){
		show_message('抱歉，请输入购买数量');
	}

	//用户卡余额
	$card_money = $GLOBALS['db']->getOne('SELECT card_money FROM '.$GLOBALS['ecs']->table('users')." WHERE user_id = '".intval($_SESSION['user_id'])."'");
	if ($arr_orderInfo['goods_amount'] > $card_money){
		show_message('抱歉，您的卡余额不足');
	}
	
	//或许项目信息
	$arr_param = array(
			'itemId'    => $int_itemId,
	);
	$obj_result   = getYCApi($arr_param, 'getItem');
	$int_showTimeCount = count($obj_result->showtimes->showtime);
	$arr_itemInfo = object2array($obj_result);
	if (empty($arr_itemInfo)){
		show_message('抱歉，此产品暂时无法购买，请换个产品吧！');
	}

	$arr_orderInfo['itemName']   = $arr_itemInfo['itemName'];//项目名称
	$arr_orderInfo['siteName']   = $arr_itemInfo['site']['@attributes']['siteName'];//场馆名称
	$arr_orderInfo['storeId']    = $arr_itemInfo['store']['@attributes']['storeId'];//供货商id
	$arr_orderInfo['storeName']  = $arr_itemInfo['store']['@attributes']['storeName'];//供货商名称

	if($arr_itemInfo['showtimes']){
		if ($int_showTimeCount > 1){
			$arr_showtime = $arr_itemInfo['showtimes']['showtime'];
		}else{
			$arr_showtime[0] = $arr_itemInfo['showtimes']['showtime'];
		}
		foreach ($arr_showtime as $key=>$var){
			$var['shStartDateFormat'] = !empty($var['shStartDate']) ? local_date('Y-m-d H:i', $var['shStartDate']).'（周'.$arr_week[local_date('w', $var['shStartDate'])].'）' : '';
			$var['shEndDateFormat']   = !empty($var['shEndDate']) ? local_date('Y-m-d H:i', $var['shEndDate']).'（周'.$arr_week[local_date('w', $var['shEndDate'])].'）' : '';
			$var['statusZn']    = $arr_statusZN[$var['status']];
			if ($var['specs']['spec']){
			    if (count($var['specs']['spec']) == 1){
			        $arr_temp[0] = $var['specs']['spec'];
			    }else {
			        $arr_temp = $var['specs']['spec'];
			    }
				$arr_spec = array();
				foreach ($arr_temp as $k=>$v){
					$arr_spec[$v['@attributes']['specId']]['specId']   = $v['@attributes']['specId'];
					$arr_spec[$v['@attributes']['specId']]['specType'] = $v['@attributes']['specType'];
					$arr_spec[$v['@attributes']['specId']]['price']    = $v['@attributes']['price'];
					$arr_spec[$v['@attributes']['specId']]['stock']    = $v['@attributes']['stock'];
					$arr_spec[$v['@attributes']['specId']]['layout']   = $v['@attributes']['layout'];
					$arr_spec[$v['@attributes']['specId']]['say']      = $v['@attributes']['say'];
				}
				unset($var['specs']['spec']);
				$var['specs'] = $arr_spec;
			}
			$arr_showtime[$key] = $var;
		}
	}
	
	// 时间和价格的判断
	$times = $_POST['time']-(8*3600);
	$is_ok = false;
	foreach ($arr_showtime as $showtime)
	{
	    if ($showtime['shEndDate'] == $times)
	    {
	        if ($showtime['specs'][$_POST['specid']])
	        {
	            $is_ok = true;
	            $arr_orderInfo['cost_price'] = $showtime['specs'][$_POST['specid']]['price'];
	            $arr_orderInfo['layout'] = $showtime['specs'][$_POST['specid']]['layout'];
	        }
	    }
	}
	if ($is_ok === false)
	{
	    show_message('未知错误，请从新选择！');
	}
	
	$_SESSION['yc_flow_order'] = array('flow'=>$arr_orderInfo, 'item'=>$arr_itemInfo);//将订单信息保存到session中
	ecs_header('location:yanchu_order.php?act=checkout');
}

// 下单操作
elseif ($_REQUEST['act'] == 'checkout')
{
	$arr_orderInfo = $_SESSION['yc_flow_order']['flow'];
	if(empty($arr_orderInfo))
	{
		show_message('没有任何信息，请从新选择一个产品购买！');
	}
	
	//配送费用
	$consignee = get_consignee($_SESSION['user_id']);
	if (!empty($consignee)){
	    $consignee['country_cn']  = get_add_cn($consignee['country']);
	    $consignee['province_cn'] = get_add_cn($consignee['province']);
	    $consignee['city_cn']     = get_add_cn($consignee['city']);
	    $consignee['district_cn'] = get_add_cn($consignee['district']);
	}

	/* 检查收货人信息是否完整 */
	if (!empty($consignee['consignee']) &&
	    !empty($consignee['country']) &&
	    (!empty($consignee['tel']) || !empty($consignee['mobile'])))
	{
	    $smarty->assign('checkconsignee', 1);
	}else{
	    $smarty->assign('checkconsignee', 0);
	}
	
	
	/* 检查当前地址是否支持配送*/
	if ( !check_consignee($consignee) ){
	    $smarty->assign('peisong', 0);
	}else{
	    $smarty->assign('peisong', 1);
	}
	
	$smarty->assign('consignee', $consignee);

	$arr_region = array($consignee['country'], $consignee['province'], $consignee['city'], $consignee['district']);//取得配送列表

	$arr_shipping = shipping_area_info(1, $arr_region);
	if (!empty($arr_shipping)){
		$arr_shipping['shipping_id'] = 1;
		$shipping_cfg = unserialize_config($arr_shipping['configure']);
		$shipping_fee = shipping_fee($arr_shipping['shipping_code'], unserialize($arr_shipping['configure']), 1, $arr_orderInfo['goods_amount'], $arr_orderInfo['number']);
		$arr_shipping['format_shipping_fee'] = price_format($shipping_fee, false);
		$arr_shipping['shipping_fee']        = $shipping_fee;
		$arr_shipping['free_money']          = price_format($shipping_cfg['free_money'], false);
	}else{
		$arr_shipping['shipping_fee']        = 0;
	}
	
	// 如果是自取，运费为0
	if (ispick($arr_orderInfo) === true)
	{
	    $arr_shipping['shipping_fee']        = 0;
	    $arr_orderInfo['take_way']     = '自取';
	}
	else {
	    $arr_orderInfo['take_way']     = '快递';
	}
	
	//配送方式
	$smarty->assign('shipping_info',   $arr_shipping);
	//支付方式
	$smarty->assign('payment_info', payment_info(2));	
	
	$order = $_SESSION['yc_flow_order']['flow'];
	$item = $_SESSION['yc_flow_order']['item'];
	$order['amount'] = $order['goods_amount'] + $arr_shipping['shipping_fee'];
	
	$smarty->assign('order',  $order);
	$smarty->assign('yanchu', $item);
	
	$smarty->assign('backHtml',    getBackHtml( get_yanchu_back($int_cateId)));
	$smarty->display('yanchu/yanchuCheckout.dwt');
}

else if ($str_action == 'act_order'){	
	
	$arr_order = $_SESSION['yc_flow_order']['flow'];
	if (empty($arr_order)){
		show_message('没有任何信息，请从新选择一个产品购买！');
	}
	
	$ratio = get_card_rule_ratio($arr_order['cateId']);
	
	$arr_consignee = get_consignee($_SESSION['user_id']);//获取用户默认配送地址
	if (empty($arr_consignee)){
		show_message('请选择一个收货地址！');
	}
    
	/* 检查当前地址是否支持配送*/
	if ( !check_consignee($arr_consignee) )
	{
	    show_message('当前地址不支持配送！');
	}
	
	$str_regionName = get_add_cn($arr_consignee['country']).' '.get_add_cn($arr_consignee['province']).' '.get_add_cn($arr_consignee['city']).' '.get_add_cn($arr_consignee['district']);

	//计算配送费用
	$arr_region = array($arr_consignee['country'], $arr_consignee['province'], $arr_consignee['city'], $arr_consignee['district']);//取得配送列表
	$arr_shipping = shipping_area_info(1, $arr_region);	
	
	if (!empty($arr_shipping)){
		$arr_shipping['shipping_id'] = 1;
		$shipping_cfg = unserialize_config($arr_shipping['configure']);
		$shipping_fee = shipping_fee($arr_shipping['shipping_code'], unserialize($arr_shipping['configure']), 1, $arr_order['goods_amount'], $arr_order['number']);
		$arr_shipping['format_shipping_fee'] = price_format($shipping_fee, false);
		$arr_shipping['shipping_fee']        = $shipping_fee;
		$arr_shipping['free_money']          = price_format($shipping_cfg['free_money'], false);
	}else{
		$arr_shipping['shipping_fee']        = 0;
	}
	
	// 如果是自取，运费为0
	if (ispick() === true)
	{
	    $shipping_fee  = 0;
	    $arr_order['take_way']     = '自取';
	}
	else {
	    $arr_order['take_way']     = '快递';
	}
	
	$arr_order['shipping_fee'] = $shipping_fee;
	$arr_order['amount'] = $arr_order['goods_amount'] + $shipping_fee;
	$arr_order['order_sn'] = get_order_sn();
	
	//接口下单
	$int_shippingId = 0;
	$arr_param = array(
			'storeId' => $arr_order['storeId']
	);
	$obj_result = getYCApi($arr_param, 'getShippings');
	$int_shippingCount = count($obj_result->shipping);
	$arr_itemInfo = object2array($obj_result);
	$arr_shipping = $arr_itemInfo['shipping'];	

	
	if ($int_shippingCount > 1){
		$int_shippingKey = array_rand($arr_shipping);
		$int_shippingId = intval($arr_shipping[$int_shippingKey]['@attributes']['id']);
	}else{
		$int_shippingId = intval($arr_shipping['@attributes']['id']);
	}
	
	$str_sign = md5('u='.$GLOBALS['_CFG']['ycappUser'].'&storeId='.$arr_order['storeId'].'&specId='.$arr_order['specid'].'&num='.$arr_order['number'].'&consignee='.$arr_consignee['consignee'].'&address='.$arr_consignee['address'].'&tel='.$arr_consignee['tel'].'&mob='.$arr_consignee['mobile'].'&email=ceshi@qq.com&shippingId='.$int_shippingId.'&apiorderSn='.$arr_order['order_sn'].'&key='.$GLOBALS['_CFG']['ycappKey']);
	//项目信息
	$arr_param = array(
			'storeId'        => $arr_order['storeId'],
			'specId'         => $arr_order['specid'],
			'num'            => $arr_order['number'],
			'consignee'      => $arr_consignee['consignee'],
			'regionId'       => $arr_consignee['province'],
			'regionName'     => $str_regionName,
			'address'        => $arr_consignee['address'],
			'zip'            => '',
			'tel'            => $arr_consignee['tel'],
			'mob'            => $arr_consignee['mobile'],
			'email'          => 'ceshi@qq.com',
			'invoiceTitle'   => '',
			'invoiceContent' => '',
			'postscript'     => '',
			'shippingId'     => $int_shippingId,
			'apiorderSn'     => $arr_order['order_sn'],
			'sign'           => $str_sign
	);
	
	$obj_result = getYCApi($arr_param, 'apiorder');
	$arr_itemInfo = object2array($obj_result);
	
	if (!empty($arr_itemInfo['error'])){
		show_message($arr_itemInfo['error']);
	}else{
		$db->query('INSERT INTO ' .$ecs->table('yanchu_order'). " (order_sn, api_order_sn, user_id, user_name, consignee, address, order_status, itemid, itemname, sitename, storeId, storeName, specid, cateid, catename, mobile, tel, best_time, country, province, city, district, regionname, number, pay_id, pay_name, shipping_id, shipping_name, goods_amount, price, shipping_fee, order_amount, add_time, confirm_time, market_price,source, layout, take_way,card_ratio,api_price) VALUES ('".$arr_order['order_sn']."','".$arr_itemInfo['orderSn']."', '".$_SESSION['user_id']."', '".$_SESSION['user_name']."', '".$arr_consignee['consignee']."', '".$arr_consignee['address']."', '1', '".$arr_order['itemId']."', '".$arr_order['itemName']."', '".$arr_order['siteName']."', '".$arr_order['storeId']."', '".$arr_order['storeName']."', '".$arr_order['specid']."', '".$arr_order['cateId']."', '".$arr_order['catename']."', '".$arr_consignee['mobile']."', '".$arr_consignee['tel']."', '".$arr_order['best_time']."', '".$arr_consignee['country']."', '".$arr_consignee['province']."', '".$arr_consignee['city']."', '".$arr_consignee['district']."', '".$str_regionName."', '".$arr_order['number']."', '2', '华影支付', '1', '供货商物流', '".$arr_order['goods_amount']."', '".$arr_order['price']."', '".$arr_order['shipping_fee']."', '".$arr_order['amount']."', '".gmtime()."', '".gmtime()."', '".$arr_order['market_price']."',0, '".$arr_order['layout']."', '".$arr_order['take_way']."', '".$ratio."', '".$arr_order['cost_price']."')");
		$int_orderId = $db->insert_id();
	}

	unset($_SESSION['flow_consignee']);
	unset($_SESSION['yc_flow_order']);
	ecs_header('location:yanchu_order.php?act=pay&id='.$arr_order['itemId'].'&orderid='.$int_orderId);
	exit;
}
elseif( $_REQUEST['act'] == 'pay')
{
	assign_template();
	
	$int_orderid = intval($_REQUEST['orderid']);
	$arr_order = $db->getRow('SELECT *, (goods_amount + shipping_fee) as total_amount FROM '.$ecs->table('yanchu_order')." WHERE order_id = '$int_orderid'");
	$smarty->assign('order', $arr_order);
	$smarty->display('yanchu/yanchuPay.dwt');	
	
}
else if ($str_action == 'act_pay'){
	$str_password = !empty($_POST['password']) ? $_POST['password'] : '';
	$order_sn     = $_POST['order_sn'];
	$order_id     = $_POST['order_id'];
	$order_amount = floatval($_POST['order_amount']);

	$arr_result = array('error' => 0, 'message' => '', 'content' => '');

	if (empty($str_password)){
		$arr_result['error'] = 1;
		$arr_result['message'] = '卡密码不能为空';
		die(json_encode($arr_result));
	}
	if (empty($order_sn)){
		$arr_result['error'] = 2;
		$arr_result['message'] = '订单信息不正确';
		die(json_encode($arr_result));
	}
	if ($order_amount > $_SESSION['BalanceCash']){
		$arr_result['error'] = 3;
		$arr_result['message'] = '卡余额不足';
		die(json_encode($arr_result));
	}
	$arr_order = $db->getRow('SELECT * FROM '.$ecs->table('yanchu_order')." WHERE order_id = '$order_id'");
	if (empty($arr_order)){
		$arr_result['error'] = 6;
		$arr_result['message'] = '支付订单信息不存在';
		die(json_encode($arr_result));
	} 

	/** TODO 支付 （双卡版） */
	$arr_param = array(
			'CardInfo' => array( 'CardNo'=> $_SESSION['user_name'], 'CardPwd' => $str_password),
			'TransationInfo' => array( 'TransRequestPoints'=>$order_amount, 'TransSupplier'=>setCharset('中票'))
	);
	$state = $cardPay->action($arr_param, 1, $order_sn);

	if ($state == 0){
		$cardResult = $cardPay->getResult();
		//支付成功修改本网站订单状态
		$db->query('UPDATE '.$ecs->table('yanchu_order')." SET pay_status = '2', pay_time = '".gmtime()."', money_paid = order_amount, order_amount = 0, api_order_id = '".$cardResult."' WHERE order_id = '$order_id'");
		//更新订单状态
		$str_sign = md5('u='.$GLOBALS['_CFG']['ycappUser'].'&apiorderSn='.$arr_order['order_sn'].'&pay=1&key='.$GLOBALS['_CFG']['ycappKey']);
		//演出订单支付确认接口
		$arr_param = array(
				'apiorderSn' => $arr_order['order_sn'],
				'pay'        => 1,
				'sign'       => $str_sign
		);
		$obj_result = getYCApi($arr_param, 'apiorder');//确认支付订单

		$_SESSION['BalanceCash'] -= $order_amount; //重新计算用户卡余额
		//更新卡金额
		$db->query('UPDATE '.$ecs->table('users')." SET card_money = card_money - ('$order_amount') WHERE user_id = '".intval($_SESSION['user_id'])."'");
		$arr_result['content'] = $order_id;
		$arr_result['itemid']  = $arr_order['itemid'];
	}else{
		$arr_result['error']   = 2;
		$arr_result['message'] = $cardPay->getMessage();
	}

	die(json_encode($arr_result));
}
else if ($str_action == 'respond')
{
    $smarty->display('yanchu/respond.dwt');
}
// 判断演出票是否是三天内的，如果是运费就是0，演出票自取
function ispick( $data=array() )
{
    if (empty($data))
        $flow = $_SESSION['yc_flow_order']['flow'];
    else
        $flow = $data;
    
    $oldTime = strtotime(date('Y-m-d',strtotime('+3 day', local_gettime())));
    $ticketTime = strtotime($flow['best_time']);
    if ($ticketTime < $oldTime)
        return true;
    else
        return false;
}

