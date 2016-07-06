<?php

/**
 * 用户身份验证
 * @var unknown
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

if ($_REQUEST['act'] == 'navList')
{
    $navList = get_navigator();
    $jsonArray['data'] = $navList['middle'];
    exit(json_encode($jsonArray));
    exit($_GET['jsoncallback']."(".json_encode($jsonArray).")");
}

