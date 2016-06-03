<?php
/**
 * 生活
 * @var unknown_type
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_basic.php');

if (!isset($_REQUEST['act']))
{
	$_REQUEST['act'] = "index";
}

assign_template();

// 生活首页
if($_REQUEST['act'] == "index")
{
    $list = array();

    // 二级分类
    $childNav = getCinemaCate(17);    
    if ( empty($childNav) )
    {
        show_message('没有可显示的内容！');
    }
    
    $floor = 1;
    // 楼层广告
    foreach ($childNav as $key=>$value)
    {
        $list[$key]['url'] = $value['url'];
        $list[$key]['name'] = $value['name'];
        $list[$key]['floor'] = $floor;
        $list[$key]['ad_list'][1] = attrAd($value['name'],1,18);
        $list[$key]['ad_list'][2] = attrAd($value['name'],2,18);
        $list[$key]['ad_list'][3] = attrAd($value['name'],3,18);
        $list[$key]['ad_list'][4] = attrAd($value['name'],4,18);
        $list[$key]['ad_list'][5] = attrAd($value['name'],5,18);
        $list[$key]['ad_list'][6] = attrAd($value['name'],6,18);
        $list[$key]['ad_list'][7] = attrAd($value['name'],7,18);
        $floor++;
    }
    $smarty->assign('list', $list);
    // 当前城市支持的蛋糕品牌（导航信息）
    $smarty->assign('banner', getNavadvs(20));
    $smarty->assign('text', getNavadvs(19));
    $smarty->display('life/lifeIndex.dwt');
    
}