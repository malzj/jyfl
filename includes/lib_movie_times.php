<?php
/**
 * 每次消费的具体点数
 * @param unknown $unitPrice 单张票价
 */
function getBuyPrice($unitPrice)
{
    $buyPrice = 0;
    
    $data = getCardBin(substr($_SESSION['user_name'], 0, 6));
    if ($unitPrice > $data['cordon_up'])
        $buyPrice = $data['cordon_up'];
    else
        $buyPrice = $data['cordon_dwon'];
    
    return $buyPrice;
}

//判断是否为次卡
function is_times_card(){
    $data = getCardBin(substr($_SESSION['user_name'], 0, 6));
    return !empty($data);
}
//判断是否电影比价
function is_mate_movie(){
    return intval($GLOBALS['int_cityId'])==2?true:false;
}

// 获取次数电影新地址
function get_times_nav($nav_list){
    foreach ($nav_list['middle'] as $key => &$nav){
        $nav['url'] = preg_replace('/movie.php/','movie_times.php',$nav['url']);
        if(isset($nav['child'])){
            foreach ($nav['child'] as $k => &$child){
                $child['url'] = preg_replace('/movie.php/','movie_times.php',$child['url']);
            }
        }
    }
    $nav_list['middle'] = array_reverse($nav_list['middle']);
    return $nav_list;
}

/**
 * 差额计算
 * @param  integer  $unitPrice 单价
 * @param  integer  $seatCount 数量 
 */
function getDiffPrice($unitPrice, $seatCount)
{
    $deffPrice = 0;
    // 卡BIN数据
    $data = getCardBin(substr($_SESSION['user_name'], 0, 6));  
    // 检查剩余次数
    if (!checkMaxCount($seatCount, $data))
        return -1;

    if ($unitPrice > $data['cordon_up'])
        $deffPrice = $unitPrice - $data['cordon_up'];

    return $deffPrice * $seatCount;
}

// 次卡最大的购买次数
function getMaxBuyCount($data=array())
{
    if (empty($data))
        $currentData = getCardBin(substr($_SESSION['user_name'], 0, 6));
    else
        $currentData = $data;

    $userMoney = $GLOBALS['db']->getOne("SELECT card_money FROM ".$GLOBALS['ecs']->table('users').' WHERE user_id = '.$_SESSION['user_id']);
     
    return floor($userMoney / $currentData['cordon_dwon']);
}

// 检查消费次数和剩余次数的合法性
function checkMaxCount($seatCount, $data=array())
{
    $maxCount = getMaxBuyCount($data);
    return ($seatCount <= $maxCount);
}

// 得到卡BIN信息
function getCardBin($cardBin){
    $data = findData('cardbin','cardBin='.$cardBin.' AND card_ext = 2');
    return current($data);
}

/**
 * 将网票网电影格式转换成抠电影格式
 * @param $movie
 */
function transMovieWtoK($movie){
    $re_array = array(
        'movieId'=>$movie['ID'],
        'movieName'=>$movie['Name'],
        'movieType'=>$movie['Sort'],
        'pathVerticalS'=>$movie['SPhoto'],
        'pathHorizonH'=>$movie['Hphoto'],
        'director'=>$movie['Director'],
        'actor'=>$movie['MP'],
        'intro'=>$movie['Des'],
        'movieLength'=>$movie['Duration'],
        'publishTime'=>$movie['Showdate'],
    );
    return $re_array;
}
/**
 * 将网票网电影排期格式转换成抠电影格式
 * @param $plan
 */
function transPlanWtoK($plan,$movieid,$cinemaid){
    $re_array = array(
//        'movieId'=>$movieid,//本地电影id
//        'cinemaId'=>$cinemaid,//本地电影id
//        'wangMovieId'=>$plan['FilmID'],
//        'wangCinemaId'=>$plan['CinemaId'],
        'movieId'=>$plan['FilmID'],
        'cinemaId'=>$plan['CinemaID'],
        'planId'=>0,
        'featureTime'=>$plan['ShowTime'],
        'screenType'=>$plan['Dimensional'],
        'language'=>$plan['LG'],
        'hallNo'=>$plan['HallID'],
        'hallName'=>$plan['HallName'],
        'price'=>$plan['VPrice'],
        'ShowIndex'=>$plan['ShowIndex'], //网票网场次号
        'from_source' => 'wangmovie'    //标识信息来源为网票网
    );
    return $re_array;
}
/**
 * 将本地影院格式转换成抠电影格式
 * @param $cinema
 * @return array
 */
function transCinemaInfo($cinema){
    $re_array = array(
        'cinemaId' => $cinema['id'],
        'logo' => $cinema['logo'],
        'cinemaName' => $cinema['cinema_name'],
        'cinemaAddress' => $cinema['cinema_address'],
        'cinemaTel' => $cinema['cinema_tel'],
        'districtId' => $cinema['area_id'],
        'districtName' => $cinema['area_name'],
        'openTime' => $cinema['open_time'],
    );
    return $re_array;
}
function transSeatsWtoK($seats,$sell_seats,$hallid){
    if(empty($seats))
        return array();
    $status=array(
        'Y'=>0,
        'N'=>1
    );

    $sell_status = array();
    if(!empty($sell_seats)){
        foreach($sell_seats as $sell){
            $sell_status[$sell['SeatID']] = $status[$sell['Status']];
        }
    }
    $maxRowLength = 0;
    $maxColHeight = 0;
    $minLeft = 0;
    foreach($seats as $seat){
        $seat_name = explode(':',$seat['Name']);
        if(!$status[$seat['Status']]&&!$sell_status[$seat['SeatID']]){
            $seat_status = 0; //可用
        }else{
            $seat_status = 1; //不可用
        }
        if((int)$seat['ColumnID'] > $maxRowLength)
            $maxRowLength = (int)$seat['ColumnID'];
        if((int)$seat['RowID'] > $maxColHeight)
            $maxColHeight = (int)$seat['RowID'];
        $minLeft = (int)$seat['ColumnID'];
        if((int)$seat['ColumnID'] < $minLeft)
            $minLeft = (int)$seat['ColumnID'];
        $re_array['seat'][] = array(
                'graphCol'=>$seat['ColumnID'],
                'graphRow'=>$seat['RowID'],
//            'graphCol'=>(int)$seat_name[1],
//            'graphRow'=>(int)$seat_name[0],
                'hallId'=>$hallid,
                'seatCol'=>$seat_name[1],
                'seatNo'=>$seat['SeatIndex'],
                'seatPieceName'=>$seat['SeatID'],
                'seatPieceNo'=>$seat['SeatID'],
                'seatRow'=>$seat_name[0],
                'seatState'=>$seat_status,
                'seatType'=>!empty($seat['LoveFlag'])?1:0,
        );
    }
    $re_array['maxRowLength']=$maxRowLength;
    $re_array['maxColHeight']=$maxColHeight;
    $re_array['minLeft']=$minLeft;

    return $re_array;
}
function getMateMoviePlan($cinemaid,$movieid,$currentTime,$ratio){
    //获取对应的影院id
    $cinema_ids = $GLOBALS['db']->getRow("SELECT komovie_cinema_id,wang_cinema_id FROM ".$GLOBALS['ecs']->table('mate_cinema')." WHERE id = ".$cinemaid);
    //获取排期对应电影id
    $movie_ids = $GLOBALS['db']->getRow("SELECT komovie_id,wangmovie_id FROM ".$GLOBALS['ecs']->table('mate_movie')." WHERE movieId =".$movieid);

    $kfeatureTimes = array();
    $wfeatureTimes = array();
    // 获得抠电影影片的排期
    if(!empty($cinema_ids['komovie_cinema_id'])&&!empty($movie_ids['komovie_id'])) {
        $moviePlan = getMoviePlan($cinema_ids['komovie_cinema_id'], $movie_ids['komovie_id']);
        // 整理排期日期
        $kfeatureTimes = featureTime($moviePlan);

        if($moviePlan) {
            $koplan_list = array();
            foreach ($moviePlan as $kplan) {
                $koplan_list[$kplan['featureTime']] = $kplan;
            }
        }
    }

    //获取网票网影片的排期
    if(!empty($cinema_ids['wang_cinema_id'])&&!empty($movie_ids['wangmovie_id'])) {
        $startdate = gmtime();
        $enddate = strtotime("+6 days",$startdate);
        while ($startdate <= $enddate) {
            $strtotime = date('Y-m-d', $startdate);
            $totime = strtotime($strtotime);
            $planTimes[$totime]['strtotime'] = $strtotime;
            $planTimes[$totime]['strtotime_sn'] = date('m月d日',$totime).' '.timeWeek($totime);
            ksort($planTimes);
            $startdate = strtotime("+1 day", $startdate);
        }
        require_once(ROOT_PATH . 'includes/lib_wpwMovieClass.php');
        include_once(ROOT_PATH . 'includes/lib_cardApi.php');
        $wpwClass = new wpwMovie();
        $cacheName = 'wang_plan_'. $cinema_ids['wang_cinema_id'] . '_'.$movie_ids['wangmovie_id'];
        $plan_array = F($cacheName, '', 1800, 'wang_'.$GLOBALS['int_cityId'] .'/');//缓存半小时
        if(empty($plan_array)) {
            foreach($planTimes as $key=>$ft) {
                $wpwPlan = $wpwClass->baseFilmShow($cinema_ids['wang_cinema_id'], $ft['strtotime'], $movie_ids['wangmovie_id']);
                if($wpwPlan['ErrNo']==0&&!empty($wpwPlan['Data'])){
                    $plan_array['feature_times'][$key] = $ft;
                    $plan_array['movie_plan'][$ft['strtotime']]=$wpwPlan['Data'];
                }
            }
        }
        if(!empty($plan_array)){
            F($cacheName, $plan_array, 1800, 'wang_'.$GLOBALS['int_cityId'] .'/');//写入缓存
            $wfeatureTimes = $plan_array['feature_times'];
        }
    }

    $featureTimes = $kfeatureTimes + $wfeatureTimes;
    // 如果没有选择时间，默认选择第一个
    if (empty($currentTime)) {
        reset($featureTimes);
        $currentTimes = current($featureTimes);
        $currentTime = $currentTimes['strtotime'];
    }

    if (!empty($plan_array)) {
        if(isset($plan_array['movie_plan'][$currentTime])) {
            foreach ($plan_array['movie_plan'][$currentTime] as $k => $plan) {
                $begin_time = date('Y-m-d H:i:s', strtotime($plan['ShowTime']));
                if (!empty($koplan_list)) {
                    if ($plan['VPrice'] < $koplan_list[$begin_time]['price']) {
                        $moviePlans[$begin_time] = transPlanWtoK($plan, $movieid, $cinemaid);
                    } else {
                        $moviePlans[$begin_time] = $koplan_list[$begin_time];
                    }
                } else {
                    $moviePlans[$begin_time] = transPlanWtoK($plan, $movieid, $cinemaid);
                }
            }
            ksort($moviePlans, SORT_STRING);
            $moviePlan = array_values($moviePlans);
        }
    }
    $planList = searchPlan($moviePlan, $currentTime, $ratio);
    $re_array = array(
        'planList'=>$planList,
        'featureTimes'=>$featureTimes,
        'currentTime'=>$currentTime
    );
    return $re_array;
}

/**
 * 查询影院列表
 * @param int $page
 * @param string $pagesize
 * @param $area_id
 * @return mixed
 */
function getMateCinemaList($page=1, $pagesize="10", $area_id){
    $where = "WHERE 1 AND ma.region_id =".$_SESSION['cityid']." ";

    // 筛选条件
    if (!empty($area_id))
        $where .= ' AND ma.area_id = '.$area_id;
    else
        $where .= ' AND ma.area_id > 0';

    // 分页
    $startLimit = ($page-1 != 0) ? ($page-1)*$pagesize : 0 ;
    $limit = 'LIMIT '.$startLimit.','.$pagesize;
    $sql = "SELECT ma.* FROM ".$GLOBALS['ecs']->table('mate_cinema')
        ." ma LEFT JOIN (SELECT cinema_id,count(movie_id) as movie_count FROM ".$GLOBALS['ecs']->table('movie_cinema')
        ." GROUP BY cinema_id) mo ON ma.id = mo.cinema_id ".$where." AND mo.movie_count>0 ORDER BY id DESC ".$limit;
    $cinemas = $GLOBALS['db']->getAll($sql);

    return cinemaLogo($cinemas);
}
/**
 * 查询影院总数
 * @param $area_id
 * @return mixed
 */
function getMateCinemaCount($area_id)
{
    $where = "ma.region_id =".$_SESSION['cityid']." ";

    // 筛选条件
    if (!empty($area_id))
        $where .= ' AND area_id = '.$area_id;
    else
        $where .= ' AND area_id > 0';
    $sql = "SELECT count(*) AS count FROM ".$GLOBALS['ecs']->table('mate_cinema')
        ." ma LEFT JOIN (SELECT cinema_id,count(movie_id) as movie_count FROM ".$GLOBALS['ecs']->table('movie_cinema')
        ." GROUP BY cinema_id) mo ON ma.id = mo.cinema_id WHERE ".$where." AND mo.movie_count>0";
    $count = $GLOBALS['db'] -> getOne($sql);
    return $count;
}

//获取影院区域列表
function getMateCinemaArea(){
    $returnArray = array();
    $where = " region_id =".$_SESSION['cityid']." ";

    $result = findData('mate_cinema', $where, 'area_name,area_id');
    if (!empty($result))
    {
        foreach ($result as $value)
        {
            $returnArray[$value['area_id']]['area_name'] = $value['area_name'];
            $returnArray[$value['area_id']]['area_id'] = $value['area_id'];
        }
    }

    return $returnArray;
}