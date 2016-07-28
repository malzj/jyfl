<?php

/**
 * 用户身份验证
 * @var unknown
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_transaction.php');
include_once(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

$user_id = $_SESSION['user_id'];
$size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 20;

if (!isset($_REQUEST['act']))
{
    $_REQUEST['act'] = "empty";
}

/* 查看订单列表 */
if ($_REQUEST['act'] == 'order_list')
{
    $page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('order_info'). " WHERE user_id = '$user_id' AND parent_order_id = 0");

    $pagebar = get_wap_pager($record_count, $size, $page, 'order.php?act=order_list');
    $orders = get_user_orders($user_id, $size, (($page-1)*10));
    //从0重排键值解决浏览器更改顺序
    $orders = array_merge($orders);
    $merge  = get_user_merge($user_id);

    $jsonArray['data'] = array(
        'merge' => $merge,
        'pages' => array(
            'count' => $record_count,
            'size' => $size,
            'page' => $page
        ),
        'orders' => $orders,
    );

    JsonpEncode($jsonArray);
}
//电影订单
else if ($_REQUEST['act'] == 'film_order'){

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('seats_order'). " WHERE user_id = '$user_id'");

    $pagebar = get_wap_pager($record_count, $size, $page, 'order.php?act=film_order');

    $orders = get_user_film_orders_cdy($user_id, $size, (($page-1)*10));

    movie_orders_status('',$user_id);

    $jsonArray['data'] = array(
        'pages' => array(
            'count' => $record_count,
            'size' => $size,
            'page' => $page
        ),
        'orders' => $orders,
    );

    JsonpEncode($jsonArray);
}
/* 查看订单详情 */
elseif ($_REQUEST['act'] == 'order_detail')
{
    include_once(ROOT_PATH . 'includes/lib_payment.php');
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $order_id = isset($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 0;

    /* 订单详情 */
    $order = get_order_detail($order_id, $user_id);

    if ($order === false)
    {
        $jsonArray['state'] = "false";
        $jsonArray['message'] = "未找到订单";
        JsonpEncode($jsonArray);
    }

    /* 是否显示添加到购物车 */
    if ($order['extension_code'] != 'group_buy' && $order['extension_code'] != 'exchange_goods')
    {
        $jsonArray['data']['allow_to_cart'] = 1;
    }

    /* 订单商品 */
    $goods_list = order_goods($order_id);
    foreach ($goods_list AS $key => $value)
    {
        $goods_list[$key]['market_price'] = price_format($value['market_price'], false);
        $goods_list[$key]['goods_price']  = price_format($value['goods_price'], false);
        $goods_list[$key]['subtotal']     = price_format($value['subtotal'], false);
        $goods_list[$key]['goods_thumb']        = get_image_path($value['goods_id'], $value['goods_thumb'], true);
    }

    /* 设置能否修改使用余额数 */
    if ($order['order_amount'] > 0)
    {
        if ($order['order_status'] == OS_UNCONFIRMED || $order['order_status'] == OS_CONFIRMED)
        {
            $user = user_info($order['user_id']);
            if ($user['user_money'] + $user['credit_line'] > 0)
            {
                $jsonArray['data']['allow_edit_surplus'] = 1;
                $jsonArray['data']['max_surplus'] = sprintf($_LANG['max_surplus'], $user['user_money']);
            }
        }
    }

    /* 未发货，未付款时允许更换支付方式 */
    if ($order['order_amount'] > 0 && $order['pay_status'] == PS_UNPAYED && $order['shipping_status'] == SS_UNSHIPPED)
    {
        $payment_list = available_payment_list(false, 0, true);

        /* 过滤掉当前支付方式和余额支付方式 */
        if(is_array($payment_list))
        {
            foreach ($payment_list as $key => $payment)
            {
                if ($payment['pay_id'] == $order['pay_id'] || $payment['pay_code'] == 'balance')
                {
                    unset($payment_list[$key]);
                }
            }
        }
        $jsonArray['data']['payment_list'] = $payment_list;
    }

    /* 订单 支付 配送 状态语言项 */
    $order['order_status_cn'] = $_LANG['os'][$order['order_status']];
    $order['pay_statuses'] = $order['pay_status'];
    $order['pay_status_cn'] = $_LANG['ps'][$order['pay_status']];
    $order['shipping_status_cn'] = $_LANG['ss'][$order['shipping_status']];

    $jsonArray['data']['order'] = $order;
    $jsonArray['data']['goods_list'] = $goods_list;
    JsonpEncode($jsonArray);
}
//电子券订单
else if ($_REQUEST['act'] == 'dzq_order'){

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('dzq_order'). " WHERE user_id = '$user_id'");

    $pagebar = get_wap_pager($record_count, $size, $page, 'order.php?act=film_order');

    $orders = get_user_dzq_orders($user_id, $size, (($page-1)*10));

    $jsonArray['data'] = array(
        'pages' => array(
            'count' => $record_count,
            'size' => $size,
            'page' => $page
        ),
        'orders' => $orders,
    );

    JsonpEncode($jsonArray);
}
//演出订单
else if ($_REQUEST['act'] == 'yanchu_order'){
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('yanchu_order'). " WHERE user_id = '$user_id'");

    $pagebar = get_wap_pager($record_count, $size, $page, 'order.php?act=yanchu_order');

    $orders = get_user_yanchu_orders($user_id, $size, (($page-1)*10));

    $jsonArray['data'] = array(
        'pages' => array(
            'count' => $record_count,
            'size' => $size,
            'page' => $page
        ),
        'orders' => $orders,
    );

    JsonpEncode($jsonArray);
}
// 场馆订单
else if ($_REQUEST['act'] == 'venues_order')
{
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('venues_order'). " WHERE user_id = '$user_id'");
    $pagebar = get_wap_pager($record_count, $size, $page, 'order.php?act=venues_order');
    // 更新门票订单状态
    //update_piao_order_status($_SESSION['user_id']);
    update_venues_state($_SESSION['user_id']);
    $orders = array();

    $sql = "SELECT * ".
        " FROM " .$GLOBALS['ecs']->table('venues_order') .
        " WHERE user_id = '$user_id' ORDER BY add_time DESC";
    $res = $GLOBALS['db']->SelectLimit($sql, $size, (($page-1)*10));
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $orders[$row['id']] = $row;
        $orders[$row['id']]['order_state_sn'] = order_start_sn($row);
        $orders[$row['id']]['add_time'] = date('Y-m-d H:i:s',$row['add_time']);
        foreach (json_decode($row['times']) as $time)
        {
            $orders[$row['id']]['times_mt'][] =urldecode($time);
        }
    }

    $jsonArray['data'] = array(
        'pages' => array(
            'count' => $record_count,
            'size' => $size,
            'page' => $page
        ),
        'orders' => $orders,
    );

    JsonpEncode($jsonArray);
}

function order_start_sn($row){

    $order_state_sn = '';

    //未付款
    if ($row['state'] == 0 )
    {
        $order_state_sn = '<font color=red>未付款</font>';
    }
    // 已付款
    if ($row['state'] == 1 )
    {
        $order_state_sn = '<font color=red>已付款</font>';
    }
    // 已完成
    if ($row['state'] == 3 )
    {
        $order_state_sn = '<font color=green>已完成</font>';
    }
    // 已退票
    if ($row['state'] == 2 )
    {
        $order_state_sn = '<font color=red>已退票</font>';
    }
    if ($row['state'] == 4)
    {
        $order_state_sn = '<font color=red>退票中</font>';
    }
    return $order_state_sn;
}