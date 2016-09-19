<?php

define('IN_ECS', true);

define('SHOW_TYPE', 'ALL');

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_huanlegu.php');

if ((DEBUG_MODE & 2) != 2)
{
	$smarty->caching = false;
}

assign_template();

if (!isset($_REQUEST['step']))
{
    $_REQUEST['step'] = "show";
}

if ($_REQUEST['step'] == 'show')
{
    // 景区id
    $sceneryId = isset($_REQUEST['sceneryid']) ? $_REQUEST['sceneryid'] : 0;
    $goodsId = isset($_REQUEST['goodsid']) ? $_REQUEST['goodsid'] : 0;
    if (empty($sceneryId))
    {
        show_message('没有选择景区');
    }
    
    if (empty($goodsId))
    {
        show_message('没有选择产品');
    }
    
    // 有哪些产品在当前城市售卖
    $actionScenery = actionScenery();
    if (array_key_exists($int_cityId, $actionScenery) )
    {
        if (!in_array($goodsId, $actionScenery[$int_cityId]))
        {
            show_message('此产品不可购买！');
        }
    }
    
    // 景区产品
    $sceneryGoods = sceneryGoods($sceneryId);
    $good = array();
    if (count($sceneryGoods) == 1)
    {
        $good = $sceneryGoods[0];
    }
    else {
        foreach ($sceneryGoods as $goods)
        {
            if ($goods['GoodsId'] == $goodsId)
            {
                $good = $goods;
            }
        }
    }
    
    $smarty->assign('goods', $good);
    $smarty->assign('secret', mt_rand());
    $position = assign_ur_here(0,       '生活服务 <code>&gt;</code> 欢乐谷');
    $smarty->assign('page_title',       '欢乐谷_生活服务_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
    
    
    $smarty->display('huanlegu/huanlegu_goods_show.dwt');
}
// ajax 价格日历
else if($_REQUEST['step'] == 'price'){
    // 引入日历类（只适合动网）
    include_once(ROOT_PATH . 'includes/calendar.php');

    $date = date('Y-m-d',strtotime(local_date('Y-m-d')));

    // 日期处理
    if (!empty($_REQUEST['date']))
    {
        $times = explode('-',$_REQUEST['date']);
        $month = $times[1];
        $year  = $times[0];
        if ($month > 12)
        {
            $floor 	 	= floor($month/12);
            $newYear 	= $year+$floor;
            $newMonth	= $month-($floor*12);
            if ($newMonth == 0){
                $newMonth = 12;
            }
            if ($newMonth < 10){
                $newMonth = '0'.$newMonth;
            }

            $date 	= $newYear.'-'.$newMonth.'-01';
        }
        else{
            if ($month < 10){
                $times[1] = '0'.$times[1];
            }
            $date   = $times[0].'-'.$times[1].'-01';
        }
    }
    
    $price = get_price($_REQUEST['ScenicId'], $_REQUEST['GoodsId'], $date);
    
    // 日历价格
    $calendarPrice = array();
    
    if (isset($price))
    {
        foreach($price as  $datak=>$pe){
            $priceDay = explode('-',$pe['date']);
            $calendarPrice[$priceDay[2]] = intval($pe['salePrice']);
        }
    }

    $date = explode('-',$date);

    $html = new calendar($date[0], $date[1], $calendarPrice);
    echo $html->style();
}


// 
function get_price($scenicId, $goodsId, $date){
   
    $goods = array();
    $sceneryGoods = sceneryGoods($scenicId);
    foreach ($sceneryGoods as $good)
    {
        if ($good['GoodsId'] == $goodsId)
        {
            $goods = $good;
        }
    }   
    // 商品可购买的开始时间
    $startTime = strtotime($goods['ValidityBuyStart']);
    // 商品可购买的结束时间
    $endTime = strtotime($goods['ValidityBuyEnd']);
    // 可提前下单的最大天数
    //$maxDay = $goods['MaxAdvanceDays'];
    $maxDay = '';
    // 售价
    $showPrice = $goods['showPrice'];
    // 传过来的天 / 年 / 月
    $postDay = date('d',strtotime($date));
    $postYear = date('Y',strtotime($date));
    $postMonth = date('m',strtotime($date));
    // 当前的 天 / 年 / 月
    $currentDay = date('d',local_gettime());
    $currentYear = date('Y',local_gettime());
    $currentMonth = date('m',local_gettime());
    // 如果传过来的是当月日期，起始天数是当前日期到月末
    if ($currentMonth == $postMonth && $currentYear == $postYear)
    {
        $indexStart = $currentDay+1;
        $indexEnd = date('t',local_gettime());
    }
    // 不是本月，则起始天数是，1 到 月末
    else {
        $indexStart = $postDay;
        $indexEnd = date('t',strtotime($date));
    }
    
    // 如果传过来日期小于当前日期，返回空
    if (local_strtotime($postYear.'-'.$postMonth.'-01') < local_strtotime($currentYear.'-'.$currentMonth.'-01'))
    {
        return  array();
    }
    
    $returnArray = array();
    
    for ($i=$indexStart; $i<=$indexEnd; $i++)
    {
        $thisDay = $i;
        if (strlen($thisDay) == 1)
        {
            $thisDay = '0'.$thisDay;
        }
        // 拼接日期
        $priceDate = $postYear.'-'.$postMonth.'-'.$thisDay;         
        
        $returnArray[$i]=array(
                'id'        => $i,
                'date'      => $priceDate,
                'salePrice' => $showPrice,
                'remainNum' => 999,
                'startNum'  => NULL,
        );
    }
    
    // 过滤掉提前预订的天数
    if ( !empty($maxDay) )
    {
        $maxDayStrtotime = strtotime('+'.$maxDay.' day', local_gettime());
        foreach ($returnArray as $key=>$val)
        {
            $riliTime = strtotime($val['date']);
            if ($riliTime < $maxDayStrtotime)
            {
                unset($returnArray[$key]);
            }
        }
    }
    // 过滤掉不销售的时间
    foreach ($returnArray as $k=>$v)
    {
        $maxDayStrtotime = strtotime($v['date']);
        if ($maxDayStrtotime > $endTime || $maxDayStrtotime < $startTime)
        {
            unset($returnArray[$k]);
        }
    } 
    // 过滤掉特殊日 、
     $specialTime = array();
     $validitySpecialDay = json_decode($goods['GoodsTimeInfo']['ValiditySpecialDay'], true);
     $specialTime = searchData($validitySpecialDay, strtotime($date));
     if (!empty($specialTime))
     {
         foreach ($returnArray as $key=>$val)
         {
             $riliTime = strtotime($val['date']);
             if ( in_array(date('z',$riliTime), $specialTime) )
             {
                 unset($returnArray[$key]);
             }
         }     
     }
     // 有效周次判断
     $weekTime = $goods['GoodsTimeInfo']['ValidityWeekDay'];
     if (!empty($weekTime))
     {
         $weekTimes = explode(',', $weekTime);
         foreach ($returnArray as $key=>$val)
         {
             $riliTime = strtotime($val['date']);
             if ( !in_array(date('N',$riliTime), $weekTimes) )
             {
                 unset($returnArray[$key]);
             }
         }
     }
    
    return $returnArray;
}





 









