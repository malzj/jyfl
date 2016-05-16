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

/* 载入语言文件 */
require_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/user.php');

$user_id = $_SESSION['user_id'];
$action  = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : 'default';

$affiliate = unserialize($GLOBALS['_CFG']['affiliate']);
$smarty->assign('affiliate', $affiliate);
$back_act='';


// 不需要登录的操作或自己验证是否登录（如ajax处理）的act
$not_login_arr =
array('login','act_login','logout','account_deposit','act_account','card_merge','act_card_merge','profile','act_edit_password','cardyue','docardyue','checkcardno','pays');

/* 显示页面的action列表 */
$ui_arr = array('register', 'profile','reyanzheng', 'order_list', 'order_detail', 'address_list', 'collection_list',
'message_list', 'tag_list', 'get_password', 'reset_password', 'booking_list', 'add_booking', 'account_raply',
'account_deposit', 'account_log', 'account_detail', 'act_account', 'pay', 'default', 'bonus', 'group_buy', 'group_buy_detail', 'affiliate', 'comment_list','validate_email','track_packages', 'transform_points','qpassword_name', 'get_passwd_question', 'check_answer', 'card_merge', 'act_card_merge', 'film_order', 'dzq_order', 'dongpiao_order', 'yanchu_order','bangding_tel','coupons_order','piaoduoduo_order','huanlegu_order');

/* 未登录处理 */
if (empty($_SESSION['user_id']))
{
    if (!in_array($action, $not_login_arr))
    {
        if (in_array($action, $ui_arr))
        {          
            if (!empty($_SERVER['QUERY_STRING']))
            {
                $back_act = 'user.php?' . strip_tags($_SERVER['QUERY_STRING']);
            }
            $action = 'login';
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
    if ($rank = get_rank_info())
    {
        $smarty->assign('rank_name', sprintf($_LANG['your_level'], $rank['rank_name']));
        if (!empty($rank['next_rank_name']))
        {
            $smarty->assign('next_rank_name', sprintf($_LANG['next_level'], $rank['next_rank'] ,$rank['next_rank_name']));
        }
    }
    $smarty->assign('get_header', get_header('个人中心',true,true));
    $smarty->assign('get_fixed', get_fixed(4));
    $smarty->assign('info',        get_user_default($user_id));
    $smarty->display('user.html');
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

    $smarty->assign('back_act', $back_act);
    $smarty->display('login.html');   
}

/* 处理会员的登录 */
elseif ($action == 'act_login')
{	
	$username = isset($_POST['username']) ? trim($_POST['username']) : '';
	$password = isset($_POST['password']) ? trim($_POST['password']) : '';
		
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
			$GLOBALS['db']->query('INSERT INTO ' . $GLOBALS['ecs']->table("users") . "(`user_name`, `password`, `card_money`, `reg_time`, `last_login`, `last_ip`, `youxiao_time`) VALUES ('$username', '".md5($password)."', '$cardMoney', '$reg_date', '$reg_date', '$last_ip', '".$cardOutTime."')");
		}else{//更新用户信息
			$GLOBALS['db']->query('UPDATE ' . $GLOBALS['ecs']->table("users") . " SET password='".md5($password)."', card_money = '$cardMoney', youxiao_time = '".$cardOutTime."' WHERE user_id = '$int_uid'");
		}
	
		//设置本站登录成功
		$GLOBALS['user']->set_session($username);
		$GLOBALS['user']->set_cookie($username);
		$_SESSION['BalanceCash'] = $cardMoney;
	
		update_user_info();
		recalculate_price();
		exit('success');
	}
		
	exit( $cardPay->getMessage() );
	
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
    
	//show_message($_LANG['logout'] . $ucdata, array($_LANG['back_up_page'], $_LANG['back_home_lnk']), array($back_act, 'index.php'), 'info');
}

/* 个人资料页面 */
elseif ($action == 'profile')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $user_info = get_profile($user_id);

    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 ORDER BY dis_order, id';
    $extend_info_list = $db->getAll($sql);

    $sql = 'SELECT reg_field_id, content ' .
           'FROM ' . $ecs->table('reg_extend_info') .
           " WHERE user_id = $user_id";
    $extend_info_arr = $db->getAll($sql);

    $temp_arr = array();
    foreach ($extend_info_arr AS $val)
    {
        $temp_arr[$val['reg_field_id']] = $val['content'];
    }

    foreach ($extend_info_list AS $key => $val)
    {
        switch ($val['id'])
        {
            case 1:     $extend_info_list[$key]['content'] = $user_info['msn']; break;
            case 2:     $extend_info_list[$key]['content'] = $user_info['qq']; break;
            case 3:     $extend_info_list[$key]['content'] = $user_info['office_phone']; break;
            case 4:     $extend_info_list[$key]['content'] = $user_info['home_phone']; break;
            case 5:     $extend_info_list[$key]['content'] = $user_info['mobile_phone']; break;
            default:    $extend_info_list[$key]['content'] = empty($temp_arr[$val['id']]) ? '' : $temp_arr[$val['id']] ;
        }
    }

    $smarty->assign('extend_info_list', $extend_info_list);
	$is_login = 1;
    if(!empty($_SESSION['user_id']))
    {    	
    	$is_login = 2;	
    }
  
    /* 密码提示问题 */
    $smarty->assign('passwd_questions', $_LANG['passwd_questions']);
	$smarty->assign('is_login',$is_login);
    $smarty->assign('header', get_header('修改卡密码',true,true));
    $smarty->assign('profile', $user_info);
    $smarty->display('password.html');
}

/* 查询余额 */
elseif ($action == 'cardyue')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    $user_info = get_profile($user_id);

    /* 取出注册扩展字段 */
    $sql = 'SELECT * FROM ' . $ecs->table('reg_fields') . ' WHERE type < 2 AND display = 1 ORDER BY dis_order, id';
    $extend_info_list = $db->getAll($sql);

    $sql = 'SELECT reg_field_id, content ' .
           'FROM ' . $ecs->table('reg_extend_info') .
           " WHERE user_id = $user_id";
    $extend_info_arr = $db->getAll($sql);

    $temp_arr = array();
    foreach ($extend_info_arr AS $val)
    {
        $temp_arr[$val['reg_field_id']] = $val['content'];
    }

    foreach ($extend_info_list AS $key => $val)
    {
        switch ($val['id'])
        {
            case 1:     $extend_info_list[$key]['content'] = $user_info['msn']; break;
            case 2:     $extend_info_list[$key]['content'] = $user_info['qq']; break;
            case 3:     $extend_info_list[$key]['content'] = $user_info['office_phone']; break;
            case 4:     $extend_info_list[$key]['content'] = $user_info['home_phone']; break;
            case 5:     $extend_info_list[$key]['content'] = $user_info['mobile_phone']; break;
            default:    $extend_info_list[$key]['content'] = empty($temp_arr[$val['id']]) ? '' : $temp_arr[$val['id']] ;
        }
    }

    $smarty->assign('extend_info_list', $extend_info_list);
    $is_login = 1;
    if(!empty($_SESSION['user_id']))
    {       
        $is_login = 2;  
    }
    /* 密码提示问题 */
    $smarty->assign('passwd_questions', $_LANG['passwd_questions']);
    $smarty->assign('is_login',$is_login);
    $smarty->assign('header', get_header('卡余额',true,true));
    $smarty->assign('info', $user_info);
    $smarty->display('cardyue.html');
}

elseif ($action == 'docardyue')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    $password      = isset($_REQUEST['password'])  ? trim($_REQUEST['password']) : '';
    $user_name    = isset($_REQUEST['user_name']) ? trim($_REQUEST['user_name']) : '';

    if (strlen($password) < 6)
    {
        // exit('密码长度不能少于6位');
        show_wap_message('密码长度不能少于6位');
    }
    //查询卡余额
    $arr_param = array( 'CardInfo'=>array('CardNo'  => $user_name, 'CardPwd' => $password ));
    $state = $cardPay->action($arr_param,8);
    if($state==0){
        $sql = 'SELECT * FROM ' . $ecs->table('users') . " WHERE user_name='$user_name'";
        $user_id = $db->getOne($sql);
        $user_info = get_profile($user_id);

    }else{
        show_wap_message( $cardPay->getMessage() );
    }
    /* 密码提示问题 */
    $smarty->assign('passwd_questions', $_LANG['passwd_questions']);
    $smarty->assign('is_login',2);
    $smarty->assign('header', get_header('卡余额',true,true));
    $smarty->assign('info', $user_info);
    $smarty->display('cardyue.html');
}
/* 修改个人资料的处理 */
elseif ($action == 'act_edit_profile')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $birthday = trim($_POST['birthdayYear']) .'-'. trim($_POST['birthdayMonth']) .'-'.
    trim($_POST['birthdayDay']);
    $email = trim($_POST['email']);
    $other['msn'] = $msn = isset($_POST['extend_field1']) ? trim($_POST['extend_field1']) : '';
    $other['qq'] = $qq = isset($_POST['extend_field2']) ? trim($_POST['extend_field2']) : '';
    $other['office_phone'] = $office_phone = isset($_POST['extend_field3']) ? trim($_POST['extend_field3']) : '';
    $other['home_phone'] = $home_phone = isset($_POST['extend_field4']) ? trim($_POST['extend_field4']) : '';
    $other['mobile_phone'] = $mobile_phone = isset($_POST['extend_field5']) ? trim($_POST['extend_field5']) : '';
    $sel_question = empty($_POST['sel_question']) ? '' : compile_str($_POST['sel_question']);
    $passwd_answer = isset($_POST['passwd_answer']) ? compile_str(trim($_POST['passwd_answer'])) : '';

    /* 更新用户扩展字段的数据 */
    $sql = 'SELECT id FROM ' . $ecs->table('reg_fields') . ' WHERE type = 0 AND display = 1 ORDER BY dis_order, id';   //读出所有扩展字段的id
    $fields_arr = $db->getAll($sql);

    foreach ($fields_arr AS $val)       //循环更新扩展用户信息
    {
        $extend_field_index = 'extend_field' . $val['id'];
        if(isset($_POST[$extend_field_index]))
        {
            $temp_field_content = strlen($_POST[$extend_field_index]) > 100 ? mb_substr(htmlspecialchars($_POST[$extend_field_index]), 0, 99) : htmlspecialchars($_POST[$extend_field_index]);
            $sql = 'SELECT * FROM ' . $ecs->table('reg_extend_info') . "  WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";
            if ($db->getOne($sql))      //如果之前没有记录，则插入
            {
                $sql = 'UPDATE ' . $ecs->table('reg_extend_info') . " SET content = '$temp_field_content' WHERE reg_field_id = '$val[id]' AND user_id = '$user_id'";
            }
            else
            {
                $sql = 'INSERT INTO '. $ecs->table('reg_extend_info') . " (`user_id`, `reg_field_id`, `content`) VALUES ('$user_id', '$val[id]', '$temp_field_content')";
            }
            $db->query($sql);
        }
    }

    /* 写入密码提示问题和答案 */
    if (!empty($passwd_answer) && !empty($sel_question))
    {
        $sql = 'UPDATE ' . $ecs->table('users') . " SET `passwd_question`='$sel_question', `passwd_answer`='$passwd_answer'  WHERE `user_id`='" . $_SESSION['user_id'] . "'";
        $db->query($sql);
    }

    if (!empty($office_phone) && !preg_match( '/^[\d|\_|\-|\s]+$/', $office_phone ) )
    {
        show_message($_LANG['passport_js']['office_phone_invalid']);
    }
    if (!empty($home_phone) && !preg_match( '/^[\d|\_|\-|\s]+$/', $home_phone) )
    {
         show_message($_LANG['passport_js']['home_phone_invalid']);
    }
    if (!is_email($email))
    {
		echo $_LANG['msg_email_format'];
		exit;
        //show_message($_LANG['msg_email_format']);
    }
    if (!empty($msn) && !is_email($msn))
    {
         show_message($_LANG['passport_js']['msn_invalid']);
    }
    if (!empty($qq) && !preg_match('/^\d+$/', $qq))
    {
         show_message($_LANG['passport_js']['qq_invalid']);
    }
    if (!empty($mobile_phone) && !preg_match('/^[\d-\s]+$/', $mobile_phone))
    {
        show_message($_LANG['passport_js']['mobile_phone_invalid']);
    }


    $profile  = array(
        'user_id'  => $user_id,
        'email'    => isset($_POST['email']) ? trim($_POST['email']) : '',
        'sex'      => isset($_POST['sex'])   ? intval($_POST['sex']) : 0,
        'birthday' => $birthday,
        'other'    => isset($other) ? $other : array()
        );

    if (edit_profile($profile))
    {
		echo 'success';
		exit;
        show_message($_LANG['edit_profile_success'], $_LANG['profile_lnk'], 'user.php?act=profile', 'info');
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
		echo $msg;
		exit;
        show_message($msg, '', '', 'info');
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
    include_once(ROOT_PATH . 'includes/lib_passport.php');

    $old_password = isset($_POST['old_password']) ? trim($_POST['old_password']) : null;
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $user_id      = isset($_POST['uid'])  ? intval($_POST['uid']) : $user_id;
    $code         = isset($_POST['code']) ? trim($_POST['code'])  : '';
    $post_user_name    = isset($_POST['user_name']) ? trim($_POST['user_name']) : '';

    if (strlen($new_password) < 6)
    {
		echo '密码长度不能少于6位';
		exit;
        show_message($_LANG['passport_js']['password_shorter']);
    }

    $user_name = $_SESSION['user_name'];
    if (empty($_SESSION['user_id']))
    {
    	if (empty($post_user_name))
    	{
    		echo '卡号是不能为空';
    		exit;
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
			exit('修改成功');
		}else{
			$user->logout();
			exit('success');
		}
	}
	else
	{
		exit($cardPay->getMessage());
	}
}

/* 查看订单列表 */
elseif ($action == 'order_list')
{
	
    include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('order_info'). " WHERE user_id = '$user_id'");

    $pagebar = get_wap_pager($record_count, 10, $page, 'user.php?act=order_list');
    $orders = get_user_orders($user_id, 10, (($page-1)*10));

   /*  echo '<pre>';
    print_r($orders);
    echo '</pre>'; */
	$smarty->assign('header', get_header('我的订单',true,true));
    $smarty->assign('pagebar',  $pagebar);
    $smarty->assign('orders', $orders);
    $smarty->display('order_list.html');
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
    	$goods_list[$key]['goods_thumb']  = $GLOBALS['db']->getOne(" SELECT goods_thumb FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id = ".$value['goods_id']);
        $goods_list[$key]['market_price'] = price_format($value['market_price'], false);
        $goods_list[$key]['goods_price']  = price_format($value['goods_price'], false);
        $goods_list[$key]['subtotal']     = price_format($value['subtotal'], false);
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
    $order['order_status'] = $_LANG['os'][$order['order_status']];
	$order['pay_statuses'] = $order['pay_status'];
    $order['pay_status'] = $_LANG['ps'][$order['pay_status']];
    $order['shipping_status'] = $_LANG['ss'][$order['shipping_status']];
    $order['country_cn'] = get_add_cn($order['country']);
    $order['province_cn'] = get_add_cn($order['province']);
    $order['best_time'] = trim($order['best_time']);
    $order['is_best_time'] = empty($order['best_time']) ? 0 : 1;
    $smarty->assign('header', get_header('订单详情',true,true));
    $smarty->assign('order',      $order);
    $smarty->assign('goods_list', $goods_list);
    $smarty->display('order_detail.html');
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

    /* 取得国家列表、商店所在国家、商店所在国家的省列表 */
    //$smarty->assign('country_list',       get_regions());
    //$smarty->assign('shop_province_list', get_regions(1, $_CFG['shop_country']));

    /* 获得用户所有的收货人信息 */
    $consignee_list = get_consignee_list($_SESSION['user_id']);
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
    $address_id  = $db->getOne("SELECT address_id FROM " .$ecs->table('users'). " WHERE user_id='$user_id'");

    //赋值于模板
    $smarty->assign('real_goods_count', 1);
    //$smarty->assign('shop_province',    get_regions(1, $_CFG['shop_country']));
    $smarty->assign('province_list',    get_regions(1, $int_cityId));
    $smarty->assign('address',          $address_id);
    //$smarty->assign('city_list',        $city_list);
    //$smarty->assign('district_list',    $district_list);
    $smarty->assign('currency_format',  $_CFG['currency_format']);
    $smarty->assign('integral_scale',   $_CFG['integral_scale']);
    $smarty->assign('name_of_region',   array($_CFG['name_of_region_1'], $_CFG['name_of_region_2'], $_CFG['name_of_region_3'], $_CFG['name_of_region_4']));
	$smarty->assign('header', get_header('管理收货地址',true, array(array('title'=>'添加', 'link'=>'user.php?act=edit_address')) ) );
    $smarty->display('address_list.html');
    
}

/* 添加  / 修改收货人信息 */
elseif( $action == 'edit_address'){
	$address_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : '' ;
	$from = isset($_REQUEST['from']) ? $_REQUEST['from'] : 'user' ;
	
	include_once(ROOT_PATH . 'includes/lib_transaction.php');
	include_once(ROOT_PATH . 'languages/' .$_CFG['lang']. '/shopping_flow.php');
	$smarty->assign('lang',  $_LANG);
	$smarty->assign('from',  $from);
	
	$smarty->assign('province_list',    get_regions(1, $int_cityId));
	$smarty->assign('address_id', $address_id);
	$smarty->display('address_info.html');
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
elseif ($action == 'checkcardno')
{
	$smarty->assign('header',get_header('卡充值','user.php',true));
	$smarty->display('checkcardno.html');
} 
/* 会员预付款界面 */
elseif ($action == 'account_deposit')
{
	// 如果没有登陆，就去验证卡号密码后，在充值
	if (empty($user_id))
	{
		ecs_header('Location:user.php?act=checkcardno');
	}
	include_once(ROOT_PATH . 'includes/lib_clips.php');
	$surplus_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	$account    = get_surplus_info($surplus_id);
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

	//获取余额记录
	$account_log = get_account_log($user_id, $pager['size'], $pager['start']);

	//模板赋值
	$smarty->assign('priceList', $priceList);
	$smarty->assign('surplus_amount', price_format($surplus_amount, false));
	$smarty->assign('account_log',    $account_log);
	$smarty->assign('pager',          $pager);

	$smarty->assign('username', $_SESSION['user_name']);
	$smarty->assign('payment', get_online_payment_list(false));
	$smarty->assign('order',   $account);
	$smarty->assign('header',get_header('卡充值','user.php',true));
	$smarty->display('account_deposit10.html');
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
    $account_log = get_account_log($user_id, $pager['size'], $pager['start']);

    //模板赋值
    $smarty->assign('surplus_amount', price_format($surplus_amount, false));
    $smarty->assign('account_log',    $account_log);
    $smarty->assign('pager',          $pager);
    $smarty->display('user_transaction.dwt');
}

/* 对会员余额申请的处理 */
elseif ($action == 'act_account')
{
	$ajaxMange = array('error'=>0, 'message'=>'', 'href'=>'');	

 	include_once(ROOT_PATH . 'includes/lib_clips.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    
    // 充值金额为0的话，定义为非法操作
    if ($amount <= 0){
    	$ajaxMange['error']   = 1;
    	$ajaxMange['message'] = '非法操作！';  
        exit(json_encode($ajaxMange));
    }
    
    // 如果没有登陆不能充值
    if(empty($user_id))
    { 
    	$ajaxMange['error']   = 1;
    	$ajaxMange['message'] = '请先验证卡号!';
    	exit(json_encode($ajaxMange));
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
     
    }
    /* 如果是会员预付款，跳转到下一步，进行线上支付的操作 */
    else
    {
    	// 如果没有选择支付方式，输出错误消息。
        if ($surplus['payment_id'] < 3)
        {
            $ajaxMange['error']   = 1;
	    	$ajaxMange['message'] = '选择支付方式！';  
	        exit(json_encode($ajaxMange));
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
					$ajaxMange['error']   = 1;
					$ajaxMange['message'] = $arr_cardInfo['ReturnMessage'];
					exit(json_encode($ajaxMange));
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
        $ajaxMange['error']   = 0;
        if ($payment_info['pay_code'] == 'alipay')
        {
        	$ajaxMange['href'] = 'user.php?act=pays&code='.base64_encode($linkpay);
        }
        else if ( $payment_info['pay_code'] == 'weixin')
        {
        	$ajaxMange['href'] = $linkpay;
        }
        
        exit(json_encode($ajaxMange));
    }
}

elseif ($action == 'pays')
{
	$code = base64_decode($_GET['code']);
	$smarty->assign('url', $code);
	$smarty->display('alipayPay.html');
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
	$smarty->assign('header', get_header('余额转移','user.php',true));
	$smarty->display('card_merge.html');
}

/* TODO 可合并，暂时没做 */
else if ($action == 'act_card_merge'){
include_once(ROOT_PATH . './includes/lib_order.php');
	$str_fromCard    = trim($_POST['fromcard']);
	$str_fromCardPwd = trim($_POST['fromcardpwd']);
	
	$str_toCard    = trim($_POST['tocard']);
	$str_toCardPwd = trim($_POST['tocardpwd']);

	if (empty($str_fromCard) || empty($str_fromCardPwd)){
		echo '要合并的卡号或密码不能为空！';
		exit;
	}
	if (empty($str_toCard) || empty($_POST['tocardpwd'])){
		echo '合并到的卡号或密码不能为空！';
		exit;
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
		echo '丫的，你玩呢！';
		exit;
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
		if ($times < 90*24*3600){
			echo '您的这卡在短期内已经转移过！';
			exit;
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
			echo '您的这卡在短期内已经转移过！';
			exit;
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
			exit('转出的卡已过有效期，合并失败');
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
				exit('转入的卡已过有效期，合并失败');
			}
		}
		else
		{
			exit('转入的卡密码不正确！');
		}
	}
	else
	{
		exit('转出的卡秘密不正确！');
	} 
	
	// 转出的卡余额是0，终止执行
	if (floatval($cardValueMoney) <=0)
	{
		exit('转出的卡点数为 0 ，请换张有点数的卡！');
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
			echo 'success'.$flo_money;
			exit;
		}
		else
		{
			exit($cardPay->getMessage());
		}
	}
	// 卡合并失败后的操作，次状态只有中影卡合并时候才有，意思是，来源卡扣款了，充值失败了。
	elseif($state == 2){
		//插入日志表
		$sql = "INSERT INTO " . $GLOBALS['ecs']->table('card_log') . " (card_from, card_to, card_money, pay_time, message) " .
				"VALUES('$str_fromCard', '$str_toCard', '$cardValueMoney', '".gmtime()."', '卡合并失败，来源卡点数已扣')";
	
		exit($cardPay->getMessage());
	}
	else
	{
		exit($cardPay->getMessage());
	}	
}


//电影订单
else if ($action == 'film_order'){
	include_once(ROOT_PATH . 'includes/lib_transaction.php');
	include_once(ROOT_PATH . 'includes/lib_order.php');
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	
    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('seats_order'). " WHERE user_id = '$user_id'");
    $pagebar = get_wap_pager($record_count, 10, $page, 'user.php?act=film_order');
    $orders = get_user_film_orders_cdy($user_id, 10, ($page-1)*10);	
   
    movie_orders_status('',$user_id);
    $smarty->assign('pagebar',  $pagebar);
    $smarty->assign('orders', $orders);
    $smarty->assign('header',get_header('我的订单',true,true));
    $smarty->display('order_list.html');
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
    
    $pagebar = get_wap_pager($record_count, 10, $page, 'user.php?act=dzq_order');
    $orders = get_user_dzq_orders($user_id, 10, ($page-1)*10);
    

    $smarty->assign('header',get_header('我的订单',true,true));
    $smarty->assign('pagebar',  $pagebar);
    $smarty->assign('orders', $orders);
    $smarty->display('order_list.html');
}
else if ($action == 'huanlegu_order')
{
    include_once(ROOT_PATH . 'includes/lib_transaction.php');
    include_once(ROOT_PATH . 'includes/lib_order.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('huanlegu_order'). " WHERE user_id = '$user_id'");
    $pager  = get_pager('user.php', array('act' => $action), $record_count, $page);
    $orders = array();
    $sql = "SELECT * ".
        " FROM " .$GLOBALS['ecs']->table('huanlegu_order') .
        " WHERE user_id = '$user_id' ORDER BY add_time DESC";
    $res = $GLOBALS['db']->SelectLimit($sql, $pager['size'], $pager['start']);
    while ($row = $GLOBALS['db']->fetchRow($res))
    {
        $orders[$row['id']] = $row;
        if ($row['state'] == 0)
            $order_state_sn = '未付款';
        elseif ($row['state'] == 1)
        $order_state_sn = '已付款';
        elseif ($row['state'] == 2)
        $order_state_sn = '部分退款';
        else
            $order_state_sn = '全部退款';

        $orders[$row['id']]['order_state_sn'] = $order_state_sn;
        $orders[$row['id']]['add_time'] = date('Y-m-d H:i:s',$row['add_time']);
    }

    //var_dump($orders); exit;
    $smarty->assign('pager',  $pager);
    $smarty->assign('order_list', $orders);
    $smarty->display('order_list.html');

}
//演出订单
else if ($action == 'yanchu_order'){
	include_once(ROOT_PATH . 'includes/lib_transaction.php');

    $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;

    $record_count = $db->getOne("SELECT COUNT(*) FROM " .$ecs->table('yanchu_order'). " WHERE user_id = '$user_id'");

    $pagebar = get_wap_pager($record_count, 10, $page, 'user.php?act=yanchu_order');
    $orders = get_user_yanchu_orders($user_id, 10, ($page-1)*10);

    $smarty->assign('header',get_header('我的订单',true,true));
    $smarty->assign('pagebar',  $pagebar);
    $smarty->assign('orders', $orders);
    $smarty->display('order_list.html');
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

?>