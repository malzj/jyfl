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

$wpwMovie = new wpwMovie();

$num = 10000;
for($i=1;$i<=$num;$i++){
    $des = rand(1,10000);
    $time = date('Y-m-d H:i:s',time());
    $result = $wpwMovie->baseWLanCheck($time,$des);
    $data['stime'] = $time;
    $data['des'] = $des;
    $data['result']=$result;
    $db -> autoExecute($ecs->table('wlantest'),$data);
}

