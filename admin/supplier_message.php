<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/8/19
 * Time: 15:06
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

require(ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/supplier.php');
$smarty->assign('lang', $_LANG);
/*
 * 供应商短信列表
 */
if($_REQUEST['act'] == 'list'){

    $smarty->assign('full_page',    1);
    $smarty->assign('ur_here',      $_LANG['06_supplier_message']);
    $smarty->assign('action_link', array('text'=>'新增信息', 'href'=>'supplier_message.php?act=add'));

    $list = message_list();

    $smarty->assign('message_list',    $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    assign_query_info();
    $smarty->display('supplier_message_list.htm');
}
/*
 * 新增信息
 */
elseif ($_REQUEST['act'] == 'add'||$_REQUEST['act'] == 'edit'){
    $supplier_list = get_supplier_list();
    $msg_info = array();

    if(!empty($_REQUEST['id'])){
        $msg_info = $db -> getRow("SELECT * FROM ".$ecs -> table('supplier_message')." WHERE id = ".intval($_REQUEST['id']));
    }
    $smarty->assign('msg_info',$msg_info);
    $smarty->assign('supplier_list',$supplier_list);
    $smarty->assign('action_link', array('text'=>$_LANG['go_back'], 'href'=>'supplier_message.php?act=list'));
    $smarty->display('supplier_add_message.htm');
}
/*
 * 保存短信
 */
elseif ($_REQUEST['act'] == 'save'){
    $id = empty($_POST['id'])?'':intval($_POST['id']);
    $data = array();
    $data['title'] = empty($_POST['title']) ? sys_msg("标题不能为空！",1) : trim($_POST['title']);
    $data['content'] = empty($_POST['content']) ? sys_msg("内容不能为空！",1)  : trim($_POST['content']);
    $data['supplier_id'] = empty($_POST['supplier_id']) ? sys_msg("请选择供应商！",1)  : trim($_POST['supplier_id']);
    $data['add_time'] = gmtime();
    if(empty($id)) {
        $sql = "INSERT INTO " . $ecs->table('supplier_message') . "(" . implode(',', array_keys($data)) . ") VALUE('" . implode("','", $data) . "')";
        $db->query($sql);
    }else{
        $str = '';
        foreach($data as $key=>$val){
            $str .= empty($str)?$key."='".$val."'":",".$key."='".$val."'";
        }
        $sql = "UPDATE ".$ecs->table('supplier_message')." SET ".$str." WHERE id = ".$id;
        $db->query($sql);
    }

    $link[] = array('text'=>$_LANG['go_back'], 'href'=>'supplier_message.php?act=list');
    sys_msg("添加信息成功！",0,$link);
}
/*删除信息*/
elseif ($_REQUEST['act'] == 'delete'){
    $id = intval($_REQUEST['id']);
    $sql = "DELETE FROM ".$ecs->table('supplier_message')." WHERE id = ".$id;
    $db -> query($sql);
    $url = 'supplier_message.php?act=query&' . str_replace('act=delete', '', $_SERVER['QUERY_STRING']);
    ecs_header("Location: $url\n");
    exit;
}
/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $list = message_list();

    $smarty->assign('message_list',    $list['item']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    make_json_result($smarty->fetch('supplier_message_list.htm'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

function message_list()
{
    $result = get_filter();
    if ($result === false){
        /* 过滤信息 */
//        $filter['search_type'] = empty($_REQUEST['search_type']) ? '' : trim($_REQUEST['search_type']);
//        $filter['keyword']     = empty($_REQUEST['keyword'])     ? 0 : trim($_REQUEST['keyword']);

        $filter['supplier_name'] = empty($_REQUEST['supplier_name']) ? '' : trim($_REQUEST['supplier_name']);
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = 'WHERE 1 ';
        if ($filter['supplier_name'])
        {
            $where .= " AND s.supplier_name LIKE '%" . mysql_like_quote($filter['supplier_name']) . "%'";
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
            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('supplier_message') . " AS sm LEFT JOIN".
                $GLOBALS['ecs']->table('supplier') . " AS s ON sm.supplier_id = s.supplier_id " . $where;
        }
        else
        {
            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('supplier_message') . $where;
        }

        $filter['record_count']   = $GLOBALS['db']->getOne($sql);
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

        /* 查询 */
        $sql = "SELECT sm.*,s.supplier_name FROM ".$GLOBALS['ecs']->table('supplier_message')." AS sm LEFT JOIN "
            .$GLOBALS['ecs']->table('supplier')." AS s ON sm.supplier_id = s.supplier_id ".$where.
            " ORDER BY ".$filter['sort_by']." ".$filter['sort_order'].
            " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",".$filter['page_size'];

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $row = $GLOBALS['db']->getAll($sql);

    $arr = array('item' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

/**
 * 取得供货商列表
 * @return array    二维数组
 */
function get_supplier_list()
{
    $sql = 'SELECT supplier_id,supplier_name
            FROM ' . $GLOBALS['ecs']->table('supplier') . ' ORDER BY supplier_id ASC';
    $res = $GLOBALS['db']->getAll($sql);

    if (!is_array($res))
    {
        $res = array();
    }

    return $res;
}