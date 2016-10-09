<?php 
define('IN_ECS', true);

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

$customRatio = get_card_rule_ratio(10003);


// 场馆id
$venueId = isset($_REQUEST['venueId']) ? intval($_REQUEST['venueId']) : 0 ;

if ( empty($venueId) )
{
    $jsonArray['state'] = 'false';
    $jsonArray['message'] = '无效的操作！';
    $jsonArray['data']['go'] = -1;
    JsonpEncode($jsonArray);
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

$jsonArray['data'] = array(
    'imgList'=>$imgList,
    'detail'=>$detail,
    'ticket'=>$ticketData,
    'venues'=>$venueData,
    'venueId'=>$venueId
);

JsonpEncode($jsonArray);
?>