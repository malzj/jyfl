<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_dongsport.php');

if ((DEBUG_MODE & 2) != 2)
{
	$smarty->caching = false;
}

assign_template();

// 分页
$int_page     = (isset($_GET['page']) && $_GET['page'] > 1) ? intval($_GET['page']) : 1;
$int_pageSize = 10;
$int_start    = ($int_page - 1) * $int_pageSize;

$keyWord = !empty($_GET['keywords']) ? addslashes_deep($_GET['keywords']) : null;       // 关键字
$type = !empty($_GET['type']) ? addslashes_deep($_GET['type']) : null;          // 场馆项目 
$area = !empty($_GET['area']) ? intval($_GET['area']) : null;                   // 城区
// 接口对应的城市id
$dongwang_id = getAreaNo(0,'venues');
// 分页参数
$pageParam = array();

// 项目数据
$venues = venuesType();
$typeids = array();
foreach ($venues as &$venue)
{
    if ($type == $venue['code'])
        $venue['active'] = 1;
    else
        $venue['active'] = 0;
    
    $typeids[] = $venue['code'];
}

// 城区数据
$areaList = areaList(1, $_SESSION['cityid']);
foreach ($areaList as &$alist)
{
    if ($area == $alist['dongwang_id'])
        $alist['active'] = 1;
    else
        $alist['active'] = 0;
}


// where 条件
$where = ' WHERE cityId = '.$dongwang_id;

// 项目筛选
if ($type != null)
{
    $where .= ' AND sportType = "'.$type.'"';
    $pageParam = array_merge($pageParam, array('type'=>$type));
}
else {
    $where .= ' AND sportType IN("'.implode('","', $typeids).'")';
}
// 城区筛选
if ($area != null)
{
    $where .= ' AND area_id = '.$area;
    $pageParam = array_merge($pageParam, array('area'=>$area));
}   
// 城区筛选
if ($keyWord != null)
{
    $where .= ' AND venueName like "%'.$keyWord.'%" ';
    $pageParam = array_merge($pageParam, array('venueName'=>$keyWord));
}
// 数据类型显示
switch (SHOW_TYPE)
{
    case 'TICKET':  $where.=" AND is_ticket = 1 ";   break;
    case 'VENUES':  $where.=" AND is_venue = 1 ";   break;
    case 'ALL':     $where.=" AND (is_venue <> 0 OR is_ticket <> 0) ";   break;
}
// 上架
$where.= ' AND is_sale = 0';


// 统计数据
$count = get_venues_count($where);
$data  = get_venues($where, $int_start, $int_pageSize);
$pager = get_pager('venues.php', $pageParam, $count, $int_page, $int_pageSize);

$smarty->assign('typeId', $type);
$smarty->assign('areaId', $area);
$smarty->assign('area_list', $areaList);
$smarty->assign('venues_type', $venues);
$smarty->assign('list', $data);
$smarty->assign('pager', $pager);
$smarty->display('venues/venuesList.dwt');

// 获得场馆项目
function venuesType()
{
    return $GLOBALS['db']->getAll( 'SELECT * FROM '.$GLOBALS['ecs']->table('venues_type'). ' WHERE is_show = 1');
}

function get_venues_count($where)
{
    return $GLOBALS['db']->getOne( 'SELECT count(*) FROM '.$GLOBALS['ecs']->table('venues'). ''.$where );
}

function get_venues($where, $start, $size)
{
    // 卡规则比例
    $customRatio = get_card_rule_ratio(10003);
    $return = array();
    $sql = "SELECT * FROM ".$GLOBALS['ecs']->table('venues')."". $where.' ORDER BY id DESC';
    $res = $GLOBALS['db']->selectLimit($sql, $size, $start);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        // 过滤掉没有门票也没有场地的场馆
        if ($row['is_ticket'] == 0 && $row['is_venue'] ==0 )
        {
            continue;
        }
                
        // 产品均价价格
        $row['salePrice'] = initSalePrice($row['salePrice'], $customRatio);                
        // 场馆信息
        $return[$row['venueId']]['venue'] = $row;
    }
    
    // 处理场馆所有的门票或场馆数据
    $venueIds = array_keys($return);
    if (!empty($venueIds))
    {
        $tsql = "SELECT * FROM ".$GLOBALS['ecs']->table('venues_ticket')." WHERE venueId IN(".implode(',', $venueIds).") order by id ASC";
        $tickets = $GLOBALS['db']->getAll($tsql); 
        if ( !empty($tickets) )
        {
            foreach ($tickets as $ticket)
            {
                // 价格处理
                $ticket['salePrice'] = initSalePrice( $ticket['salePrice'], $customRatio );
                if ($ticket['type'] == 1)
                {
                    $return[$ticket['venueId']]['venueTicket'][] = $ticket;
                }
                else {
                    $ticket['date_mt'] = local_date('m-d',local_strtotime($ticket['date']));
                    $return[$ticket['venueId']]['venueSite'][] = $ticket;
                }
            }
        }
    }
    return $return;
}

function areaList($type = 0, $parent = 0)
{
    $sql = 'SELECT region_id, region_name, dongwang_id FROM ' . $GLOBALS['ecs']->table('region') .
    " WHERE region_type = '$type' AND parent_id = '$parent' AND dongwang_id > 0";

    return $GLOBALS['db']->GetAll($sql);
}

 









