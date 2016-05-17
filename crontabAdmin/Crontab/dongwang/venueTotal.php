<?php
/**
 * 统计场地数量，和门票数量
 * @var unknown_type
 */
define('IN_ECS', true);

include_once(dirname(__FILE__) . '/lib/_init.php');
include_once(dirname(__FILE__) . '/lib/function.php');

include_once(ROOT_PATH . 'includes/lib_cardApi.php');
error_reporting(0);

set_time_limit(0);

// 场馆的统计信息
$venuesTotal = array();
/* 动网所有产品信息 */
$venuesTicket = findData('venues_ticket');
foreach ($venuesTicket as $ticket)
{
    // 如果有数据，并且是门票，门票数加加
    if ( isset($venuesTotal[$ticket['venueId']]) && 
        !empty($venuesTotal[$ticket['venueId']]) && $ticket['type'] == 1)
    {
        $venuesTotal[$ticket['venueId']]['ticket'] +=1;
    }    
    // 如果没有数据，并且是门票，门票数量是 1
    else if( !isset($venuesTotal[$ticket['venueId']]) &&
        empty($venuesTotal[$ticket['venueId']]) && $ticket['type'] == 1)
    {
        $venuesTotal[$ticket['venueId']]['ticket'] = 1;
    }
    // 以上条件不成立，说明该产品是场地，设置场地时间 date
    else
    {
        $venuesTotal[$ticket['venueId']]['date'] = $ticket['date'];
        $venuesTotal[$ticket['venueId']]['infoId'] = $ticket['infoId'];
    }
	
}

// 先更新门票数量
foreach ($venuesTotal as $venueId=>$total)
{
    if ( isset($total['ticket']) )
       //update("totalTicket = ".$total['ticket']."" , "venueId = $venueId ", 'venues');   
    
    if ( !isset($total['date']))    
       unset($venuesTotal[$venueId]);    
}

// 每个场馆场地块数，单独抓取，保存
foreach ($venuesTotal as $key=>$val)
{
    $venue = getDongSite( 'venuePrice', array('infoId' => $val['infoId'], 'orderDate'=>$val['date']));

    if ($venue['code'] == 0)
    {        
        $totalVenue = $venue['body']['venueCount'];
        update("totalVenue = $totalVenue" , "venueId = $key ", 'venues');
    }
}

