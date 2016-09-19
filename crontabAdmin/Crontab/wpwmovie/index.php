<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/9/1
 * Time: 10:06
 */
define('IN_ECS', true);

include_once(dirname(__FILE__) . '/lib/_init.php');
require(ROOT_PATH . 'includes/lib_wpwMovieClass.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');

$city_id = 2;
$cinema_id = 2007;
//$cinema_id = 2143;
$hall_id = 20408;
$film_id = 26195;
$show_index = 62372501;
$date = date("Y-m-d H:i:s",time());
$wpwMovie = new wpwMovie();

//$result = $wpwMovie -> baseCinemaQuery($city_id, $date,$film_id);
//$result = $wpwMovie ->baseFilmShow($cinema_id,$date,$film_id);
//$result = $wpwMovie ->baseSellSeat($show_index,$cinema_id);
//$result = $wpwMovie ->baseHallSeat($hall_id,$cinema_id);
$result = $wpwMovie ->baseFilm($city_id);
//$result = $wpwMovie ->baseFilmIM();
//$result = $wpwMovie ->baseFilmHE(26081);
//$result = $wpwMovie ->baseFilmPhotos($film_id);
//$result = $wpwMovie ->baseFilmView();
//$result = $wpwMovie ->baseFilmShowCheck($cinema_id,$date,$show_index);
//$result = $wpwMovie ->baseCity();


$wpw_movie = array();
foreach($result['Data'] as $key => $movie){
    $wpw_movie[$movie['ID']] = $movie['Name'];
}
$int_areaNo = 53;
$arr_param = array('action'=>'movie_Query','city_id'=>$int_areaNo);
$arr_result = getCDYApi($arr_param);
$ko_movie = array();
//影院所有信息
$ko_movie_array = array();
foreach($arr_result['movies'] as $k => $kmovie){
    $ko_movie[$kmovie['movieId']] = $kmovie['movieName'];
    $ko_movie_array[$kmovie['movieId']] = $kmovie;
}

$wpw_movie_flip = array_flip($wpw_movie);
//$ko_movie_diff = array_diff($ko_movie,$wpw_movie);
//$wpw_movie_diff = array_diff($wpw_movie,$ko_movie);
//$ko_inter = array_intersect($ko_movie,$wpw_movie);

//获取本地当前城市id
$region_id = $db->getOne("SELECT region_id FROM ".$ecs->table('region')." WHERE komovie_id =".$int_areaNo." AND wangpiaowang_id = ".$city_id." LIMIT 1");
//获取本地电影列表
$local_movies = $db -> getAll("SELECT * FROM ".$ecs->table('mate_movie')." WHERE region_id = ".$region_id);
$local_id_movie = array();
foreach($local_movies as $lmovie){
    $local_id_movie[$lmovie['id']] = $lmovie['movieName'];
}

//找出本地当前城市与抠电影不同的电影,并删除
$local_diff = array_diff($local_id_movie,$ko_movie);
$local_diff_id = array_keys($local_diff);
if(!empty($local_diff_id)) {
    $a = $db->query("DELETE FROM " . $ecs->table('mate_movie') . " WHERE id IN(" . implode(',', $local_diff_id) . ") AND region_id = " . $region_id);
}

//echo '<pre>';
//print_r($local_diff_id);
//echo '</pre>';
//exit;

$ko_wpw = array();
//开始事物
$db -> query('start transaction');
//创建存储过程
try {
    $sql = "
CREATE PROCEDURE update_mate_movie(in reg_id int,in ko_movie_id int,in wpw_movie_id int,in movie_name varchar(45),in pic_h varchar(225),in pic_s varchar(225),in publish_tm varchar(20),in m_score varchar(20),in update_tm int)
BEGIN
if(SELECT id FROM `juyoufuli`.`ecs_mate_movie` WHERE komovie_id = ko_movie_id) then
    UPDATE `juyoufuli`.`ecs_mate_movie` SET region_id =reg_id, wangmovie_id = wpw_movie_id,movieName = movie_name,pathHorizonH = pic_h,pathVerticalS = pic_s,publishTime = publish_tm,score = m_score,update_time = update_tm WHERE komovie_id = ko_movie_id;
else
    INSERT INTO `juyoufuli`.`ecs_mate_movie` (`region_id`,`komovie_id`,`wangmovie_id`,`movieName`,`pathHorizonH`,`pathVerticalS`,`publishTime`,`score`,`update_time`) VALUE(reg_id,ko_movie_id,wpw_movie_id,movie_name,pic_h,pic_s,publish_tm,m_score,update_tm);
end if;
END;
";

    $db->query($sql);

//$sql = "INSERT INTO ".$ecs -> table('mate_movie')." (`komovie_id`,`wangmovie_id`) VALUE";
    foreach ($ko_movie as $movie_id => $ko_m) {
//    $sql .= "('$movie_id','$wpw_movie_flip[$ko_m]'),";
        $update_time = time();
        $publish_time = $ko_movie_array[$movie_id]['publishTime'];
        $wpw_movie_id = isset($wpw_movie_flip[$ko_m])?$wpw_movie_flip[$ko_m]:0;
        $db->query("call update_mate_movie('".$region_id."','".$movie_id."','".$wpw_movie_id."','".$ko_movie_array[$movie_id]['movieName']."','".$ko_movie_array[$movie_id]['pathHorizonH']."','".$ko_movie_array[$movie_id]['pathVerticalS']."','".$publish_time."',".$ko_movie_array[$movie_id]['score'].",".$update_time.");");
//        $ko_wpw[$movie_id] = $wpw_movie_flip[$ko_m];
    }

    $db->query("drop procedure update_mate_movie");
    $db->query('commit');
}
catch(Exception $e){
    print $e->getMessage();
    $db->query('rollback');
    exit;
}

//循环抠电影影院列表，对应各影院上映的电影
//查询影院列表
//$sql = "SELECT * FROM ".$ecs->table('mate_cinema');
//$allCinema = $db->getAll($sql);
////查询电影列表
//$sql = "SELECT * FROM ".$ecs->table('mate_movie');
//$allFilm = $db->getAll($sql);
//$film_array = array();
//foreach($allFilm as $key => $film){
//    $film_array[$film['komovie_id']] = $film['id'];
//}
//
//$i=0;
//foreach($allCinema as $key => $cinema){
//    $param = array(
//        'action'=>'movie_Query', 'cinema_id'=> $cinema['kocinema_id']
//    );
//    $movie_list = getCDYApi($param);
//    if(is_array($movie_list['movies'])&&!empty($movie_list['movies'])){
//        foreach ($movie_list['movies'] as $k => $movie){
//            $sql = "INSERT INTO ".$ecs->table('movie_cinema')." (`movie_id`,`cinema_id`) VALUE(".$film_array[$movie['movieId']].",".$cinema['id'].")";
//            $db -> query($sql);
//        }
//    }
//    $i++;
//    if($i == 10){
//        sleep(2);
//        $i=0;
//    }
//}
//$sql = substr($sql,0,-1);
//$result = $db -> query($sql);
echo '<pre>';
echo $result;
print_r($ko_wpw);
print_r($result);
print_r($arr_result);
echo '</pre>';