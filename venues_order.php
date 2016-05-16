<?php 
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_dongsport.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = false;
}

assign_template();

$action = isset($_REQUEST['action']) ? addslashes_deep($_REQUEST['action']) : '' ;
if ($action == 'saveOrder')
{
    
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
        'link_man'  => $link_man,
        'link_phone'=> $link_phone,   
        'info_id'   => $info_id,
        'date'      => $date,
        'num'       => $num,
        'amount'    => $amount,
        'param'     => $param,
        'venue_id'  => $venue_id,
        'secret'    => $secret
    );
    
    // 数据验证
    $checkResult = venueEmpty($checkArray);
    
    if ($checkResult['state'] == 1)
    {
        show_message( $checkResult['message'] );
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
            show_message('操作失败');
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
            show_message($result['msg']);
        }
    }
    // 已提交订单的话，获取当前订单信息
    else 
    {
        $order = venueOrder($orderId);
    }    
    $smarty->assign( 'order', $order);
    $smarty->assign( 'detail', $venuesDetail );
    
    $position = assign_ur_here(0,       ' 运动激情 <code>&gt;</code> 运动健身 <code>&gt;</code> 确认订单信息');
    $smarty->assign('page_title',       '确认订单信息_运动健身_运动激情_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
    
}

else if($action == 'pay')
{
    $ajaxMessge = array('error'=>0, 'message'=>'');
    $password = isset($_REQUEST['password']) ? intval($_REQUEST['password']) : 0 ;
    $orderId = isset($_REQUEST['orderid']) ? intval($_REQUEST['orderid']) : 0;
    
    if ( empty($password) || empty($orderId))
    {
        $ajaxMessge['error'] = 1;
        $ajaxMessge['message'] = '参数不全！';
        exit(json_encode($ajaxMessge));
    }
    
    $arr_param = array(	'CardInfo'=>array('CardNo'  => $_SESSION['username'], 'CardPwd' => $password) );
	if ($cardPay->action($arr_param, 8) == 1)
	{
	    $ajaxMessge['error'] = 1;
	    $ajaxMessge['message'] = '密码错误！';
	    exit(json_encode($ajaxMessge));
	}
    
	$order = venueOrder($orderId);
	
    // 是否已经支付的判断
    if ($order['is_pay'] == 1)
    {
        $ajaxMessge['error'] = 1;
        $ajaxMessge['message'] = '已经支付过了！';
        exit(json_encode($ajaxMessge));
    }    
    
    // 支付操作 
    /** TODO 支付 （双卡版） */
    $param = array(
        'CardInfo' => array( 'CardNo'=> $_SESSION['user_name'], 'CardPwd' => $password),
        'TransationInfo' => array( 'TransRequestPoints'=>$order['money'])
    );
    
    if ( $cardPay->action($param, 1, $order['order_sn']) == 0)
    {
        $cardResult = $cardPay->getResult();
        $_SESSION['BalanceCash'] -= $order['money']; //重新计算用户卡余额        
        $GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('users')." SET card_money = card_money - (".$order['money'].") WHERE user_id = '".intval($_SESSION['user_id'])."'");
        
        // 更新订单的支付状态、支付流水号、支付时间
        update( 'is_pay=1,pay_time="'.local_gettime().'",api_card_id="'.$cardResult.'"', 'id='.$orderId, 'venues_order');
        // 接口付款
        $result = getDongSite('pay', array('orderId' => $order['api_order_id']));
        if ($result['code'] == 0)
        {                     
            update( 'is_pay=1,state=1', 'id='.$orderId, 'venues_order');           
        }
        else {
            $ajaxMessge['error'] = 1;
            $ajaxMessge['message'] = $result['msg'];
            exit(json_encode($ajaxMessge));
        }        
    }
    else {
        $ajaxMessge['error'] = 1;
        $ajaxMessge['message'] = $cardPay->getMessage();
        exit(json_encode($ajaxMessge));
    }
    
    exit(json_encode($ajaxMessge));
}

else if($action == 'respond') {
    
    $position = assign_ur_here(0,       ' 运动激情 <code>&gt;</code> 运动健身 <code>&gt;</code> 支付成功');
    $smarty->assign('page_title',       '支付成功_运动健身_运动激情_'.$GLOBALS['_CFG']['shop_name']);    // 页面标题
    $smarty->assign('ur_here',          $position['ur_here']);  // 当前位置
}

$smarty->assign('action', $action);
$smarty->display('venues_order.dwt');
