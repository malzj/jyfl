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
$order_unpaied = $db->getAll("SELECT * FROM ".$ecs->table('code_order')." WHERE order_status = 1 AND add_time+".(15*60)."<unix_timestamp(now())");
//取消未付款已超时的订单，解锁已锁定商品码
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
        $code_sql = "SELECT c.*,s.supplier_name,m.content FROM ".$ecs->table('code')." AS c , ".$ecs->table('supplier')." AS s LEFT JOIN ".$ecs->table('supplier_message')." AS m ON s.supplier_id = m.supplier_id WHERE c.supplier_id = s.supplier_id AND c.id IN(".$order_info['code_id'].")";
        $code_info = $db->getAll($code_sql);

//支付成功短信发放电子码
        $userInfo = $db->getRow("SELECT nickname FROM " . $ecs->table('users') . " WHERE user_name = " . $order_info['user_name']);
        $msgInfo = array(
            'nickname' => $userInfo['nickname'],
            'mobile' => $order_info['mobile']
        );
        $Smsvrerify = new smsvrerifyApi();
        $error = 0;
        foreach ($code_info as $code) {
            $code['content'] = empty($code['content'])?'您好{$nickname}先生（女士）,感谢你购买{$supplier_name}商品码，账号：{$account},密码：{$password}':$code['content'];
            $msgInfo = array_merge($msgInfo,$code);
            $smarty->assign($msgInfo);
            $message = $smarty->fetch("str:" . $code['content']);
            $result = $Smsvrerify->smsvrerify($msgInfo['mobile'], $message, '', '聚优福利');
            if($result != 0)
                $error ++;
        }
        if($error == 0)
            //修改商品码信息为已发送
            $db->query("UPDATE " . $ecs->table('code_order') . " SET send_msg = 1 WHERE id = " . $order_info['id']);
    }
}