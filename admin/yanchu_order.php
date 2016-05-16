<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 订单列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list'){
	$smarty->assign('ur_here', $_LANG['03_ticket_order']);
	$smarty->assign('full_page',        1);

	$order_list = order_list();

	$smarty->assign('order_list',   $order_list['orders']);
	$smarty->assign('filter',       $order_list['filter']);
	$smarty->assign('record_count', $order_list['record_count']);
	$smarty->assign('page_count',   $order_list['page_count']);
	$smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');

	/* 显示模板 */
	assign_query_info();
	$smarty->display('yanchu_order_list.htm');
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
	make_json_result($smarty->fetch('yanchu_order_list.htm'), '', array('filter' => $order_list['filter'], 'page_count' => $order_list['page_count']));
}

//订单批量删除操作
elseif ($_REQUEST['act'] == 'operate'){
	$batch          = isset($_REQUEST['batch']); // 是否批处理
	$order_sn   = $_REQUEST['order_id'];
	$order_sn_list = explode(',', $order_sn);
	if (isset($_POST['remove'])){
		foreach ($order_sn_list as $sn_order){
			/* 删除订单 */
			$db->query("DELETE FROM ".$ecs->table('yanchu_order'). " WHERE order_sn = '$sn_order'");
			$sn_list[] = $sn_order;
		}

		$sn_list = join($sn_list, ',');
		$msg = $sn_list.'删除成功';
		$links[] = array('text' => $_LANG['return_list'], 'href' => 'yanchu_order.php?act=list');
		sys_msg($msg, 0, $links);
	}
}

elseif ($_REQUEST['act'] == 'remove_order'){

	$order_id = intval($_REQUEST['id']);

	$GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table('yanchu_order'). " WHERE order_id = '$order_id'");
	if ($GLOBALS['db'] ->errno() == 0)
	{
		$url = 'yanchu_order.php?act=list';
		ecs_header("Location: $url\n");
		exit;
	}
	else
	{
		sys_msg('删除失败');
	}
}
//退票,只是退票，不退运费
else if ($_REQUEST['act'] == 'return'){
	$order_id = intval($_REQUEST['id']);
	
	 $sql = "SELECT * FROM " . $ecs->table('yanchu_order') . " WHERE order_id = '$order_id'";
     $order = $db->getRow($sql);
	 //var_dump($order);exit;
	 $user_name = trim($order['user_name']);
	
	 $userinfo = $db->getRow('SELECT user_name, password FROM '.$ecs->table('users')." WHERE user_id = '".$order['user_id']."'");
	 /** TODO 充值 （双卡版） */
	 $arr_param['CardInfo'] = array( 'CardNo'=> $userinfo['user_name'], 'TransId'=> $order['api_order_id']);
	 //已退运费
	 if($order['return_status']==1){
	 	$arr_param['TransationInfo'] =array( 'TransRequestPoints'=>$order['goods_amount']);
	 	$sql='UPDATE '.$ecs->table('yanchu_order')." SET shipping_status = 3 WHERE order_id = '$order_id'";
	//未退运费
	 }else{
	 	$arr_param['TransationInfo'] =array( 'TransRequestPoints'=>$order['money_paid']);
	 	$sql='UPDATE '.$ecs->table('yanchu_order')." SET shipping_status = 3,return_status=1 WHERE order_id = '$order_id'";
	 }
	 $state = $cardPay->action($arr_param, 9);
	 if ($state == 1)
	 {
	 	sys_msg($cardPay->getMessage());
	 }
	$db->query($sql);

	$links[] = array('text' => $_LANG['order_info'], 'href' => 'yanchu_order.php?act=list');
    sys_msg($_LANG['act_ok'] . $msg, 0, $links);
	
	
}

//退运费，添加退运费状态
else if ($_REQUEST['act'] == 'tuiyunfei'){

	$order_id = intval($_REQUEST['id']);
	$sql = "SELECT * FROM " . $ecs->table('yanchu_order') . " WHERE order_id = '$order_id'";
    $order = $db->getRow($sql);
  
	$user_name = trim($order['user_name']);
	$userinfo = $db->getRow('SELECT user_name, password FROM '.$ecs->table('users')." WHERE user_id = '".$order['user_id']."'");
	
	//已退运费,限制条件：，未退运费，已支付，有运费，未退款，才可以退运费
	if( $order['return_status'] ==0  
		&& $order['shipping_fee']>0  
		&& $order['pay_status']==2   
		&& $order['shipping_status']!=3 )
		{
			$arr_param = array( 
				'CardInfo'=>array( 'CardNo'=> $userinfo['user_name'], 'TransId'=> $order['api_order_id']),
				'TransationInfo'=> array( 'TransRequestPoints'=>$order['shipping_fee']));
			
			if($cardPay->action($arr_param, 9) == 0)
			{
				$sql='UPDATE '.$ecs->table('yanchu_order')." SET return_status = 1 WHERE order_id = '$order_id'";
				$db->query($sql);
				$links[] = array('text' => $_LANG['order_info'], 'href' => 'yanchu_order.php?act=list');
				sys_msg('操作成功', 0, $links);
			}
			else{
				sys_msg($cardPay->getMessage());
			}
	 	
	//未退运费
	}else{
	 	sys_msg('运费已退，请不要重复退！');
	}
}
else if ($_REQUEST['act'] == 'shippingStatus'){
	$order_id = intval($_REQUEST['id']);
	$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('yanchu_order'). " SET shipping_status = 2, shipping_time = '".gmtime()."' WHERE order_id = '$order_id'");
	if ($GLOBALS['db'] ->errno() == 0)
	{
		$url = 'yanchu_order.php?act=list';
		ecs_header("Location: $url\n");
		exit;
	}
	else
	{
		sys_msg('设置失败');
	}
}


function order_list()
{
	$result = get_filter();
	if ($result === false){
		/* 过滤信息 */
		$filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
		$filter['user_id'] = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
		$filter['user_name'] = empty($_REQUEST['user_name']) ? '' : trim($_REQUEST['user_name']);

		$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'pay_time' : trim($_REQUEST['sort_by']);
		$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
		
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
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('yanchu_order') . " AS o ,".
				   $GLOBALS['ecs']->table('users') . " AS u " . $where;
		}
		else
		{
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('yanchu_order') . " AS o ". $where;
		}

		$filter['record_count']   = $GLOBALS['db']->getOne($sql);
		$filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

		/* 查询 */
		$sql = "SELECT o.order_id, o.order_sn, o.api_order_sn, o.add_time, o.order_status, o.shipping_status,  o.return_status, o.order_amount, o.money_paid, o.source, " .
					"o.layout,o.take_way,o.pay_status, o.mobile,o.market_price, o.tel, o.best_time, o.consignee, o.address, o.regionname, o.goods_amount, o.price, o.shipping_fee, (o.goods_amount + o.shipping_fee) as total_fee, o.number, o.user_name, o.itemname, o.sitename, o.catename, o.consignee, o.address, o.regionname " .
				" FROM " . $GLOBALS['ecs']->table('yanchu_order') . " AS o " .
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

	$row = $GLOBALS['db']->getAll($sql);

	/* 格式话数据 */
	foreach ($row AS $key => $value)
	{
		if ($value['order_status'] == 1){
			$row[$key]['order_status_cn'] = '已确认';
		}else{
			$row[$key]['order_status_cn'] = '未确认';
		}

		if ($value['pay_status'] == 0){
			$row[$key]['pay_status_cn'] = '未付款';
		}else if ($value['pay_status'] == 2){
			$row[$key]['pay_status_cn'] = '已付款';
		}

		if ($value['shipping_status'] == 0){
			$row[$key]['shipping_status_cn'] = '未兑票';
		}else if ($value['shipping_status'] == 2){
			$row[$key]['shipping_status_cn'] = '已兑票';
		}else if ($value['shipping_status'] == 3){
			$row[$key]['shipping_status_cn'] = '<font color=red>已退票</font>';
		}
		
		if($value['return_status']==0&&$value['shipping_fee']>0&&$value['pay_status']==2&&$value['shipping_status']!=3){
			$row[$key]['tuiyunfei'] = '<font color="#FF44AA">退运费</font>';
		}
		if($value['return_status']==1){
			$row[$key]['tuiyunfei'] = '<font color="#FF44AA">运费已退</font>';
		}
		$row[$key]['formated_order_amount'] = price_format($value['order_amount']);
		$row[$key]['formated_goods_amount'] = price_format($value['goods_amount']);
		$row[$key]['formated_money_paid']   = price_format($value['money_paid']);
		$row[$key]['short_order_time']      = local_date('Y-m-d H:i', $value['add_time']);
		$row[$key]['formated_price']        = price_format($value['price']);

		if ($value['order_status'] == OS_INVALID || $value['order_status'] == OS_CANCELED){
			$row[$key]['can_remove'] = 1;
		}else{
			$row[$key]['can_remove'] = 0;
		}
	}
	$arr = array('orders' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

	return $arr;
}