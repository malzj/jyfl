<?php
define ( 'IN_ECS', true );

require (dirname ( __FILE__ ) . '/includes/init.php');


/* ------------------------------------------------------ */
// -- 提货券列表
/* ------------------------------------------------------ */

if ($_REQUEST ['act'] == 'list') {
	$smarty->assign ( 'ur_here', '提货券列表' );
	$smarty->assign ( 'full_page', 1 );
	$res_coupons = coupons_list ();
	$smarty->assign ( 'x', $res_coupons ['x'] ); // 已使用
	$smarty->assign ( 'y', $res_coupons ['y'] ); // 未使用
	$smarty->assign ( 'z', $res_coupons ['z'] ); // 已锁定
	$smarty->assign ( 'coupons_list', $res_coupons ['list'] );
	$smarty->assign ( 'filter', $res_coupons ['filter'] );
	$smarty->assign ( 'record_count', $res_coupons ['record_count'] );
	$smarty->assign ( 'page_count', $res_coupons ['page_count'] );
	
	/* 显示模板 */
	assign_query_info ();
	$smarty->display ( 'coupons.htm' );
}// 提货券分页
elseif ($_REQUEST ['act'] == 'query') {
	$res_coupons="";
	$res_coupons = coupons_list ();
	$smarty->assign ( 'x', $res_coupons ['x'] );
	$smarty->assign ( 'y', $res_coupons ['y'] );
	$smarty->assign ( 'z', $res_coupons ['z'] );
	$smarty->assign ( 'coupons_list', $res_coupons ['list'] );
	$smarty->assign ( 'filter', $res_coupons ['filter'] );
	$smarty->assign ( 'record_count', $res_coupons ['record_count'] );
	$smarty->assign ( 'page_count', $res_coupons ['page_count'] );
	//$sort_flag = sort_flag ( $res_coupons ['filter'] );
	// $smarty->assign($sort_flag['tag'], $sort_flag['img']);
	make_json_result ( $smarty->fetch ( 'coupons.htm' ), '', array (
			'filter' => $res_coupons ['filter'],
			'page_count' => $res_coupons ['page_count'] 
	) );
}

/**
 * 提货券列表
 */
function coupons_list() {
	$result = get_filter ();
	if ($result === false) {
		// 商家id
		$res_supplier=$GLOBALS['db']->getRow("select * from ".$GLOBALS ['ecs']->table ( 'supplier' )." where supplier_name like '".$_REQUEST ['supplier_name']."%'");
		$filter ['supplier_id'] = empty($_REQUEST ['supplier_name']) ? '': trim($res_supplier['supplier_id']);
		$filter ['coupons_card'] = empty($_REQUEST ['coupons_card']) ? '' : trim( $_REQUEST['coupons_card'] );
		$where = 'WHERE 1 ';
		
		if (!empty($filter ['supplier_id'])) 
		{
			$where .= " AND c.supplier_id = " . $filter ['supplier_id'];
		}
		if (!empty($filter ['coupons_card'])) 
		{
			$where .= " AND c.coupons_card = '" . $filter ['coupons_card']."'";
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
		$sql = "SELECT c.*,s.supplier_name,g.goods_name" . " FROM " . $GLOBALS ['ecs']->table ( 'coupons' ) . " AS c " . " LEFT JOIN " . $GLOBALS ['ecs']->table ( 'supplier' ) . " AS s ON s.supplier_id=c.supplier_id "  . " LEFT JOIN " . $GLOBALS ['ecs']->table ( 'goods' ) . " AS g ON g.goods_id=c.goods_id " . $where . " ORDER BY c.id DESC " . " LIMIT " . ($filter ['page'] - 1) * $filter ['page_size'] . "," . $filter ['page_size'];
		
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
		$coupons_list [$key] ['goods_id'] = $val ['goods_id'];
		$coupons_list [$key] ['goods_name'] = $val ['goods_name'];
		$coupons_list [$key] ['coupons_card'] = $val ['coupons_card'];
		$coupons_list [$key] ['coupons_number'] = $val ['coupons_number'];
		$coupons_list [$key] ['supplier_id'] = $val ['supplier_id'];
		$coupons_list [$key] ['price'] = $val ['price'];
		$coupons_list [$key] ['start_time'] = local_date ( "Y-m-d H:i:s", $val ['start_time'] );
		$coupons_list [$key] ['end_time'] = local_date ( "Y-m-d H:i:s", $val ['end_time'] );
		$coupons_list [$key] ['supplier_name'] = $val ['supplier_name'];
		if ($val ['coupons_state'] == 1) {
			$coupons_list [$key] ['coupons_state'] = "已使用";
		} elseif($val ['coupons_state'] == 2) {
			$coupons_list [$key] ['coupons_state'] = "已锁定";
		} else {
			$coupons_list [$key] ['coupons_state'] = "未使用";
		}
	}
	$sqlx = "SELECT sum(c.coupons_state=1) as count FROM " . $GLOBALS ['ecs']->table ( 'coupons' ) . " AS c ".$where;
	$sqly = "SELECT sum(c.coupons_state=0) as count FROM " . $GLOBALS ['ecs']->table ( 'coupons' ) . " AS c ".$where;
	$sqlz = "SELECT sum(c.coupons_state=2) as count FROM " . $GLOBALS ['ecs']->table ( 'coupons' ) . " AS c ".$where;
	$x = $GLOBALS ['db']->getOne ( $sqlx );
	$y = $GLOBALS ['db']->getOne ( $sqly );
	$z = $GLOBALS ['db']->getOne ( $sqlz );
	$arr="";
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
// 提货券列表导出
if ($_REQUEST ['act'] == 'coupons_excel_out') {
	$smarty->assign ( 'ur_here', '提货券列表导出' );
	$smarty->assign ( 'act', $_REQUEST ['act'] );
	$smarty->display ( 'coupons_excel.htm' );
}
// 提货券列表导入
if ($_REQUEST ['act'] == 'coupons_excel_in') {
	$sql_supplier = "select * from  " .$GLOBALS ['ecs']->table ( 'supplier' );
	$supplier_arr = $GLOBALS ['db']->getAll($sql_supplier);
	$sql_goods = "select goods_id,goods_name from  " .$GLOBALS ['ecs']->table ( 'goods' ).' where cat_id=91'." order by goods_id desc";
	$goods_arr = $GLOBALS ['db']->getAll($sql_goods);
// echo "<pre>";
// print_r($goods_arr);
// echo "</pre>";
// die;
	$smarty->assign ( 'goods_arr', $goods_arr );
	$smarty->assign ( 'supplier_arr', $supplier_arr );
	$smarty->assign ( 'ur_here', '提货券列表导入' );
	$smarty->assign ( 'act', $_REQUEST ['act'] );
	$smarty->display ( 'coupons_excel.htm' );
}

// 提货券列表处理数据导入
elseif ($_REQUEST ['act'] == 'coupons_in'){
	if($_FILES['coupons_excel']['type']=='application/octet-stream'||$_FILES['coupons_excel']['type']=='application/vnd.ms-excel'){
	    $filename = $_FILES['coupons_excel']['name'];
	    $tmp_name = $_FILES['coupons_excel']['tmp_name'];
	    $filename = "coupons-".local_date("YmdHis",time()).".xlsx";
	    $msg = uploadFile($filename,$tmp_name);
	    //echo $msg;
	}else{
		echo "文件格式错误或者过大！请上传excel2007的*.xlsx格式文件";die;
	}
	//echo $msg;die;
	require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes/lib_autoExcels.php');
	$PHPExcel = new autoExcels('Excel2007');		//实例化类并传入导出格式（可以不传，默认是2007）
	$PHPExcel->setSaveName($msg);		//要保存的文件名（必须）
	$colsTitle = array('0'=>array('A'=>'coupons_card','B'=>'coupons_number'));
	$PHPExcel->setColsTitle($colsTitle);	
	@$list = $PHPExcel->execExcel('import');
	$list=$list[0];
	unset($list[0]);
// echo "<pre>";
// print_r($list);
// echo "</pre>";
// die;
	foreach ($list as $a => $b) {
		$array[$a]['coupons_card']=trim($b['coupons_card']);
		$array[$a]['coupons_number']=trim($b['coupons_number']);
		$array[$a]['goods_id']=$_REQUEST['goods_id'];
		$array[$a]['supplier_id']=$_REQUEST['supplier_id'];
		$array[$a]['price']=$_REQUEST['price']? trim($_REQUEST['price']):'0.0';
		$array[$a]['start_time']=$_REQUEST['start_time']? local_strtotime(trim($_REQUEST['start_time'])):gmtime();
		$array[$a]['end_time']=$_REQUEST['end_time']?local_strtotime(trim($_REQUEST['end_time'])):gmtime()+86400;
		$array[$a]['coupons_state']=0;
		$sql_select="select id from ".$GLOBALS ['ecs']->table ('coupons')."where coupons_card='".$array[$a]['coupons_card']."'";
		$sel=$GLOBALS ['db']->getOne($sql_select);
		if($sel||empty($array[$a]['coupons_card'])){
			echo "卡号不能为空或不要重复插入记录！<br/>";
			breck;
		}else{
			$sql ="insert INTO ".$GLOBALS ['ecs']->table ('coupons')."(coupons_card,coupons_number,goods_id,supplier_id,price,start_time,end_time,coupons_state) VALUES('".$array[$a]['coupons_card']."','".$array[$a]['coupons_number']."','".$array[$a]['goods_id']."','".$array[$a]['supplier_id']."','".$array[$a]['price']."','".$array[$a]['start_time']."','".$array[$a]['end_time']."','".$array[$a]['coupons_state']."');";
		
			$res[$a]=$GLOBALS ['db']->query($sql);
			if($res[$a]==1){
				echo "记录{$a}插入成功！<br/>";
				//删除上传的文件
				$del=delDir(dirname(dirname ( __FILE__ )) .'/temp/upload/');

			}else{
				echo "记录{$a}执行失败！<br/>";
				//删除上传的文件
				$del=delDir(dirname(dirname ( __FILE__ )) .'/temp/upload/');
			}
			
		}
	}
	$del=delDir(dirname(dirname ( __FILE__ )) .'/temp/upload/');
	header("refresh:3;url=/admin/coupons.php?act=list\n");
	//echo "<script>history.go(-1);</script>";
}
// 提货券列表处理数据导出
elseif($_REQUEST ['act'] == 'coupons_out'){
	// 选择的时间
	$supplier_id = empty ( $_REQUEST ['supplier_id'] ) ? '' : trim( $_REQUEST ['supplier_id'] );
	$start_time = empty ( $_REQUEST ['start_time'] ) ? '' : (strpos ( $_REQUEST ['start_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['start_time'] ) : $_REQUEST ['start_time']);
	$end_time = empty ( $_REQUEST ['end_time'] ) ? '' : (strpos ( $_REQUEST ['end_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['end_time'] ) : $_REQUEST ['end_time']);
	$filter ["start_time"] = $start_time;
	$filter ["end_time"] = $end_time;
	$filter ["supplier_id"] = $supplier_id;
	$data = coupons_excel( $filter );
	$row = 2;
	foreach ( $data as $key => $val ) {
		if($val['coupons_state']==0){
			$state[$key]="未使用";
		}elseif($val['coupons_state']==1){
			$state[$key]="已使用";
		}elseif($val['coupons_state']==2){
			$state[$key]="已锁定";
		}else{
			$state[$key]="错误";
		}
		
		// 删除不存在的数据
		if (! isset ( $key ) && empty ( $key )) {
			continue;
		}
		$exportInfo [] = array (
				'A' . $row => ' ' . $val ['id'],
				'B' . $row => ' ' . $val ['coupons_card'],
				'C' . $row => ' ' . $val ['coupons_number'],
				'D' . $row => ' ' . $val ['supplier_name'],
				'E' . $row => ' ' . $val ['price'],
				'F' . $row => local_date ( "Y-m-d H:i:s", $val ['start_time'] ), 
				'G' . $row => local_date ( "Y-m-d H:i:s", $val ['end_time'] ), 
				'H' . $row => ' ' . $state[$key]
		);
		
		$row ++;
	}
	//var_dump($exportInfo);die;
	// 导入excel类
	require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes/lib_autoExcels.php');
	$autoExcels1 = new autoExcels ( 'Excel2007' );
	$fileNmae = 'coupons-' . local_date ( 'mdHis', time () ) . '.xlsx';
	$autoExcels1->setSaveName ( $fileNmae );
	$exportContent = array (
			array (
					'sheetName' => '提货券列表',
					'title' => array (
							'A1' => '序号',
							'B1' => '提货券的卡号',
							'C1' => '提货券的密码',
							'D1' => '供应商的id', 
							'E1' => '提货券的价格',
							'F1' => '开始时间',
							'G1' => '结束时间', 
							'H1' => '状态' 
					),
					'content' => $exportInfo,
					'widths' => array (
							'A' => '8',
							'B' => '30',
							'C' => '30',
							'D' => '20',
							'E' => '30',
							'F' => '20',
							'G' => '20', 
							'H' => '20' 
					) 
			) 
	);
// echo "<pre>";
// print_r($exportContent);
// echo "</pre>";
// die;
	$autoExcels1->setTitle ( $exportContent );
	ini_set ( "memory_limit", '180M' );
	// $colRow = $autoExcels->getColsFormat('A','B');
	// $autoExcels->PHPExcel()->getSheet(0)->getStyle($colRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	$autoExcels1->execExcel('export');
}
// 提货券读取数据格式化
function coupons_excel($filter) {
	$where = ' WHERE 1 ';
	if($filter['supplier_id']!=""){
		$sql1 = "select * from  " . $GLOBALS ['ecs']->table ( 'supplier' ) . " as s" ." where s.supplier_name like '%".$filter['supplier_id']."%'";
		$res1 = $GLOBALS ['db']->getRow ( $sql1 );		
		$where.=" and c.supplier_id='".$res1['supplier_id']."'";
	}
	if ($filter ['start_time'] != '' && $filter ['end_time'] != '') {
		$where .= " and c.start_time >= '" . $filter ['start_time'] . "' and c.end_time <= '" . $filter ['end_time'] . "' ";
	}
	$sql = "select c.*,s.supplier_name from  " . $GLOBALS ['ecs']->table ( 'coupons' ) . " as c " .'LEFT JOIN ' . $GLOBALS['ecs']->table('supplier') . ' AS s ' ." ON c.supplier_id = s.supplier_id " . $where . " order by c.id DESC";
	$res = $GLOBALS ['db']->getAll ( $sql );
	return $res;
}
