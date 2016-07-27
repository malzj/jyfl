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