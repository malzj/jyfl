<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 订单列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list'){
	$smarty->assign('ur_here', $_LANG['03_film_order']);
	$smarty->assign('full_page',        1);
	include_once(ROOT_PATH . 'includes/lib_order.php');
	update_piao_order_status();
	$order_list = order_list();

	$smarty->assign('order_list',   $order_list['orders']);
	$smarty->assign('filter',       $order_list['filter']);
	$smarty->assign('record_count', $order_list['record_count']);
	$smarty->assign('page_count',   $order_list['page_count']);
	$smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');

	/* 显示模板 */
	assign_query_info();
	$smarty->display('dongpiao_order_list.htm');
}


/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
	$order_list = order_list();
	//error_log(var_export($order_list,true),'3','error.log'); exit;
	$smarty->assign('order_list',   $order_list['orders']);
	$smarty->assign('filter',       $order_list['filter']);
	$smarty->assign('record_count', $order_list['record_count']);
	$smarty->assign('page_count',   $order_list['page_count']);
	$sort_flag  = sort_flag($order_list['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	make_json_result($smarty->fetch('dongpiao_order_list.htm'), '', array('filter' => $order_list['filter'], 'page_count' => $order_list['page_count']));
}
/*-------------------------------------------------------*/
//-- 订单详细 
/*-------------------------------------------------------*/
elseif ($_REQUEST['act'] == 'show_order')
{
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	$id = intval($_REQUEST['id']);
	if ($id == 0)
	{
		sys_msg('非法操作',0,array(array('text'=>'返回门票列表', 'href'=>'dongpiao_order.php?act=list')));
	}
	// 得到订单号，和产品号
	$sql = "SELECT * FROM " . $ecs->table('piao_order') . " WHERE id = '".$id."'";
	$dborder = $db->getRow($sql);
	if (!empty($dborder))
	{
		// 产品信息
		$product_param = array('productNo'=>$dborder['product_no']);
		$detail = getDongapi('detail',$product_param);
		if ($detail['status'] == '0')
		{
			sys_msg($detail['msg'],0,array(array('text'=>'返回门票列表', 'href'=>'dongpiao_order.php?act=list')));
		}
		// 产品详细
		$detail = $detail['product'];
		
		// 有效期处理，（当天，指定日期）
		if ($detail['validityType'] == 0)
		{
			$detail['validityText'] = '当天有效';
		}
		else{
			$detail['validityText'] = $detail['validityCon'];
		}
		
		// 订单详细
		$order_param = array('orderId'=>$dborder['order_id']);
		$orders = getDongapi('odetail',$order_param);
		if ($orders['status'] == '0')
		{
			sys_msg($orders['msg'],0,array(array('text'=>'返回门票列表', 'href'=>'dongpiao_order.php?act=list')));
		}
		
		// 订单详细
		$order = $orders['orders']['order'];
		
		// 订单状态有变化时，更新表中订单的状态
		if($order['orderState'] != $dborder['order_state'] || $order['travelDate'] != $dborder['traveldate'])
		{
			$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('piao_order'). " SET order_state = '".$order['orderState']."', traveldate = '".$order['travelDate']."' WHERE order_id = ".$dborder['order_id']);
			$dborder = $db->getRow("SELECT * FROM " . $ecs->table('piao_order') . " WHERE id = '".$id."'");
		}
		/**
		 *  退款按钮的显示
		 *  思路：
		 *  card_state = 1 AND order_state = 1 						卡点支付了，门票未支付状态
		 *  card_state = 1 AND order_state = 3						卡点支付了，门票预订失败了
		 *  card_state = 1 AND order_state = 4 AND 	cancelDay !=0	卡点支付了，门票预订已完成，当仍然向退款退货的 (作废)
		 */
		$detail['order_state_sn'] = order_start_sn($dborder);
		if ( ($dborder['card_state']==1 && $dborder['order_state']==1) || 
			 ($dborder['card_state']==1 && $dborder['order_state']==3)
			 /* ($dborder['card_state']==1 && $dborder['order_state']==4 && $detail['cancelDay'] !=0) */
				)
		{
			$dborder['refund'] = 'true';
			$detail['chancelDayCn'] = '<font color="green">可以退货退款 (不代表能退款成功，如果失败，会有返回消息，如有问题请联系我)</font>';
		}
		else{
			$dborder['refund'] = 'false';
			$detail['chancelDayCn'] = '<font color="red">不可以退货退款</font>';
		}
		
		$dborder['add_time'] = date('Y-m-d H:i:s',$dborder['add_time']);
	}
	
	assign_query_info();
	$smarty->assign('detail', $detail);
	$smarty->assign('dborder', $dborder);
	$smarty->display('dongpiao_order_show.htm');
	
}
else if($_REQUEST['act'] == 'resend'){
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	
	$orderId = addslashes_deep($_REQUEST['order_id']);
	$phone =  addslashes_deep($_REQUEST['phone']);
	if (empty($orderId) || empty($phone))
	{
		sys_msg('订单号和手机号不能为空！',0,array(array('text'=>'返回门票列表', 'href'=>'dongpiao_order.php?act=list')));
	}
	$param = array(
		'orderId'=>$orderId,
		'mobile' =>$phone
	);
	
	$resend = getDongapi('resend', $param);
	if ($resend['status'] == 0)
	{
		sys_msg($resend['msg'],0,array(array('text'=>'返回门票列表', 'href'=>'dongpiao_order.php?act=list')));
	}
	else{
		sys_msg('短信重发成功',0,array(array('text'=>'返回门票列表', 'href'=>'dongpiao_order.php?act=list')));
	}
		
}
//订单批量删除操作
/* elseif ($_REQUEST['act'] == 'operate'){
	$batch      	= isset($_REQUEST['batch']); // 是否批处理
	$order_sn   	= $_REQUEST['order_id'];
	$order_sn_list 	= explode(',', $order_sn);
	if (isset($_POST['remove'])){
		foreach ($order_sn_list as $sn_order){
			// 删除订单 
			$db->query("DELETE FROM ".$ecs->table('seats_order'). " WHERE order_sn = '$sn_order'");
			$sn_list[] = $sn_order;
		}

		$sn_list = join($sn_list, ',');
		$msg = $sn_list.'删除成功';
		$links[] = array('text' => $_LANG['return_list'], 'href' => 'film_order.php?act=list');
		sys_msg($msg, 0, $links);
	}
} */

/* elseif ($_REQUEST['act'] == 'remove_order'){

	$order_id = intval($_REQUEST['id']);

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
	}
} */
else if ($_REQUEST['act'] == 'return'){
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	
	$id = intval($_REQUEST['id']);

	$sql = "SELECT * FROM " . $ecs->table('piao_order') . " WHERE id = '".$id."'";
	$order = $db->getRow($sql);
	//退款条件，true 可以退款， false 不可以退款
	$return_where = false;
	
	// 如果门票没有付款，卡点付了，可以退款
	// 如果门票取消了，卡点付了，可以退款
	if($order['card_state']==1 && ($order['order_state'] ==1 || $order['order_state'] ==3)) {
		$return_where = true;
	}

	if($return_where === false){
		sys_msg('不支持你的请求！',0,array(array('text' => '门票列表', 'href' => 'dongpiao_order.php?act=list')));
	}
	
	
	$userinfo = $db->getRow('SELECT user_name, password FROM '.$ecs->table('users')." WHERE user_id = '".$order['user_id']."'");

	/** TODO 充值 （双卡版） */
	$arr_param = array(
			'CardInfo' => array( 'CardNo'=> $userinfo['user_name'], 'TransId'=> $order['api_order_id']),
			'TransationInfo' => array( 'TransRequestPoints'=>$order['sale_price'])
	);
	
	$state = $cardPay->action($arr_param, 9);
	if ($state == 1)
	{
		sys_msg($cardPay->getMessage());
	}

	
	$db->query('UPDATE '.$ecs->table('piao_order')." SET card_state = 2 WHERE id = '$id'");

	$links[] = array('text' => $_LANG['order_info'], 'href' => 'dongpiao_order.php?act=list');
	sys_msg('操作成功', 0, $links);

}
function order_list()
{
	$result = get_filter();
	if ($result === false){
		/* 过滤信息 */
		$filter['order_id'] = empty($_REQUEST['order_id']) ? '' : trim($_REQUEST['order_id']);
		$filter['user_id'] = empty($_REQUEST['user_id']) ? 0 : intval($_REQUEST['user_id']);
		$filter['user_name'] = empty($_REQUEST['user_name']) ? '' : trim($_REQUEST['user_name']);

		$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
		$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
		
		$where = 'WHERE 1 ';
		if ($filter['order_id'])
		{
			$where .= " AND o.order_id LIKE '%" . mysql_like_quote($filter['order_id']) . "%'";
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
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('piao_order') . " AS o ,".
				   $GLOBALS['ecs']->table('users') . " AS u " . $where;
		}
		else
		{
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('piao_order') . " AS o ". $where;
		}

		$filter['record_count']   = $GLOBALS['db']->getOne($sql);
		$filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

		/* 查询 */
		$sql = "SELECT o.*, u.user_name " .
				" FROM " . $GLOBALS['ecs']->table('piao_order') . " AS o " .
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
		
		$row['unit_price']      		= price_format($row['unit_price']);
		$row['sale_price']      		= price_format($row['sale_price']);
		$row['order_price']      		= price_format($row['order_money']);
		
	}
	$arr = array('orders' => $info, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

	return $arr;
}

function order_start_sn($row){
	
	$order_state_sn = '';
	
	// 已确认、未付款      	order_state = 1
	if ($row['order_state'] == 1 && $row['card_state'] == 0)
	{
		$order_state_sn = '<font>已确认<br><font color=red>未付款</font></font>';
	}
	// 未付款、已取消       	order_state =   AND card_state = 1
	if ($row['order_state'] == 1 && $row['card_state'] == 1)
	{
		$order_state_sn = '<font>已确认<br>已付款<br><font color=red>订单失败</font></font>';
	}
	// 已付款、待出票       	order_state = 2  AND card_state = 1
	if ($row['order_state'] == 2 && $row['card_state'] == 1)
	{
		$order_state_sn = '<font>已付款<br>待出票<br></font>';
	}
	// 未付款、已取消       	order_state = 3  AND card_state = 0
	if ($row['order_state'] == 3 && $row['card_state'] == 0)
	{
		$order_state_sn = '<font>未付款<br><font color=red>已取消</font></font>';
	}
	
	// 已付款、已完成	order_state = 4  AND card_state = 1
	if ($row['order_state'] == 4 && $row['card_state'] == 1)
	{
		$order_state_sn = '<font>已付款<br>已完成</font>';
	}
	
	// 已付款、已取消	order_state = 3  AND card_state = 1
	if ($row['order_state'] == 3 && $row['card_state'] == 1)
	{
		$order_state_sn = '<font>已付款<br><br><font color=red>已取消</font></font>';
	}
	
	// 已付款、已退款	order_state = 2  AND card_state = 2
	if ($row['order_state'] == 2 && $row['card_state'] == 2)
	{
		$order_state_sn = '<font>已付款<br><font color=red>已退款</font></font>';
	}
	
	// 已取消、已退款	order_state = 3  AND card_state = 2
	if ($row['order_state'] == 3 && $row['card_state'] == 2)
	{
		$order_state_sn = '<font>已取消<br><font color=red>已退款</font></font>';
	}
	
	// 已完成、已退款	order_state = 4  AND card_state = 2
	if ($row['order_state'] == 4 && $row['card_state'] == 2)
	{
		$order_state_sn = '<font>已完成<br><font color=red>已退款</font></font>';
	}
	
	return $order_state_sn;
}

