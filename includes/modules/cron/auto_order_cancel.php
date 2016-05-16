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
$cron_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/cron/auto_order_cancel.php';

require(ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/admin/common.php');
require(ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/admin/log_action.php');

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
    $modules[$i]['desc']    = 'auto_order_cancel_desc';

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

$deltime = gmtime() - 10800;
//没有付款且没有发货
$sql = "SELECT order_id,order_sn,user_id,integral FROM " . $ecs->table('order_info') . "WHERE case when confirm_time > '0' then confirm_time else add_time end <= $deltime AND pay_status = 0 and order_status=0 AND is_cron = 0 AND shipping_status = 0";
$arr_order = array();
$arr_order = $db->getAll($sql);

foreach ($arr_order as $order){

	/* 删除订单 */
	//$db->query("DELETE FROM ".$ecs->table('order_info'). " WHERE order_id = '$order[order_id]'");
	//$db->query("DELETE FROM ".$ecs->table('order_goods'). " WHERE order_id = '$order[order_id]'");
	//$db->query("DELETE FROM ".$ecs->table('order_action'). " WHERE order_id = '$order[order_id]'");
	//$action_array = array('delivery', 'back');
	//del_delivery($order['order_id'], $action_array);

	/* todo 记录日志 */
	//admin_log($order['order_sn'], 'remove', 'order');

	if ($order['integral'] > 0){
		log_account_change($order['user_id'], 0, 0, 0, $order['integral'], '24小时未付款订单 '.$order['order_sn'].'，退回支付订单时使用的积分');
	}

	/* 修改订单 */
	$arr = array(
		'order_status' => OS_INVALID,
		'bonus_id'  => 0,
		'bonus'     => 0,
		'integral'  => 0,
		'integral_money'    => 0,
		'surplus'   => 0,
	);
	/* 标记订单为“无效” */
	update_order($order['order_id'], $arr);
	/* 记录log */
	order_action($order['order_sn'], OS_INVALID, SS_UNSHIPPED, PS_UNPAYED, '订单24小时未付款自动无效');
}

/**
 * 删除订单所有相关单子
 * @param   int     $order_id      订单 id
 * @param   int     $action_array  操作列表 Array('delivery', 'back', ......)
 * @return  int     1，成功；0，失败
 */
function del_delivery($order_id, $action_array){
	$return_res = 0;

	if (empty($order_id) || empty($action_array))
	{
		return $return_res;
	}

	$query_delivery = 1;
	$query_back = 1;
	if (in_array('delivery', $action_array))
	{
		$sql = 'DELETE O, G
				FROM ' . $GLOBALS['ecs']->table('delivery_order') . ' AS O, ' . $GLOBALS['ecs']->table('delivery_goods') . ' AS G
				WHERE O.order_id = \'' . $order_id . '\'
				AND O.delivery_id = G.delivery_id';
		$query_delivery = $GLOBALS['db']->query($sql, 'SILENT');
	}
	if (in_array('back', $action_array))
	{
		$sql = 'DELETE O, G
				FROM ' . $GLOBALS['ecs']->table('back_order') . ' AS O, ' . $GLOBALS['ecs']->table('back_goods') . ' AS G
				WHERE O.order_id = \'' . $order_id . '\'
				AND O.back_id = G.back_id';
		$query_back = $GLOBALS['db']->query($sql, 'SILENT');
	}

	if ($query_delivery && $query_back)
	{
		$return_res = 1;
	}

	return $return_res;
}

?>