<?php
/**
 * 微信支付
 * 1、将支付日志log_id，放到session中，
 * 2、跳转到微信支付的时候，把 log_id 作为商户订单号，一并发送给微信支付接口，
 * 3、待支付成功后，通过商户订单号（log_id）来完成后续操作。
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/weixin.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'weixin_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = 'guoyunpeng';

    /* 网址 */
    $modules[$i]['website'] = '#';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';
    

    return;
}

/**
 * 类
 */
class weixin
{   

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     * @param 	int		$iswap		来源（1是wap 0是pc）
     */
    function get_code($order, $payment, $iswap=0)
    {
        if (!defined('EC_CHARSET'))
        {
            $charset = 'utf-8';
        }
        else
        {
            $charset = EC_CHARSET;
        }
        
		$_SESSION['WX_log_id'] = $order['log_id'];
        return '/mobile/weixinpay/example/jsapi.php';
    }    
}

?>