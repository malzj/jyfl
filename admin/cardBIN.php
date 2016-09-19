<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/*------------------------------------------------------ */
//-- 订单列表
/*------------------------------------------------------ */

if ($_REQUEST['act'] == 'list'){
	$smarty->assign('ur_here', '卡BIN管理');
	$smarty->assign('action_link', array('href' => 'cardBIN.php?act=add', 'text' => '添加产品'));
	$smarty->assign('full_page',        1);
	include_once(ROOT_PATH . 'includes/lib_order.php');
	
	$order_list = order_list();
	$smarty->assign('order_list',   $order_list['orders']);
	$smarty->assign('filter',       $order_list['filter']);
	$smarty->assign('record_count', $order_list['record_count']);
	$smarty->assign('page_count',   $order_list['page_count']);
	$smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');

	/* 显示模板 */
	assign_query_info();
	$smarty->display('cardBIN_list.htm');
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
	make_json_result($smarty->fetch('cardBIN_list.htm'), '', array('filter' => $order_list['filter'], 'page_count' => $order_list['page_count']));
}
elseif($_REQUEST['act'] == 'add' || $_REQUEST['act'] == 'edit')
{

    $is_add = $_REQUEST['act'] == 'add';
    $smarty->assign('insert_or_update', $is_add ? 'insert' : 'update');

    if($is_add)
    {
        $data = array(
            'name' => '',
            'cardBin' => '',
            'ext'=>'',
            'cart_type'=>'',
            'card_ext' => '',
            'cordon_up' => '',
            'cordon_dwon' => '',
            'cordon_show' => ''
        );
    }
    else
    {
        $id = $_GET['id'];
        $data = get_info($id);
        $smarty->assign('ur_here',      $_LANG['tag_edit']);
    }

    $smarty->assign('data', $data);
    $smarty->assign('action_link', array('href' => 'cardBIN.php?act=list', 'text' => '卡BIN管理'));

    assign_query_info();
    $smarty->display('cardBIN_edit.htm');
}

/*------------------------------------------------------ */
//-- 鏇存柊
/*------------------------------------------------------ */

elseif($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update')
{    
    $is_insert = $_REQUEST['act'] == 'insert';

    $name = empty($_POST['name']) ? '' : trim($_POST['name']);
    $cardBin = empty($_POST['cardBin']) ? '' : trim($_POST['cardBin']);
    $ext = empty($_POST['ext']) ? '' : trim($_POST['ext']);
    $card_type = empty($_POST['card_type']) ? '' : trim($_POST['card_type']);  
    $card_ext = empty($_POST['card_ext']) ? '' : trim($_POST['card_ext']);
    $cordon_up = empty($_POST['cordon_up']) ? '' : trim($_POST['cordon_up']);
    $cordon_dwon = empty($_POST['cordon_dwon']) ? '' : trim($_POST['cordon_dwon']);
    $cordon_show = empty($_POST['cordon_show']) ? '' : trim($_POST['cordon_show']);

    $id = intval($_POST['id']);


    if($is_insert)
    {
        $sql = 'INSERT INTO ' . $ecs->table('cardBIN') . '(name, cardBin, ext, card_type,card_ext,cordon_up,cordon_dwon,cordon_show)' .
            " VALUES('$name', '$cardBin', '$ext', '$card_type','$card_ext','$cordon_up','$cordon_dwon','$cordon_show')";
        $db->query($sql);

        $link[0]['text'] = '卡BIN管理';
        $link[0]['href'] = 'cardBIN.php?act=list';
        sys_msg('添加成功', 0, $link);
    }
    else
    {
       

        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('cardBIN') .
        " SET name = '$name',
        cardBin = '$cardBin',
        ext = '$ext',
        card_type = '$card_type',
        card_ext = '$card_ext',
        cordon_up = '$cordon_up',
        cordon_dwon = '$cordon_dwon',
        cordon_show = '$cordon_show'
        WHERE id = '$id'";
        $db->query($sql);

        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'cardBIN.php?act=list';

        sys_msg('修改成功', 0, $link);
    }
}

function order_list()
{
	$result = get_filter();
	if ($result === false){
		
		$filter['cardBIN'] = empty($_REQUEST['cardBIN']) ? '' : trim($_REQUEST['cardBIN']);

		
		$where = 'WHERE 1 ';
		
		if (!empty($filter['cardBIN']))
		{
		    $where .= " AND cardBIN = '$filter[cardBIN]'";
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
		$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('cardBIN') . $where;
		
		$filter['record_count']   = $GLOBALS['db']->getOne($sql);
		$filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

		/* 查询 */
		$sql = "SELECT * FROM " . $GLOBALS['ecs']->table('cardBIN') .' '. $where .
				" ORDER BY id DESC ".
				" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
	
		set_filter($filter, $sql);
	}
	else
	{
		$sql    = $result['sql'];
		$filter = $result['filter'];
	}

	$info = $GLOBALS['db']->getAll($sql);

	
	$arr = array('orders' => $info, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

	return $arr;
}

function get_info($id)
{
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('cardBIN') ." WHERE id = '$id'";
    $row = $GLOBALS['db']->getRow($sql);
    return $row;
}

