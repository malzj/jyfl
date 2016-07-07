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

// 导航信息
if ($_REQUEST['act'] == 'navList')
{
    $navList = get_navigator();
    $jsonArray['data'] = $navList['middle'];
    JsonpEncode($jsonArray); 
}

// 城市切换
elseif ($_REQUEST['act'] == 'city')
{
    JsonpEncode($jsonArray);    
}

// 当前登录的城市id
elseif ($_REQUEST['act'] == 'getCityId')
{
    $jsonArray['data']['cityid'] = $_SESSION['cityid']; 
    JsonpEncode($jsonArray); 
}
// 城市列表
elseif ($_REQUEST['act'] == 'getCityList')
{
    $jsonArray['data'] = getCityList();;
    JsonpEncode($jsonArray); 
}


