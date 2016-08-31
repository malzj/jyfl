<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 订单列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list'){
	$smarty->assign('ur_here', $_LANG['03_code_order']);
	$smarty->assign('full_page',        1);

	$order_list = order_list();
	
	

	$smarty->assign('order_list',   $order_list['orders']);
	$smarty->assign('filter',       $order_list['filter']);
	$smarty->assign('record_count', $order_list['record_count']);
	$smarty->assign('page_count',   $order_list['page_count']);
	$smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');

	/* 显示模板 */
	assign_query_info();
	$smarty->display('code_order_list.htm');
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
	make_json_result($smarty->fetch('code_order_list.htm'), '', array('filter' => $order_list['filter'], 'page_count' => $order_list['page_count']));
}
elseif($_REQUEST['act'] == 'resend')
{
	$orderid = $_REQUEST['id'];
	//获取订单信息
	$order_sql = "SELECT * FROM ".$ecs->table('code_order')." WHERE id = ".$orderid;
	$order_info = $db -> getRow($order_sql);

	$code_sql = "SELECT c.*,s.supplier_name,m.content FROM ".$ecs->table('code')." AS c , ".$ecs->table('supplier')." AS s LEFT JOIN ".$ecs->table('supplier_message')." AS m ON s.supplier_id = m.supplier_id WHERE c.supplier_id = s.supplier_id AND c.id IN(".$order_info['code_id'].")";
	$code_info = $db -> getAll($code_sql);

	//支付成功短信发放电子码
	$userInfo = $db -> getRow("SELECT nickname FROM ".$ecs->table('users')." WHERE user_name = ".$order_info['user_name']);
	$msgInfo = array(
		'nickname'=>$userInfo['nickname'],
		'mobile'=>$order_info['mobile']
	);
	require(ROOT_PATH . 'includes/lib_smsvrerify.php');
	$Smsvrerify = new smsvrerifyApi();
	$error = 0;
	foreach($code_info as $code){
		$code['content'] = empty($code['content'])?'尊敬的聚优客户您好，您在我司官网订购的{$supplier_name}电子码券号：{$account}密码：{$password}请持电子码到合作的门店使用，谢谢！':$code['content'];
		$msgInfo = array_merge($msgInfo,$code);
		$smarty->assign($msgInfo);
		$message = $smarty->fetch("str:" . $code['content']);
		$result = $Smsvrerify->smsvrerify($msgInfo['mobile'],$message,'','聚优福利');
		if($result != 0)
			$error ++;
	}
	//修改商品码信息为已发送
	if($error == 0)
		$db->query("UPDATE ".$ecs->table('code_order')." SET send_msg = 1 WHERE id = ".$orderid);

	$ajaxArray['error'] = 0;
	die(json_encode($ajaxArray));
}
//订单批量删除操作
//elseif ($_REQUEST['act'] == 'operate'){
//	$batch          = isset($_REQUEST['batch']); // 是否批处理
//	$order_sn   = $_REQUEST['order_id'];
//	$order_sn_list = explode(',', $order_sn);
//	if (isset($_POST['remove'])){
//		foreach ($order_sn_list as $sn_order){
//			/* 删除订单 */
//			$db->query("DELETE FROM ".$ecs->table('dzq_order'). " WHERE order_sn = '$sn_order'");
//			$sn_list[] = $sn_order;
//		}
//
//		$sn_list = join($sn_list, ',');
//		$msg = $sn_list.'删除成功';
//		$links[] = array('text' => $_LANG['return_list'], 'href' => 'dzqdh_order.php?act=list');
//		sys_msg($msg, 0, $links);
//	}
//}
//
//elseif ($_REQUEST['act'] == 'remove_order'){
//
//	$order_id = intval($_REQUEST['id']);
//
//	$GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table('dzq_order'). " WHERE order_id = '$order_id'");
//	if ($GLOBALS['db'] ->errno() == 0)
//	{
//		$url = 'dzqdh_order.php?act=list';
//		ecs_header("Location: $url\n");
//		exit;
//	}
//	else
//	{
//		sys_msg('删除失败');
//	}
//}
//
//else if ($_REQUEST['act'] == 'shippingStatus'){
//	$order_id = intval($_REQUEST['id']);
//	$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('dzq_order'). " SET shipping_status = 2, shipping_time = '".gmtime()."' WHERE order_id = '$order_id'");
//	if ($GLOBALS['db'] ->errno() == 0)
//	{
//		$url = 'dzqdh_order.php?act=list';
//		ecs_header("Location: $url\n");
//		exit;
//	}
//	else
//	{
//		sys_msg('设置失败');
//	}
//}


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
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('dzq_order') . " AS o ,".
				   $GLOBALS['ecs']->table('users') . " AS u " . $where;
		}
		else
		{
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('dzq_order') . " AS o ". $where;
		}

		$filter['record_count']   = $GLOBALS['db']->getOne($sql);
		$filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

		/* 查询 */
		$sql = "SELECT o.id, o.order_sn, o.add_time, o.order_status, o.order_amount, o.money_paid," .
					" o.mobile, o.goods_amount, o.price, o.sjprice, o.goods_name, o.goods_number, o.user_name,o.goods_attr,o.send_msg" .
				" FROM " . $GLOBALS['ecs']->table('code_order') . " AS o " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=o.user_id ". $where .
				" ORDER BY ".$filter['sort_by']." ".$filter['sort_order'].
				" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",".$filter['page_size'];

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
			$row[$key]['order_status_cn'] = '未付款';
		}elseif($value['order_status'] == 2){
			$row[$key]['order_status_cn'] = '已取消';
		}elseif($value['order_status'] ==3&&$value['send_msg'] == 0){
			$row[$key]['order_status_cn'] = '已付款，未发送短信';
		}elseif($value['order_status'] ==3&&$value['send_msg'] == 1){
			$row[$key]['order_status_cn'] = '已付款，已发送短信';
		}

		$row[$key]['formated_order_amount'] = price_format($value['order_amount']);
		$row[$key]['formated_goods_amount'] = price_format($value['goods_amount']);
		$row[$key]['formated_money_paid']   = price_format($value['money_paid']);
		$row[$key]['short_order_time']      = local_date('Y-m-d H:i', $value['add_time']);
		$row[$key]['formated_price']        = price_format($value['price']);
		$row[$key]['price']        = price_format($value['price']);

		if ($value['order_status'] == OS_CANCELED){
			$row[$key]['can_remove'] = 1;
		}else{
			$row[$key]['can_remove'] = 0;
		}
	}
	$arr = array('orders' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

	return $arr;
}