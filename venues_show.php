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

$customRatio = get_card_rule_ratio(10003);

$venueId = isset($_REQUEST['venueId']) ? intval($_REQUEST['venueId']) : 0 ;
$infoId = isset($_REQUEST['infoId']) ? intval($_REQUEST['infoId']) : 0 ;
$orderDate = isset($_REQUEST['orderDate']) ? $_REQUEST['orderDate'] : 0 ;

if ( empty($infoId) || empty($orderDate) || empty($venueId))
{
    show_message('缺少参数！');
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

// 场馆开放时间
$detail['stime'] = initHours( $detail['stime'] );
// 场馆闭馆时间
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
            if ($venue['date'] == $orderDate)
                $venue['active'] = 1;
            else 
                $venue['active'] = 0;
            
            $venueData[] = $venue;
        }
    }
}

// 产品时间段
$venue = getDongSite( 'venuePrice', array('infoId' => $infoId, 'orderDate'=>$orderDate));
// 场地和时间数据
$venuesList = $timeList = array();
// 场地长度
$width = 102;

if ($venue['code'] == 0)
{    
    $venues = $venue['body'];
    // 时间段处理，
    for ($i=$venues['sTime']; $i<=$venues['eTime']; $i++)
    {
        $timeList[] = initHours($i);
    }
  
    // 场地时间段数量不够的话，填充相差的时间段为不可选的时间段
    if ( count($timeList) > count($venues['priceData']))
    {
        $minValue = getValue( $venues['priceData'],'time', 'min');
        $maxValue = getValue( $venues['priceData'],'time', 'max');     
       
        if ($minValue > $venues['sTime'] && $minValue !=0 )
        {
            for ($s=$venues['sTime']; $s<$minValue; $s++)
            {
                $venues['priceData'] = array_merge($venues['priceData'], array( array('num'=>0, 'time'=> $s) ));
            }
        }
        
        if ($maxValue < $venues['eTime'] && $maxValue !=0 )
        {
            for ($s=$maxValue; $s<$venues['eTime']; $s++)
            {
                $venues['priceData'] = array_merge($venues['priceData'], array( array('num'=>0, 'time'=> $s) ));
            }
        }      
        
        // 如果是空数据的话
        if ($minValue == 0 && $maxValue == 0)
        {
            for ($s=$venues['sTime']; $s<=$venues['eTime']; $s++)
            {
                $venues['priceData'] = array_merge($venues['priceData'], array( array('num'=>0, 'time'=> $s) ));
            }
        }
    }   
    // 按时间排序
    $timeValue= array();
    foreach ($venues['priceData'] as $key=>$value)
    {
        $timeValue[$key]['time'] = $value['time'];        
    }
    array_multisort($timeValue, SORT_ASC, $venues['priceData']);
    
    // 场地处理
    for ($v=1; $v<=$venues['venueCount']; $v++)
    {
        $tmpPriceData = array();
        foreach ($venues['priceData'] as $pk=>$pd)
        {
            $tmpPriceData[$pk]['num'] = $pd['num'] >= $v ? 1: 0 ; 
            $tmpPriceData[$pk]['sTime'] = $pd['time'];
            $tmpPriceData[$pk]['eTime'] = $pd['time']+1;
            $tmpPriceData[$pk]['sTime_mt'] = initHours($pd['time']);
            $tmpPriceData[$pk]['eTime_mt'] = initHours($pd['time']+1);
            $tmpPriceData[$pk]['salePrice'] = !empty($pd['salePrice']) ? initSalePrice($pd['salePrice'], $customRatio) : 0 ;
        }
        
        $venuesList[$v]['list'] = $tmpPriceData;
        $venuesList[$v]['rows'] = $v;
    }
    
    $width = count($timeList)*51+$width;
}



// 效验吗
$secret = mt_rand().$venueId.$infoId;
$smarty->assign('secret', $secret);
$smarty->assign('width', $width);
$smarty->assign('date', $orderDate);
$smarty->assign('venueId', $venueId);
$smarty->assign('infoId', $infoId);
$smarty->assign('priceData', $venuesList);
$smarty->assign('timeData', $timeList);
$smarty->assign('detail', $detail);
$smarty->assign('ticket', $ticketData);
$smarty->assign('venues', $venueData);

$smarty->display('venues/venuesShow.dwt');
?>