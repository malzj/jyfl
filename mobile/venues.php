<?php

define('IN_ECS', true);
/** 显示类型
 *  ALL     （门票和场地都显示）
 *  TICKET  （只显示门票）
 *  VENUES  （只显示场地）
 */
define('SHOW_TYPE', 'ALL');
require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_venues.php');
include_once(ROOT_PATH . 'includes/lib_basic.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_dongsport.php');

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

set_card_rules();

// 分页
$int_page     = (isset($_GET['page']) && $_GET['page'] > 1) ? intval($_GET['page']) : 1;
$int_pageSize = 20;
//$int_pageSize = 1000;
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
$pager = get_wap_pager($count, $int_pageSize, $int_page, 'venues.php');

$jsonArray['data'] = array(
    'typeId'=>$type,
    'areaId'=>$area,
    'area_list'=>$areaList,
    'venues_type'=>$venues,
    'list'=>$data,
    'page'=>$int_page,
    'pager'=>$pager,
    'keyword'=>$keyWord
);
JsonpEncode($jsonArray);

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

 









