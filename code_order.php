<?php
/**
 * 试听盛宴-----> 影院 ------> 下单
 * @var unknown_type
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/mobile/includes/lib_cinema.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');

//根据城市id获取影院区域编号
$int_areaNo = getAreaNo(0,'komovie');

if (!isset($_REQUEST['step']))
{
    $_REQUEST['step'] = "order";
}

assign_template();

$smarty->assign('act', $_REQUEST['act']);

// 电子券下单
if ($_REQUEST['act'] == "orderCode")
{
    $returnAjax = array( 'error'=>0, 'message'=>'');
    // 卡规则折扣
    $ratio = getDzqRatio();
    //电子券兑换订单
    $int_cAreaNo    = intval($_POST['areaNo']);//区域编号
    $int_areaName   = $_POST['areaName'];//区域名称
    $str_cinemaNo   = $_POST['cinemaNo'];//影院编号
    $str_cinemaName = $_POST['cinemaName'];//影院名称
    $int_ticketNo   = $_POST['ticketNo'];//电子券编号
    $str_mobile     = $_POST['mobile'];//手机
    $flo_price      = $_POST['price'];//价格

    // 基础价格
    $flo_prices     = number_format($_POST['price'],2,'.','');
    $int_number     = intval($_POST['number']);//数量

    //实际价格(商城卖的实际单价)
    if ($ratio !== false){
        $flo_sjprice = price_format(($_POST['price']/1.2*1.06)*$ratio);
        $flo_amount		= price_format($flo_price/1.2*1.06*$ratio * $int_number);
    }else{
        $flo_sjprice = price_format($_POST['price']/1.2*1.06);
        $flo_amount		= price_format($flo_price/1.2*1.06 * $int_number);
    }

    if (empty($str_mobile)){
        $returnAjax['error'] = 1;
        $returnAjax['message'] = '请填写手机号码';
        exit(json_encode($returnAjax));
    }

    // 获得影票信息
    $cinemaDzq = getCinemaDzq($str_cinemaNo, $ratio);

    // 余额不足判断
    $card_money = $GLOBALS['db']->getOne('SELECT card_money FROM '.$GLOBALS['ecs']->table('users')." WHERE user_id = '".intval($_SESSION['user_id'])."'");

    if (($flo_sjprice * $int_number) > $card_money){
        $returnAjax['error'] = 1;
        $returnAjax['message'] = '抱歉您的卡余额不足!';
        exit(json_encode($returnAjax));
    }

    $arr_dzqinfo = $cinemaDzq[$int_ticketNo];

    $str_youxiaoq = $arr_dzqinfo['ValidType'] == 1 ? $arr_dzqinfo['HotSellEnd'].'天' : $arr_dzqinfo['HotSellEnd'];

    $str_sign = md5($str_mobile.'1'.$str_cinemaNo.$int_ticketNo.$flo_price.$int_number.$GLOBALS['_CFG']['yyappKey']);
    $arr_param = array(
        'Mobile' => $str_mobile,
        'OrderType' => 1,
        'ExOrderNo' => '',
        'CinemaNo'  => $str_cinemaNo,
        'TicketNo'  => $int_ticketNo,
        'Price'     => $flo_price,
        'Count'     => $int_number,
        'AreaNo'    => $int_areaNo,
        'IsCustomPrice' => 0,
        'Sign'      => $str_sign
    );

    $arr_result = getYYApi($arr_param, 'createCommTicketOrder');//下通兑票订单
    if ($arr_result['head']['errCode'] == '0'){

        $ratioDzq = getDzqRatio(true);
        $arr_orderInfo = $arr_result['body'];
        //插入订单信息
        $str_sql = 'INSERT INTO '.$ecs->table('dzq_order')." (order_sn, user_id, user_name, order_status, mobile, city, AreaNo, CinemaNo, CinemaName, TicketNo, TicketName, ProductSizeZn, TicketYXQ, number, pay_id, pay_name, price, sjprice, goods_amount, order_amount, add_time, confirm_time, source, card_ratio, shop_ratio,raise,ext) VALUES ('".$arr_orderInfo['OrderNo']."', '".$_SESSION['user_id']."', '".$_SESSION['user_name']."', '1', '$str_mobile', '$int_cityId', '$int_cAreaNo', '$str_cinemaNo', '$str_cinemaName', '$int_ticketNo', '".$arr_dzqinfo['TicketName']."', '".$arr_dzqinfo['ProductSizeZn']."', '$str_youxiaoq', '$int_number', '2', '聚优支付', '$flo_prices', '$flo_sjprice', '$flo_amount', '$flo_amount', '".gmtime()."', '".gmtime()."', 0, '".$ratioDzq['card_ratio']."', '".$ratioDzq['shop_ratio']."', '".$ratioDzq['raise']."', '".$ratioDzq['ext']."')";
        $query = $db->query($str_sql);
        $returnAjax['message'] = $db->insert_id();
        exit(json_encode($returnAjax));
    }else{
        $returnAjax['error'] = 1;
        $returnAjax['message'] = $arr_result['head']['errMsg'];
        exit(json_encode($returnAjax));
    }
}
