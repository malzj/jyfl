<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_venues.php');
include_once(ROOT_PATH . 'includes/lib_basic.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_dongsport.php');

// 返回的数据
$jsonArray = array(
	'state'=>'true',
	'data'=>'',
	'message'=>''
);

set_card_rules();

if (!isset($_REQUEST['step']))
{
	$_REQUEST['step'] = "show";
}

// 折扣比例
$customRatio = get_card_rule_ratio(10003);
// 产品id
$product = intval($_REQUEST['productno']);
if ($product == 0)
{
	$jsonArray['state'] = 'false';
	$jsonArray['message'] = '无效操作！';
	JsonpEncode($jsonArray);
}

// 产品详细
if ($_REQUEST['step'] == 'show')
{	
	$arr_param = array(
			'productNo' 	=> $product
	);
		
	$dongpiaoName = 'dongpiao-detail-'.$product;
	$dongpiao = F($dongpiaoName, '', 1800, 'dongpiao/');
	$dongpiao_filter = array_filter($dongpiao);
	if (empty($dongpiao_filter))
	{

		$dongpiao_temp = getDongapi('detail', $arr_param );
		$dongpiao = $dongpiao_temp['product'];
		$dongpiao_tmp=F("dongpiaoInter-".$_SESSION['cityid'],'', 1209600, "dongpiao/");
		$dongpiao['show_price'] = $dongpiao_tmp[$product]['show_price'];
		F($dongpiaoName, $dongpiao, 1800, 'dongpiao/');
	}
	// 如果没有数据，跳转到场馆列表
	$dongpiao_filter = array_filter($dongpiao);
	if (empty($dongpiao_filter))
	{
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '没有该门票！';
		JsonpEncode($jsonArray);
	}
	// 如果没有基础价格，从新计算
	$dongpiao['salePrice'] = initSalePrice($dongpiao['salePrice'], $customRatio);
		
	// 图片相册处理
	if (!empty($dongpiao['imgs']))
	{
		$dongpiao['imgs'] = explode(',',$dongpiao['imgs']);
	}
	
	if (empty($dongpiao['imgs']))
	{
		$dongpiao['imgs'] = $dongpiao['img'];
	}
	//时间处理
	if(empty($dongpiao['businessHours'])){
		$dongpiao['businessHours'] = '暂无';
	}
	// 地址处理
	if (empty($dongpiao['viewAddress']))
	{
		$dongpiao['viewAddress'] = '暂无';
	}
		
	if(strpos($dongpiao['orderDesc'], "<p>·支付方式")!==false){
		$sczf0="<p>·支付方式";
		$sczf2='<p>·取票方式';
	
	}
	else {
		$sczf0="<p>·<strong>支付方式";
		$sczf2='<p>·<strong>取票方式';
	
	}
	$zfint1=strpos($dongpiao['orderDesc'], $sczf0);
	$zfint2=strpos($dongpiao['orderDesc'], $sczf2);
	$zfint=$zfint2-$zfint1;
	$sub=substr($dongpiao['orderDesc'], $zfint1,$zfint);
	$dongpiaoa=str_replace($sub, '', $dongpiao['orderDesc']);

	$jsonArray['data'] = array(
		'detailww'=>$dongpiaoa,
		'detail'=>$dongpiao,

	);
	JsonpEncode($jsonArray);
}

// ajax 价格日历
else if($_REQUEST['step'] == 'price'){
	// 引入日历类（只适合动网）
	include_once(dirname(__FILE__)  . '/includes/calendar.php');
	
	$date = date('Y-m-d',strtotime(local_date('Y-m-d')));
	
	// 日期处理
	if (!empty($_REQUEST['date']))
	{
		$times = explode('-',$_REQUEST['date']);
		$month = $times[1];
		$year  = $times[0];
		if ($month > 12)
		{
			$floor 	 	= floor($month/12);
			$newYear 	= $year+$floor;
			$newMonth	= $month-($floor*12);
			if ($newMonth == 0){
				$newMonth = 12;
			}
			if ($newMonth < 10){
				$newMonth = '0'.$newMonth;
			}
				
			$date 	= $newYear.'-'.$newMonth.'-01';
		}
		else{
			if ($month < 10){
				$times[1] = '0'.$times[1];
			}
			$date   = $times[0].'-'.$times[1].'-01';
		}
	}	
	
	// 日历价格	
	$calendarPrice = array();
	$priceCalendar = getDongapi('price', array('productNo'=>$product, 'travelDate'=>$date));
	$priceCalendar['prices']['price'][] = $priceCalendar['prices']['price'];
	if (isset($priceCalendar['prices']['price']))
	{
		foreach($priceCalendar['prices']['price'] as  $datak=>$pe){
			$priceDay = explode('-',$pe['date']);				
			$calendarPrice[$priceDay[2]] = initSalePrice($pe['salePrice'], $customRatio);			
		}
	}
	
	$date = explode('-',$date);
	error_log(var_export($date,true),'3', 'error.log');
	error_log(var_export($priceCalendar,true),'3', 'error.log');
	
	
	$html = new calendar($date[0], $date[1], $calendarPrice);
	$jsonArray['data'] = $html->style();

	JsonpEncode($jsonArray);
}















