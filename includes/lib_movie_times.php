<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/8/29
 * Time: 10:24
 */
/**
 * 判断是否为次卡
 * @return bool
 */
function is_times_card(){
    if(preg_match('/^711001/',$_SESSION['user_name'])){
        return true;
    }
    return false;
}

/**
 * 获取次数电影新地址
 * @param $nav_list
 * @return mixed
 */
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
 * 获取次数
 * @param $card_money   剩余点数
 * @param int $once
 * @return float
 */
function get_times($card_money,$once=50){
    $card_money = floor($card_money/$once);
    return $card_money;
}