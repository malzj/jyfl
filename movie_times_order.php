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

//头部显示次数
$smarty->assign('maxCount', getMaxBuyCount());
//如果是次卡更新左侧导航链接
$smarty->assign('navigator_list', get_times_nav( get_navigator() ));

// 删除在线选座订单
if($_REQUEST['act'] == 'delorder')
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
	
	// 差额（需要支付宝补的差价）
	$diffPrice = getDiffPrice($unitPrice, $seatCount);

	// 剩余次数验证
	if ($diffPrice == -1){
	    show_message('您的剩余次数不足，请修改！');
	}
	
	if (empty($mobile)){
		show_message('请填写手机号码！');
	}
	if (empty($seatsNo)){
		show_message('请选择座位！');
	}

	// 拆分座位号
	if(!empty($seatsNo)){
		$seatsNos = explode('|',$seatsNo);
		$seatsNos = implode(',',$seatsNos);
	}

	// 座位信息
	$seatsNameArr = array();
	if(!empty($seatsName)){
		$seatsNameArr = explode('|',$seatsName);
	}

	// 下单
	$arr_param = array(
			'action'        => 'order_Add',
			'mobile'     	=> $mobile,
			'seat_no'      	=> $seatsNos,
			'plan_id'       => $planId,
	);

	$arr_result = getCDYApi($arr_param);//下选座订单
	if ($arr_result['status'] == 0){
		$arr_orderInfo = $arr_result['order'];		
		$ratioMovie = getMovieRatio(true);
		//插入订单信息
		$str_sql = 'INSERT INTO '. $ecs->table('seats_order') ."(order_sn, user_id, user_name, order_status, mobile, city_id, activity_id, channel_id, count, agio, money, unit_price, seat_info, seat_no, hall_name, hall_id, language, screen_type, featuretime, pay_id,pay_name, add_time, payment_time, movie_name, cinema_name,param_url,source,movie_id,extInfo,card_ratio,shop_ratio,raise,ext,diff_price,cika_agio) VALUES('".$arr_orderInfo['orderId']."', '".$_SESSION['user_id']."', '".$_SESSION['user_name']."', '".$arr_orderInfo['orderStatus']."', '$mobile','".$int_cityId."', '".$arr_orderInfo['activityId']."', '".$arr_orderInfo['channelId']."', '".$seatCount."', '".$arr_orderInfo['agio']."', '".$money."', '".$unitPrice."', '".$seatsName."', '".$seatsNo."', '".$hallName."', '".$arr_orderInfo['plan']['hallNo']."', '".$arr_orderInfo['plan']['language']."', '".$arr_orderInfo['plan']['screenType']."', '".$featureTimeStr."', '2', '华影支付', '".gmtime()."', '0', '".$movieName."', '".$cinemaName."', '".$seatParamUrl."',0,'".$movieId."','".$extInfo."','".$ratioMovie['card_ratio']."','".$ratioMovie['shop_ratio']."', '".$ratioMovie['raise']."', '".$ratioMovie['ext']."','".$diffPrice."','".getBuyPrice($unitPrice)."')";
		$query = $db->query($str_sql);
		$int_orderid = $db->insert_id();
		
		/** 
		 * 下单成功，计算需要补差价的余额
		 * 差价是0，不记录支付日志
		 * 差价大于0，记录支付日志
		 */
		
		$payment_info = array();
		$payment_info = payment_info(3);
		
		if($diffPrice > 0)
		{		   
		    //记录支付log
            $log_id = insert_pay_log($int_orderid, $diffPrice, $type=PAY_MOVIE_ALIPAY, 0);
	    }
	    
		ecs_header('location:movie_times_order.php?act=payinfoMovie&id='.$int_orderid.'&log_id='.$log_id);//跳到支付页面
		exit;
	}else{
		show_message($arr_result['error']);
	}
}

else if ($_REQUEST['act'] == 'payinfoMovie'){
    
    include_once(ROOT_PATH .'includes/lib_payment.php');
    
	//支付线选座页面
	$int_orderid = intval($_REQUEST['id']);
	$log_id = intval($_REQUEST['log_id']);
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
	
	// 是否显示支付宝支付，默认是0，不显示
	$alipay_show = 0;
	
	/**
	 * 如果需要补差价，就生成支付宝连接地址
	 */
	if ( floatval($arr_order['diff_price']) > 0)
	{
	    $payment_info = array();
	    $payment_info = payment_info(3);
	    
	    //取得支付信息，生成支付代码
	    $payment = unserialize_config($payment_info['pay_config']);
	    
	    //生成伪订单号, 不足的时候补0
	    $order = array();
	    $order['order_sn']       = $arr_order['order_sn'];
	    $order['log_id']         = $log_id;
	    $order['order_amount']   = $arr_order['diff_price'];
	    	    
	    /* 调用相应的支付方式文件 */
	    include_once(ROOT_PATH . 'includes/modules/payment/' . $payment_info['pay_code'] . '.php');
	    
	    /* 取得在线支付方式的支付按钮 */
	    $pay_obj = new $payment_info['pay_code'];
	    $alipay_url = $pay_obj->get_code($order, $payment, true);
	    
	    $alipay_show = 1;	    
	    $smarty->assign('alipay_url', $alipay_url);
	}
	
	
	$smarty->assign('alipay_show',$alipay_show);
	
	// 影片信息
	$movieDetail = getMovieDetail($arr_order['movie_id']);
	$smarty->assign('backHtml',getBackHtml('movie.php'));
	$smarty->assign('detail', $movieDetail);
	$smarty->assign('order', $arr_order);
	$smarty->display('movie_times/tmoviePayinfo.dwt');
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
	$card_price = number_format(round($arr_orderInfo['cika_agio'],1), 2, '.', '');

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
	
	// 卡订单号
	$cardOrderId = local_date('ymdHis').mt_rand(1,1000);	
	/** TODO 支付 （双卡版） */
	$arr_param = array(
			'CardInfo' => array( 'CardNo'=> $_SESSION['user_name'], 'CardPwd' => $str_password),
			'TransationInfo' => array( 'TransRequestPoints'=>$card_price, 'TransSupplier'=>setCharset('抠电影'))
	);
	$state = $cardPay->action($arr_param, 1, $cardOrderId);	
	//$state = 0;
	if ($state == 0){
		$cardResult = $cardPay->getResult();
		$_SESSION['BalanceCash'] -= $card_price; //重新计算用户卡余额
		//更新卡金额
		$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('users')." SET card_money = card_money - ('$card_price') WHERE user_id = '".intval($_SESSION['user_id'])."'");
		//更新卡支付状态
		$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('seats_order')." SET card_pay = '1', api_order_id = '".$cardResult."', card_order_id= '".$cardOrderId."' WHERE id = $int_orderId");
		// 电影票支付
		$arr_param = array(
				'action'     => 'order_Confirm',
				'order_id'   => $arr_orderInfo['order_sn'],
				'balance'	 => $float_price
		);
		$arr_result['status'] = 0;
		//$arr_result = getCDYApi($arr_param);
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
    $smarty->display('movie_times/tmovieRespond.dwt');
}












