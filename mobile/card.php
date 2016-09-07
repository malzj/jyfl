<?php
/**
 *卡操作接口
 * User: chao
 * Date: 2016/7/14
 * Time: 16:48
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);
/**
 * 卡合并
 */
if($_REQUEST['act'] == 'act_card_merge'){
    include_once(ROOT_PATH . './includes/lib_order.php');
    $str_fromCard    = trim($_REQUEST['fromcard']);
    $str_fromCardPwd = trim($_REQUEST['fromcardpwd']);

    $str_toCard    = trim($_REQUEST['tocard']);
    $str_toCardPwd = trim($_REQUEST['tocardpwd']);

    if (empty($str_fromCard) || empty($str_fromCardPwd)){
        $jsonArray['state']="false";
        $jsonArray['message']='要合并的卡号或密码不能为空！';
        JsonpEncode($jsonArray);
    }
    if (empty($str_toCard) || empty($_REQUEST['tocardpwd'])){
        $jsonArray['state']="false";
        $jsonArray['message']='合并到的卡号或密码不能为空！';
        JsonpEncode($jsonArray);
    }
    $a=substr($str_fromCard,0,6);
    $b=substr($str_toCard,0,6);

    // 不是同一个卡系统的卡，不能合并 TODO guoyunepng
//    $cardno = array('999011', '999013');
    /* if ( (in_array($a, $cardno) && !in_array($b, $cardno)) || (!in_array($a, $cardno) && in_array($b, $cardno)))
    {
        echo '两张卡类型不符合，不能合并，有问题请拨打客服电话：400-010-0689';
        exit;
    } */

    if ($str_fromCard == $str_toCard){
        $jsonArray['state']="false";
        $jsonArray['message']='要合并的卡号与合并到的卡号不能相同！';
        JsonpEncode($jsonArray);
    }

    $cardno2 = array('999011', '999013');

    // 每点金额一样的卡才可以合并
    $cardno = array(
        '999011' => 1.19,
        '999013' => 0.97,
        '711001' => 1.19,
        '711002' => 0.97,
        '711003' => 1.19,
        '711005' => 0.97,
        '711006' => 0.97,
        '711007' => 0.97,
        '711008' => 0.97,
        '711009' => 0.97,
        '711015' => 1.19,
        '711016' => 1.19,
        '711017' => 1.19,
        '711018' => 1.19,
        '711019' => 1.19
    );

    if ($cardno[$a] != $cardno[$b])
    {
        $jsonArray['state']="false";
        $jsonArray['message']='两张卡类型不统一，不支持合并！';
        JsonpEncode($jsonArray);
    }

    // 卡规则，卡合并限制
    // 无卡规则，默认限制是开启的
    if (!empty($_SESSION['card_id']))
        $merge_limit = $GLOBALS['db']->getOne('SELECT merge_limit FROM '.$GLOBALS['ecs']->table('card_rule')." where id = ".$_SESSION['card_id']);
    else
        $merge_limit = 1;

    // 卡规则，卡合并限制
    // 来源卡，卡合并限制
    //$card_from_id = $GLOBALS['db']->getOne('SELECT card_id FROM '.$GLOBALS['ecs']->table('users')." where user_name = '".$str_fromCard."'");
    $arr_cardRules = $GLOBALS['db']->getAll('SELECT merge_limit,card FROM '.$GLOBALS['ecs']->table('card_rule'));
    foreach ($arr_cardRules as $key=>$var){
        if (!empty($var['card'])){
            $arr_card = unserialize($var['card']);
            if (in_array($str_fromCard, $arr_card)){
                $merge_limit_from = $var['merge_limit'];
            }
        }
    }

    // 只有都关闭了卡合并限制，才会跳过卡合并限制
    if ($merge_limit_from == 1 || $merge_limit == 1)
    {
        //搜索是否已经存在记录
        //var_dump($str_fromCard);
        $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('card_log') .
            " WHERE card_from = '$str_fromCard' or card_to = '$str_fromCard' ORDER BY  log_id DESC";
        $res = $GLOBALS['db']->query($sql);

        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $pay_time = $row['pay_time'];
            $times  = gmtime() - $pay_time;
            if ($times < 90*24*3600){
                $jsonArray['state']="false";
                $jsonArray['message']='您的这卡在短期内已经转移过！';
                JsonpEncode($jsonArray);
            }
        }

        $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('card_log') .
            " WHERE card_from = '$str_toCard' or card_to = '$str_toCard' ORDER BY  log_id DESC";
        $res = $GLOBALS['db']->query($sql);

        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $pay_time = $row['pay_time'];
            $times  = gmtime() - $pay_time;
            if ($times < 90*24*3600){
                $jsonArray['state']="false";
                $jsonArray['message']='您的这卡在短期内已经转移过！';
                JsonpEncode($jsonArray);
            }
        }
    }

    // 双卡密码验证==> 转出卡密码验证
    $arr_param = array( 'CardInfo'=>array( 'CardNo' => $str_fromCard, 'CardPwd'=> $str_fromCardPwd));
    $state = $cardPay->action($arr_param, 8);

    if ($state == 0)
    {
        // 转出的卡余额
        $cardResult = $cardPay->getResult();
        if ($cardPay->getCardType() == 1)
        {
            $cardValueMoney = $cardResult['Points'];
            $cardOutTime = strtotime($cardResult['CardValieTime']);
        }
        else {
            $cardValueMoney = $cardResult['BalanceCash'];
            $cardOutTime = strtotime($cardResult['ExpDate'].' +1 day');
        }

        // 有效期验证
        if (gmtime() > $cardOutTime)
        {
            $jsonArray['state']="false";
            $jsonArray['message']='转出的卡已过有效期，合并失败！';
            JsonpEncode($jsonArray);
        }
        // 转入卡密码验证
        $param = array( 'CardInfo'=>array( 'CardNo' => $str_toCard, 'CardPwd'=> $str_toCardPwd));
        $state = $cardPay->action($param, 8);
        if ($state == 0)
        {
            $cardResult = $cardPay->getResult();
            if ($cardPay->getCardType() == 1)
            {
                $cardOutTime = strtotime($cardResult['CardValieTime']);
            }
            else {
                $cardOutTime = strtotime($cardResult['ExpDate'].' +1 day');
            }

            // 有效期验证
            if (gmtime() > $cardOutTime)
            {
                $jsonArray['state']="false";
                $jsonArray['message']='转入的卡已过有效期，合并失败！';
                JsonpEncode($jsonArray);
            }

            // 卡状态验证
            if ($cardPay->getCardType() == 1)
            {
                if ($cardResult['Status'] !=2)
                {
                    $jsonArray['state']="false";
                    $jsonArray['message']='不是激活状态，请联系华影客服！';
                    JsonpEncode($jsonArray);
                }
            }
            else
            {
                if ($cardResult['Status'] != '正常')
                {
                    $jsonArray['state']="false";
                    $jsonArray['message']=$cardResult['Status'];
                    JsonpEncode($jsonArray);
                }
            }

        }
        else
        {
            $jsonArray['state']="false";
            $jsonArray['message']='转入的卡密码不正确！';
            JsonpEncode($jsonArray);
        }
    }
    else
    {
        $jsonArray['state']="false";
        $jsonArray['message']='转出的卡秘密不正确！';
        JsonpEncode($jsonArray);
    }

    // 转出的卡余额是0，终止执行
    if (floatval($cardValueMoney) <=0)
    {
        $jsonArray['state']="false";
        $jsonArray['message']='转出的卡点数为 0 ，请换张有点数的卡！';
        JsonpEncode($jsonArray);
    }

    $state = 0;
    // 两个卡系统的卡执行卡合并的时候，一个充值一个是消费
    if ( (in_array($a, $cardno2) && !in_array($b, $cardno2)) || (!in_array($a, $cardno2) && in_array($b, $cardno2)))
    {
        $pay_param = array(
            'CardInfo' => array( 'CardNo'=> $str_fromCard, 'CardPwd' => $str_fromCardPwd),
            'TransationInfo' => array( 'TransRequestPoints'=>floatval($cardValueMoney))
        );

        $recharge_param = array(
            'CardInfo' => array( 'CardNo'=> $str_toCard, 'CardPwd' => $str_toCardPwd),
            'TransationInfo' => array( 'TransRequestPoints'=>floatval($cardValueMoney))
        );

        if ($cardPay->action($pay_param, 1) == 0)
        {
            $logMsg .= "[卡号] ".$str_fromCard." ～ [状态] 已消费  ～ [时间] ".local_date('Y-m-d H:i:s', gmtime())." \r\n";
            error_log($logMsg,3,'../temp/card_merge/deffmessage_'.date('Ym',time()).'.log');
            $state = $cardPay->action($recharge_param, 6);
        }
    }
    // 同一个卡系统的卡执行可合并，调用余额转移接口
    else {
        $arr_param = array(
            'CardInfo'=>array(
                'OldCardNo'	=>$str_fromCard, 'OldPwd'	=>$str_fromCardPwd,   // 原卡号、密码
                'DesCardNo'	=>$str_toCard,	'DesPwd'	=>$str_toCardPwd	  // 目标卡号、密码
            )
        );
        $state = $cardPay->action($arr_param, 10);
    }

    if ($state == 0)
    {
        $param = array( 'CardInfo'=>array( 'CardNo'=>$str_toCard, 'CardPwd'=>$str_toCardPwd) );
        if ($cardPay->action($param, 8) == 0)
        {
            $cardResult = $cardPay->getResult();
            // 获得华影卡余额
            if ($cardPay->getCardType() == 1)
            {
                $flo_money = $cardResult['Points'];
            }
            // 获得中影卡余额
            else
            {
                $flo_money = $cardResult['BalanceCash'];
            }

            //插入日志表
            $sql = "INSERT INTO " . $GLOBALS['ecs']->table('card_log') . " (card_from, card_to, card_money, pay_time, source) " .
                "VALUES('$str_fromCard', '$str_toCard', '$cardValueMoney', '".gmtime()."', 1)";

            $GLOBALS['db']->query($sql);

            //要合并的卡等于当前用户修改当前用户卡余额
            if ($str_fromCard == $_SESSION['user_name']){
                $_SESSION['BalanceCash'] += $cardValueMoney;
                $db->query('UPDATE '.$ecs->table('users')." SET card_money = '0' WHERE user_id = '$user_id'");
            }
            if ($str_toCard == $_SESSION['user_name']){
                $_SESSION['BalanceCash'] += $cardValueMoney;
                $db->query('UPDATE '.$ecs->table('users')." SET card_money = card_money + ('$cardValueMoney') WHERE user_id = '$user_id'");
            }
            $jsonArray['state']="true";
            $jsonArray['message']='success'.$flo_money;
            JsonpEncode($jsonArray);
        }
        else
        {
            $jsonArray['state']="false";
            $jsonArray['message']=$cardPay->getMessage();
            JsonpEncode($jsonArray);
        }
    }
    // 卡合并失败后的操作，次状态只有中影卡合并时候才有，意思是，来源卡扣款了，充值失败了。
    elseif($state == 2){
        //插入日志表
        $sql = "INSERT INTO " . $GLOBALS['ecs']->table('card_log') . " (card_from, card_to, card_money, pay_time, message) " .
            "VALUES('$str_fromCard', '$str_toCard', '$cardValueMoney', '".gmtime()."', '卡合并失败，来源卡点数已扣')";
        $db->query($sql);
        $jsonArray['state']="false";
        $jsonArray['message']=$cardPay->getMessage();
        JsonpEncode($jsonArray);
    }
    else
    {
        $jsonArray['state']="false";
        $jsonArray['message']=$cardPay->getMessage();
        JsonpEncode($jsonArray);
    }
}
/*
 * 卡充值
 *  会员预付款界面 
 * */
elseif ($_REQUEST['act'] == 'account_deposit')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    $surplus_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $account    = get_surplus_info($surplus_id);
    $user_id=$_SESSION['user_id'];

    //获取剩余余额
    $surplus_amount = get_user_surplus($user_id);
    if (empty($surplus_amount))
    {
        $surplus_amount = 0;
    }

    // 售价比例 TODO 2015-11-06
    $pay_than = 1.3;

    if( !is_null($_SESSION['card_id']) )
    {
        $pay_price = $GLOBALS['db']->getOne('SELECT pay_than FROM '.$GLOBALS['ecs']->table('card_rule')." where id = ".$_SESSION['card_id']);
        if ( !empty($pay_price) && $pay_price > 0.001)
        {
            $pay_than = $pay_price;
        }
    }

    $priceList = array( 30=>30, 50=>50, 100=>100 );
    foreach($priceList as &$point){
        $point = price_format($point * $pay_than);
    }

    $jsonArray['data']=array(
        'priceList'=>$priceList,
        'surplus_amount'=>price_format($surplus_amount, false),
        'username'=>$_SESSION['user_name'],
        'order'=>$account,
    );
    JsonpEncode($jsonArray);
}
/**
 * 点击确认充值
 * 对会员余额申请的处理 */
elseif ($_REQUEST['act'] == 'act_account')
{
    $user_id=$_SESSION['user_id'];
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');
    $amount = isset($_REQUEST['amount']) ? floatval($_REQUEST['amount']) : 0;

    // 充值金额为0的话，定义为非法操作
    if ($amount <= 0){
        $jsonArray['state']   = 'false';
        $jsonArray['message'] = '非法操作！';
        JsonpEncode($jsonArray);
    }

    // 如果没有登陆不能充值
    if(empty($user_id))
    {
        $jsonArray['state']   = 'false';
        $jsonArray['message'] = '请先验证卡号！';
        JsonpEncode($jsonArray);
    }

    /* 变量初始化 */
    $surplus = array(
        'user_id'      => $user_id,
        'rec_id'       => !empty($_REQUEST['rec_id'])      ? intval($_REQUEST['rec_id'])       : 0,
        'process_type' => isset($_REQUEST['surplus_type']) ? intval($_REQUEST['surplus_type']) : 0,
        'payment_id'   => isset($_REQUEST['payment_id'])   ? intval($_REQUEST['payment_id'])   : 0,
        'user_note'    => isset($_REQUEST['user_note'])    ? trim($_REQUEST['user_note'])      : '',
        'amount'       => $amount
    );

    /* 退款申请的处理 */
    if ($surplus['process_type'] == 1)
    {

    }
    /* 如果是会员预付款，跳转到下一步，进行线上支付的操作 */
    else
    {
        // 如果没有选择支付方式，输出错误消息。
        if ($surplus['payment_id'] < 3)
        {
            $jsonArray['state']   = 'false';
            $jsonArray['message'] = '选择支付方式！';
            JsonpEncode($jsonArray);
        }

        include_once(ROOT_PATH .'includes/lib_payment.php');

        //获取支付方式名称
        $payment_info = array();
        $payment_info = payment_info($surplus['payment_id']);
        $surplus['payment'] = $payment_info['pay_name'];

        if ($surplus['rec_id'] > 0)
        {
            //更新会员账目明细
            $surplus['rec_id'] = update_user_account($surplus);
        }
        else
        {
            //充值申请接口
            include_once(ROOT_PATH . 'includes/lib_cardApi.php');

            // 售价比例 TODO 2015-11-06
            $pay_than = 1.3;

            if( !is_null($_SESSION['card_id']) )
            {
                $pay_price = $GLOBALS['db']->getOne('SELECT pay_than FROM '.$GLOBALS['ecs']->table('card_rule')." where id = ".$_SESSION['card_id']);
                if ( !empty($pay_price) && $pay_price > 0.001)
                {
                    $pay_than = $pay_price;
                }
            }


            // 点数 => 金额
            $priceList = array( 30=>30, 50=>50 ,100=>100);
            foreach($priceList as &$point){
                $point = price_format($point * $pay_than);
            }
            if (!in_array( $amount , $priceList))
            {
                $int_sjAmount = 0;
            }
            else {
                $int_sjAmount = array_search( $amount, $priceList);
            }

            // TODO 卡系统识别
            $userinfo = $db->getRow('SELECT user_name, password FROM '.$ecs->table('users')." WHERE user_id = '".$user_id."'");
            $arr_param = array(	'CardInfo' => array( 'CardNo'=>$userinfo['user_name'], 'CardPwd'=>$userinfo['password']));
            $state = $cardPay->action($arr_param, 8);
            // 中影卡充值
            if ($cardPay->getCardType() == 2)
            {
                $arr_param = array(
                    'cardSeq'   => $_SESSION['user_name'],//卡序号
                    'orderType' => 1,//1，单卡充值，2，批量充值
                    'operId'    => $GLOBALS['_CFG']['operId'],//充值操作员(自助终端传终端编号)
                    'cardNum'   => 1,//充值卡数量
                    'saleId'    => $GLOBALS['_CFG']['saleId'],//售卡机构编号
                    'timeStamp' => local_date('YmdHis'),//时间戳
                    'company'   => 'alipay',//购卡单位
                    'singleSaveAmount' => $int_sjAmount,//单张充值金额
                    'singleRealAmount' => $int_sjAmount,//单张实收金额
                    'totalSaveAmount'  => '',//总充值金额
                    'totalRealAmount'  => '',//总实收金额
                    'expDate'      => '',//有效期
                    'thirdJournal' => '',//第三方流水号
                    'extendInfo'   => ''//接口扩展字段信息
                );
                $arr_cardInfo = getCardApi($arr_param, 'CARD-RECHARGE', 7);
                if ($arr_cardInfo['ReturnCode'] == '0'){
                    $surplus['order_sn'] = $arr_cardInfo['OrderId'];
                }else{
                    $jsonArray['state']   = 'false';
                    $jsonArray['message'] = $arr_cardInfo['ReturnMessage'];
                    JsonpEncode($jsonArray);
                }
            }

            //插入会员账目明细
            $surplus['rec_id'] = insert_user_account($surplus, $amount);

            // 华影卡，在支付宝成功支付后，在充值 。 lib_common.php   log_account_change（）；

        }

        //取得支付信息，生成支付代码
        $payment = unserialize_config($payment_info['pay_config']);

        //生成伪订单号, 不足的时候补0
        $order = array();
        $order['order_sn']       = $surplus['rec_id'];
        $order['user_name']      = $_SESSION['user_name'];
        $order['surplus_amount'] = $amount;

        //计算支付手续费用
        $payment_info['pay_fee'] = pay_fee($surplus['payment_id'], $order['surplus_amount'], 0);

        //计算此次预付款需要支付的总金额
        $order['order_amount']   = $amount + $payment_info['pay_fee'];

        //记录支付log
        $order['log_id'] = insert_pay_log($surplus['rec_id'], $order['order_amount'], $type=PAY_SURPLUS, 0);


        /* 调用相应的支付方式文件 */
        include_once(ROOT_PATH . 'includes/modules/payment/' . $payment_info['pay_code'] . '.php');
        /* 取得在线支付方式的支付按钮 */
        $pay_obj = new $payment_info['pay_code'];
        $linkpay = $pay_obj->get_code($order, $payment, 1);
        $jsonArray['state']   = 'true';
        if ($payment_info['pay_code'] == 'alipay')
        {
            $jsonArray['data']['href'] = '/mobile/user.php?act=pays&code='.base64_encode($linkpay);
        }
        else if ( $payment_info['pay_code'] == 'weixin')
        {
            $jsonArray['data']['href'] = $linkpay;
        }

        JsonpEncode($jsonArray);
    }
}
elseif ($_REQUEST['act'] == 'pays')
{
    $code = base64_decode($_GET['code']);
    $smarty->assign('url', $code);
    $smarty->display('alipayPay.html');
}