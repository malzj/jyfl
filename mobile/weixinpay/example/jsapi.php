<?php 

define('IN_ECS', true);
require(dirname(__FILE__) . '../../../includes/weixin_init.php');
include_once(dirname(__FILE__) . '../../../../includes/lib_clips.php');

require_once "../lib/WxPay.Api.php";
require_once "WxPay.JsApiPay.php";

$order_id = $_SESSION['weixinpay']['order_id'];
// 订单id不能为空
if (empty($order_id))
{
    show_wap_message('无效的操作！','从新选择电影', '/mobileh5/movie_times/movie.html');
}

$order = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('seats_order') ." WHERE id = '$order_id'");
// 订单信息不能为空
if (empty($order))
{
    show_wap_message('无效的操作！','从新选择电影', '/mobileh5/movie_times/movie.html');
}

// 要补的差价不能为空
if( empty($order['diff_price']) )
{
    show_wap_message('无效的操作!','从新选择电影', '/mobileh5/movie_times/movie.html');
}

// 需要补的差价
$diffPrice = $order['diff_price'] * $order['count'];

$log_id = insert_pay_log($order_id, $diffPrice, $type=PAY_MOVIE_ALIPAY, 0);

// 支付费用
$amount = $diffPrice * 100;

// 微信支付操作88888****************************************************** //

//①、获取用户openid
$tools = new JsApiPay();
$openId = $tools->GetOpenid();

//②、统一下单
$input = new WxPayUnifiedOrder();
$input->SetBody("在线选座补差价");
$input->SetAttach("在线选座补差价");
$input->SetOut_trade_no($order_id);
$input->SetTotal_fee($amount);
$input->SetTime_start(local_date("YmdHis"));
$input->SetTime_expire(local_date("YmdHis", local_gettime() + 600));
$input->SetGoods_tag("在线选座补差价");
$input->SetNotify_url("http://www.juyoufuli.com/mobile/weixinpay/example/notify.php");
$input->SetTrade_type("JSAPI");
$input->SetOpenid($openId);
$order = WxPayApi::unifiedOrder($input);
$jsApiParameters = $tools->GetJsApiParameters($order);

// 微信支付操作 ********************************************************** //

$smarty->assign('params', $jsApiParameters);
$smarty->display('wx_pay.html');