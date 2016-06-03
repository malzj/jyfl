<?php
/**
 * 优品生活
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
    $childNav = getCinemaCate(40);    
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
        $list[$key]['ad_list'][1] = attrAd($value['name'],1,29);
        $list[$key]['ad_list'][2] = attrAd($value['name'],2,29);
        $list[$key]['ad_list'][3] = attrAd($value['name'],3,29);
        $list[$key]['ad_list'][4] = attrAd($value['name'],4,29);
        $list[$key]['ad_list'][5] = attrAd($value['name'],5,29);
        $list[$key]['ad_list'][6] = attrAd($value['name'],6,29);
        $list[$key]['ad_list'][7] = attrAd($value['name'],7,29);
        $floor++;
    }
    $smarty->assign('list', $list);
    $smarty->assign('banner', getNavadvs(30));
    $smarty->assign('text', getNavadvs(31));
    $smarty->display('ylife/ylifeIndex.dwt');
    
}