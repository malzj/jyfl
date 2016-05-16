<?php 
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_dongsport.php');

$customRatio = get_card_rule_ratio(10003);

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = false;
}

assign_template();

// 场馆id
$venueId = isset($_REQUEST['venueId']) ? intval($_REQUEST['venueId']) : 0 ;

if ( empty($venueId) )
{
    show_message('无效的操作！');
}

// 场馆详情
$detail = F('dongwang_detail', '', 1800, 'venues/'.$venueId.'/');//缓存半小时
if(empty($detail))
{
    $apiData = getDongSite('detail', array('id'=>$venueId));    
    if ($apiData['code'] == 0)
        $detail = $apiData['body'];
    else 
        $detail = array();
    
    F('dongwang_detail', $detail, 1800, 'venues/'.$venueId.'/');//写入缓存
}

// 图片集处理
if ( !empty($detail['img']) )
{
    $imgList = explode(',', $detail['img']);
}
else {
    $imgList = array( $detail['signImg'] );
}

$detail['stime'] = initHours( $detail['stime'] ); 
$detail['etime'] = initHours( $detail['etime'] );
$smarty->assign('imgList',   $imgList);
$smarty->assign('detail',    $detail);

// 场馆产品
$apiPrice = F('dongwang_price', '', 1800, 'venues/'.$venueId.'/');//缓存半小时
if(empty($apiPrice))
{
    $apiData = getDongSite('priceList', array( 'id'=> $venueId));
    if ($apiData['code'] == 0)
        $apiPrice = $apiData['body'];
    else
        $apiPrice = array();

    F('dongwang_price', $apiPrice, 1800, 'venues/'.$venueId.'/');//写入缓存
}


$ticketData =  $venueData = array();
if ( !empty($apiPrice) )
{    
    if ( !empty($apiPrice['ticketData']) )
    {
        foreach ($apiPrice['ticketData'] as $ticket)
        {
            $ticket['salePrice'] = initSalePrice($ticket['salePrice'], $customRatio);
            $ticketData[] = $ticket;
        }
    }
    if ( !empty($apiPrice['venueData']['weeksData']) )
    {
        foreach ($apiPrice['venueData']['weeksData'] as $venue)
        {
            $venue['salePrice'] = initSalePrice($venue['salePrice'], $customRatio);
            $venue['isConfirm'] = $apiPrice['venueData']['isConfirm'];
            $venue['infoId']    = $apiPrice['venueData']['infoId'];
            $venue['date_mt']   = local_date('m-d',local_strtotime($venue['date']));
            $venueData[] = $venue;
        }
    }
}

$smarty->assign('ticket', $ticketData);
$smarty->assign('venues', $venueData);
$smarty->assign('venueId', $venueId);
$position = assign_ur_here(0,       ' 运动激情 <code>&gt;</code> 运动健身 <code>&gt;</code> '.$detail['venueName']);
$smarty->assign('page_title',       '运动健身_运动激情_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
$smarty->assign('ur_here',          $position['ur_here']);  // 当前位置

$smarty->display('venues_detail.dwt');
?>