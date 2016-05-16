<?php

/**
 * ECSHOP 会员帐目管理(包括预付款，余额)
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: user_account.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 人工扣费列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 权限判断 */
    admin_priv('surplus_manage');
    /* 指定会员的ID为查询条件 */
    // $user_id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $smarty->assign('ur_here',       $_LANG['11_user_artificial']);
    // $smarty->assign('id',            $user_id);
    $smarty->assign('action',           $_REQUEST['act']);
    $list = artificial_list();
    $smarty->assign('list',         $list['list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('full_page',    1);
    assign_query_info();
    $smarty->display('user_artificial.htm');
}
/*------------------------------------------------------ */
//-- ajax帐户信息列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $smarty->assign('action',           'list');
    $list = artificial_list();
    $smarty->assign('ur_here',      $_LANG['11_user_artificial']);
    $smarty->assign('list',         $list['list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    // $sort_flag  = sort_flag($list['filter']);
    // $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    //http://www.huaying.ccc/admin/user_artificial.php?act=query&sort_by=Id&sort_order=DESC&orderid=&card=9990111942832469&operation=&price=&operation_reason=&add_time=1442901319&record_count=8&page_size=15&page=1&page_count=1&start=0
// echo "<pre>";
// print_r($list);
// echo "</pre>";
// die;
    // make_json_result($smarty->fetch('user_artificial.htm'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
    make_json_result ( $smarty->fetch ( 'user_artificial.htm' ), '', array (
            'filter' => $list ['filter'],
            'page_count' => $list ['page_count'] 
    ) );

}
/*------------------------------------------------------ */
//--添加人工扣费
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'add')
{
    // include_once(ROOT_PATH . 'includes/fckeditor/fckeditor.php'); // 包含 html editor 类文件
    admin_priv('surplus_manage'); //权限判断
    /* 模板赋值 */
    $smarty->assign('ur_here',          '添加人工扣费');
    $smarty->assign('action',           $_REQUEST['act']);
    $smarty->assign('full_page',    1);
    assign_query_info();
    $smarty->display('user_artificial.htm');
}

/*------------------------------------------------------ */
//--添加人工扣费数据处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'doadd')
{
    admin_priv('surplus_manage'); //权限判断
    $card = !empty($_REQUEST['card']) ? trim($_REQUEST['card']) : '';
    $password = !empty($_REQUEST['password']) ? trim($_REQUEST['password']) : '';
    $price = !empty($_REQUEST['price']) ? trim($_REQUEST['price']) : '';
    $operation = $_SESSION['admin_name'];
    $operation_reason = !empty($_REQUEST['operation_reason']) ? trim($_REQUEST['operation_reason']) : '';
    $add_time=gmtime();
    //检测卡号是否存在，余额是否足够，密码是否正确
     $user_res = $db->getRow("SELECT user_name,password,card_money FROM " .$ecs->table('users'). " WHERE user_name = '$card'");

    /* 此会员是否存在 */
    if (empty($user_res['user_name']))
    {
        $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
        sys_msg('卡号不正确或者卡号不能为空', 0, $link);
    }
    
    // 如果不是 999011 或 999013 开头的卡就不能使用人工扣费
    if ( !in_array(substr($card,0,6), array('999013','999011') ))
    {
    	$link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
    	sys_msg('只有中影卡才可以用人工扣费，華影卡不支持', 0, $link);
    }
        
    $orderid=get_order_sn();//生成的订单号
    /** TODO 支付 （双卡版） */
    $arr_param = array(
    		'CardInfo' => array( 'CardNo'=> $card, 'CardPwd' => $password),
    		'TransationInfo' => array( 'TransRequestPoints'=>$price)
    );
    $state = $cardPay->action($arr_param, 1, $orderid);
    
    //$arr_cardInfo['ReturnCode'] = '0';
    if ($state == 0) {
            $_SESSION['BalanceCash'] -= $price; //重新计算用户卡余额
            //更新卡金额
            $res_users=$db->query('UPDATE '.$ecs->table('users')." SET card_money = card_money - ('$price') WHERE user_name = '".$card."'");
            $res_users=1;
            if($res_users){
                $sql_artificial = "INSERT INTO " .$GLOBALS['ecs']->table('artificial').
           " VALUES ('','$orderid','$card', '$price', '$operation','$operation_reason','$add_time')";
                $res_artificial = $GLOBALS['db']->query($sql_artificial);
                $link[] = array('text' => '返回列表','href'=>'/admin/user_artificial.php?act=list');
                sys_msg('扣费成功！',0,$link);
            }

    }else{
        sys_msg($cardPay->getMessage());
    }        
        // $smarty->assign('ur_here', '人工扣费');
        // $smarty->assign('action','list');
        // $smarty->assign('full_page',1);
        // $smarty->display('user_artificial.htm');
     
}

/*------------------------------------------------------ */
//--人工扣费导出
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'excel_out')
{
    // include_once(ROOT_PATH . 'includes/fckeditor/fckeditor.php'); // 包含 html editor 类文件
    admin_priv('surplus_manage'); //权限判断
    /* 模板赋值 */
    $smarty->assign('ur_here',          '人工扣费导出');
    $smarty->assign('action',           $_REQUEST['act']);
    $smarty->assign('full_page',    1);
    assign_query_info();
    $smarty->display('user_artificial.htm');
}
/*------------------------------------------------------ */
//--人工扣费导出处理
/*------------------------------------------------------ */
elseif($_REQUEST ['act'] == 'do_excel_out'){
    // 选择的时间
    $start_time = empty ( $_REQUEST ['start_time'] ) ? '' : (strpos ( $_REQUEST ['start_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['start_time'] ) : $_REQUEST ['start_time']);
    $end_time = empty ( $_REQUEST ['end_time'] ) ? '' : (strpos ( $_REQUEST ['end_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['end_time'] ) : $_REQUEST ['end_time']);
    $filter ["start_time"] = $start_time;
    $filter ["end_time"] = $end_time;
    $data = artificial_list( $filter , true);
    $row = 2;
    foreach ( $data['list'] as $key => $val ) {
        
        if (! isset ( $key ) && empty ( $key )) {
            continue;
        }
        $exportInfo [] = array (
                'A' . $row => ' ' . $val ['Id'],
                'B' . $row => ' ' . $val ['orderid'],
                'C' . $row => ' ' . $val ['card'],
                'D' . $row => ' ' . $val ['price'],
                'E' . $row => ' ' . $val ['operation'],
                'F' . $row => ' ' . $val ['operation_reason'],
                'G' . $row => ' ' . $val ['add_time']
         
        );
        
        $row ++;
    }

    // 导入excel类
    require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes\lib_autoExcels.php');
    require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes\Classes\PHPExcel.php');
    //创建对象
    $excel = new PHPExcel();
    //Excel表格式
    $letter = array('A','B','C','D','E','F','G');
    //表头数组
    $tableheader = array('序号','订单号','卡号','金额','操作人','操作原因','时间');
    //填充表格信息
    for ($i = 2;$i <= count($exportInfo) + 1;$i++) {
        $j = 0;
        foreach ($exportInfo[$i - 2] as $key=>$value) {
            $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
            $j++;
        }
    }
    //填充表头信息
    for($i = 0;$i < count($tableheader);$i++) {
        $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
    }

    //自动换行
    $excel->getActiveSheet()->getStyle('F2:F10000')->getAlignment()->setWrapText(true);
    $excel->getActiveSheet()->getColumnDimension("A")->setWidth('20');
    $excel->getActiveSheet()->getColumnDimension("B")->setWidth('30');
    $excel->getActiveSheet()->getColumnDimension("C")->setWidth('20');
    $excel->getActiveSheet()->getColumnDimension("D")->setWidth('20');
    $excel->getActiveSheet()->getColumnDimension("E")->setWidth('10');
    $excel->getActiveSheet()->getColumnDimension("F")->setWidth('70');
    $excel->getActiveSheet()->getColumnDimension("G")->setWidth('20');


    //创建Excel输入对象
    $fileNmae = 'artificial-' . local_date ( 'mdHis', time () ) . '.xlsx';
    $write = new PHPExcel_Writer_Excel5($excel);
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
    header("Content-Type:application/force-download");
    header("Content-Type:application/vnd.ms-execl");
    header("Content-Type:application/octet-stream");
    header("Content-Type:application/download");;
    header('Content-Disposition:attachment;filename='.$fileNmae);
    header("Content-Transfer-Encoding:binary");
    $write->save('php://output');

}
/*------------------------------------------------------ */
//-- 添加/编辑会员余额的处理部分
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update')
{
    /* 权限判断 */
    admin_priv('surplus_manage');

    /* 初始化变量 */
    $id           = isset($_POST['id'])            ? intval($_POST['id'])             : 0;
    $is_paid      = !empty($_POST['is_paid'])      ? intval($_POST['is_paid'])        : 0;
    $amount       = !empty($_POST['amount'])       ? floatval($_POST['amount'])       : 0;
    $process_type = !empty($_POST['process_type']) ? intval($_POST['process_type'])   : 0;
    $user_name    = !empty($_POST['user_id'])      ? trim($_POST['user_id'])          : '';
    $admin_note   = !empty($_POST['admin_note'])   ? trim($_POST['admin_note'])       : '';
    $user_note    = !empty($_POST['user_note'])    ? trim($_POST['user_note'])        : '';
    $payment      = !empty($_POST['payment'])      ? trim($_POST['payment'])          : '';

    $user_id = $db->getOne("SELECT user_id FROM " .$ecs->table('users'). " WHERE user_name = '$user_name'");

    /* 此会员是否存在 */
    if ($user_id == 0)
    {
        $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
        sys_msg($_LANG['username_not_exist'], 0, $link);
    }

    /* 退款，检查余额是否足够 */
    if ($process_type == 1)
    {
        $user_account = get_user_surplus($user_id);

        /* 如果扣除的余额多于此会员拥有的余额，提示 */
        if ($amount > $user_account)
        {
            $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
            sys_msg($_LANG['surplus_amount_error'], 0, $link);
        }
    }

    if ($_REQUEST['act'] == 'insert')
    {
        /* 入库的操作 */
        if ($process_type == 1)
        {
            $amount = (-1) * $amount;
        }
        $sql = "INSERT INTO " .$ecs->table('user_account').
               " VALUES ('', '$user_id', '$_SESSION[admin_name]', '$amount', '".gmtime()."', '".gmtime()."', '$admin_note', '$user_note', '$process_type', '$payment', '$is_paid')";
        $db->query($sql);
        $id = $db->insert_id();
    }
    else
    {
        /* 更新数据表 */
        $sql = "UPDATE " .$ecs->table('user_account'). " SET ".
               "admin_note   = '$admin_note', ".
               "user_note    = '$user_note', ".
               "payment      = '$payment' ".
              "WHERE id      = '$id'";
        $db->query($sql);
    }

    // 更新会员余额数量
    if ($is_paid == 1)
    {
        $change_desc = $amount > 0 ? $_LANG['surplus_type_0'] : $_LANG['surplus_type_1'];
        $change_type = $amount > 0 ? ACT_SAVING : ACT_DRAWING;
        log_account_change($user_id, $amount, 0, 0, 0, $change_desc, $change_type);
    }

    //如果是预付款并且未确认，向pay_log插入一条记录
    if ($process_type == 0 && $is_paid == 0)
    {
        include_once(ROOT_PATH . 'includes/lib_order.php');

        /* 取支付方式信息 */
        $payment_info = array();
        $payment_info = $db->getRow('SELECT * FROM ' . $ecs->table('payment').
                                    " WHERE pay_name = '$payment' AND enabled = '1'");
        //计算支付手续费用
        $pay_fee   = pay_fee($payment_info['pay_id'], $amount, 0);
        $total_fee = $pay_fee + $amount;

        /* 插入 pay_log */
        $sql = 'INSERT INTO ' . $ecs->table('pay_log') . " (order_id, order_amount, order_type, is_paid)" .
                " VALUES ('$id', '$total_fee', '" .PAY_SURPLUS. "', 0)";
        $db->query($sql);
    }

    /* 记录管理员操作 */
    if ($_REQUEST['act'] == 'update')
    {
        admin_log($user_name, 'edit', 'user_surplus');
    }
    else
    {
        admin_log($user_name, 'add', 'user_surplus');
    }

    /* 提示信息 */
    if ($_REQUEST['act'] == 'insert')
    {
        $href = 'user_account.php?act=list';
    }
    else
    {
        $href = 'user_account.php?act=list&' . list_link_postfix();
    }
    $link[0]['text'] = $_LANG['back_list'];
    $link[0]['href'] = $href;

    $link[1]['text'] = $_LANG['continue_add'];
    $link[1]['href'] = 'user_account.php?act=add';

    sys_msg($_LANG['attradd_succed'], 0, $link);
}

/*------------------------------------------------------ */
//-- 审核会员余额页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'check')
{
    /* 检查权限 */
    admin_priv('surplus_manage');

    /* 初始化 */
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* 如果参数不合法，返回 */
    if ($id == 0)
    {
        ecs_header("Location: user_account.php?act=list\n");
        exit;
    }

    /* 查询当前的预付款信息 */
    $account = array();
    $account = $db->getRow("SELECT * FROM " .$ecs->table('user_account'). " WHERE id = '$id'");
    $account['add_time'] = local_date($_CFG['time_format'], $account['add_time']);

    //余额类型:预付款，退款申请，购买商品，取消订单
    if ($account['process_type'] == 0)
    {
        $process_type = $_LANG['surplus_type_0'];
    }
    elseif ($account['process_type'] == 1)
    {
        $process_type = $_LANG['surplus_type_1'];
    }
    elseif ($account['process_type'] == 2)
    {
        $process_type = $_LANG['surplus_type_2'];
    }
    else
    {
        $process_type = $_LANG['surplus_type_3'];
    }

    $sql = "SELECT user_name FROM " .$ecs->table('users'). " WHERE user_id = '$account[user_id]'";
    $user_name = $db->getOne($sql);

    /* 模板赋值 */
    $smarty->assign('ur_here',      $_LANG['check']);
    $account['user_note'] = htmlspecialchars($account['user_note']);
    $smarty->assign('surplus',      $account);
    $smarty->assign('process_type', $process_type);
    $smarty->assign('user_name',    $user_name);
    $smarty->assign('id',           $id);
    $smarty->assign('action_link',  array('text' => $_LANG['09_user_account'],
    'href'=>'user_account.php?act=list&' . list_link_postfix()));

    /* 页面显示 */
    assign_query_info();
    $smarty->display('user_artificial.htm');
}

/*------------------------------------------------------ */
//-- 更新会员余额的状态
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'action')
{
    /* 检查权限 */
    admin_priv('surplus_manage');

    /* 初始化 */
    $id         = isset($_POST['id'])         ? intval($_POST['id'])             : 0;
    $is_paid    = isset($_POST['is_paid'])    ? intval($_POST['is_paid'])        : 0;
    $admin_note = isset($_POST['admin_note']) ? trim($_POST['admin_note'])       : '';

    /* 如果参数不合法，返回 */
    if ($id == 0 || empty($admin_note))
    {
        ecs_header("Location: user_account.php?act=list\n");
        exit;
    }

    /* 查询当前的预付款信息 */
    $account = array();
    $account = $db->getRow("SELECT * FROM " .$ecs->table('user_account'). " WHERE id = '$id'");
    $amount  = $account['amount'];

    //如果状态为未确认
    if ($account['is_paid'] == 0)
    {
        //如果是退款申请, 并且已完成,更新此条记录,扣除相应的余额
        if ($is_paid == '1' && $account['process_type'] == '1')
        {
            $user_account = get_user_surplus($account['user_id']);
            $fmt_amount   = str_replace('-', '', $amount);

            //如果扣除的余额多于此会员拥有的余额，提示
            if ($fmt_amount > $user_account)
            {
                $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
                sys_msg($_LANG['surplus_amount_error'], 0, $link);
            }

            update_user_account($id, $amount, $admin_note, $is_paid);

            //更新会员余额数量
            log_account_change($account['user_id'], $amount, 0, 0, 0, $_LANG['surplus_type_1'], ACT_DRAWING);
        }
        elseif ($is_paid == '1' && $account['process_type'] == '0')
        {
			//更新会员余额数量
            $res = log_account_change($account['user_id'], $amount, 0, 0, 0, $_LANG['surplus_type_0'], ACT_SAVING, 1, $account);
			if ($res === true){
				update_user_account($id, $amount, $admin_note, $is_paid);
			}else{
				sys_msg($res, 0, $link);
			}
        }
        elseif ($is_paid == '0')
        {
            /* 否则更新信息 */
            $sql = "UPDATE " .$ecs->table('user_account'). " SET ".
                   "admin_user    = '$_SESSION[admin_name]', ".
                   "admin_note    = '$admin_note', ".
                   "is_paid       = 0 WHERE id = '$id'";
            $db->query($sql);
        }

        /* 记录管理员日志 */
        admin_log('(' . addslashes($_LANG['check']) . ')' . $admin_note, 'edit', 'user_surplus');

        /* 提示信息 */
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'user_account.php?act=list&' . list_link_postfix();

        sys_msg($_LANG['attradd_succed'], 0, $link);
    }
}


/*------------------------------------------------------ */
//-- ajax删除一条信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    /* 检查权限 */
    check_authz_json('surplus_manage');
    $id = @intval($_REQUEST['id']);
    $sql = "SELECT u.user_name FROM " . $ecs->table('users') . " AS u, " .
           $ecs->table('user_account') . " AS ua " .
           " WHERE u.user_id = ua.user_id AND ua.id = '$id' ";
    $user_name = $db->getOne($sql);
    $sql = "DELETE FROM " . $ecs->table('user_account') . " WHERE id = '$id'";
    if ($db->query($sql, 'SILENT'))
    {
       admin_log(addslashes($user_name), 'remove', 'user_surplus');
       $url = 'user_account.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
       ecs_header("Location: $url\n");
       exit;
    }
    else
    {
        make_json_error($db->error());
    }
}

/*------------------------------------------------------ */
//-- 会员余额函数部分
/*------------------------------------------------------ */
/**
 * 查询会员余额的数量
 * @access  public
 * @param   int     $user_id        会员ID
 * @return  int
 */
function get_user_surplus($user_id)
{
    $sql = "SELECT SUM(user_money) FROM " .$GLOBALS['ecs']->table('account_log').
           " WHERE user_id = '$user_id'";

    return $GLOBALS['db']->getOne($sql);
}

/**
 * 更新会员账目明细
 *
 * @access  public
 * @param   array     $id          帐目ID
 * @param   array     $admin_note  管理员描述
 * @param   array     $amount      操作的金额
 * @param   array     $is_paid     是否已完成
 *
 * @return  int
 */
function update_user_account($id, $amount, $admin_note, $is_paid)
{
    $sql = "UPDATE " .$GLOBALS['ecs']->table('user_account'). " SET ".
           "admin_user  = '$_SESSION[admin_name]', ".
           "amount      = '$amount', ".
           "paid_time   = '".gmtime()."', ".
           "admin_note  = '$admin_note', ".
           "is_paid     = '$is_paid' WHERE id = '$id'";
    return $GLOBALS['db']->query($sql);
}

/**
 *
 *
 * @access  public
 * @param
 *人工扣费列表
 * @return void
 */
function artificial_list($filter='', $out=false)
{
    $result = get_filter();
    if ($result === false)
    {

        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'Id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        /* 过滤列表 */
        $filter['orderid'] = !empty($_REQUEST['orderid']) ? trim($_REQUEST['orderid']) : '';//订单号
        $filter['card'] = !empty($_REQUEST['card']) ? trim($_REQUEST['card']) : '';//卡号
        $filter['operation'] = empty($_REQUEST['operation']) ? '' : trim($_REQUEST['operation']);//操作人

        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['operation'] = json_str_iconv($filter['operation']);
        }

        $filter['price'] = trim($_REQUEST['price'])>0 ? trim($_REQUEST['price']) : '';
        $filter['operation_reason'] = empty($_REQUEST['operation_reason']) ? '' : trim($_REQUEST['operation_reason']);
        $filter['add_time'] = gmtime();
    

        $where = " WHERE 1 ";
        if ($filter['price'] > 0)
        {
            $where .= " AND a.price = '$filter[price]' ";
        }
        if (!empty($filter['orderid']))
        {
            $where .= " AND a.orderid = '$filter[orderid]' ";
        }
        if (!empty($filter['card']))
        {
            $where .= " AND a.card = '$filter[card]' ";
        }
       
        if (!empty($filter['operation_reason']))
        {
            $where .= " AND a.operation_reason = '$filter[operation_reason]' ";
        }
        if ($filter['operation'])
        {
            $where .= " AND a.operation LIKE '%" . mysql_like_quote($filter['operation']) . "%'";
            $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('artificial')." as a". $where;
       // $sql = "SELECT * FROM ".$ecs->table('artificial').
    //        " ORDER BY Id DESC";
    // $res = $db->getAll($sql);
        }
       
        /*　时间过滤　*/
        if (!empty($filter['start_time']) && !empty($filter['end_time']))
        {
            $where .= "AND add_time >= " . $filter['start_time']. " AND add_time < '" . $filter['end_time'] . "'";
        }

        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('artificial'). " AS a ".$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);
        // 导出的话，去掉分页参数
        if ($out == true)
        {
            $filter['page_size'] = 1000;
        
        }
          /* 查询数据 */
        $sql  = 'SELECT * FROM ' .
            $GLOBALS['ecs']->table('artificial'). ' AS a  ' .
            $where . "ORDER by " . $filter['sort_by'] . " " .$filter['sort_order']. " LIMIT ".$filter['start'].", ".$filter['page_size'];

        $filter['operation'] = stripslashes($filter['operation']);
        set_filter($filter, $sql);

    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $list = $GLOBALS['db']->getAll($sql);

    foreach ($list AS $key => $value)
    {
        $list[$key]['Id']    =  $value['Id'];
        $list[$key]['price']       = price_format(abs($value['price']), false);
        $list[$key]['add_time']             = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);
        $list[$key]['operation']    =  $value['operation'];
        $list[$key]['operation_reason']    =  $value['operation_reason'];
     }
     
    $arr = array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    return $arr;
}

?>