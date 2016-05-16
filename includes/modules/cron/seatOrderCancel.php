<?php

/**
 * ECSHOP 定期删除
 * ===========================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ==========================================================
 * $Author: liubo $
 * $Id: ipdel.php 17217 2011-01-19 06:29:08Z liubo $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
$cron_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/cron/seatOrderCancel.php';

require(ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/admin/common.php');

if (file_exists($cron_lang))
{
    global $_LANG;

    include_once($cron_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'seatOrderCancelDesc';

    /* 作者 */
    $modules[$i]['author']  = 'ECSHOP TEAM';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.ecshop.com';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';

    return;
}

include_once(ROOT_PATH.'admin/includes/lib_main.php');
require(ROOT_PATH . 'includes/lib_order.php');

$deltime = gmtime() - 15 * 60;
//没有付款且没有发货
$sql = "SELECT order_id,order_sn,user_id FROM " . $ecs->table('seat_order') . "WHERE case when confirm_time > '0' then confirm_time else add_time end <= $deltime AND pay_status = 0";
$arr_order = array();
$arr_order = $db->getAll($sql);

foreach ($arr_order as $order){
	/* 删除订单 */
	$db->query("DELETE FROM ".$ecs->table('seat_order'). " WHERE order_id = '$order[order_id]'");
}
?>