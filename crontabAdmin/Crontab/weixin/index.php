<?php
/**
 * 微信支付成功后，对卡进行充值的操作
 * 
 * 计划任务：1分钟执行一次
 * 
 * 1、 查询 ecs_weixin_pay 中所有的记录。
 * 2、通过log_id找到对应的充值日志，得到卡信息。
 * 3、调用接口进行充值操作。
 * 4、修改支付状态。
 * 5、修改用户充值状态。
 * 
 * @var unknown_type
 */
define('IN_ECS', true);

include_once(dirname(__FILE__) . '/lib/_init.php');
include_once(dirname(__FILE__) . '/lib/function.php');

include_once(ROOT_PATH . 'includes/lib_payment.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
error_reporting(E_ERROR);
set_time_limit(0);

$all = findData( 'weixin_pay' );
if ( $all )
{
	foreach ($all as $row)
	{
		// 修改数据库数据
		order_paid($row['log_id'], '', '', $row['transaction_id']);
		// 删除已操作的记录
		dropWeixinpay($row['id']);
		// 得到卡号
		$pay_log = $GLOBALS['db']->getOne("SELECT order_id FROM " . $GLOBALS['ecs']->table('pay_log') ." WHERE log_id = '$row[log_id]'");
		$user_id = $GLOBALS['db']->getOne('SELECT user_id FROM ' . $GLOBALS['ecs']->table('user_account') .  " WHERE `id` = '$pay_log' LIMIT 1");
		$user_name = $GLOBALS['db']->getOne('SELECT user_name FROM '.$GLOBALS['ecs']->table('users'). " WHERE user_id = ".$user_id);
	}
}



