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
set_time_limit(0);

$city_id = 2; //网票网城市ID（上海）
$int_areaNo = 53;//抠电影城市ID（上海）

//获取本地当前城市id
$region_id = $db->getOne("SELECT region_id FROM ".$ecs->table('region')." WHERE komovie_id =".$int_areaNo." AND wangpiaowang_id = ".$city_id." LIMIT 1");

$wpwMovie = new wpwMovie();

$result = $wpwMovie ->baseFilm($city_id);

$wpw_movie = array();
foreach($result['Data'] as $key => $movie){
    $wpw_movie[$movie['ID']] = $movie['Name'];
}
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

//获取本地电影列表
$local_movies = $db -> getAll("SELECT * FROM ".$ecs->table('mate_movie')." WHERE region_id = ".$region_id);
$local_id_movie = array();
$local_komovieids = array();
foreach($local_movies as $lmovie){
    $local_id_movie[$lmovie['movieId']] = $lmovie['movieName'];
    //本地抠电影id
    $local_komovieids[] = $lmovie['komovie_id'];
}

//找出本地当前城市与抠电影不同的电影,并删除
$local_diff = array_diff($local_id_movie,$ko_movie);
$local_diff_id = array_keys($local_diff);
if(!empty($local_diff_id)) {
    $a = $db->query("DELETE FROM " . $ecs->table('mate_movie') . " WHERE movieId IN(" . implode(',', $local_diff_id) . ") AND region_id = " . $region_id);
}

//echo '<pre>';
//print_r($local_diff_id);
//echo '</pre>';
//exit;

$ko_wpw = array();
foreach ($ko_movie as $movie_id => $ko_m) {
    $publish_time = $ko_movie_array[$movie_id]['publishTime'];
    $wpw_movie_id = isset($wpw_movie_flip[$ko_m])?$wpw_movie_flip[$ko_m]:0;
    $data = array(
        'region_id' => $region_id,
        'actor' => isset($ko_movie_array[$movie_id]['actor'])?$ko_movie_array[$movie_id]['actor']:'',
        'country' => isset($ko_movie_array[$movie_id]['country'])?$ko_movie_array[$movie_id]['country']:'',
        'director' => $ko_movie_array[$movie_id]['director'],
        'has2D' => isset($ko_movie_array[$movie_id]['has2D'])?$ko_movie_array[$movie_id]['has2D']:'',
        'has3D' => isset($ko_movie_array[$movie_id]['has3D'])?$ko_movie_array[$movie_id]['has3D']:'',
        'hot' => $ko_movie_array[$movie_id]['hot'],
        'minPrice' => $ko_movie_array[$movie_id]['minPrice'],
        'komovie_id' => $ko_movie_array[$movie_id]['movieId'],
        'wangmovie_id' => $wpw_movie_id,
        'movieLength' => isset($ko_movie_array[$movie_id]['movieLength'])?$ko_movie_array[$movie_id]['movieLength']:'',
        'movieName' => $ko_movie_array[$movie_id]['movieName'],
        'movieType' => isset($ko_movie_array[$movie_id]['movieType'])?$ko_movie_array[$movie_id]['movieType']:'',
        'pathHorizonH' => isset($ko_movie_array[$movie_id]['pathHorizonH'])?$ko_movie_array[$movie_id]['pathHorizonH']:'',
        'pathSquare' => isset($ko_movie_array[$movie_id]['pathSquare'])?$ko_movie_array[$movie_id]['pathSquare']:'',
        'pathVerticalS' => $ko_movie_array[$movie_id]['pathVerticalS'],
        'publishTime' => $ko_movie_array[$movie_id]['publishTime'],
        'score' => $ko_movie_array[$movie_id]['score'],
        'update_time' => date('Y-m-d H:i:s',gmtime())
    );
    if(in_array($movie_id,$local_komovieids)){
        $set_str = '';
        foreach ($data as $k => $val){
            if(empty($val)) continue;
            if(empty($set_str)){
                $set_str = "SET ".$k."='".$val."'";
            }else{
                $set_str .= ",".$k."='".$val."'";
            }
        }
        $db -> query("UPDATE ".$ecs->table('mate_movie')." ".$set_str." WHERE komovie_id = ".$movie_id);
    }else{
        $key_array = array_keys($data);
        $db -> query("INSERT INTO ".$ecs -> table('mate_movie')." (`".implode("`,`",$key_array)."`) VALUE('".implode("','",$data)."')");
    }
}


//循环抠电影影院列表，对应各影院上映的电影
//查询影院列表
$sql = "SELECT * FROM ".$ecs->table('mate_cinema')." WHERE region_id = ".$region_id;
$allCinema = $db->getAll($sql);
//查询电影列表
$sql = "SELECT * FROM ".$ecs->table('mate_movie')." WHERE region_id = ".$region_id;
$allFilm = $db->getAll($sql);
$film_kid_lid = array();
$film_wid_lid = array();
foreach($allFilm as $key => $film){
    $film_kid_lid[$film['komovie_id']] = $film['movieId'];
    if(!empty($film['wangmovie_id']))
        $film_wid_lid[$film['wangmovie_id']] = $film['movieId'];
}

$i=0;
foreach($allCinema as $key => $cinema){
    //查询本地数据库中影院有排期的电影
    $show_movies = $db -> getAll("SELECT mc.movie_id,mm.komovie_id,mm.wangmovie_id,mc.komovie_show,mc.wangmovie_show FROM " . $ecs->table('movie_cinema') . " mc LEFT JOIN ".$ecs->table('mate_movie')." mm ON mc.movie_id = mm.movieId  WHERE mc.cinema_id = ".$cinema['id']);
    $show_array = array();
    $show_movieids = array();
    $show_komovieids = array();
    $show_wangmovieids = array();
    foreach($show_movies as $show_movie){
        $show_array[$show_movie['movie_id']] = $show_movie;
        $show_movieids[] = $show_movie['movie_id'];
        $show_komovieids[$show_movie['movie_id']] = $show_movie['komovie_id'];
        $show_wangmovieids[$show_movie['movie_id']] = $show_movie['wangmovie_id'];
    }
    //抠电影上映电影对应
    if(!empty($cinema['komovie_cinema_id'])) {
        $param = array(
            'action' => 'movie_Query', 'cinema_id' => $cinema['komovie_cinema_id']
        );
        $movie_list = getCDYApi($param);

        //判断请求抠电影接口返回数据
        if (empty($movie_list['error']) && is_array($movie_list['movies']) && !empty($movie_list['movies'])) {
            $komovieids = array();
            foreach ($movie_list['movies'] as $mo) {
                $komovieids[] = $mo['movieId'];
            }
            //对比找出该影院下映的电影
            $show_movieids_diff = array_diff($show_komovieids, $komovieids);
            //        修改状态为下映
            foreach ($show_movieids_diff as $key => $val) {
                $db->query("UPDATE " . $ecs->table('movie_cinema') . " SET komovie_show = 0 WHERE movie_id = " . $key . " AND cinema_id = " . $cinema['id']);
            }
            foreach ($movie_list['movies'] as $k => $movie) {
                //判断本地上映电影表中有无该电影
                if (isset($film_kid_lid[$movie['movieId']])) {
                    //如果该电影存在，跳过循环
                    if (in_array($film_kid_lid[$movie['movieId']], $show_movieids)){
                        //如果上映字段为0，修改为1
                        if(!empty($show_array)&&$show_array[$film_kid_lid[$movie['movieId']]]['komovie_show'] == 0){
                            $db->query("UPDATE " . $ecs->table('movie_cinema') . " SET komovie_show = 1 WHERE movie_id = " . $film_kid_lid[$movie['movieId']] . " AND cinema_id = " . $cinema['id']);
                        }
                        continue;
                    }
                } else {
                    continue;
                }
                $sql = "INSERT INTO " . $ecs->table('movie_cinema') . " (`movie_id`,`cinema_id`,`komovie_show`) VALUE('" . (int)$film_kid_lid[$movie['movieId']] . "','" . (int)$cinema['id'] . "','1')";
                $db->query($sql);
            }
        }
    }

    //查询本地数据库中影院有排期的电影
    $show_movies = $db -> getAll("SELECT mc.movie_id,mm.komovie_id,mm.wangmovie_id,mc.komovie_show,mc.wangmovie_show FROM " . $ecs->table('movie_cinema') . " mc LEFT JOIN ".$ecs->table('mate_movie')." mm ON mc.movie_id = mm.movieId  WHERE mc.cinema_id = ".$cinema['id']);
    $show_array = array();
    $show_movieids = array();
    $show_komovieids = array();
    $show_wangmovieids = array();
    foreach($show_movies as $show_movie){
        $show_array[$show_movie['movie_id']] = $show_movie;
        $show_movieids[] = $show_movie['movie_id'];
        $show_komovieids[$show_movie['movie_id']] = $show_movie['komovie_id'];
        $show_wangmovieids[$show_movie['movie_id']] = $show_movie['wangmovie_id'];
    }
    //网票网上映电影对应
    if(!empty($cinema['wang_cinema_id'])){
        $res_movie = $wpwMovie ->baseFilm($city_id,'',$cinema['wang_cinema_id']);
        if(empty($res_movie['ErrNo'])&&is_array($res_movie['Data'])&&!empty($res_movie['Data'])){
            $wangmovieids = array();
            foreach ($res_movie['Data'] as $mo){
                $wangmovieids[] = $mo['ID'];
            }
            //对比找出该影院下映的电影
            $show_movieids_diff = array_diff($show_wangmovieids,$wangmovieids);
            //        修改状态为下映
            foreach ($show_movieids_diff as $key=>$val){
                $db -> query("UPDATE ".$ecs->table('movie_cinema')." SET wangmovie_show = 0 WHERE movie_id = ".$key." AND cinema_id = ".$cinema['id']);
            }
            foreach($res_movie['Data'] as $key => $wmovie){
                //判断本地上映电影表中有无该电影
                if (isset($film_wid_lid[$wmovie['ID']])) {
                    //如果该电影存在，跳过循环
                    if(in_array($film_wid_lid[$wmovie['ID']],$show_movieids)) {
                        //如果上映字段为0，修改为1
                        if (!empty($show_array)&&$show_array[$film_wid_lid[$wmovie['ID']]]['wangmovie_show'] == 0) {
                            $db->query("UPDATE " . $ecs->table('movie_cinema') . " SET wangmovie_show = 1 WHERE movie_id = " . $film_wid_lid[$wmovie['ID']] . " AND cinema_id = " . $cinema['id']);
                        }
                        continue;
                    }
                }else{
                    continue;
                }
                $sql = "INSERT INTO " . $ecs->table('movie_cinema') . " (`movie_id`,`cinema_id`,`wangmovie_show`) VALUE('".(int)$film_wid_lid[$wmovie['ID']]."','".(int)$cinema['id']."','1')";
                $db->query($sql);
            }
        }
    }
    $i++;
    if ($i == 1000) {
        sleep(2);
        $i = 0;
    }
}

//再次查询本地影院对应电影
$movie_cinema = $db -> getAll("SELECT * FROM ".$ecs -> table('movie_cinema'));
foreach($movie_cinema as $value){
    if($value['komovie_show'] == 1 || $value['wangmovie_show'] == 1){
        $db->query("UPDATE " . $ecs->table('movie_cinema') . " SET is_show = 1 WHERE movie_id = " . $value['movie_id'] . " AND cinema_id = " . $value['cinema_id']);
    }else{
        $db->query("UPDATE " . $ecs->table('movie_cinema') . " SET is_show = 0 WHERE movie_id = " . $value['movie_id'] . " AND cinema_id = " . $value['cinema_id']);
    }
}