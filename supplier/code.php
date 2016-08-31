<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/8/18
 * Time: 11:39
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if($_REQUEST['act'] == 'code_list'){
    /* 检查权限 */

    if($_REQUEST['order_sn']){
        $_REQUEST['order_sn'] = '';
    }

    $smarty->assign('full_page',    1);
    $smarty->assign('lang',         $_LANG);
    $smarty->assign('ur_here',      $_LANG['54_code_list']);

    //获取供应商码列表
    $list = get_code_list();

    $smarty->assign('card_list',    $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('code_list.htm');

}
/*------------------------------------------------------ */
//-- 编辑补货信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_code')
{
    /* 检查权限 */
    /* 获取卡片信息 */
    $sql = "SELECT id, price, account, password, validity, supplier_id FROM ".
        $ecs->table('code')."WHERE id = '".$_REQUEST['id']."'";
    $card = $db->GetRow($sql);

    $smarty->assign('ur_here',     "编辑");
    $smarty->assign('action_link', array('text'=>$_LANG['go_list'], 'href'=>'virtual_card.php?act=card&goods_id='.$card['goods_id']));
    $smarty->assign('card',        $card);
    $smarty->display('code_info.htm');
}
elseif ($_REQUEST['act'] == 'action')
{
    /* 检查权限 */

    $_POST['account'] = trim($_POST['account']);
    $_POST['password'] = trim($_POST['password']);

    /* 在前后两次card_sn不一致时，检查是否有重复记录,一致时直接更新数据 */
    if ($_POST['account'] != $_POST['old_account'])
    {
        $sql = "SELECT count(*) FROM ".$ecs->table('code')." WHERE supplier_id='".$_POST['supplier_id']."' AND account='".$_POST['account']."'";

        if ($db->GetOne($sql) > 0)
        {
            $link[] = array('text'=>$_LANG['go_back'], 'href'=>'code.php?act=code_list');
            sys_msg(sprintf($_LANG['card_sn_exist'],$_POST['card_sn']),1,$link);
        }
    }

    /* 如果old_card_sn不存在则新加一条记录 */
    if (empty($_POST['old_account']))
    {
        sys_msg('更新失败',1,$link);
    }
    else
    {
        /* 更新数据 */
        $end_date = $_POST['validityYear'] . "-" . $_POST['validityMonth'] . "-" . $_POST['validityDay'];
        $sql = "UPDATE ".$ecs->table('code')." SET account='".$_POST['account']."', password='".$_POST['password']."', validity='$end_date' ".
            "WHERE id='".$_POST['id']."'";
        $db->query($sql);

        $link[] = array('text'=>$_LANG['go_list'], 'href'=>'code.php?act=code_list');
        sys_msg($_LANG['action_success'], 0, $link);
    }

}
/*------------------------------------------------------ */
//-- 虚拟卡列表，用于排序、翻页
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'query_card')
{
    $list = get_code_list();

    $smarty->assign('card_list',    $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('code_list.htm'), '',
        array('filter'=>$list['filter'], 'page_count'=>$list['page_count']));
}

/**
 * 返回商品码列表
 *
 * @return array
 */
function get_code_list()
{
    /* 查询条件 */
    $filter['supplier_id']    = empty($_REQUEST['supplier_id'])    ? 0 : intval($_REQUEST['supplier_id']);
    $filter['search_type'] = empty($_REQUEST['search_type']) ? 0 : trim($_REQUEST['search_type']);
    $filter['order_sn']    = empty($_REQUEST['order_sn'])    ? 0 : trim($_REQUEST['order_sn']);
    $filter['keyword']     = empty($_REQUEST['keyword'])     ? 0 : trim($_REQUEST['keyword']);
    if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
    {
        $filter['keyword'] = json_str_iconv($filter['keyword']);
    }
    $filter['sort_by']     = empty($_REQUEST['sort_by'])     ? 'id' : trim($_REQUEST['sort_by']);
    $filter['sort_order']  = empty($_REQUEST['sort_order'])  ? 'DESC' : trim($_REQUEST['sort_order']);

    $where  = " AND supplier_id = '" . $_SESSION['supplier_id'] . "' ";
    $where .= (!empty($filter['order_sn'])) ? " AND order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%' " : '';

    if (!empty($filter['keyword']))
    {
        if ($filter['search_type'] == 'card_sn')
        {
            $where .= " AND account = '" .$filter['keyword']. "'";
        }
        else
        {
            $where .= " AND order_sn LIKE '%" . mysql_like_quote($filter['keyword']). "%' ";
        }
    }

    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('code') . " WHERE 1 $where";
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);
    $start  = ($filter['page'] - 1) * $filter['page_size'];

    /* 查询 */
    $sql = "SELECT id AS card_id,account AS card_sn, supplier_id,password AS card_password,status AS is_saled, order_sn,validity AS end_date".
        " FROM ".$GLOBALS['ecs']->table('code').
        " WHERE 1 ".$where.
        " ORDER BY ".$filter['sort_by']." ".$filter['sort_order']." ".
        " LIMIT ".$start.", ".$filter['page_size'];
    $all = $GLOBALS['db']->getAll($sql);

//    $arr = array();
//    foreach ($all AS $key => $row)
//    {
//        if ($row['crc32'] == 0 || $row['crc32'] == crc32(AUTH_KEY))
//        {
//            $row['card_sn']       = decrypt($row['card_sn']);
//            $row['card_password'] = decrypt($row['card_password']);
//        }
//        elseif ($row['crc32'] == crc32(OLD_AUTH_KEY))
//        {
//            $row['card_sn']       = decrypt($row['card_sn'], OLD_AUTH_KEY);
//            $row['card_password'] = decrypt($row['card_password'], OLD_AUTH_KEY);
//        }
//        else
//        {
//            $row['card_sn']       = '***';
//            $row['card_password'] = '***';
//        }

//        $row['end_date'] = $row['end_date'] == 0 ? '' : date($GLOBALS['_CFG']['date_format'], $row['end_date']);

//        $arr[] = $row;
//    }

    return array('item' => $all, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}