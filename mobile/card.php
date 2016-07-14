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
    $cardno = array('999011', '999013');
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

    //搜索是否已经存在记录
    //var_dump($str_fromCard);
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('card_log') .
        " WHERE card_from = '$str_fromCard' or card_to = '$str_fromCard' ORDER BY  log_id DESC";
    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $pay_time = $row['pay_time'];
        $times  = gmtime() - $pay_time;
//        if ($times < 90*24*3600){
//            $jsonArray['state']="false";
//            $jsonArray['message']='您的这卡在短期内已经转移过！';
//            JsonpEncode($jsonArray);
//        }
    }

    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('card_log') .
        " WHERE card_from = '$str_toCard' or card_to = '$str_toCard' ORDER BY  log_id DESC";
    $res = $GLOBALS['db']->query($sql);

    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $pay_time = $row['pay_time'];
        $times  = gmtime() - $pay_time;
//        if ($times < 90*24*3600){
//            $jsonArray['state']="false";
//            $jsonArray['message']='您的这卡在短期内已经转移过！';
//            JsonpEncode($jsonArray);
//        }
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
    if ( (in_array($a, $cardno) && !in_array($b, $cardno)) || (!in_array($a, $cardno) && in_array($b, $cardno)))
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