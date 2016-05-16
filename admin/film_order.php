<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 订单列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list'){
	set_time_limit(0);
	$smarty->assign('ur_here', $_LANG['03_film_order']);
	$smarty->assign('full_page',        1);
	include_once(ROOT_PATH . 'includes/lib_order.php');
	movie_orders_status();
    if ($_REQUEST['return'] == 1){
	    $_REQUEST['order_status'] = 5;
	    $_REQUEST['card_pay'] = 1;
	}
	$order_list = order_list();

	$smarty->assign('order_list',   $order_list['orders']);
	$smarty->assign('filter',       $order_list['filter']);
	$smarty->assign('record_count', $order_list['record_count']);
	$smarty->assign('page_count',   $order_list['page_count']);
	$smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');

	/* 显示模板 */
	assign_query_info();
	$smarty->display('seat_order_list.htm');
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
	make_json_result($smarty->fetch('seat_order_list.htm'), '', array('filter' => $order_list['filter'], 'page_count' => $order_list['page_count']));
}

//订单批量删除操作
elseif ($_REQUEST['act'] == 'operate'){
	$batch      	= isset($_REQUEST['batch']); // 是否批处理
	$order_sn   	= $_REQUEST['order_id'];
	$order_sn_list 	= explode(',', $order_sn);
	if (isset($_POST['remove'])){
		foreach ($order_sn_list as $sn_order){
			/* 删除订单 */
			$db->query("DELETE FROM ".$ecs->table('seats_order'). " WHERE order_sn = '$sn_order'");
			$sn_list[] = $sn_order;
		}

		$sn_list = join($sn_list, ',');
		$msg = $sn_list.'删除成功';
		$links[] = array('text' => $_LANG['return_list'], 'href' => 'film_order.php?act=list');
		sys_msg($msg, 0, $links);
	}
}

elseif ($_REQUEST['act'] == 'remove_order'){

	/*$order_id = intval($_REQUEST['id']);

	$GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table('seats_order'). " WHERE id = '$order_id'");
	if ($GLOBALS['db'] ->errno() == 0)
	{
		$url = 'film_order.php?act=list';
		ecs_header("Location: $url\n");
		exit;
	}
	else
	{
		sys_msg('删除失败');
	}*/
}
else if ($_REQUEST['act'] == 'return'){
	$order_id = intval($_REQUEST['id']);

	$sql = "SELECT * FROM " . $ecs->table('seats_order') . " WHERE id = '$order_id'";
	$order = $db->getRow($sql);
	//退款条件，true 可以退款， false 不可以退款
	$return_where = false;
	// 如果购票失败，可以退款
	if($order['order_status'] == 5){
		$return_where = true;
	}
	// 如果电影票没有付款，卡点付了，可以退款
	if($order['card_pay']==1 && $order['order_status'] < 3){
		$return_where = true;
	}
	if($return_where === false){
		sys_msg('非法操作！',0,array(array('text' => '电影订单列表', 'href' => 'film_order.php?act=list')));
	}
	$user_name = trim($order['user_name']);
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	
	$userinfo = $db->getRow('SELECT password FROM '.$ecs->table('users')." WHERE user_name = '".$user_name."'");
	/** TODO 退款 （双卡版） */
	$arr_param = array(
			'CardInfo' => array( 'CardNo'=> $user_name, 'TransId'=> $order['api_order_id']),
			'TransationInfo' => array( 'TransRequestPoints'=>$order['money'])
	);
	$state = $cardPay->action($arr_param, 9);
	if ($state == 1)
	{
		sys_msg($cardPay->getMessage());
	}
	
	$db->query('UPDATE '.$ecs->table('seats_order')." SET order_status = 6, card_pay = 0 WHERE id = '$order_id'");

	$links[] = array('text' => $_LANG['order_info'], 'href' => 'film_order.php?act=list');
	sys_msg('操作成功', 0, $links);

}
// 抠电影订单，短信拉取
elseif( $_REQUEST['act'] == 'order_msg')
{
	$id = intval($_GET['id']);
	if ($id == 0)
	{
		echo json_encode(array( 'error'=>1, 'message'=>'非法操作！'));
		exit;
	}
	
	$sql = " SELECT order_sn FROM ".$GLOBALS['ecs']->table('seats_order')." WHERE id = ".$id;
	$order_sn = $GLOBALS['db']->getOne($sql);
	
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	$arr_result = getCDYapi(array('action'=>'order_Query', 'order_id'=>$order_sn));
	
	if($arr_result['status'] != 0)
	{
		echo json_encode(array('message'=>$arr_result['error']));
		exit;
	}
	
	$order_msg = null;
	
	foreach( (array)$arr_result['orders'] as $order){
		if ($order['orderStatus'] == 4)
		{
			$order_msg = $order['orderMsg'];
		}
	}
	
	if($order_msg == null)
	{
		echo json_encode(array('error'=>1, 'message'=> '目前订单状态不是购票成功，无法拉取短信消息'));
		exit;
	}
	
	//$GLOBALS['db']->query('UPDATE '.$ecs->table('seats_order')." SET order_msg = '$order_msg' WHERE id = '$id'");	
	echo json_encode(array('message'=> $order_msg));
	
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
		$filter['order_status'] = empty($_REQUEST['order_status']) ? '' : intval($_REQUEST['order_status']);
		$filter['card_pay'] = empty($_REQUEST['card_pay']) ? '' : intval($_REQUEST['card_pay']);
		
		$where = 'WHERE 1 ';
		if ($filter['order_sn'])
		{
			$where .= " AND o.order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%'";
		}
		if ($filter['user_id'])
		{
			$where .= " AND o.user_id = '$filter[user_id]'";
		}
		if ($filter['user_name'])
		{
			$where .= " AND u.user_name = '$filter[user_name]'";
		}
		if ($filter['order_status'])
		{
		    $where .= " AND o.order_status = '$filter[order_status]'";
		}
		if ($filter['pay_status'])
		{
		    $where .= " AND o.card_pay = '$filter[pay_status]'";
		}

		//error_log($where."\r\n",'3','error.log');
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
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('seats_order') . " AS o ,".
				   $GLOBALS['ecs']->table('users') . " AS u " . $where;
		}
		else
		{
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('seats_order') . " AS o ". $where;
		}

		$filter['record_count']   = $GLOBALS['db']->getOne($sql);
		$filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

		/* 查询 */
		$sql = "SELECT o.*" .
				" FROM " . $GLOBALS['ecs']->table('seats_order') . " AS o " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=o.user_id ". $where .
				" ORDER BY $filter[sort_by] $filter[sort_order] ".
				" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";

		foreach (array('order_sn', 'user_name') AS $val)
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

	$seatInfo = $GLOBALS['db']->getAll($sql);

	/* 格式话数据 */
	foreach ($seatInfo AS $key => &$row)
	{
		switch($row['order_status']){
			// 下单未付款的
			case '1':
				$row['order_status_cn'] = array('<font color="green">已下单</font>','<font color="red">未付款</font>','<font color="red">未出票</font>');
				break;
			// 取消订单
			case '2':
				$row['order_status_cn'] = array('<font color="green">已下单</font>','<font color="red">未付款</font>','<font color="blue">已取消</font>');
				break;
			// 已付款
			case '3':
				$row['order_status_cn'] = array('<font color="green">已下单</font>','<font color="green">已付款</font>','<font color="red">出票中</font>'	);
				break;
			// 购票成功
			case '4':
				$row['order_status_cn'] = array('<font color="green">已下单</font>','<font color="green">已付款</font>','<font color="green">购票成功</font>');
				break;
			// 购票失败
			case '5':
				$row['order_status_cn'] = array('<font color="green">已下单</font>','<font color="green">已付款</font>','<font color="red";>购票失败</font>');
				break;
			// 已退款
			case '6':
				$row['order_status_cn'] = array('<font color="green">已下单</font>','<font color="green">已付款</font>','<font color="red";>购票失败</font>','<font color="blue";>已退款</font>');
				break;
		}
		// 如果卡支付了，电影取没有支付，显示请联系华影客户
		if($row['card_pay'] == 1 && $row['order_status'] < 3){
			$row['pay_status'] = 1;
		}else{
			$row['pay_status'] = 0;
		}
		$row['seat_info'] 		= str_replace('|', '，', $row['seat_info']);
		$row['add_time']      	= local_date('Y-m-d H:i', $row['add_time']);
		$row['unit_price']      = price_format($row['unit_price']);
		$row['money']      		= price_format($row['money']);
		if (empty($row['order_msg']))
		{
			$row['order_msg_status'] 	= 1;
		}
		else{
			$row['order_msg_status']	= 0;
		}
	}
	$arr = array('orders' => $seatInfo, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

	return $arr;
}