<?php

define('IN_ECS', true);

define('SHOW_TYPE', 'ALL');

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_huanlegu.php');

if ((DEBUG_MODE & 2) != 2)
{
	$smarty->caching = false;
}

assign_template();

// 景区id
$sceneryId = isset($_REQUEST['sceneryid']) ? $_REQUEST['sceneryid'] : 0;
if (empty($sceneryId))
{
    show_message('没有选择景区');
}

// 景区产品
$sceneryGoods = sceneryGoods($sceneryId);

$smarty->assign('goods', $sceneryGoods);

$position = assign_ur_here(0,       '生活服务 <code>&gt;</code> 欢乐谷');
$smarty->assign('page_title',       '欢乐谷_生活服务_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置


$smarty->display('huanlegu_goods.dwt');



 









