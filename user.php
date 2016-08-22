<?php

/**
 * ECSHOP 会员中心
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: user.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_cardApi.php');

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');

$user_id = $_SESSION['user_id'];
$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';

$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
$smarty->assign('affiliate', $affiliate);
$back_act='';


// 不需要登录的操作或自己验证是否登录（如ajax处理）的act
$not_login_arr =
array('login','act_login','register','act_register','reyanzheng','act_edit_password','get_password','send_pwd_email','password', 'signin', 'add_tag', 'collect', 'return_to_cart', 'logout', 'email_list', 'validate_email', 'send_hash_mail', 'order_query', 'is_registered', 'check_email','clear_history','qpassword_name', 'get_passwd_question', 'check_answer');

/* 显示页面的action列表 */
$ui_arr = array('register', 'login', 'profile','reyanzheng', 'order_list', 'order_detail', 'address_list', 'collection_list',
'message_list', 'tag_list', 'get_password', 'reset_password', 'booking_list', 'add_booking', 'account_raply',
'account_deposit', 'account_log', 'account_detail', 'act_account', 'pay', 'default', 'bonus', 'group_buy', 'group_buy_detail', 'affiliate', 'comment_list','validate_email','track_packages', 'transform_points','qpassword_name', 'get_passwd_question', 'check_answer', 'card_merge', 'act_card_merge', 'film_order', 'dzq_order', 'dongpiao_order', 'yanchu_order','bangding_tel','coupons_order','piaoduoduo_order','venues_order','code_order');

/* 未登录处理 */
if (empty($_SESSION['user_id']))
{
    if (!in_array($action, $not_login_arr))
    {
        if (in_array($action, $ui_arr))
        {
            /* 如果需要登录,并是显示页面的操作，记录当前操作，用于登录后跳转到相应操作
            if ($action == 'login')
            {
                if (isset($_REQUEST['back_act']))
                {
                    $back_act = trim($_REQUEST['back_act']);
                }
            }
            else
            {}*/
            if (!empty($_SERVER['QUERY_STRING']))
            {
                $back_act = 'user.php?' . strip_tags($_SERVER['QUERY_STRING']);
            }
            header("Location: /hy/index.html\n");
            //$action = 'login';
        }
        else
        {
            //未登录提交数据。非正常途径提交数据！
            die($_LANG['require_login']);
        }
    }
}

/* 如果是显示页面，对页面进行相应赋值 */
if (in_array($action, $ui_arr))
{
    assign_template();
    $position = assign_ur_here(0, $_LANG['user_center']);
    $smarty->assign('page_title', $position['title']); // 页面标题
    $smarty->assign('ur_here',    $position['ur_here']);
    $sql = "SELECT value FROM " . $ecs->table('shop_config') . " WHERE id = 419";
    $row = $db->getRow($sql);
    $car_off = $row['value'];
    $smarty->assign('car_off',       $car_off);
    /* 是否显示积分兑换 */
    if (!empty($_CFG['points_rule']) && unserialize($_CFG['points_rule']))
    {
        $smarty->assign('show_transform_points',     1);
    }
    $smarty->assign('helps',      get_shop_help());        // 网店帮助
    $smarty->assign('data_dir',   DATA_DIR);   // 数据目录
    $smarty->assign('action',     $action);
    $smarty->assign('lang',       $_LANG);
    $smarty->assign('is_rights',  1);
}

//用户中心欢迎页
if ($action == 'default')
{
    include_once(ROOT_PATH .'includes/lib_clips.php');
    $sql = 'SELECT company_id FROM '.$GLOBALS['ecs']->table('users').' where user_id='.$user_id;
    $company_id = $GLOBALS['db']->getOne($sql);
    $sql1 = 'SELECT * FROM '.$GLOBALS['ecs']->table('company').' WHERE card_company_id = '.$company_id;
    $company = $GLOBALS['db']->getRow($sql1);
    $smarty->assign('company',$company);
    $smarty->display('user_clips.dwt');
}

/* 用户登录界面 */
elseif ($action == 'login')
{
    if (empty($back_act))
    {
        if (empty($back_act) && isset($GLOBALS['_SERVER']['HTTP_REFERER']))
        {
            $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
        }
        else
        {
            $back_act = 'user.php';
        }

    }


    $captcha = intval($_CFG['captcha']);
    if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
    {
        $GLOBALS['smarty']->assign('enabled_captcha', 1);
        $GLOBALS['smarty']->assign('rand', mt_rand());
    }

    $smarty->assign('back_act', $back_act);
    $smarty->display('user_passport.dwt');
}

/* 处理会员的登录 */
elseif ($action == 'act_login')
{
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $type = isset($_POST['type']) ? trim($_POST['type']) : '';
        
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
				exit('不是激活状态，请联系华影客服！');
			}
		}
		// 中影卡处理结果
		else
		{
			// 卡余额
			$cardMoney = $card_result['BalanceCash'];
			// 卡有效期
			$cardOutTime = date('Y-m-d',strtotime($card_result['ExpDate']));
            $company_id = 1601050002;//现无公司字段，防止出错，默认(聚优福利）
			if ($card_result['Status'] != '正常')
			{
				exit($card_result['Status']);
			}
		}
		
		// 执行本地操作		
		include_once(ROOT_PATH . 'includes/lib_passport.php');
		$int_uid = $db->getOne('SELECT user_id FROM '.$ecs->table('users'). " WHERE user_name = '$username'");
		if (empty($int_uid)){//插入用户信息
			$reg_date = gmtime();
			$last_ip  = real_ip();
//            设置默认值
            $userheader = '/hy/images/headpic.png';
            $basic = '保密';
            $pass_edit = 0;
//			$GLOBALS['db']->query('INSERT INTO ' . $GLOBALS['ecs']->table("users") . "(`user_name`, `password`, `card_money`, `reg_time`, `last_login`, `last_ip`, `youxiao_time`,`company_id`) VALUES ('$username', '".md5($password)."', '$cardMoney', '$reg_date', '$reg_date', '$last_ip', '".$cardOutTime."','".$company_id."')");
			$GLOBALS['db']->query('INSERT INTO ' . $GLOBALS['ecs']->table("users") . "(`user_name`, `password`, `card_money`, `reg_time`, `last_login`, `last_ip`, `youxiao_time`,`nickname`,`basic`,`pass_edit`,`pic`,`company_id`) VALUES ('$username', '".md5($password)."', '$cardMoney', '$reg_date', '$reg_date', '$last_ip', '".$cardOutTime."','".$username."','".$basic."','".$pass_edit."','".$userheader."','".$company_id."')");
		}else{//更新用户信息
			$GLOBALS['db']->query('UPDATE ' . $GLOBALS['ecs']->table("users") . " SET password='".md5($password)."', card_money = '$cardMoney', youxiao_time = '".$cardOutTime."' WHERE user_id = '$int_uid'");
		}
		
		
		// 卡类型判断
		if ( checkCardType($username, $type) == false )
		{
		    exit( '卡类型不符，请从新选择并登录！' );
		}	
		
		//设置本站登录成功
		$GLOBALS['user']->set_session($username);
		$GLOBALS['user']->set_cookie($username);
		$_SESSION['BalanceCash'] = $cardMoney;
		
		update_user_info();
		recalculate_price();
		echo 'success';
		exit;
		
	}
	else
	{
		exit($cardPay->getMessage());
	}
	
}

/* 退出会员中心 */
elseif ($action == 'logout')
{
    if ((!isset($back_act)|| empty($back_act)) && isset($GLOBALS['_SERVER']['HTTP_REFERER']))
    {
        $back_act = strpos($GLOBALS['_SERVER']['HTTP_REFERER'], 'user.php') ? './index.php' : $GLOBALS['_SERVER']['HTTP_REFERER'];
    }

    $user->logout();
    $ucdata = empty($user->ucdata)? "" : $user->ucdata;
	header("Location: index.php");     
}

/* 个人资料页面 */
elseif ($action == 'profile')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    $user_info = get_profile($user_id);   
    exit(json_encode( array('result'=>'true','body'=>$user_info)));    
}

/* 修改个人资料的处理 */
elseif ($action == 'act_edit_profile')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $birthday = trim($_POST['birthdayYear']) .'-'. trim($_POST['birthdayMonth']) .'-'. trim($_POST['birthdayDay']);
    $pic = isset($_POST['pic']) ? trim($_POST['pic']) : null ;
    $profile  = array(
        'user_id'  => $user_id,
        'sex'      => isset($_POST['sex'])   ? intval($_POST['sex']) : 0,
        'birthday' => $birthday,
        'nickname'    => isset($_POST['nickname']) ? trim($_POST['nickname']) : '',
        'xingqu'    => isset($_POST['xingqu']) ? trim($_POST['xingqu']) : '',
        'basic'    => isset($_POST['basic']) ? trim($_POST['basic']) : '',
     );
    if ( !empty($pic) )
    {
        $profile['pic'] = $pic; 
    }
 
    if (edit_profile($profile))
    {
		exit(json_encode( array('result'=>'true')));          
    }
    else
    {
        if ($user->error == ERR_EMAIL_EXISTS)
        {
            $msg = sprintf($_LANG['email_exist'], $profile['email']);
        }
        else
        {
            $msg = $_LANG['edit_profile_failed'];
        }
		exit(json_encode( array('result'=>'false','body'=>$msg)));          
    }
}

/* 密码找回-->修改密码界面 */
elseif ($action == 'get_password')
{
    include_once(ROOT_PATH . 'includes/lib_passport.php');

    if (isset($_GET['code']) && isset($_GET['uid'])) //从邮件处获得的act
    {
        $code = trim($_GET['code']);
        $uid  = intval($_GET['uid']);

        /* 判断链接的合法性 */
        $user_info = $user->get_profile_by_id($uid);
        if (empty($user_info) || ($user_info && md5($user_info['user_id'] . $_CFG['hash_code'] . $user_info['reg_time']) != $code))
        {
            show_message($_LANG['parm_error'], $_LANG['back_home_lnk'], './', 'info');
        }

        $smarty->assign('uid',    $uid);
        $smarty->assign('code',   $code);
        $smarty->assign('action', 'reset_password');
        $smarty->display('user_passport.dwt');
    }
    else
    {
        //显示用户名和email表单
        $smarty->display('user_passport.dwt');
    }
}

/* 密码找回-->输入用户名界面 */
elseif ($action == 'qpassword_name')
{
    //显示输入要找回密码的账号表单
    $smarty->display('user_passport.dwt');
}

/* 密码找回-->根据注册用户名取得密码提示问题界面 */
elseif ($action == 'get_passwd_question')
{
    if (empty($_POST['user_name']))
    {
        show_message($_LANG['no_passwd_question'], $_LANG['back_home_lnk'], './', 'info');
    }
    else
    {
        $user_name = trim($_POST['user_name']);
    }

    //取出会员密码问题和答案
    $sql = 'SELECT user_id, user_name, passwd_question, passwd_answer FROM ' . $ecs->table('users') . " WHERE user_name = '" . $user_name . "'";
    $user_question_arr = $db->getRow($sql);

    //如果没有设置密码问题，给出错误提示
    if (empty($user_question_arr['passwd_answer']))
    {
        show_message($_LANG['no_passwd_question'], $_LANG['back_home_lnk'], './', 'info');
    }

    $_SESSION['temp_user'] = $user_question_arr['user_id'];  //设置临时用户，不具有有效身份
    $_SESSION['temp_user_name'] = $user_question_arr['user_name'];  //设置临时用户，不具有有效身份
    $_SESSION['passwd_answer'] = $user_question_arr['passwd_answer'];   //存储密码问题答案，减少一次数据库访问

    $captcha = intval($_CFG['captcha']);
    if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
    {
        $GLOBALS['smarty']->assign('enabled_captcha', 1);
        $GLOBALS['smarty']->assign('rand', mt_rand());
    }

    $smarty->assign('passwd_question', $_LANG['passwd_questions'][$user_question_arr['passwd_question']]);
    $smarty->display('user_passport.dwt');
}

/* 密码找回-->根据提交的密码答案进行相应处理 */
elseif ($action == 'check_answer')
{
    $captcha = intval($_CFG['captcha']);
    if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
    {
        if (empty($_POST['captcha']))
        {
            show_message($_LANG['invalid_captcha'], $_LANG['back_retry_answer'], 'user.php?act=qpassword_name', 'error');
        }

        /* 检查验证码 */
        include_once('includes/cls_captcha.php');

        $validator = new captcha();
        $validator->session_word = 'captcha_login';
        if (!$validator->check_word($_POST['captcha']))
        {
            show_message($_LANG['invalid_captcha'], $_LANG['back_retry_answer'], 'user.php?act=qpassword_name', 'error');
        }
    }

    if (empty($_POST['passwd_answer']) || $_POST['passwd_answer'] != $_SESSION['passwd_answer'])
    {
        show_message($_LANG['wrong_passwd_answer'], $_LANG['back_retry_answer'], 'user.php?act=qpassword_name', 'info');
    }
    else
    {
        $_SESSION['user_id'] = $_SESSION['temp_user'];
        $_SESSION['user_name'] = $_SESSION['temp_user_name'];
        unset($_SESSION['temp_user']);
        unset($_SESSION['temp_user_name']);
        $smarty->assign('uid',    $_SESSION['user_id']);
        $smarty->assign('action', 'reset_password');
        $smarty->display('user_passport.dwt');
    }
}

/* 发送密码修改确认邮件 */
elseif ($action == 'send_pwd_email')
{
    include_once(ROOT_PATH . 'includes/lib_passport.php');

    /* 初始化会员用户名和邮件地址 */
    $user_name = !empty($_POST['user_name']) ? trim($_POST['user_name']) : '';
    $email     = !empty($_POST['email'])     ? trim($_POST['email'])     : '';

    //用户名和邮件地址是否匹配
    $user_info = $user->get_user_info($user_name);

    if ($user_info && $user_info['email'] == $email)
    {
        //生成code
         //$code = md5($user_info[0] . $user_info[1]);

        $code = md5($user_info['user_id'] . $_CFG['hash_code'] . $user_info['reg_time']);
        //发送邮件的函数
        if (send_pwd_email($user_info['user_id'], $user_name, $email, $code))
        {
            show_message($_LANG['send_success'] . $email, $_LANG['back_home_lnk'], './', 'info');
        }
        else
        {
            //发送邮件出错
            show_message($_LANG['fail_send_password'], $_LANG['back_page_up'], './', 'info');
        }
    }
    else
    {
        //用户名与邮件地址不匹配
        show_message($_LANG['username_no_email'], $_LANG['back_page_up'], '', 'info');
    }
}

/* 重置新密码 */
elseif ($action == 'reset_password')
{
		
    //显示重置密码的表单
    $smarty->display('user_passport.dwt');
}

/* 修改会员密码 */
elseif ($action == 'act_edit_password')
{
    $returnArray = array( 'result'=>'false', 'business'=>array(), 'msg'=>'成功');
    
    include_once(ROOT_PATH . 'includes/lib_passport.php');

    $old_password = isset($_POST['old_password']) ? trim($_POST['old_password']) : null;
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $user_id      = isset($_POST['uid'])  ? intval($_POST['uid']) : $user_id;
    $code         = isset($_POST['code']) ? trim($_POST['code'])  : '';

    if (strlen($new_password) < 6)
    {
		$returnArray['result'] = 'true';
		$returnArray['msg'] = '密码长度不能少于6位';
		exit( json_encode($returnArray) );
    }

    $user_name = $GLOBALS['db']->getOne("SELECT user_name FROM ".$GLOBALS['ecs']->table('users').' where user_id = '.$user_id);
	//修改卡密码
	$arr_param = array(	'CardInfo'=>array('CardNo'  => $user_name, 'CardPwd' => $old_password, 'CardNewPwd'=>$new_password) );
	$state = $cardPay->action($arr_param, 2);
	
	// 查询成功
	if ($state == 0)
	{			
		$sql="UPDATE ".$ecs->table('users'). "SET password = '".md5($new_password)."', `ec_salt`='0' WHERE user_id= '".$user_id."'";
		$db->query($sql);			
		
		$returnArray['result'] = 'true';
		$returnArray['msg'] = '成功';
		exit( json_encode($returnArray) );
	}
	else
	{
		$returnArray['result'] = 'false';
		$returnArray['msg'] = $cardPay->getMessage();
		exit( json_encode($returnArray) );
	}
}

/* 添加一个红包 */
elseif ($action == 'act_add_bonus')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $bouns_sn = isset($_POST['bonus_sn']) ? intval($_POST['bonus_sn']) : '';
	$bouns_pwd = isset($_POST['bonus_pwd']) ? trim($_POST['bonus_pwd']) : '';


    if (($str_msg = add_bonus($user_id, $bouns_sn, $bouns_pwd)) === true)
    {
		echo 'success';
		exit;
        show_message($_LANG['add_bonus_sucess'], $_LANG['back_up_page'], 'user.php?act=bonus', 'info');
    }
    else
    {
		echo $str_msg;
		exit;
        $err->show($_LANG['back_up_page'], 'user.php?act=bonus');
    }
}

/* 查看订单列表 */
elseif ($action == 'order_list')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('order_info'). " WHERE user_id = '$user_id' AND parent_order_id = 0");

    $pager  = get_pager('user.php', array('act' => $action), $record_count, $page);

    $orders = get_user_orders($user_id, $pager['size'], $pager['start']);
    $merge  = get_user_merge($user_id);
    $smarty->assign('merge',  $merge);
    $smarty->assign('pager',  $pager);
    $smarty->assign('orders', $orders);
    $smarty->display('order/orderList.dwt');
}
/* 绑定手机 */
elseif ($action == 'bangding_tel')
{
     include_once(ROOT_PATH . 'includes/sms.php');
     //$smarty->assign ( 'ur_here', '绑定手机' );
     $smarty->display('user_transaction.dwt');
}

/* 绑定手机发送验证码 */
elseif ($action == 'fasong')
{
    include_once(ROOT_PATH . 'includes/lib_cardApi.php');
    $tel=$_REQUEST['tel'];
    $verify = mt_rand(123456, 999999);//获取随机验证码
    $data=smsvrerify($tel,$verify,1);
    $smarty->assign('data', $data);
    //$smarty->assign('verify', $verify);
    //$smarty->display('user_transaction.dwt');
}

/* 查看订单详情 */
elseif ($action == 'order_detail')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'includes/lib_payment.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

    /* 订单详情 */
    $order = get_order_detail($order_id, $user_id);

    if ($order === false)
    {
        $err->show($_LANG['back_home_lnk'], './');

        exit;
    }

    /* 是否显示添加到购物车 */
    if ($order['extension_code'] != 'group_buy' && $order['extension_code'] != 'exchange_goods')
    {
        $smarty->assign('allow_to_cart', 1);
    }

    /* 订单商品 */
    $goods_list = order_goods($order_id);
    foreach ($goods_list AS $key => $value)
    {
        $goods_list[$key]['market_price'] = price_format($value['market_price'], false);
        $goods_list[$key]['goods_price']  = price_format($value['goods_price'], false);
        $goods_list[$key]['subtotal']     = price_format($value['subtotal'], false);
        $goods_list[$key]['goods_thumb']        = get_image_path($value['goods_id'], $value['goods_thumb'], true);
    }

     /* 设置能否修改使用余额数 */
    if ($order['order_amount'] > 0)
    {
        if ($order['order_status'] == OS_UNCONFIRMED || $order['order_status'] == OS_CONFIRMED)
        {
            $user = user_info($order['user_id']);
            if ($user['user_money'] + $user['credit_line'] > 0)
            {
                $smarty->assign('allow_edit_surplus', 1);
                $smarty->assign('max_surplus', sprintf($_LANG['max_surplus'], $user['user_money']));
            }
        }
    }

    /* 未发货，未付款时允许更换支付方式 */
    if ($order['order_amount'] > 0 && $order['pay_status'] == PS_UNPAYED && $order['shipping_status'] == SS_UNSHIPPED)
    {
        $payment_list = available_payment_list(false, 0, true);

        /* 过滤掉当前支付方式和余额支付方式 */
        if(is_array($payment_list))
        {
            foreach ($payment_list as $key => $payment)
            {
                if ($payment['pay_id'] == $order['pay_id'] || $payment['pay_code'] == 'balance')
                {
                    unset($payment_list[$key]);
                }
            }
        }
        $smarty->assign('payment_list', $payment_list);
    }

    /* 订单 支付 配送 状态语言项 */
    $order['order_status_cn'] = $_LANG['os'][$order['order_status']];
	$order['pay_statuses'] = $order['pay_status'];
    $order['pay_status_cn'] = $_LANG['ps'][$order['pay_status']];
    $order['shipping_status_cn'] = $_LANG['ss'][$order['shipping_status']];

    $smarty->assign('order',      $order);
    $smarty->assign('goods_list', $goods_list);
    $smarty->display('order/orderDetails.dwt');
}

/* 取消订单 */
elseif ($action == 'cancel_order')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

    if (cancel_order($order_id, $user_id))
    {
        ecs_header("Location: user.php?act=order_list\n");
        exit;
    }
    else
    {
        $err->show($_LANG['order_list_lnk'], 'user.php?act=order_list');
    }
}

/* 收货地址列表界面*/
elseif ($action == 'address_list')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');
    $smarty->assign('lang',  $_LANG);

    /* 获得用户所有的收货人信息 */
    $consignee_list = get_consignee_list($_SESSION['user_id']);
    $returnArray = array( array('result'=>'true', 'body'=>$consignee_list) );
    //exit(json_encode($returnArray)); 
     $smarty->assign('count', count($consignee_list));
    $smarty->assign('consignee_list', $consignee_list);
 
    //取得国家列表，如果有收货人列表，取得省市区列表
    /*foreach ($consignee_list AS $region_id => $consignee)
    {
        $consignee['country']  = isset($consignee['country'])  ? intval($consignee['country'])  : 0;
        $consignee['province'] = isset($consignee['province']) ? intval($consignee['province']) : 0;
        $consignee['city']     = isset($consignee['city'])     ? intval($consignee['city'])     : 0;
        $province_list[$region_id] = get_regions(1, $consignee['country']);
        $city_list[$region_id]     = get_regions(2, $consignee['province']);
        $district_list[$region_id] = get_regions(3, $consignee['city']);
    }*/
    /* 获取默认收货ID */
    //$address_id  = $db->getOne("SELECT address_id FROM " .$ecs->table('users'). " WHERE user_id='$user_id'");

    //赋值于模板
    //$smarty->assign('real_goods_count', 1);
    /* $smarty->assign('shop_province',    get_regions(1, $_CFG['shop_country']));
    $smarty->assign('province_list',    get_regions(1, $int_cityId));
    $smarty->assign('address',          $address_id);
    $smarty->assign('city_list',        $city_list);
    $smarty->assign('district_list',    $district_list); */
    //$smarty->assign('currency_format',  $_CFG['currency_format']);
    //$smarty->assign('integral_scale',   $_CFG['integral_scale']);
    //$smarty->assign('name_of_region',   array($_CFG['name_of_region_1'], $_CFG['name_of_region_2'], $_CFG['name_of_region_3'], $_CFG['name_of_region_4']));

    $smarty->display('user_transaction.dwt');
}

/* 添加/编辑收货地址的处理 */
elseif ($action == 'act_edit_address')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');
    $smarty->assign('lang', $_LANG);

    $address = array(
        'user_id'    => $user_id,
        'address_id' => intval($_POST['address_id']),
        'country'    => isset($_POST['country'])   ? intval($_POST['country'])  : 0,
        'province'   => isset($_POST['province'])  ? intval($_POST['province']) : 0,
        'city'       => isset($_POST['city'])      ? intval($_POST['city'])     : 0,
        'district'   => isset($_POST['district'])  ? intval($_POST['district']) : 0,
        'address'    => isset($_POST['address'])   ? compile_str(trim($_POST['address']))    : '',
        'consignee'  => isset($_POST['consignee']) ? compile_str(trim($_POST['consignee']))  : '',
        'email'      => isset($_POST['email'])     ? compile_str(trim($_POST['email']))      : '',
        'tel'        => isset($_POST['tel'])       ? compile_str(make_semiangle(trim($_POST['tel']))) : '',
        'mobile'     => isset($_POST['mobile'])    ? compile_str(make_semiangle(trim($_POST['mobile']))) : '',
        'best_time'  => isset($_POST['best_time']) ? compile_str(trim($_POST['best_time']))  : '',
        'sign_building' => isset($_POST['sign_building']) ? compile_str(trim($_POST['sign_building'])) : '',
        'zipcode'       => isset($_POST['zipcode'])       ? compile_str(make_semiangle(trim($_POST['zipcode']))) : '',
        );

    if (update_address($address))
    {
        show_message($_LANG['edit_address_success'], $_LANG['address_list_lnk'], 'user.php?act=address_list');
    }
}

/* 删除收货地址 */
elseif ($action == 'drop_consignee')
{
    include_once('includes/lib_transaction.php');

    $consignee_id = intval($_GET['id']);

    if (drop_consignee($consignee_id))
    {
        ecs_header("Location: user.php?act=address_list\n");
        exit;
    }
    else
    {
        show_message($_LANG['del_address_false']);
    }
}

/* 显示收藏商品列表 */
elseif ($action == 'collection_list')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('collect_goods').
                                " WHERE user_id='$user_id' ORDER BY add_time DESC");

    $pager = get_pager('user.php', array('act' => $action), $record_count, $page);
    $smarty->assign('pager', $pager);
    $smarty->assign('goods_list', get_collection_goods($user_id, $pager['size'], $pager['start']));
    $smarty->assign('url',        $ecs->url());
    $lang_list = array(
        'UTF8'   => $_LANG['charset']['utf8'],
        'GB2312' => $_LANG['charset']['zh_cn'],
        'BIG5'   => $_LANG['charset']['zh_tw'],
    );
    $smarty->assign('lang_list',  $lang_list);
    $smarty->assign('user_id',  $user_id);
    $smarty->display('user_clips.dwt');
}

/* 删除收藏的商品 */
elseif ($action == 'delete_collection')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $collection_id = isset($_GET['collection_id']) ? intval($_GET['collection_id']) : 0;

    if ($collection_id > 0)
    {
        $db->query('DELETE FROM ' .$ecs->table('collect_goods'). " WHERE rec_id='$collection_id' AND user_id ='$user_id'" );
    }

    ecs_header("Location: user.php?act=collection_list\n");
    exit;
}

/* 添加关注商品 */
elseif ($action == 'add_to_attention')
{
    $rec_id = (int)$_GET['rec_id'];
    if ($rec_id)
    {
        $db->query('UPDATE ' .$ecs->table('collect_goods'). "SET is_attention = 1 WHERE rec_id='$rec_id' AND user_id ='$user_id'" );
    }
    ecs_header("Location: user.php?act=collection_list\n");
    exit;
}
/* 取消关注商品 */
elseif ($action == 'del_attention')
{
    $rec_id = (int)$_GET['rec_id'];
    if ($rec_id)
    {
        $db->query('UPDATE ' .$ecs->table('collect_goods'). "SET is_attention = 0 WHERE rec_id='$rec_id' AND user_id ='$user_id'" );
    }
    ecs_header("Location: user.php?act=collection_list\n");
    exit;
}
/* 显示留言列表 */
elseif ($action == 'message_list')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $order_id = empty($_GET['order_id']) ? 0 : intval($_GET['order_id']);
    $order_info = array();

    /* 获取用户留言的数量 */
    if ($order_id)
    {
        $sql = "SELECT COUNT(*) FROM " .$ecs->table('feedback').
                " WHERE parent_id = 0 AND order_id = '$order_id' AND user_id = '$user_id'";
        $order_info = $db->getRow("SELECT * FROM " . $ecs->table('order_info') . " WHERE order_id = '$order_id' AND user_id = '$user_id'");
        $order_info['url'] = 'user.php?act=order_detail&order_id=' . $order_id;
    }
    else
    {
        $sql = "SELECT COUNT(*) FROM " .$ecs->table('feedback').
           " WHERE parent_id = 0 AND user_id = '$user_id' AND user_name = '" . $_SESSION['user_name'] . "' AND order_id=0";
    }

    $record_count = $db->getOne($sql);
    $act = array('act' => $action);

    if ($order_id != '')
    {
        $act['order_id'] = $order_id;
    }

    $pager = get_pager('user.php', $act, $record_count, $page, 5);

    $smarty->assign('message_list', get_message_list($user_id, $_SESSION['user_name'], $pager['size'], $pager['start'], $order_id));
    $smarty->assign('pager',        $pager);
    $smarty->assign('order_info',   $order_info);
    $smarty->display('user_clips.dwt');
}

/* 显示评论列表 */
elseif ($action == 'comment_list')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    /* 获取用户留言的数量 */
    $sql = "SELECT COUNT(*) FROM " .$ecs->table('comment').
           " WHERE parent_id = 0 AND user_id = '$user_id'";
    $record_count = $db->getOne($sql);
    $pager = get_pager('user.php', array('act' => $action), $record_count, $page, 5);

    $smarty->assign('comment_list', get_comment_list($user_id, $pager['size'], $pager['start']));
    $smarty->assign('pager',        $pager);
    $smarty->display('user_clips.dwt');
}

/* 添加我的留言 */
elseif ($action == 'act_add_message')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $message = array(
        'user_id'     => $user_id,
        'user_name'   => $_SESSION['user_name'],
        'user_email'  => $_SESSION['email'],
        'msg_type'    => isset($_POST['msg_type']) ? intval($_POST['msg_type'])     : 0,
        'msg_title'   => isset($_POST['msg_title']) ? trim($_POST['msg_title'])     : '',
        'msg_content' => isset($_POST['msg_content']) ? trim($_POST['msg_content']) : '',
        'order_id'=>empty($_POST['order_id']) ? 0 : intval($_POST['order_id']),
        'upload'      => (isset($_FILES['message_img']['error']) && $_FILES['message_img']['error'] == 0) || (!isset($_FILES['message_img']['error']) && isset($_FILES['message_img']['tmp_name']) && $_FILES['message_img']['tmp_name'] != 'none')
         ? $_FILES['message_img'] : array()
     );

    if (add_message($message))
    {
        show_message($_LANG['add_message_success'], $_LANG['message_list_lnk'], 'user.php?act=message_list&order_id=' . $message['order_id'],'info');
    }
    else
    {
        $err->show($_LANG['message_list_lnk'], 'user.php?act=message_list');
    }
}

/* 标签云列表 */
elseif ($action == 'tag_list')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $good_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $smarty->assign('tags',      get_user_tags($user_id));
    $smarty->assign('tags_from', 'user');
    $smarty->display('user_clips.dwt');
}

/* 删除标签云的处理 */
elseif ($action == 'act_del_tag')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $tag_words = isset($_GET['tag_words']) ? trim($_GET['tag_words']) : '';
    delete_tag($tag_words, $user_id);

    ecs_header("Location: user.php?act=tag_list\n");
    exit;

}

/* 显示缺货登记列表 */
elseif ($action == 'booking_list')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    /* 获取缺货登记的数量 */
    $sql = "SELECT COUNT(*) " .
            "FROM " .$ecs->table('booking_goods'). " AS bg, " .
                     $ecs->table('goods') . " AS g " .
            "WHERE bg.goods_id = g.goods_id AND user_id = '$user_id'";
    $record_count = $db->getOne($sql);
    $pager = get_pager('user.php', array('act' => $action), $record_count, $page);

    $smarty->assign('booking_list', get_booking_list($user_id, $pager['size'], $pager['start']));
    $smarty->assign('pager',        $pager);
    $smarty->display('user_clips.dwt');
}
/* 添加缺货登记页面 */
elseif ($action == 'add_booking')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $goods_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($goods_id == 0)
    {
        show_message($_LANG['no_goods_id'], $_LANG['back_page_up'], '', 'error');
    }

    /* 根据规格属性获取货品规格信息 */
    $goods_attr = '';
    if ($_GET['spec'] != '')
    {
        $goods_attr_id = $_GET['spec'];

        $attr_list = array();
        $sql = "SELECT a.attr_name, g.attr_value " .
                "FROM " . $ecs->table('goods_attr') . " AS g, " .
                    $ecs->table('attribute') . " AS a " .
                "WHERE g.attr_id = a.attr_id " .
                "AND g.goods_attr_id " . db_create_in($goods_attr_id);
        $res = $db->query($sql);
        while ($row = $db->fetchRow($res))
        {
            $attr_list[] = $row['attr_name'] . ': ' . $row['attr_value'];
        }
        $goods_attr = join(chr(13) . chr(10), $attr_list);
    }
    $smarty->assign('goods_attr', $goods_attr);

    $smarty->assign('info', get_goodsinfo($goods_id));
    $smarty->display('user_clips.dwt');

}

/* 添加缺货登记的处理 */
elseif ($action == 'act_add_booking')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $booking = array(
        'goods_id'     => isset($_POST['id'])      ? intval($_POST['id'])     : 0,
        'goods_amount' => isset($_POST['number'])  ? intval($_POST['number']) : 0,
        'desc'         => isset($_POST['desc'])    ? trim($_POST['desc'])     : '',
        'linkman'      => isset($_POST['linkman']) ? trim($_POST['linkman'])  : '',
        'email'        => isset($_POST['email'])   ? trim($_POST['email'])    : '',
        'tel'          => isset($_POST['tel'])     ? trim($_POST['tel'])      : '',
        'booking_id'   => isset($_POST['rec_id'])  ? intval($_POST['rec_id']) : 0
    );

    // 查看此商品是否已经登记过
    $rec_id = get_booking_rec($user_id, $booking['goods_id']);
    if ($rec_id > 0)
    {
        show_message($_LANG['booking_rec_exist'], $_LANG['back_page_up'], '', 'error');
    }

    if (add_booking($booking))
    {
        show_message($_LANG['booking_success'], $_LANG['back_booking_list'], 'user.php?act=booking_list',
        'info');
    }
    else
    {
        $err->show($_LANG['booking_list_lnk'], 'user.php?act=booking_list');
    }
}

/* 删除缺货登记 */
elseif ($action == 'act_del_booking')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id == 0 || $user_id == 0)
    {
        ecs_header("Location: user.php?act=booking_list\n");
        exit;
    }

    $result = delete_booking($id, $user_id);
    if ($result)
    {
        ecs_header("Location: user.php?act=booking_list\n");
        exit;
    }
}

/* 确认收货 */
elseif ($action == 'affirm_received')
{
	include_once(ROOT_PATH . 'includes/lib_order.php');
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

    if (affirm_received($order_id, $user_id))
    {
        ecs_header("Location: user.php?act=order_list\n");
        exit;
    }
    else
    {
        $err->show($_LANG['order_list_lnk'], 'user.php?act=order_list');
    }
}

/* 会员退款申请界面 */
elseif ($action == 'account_raply')
{
    $smarty->display('user_transaction.dwt');
}

/* 会员预付款界面 */
elseif ($action == 'account_deposit')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    $surplus_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $account    = get_surplus_info($surplus_id);
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $user_id = $user_id?$user_id:intval($_REQUEST['user_id']);
    /* 获取记录条数 */
    $sql = "SELECT COUNT(*) FROM " .$ecs->table('user_account').
           " WHERE user_id = '$user_id'" .
           " AND process_type " . db_create_in(array(SURPLUS_SAVE, SURPLUS_RETURN));
    $record_count = $db->getOne($sql);

    //分页函数
    $pager = get_pager('user.php', array('act' => $action), $record_count, $page);

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
    	if ( !empty($pay_price) && $pay_price > 0.5)
    	{
    		$pay_than = $pay_price;
    	}
    }
     
    $priceList = array( 30=>30, 50=>50, 100=>100 );
    foreach($priceList as &$point){
    	$point = price_format($point * $pay_than);
    }
    
    //获取余额记录
    $account_log = get_account_log($user_id, $pager['size'], $pager['start']);
	
    //模板赋值
//    $smarty->assign('priceList', $priceList);
//    $smarty->assign('surplus_amount', price_format($surplus_amount, false));
//    $smarty->assign('account_log',    $account_log);
//    $smarty->assign('pager',          $pager);
//
//
//    $smarty->assign('payment', get_online_payment_list(false));
//    $smarty->assign('order',   $account);
//    $smarty->display('user_transaction.dwt');
    $rudata['result']='true';
    $rudata['info']=array(
        'priceList'=>$priceList,
        'surplus_amount'=>price_format($surplus_amount, false),
        'account_log'=>$account_log,
        'pager'=>$pager,
        'payment'=>get_online_payment_list(false),
        'order'=>$account,
    );

    $jsondData=json_encode($rudata);
    echo $jsondData;
}

/* 会员账目明细界面 */
elseif ($action == 'account_detail')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $account_type = 'user_money';

    /* 获取记录条数 */
    $sql = "SELECT COUNT(*) FROM " .$ecs->table('account_log').
           " WHERE user_id = '$user_id'" .
           " AND $account_type <> 0 ";
    $record_count = $db->getOne($sql);

    //分页函数
    $pager = get_pager('user.php', array('act' => $action), $record_count, $page);

    //获取剩余余额
    $surplus_amount = get_user_surplus($user_id);
    if (empty($surplus_amount))
    {
        $surplus_amount = 0;
    }

    //获取余额记录
    $account_log = array();
    $sql = "SELECT * FROM " . $ecs->table('account_log') .
           " WHERE user_id = '$user_id'" .
           " AND $account_type <> 0 " .
           " ORDER BY log_id DESC";
    $res = $GLOBALS['db']->selectLimit($sql, $pager['size'], $pager['start']);
    while ($row = $db->fetchRow($res))
    {
        $row['change_time'] = local_date($_CFG['date_format'], $row['change_time']);
        $row['type'] = $row[$account_type] > 0 ? $_LANG['account_inc'] : $_LANG['account_dec'];
        $row['user_money'] = price_format(abs($row['user_money']), false);
        $row['frozen_money'] = price_format(abs($row['frozen_money']), false);
        $row['rank_points'] = abs($row['rank_points']);
        $row['pay_points'] = abs($row['pay_points']);
        $row['short_change_desc'] = sub_str($row['change_desc'], 60);
        $row['amount'] = $row[$account_type];
        $account_log[] = $row;
    }

    //模板赋值
    $smarty->assign('surplus_amount', price_format($surplus_amount, false));
    $smarty->assign('account_log',    $account_log);
    $smarty->assign('pager',          $pager);
    $smarty->display('user_transaction.dwt');
}

/* 会员充值和提现申请记录 */
elseif ($action == 'account_log')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    /* 获取记录条数 */
    $sql = "SELECT COUNT(*) FROM " .$ecs->table('user_account').
           " WHERE user_id = '$user_id'" .
           " AND process_type " . db_create_in(array(SURPLUS_SAVE, SURPLUS_RETURN));
    $record_count = $db->getOne($sql);

    //分页函数
    $pager = get_pager('user.php', array('act' => $action), $record_count, $page);

    //获取剩余余额
    $surplus_amount = get_user_surplus($user_id);
    if (empty($surplus_amount))
    {
        $surplus_amount = 0;
    }

    //获取余额记录
    $sql1 = 'SELECT * FROM ' .$GLOBALS['ecs']->table('user_account').
        " WHERE user_id = '$user_id'" .
        " AND process_type " . db_create_in(array(SURPLUS_SAVE, SURPLUS_RETURN)) .
        " ORDER BY add_time DESC";
//    $account_log = get_account_log($user_id, $pager['size'], $pager['start']);
    $res = $GLOBALS['db']->query($sql1);
    if ($res)
    {
        while ($rows = $GLOBALS['db']->fetchRow($res))
        {
            $rows['add_time']         = local_date($GLOBALS['_CFG']['date_format'], $rows['add_time']);
            $rows['admin_note']       = nl2br(htmlspecialchars($rows['admin_note']));
            $rows['short_admin_note'] = ($rows['admin_note'] > '') ? sub_str($rows['admin_note'], 30) : 'N/A';
            $rows['user_note']        = nl2br(htmlspecialchars($rows['user_note']));
            $rows['short_user_note']  = ($rows['user_note'] > '') ? sub_str($rows['user_note'], 30) : 'N/A';
            $rows['pay_status']       = ($rows['is_paid'] == 0) ? $GLOBALS['_LANG']['ps'][PS_UNPAYED] : $GLOBALS['_LANG']['is_confirm'];
            $rows['amount']           = price_format(abs($rows['amount']), false);

            /* 会员的操作类型： 冲值，提现 */
            if ($rows['process_type'] == 0)
            {
                $rows['type'] = $GLOBALS['_LANG']['surplus_type_0'];
            }
            else
            {
                $rows['type'] = $GLOBALS['_LANG']['surplus_type_1'];
            }

            /* 支付方式的ID */
            $sql = 'SELECT pay_id FROM ' .$GLOBALS['ecs']->table('payment').
                " WHERE pay_name = '$rows[payment]' AND enabled = 1";
            $pid = $GLOBALS['db']->getOne($sql);

            /* 如果是预付款而且还没有付款, 允许付款 */
            if (($rows['is_paid'] == 0) && ($rows['process_type'] == 0))
            {
//                $rows['handle'] = '<a href="user.php?act=pay&id='.$rows['id'].'&pid='.$pid.'">'.$GLOBALS['_LANG']['pay'].'</a>';
                $rows['handle'] = '<a class="deposit_pay" href="javascript:void(0);" data-href="user.php?act=pay&id='.$rows['id'].'&pid='.$pid.'">'.$GLOBALS['_LANG']['pay'].'</a>';
            }

            $account_log[] = $rows;
        }

    }
    else
    {
        $account_log=array();
    }

    //模板赋值
//    $smarty->assign('surplus_amount', price_format($surplus_amount, false));
//    $smarty->assign('account_log',    $account_log);
//    $smarty->assign('pager',          $pager);
//    $smarty->display('user_transaction.dwt');
    $rudata['result']='true';
    $rudata['info']=array(
        'surplus_amount'=>price_format($surplus_amount, false),
        'account_log'=>$account_log,
        'pager'=>$pager,
    );

    $jsondData=json_encode($rudata);
    echo $jsondData;

}

/* 对会员余额申请的处理 */
elseif ($action == 'act_account')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    if ($amount <= 0)
    {
//        show_message($_LANG['amount_gt_zero']);
        $redata['result'] = 'false';
        $redata['msg'] = $_LANG['amount_gt_zero'];
        $jsondData = json_encode($redata);
        exit($jsondData);
    }
    /* 变量初始化 */
    $surplus = array(
            'user_id'      => $user_id,
            'rec_id'       => !empty($_POST['rec_id'])      ? intval($_POST['rec_id'])       : 0,
            'process_type' => isset($_POST['surplus_type']) ? intval($_POST['surplus_type']) : 0,
            'payment_id'   => isset($_POST['payment_id'])   ? intval($_POST['payment_id'])   : 0,
            'user_note'    => isset($_POST['user_note'])    ? trim($_POST['user_note'])      : '',
            'amount'       => $amount
    );

    /* 退款申请的处理 */
    if ($surplus['process_type'] == 1)
    {
        /* 判断是否有足够的余额的进行退款的操作 */
        $sur_amount = get_user_surplus($user_id);
        if ($amount > $sur_amount)
        {
            $content = $_LANG['surplus_amount_error'];
            $redata['result'] = 'false';
            $redata['msg'] = $content;
            $jsondData = json_encode($redata);
            exit($jsondData);

//            show_message($content, $_LANG['back_page_up'], '', 'info');
        }

        //插入会员账目明细
        $amount = '-'.$amount;
        $surplus['payment'] = '';
        $surplus['rec_id']  = insert_user_account($surplus, $amount);

        /* 如果成功提交 */
        if ($surplus['rec_id'] > 0)
        {
            $content = $_LANG['surplus_appl_submit'];
            $redata['result'] = 'true';
            $redata['msg'] = $content;
            $jsondData = json_encode($redata);
            exit($jsondData);

//            show_message($content, $_LANG['back_account_log'], 'user.php?act=account_log', 'info');
        }
        else
        {
            $content = $_LANG['process_false'];
            $redata['result'] = 'false';
            $redata['msg'] = $content;
            $jsondData = json_encode($redata);
            exit($jsondData);

//            show_message($content, $_LANG['back_page_up'], '', 'info');
        }
    }
    /* 如果是会员预付款，跳转到下一步，进行线上支付的操作 */
    else
    {
        if ($surplus['payment_id'] <= 0)
        {
//            show_message($_LANG['select_payment_pls']);
            $redata['result'] = 'false';
            $redata['msg'] = $_LANG['select_payment_pls'];
            $jsondData = json_encode($redata);
            exit($jsondData);

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
        		if ( !empty($pay_price) && $pay_price > 0.5)
        		{
        			$pay_than = $pay_price;
        		}
        	}
        		
        	$priceList = array( 30=>30, 50=>50, 100=>100);
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
//					show_message($arr_cardInfo['ReturnMessage']);
                    $redata['result'] = 'false';
                    $redata['msg'] = $arr_cardInfo['ReturnMessage'];
                    $jsondData = json_encode($redata);
                    exit($jsondData);

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
        $payment_info['pay_button'] = $pay_obj->get_code($order, $payment);

        /* 模板赋值 */
//        $smarty->assign('payment', $payment_info);
//        $smarty->assign('pay_fee', price_format($payment_info['pay_fee'], false));
//        $smarty->assign('amount',  price_format($amount, false));
//        $smarty->assign('order',   $order);
//        $smarty->display('user_transaction.dwt');
        $redata['result'] = 'true';
        $redata['info'] = array(
            'payment'=> $payment_info,
            'pay_fee'=>price_format($payment_info['pay_fee'], false),
            'amount'=>price_format($amount, false),
            'order'=>$order,
        );
        $jsondData = json_encode($redata);
        exit($jsondData);

    }
}

/* 删除会员余额 */
elseif ($action == 'cancel')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id == 0 || $user_id == 0)
    {
//        ecs_header("Location: user.php?act=account_log\n");
        $redata['result'] = 'false';
        $redata['msg']='刷新重试！';
        echo json_encode($redata);
        exit;
    }

    $result = del_user_account($id, $user_id);
    if ($result)
    {
//        ecs_header("Location: user.php?act=account_log\n");
        $redata['result'] = 'true';
        $redata['msg']='删除成功！';
        echo json_encode($redata);
        exit;
    }
}

/* 会员通过帐目明细列表进行再付款的操作 */
elseif ($action == 'pay')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');
    include_once(ROOT_PATH . 'includes/lib_payment.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');

    //变量初始化
    $surplus_id = isset($_GET['id'])  ? intval($_GET['id'])  : 0;
    $payment_id = isset($_GET['pid']) ? intval($_GET['pid']) : 0;

    if ($surplus_id == 0)
    {
//        ecs_header("Location: user.php?act=account_log\n");
        $redata['result']='false';
        $redata['info']['action']='account_log';
        $redata['msg']='该条付款已失效！';
        echo json_encode($redata);
        exit;
    }

    //如果原来的支付方式已禁用或者已删除, 重新选择支付方式
    if ($payment_id == 0)
    {
//        ecs_header("Location: user.php?act=account_deposit&id=".$surplus_id."\n");

        include_once(ROOT_PATH.'includes/httpRequest.php');
        $Http = new HttpRequest();
        $url = $_SERVER['SERVER_NAME']."/user.php?act=account_deposit&id=".$surplus_id."&user_id=".$user_id;
        $data = $Http->get($url,'','','curl');
        $dataArr = json_decode($data,ture);

        $redata['result']='false';
        $redata['msg']='请重新选择支付方式！';
        $redata['info'] = $dataArr['info'];
        $redata['info']['action']='account_deposit';
        echo json_encode($redata);
        exit;
    }

    //获取单条会员帐目信息
    $order = array();
    $order = get_surplus_info($surplus_id);

    //支付方式的信息
    $payment_info = array();
    $payment_info = payment_info($payment_id);

    /* 如果当前支付方式没有被禁用，进行支付的操作 */
    if (!empty($payment_info))
    {
        //取得支付信息，生成支付代码
        $payment = unserialize_config($payment_info['pay_config']);

        //生成伪订单号
        $order['order_sn'] = $surplus_id;

        //获取需要支付的log_id
        $order['log_id'] = get_paylog_id($surplus_id, $pay_type = PAY_SURPLUS);

        $order['user_name']      = $_SESSION['user_name'];
        $order['surplus_amount'] = $order['amount'];

        //计算支付手续费用
        $payment_info['pay_fee'] = pay_fee($payment_id, $order['surplus_amount'], 0);

        //计算此次预付款需要支付的总金额
        $order['order_amount']   = $order['surplus_amount'] + $payment_info['pay_fee'];

        //如果支付费用改变了，也要相应的更改pay_log表的order_amount
        $order_amount = $db->getOne("SELECT order_amount FROM " .$ecs->table('pay_log')." WHERE log_id = '$order[log_id]'");
        if ($order_amount <> $order['order_amount'])
        {
            $db->query("UPDATE " .$ecs->table('pay_log').
                       " SET order_amount = '$order[order_amount]' WHERE log_id = '$order[log_id]'");
        }

        /* 调用相应的支付方式文件 */
        include_once(ROOT_PATH . 'includes/modules/payment/' . $payment_info['pay_code'] . '.php');

        /* 取得在线支付方式的支付按钮 */
        $pay_obj = new $payment_info['pay_code'];
        $payment_info['pay_button'] = $pay_obj->get_code($order, $payment);

        /* 模板赋值 */
//        $smarty->assign('payment', $payment_info);
//        $smarty->assign('order',   $order);
//        $smarty->assign('pay_fee', price_format($payment_info['pay_fee'], false));
//        $smarty->assign('amount',  price_format($order['surplus_amount'], false));
//        $smarty->assign('action',  'act_account');
//        $smarty->display('user_transaction.dwt');
        $redata['result'] = 'true';
        $redata['info'] = array(
            'payment'=> $payment_info,
            'pay_fee'=>price_format($payment_info['pay_fee'], false),
            'amount'=>price_format($order['surplus_amount'], false),
            'order'=>$order,
            'action'=>'act_account',
        );
        $jsondData = json_encode($redata);
        exit($jsondData);

    }
    /* 重新选择支付方式 */
    else
    {
        include_once(ROOT_PATH . 'includes/lib_clips.php');

//        $smarty->assign('payment', get_online_payment_list());
//        $smarty->assign('order',   $order);
//        $smarty->assign('action',  'account_deposit');
//        $smarty->display('user_transaction.dwt');
        $redata['result'] = 'false';
        $redata['msg']='请重新选择支付方式！';
        $redata['info'] = array(
            'payment'=> get_online_payment_list(),
            'order'=>$order,
            'action'=>'account_deposit',
        );
    }
}

/* 添加标签(ajax) */
elseif ($action == 'add_tag')
{
    include_once('includes/cls_json.php');
    include_once('includes/lib_clips.php');

    $result = array('error' => 0, 'message' => '', 'content' => '');
    $id     = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $tag    = isset($_POST['tag']) ? json_str_iconv(trim($_POST['tag'])) : '';

    if ($user_id == 0)
    {
        /* 用户没有登录 */
        $result['error']   = 1;
        $result['message'] = $_LANG['tag_anonymous'];
    }
    else
    {
        add_tag($id, $tag); // 添加tag
        clear_cache_files('goods'); // 删除缓存

        /* 重新获得该商品的所有缓存 */
        $arr = get_tags($id);

        foreach ($arr AS $row)
        {
            $result['content'][] = array('word' => htmlspecialchars($row['tag_words']), 'count' => $row['tag_count']);
        }
    }

    $json = new JSON;

    echo $json->encode($result);
    exit;
}

/* 添加收藏商品(ajax) */
elseif ($action == 'collect')
{
    include_once(ROOT_PATH .'includes/cls_json.php');
    $json = new JSON();
    $result = array('error' => 0, 'message' => '');
    $goods_id = $_GET['id'];

    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == 0)
    {
        $result['error'] = 1;
        $result['message'] = $_LANG['login_please'];
        die($json->encode($result));
    }
    else
    {
        /* 检查是否已经存在于用户的收藏夹 */
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('collect_goods') .
            " WHERE user_id='$_SESSION[user_id]' AND goods_id = '$goods_id'";
        if ($GLOBALS['db']->GetOne($sql) > 0)
        {
            $result['error'] = 1;
            $result['message'] = $GLOBALS['_LANG']['collect_existed'];
            die($json->encode($result));
        }
        else
        {
            $time = gmtime();
            $sql = "INSERT INTO " .$GLOBALS['ecs']->table('collect_goods'). " (user_id, goods_id, add_time)" .
                    "VALUES ('$_SESSION[user_id]', '$goods_id', '$time')";

            if ($GLOBALS['db']->query($sql) === false)
            {
                $result['error'] = 1;
                $result['message'] = $GLOBALS['db']->errorMsg();
                die($json->encode($result));
            }
            else
            {
                $result['error'] = 0;
                $result['message'] = $GLOBALS['_LANG']['collect_success'];
                die($json->encode($result));
            }
        }
    }
}

/* 删除留言 */
elseif ($action == 'del_msg')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $order_id = empty($_GET['order_id']) ? 0 : intval($_GET['order_id']);

    if ($id > 0)
    {
        $sql = 'SELECT user_id, message_img FROM ' .$ecs->table('feedback'). " WHERE msg_id = '$id' LIMIT 1";
        $row = $db->getRow($sql);
        if ($row && $row['user_id'] == $user_id)
        {
            /* 验证通过，删除留言，回复，及相应文件 */
            if ($row['message_img'])
            {
                @unlink(ROOT_PATH . DATA_DIR . '/feedbackimg/'. $row['message_img']);
            }
            $sql = "DELETE FROM " .$ecs->table('feedback'). " WHERE msg_id = '$id' OR parent_id = '$id'";
            $db->query($sql);
        }
    }
    ecs_header("Location: user.php?act=message_list&order_id=$order_id\n");
    exit;
}

/* 删除评论 */
elseif ($action == 'del_cmt')
{
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id > 0)
    {
        $sql = "DELETE FROM " .$ecs->table('comment'). " WHERE comment_id = '$id' AND user_id = '$user_id'";
        $db->query($sql);
    }
    ecs_header("Location: user.php?act=comment_list\n");
    exit;
}

/* 合并订单 */
elseif ($action == 'merge_order')
{
    include_once(ROOT_PATH .'includes/lib_transaction.php');
    include_once(ROOT_PATH .'includes/lib_order.php');
    $from_order = isset($_POST['from_order']) ? trim($_POST['from_order']) : '';
    $to_order   = isset($_POST['to_order']) ? trim($_POST['to_order']) : '';
    if (merge_user_order($from_order, $to_order, $user_id))
    {
        show_message($_LANG['merge_order_success'],$_LANG['order_list_lnk'],'user.php?act=order_list', 'info');
    }
    else
    {
        $err->show($_LANG['order_list_lnk']);
    }
}
/* 将指定订单中商品添加到购物车 */
elseif ($action == 'return_to_cart')
{
    include_once(ROOT_PATH .'includes/cls_json.php');
    include_once(ROOT_PATH .'includes/lib_transaction.php');
    $json = new JSON();

    $result = array('error' => 0, 'message' => '', 'content' => '');
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    if ($order_id == 0)
    {
        $result['error']   = 1;
        $result['message'] = $_LANG['order_id_empty'];
        die($json->encode($result));
    }

    if ($user_id == 0)
    {
        /* 用户没有登录 */
        $result['error']   = 1;
        $result['message'] = $_LANG['login_please'];
        die($json->encode($result));
    }

    /* 检查订单是否属于该用户 */
    $order_user = $db->getOne("SELECT user_id FROM " .$ecs->table('order_info'). " WHERE order_id = '$order_id'");
    if (empty($order_user))
    {
        $result['error'] = 1;
        $result['message'] = $_LANG['order_exist'];
        die($json->encode($result));
    }
    else
    {
        if ($order_user != $user_id)
        {
            $result['error'] = 1;
            $result['message'] = $_LANG['no_priv'];
            die($json->encode($result));
        }
    }

    $message = return_to_cart($order_id);

    if ($message === true)
    {
        $result['error'] = 0;
        $result['message'] = $_LANG['return_to_cart_success'];
        die($json->encode($result));
    }
    else
    {
        $result['error'] = 1;
        $result['message'] = $_LANG['order_exist'];
        die($json->encode($result));
    }

}

/* 编辑使用余额支付的处理 */
elseif ($action == 'act_edit_surplus')
{
    /* 检查是否登录 */
    if ($_SESSION['user_id'] <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查订单号 */
    $order_id = intval($_POST['order_id']);
    if ($order_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查余额 */
    $surplus = floatval($_POST['surplus']);
    if ($surplus <= 0)
    {
        $err->add($_LANG['error_surplus_invalid']);
        $err->show($_LANG['order_detail'], 'user.php?act=order_detail&order_id=' . $order_id);
    }

    include_once(ROOT_PATH . 'includes/lib_order.php');

    /* 取得订单 */
    $order = order_info($order_id);
    if (empty($order))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查订单用户跟当前用户是否一致 */
    if ($_SESSION['user_id'] != $order['user_id'])
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查订单是否未付款，检查应付款金额是否大于0 */
    if ($order['pay_status'] != PS_UNPAYED || $order['order_amount'] <= 0)
    {
        $err->add($_LANG['error_order_is_paid']);
        $err->show($_LANG['order_detail'], 'user.php?act=order_detail&order_id=' . $order_id);
    }

    /* 计算应付款金额（减去支付费用） */
    $order['order_amount'] -= $order['pay_fee'];

    /* 余额是否超过了应付款金额，改为应付款金额 */
    if ($surplus > $order['order_amount'])
    {
        $surplus = $order['order_amount'];
    }

    /* 取得用户信息 */
    $user = user_info($_SESSION['user_id']);

    /* 用户帐户余额是否足够 */
    if ($surplus > $user['user_money'] + $user['credit_line'])
    {
        $err->add($_LANG['error_surplus_not_enough']);
        $err->show($_LANG['order_detail'], 'user.php?act=order_detail&order_id=' . $order_id);
    }

    /* 修改订单，重新计算支付费用 */
    $order['surplus'] += $surplus;
    $order['order_amount'] -= $surplus;
    if ($order['order_amount'] > 0)
    {
        $cod_fee = 0;
        if ($order['shipping_id'] > 0)
        {
            $regions  = array($order['country'], $order['province'], $order['city'], $order['district']);
            $shipping = shipping_area_info($order['shipping_id'], $regions);
            if ($shipping['support_cod'] == '1')
            {
                $cod_fee = $shipping['pay_fee'];
            }
        }

        $pay_fee = 0;
        if ($order['pay_id'] > 0)
        {
            $pay_fee = pay_fee($order['pay_id'], $order['order_amount'], $cod_fee);
        }

        $order['pay_fee'] = $pay_fee;
        $order['order_amount'] += $pay_fee;
    }

    /* 如果全部支付，设为已确认、已付款 */
    if ($order['order_amount'] == 0)
    {
        if ($order['order_status'] == OS_UNCONFIRMED)
        {
            $order['order_status'] = OS_CONFIRMED;
            $order['confirm_time'] = gmtime();
        }
        $order['pay_status'] = PS_PAYED;
        $order['pay_time'] = gmtime();
    }
    $order = addslashes_deep($order);
    update_order($order_id, $order);

    /* 更新用户余额 */
    $change_desc = sprintf($_LANG['pay_order_by_surplus'], $order['order_sn']);
    log_account_change($user['user_id'], (-1) * $surplus, 0, 0, 0, $change_desc);

    /* 跳转 */
    ecs_header('Location: user.php?act=order_detail&order_id=' . $order_id . "\n");
    exit;
}

/* 编辑使用余额支付的处理 */
elseif ($action == 'act_edit_payment')
{
    /* 检查是否登录 */
    if ($_SESSION['user_id'] <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查支付方式 */
    $pay_id = intval($_POST['pay_id']);
    if ($pay_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    include_once(ROOT_PATH . 'includes/lib_order.php');
    $payment_info = payment_info($pay_id);
    if (empty($payment_info))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查订单号 */
    $order_id = intval($_POST['order_id']);
    if ($order_id <= 0)
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 取得订单 */
    $order = order_info($order_id);
    if (empty($order))
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查订单用户跟当前用户是否一致 */
    if ($_SESSION['user_id'] != $order['user_id'])
    {
        ecs_header("Location: ./\n");
        exit;
    }

    /* 检查订单是否未付款和未发货 以及订单金额是否为0 和支付id是否为改变*/
    if ($order['pay_status'] != PS_UNPAYED || $order['shipping_status'] != SS_UNSHIPPED || $order['goods_amount'] <= 0 || $order['pay_id'] == $pay_id)
    {
        ecs_header("Location: user.php?act=order_detail&order_id=$order_id\n");
        exit;
    }

    $order_amount = $order['order_amount'] - $order['pay_fee'];
    $pay_fee = pay_fee($pay_id, $order_amount);
    $order_amount += $pay_fee;

    $sql = "UPDATE " . $ecs->table('order_info') .
           " SET pay_id='$pay_id', pay_name='$payment_info[pay_name]', pay_fee='$pay_fee', order_amount='$order_amount'".
           " WHERE order_id = '$order_id'";
    $db->query($sql);

    /* 跳转 */
    ecs_header("Location: user.php?act=order_detail&order_id=$order_id\n");
    exit;
}

/* 保存订单详情收货地址 */
elseif ($action == 'save_order_address')
{
    include_once(ROOT_PATH .'includes/lib_transaction.php');
    
    $address = array(
        'consignee' => isset($_POST['consignee']) ? compile_str(trim($_POST['consignee']))  : '',
        'email'     => isset($_POST['email'])     ? compile_str(trim($_POST['email']))      : '',
        'address'   => isset($_POST['address'])   ? compile_str(trim($_POST['address']))    : '',
        'zipcode'   => isset($_POST['zipcode'])   ? compile_str(make_semiangle(trim($_POST['zipcode']))) : '',
        'tel'       => isset($_POST['tel'])       ? compile_str(trim($_POST['tel']))        : '',
        'mobile'    => isset($_POST['mobile'])    ? compile_str(trim($_POST['mobile']))     : '',
        'sign_building' => isset($_POST['sign_building']) ? compile_str(trim($_POST['sign_building'])) : '',
        'best_time' => isset($_POST['best_time']) ? compile_str(trim($_POST['best_time']))  : '',
        'order_id'  => isset($_POST['order_id'])  ? intval($_POST['order_id']) : 0
        );
    if (save_order_address($address, $user_id))
    {
        ecs_header('Location: user.php?act=order_detail&order_id=' .$address['order_id']. "\n");
        exit;
    }
    else
    {
        $err->show($_LANG['order_list_lnk'], 'user.php?act=order_list');
    }
}

/* 我的红包列表 */
elseif ($action == 'bonus')
{
    include_once(ROOT_PATH .'includes/lib_transaction.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    //$record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('user_bonus'). " WHERE user_id = '$user_id'");
    //$pager = get_pager('user.php', array('act' => $action), $record_count, $page);
    //$bonus = get_user_bouns_list($user_id, $pager['size'], $pager['start']);

	$arr_bonus = get_user_bouns_list($user_id);
	$record_count = count($arr_bonus);
	$pager = get_pager('user.php', array('act' => $action), $record_count, $page);
	$bonus = array_slice($arr_bonus, $pager['start'], $pager['size']);

    $smarty->assign('pager', $pager);
    $smarty->assign('bonus', $bonus);
    $smarty->display('user_transaction.dwt');
}

/* 我的团购列表 */
elseif ($action == 'group_buy')
{
    include_once(ROOT_PATH .'includes/lib_transaction.php');

    //待议
    $smarty->display('user_transaction.dwt');
}

/* 团购订单详情 */
elseif ($action == 'group_buy_detail')
{
    include_once(ROOT_PATH .'includes/lib_transaction.php');

    //待议
    $smarty->display('user_transaction.dwt');
}

// 用户推荐页面
elseif ($action == 'affiliate')
{
    $goodsid = intval(isset($_REQUEST['goodsid']) ? $_REQUEST['goodsid'] : 0);
    if(empty($goodsid))
    {
        //我的推荐页面

        $page       = !empty($_REQUEST['page'])  && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
        $size       = !empty($_CFG['page_size']) && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

        empty($affiliate) && $affiliate = array();

        if(empty($affiliate['config']['separate_by']))
        {
            //推荐注册分成
            $affdb = array();
            $num = count($affiliate['item']);
            $up_uid = "'$user_id'";
            $all_uid = "'$user_id'";
            for ($i = 1 ; $i <=$num ;$i++)
            {
                $count = 0;
                if ($up_uid)
                {
                    $sql = "SELECT user_id FROM " . $ecs->table('users') . " WHERE parent_id IN($up_uid)";
                    $query = $db->query($sql);
                    $up_uid = '';
                    while ($rt = $db->fetch_array($query))
                    {
                        $up_uid .= $up_uid ? ",'$rt[user_id]'" : "'$rt[user_id]'";
                        if($i < $num)
                        {
                            $all_uid .= ", '$rt[user_id]'";
                        }
                        $count++;
                    }
                }
                $affdb[$i]['num'] = $count;
                $affdb[$i]['point'] = $affiliate['item'][$i-1]['level_point'];
                $affdb[$i]['money'] = $affiliate['item'][$i-1]['level_money'];
            }
            $smarty->assign('affdb', $affdb);

            $sqlcount = "SELECT count(*) FROM " . $ecs->table('order_info') . " o".
        " LEFT JOIN".$ecs->table('users')." u ON o.user_id = u.user_id".
        " LEFT JOIN " . $ecs->table('affiliate_log') . " a ON o.order_id = a.order_id" .
        " WHERE o.user_id > 0 AND (u.parent_id IN ($all_uid) AND o.is_separate = 0 OR a.user_id = '$user_id' AND o.is_separate > 0)";

            $sql = "SELECT o.*, a.log_id, a.user_id as suid,  a.user_name as auser, a.money, a.point, a.separate_type FROM " . $ecs->table('order_info') . " o".
                    " LEFT JOIN".$ecs->table('users')." u ON o.user_id = u.user_id".
                    " LEFT JOIN " . $ecs->table('affiliate_log') . " a ON o.order_id = a.order_id" .
        " WHERE o.user_id > 0 AND (u.parent_id IN ($all_uid) AND o.is_separate = 0 OR a.user_id = '$user_id' AND o.is_separate > 0)".
                    " ORDER BY order_id DESC" ;

            /*
                SQL解释：

                订单、用户、分成记录关联
                一个订单可能有多个分成记录

                1、订单有效 o.user_id > 0
                2、满足以下之一：
                    a.直接下线的未分成订单 u.parent_id IN ($all_uid) AND o.is_separate = 0
                        其中$all_uid为该ID及其下线(不包含最后一层下线)
                    b.全部已分成订单 a.user_id = '$user_id' AND o.is_separate > 0

            */

            $affiliate_intro = nl2br(sprintf($_LANG['affiliate_intro'][$affiliate['config']['separate_by']], $affiliate['config']['expire'], $_LANG['expire_unit'][$affiliate['config']['expire_unit']], $affiliate['config']['level_register_all'], $affiliate['config']['level_register_up'], $affiliate['config']['level_money_all'], $affiliate['config']['level_point_all']));
        }
        else
        {
            //推荐订单分成
            $sqlcount = "SELECT count(*) FROM " . $ecs->table('order_info') . " o".
                    " LEFT JOIN".$ecs->table('users')." u ON o.user_id = u.user_id".
                    " LEFT JOIN " . $ecs->table('affiliate_log') . " a ON o.order_id = a.order_id" .
                    " WHERE o.user_id > 0 AND (o.parent_id = '$user_id' AND o.is_separate = 0 OR a.user_id = '$user_id' AND o.is_separate > 0)";


            $sql = "SELECT o.*, a.log_id,a.user_id as suid, a.user_name as auser, a.money, a.point, a.separate_type,u.parent_id as up FROM " . $ecs->table('order_info') . " o".
                    " LEFT JOIN".$ecs->table('users')." u ON o.user_id = u.user_id".
                    " LEFT JOIN " . $ecs->table('affiliate_log') . " a ON o.order_id = a.order_id" .
                    " WHERE o.user_id > 0 AND (o.parent_id = '$user_id' AND o.is_separate = 0 OR a.user_id = '$user_id' AND o.is_separate > 0)" .
                    " ORDER BY order_id DESC" ;

            /*
                SQL解释：

                订单、用户、分成记录关联
                一个订单可能有多个分成记录

                1、订单有效 o.user_id > 0
                2、满足以下之一：
                    a.订单下线的未分成订单 o.parent_id = '$user_id' AND o.is_separate = 0
                    b.全部已分成订单 a.user_id = '$user_id' AND o.is_separate > 0

            */

            $affiliate_intro = nl2br(sprintf($_LANG['affiliate_intro'][$affiliate['config']['separate_by']], $affiliate['config']['expire'], $_LANG['expire_unit'][$affiliate['config']['expire_unit']], $affiliate['config']['level_money_all'], $affiliate['config']['level_point_all']));

        }

        $count = $db->getOne($sqlcount);

        $max_page = ($count> 0) ? ceil($count / $size) : 1;
        if ($page > $max_page)
        {
            $page = $max_page;
        }

        $res = $db->SelectLimit($sql, $size, ($page - 1) * $size);
        $logdb = array();
        while ($rt = $GLOBALS['db']->fetchRow($res))
        {
            if(!empty($rt['suid']))
            {
                //在affiliate_log有记录
                if($rt['separate_type'] == -1 || $rt['separate_type'] == -2)
                {
                    //已被撤销
                    $rt['is_separate'] = 3;
                }
            }
            $rt['order_sn'] = substr($rt['order_sn'], 0, strlen($rt['order_sn']) - 5) . "***" . substr($rt['order_sn'], -2, 2);
            $logdb[] = $rt;
        }

        $url_format = "user.php?act=affiliate&page=";

        $pager = array(
                    'page'  => $page,
                    'size'  => $size,
                    'sort'  => '',
                    'order' => '',
                    'record_count' => $count,
                    'page_count'   => $max_page,
                    'page_first'   => $url_format. '1',
                    'page_prev'    => $page > 1 ? $url_format.($page - 1) : "javascript:;",
                    'page_next'    => $page < $max_page ? $url_format.($page + 1) : "javascript:;",
                    'page_last'    => $url_format. $max_page,
                    'array'        => array()
                );
        for ($i = 1; $i <= $max_page; $i++)
        {
            $pager['array'][$i] = $i;
        }

        $smarty->assign('url_format', $url_format);
        $smarty->assign('pager', $pager);


        $smarty->assign('affiliate_intro', $affiliate_intro);
        $smarty->assign('affiliate_type', $affiliate['config']['separate_by']);

        $smarty->assign('logdb', $logdb);
    }
    else
    {
        //单个商品推荐
        $smarty->assign('userid', $user_id);
        $smarty->assign('goodsid', $goodsid);

        $types = array(1,2,3,4,5);
        $smarty->assign('types', $types);

        $goods = get_goods_info($goodsid);
        $shopurl = $ecs->url();
        $goods['goods_img'] = (strpos($goods['goods_img'], 'http://') === false && strpos($goods['goods_img'], 'https://') === false) ? $shopurl . $goods['goods_img'] : $goods['goods_img'];
        $goods['goods_thumb'] = (strpos($goods['goods_thumb'], 'http://') === false && strpos($goods['goods_thumb'], 'https://') === false) ? $shopurl . $goods['goods_thumb'] : $goods['goods_thumb'];
        $goods['shop_price'] = price_format($goods['shop_price']);

        $smarty->assign('goods', $goods);
    }

    $smarty->assign('shopname', $_CFG['shop_name']);
    $smarty->assign('userid', $user_id);
    $smarty->assign('shopurl', $ecs->url());
    $smarty->assign('logosrc', 'themes/' . $_CFG['template'] . '/images/logo.gif');

    $smarty->display('user_clips.dwt');
}

//首页邮件订阅ajax操做和验证操作
elseif ($action =='email_list')
{
    $job = $_GET['job'];

    if($job == 'add' || $job == 'del')
    {
        if(isset($_SESSION['last_email_query']))
        {
            if(time() - $_SESSION['last_email_query'] <= 30)
            {
                die($_LANG['order_query_toofast']);
            }
        }
        $_SESSION['last_email_query'] = time();
    }

    $email = trim($_GET['email']);
    $email = htmlspecialchars($email);

    if (!is_email($email))
    {
        $info = sprintf($_LANG['email_invalid'], $email);
        die($info);
    }
    $ck = $db->getRow("SELECT * FROM " . $ecs->table('email_list') . " WHERE email = '$email'");
    if ($job == 'add')
    {
        if (empty($ck))
        {
            $hash = substr(md5(time()), 1, 10);
            $sql = "INSERT INTO " . $ecs->table('email_list') . " (email, stat, hash) VALUES ('$email', 0, '$hash')";
            $db->query($sql);
            $info = $_LANG['email_check'];
            $url = $ecs->url() . "user.php?act=email_list&job=add_check&hash=$hash&email=$email";
            send_mail('', $email, $_LANG['check_mail'], sprintf($_LANG['check_mail_content'], $email, $_CFG['shop_name'], $url, $url, $_CFG['shop_name'], local_date('Y-m-d')), 1);
        }
        elseif ($ck['stat'] == 1)
        {
            $info = sprintf($_LANG['email_alreadyin_list'], $email);
        }
        else
        {
            $hash = substr(md5(time()),1 , 10);
            $sql = "UPDATE " . $ecs->table('email_list') . "SET hash = '$hash' WHERE email = '$email'";
            $db->query($sql);
            $info = $_LANG['email_re_check'];
            $url = $ecs->url() . "user.php?act=email_list&job=add_check&hash=$hash&email=$email";
            send_mail('', $email, $_LANG['check_mail'], sprintf($_LANG['check_mail_content'], $email, $_CFG['shop_name'], $url, $url, $_CFG['shop_name'], local_date('Y-m-d')), 1);
        }
        die($info);
    }
    elseif ($job == 'del')
    {
        if (empty($ck))
        {
            $info = sprintf($_LANG['email_notin_list'], $email);
        }
        elseif ($ck['stat'] == 1)
        {
            $hash = substr(md5(time()),1,10);
            $sql = "UPDATE " . $ecs->table('email_list') . "SET hash = '$hash' WHERE email = '$email'";
            $db->query($sql);
            $info = $_LANG['email_check'];
            $url = $ecs->url() . "user.php?act=email_list&job=del_check&hash=$hash&email=$email";
            send_mail('', $email, $_LANG['check_mail'], sprintf($_LANG['check_mail_content'], $email, $_CFG['shop_name'], $url, $url, $_CFG['shop_name'], local_date('Y-m-d')), 1);
        }
        else
        {
            $info = $_LANG['email_not_alive'];
        }
        die($info);
    }
    elseif ($job == 'add_check')
    {
        if (empty($ck))
        {
            $info = sprintf($_LANG['email_notin_list'], $email);
        }
        elseif ($ck['stat'] == 1)
        {
            $info = $_LANG['email_checked'];
        }
        else
        {
            if ($_GET['hash'] == $ck['hash'])
            {
                $sql = "UPDATE " . $ecs->table('email_list') . "SET stat = 1 WHERE email = '$email'";
                $db->query($sql);
                $info = $_LANG['email_checked'];
            }
            else
            {
                $info = $_LANG['hash_wrong'];
            }
        }
        show_message($info, $_LANG['back_home_lnk'], 'index.php');
    }
    elseif ($job == 'del_check')
    {
        if (empty($ck))
        {
            $info = sprintf($_LANG['email_invalid'], $email);
        }
        elseif ($ck['stat'] == 1)
        {
            if ($_GET['hash'] == $ck['hash'])
            {
                $sql = "DELETE FROM " . $ecs->table('email_list') . "WHERE email = '$email'";
                $db->query($sql);
                $info = $_LANG['email_canceled'];
            }
            else
            {
                $info = $_LANG['hash_wrong'];
            }
        }
        else
        {
            $info = $_LANG['email_not_alive'];
        }
        show_message($info, $_LANG['back_home_lnk'], 'index.php');
    }
}

/* ajax 发送验证邮件 */
elseif ($action == 'send_hash_mail')
{
    include_once(ROOT_PATH .'includes/cls_json.php');
    include_once(ROOT_PATH .'includes/lib_passport.php');
    $json = new JSON();

    $result = array('error' => 0, 'message' => '', 'content' => '');

    if ($user_id == 0)
    {
        /* 用户没有登录 */
        $result['error']   = 1;
        $result['message'] = $_LANG['login_please'];
        die($json->encode($result));
    }

    if (send_regiter_hash($user_id))
    {
        $result['message'] = $_LANG['validate_mail_ok'];
        die($json->encode($result));
    }
    else
    {
        $result['error'] = 1;
        $result['message'] = $GLOBALS['err']->last_message();
    }

    die($json->encode($result));
}
else if ($action == 'track_packages')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH .'includes/lib_order.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $orders = array();

    $sql = "SELECT order_id,order_sn,invoice_no,shipping_id FROM " .$ecs->table('order_info').
            " WHERE user_id = '$user_id' AND shipping_status = '" . SS_SHIPPED . "'";
    $res = $db->query($sql);
    $record_count = 0;
    while ($item = $db->fetch_array($res))
    {
        $shipping   = get_shipping_object($item['shipping_id']);

        if (method_exists ($shipping, 'query'))
        {
            $query_link = $shipping->query($item['invoice_no']);
        }
        else
        {
            $query_link = $item['invoice_no'];
        }

        if ($query_link != $item['invoice_no'])
        {
            $item['query_link'] = $query_link;
            $orders[]  = $item;
            $record_count += 1;
        }
    }
    $pager  = get_pager('user.php', array('act' => $action), $record_count, $page);
    $smarty->assign('pager',  $pager);
    $smarty->assign('orders', $orders);
    $smarty->display('user_transaction.dwt');
}
else if ($action == 'order_query')
{
    $_GET['order_sn'] = trim(substr($_GET['order_sn'], 1));
    $order_sn = empty($_GET['order_sn']) ? '' : addslashes($_GET['order_sn']);
    include_once(ROOT_PATH .'includes/cls_json.php');
    $json = new JSON();

    $result = array('error'=>0, 'message'=>'', 'content'=>'');

    if(isset($_SESSION['last_order_query']))
    {
        if(time() - $_SESSION['last_order_query'] <= 10)
        {
            $result['error'] = 1;
            $result['message'] = $_LANG['order_query_toofast'];
            die($json->encode($result));
        }
    }
    $_SESSION['last_order_query'] = time();

    if (empty($order_sn))
    {
        $result['error'] = 1;
        $result['message'] = $_LANG['invalid_order_sn'];
        die($json->encode($result));
    }

    $sql = "SELECT order_id, order_status, shipping_status, pay_status, ".
           " shipping_time, shipping_id, invoice_no, user_id ".
           " FROM " . $ecs->table('order_info').
           " WHERE order_sn = '$order_sn' LIMIT 1";

    $row = $db->getRow($sql);
    if (empty($row))
    {
        $result['error'] = 1;
        $result['message'] = $_LANG['invalid_order_sn'];
        die($json->encode($result));
    }

    $order_query = array();
    $order_query['order_sn'] = $order_sn;
    $order_query['order_id'] = $row['order_id'];
    $order_query['order_status'] = $_LANG['os'][$row['order_status']] . ',' . $_LANG['ps'][$row['pay_status']] . ',' . $_LANG['ss'][$row['shipping_status']];

    if ($row['invoice_no'] && $row['shipping_id'] > 0)
    {
        $sql = "SELECT shipping_code FROM " . $ecs->table('shipping') . " WHERE shipping_id = '$row[shipping_id]'";
        $shipping_code = $db->getOne($sql);
        $plugin = ROOT_PATH . 'includes/modules/shipping/' . $shipping_code . '.php';
        if (file_exists($plugin))
        {
            include_once($plugin);
            $shipping = new $shipping_code;
            $order_query['invoice_no'] = $shipping->query((string)$row['invoice_no']);
        }
        else
        {
            $order_query['invoice_no'] = (string)$row['invoice_no'];
        }
    }

    $order_query['user_id'] = $row['user_id'];
    /* 如果是匿名用户显示发货时间 */
    if ($row['user_id'] == 0 && $row['shipping_time'] > 0)
    {
        $order_query['shipping_date'] = local_date($GLOBALS['_CFG']['date_format'], $row['shipping_time']);
    }
    $smarty->assign('order_query',    $order_query);
    $result['content'] = $smarty->fetch('library/order_query.lbi');
    die($json->encode($result));
}
elseif ($action == 'transform_points')
{
    $rule = array();
    if (!empty($_CFG['points_rule']))
    {
        $rule = unserialize($_CFG['points_rule']);
    }
    $cfg = array();
    if (!empty($_CFG['integrate_config']))
    {
        $cfg = unserialize($_CFG['integrate_config']);
        $_LANG['exchange_points'][0] = empty($cfg['uc_lang']['credits'][0][0])? $_LANG['exchange_points'][0] : $cfg['uc_lang']['credits'][0][0];
        $_LANG['exchange_points'][1] = empty($cfg['uc_lang']['credits'][1][0])? $_LANG['exchange_points'][1] : $cfg['uc_lang']['credits'][1][0];
    }
    $sql = "SELECT user_id, user_name, pay_points, rank_points FROM " . $ecs->table('users')  . " WHERE user_id='$user_id'";
    $row = $db->getRow($sql);
    if ($_CFG['integrate_code'] == 'ucenter')
    {
        $exchange_type = 'ucenter';
        $to_credits_options = array();
        $out_exchange_allow = array();
        foreach ($rule as $credit)
        {
            $out_exchange_allow[$credit['appiddesc'] . '|' . $credit['creditdesc'] . '|' . $credit['creditsrc']] = $credit['ratio'];
            if (!array_key_exists($credit['appiddesc']. '|' .$credit['creditdesc'], $to_credits_options))
            {
                $to_credits_options[$credit['appiddesc']. '|' .$credit['creditdesc']] = $credit['title'];
            }
        }
        $smarty->assign('selected_org', $rule[0]['creditsrc']);
        $smarty->assign('selected_dst', $rule[0]['appiddesc']. '|' .$rule[0]['creditdesc']);
        $smarty->assign('descreditunit', $rule[0]['unit']);
        $smarty->assign('orgcredittitle', $_LANG['exchange_points'][$rule[0]['creditsrc']]);
        $smarty->assign('descredittitle', $rule[0]['title']);
        $smarty->assign('descreditamount', round((1 / $rule[0]['ratio']), 2));
        $smarty->assign('to_credits_options', $to_credits_options);
        $smarty->assign('out_exchange_allow', $out_exchange_allow);
    }
    else
    {
        $exchange_type = 'other';

        $bbs_points_name = $user->get_points_name();
        $total_bbs_points = $user->get_points($row['user_name']);

        /* 论坛积分 */
        $bbs_points = array();
        foreach ($bbs_points_name as $key=>$val)
        {
            $bbs_points[$key] = array('title'=>$_LANG['bbs'] . $val['title'], 'value'=>$total_bbs_points[$key]);
        }

        /* 兑换规则 */
        $rule_list = array();
        foreach ($rule as $key=>$val)
        {
            $rule_key = substr($key, 0, 1);
            $bbs_key = substr($key, 1);
            $rule_list[$key]['rate'] = $val;
            switch ($rule_key)
            {
                case TO_P :
                    $rule_list[$key]['from'] = $_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
                    $rule_list[$key]['to'] = $_LANG['pay_points'];
                    break;
                case TO_R :
                    $rule_list[$key]['from'] = $_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
                    $rule_list[$key]['to'] = $_LANG['rank_points'];
                    break;
                case FROM_P :
                    $rule_list[$key]['from'] = $_LANG['pay_points'];$_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
                    $rule_list[$key]['to'] =$_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
                    break;
                case FROM_R :
                    $rule_list[$key]['from'] = $_LANG['rank_points'];
                    $rule_list[$key]['to'] = $_LANG['bbs'] . $bbs_points_name[$bbs_key]['title'];
                    break;
            }
        }
        $smarty->assign('bbs_points', $bbs_points);
        $smarty->assign('rule_list',  $rule_list);
    }
    $smarty->assign('shop_points', $row);
    $smarty->assign('exchange_type',     $exchange_type);
    $smarty->assign('action',     $action);
    $smarty->assign('lang',       $_LANG);
    $smarty->display('user_transaction.dwt');
}
elseif ($action == 'act_transform_points')
{
    $rule_index = empty($_POST['rule_index']) ? '' : trim($_POST['rule_index']);
    $num = empty($_POST['num']) ? 0 : intval($_POST['num']);


    if ($num <= 0 || $num != floor($num))
    {
        show_message($_LANG['invalid_points'], $_LANG['transform_points'], 'user.php?act=transform_points');
    }

    $num = floor($num); //格式化为整数

    $bbs_key = substr($rule_index, 1);
    $rule_key = substr($rule_index, 0, 1);

    $max_num = 0;

    /* 取出用户数据 */
    $sql = "SELECT user_name, user_id, pay_points, rank_points FROM " . $ecs->table('users') . " WHERE user_id='$user_id'";
    $row = $db->getRow($sql);
    $bbs_points = $user->get_points($row['user_name']);
    $points_name = $user->get_points_name();

    $rule = array();
    if ($_CFG['points_rule'])
    {
        $rule = unserialize($_CFG['points_rule']);
    }
    list($from, $to) = explode(':', $rule[$rule_index]);

    $max_points = 0;
    switch ($rule_key)
    {
        case TO_P :
            $max_points = $bbs_points[$bbs_key];
            break;
        case TO_R :
            $max_points = $bbs_points[$bbs_key];
            break;
        case FROM_P :
            $max_points = $row['pay_points'];
            break;
        case FROM_R :
            $max_points = $row['rank_points'];
    }

    /* 检查积分是否超过最大值 */
    if ($max_points <=0 || $num > $max_points)
    {
        show_message($_LANG['overflow_points'], $_LANG['transform_points'], 'user.php?act=transform_points' );
    }

    switch ($rule_key)
    {
        case TO_P :
            $result_points = floor($num * $to / $from);
            $user->set_points($row['user_name'], array($bbs_key=>0 - $num)); //调整论坛积分
            log_account_change($row['user_id'], 0, 0, 0, $result_points, $_LANG['transform_points'], ACT_OTHER);
            show_message(sprintf($_LANG['to_pay_points'],  $num, $points_name[$bbs_key]['title'], $result_points), $_LANG['transform_points'], 'user.php?act=transform_points');

        case TO_R :
            $result_points = floor($num * $to / $from);
            $user->set_points($row['user_name'], array($bbs_key=>0 - $num)); //调整论坛积分
            log_account_change($row['user_id'], 0, 0, $result_points, 0, $_LANG['transform_points'], ACT_OTHER);
            show_message(sprintf($_LANG['to_rank_points'], $num, $points_name[$bbs_key]['title'], $result_points), $_LANG['transform_points'], 'user.php?act=transform_points');

        case FROM_P :
            $result_points = floor($num * $to / $from);
            log_account_change($row['user_id'], 0, 0, 0, 0-$num, $_LANG['transform_points'], ACT_OTHER); //调整商城积分
            $user->set_points($row['user_name'], array($bbs_key=>$result_points)); //调整论坛积分
            show_message(sprintf($_LANG['from_pay_points'], $num, $result_points,  $points_name[$bbs_key]['title']), $_LANG['transform_points'], 'user.php?act=transform_points');

        case FROM_R :
            $result_points = floor($num * $to / $from);
            log_account_change($row['user_id'], 0, 0, 0-$num, 0, $_LANG['transform_points'], ACT_OTHER); //调整商城积分
            $user->set_points($row['user_name'], array($bbs_key=>$result_points)); //调整论坛积分
            show_message(sprintf($_LANG['from_rank_points'], $num, $result_points, $points_name[$bbs_key]['title']), $_LANG['transform_points'], 'user.php?act=transform_points');
    }
}
elseif ($action == 'act_transform_ucenter_points')
{
    $rule = array();
    if ($_CFG['points_rule'])
    {
        $rule = unserialize($_CFG['points_rule']);
    }
    $shop_points = array(0 => 'rank_points', 1 => 'pay_points');
    $sql = "SELECT user_id, user_name, pay_points, rank_points FROM " . $ecs->table('users')  . " WHERE user_id='$user_id'";
    $row = $db->getRow($sql);
    $exchange_amount = intval($_POST['amount']);
    $fromcredits = intval($_POST['fromcredits']);
    $tocredits = trim($_POST['tocredits']);
    $cfg = unserialize($_CFG['integrate_config']);
    if (!empty($cfg))
    {
        $_LANG['exchange_points'][0] = empty($cfg['uc_lang']['credits'][0][0])? $_LANG['exchange_points'][0] : $cfg['uc_lang']['credits'][0][0];
        $_LANG['exchange_points'][1] = empty($cfg['uc_lang']['credits'][1][0])? $_LANG['exchange_points'][1] : $cfg['uc_lang']['credits'][1][0];
    }
    list($appiddesc, $creditdesc) = explode('|', $tocredits);
    $ratio = 0;

    if ($exchange_amount <= 0)
    {
        show_message($_LANG['invalid_points'], $_LANG['transform_points'], 'user.php?act=transform_points');
    }
    if ($exchange_amount > $row[$shop_points[$fromcredits]])
    {
        show_message($_LANG['overflow_points'], $_LANG['transform_points'], 'user.php?act=transform_points');
    }
    foreach ($rule as $credit)
    {
        if ($credit['appiddesc'] == $appiddesc && $credit['creditdesc'] == $creditdesc && $credit['creditsrc'] == $fromcredits)
        {
            $ratio = $credit['ratio'];
            break;
        }
    }
    if ($ratio == 0)
    {
        show_message($_LANG['exchange_deny'], $_LANG['transform_points'], 'user.php?act=transform_points');
    }
    $netamount = floor($exchange_amount / $ratio);
    include_once(ROOT_PATH . './includes/lib_uc.php');
    $result = exchange_points($row['user_id'], $fromcredits, $creditdesc, $appiddesc, $netamount);
    if ($result === true)
    {
        $sql = "UPDATE " . $ecs->table('users') . " SET {$shop_points[$fromcredits]}={$shop_points[$fromcredits]}-'$exchange_amount' WHERE user_id='{$row['user_id']}'";
        $db->query($sql);
        $sql = "INSERT INTO " . $ecs->table('account_log') . "(user_id, {$shop_points[$fromcredits]}, change_time, change_desc, change_type)" . " VALUES ('{$row['user_id']}', '-$exchange_amount', '". gmtime() ."', '" . $cfg['uc_lang']['exchange'] . "', '98')";
        $db->query($sql);
        show_message(sprintf($_LANG['exchange_success'], $exchange_amount, $_LANG['exchange_points'][$fromcredits], $netamount, $credit['title']), $_LANG['transform_points'], 'user.php?act=transform_points');
    }
    else
    {
        show_message($_LANG['exchange_error_1'], $_LANG['transform_points'], 'user.php?act=transform_points');
    }
}
/* 清除商品浏览历史 */
elseif ($action == 'clear_history')
{
    setcookie('ECS[history]',   '', 1);
}


//会员卡合并
else if ($action == 'card_merge'){
	$smarty->display('user_transaction.dwt');
}

else if ($action == 'act_card_merge'){
	include_once(ROOT_PATH . './includes/lib_order.php');
	$str_fromCard    = trim($_POST['fromcard']);
	$str_fromCardPwd = trim($_POST['fromcardpwd']);
	
	$str_toCard    = trim($_POST['tocard']);
	$str_toCardPwd = trim($_POST['tocardpwd']);

	if (empty($str_fromCard) || empty($str_fromCardPwd)){
//		echo '要合并的卡号或密码不能为空！';
//		exit;
        $redata['msg']='要合并的卡号或密码不能为空！';
        echo json_encode($redata);
        exit;
	}
	if (empty($str_toCard) || empty($_POST['tocardpwd'])){
//		echo '合并到的卡号或密码不能为空！';
//		exit;
        $redata['msg']='合并到的卡号或密码不能为空！';
        echo json_encode($redata);
        exit;
    }
	$a=substr($str_fromCard,0,6);
	$b=substr($str_toCard,0,6);
	
	// 不是同一个卡系统的卡，不能合并 TODO guoyunepng
	//$cardno = array('999011', '999013');
	/* if ( (in_array($a, $cardno) && !in_array($b, $cardno)) || (!in_array($a, $cardno) && in_array($b, $cardno)))
	{
		echo '两张卡类型不符合，不能合并，有问题请拨打客服电话：400-010-0689';
		exit;
	} */

	if ($str_fromCard == $str_toCard){
//		echo '丫的，你玩呢！';
//		exit;
        $redata['msg']='两张卡号不能相同！';
        echo json_encode($redata);
        exit;
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
	    $redata['msg']='两张卡类型不统一，不支持合并！';
	    exit(json_encode($redata));
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
	
	    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('card_log') .
	    " WHERE card_from = '$str_fromCard' or card_to = '$str_fromCard' ORDER BY  log_id DESC";
	    $res = $GLOBALS['db']->query($sql);
	     
	    while ($row = $GLOBALS['db']->fetchRow($res))
	    {
	        $pay_time = $row['pay_time'];
	        $times  = gmtime() - $pay_time;
	        if ($times < 90*24*3600){
	            $redata['msg']='您的这卡在短期内已经转移过！';
	    	    exit(json_encode($redata));
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
	            $redata['msg']='您的这卡在短期内已经转移过！';
	    	    exit(json_encode($redata));
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
//			exit('转出的卡已过有效期，合并失败');
            $redata['msg']='转出的卡已过有效期，合并失败';
            echo json_encode($redata);
            exit;
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
				$cardOutTime = strtotime($cardResult['ExpDate']." +1 day");
			}

			// 有效期验证
			if (gmtime() > $cardOutTime)
			{
//				exit('转入的卡已过有效期，合并失败');
                $redata['msg']='转出的卡已过有效期，合并失败';
                echo json_encode($redata);
                exit;
            }
		}
		else
		{
//			exit('转入的卡密码不正确！');
            $redata['msg']='转入的卡密码不正确！';
            echo json_encode($redata);
            exit;

        }
	}
	else
	{
//		exit('转出的卡秘密不正确！');
        $redata['msg']='转出的卡秘密不正确！';
        echo json_encode($redata);
        exit;

    }
	
	// 转出的卡余额是0，终止执行
	if (floatval($cardValueMoney) <=0)
	{
//		exit('转出的卡点数为 0 ，请换张有点数的卡！');
        $redata['msg']='转出的卡点数为 0 ，请换张有点数的卡！';
        echo json_encode($redata);
        exit;
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
	
	if ( $state == 0)
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
			$sql = "INSERT INTO " . $GLOBALS['ecs']->table('card_log') . " (card_from, card_to, card_money, pay_time) " .
					"VALUES('$str_fromCard', '$str_toCard', '$cardValueMoney', '".gmtime()."')";
		
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
//			echo 'success'.$flo_money;
//			exit;
            $msg = 'success'.$flo_money;
            $redata['msg']=$msg;
            echo json_encode($redata);
            exit;

        }
		else
		{
//			exit($cardPay->getMessage());
            $msg = $cardPay->getMessage();
            $redata['msg']=$msg;
            echo json_encode($redata);
            exit;
        }
	}
	// 卡合并失败后的操作，次状态只有中影卡合并时候才有，意思是，来源卡扣款了，充值失败了。
	elseif($state == 2){
		//插入日志表
		$sql = "INSERT INTO " . $GLOBALS['ecs']->table('card_log') . " (card_from, card_to, card_money, pay_time, message) " .
				"VALUES('$str_fromCard', '$str_toCard', '$cardValueMoney', '".gmtime()."', '卡合并失败，来源卡点数已扣')";
		
//		exit($cardPay->getMessage());
        $msg = $cardPay->getMessage();
        $redata['msg']=$msg;
        echo json_encode($redata);
        exit;
    }
	else
	{
//		exit($cardPay->getMessage());
        $msg = $cardPay->getMessage();
        $redata['msg']=$msg;
        echo json_encode($redata);
        exit;
    }
	
}

// 动网门票订单 
else if ($action == 'dongpiao_order')
{
	include_once(ROOT_PATH . 'includes/lib_transaction.php');
	include_once(ROOT_PATH . 'includes/lib_order.php');
	
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
	$record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('piao_order'). " WHERE user_id = '$user_id'");
	$pager  = get_pager('user.php', array('act' => $action), $record_count, $page);
	// 更新门票订单状态
	update_piao_order_status($_SESSION['user_id']);
	
	$orders = array();
	
	$sql = "SELECT * ".
			" FROM " .$GLOBALS['ecs']->table('piao_order') .
			" WHERE user_id = '$user_id' ORDER BY add_time DESC";
	$res = $GLOBALS['db']->SelectLimit($sql, $pager['size'], $pager['start']);
	while ($row = $GLOBALS['db']->fetchRow($res))
	{
		// 未确认、未付款       	order_state = 1  AND card_state = 1
		if ($row['order_state'] == 0 && $row['card_state'] == 0)
		{
			$row['order_state_sn'] = '<font>未确认<br>未付款</font>';
		}
		// 已确认、未付款      	order_state = 1 AND card_state == 0
		if ($row['order_state'] == 1 && $row['card_state'] == 0)
		{
			$row['order_state_sn'] = '<font>已确认<br>未付款<br><a href="dongpiao_order.php?step=upay&order_id='.$row['order_id'].'">去付款</a></font>';
		}
		// 已确认、已付款 、订单失败      	order_state = 1  AND card_state = 1
		if ($row['order_state'] == 1 && $row['card_state'] == 1)
		{
			$row['order_state_sn'] = '<font>已确认<br>已付款<br><font color=red>订单失败</font></font>';
		}
		// 已付款、待出票       	order_state = 2  AND card_state = 1
		if ($row['order_state'] == 2 && $row['card_state'] == 1)
		{
			$row['order_state_sn'] = '<font>已付款<br>待出票<br></font>';
		}
		// 未付款、已取消       	order_state = 3  AND card_state = 0
		if ($row['order_state'] == 3 && $row['card_state'] == 0)
		{
			$row['order_state_sn'] = '<font>未付款<br>已取消</font>';
		}
		
		// 已付款、已完成	order_state = 4  AND card_state = 1
		if ($row['order_state'] == 4 && $row['card_state'] == 1)
		{
			$row['order_state_sn'] = '<font>已付款<br>已完成</font>';
		}
		
		// 已付款、已取消	order_state = 3  AND card_state = 1
		if ($row['order_state'] == 3 && $row['card_state'] == 1)
		{
			$row['order_state_sn'] = '<font>已付款<br>已取消</font>';
		}
		
		// 已付款、已退款	order_state = 2  AND card_state = 2
		if ($row['order_state'] == 2 && $row['card_state'] == 2)
		{
			$row['order_state_sn'] = '<font>已付款<br>已退款</font>';
		}
		
		// 已取消、已退款	order_state = 3  AND card_state = 2
		if ($row['order_state'] == 3 && $row['card_state'] == 2)
		{
			$row['order_state_sn'] = '<font>已取消<br>已退款</font>';
		}
		
		// 已完成、已退款	order_state = 4  AND card_state = 2
		if ($row['order_state'] == 4 && $row['card_state'] == 2)
		{
			$row['order_state_sn'] = '<font>已完成<br>已退款</font>';
		}
		
		$row['add_time'] = date('Y-m-d',$row['add_time']);
		
		$orders[] = $row;
	}
	
	/*
	 *  已确认、未付款      	order_state = 1
	 *  未付款、已取消       	order_state = 3  AND card_state = 0
	 *  已付款、已完成	order_state = 4  AND card_state = 1
	 *  已付款、已取消	order_state = 3  AND card_state = 1
	 *  已付款、已退款	order_state = 2  AND card_state = 2
	 *  已取消、已退款	order_state = 3  AND card_state = 2
	 **/
	//var_dump($orders);
	$smarty->assign('pager',  $pager);
	$smarty->assign('orders', $orders);
	$smarty->display('user_transaction.dwt');
}

//电影订单
else if ($action == 'film_order'){
	include_once(ROOT_PATH . 'includes/lib_transaction.php');
	include_once(ROOT_PATH . 'includes/lib_order.php');
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('seats_order'). " WHERE user_id = '$user_id'");

    $pager  = get_pager('user.php', array('act' => $action), $record_count, $page);

    $orders = get_user_film_orders_cdy($user_id, $pager['size'], $pager['start']);
	
    /* $orderQuery = getCDYapi(array('action'=>'order_Query', 'order_id'=>'a1431051658109363413'));
    echo $orderQuery['orders'][0]['orderStatus'];
    echo '<pre>';
    print_r($orderQuery);
    echo '</pre>';  exit;   */
    movie_orders_status('',$user_id);
    $smarty->assign('pager',  $pager);
    $smarty->assign('orders', $orders);
    $smarty->display('order/filmOrderList.dwt');
}

else if ($action == 'reyanzheng'){
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	$int_orderId = intval($_GET['orderid']);	
	 $sql = "SELECT order_sn, mobile, sjprice, number, user_name " .
           " FROM " .$GLOBALS['ecs']->table('seat_order') .
           " WHERE order_id = '$int_orderId' ";
	$order_list = $GLOBALS['db']->getRow($sql);
	
	$str_mobile = $order_list['mobile'];
	$str_orderSn = $order_list['order_sn'];
	$float_price = $order_list['sjprice']*$order_list['number'];
	$float_price1 = number_format(floatval($float_price), 2, '.', '');


			$arr_param = array(
				'Mobile'     => $str_mobile,
				'OrderNo'    => $str_orderSn,
				'orderPrice' => $float_price1,
				'payment'    => $float_price1,
				'DistributorUrl' => '',
				'sign'     => md5($str_mobile.$str_orderSn.$float_price1.$arr_param['DistributorUrl'].$GLOBALS['_CFG']['yyappKey']),
				'Remark'   => '',
				'SendType' => 3
			);
			$arr_result = getYYApi($arr_param, 'confirmOrder');//确认支付订单
			show_message('如果没有收到短信，请立即联系华影客服！');
			ecs_header('location:user.php?act=film_order');
}

//电子券订单
else if ($action == 'dzq_order'){
	include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('dzq_order'). " WHERE user_id = '$user_id'");

    $pager  = get_pager('user.php', array('act' => $action), $record_count, $page);

    $orders = get_user_dzq_orders($user_id, $pager['size'], $pager['start']);
    $smarty->assign('pager',  $pager);
    $smarty->assign('orders', $orders);
    $smarty->display('order/dzqOrder.dwt');
}
//商品码订单
else if ($action == 'code_order'){
	include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('code_order'). " WHERE user_id = '$user_id'");

    $pager  = get_pager('user.php', array('act' => $action), $record_count, $page);

    $orders = get_user_code_orders($user_id, $pager['size'], $pager['start']);
    $smarty->assign('pager',  $pager);
    $smarty->assign('orders', $orders);
    $smarty->display('order/codeOrder.dwt');
}


//提货券订单列表
else if ($action == 'coupons_order'){
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'includes/lib_coupons.php');
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $user_name=$_SESSION['user_name'];
    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('coupons_order'). " WHERE user_name = '$user_name'");
    $pager  = get_pager('user.php', array('act' => $action), $record_count, $page);
    $sql_sel_coupons_order="select * from ".$GLOBALS ['ecs']->table ('coupons_order')." where user_name='".$user_name."'"." order by add_time desc";
    $orders=$GLOBALS ['db']->getAll($sql_sel_coupons_order);
    $result=array();
    foreach ($orders as $key => $value) {
        $orders[$key]['add_time']=local_date("Y-m-d H:i:s",$value['add_time']);
        $result=guigeSms($value['orderid']);
        if($value['order_state']==0){
            $orders[$key]['order_state']="未付款";
            $orders[$key]['coupons_id']="";
        }elseif($value['order_state']==1){
            $orders[$key]['order_state']="已付款";
            $orders[$key]['coupons_id']='';
            $orders[$key]['coupons_id']=$result['tihuo'].' 有效期:'.$result['end_time'];
        }elseif($value['order_state']==2){
            $orders[$key]['order_state']="无效";
            $orders[$key]['coupons_id']="";
        }else{
            $orders[$key]['order_state']="未下单";
            $orders[$key]['coupons_id']="";
        }
    }
    echo '<pre>';
    print_r($orders);
    echo '</pre>';
    exit;

    $smarty->assign('pager',  $pager);
    $smarty->assign('orders', $orders);
    $smarty->display('user_transaction.dwt');
}


//票工厂订单列表
else if ($action == 'piaoduoduo_order'){
	include_once(ROOT_PATH . 'includes/lib_transaction.php');
	$smarty->assign('page_title',       '景点门票订单列表');    // 页面标题
	$page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

	$record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('piaoduoduo_order'). " WHERE user_id = '$user_id'");

	$pager  = get_pager('user.php', array('act' => $action), $record_count, $page,5);
	$orders = get_user_piaoduoduo_orders($user_id, $pager['size'], $pager['start']);
	
	$smarty->assign('pager',  $pager);
	$smarty->assign('orders', $orders);
	$smarty->display('user_transaction.dwt');
}
// 场馆订单
else if ($action == 'venues_order')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');
    
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('venues_order'). " WHERE user_id = '$user_id'");
    $pager  = get_pager('user.php', array('act' => $action), $record_count, $page);
    // 更新门票订单状态
    //update_piao_order_status($_SESSION['user_id']);
    update_venues_state($_SESSION['user_id']);
    $orders = array();

    $sql = "SELECT * ".
        " FROM " .$GLOBALS['ecs']->table('venues_order') .
        " WHERE user_id = '$user_id' ORDER BY add_time DESC";
    $res = $GLOBALS['db']->SelectLimit($sql, $pager['size'], $pager['start']);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $orders[$row['id']] = $row;
        $orders[$row['id']]['order_state_sn'] = order_start_sn($row);
        $orders[$row['id']]['add_time'] = date('Y-m-d H:i:s',$row['add_time']);
        foreach (json_decode($row['times']) as $time)
        {
            $orders[$row['id']]['times_mt'][] =urldecode($time);
        }
    }
    $smarty->assign('pager',  $pager);
    $smarty->assign('order_list', $orders);
    $smarty->display('order/venuesOrderList.dwt');
    
}
//演出订单
else if ($action == 'yanchu_order'){
	include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('yanchu_order'). " WHERE user_id = '$user_id'");

    $pager  = get_pager('user.php', array('act' => $action), $record_count, $page);

    $orders = get_user_yanchu_orders($user_id, $pager['size'], $pager['start']);

    $smarty->assign('pager',  $pager);
    $smarty->assign('orders', $orders);
    $smarty->display('order/yanchuOrder.dwt');
}
elseif ($action == 'dzq_order_del')
{
    include_once(ROOT_PATH . 'includes/lib_clips.php');

    $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($order_id > 0)
    {
        $db->query('DELETE FROM ' .$ecs->table('dzq_order'). " WHERE order_id='$order_id' AND user_id ='$user_id'" );
    }

    ecs_header("Location: user.php?act=dzq_order\n");
    exit;
}


function order_start_sn($row){

    $order_state_sn = '';

    //未付款
    if ($row['state'] == 0 )
    {
        $order_state_sn = '<font color=red>未付款</font>';
    }    
    // 已付款
    if ($row['state'] == 1 )
    {
        $order_state_sn = '<font color=red>已付款</font>';
    }
    // 已完成
    if ($row['state'] == 3 )
    {
        $order_state_sn = '<font color=green>已完成</font>';
    }
    // 已退票
    if ($row['state'] == 2 )
    {
        $order_state_sn = '<font color=red>已退票</font>';
    }
    if ($row['state'] == 4)
    {
        $order_state_sn = '<font color=red>退票中</font>';
    }
    return $order_state_sn;
}

?>