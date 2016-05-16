<?php 

define('IN_ECS', true);
require(dirname(__FILE__) . '../../../includes/init.php');

require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";

// 支付日志id
$log_id = intval($_SESSION['WX_log_id']);
//
if (empty($log_id))
{
	show_wap_message('无效的操作！','进入用户中心', '/mobile/user.php');
} 

// 得到用户支付记录
$pay_log = $GLOBALS['db']->getOne("SELECT order_id FROM " . $GLOBALS['ecs']->table('pay_log') ." WHERE log_id = '$log_id'");
$account = $GLOBALS['db']->getRow('SELECT * FROM ' . $GLOBALS['ecs']->table('user_account') .  " WHERE `id` = '$pay_log' LIMIT 1");

if ($account['is_paid'] == 1)
{
	show_wap_message('已经支付了，请不要重复支付！', '进入用户中心', '/mobile/user.php');
}

// 支付费用
$amount = $account['amount']*100;

// 支付金额为0
if ($amount <= 0)
{
	show_wap_message('无效的操作！','进入用户中心', '/mobile/user.php');
}

// 微信支付操作88888****************************************************** //

//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody("卡充值");
$input->SetAttach("卡充值");
$input->SetOut_trade_no($log_id);
$input->SetTotal_fee($amount);
$input->SetTime_start(local_date("YmdHis"));
$input->SetTime_expire(local_date("YmdHis", local_gettime() + 600));
$input->SetGoods_tag("卡充值");
$input->SetNotify_url("http://huayingcul.com/mobile/weixinpay/example/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
$jsApiParameters = $tools->GetJsApiParameters($order);

// 微信支付操作 ********************************************************** //

$smarty->assign('params', $jsApiParameters);
$smarty->assign('account', $account);
$smarty->display('wx_pay.html');