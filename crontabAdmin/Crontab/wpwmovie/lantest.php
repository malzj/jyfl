<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/9/9
 * Time: 15:33
 */
define('IN_ECS', true);

include_once(dirname(__FILE__) . '/lib/_init.php');
require(ROOT_PATH . 'includes/lib_wpwMovieClass.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');

//$city_id = 1;
$city_id = 2;
//$cinema_id = 2007;
$cinema_id = 2165;

$hall_id = 20408;
$film_id = 26255;
$show_index = 62372501;
$date = date("Y-m-d H:i:s",time());

//$cinema_id = 2182;
//$cinema_id = 3532;
//$hall_id = 53531;
//$film_id = 26251;
$show_index = '62372501,64226584,64226583';
//$date = date("Y-m-d",time());
$date = '2016-10-05 10:10:10';
$wpwMovie = new wpwMovie();
//echo $date;
//$num = 10000;
//for($i=1;$i<=$num;$i++){
//    $des = rand(1,10000);
//    $time = date('Y-m-d H:i:s',time());
//    $result = $wpwMovie->baseWLanCheck($time,$des);
//    $data['stime'] = $time;
//    $data['des'] = $des;
//    $data['result']=$result;
//    $db -> autoExecute($ecs->table('wlantest'),$data);
//}

//$result = $wpwMovie -> baseCinemaQuery($city_id, $date,$film_id);
//$result = $wpwMovie ->baseFilmShow($cinema_id,$date,$film_id);
//$result = $wpwMovie ->baseSellSeat($show_index,$cinema_id);
//$result = $wpwMovie ->baseHallSeat($hall_id,$cinema_id);
$result = $wpwMovie ->baseFilm($city_id,'',$cinema_id);
//$result = $wpwMovie ->baseFilmShow($cinema_id,$date,$film_id);
//$result = $wpwMovie ->baseFilmIM();
//$result = $wpwMovie ->baseFilmHE(26081);
//$result = $wpwMovie ->baseFilmPhotos($film_id);
//$result = $wpwMovie ->baseFilmView();
//$result = $wpwMovie ->baseFilmShowCheck($cinema_id,$date,$show_index);
//$result = $wpwMovie ->baseCity();
//$result = $wpwMovie ->baseCinemaQuery($city_id,$date);
//$result = $wpwMovie ->baseHallSeat($hall_id,$cinema_id);
//$result = $wpwMovie -> sellSearchOrderInfoBySID('0041443194');
//$result = $wpwMovie -> sellBuyTicket('0041360387','3b29d28c-a7be-4e5b-8fea-e18d03179d1a','160923185117124');
//$param = array(
//    'Mobile' => "18501253681",
//    'SID' => '0041363308'
//);
//$result = $wpwMovie -> doTarget('Sell_SearchMsg',$param);

//$result = $wpwMovie -> sellStopBuyTicket('0041363618');
//$show_movies = $db -> getAll("SELECT movie_id FROM " . $ecs->table('movie_cinema') . " WHERE cinema_id = 8568");
//$show_movieids = array();
//foreach($show_movies as $show_movie){
//    $show_movieids[] = $show_movie['movie_id'];
//}
//$show_movies = $db -> getAll("SELECT mc.movie_id,mm.komovie_id FROM " . $ecs->table('movie_cinema') . " mc LEFT JOIN ".$ecs->table('mate_movie')." mm ON mc.movie_id = mm.movieId WHERE mc.cinema_id = 8568");
//$show_movieids = array();
//$show_komovieids = array();
//foreach($show_movies as $show_movie){
//    $show_movieids[] = $show_movie['movie_id'];
//    $show_komovieids[$show_movie['movie_id']] = $show_movie['komovie_id'];
//}
//$re = array_diff($show_komovieids,array(387734));
//var_dump($show_movies);
//var_dump($result);
//$param = array(
//    'action' => 'cinema_Query', 'city_id' => 53
//);
//$movie_list = getCDYApi($param);
var_dump($result);
?>