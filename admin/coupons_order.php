<?php
define ( 'IN_ECS', true );

require (dirname ( __FILE__ ) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
include_once(ROOT_PATH . 'includes/lib_coupons.php');

/* ------------------------------------------------------ */
// -- 会员提货券订单列表管理
/* ------------------------------------------------------ */
$act=$_REQUEST['act']?$_REQUEST['act']:'list';
if ($act == 'list') {
	$smarty->assign ( 'ur_here', '提货券订单列表管理' );
	$smarty->assign ( 'full_page', 1 );

	$res_coupons_order_list = coupons_order_list();
	$smarty->assign ( 'x', $res_coupons_order_list ['x'] ); // 已付款
	$smarty->assign ( 'y', $res_coupons_order_list ['y'] ); // 未付款
	$smarty->assign ( 'z', $res_coupons_order_list ['z'] ); // 无效
	$smarty->assign ( 'coupons_order_list', $res_coupons_order_list ['list'] );
	$smarty->assign ( 'filter', $res_coupons_order_list ['filter'] );
	$smarty->assign ( 'record_count', $res_coupons_order_list['record_count'] );
	$smarty->assign ( 'page_count', $res_coupons_order_list['page_count'] );
	
	/* 显示模板 */
	assign_query_info ();
	$smarty->display ( 'coupons_order.htm' );
}
// 提货券订单分页，搜索
elseif ($act == 'query') {

	$res_coupons = coupons_order_list();
	$smarty->assign ( 'x', $res_coupons ['x'] );
	$smarty->assign ( 'y', $res_coupons ['y'] );
	$smarty->assign ( 'z', $res_coupons ['z'] ); // 无效
	$smarty->assign ( 'coupons_order_list', $res_coupons ['list'] );
	$smarty->assign ( 'filter', $res_coupons ['filter'] );
	$smarty->assign ( 'record_count', $res_coupons ['record_count'] );
	$smarty->assign ( 'page_count', $res_coupons ['page_count'] );
	$sort_flag = sort_flag ( $res_coupons ['filter'] );
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	make_json_result ( $smarty->fetch ( 'coupons_order.htm' ), '', array (
			'filter' => $res_coupons ['filter'],
			'page_count' => $res_coupons ['page_count'] 
	) );
}
// 会员提货券列表导出
if ($act == 'co_excel_out') {
	$smarty->assign ( 'ur_here', '会员提货券列表导出' );
	$smarty->assign ( 'act', $act );
	$smarty->display ( 'coupons_order_excel.htm' );
}
// 会员提货券列表处理数据导出处理
// elseif($act == 'co_out'){
// 	// 选择的时间
// 	$start_time = empty ( $_REQUEST ['start_time'] ) ? '' : (strpos ( $_REQUEST ['start_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['start_time'] ) : $_REQUEST ['start_time']);
// 	$end_time = empty ( $_REQUEST ['end_time'] ) ? '' : (strpos ( $_REQUEST ['end_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['end_time'] ) : $_REQUEST ['end_time']);
// 	$filter ["start_time"] = $start_time;
// 	$filter ["end_time"] = $end_time;
// 	$data = coupons_order_excel( $filter );
// 	$row = 2;
// 	foreach ( $data as $key => $val ) {
		
// 		if (! isset ( $key ) && empty ( $key )) {
// 			continue;
// 		}
// 		$exportInfo [] = array (
// 				'A' . $row => ' ' . $val ['orderid'],
// 				'B' . $row => ' ' . $val ['goods_name'],
// 				'C' . $row => ' ' . $val ['user_name'],
// 				'D' . $row => ' ' . $val ['mobile'],
// 				'E' . $row => ' ' . $val ['coupons_youxiao'],
// 				'F' . $row => ' ' . $val ['unit_price'],
// 				'G' . $row => ' ' . $val ['number'],
// 				'H' . $row => ' ' . $val ['total_price'],
// 				'I' . $row => ' ' . $val ['market_price'], 
// 				'J' . $row => ' ' . $val ['add_time'],
// 				'k' . $row => ' ' . $val ['order_state']
				
// 		);
		
// 		$row ++;
// 	}
// 	//var_dump($exportInfo);die;
// 	// 导入excel类
// 	require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes\lib_autoExcels.php');
// 	require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes\Classes\PHPExcel.php');
// 	$autoExcels1 = new autoExcels ( 'Excel2007' );
// 	$fileNmae = 'coupons_order-' . local_date ( 'mdHis', time () ) . '.xlsx';
// 	$autoExcels1->setSaveName ( $fileNmae );
// 	$exportContent = array (
// 			array (
// 					'sheetName' => '提货券列表',
// 					'title' => array (
// 							'A1' => '订单号',
// 							'B1' => '商品名',
// 							'C1' => '会员名', 
// 							'D1' => '手机号',
// 							'E1' => '提货券（有效期）',
// 							'F1' => '单价', 
// 							'G1' => '数量', 
// 							'H1' => '总价',
// 							'I1' => '市场价',
// 							'J1' => '下单时间',
// 							'K1' => '状态',
							 
// 					),
// 					'content' => $exportInfo,
// 					'widths' => array (
// 							'A' => '20',
// 							'B' => '30',
// 							'C' => '20',
// 							'D' => '30',
// 							'E' => '50',
// 							'F' => '10',
// 							'G' => '10',
// 							'H' => '10',
// 							'I' => '10',
// 							'J' => '20', 
// 							'K' => '10', 
							
// 					) 
// 			) 
// 	);
// // echo "<pre>";
// // print_r($exportContent);
// // echo "</pre>";
// // die;
// 	$autoExcels1->setTitle ( $exportContent );
// 	ini_set ( "memory_limit", '180M' );
// 	// $colRow = $autoExcels->getColsFormat('A','B');
// 	// $autoExcels->PHPExcel()->getSheet(0)->getStyle($colRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
// 	$autoExcels1->execExcel('export');
// }
// 会员提货券列表处理数据导出处理
elseif($act == 'co_out'){
	// 选择的时间
	$start_time = empty ( $_REQUEST ['start_time'] ) ? '' : (strpos ( $_REQUEST ['start_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['start_time'] ) : $_REQUEST ['start_time']);
	$end_time = empty ( $_REQUEST ['end_time'] ) ? '' : (strpos ( $_REQUEST ['end_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['end_time'] ) : $_REQUEST ['end_time']);
	$filter ["start_time"] = $start_time;
	$filter ["end_time"] = $end_time;
	$data = coupons_order_excel( $filter );
	$row = 2;
	foreach ( $data as $key => $val ) {
		
		if (! isset ( $key ) && empty ( $key )) {
			continue;
		}
		$exportInfo [] = array (
				'A' . $row => ' ' . $val ['orderid'],
				'B' . $row => ' ' . $val ['goods_name'],
				'C' . $row => ' ' . $val ['user_name'],
				'D' . $row => ' ' . $val ['mobile'],
				'E' . $row => ' ' . $val ['coupons_youxiao'],
				'F' . $row => $val ['unit_price'],
				'G' . $row => $val ['number'],
				'H' . $row => $val ['total_price'],
				'I' . $row => $val ['market_price'], 
				'J' . $row => ' ' . $val ['add_time'],
				'k' . $row => ' ' . $val ['order_state']
				
		);
		
		$row ++;
	}

	// 导入excel类
	require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes\lib_autoExcels.php');
	require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes\Classes\PHPExcel.php');
	//创建对象
	$excel = new PHPExcel();
	//Excel表格式
	$letter = array('A','B','C','D','E','F','G','H','I','J','K');
	//表头数组
	$tableheader = array('订单号','商品名','会员名','手机号','提货券（有效期）','单价','数量','总价','市场价','下单时间','状态');
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
	$excel->getActiveSheet()->getStyle('E2:E10000')->getAlignment()->setWrapText(true);
	$excel->getActiveSheet()->getColumnDimension("A")->setWidth('20');
	$excel->getActiveSheet()->getColumnDimension("B")->setWidth('30');
	$excel->getActiveSheet()->getColumnDimension("C")->setWidth('20');
	$excel->getActiveSheet()->getColumnDimension("D")->setWidth('20');
	$excel->getActiveSheet()->getColumnDimension("E")->setWidth('70');
	$excel->getActiveSheet()->getColumnDimension("F")->setWidth('10');
	$excel->getActiveSheet()->getColumnDimension("G")->setWidth('10');
	$excel->getActiveSheet()->getColumnDimension("H")->setWidth('10');
	$excel->getActiveSheet()->getColumnDimension("I")->setWidth('10');
	$excel->getActiveSheet()->getColumnDimension("J")->setWidth('20');
	$excel->getActiveSheet()->getColumnDimension("K")->setWidth('10');

	//创建Excel输入对象
	$fileNmae = 'coupons_order-' . local_date ( 'mdHis', time () ) . '.xls';
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
// 会员提货券管理客服发送提货券短信
elseif($act == 'orderid'){
	$orderid = $_REQUEST ['orderid'];
	if(empty($orderid)){
		sys_msg('请选择订单！');
	}
	//查询提货券，数量，名称，有效期，发送
	$goods_name = 'SELECT co.* FROM ' . $GLOBALS['ecs']->table('coupons_order') . ' AS co ' ."WHERE co.order_state=1 and co.orderid='".$orderid."'";
	$res_goods_name = $db->getRow($goods_name);
	if(empty($res_goods_name)){
		sys_msg('订单状态必须已经付款！');
	}
	$guige=guigeSms($orderid);
	include_once(ROOT_PATH . 'includes/cls_sms.php');
	$sms=new sms();	
    $coupons_sms = $sms->send($res_goods_name['mobile'], $guige['content'], '', $send_num = 1);
	if($coupons_sms){
		sys_msg('短信已发送');
		
	}else{
		sys_msg('短信发送失败');
	}
}
// 提货券读取数据格式化
function coupons_order_excel($filter) {
	$where = ' WHERE 1 ';
	
	if ($filter ['add_time'] != '' && $filter ['add_time'] != '') {
		$where .= " and c.add_time >= '" . $filter ['add_time'] . "' and c.add_time <= '" . $filter ['add_time'] . "' ";
	}
	$sql = "select * from  " . $GLOBALS ['ecs']->table ( 'coupons_order' ) . " as c " . $where . " order by c.id DESC";
	$res = $GLOBALS ['db']->getAll ( $sql );
	foreach ( $res as $key => $val ) {
		$coupons_list [$key] ['id'] = $val ['Id'];
		$coupons_list [$key] ['orderid'] = $val ['orderid'];
		$sql_goods_name1='SELECT * FROM '.$GLOBALS ['ecs']->table('goods')." WHERE goods_id = ".$val ['goods_id'];
		$sql_goods_name = $GLOBALS['db']->getRow($sql_goods_name1);
		$coupons_list [$key] ['goods_name'] = $sql_goods_name ['goods_name'];//产品名
 	  	$coupons_list [$key] ['goods_id'] = $val ['goods_id'];
		$coupons_list [$key] ['userid'] = $val ['userid'];
		$coupons_list [$key] ['user_name'] = $val ['user_name'];
		$coupons_list [$key] ['mobile'] = $val ['mobile'];
		$guige=guigeSms($val ['orderid']);
		$coupons_list [$key] ['coupons_youxiao'] ='';
		@$coupons_list [$key] ['coupons_youxiao'] =$guige['tihuo']."（有效期：".$guige['end_time']."）" ;//提货券（有效期）		
		$coupons_list [$key] ['unit_price'] = $val ['unit_price'];
		$coupons_list [$key] ['number'] = $val ['number'];
		$coupons_list [$key] ['total_price'] = $val ['total_price'];
		$coupons_list [$key] ['market_price'] = $val ['market_price'];
		$coupons_list [$key] ['add_time'] = local_date ( "Y-m-d H:i:s", $val ['add_time'] );
		if ($val ['order_state'] == 1) {
			$coupons_list [$key] ['order_state'] = "已付款";

		} elseif($val ['order_state'] == 2) {
			$coupons_list [$key] ['order_state'] = "失效";
			
		} elseif($val ['order_state'] == 0) {
			$coupons_list [$key] ['order_state'] = "未付款";
			
		} else {
			$coupons_list [$key] ['order_state'] = "错误";
			
		}
	}
	return $coupons_list;
}
/**
 * 提货券列表
 */
function coupons_list() {
	$result = get_filter ();
	
	if ($result === false) {
		// 商家id
		$filter ['supplier_id'] = empty ( $_REQUEST ['supplier_id'] ) ? 0 : intval ( $_REQUEST ['supplier_id'] );
		$filter ['number'] = empty ( $_REQUEST ['number'] ) ? '' : trim( $_REQUEST ['number'] );
		$where = 'WHERE 1 ';
		
		if ($filter ['supplier_id'] > 0) 
		{
			$where .= " AND c.supplier_id = " . $filter ['supplier_id'];
		}
		if (!empty($filter ['number'])) 
		{
			$where .= " AND c.number = " . $filter ['number'];
		}
		/*
		 * echo $_COOKIE ['ECSCP'] ['page_size']; die;
		 */
		/* 分页大小 */
		$filter ['page'] = empty ( $_REQUEST ['page'] ) || (intval ( $_REQUEST ['page'] ) <= 0) ? 1 : intval ( $_REQUEST ['page'] );
		
		if (isset ( $_REQUEST ['page_size'] ) && intval ( $_REQUEST ['page_size'] ) > 0) {
			$filter ['page_size'] = intval ( $_REQUEST ['page_size'] );
		} elseif (isset ( $_COOKIE ['ECSCP'] ['page_size'] ) && intval ( $_COOKIE ['ECSCP'] ['page_size'] ) > 0) {
			$filter ['page_size'] = intval ( $_COOKIE ['ECSCP'] ['page_size'] );
		} else {
			$filter ['page_size'] = 30; // 每页显示的数目
		}
		/* 记录总数 */
		if ($filter ['supplier_id']) {
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS ['ecs']->table ( 'coupons' ) . " AS c " . $where;
		} else {
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS ['ecs']->table ( 'coupons' );
		}
		
		$filter ['record_count'] = $GLOBALS ['db']->getOne ( $sql );
		$filter ['page_count'] = $filter ['record_count'] > 0 ? ceil ( $filter ['record_count'] / $filter ['page_size'] ) : 1;
		
		/* 查询 */
		$sql = "SELECT c.*,s.supplier_name" . " FROM " . $GLOBALS ['ecs']->table ( 'coupons' ) . " AS c " . " LEFT JOIN " . $GLOBALS ['ecs']->table ( 'supplier' ) . " AS s ON s.supplier_id=c.supplier_id " . $where . " ORDER BY c.id DESC " . " LIMIT " . ($filter ['page'] - 1) * $filter ['page_size'] . "," . $filter ['page_size'];
		
		/*
		 *
		 */
	} else {
		$sql = $result ['sql'];
		$filter = $result ['filter'];
	}
	
	$conpons = $GLOBALS ['db']->getAll ( $sql );
	foreach ( $conpons as $key => $val ) {
		$coupons_list [$key] ['id'] = $val ['id'];
		$coupons_list [$key] ['coupons_number'] = $val ['coupons_number'];
		
		$coupons_list [$key] ['supplier_id'] = $val ['supplier_id'];
		$coupons_list [$key] ['price'] = $val ['price'];
		$coupons_list [$key] ['start_time'] = local_date ( "Y-m-d H:i:s", $val ['start_time'] );
		$coupons_list [$key] ['end_time'] = local_date ( "Y-m-d H:i:s", $val ['end_time'] );
		$coupons_list [$key] ['supplier_name'] = $val ['supplier_name'];
		if ($val ['coupons_state'] == 1) {
			$coupons_list [$key] ['coupons_state'] = "已使用";
		} else {
			$coupons_list [$key] ['coupons_state'] = "未使用";
		}
	}
	$sqlx = "SELECT sum(c.coupons_state=1) as count FROM " . $GLOBALS ['ecs']->table ( 'coupons' ) . " AS c ";
	$sqly = "SELECT sum(c.coupons_state=0) as count FROM " . $GLOBALS ['ecs']->table ( 'coupons' ) . " AS c ";
	$x = $GLOBALS ['db']->getOne ( $sqlx );
	$y = $GLOBALS ['db']->getOne ( $sqly );
	
	$arr = array (
			'list' => $coupons_list,
			'filter' => $filter,
			'page_count' => $filter ['page_count'],
			'record_count' => $filter ['record_count'],
			'x' => $x,
			'y' => $y 
	);
	return $arr;
}
//上传类，如果目录不存在就创建
function uploadFile($filenameUTF,$tmp_name) 
{
    //自己设置的上传文件存放路径
    $filePath =dirname(dirname ( __FILE__ )) .'/temp/upload/';
    $filenameGB=iconv("UTF-8","gb2312", $filenameUTF);
    $fileNamePath=$filePath.$filenameGB;
    // 首先需要检测目录是否存在
    //move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
    if(is_dir($filePath)){
    	if(file_exists($fileNamePath)){
    		//echo "文件已经存在，不能重复提交！";
    	}else{
    		$result=move_uploaded_file($tmp_name,$fileNamePath);
    	}
    }else{
    	mkdir($filePath);
    	
    	$result=move_uploaded_file($tmp_name,$fileNamePath);

    }
    return $fileNamePath;
} 
// // 提货券列表导出
// if ($act == 'coupons_excel_out') {
// 	$smarty->assign ( 'ur_here', '提货券列表导出' );
// 	$smarty->assign ( 'act', $act );
// 	$smarty->display ( 'coupons_excel.htm' );
// }
// // 提货券列表导入
// if ($act == 'coupons_excel_in') {
// 	$smarty->assign ( 'ur_here', '提货券列表导入' );
// 	$smarty->assign ( 'act', $act );
// 	$smarty->display ( 'coupons_excel.htm' );
// }

// 提货券列表处理数据导入
// elseif ($act == 'coupons_in'){
// 	if($_FILES['coupons_excel']['type']=='application/octet-stream'||$_FILES['coupons_excel']['type']=='application/vnd.ms-excel'){
// 	    $filename = $_FILES['coupons_excel']['name'];
// 	    $tmp_name = $_FILES['coupons_excel']['tmp_name'];
// 	    $msg = uploadFile($filename,$tmp_name);
// 	    //echo $msg;
// 	}else{
// 		echo "文件格式错误或者过大！";die;
// 	}
// 	//echo $msg;die;
// 	require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes/lib_autoExcels.php');
// 	$PHPExcel = new autoExcels('Excel2007');		//实例化类并传入导出格式（可以不传，默认是2007）
// 	//$PHPExcel = new autoExcels('Excel5');
// 	$PHPExcel->setSaveName($msg);		//要保存的文件名（必须）
// 	$colsTitle = array('0'=>array('A'=>'id','B'=>'coupons_number','C'=>'supplier_id','D'=>'price','E'=>'start_time','F'=>'end_time','G'=>'coupons_state'));
// 	$PHPExcel->setColsTitle($colsTitle);	
// 	@$list = $PHPExcel->execExcel('import');
// 	$list=$list['0'];

// 	unset($list['0']);	
// 		foreach ($list as $a => $b) {
// 			$array[$a]['id']=$b['id'];
// 			$array[$a]['coupons_number']=$b['coupons_number'];
			
// 			$sql = "select s.supplier_id from  " .  $GLOBALS ['ecs']->table ( 'supplier' ) . " as s where s.supplier_name= '".$b['supplier_id']."'";
// 			$supplier_id = $GLOBALS ['db']->getOne ( $sql );
// 			$array[$a]['supplier_id']=$supplier_id;
// 			$array[$a]['price']=$b['price'];
// 			$array[$a]['start_time']=local_strtotime($b['start_time']);
// 			$array[$a]['end_time']=local_strtotime($b['end_time']);
// 			$array[$a]['coupons_state']=0;
// 			$sql_select="select id from ".$GLOBALS ['ecs']->table ('coupons')."where coupons_number='".$array[$a]['coupons_number']."'";
// 			$sel=$GLOBALS ['db']->getOne($sql_select);
// 			if($sel){
// 				echo "请不要重复插入记录！<br/>";
// 				breck;
// 			}else{
// 				$sql ="insert INTO ".$GLOBALS ['ecs']->table ('coupons')."(coupons_number,supplier_id,price,start_time,end_time,coupons_state) VALUES('".$array[$a]['coupons_number']."','".$array[$a]['supplier_id']."','".$array[$a]['price']."','".$array[$a]['start_time']."','".$array[$a]['end_time']."','".$array[$a]['coupons_state']."');";
			
// 				$res[$a]=$GLOBALS ['db']->query($sql);
// 				if($res[$a]==1){
// 					echo "记录{$a}插入成功！<br/>";
// 				}else{
// 					echo "记录{$a}执行失败！<br/>";
// 				}
// 			}
		

			
// 		}

// }
// if  not exists(select * from 表 where 字段值 in 字段)
// begin
// insert into 表 (字段) values (字段值) where 字段值 is not in 字段
// end
// 提货券列表处理数据导出
// elseif($act == 'coupons_out'){
// 	// 选择的时间
// 	$start_time = empty ( $_REQUEST ['start_time'] ) ? '' : (strpos ( $_REQUEST ['start_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['start_time'] ) : $_REQUEST ['start_time']);
// 	$end_time = empty ( $_REQUEST ['end_time'] ) ? '' : (strpos ( $_REQUEST ['end_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['end_time'] ) : $_REQUEST ['end_time']);
// 	$filter ["start_time"] = $start_time;
// 	$filter ["end_time"] = $end_time;
// 	$data = coupons_excel( $filter );
// // echo "<pre>";
// // print_r($data);
// // echo "</pre>";
// // die;

// 	$row = 2;
// 	foreach ( $data as $key => $val ) {
// 		$sqls[$key] = "select s.supplier_name from  ".$GLOBALS ['ecs']->table ( 'supplier' ) . " as s where s.supplier_id= '".$val['supplier_id']."'";
// 		$supplier_id[$key] = $GLOBALS ['db']->getOne ( $sqls[$key] );
// 		$state[$key]=$val['coupons_state']? "已使用":"未使用";
// 		// 删除不存在的数据
// 		if (! isset ( $key ) && empty ( $key )) {
// 			continue;
// 		}
// 		$exportInfo [] = array (
// 				'A' . $row => ' ' . $val ['id'],
// 				'B' . $row => ' ' . $val ['coupons_number'],
// 				'C' . $row => ' ' . $supplier_id[$key],
// 				'D' . $row => ' ' . $val ['price'],
// 				'E' . $row => local_date ( "Y-m-d H:i:s", $val ['start_time'] ), 
// 				'F' . $row => local_date ( "Y-m-d H:i:s", $val ['end_time'] ), 
// 				'G' . $row => ' ' . $state[$key]
// 		);
		
// 		$row ++;
// 	}
// 	//var_dump($exportInfo);die;
// 	// 导入excel类
// 	require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes/lib_autoExcels.php');
// 	$autoExcels1 = new autoExcels ( 'Excel2007' );
// 	$fileNmae = 'coupons-' . local_date ( 'mdHis', time () ) . '.xlsx';
// 	$autoExcels1->setSaveName ( $fileNmae );
// 	$exportContent = array (
// 			array (
// 					'sheetName' => '提货券列表',
// 					'title' => array (
// 							'A1' => '序号',
// 							'B1' => '提货券的号码',
// 							'C1' => '供应商的id', 
// 							'D1' => '提货券的价格',
// 							'E1' => '开始时间',
// 							'F1' => '结束时间', 
// 							'G1' => '状态' 
// 					),
// 					'content' => $exportInfo,
// 					'widths' => array (
// 							'A' => '8',
// 							'B' => '30',
// 							'C' => '20',
// 							'D' => '30',
// 							'E' => '20',
// 							'F' => '20', 
// 							'G' => '20' 
// 					) 
// 			) 
// 	);
// // echo "<pre>";
// // print_r($exportContent);
// // echo "</pre>";
// // die;
// 	$autoExcels1->setTitle ( $exportContent );
// 	ini_set ( "memory_limit", '180M' );
// 	// $colRow = $autoExcels->getColsFormat('A','B');
// 	// $autoExcels->PHPExcel()->getSheet(0)->getStyle($colRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
// 	$autoExcels1->execExcel('export');
// }
// 提货券读取数据格式化
function coupons_excel($filter) {
	$where = ' WHERE 1 ';
	
	if ($filter ['start_time'] != '' && $filter ['end_time'] != '') {
		$where .= " and c.start_time >= '" . $filter ['start_time'] . "' and c.end_time <= '" . $filter ['end_time'] . "' ";
	}
	$sql = "select * from  " . $GLOBALS ['ecs']->table ( 'coupons' ) . " as c " . $where . " order by c.id DESC";
	$res = $GLOBALS ['db']->getAll ( $sql );
	
	
	return $res;
}


/**
 * 会员提货券管理列表
 */
//http://www.huaying.ccc/admin/coupons_order.php?act=query&orderid=2015091502740&user_name=9990111942832469&page=1&page_size=150&record_count=2&page_count=1
function coupons_order_list() {
	$result = get_filter ();
	
	if ($result === false) {
		// 商家id
		$filter ['orderid'] = empty ( $_REQUEST ['orderid'] ) ? 0 : trim ( $_REQUEST ['orderid'] );
		$filter ['user_name'] = empty ( $_REQUEST ['user_name'] ) ? '' : trim( $_REQUEST ['user_name'] );
		$where = 'WHERE 1 ';
		
		if ($filter ['orderid'] > 0) 
		{
			$where .= " AND c.orderid = " . $filter ['orderid'];
		}
		if (!empty($filter ['user_name'])) 
		{
			$where .= " AND c.user_name = " . $filter ['user_name'];
		}
		
		/*
		 * echo $_COOKIE ['ECSCP'] ['page_size']; die;
		 */
		/* 分页大小 */
		$filter ['page'] = empty ( $_REQUEST ['page'] ) || (intval ( $_REQUEST ['page'] ) <= 0) ? 1 : intval ( $_REQUEST ['page'] );
		
		if (isset ( $_REQUEST ['page_size'] ) && intval ( $_REQUEST ['page_size'] ) > 0) {
			$filter ['page_size'] = intval ( $_REQUEST ['page_size'] );
		} elseif (isset ( $_COOKIE ['ECSCP'] ['page_size'] ) && intval ( $_COOKIE ['ECSCP'] ['page_size'] ) > 0) {
			$filter ['page_size'] = intval ( $_COOKIE ['ECSCP'] ['page_size'] );
		} else {
			$filter ['page_size'] = 30; // 每页显示的数目
		}
		/* 记录总数 */
		if ($filter ['orderid']) {
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS ['ecs']->table ( 'coupons_order' ) . " AS c " . $where;
		} else {
			$sql = "SELECT COUNT(*) FROM " . $GLOBALS ['ecs']->table ( 'coupons_order' );
		}
		
		$filter ['record_count'] = $GLOBALS ['db']->getOne ( $sql );
		$filter ['page_count'] = $filter ['record_count'] > 0 ? ceil ( $filter ['record_count'] / $filter ['page_size'] ) : 1;
		
		/* 查询 */
		$sql = "SELECT c.* FROM " . $GLOBALS ['ecs']->table ( 'coupons_order' ) . " AS c "  . $where . " ORDER BY c.add_time DESC " . " LIMIT " . ($filter ['page'] - 1) * $filter ['page_size'] . "," . $filter ['page_size'];
		
		/*
		 *
		 */
	} else {
		$sql = $result ['sql'];
		$filter = $result ['filter'];
	}
	
	$conpons_order = $GLOBALS ['db']->getAll ( $sql );

	foreach ( $conpons_order as $key => $val ) {
		$coupons_list [$key] ['id'] = $val ['Id'];
		$coupons_list [$key] ['orderid'] = $val ['orderid'];
		$sql_goods_name1='SELECT * FROM '.$GLOBALS ['ecs']->table('goods')." WHERE goods_id = ".$val ['goods_id'];
		$sql_goods_name = $GLOBALS['db']->getRow($sql_goods_name1);
		$coupons_list [$key] ['goods_name'] = $sql_goods_name ['goods_name'];//产品名
 	  	$coupons_list [$key] ['goods_id'] = $val ['goods_id'];
		$coupons_list [$key] ['userid'] = $val ['userid'];
		$coupons_list [$key] ['user_name'] = $val ['user_name'];
		$coupons_list [$key] ['mobile'] = $val ['mobile'];
		$guige=guigeSms($val ['orderid']);
		$coupons_list [$key] ['coupons_youxiao'] ='';
		@$coupons_list [$key] ['coupons_youxiao'] =$guige['tihuo']."（有效期：".$guige['end_time']."）" ;//提货券（有效期）	
		$coupons_list [$key] ['unit_price'] = $val ['unit_price'];
		$coupons_list [$key] ['number'] = $val ['number'];
		$coupons_list [$key] ['total_price'] = $val ['total_price'];
		$coupons_list [$key] ['market_price'] = $val ['market_price'];
		$coupons_list [$key] ['add_time'] = local_date ( "Y-m-d H:i:s", $val ['add_time'] );
		
		if ($val ['order_state'] == 1) {
			$coupons_list [$key] ['order_state'] = "已付款";

		} elseif($val ['order_state'] == 2) {
			$coupons_list [$key] ['order_state'] = "失效";
			$coupons_list [$key] ['coupons_youxiao'] ='';
			
		} elseif($val ['order_state'] == 0) {
			$coupons_list [$key] ['order_state'] = "未付款";
			
		} else {
			$coupons_list [$key] ['order_state'] = "错误";
			
		}
	}
	$sqlx = "SELECT sum(c.order_state=1) as count FROM " . $GLOBALS ['ecs']->table ( 'coupons_order' ) . " AS c ".$where;
	$sqly = "SELECT sum(c.order_state=0) as count FROM " . $GLOBALS ['ecs']->table ( 'coupons_order' ) . " AS c ".$where;
	$sqlz = "SELECT sum(c.order_state=2) as count FROM " . $GLOBALS ['ecs']->table ( 'coupons_order' ) . " AS c ".$where;
	$x = $GLOBALS ['db']->getOne ( $sqlx );
	$y = $GLOBALS ['db']->getOne ( $sqly );
	$z = $GLOBALS ['db']->getOne ( $sqlz );
	
	$arr = array (
			'list' => $coupons_list,
			'filter' => $filter,
			'page_count' => $filter ['page_count'],
			'record_count' => $filter ['record_count'],
			'x' => $x,
			'y' => $y,
			'z' => $z 
	);

	return $arr;
}
