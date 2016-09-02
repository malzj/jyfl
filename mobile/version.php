<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/9/1
 * Time: 10:16
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$jsonArray = array(
    'appid'     =>  'JUYOU0711',
    'iOS'       =>  array(
        'version'   =>  '1.0.2',
        'title'     =>  '版本更新',
        'note'      =>  '修复BUG',
        'url'       =>  'http://www.juyoufuli.com',
    ),
    'Android'   =>  array(
        'version'   =>  '1.0.2',
        'title'     =>  '版本更新',
        'note'      =>  '修复BUG',
        'url'       =>  'http://www.juyoufuli.com',
    ),
);
JsonpEncode($jsonArray);