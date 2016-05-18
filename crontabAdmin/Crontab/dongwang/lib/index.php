<?php
/**
 * 动网场地数据更新
 * @var unknown_type
 */
define('IN_ECS', true);

include_once(dirname(__FILE__) . '/lib/_init.php');
include_once(dirname(__FILE__) . '/lib/function.php');

include_once(ROOT_PATH . 'includes/lib_cardApi.php');
error_reporting(0);

set_time_limit(0);
/* 产品类型  */
defined('TYPE_TICKET') or define('TYPE_TICKET', 1);	        // 門票
defined('TYPE_VENUES') or define('TYPE_VENUES', 0);			// 場地 

/* 动网支持的城市 */
$region = findData('region','dongwang_id > 0');
foreach ($region as $regs)
{
    $topId = 0;
    if ($regs['parent_id'] != 0)
    {
        $tops = findData('region','region_id ='.$regs['parent_id']);
        $topId = (int) $tops[0]['dongwang_id'];
    }       
   
    // api 场馆数据
    $venues = getDongSite('list', array('areaId'=>$regs['dongwang_id'], 'format'=>'json', 'pageNum'=>2000));
    // data 数据
    $dataVenues = findData('venues', 'cityId = '.$regs['dongwang_id']);
    // $dataVenues数据id 和 $venues 数据id
    $dataVenueIds = $apiVenueIds = array();
    
    foreach ($dataVenues as $data)
    {
        $dataVenueIds[] = $data['venueId']; 
    }
    foreach ((array)$venues['body'] as $api)
    {
        $apiVenueIds[] = $api['venueId'];
    }           
    
    $venuesData = array();
    // 处理单个场馆详细数据
    foreach ((array)$venues['body'] as $key=>$venue)
    { 
        // 当前城市是一级城市，就将cityId 保存到 cityId字段了，否则放到area_id里
        if ($topId !=0)
        {
            $venue['area_id'] = $venue['cityId'];
            $venue['cityId'] = $topId;
        }
        else {
            $venue['area_id'] = getAreaId($regs['region_id'],$venue['place']);    
        }
        $venuesData[$key] = $venue;
        
        // 场馆产品查询
        $priceList = getDongSite('priceList', array('id'=>$venue['venueId']));
        $dataPrice = findData('venues_ticket', 'venueId='.$venue['venueId']);
      
        // 如果产品已经下架，设置场馆为下架状态
        if ($priceList['code'] == 1)
        {
            $venuesData[$key]['is_sale'] = 1;
        }
        else {
            $venuesData[$key]['is_sale'] = 0;
        }
        
        $ticketData = isset($priceList['body']['ticketData']) ? $priceList['body']['ticketData'] : array() ;
        $venueData = isset($priceList['body']['venueData']) ? $priceList['body']['venueData'] : array();
        
        // 保存的字段 
        $cols = array(
            'infoId'        => '',
            'infoTitle'     => '',
            'isConfirm'     => 0,
            'marketPrice'   => '', 
            'salePrice'     => 0, 
            'ticketDesc'    => '', 
            'date'          => '',
            'venueNum'      => 0,
            'week'          =>'',
            'type'          => 0,
            'venueId'       => $venue['venueId']           
        );
        
       
        // 如果数据库中没有当前场馆的产品，就添加
        if (empty($dataPrice))
        {
            if (!empty($ticketData))
            {
                $_ticketData = array();
                foreach ($ticketData as $ticketKey=>$ticketRow)
                {
                    $_ticketData[$ticketKey] = array_merge($cols, $ticketRow);
                    $_ticketData[$ticketKey] ['type'] = TYPE_TICKET;                    
                }  
                saveVenueData( $_ticketData );
                $venuesData[$key]['is_ticket'] = 1;
            }
            else {
                $venuesData[$key]['is_ticket'] = 0;
            }
            if (!empty($venueData))
            {
                $_venueData = array();
                foreach ($venueData['weeksData'] as $keys=>$week)
                {
                    $_venueData[$keys] = array_merge($cols, $week);
                    $_venueData[$keys]['type'] = TYPE_VENUES;
                    $_venueData[$keys]['infoId'] = $venueData['infoId'];
                    $_venueData[$keys]['isConfirm'] = $venueData['isConfirm'];
                }
                saveVenueData($_venueData);
                $venuesData[$key]['is_venue'] = 1;
            }
            else {
                $venuesData[$key]['is_venue'] = 0;
            }
        }        
        
        // 有产品的情况下，更新产品信息
        else {
            
            // 场地数据  、 门票数据（数据库）
            $venuess = $ticket = array();
            foreach ($dataPrice as $price)
            {
                 if ($price['type'] == TYPE_TICKET)
                     $ticket[] = $price;
                 else 
                     $venuess[] = $price;
            }
            // 处理门票数据
            if (!empty($ticketData))
            {               
               // 数据库里面的infoid 
               $dataTicket = getIds($ticket, 'infoId');
               // 接口过来的门票数据的infoId              
               $apiTicket = getIds($ticketData, 'infoId');               
               // 得到新增的产品
               $updateTicket = array_diff($apiTicket, $dataTicket);
               // 得到已经删除的产品
               $deleteTicket = array_diff($dataTicket, $apiTicket);
               
               // 如果有新增的产品，执行新增操作
               if ( !empty($updateTicket))
               {
                   $__ticketData = array();
                   foreach ($ticketData as $ticketKey=>$ticketRow)
                   {
                       $__ticketData[$ticketKey] = array_merge($cols, $ticketRow);
                       $__ticketData[$ticketKey] ['type'] = TYPE_TICKET;
                   }
                   updateRows($__ticketData, $updateTicket, 'infoId', 'saveVenueData');
               }
               // 如果还有删除的产品，执行删除操作
               if ( !empty($deleteTicket))
               {
                   dropRows( $deleteTicket, 'infoId', 'venues_ticket');
               }
               
               // 去掉新增的产品，剩下的产品更新价格和星期信息。               
               $editData = initDUData($ticketData, $updateTicket, 'infoId');
               // 更新数据
               if ( !empty($editData) )
               {
                   editRows($editData, array('salePrice', 'isConfirm'), 'venues_ticket', array('infoId'));
               }
               // 设置场馆支持的产品类型，门票、场地
               // 如果有更新的产品，或新增的产品，只要一个条件成立就说明该场馆有门票在售
               if ( !empty($updateTicket) || !empty($editData))
               {
                    $venuesData[$key]['is_ticket'] = 1;
               }
               else {
                    $venuesData[$key]['is_ticket'] = 0;
               }
            }
            else 
            {
                $venuesData[$key]['is_ticket'] = 0;
            }
            
            // 处理场地数据
            if (!empty($venueData)){
                
                // 数据库里面的date
                $dataVenue10 = getIds($venuess, 'date');
                // 接口过来的门票数据的date
                $apiVenue10 = getIds($venueData['weeksData'], 'date');
                // 得到新增的产品
                $updateVenue10 = array_diff($apiVenue10, $dataVenue10);
                // 得到已经删除的产品
                $deleteVenue10 = array_diff($dataVenue10, $apiVenue10);

                
                // 如果有新增的产品，执行新增操作
                if ( !empty($updateVenue10))
                {
                    $__venueDate = array();
                    foreach ($venueData['weeksData'] as $venueKey=>$venueRow)
                    {
                        $__venueDate[$venueKey] = array_merge($cols, $venueRow);
                        $__venueDate[$venueKey] ['type'] = TYPE_VENUES;
                        $__venueDate[$venueKey]['infoId'] = $venueData['infoId'];
                        $__venueDate[$venueKey]['isConfirm'] = $venueData['isConfirm'];
                    }
                    updateRows($__venueDate, $updateVenue10, 'date', 'saveVenueData');
                }
                
                // 如果还有删除的产品，执行删除操作
                if ( !empty($deleteVenue10))
                {
                    dropRows( $deleteVenue10, 'date', 'venues_ticket', ' venueId='.$venue['venueId']);
                }
                // 去掉新增的产品，剩下的产品更新价格和星期信息。
                $editData10 = initDUData($venueData['weeksData'], $updateVenue10, 'date');                
                // 把isConfirm 和 infoId 加入到$wditDate10中;
                initVenues($editData10, array('infoId'=>$venueData['infoId'], 'isConfirm'=>$venueData['isConfirm']));
                // 更新数据 
                if ( !empty($editData10) )
                {
                    editRows($editData10, array('salePrice', 'isConfirm', 'week'), 'venues_ticket', array('date', 'infoId'));
                }
                // 设置场馆支持的产品类型，门票、场地
                // 如果有更新的产品，或新增的产品，只要一个条件成立就说明该场馆有门票在售
                if ( !empty($updateVenue10) || !empty($editData10))
                {
                    $venuesData[$key]['is_venue'] = 1;
                }
                else {
                    $venuesData[$key]['is_venue'] = 0;
                }                
            }
            else 
            {
                $venuesData[$key]['is_venue'] = 0;
            }
        }     
    }
    
    // 新增的场馆  / 删除的场馆
    $insertVenue = $deleteVenue = array();
   
    $insertVenue = array_diff($apiVenueIds, $dataVenueIds);
	$deleteVenue = array_diff($dataVenueIds, $apiVenueIds);
	
	// 处理删除的场馆
	if ( !empty($deleteVenue))
	{
    	foreach ($deleteVenue as $did)
    	{
    	    drop('venues', 'venueId='.$did);
    	}
	}
	// 处理新增的场馆
	if ( !empty($insertVenue))
	{
    	$apiArray = array();
    	foreach ((array)$venuesData as $row)
    	{
    	    if (in_array( $row['venueId'], $insertVenue))
    	    {
    	        $apiArray[] = $row;
    	    }
    	}
    	if ( !empty($apiArray) )
    	{
    	    saveData($apiArray);
    	}
	}
	
	// 更新场馆信息
	$editVenue = initDUData($venuesData, $insertVenue, 'venueId');
	
	if ( !empty($editVenue) )
	{
	    editRows($editVenue, array('is_ticket', 'is_venue', 'salePrice', 'is_sale', 'area_id'), 'venues', array('venueId'));
	}
	sleep(3);
}

