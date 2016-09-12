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
$hall_id = 20408;
$film_id = 26195;
$show_index = 62372501;
$date = date("Y-m-d H:i:s",time());
$wpwMovie = new wpwMovie();

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
$ko_movie_diff = array_diff($ko_movie,$wpw_movie);
$wpw_movie_diff = array_diff($wpw_movie,$ko_movie);
$ko_inter = array_intersect($ko_movie,$wpw_movie);
$ko_wpw = array();
//开始事物
$db -> query('start transaction');
//创建存储过程
try {
    $sql = "
CREATE PROCEDURE update_mate_movie(in ko_movie_id int,in wpw_movie_id int,in publish_tm int,in update_tm int)
BEGIN
if(SELECT id FROM `juyoufuli`.`ecs_mate_movie` WHERE komovie_id = ko_movie_id) then
    UPDATE `juyoufuli`.`ecs_mate_movie` SET wangmovie_id = wpw_movie_id,publish_time = publish_tm,update_time = update_tm WHERE komovie_id = ko_movie_id;
else
    INSERT INTO `juyoufuli`.`ecs_mate_movie` (`komovie_id`,`wangmovie_id`,`publish_time`,`update_time`) VALUE(ko_movie_id,wpw_movie_id,publish_tm,update_tm);
end if;
END;
";

    $db->query($sql);

//$sql = "INSERT INTO ".$ecs -> table('mate_movie')." (`komovie_id`,`wangmovie_id`) VALUE";
    foreach ($ko_inter as $movie_id => $ko_m) {
//    $sql .= "('$movie_id','$wpw_movie_flip[$ko_m]'),";
        $update_time = time();
        $publish_time = strtotime($ko_movie_array[$movie_id]['publishTime']);
        $db->query("call update_mate_movie($movie_id,$wpw_movie_flip[$ko_m],$publish_time,$update_time);");
        $ko_wpw[$movie_id] = $wpw_movie_flip[$ko_m];
    }

    $db->query("drop procedure update_mate_movie");
    $db->query('commit');
}
catch(Exception $e){
    print $e->getMessage();
    $db->query('rollback');
    exit;
}
$sql = "SELECT * FROM ".$ecs->table('mate_cinema');
$allCinema = $db->getAll($sql);
$i=0;
foreach($allCinema as $key => $cinema){
    $param = array(
        'action'=>'movie_Query', 'cinema_id'=> $cinema['kocinema_id']
    );
    $movie_list = getCDYApi($param);
    if(is_array($movie_list['movies'])&&!empty($movie_list['movies'])){
        foreach ($movie_list['movies'] as $k => $movie){
            
        }
    }
    $i++;
    if($i == 10){
        sleep(2);
        $i=0;
    }
}
//$sql = substr($sql,0,-1);
//$result = $db -> query($sql);
echo '<pre>';
echo $result;
print_r($ko_wpw);
print_r($result);
print_r($arr_result);
echo '</pre>';