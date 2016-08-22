<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

$str_action = !empty($_REQUEST['act']) ? $_REQUEST['act'] : 'zxxz';
assign_template();

//根据城市id获取影院区域编号
$int_areaNo = getAreaNo();
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
// 卡规则折扣
$ratio = get_card_rule_ratio(10002);

$int_cinemaNo = $_GET['id'];
if (empty($int_cinemaNo)){
	ecs_header("Location:shiting.php?act=dzqdh");
	exit;
}
$smarty->assign('cinemaNo',          $int_cinemaNo);

//查询影院详细信息
$str_tradaId = 'getCinemaInfo';

$arr_param = array(
	'cinemaNo' => $int_cinemaNo//通兑票编号
);
$str_cacheName = $str_tradaId . '_' . $int_cinemaNo.'_'.$int_areaNo;//缓存名称为接口ID与地区编号结合
$arr_data = F($str_cacheName, '', -1, $arr_cityInfo['region_english'].'/');//缓存半小时
if (empty($arr_data)){
	$arr_result = getYYApi($arr_param, $str_tradaId);
	if (!empty($arr_result['body'])){
		$arr_data = $arr_result['body'];
		F($str_cacheName, $arr_data, 0, $arr_cityInfo['region_english'].'/');//写入缓存
	}
}

//print_r($arr_data);
//exit;
if (empty($arr_data)){
	ecs_header("Location:shiting.php?act=dzqdh");
	exit;
}

$arr_data['CinemaImages'] = !empty($arr_data['CinemaImages']) ? explode(',', $arr_data['CinemaImages']) : '';
if ($arr_data['CinemaImages']){
	foreach ($arr_data['CinemaImages'] as $key=>$var){
		if (strpos($var, 'http') === false){
			$var = 'http://douyou100.com:7000'.$var;
		}
		$arr_data['CinemaImages'][$key] = $var;
	}
}

if ($arr_data['AverageDegree'] > 0){
	$arr_data['AverageDegreeFormat'] = $arr_data['AverageDegree'] * 10;
	$arr_data['intComment']          = $arr_data['AverageDegree'] > 0 ? substr($arr_data['AverageDegree'], 0, strrpos($arr_data['AverageDegree'], '.')) : 0;
	$arr_data['floComment']          = $arr_data['AverageDegree'] > 0 ? substr($arr_data['AverageDegree'], -1) : 0;
}

//var_dump($arr_data);
//exit;

$smarty->assign('dzqyy',          $arr_data);//电子券使用范围

//获取电子券信息
$str_tradaId = 'getCommTickets';
$arr_param = array(
	'AreaNo'   => $int_areaNo,//区域编号
	'CinemaNo' => $int_cinemaNo//影院编号
);
$str_cacheName = $str_tradaId . '_' . $int_cinemaNo.'_'.$int_areaNo;//缓存名称
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
				$var['CinemaPriceFormat'] = $var['SalePrice'];				
				
				if ($ratio !== false){
					$var['SalePriceFormat'] = price_format(($var['SalePrice']/1.2*1.06)*$ratio);
				}else{
					$var['SalePriceFormat'] = price_format($var['SalePrice']/1.2*1.06);
				}
				
				//$var['SalePriceFormat']   = price_format($var['SalePrice']/1.2*1.06);
				$arr_dzqData[$var['TicketNo']] = $var;
			}
		}
		//F($str_cacheName, $arr_dzqData, 0, $arr_cityInfo['region_english'].'/');//写入缓存
	}
}
//print_r($arr_dzqData);
$smarty->assign('dzq',          $arr_dzqData);//电子券

$position = assign_ur_here(0, '<a href="shiting.php?id=1">视听盛宴</a> <code>&gt;</code> <a href="shiting.php?id=1">电影</a> <code>&gt;</code> <a href="shiting.php?act=dzqdh">电子券兑换</a> <code>&gt;</code> '.$arr_data['CinemaName']);
$smarty->assign('page_title',       $arr_data['CinemaName'].'_电子券兑换_电影_视听盛宴_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

$smarty->display('shiting_show_dzq.dwt');
?>