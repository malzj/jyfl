<?php

/**
 * ECSHOP 分类聚合页
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: index.php 15013 2010-03-25 09:31:42Z liuhui $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

assign_template();

if ((DEBUG_MODE & 2) != 2)
{
	$smarty->caching = true;
}

$navigator = get_navigator();
$middle = array_reverse($navigator['middle']);
init_wap_middle($middle);
$smarty->assign('middle',$middle);

$smarty->assign('header', get_header('全部分类',true,true));
$smarty->assign('get_fixed' ,get_fixed(2));
$smarty->display("cat_all.html");

?>
