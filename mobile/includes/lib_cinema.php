<?php

// 电影销售比例销售比例
function getMovieRatio($returnRatio = false)
{
    return get_card_rule_ratio(10002,$returnRatio);
}

function getDzqRatio($returnRatio = false)
{
    return get_card_rule_ratio(10004,$returnRatio);
}

/** 获得影片详情
 *  @param	int		$movieid 	影片id
 *  @return	array	返回影片信息  
 */
function getMovieDetail( $movieid )
{
	if ( empty($movieid ))
		return array();
	
	// 当前选择的城市
	$int_cityId = getAreaNo(0,'komovie');
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	
	$arr_param = array('action'=> 'movie_Query','movie_id' => $movieid,'city_id'=>$int_cityId);
	$str_cacheName = 'movie_Query_' . $int_cityId;       //缓存名称为接口名称+电影ID
	$movieDetail = F($str_cacheName, '', 1800, $movieid.'/');//缓存半小时
	if ( empty($movieDetail) )
	{
		$arr_result = getCDYApi($arr_param);
		if (!empty($arr_result['movie'])){
			$movieDetail = $arr_result['movie'];
			F($str_cacheName, $movieDetail, 0, $int_cityId.'/');//写入缓存
		}
	}
	
	// 评分处理
	$score = explode('.', $movieDetail['score']);
	$movieDetail['left_score'] = $score[0];
	$movieDetail['right_score'] = $score[1];
	
	$moviesImages = moviesImages(array($movieDetail));
	return $moviesImages[0];
}

/**  
 *  获得影片上映的影院列表
 *  @param 	int		$movieid	影片id
 *  @return	array	返回影院列表
 */
function getMovieCinema( $movieid )
{
	if ( empty($movieid ))
		return array();
	
	// 当前选择的城市
	$int_cityId = getAreaNo(0,'komovie');
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	
	$arr_param = array('action'=>'cinema_Query','movie_id'=>$movieid,'city_id'=>$int_cityId);
	$str_cacheName = 'cinema_Query_'.$movieid; //该城市播放该影片的影院  缓存名称   接口名称+城市ID+电影ID
	$cinemas = F($str_cacheName, '', 1800, $int_cityId .'/');//缓存半小时
	if(empty($cinemas)){
		$arr_result = getCDYApi($arr_param);
		if(!empty($arr_result['cinemas'])){
			$tmpCinemas = $arr_result['cinemas'];			
			$cinemas = dorpOutCinema($tmpCinemas);
			F($str_cacheName, $cinemas, 1800, $int_cityId.'/');//该城市播放该影片的影院写入缓存
		}
	}
			
	return $cinemas;
}

/**
 *	获得影院详细信息
 *  
 *  @param	$cinemaid		int		影院id
 *  @parma	$ext			str		搜索条件的健，默认是id    
 *  @return array					影院详细信息
 */
function getCinemaDetail($cinemaid, $ext = 'id')
{
	if (empty($cinemaid))
		return array();

	$cinemaResult = $GLOBALS['db']->getRow('SELECT * FROM '.$GLOBALS['ecs']->table('cinema_list'). " WHERE $ext = '$cinemaid'");
	
	// 如果数据库里不存在该影院，就调用接口获取影院信息
	// 只有在线选座过来的影院没有在数据库中，才调用抠电影接口获取影院名称，
	// 电子券 和 线下刷卡，只要支持，就一定会有对应的影院id，
	if (empty($cinemaResult) && $ext == 'komovie_cinema_id')
	{
		$int_cityId = getAreaNo(0,'komovie');
		include_once(ROOT_PATH . 'includes/lib_cardApi.php');
		
		$param = array( 'action'=>'cinema_Query', 'cinema_id'=>$cinemaid);
		$cacheName = 'cinema_detail-'.$cinemaid;
		$cinemaDetail = F( $cacheName, '', 1800, $int_cityId.'/');
		if (empty($cinemaDetail))
		{
			$arr_result = getCDYApi($param);
			$cinemaDetail = $arr_result['cinema'];
			F( $cacheName, $cinemaDetail, 1800, $int_cityId.'/');
		}
		
		// 统一影院输出格式（以数据库字段为准）
		$cinemaResult = array();
		
		$cinemaResult['cinema_name'] 	= $cinemaDetail['cinemaName'];
		$cinemaResult['cinema_address']	= $cinemaDetail['cinemaAddress'];
		$cinemaResult['is_komovie']		= 1;
		$cinemaResult['open_time']		= $cinemaDetail['openTime'];
		$cinemaResult['logo']		    = !empty($cinemaDetail['logo']) ? $cinemaDetail['logo'] : 'images/dongwang/null.jpg' ;
		$cinemaResult['cinema_tel']		= !empty($cinemaDetail['cinemaTel']) ? $cinemaDetail['cinemaTel'] : '无' ;
	}
	
	$newCinemaResult = cinemaLogo(array($cinemaResult));
    return $newCinemaResult[0];
}

/**
 * 获取影院上映的影片列表
 * 
 * @param	$cinemaid 		int		影院id
 * @return	$returnArray	array	返回影院上映的所有影片列表
 */
function getCinemaMovies($cinemaid)
{
	if (empty($cinemaid))
		return array();
	
	$int_cityId = getAreaNo(0,'komovie');
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	
	$param = array( 'action'=>'movie_Query', 'cinema_id'=> $cinemaid);
	$cacheName = 'cinema_movies_'.$cinemaid;
	$cinemaMovies = F( $cacheName, '', 1800, $int_cityId.'/');
	if (empty($cinemaMovies))
	{
		$arr_result = getCDYApi($param);
		$cinemaMovies = $arr_result['movies'];
		F( $cacheName, $cinemaMovies, 1800, $int_cityId.'/');
	}
		
	return moviesImages($cinemaMovies);
}

/**  
 *  获得指定影院指定影片的排期列表
 *  
 *  @param	$cinemaid	int		影院id
 *  @param	$movieid	int		影片id
 *  
 */
function getMoviePlan( $cinemaid, $movieid)
{
	$int_cityId = getAreaNo(0,'komovie');
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	
	$param = array('action'=>'plan_Query','movie_id'=>$movieid,'cinema_id'=>$cinemaid);//
	$cacheName = 'plan_Query_'. $cinemaid . '_'.$movieid;
	$moviePlan = F($cacheName, '', 1800, $int_cityId .'/');//缓存十分钟
	if (empty($moviePlan)) {
		$result = getCDYApi($param);
		if ($result['plans']) {
			$moviePlan = $result['plans'];			
			F($cacheName, $moviePlan, 1800, $int_cityId.'/');//写入缓存
		}
	}
	
	return $moviePlan;
}

/* 手机端影院列表 */
function wapCinemaList()
{
    $returnArray = array();
    $cinemas = $GLOBALS['db']->getAll('SELECT * FROM ' . $GLOBALS['ecs']->table('cinema_list') .' WHERE region_id ='.$_SESSION['cityid']);
    foreach ($cinemas as $cinema)
    {
        // 删除地区为空的影院
        if (empty($cinema['area_id']))
        {
            continue;
        }
        // 删除什么都不支持的影院
        if ($cinema['is_komovie'] == 0 && $cinema['is_dzq'] == 0 && $cinema['is_brush'] ==0)
        {
            continue;
        }
    
        if ( empty($returnArray[$cinema['area_id']]) )
        {
            $returnArray[$cinema['area_id']]['area_name'] = $cinema['area_name'];
        }
    
        $returnArray[$cinema['area_id']]['cinemas'][] = $cinema;
    }
    
    return $returnArray;
}
/**
 * 获得所有影院列表
 */
function getCinemaList($type = 'komovie', $page=1, $pagesize="10", $area_id)
{
	$where = "WHERE 1 AND region_id =".$_SESSION['cityid']." ";
	if ($type == 'komovie')
	   $where .= " AND is_komovie = 1 ";
	elseif($type == 'dzq')
	   $where .= " AND is_dzq = 1 ";
	else 
	   $where .= ' AND is_brush = 1';
	
	// 筛选条件
	if (!empty($area_id))
        $where .= ' AND komovie_area_id = '.$area_id;
    else 
        $where .= ' AND komovie_area_id > 0';
	
	// 分页
	$startLimit = ($page-1 != 0) ? ($page-1)*$pagesize : 0 ;
	$limit = 'LIMIT '.$startLimit.','.$pagesize;
	$cinemas = $GLOBALS['db']->getAll('SELECT * FROM ' . $GLOBALS['ecs']->table('cinema_list') .' '.$where.' ORDER BY id DESC '.$limit );	
    
	return cinemaLogo($cinemas);
}

function getCinemaCount($area_id, $type='komovie')
{
    $where = "region_id =".$_SESSION['cityid']." ";
    if ($type == 'komovie')
        $where .= " AND is_komovie = 1 ";
    elseif($type == 'dzq')
        $where .= " AND is_dzq = 1 ";
    else
        $where .= ' AND is_brush = 1';
    
    // 筛选条件
    if (!empty($area_id))
        $where .= ' AND komovie_area_id = '.$area_id;
    else 
        $where .= ' AND komovie_area_id > 0';
    
    $counts = findData('cinema_list', $where,'count(*) as count');
    return $counts[0]['count'];
}

/**  
 * 获取地区分类
 * @param string $type
 */
function getCinemaArea($type = 'komovie')
{
   $returnArray = array();
   $where = " region_id =".$_SESSION['cityid']." ";
   
   if ($type == 'komovie')
       $where .= " AND is_komovie = 1 AND komovie_area_id > 0";
   elseif($type == 'dzq')
       $where .= " AND is_dzq = 1 ";
   else
       $where .= ' AND is_brush = 1';
   
   $result = findData('cinema_list', $where, 'area_name,komovie_area_id');
   if (!empty($result))
   {
       foreach ($result as $value)
       {
           $returnArray[$value['komovie_area_id']]['area_name'] = $value['area_name'];
           $returnArray[$value['komovie_area_id']]['area_id'] = $value['komovie_area_id'];
       }
   }
   
   return $returnArray;
}
/**  
 *  电影 banner图片    
 */
 
 function getMovieBanner()
 {
     // $posid = 1 的是电影banner广告
    return getNavadvs(1);

 }

/**
 * 整理并获得排期日期（月-日 星期）  
 */
function featureTime( $moviePlan )
{
	$returnArray = array();
	foreach ($moviePlan as $plan)
	{
		 $strtotime = date('Y-m-d', strtotime($plan['featureTime']));
		 $totime = strtotime($strtotime);
		 if (!array_key_exists($totime, $returnArray))
		 {
			$returnArray[$totime]['strtotime'] = $strtotime;
			$returnArray[$totime]['strtotime_sn'] = date('m月d日',$totime).' '.timeWeek($totime);
		 }
	}
	ksort($returnArray);
	return $returnArray;
}

/**  
 *  星期
 */
function timeWeek($time)
{
    $strWeek = '';
    $week = array('周日','周一','周二','周三','周四','周五','周六');
    $w = date('w',$time);
    if ($w == date('w', local_gettime()))
        $strWeek = '今天';
    else 
        $strWeek = $week[$w];
       
    
    return $strWeek;
}

/**
 *  通过日期找到符合该日期的排期
 */
function searchPlan( $moviePlan, $currentTime, $ratio)
{
	$returnArray = array();
	
	foreach ($moviePlan as $plan)
	{
		$featureTime = date('Y-m-d', strtotime($plan['featureTime']));
		if (strcasecmp($currentTime, $featureTime) == 0)
		{
			// 小于当前时间的排期，是不可选的，所有要过滤掉
			$time = strtotime($plan['featureTime']);			
			if (local_gettime() >= $time )
			{
				continue;
			}
			// 开场前60分钟的的排期设置成已过场			
			if ( $time - local_gettime() <= 3600){
				$plan['is_cut'] = 1;
			}else{
				$plan['is_cut'] = 0;
			}
			//时间分段
			$ufeatureTime = strtotime(date('H:i:s',strtotime($plan['featureTime'])));
			if($ufeatureTime>=strtotime('00:00:00')&&$ufeatureTime<strtotime('05:59:59')){
				$plan['periods']=1;//晚上
			}elseif($ufeatureTime>=strtotime('06:00:00')&&$ufeatureTime<strtotime('11:59:59')){
				$plan['periods']=2;//上午
			}elseif ($ufeatureTime>=strtotime('12:00:00')&&$ufeatureTime<strtotime('17:59:59')){
				$plan['periods']=3;//下午
			}elseif ($ufeatureTime>=strtotime('18:00:00')&&$ufeatureTime<strtotime('23:59:59')){
				$plan['periods']=1;//晚上
			}
			// 成本价
			$plan['extInfo'] = $plan['price'];
			if ($ratio !== false){
				$plan['price'] = number_format(round($plan['price']*$ratio,1),2);
			}else{
				$plan['price'] = interface_price($plan['price'], 'komovie');
			}
			$plan['time'] = date('H:i', strtotime($plan['featureTime']));
			$returnArray[] = $plan;
		}
	}
	
	return $returnArray;
}

/**  
 *  获得指定影院的电子券信息
 *  
 *  @param	$cinemaid	str 	影院编号（电子券的影院编号）
 *  @param 	$ratio		int		销售比例
 *  @param 	array				返回电子券信息列表
 */

function getCinemaDzq( $cinemaid, $ratio)
{
	$dzqData = array();
	$int_cityId = getAreaNo();
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	
	$param = array('AreaNo'=>$int_cityId ,'CinemaNo'=>$cinemaid);//
	$cacheName = 'getCommTickets_'. $cinemaid . '_'.$int_cityId;
	$result = getYYApi($param, 'getCommTickets');
	
	// 整理数据格式
	if ( !empty($result['body']['item']))
	{
		$dzqData = $result['body']['item'];
		foreach ($dzqData as $key=>$val)
		{
			$arr_type = array(	'1' => '2D','2' => '3D','3' => '4D','4' => 'IMAX','5' => '点卡'	);			
			unset($dzqData[$key]);
			$val['ProductSizeZn'] = $arr_type[$val['ProductSize']];
			if ($ratio !== false){
				$val['SalePriceFormat'] = price_format(($val['SalePrice']/1.2*1.06)*$ratio);
			}else{
				$val['SalePriceFormat'] = price_format($val['SalePrice']/1.2*1.06);
			}
						
			$dzqData[$val['TicketNo']] = $val;
		}
	}

	return $dzqData;
	
}

/**
 *  远程图片本地化--电影
 */
function moviesImages( $movies )
{
	foreach ($movies as &$arr)
	{
	    // 缩略图
		$image_path = explode('/', $arr['pathVerticalS']);
		$filenames = array_pop($image_path);
		if (!file_exists(ROOT_PATH.'temp/komovie/'.$filenames)){
			$new_images = getImage($arr['pathVerticalS'], ROOT_PATH. 'temp/komovie', $filenames);
		}
		// 宣传图
		$images_path = explode('/', $arr['pathHorizonH']);
		$filenames2 = array_pop($images_path);
		if (!file_exists(ROOT_PATH.'temp/komovie/'.$filenames2)){
		    $new_images = getImage($arr['pathHorizonH'], ROOT_PATH. 'temp/komovie', $filenames2);
		}
		$arr['thumb'] = 'temp/komovie/'.$filenames;
		$arr['thumbH'] = 'temp/komovie/'.$filenames2;
	}
	
	return $movies;
}

/**
 *  远程图片本地化--影院
 */
function cinemaLogo( $cinema )
{
    foreach ($cinema as &$arr)
    {
        if (empty($arr['logo']))
        {
            $arr['logo'] = '/images/dongwang/null.jpg';
        }
        else {
            $image_path = explode('/', $arr['logo']);
            $filenames = array_pop($image_path);
            if (!file_exists(ROOT_PATH. 'temp/komovie/logo/'.$filenames)){ 
                $new_images = getImage($arr['logo'], ROOT_PATH. 'temp/komovie/logo/', $filenames);
            }
            $arr['logo'] = 'temp/komovie/logo/'.$filenames;
        }
    }

    return $cinema;
}


/**
 * 删除 platform 不在 10000 - 20000 直接的影院（不在 10000 - 20000 之间的影院不能在线选座）
 * 
 * @param array $cinemas	影院列表
 * @return array 整理后的影院
 */
function dorpOutCinema( $cinemas )
{
	$returnCinema = array();
	foreach ( $cinemas as $cinemaKey=>$cinemaVal)
	{
		if ($cinemaVal['platform'] >= 10000 && $cinemaVal['platform'] <= 20000)
		{
			$returnCinema[$cinemaKey] = $cinemaVal;
		}
	}
	return $returnCinema;
}
/**
 * 根据精度纬度得到距离影院的距离
 */

function getdistance($lng1,$lat1,$lng2,$lat2){
	//将角度转为狐度
	$radLat1=deg2rad($lat1);//deg2rad()函数将角度转换为弧度
	$radLat2=deg2rad($lat2);
	$radLng1=deg2rad($lng1);
	$radLng2=deg2rad($lng2);
	$a=$radLat1-$radLat2;
	$b=$radLng1-$radLng2;
	$s=2*asin(sqrt(pow(sin($a/2),2)+cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)))*6378.137*1000;
	return $s;
}
