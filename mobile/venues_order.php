<?php 
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_basic.php');
include_once(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_dongsport.php');

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

$action = isset($_REQUEST['action']) ? addslashes_deep($_REQUEST['action']) : '' ;
if ($action == 'saveOrder')
{
    // 卡规则比例
    $customRatio = get_card_rule_ratio(10003,true);
    //var_dump($_POST);
    $link_man   = isset($_REQUEST['link_man']) ? addslashes($_REQUEST['link_man']) : null ;
    $link_phone = isset($_REQUEST['link_phone']) ? addslashes($_REQUEST['link_phone']) : null ;
    $info_id    = isset($_REQUEST['info_id']) ? intval($_REQUEST['info_id']) : null ;
    $date       = isset($_REQUEST['travel_date']) ? addslashes($_REQUEST['travel_date']) : null ;
    $num        = isset($_REQUEST['num']) ? intval($_REQUEST['num']) : null ;
    $amount     = isset($_REQUEST['amount']) ? addslashes($_REQUEST['amount']) : null ;
    $param      = isset($_REQUEST['param']) ? addslashes($_REQUEST['param']) : null ;
    $venue_id   = isset($_REQUEST['venue_id']) ? intval($_REQUEST['venue_id']) : null ;
    $secret     = isset($_REQUEST['secret']) ? $_REQUEST['secret'].md5($param) : null ;
        
    $checkArray = array(
        'link_man'     => $link_man,
        'link_phone'   => $link_phone,   
        'info_id'      => $info_id,
        'date'         => $date,
        'num'          => $num,
        'amount'       => $amount,
        'market_price' => '',
        'param'        => $param,
        'venue_id'     => $venue_id,
        'secret'       => $secret,
        'shop_ratio'  => $customRatio['shop_ratio'],
        'card_ratio'  => $customRatio['card_ratio'],
        'raise'       => $customRatio['raise'],
        'ext'         => $customRatio['ext']
    );
    
    // 数据验证
    $checkResult = venueEmpty($checkArray);
    
    if ($checkResult['state'] == 1)
    {
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = $checkResult['message'];
        JsonpEncode($jsonArray);
    }
    // 场馆信息
    $venuesDetail = venuesDetail($venue_id);
    $createData = array('detail'=>$venuesDetail, 'order'=>$checkArray);
    
    //是否已经提交过,已经提交过返回订单id，否则返回 false并提交订单
    $orderId = hasData( $secret );
    
    if ($orderId == false)
    {
        // 创建订单
        $orderId = createVenues( $createData );
        if ( false == $orderId)
        {
            $jsonArray['state'] = 'false';
            $jsonArray['message'] = '操作失败';
            JsonpEncode($jsonArray);
        }
        // 订单信息
        $order = venueOrder($orderId);
        
        // 接口下单
        $addParams = array(
            'infoId'        => $order['infoId'],
            'sourceOrderId' => $order['order_sn'],
            'num'           => $order['total'],
            'linkName'      => $order['link_man'],
            'linkPhone'     => $order['link_phone'],
            'travelDate'    => $order['date'],
            'selected'      => $order['selected']
        );
        
        $result = getDongSite('add', $addParams);
        if ($result['code'] == 0)
        {
            $apiorderId = $result['body']['orderId'];
            update( 'api_order_id='.$apiorderId, 'id='.$orderId, 'venues_order');
        }
        // 接口下单失败，删除数据库中的这条记录
        else {
            drop('venues_order', 'id='.$order['id']);
            $jsonArray['state'] = 'false';
            $jsonArray['message'] = $result['msg'];
            JsonpEncode($jsonArray);
        }
    }

    $jsonArray['data'] = array(
        'order_id'=>$orderId,
        'venue_id'=>$venue_id
    );
    JsonpEncode($jsonArray);
}
//获取运动场馆订单
else if($action == 'getOrder'){
    $order_id   = isset($_REQUEST['orderid']) ? intval($_REQUEST['orderid']) : null ;
    $venue_id   = isset($_REQUEST['venueid']) ? intval($_REQUEST['venueid']) : null ;

    if($order_id==null||$venue_id==null){
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '非法请求！';
        JsonpEncode($jsonArray);
    }

    // 场馆信息
    $venuesDetail = venuesDetail($venue_id);
    // 订单信息
    $order = venueOrder($order_id);

    if(empty($order)){
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '无效订单！';
        JsonpEncode($jsonArray);
    }

    $jsonArray['data'] = array(
        'order'=>$order,
        'detail'=>$venuesDetail
    );
    JsonpEncode($jsonArray);
}
//订单支付
else if($action == 'pay')
{
    $password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : 0 ;
    $orderId = isset($_REQUEST['orderid']) ? intval($_REQUEST['orderid']) : 0;
    
    if ( empty($password) || empty($orderId))
    {
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '参数不全！';
        JsonpEncode($jsonArray);
    }
    
	$order = venueOrder($orderId);
	
    // 是否已经支付的判断
    if ($order['is_pay'] == 1)
    {
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '已经支付过了！';
        JsonpEncode($jsonArray);
    }
    
    // 支付操作 
    /** TODO 支付 （双卡版） */
    $param = array(
        'CardInfo' => array( 'CardNo'=> $_SESSION['user_name'], 'CardPwd' => $password),
        'TransationInfo' => array( 'TransRequestPoints'=>$order['money'], 'TransSupplier'=>setCharset('动网场馆'))
    );
    
    if ( $cardPay->action($param, 1, $order['order_sn']) == 0)
    {
        $cardResult = $cardPay->getResult();
        $_SESSION['BalanceCash'] -= $order['money']; //重新计算用户卡余额        
        $GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('users')." SET card_money = card_money - (".$order['money'].") WHERE user_id = '".intval($_SESSION['user_id'])."'");
        
        // 更新订单的支付状态、支付流水号、支付时间
        update( 'is_pay=1,pay_time="'.local_gettime().'",api_card_id="'.$cardResult.'"', 'id='.$orderId, 'venues_order');
        // 接口付款
//        $result = getDongSite('pay', array('orderId' => $order['api_order_id']));
        if ($result['code'] == 0)
        {                     
            update( 'is_pay=1,state=1', 'id='.$orderId, 'venues_order');
            $jsonArray['message'] = '支付成功！';
        }
        else {
            $jsonArray['state'] = 'false';
            $jsonArray['message'] = $result['msg'];
            JsonpEncode($jsonArray);
        }
    }
    else {
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = $cardPay->getMessage();
        JsonpEncode($jsonArray);
    }
    
    JsonpEncode($jsonArray);
}


