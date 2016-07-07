<?php
/**
 * 试听盛宴-----> 影院
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
	$_REQUEST['step'] = "movie";
}

assign_template();

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

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
	$arr_data = F($str_cacheName, '', 1800, $int_areaNo.'/');//缓存半小时
	if (empty($arr_data)){
		$arr_result = getCDYApi($arr_param);
		if (!empty($arr_result)){
			$arr_data = $arr_result['movies'];
			F($str_cacheName, $arr_data, 0, $int_areaNo.'/');//写入缓存
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
	$jsonArray['data'] = wapCinemaList();
	JsonpEncode($jsonArray); 
}

// 影片详情
elseif ($_REQUEST['step'] == "movieDetail")
{
    $movieid = !empty($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;
    // 获得影片详细信息
    $movieDetail = getMovieDetail( $movieid );
    // 影片评分
    $movieDetail['scoreBest'] = $movieDetail['score'] * 10;
    $jsonArray['data'] = $movieDetail;
    JsonpEncode($jsonArray); 
    
}
// 影片详情--选影院
elseif ($_REQUEST['step'] == "movieCinema")
{
	$movieid = !empty($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;
	
	// 根据影片得到该影片上映的影院信息
	$cinemaList = getMovieCinema( $movieid );
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
	
	$jsonArray['data'] = $cinemas;
    JsonpEncode($jsonArray); 
	
}
// 影院详情(显示影院信息，影院支持信息)
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

// 影院所有影片列表
elseif ($_REQUEST['step'] == "movieList")
{	
	// 影院id
	$cinemaid = !empty($_REQUEST['cinemaid']) ? intval($_REQUEST['cinemaid']) : 0 ;
	// 影片id
	$movieid = !empty($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;
	// 影院所有影片列表
	$movies = getCinemaMovies($cinemaid);
	
	// 处理选中的影片，如果没有选择影片，默认取第一个	
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
	$moviePlan = getMoviePlan( $cinemaid, $movieid );

	// 整理排期日期
	$featureTimes = featureTime( $moviePlan );

	// 如果没有选择时间，默认选择第一个
	if (empty($currentTime))
	{
		reset($featureTimes);
		$currentTimes = current($featureTimes);
		$currentTime = $currentTimes['strtotime'];
	}	
	
	$planList = searchPlan($moviePlan, $currentTime, $ratio);	
	
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
	$delId		= isset($_REQUEST['delid']) ? intval($_REQUEST['delid']) : 0 ;			// 上一次的订单id
	
	// 重新选择作为的时候，取消上一个订单
	if($delId > 0 ){
		if( $order_sn = $GLOBALS['db']->getOne('SELECT order_sn FROM '.$GLOBALS['ecs']->table('seats_order')." WHERE user_id = '".intval($_SESSION['user_id'])."' and id = '".$delId."' and  order_status = 1")){
			$GLOBALS['db']->query('UPDATE '.$ecs->table('seats_order')." SET order_status = 2 WHERE id = '$delId' and  order_status = 1");
			$deleteStatus = getCDYapi(array('action'=>'order_Delete', 'order_id'=>$order_sn));
		}
	
	}
	
	// 影片排序信息
	$planQuery = getMoviePlan($cinemaid, $movieid);
	
	$planInfo = array();
	
	if (!empty($planQuery))
	{
		$weeks = array(0=>'星期日',1=>'星期一',2=>'星期二',3=>'星期三',4=>'星期四',5=>'星期五',6=>'星期六');
		foreach ($planQuery as $plans)
		{
			if($plans['planId'] !== $planid) continue;
			$strtotime = strtotime($plans['featureTime']);
			$currentDate = date('Y-m-d',$strtotime);
			$week = $weeks[date('w', $strtotime)];
			$hours = date('H:i',$strtotime);
			$plans['featureTimeStr'] = $currentDate.' '.$week.' '.$hours;
			
			$tmpplanInfo = searchPlan( array($plans), $currentDate, $ratio);
			$planInfo = $tmpplanInfo[0];			
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
	
	// 座位类图
	include_once(ROOT_PATH . 'includes/lib_seatQuery.php');
	$arr_param = array(
			'action'=>'seat_Query',
			'plan_id'=> $planid,
			'hall_id'=>$hallno,
			'cinema_id'=>$cinemaid
	);
	
	$arr_result = getCDYapi($arr_param);
	
	$setField = array(
			'hallName'=>'hallId',			// 厅号属性名
			'graphRowName'=>'graphRow',  	// 排号属性名     X
			'graphColName'=>'graphCol',		// 座位号属性名  	Y
			'seatRowName' =>'seatRow',
			'width'=>699					// 影院的总宽
	);
	// 场次已经开始
	if($arr_result['status'] !=0){
	    $jsonArray['state'] = 'false';
		$jsonArray['message'] = $arr_result['error'];
		exit(json_encode($jsonArray));
	}
	
	// 未返回座位信息
	if(empty($arr_result['seats'])) {
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '该场次不能选择，请选择其他场次！';
		exit(json_encode($jsonArray));
	}
	
	
	$seatQuery = new seatQueryInfo($setField);
	$seatInfo = $seatQuery->getSeatInfo($arr_result['seats']);
	$allWidth = max($seatQuery->getField('colsCount')) * 24 + 20;	
	
	$smarty->assign('allwidth',		$allWidth);
	$smarty->assign('seatInfo',     $seatInfo);   //座位信息
	
	$jsonArray['data']['width'] = $allWidth;
	$jsonArray['data']['seat'] = $seatInfo;
	JsonpEncode($jsonArray); 
	
}

