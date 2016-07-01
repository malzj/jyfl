<?php 

/**
 * 城市切换
 * 
 * 
 * 
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
	$smarty->caching = true;
}

assign_template();
$smarty->assign('header', get_header('选择所在城市', false));
$smarty->assign('get_fixed', get_fixed());
$smarty->display("province.html");

?>