<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/8/17
 * Time: 17:06
 */
define('IN_ECS', true);

include_once(dirname(__FILE__) . '/lib/_init.php');
require(ROOT_PATH . 'includes/lib_smsvrerify.php');

//获取未付款已超时的订单
$order_unpaied = $db->getAll("SELECT * FROM ".$ecs->table('code_order')." WHERE order_status = 1 AND add_time+".(1*60)."<unix_timestamp(now())");
//取消未付款已超时的订单，解锁已锁定商品码
var_dump($order_unpaied);
if(!empty($order_unpaied)) {
    foreach ($order_unpaied as $order) {
        $db->query("UPDATE " . $ecs->table('code_order') . "SET order_status = 2 WHERE id = " . $order['id']);
        $db->query("UPDATE " . $ecs->table('code') . "SET status = 0,order_sn='' WHERE id IN (" . $order['code_id'].")");
    }
}
//获取已付款未发送信息订单
$order_unsend = $db->getAll("SELECT * FROM ".$ecs->table('code_order')." WHERE order_status = 3 AND send_msg = 0");

if(!empty($order_unsend)) {
    foreach ($order_unsend as $order_info) {
        $code_sql = "SELECT c.*,s.supplier_name FROM " . $ecs->table('code') . " AS c LEFT JOIN " . $ecs->table('supplier') . " AS s ON c.supplier_id = s.supplier_id WHERE c.id IN(" . $order_info['code_id'] . ")";
        $code_info = $db->getAll($code_sql);

//支付成功短信发放电子码
        $userInfo = $db->getRow("SELECT nickname FROM " . $ecs->table('users') . " WHERE user_name = " . $order_info['user_name']);
        $msgInfo = array(
            'nickname' => $userInfo['nickname'],
            'mobile' => $order_info['mobile']
        );
        $content = "%s先生（女士）感谢你购买%s商品码，账号：%s，密码：%s。";
        $Smsvrerify = new smsvrerifyApi();
        $error = 0;
        foreach ($code_info as $code) {
            $msgInfo = array_merge($msgInfo, $code);
            $message = sprintf($content, $msgInfo['nickname'], $msgInfo['supplier_name'], $msgInfo['account'], $msgInfo['password']);
            $result = $Smsvrerify->smsvrerify($msgInfo['mobile'], $message, '', '聚优福利');
            if($result != 0)
                $error ++;
        }
        if($error == 0)
            //修改商品码信息为已发送
            $db->query("UPDATE " . $ecs->table('code_order') . " SET send_msg = 1 WHERE id = " . $order_info['id']);
    }
}