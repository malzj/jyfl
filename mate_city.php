<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/9/1
 * Time: 10:06
 */
define('IN_ECS', true);

include_once(dirname(__FILE__) . '/includes/_init.php');
require(ROOT_PATH . 'includes/lib_wpwMovieClass.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');

$wpwMovie = new wpwMovie();

$result = $wpwMovie ->baseCity();

$wpw_city = array();
foreach($result['Data'] as $key => $city){
    $wpw_city[$city['ID']]=trim($city['Name']);
}
$sql = "SELECT region_id,region_name FROM ".$ecs -> table('region')." WHERE parent_id = 0";
$arr_result = $db -> getAll($sql);
$local_city = array();
foreach($arr_result as $k => $kcity){
    $local_city[$kcity['region_id']]=trim($kcity['region_name']);
}
//网票网与本地取差集
$wpw_diff = array_diff($wpw_city,$local_city);
//本地与网票网取差集
$local_diff = array_diff($local_city,$wpw_city);
//网票网与本地取交集
$wpw_intersect = array_intersect($wpw_city,$local_city);
//本地与网票网取交集
$local_intersect = array_intersect($local_city,$wpw_city);
$local_intersect_flip = array_flip($local_intersect);
$up_sql = "UPDATE ".$ecs -> table('region')." SET wangpiaowang_id = CASE region_id ";
//wpw_id 对应 local_id
$wpw_local_id = array();
foreach ($wpw_intersect as $wcid => $wcity){
    $wpw_local_id[$wcid] = $local_intersect_flip[$wcity];
    $up_sql.= sprintf("WHEN %d THEN %d ",$local_intersect_flip[$wcity],$wcid);
}

//将城市截取为两个汉字
foreach($wpw_diff as $id => $c){
    $diff_array[$id] = mb_substr($c,0,2,'utf-8');
}
foreach($local_diff as $kid => $kc){
    $d_array[$kid] = mb_substr($kc,0,2,'utf-8');
}
//网票网与本地取交集
$wd_intersect = array_intersect($diff_array,$d_array);
//本地与网票网取交集
$ld_intersect = array_intersect($d_array,$diff_array);
$ld_intersect_flip = array_flip($ld_intersect);
foreach ($wd_intersect as $wid => $wct){
    $wpw_local_id[$wid] = $ld_intersect_flip[$wct];
    $up_sql.= sprintf("WHEN %d THEN %d ",$ld_intersect_flip[$wct],$wid);
}

$up_sql.="END WHERE region_id IN(".implode(',',array_values($wpw_local_id)).")";

$resuslt = $db -> query($up_sql);
var_dump($resuslt);
//echo "<pre>";
//echo '</br>总数：'.count($wpw_local_id);
//
//print_r($wpw_local_id);
//echo $up_sql;
//echo "</pre>";
//
//echo '</br>网票网本地共有数据：'.count($local_intersect);
//echo '</br>网票网城市总数：'.count($result['Data']).'</br>';
//echo '本地城市总数：'.count($arr_result).'</br>';
//echo "网票网有，本地没有城市：".count($diff_array)."</br>";
//echo "<pre>";
//print_r($wpw_diff);
//echo "</pre>";
//echo "本地有，网票网没有城市：".count($d_array)."</br>";
//echo "<pre>";
//print_r($local_diff);
//echo "</pre>";
//echo "差集共有：".count($wd_intersect)."</br>";
//echo "<pre>";
//print_r($wd_intersect);
//echo "</pre>";