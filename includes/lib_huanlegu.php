<?php 

// 有哪些产品在当前城市售卖
function actionScenery(){
    $actionScenery = array(
        // 当前城市 =》 景区id
        '1' => array( '100100001552', '100100001553', '100100001451' )
    );
    return $actionScenery;
}

// 价格处理
function initSalePrice( $price=0, $customRatio )
{   
    // 基础价比例
    $defaultRatio = '1';
    $newPrice = ($price-21) * $defaultRatio;
    if ($customRatio !== false)
        $returnPrice = ceil($newPrice*$customRatio);
    else
        $returnPrice = ceil($newPrice);

    return $returnPrice;
}

/**  
 *  得到产品信息
 */
 function sceneryGoods($sceneryId)
 {
     //卡规则折扣 
     // 999013的卡，销售比例是1.2，其他的卡是1
     $customRatio = 1;
     $startCardno = substr($_SESSION['user_name'], 0,6);
     
     $ext = current(findData('cardBIN','cardBin="'.$startCardno.'"'));
     
     if ( $ext['ext'] == 2 )
         $customRatio = 1.2;
     
     $sceneryGoods = F('scenery-goods-'.$sceneryId, '', 1800, 'huaqiaocheng/');//缓存半小时
     if (empty($sceneryGoods)){
         $arr_result = getHQCapi( array('GoodsId'=>'','PageIndex'=>'','PageSize'=>'','ScenicId'=>$sceneryId), 'goods');
     
         if ($arr_result['Count'] == 1)
             $sceneryGoods = array($arr_result['Items']['Goods']);
         else
             $sceneryGoods = (array)$arr_result['Items']['Goods'];
     
         foreach ($sceneryGoods as &$goods)
         {
             $goods['ScenicId']          = !empty($goods['ScenicId']) ? $goods['ScenicId'] : 0 ;
             $goods['GoodsId']           = !empty($goods['GoodsId']) ? $goods['GoodsId'] : 0 ;
             $goods['GoodsName']         = !empty($goods['GoodsName']) ? '北京欢乐谷-华影文化套票（含金面王朝演出、日场、夜场）（电子码换票、刷身份证直接入园）' : '北京欢乐谷-华影文化套票（含金面王朝演出、日场、夜场）（电子码换票、刷身份证直接入园）' ;
             $goods['Cover']             = !empty($goods['Cover']) ? $goods['Cover'] : 'http://smartoct.com:81/ugc/goods/201509/2015991717269.jpg' ;
             $goods['LimitCount']        = !empty($goods['LimitCount']) ? $goods['LimitCount'] : 0 ;
             $goods['DayMaxSellNum']     = !empty($goods['DayMaxSellNum']) ? $goods['DayMaxSellNum'] : 0 ;
             $goods['ValidityBuyStart']  = !empty($goods['ValidityBuyStart']) ? $goods['ValidityBuyStart'] : '' ;
             $goods['ValidityBuyEnd']    = !empty($goods['ValidityBuyEnd']) ? $goods['ValidityBuyEnd'] : '' ;
             $goods['CurrentDayLastBuyTime'] = !empty($goods['CurrentDayLastBuyTime']) ? $goods['CurrentDayLastBuyTime'] : '' ;
             $goods['MaxAdvanceDays']    = !empty($goods['MaxAdvanceDays']) ? $goods['MaxAdvanceDays'] : '' ;
             $goods['MaxQuantityEachOrder'] = !empty($goods['MaxQuantityEachOrder']) ? $goods['MaxQuantityEachOrder'] : 1 ;
             $goods['PrintModel']        = !empty($goods['PrintModel']) ? $goods['PrintModel'] : 0 ;
             $goods['CanPayBeginTime']   = !empty($goods['CanPayBeginTime']) ? $goods['CanPayBeginTime'] : '' ;
             $goods['CanPayEndTime']     = !empty($goods['CanPayEndTime']) ? $goods['CanPayEndTime'] : '' ;
             $goods['Description']       = !empty($goods['Description']) ? $goods['Description'] : '' ;
             $goods['GuestPrompt']       = !empty($goods['GuestPrompt']) ? $goods['GuestPrompt'] : '' ;
             $goods['SalesPrice']        = !empty($goods['SalesPrice']) ? $goods['SalesPrice'] : '' ;
             $goods['PayType']           = !empty($goods['PayType']) ? $goods['PayType'] : 0 ;
             $goods['GuidingPriceRange'] = !empty($goods['GuidingPriceRange']) ? $goods['GuidingPriceRange'] : '' ;
             $goods['CertificateType']   = !empty($goods['CertificateType']) ? $goods['CertificateType'] : '' ;
             $goods['ValidityType']      = !empty($goods['ValidityType']) ? $goods['ValidityType'] : '' ;
     
             $GoodsTimeInfo = $goods['GoodsTimeInfos']['GoodsTimeInfo'];
             unset($goods['GoodsTimeInfos']);
     
             $goods['GoodsTimeInfo']['ValidityStart']        = !empty($GoodsTimeInfo['ValidityStart']) ? $GoodsTimeInfo['ValidityStart'] : '' ;
             $goods['GoodsTimeInfo']['ValidityEnd']          = !empty($GoodsTimeInfo['ValidityEnd']) ? $GoodsTimeInfo['ValidityEnd'] : '' ;
             $goods['GoodsTimeInfo']['DelayEffectTime']      = !empty($GoodsTimeInfo['DelayEffectTime']) ? $GoodsTimeInfo['DelayEffectTime'] : '' ;
             $goods['GoodsTimeInfo']['EffectEndTime']        = !empty($GoodsTimeInfo['EffectEndTime']) ? $GoodsTimeInfo['EffectEndTime'] : '' ;
             $goods['GoodsTimeInfo']['OptionalDay']          = !empty($GoodsTimeInfo['OptionalDay']) ? $GoodsTimeInfo['OptionalDay'] : '' ;
             $goods['GoodsTimeInfo']['ValidityWeekDay']      = !empty($GoodsTimeInfo['ValidityWeekDay']) ? $GoodsTimeInfo['ValidityWeekDay'] : '' ;
             $goods['GoodsTimeInfo']['ValiditySpecialDay']   = !empty($GoodsTimeInfo['ValiditySpecialDay']) ? $GoodsTimeInfo['ValiditySpecialDay'] : '' ;
             $goods['GoodsTimeInfo']['StartExpAllowTime']    = !empty($GoodsTimeInfo['StartExpAllowTime']) ? $GoodsTimeInfo['StartExpAllowTime'] : '' ;
             $goods['GoodsTimeInfo']['EndExpAllowTime']      = !empty($GoodsTimeInfo['EndExpAllowTime']) ? $GoodsTimeInfo['EndExpAllowTime'] : '' ;
             $goods['GoodsTimeInfo']['SettlementPrice']      = !empty($GoodsTimeInfo['SettlementPrice']) ? $GoodsTimeInfo['SettlementPrice'] : '' ;
              
         }
     
         F('scenery-goods-'.$sceneryId, $sceneryGoods, 1800, 'huaqiaocheng/');//缓存半小时
     }
     
     // 价格处理
     foreach ($sceneryGoods as &$good)
     {
         $good['showPrice'] = initSalePrice($good['GoodsTimeInfo']['SettlementPrice'], $customRatio);
     }
     
     return $sceneryGoods;
 }

/**取得数组中的某个值
 * 
 * @param array $data       数组
 * @param string $value     值的键名
 * @param string $sort      排序，支持 max，min，默认是null，将以一维数组方式处理。
 */
function getValue( $data=array(), $value, $sort=null)
{
    $array = array();
    
    if ($sort !== null)
    {
        foreach ($data as $row)
        {
            $array[] = $row[$value];
        }
    }
    else
    {
        $array[] = $data[$value];
    }
    
    // 如果是空数组
    if (empty($array))
    {
        $array = array(0) ;
    }
    
    return $sort($array);
}


/* 创建订单 */
function createVenues( $data )
{
    $default = array(
        'is_pay'    => 0,
        'state'     => 0,
        'add_time'  => local_gettime(),
        'venueName' => $data['detail']['venueName'],
        'venueId'   => $data['detail']['venueId'],
        'infoId'    => $data['order']['info_id'],
        'link_man'  => $data['order']['link_man'],
        'link_phone'=> $data['order']['link_phone'],
        'date'      => $data['order']['date'],
        'order_sn'  => get_order_sn(),
        'username'  => $_SESSION['user_name'],
        'user_id'   => $_SESSION['user_id'],
        'total'     => $data['order']['num'],
        'place'     => $data['detail']['place'],
        'secret'    => $data['order']['secret'],
        'source'    => 0,
        'market_price'=> $data['order']['market_price'],
        'sales_ratio' => $data['order']['sales_ratio']
    );
    
    // 预订时间段  / 选场地信息（用于下单） 
    $times = $selected = array();
    // 预订时间段处理
    if ( strpos(urldecode($data['order']['param']), '|') !== false)
    {
        $params = explode('|', urldecode($data['order']['param']));
    }
    else {
        $params = array(urldecode($data['order']['param']));
    }
    
    $totalPrice = 0;
    foreach ($params as $param)
    {
        $tmpArr = explode('-', $param);
        $sTime = initHours($tmpArr[0]);
        $eTime = initHours($tmpArr[0]+1);
        $num = $tmpArr[1];
        $price = $tmpArr[2];
        $totalPrice += $tmpArr[3];
        
        $times[] = urlencode($sTime.' - '.$eTime.' ( '.$num.'块 / '.$price.' 点 ) ');
        $selected[] = array( 'cond' => $tmpArr[0], 'num'=>$num );
    }
    
    if ($totalPrice != $data['order']['amount'])
    {
        return false;
    }
    
    $insertData = array_merge($default, array('times'=>json_encode($times), 'selected'=> json_encode($selected), 'money'=>$totalPrice));
    $cols = array_keys($insertData);
    $GLOBALS['db']->query(' INSERT INTO '.$GLOBALS['ecs']->table('venues_order')." ( ".implode(',', $cols)." ) VALUES ('".implode("','", $insertData)."')"); 
    return $GLOBALS['db']->insert_id();
}

/* 得到一条订单信息 */
function venueOrder( $orderId = 0 )
{
    if ( empty($orderId) )
    {
        return array();
    }
    
    $reulst = $GLOBALS['db']->getRow('SELECT * FROM '.$GLOBALS['ecs']->table('venues_order'). ' WHERE id = '.$orderId);
    foreach (json_decode($reulst['times']) as $time)
    {
        $reulst['times_mt'][] =urldecode($time);
    }
    return $reulst;
}
/**
 * 更新数据
 *
 * @param	$set		str		更新的数据（is_komovie=0, is_dzq=0, is_brush=0）
 * @param   $where		str		更新的条件
 * @param   $table      str     更新的数据表
 */
function update( $set, $where, $table )
{
    return $GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table($table)." SET ".$set." WHERE ".$where);
}

/**
 * 删除数据
 */
function drop( $table, $where)
{
    return $GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table($table)." WHERE ".$where);
}

/* 检查数据是否存在 */
function hasData( $secret )
{
    $secrets = findData( 'venues_order', 'is_pay=0 AND user_id = "'.$_SESSION['user_id'].'" AND secret="'.$secret.'"' , 'id');
    if ($secrets[0]['id'])
        return $secrets[0]['id'];
    else 
        return false;
}


// 欢乐谷日期查询
function searchData($array=array(), $strtotime)
{
    $selectTime = array();
    if (empty($array))
        return $selectTime;
    
    foreach ($array as $optiona)
    {
        if ($optiona['year'] == local_date('Y', $strtotime))
        {
            $selectTime = explode(',', $optiona['selectTime']);
        }
    }
    return $selectTime;
}

?>