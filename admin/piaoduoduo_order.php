<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . 'includes/lib_cardApi.php');
// include_once(ROOT_PATH . 'includes/lib_order.php');
/*------------------------------------------------------ */
//-- 订单列表
/*------------------------------------------------------ */
$act=$_REQUEST['act']?$_REQUEST['act']:'list';
if ($act == 'list'){

	$smarty->assign('ur_here', '票工厂订单');
	$smarty->assign('full_page',        1);
	//自动更新订单状态
	// $orders=update_piaoduoduo_order_status();
	$order_list = piaoduoduo_order_list();
	$smarty->assign('order_list',   $order_list['orders']);
	$smarty->assign('filter',       $order_list['filter']);
	$smarty->assign('record_count', $order_list['record_count']);
	$smarty->assign('page_count',   $order_list['page_count']);
	$smarty->assign('sort_order_time', '<img src="images/sort_desc.gif">');
// echo "<pre>";
// print_r($order_list);
// echo "</pre>";
// die;
	/* 显示模板 */
	assign_query_info();
	$smarty->display('piaoduoduo_order.htm');
}


/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
//http://www.huaying.ccc/admin/piaoduoduo_order.php?act=query&order_sn=2015102729354&user_name=&page=1&page_size=15&record_count=28&page_count=2
elseif ($act == 'query')
{
	$order_list = piaoduoduo_order_list();
	$smarty->assign('order_list',   $order_list['orders']);
	$smarty->assign('filter',       $order_list['filter']);
	$smarty->assign('record_count', $order_list['record_count']);
	$smarty->assign('page_count',   $order_list['page_count']);
	$sort_flag  = sort_flag($order_list['filter']);
// echo "<pre>";
// print_r($order_list);
// echo "</pre>";
// die;
	// error_log(var_export($order_list,true),'3','error.log');
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	make_json_result($smarty->fetch('piaoduoduo_order.htm'), '', array('filter' => $order_list['filter'], 'page_count' => $order_list['page_count']));
}

// //订单批量删除操作
// elseif ($act == 'operate'){
	
// 	$batch      = isset($_REQUEST['batch']); // 是否批处理
// 	$order_sn   = $_REQUEST['order_sn'];
// 	$order_sn_list = explode(',', $order_sn);
// 	if (isset($_POST['remove'])){
// 		foreach ($order_sn_list as $sn_order){
// 			/* 删除订单 */
// 			$res=$db->query("DELETE FROM ".$ecs->table('piaoduoduo_order'). " WHERE order_sn = '$sn_order'");
// 			$sn_list[] = $sn_order;
// 		}

// 		$sn_list = join($sn_list, ',');
// 		$msg = $sn_list.'删除成功';
// 		$links[] = array('text' => $_LANG['return_list'], 'href' => 'piaoduoduo_order.php?act=list');
// 		sys_msg($msg, 0, $links);
// 	}
// }

// elseif ($act == 'remove_order'){

// 	$order_sn = intval($_REQUEST['order_sn']);

// 	$GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table('piaoduoduo_order'). " WHERE order_sn = '$order_sn'");
// 	if ($GLOBALS['db'] ->errno() == 0)
// 	{
// 		$url = 'piaoduoduo_order.php?act=list';
// 		ecs_header("Location: $url\n");
// 		exit;
// 	}
// 	else
// 	{
// 		sys_msg('删除失败');
// 	}
// }



//取消订单
else if(!empty($act) && $act == 'quxiao'){
    if($_REQUEST['order_sn']){
        $order_sn=trim($_REQUEST['order_sn']);//自动生成的订单号 
    }else{
        echo"<script>alert('单号不能为空');history.go(-1);</script>";
    }
    $sel_piaoduoduo_order=sql_piaoduoduo_order($order_sn,'select',$arr="");
    $orderKeyId=$sel_piaoduoduo_order['api_order_sn'];//返回的订单号
    $piao_order_quxiao = piaoduoduo('Agent_OrderCancel',array('orderKeyId'=>$orderKeyId));//取消订单
    $piao_order_list = piaoduoduo('Agent_GetOrderInfoByKeyId',array('orderKeyId'=>$orderKeyId));//查询订单
    $arr['Status']=2048;    
    if($piao_order_quxiao['head']['statuscode']==100){
        // $arr['Status']=$piao_order_quxiao['Status']=='CANCEL'?1:'';
        $update_piaoduoduo_order=sql_piaoduoduo_order($order_sn,'update',$arr);
        sys_msg('订单已取消');
    }else{
        sys_msg('已发码不能取消或请重试！');
    }
}
//重新发码
else if(!empty($act) && $act == 'again'){
    if($_REQUEST['order_sn']){
        $order_sn=trim($_REQUEST['order_sn']);//自动生成的订单号 
    }else{
        echo"<script>alert('单号不能为空');history.go(-1);</script>";
    }
    $sel_piaoduoduo_order=sql_piaoduoduo_order($order_sn,'select',$arr="");
    $orderKeyId=$sel_piaoduoduo_order['api_order_sn'];//返回的订单号
    $piao_order_again = piaoduoduo('Agent_ReSendMsg',array('orderKeyId'=>$orderKeyId));//重新发码
    if($piao_order_again['head']['statuscode']==100){
	 	$piao_order_list = piaoduoduo('Agent_GetOrderInfoByKeyId',array('orderKeyId'=>$orderKeyId));//查询订单
	    $arr['Status']=$piao_order_list['Status'];
	    $update_piaoduoduo_order=sql_piaoduoduo_order($order_sn,'update',$arr);
	    if($update_piaoduoduo_order){
	    	sys_msg('短信已发送，请注意查收！');
	        // echo "<script>alert('短信已发送，请注意查收！');history.go(-1);</script>";
	    }else{
	        sys_msg('请重试！');
	    }
    }else{
    	sys_msg('请重试！');
    }
   
  
// echo "<pre>";
// print_r($piao_order_again);
// echo "</pre>";
// die;
}
//
else if(!empty($act) && $act == 'tuipiao'){
    if($_REQUEST['order_sn']){
        $order_sn=trim($_REQUEST['order_sn']);//自动生成的订单号 
    }else{
        echo"<script>alert('单号不能为空');history.go(-1);</script>";
    }
    $num=$_REQUEST['num']?$_REQUEST['num']:1;
    $sel_piaoduoduo_order=sql_piaoduoduo_order($order_sn,'select',$arr="");
    $orderKeyId=$sel_piaoduoduo_order['api_order_sn'];
    $arr_param=array(
        'orderKeyId'=>$orderKeyId,//票务吧订单id
        'ParnterId'=>$sel_piaoduoduo_order['order_sn'],//伙伴退票id
        'ProductVersionKeyId'=>$sel_piaoduoduo_order['productVersionKeyId'],//退票产品版本id
        'Num'=>$num
        );
    $piao_order_tuipiao = piaoduoduo('Agent_OrderRefund',$arr_param);//退票
	if($piao_order_tuipiao['head']['statuscode']==100){
		$piao_order_list = piaoduoduo('Agent_GetOrderInfoByKeyId',array('orderKeyId'=>$orderKeyId));//查询订单
		//退还用户点数
		//卡充值
		$user_name=$sel_piaoduoduo_order['user_name'];
		$total_price=price_format($sel_piaoduoduo_order['SellPrice']*$num);
		$arr_param = array(
			'cardSeq'   => $user_name,//卡序号
			'orderType' => 1,//1，单卡充值，2，批量充值
			'operId'    => $GLOBALS['_CFG']['operId'],//充值操作员(自助终端传终端编号)
			'cardNum'   => 1,//充值卡数量
			'saleId'    => $GLOBALS['_CFG']['saleId'],//售卡机构编号
			'timeStamp' => local_date('YmdHis'),//时间戳
			'company'   => '',//购卡单位
			'singleSaveAmount' => $total_price,//单张充值金额
			'singleRealAmount' => $total_price,//单张实收金额
			'totalSaveAmount'  => '',//总充值金额
			'totalRealAmount'  => '',//总实收金额
			'expDate'      => '',//有效期
			'thirdJournal' => '',//第三方流水号
			'extendInfo'   => ''//接口扩展字段信息
		);
		//var_dump($arr_param);exit;
		$arr_cardInfo = getCardApi($arr_param, 'CARD-RECHARGE', 7);
		// $arr_cardInfo['ReturnCode'] = '0';
		if ($arr_cardInfo['ReturnCode'] != '0'){
			sys_msg($arr_cardInfo['ReturnMessage']);
		}else{
			//审核充值操作
			$arr_param = array(
				'orderId'    => $arr_cardInfo['OrderId'],
				'operId'     => $GLOBALS['_CFG']['operId'],
				'extendInfo' => ''
			);
			$arr_cardInfo = getCardApi($arr_param, 'CARD-AUTH-RECHARGE', 7);
			if ($arr_cardInfo['ReturnCode'] != '0'){
				sys_msg($arr_cardInfo['ReturnMessage']);
				//更新卡金额
	        	$update_user=$db->query('UPDATE '.$ecs->table('users')." SET card_money = card_money + ('$total_price') WHERE user_id = '".intval($sel_piaoduoduo_order['user_id'])."'");
	        
			}
		}	
	    // $arr['Status']=64;//订单状态
	    $arr['refund']=$piao_order_list['WithDrawAmount'];//退票次数
	    if($arr['refund']==($sel_piaoduoduo_order['refund']+$num)){
	    	$arr['Status']=128;//订单状态
	    }else{
	    	$arr['Status']=64;//订单状态
	    }
	    // $arr['refund_num']=$piao_order_list['WithDrawAmount'];//退票数量
	    $update_piaoduoduo_order=sql_piaoduoduo_order($order_sn,'update',$arr);
	    if($update_piaoduoduo_order){
	    	sys_msg('已退票一张！');
	        // echo "<script>alert('已退票一张！');</script>";
	    }else{
	        sys_msg('请重试！');
	    }
	}else{
		sys_msg('退票失败！');
	}   

}
// 票工厂列表导出
else if (!empty($act) && $act == 'piaoduoduo_excel_out') {
// echo "<pre>";
// print_r($_REQUEST);
// echo "</pre>";
// die;
	$smarty->assign ( 'ur_here', '票工厂列表导出' );
	$smarty->assign ( 'act', $act );
	$smarty->display ('piaoduoduo_excel.htm' );
}

// 票工厂列表导出动作
else if (!empty($act) && $act == 'piaoduoduo_out') {
	// 选择的时间
	$start_time = empty ( $_REQUEST ['start_time'] ) ? '' : (strpos ( $_REQUEST ['start_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['start_time'] ) : $_REQUEST ['start_time']);
	$end_time = empty ( $_REQUEST ['end_time'] ) ? '' : (strpos ( $_REQUEST ['end_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['end_time'] ) : $_REQUEST ['end_time']);
	$filter ["start_time"] = $start_time;
	$filter ["end_time"] = $end_time;
	$data = piaoduoduo_order_list( $filter );
	
	$row = 2;
	foreach ( $data['orders'] as $key => $val ) {
		
		if (! isset ( $key ) && empty ( $key )) {
			continue;
		}
		$exportInfo [] = array (
				'A' . $row => ' ' . $val ['order_sn'],
				'B' . $row => ' ' . $val ['api_order_sn'],
				'C' . $row => ' ' . $val ['pay_time'],
				'D' . $row => ' ' . '用户名：'.$val['user_name']."\r\n".'真实姓名：'.$val['name'] ."\r\n".'证件号码：'.$val['card_num']."\r\n".'手机:' .$val['MobileNumberToGetEticket']."\r\n".' 票名称：'.$val['ProductName']."\r\n".'票类型：'.$val['TicketCategory']."\r\n".'票码：'.$val['ListETicketCode']."\r\n".'预约时间：'.$val['TripDate'],
				'E' . $row => ' ' . $val ['SellPrice'],
				'F' . $row => ' ' . $val ['TotalTicketQuantity'],
				'G' . $row => ' ' . $val ['total_price'],
				'H' . $row => ' ' . $val ['MarketPrice'],
				'I' . $row => ' ' . $val ['tuipiao_state'],
				'J' . $row => ' ' . $val ['refund'],  
				'K' . $row => ' ' . $val ['Status']
				
				
		);
		
		$row ++;
	}
// echo "<pre>";
// print_r($exportInfo);
// echo "</pre>";
// die;
	// 导入excel类
	require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes\lib_autoExcels.php');
	require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes\Classes\PHPExcel.php');
	//创建对象
	$excel = new PHPExcel();
	//Excel表格式
	$letter = array('A','B','C','D','E','F','G','H','I','J','K');
	//表头数组
	$tableheader = array('订单号 ','返回订单号','支付时间','购买人','单价','数量','订单金额','市场价(单价)','是否可以退票','退票数量','订单状态');
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
	$excel->getActiveSheet()->getStyle('D2:E10000')->getAlignment()->setWrapText(true);
	$excel->getActiveSheet()->getColumnDimension("A")->setWidth('20');
	$excel->getActiveSheet()->getColumnDimension("B")->setWidth('30');
	$excel->getActiveSheet()->getColumnDimension("C")->setWidth('20');
	$excel->getActiveSheet()->getColumnDimension("D")->setWidth('70');
	$excel->getActiveSheet()->getColumnDimension("E")->setWidth('10');
	$excel->getActiveSheet()->getColumnDimension("F")->setWidth('10');
	$excel->getActiveSheet()->getColumnDimension("G")->setWidth('10');
	$excel->getActiveSheet()->getColumnDimension("H")->setWidth('10');
	$excel->getActiveSheet()->getColumnDimension("I")->setWidth('10');
	$excel->getActiveSheet()->getColumnDimension("J")->setWidth('20');
	$excel->getActiveSheet()->getColumnDimension("K")->setWidth('20');
// echo "<pre>";
// print_r($excel);
// echo "</pre>";
// die;
	//创建Excel输入对象
	$fileNmae = 'piaoduoduo_order-' . local_date ( 'mdHis', time () ) . '.xls';
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
//查询票工厂数据
//http://www.huaying.ccc/admin/piaoduoduo_order.php?act=query&order_sn=2015102878961&user_name=9990111942832469&page=2&page_size=15&record_count=29&page_count=5
function piaoduoduo_order_list()
{

	$result = get_filter();

	if ($result === false){
		/* 过滤信息 */
		$filter['order_sn'] = empty($_REQUEST['order_sn']) ? '' : trim($_REQUEST['order_sn']);
		$filter['user_id'] = empty($_REQUEST['user_id']) ? '' : intval($_REQUEST['user_id']);
		$filter['user_name'] = empty($_REQUEST['user_name']) ? '' : trim($_REQUEST['user_name']);
		$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'pay_time' : trim($_REQUEST['sort_by']);
		$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
		$where = 'WHERE 1 ';
		if ($filter ['start_time'] != '' && $filter ['end_time'] != '') {
			$where .= " and p.pay_time >= '" . $filter ['add_time'] . "' and p.pay_time <= '" . $filter ['end_time'] . "' ";
		}
		if ($filter['order_sn'])
		{
			$where .= " AND p.order_sn LIKE '%" . mysql_like_quote($filter['order_sn']) . "%'";
		}
		if ($filter['user_id'])
		{
			$where .= " AND p.user_id = '$filter[user_id]'";
		}
		if ($filter['user_name'])
		{
			$where .= " AND p.user_name = '$filter[user_name]'";
		}

		/* 分页大小 */
		$filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

		if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
		{
			$filter['page_size'] = intval($_REQUEST['page_size']);
		}
		elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
		{
			$filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
		}
		else
		{
			$filter['page_size'] = 15;
		}
		$filter['keywords'] = stripslashes($filter['keywords']);
		if ($filter['keywords'])
		{
			$where .= " AND p.ProductName LIKE '%" . mysql_like_quote($filter['keywords']) . "%'";
		}  
		set_filter($filter, $sql); 
		/* 记录总数 */
		if ($filter['user_name'])
		{
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('piaoduoduo_order') . " AS p ".
				   $where;

		}
		else
		{
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('piaoduoduo_order') . " AS p ". $where;
		}

		$filter['record_count']   = $GLOBALS['db']->getOne($sql);
		$filter = page_and_size($filter);
// echo "<pre>";
// print_r($filter);
// echo "</pre>";
// die;	
		$filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
		/* 查询 */
		$sql = "SELECT p.* FROM " . $GLOBALS['ecs']->table('piaoduoduo_order') . " AS p " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=p.user_id ". $where .
				" ORDER BY $filter[sort_by] $filter[sort_order] ".
				" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";
// echo "<pre>";
// print_r($sql);
// echo "</pre>";
// die;
		foreach (array('order_sn', 'user_name') AS $val)
		{
			$filter[$val] = stripslashes($filter[$val]);
		}
		set_filter($filter, $sql);
	}
	else
	{
		$sql    = $result['sql'];
		$filter = $result['filter'];
	}
 
	$row = $GLOBALS['db']->getAll($sql);
// echo "<pre>";
// print_r($filter);
// echo "</pre>";
// die;
	/* 格式话数据 */
	foreach ($row AS $key => $value)
	{
		if ($value['Status'] == 2){
            $row[$key]['Status'] = '未付款';
            if ($value['pay_status'] == 0){
            $row[$key]['pay_status'] = "未付款";
            }else if ($value['pay_status'] == 1){
                $row[$key]['pay_status'] = '已付款';
            }
        }elseif($value['Status'] == 1){
            $row[$key]['Status'] = '已取消';
        }elseif($value['Status'] == 4){
            $row[$key]['Status'] = '已付款，待发码';
            
        }elseif($value['Status'] == 8){
            $row[$key]['Status'] = '已付款，已发码';

        }elseif($value['Status'] == 16){
            $row[$key]['Status'] = '部分使用';
          
        }elseif($value['Status'] == 32){
            $row[$key]['Status'] = '全部使用';
        }elseif($value['Status'] == 64){
            $row[$key]['Status'] = '部分退票';
          	$row[$key]['refund'] = $value['refund'];
        }elseif($value['Status'] == 128){
            $row[$key]['Status'] = '全部退票';
        }elseif($value['Status'] == 256){
            $row[$key]['Status'] = '已退款';
        }elseif($value['Status'] == 1024){
            $row[$key]['Status'] = '电子票使用期已过期';
        }elseif($value['Status'] == 2048){
            $row[$key]['Status'] = '用户已取消';
        }elseif($value['Status'] ==4096){
            $row[$key]['Status'] = '订单未支付已过期';
        }elseif($row['Status'] == 8192){
            $row[$key]['Status'] = '向供应商下单失败';                                                  
        }else{
            $row[$key]['Status'] = '失效';
        }

        if ($value['pay_status'] == 1){
                $row[$key]['pay_status'] = '已付款';
        }

        if($value['TicketCategory'] == 1){
            $row[$key]['TicketCategory'] = '散客';
        }elseif($value['TicketCategory'] == 2){
            $row[$key]['TicketCategory'] = '团队';
        }else{

        }
        if($value['PeriodValidityOfRefund']>0){
        	$row[$key]['tuipiao_state']      = "是";
        	if(in_array($value['Status'],array(4,8,16,32,64))){
        		$row[$key]['tuipiao']  =  '<a href="/admin/piaoduoduo_order.php?act=tuipiao&order_sn='.$value['order_sn'].'">申请退票</a>';
        	}
        		
        }else{
        	$row[$key]['tuipiao_state']      = "否";
        }
    	if(in_array($value['Status'],array(2,4))){
    		$row[$key]['quxiao']  =  '<a onclick="p_del()" href="/admin/piaoduoduo_order.php?act=quxiao&order_sn='.$value['order_sn'].'">取消订单</a>';    
        }
    	if(in_array($value['Status'],array(4,8,16,32,64))){
    		$row[$key]['again_sending']='<a href="/admin/piaoduoduo_order.php?act=again&order_sn='.$value['order_sn'].'">重新发码</a>';
        }        
        $row[$key]['add_time']      = local_date('Y-m-d H:i', $value['add_time']);
        $row[$key]['pay_time']      = local_date('Y-m-d H:i', $value['pay_time']);
        $row[$key]['SettlementPrice']   = price_format($value['SettlementPrice']);
        $row[$key]['SellPrice']     = price_format($value['SellPrice']);
        $row[$key]['total_price']   = price_format($value['SellPrice']*$value['TotalTicketQuantity']);
        $row[$key]['card_num']      = substr_replace($value['card_num'],"****",10,4);

	}
	$arr = array('orders' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	return $arr;
}
//票工厂订单
function sql_piaoduoduo_order($order_sn,$sql,$arr){
    $res="";
    if($sql=="select"){
        if($order_sn==""){
            $sql_sel_coupons_order="select * from ".$GLOBALS ['ecs']->table ('piaoduoduo_order')." where username='".$arr."'";
            //echo $sql_sel_coupons_order;die;
            $res=$GLOBALS ['db']->getAll($sql_sel_coupons_order);
        }else{
            $sql_sel_piaoduoduo_order="select * from ".$GLOBALS ['ecs']->table ('piaoduoduo_order')." where order_sn='".$order_sn."'";
            //echo $sql_sel_coupons_order;die;
            $res=$GLOBALS ['db']->getRow($sql_sel_piaoduoduo_order);
        }
    
    
    }elseif($sql=="insert"){
        
        $insert_piaoduoduo_order="insert INTO ".$GLOBALS ['ecs']->table ('piaoduoduo_order')."(order_sn,api_order_sn,user_id,user_name,name,MobileNumberToGetEticket,card_num,card_class,pay_status,Status,PeriodValidityOfRefund,productVersionKeyId,SupplierKeyId,ProductVersionCode,ProductName,City,SightAddress,MarketPrice,SellPrice,SettlementPrice,market_price,TotalTicketQuantity,TicketCategory,TripDate,add_time,pay_time) VALUES('".$arr['order_sn']."','".$arr['api_order_sn']."','".$arr['user_id']."','".$arr['user_name']."','".$arr['name']."','".$arr['MobileNumberToGetEticket']."','".$arr['card_num']."','".$arr['card_class']."','".$arr['pay_status']."','".$arr['Status']."','".$arr['PeriodValidityOfRefund']."','".$arr['productVersionKeyId']."','".$arr['SupplierKeyId']."','".$arr['ProductVersionCode']."','".$arr['ProductName']."','".$arr['City']."','".$arr['SightAddress']."','".$arr['MarketPrice']."','".$arr['SellPrice']."','".$arr['SettlementPrice']."','".$arr['market_price']."','".$arr['TotalTicketQuantity']."','".$arr['TicketCategory']."','".$arr['TripDate']."','".$arr['add_time']."','".$arr['pay_time']."')";
        // echo $insert_piaoduoduo_order;die;
        $res=$GLOBALS ['db']->query($insert_piaoduoduo_order);
        
    }elseif($sql=="update"){
        // $pay_time=gmtime();
        // $set.="pay_time='".$pay_time."'";
        foreach ($arr as $key => $value) {
            if($value){
                $set1.=$key."='".$value."',";
            }
        }
        $set=mb_substr($set1,0,mb_strlen($set1)-1); 
        $update_piaoduoduo_order="UPDATE ".$GLOBALS['ecs']->table('piaoduoduo_order')." set ".$set." where order_sn='".$order_sn."'";
         // echo $update_piaoduoduo_order;die;
        $res= $GLOBALS['db']->query($update_piaoduoduo_order);
    }else{
        return false;
    }

    return $res;    

}
