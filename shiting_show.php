<?php
/* 
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

//ajax获取影片排期
if ($str_action == 'getZxxzInfo'){
	//根据区域编号影片编号获取影片排期
	$int_areaNo = $_GET['areaNo'];
	$int_filmNo = $_GET['filmNo'];
	$smarty->assign('filmNo',      $int_filmNo);

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

	$arr_result = array('error' => 0, 'content' => '', 'message' => '');

	$str_date      = !empty($_GET['datetime']) ? $_GET['datetime'] : '';
	$str_district  = !empty($_GET['district']) ? $_GET['district'] : '';
	$str_cinema    = !empty($_GET['cinema']) ? $_GET['cinema'] : '';
	

	$int_today  = local_date('Y-m-d');
	$smarty->assign('today',       $int_today);
	

	$arr_week = $arr_district = $arr_cinema = $arr_seq = array();
	if ($arr_filmsPaiq){
		//获取日期
		$arr_wLang = array(1=>'一', 2=>'二', 3=>'三', 4=>'四', 5=>'五', 6=>'六', 7=>'日');
		$int_sunTime = local_mktime(23, 59, 59, local_date('m'), local_date('d') - local_date('w') + 7, local_date('Y'));
		$arr_filmsDate = array_keys($arr_filmsPaiq);
		$int_index = 0;
		foreach ($arr_filmsDate as $var){
			$arr_week[$int_index]['date']       = $var;
			$arr_week[$int_index]['time']       = local_strtotime($var);//时间戳
			$arr_week[$int_index]['dateFormat'] = local_date('m月d日', local_strtotime($var));//日期格式
			$arr_week[$int_index]['week']       = local_date('N', local_strtotime($var));//时间戳
			$arr_week[$int_index]['zhcn']       = $arr_wLang[local_date('N', local_strtotime($var))];//语言
			$int_index++;
		}

		//默认获取第一条数据日期的所有区域
		$str_defaultDate = !empty($str_date) ? $str_date : $arr_filmsDate[0];
		$int_index = 0;
		foreach ($arr_filmsPaiq[$str_defaultDate]['Area'] as $key=>$var){
			$arr_district[$int_index]['AreaNo']   = $key;
			$arr_district[$int_index]['AreaName'] = $var['AreaName'];
			$arr_district[$int_index]['Count']    = count($var['Cinema']);
			$int_index++;
		}

		if ($str_district == 'all'){
			//默认获取第一条数据日期的所有热门影院
			$arr_cinema = $arr_filmsPaiq[$str_defaultDate]['all'];
			$smarty->assign('disClass',       'all');
		}else if (!empty($str_district) && $str_district != 'hot'){
			$arr_cinema = $arr_filmsPaiq[$str_defaultDate]['Area'][$str_district]['Cinema'];
			$smarty->assign('disClass',       $str_district);
		}else{
			//默认获取第一条数据日期的所有热门影院
			$arr_cinema = $arr_filmsPaiq[$str_defaultDate]['all'];
			foreach ($arr_cinema as $key => $var) {
				$volume[$key]  = $var['AverageDegree'];
			}
			array_multisort($volume, SORT_DESC, $arr_cinema);//按综合评分降序排序
			$smarty->assign('disClass',       'hot');
		}

		if (!empty($str_cinema)){
			$arr_defaultCinema = $arr_cinema[$str_cinema];
		}else{
			$arr_defaultCinema = current($arr_cinema);
		}

		//默认获取第一条数据日期的默认影院的排期
		$arr_seq = $arr_filmsPaiq[$str_defaultDate]['Seq'][$arr_defaultCinema['CinemaNo']];

		$smarty->assign('defaultDate',      $str_defaultDate);
		$smarty->assign('weeks',            $arr_week);
		$smarty->assign('districts',        $arr_district);
		$smarty->assign('cinemas',          $arr_cinema);
		$smarty->assign('defaultCinema',    $arr_defaultCinema);
		$smarty->assign('seqs',             $arr_seq);

		$smarty->assign('ShowTypes',        array('2D', '3D', '4D', 'IMAX'));

		$arr_result['content'] = $smarty->fetch('library/shiting_zxxz.lbi');
	}else{
		$arr_result['error']   = 1;
		$arr_result['message'] = '';
		$arr_result['content'] = '
			<div class="hy2_juqing_box">
				<p class="hy2_jq_t1">在线选座</p>
				<p class="hy2_jq_t2">抱歉，该影片暂无影院排期</p>
			</div>
		';
	}

	die(json_encode($arr_result));
}


$int_filmNo = intval($_GET['id']);
if (empty($int_filmNo)){
	ecs_header("Location:shiting.php?id=1");
	exit;
}

//影片详细接口参数
$str_tradaId = 'getFilmInfo';
$arr_param = array(
	'filmNo'    => $int_filmNo,
	'IsParater' => 1
);

$str_cacheName = $str_tradaId . '_' . $int_filmNo;
$arr_filmsInfo = F($str_cacheName, '', 1800, $arr_cityInfo['region_english'].'/');//缓存半小时
if (empty($arr_filmsInfo)){
	$arr_result = getYYApi($arr_param, $str_tradaId);
	if (!empty($arr_result['body'])){
		$arr_filmsInfo = $arr_result['body'];
		F($str_cacheName, $arr_filmsInfo, 0, $arr_cityInfo['region_english'].'/');//写入缓存
	}
}

if (empty($arr_filmsInfo)){
	ecs_header("Location:shiting.php?id=1");
	exit;
}
$arr_filmsInfo['year'] = substr($arr_filmsInfo['FirstShowDate'], 0, 4);
$arr_filmsInfo['FirstShowDateFormat'] = $arr_filmsInfo['FirstShowDate'] ? local_date('Y年m月d日', local_strtotime($arr_filmsInfo['FirstShowDate'])) : '';

$arr_filmsInfo['shortFilmDesc'] = sub_str($arr_filmsInfo['FilmDesc'], 200);

$arr_filmsInfo['AverageDegreeFormat'] = $arr_filmsInfo['AverageDegree'] * 10;
$arr_filmsInfo['intComment'] = $arr_filmsInfo['AverageDegree'] > 0 ? substr($arr_filmsInfo['AverageDegree'], 0, 1) : 0;
$arr_filmsInfo['floComment'] = $arr_filmsInfo['AverageDegree'] > 0 ? substr($arr_filmsInfo['AverageDegree'], 2) : 0;

if (strpos($arr_filmsInfo['MainActors'], '、')){
	$arr_filmsInfo['MainActors'] = $arr_filmsInfo['MainActors'] ? explode('、', $arr_filmsInfo['MainActors']) : '';
}else{
	$arr_filmsInfo['MainActors'] = $arr_filmsInfo['MainActors'] ? explode('，', $arr_filmsInfo['MainActors']) : '';
}

$arr_filmPKey = array_keys($arr_filmsInfo['FilmPictures']['item']);
if (!empty($arr_filmPKey) && in_array(1, $arr_filmPKey)){
	$arr_FilmPictures = $arr_filmsInfo['FilmPictures']['item'];
}else{
	$arr_FilmPictures = array($arr_filmsInfo['FilmPictures']['item']);
}

$arr_filmVKey = array_keys($arr_filmsInfo['FilmVideos']['item']);
if (!empty($arr_filmPKey) && in_array(1, $arr_filmVKey)){
	$arr_FilmVideos = $arr_filmsInfo['FilmVideos']['item'];
}else{
	$arr_FilmVideos = array($arr_filmsInfo['FilmVideos']['item']);
}

//var_dump( $arr_filmsInfo);exit;

$smarty->assign('FilmPictures',       $arr_FilmPictures);
$smarty->assign('FilmVideos',         $arr_FilmVideos);
$smarty->assign('filmsinfo',          $arr_filmsInfo);
$smarty->assign('action',             $_GET['act'] ? $_GET['act'] : 'hot');

//在线选座
$position = assign_ur_here(0, '<a href="shiting.php?id=1">视听盛宴</a> <code>&gt;</code> <a href="shiting.php?id=7">电影</a> <code>&gt;</code> <a href="shiting.php?act=zxxz">在线选座</a> <code>&gt;</code> '.$arr_filmsInfo['FilmName']);
$smarty->assign('page_title',       $arr_filmsInfo['FilmName'].'_在线选座_电影_视听盛宴_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

//获取影片影院排期信息
$str_tradaId = 'getShowTimeByAreaNoFilmNo';
$arr_param = array(
	'areaNo' => $int_areaNo,
	'filmNo' => $int_filmNo
);



$str_cacheName = $str_tradaId . '_' . $int_filmNo;


$arr_filmsPaiq = F($str_cacheName, '', 1800, $arr_cityInfo['region_english'].'/');//缓存半小时

if (empty($arr_filmsPaiq)){
	$arr_result = getYYApi($arr_param, $str_tradaId);
	//var_dump($arr_result);exit;
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


//var_dump($arr_filmsPaiq);

$smarty->display('shiting_show.dwt');


//获取指定日的当前一周日期
//$mix_date    指定日期
//is_past      是否显示已过去的星期
function getWeekDate($mix_date, $is_past = 1){
	$int_stime = preg_match('/^\d{10}$/', $mix_date) ? $mix_date : local_strtotime($mix_date);//获取给定日期的时间戳
	$int_curW  = local_date('N', $int_stime);//给定日期在本周是星期几
	$arr_weeks = array();
	$arr_wLang = array(1=>'一', 2=>'二', 3=>'三', 4=>'四', 5=>'五', 6=>'六', 7=>'日');
	for ($i = 1; $i < 8; $i++){
		if ($i < $int_curW && empty($is_past)) continue;
		$arr_weeks[$i]['zh_cn'] = $arr_wLang[$i];
		$arr_weeks[$i]['time']  = local_mktime(0, 0, 0, local_date('m', $int_stime), local_date('d', $int_stime) - $int_curW + $i, local_date('Y', $int_stime));
		$arr_weeks[$i]['date']  = local_date('m月d日', $arr_weeks[$i]['time']);
	}
	return $arr_weeks;
} */
?>