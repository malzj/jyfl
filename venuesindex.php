<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_basic.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_dongsport.php');

assign_template();

$dongwang_id = getAreaNo(0,'venues');

$totalData = array();
// 统计每个类型下的场馆数量，场地数量，门票数量

$type = findData('venues_type','is_show=1');
$data = findData('venues',"cityId=$dongwang_id AND is_sale = 0");
foreach ( (array)$type as $key=>$val)
{
    $totalData[$val['code']] = array( 'venueTotal'=>0 , 'ticketTotal'=>0, 'venuesTotal'=>0); // 场地数量、门票数量、场馆数量
    foreach ($data as $k=>$v)
    {
        if($v['sportType'] == $val['code'])
        {
            // 场馆总数量
            $totalData[$val['code']]['venuesTotal'] +=1;
            // 场地总数量
            $totalData[$val['code']]['venueTotal'] += $v['totalVenue'];
            // 门票总数量
            $totalData[$val['code']]['ticketTotal'] += $v['totalTicket'];
        }
    }
}

$smarty->assign('data', $totalData);
$smarty->display('venues/venuesIndex.dwt');

?>