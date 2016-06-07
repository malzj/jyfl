<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_order.php');
if ((DEBUG_MODE & 2) != 2){
	$smarty->caching = true;
}

$str_action = !empty($_REQUEST['act']) ? $_REQUEST['act'] : '';
$smarty->assign('action',  $str_action);
assign_template();


//根据城市id获取影院区域编号
$int_areaNo = getAreaNo();

include_once(ROOT_PATH . 'includes/lib_cardApi.php');

if ($str_action == 'respond'){
	//支付成功页面

	$int_orderId = intval($_GET['id']);
	$position = assign_ur_here(0, '<a href="shiting.php?id=1">视听盛宴</a> <code>&gt;</code> <a href="shiting.php?id=7">电影</a> <code>&gt;</code> <a href="shiting.php?act=zxxz">在线选座</a> <code>&gt;</code> 订单支付');
	$smarty->assign('page_title',       '订单支付_在线选座_电影_视听盛宴_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

} else if ($str_action == 'delorder'){
	//删除限时没支付的订单

	$int_orderId = intval($_GET['order_id']);
	$db->query('DELETE FROM '.$ecs->table('seat_order')." WHERE order_id = '$int_orderId' and  pay_status = 0");
	die('ok');
} else if ($str_action == 'done'){
	//完成支付在线选座订单
/* 
	$str_mobile  = $_POST['mobile'];
	$str_orderSn = $_POST['order_sn'];
	$int_orderId = intval($_POST['order_id']);
	$str_password = $_POST['password'];

	$arr_orderInfo = $db->getRow('SELECT * FROM '.$ecs->table('seat_order')." WHERE order_id = '$int_orderId'");
	$float_price = number_format(floatval($arr_orderInfo['order_amount']), 2, '.', '');

	if (empty($arr_orderInfo)){
		show_message('抱歉，您提交的支付信息不存在');
	}

	if (empty($str_mobile)){
		show_message('手机号码不能为空');
	}

	if ($str_mobile != $arr_orderInfo['mobile']){
		show_message('您输入的手机号码和选座时的手机号码不一致');
	}

	if (empty($str_password)){
		show_message('卡密码不能为空');
	}

	$float_price1 = $float_price*1.06;
	//卡消费接口
	$arr_param = array(
		'cardId'     => $_SESSION['user_name'],
		'cardPwd'    => $str_password,
		'posJournal' => $str_orderSn,
		'payTime'    => local_date('YmdHis'),
		'payAmount'  => $float_price,
		'deviceId'   => $GLOBALS['_CFG']['deviceId'],
		'operId'     => $GLOBALS['_CFG']['operId'],
		'merchantId' => $GLOBALS['_CFG']['merchantId'],
		'goodName'   => '',
		'storeId'    => $GLOBALS['_CFG']['storeId']
	);
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	$arr_cardInfo = getCardApi($arr_param, 'CARD-PAY', 7);
	if (!empty($arr_cardInfo)){
		if ($arr_cardInfo['ReturnCode'] == '0'){
			//支付成功修改本网站订单状态
				//选座订单确认接口
			$arr_param = array(
				'Mobile'     => $str_mobile,
				'OrderNo'    => $str_orderSn,
				'orderPrice' => $float_price1,
				'payment'    => $float_price1,
				'DistributorUrl' => '',
				'sign'     => md5($str_mobile.$str_orderSn.$float_price1.$arr_param['DistributorUrl'].$GLOBALS['_CFG']['yyappKey']),
				'Remark'   => '',
				'SendType' => 3
			);
			//var_dump($arr_param);

			$arr_result = getYYApi($arr_param, 'confirmOrder');//确认支付订单
			if($arr_result['body']['OrderStatus'] == 0){
			$db->query('UPDATE '.$ecs->table('seat_order')." SET pay_status = '2', pay_time = '".gmtime()."', money_paid = order_amount, order_amount = 0 WHERE order_id = '$int_orderId'");
			}else{
			$arr_result = getYYApi($arr_param, 'confirmOrder');//确认支付订单
			show_message('订单支付失败，如果卡金额已经扣除，请联系华影客服！');
			}
		
			//if ($arr_result['head']['errCode'] == '0'){}
			$_SESSION['BalanceCash'] -= $float_price; //重新计算用户卡余额
			//更新卡金额
			$db->query('UPDATE '.$ecs->table('users')." SET card_money = card_money - ('$float_price') WHERE user_id = '".intval($_SESSION['user_id'])."'");
			show_message('如果没有收到短信，请立即联系华影客服或进入会员中心点击再次发送验证码，有效时间为10分钟之内！');
			ecs_header('location:user.php?act=film_order');
			exit;
		}else{
			show_message($arr_cardInfo['ReturnMessage']);
		}
	}else{
		show_message('卡密码错误');
	} */

}else if ($str_action == 'payinfo'){
	//支付线选座页面
	/* $int_orderid = intval($_REQUEST['id']);
	if (empty($int_orderid)){
		ecs_header('location:shiting.php?id=1');
		exit;
	}
	$arr_order = $db->getRow('SELECT * FROM ' .$ecs->table('seat_order'). " WHERE order_id = '$int_orderid' and user_id = '".$_SESSION['user_id']."'");
	if (empty($arr_order)){
		ecs_header('location:shiting.php?id=1');
		exit;
	}
	
	//支付倒计时
	$int_endPayTime = $arr_order['add_time'] + 15 * 60;
	if ($int_endPayTime < gmtime()){
		$db->query('DELETE FROM '.$ecs->table('seat_order')." WHERE order_id = '$int_orderid'");
		ecs_header('location:index.php');
		exit;
	}
	$smarty->assign('endPayTime', local_date('M d, Y H:i:s',$int_endPayTime));


	$arr_order['SeatsName'] = str_replace('|', '<br/>', $arr_order['SeatsName']);
	$arr_order['date'] = local_date('Y-m-d', $arr_order['dateline']);
	$smarty->assign('order', $arr_order);

	$position = assign_ur_here(0, '<a href="shiting.php?id=1">视听盛宴</a> <code>&gt;</code> <a href="shiting.php?id=7">电影</a> <code>&gt;</code> <a href="shiting.php?act=zxxz">在线选座</a> <code>&gt;</code> 订单支付');
	$smarty->assign('page_title',       '订单支付_在线选座_电影_视听盛宴_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置 */

}else if ($str_action == 'order'){
	//下在线选择订单
/* 
	$str_seats  = $_POST['Seats'];
	$str_mobile = $_POST['mobile'];
	$int_filmNo   = !empty($_REQUEST['filmNo'])   ? $_REQUEST['filmNo']         : 0;//影片编号
	$int_cinemaNo = !empty($_REQUEST['cinemaNo']) ? $_REQUEST['cinemaNo']       : 0;//影院编号
	$int_seqNo    = !empty($_REQUEST['seqNo'])    ? $_REQUEST['seqNo']          : 0;//影院排期编号
	$int_hallNo   = !empty($_REQUEST['hallNo'])   ? $_REQUEST['hallNo']         : 0;//影庁编号
	$float_price  = number_format(floatval($_POST['price']), 2, '.', '');
	$int_orderPrice = ceil(floatval($_POST['price']) / 1.06);
	$int_orderPrice1 = ceil(floatval($_POST['price']) * 1.06);
	$str_seats    = $_POST['seats'];
	$int_seatCount = intval($_POST['seatsCount']);
	$flo_amount    = $int_orderPrice * $int_seatCount;
	$str_bestTime   = $_POST['best_time'];
	$str_filmName   = $_POST['filmName'];
	$str_cinemaName = $_POST['cinemaName'];
	$str_hallName   = $_POST['hallName'];
	$str_seatsName  = $_POST['seatsName'];
	$str_languageType  = $_POST['languageType'];
	$int_areaNo     = intval($_POST['areaNo']);
	$int_dateline   = intval($_POST['dateline']);

	if (empty($str_mobile)){
		show_message('请填写手机号码！');
	}
	if (empty($str_seats)){
		show_message('请选择座位！');
	}
	if (empty($int_seqNo)){
		show_message('影片排期已过，请重新选择！', 'shiting.php?id=1');
	}
	
	//用户卡余额
	$card_money = $GLOBALS['db']->getOne('SELECT card_money FROM '.$GLOBALS['ecs']->table('users')." WHERE user_id = '".intval($_SESSION['user_id'])."'");
	if (($int_orderPrice * $int_seatCount) > $card_money){
		show_message('抱歉您的卡余额不足！');
	}

	$arr_param = array(
		'Mobile'        => $str_mobile,
		'OrderType'     => 1,
		'CinemaNo'      => $int_cinemaNo,
		'FilmNo'        => $int_filmNo,
		'SeqNo'         => $int_seqNo,
		'HallNo'        => $int_hallNo,
		'Price'         => $float_price,
		'Seats'         => $str_seats,
		'CityNo'        => $int_areaNo,
		'IsCustomPrice' => 0,
		'Sign'          => md5($str_mobile.'1'.$int_cinemaNo.$int_filmNo.$int_seqNo.$int_hallNo.$float_price.$GLOBALS['_CFG']['yyappKey'])
	);
	$arr_result = getYYApi($arr_param, 'createSeatTicketOrder');//下选座订单
	if ($arr_result['head']['errCode'] == '0'){
		$arr_orderInfo = $arr_result['body'];
		//插入订单信息
		$str_sql = 'INSERT INTO '. $ecs->table('seat_order') ." (order_sn, user_id, user_name, order_status, mobile, best_time, city, AreaNo, FilmNo, FilmName, CinemaNo, CinemaName, SeqNo, HallNo, HallName, Seats, SeatsName, languageType, number, pay_id, pay_name, goods_amount, sjprice, price, order_amount, add_time, confirm_time, dateline) VALUES ('".$arr_orderInfo['OrderNo']."', '".$_SESSION['user_id']."', '".$_SESSION['user_name']."', '1', '$str_mobile', '$str_bestTime', '$int_cityId', '$int_areaNo', '".$arr_orderInfo['FilmNo']."', '$str_filmName', '".$arr_orderInfo['CinemaNo']."', '$str_cinemaName', '".$arr_orderInfo['SeqNo']."', '$int_hallNo', '$str_hallName', '$str_seats', '$str_seatsName', '$str_languageType', '$int_seatCount', '2', '华影支付', '$flo_amount', '".$arr_orderInfo['Price']."', '$int_orderPrice', '".$flo_amount."', '".local_strtotime($arr_orderInfo['OrderTime'])."', '".local_strtotime($arr_orderInfo['OrderTime'])."', '$int_dateline')";
		$query = $db->query($str_sql);
		$int_orderid = $db->insert_id();
		ecs_header('location:shiting_order.php?act=payinfo&id='.$int_orderid);//跳到支付页面
		exit;
	}else{
		show_message($arr_result['head']['errMsg']);
	} */
}else if ($str_action == 'dzqdh_respond'){
	//支付成功页面

	$int_orderId = intval($_GET['id']);
	$position = assign_ur_here(0, '<a href="shiting.php?id=1">视听盛宴</a> <code>&gt;</code> <a href="shiting.php?id=7">电影</a> <code>&gt;</code> <a href="shiting.php?act=zxxz">电子兑换券</a> <code>&gt;</code> 确认订单');
	$smarty->assign('page_title',       '确认订单_电子兑换券_电影_视听盛宴_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

}else if ($str_action == 'dzqdh_done'){
	//完成支付电子兑换券订单

	$str_mobile  = $_POST['mobile'];
	$str_orderSn = $_POST['order_sn'];
	$int_orderId = intval($_POST['order_id']);
	$str_password = $_POST['password'];

	$arr_orderInfo = $db->getRow('SELECT * FROM '.$ecs->table('dzq_order')." WHERE order_id = '$int_orderId'");
	$order_amount = price_format($arr_orderInfo['sjprice'])*$arr_orderInfo['number'];
	$float_price = number_format(floatval($order_amount), 2, '.', '');
	
	$order_amount1 = $arr_orderInfo['price']*$arr_orderInfo['number'];
	$float_price1 = number_format(floatval($order_amount1), 2, '.', '');	

	if (empty($arr_orderInfo)){
		show_message('抱歉，您提交的支付信息不存在');
	}

	if (empty($str_mobile)){
		show_message('手机号码不能为空');
	}

	if ($str_mobile != $arr_orderInfo['mobile']){
		show_message('您输入的手机号码和购票时的手机号码不一致');
	}

	if (empty($str_password)){
		show_message('卡密码不能为空');
	}
	
	//卡消费接口
	/* $arr_param = array(
		'cardId'     => $_SESSION['user_name'],
		'cardPwd'    => $str_password,
		'posJournal' => $str_orderSn,
		'payTime'    => local_date('YmdHis'),
		'payAmount'  => $float_price,
		'deviceId'   => $GLOBALS['_CFG']['deviceId'],
		'operId'     => $GLOBALS['_CFG']['operId'],
		'merchantId' => $GLOBALS['_CFG']['merchantId'],
		'goodName'   => '',
		'storeId'    => $GLOBALS['_CFG']['storeId']
	);
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');	
	
	$arr_cardInfo = getCardApi($arr_param, 'CARD-PAY', 7); */
	/** TODO 支付 （双卡版） */
	$arr_param = array(
			'CardInfo' => array( 'CardNo'=> $_SESSION['user_name'], 'CardPwd' => $str_password),
			'TransationInfo' => array( 'TransRequestPoints'=>$float_price,'TransSupplier'=>setCharset('中影电子券'))
	);
	$state = $cardPay->action($arr_param, 1, $str_orderSn);	
	
	if ($state == 0){
		$cardResult = $cardPay->getResult();
		//支付成功修改本网站订单状态
		$db->query('UPDATE '.$ecs->table('dzq_order')." SET pay_status = '2', pay_time = '".gmtime()."', money_paid = order_amount, order_amount = 0, api_order_id = '".$cardResult."' WHERE order_id = '$int_orderId'");
		
		//通兑票订单确认接口
		$arr_param = array(
			'Mobile'     => $str_mobile,
			'OrderNo'    => $str_orderSn,
			'orderPrice' => $float_price1,
			'payment'    => $float_price1,
			'DistributorUrl' => '',
			'sign'     => md5($str_mobile.$str_orderSn.$float_price1.$arr_param['DistributorUrl'].$GLOBALS['_CFG']['yyappKey']),
			'Remark'   => '',
			'SendType' => 3
		);

		$arr_result = getYYApi($arr_param, 'confirmOrder');//确认支付订单
		
		//if ($arr_result['head']['errCode'] == '0'){}
		$_SESSION['BalanceCash'] -= $float_price; //重新计算用户卡余额
		//更新卡金额
		$db->query('UPDATE '.$ecs->table('users')." SET card_money = card_money - ('$float_price') WHERE user_id = '".intval($_SESSION['user_id'])."'");
		if ($arr_result['body']['OrderStatus'] == '1'){
		 show_message('付款失败，如果卡点已扣请联系华影客服！');
		}
		
		ecs_header('location:shiting_order.php?act=dzqdh_respond&id='.$int_orderId);
		exit;
	}else{
		show_message($cardPay->getMessage());
	}


}else if ($str_action == 'dzqdh_payinfo'){
	//电子兑换券订单支付页面

	$int_orderid = intval($_REQUEST['id']);
	if (empty($int_orderid)){
		ecs_header('location:shiting.php?id=1');
		exit;
	}
	$arr_order = $db->getRow('SELECT * FROM ' .$ecs->table('dzq_order'). " WHERE order_id = '$int_orderid' and user_id = '".$_SESSION['user_id']."'");
	if (empty($arr_order)){
		ecs_header('location:shiting.php?id=1');
		exit;
	}
	//print_r($arr_order);
	$arr_order['price'] = price_format($arr_order['price']);
	$smarty->assign('order', $arr_order);

	$position = assign_ur_here(0, '<a href="shiting.php?id=1">视听盛宴</a> <code>&gt;</code> <a href="shiting.php?id=1">电影</a> <code>&gt;</code> <a href="shiting.php?act=dzqdh">电子券兑换</a> <code>&gt;</code> 确认订单');
	$smarty->assign('page_title',       '确认订单_电子券兑换_电影_视听盛宴_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

}else if ($str_action == 'dzqdh_order'){
	// 卡规则折扣
	$ratio = get_card_rule_ratio(10002);
	//电子券兑换订单
	$int_cAreaNo    = intval($_POST['areaNo']);//区域编号
	$int_areaName   = intval($_POST['areaName']);//区域名称
	$str_cinemaNo   = $_POST['cinemaNo'];//影院编号
	$str_cinemaName = $_POST['cinemaName'];//影院名称
	$int_ticketNo   = $_POST['ticketNo'];//电子券编号
	$str_mobile     = $_POST['mobile'];//手机
	$flo_price      = $_POST['price'];//价格
	
	// 基础价格
	$flo_prices     = number_format($_POST['price'],2,'.','');
	$int_number     = intval($_POST['number']);//数量
	
	//实际价格(商城卖的实际单价)
	if ($ratio !== false){
		$flo_sjprice = price_format(($_POST['price']/1.2*1.06)*$ratio);
		$flo_amount		= price_format($flo_price/1.2*1.06*$ratio * $int_number);
	}else{
		$flo_sjprice = price_format($_POST['price']/1.2*1.06);
		$flo_amount		= price_format($flo_price/1.2*1.06 * $int_number);
	}

	if (empty($str_mobile)){
		show_message('请填写手机号码');
	}

	//var_dump($_POST['ticketNo']);
	//exit;

	

	//获取电子券信息
	$str_tradaId = 'getCommTickets';
	$arr_param = array(
		'AreaNo'   => $int_areaNo,//区域编号
		'CinemaNo' => $str_cinemaNo//影院编号
	);
	$str_cacheName = $str_tradaId . '_' . $str_cinemaNo.'_'.$int_areaNo;//缓存名称
	//$arr_dzqData = F($str_cacheName, '', '1800', $arr_cityInfo['region_english'].'/');
	if (empty($arr_dzqData)){
		$arr_result = getYYApi($arr_param, $str_tradaId);
		if (!empty($arr_result['body']['item'])){
			$arr_dzqData = $arr_result['body']['item'];
			if (!empty($arr_dzqData)){
				$arr_type = array(
					'1' => '2D',
					'2' => '3D',
					'3' => '4D',
					'4' => 'IMAX',
					'5' => '点卡',
				);
				
				foreach ($arr_dzqData as $key=>$var){
					unset($arr_dzqData[$key]);
					$var['ProductSizeZn'] = $arr_type[$var['ProductSize']];
					$var['CinemaPriceFormat'] = price_format($var['CinemaPrice']);
					//$var['SalePriceFormat']   = ceil($var['SalePrice']);
					if ($ratio !== false){
						$var['SalePriceFormat'] = price_format(($var['SalePrice']/1.2*1.06)*$ratio);
					}else{
						$var['SalePriceFormat'] = price_format($var['SalePrice']/1.2*1.06);
					}
					$arr_dzqData[$var['TicketNo']] = $var;
				}
			}
			//F($str_cacheName, $arr_dzqData, 0, $arr_cityInfo['region_english'].'/');//写入缓存
		}
	}

	/*
	foreach($arr_dzqData as $key=>$var){
		if($int_ticketNo == $var['TicketNo']){
					$flo_price  = $var['SalePrice'];//价格
					
		}
	}
*/
	
	//print_r($flo_price);
	//print_r($flo_amount);

	//var_dump($arr_dzqData);
	//exit;
	//用户卡余额
	$card_money = $GLOBALS['db']->getOne('SELECT card_money FROM '.$GLOBALS['ecs']->table('users')." WHERE user_id = '".intval($_SESSION['user_id'])."'");
	
	if (($flo_sjprice * $int_number) > $card_money){
		show_message('抱歉您的卡余额不足！');
	}

  
	$arr_dzqinfo = $arr_dzqData[$int_ticketNo];
	$str_youxiaoq = $arr_dzqinfo['ValidType'] == 1 ? $arr_dzqinfo['HotSellEnd'].'天' : $arr_dzqinfo['HotSellEnd'];

	$str_sign = md5($str_mobile.'1'.$str_cinemaNo.$int_ticketNo.$flo_price.$int_number.$GLOBALS['_CFG']['yyappKey']);
	$arr_param = array(
		'Mobile' => $str_mobile,
		'OrderType' => 1,
		'ExOrderNo' => '',
		'CinemaNo'  => $str_cinemaNo,
		'TicketNo'  => $int_ticketNo, 
		'Price'     => $flo_price, 
		'Count'     => $int_number,
		'AreaNo'    => $int_areaNo,
		'IsCustomPrice' => 0,
		'Sign'      => $str_sign
	);
	//var_dump($arr_param);exit;
	
	$arr_result = getYYApi($arr_param, 'createCommTicketOrder');//下通兑票订单
	if ($arr_result['head']['errCode'] == '0'){
		$arr_orderInfo = $arr_result['body'];
		//插入订单信息
		$str_sql = 'INSERT INTO '.$ecs->table('dzq_order')." (order_sn, user_id, user_name, order_status, mobile, city, AreaNo, CinemaNo, CinemaName, TicketNo, TicketName, ProductSizeZn, TicketYXQ, number, pay_id, pay_name, price, sjprice, goods_amount, order_amount, add_time, confirm_time) VALUES ('".$arr_orderInfo['OrderNo']."', '".$_SESSION['user_id']."', '".$_SESSION['user_name']."', '1', '$str_mobile', '$int_cityId', '$int_cAreaNo', '$str_cinemaNo', '$str_cinemaName', '$int_ticketNo', '".$arr_dzqinfo['TicketName']."', '".$arr_dzqinfo['ProductSizeZn']."', '$str_youxiaoq', '$int_number', '2', '华影支付', '$flo_prices', '$flo_sjprice', '$flo_amount', '$flo_amount', '".gmtime()."', '".gmtime()."')";
		$query = $db->query($str_sql);
		$int_orderid = $db->insert_id();
		ecs_header('location:shiting_order.php?act=dzqdh_payinfo&id='.$int_orderid);//跳到支付页面
		exit;
	}else{
		show_message($arr_result['head']['errMsg']);
	}
}else{
	//在线选座
/* 
	$int_filmNo   = !empty($_REQUEST['filmNo'])   ? $_REQUEST['filmNo']   : 0;//影片编号
	if (empty($int_filmNo)){
		ecs_header("Location:shiting.php?id=1");
		exit;
	}

	//影片详细内容接口参数
	$str_tradaId = 'getFilmInfo';
	$arr_param = array(
		'filmNo'    => $int_filmNo,
		'IsParater' => 1
	);

	$str_cacheName = $str_tradaId . '_' . $int_filmNo;
	$arr_filmsInfo = F($str_cacheName, '', -1, $arr_cityInfo['region_english'].'/');//缓存半小时
	if (empty($arr_filmsInfo)){
		$arr_result = getYYApi($arr_param, $str_tradaId);
		if (!empty($arr_result['body'])){
			$arr_filmsInfo = $arr_result['body'];
			F($str_cacheName, $arr_filmsInfo, 0, $arr_cityInfo['region_english'].'/');//写入缓存
		}
	}
	if (empty($arr_filmsInfo)){//没有影片信息
		ecs_header("Location:shiting.php?id=1");
		exit;
	}
	$smarty->assign('filmInfo',  $arr_filmsInfo);

	//获取指定地区指定影片的排期信息
	$str_tradaId = 'getShowTimeByAreaNoFilmNo';
	$arr_param = array(
		'areaNo' => $int_areaNo,
		'filmNo' => $int_filmNo
	);
	$str_cacheName = $str_tradaId . '_' . $int_filmNo;

	$arr_filmsPaiq = F($str_cacheName, '', 1800, $arr_cityInfo['region_english'].'/');//缓存半小时
	if (empty($arr_filmsPaiq)){
		$arr_result = getYYApi($arr_param, $str_tradaId);
		if (!empty($arr_result['body']['item'])){
			$arr_filmsPaiq = $arr_result['body']['item'];
			if (!empty($arr_filmsPaiq)){
				$arr_xuanzInfo = array();
				foreach ($arr_filmsPaiq as $key=>$var){
					$arr_xuanzInfo[$var['ShowDate']]['Area'][$var['AreaNo']]['AreaNo']   = $var['AreaNo'];//区域编号
					$arr_xuanzInfo[$var['ShowDate']]['Area'][$var['AreaNo']]['AreaName'] = $var['AreaName'];//区域名称
					$arr_xuanzInfo[$var['ShowDate']]['Area'][$var['AreaNo']]['Cinema'][$var['CinemaNo']]['CinemaNo']      = $var['CinemaNo'];//影院编号
					$arr_xuanzInfo[$var['ShowDate']]['Area'][$var['AreaNo']]['Cinema'][$var['CinemaNo']]['CinemaName']    = $var['CinemaName'];//影院名称
					$arr_xuanzInfo[$var['ShowDate']]['Area'][$var['AreaNo']]['Cinema'][$var['CinemaNo']]['PhoneNo']       = $var['PhoneNo'];//影院电话
					$arr_xuanzInfo[$var['ShowDate']]['Area'][$var['AreaNo']]['Cinema'][$var['CinemaNo']]['AverageDegree'] = $var['AverageDegree'];//影院综合评分
					$arr_xuanzInfo[$var['ShowDate']]['Area'][$var['AreaNo']]['Cinema'][$var['CinemaNo']]['Address']       = $var['Address'];//影院地址
					$arr_xuanzInfo[$var['ShowDate']]['Area'][$var['AreaNo']]['Cinema'][$var['CinemaNo']]['LatLng']        = $var['LatLng'];//影院地址经纬度

					//指定同一日期同一影院的排期
					$arr_xuanzInfo[$var['ShowDate']]['Seq'][$var['CinemaNo']][$var['SeqNo']]['SeqNo']       = $var['SeqNo'];//影院排期编号
					$arr_xuanzInfo[$var['ShowDate']]['Seq'][$var['CinemaNo']][$var['SeqNo']]['ShowTime']    = $var['ShowTime'];//放映时间
					$arr_xuanzInfo[$var['ShowDate']]['Seq'][$var['CinemaNo']][$var['SeqNo']]['ShowType']    = $var['ShowType'];//放映类型
					$arr_xuanzInfo[$var['ShowDate']]['Seq'][$var['CinemaNo']][$var['SeqNo']]['Language']    = $var['Language'];//影院排期编号
					$arr_xuanzInfo[$var['ShowDate']]['Seq'][$var['CinemaNo']][$var['SeqNo']]['HallNo']      = $var['HallNo'];//影厅编号
					$arr_xuanzInfo[$var['ShowDate']]['Seq'][$var['CinemaNo']][$var['SeqNo']]['HallName']    = $var['HallName'];//影厅名称
					$arr_xuanzInfo[$var['ShowDate']]['Seq'][$var['CinemaNo']][$var['SeqNo']]['SeatNum']     = $var['SeatNum'];//影厅座位数
					$arr_xuanzInfo[$var['ShowDate']]['Seq'][$var['CinemaNo']][$var['SeqNo']]['CinemaPrice'] = $var['CinemaPrice'];//市场价
					$arr_xuanzInfo[$var['ShowDate']]['Seq'][$var['CinemaNo']][$var['SeqNo']]['SalePrice']   = $var['SalePrice'];//销售价
					$arr_xuanzInfo[$var['ShowDate']]['Seq'][$var['CinemaNo']][$var['SeqNo']]['SalePrice1']   = ceil($var['SalePrice']/1.06);//销售价
					
					//指定日期下的所有影院
					$arr_xuanzInfo[$var['ShowDate']]['all'][$var['CinemaNo']]['CinemaNo']      = $var['CinemaNo'];//影院编号
					$arr_xuanzInfo[$var['ShowDate']]['all'][$var['CinemaNo']]['CinemaName']    = $var['CinemaName'];//影院名称
					$arr_xuanzInfo[$var['ShowDate']]['all'][$var['CinemaNo']]['PhoneNo']       = $var['PhoneNo'];//影院电话
					$arr_xuanzInfo[$var['ShowDate']]['all'][$var['CinemaNo']]['AverageDegree'] = $var['AverageDegree'];//影院综合评分
					$arr_xuanzInfo[$var['ShowDate']]['all'][$var['CinemaNo']]['Address']       = $var['Address'];//影院地址
					$arr_xuanzInfo[$var['ShowDate']]['all'][$var['CinemaNo']]['LatLng']        = $var['LatLng'];//影院地址经纬度
				}
			}
			$arr_filmsPaiq = $arr_xuanzInfo;
			F($str_cacheName, $arr_filmsPaiq, 0, $arr_cityInfo['region_english'].'/');//写入缓存
		}
	}

	if (empty($arr_filmsPaiq)){//没有排期
		ecs_header("Location:shiting_show.php?id=$int_filmNo");
		exit;
	}

	$str_date     = !empty($_REQUEST['date'])     ? $_REQUEST['date']           : '';//时间
	$int_areaNo   = !empty($_REQUEST['areaNo'])   ? intval($_REQUEST['areaNo']) : 0;//区域编号
	$int_cinemaNo = !empty($_REQUEST['cinemaNo']) ? $_REQUEST['cinemaNo']       : 0;//影院编号
	$int_seqNo    = !empty($_REQUEST['seqNo'])    ? $_REQUEST['seqNo']          : 0;//影院排期编号

	if (empty($str_date) || empty($int_cinemaNo) || empty($int_seqNo)){//没有选择时间，影院，排期返回影片详细页
		ecs_header("Location:shiting_show.php?id=$int_filmNo");
	}
	$smarty->assign('date',  local_date('m月d日', local_strtotime($str_date)));
	$smarty->assign('time',  local_strtotime($str_date));
	$arr_week = array('日', '一', '二', '三', '四', '五', '六');
	$smarty->assign('week',  $arr_week[local_date('w', local_strtotime($str_date))]);

	$arr_filmPaiq = $arr_filmsPaiq[$str_date];//获取指定日期，指定影片下的排期内容
	
	//如果选择区域，显示区域名称
	if (!empty($int_areaNo)){
		$str_areaName = $arr_filmPaiq['Area'][$int_areaNo]['AreaName'];
		$smarty->assign('areaNo',  $int_areaNo);
		$smarty->assign('areaname',  $str_areaName);
	}
	
	//获取影院信息
	$arr_cinema = $arr_filmPaiq['all'][$int_cinemaNo];
	$smarty->assign('cinemaInfo',  $arr_cinema);


	//获取影院排期信息
	$arr_seq = $arr_filmPaiq['Seq'][$int_cinemaNo][$int_seqNo];
	$smarty->assign('seqInfo',  $arr_seq);

	
	//查询影厅实时座位getSeat
	$str_cacheName = 'getSeat_'.$int_cinemaNo.'_'.$int_seqNo;
	//$arr_seatInfo  = F($str_cacheName, '', 1800, $arr_cityInfo['region_english'].'/');//缓存半小时
	if (empty($arr_seatInfo)){
		$arr_param = array(
			'SeqNo' => $int_seqNo
		);
		$arr_result = getYYApi($arr_param, 'getSeat');
		//var_dump($arr_result);exit;
		if ($arr_result['body']['item']){
			$arr_seat = $arr_result['body']['item'];
			$int_rowNo = count($arr_seat);
			$arr_seatInfo = array();
			$str_width = 0;
			foreach ($arr_seat as $key=>$var){				
				$var['ColumnNoArr']   = explode(',', $var['ColumnNo']);//列数
				$var['SeatTypeArr']   = explode(',', $var['SeatType']);//座位类型1--座位 2-情侣座 3-走廊
				$var['SeatNoArr']     = explode(',', $var['SeatNo']);//座位编号
				$var['SeatStatusArr'] = explode(',', $var['SeatStatus']);//座位状态0--可选 1--已售 2--维修
				$var['ColumnWidth']   = count($var['ColumnNoArr']) * 17;
				$var['zzx']           = ceil(count($var['ColumnNoArr']) / 2);
				$var['firstColumn']   = ceil((699 - $var['ColumnWidth']) / 2);
				$var['zzxWidth']      = $var['zzx'] * 17;
				$var['firstColumn']   = 340 - $var['zzxWidth'];
				$var['allWidth']      = count($var['ColumnNoArr']) * 17 + $var['firstColumn'] + 20;
				$arr_seatInfo[$key]['LocNo']       = $var['LocNo'];
				$arr_seatInfo[$key]['HallNo']      = $var['HallNo'];
				$arr_seatInfo[$key]['HallName']    = $var['HallName'];
				$arr_seatInfo[$key]['RowNo']       = $var['RowNo'];
				$arr_seatInfo[$key]['SeatImgRow']  = $var['SeatImgRow'];
				$arr_seatInfo[$key]['ColumnWidth'] = $var['ColumnWidth'];
				$arr_seatInfo[$key]['zzx']         = $var['zzx'];
				$arr_seatInfo[$key]['firstColumn'] = $var['firstColumn'];
				$arr_seatInfo[$key]['allWidth']    = $var['allWidth'];
				$arr_seatInfo[$key]['Column']      = array();
				$int_index = 0;
				for ($i=0; $i<count($var['ColumnNoArr']); $i++){
					$arr_seatInfo[$key]['Column'][$int_index][$i]['ColumnNo']   = $var['ColumnNoArr'][$i];
					$arr_seatInfo[$key]['Column'][$int_index][$i]['SeatType']   = $var['SeatTypeArr'][$i];
					$arr_seatInfo[$key]['Column'][$int_index][$i]['SeatNo']     = $var['SeatNoArr'][$i];
					$arr_seatInfo[$key]['Column'][$int_index][$i]['SeatStatus'] = $var['SeatStatusArr'][$i];
					if ($i+1 == $var['zzx']){
						$int_index++;
					}
				}
			}

			F($str_cacheName, $arr_seatInfo, 0, $arr_cityInfo['region_english'].'/');//写入缓存
		}
	}
	
	$arr_width = array();
	foreach ($arr_seatInfo as $key=>$var){
		$arr_width[] = $var['allWidth'];
	}
	$smarty->assign('width',          max($arr_width)); //页面座位宽度
	$smarty->assign('seatInfo',       $arr_seatInfo);   //座位信息

	$position = assign_ur_here(0, '<a href="shiting.php?id=1">视听盛宴</a> <code>&gt;</code> <a href="shiting.php?id=7">电影</a> <code>&gt;</code> <a href="shiting.php?act=zxxz">在线选座</a> <code>&gt;</code> '.$arr_filmsInfo['FilmName']);
	$smarty->assign('page_title',       $arr_filmsInfo['FilmName'].'_在线选座_电影_视听盛宴_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

	$smarty->assign('ShowTypes',        array('2D', '3D', '4D', 'IMAX')); */
}

$smarty->display('shiting_order.dwt');