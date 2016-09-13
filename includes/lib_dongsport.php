<?php 

function initHours( $hours ){
    $return = '';
    if ( strlen($hours) == 1)
        $return = '0'.$hours.':00';
    else
        $return = $hours.':00';

    return $return;
}

// 价格处理
function initSalePrice( $price=0, $customRatio )
{   
    // 基础价比例
    $defaultRatio = '1.1';
    $newPrice = $price * $defaultRatio;
    if ($customRatio !== false)
        $returnPrice = ceil($newPrice*$customRatio);
    else
        $returnPrice = ceil($newPrice);

    return $returnPrice;
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

/**  
 * 场地数据验证
 * @param unknown $param
 */
function venueEmpty( $params = array() )
{
   $returnMessage = array( 'state'=>0, 'message'=>'');
   if ( empty($params['link_man']))
   {
       $returnMessage['state'] = 1;
       $returnMessage['message'] = '姓名不能为空！';
   }
   if ( empty($params['link_phone']))
   {
       $returnMessage['state'] = 1;
       $returnMessage['message'] = '手机号不能为空！';
   }
   /* else {
       var_dump(preg_match('/^1[34578]{1}\d{9}$/', $params['link_phone']));
       if (!preg_match('/^1[34578]{1}\d{9}$/', $params['link_phone']))
       {
           $returnMessage['state'] = 1;
           $returnMessage['message'] = '请填写正确的手机号！';
       }
   } */
   
   if ( empty($params['link_man']))
   {
       $returnMessage['state'] = 1;
       $returnMessage['message'] = '姓名不能为空！';
   }
   
   if ( empty($params['info_id']) || empty($params['date']) || 
        empty($params['num']) || empty($params['amount']) || 
        empty($params['param']) || empty($params['venue_id']) )
   {
       $returnMessage['state'] = 1;
       $returnMessage['message'] = '非法请求';
   }
   
   return $returnMessage;       
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
        'shop_ratio' => $data['order']['shop_ratio'],
        'card_ratio' => $data['order']['card_ratio'],
        'raise' => $data['order']['raise'],
        'ext' => $data['order']['ext'],
        'real_price' => $data['order']['real_price'],
        'cordon_show' => $data['order']['cordon_show']
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

/** 获得场馆信息  
 *  
 *  @param string   $venue_id       场馆id
 *  @param unknown $venue_id
 */
function venuesDetail( $venue_id )
{
    // 场馆详情
    $venuesDetail = F('dongwang_detail', '', 1800, 'venues/'.$venue_id.'/');//缓存半小时
    if(empty($venuesDetail))
    {
        $apiData = getDongSite('detail', array('id'=>$venue_id));
        if ($apiData['code'] == 0)
            $venuesDetail = $apiData['body'];
        else
            $venuesDetail = array();
    
        F('dongwang_detail', $venuesDetail, 1800, 'venues/'.$venue_id.'/');//写入缓存
    }
    return $venuesDetail;
}

/**
 * 
 * 门票数据部分处理
 * 
 * 
 * 
 * 
 * 
 * 
 */
 
// 联系人信息
function link_info($link){

    $linkName = array();

    switch($link){

        case 'link_man':
            $linkName['name'] = '你的名字';
            $linkName['tip']  = '请填写全名("李先生","张女士"之类名称无效)，凭有效证件的姓名进行验证';
            break;
        case 'link_phone':
            $linkName['name'] = '手机号码';
            $linkName['tip']  = '此产品需要给客人发送短信，手机号码必填';
            break;
        case 'link_email':
            $linkName['name'] = 'E-mail';
            break;
        case 'link_address':
            $linkName['name'] = '快递地址';
            break;
        case 'linkCode':
            $linkName['name'] = '邮政编码';
            break;
        case 'link_credit_type':
            $linkName['name'] 		= '证件类型';
            $linkName['selects']   	= array( 0=>'身份证', 1=>'学生证', 2=>'军官证', 3=>'护照', 4=>'户口本（儿童请选择此项）', 5=>'港澳通行证', 6=>'台胞证');
            break;
        case 'link_credit_no':
            $linkName['name'] = '证件号码';
            break;
    }
    $linkName['link'] = $link;
    return $linkName;
}
?>