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

function getMovieId(){

}

function getMateMoviePlan($cinemaid,$movieid){
    // 获得抠电影影片的排期
    $moviePlan = getMoviePlan( $cinemaid, $movieid );
    $koplan_list = array();
    foreach($moviePlan as $kplan){
        $koplan_list[$kplan['featureTime']] = $kplan;
    }
    //获取对应的影院id
    $wangcinema_id = $GLOBALS['db']->getOne("SELECT wangcinema_id FROM ".$GLOBALS['ecs']->table('mate_cinema')." WHERE kocinema_id = ".$cinemaid);
    //获取排期对应电影id
    $wangmovie_id = $GLOBALS['db']->getOne("SELECT * FROM ".$GLOBALS['ecs']->table('mate_movie')." WHERE komovie_id =".$movieid);

    //获取网票网影片的排期
    if(!empty($wangcinema_id)&&!empty($wangmovie_id)) {
        require_once(ROOT_PATH . 'includes/lib_wpwMovieClass.php');
        $wpwClass = new wpwMovie();
        $wpwPlan = $wpwClass->baseFilmShow($wangcinema_id, '', $wangmovie_id);
        if (!empty($wpwPlan['data'])) {
            foreach ($wpwPlan['data'] as $k => $plan) {
                $begin_time = date('H:i:s', $plan['ShowTime']);
                if ($plan['UPrice'] < $koplan_list[$begin_time]['price']) {
                    $koplan_list[$begin_time]['price'] = $plan['UPrice'];
                    $koplan_list[$begin_time]['ShowIndex'] = $plan['ShowIndex'];
                    $koplan_list[$begin_time]['wangCinemaId'] = $plan['CinemaId'];
                    $koplan_list[$begin_time]['wangHallID'] = $plan['HallID'];
                }
            }
        }
    }
    return array_values($koplan_list);
}