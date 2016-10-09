<?php
/**
 * 试听盛宴-----> 影院 ------> 下单
 * @var unknown_type
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_cinema.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');

//根据城市id获取影院区域编号
$int_areaNo = getAreaNo(0,'komovie');

if (!isset($_REQUEST['step']))
{
	$_REQUEST['step'] = "order";
}

assign_template();

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

// 电子券下单
if ($_REQUEST['act'] == "orderDzq")
{
	$int_areaNo = getAreaNo();
	// 卡规则折扣
	$ratio = getDzqRatio();
	//电子券兑换订单
	$int_cAreaNo    = intval($_REQUEST['areaNo']);//区域编号
	$int_areaName   = $_REQUEST['areaName'];//区域名称
	$str_cinemaNo   = $_REQUEST['cinemaNo'];//影院编号
	$str_cinemaName = $_REQUEST['cinemaName'];//影院名称
	$int_ticketNo   = $_REQUEST['ticketNo'];//电子券编号
	$str_mobile     = $_REQUEST['mobile'];//手机
	$flo_price      = $_REQUEST['price'];//价格	
	
	// 基础价格
	$flo_prices     = number_format($_REQUEST['price'],2,'.','');
	$int_number     = intval($_REQUEST['number']);//数量
	
	//实际价格(商城卖的实际单价)
	if ($ratio !== false){
		$flo_sjprice = price_format(($flo_price/1.2*1.06)*$ratio);
		$flo_amount		= price_format($flo_price/1.2*1.06*$ratio * $int_number);
	}else{
		$flo_sjprice = price_format($flo_price/1.2*1.06);
		$flo_amount		= price_format($flo_price/1.2*1.06 * $int_number);
	}
	
	if (empty($str_mobile)){
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '请填写手机号码';
		JsonpEncode($jsonArray); 		
	}
	
	// 获得影票信息
	$cinemaDzq = getCinemaDzq($str_cinemaNo, $ratio);
	
	// 余额不足判断
	$card_money = $GLOBALS['db']->getOne('SELECT card_money FROM '.$GLOBALS['ecs']->table('users')." WHERE user_id = '".intval($_SESSION['user_id'])."'");
	
	if (($flo_sjprice * $int_number) > $card_money){
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '抱歉您的卡余额不足';
		JsonpEncode($jsonArray); 
	}
		
	$arr_dzqinfo = $cinemaDzq[$int_ticketNo];
		
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
	
	$arr_result = getYYApi($arr_param, 'createCommTicketOrder');//下通兑票订单
	if ($arr_result['head']['errCode'] == '0'){
	    $ratioDzq = getDzqRatio(true);
		$arr_orderInfo = $arr_result['body'];
		//插入订单信息
		$str_sql = 'INSERT INTO '.$ecs->table('dzq_order')." (order_sn, user_id, user_name, order_status, mobile, city, AreaNo, CinemaNo, CinemaName, TicketNo, TicketName, ProductSizeZn, TicketYXQ, number, pay_id, pay_name, price, sjprice, goods_amount, order_amount, add_time, confirm_time, source, card_ratio, shop_ratio,raise,ext,real_price,cordon_show) VALUES ('".$arr_orderInfo['OrderNo']."', '".$_SESSION['user_id']."', '".$_SESSION['user_name']."', '1', '$str_mobile', '$int_cityId', '$int_cAreaNo', '$str_cinemaNo', '$str_cinemaName', '$int_ticketNo', '".$arr_dzqinfo['TicketName']."', '".$arr_dzqinfo['ProductSizeZn']."', '$str_youxiaoq', '$int_number', '2', '聚优支付', '$flo_prices', '$flo_sjprice', '$flo_amount', '$flo_amount', '".gmtime()."', '".gmtime()."', 1,'".$ratioDzq['card_ratio']."', '".$ratioDzq['shop_ratio']."', '".$ratioDzq['raise']."', '".$ratioDzq['ext']."', '".$ratioDzq['real_price']."', '".$ratioDzq['cordon_show']."')";
		$query = $db->query($str_sql);
		// 下单成功，返回订单id
		$jsonArray['data']['orderid'] = $db->insert_id();
		JsonpEncode($jsonArray); 
	}else{		
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = $arr_result['head']['errMsg'];
		JsonpEncode($jsonArray); 
	}
}

// 电子券支付页面
elseif ($_REQUEST['act'] == "payinfoDzq")
{	
	$int_orderid = intval($_REQUEST['id']);
	$arr_order = $db->getRow('SELECT * FROM ' .$ecs->table('dzq_order'). " WHERE order_id = '$int_orderid' and user_id = '".$_SESSION['user_id']."'");
	if (empty($arr_order)){
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '没有订单信息';
		JsonpEncode($jsonArray); 
	}
	// 已经支付了的订单，跳转到提示页面 
	if ($arr_order['pay_status'] == 2)
	{	
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '订单已经支付过了';
		JsonpEncode($jsonArray); 
	}

	$arr_order['price'] = price_format($arr_order['price']);
	
	$jsonArray['data'] = $arr_order;
	JsonpEncode($jsonArray); 
}
// 电子券支付操作
elseif ($_REQUEST['act'] == 'doneDzq')
{	
	$int_orderId = intval($_REQUEST['order_id']);
	$str_password = $_REQUEST['password'];
	
	$arr_orderInfo = $db->getRow('SELECT * FROM '.$ecs->table('dzq_order')." WHERE order_id = '$int_orderId'");
	$order_amount = price_format($arr_orderInfo['sjprice'])*$arr_orderInfo['number'];
	$float_price = number_format(floatval($order_amount), 2, '.', '');
	
	$order_amount1 = $arr_orderInfo['price']*$arr_orderInfo['number'];
	$float_price1 = number_format(floatval($order_amount1), 2, '.', '');
	
	if (empty($arr_orderInfo))
	{
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '抱歉，您提交的支付信息不存在';
		JsonpEncode($jsonArray); 
	}
	
	if (empty($str_password))
	{		
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '卡密码不能为空';
		JsonpEncode($jsonArray); 		
	}
	
	// 已经支付了的订单
	if ($arr_orderInfo['pay_status'] == 2)
	{
	    $jsonArray['state'] = 'false';
	    $jsonArray['message'] = '订单已经支付过了';
	    JsonpEncode($jsonArray); 
	}
	
	// 卡消费
	$arr_param = array(
			'CardInfo' => array( 'CardNo'=> $_SESSION['user_name'], 'CardPwd' => $str_password),
			'TransationInfo' => array( 'TransRequestPoints'=>$float_price, 'TransSupplier'=>setCharset('中影电子券'))
	);
	
	if ($cardPay->action($arr_param, 1, $arr_orderInfo['order_sn']) == 0)
	{
		$cardResult = $cardPay->getResult();
		$db->query('UPDATE '.$ecs->table('dzq_order')." SET pay_status = '2', pay_time = '".gmtime()."', money_paid = order_amount, order_amount = 0, api_order_id = '".$cardResult."' WHERE order_id = '$int_orderId'");
		//通兑票订单确认接口
		$arr_param = array(
				'Mobile'     => $arr_orderInfo['mobile'],
				'OrderNo'    => $arr_orderInfo['order_sn'],
				'orderPrice' => $float_price1,
				'payment'    => $float_price1,
				'DistributorUrl' => '',
				'sign'     => md5($arr_orderInfo['mobile'].$arr_orderInfo['order_sn'].$float_price1.$arr_param['DistributorUrl'].$GLOBALS['_CFG']['yyappKey']),
				'Remark'   => '',
				'SendType' => 3
		);
		//确认支付订单
		$arr_result = getYYApi($arr_param, 'confirmOrder');
		//重新计算用户卡余额
		$_SESSION['BalanceCash'] -= $float_price; 
		//更新卡金额
		$db->query('UPDATE '.$ecs->table('users')." SET card_money = card_money - ('$float_price') WHERE user_id = '".intval($_SESSION['user_id'])."'");
		if ($arr_result['body']['OrderStatus'] == '1'){		   
		    $jsonArray['state'] = 'false';
		    $jsonArray['message'] = '付款失败，如果卡点已扣请联系客服';
		    JsonpEncode($jsonArray); 
		    
		}
		$jsonArray['message'] = '支付成功';
		JsonpEncode($jsonArray); 
	}
	else
	{
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = $cardPay->getMessage();
		JsonpEncode($jsonArray); 
	}
	
}

// 删除在线选座订单
else if($_REQUEST['act'] == 'delorder')
{
	//删除限时没支付的订单
	$int_orderId = intval($_GET['order_id']);
	$order_sn = $GLOBALS['db']->getOne('SELECT order_sn FROM '.$GLOBALS['ecs']->table('seats_order')." WHERE user_id = '".intval($_SESSION['user_id'])."' and id = '".$int_orderId."'");
	$orderQuery = getCDYapi(array('action'=>'order_Query', 'order_id'=>$order_sn));
	if($orderQuery['orders'][0]['orderStatus']==1){
		$db->query('UPDATE '.$ecs->table('seats_order')." SET order_status = 2 WHERE id = '$int_orderId'");
		getCDYapi(array('action'=>'order_Delete', 'order_id'=>$order_sn));
	}
}
/* 在线选座下单 */
else if ($_REQUEST['act'] == 'order'){
	//下在线选择订单
	$mobile 			= $_REQUEST['mobile'];				// 手机号
	$planId 			= intval($_REQUEST['planId']);		// 排期ID
	$cinemaId 			= intval($_REQUEST['cinemaId']);		// 影院ID(网票网用)
	$show_index			= intval($_REQUEST['showIndex']);		// 场次号(网票网用)

	$vipPrice  			= number_format(round($_REQUEST['vipPrice'],1), 2, '.', '');  // 价格
	$seatCount 			= intval($_REQUEST['seatsCount']);  // 座位数
	$totalMoney    		= $vipPrice * $seatCount;	// 总价格
	$extInfo            = addslashes_deep($_REQUEST['extInfo']);
	
	$hallName   		= addslashes_deep(trim($_REQUEST['hallName']));
	$featureTimeStr 	= addslashes_deep(trim($_REQUEST['featureTimeStr']));
	$movieName 			= addslashes_deep(trim($_REQUEST['movieName']));
	$cinemaName   		= addslashes_deep(trim($_REQUEST['cinemaName']));
	$language  			= addslashes_deep(trim($_REQUEST['language']));
	$seatsNo  			= addslashes_deep(trim($_REQUEST['seatsNo']));		// 座位号
	$seatsName  		= addslashes_deep(trim($_REQUEST['seatsName']));
	$movieId  		    = intval(trim($_REQUEST['movieId']));

	$seatParam 			= stripslashes_deep(trim($_REQUEST['seatParam']));
	$seatParamArr 		= json_decode($seatParam, TRUE);
	$seatParamUrl 		= array2url($seatParamArr);
	
	$unitPrice = $vipPrice;
	$money = number_format(round($seatCount*$unitPrice,1),2);

	$seatsNo = trim($seatsNo);

	if (empty($mobile)){
	    $jsonArray['state'] = 'false';
	    $jsonArray['message'] = '请填写手机号码';
	    JsonpEncode($jsonArray); 	
	}
	if (empty($seatsNo)){		
		$jsonArray['state'] = 'false';
	    $jsonArray['message'] = '请选择座位';
	    JsonpEncode($jsonArray); 	
	}

	// 座位信息
	$seatsNameArr = array();
	if(!empty($seatsName)){
		$seatsNameArr = explode('|',$seatsName);
	}

	//用户卡余额
	$card_money = $GLOBALS['db']->getOne('SELECT card_money FROM '.$GLOBALS['ecs']->table('users')." WHERE user_id = '".intval($_SESSION['user_id'])."'");
	if ($totalMoney > $card_money){
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '抱歉您的卡余额不足';
		JsonpEncode($jsonArray); 
	}
	if(!empty($show_index)&&IS_MATE){
		$platform = PLATFORM_WANGMOVIE;
		include_once (ROOT_PATH . 'includes/lib_wpwMovieClass.php');
		$wpwMovie = new wpwMovie();
		//网票网下单操作
		//锁座
		$res_lock =  $wpwMovie -> sellLockSeat($show_index,$cinemaId,$seatsNo);
		if($res_lock['ErrNo'] != 0){
			$jsonArray['state'] = 'false';
			$jsonArray['message'] = $res_lock['Msg'];
			JsonpEncode($jsonArray);
		}

		//申请下单
		$params = array(
			'SID' => $res_lock['Data'][0]['SID'],
			'PayType' =>9998,
			'AID' => 0,
			'Mobile' => $mobile,
			'MsgType' => 1,
			'Amount' => number_format(round($seatCount*$extInfo,1),2),
			'UserAmount' => $money,
			'GoodsType' => 1,
		);
		$arr_result = $wpwMovie -> doTarget('Sell_ApplyTicket',$params);
		if ($arr_result['ErrNo'] == 0){
			if(empty($arr_result['Data'])){
				$jsonArray['state'] = 'false';
				$jsonArray['message'] = '下单失败，请重新下单！';
				JsonpEncode($jsonArray);
			}

			$movieId = $db -> getOne("SELECT wangmovie_id FROM ".$ecs->table('mate_movie')." WHERE movieId = ".$movieId);

			$arr_orderInfo['orderId'] = $arr_result['Data'][0]['SID'];
			$arr_orderInfo['orderStatus'] = 1;//订单状态
			$arr_orderInfo['activityId'] = 0; //活动id
			$arr_orderInfo['channelId'] = 0;//购买渠道
			$arr_orderInfo['agio'] = $extInfo;//还需支付金额
			$arr_orderInfo['PayNo'] = $arr_result['Data'][0]['PayNo']; //对应订单的支付信息标识
			$arr_orderInfo['plan']['hallNo'] = 0;
			$arr_orderInfo['plan']['language'] = $language;
			$arr_orderInfo['plan']['screenType'] = '';

		}else{
			$jsonArray['state'] = 'false';
			$jsonArray['message'] = $arr_result['Msg'];
			JsonpEncode($jsonArray);
		}

	}else {
		$platform = PLATFORM_KOMOVIE;
		// 拆分座位号
		if (!empty($seatsNo)) {
			$seatsNos = explode('|', $seatsNo);
			$seatsNos = implode(',', $seatsNos);
		}
		// 下单
		$arr_param = array(
			'action' => 'order_Add',
			'mobile' => $mobile,
			'seat_no' => $seatsNos,
			'plan_id' => $planId,
		);
		$arr_result = getCDYApi($arr_param);//下选座订单
		if ($arr_result['status'] == 0) {
			$arr_orderInfo = $arr_result['order'];
		} else {
			$jsonArray['state'] = 'false';
			$jsonArray['message'] = $arr_result['error'];
			JsonpEncode($jsonArray);
		}
	}

	$ratioMovie = getMovieRatio(true);
	//插入订单信息
	$str_sql = 'INSERT INTO '. $ecs->table('seats_order')
		."(order_sn, user_id, user_name, order_status, mobile, city_id, activity_id, channel_id, count, agio, money, unit_price, seat_info, seat_no, hall_name, hall_id, language, screen_type, featuretime, pay_id,pay_name, add_time, payment_time, movie_name, cinema_name,param_url,source,movie_id,extInfo,card_ratio,shop_ratio,raise,ext,real_price,cordon_show,pay_no,platform)
	 	  VALUES
	      ('".$arr_orderInfo['orderId']."', '".$_SESSION['user_id']."', '".$_SESSION['user_name']."', '".$arr_orderInfo['orderStatus']."', '$mobile','".$int_cityId."', '".$arr_orderInfo['activityId']."', '".$arr_orderInfo['channelId']."', '".$seatCount."', '".$arr_orderInfo['agio']."', '".$money."', '".$unitPrice."', '".$seatsName."', '".$seatsNo."', '".$hallName."', '".$arr_orderInfo['plan']['hallNo']."', '".$arr_orderInfo['plan']['language']."', '".$arr_orderInfo['plan']['screenType']."', '".$featureTimeStr."', '2', '华影支付', '".gmtime()."', '0', '".$movieName."', '".$cinemaName."', '".$seatParamUrl."',0,'".$movieId."','".$extInfo."','".$ratioMovie['card_ratio']."','".$ratioMovie['shop_ratio']."', '".$ratioMovie['raise']."', '".$ratioMovie['ext']."','".$ratioMovie['real_price']."','".$ratioMovie['cordon_show']."','".$arr_orderInfo['PayNo']."','".$platform."')";
	$query = $db->query($str_sql);
	$int_orderid = $db->insert_id();

	$jsonArray['data']['orderid'] = $int_orderid;
	JsonpEncode($jsonArray);

}

else if ($_REQUEST['act'] == 'payinfoMovie'){
	//支付线选座页面
	$int_orderid = intval($_REQUEST['id']);
	if (empty($int_orderid)){
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '订单号不能为空';
		JsonpEncode($jsonArray); 
	}
	$arr_order = $db->getRow('SELECT * FROM ' .$ecs->table('seats_order'). " WHERE id = '$int_orderid' and user_id = '".$_SESSION['user_id']."'");
	if (empty($arr_order)){
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '订单不存在';
		JsonpEncode($jsonArray); 
	}
	//过滤支付过的订单
	if($arr_order['order_status'] !=1){		
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '订单已经支付过了';
		JsonpEncode($jsonArray); 
	}
	//支付倒计时
	$int_endPayTime = $arr_order['add_time'] + 15 * 60;
	if ($int_endPayTime < gmtime()){
		$db->query('UPDATE '.$ecs->table('seats_order')." SET order_status=2 WHERE id = '$int_orderid'");
		getCDYapi(array('action'=>'order_Delete', 'order_id'=>$arr_order['order_sn']));
		
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '订单超时，无法支付';
		JsonpEncode($jsonArray); 
	}

	$arr_order['seat_info'] = str_replace('|', ', ', $arr_order['seat_info']);
	$arr_order['date'] = local_date('Y-m-d', $arr_order['add_time']);
	$arr_order['end_paytime'] = local_date('M d, Y H:i:s',$int_endPayTime);
	
	$jsonArray['data'] = $arr_order;
	JsonpEncode($jsonArray); 
}

/* 支付电影票 */
else if ($_REQUEST['act'] == 'doneMovie'){	

	//完成支付在线选座订单
	$int_orderId = intval($_REQUEST['order_id']);
	$str_password = trim($_REQUEST['password']);
	
	$arr_orderInfo = $db->getRow('SELECT * FROM '.$ecs->table('seats_order')." WHERE id = '$int_orderId'");	
	// 需要支付的电影票的价格
	$float_price = number_format(round($arr_orderInfo['agio'],1), 2, '.', '');
	// 需要支付的卡点的价格
	$card_price = number_format(round($arr_orderInfo['money'],1), 2, '.', '');

	if (empty($arr_orderInfo)){		
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '抱歉，您提交的支付信息不存在';
		JsonpEncode($jsonArray); 
	}
	
	// 检查订单是否在支付中，如果在支付中返回错误消息
	if ($arr_orderInfo['card_pay'] > 0){		
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '订单已经支付';
		JsonpEncode($jsonArray); 
	}
	
	if (empty($str_password)){
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '卡密码不能为空';
		JsonpEncode($jsonArray); 
	}	
	
	$int_endPayTime = $arr_orderInfo['add_time'] + 15 * 60;
	if ($int_endPayTime < gmtime()){
	    $db->query('UPDATE '.$ecs->table('seats_order')." SET order_status=2 WHERE id = '$int_orderid'");	
	    $jsonArray['state'] = 'false';
	    $jsonArray['message'] = '订单超时，无法支付';
	    JsonpEncode($jsonArray); 
	}

	//购票平台
	$platform = $arr_orderInfo['platform']?$arr_orderInfo['platform']:'抠电影';

	// 卡订单号
	$cardOrderId = local_date('ymdHis').mt_rand(1,1000);
	
	/** TODO 支付 （双卡版） */
	$arr_param = array(
			'CardInfo' => array( 'CardNo'=> $_SESSION['user_name'], 'CardPwd' => $str_password),
			'TransationInfo' => array( 'TransRequestPoints'=>$card_price, 'TransSupplier'=>setCharset($platform))
	);
	$state = $cardPay->action($arr_param, 1, $cardOrderId);	
	if ($state == 0){
		$cardResult = $cardPay->getResult();
		$_SESSION['BalanceCash'] -= $card_price; //重新计算用户卡余额
		//更新卡金额
		$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('users')." SET card_money = card_money - ('$card_price') WHERE user_id = '".intval($_SESSION['user_id'])."'");
		//更新卡支付状态
		$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('seats_order')." SET card_pay = '1', api_order_id = '".$cardResult."', card_order_id= '".$cardOrderId."' WHERE id = $int_orderId");
		// 电影票支付
		if($platform == PLATFORM_KOMOVIE) {
			$arr_param = array(
				'action' => 'order_Confirm',
				'order_id' => $arr_orderInfo['order_sn'],
				'balance' => $float_price
			);
			$arr_result = getCDYApi($arr_param);
		}elseif($platform == PLATFORM_WANGMOVIE){
			include_once (ROOT_PATH . 'includes/lib_wpwMovieClass.php');
			$wpwMovie = new wpwMovie();
			for($i=0;$i<3;$i++){
				$buy_result = $wpwMovie -> sellBuyTicket($arr_orderInfo['order_sn'],$arr_orderInfo['pay_no'],$cardOrderId);
				if($buy_result['ErrNo'] == 0){
					break;
				}
				sleep(3);
			}
			if($buy_result['ErrNo'] != 0){
				$order_result = $wpwMovie -> sellSearchOrderInfoBySID($arr_orderInfo['order_sn']);
				if($order_result['Data'][0]['PayFlag']!=3) {
					$ajaxArray['error'] = 0;
					$ajaxArray['message'] = '正在出票中，如未收到短信请联系客服！';
					exit(json_encode($ajaxArray));
				}
			}
			if($buy_result['Data'][0]['Result']){
				$arr_result['status'] = 0;
			}else{
				$ajaxArray['error'] = 1;
				$ajaxArray['message'] = '购票失败，请联系客服！';
				exit(json_encode($ajaxArray));
			}
		}
		if($arr_result['status'] == 0){
			// 支付成功，更新订单状态
			$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('seats_order')." SET order_status = '3', payment_time = '".gmtime()."' WHERE id = '$int_orderId'");
		
    		$jsonArray['message'] = '支付成功';
    		JsonpEncode($jsonArray); 
		}else{			
		    $jsonArray['state'] = 'false';
		    $jsonArray['message'] = $arr_result['error'];
		    JsonpEncode($jsonArray); 			
		}
	
	}else{
		$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('seats_order')." SET card_order_id= '0' WHERE id = '$int_orderId'");
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = $cardPay->getMessage();
		JsonpEncode($jsonArray); 
	}
}