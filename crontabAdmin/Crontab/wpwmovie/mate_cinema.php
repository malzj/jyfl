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

$city_id = 1;
//$cinema_id = 2007;
$cinema_id = 2143;
$hall_id = 20408;
$film_id = 26195;
$show_index = 62372501;
$date = date("Y-m-d H:i:s",time());
$wpwMovie = new wpwMovie();

//$result = $wpwMovie -> baseCinemaQuery($city_id, $date,$film_id);
//$result = $wpwMovie ->baseFilmShow($cinema_id,$date,$film_id);
//$result = $wpwMovie ->baseSellSeat($show_index,$cinema_id);
//$result = $wpwMovie ->baseHallSeat($hall_id,$cinema_id);
//$result = $wpwMovie ->baseFilm($city_id);
//$result = $wpwMovie ->baseFilmIM();
//$result = $wpwMovie ->baseFilmHE(26081);
//$result = $wpwMovie ->baseFilmPhotos($film_id);
//$result = $wpwMovie ->baseFilmView();
//$result = $wpwMovie ->baseFilmShowCheck($cinema_id,$date,$show_index);
//$result = $wpwMovie ->baseCity();
//$result = $wpwMovie -> getCinemas();//获取合作影院
$result = $wpwMovie -> baseDistrict();

echo '<pre>';
print_r($result);
echo '</pre>';