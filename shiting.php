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
//$int_areaNo = 110100;
get_card_rule_ratio(10002);
include_once(ROOT_PATH . 'includes/lib_cardApi.php');


$int_page     = (isset($_GET['page']) && $_GET['page'] > 1) ? intval($_GET['page']) : 1;
$int_pageSize = 20;
$int_start    = ($int_page - 1) * $int_pageSize;

if ($str_action == 'zxxz'){
	header("Location:komovie.php");
}else if ($str_action == 'dzqdh'){
	//电子券兑换
	$position = assign_ur_here(0, '<a href="shiting.php?id=1">视听盛宴</a> <code>&gt;</code> 电影 <code>&gt;</code> 电子券兑换');
	$smarty->assign('page_title',       '电子券兑换_电影_视听盛宴_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

	$str_tradaId = 'getCinemas';//查询指定城市下的所有影院
	$arr_param = array(
		'AreaNo'   => $int_areaNo,//区域编号
		'filmNo'   => ''//影院编号
	);

	$str_cacheName = $str_tradaId.'_'.$int_areaNo;//缓存名称为接口ID与地区编号结合
	$arr_data = F($str_cacheName, '', 1800, $arr_cityInfo['region_english'].'/');//缓存半小时
	if (empty($arr_data)){
		$arr_result = getYYApi($arr_param, $str_tradaId);
		if (!empty($arr_result['body']['item'])){
			$arr_data = $arr_result['body']['item'];
			if (!empty($arr_data)){
				
				foreach ($arr_data as $key=>$var){
					unset($arr_data[$key]);
					if (in_array($var['SellFlag'], array(1,4))){
						//unset($arr_data[$key]);
						continue;
					}
					$var['AverageDegreeFormat'] = $var['AverageDegree'] * 10;
					$var['intComment']          = $var['AverageDegree'] > 0 ? substr($var['AverageDegree'], 0, strrpos($var['AverageDegree'], '.')) : 0;
					$var['floComment']          = $var['AverageDegree'] > 0 ? substr($var['AverageDegree'], -1) : 0;
					$arr_data['cinemas']['all'][$key]  = $var;
					$arr_data['cinemas']['area'][$var['AreaNo']][]  = $var;
					$arr_data['areas'][$var['AreaNo']]['areaNo']   = $var['AreaNo'];
					$arr_data['areas'][$var['AreaNo']]['areaName'] = $var['AreaName'];
					$arr_data['areas'][$var['AreaNo']]['count']    += 1;
				}
			}
			F($str_cacheName, $arr_data, 0, $arr_cityInfo['region_english'].'/');//写入缓存
		}
	}
	
	$_GET['area'] = !empty($_GET['area']) ? $_GET['area'] : 'hot';
	if (!empty($_GET['area']) && $_GET['area'] != 'hot' && $_GET['area'] != 'all'){
		$arr_cinemas = $arr_data['cinemas']['area'][$_GET['area']];
	}else{
		$arr_cinemas = $arr_data['cinemas']['all'];
	}

	if ($_GET['area'] == 'hot'){
		foreach ($arr_cinemas as $key => $var) {
			$volume[$key]  = $var['AverageDegree'];
		}
		array_multisort($volume, SORT_DESC, $arr_cinemas);//按综合评分降序排序
	}

	if (!empty($_GET['cinemaKey']) && $_GET['cinemaKey'] != '搜索影院名称'){
		$str_keyword = trim($_GET['cinemaKey']);
		foreach ($arr_cinemas as $key=>$var){
			if (strpos($var['CinemaName'], $str_keyword) === false){
				unset($arr_cinemas[$key]);
			}
		}
	}

	$int_count = count($arr_cinemas);
	$arr_dzqdh = array_splice($arr_cinemas, $int_start, $int_pageSize);


	$smarty->assign('dzq',  $arr_dzqdh);
	$pager  = get_pager('shiting.php', array('act' => $str_action, 'area'=>$_GET['area'], 'cinemaKey'=>$str_keyword), $int_count, $int_page, $int_pageSize);
	$smarty->assign('pager',  $pager);
	$smarty->assign('arealist',  $arr_data['areas']);
	$smarty->assign('type',  $_GET['area']);
	$smarty->assign('cinemaKey',  $_GET['cinemaKey']);
	$smarty->display('shiting_dzqdh.dwt');
}else if ($str_action == 'skyy'){
	//刷卡影院列表
	$position = assign_ur_here(0, '<a href="shiting.php?id=1">视听盛宴</a> <code>&gt;</code> 电影 <code>&gt;</code> 刷卡影院');
	$smarty->assign('page_title',       '刷卡影院_电影_视听盛宴_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
	$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置


	$int_city     = intval($_REQUEST['city']);
	$int_district = intval($_REQUEST['district']);

	
	$str_where = 'is_open = 1';
	if (empty($int_city)){
	$int_city = $_SESSION['cityid'];
	}
	if (!empty($int_city)){
			$str_where .= ' AND city = '.$int_city;
		}
		if (!empty($int_district)){
			$str_where .= ' AND district = '.$int_district;
	}

	$query = $db->query('SELECT title, city, district,2d,3d, address FROM '.$ecs->table('yingyuan')." WHERE $str_where ORDER BY add_time DESC");

	while ($row = $db->fetch_array($query)){
		$row['city']     = get_add_cn($row['city']);
		$row['district'] = get_add_cn($row['district']);
		$yingyuan_list[] = $row;
	}

	$smarty->assign('yingyuan_list',       $yingyuan_list);    // 影院列表
	$smarty->assign('int_city',       $int_city);    // 市
	$smarty->assign('int_district',       $int_district);    // 区


	$smarty->assign('province_list',    get_regions(1, $int_cityId));

	$district_list = get_regions(3, $int_city);

	$smarty->assign('city_list',         $city_list);
	$smarty->assign('district_list',         $district_list);

	$smarty->display('shiting_skyy.dwt');
}else if ($str_action == 'yingyuan'){
	//影院列表
	
	$str_tradaId = 'getCinemas';
	$arr_param = array(
		'AreaNo'   => $int_areaNo,//区域编号
		'CinemaNo' => ''//影院编号
	);
	$str_cacheName = $str_tradaId.'_'.$int_areaNo;//缓存名称为接口ID与地区编号结合
	$arr_data = F($str_cacheName, '', 1800, $arr_cityInfo['region_english'].'/');//缓存半小时
	if (empty($arr_data)){
		$arr_result = getYYApi($arr_param, $str_tradaId);
		if (!empty($arr_result['body']['item'])){
			$arr_data = $arr_result['body']['item'];
			F($str_cacheName, $arr_data, 0, $arr_cityInfo['region_english'].'/');//写入缓存
		}
	}
	$smarty->display('shiting_yingyuan.dwt');
}


?>