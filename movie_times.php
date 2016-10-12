<?php
/**
 * 试听盛宴-----> 影院
 * @var unknown_type
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_basic.php');
require(ROOT_PATH . 'mobile/includes/lib_cinema.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_movie_times.php');
//判断是否为次卡，如果不是则跳转到点卡影院
if(!is_times_card()){
    ecs_header("Location:".str_replace('movie_times.php','movie.php',$_SERVER['REQUEST_URI'])."\n");
    exit;
}
//判断是否电影比价
if(is_mate_movie()){
	define('IS_MATE', true);
}else{
	define('IS_MATE', false);
}
//根据城市id获取影院区域编号
$int_areaNo = getAreaNo(0,'komovie');

if (!isset($_REQUEST['step']))
{
	$_REQUEST['step'] = "movie";
}

assign_template();

$smarty->assign('is_cika', '1');
//头部显示次数
$smarty->assign('maxCount', getMaxBuyCount());
//如果是次卡更新左侧导航链接
$smarty->assign('navigator_list', get_times_nav( get_navigator() ));

// ajax影片列表
if($_REQUEST['step'] == "ajaxMovieList")
{
    
    $arr_param = array('action'=>'movie_Query','city_id'=>$int_areaNo);
    $str_cacheName = 'komovie'.'_'.$int_areaNo;//缓存名称为接口名称与地区ID号结合
    $arr_data = F($str_cacheName, '', 1800, $int_areaNo.'/');//缓存半小时
    if (empty($arr_data)){
        $arr_result = getCDYApi($arr_param);
        if (!empty($arr_result)){
            $arr_data = $arr_result['movies'];
            F($str_cacheName, $arr_data, 0, $int_areaNo.'/');//写入缓存
        }
    }
    
    $smarty->assign('data',$arr_data);
    exit($smarty->fetch('movie_times/search/tajaxMovie.dwt'));
    
}

// ajax 影院列表
elseif ($_REQUEST['step'] == "ajaxCinemaList")
{
    $movieid = !empty($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;
    $cinemaList = getMovieCinema( $movieid );
    $smarty->assign('list',$cinemaList);
    $smarty->assign('movieid',$movieid);  
    exit($smarty->fetch('movie_times/search/tajaxCinema.dwt'));
}

// ajax 排期列表
elseif ($_REQUEST['step'] == "ajaxPlanList")
{
    $ratio = getMovieRatio();
    $movieid = !empty($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;
    $cinemaid = !empty($_REQUEST['cinemaid']) ? intval($_REQUEST['cinemaid']) : 0 ;
    
    // 当前选择的日期
    $currentTime = !empty($_REQUEST['currentTime']) ? trim($_REQUEST['currentTime']) : 0 ;
    
    if ( empty($cinemaid) || empty($movieid))
    {
      exit("<center> 参数不全 </center>");
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
    
    $smarty->assign('cinemaid', 	$cinemaid);
    $smarty->assign('planlist', 	$moviePlan);
    $smarty->assign('movieid',      $movieid);
    $smarty->assign('featureTimes', $featureTimes);
    $smarty->assign('currentTime',  $currentTime);
    $smarty->assign('moviePlan', 	$planList);    
    
    $html = empty($_REQUEST['currentTime']) ? $smarty->fetch('movie_times/search/tajaxPlan.dwt') : $smarty->fetch('movie_times/search/tajaxPlanRow.dwt');
    exit($html);

}

// 影片列表
elseif ($_REQUEST['step'] == "movie")
{
	// 正在上映 (hot) 、即将上映 (coming)
	$op = !empty($_REQUEST['op']) ? $_REQUEST['op'] : 'hot';
	
	// 正在上映
	if(IS_MATE) {
		$arr_data = $db -> getAll("SELECT * FROM ".$ecs -> table("mate_movie")." ORDER BY hot DESC");
	}else{
		$arr_param = array('action' => 'movie_Query', 'city_id' => $int_areaNo);
		$str_cacheName = 'komovie' . '_' . $int_areaNo;//缓存名称为接口名称与地区ID号结合
		$arr_data = F($str_cacheName, '', 1800, $int_areaNo . '/');//缓存半小时
		if (empty($arr_data)) {
			$arr_result = getCDYApi($arr_param);
			if (!empty($arr_result)) {
				$arr_data = $arr_result['movies'];
				F($str_cacheName, $arr_data, 0, $int_areaNo . '/');//写入缓存
			}
		}
	}

	// 正在上映影片的，上映时间格式化
	foreach ($arr_data as $key=>$val)
	{
	    $arr_data[$key]['publishTime_cn'] = date('m月d日',strtotime($val['publishTime']));
	    $score = explode('.', $val['score']);
	    $arr_data[$key]['left_score'] = $score[0];
	    $arr_data[$key]['right_score'] = $score[1];
	}
	
	
	// 即将上映
	$arr_params = array('action'=>'movie_Query','coming'=>100,'city_id'=>$int_areaNo);
	$str_cacheNames = 'komovie_coming'.'_'.$int_areaNo;//缓存名称为接口名称即将上映与地区ID号结合	
	$arr_datas = F($str_cacheNames, '', 1800, $int_areaNo.'/');//缓存半小时
	if (empty($arr_datas)){
	    $arr_result = getCDYApi($arr_params);
	    if (!empty($arr_result)){
	        $arr_datas = $arr_result['movies'];
	        F($str_cacheNames, $arr_datas, 0, $int_areaNo.'/');//写入缓存
	    }
	}
	
	// 影片数量
	$smarty->assign('count', count($arr_data));
	
	// 即将上映影片的，上映时间格式化
	foreach ($arr_datas as $key=>$val)
	{
	    $arr_datas[$key]['publishTime_cn'] = date('m月d日',strtotime($val['publishTime']));
	    $score = explode('.', $val['score']);
	    $arr_datas[$key]['left_score'] = $score[0];
	    $arr_datas[$key]['right_score'] = $score[1];
	}
	
	// 电影产品
	$shifuMovie = $shifuComing = array();
	$shifuComing = array_shift($arr_datas);
	$movies = array(
	    'hot'=>$arr_data, 
	    'shifuMovie'=>$shifuMovie,
	    'coming'=>$arr_datas,
	    'shifuComing'=>$shifuComing	    
	);
	
	// 得到电影banner图片
	$banner = getMovieBanner();
	// 电影预告
    $movieYugao = $GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['ecs']->table('article')." WHERE article_id = 35");
        
    $smarty->assign('yugao',$movieYugao);
	$smarty->assign('banner',$banner);
	$smarty->assign('category',getCinemaCate(4));
	$smarty->assign('movies', $movies);
	$smarty->display('/movie_times/tmovieList.dwt');
}

// 影院列表
elseif ($_REQUEST['step'] == "cinema")
{	
    $area_id = !empty($_REQUEST['area_id']) ? intval($_REQUEST['area_id']) : 0 ;
    // 分页
    $page = !empty($_REQUEST['page']) ? intval($_REQUEST['page']) : 1 ;
	// 区分类
	if(IS_MATE){
		$areas = getMateCinemaArea();
		$count = getMateCinemaCount($area_id);
		$cinemas = getMateCinemaList($page, 10, $area_id);
	}else{
		$areas = getCinemaArea('komovie');
		$count = getCinemaCount($area_id);
		$cinemas = getCinemaList('komovie', $page, 10, $area_id);
	}

    // 得到电影banner图片
    $banner = getMovieBanner();
    
    $smarty->assign('banner',$banner);
    $smarty->assign('page', $page);
    $smarty->assign('areas', $areas);
    $smarty->assign('area_id', $area_id);
	$smarty->assign('cinemas',$cinemas);
	$pager = get_pager('movie_times.php', array('step'=>'cinema','area_id'=>$area_id), $count, $page, 10);
	$smarty->assign('pager', $pager);
	$smarty->assign('category',getCinemaCate(4));
	$smarty->assign('backHtml',getBackHtml('movie.php'));
	$smarty->display('/movie_times/tcinema.dwt');
}

// 影片详细 -- 电影
elseif ($_REQUEST['step'] == "planCinema")
{
	$movieid = !empty($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;
	$cinemaid = !empty($_REQUEST['cinemaid']) ? intval($_REQUEST['cinemaid']) : 0 ;
	$cityid = !empty($_REQUEST['city']) ? intval($_REQUEST['city']) : 0 ;
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
	$movieDeatil = getMovieDetail($movieid);	
	//评分分割
	$scoreSplit = explode('.',$movieDeatil['score']);
	$movieDeatil['left_score'] = $scoreSplit[0];
	$movieDeatil['right_score'] = $scoreSplit[1];
	// 整理区
	$districts = array();
	if(IS_MATE){
		$movieid = $local_movieid;
		$sql = "SELECT * FROM ".$ecs->table('mate_cinema')." ma LEFT JOIN ".$ecs->table('movie_cinema')." mc ON ma.id = mc.cinema_id WHERE mc.movie_id=".$local_movieid." AND mc.is_show = 1";
		$cinemaList = $db->getAll($sql);
		foreach($cinemaList as $cinema){
			if(!isset($districts[$cinema['area_id']])){
				$districts[$cinema['area_id']] = array('id' => $cinema['area_id'],'name' => $cinema['area_name']);
			}
		}
	}else {
		$cinemaList = getMovieCinema($movieid);
		foreach ($cinemaList as $cinema) {
			if (!isset($districts[$cinema['districtId']])) {
				$districts[$cinema['districtId']] = array('id' => $cinema['districtId'], 'name' => $cinema['districtName']);
			}
		}
	}
	// 得到电影banner图片
	$banner = getMovieBanner();
	
	$smarty->assign('backHtml',getBackHtml('movie.php'));
	$smarty->assign('banner',$banner);
	$smarty->assign('cityid', $cityid);
	$smarty->assign('cinemaid', $cinemaid);
	$smarty->assign('movieid', $movieid);
	$smarty->assign('movieDetail', $movieDeatil);
	$smarty->assign('districts', $districts);
	$smarty->assign('category',getCinemaCate(4));
	$smarty->display('/movie_times/tmovieCinemaDetail.dwt');

}
// 影片详细 -- 影院
elseif ($_REQUEST['step'] == "planCinemas")
{
    $movieid = !empty($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;
	$cinemaid = !empty($_REQUEST['cinemaid']) ? intval($_REQUEST['cinemaid']) : 0 ;
	$cityid = !empty($_REQUEST['city']) ? intval($_REQUEST['city']) : 0 ;
	

	if(IS_MATE){
		$movieDetail = $db -> getRow("SELECT * FROM ".$ecs->table('mate_movie')." WHERE movieId = ".$movieid);
		$cinemaDetail = $db -> getRow("SELECT * FROM ".$ecs->table('mate_cinema')." WHERE id = ".$cinemaid);
		// 评分处理
		$score = explode('.', $movieDetail['score']);
		$movieDetail['left_score'] = $score[0];
		$movieDetail['right_score'] = $score[1];
		$moviesImages = moviesImages(array($movieDetail));
		$movieDetail = $moviesImages[0];
	}else{
		// 影片信息
		$movieDetail = getMovieDetail($movieid);

		// 获得影院详细信息
		$cinemaDetail = getCinemaDetail( $cinemaid, 'komovie_cinema_id');
	}

	// 整理区
	$districts = array();
	if(IS_MATE){
		$areas = getMateCinemaArea();
	}else{
		$areas = getCinemaArea('komovie');
	}
	foreach ($areas as $area)
	{
		if ( !isset($districts[$area['area_id']]) )
		{
			$districts[$area['area_id']] = array( 'id' => $area['area_id'], 'name'=>$area['area_name']);
		}
	}

	// 得到电影banner图片
	$banner = getMovieBanner();
	
	$smarty->assign('backHtml',getBackHtml('movie.php'));
	$smarty->assign('category',getCinemaCate(4));
	$smarty->assign('banner',$banner);
	$smarty->assign('cityid', $cityid);
	$smarty->assign('cinemaDetail', $cinemaDetail);
	$smarty->assign('cinemaid', $cinemaid);
	$smarty->assign('movieid', $movieid);
	$smarty->assign('movieDetail', $movieDetail);
	$smarty->assign('districts', $districts);
    $smarty->display('/movie_times/tmovieCinemaDetails.dwt');

}

// 得到影片在该区域上映的影院列表
elseif ($_REQUEST['step'] == "cinemaList")
{
    // 输出的ajax格式
    $ajaxArray = array( 'error'=>0, 'message'=>'', 'html'=>'', 'cinemaId'=>0);   
    // 影片id
    $movieid = !empty($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;
    // 区域id 
    $disid = !empty($_REQUEST['dis']) ? intval($_REQUEST['dis']) : 0 ;
    // 影院id
    $cinemaid = !empty($_REQUEST['cinemaid']) ? intval($_REQUEST['cinemaid']) : 0 ;
    
    if (empty($movieid) || empty($disid) )
    {
        $ajaxArray['error']    = 1;
        $ajaxArray['message']  = '参数不全！';
        exit(json_encode($ajaxArray));
    }
    
        
    // 影院列表
    $cinemas = array();
	if(IS_MATE) {
		$sql = "SELECT c.* FROM ".$ecs->table('movie_cinema')." mc LEFT JOIN ".$ecs->table('mate_cinema')
			." c ON mc.cinema_id = c.id WHERE c.area_id = ".$disid." AND mc.movie_id = ".$movieid." AND mc.is_show = 1";
		$cinemaList = $db -> getAll($sql);
		foreach ($cinemaList as $cinema){
			$cinemas[$cinema['id']] = transCinemaInfo($cinema);
		}
	}else{
		$cinemaList = getMovieCinema($movieid);
		foreach ($cinemaList as $cinema) {
			if ($cinema['districtId'] == $disid) {
				$cinemas[$cinema['cinemaId']] = $cinema;
			}
		}
	}
    // 处理选中的影院
    // 如果没有指定的影院，默认选择第一个影院
    if (empty($cinemaid))
    {
        reset($cinemas);
        $cinemaIds = current($cinemas);  
        $cinemaid = $cinemaIds['cinemaId'];
    }    
    
    $ajaxArray['cinemaId'] = $cinemaid;
    
    $smarty->assign('cinemaId',  $ajaxArray['cinemaId']);
    $smarty->assign('cinemas',   $cinemas);
    $ajaxArray['html'] = $smarty->fetch('/movie_times/row/tcinemaList.dwt');
    exit(json_encode($ajaxArray));
}

// 当前区域所有的影院信息
elseif ($_REQUEST['step'] == "cinemaLists")
{
    // 输出的ajax格式
    $ajaxArray = array( 'error'=>0, 'message'=>'', 'html'=>'', 'cinemaId'=>0);
    // 影片id
    $movieid = !empty($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;
    // 区域id
    $disid = !empty($_REQUEST['dis']) ? intval($_REQUEST['dis']) : 0 ;
    // 影院id
    $cinemaid = !empty($_REQUEST['cinemaid']) ? intval($_REQUEST['cinemaid']) : 0 ;

    if ( empty($disid) )
    {
        $ajaxArray['error']    = 1;
        $ajaxArray['message']  = '参数不全！';
        exit(json_encode($ajaxArray));
    }


    // 影院列表
    $cinemas = array();
	if(IS_MATE){
		$cinemaList = getMateCinemaList( 1,'1000', $disid );
		foreach ( $cinemaList as $cinema)
		{
			$cinema['cinemaId'] = $cinema['id'];
			$cinema['areaId'] = $cinema['area_id'];
			$cinemas[$cinema['id']] = $cinema;
		}
	}else{
		$cinemaList = getCinemaList( 'komovie',1,'1000', $disid );
		foreach ( $cinemaList as $cinema)
		{
			$cinema['cinemaId'] = $cinema['komovie_cinema_id'];
			$cinema['areaId'] = $cinema['komovie_area_id'];
			$cinemas[$cinema['komovie_cinema_id']] = $cinema;
		}
	}

    // 处理选中的影院
    // 如果没有指定的影院，默认选择第一个影院
//    if (empty($cinemaid))
//    {
//        reset($cinemas);
//        $cinemaIds = current($cinemas);
//        $cinemaid = $cinemaIds['cinemaId'];
//    }

    $ajaxArray['cinemaId'] = $cinemaid;

    $smarty->assign('cinemaId',  $ajaxArray['cinemaId']);
    $smarty->assign('cinemas',   $cinemas);
    $ajaxArray['html'] = $smarty->fetch('/movie_times/row/tcinemaLists.dwt');
    exit(json_encode($ajaxArray));
}

// 影院所有影片列表
elseif ($_REQUEST['step'] == "movieList")
{
	// 输出的ajax格式
	$ajaxArray = array( 'error'=>0, 'message'=>'', 'html'=>'', 'movieid'=>0);
	// 影院id
	$cinemaid = !empty($_REQUEST['cinemaid']) ? intval($_REQUEST['cinemaid']) : 0 ;	
	// 影院所有影片列表
	if(IS_MATE){
		$sql = "SELECT m.* FROM ".$ecs->table('movie_cinema')." c LEFT JOIN ".$ecs->table('mate_movie')
			." m ON c.movie_id = m.movieId WHERE c.cinema_id = ".$cinemaid." AND c.is_show = 1 ORDER BY m.hot DESC";
		$movies = $db -> getAll($sql);
		$movies = moviesImages($movies);
	}else{
		$movies = getCinemaMovies($cinemaid);
	}

	$smarty->assign('movies',  $movies);
	$ajaxArray['html'] = $smarty->fetch('movie_times/row/tmoviesList.dwt');
	echo json_encode($ajaxArray);
}

// 指定影院，指定影片的排期
elseif ($_REQUEST['step'] == "planList")
{
	// 销售比例
	$ratio = getMovieRatio();//get_card_rule_ratio(10002);	
	// 输出的ajax格式
	$ajaxArray = array( 'error'=>0, 'message'=>'', 'html'=>'', 'date'=>'');
	// 影院id
	$cinemaid = !empty($_REQUEST['cinemaid']) ? intval($_REQUEST['cinemaid']) : 0 ;
	// 影片id
	$movieid = !empty($_REQUEST['movieid']) ? intval($_REQUEST['movieid']) : 0 ;
	// 当前选择的日期
	$currentTime = !empty($_REQUEST['currentTime']) ? trim($_REQUEST['currentTime']) : 0 ;	

	if ( empty($cinemaid) || empty($movieid))
	{
		$ajaxArray['error'] = 1;
		$ajaxArray['message'] = '<center style="line-height:487px">暂时没有可用的场次</center>';
		exit(json_encode($ajaxArray));
	}			
	// 获得抠电影影片的排期
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
	$smarty->assign('cinemaid', 	$cinemaid);
	$smarty->assign('planlist', 	$moviePlan);
	$smarty->assign('movieid',      $movieid);
	$smarty->assign('featureTimes', $featureTimes);
	$smarty->assign('currentTime',  $currentTime);
	$smarty->assign('moviePlan', 	$planList);
	
	if (!empty($_REQUEST['currentTime']))
	{
	 
	    $ajaxArray['html'] = $smarty->fetch('/movie_times/row/tplanList.dwt');
	}
	else 
	{
	    $ajaxArray['date'] = $smarty->fetch('/movie_times/row/tdateList.dwt');
	    $ajaxArray['html'] = $smarty->fetch('/movie_times/row/tplanList.dwt');
	}
//	echo '<pre>';
//	print_r($ajaxArray);
//	echo '</pre>';
//	exit;
	exit(json_encode($ajaxArray));		
}

// 在线选座
elseif ($_REQUEST['step'] == "cinemaSeats")
{
	// 销售比例
	$ratio = getMovieRatio();//get_card_rule_ratio(10002);
	
	$hallno		= isset($_GET['hallno']) ? addslashes_deep($_GET['hallno']) : 0 ; 		// 厅号
	$planid 	= isset($_GET['planid']) ? intval($_GET['planid']) : 0 ;		// 场次
	$cinemaid 	= isset($_GET['cinemaid']) ? intval($_GET['cinemaid']) : 0 ;	// 影院
	$movieid 	= isset($_GET['movieid']) ? intval($_GET['movieid']) : 0 ;		// 电影
	$show_index	= isset($_GET['showindex']) ? intval($_GET['showindex']) : 0 ;	// 网票网放映流水号
	$time		= isset($_GET['featuretime'])?addslashes_deep($_GET['featuretime']):date('Y-m-d H:i:s',time());//排期时间
	$delId		= isset($_GET['delid']) ? intval($_GET['delid']) : 0 ;			// 上一次的订单id

	// 重新选择作为的时候，取消上一个订单
	if($delId > 0 ){
		if( $order_sn = $GLOBALS['db']->getOne('SELECT order_sn FROM '.$GLOBALS['ecs']->table('seats_order')." WHERE user_id = '".intval($_SESSION['user_id'])."' and id = '".$delId."' and  order_status = 1")){
			$GLOBALS['db']->query('UPDATE '.$ecs->table('seats_order')." SET order_status = 2 WHERE id = '$delId' and  order_status = 1");
			$deleteStatus = getCDYapi(array('action'=>'order_Delete', 'order_id'=>$order_sn));
		}	
	}

	$planInfo = array();
	$weeks = array(0=>'星期日',1=>'星期一',2=>'星期二',3=>'星期三',4=>'星期四',5=>'星期五',6=>'星期六');

	// 影片排序信息
	if(!empty($show_index)&&IS_MATE){
		//获取网票网影片的排期
		require_once(ROOT_PATH . 'includes/lib_wpwMovieClass.php');
		$wpwClass = new wpwMovie();
		$show_check = $wpwClass->baseFilmShowCheck($cinemaid, $time, $show_index);
		if($show_check['ErrNo'] != 0||$show_check['Data'][0]['Result']==false){ show_message('排期无效或已改场，请选择其他场次！'); }
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
	}else{
		$planQuery = getMoviePlan($cinemaid, $movieid);

		if (!empty($planQuery))
		{
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
	}

	$smarty->assign('backHtml',        getBackHtml('movie.php'));
	$smarty->assign('planInfo',        $planInfo);
	$smarty->assign('seatParam', 	   json_encode(array('hallno'=>$hallno, 'planid'=>$planid, 'cinemaid'=>$cinemaid, 'movieid'=>$movieid,'showindex'=>$show_index)) );

	$smarty->display('/movie_times/tmovieSeats.dwt');
}

// 加载座位图
elseif ($_REQUEST['step'] == "seatAjax")
{
	$hallno = isset($_POST['hallno']) ? addslashes_deep($_POST['hallno']) : 0 ; 		// 厅号
	$planid = isset($_POST['planid']) ? intval($_POST['planid']) : 0 ;		// 场次
	$cinemaid = isset($_POST['cinemaid']) ? intval($_POST['cinemaid']) : 0 ;	// 影院
	$movieid = isset($_POST['movieid']) ? intval($_POST['movieid']) : 0 ;		// 电影
	$show_index = isset($_POST['showindex']) ? intval($_POST['showindex']) : 0 ;	// 网票网放映流水号

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
		$seat_size = array(
			'size' => 30,//渲染时座位尺寸
			'gap'=> 4//渲染时座位间隙
		);
		$tran_seat = transSeatsWtoK($result['Data'],$sell_seat_result['Data'],$hallno,$seat_size);
		$seatInfo = $tran_seat['seat'];
		$allWidth = $tran_seat['maxRowLength'];
		$allheight = $tran_seat['maxColHeight'] +50;
	}else {
		$arr_param = array(
			'action' => 'seat_Query',
			'plan_id' => $planid,
			'hall_id' => $hallno,
			'cinema_id' => $cinemaid
		);

		$arr_result = getCDYapi($arr_param);

		// 场次已经开始
		if ($arr_result['status'] != 0) {
			exit("<center style='font-size:14px;color:#666'>" . $arr_result['error'] . "</center>");
		}

		// 未返回座位信息
		if (empty($arr_result['seats'])) {
			exit("<center style='font-size:14px;color:#666'>该场次不能选择，请选择其他场次！</center>");
		}
		$setField = array(
			'hallName' => 'hallId',            // 厅号属性名
			'graphRowName' => 'graphRow',    // 排号属性名     X
			'graphColName' => 'graphCol',        // 座位号属性名  	Y
			'seatRowName' => 'seatRow',
			'width' => 699                    // 影院的总宽
		);

		$seatQuery = new seatQueryInfo($setField);
		$seatInfo = $seatQuery->getSeatInfo($arr_result['seats']);
		$allWidth = max($seatQuery->getField('colsCount')) * 40 + 20;
	}

	$smarty->assign('allwidth',		$allWidth);
	$smarty->assign('seatInfo',     $seatInfo);   //座位信息
	if(IS_MATE&&!empty($show_index)) {
		$smarty->assign('allheight', $allheight);
		exit($smarty->fetch('movie_times/row/twangseatmap.dwt'));
	}else{
		exit($smarty->fetch('movie_times/row/tseatmap.dwt'));
	}
}

// 刷卡影院列表
else if ( $_REQUEST['step'] == "shuaka" )
{
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
    
    $smarty->assign('category',getCinemaCate(4));
    $smarty->assign('city_list',         $city_list);
    $smarty->assign('district_list',         $district_list);
    
    // 得到电影banner图片
    $banner = getMovieBanner();    
    $smarty->assign('banner',$banner);
    $smarty->assign('backHtml',getBackHtml('movie.php'));
    $smarty->display('movie_times/tshuaka.dwt');
}

// 电子兑换券--影院列表
elseif ($_REQUEST['step'] == "cinemaDzq")
{
    ecs_header("Location:movie.php?step=cinemaDzq");
}
// 兑换券详情
elseif ($_REQUEST['step'] == "showDzq")
{   
    ecs_header("Location:movie.php?step=cinemaDzq");
}

