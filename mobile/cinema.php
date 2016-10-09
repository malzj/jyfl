<?php
/**
 * 试听盛宴-----> 影院
 * @var unknown_type
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_cinema.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_movie_times.php');


//根据城市id获取影院区域编号
$int_areaNo = getAreaNo(0,'komovie');

if (!isset($_REQUEST['step']))
{
	$_REQUEST['step'] = "movie";
}

assign_template();

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

//判断是否为次卡，如果不是则跳转到点卡影院
if(!is_times_card()){
	$jsonArray['data']['is_cika'] = 0;
}else{
	$jsonArray['data']['is_cika'] = 1;
}

//判断是否电影比价
if(is_mate_movie()){
	define('IS_MATE', true);
}else{
	define('IS_MATE', false);
}

$jsonArray['data']['maxCount'] = getMaxBuyCount();
//如果是次卡更新左侧导航链接
$jsonArray['data']['navigator_list'] = get_times_nav( get_navigator() );

// 影片列表
if ($_REQUEST['step'] == "movie")
{       
	// 正在上映 (hot) 、即将上映 (coming)
	$op = !empty($_REQUEST['op']) ? $_REQUEST['op'] : 'hot';
	if ($op == 'coming'){
		$arr_param = array('action'=>'movie_Query','coming'=>100,'city_id'=>$int_areaNo);
		$str_cacheName = 'komovie_coming'.'_'.$int_areaNo;//缓存名称为接口名称即将上映与地区ID号结合
	}else{
		$arr_param = array('action'=>'movie_Query','city_id'=>$int_areaNo);
		$str_cacheName = 'komovie'.'_'.$int_areaNo;//缓存名称为接口名称与地区ID号结合
	}
	
	// 获得影片列表
	if(IS_MATE&&$op == "hot") {
		$arr_data = $db -> getAll("SELECT * FROM ".$ecs -> table("mate_movie")." ORDER BY hot DESC");
	}else {
		$arr_data = F($str_cacheName, '', 1800, $int_areaNo . '/');//缓存半小时
		if (empty($arr_data)) {
			$arr_result = getCDYApi($arr_param);
			if (!empty($arr_result)) {
				$arr_data = $arr_result['movies'];
				F($str_cacheName, $arr_data, 0, $int_areaNo . '/');//写入缓存
			}
		}
	}
	// 图片本地化
	foreach ($arr_data as &$arr)
	{
		$image_path = explode('/', $arr['pathVerticalS']);
		$filenames = array_pop($image_path);
		if (!file_exists('../temp/komovie/'.$filenames)){
			$new_images = getImage($arr['pathVerticalS'], ROOT_PATH. 'temp/komovie', $filenames);
		}
		$arr['thumb'] = '../temp/komovie/'.$filenames;
	}
    
	$jsonArray['data'] = $arr_data;
	JsonpEncode($jsonArray); 

}
// 影院列表
elseif ($_REQUEST['step'] == "cinema")
{	
	if(IS_MATE){
		$returnArray = array();
		$cinemaList = getMateCinemaList(1, 1000);
		foreach($cinemaList as $cinema){
			// 删除地区为空的影院
			if (empty($cinema['area_id']))
			{
				continue;
			}
			if ( empty($returnArray[$cinema['area_id']]) )
			{
				$returnArray[$cinema['area_id']]['area_name'] = $cinema['area_name'];
			}

			$returnArray[$cinema['area_id']]['cinemas'][] = $cinema;
		}
		$jsonArray['data'] = $returnArray;
	}else{
		$jsonArray['data'] = wapCinemaList();
	}
	$jsonArray['is_mate'] = IS_MATE;
	JsonpEncode($jsonArray);
}

// 影片详情及影片影院列表
elseif ($_REQUEST['step'] == "movieDetail")
{
    $movieid = !empty($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;
	$from_banner = !empty($_REQUEST['banner']) ? intval($_REQUEST['banner']) : 0 ;

	if(IS_MATE){
		//查询接口电影id
		if($from_banner==0) {
			$local_movieid = $movieid;
			$movieid = $db->getOne("SELECT komovie_id FROM " . $ecs->table('mate_movie') . " WHERE movieId = " . $movieid);
		}elseif($from_banner==1){
			$local_movieid = $db->getOne("SELECT movieId FROM " . $ecs->table('mate_movie') . " WHERE komovie_id = " . $movieid);
		}
	}

    // 获得影片详细信息
    $movieDetail = getMovieDetail( $movieid );
    // 影片评分
    $movieDetail['scoreBest'] = $movieDetail['score'] * 10;
	// 根据影片得到该影片上映的影院信息
	if(IS_MATE){
		$cinemaList = array();
		$sql = "SELECT c.* FROM ".$ecs->table('movie_cinema')." mc LEFT JOIN ".$ecs->table('mate_cinema')
			." c ON mc.cinema_id = c.id WHERE mc.movie_id = ".$local_movieid." AND mc.is_show = 1";
		$cinemas = $db -> getAll($sql);
		foreach ($cinemas as $cinema){
			$cinemaList[] = transCinemaInfo($cinema);
		}
	}else{
		$cinemaList = getMovieCinema( $movieid );
	}
	// 整理区
	$districts = array();
	foreach ($cinemaList as $cinema)
	{
		if ( !isset($districts[$cinema['districtId']]) )
		{
			$districts[$cinema['districtId']] = array( 'id' => $cinema['districtId'], 'name'=>$cinema['districtName']); 
		}	
	}
	
	// 根据城市整理影院列表
	$cinemas = array();
	
	foreach ( $districts as $disVal)
	{
		foreach ( $cinemaList as $cinema)
		{
			if ($cinema['districtId'] == $disVal['id'])
			{
				$cinemas[$disVal['id']]['districtName'] = $disVal['name'];
				$cinemas[$disVal['id']]['cinema'][] = $cinema;
			}
			
		}
	}
	$jsonArray['data']['movieDetail'] = $movieDetail;
	$jsonArray['data']['cinemas'] = $cinemas;
    JsonpEncode($jsonArray); 
	
}

//影院详情页对应整体接口
elseif($_REQUEST['step'] == 'allCinemaDetail'){
	/*
	 * 影院详情(显示影院信息，影院支持信息)
	 */

	$cinemaid = !empty($_REQUEST['cinemaid']) ? trim($_REQUEST['cinemaid']) : 0 ;//影院id
	if(IS_MATE){
		$cinemainfo = $db -> getRow("SELECT * FROM ".$ecs->table('mate_cinema')." WHERE id = ".$cinemaid);
		$newCinemaResult = cinemaLogo(array($cinemainfo));
		$cinemaDetail = $newCinemaResult[0];
	}else{
		$cinemaDetail = getCinemaDetail($cinemaid, 'komovie_cinema_id');
	}

	$jsonArray['data']['cinemaDetail']=$cinemaDetail;

	if($cinemaDetail['is_komovie']==1||$cinemaDetail['is_wangmovie'] == 1) {

		/*
         * 影院所有影片列表
         */
		// 影片id
		$movieid = !empty($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0;
		// 影院所有影片列表
		if(IS_MATE){
			$sql = "SELECT m.* FROM ".$ecs->table('movie_cinema')." c LEFT JOIN ".$ecs->table('mate_movie')
				." m ON c.movie_id = m.movieId WHERE c.cinema_id = ".$cinemaid." AND c.is_show = 1 ORDER BY m.hot DESC";
			$movies = $db -> getAll($sql);
			$movies = moviesImages($movies);
		}else{
			$movies = getCinemaMovies($cinemaid);
		}

		// 处理选中的影片，如果没有选择影片，默认取第一个
		if(empty($movieid)){
			reset($movies);
			$firstmovie = current($movies);
			$movieid = $firstmovie['movieId'];
		}

		foreach ($movies as &$movie) {
			if (strcasecmp($movie['movieId'], $movieid) == 0)
				$movie['selected'] = 1;
			else
				$movie['selected'] = 0;
		}
		/*
         * 指定影院，指定影片的排期
         */
		// 销售比例
		$ratio = getMovieRatio();
		// 当前选择的日期
		$currentTime = !empty($_REQUEST['currentTime']) ? trim($_REQUEST['currentTime']) : 0;

		if (empty($cinemaid) || empty($movieid)) {
			$jsonArray['state'] = 'false';
			$jsonArray['message'] = '暂时没有可用的场次';
			JsonpEncode($jsonArray);
		}
		// 获得影片的排期
		if(IS_MATE){
			$moviePlan = getMateMoviePlan( $cinemaid, $movieid,$currentTime,$ratio );
			$featureTimes = $moviePlan['featureTimes'];
			$currentTime = $moviePlan['currentTime'];
			$planList = $moviePlan['planList'];
		}else {
			$moviePlan = getMoviePlan($cinemaid, $movieid);

			// 整理排期日期
			$featureTimes = featureTime($moviePlan);
			// 如果没有选择时间，默认选择第一个
			if (empty($currentTime)) {
				reset($featureTimes);
				$currentTimes = current($featureTimes);
				$currentTime = $currentTimes['strtotime'];
			}

			$planList = searchPlan($moviePlan, $currentTime, $ratio);
		}
		$jsonArray['data']['movies']=$movies;
		$jsonArray['data']['movieid']=$movieid;
		$jsonArray['data']['time'] = $featureTimes;
		$jsonArray['data']['plan'] = $planList;

	}
	if($cinemaDetail['is_dzq']==1){
		/*
         * 影院购票详情（电子兑换券购买）
         */
		//电子券影院id
		$dzqcinemaid = $cinemaDetail['dzq_cinema_id'];
		// 销售比例
		$ratio = getDzqRatio();

		// 电子券信息
		$cinemaDzq = getCinemaDzq($dzqcinemaid, $ratio);
		$jsonArray['data']['cinemaDzq']=$cinemaDzq;
	}


	JsonpEncode($jsonArray);
}

// 影院详情(显示影院信息，影院支持信息)(未用)
elseif ($_REQUEST['step'] == 'cinemaDetail')
{
    $cinemaids = !empty($_REQUEST['cinemaid']) ? trim($_REQUEST['cinemaid']) : 0 ;
    
    // 直接从影院列表点过来的情况下
    if (strpos($cinemaids, 'c') !== false )
    {
        $temp = explode('-', $cinemaids);
        $cinemaDetail = getCinemaDetail($temp['1']);
    }
    else {
        $cinemaid = $cinemaids;
        $cinemaDetail = getCinemaDetail($cinemaid, 'komovie_cinema_id');
    }

    $jsonArray['data'] = $cinemaDetail;    
    JsonpEncode($jsonArray); 
    
}

// 影院购票详情（电子兑换券购买）
elseif ($_REQUEST['step'] == "cinemaDzq")
{
	// 销售比例
	$ratio = getDzqRatio();	
	// 电子兑换券影院编号
	$cinemaid = !empty($_REQUEST['cinemaid']) ? trim($_REQUEST['cinemaid']) : '' ;	
	// 电子券信息
	$cinemaDzq = getCinemaDzq($cinemaid, $ratio);
	
	$jsonArray['data'] = $cinemaDzq;    
    JsonpEncode($jsonArray); 
}

// 影院所有影片列表(未用)
elseif ($_REQUEST['step'] == "movieList")
{	
	// 影院id
	$cinemaid = !empty($_REQUEST['cinemaid']) ? intval($_REQUEST['cinemaid']) : 0 ;
	// 影片id
	$movieid = !empty($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;
	// 影院所有影片列表
	$movies = getCinemaMovies($cinemaid);
	
	// 处理选中的影片，如果没有选择影片，默认取第一个
	if(empty($movieid)){
		reset($movies);
		$firstmovie = current($movies);
		$movieid = $firstmovie['movieId'];
	}

	foreach ($movies as &$movie)
	{
		if ( strcasecmp($movie['movieId'], $movieid) == 0 )
			$movie['selected'] = 1;
		else
		    $movie['selected'] = 0;
	}
	
	$jsonArray['data'] = $movies;    
    JsonpEncode($jsonArray); 
}

// 指定影院，指定影片的排期
elseif ($_REQUEST['step'] == "planList")
{
	// 销售比例
	$ratio = getMovieRatio();
	// 影院id
	$cinemaid = !empty($_REQUEST['cinemaid']) ? intval($_REQUEST['cinemaid']) : 0 ;
	// 影片id
	$movieid = !empty($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;
	// 当前选择的日期
	$currentTime = !empty($_REQUEST['currentTime']) ? trim($_REQUEST['currentTime']) : 0 ;
	
	if ( empty($cinemaid) || empty($movieid))
	{
	    $jsonArray['state'] = 'false';
		$jsonArray['message'] = '暂时没有可用的场次';
		exit(json_encode($jsonArray));
	}			
	// 获得影片的排期
	if(IS_MATE){
		$moviePlan = getMateMoviePlan( $cinemaid, $movieid,$currentTime,$ratio );
		$featureTimes = $moviePlan['featureTimes'];
		$currentTime = $moviePlan['currentTime'];
		$planList = $moviePlan['planList'];
	}else {
		$moviePlan = getMoviePlan($cinemaid, $movieid);

		// 整理排期日期
		$featureTimes = featureTime($moviePlan);

		// 如果没有选择时间，默认选择第一个
		if (empty($currentTime)) {
			reset($featureTimes);
			$currentTimes = current($featureTimes);
			$currentTime = $currentTimes['strtotime'];
		}

		$planList = searchPlan($moviePlan, $currentTime, $ratio);
	}
	$jsonArray['data']['time'] = $featureTimes;
	$jsonArray['data']['plan'] = $planList;    // 排期列表	
	JsonpEncode($jsonArray); 	
}

// 在线选座
elseif ($_REQUEST['step'] == "cinemaSeats")
{
	// 销售比例
	$ratio = getMovieRatio();
	
	$hallno		= isset($_REQUEST['hallno']) ? addslashes_deep($_REQUEST['hallno']) : 0 ; 		// 厅号
	$planid 	= isset($_REQUEST['planid']) ? intval($_REQUEST['planid']) : 0 ;		// 场次
	$cinemaid 	= isset($_REQUEST['cinemaid']) ? intval($_REQUEST['cinemaid']) : 0 ;	// 影院
	$movieid 	= isset($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;		// 电影
	$show_index	= isset($_REQUEST['showindex']) ? intval($_REQUEST['showindex']) : 0 ;		// 电影
	$time		= isset($_REQUEST['featuretime'])?addslashes_deep($_REQUEST['featuretime']):date('Y-m-d H:i:s',time());//排期时间
	$delId		= isset($_REQUEST['delid']) ? intval($_REQUEST['delid']) : 0 ;			// 上一次的订单id
	
	// 重新选择作为的时候，取消上一个订单
	if($delId > 0 ){
		if( $order_sn = $GLOBALS['db']->getOne('SELECT order_sn FROM '.$GLOBALS['ecs']->table('seats_order')." WHERE user_id = '".intval($_SESSION['user_id'])."' and id = '".$delId."' and  order_status = 1")){
			$GLOBALS['db']->query('UPDATE '.$ecs->table('seats_order')." SET order_status = 2 WHERE id = '$delId' and  order_status = 1");
			$deleteStatus = getCDYapi(array('action'=>'order_Delete', 'order_id'=>$order_sn));
		}
	
	}

	$planInfo = array();
	$weeks = array(0 => '星期日', 1 => '星期一', 2 => '星期二', 3 => '星期三', 4 => '星期四', 5 => '星期五', 6 => '星期六');

	// 影片排序信息
	if(!empty($show_index)&&IS_MATE){
		//获取网票网影片的排期
		require_once(ROOT_PATH . 'includes/lib_wpwMovieClass.php');
		$wpwClass = new wpwMovie();
		$show_check = $wpwClass->baseFilmShowCheck($cinemaid, $time, $show_index);
		if($show_check['ErrNo'] != 0||$show_check['Data'][0]['Result']==false){
			$jsonArray['state'] = 'false';
			$jsonArray['message'] = '排期无效或已改场，请选择其他场次！';
			JsonpEncode($jsonArray);
		}
		$wpwPlan = $wpwClass->baseFilmShow($cinemaid, $time, $movieid);
		if (!empty($wpwPlan['Data'])) {
			foreach ($wpwPlan['Data'] as $k => $plan) {
				if($plan['ShowIndex'] != $show_index) continue;

				$plans = transPlanWtoK($plan, $movieid, $cinemaid);
				$strtotime = strtotime($plans['featureTime']);
				$currentDate = date('Y-m-d',$strtotime);
				$week = $weeks[date('w', $strtotime)];
				$hours = date('H:i',$strtotime);
				$plans['featureTimeStr'] = $currentDate.' '.$week.' '.$hours;

				//从本地查询电影详情
				$plans['movie'] = $db -> getRow("SELECT * FROM ".$ecs->table('mate_movie')." WHERE wangmovie_id = ".$movieid);
				//从本地查询影院名称
				$plans['cinema']['cinemaName'] = $db -> getOne("SELECT cinema_name FROM ".$ecs->table('mate_cinema')." WHERE wang_cinema_id = ".$cinemaid);
				$tmpplanInfo = searchPlan( array($plans), $currentDate, $ratio);
				$planInfo = $tmpplanInfo[0];
			}
		}
	}else {
		$planQuery = getMoviePlan($cinemaid, $movieid);


		if (!empty($planQuery)) {
			foreach ($planQuery as $plans) {
				if ($plans['planId'] !== $planid) continue;
				$strtotime = strtotime($plans['featureTime']);
				$currentDate = date('Y-m-d', $strtotime);
				$week = $weeks[date('w', $strtotime)];
				$hours = date('H:i', $strtotime);
				$plans['featureTimeStr'] = $currentDate . ' ' . $week . ' ' . $hours;

				$tmpplanInfo = searchPlan(array($plans), $currentDate, $ratio);
				$planInfo = $tmpplanInfo[0];
			}
		}
	}
	$jsonArray['data'] = $planInfo;
	JsonpEncode($jsonArray); 	
}

// 加载座位图
elseif ($_REQUEST['step'] == "seatAjax")
{
	$hallno = isset($_REQUEST['hallno']) ? addslashes_deep($_REQUEST['hallno']) : 0 ; 		// 厅号
	$planid = isset($_REQUEST['planid']) ? intval($_REQUEST['planid']) : 0 ;		// 场次
	$cinemaid = isset($_REQUEST['cinemaid']) ? intval($_REQUEST['cinemaid']) : 0 ;	// 影院
	$movieid = isset($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;		// 电影
	$show_index = isset($_REQUEST['showindex']) ? intval($_REQUEST['showindex']) : 0 ;	// 网票网放映流水号

	// 座位类图
	include_once(ROOT_PATH . 'includes/lib_seatQuery.php');
	if(IS_MATE&&!empty($show_index)){
		include_once (ROOT_PATH . 'includes/lib_wpwMovieClass.php');
		$wpwMovie = new wpwMovie();
		//座位查询
		$result = $wpwMovie -> baseHallSeat($hallno,$cinemaid);
		//已售出座位查询
		$sell_seat_result = $wpwMovie -> baseSellSeat($show_index,$cinemaid);
		// 场次已经开始
		if ($result['ErrNo'] != 0) {
			exit("<center style='font-size:14px;color:#666'>" . $arr_result['Msg'] . "</center>");
		}

		// 未返回座位信息
		if (empty($result['Data'])) {
			exit("<center style='font-size:14px;color:#666'>该场次不能选择，请选择其他场次！</center>");
		}
		$tran_seat = transSeatsWtoK($result['Data'],$sell_seat_result['Data'],$hallno);
		$seatInfo = $tran_seat['seat'];
		$allWidth = $tran_seat['maxRowLength'] + 20 + $tran_seat['minLeft'];
		$allheight = $tran_seat['maxColHeight'] +50;
	}else {
		$arr_param = array(
			'action' => 'seat_Query',
			'plan_id' => $planid,
			'hall_id' => $hallno,
			'cinema_id' => $cinemaid
		);

		$arr_result = getCDYapi($arr_param);

		$setField = array(
			'hallName' => 'hallId',            // 厅号属性名
			'graphRowName' => 'graphRow',    // 排号属性名     X
			'graphColName' => 'graphCol',        // 座位号属性名  	Y
			'seatRowName' => 'seatRow',
			'width' => 699                    // 影院的总宽
		);
		// 场次已经开始
		if ($arr_result['status'] != 0) {
			$jsonArray['state'] = 'false';
			$jsonArray['message'] = $arr_result['error'];
			JsonpEncode($jsonArray);
		}

		// 未返回座位信息
		if (empty($arr_result['seats'])) {
			$jsonArray['state'] = 'false';
			$jsonArray['message'] = '该场次不能选择，请选择其他场次！';
			JsonpEncode($jsonArray);
		}


		$seatQuery = new seatQueryInfo($setField);
		$seatInfo = $seatQuery->getSeatInfo($arr_result['seats']);
		$allWidth = max($seatQuery->getField('colsCount')) * 24 + 20;
	}
	$smarty->assign('allwidth',		$allWidth);
	$smarty->assign('seatInfo',     $seatInfo);   //座位信息
	if(IS_MATE&&!empty($show_index)) {
		$smarty->assign('allheight', $allheight);
		$jsonArray['data']['info']=$smarty->fetch('_wang_seat_map.html');
	}else{
		$jsonArray['data']['info']=$smarty->fetch('_seat_map.html');
	}
	$jsonArray['data']['width'] = $allWidth;
	$jsonArray['data']['seat'] = $seatInfo;
	JsonpEncode($jsonArray);
//	exit(json_encode($jsonArray));
}

