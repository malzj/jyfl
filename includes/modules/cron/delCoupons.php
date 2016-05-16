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
$cron_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/cron/delCoupons.php';

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
    $modules[$i]['desc']    = 'delCoupons';

    /* 作者 */
    $modules[$i]['author']  = '李治华';

    /* 网址 */
    $modules[$i]['website'] = 'http://www.huayingcurl.com';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.0';

    return;
}

include_once(ROOT_PATH.'admin/includes/lib_main.php');
require(ROOT_PATH . 'includes/lib_order.php');

$deltime = gmtime() - 30 * 60;
//超过30分钟未支付的，修改订单状态为失效，并释放提货券
$sql = "SELECT * FROM " . $ecs->table('coupons_order') . " WHERE add_time <= ".$deltime." AND order_state != 1";
//echo $sql;die;
$arr_order = array();
$arr_order = $db->getAll($sql);
foreach ($arr_order as $order){
    //更新订单状态为无效2
    //$res_coupons_order = array();
    $update_coupons_order="UPDATE ".$GLOBALS['ecs']->table('coupons_order')." set order_state=2,coupons_id='' "."where orderid='".$order['orderid']."'";
    $res_coupons_order= $GLOBALS['db']->query($update_coupons_order);
   $coupons_number_arr=explode(",", $order['coupons_id']);
   foreach ($coupons_number_arr as $key => $value) {
        //$res_coupons = array();
        if($value){
            $sql_suo_jie="update ".$GLOBALS['ecs']->table('coupons')." set coupons_state=0 where id='".$value."'";
            $res_coupons= $GLOBALS['db']->query($sql_suo_jie);
        }
        
   }
	       
}

?>