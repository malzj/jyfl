<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 订单列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list'){
	$smarty->assign('ur_here', '场馆订单');
	$smarty->assign('full_page',        1);
	include_once(ROOT_PATH . 'includes/lib_order.php');
	update_venues_state();
	if ($_REQUEST['return'] == 1){
	    $_REQUEST['state'] = 2;
	    $_REQUEST['return_point'] = 2;
	}
	$order_list = order_list();
	$smarty->assign('order_list',   $order_list['orders']);
	$smarty->assign('filter',       $order_list['filter']);
	$smarty->assign('record_count', $order_list['record_count']);
	$smarty->assign('page_count',   $order_list['page_count']);
	$smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');

	/* 显示模板 */
	assign_query_info();
	$smarty->display('venues_order_list.htm');
}


/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
	$order_list = order_list();
	$smarty->assign('order_list',   $order_list['orders']);
	$smarty->assign('filter',       $order_list['filter']);
	$smarty->assign('record_count', $order_list['record_count']);
	$smarty->assign('page_count',   $order_list['page_count']);
	$sort_flag  = sort_flag($order_list['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	make_json_result($smarty->fetch('venues_order_list.htm'), '', array('filter' => $order_list['filter'], 'page_count' => $order_list['page_count']));
}

// 退票操作
else if($_REQUEST['act'] == 'returnTicket'){
    include_once(ROOT_PATH . 'includes/lib_cardApi.php');
    $id = intval($_REQUEST['id']);    
    $sql = "SELECT * FROM " . $ecs->table('venues_order') . " WHERE id = '".$id."'";
    $order = $db->getRow($sql);
    
    // 订单状态是已付款或已完成的情况下，才能操作退票，其他状态不支持退票申请
    if ( in_array( $order['state'], array(1,3)) )
    {
        $db->query('UPDATE '.$ecs->table('venues_order')." SET state=4 WHERE id = '$id'");
        sys_msg('操作成功',0,array(array('text' => '场馆订单', 'href' => 'venues_order.php?act=list')));
    }
    
    else 
    {
        sys_msg('次订单不可退票的操作',0,array(array('text' => '场馆订单', 'href' => 'venues_order.php?act=list')));
    }
    
}
// 退票成功操作
else if($_REQUEST['act'] == 'ticketSuccess'){
    include_once(ROOT_PATH . 'includes/lib_cardApi.php');
    $id = intval($_REQUEST['id']);
    $sql = "SELECT * FROM " . $ecs->table('venues_order') . " WHERE id = '".$id."'";
    $order = $db->getRow($sql);

    // 订单状态是已付款或已完成的情况下，才能操作退票，其他状态不支持退票申请
    if ( $order['state'] == 4 )
    {
        $db->query('UPDATE '.$ecs->table('venues_order')." SET state=2 WHERE id = '$id'");
        sys_msg('操作成功',0,array(array('text' => '场馆订单', 'href' => 'venues_order.php?act=list')));
    }

    else
    {
        sys_msg('次订单不可退票的操作',0,array(array('text' => '场馆订单', 'href' => 'venues_order.php?act=list')));
    }

}

// 退点
else if ($_REQUEST['act'] == 'returnPoint'){
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');	
	$id = intval($_REQUEST['id']);

	$sql = "SELECT * FROM " . $ecs->table('venues_order') . " WHERE id = '".$id."'";
	$order = $db->getRow($sql);
    // 动网已退款并且卡点已经支付，执行退点
    // 未付款并且卡点已支付，执行退点
	if( ($order['state']==2 && $order['is_pay'] == 1) || ($order['state']==0 && $order['is_pay'] == 1) ) 
	{
	    $returnState = returnPoint($order);
	    if ( $returnState !== true)
	    {
	       sys_msg($returnState,0,array(array('text' => '场馆订单', 'href' => 'venues_order.php?act=list')));
	       exit;
	    }	    
	    
	    // 退款成功，修改状态
	    $db->query('UPDATE '.$ecs->table('venues_order')." SET return_point=1 WHERE id = '$id'");	    
	    $links[] = array('text' => $_LANG['order_info'], 'href' => 'venues_order.php?act=list');
	    sys_msg('操作成功', 0, $links);	    
	}
    else 
    {
        sys_msg('不支持你的请求！',0,array(array('text' => '场馆订单', 'href' => 'venues_order.php?act=list')));
    }		
}

// 退点函数
function returnPoint($order)
{
    require_once (ROOT_PATH . 'includes/lib_huayingcard.php');
    $card = new huayingcard();
    /** TODO 充值 （双卡版） */
    $arr_param = array(
        'CardInfo' => array( 'CardNo'=> $order['username'], 'TransId'=> $order['api_card_Id']),
        'TransationInfo' => array( 'TransRequestPoints'=>$order['money'])
    );    
    $state = $card->action($arr_param, 9);
    if ($state == 1)
    {
        return $card->getMessage();
    }
    
    return true;
}

function order_list()
{
	$result = get_filter();
	if ($result === false){
		/* 过滤信息 */
		$filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
		$filter['user_id'] = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
		$filter['user_name'] = empty($_REQUEST['user_name']) ? '' : trim($_REQUEST['user_name']);

		$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
		$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
		
		$filter['state'] = empty($_REQUEST['state']) ? '' : intval($_REQUEST['state']);
		$filter['return_point'] = empty($_REQUEST['return_point']) ? '' : intval($_REQUEST['return_point']);
		
		$where = 'WHERE 1 ';
		if ($filter['order_sn'])
		{
			$where .= " AND o.order_sd LIKE '%" . mysql_like_quote($filter['order_sn']) . "%'";
		}
		if ($filter['user_id'])
		{
			$where .= " AND o.user_id = '$filter[user_id]'";
		}
		if ($filter['user_name'])
		{
			$where .= " AND u.user_name = '$filter[user_name]'";
		}
		if ($filter['state'])
		{
		    $where .= " AND o.state = '$filter[state]'";
		}
		if ($filter['return_point'] == 2)
		{
		    $where .= " AND o.return_point = 0";
		}    
		
		/* 分页大小 */
		$filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

		if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
		{
			$filter['page_size'] = intval($_REQUEST['page_size']);
		}
		elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
		{
			$filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
		}
		else
		{
			$filter['page_size'] = 15;
		}

		/* 记录总数 */
		if ($filter['user_name'])
		{
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('venues_order') . " AS o ,".
				   $GLOBALS['ecs']->table('users') . " AS u " . $where;
		}
		else
		{
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('venues_order') . " AS o ". $where;
		}

		$filter['record_count']   = $GLOBALS['db']->getOne($sql);
		$filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

		/* 查询 */
		$sql = "SELECT o.*, u.user_name " .
				" FROM " . $GLOBALS['ecs']->table('venues_order') . " AS o " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=o.user_id ". $where .
				" ORDER BY $filter[sort_by] $filter[sort_order] ".
				" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";

		foreach (array('order_id', 'user_name') AS $val)
		{
			$filter[$val] = stripslashes($filter[$val]);
		}
		set_filter($filter, $sql);
	}
	else
	{
		$sql    = $result['sql'];
		$filter = $result['filter'];
	}

	$info = $GLOBALS['db']->getAll($sql);

	/* 格式话数据 */
	foreach ($info AS $key => &$row)
	{
		$row['order_state_sn'] = order_start_sn($row);
		$row['add_time'] = date('Y-m-d H:i:s',$row['add_time']);
		foreach (json_decode($row['times']) as $time)
		{
		    $row['times_mt'][] =urldecode($time);
		}
		
	}
	$arr = array('orders' => $info, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

	return $arr;
}

function order_start_sn($row){
	
	$order_state_sn = '';
	
	//未付款 
	if ($row['state'] == 0 && $row['is_pay'] == 0)
	{
		$order_state_sn = '<font color=red>未付款</font>';
	}
	//未付款
	if ($row['state'] == 0 && $row['is_pay'] == 1)
	{
	    $order_state_sn = '<font color=red>未付款</font><br><font color=red>卡点已扣</font>';
	}
	// 已付款
	if ($row['state'] == 1 && $row['is_pay'] == 1)
	{
		$order_state_sn = '<font>已付款<br><font color=red>出票中</font></font>';
	}
	// 已完成
	if ($row['state'] == 3 && $row['is_pay'] == 1)
	{
		$order_state_sn = '<font>已完成</font>';
	}
	// 已退票
	if ($row['state'] == 2 && $row['return_point'] == 0)
	{
		$order_state_sn = '<font color=red>已退票（未退点）</font>';
	}
	// 已退款
	if ($row['state'] == 2 && $row['return_point'] == 1)
	{
	    $order_state_sn = '<font color=red>已退票（已退点）</font>';
	}
	// 退票中
	if ($row['state'] == 4)
	{
	    $order_state_sn = '<font color=red>退票中</font>';
	}
	
	return $order_state_sn;
}

