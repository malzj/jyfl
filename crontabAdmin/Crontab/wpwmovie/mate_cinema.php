<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/9/18
 * Time: 16:42
 */
define('IN_ECS', true);

include_once(dirname(__FILE__) . '/lib/_init.php');
require(ROOT_PATH . 'includes/lib_wpwMovieClass.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');

$city_id = 2; //网票网城市ID（上海）
$wpwMovie = new wpwMovie();

//$result = $wpwMovie -> baseCinemaQuery($city_id, $date,$film_id);
//$result = $wpwMovie ->baseFilmShow($cinema_id,$date,$film_id);
//$result = $wpwMovie ->baseSellSeat($show_index,$cinema_id);
//$result = $wpwMovie ->baseHallSeat($hall_id,$cinema_id);
//$result = $wpwMovie ->baseFilm($city_id,'',$cinema_id);
//$result = $wpwMovie ->baseFilmIM();
//$result = $wpwMovie ->baseFilmHE(26081);
//$result = $wpwMovie ->baseFilmPhotos($film_id);
//$result = $wpwMovie ->baseFilmView();
//$result = $wpwMovie ->baseFilmShowCheck($cinema_id,$date,$show_index);
//$result = $wpwMovie ->baseCity();
$result = $wpwMovie -> getCinemas();//获取合作影院
//$result = $wpwMovie -> baseDistrict();
$dist_list = $wpwMovie -> baseDistrict();
$dist_array = array();
foreach($dist_list['Data'] as $dist){
    $dist_array[$dist['ID']]=$dist['Name'];
}
$cinemaList = array();
$insert_str = '';
foreach($result['Data'] as $cinema){
    if($city_id == $cinema['CityID']){
//        echo $cinema['BusLine'].'</br>';
        $cinemaList[$cinema['ID']] = $cinema;
//        $sql = "INSERT INTO ".$ecs->table('wang_cinema_list')." (`cinema_name`,`cinema_address`,`is_wangmovie`,`wang_cinema_id`,`wangmovie_area_id`,`area_name`)
//            VALUE('".$cinema['Name']."','".$cinema['Address']."','1','".$cinema['ID']."','".$cinema['DistID']."','".$dist_array[$cinema['DistID']]."')";
//        $db -> query($sql);
    }
}
unset($result);
//获取本地只属于网票网的影院
$cinemas = $db -> getAll("SELECT wang_cinema_id,logo FROM ".$ecs->table('mate_cinema')." WHERE is_wangmovie = 1");
foreach($cinemas as $val){
    if(!empty($cinemaList[$val['wang_cinema_id']])&&empty($cinemaList[$val['logo']])){
        $db -> query("UPDATE ".$ecs->table('mate_cinema')." SET cinema_tel = '".$cinemaList[$val['wang_cinema_id']]['Tel']."',drive_path ='".$cinemaList[$val['wang_cinema_id']]['BusLine']."',logo = '".$cinemaList[$val['wang_cinema_id']]['Photo']."' WHERE wang_cinema_id = ".$val['wang_cinema_id']);
    }
}
