<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/lib_goods.php');

if ($_REQUEST['act'] == 'list')
{		
	/* 载入订单状态、付款状态、发货状态 */
	$order_list =  order_list();
	
	$smarty->assign('ur_here', $_LANG['13_entity_order']);
	/* 显示模板 */
	assign_query_info();
	/* 模板赋值 */
	$smarty->assign('full_page',    1);
	$smarty->assign('order_list', 	$order_list['orders']);
	$smarty->assign('filter',       $order_list['filter']);
	$smarty->assign('record_count', $order_list['record_count']);
	$smarty->assign('page_count',   $order_list['page_count']);
	$smarty->display('entity_list.htm');
}
elseif ($_REQUEST['act'] == 'query')
{
	$order_list =  order_list();
	
	$smarty->assign('full_page',    1);
	$smarty->assign('order_list', 	$order_list['orders']);
	$smarty->assign('filter',       $order_list['filter']);
	$smarty->assign('record_count', $order_list['record_count']);
	$smarty->assign('page_count',   $order_list['page_count']);
	make_json_result($smarty->fetch('entity_list.htm'), '', array('filter' => $order_list['filter'], 'page_count' => $order_list['page_count']));
}
elseif($_REQUEST['act'] == 'show'){
	$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']): 0 ;
	$row = $GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['ecs']->table('entity_order')." WHERE id=".$id);
	if (empty($row))
	{
		sys_msg('无效的操作',0,array(array('text'=>'返回列表', 'href'=>'entity.php?act=list')));
	}
	// 内容处理
	$row['supplier_name'] 	= get_supplier_name($row['user_id']);
	$row['card_goods']		= unserialize($row['card_goods']);
	$row['money_goods']		= unserialize($row['money_goods']);
	$row['pay_cards']		= unserialize($row['pay_cards']);
	$row['utype']			= $row['utype']==1 ? '华影VIP' : '普通客户';
	$row['add_time']		= date('Y-m-d H:i:s', $row['add_time']);
	$smarty->assign('row', $row);
	$smarty->display('entity_info.htm');
}



function order_list()
{
	$result = get_filter();
	if ($result === false){
		/* 过滤信息 */
		$filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
		//$filter['user_id'] = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
		$filter['supplier_name'] = empty($_REQUEST['supplier_name']) ? '' : trim($_REQUEST['supplier_name']);

		$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
		$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

		$where = 'WHERE 1 ';
		if ($filter['order_sn'])
		{
			$where .= " AND o.order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%'";
		}
		/* if ($filter['user_id'])
		{
			$where .= " AND o.user_id = '$filter[user_id]'";
		} */
		if ($filter['user_name'])
		{
			$where .= " AND s.supplier_name = '$filter[supplier_name]'";
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
		if ($filter['supplier_name'])
		{
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('entity_order') . " AS o ,".
					$GLOBALS['ecs']->table('supplier') . " AS s " . $where;
		}
		else
		{
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('entity_order') . " AS o ". $where;
		}

		$filter['record_count']   = $GLOBALS['db']->getOne($sql);
		$filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

		/* 查询 */
		$sql = "SELECT o.*,s.supplier_name" .
				" FROM " . $GLOBALS['ecs']->table('entity_order') . " AS o " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('supplier'). " AS s ON s.supplier_id=o.user_id ". $where .
				" ORDER BY id DESC ".
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
		$row['status_name'] = $row['status']=='1' ? '已支付' : '未支付';
		$row['add_time'] = date('Y-m-d H:i:s', $row['add_time']);
	}
	$arr = array('orders' => $seatInfo, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

	return $arr;
}

