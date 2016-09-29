<?php
/**
 * 试听盛宴-----> 影院 ------> 下单
 * @var unknown_type
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/mobile/includes/lib_cinema.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_clips.php');
include_once(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_movie_times.php');

//根据城市id获取影院区域编号
$int_areaNo = getAreaNo(0,'komovie');

if (!isset($_REQUEST['step']))
{
	$_REQUEST['step'] = "order";
}

assign_template();

$smarty->assign('act', $_REQUEST['act']);

// 电子券下单
if ($_REQUEST['act'] == "orderDzq")
{
	$returnAjax = array( 'error'=>0, 'message'=>'');
	$int_areaNo = getAreaNo();
	// 卡规则折扣
	$ratio = getDzqRatio();
	//电子券兑换订单
	$int_cAreaNo    = intval($_POST['areaNo']);//区域编号
	$int_areaName   = $_POST['areaName'];//区域名称
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
		$returnAjax['error'] = 1;
		$returnAjax['message'] = '请填写手机号码';
		exit(json_encode($returnAjax));
	}
	
	// 获得影票信息
	$cinemaDzq = getCinemaDzq($str_cinemaNo, $ratio);
	
	// 余额不足判断
	$card_money = $GLOBALS['db']->getOne('SELECT card_money FROM '.$GLOBALS['ecs']->table('users')." WHERE user_id = '".intval($_SESSION['user_id'])."'");
	
	if (($flo_sjprice * $int_number) > $card_money){
		$returnAjax['error'] = 1;
		$returnAjax['message'] = '抱歉您的卡余额不足!';
		exit(json_encode($returnAjax));
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
		$str_sql = 'INSERT INTO '.$ecs->table('dzq_order')." (order_sn, user_id, user_name, order_status, mobile, city, AreaNo, CinemaNo, CinemaName, TicketNo, TicketName, ProductSizeZn, TicketYXQ, number, pay_id, pay_name, price, sjprice, goods_amount, order_amount, add_time, confirm_time, source, card_ratio, shop_ratio,raise,ext,real_price,cordon_show) VALUES ('".$arr_orderInfo['OrderNo']."', '".$_SESSION['user_id']."', '".$_SESSION['user_name']."', '1', '$str_mobile', '$int_cityId', '$int_cAreaNo', '$str_cinemaNo', '$str_cinemaName', '$int_ticketNo', '".$arr_dzqinfo['TicketName']."', '".$arr_dzqinfo['ProductSizeZn']."', '$str_youxiaoq', '$int_number', '2', '聚优支付', '$flo_prices', '$flo_sjprice', '$flo_amount', '$flo_amount', '".gmtime()."', '".gmtime()."', 0, '".$ratioDzq['card_ratio']."', '".$ratioDzq['shop_ratio']."', '".$ratioDzq['raise']."', '".$ratioDzq['ext']."', '".$ratioDzq['real_price']."', '".$ratioDzq['cordon_show']."')";
		$query = $db->query($str_sql);
		$returnAjax['message'] = $db->insert_id();
		exit(json_encode($returnAjax));		
	}else{
		$returnAjax['error'] = 1;
		$returnAjax['message'] = $arr_result['head']['errMsg'];
		exit(json_encode($returnAjax));
	}
}

// 电子券支付页面
elseif ($_REQUEST['act'] == "payinfoDzq")
{	
	$int_orderid = intval($_REQUEST['id']);
	$arr_order = $db->getRow('SELECT * FROM ' .$ecs->table('dzq_order'). " WHERE order_id = '$int_orderid' and user_id = '".$_SESSION['user_id']."'");
	if (empty($arr_order)){
		ecs_header('Location:index.php');
		exit;
	}
	// 已经支付了的订单，跳转到提示页面 
	if ($arr_order['pay_status'] == 2)
	{
		show_message('订单已经支付过了', '网站首页','index.php');
	}

	$arr_order['price'] = price_format($arr_order['price']);
	
	// 影院图片
	$arr_param = array( 'cinemaNo' => $arr_order['CinemaNo'] );	
    $arr_result = getYYApi($arr_param, 'getCinemaInfo');
    if (!empty($arr_result['body'])){
        $arr_data = $arr_result['body'];	       
    }
	
    $smarty->assign('backHtml',getBackHtml('movie.php'));
	$smarty->assign('cinemaLogo', $arr_data['CinemaLogo']);
	$smarty->assign('order', $arr_order);	
	$smarty->display('movie/dzqPayinfo.dwt');
}
// 电子券支付操作
elseif ($_REQUEST['act'] == 'doneDzq')
{
	$ajaxArray = array( 'error'=>0, 'message'=>'' );
	$int_orderId = intval($_POST['order_id']);
	$str_password = $_POST['password'];
	
	$arr_orderInfo = $db->getRow('SELECT * FROM '.$ecs->table('dzq_order')." WHERE order_id = '$int_orderId'");
	$order_amount = price_format($arr_orderInfo['sjprice'])*$arr_orderInfo['number'];
	$float_price = number_format(floatval($order_amount), 2, '.', '');
	
	$order_amount1 = $arr_orderInfo['price']*$arr_orderInfo['number'];
	$float_price1 = number_format(floatval($order_amount1), 2, '.', '');
	
	if (empty($arr_orderInfo))
	{
		$ajaxArray['error'] = 1;
		$ajaxArray['message'] = '抱歉，您提交的支付信息不存在';
		exit(json_encode($ajaxArray));
	}
	
	if (empty($str_password))
	{
		$ajaxArray['error'] = 1;
		$ajaxArray['message'] = '卡密码不能为空';
		exit(json_encode($ajaxArray));
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
		$arr_result['body']['OrderStatus'] == '0';
		//重新计算用户卡余额
		$_SESSION['BalanceCash'] -= $float_price; 
		//更新卡金额
		$db->query('UPDATE '.$ecs->table('users')." SET card_money = card_money - ('$float_price') WHERE user_id = '".intval($_SESSION['user_id'])."'");
		if ($arr_result['body']['OrderStatus'] == '1'){
		    $ajaxArray['error'] = 1;
		    $ajaxArray['message'] = '付款失败，如果卡点已扣请联系华影客服！';
		    exit(json_encode($ajaxArray));
		}
		$ajaxArray['message'] = '支付成功';
		exit(json_encode($ajaxArray));
	}
	else
	{
		$ajaxArray['error'] = 1;
		$ajaxArray['message'] = $cardPay->getMessage();
		exit(json_encode($ajaxArray));
	}
	
}

// 删除在线选座订单
else if($_REQUEST['act'] == 'delorder')
{
	//删除限时没支付的订单
	$int_orderId = intval($_GET['order_id']);
	$order_sn = $GLOBALS['db']->getOne('SELECT order_sn FROM '.$GLOBALS['ecs']->table('seats_order')." WHERE user_id = '".intval($_SESSION['user_id'])."' and id = '".$int_orderId);
	$orderQuery = getCDYapi(array('action'=>'order_Query', 'order_id'=>$order_sn));
	if($orderQuery['orders'][0]['orderStatus']==1){
		$db->query('UPDATE '.$ecs->table('seats_order')." SET order_status = 2 WHERE id = '$int_orderId'");
		getCDYapi(array('action'=>'order_Delete', 'order_id'=>$order_sn));
	}
}
/* 在线选座下单 */
else if ($_REQUEST['act'] == 'order'){
	//下在线选择订单
	$mobile 			= $_POST['mobile'];				// 手机号
	$planId 			= intval($_POST['planId']);		// 排期ID
	$cinemaId 			= intval($_POST['cinemaId']);		// 影院ID(网票网用)
	$show_index			= intval($_POST['showIndex']);		// 场次号(网票网用)

	$vipPrice  			= number_format(round($_POST['vipPrice'],1), 2, '.', '');  // 价格
	$seatCount 			= intval($_POST['seatsCount']);  // 座位数
	$totalMoney    		= $vipPrice * $seatCount;	// 总价格
	$extInfo            = addslashes_deep($_POST['extInfo']);

	$hallName   		= addslashes_deep(trim($_POST['hallName']));
	$featureTimeStr 	= addslashes_deep(trim($_POST['featureTimeStr']));
	$movieName 			= addslashes_deep(trim($_POST['movieName']));
	$cinemaName   		= addslashes_deep(trim($_POST['cinemaName']));
	$language  			= addslashes_deep(trim($_POST['language']));
	$seatsNo  			= addslashes_deep(trim($_POST['seatsNo']));		// 座位号
	$seatsName  		= addslashes_deep(trim($_POST['seatsName']));
	$movieId  		    = intval(trim($_POST['movieId']));

	$seatParam 			= stripslashes_deep(trim($_POST['seatParam']));
	$seatParamArr 		= json_decode($seatParam, TRUE);
	$seatParamUrl 		= array2url($seatParamArr);
	
	$unitPrice = $vipPrice;
	$money = number_format(round($seatCount*$unitPrice,1),2);

	$seatsNo = trim($seatsNo);
	
	// 卡规则比例
	$card_ratio = getMovieRatio();

	if (empty($mobile)){
		show_message('请填写手机号码！');
	}
	if (empty($seatsNo)){
		show_message('请选择座位！');
	}

	// 座位信息
	$seatsNameArr = array();
	if(!empty($seatsName)){
		$seatsNameArr = explode('|',$seatsName);
	}

	//用户卡余额
	$card_money = $GLOBALS['db']->getOne('SELECT card_money FROM '.$GLOBALS['ecs']->table('users')." WHERE user_id = '".intval($_SESSION['user_id'])."'");
	if ($totalMoney > $card_money){
		show_wap_message('抱歉您的卡余额不足！');
	}
	if(!empty($show_index)&&IS_MATE){
		$platform = PLATFORM_WANGMOVIE;
		include_once (ROOT_PATH . 'includes/lib_wpwMovieClass.php');
		$wpwMovie = new wpwMovie();
		//网票网下单操作
		//锁座
		$res_lock =  $wpwMovie -> sellLockSeat($show_index,$cinemaId,$seatsNo);
		if($res_lock['ErrNo'] != 0){show_message($res_lock['Msg']);}
		//获取服务器时间
//		$res_time = $wpwMovie -> doTarget('Base_ServerTime');
//		if($res_time['ErrNo'] != 0){show_message($res_time['Msg']);}
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
			if(empty($arr_result['Data'])){show_message('下单失败，请重新下单！');}
//			//查询订单
//			$order_result = $wpwMovie -> sellSearchOrderInfoBySid($arr_result['Data']['SID']);
//			if ($order_result['ErrNo'] != 0){show_message($order_result['Msg']);}
//			if(empty($order_result['Data'])){show_message('下单失败，请重新下单！');}

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
			show_message($arr_result['Msg']);
		}

	}else {
		$platform = PLATFORM_KOMOVIE;
		// 拆分座位号
		if(!empty($seatsNo)){
			$seatsNos = explode('|',$seatsNo);
			$seatsNos = implode(',',$seatsNos);
		}
		// 下单
		$arr_param = array(
			'action' => 'order_Add',
			'mobile' => $mobile,
			'seat_no' => $seatsNos,
			'plan_id' => $planId,
		);

		$arr_result = getCDYApi($arr_param);//下选座订单
		if ($arr_result['status'] == 0){
			$arr_orderInfo = $arr_result['order'];
		}else{
			show_message($arr_result['error']);
		}
	}
	$ratioMovie = getMovieRatio(true);

	$arr_orderInfo['PayNo'] = !empty($arr_orderInfo['PayNo'])?$arr_orderInfo['PayNo']:0;
	//下单成功插入订单信息
	$str_sql = 'INSERT INTO '. $ecs->table('seats_order')
		 ."(order_sn, user_id, user_name, order_status, mobile, city_id, activity_id, channel_id, count, agio, money, unit_price, seat_info, seat_no, hall_name, hall_id, language, screen_type, featuretime, pay_id,pay_name, add_time, payment_time, movie_name, cinema_name,param_url,source,movie_id,extInfo,card_ratio,shop_ratio,raise,ext,real_price,cordon_show,pay_no,platform)
	 	  VALUES
	      ('".$arr_orderInfo['orderId']."', '".$_SESSION['user_id']."', '".$_SESSION['user_name']."', '".$arr_orderInfo['orderStatus']."', '$mobile','".$int_cityId."', '".$arr_orderInfo['activityId']."', '".$arr_orderInfo['channelId']."', '".$seatCount."', '".$arr_orderInfo['agio']."', '".$money."', '".$unitPrice."', '".$seatsName."', '".$seatsNo."', '".$hallName."', '".$arr_orderInfo['plan']['hallNo']."', '".$arr_orderInfo['plan']['language']."', '".$arr_orderInfo['plan']['screenType']."', '".$featureTimeStr."', '2', '华影支付', '".gmtime()."', '0', '".$movieName."', '".$cinemaName."', '".$seatParamUrl."',0,'".$movieId."','".$extInfo."','".$ratioMovie['card_ratio']."','".$ratioMovie['shop_ratio']."', '".$ratioMovie['raise']."', '".$ratioMovie['ext']."','".$ratioMovie['real_price']."','".$ratioMovie['cordon_show']."','".$arr_orderInfo['PayNo']."','".$platform."')";

		$query = $db->query($str_sql);
		$int_orderid = $db->insert_id();
		ecs_header('location:movie_order.php?act=payinfoMovie&id='.$int_orderid);//跳到支付页面
		exit;
}

else if ($_REQUEST['act'] == 'payinfoMovie'){
	//支付线选座页面
	$int_orderid = intval($_REQUEST['id']);
	if (empty($int_orderid)){
		ecs_header('location:movie.php');
		exit;
	}
	$arr_order = $db->getRow('SELECT * FROM ' .$ecs->table('seats_order'). " WHERE id = '$int_orderid' and user_id = '".$_SESSION['user_id']."'");
	if (empty($arr_order)){
		ecs_header('location:movie.php');
		exit;
	}
	//过滤支付过的订单
	if($arr_order['order_status'] !=1){
		show_message('订单已经支付过了，请选择其他电影吧！',' ','movie.php');
		exit;
	}
	//支付倒计时
	$int_endPayTime = $arr_order['add_time'] + 15 * 60;
	if ($int_endPayTime < gmtime()){
		$db->query('UPDATE '.$ecs->table('seats_order')." SET order_status=2 WHERE id = '$int_orderid'");
		getCDYapi(array('action'=>'order_Delete', 'order_id'=>$arr_order['order_sn']));
		ecs_header('location:index.php');
		exit;
	}
	$smarty->assign('endPayTime', local_date('M d, Y H:i:s',$int_endPayTime));
	
	$arr_order['seat_info'] = str_replace('|', ', ', $arr_order['seat_info']);
	$arr_order['date'] = local_date('Y-m-d', $arr_order['add_time']);
	
	// 影片信息
	if($arr_order['platform'] == PLATFORM_KOMOVIE) {
		$movieDetail = getMovieDetail($arr_order['movie_id']);
	}elseif($arr_order['platform'] == PLATFORM_WANGMOVIE){
		$movieDetail = $db -> getRow("SELECT * FROM ".$ecs->table('mate_movie')." WHERE wangmovie_id = ".$arr_order['movie_id']);
		$moviesImages = moviesImages(array($movieDetail));
		$movieDetail = $moviesImages[0];
	}
	$smarty->assign('backHtml',getBackHtml('movie.php'));
	$smarty->assign('detail', $movieDetail);
	$smarty->assign('order', $arr_order);
	$smarty->display('movie/moviePayinfo.dwt');
}

/* 支付电影票 */
else if ($_REQUEST['act'] == 'doneMovie'){
	
	$ajaxArray = array( 'error'=>0, 'message'=>'' );
	//完成支付在线选座订单
	$int_orderId = intval($_POST['order_id']);
	$str_password = trim($_POST['password']);
	
	
	$arr_orderInfo = $db->getRow('SELECT * FROM '.$ecs->table('seats_order')." WHERE id = '$int_orderId'");	
	// 需要支付的电影票的价格
	$float_price = number_format(round($arr_orderInfo['agio'],1), 2, '.', '');
	// 需要支付的卡点的价格
	$card_price = number_format(round($arr_orderInfo['money'],1), 2, '.', '');

	if (empty($arr_orderInfo)){
		$ajaxArray['error'] = 1;
		$ajaxArray['message'] = '抱歉，您提交的支付信息不存在';
		exit(json_encode($ajaxArray));
	}

	// 检查订单是否在支付中，如果在支付中返回错误消息
	if ($arr_orderInfo['card_pay'] > 0){
		$ajaxArray['error'] = 1;
		$ajaxArray['message'] = '订单已经支付';
		exit(json_encode($ajaxArray));
	}
	
	if (empty($str_password)){
		$ajaxArray['error'] = 1;
		$ajaxArray['message'] = '卡密码不能为空';
		exit(json_encode($ajaxArray));
	}	

	//购票平台
	$platform = $arr_orderInfo['platform']?$arr_orderInfo['platform']:'抠电影';
	// 卡订单号
	$cardOrderId = local_date('ymdHis').mt_rand(1,1000);	
	//exit(json_encode(array('error'=>1, 'message'=>$int_orderId.'=2='.$str_password)));
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
			}
			if($buy_result['ErrNo'] != 0){
				$order_result = $wpwMovie -> sellSearchOrderInfoBySID($arr_orderInfo['order_sn']);
				if($order_result['Data'][0]['PayFlag']!=3) {
					$ajaxArray['error'] = 0;
					$ajaxArray['message'] = '正在出票中，如未收到短信请联系客服！';
					exit(json_encode($ajaxArray));
				}			}
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
			$ajaxArray['message'] = '支付成功';
			exit(json_encode($ajaxArray));
		}else{			
			$ajaxArray['error'] = 1;
			$ajaxArray['message'] = $arr_result['error'];
			exit(json_encode($ajaxArray));
		}
	
	}else{
		$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('seats_order')." SET card_order_id= '0' WHERE id = '$int_orderId'");
		$ajaxArray['error'] = 1;
		$ajaxArray['message'] = $cardPay->getMessage();
		exit(json_encode($ajaxArray));
	}
}
// 支付成功页面
else if ($_REQUEST['act'] == 'respond')
{
    $flowTab = !empty($_REQUEST['flow']) ? $_REQUEST['flow'] : 'movie'; 
    $smarty->assign('flow', $flowTab);
    $smarty->display('movie/movieRespond.dwt');
}












