<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_basic.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_dongsport.php');

if ((DEBUG_MODE & 2) != 2)
{
	$smarty->caching = false;
}

if (!isset($_REQUEST['step']))
{
    $_REQUEST['step'] = "cart";
}
assign_template();
// 购物车
if ($_REQUEST['step'] == 'cart')
{	
	// 产品id
	$product = intval($_REQUEST['product']);

	// 游玩日期
	$travelDate = trim($_REQUEST['date']);
	if ($product == 0)
	{
		ecs_header('location:venues.php');
		exit;
	}
	
	// 产品详细	
	$detail = get_detail($product);
	
	// 没有产品详细，跳转到门票列表
	$detail_filter = array_filter($detail);
	if (empty($detail_filter))
	{
		ecs_header("location:venues.php");
		exit;
	}
	
	// 价格日历为空，跳转到产品详情页
	if (empty($travelDate))
		ecs_header("location:ticket_show.php?productno=".$product);
	else
		$date = $travelDate;
		
	$prices = array( 'productNo'=>$product, 'travelDate'=>$date );
	$price_tmp = getDongapi('price', $prices);
	$price = $price_tmp['prices']['price'];	
	if (empty($price))
	{
		show_message('产品不满足预订条件，无法进行预订!','返回门票列表','venues.php');
		exit;
	}
	
	// 没选择日期的情况下，取第一个时间, 如果当前下单时间 大于$detail['startTime']时间，取第二天的时间
	if ($detail['startTime'] != '24:00' && empty($travelDate)
			&& strtotime(local_date('Y-m-d H:i:s',gmtime())) >  strtotime(local_date('Y-m-d ',gmtime()).$detail['startTime'].':00') )
	{
		$travelDate = $price[1]['date']; 
		
	}
	
	// 如果日期还依然是空的，取第一个日期（执行这一步，说明上面的条件没有执行）	
	if (empty($travelDate))
	{
		$travelDate = $price[0]['date'];
	}
	
	// 提起多少天预订，未达到指定天数，跳转到上一页
	$postDate = strtotime($travelDate);
	$thisDate = strtotime(date('Y-m-d',strtotime(local_date('Y-m-d H:i:s'))));
	$deffDay = round(($postDate-$thisDate)/3600/24);
	if ($deffDay < $detail['startDay'])
	{
		show_message('此产品需要提前'.$detail['startDay'].'天预订，请从新选择一个时间！');
		exit;
	}
		
	// 得到当前日期对应的价格信息
	$salePrice = check_price($price,$travelDate);
	
	if ($salePrice == 0)
	{
		show_message('这个时间不能预订，请选择其他时间段');
	}	
	
	// 商城售价
	$detail['salePrice'] = $salePrice;
	
	// 有效期字符串的处理
	$validityType = array(
		'0'=>'本产品选定后 %s 当天有效',
		'1'=>'本产品预订后从 %s 往后延长 %u 天有效',
		'2'=>'本产品预订后从 %s 到 %s 有效',
		'3'=>'本产品选定后从 %s 往后延长 %u 天有效',
		'4'=>'本产品选定后从 %s 到 %s 有效'
	);
	
	if($detail['validityType'] == 0)
		$validity = sprintf($validityType[0], $travelDate);	
	else
		$validity = sprintf($validityType[$detail['validityType']], $travelDate, $detail['validityCon']);
	
	// 联系人信息
	$fields    = array(); 
	$custField = explode(',',$detail['custField']);
	$custField = array_filter($custField);
	
	foreach ( $custField as $keys=>$link){
		if ($link == 'link_credit_no')
		{
			$fields[$keys] 	= link_info('link_credit_type');
			$keys++;
		}
		$fields[$keys] 	= link_info($link);
	
	}
	
	// 运费处理
	$expressPrice = 0;
	if ($detail['isExpress'] == 1)
	{
		$expressPrice = $detail['expressPrice'];
	}
	global $validity;
	$smarty->assign('travelDate', 	$travelDate);
	$smarty->assign('expressPrice',	$expressPrice);
	$smarty->assign('fields', 		$fields);
	$smarty->assign('validity', 	$validity);
	$smarty->assign('detail', 		$detail);
	$smarty->assign('backHtml',     getBackHtml('venuesindex.php'));
	$smarty->display('venues/ticketOrder.dwt');
}

// 下单
else if ($_REQUEST['step'] == 'done')
{	
    $customRatio = get_card_rule_ratio(10003,true);
    
	$num 				=  !empty($_POST['goods_number']) ? intval($_POST['goods_number']) : 0 ;
	$productNo	 		=  !empty($_POST['productno']) ? intval($_POST['productno']) : 0;
	$traveldate 		=  trim($_POST['traveldate']);
	
	// 联系人信息
	$link_man 			=  !empty($_POST['links']['link_man']) ? addslashes_deep($_POST['links']['link_man']) : '';
	$link_phone 		=  !empty($_POST['links']['link_phone']) ? addslashes_deep($_POST['links']['link_phone']) : '';
	$link_email 		=  !empty($_POST['links']['link_email']) ? addslashes_deep($_POST['links']['link_email']) : '';
	$link_address		=  !empty($_POST['links']['link_address']) ? addslashes_deep($_POST['links']['link_address']) : '';
	$linkCode 			=  !empty($_POST['links']['linkCode']) ? intval($_POST['links']['linkCode']) : '';
	$link_credit_type 	=  !empty($_POST['links']['link_credit_type']) ? intval($_POST['links']['link_credit_type']) : '';
	$link_credit_no		=  !empty($_POST['links']['link_credit_no']) ? addslashes_deep($_POST['links']['link_credit_no']) : '';
	$secret             =  !empty($_POST['secret']) ? $_POST['secret'].md5($productNo) : null ;
	
	if ($productNo == 0)
	{
		ecs_header('location:venues.php');
		exit;
	} 
	
	// 产品详细
	$detail = get_detail($productNo);
	
	if ($num == 0)
	{
		show_message('产品预订数量不能为空！');
	}
	
	// 如果预订数量少于最低产品数量，返回上一页
	if ($num > 0 && $detail['startNum'] > 0 && $num < $detail['startNum'])
	{
		show_message('最低预订产品数量是 '.$detail['startNum'].' , 请返回修改！');
	}
	
	// 如果预订数量大于最多产品数量，返回上一页
	if ($num > 0 && $detail['maxNum'] > 0 && $num > $detail['startNum'])
	{
		show_message('最多预订产品数量是 '.$detail['maxNum']. ' , 请返回修改！');
	}
	
	// 价格日历
	$prices = array( 'productNo'=>$productNo, 'travelDate'=>$traveldate );
	$price_tmp = getDongapi('price', $prices);
	$price = $price_tmp['prices']['price'];
	if (empty($price))
	{
		show_message('产品不满足预订条件，无法进行预订!','返回场馆列表','venues.php');
		exit;
	}
	
	// 得到当前日期对应的价格信息
	$salePrice = check_price($price,$traveldate);
	
	if ($salePrice == 0)
	{
		show_message('这个时间不能预订，请选择其他时间段');
	}		
	// 商城扣点计算
	$detail['salePrice'] = $salePrice;	
	
	// 卡要支付的总金额
	$cardPrice = round($detail['salePrice'] * $num,1);
	$custField = explode(',',$detail['custField']);
	$custField = array_filter($custField);
	
	$param  = "<order>";
	$param .= "<travel_date>".$traveldate."</travel_date>"; 	// 门票日期
	$param .= "<info_id>".$productNo."</info_id>";		 		// 产品号
	$param .= "<cust_id>247640</cust_id>";						// 分销商ID
	$param .= "<num>".$num."</num>";							// 数量
	// 联系人信息
	foreach($custField as $field){
		$param .= "<".$field.">".${$field}."</".$field.">";
	}	
	$param .= "</order>";
	
	// 下单并保存到订单表里
 	$orders = getDongapi('order',array('param'=>$param));
 	if($orders['status'] == 0)
 	{
 		show_message($orders['msg']);
 		exit;
 	} 

 	$default = array(
 	    'is_pay'    => 0,
 	    'state'     => 0,
 	    'add_time'  => local_gettime(),
 	    'venueName' => $detail['viewName'],
 	    'venueId'   => 0,
 	    'infoId'    => $detail['productNo'],
 	    'link_man'  => $link_man,
 	    'link_phone'=> $link_phone,
 	    'date'      => $traveldate,
 	    'order_sn'  => get_order_sn(),
 	    'username'  => $_SESSION['user_name'],
 	    'user_id'   => $_SESSION['user_id'],
 	    'total'     => $num,
 	    'place'     => $detail['viewAddress'],
 	    'money'     => $cardPrice,
 	    'api_order_id' =>$orders['order_id'],
 	    'secret'    => $secret,
 	    'source'    => 1,
 	    'market_price' => check_price($price,$traveldate,true),
 	    'unit_price'=> $detail['salePrice'],
 	    'shop_ratio'  => $customRatio['shop_ratio'],
        'card_ratio'  => $customRatio['card_ratio'],
        'raise'       => $customRatio['raise'],
        'ext'         => $customRatio['ext'],
 	    'real_price'  => $customRatio['real_price'],
 	    'cordon_show' => $customRatio['cordon_show']
 	); 	
 	$cols = array_keys($default);
 	$GLOBALS['db']->query(' INSERT INTO '.$GLOBALS['ecs']->table('venues_order')." ( ".implode(',', $cols)." ) VALUES ('".implode("','", $default)."')");
 	
	$lists = array(
	    'id'           => $GLOBALS['db']->insert_id(),
		'api_order_id' 	 => $orders['order_id'],
		'num'		 => $num,
		'price'		 => $detail['salePrice'],
		'money'      => $cardPrice,
		'start'		 => '待支付',
		'orderPolicy'=> $detail['orderPolicy'],
		'phone'		 => $link_phone
	);	
	
	$validityType = array(
			'0'=>' 预约成功当天有效',
			'1'=>'  %u 天',
			'2'=>' %s',
			'3'=>'  %u 天',
			'4'=>' %s '
	);
	if($detail['validityType'] == 0)
		$validity = $validityType[0];
	else
		$validity = sprintf($validityType[$detail['validityType']], $detail['validityCon']);
	$smarty->assign('validit', 	$validity);
	$smarty->assign('list', $lists);
	$smarty->assign('backHtml',  getBackHtml('venuesindex.php'));
	$smarty->display('venues/ticketPay.dwt');
}
// 我的订单点过来的支付操作
else if ($_REQUEST['step'] == 'upay'){
	
	$orderId = isset($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 0 ;
	if ($orderId == 0)
	{
		show_message('非法操作');
	}
	
	$orders = $db->getAll('SELECT * FROM '.$ecs->table('venues_order').' WHERE id = '.$orderId);
	if (empty($orders))
	{
		show_message('订单信息不正确');
	}	
	
	if ($orders[0]['is_pay'] == 1)
	{		
		show_message('你已经支付过，请不要重复支付！');
	}
	
	// 产品详细
	$detail = get_detail($orders[0]['infoId']);
	
	$lists = array(
	        'id'           => $orders[0]['id'],
			'api_order_id' 	 => $orders[0]['api_order_id'],
			'num'		 => $orders[0]['total'],
			'price'		 => $orders[0]['unit_price'],
			'money'      => $orders[0]['money'],
			'start'		 => '待支付',
			'orderPolicy'=> $detail['orderPolicy'],
			'phone'		 => $orders[0]['link_phone']
	);
	
	//有效期

	$validityType = array(
			'0'=>' 预约成功当天有效',
			'1'=>'  %u 天',
			'2'=>' %s',
			'3'=>'  %u 天',
			'4'=>' %s '
	);
	if($detail['validityType'] == 0)
		$validity = $validityType[0];
	else
		$validity = sprintf($validityType[$detail['validityType']], $detail['validityCon']);
	$smarty->assign('validit', 	$validity);
	$smarty->assign('list', $lists);	
	
	$smarty->assign('backHtml', getBackHtml('venuesindex.php'));
	$smarty->display('venues/ticketPay.dwt');
}
// 支付
else if ($_REQUEST['step'] == 'pay')
{
	$str_password = !empty($_POST['password']) ? $_POST['password'] : '';
	$order_id     = $_POST['order_id'];
	$order_amount = floatval($_POST['order_amount']);
	
	$arr_result = array('error' => 0, 'message' => '', 'content' => '');
	
	if (empty($str_password)){
		$arr_result['error'] = 1;
		$arr_result['message'] = '卡密码不能为空';
		die(json_encode($arr_result));
	}
	if (empty($order_id)){
		$arr_result['error'] = 2;
		$arr_result['message'] = '订单信息不正确';
		die(json_encode($arr_result));
	}
	if ($order_amount > $_SESSION['BalanceCash']){
		$arr_result['error'] = 3;
		$arr_result['message'] = '卡余额不足';
		die(json_encode($arr_result));
	}
	
	$orders = $db->getAll('SELECT * FROM '.$ecs->table('venues_order').' WHERE id = '.$order_id);
	if (empty($orders))
	{
		$arr_result['error'] = 2;
		$arr_result['message'] = '订单信息不正确';
		die(json_encode($arr_result));
	}
	
	if ($orders[0]['card_state'] == 1)
	{
		$arr_result['error'] = 2;
		$arr_result['message'] = '你已经支付过，请不要重复支付！';
		die(json_encode($arr_result));
	}	
	
	/** TODO 支付 （双卡版） */
	$arr_param = array(
			'CardInfo' => array( 'CardNo'=> $_SESSION['user_name'], 'CardPwd' => $str_password),
			'TransationInfo' => array( 'TransRequestPoints'=>$orders[0]['money'], 'TransSupplier'=>setCharset('动网门票'))
	);
	$state = $cardPay->action($arr_param, 1, $orders[0]['order_sn']);
	
	if ($state == 0){
		$cardResult = $cardPay->getResult();
		$_SESSION['BalanceCash'] -= $orders[0]['money']; //重新计算用户卡余额
		
		$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('users')." SET card_money = card_money - (".$orders[0]['money'].") WHERE user_id = '".intval($_SESSION['user_id'])."'");
		// 动网支付
		$dongPay = getDongapi('pay',array('orderId'=>$orders[0]['api_order_id']));
		if ($dongPay['status'] == '1')
		{
			$arr_result['error'] = 0;
			$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('venues_order'). " SET is_pay = 1, state = 1, api_card_Id = '".$cardResult."' WHERE id = ".$orders[0]['id']);
		}
		else {
			$arr_result['error'] = 2;
			$arr_result['message'] = '预订失败，如果卡点已扣，请联系聚优客服！';				
			$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('venues_order'). " SET is_pay = 1, api_card_Id = '".$cardResult."', msg='".$dongPay['msg']."' WHERE id = ".$orders[0]['id']);
		}			
	}
	else {
		$arr_result['error']   = 5;
		$arr_result['message'] = $cardPay->getMessage();
	}
	die(json_encode($arr_result));
}
else if($_REQUEST['step'] == 'respond'){
    $smarty->display('venues/respond2.dwt');
}



// 产品详细
function get_detail($product){	
	$detailName = 'dongpiao-detail-'.$product;
	$detail = F($detailName, '', 1800, 'dongpiao/');
	$detail_filter = array_filter($detail);
	if (empty($detail_filter))
	{
		$arr_param = array( 'productNo' => $product	);
		$detail_tmp = getDongapi('detail', $arr_param );
		if($detail_tmp['status']==0)
		{
			show_message($detail_tmp['msg'], '', 'dongpiao.php');
		}
		$detail = $detail_tmp['product'];
		F($detailName, $detail, 1800, 'dongpiao/');
	}
	return $detail;	
}


// 日期的合法性，根据选择的日期，遍历价格日历，如果不存在返回false ，否则 true
function check_price($price, $date, $is_api_price=false){
    $customRatio = get_card_rule_ratio(10003);
    // 售价
	$salePrice = 0;
	// 接口价
	$apiPrice = 0;
	// 如果只有一个时间
	if ($price['date'] == $date)
	{
		// 计算扣点基础价格
		$salePrice = initSalePrice($price['salePrice'], $customRatio);
		$apiPrice = $price['salePrice'];
	}
	// 多个时间的时候
	foreach ($price as $pk=>$ps)
	{		
		if ($ps['date'] == $date )
		{
			$salePrice = initSalePrice($ps['salePrice'], $customRatio);
			$apiPrice = $ps['salePrice'];
		}
	}
	if ($is_api_price == true)
	    return $apiPrice;
	else 
	    return $salePrice;
}