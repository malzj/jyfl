<?php

/**
 * 用户身份验证
 * @var unknown
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

/* 登录 */
if ($_REQUEST['act'] == 'actLogin')
{
    $username = isset($_REQUEST['username']) ? trim($_REQUEST['username']) : '';
    $password = isset($_REQUEST['password']) ? trim($_REQUEST['password']) : '';
    $type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
    
    //获取卡信息
    $arr_param = array(	'CardInfo'=>array('CardNo'  => $username, 'CardPwd' => $password) );
    $state = $cardPay->action($arr_param, 8);

    // 查询成功
    if ($state == 0)
    {
        // 获得接口返回信息
        $card_result = $cardPay->getResult();
        // 华影卡处理结果
        if ($cardPay->getCardType() == 1)
        {
            // 卡余额
            $cardMoney = $card_result['Points'];
            // 卡有效期
            $cardOutTime = date('Y-m-d',strtotime($card_result['CardValieTime']));
            $company_id = $card_result['CustomerID'];
            if ($card_result['Status'] !=2)
            {
                $jsonArray['state'] = 'false';
                $jsonArray['message'] = '不是激活状态，请联系华影客服';
                exit($_GET['jsoncallback']."(".json_encode($jsonArray).")");   
            }
        }
        // 中影卡处理结果
        else
        {
            // 卡余额
            $cardMoney = $card_result['BalanceCash'];
            // 卡有效期
            $cardOutTime = date('Y-m-d',strtotime($card_result['ExpDate']));
            $company_id = 1;//现无公司字段，防止出错，默认1
            if ($card_result['Status'] != '正常')
            {
                $jsonArray['state'] = 'false';
                $jsonArray['message'] = $card_result['Status'];
                exit($_GET['jsoncallback']."(".json_encode($jsonArray).")"); 
                
            }
        }

        // 执行本地操作
        include_once(ROOT_PATH . 'includes/lib_passport.php');
        $int_user = $db->getRow('SELECT * FROM '.$ecs->table('users'). " WHERE user_name = '$username'");
        if (empty($int_user['user_id'])){//插入用户信息
            $reg_date = gmtime();
            $last_ip  = real_ip();
            //设置默认值
            $userheader = '/hy/images/headpic.png';
            $basic = '保密';
            $pass_edit = 0;
            $GLOBALS['db']->query('INSERT INTO ' . $GLOBALS['ecs']->table("users") . "(`user_name`, `password`, `card_money`, `reg_time`, `last_login`, `last_ip`, `youxiao_time`,`nickname`,`basic`,`pass_edit`,`pic`,`company_id`) VALUES ('$username', '".md5($password)."', '$cardMoney', '$reg_date', '$reg_date', '$last_ip', '".$cardOutTime."','".$username."','".$basic."','".$pass_edit."','".$userheader."','".$company_id."')");
        }else{//更新用户信息
            $GLOBALS['db']->query('UPDATE ' . $GLOBALS['ecs']->table("users") . " SET password='".md5($password)."', card_money = '$cardMoney', youxiao_time = '".$cardOutTime."' WHERE user_id = '".$int_user['user_id']."'");
        }

        // 卡类型判断
        if ( checkCardType($username, $type) == false )
        {
            $jsonArray['state'] = 'false';
            $jsonArray['message'] = '卡类型不符，请从新选择并登录';
            exit($_GET['jsoncallback']."(".json_encode($jsonArray).")");   
        }

        //设置本站登录成功
        $GLOBALS['user']->set_session($username);
        $GLOBALS['user']->set_cookie($username);
        $_SESSION['BalanceCash'] = $cardMoney;
    
        update_user_info();
        recalculate_price();
        
        $jsonArray['data'] = $int_user;
        exit($_GET['jsoncallback']."(".json_encode($jsonArray).")");  
    }
    else
    {
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = $cardPay->getMessage();
        exit($_GET['jsoncallback']."(".json_encode($jsonArray).")"); 
    }
}

// 登录验证
elseif ($_REQUEST['act']=='checkLogin')
{
    if ($_SESSION['user_id'] && $_SESSION['user_id'] == $_REQUEST['userid'])
    {
        $usernames = userinfo($_SESSION['user_name']);        
        $jsonArray['data'] = $usernames;
        exit($_GET['jsoncallback']."(".json_encode($jsonArray).")"); 
    }
    else 
    {
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '验证失败';
        exit($_GET['jsoncallback']."(".json_encode($jsonArray).")"); 
    }    
}

// 登出
elseif ($_REQUEST['act'] == 'logout')
{
    $user->logout();
    exit($_GET['jsoncallback']."(".json_encode($jsonArray).")"); 
}

/* 修改会员密码 */
elseif ($_REQUEST['act'] == 'act_edit_password')
{
    include_once(ROOT_PATH . 'includes/lib_passport.php');

    $old_password = isset($_POST['old_password']) ? trim($_POST['old_password']) : null;
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $user_id      = isset($_POST['uid'])  ? intval($_POST['uid']) : $user_id;
    $code         = isset($_POST['code']) ? trim($_POST['code'])  : '';
    $post_user_name    = isset($_POST['user_name']) ? trim($_POST['user_name']) : '';

    if (strlen($new_password) < 6)
    {
        $jsonArray['state']='false';
        $jsonArray['message']='密码长度不能少于6位';
        exit($_GET['jsoncallback']."(".json_encode($jsonArray).")");
    }

    $user_name = $_SESSION['user_name'];
    if (empty($_SESSION['user_id']))
    {
        if (empty($post_user_name))
        {
            $jsonArray['state']='false';
            $jsonArray['message']='卡号是不能为空';
            exit($_GET['jsoncallback']."(".json_encode($jsonArray).")");
        }
        else{
            $user_name = $post_user_name;
        }
    }

    //修改卡密码
    $arr_param = array(	'CardInfo'=>array('CardNo'  => $user_name, 'CardPwd' => $old_password, 'CardNewPwd'=>$new_password) );
    $state = $cardPay->action($arr_param, 2);

    // 查询成功
    if ($state == 0)
    {
        $sql="UPDATE ".$ecs->table('users'). "SET `ec_salt`='0' WHERE user_id= '".$user_id."'";
        $db->query($sql);

        if (empty($_SESSION['user_id']))
        {
            $jsonArray['state']='true';
            $jsonArray['message']='密码修改成功！';
            exit($_GET['jsoncallback']."(".json_encode($jsonArray).")");
        }else{
            $jsonArray['state']='true';
            $jsonArray['message']='密码修改成功！';
            exit($_GET['jsoncallback']."(".json_encode($jsonArray).")");
        }
    }
    else
    {
        $jsonArray['state']='false';
        $jsonArray['message']=$cardPay->getMessage();
        exit($_GET['jsoncallback']."(".json_encode($jsonArray).")");
    }
}

