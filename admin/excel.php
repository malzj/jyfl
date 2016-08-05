<?php
define ( 'IN_ECS', true );

require (dirname ( __FILE__ ) . '/includes/init.php');
require_once (ROOT_PATH . 'includes/lib_goods.php');
// 导入excel类
// require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR .
// 'includes/lib_autoExcels.php');

if ($_REQUEST ['act'] == 'order_excel') {
	require dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'supplier.php';
	$suppliers = suppliers_list ( 1 );
	$supplier = array ();
	if (! empty ( $suppliers ['result'] )) {
		$supplier = $suppliers ['result'];
		foreach ( array (
				'10000' => '在线选座',
				'10001' => '电子兑换券',
				'10002' => '演出订单',
		        '10003' => '动网订单',
				'10004' => '票工厂订单' 
		) as $key => $val ) {
			$supplier [] = array (
					'supplier_id' => $key,
					'user_name' => $val 
			);
		}
	}
	$smarty->assign ( 'ur_here', '经销商订单导出' );	
	$smarty->assign ( 'act', $_REQUEST ['act'] );
	$smarty->assign ( 'supplier', $supplier );
	$smarty->display ( 'order_excel.htm' );
}
// 卡合并列表导出
if ($_REQUEST ['act'] == 'back_list') {
	$smarty->assign ( 'ur_here', '退换货列表导出' );
	$smarty->assign ( 'act', $_REQUEST ['act'] );
	$smarty->display ( 'order_excel.htm' );
}
// 卡合并列表导出
if ($_REQUEST ['act'] == 'card_excel_out') {
	$smarty->assign ( 'ur_here', '卡合并列表导出' );
	$smarty->display ( 'card_excel.htm' );
}
// 会员充值列表导出
if ($_REQUEST ['act'] == 'chongzhi_execl') {
	$smarty->assign ( 'ur_here', '会员充值列表导出' );
	$smarty->display ( 'chongzhi_execl.htm' );
}
if ($_REQUEST ['act'] == 'filmorder_excel') {
	$smarty->display ( 'filmorder_excel.htm' );
}
if ($_REQUEST ['act'] == 'ticketorder_excel') {
	$smarty->display ( 'ticketorder_excel.htm' );
}
if ($_REQUEST ['act'] == 'yanchuorder_excel') {
	$smarty->display ( 'yanchuorder_excel.htm' );
}

// 导出退换货列表
elseif ($_REQUEST ['act'] == 'doback_list') {
	// 选择的时间
	$start_time = empty ( $_REQUEST ['start_time'] ) ? '' : (strpos ( $_REQUEST ['start_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['start_time'] ) : $_REQUEST ['start_time']);
	$end_time = empty ( $_REQUEST ['end_time'] ) ? '' : (strpos ( $_REQUEST ['end_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['end_time'] ) : $_REQUEST ['end_time']);
	$filter ["start_time"] = $start_time;
	$filter ["end_time"] = $end_time;
	$data = back_list ( $filter );

	$row = 2;
	foreach ( $data as $key => $val ) {
		// 删除不存在的数据
		if (! isset ( $key ) && empty ( $key )) {
			continue;
		}
		$exportInfo [] = array (
				'A' . $row => ' ' . $val ['return_time'],
				'B' . $row => ' ' . $val ['order_sn'],
				'C' . $row => ' ' . $val ['user_name'],
				'D' . $row => ' ' . $val ['supplier_name'],
				'E' . $row => ' ' . $val ['postscript'],
				'F' . $row => $val ['refund_money'],
				'G' . $row => ' ' . $val ['action_user'],
				'H' . $row => '线上' 
		);
		
		$row ++;
	}
	// 导入excel类
	require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes/lib_autoExcels.php');
	$autoExcels = new autoExcels ( 'Excel2007' );
	$fileNmae = 'back_list-' . date ( 'mdHis', time () ) . '.xlsx';
	$autoExcels->setSaveName ( $fileNmae );
	$exportContent = array (
			array (
					'sheetName' => '退换货列表',
					'title' => array (
							'A1' => '退换货日期',
							'B1' => '订单号',
							'C1' => '卡号',
							'D1' => '供应商',
							'E1' => '退单原因' ,
							'F1' => '金额（点）',
							'G1' => '操作人',
							'H1' => '退款类型'
					),
					'content' => $exportInfo,
					'widths' => array (
							'A' => '20',
							'B' => '20',
							'C' => '20',
							'D' => '10',
							'E' => '30',
							'F' => '15',
							'G' => '10',							
							'H' => '10' 
					) 
			) 
	);
	$autoExcels->setTitle ( $exportContent );
	ini_set ( "memory_limit", '180M' );
	// $colRow = $autoExcels->getColsFormat('A','B');
	// $autoExcels->PHPExcel()->getSheet(0)->getStyle($colRow)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
	$autoExcels->execExcel ( 'export' );
} 
// 导出卡合并列表处理数据
elseif ($_REQUEST ['act'] == 'card') {
	// 选择的时间
	$start_time = empty ( $_REQUEST ['start_time'] ) ? '' : (strpos ( $_REQUEST ['start_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['start_time'] ) : $_REQUEST ['start_time']);
	$end_time = empty ( $_REQUEST ['end_time'] ) ? '' : (strpos ( $_REQUEST ['end_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['end_time'] ) : $_REQUEST ['end_time']);
	$filter ["start_time"] = $start_time;
	$filter ["end_time"] = $end_time;
	$data = card_excel ( $filter );
	$row = 2;
	foreach ( $data as $key => $val ) {
		// 删除不存在的数据
		if (! isset ( $key ) && empty ( $key )) {
			continue;
		}
		$exportInfo [] = array (
				'A' . $row => ' ' . $val ['log_id'],
				'B' . $row => ' ' . $val ['card_from'],
				'C' . $row => ' ' . $val ['card_to'],
				'D' . $row => ' ' . $val ['card_money'],
				'E' . $row => local_date ( $GLOBALS ['_CFG'] ['time_format'], $val ['pay_time'] ),
				'F' . $row => ' ' . $val['message']
		);
		
		$row ++;
	}
	// 导入excel类
	require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes/lib_autoExcels.php');
	$autoExcels = new autoExcels ( 'Excel2007' );
	$fileNmae = 'card-' . date ( 'mdHis', time () ) . '.xlsx';
	$autoExcels->setSaveName ( $fileNmae );
	$exportContent = array (
			array (
					'sheetName' => '卡合并列表',
					'title' => array (
							'A1' => '编号',
							'B1' => '合并来源卡',
							'C1' => '合并去向卡',
							'D1' => '金额',
							'E1' => '合并时间',
							'F1' => '消息'
					),
					'content' => $exportInfo,
					'widths' => array (
							'A' => '8',
							'B' => '30',
							'C' => '30',
							'D' => '10',
							'E' => '20',
							'F' => '80'
					) 
			) 
	);
	$autoExcels->setTitle ( $exportContent );
	ini_set ( "memory_limit", '180M' );	
	$autoExcels->execExcel ( 'export' );
} 
// 导出会员充值列表处理数据
elseif ($_REQUEST ['act'] == 'chongzhi') {
	// 选择的时间
	$start_time = empty ( $_REQUEST ['start_time'] ) ? '' : (strpos ( $_REQUEST ['start_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['start_time'] ) : $_REQUEST ['start_time']);
	$end_time = empty ( $_REQUEST ['end_time'] ) ? '' : (strpos ( $_REQUEST ['end_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['end_time'] ) : $_REQUEST ['end_time']);
	$filter ["start_time"] = $start_time;
	$filter ["end_time"] = $end_time;
	$data = chongzhi_excel ( $filter );
	$row = 2;
	foreach ( $data as $key => $val ) {
		// 删除不存在的数据
		if (! isset ( $key ) && empty ( $key )) {
			continue;
		}
		$val ['is_paid'] = $val ['is_paid'] ? '已完成' : '未确认';
		$val ['process_type'] = $val ['process_type'] ? '退款' : '充值';
		$exportInfo [] = array (
				'A' . $row => ' ' . $val ['id'],
				'B' . $row => ' ' . $val ['user_name'],
				'C' . $row => local_date ( $GLOBALS ['_CFG'] ['time_format'], $val ['add_time'] ),
				'D' . $row => ' ' . $val ['process_type'],
				'E' . $row => ' ' . $val ['amount'],
				'F' . $row => ' ' . $val ['payment'],
				'G' . $row => ' ' . $val ['is_paid'],
				'H' . $row => ' ' . $val ['pay_no']
		);
		
		$row ++;
	}
	
	// 导入excel类
	require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes/lib_autoExcels.php');
	$autoExcels = new autoExcels ( 'Excel2007' );
	$fileNmae = 'chongzhi-' . date ( 'mdHis', time () ) . '.xlsx';
	$autoExcels->setSaveName ( $fileNmae );
	$exportContent = array (
			array (
					'sheetName' => '会员充值列表',
					'title' => array (
							'A1' => '编号',
							'B1' => '会员名称',
							'C1' => '操作日期',
							'D1' => '类型',
							'E1' => '金额',
							'F1' => '支付方式',
							'G1' => '到款状态', 
							'H1' => '支付宝订单号'
					),
					'content' => $exportInfo,
					'widths' => array (
							'A' => '10',
							'B' => '20',
							'C' => '20',
							'D' => '10',
							'E' => '15',
							'F' => '10',
							'G' => '10',
							'H' => '35'
					) 
			) 
	);
	$autoExcels->setTitle ( $exportContent );
	ini_set ( "memory_limit", '180M' );
	$autoExcels->execExcel ( 'export' );
} 

elseif ($_REQUEST ['act'] == 'order') {
	// $stime=microtime(true); //获取程序开始执行的时间
	set_time_limit(0);
	ini_set ( "memory_limit", '512M' );
	// 订单状态
	$order_status = isset ( $_REQUEST ['order_status'] ) ? intval ( $_REQUEST ['order_status'] ) : - 1;
	// 供货商id
	$supplier_id = isset ( $_REQUEST ['supplier_id'] ) ? intval ( $_REQUEST ['supplier_id'] ) : 0;
	// 订单时间
	$start_time = empty ( $_REQUEST ['start_time'] ) ? '' : (strpos ( $_REQUEST ['start_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['start_time'] ) : $_REQUEST ['start_time']);
	$end_time = empty ( $_REQUEST ['end_time'] ) ? '' : (strpos ( $_REQUEST ['end_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['end_time'] ) : $_REQUEST ['end_time']);
	
	// 供应商列表
	$supplier_ids = $filter_supplier = array ();
	require dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'supplier.php';
	$suppliers = suppliers_list ();
	if (! empty ( $suppliers ['result'] )) {
		foreach ( $suppliers ['result'] as $supplier ) {
			$supplier_ids [$supplier ['supplier_id']] = $supplier ['user_name'];
			if ($supplier_id == $supplier ['supplier_id']) {
				$filter_supplier [] = $supplier;
			}
		}
	}
	// 如果没有选择供应商，就得到所有供应的信息
	if ($supplier_id == 0) {
		$filter_supplier = $suppliers ['result'];
	}
	// 组合条件语句
	$filter = array (
			'supplier_id' => $supplier_id,
			'start_time' => $start_time,
			'end_time' => $end_time,
			'order_status' => $order_status,
			'suppliers' => $filter_supplier 
	);
	// 数据集合
	$order_list = array ();
	
	// 如果是供应商的话，根据供应商导出信息
	if ($supplier_id > 0 && array_key_exists ( $supplier_id, $supplier_ids )) {
		$order_list ['suppliers_order'] = suppliers_order ( $filter );
	}
	// 电影票
	if ($supplier_id === 10000) {
		$order_list ['movie_orders'] = movie_orders ( $filter );
	}
	// 电子兑换券
	if ($supplier_id === 10001) {
		$order_list ['ticket_orders'] = ticket_orders ( $filter );
	}
	// 演出订单
	if ($supplier_id === 10002) {
		$order_list ['yanchu_orders'] = yanchu_orders ( $filter );
	}
	// 票工厂订单
	if ($supplier_id === 10004) {
		$order_list ['piaoduoduo_orders'] = piaoduoduo_orders( $filter );
		
	}
	// 动网订单
	if ($supplier_id === 10003) {
	    $order_list ['venues_orders'] = venues_orders( $filter );
	
	}
	// 没选中供应商，导出所有的供应商订单，和接口订单
	if ($supplier_id === 0) {
		foreach ( array ('suppliers_order',	'movie_orders',	'ticket_orders','yanchu_orders' ,'piaoduoduo_orders','venues_orders') as $fun ) {
			$order_list [$fun] = $fun ( $filter );
		}
	}
	
	/*
	 * echo '<pre>'; print_r($order_list); echo '</pre>'; exit;
	 */
	// 所有数据
	$exportInfo = array ();
	$row = 2; // 从第二行开始是数据，第一行是标题
	foreach ( array ('suppliers_order',	'movie_orders',	'ticket_orders','yanchu_orders' ,'piaoduoduo_orders','venues_orders') as $key ) {
		// 删除不存在的供应商数据
		if (! isset ( $order_list [$key] ) && empty ( $order_list [$key] )) {
			continue;
		}
		// 经销商订单
		if ($key == 'suppliers_order') {
			foreach ( $order_list [$key] as $val ) {
				$g = 0;
				foreach ( $val ['goods'] as $goods ) {
					if ($g == 0) {
						$shipping_fee = $val ['shipping_fee'];
					} else {
						$shipping_fee = 0;
					}
					$exportInfo[] = array(
					    'A' . $row => ' ' . $val ['order_sn'],
					    'B' . $row => ' ' . $val ['user_name'],
					    'C' . $row => $val ['consignee'],
					    'D' . $row => $val ['address'],
					    'E' . $row => $val ['tel'],
					    'F' . $row => local_date ( 'Y', $val ['add_time'] ),
					    'G' . $row => local_date ( 'm', $val ['add_time'] ),
					    'H' . $row => local_date ( 'd', $val ['add_time'] ),
					    'I' . $row => local_date ( 'H:i', $val ['add_time'] ),
					    'J' . $row => $goods ['goods_name'],
					    'K' . $row => $goods ['goods_price'],
					    'L' . $row => $goods ['goods_number'],
					    'M' . $row => $goods ['money'],
					    	
					    'N' . $row => $val ['card_ratio'], // 卡规则比例
					    'O' . $row => $val ['shop_ratio'], // 商城售比
					    	
					    'P' . $row => $val ['raise'],
					    'Q' . $row => $val ['unit_ratio'],
					    'R' . $row => $goods ['market_cost_price'],					    	
					    'S' . $row => $goods ['spec_price'],
					    'T' . $row => $goods ['cost_price'],
					    'U' . $row => $goods ['cost_ratio'],
					    
					    'V' . $row => $val ['order_status'],
					    'W' . $row => $supplier_ids [$val ['supplier_id']],
					    'X' . $row => ' '.$shipping_fee,
					    'Y' . $row => ' '.$val['order_action'],
					    'Z' . $row => $val['ext'] == 1 ? "1.19" : '0.97',
					    'AA'. $row => ' '.$val['invoice_no']
					    
					    
					);					
					$g ++;
					$row ++;
				}
			}
		}
		// 电影票订单
		if ($key == 'movie_orders') {
			foreach ( $order_list [$key] as $val ) {
				$exportInfo [] = array (
				    'A' . $row => ' ' . $val ['order_sn'],
				    'B' . $row => ' ' . $val ['user_name'],
				    'C' . $row => ' ',
				    'D' . $row => ' ',
				    'E' . $row => $val ['mobile'],
				    'F' . $row => local_date ( 'Y', $val ['add_time'] ),
				    'G' . $row => local_date ( 'm', $val ['add_time'] ),
				    'H' . $row => local_date ( 'd', $val ['add_time'] ),
				    'I' . $row => local_date ( 'H:i', $val ['add_time'] ),
				    'J' . $row => $val ['movie_name'],
					'K' . $row => $val ['price'],
					'L' . $row => $val ['number'],
					'M' . $row => $val ['goods_amount'],
				    
				    'N' . $row => $val ['card_ratio'], // 卡规则比例
				    'O' . $row => $val ['shop_ratio'], // 商城售比
				    
				    'P' . $row => $val ['raise'],
				    'Q' . $row => 1,
				    'R' . $row => $val ['market_cost_price'],
				    'S' . $row => 0,
				    'T' . $row => $val ['cost_price'],
				    'U' . $row => $val ['cost_ratio'],
				    	
				    'V' . $row => $val ['order_status_cn'],
				    'W' . $row => '在线选座',
				    'X' . $row => ' ',
				    'Y' . $row => ' ',
				    'Z' . $row => $val['ext'] == 1 ? "1.19" : '0.97',
				    'AA'. $row => ' '
				);
				$row ++;
			}
		}
		// 电子兑换券
		if ($key == 'ticket_orders') {
			foreach ( $order_list [$key] as $val ) {
				$exportInfo [] = array (
    				    'A' . $row => ' ' . $val ['order_sn'],
    				    'B' . $row => ' ' . $val ['user_name'],
    				    'C' . $row => ' ',
    				    'D' . $row => ' ',
    				    'E' . $row => $val ['mobile'],
    				    'F' . $row => local_date ( 'Y', $val ['add_time'] ),
    				    'G' . $row => local_date ( 'm', $val ['add_time'] ),
    				    'H' . $row => local_date ( 'd', $val ['add_time'] ),
    				    'I' . $row => local_date ( 'H:i', $val ['add_time'] ),
    				    'J' . $row => $val ['movie_name'],
    				    'K' . $row => $val ['sjprice'],
    				    'L' . $row => $val ['number'],
    				    'M' . $row => $val ['goods_amount'],
    				    
    				    'N' . $row => $val ['card_ratio'], // 卡规则比例
    				    'O' . $row => $val ['shop_ratio'], // 商城售比
    				    
    				    'P' . $row => $val ['raise'],
    				    'Q' . $row => 1,
    				    'R' . $row => $val ['market_cost_price'],
    				    'S' . $row => 0,
    				    'T' . $row => ' ',//$val ['cost_price'],
    				    'U' . $row => ' ',//$val ['cost_ratio'],
    				     
    				    'V' . $row => $val ['order_status_cn'],
    				    'W' . $row => '电子券兑换',
    				    'X' . $row => ' ',
    				    'Y' . $row => ' ',
    				    'Z' . $row => $val['ext'] == 1 ? "1.19" : '0.97',
    				    'AA'. $row => ' '
				);
				$row ++;
			}
		}
		
		// 演出订单
		if ($key == 'yanchu_orders') {
			foreach ( $order_list [$key] as $val ) {
				$exportInfo [] = array (
    				    'A' . $row => ' ' . $val ['order_sn'],
    				    'B' . $row => ' ' . $val ['user_name'],
    				    'C' . $row => ' ' . $val ['consignee'],
    				    'D' . $row => ' ' . $val ['regionname'],
    				    'E' . $row => $val ['mobile'],
    				    'F' . $row => local_date ( 'Y', $val ['add_time'] ),
    				    'G' . $row => local_date ( 'm', $val ['add_time'] ),
    				    'H' . $row => local_date ( 'd', $val ['add_time'] ),
    				    'I' . $row => local_date ( 'H:i', $val ['add_time'] ),
    				    'J' . $row => $val ['itemname'],
						'K' . $row => $val ['price'],
						'L' . $row => $val ['number'],
						'M' . $row => $val ['goods_amount'],
    				    
    				    'N' . $row => $val ['card_ratio'], // 卡规则比例
    				    'O' . $row => $val ['shop_ratio'], // 商城售比
    				    
    				    'P' . $row => $val ['raise'],
    				    'Q' . $row => 1,
    				    'R' . $row => $val ['market_cost_price'],
    				    'S' . $row => 0,
    				    'T' . $row => ' ',//$val ['cost_price'],
    				    'U' . $row => ' ',//$val ['cost_ratio'],
    				     
    				    'V' . $row => $val ['order_status_cn'],
    				    'W' . $row => '演出订单',
    				    'X' . $row => $val ['shipping_fee'],
    				    'Y' . $row => ' ',
    				    'Z' . $row => $val['ext'] == 1 ? "1.19" : '0.97',
    				    'AA'. $row => ' '						
				);
				$row ++;
			}
		}
		// 场馆订单
		if ($key == 'venues_orders') {
		    foreach ( $order_list [$key] as $val ) {
		        $exportInfo [] = array (
				    'A' . $row => ' ' . $val ['order_sn'],
				    'B' . $row => ' ' . $val ['user_name'],
				    'C' . $row => ' ' . $val ['link_man'],
				    'D' . $row => ' ', //$val ['regionname'],
				    'E' . $row => $val ['link_phone'],
				    'F' . $row => date ( 'Y', $val ['add_time'] ),
				    'G' . $row => date ( 'm', $val ['add_time'] ),
				    'H' . $row => date ( 'd', $val ['add_time'] ),
				    'I' . $row => date ( 'H:i', $val ['add_time'] ),
				    'J' . $row => $val ['venueName'],
		            'K' . $row => $val ['unit_price'],
		            'L' . $row => $val ['total'],
		            'M' . $row => $val ['money'],

				    'N' . $row => $val ['card_ratio'], // 卡规则比例
				    'O' . $row => $val ['shop_ratio'], // 商城售比

				    'P' . $row => $val ['raise'],
				    'Q' . $row => 1,
				    'R' . $row => $val ['market_cost_price'],
				    'S' . $row => 0,
				    'T' . $row => ' ',//$val ['cost_price'],
				    'U' . $row => ' ',//$val ['cost_ratio'],
        	
				    'V' . $row => $val ['order_status_cn'],
				    'W' . $row => '场馆订单',
				    'X' . $row => ' ',//$val ['shipping_fee'],
				    'Y' . $row => ' ',
				    'Z' . $row => $val['ext'] == 1 ? "1.19" : '0.97',
				    'AA'. $row => ' '
		        );
		        $row ++;
		    }
		}
		// 票工厂订单
		if ($key == 'piaoduoduo_orders') {
			
			foreach ( $order_list [$key]['orders'] as $val ) {
				$exportInfo [] = array (
						'A' . $row => ' ' . $val ['order_sn'],
						'B' . $row => ' ' .$val ['api_order_sn'],
						'C' . $row => ' ' . $val ['user_name'],
						'D' . $row => ' ' . $val ['name'],
						'E' . $row => ' ' . $val ['card_num'],
						'F' . $row => ' ' .$val ['MobileNumberToGetEticket'],
						'G' . $row => ' ' .$val ['ProductName'],
						'H' . $row => ' ' .$val ['ListETicketCode'],
						'I' . $row => ' ' .$val ['TicketCategory'],
						'J' . $row => ' ' .$val ['SellPrice'],
						'K' . $row => ' ' .$val ['TotalTicketQuantity'],
						'L' . $row => ' ' .$val ['total_price'],
						'M' . $row => ' ' .$val ['SettlementPrice'],
						'N' . $row => ' ' .$val ['TripDate'],
						'O' . $row =>' ' .$val ['Status'],
						'P' . $row =>' ' . $val ['refund'],
						'Q' . $row => ' ' .$val ['tuipiao_state'],
						'R' . $row => ' ' . $val ['pay_time'] ,
						'S' . $row => '票工厂订单' 

				);
				$row ++;
			}
		}
	}
	// 导入excel类
	require (dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'includes/lib_autoExcels.php');
	$autoExcels = new autoExcels ( 'Excel2007' );
	$filename = 'ORDER-' . date ( 'mdHis', time () ) . '.xlsx';
	$autoExcels->setSaveName (iconv('utf-8', "gb2312", $filename) );
	
	$exportContent = array (
	    array (
	        'sheetName' => '当单信息',
	        'title' => array (
	            'A1' => '订单号',
	            'B1' => '卡号',
	            'C1' => '收货人',
	            'D1' => '地址',
	            'E1' => '联系电话',
	            'F1' => '年',
	            'G1' => '月',
	            'H1' => '日',
	            'I1' => '时间',
	            'J1' => '商品名称',
	            'K1' => '商城售点',
	            'L1' => '数量',
	            'M1' => '总售点',
	            	
	            'N1' => '卡规则比例',
	            'O1' => '商城售比',
	            	
	            'P1' => '浮比',
	            'Q1' => '单品比',
	            'R1' => '商品原价',
	            	
	            'S1' => '配件价格',
	            'T1' => '成本价',
	            'U1' => '成本比例',
	            'V1' => '订单状态',
	            'W1' => '供应商',
	            'X1' => '运费',
	            'Y1' => '备注',
	            'Z1' => '卡单价',
	            'AA1'=> '快递单号'
	        ),
	        
	        'widths' => array ( 'A' => '20',  'B' => '20', 'C' => '10',  'D' => '40', 'E' => '15', 'F' => '10',
	                            'G' => '7',   'H' => '7',  'I' => '7',   'J' => '40', 'K' => '13', 'L' => '13',
	                            'M' => '13',  'N' => '13', 'O' => '13',  'P' => '13', 'Q' => '13', 'R' => '13',
	                            'S' => '13',  'T' => '13', 'U' => '13',  'V' => '25', 'W' => '10', 'X' => '7',
	                            'Y' => '25',  'Z' => '20'
	        )
	    )
	);

	// 计数器
	$cnt = 0;
	// 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
	$limit = 10000;
	// 逐行取出数据，不浪费内存
	$count = count($exportInfo);
	for($t=0;$t<$count;$t++) {
	    $cnt ++;
	    if ($limit == $cnt) { //刷新一下输出buffer，防止由于数据过多造成问题
	        ob_flush();
	        flush();
	        $cnt = 0;
	        sleep(3);
	    }
	    $value='';
	    foreach ($exportInfo[$t] as $key => $value) {
	    	$exportContent[0]['content'][$t][$key] =$value;
	    }
	}
	$autoExcels->setTitle ( $exportContent );
	$autoExcels->execExcel ( 'export' );

} elseif ($_REQUEST ['act'] == 'filmorder') {
	$filename = 'filmorderexcel';
	header ( "Content-type: application/vnd.ms-excel; charset=gbk" );
	header ( "Content-Disposition: attachment; filename=$filename.xls" );
	
	$order_status = $_REQUEST ['order_status'];
	$start_time = empty ( $_REQUEST ['start_time'] ) ? '' : (strpos ( $_REQUEST ['start_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['start_time'] ) : $_REQUEST ['start_time']);
	$end_time = empty ( $_REQUEST ['end_time'] ) ? '' : (strpos ( $_REQUEST ['end_time'], '-' ) > 0 ? local_strtotime ( $_REQUEST ['end_time'] ) : $_REQUEST ['end_time']);
	$order_sn1 = $_REQUEST ['order_sn1'];
	$order_sn2 = $_REQUEST ['order_sn2'];
	
	$where = 'WHERE 1 ';
	// $where = "WHERE o.supplier_id='".$_SESSION['supplier_id']."' ";
	
	if ($order_status > - 1) {
		if ($order_status == 1) {
			$where .= " and o.order_status = 0";
		}
		if ($order_status == 2) {
			$where .= " and o.order_status = 1";
		}
		if ($order_status == 3) {
			$where .= " and o.pay_status = 0";
		}
		if ($order_status == 4) {
			$where .= " and o.pay_status = 2";
		}
		if ($order_status == 5) {
			$where .= " and o.shipping_status = 0";
		}
		if ($order_status == 6) {
			$where .= " and o.shipping_status = 2";
		}
	}
	
	if ($start_time != '' && $end_time != '') {
		$where .= " and o.add_time >= '$start_time' and o.add_time <= '$end_time' ";
	}
	
	if ($order_sn1 != '' && $order_sn2 != '') {
		$where .= " and o.order_sn >= '$order_sn1' and o.order_sn <= '$order_sn2' ";
	}
	
	$sql = "select o.*  from  " . $GLOBALS ['ecs']->table ( 'seat_order' ) . " as o   $where ";
	
	$res = $db->getAll ( $sql );
	// print_r($res);
	// exit;
	$list = array ();
	foreach ( $res as $key => $rows ) {
		$list [$rows ['order_sn']] ['order_sn'] = $rows ['order_sn'];
		$list [$rows ['order_sn']] ['user_name'] = $rows ['user_name'];
		$list [$rows ['order_sn']] ['mobile'] = $rows ['mobile'];
		$list [$rows ['order_sn']] ['FilmName'] = $rows ['FilmName'];
		$list [$rows ['order_sn']] ['CinemaName'] = $rows ['CinemaName'];
		$list [$rows ['order_sn']] ['SeatsName'] = $rows ['SeatsName'];
		$list [$rows ['order_sn']] ['number'] = $rows ['number'];
		$list [$rows ['order_sn']] ['price'] = $rows ['price'];
		$list [$rows ['order_sn']] ['goods_amount'] = $rows ['goods_amount'];
		$list [$rows ['order_sn']] ['dateline'] = local_date ( 'y-m-d H:i', $rows ['dateline'] );
		$list [$rows ['order_sn']] ['add_time'] = local_date ( 'y-m-d H:i', $rows ['add_time'] );
	}
	
	foreach ( $list as $key => $val ) {
		$data .= "<table border='1'>";
		$data .= "<tr><td>订单号</td><td>用户名</td><td>手机号</td><td>电影院名称</td><td>电影名称</td><td>座位</td><td>订单数量</td><td>单价</td><td>总价</td><td>观影时间</td><td>下单时间</td></tr>";
		$data .= "<tr><td>" . $val ['order_sn'] . "</td><td>" . $val ['user_name'] . "</td><td>" . $val ['mobile'] . "</td><td>" . $val ['FilmName'] . "</td><td>" . $val ['CinemaName'] . "</td><td>" . $val ['SeatsName'] . "</td><td>" . $val ['number'] . "</td><td>" . $val ['price'] . "</td><td>" . $val ['goods_amount'] . "</td><td>" . $val ['dateline'] . "</td><td>" . $val ['add_time'] . "</td></tr>";
		
		$data .= "</table>";
		$data .= "<br>";
	}
	
	echo $data . "\t";
}

// 供应商订单
function suppliers_order($filter) {
	$where = ' WHERE 1 ';
	if ($filter ['order_status'] > - 1) {
		if ($filter ['order_status'] == 5) {
			$where .= " and o.shipping_status = 0 AND o.pay_status = 2";
		} else {
			$where .= " and o.order_status = '" . $filter ['order_status'] . "'";
		}
	}
	
	if ($filter ['start_time'] != '' && $filter ['end_time'] != '') {
		$where .= " and o.add_time >= '" . $filter ['start_time'] . "' and o.add_time <= '" . $filter ['end_time'] . "' ";
	}
	
	if ($filter ['supplier_id'] > 0) {
		$where .= " and o.supplier_id = " . $filter ['supplier_id'];
	}
	
	$sql = "select o.*,g.goods_id,g.goods_name,g.goods_attr,g.goods_number,g.goods_attr_id,g.goods_sn,g.market_price,g.goods_price,g.order_id ,g.goods_number,g.goods_price*g.goods_number as money,u.user_name from  " . $GLOBALS ['ecs']->table ( 'order_info' ) . 
	" as o left join " . $GLOBALS ['ecs']->table ( 'users' ) . 	" as u on o.user_id=u.user_id " 
	. "left join  " . $GLOBALS ['ecs']->table ( 'order_goods' ) . " as g on o.order_id=g.order_id " 
	. $where . " order by o.add_time ASC";
	$res = $GLOBALS ['db']->getAll ( $sql );
	$list = array ();
	foreach ( $res as $key => $rows ) {
		$list [$rows ['order_sn']] ['order_sn'] = $rows ['order_sn'];
		$list [$rows ['order_sn']] ['user_name'] = $rows ['user_name'];
		$list [$rows ['order_sn']] ['consignee'] = $rows ['consignee'];
		$list [$rows ['order_sn']] ['tel'] = $rows ['mobile'] . ($rows ['tel'] ? '(' . $rows ['tel'] . ')' : '');
		$list [$rows ['order_sn']] ['address'] = $rows ['address'];
		$list [$rows ['order_sn']] ['add_time'] = $rows ['add_time'];
		$list [$rows ['order_sn']] ['shipping_fee'] = $rows ['shipping_fee'];
		$list [$rows ['order_sn']] ['supplier_id'] = $rows ['supplier_id'];
		$list [$rows ['order_sn']] ['ext'] = $rows ['ext'];
		$list [$rows ['order_sn']] ['raise'] = $rows ['raise'];
		$list [$rows ['order_sn']] ['unit_ratio'] = $rows ['unit_ratio'];
		$list [$rows ['order_sn']] ['card_ratio'] = $rows ['card_ratio'];
		$list [$rows ['order_sn']] ['shop_ratio'] = $rows ['shop_ratio'];
		// 订单状态
		$order_status = null;
		$o = $s = $p = $t = false;
		switch ($rows ['order_status']) {
			case '1' :
				$o = true;
				break;
			case '5' :
				$o = true;
				break;
			case '4' :
				$order_status = '退货';
				break;
			case '6' :
				$order_status = '部分发货';
				break;
			case '7' :
				$order_status = '部分退货';
				break;
		}
		// 发货状态
		switch ($rows ['shipping_status']) {
			case '1' :
				$s = true;
				break;
			case '2' :
				$s = true;
				$order_status = $order_status == null ? '收货确认' : $order_status;
				break;
		}
		// 付款状态
		switch ($rows ['pay_status']) {
			case '1' :
				$p = true;
				break;
			case '2' :
				$p = true;
				break;
			case '0' :
				$order_status = '未付款';
				break;
		}
		
		// 已完成状态
		if ($o == true && $p == true && $s == true) {
			$order_status = '已完成';
		}
		// 未发货状态
		if ($o == true && $p == true && $s == false) {
			$order_status = '未发货';
		}
		// 退货部分退货
		switch ($rows ['return_status']) {
			case '1' :
				$order_status = '部分退货';
				break;
			case '2' :
				$order_status = '退货';
				break;
		}
		
		// 再次验证是否是已确认收货
		if ($rows ['shipping_status'] == 2 && $rows ['return_status'] != 1) {
			$order_status = '收货确认';
		}					
				
		$goods_attrs = array();
		$sk = 0;
		// 销售比例
		if (strpos($rows['goods_attr_id'], ',') !== false)
		{
		    $goods_attrs = explode(',', $rows['goods_attr_id']);		   
		}
		else
		{
		    $goods_sn = $rows['goods_attr_id'];
		}
		
		
		$list [$rows ['order_sn']] ['order_status'] = $order_status;
		$list [$rows ['order_sn']] ['goods'] [$key] ['goods_sn'] = $rows ['goods_sn'];
		$list [$rows ['order_sn']] ['goods'] [$key] ['goods_name'] = $rows ['goods_name'];
		$list [$rows ['order_sn']] ['goods'] [$key] ['market_price'] = $rows ['market_price'];
		$list [$rows ['order_sn']] ['goods'] [$key] ['goods_price'] = $rows ['goods_price'];
		$list [$rows ['order_sn']] ['goods'] [$key] ['goods_number'] = $rows ['goods_number'];
		$list [$rows ['order_sn']] ['goods'] [$key] ['money'] = $rows ['money'];
		
		// 原价,时间戳补回8个小时
		$add_time = strtotime(local_date('Y-m-d H:i:s', $rows['add_time']));

		$list [$rows ['order_sn']] ['goods'] [$key] ['market_cost_price'] = $rows['market_price'];		// 商品原价
				
		// 属性价格
		$list [$rows ['order_sn']] ['goods'] [$key] ['spec_price'] = spec_price ( $goods_attrs ); 
		
		// 供应商折扣比例
		foreach ( $filter ['suppliers'] as $supplier ) {
			if ($supplier ['supplier_id'] == $rows ['supplier_id']) {
				$filter_supplier = $supplier;
			}
		}
		$cost = supplier_cost ( $list [$rows ['order_sn']] ['goods'] [$key], $filter_supplier );
		$list [$rows ['order_sn']] ['goods'] [$key] ['cost_price'] = $cost ['cost_price'];
		$list [$rows ['order_sn']] ['goods'] [$key] ['cost_ratio'] = $cost ['cost_ratio'];
	
		// 订单备注
		if($rows['order_id']){
			$order_action = $GLOBALS['db']->getOne("SELECT action_note FROM ".$GLOBALS['ecs']->table('order_action')." WHERE order_status = 5 AND order_id = ".$rows['order_id']." ORDER by action_id DESC");
		}
		$list [$rows ['order_sn']] ['order_action'] = $order_action;
	}
	
	unset($_SESSION['_card_user_info']);
	/*
	 * 已完成 102 order_status in(1,5) shipping_status in(1,2) pay_status in(1,2)
	 * 未付款 	0 		pay_status 	0 退货 	4 		order_status	4 部分退货	7 		order_status	7
	 * 部分发货	6 	order_status	6 收货确认	2		shipping_status 2
	 */
	// return array();
	return $list;
}
// 电影订单
function movie_orders($filter) {
	$where = 'WHERE 1 ';
	if ($filter ['order_status'] > 0) {
		$where .= " and o.order_status = " . $filter ['order_status'];
	}
	
	if ($filter ['start_time'] != '' && $filter ['end_time'] != '') {
		$where .= " and o.add_time >= '" . $filter ['start_time'] . "' and o.add_time <= '" . $filter ['end_time'] . "' ";
	}
	
	$sql = "select o.*  from  " . $GLOBALS ['ecs']->table ( 'seats_order' ) . " as o $where order by o.add_time ASC";
	
	$res = $GLOBALS ['db']->getAll ( $sql );
	$list = array ();
	foreach ( $res as $key => $rows ) {
		$list [$rows ['order_sn']] ['order_sn'] = $rows ['order_sn'];
		$list [$rows ['order_sn']] ['user_name'] = $rows ['user_name'];
		$list [$rows ['order_sn']] ['mobile'] = $rows ['mobile'];
		$list [$rows ['order_sn']] ['movie_name'] = $rows ['movie_name'];
		$list [$rows ['order_sn']] ['number'] = $rows ['count'];
		$list [$rows ['order_sn']] ['price'] = $rows ['unit_price'];
		$list [$rows ['order_sn']] ['goods_amount'] = price_format ( $rows ['money'] );
		$list [$rows ['order_sn']] ['add_time'] = $rows ['add_time'];
		
		if ($rows ['order_status'] == 1) {
			$order_status_cn = '已下单';
		}
		if ($rows ['order_status'] == 2) {
			$order_status_cn = '已取消';
		}
		if ($rows ['order_status'] == 3) {
			$order_status_cn = '已付款';
		}
		if ($rows ['order_status'] == 4) {
			$order_status_cn = '购票成功';
		}
		if ($rows ['order_status'] == 5) {
			$order_status_cn = '购票失败';
		}
		if ($rows ['order_status'] == 6) {
			$order_status_cn = '已退款';
		}
		
		$list [$rows ['order_sn']] ['order_status_cn'] = $order_status_cn;
		
		// 成本价
		$list [$rows ['order_sn']] ['cost_price'] = $rows ['extInfo'];
		// 成本比例
		$list [$rows ['order_sn']] ['cost_ratio'] = 1;
		// 销售比例
		$list [$rows ['order_sn']] ['shop_ratio'] = $rows['shop_ratio'];
		// 市场价（=成本价）
		$list [$rows ['order_sn']] ['market_cost_price'] = $rows ['extInfo'];
		// 卡规则比例
		$list [$rows ['order_sn']] ['card_ratio'] = $rows ['card_ratio'];
		// 上调浮比
		$list [$rows ['order_sn']] ['raise'] = $rows ['raise'];
		// 商城销售策略
		$list [$rows ['order_sn']] ['ext'] = $rows ['ext'];
	}
	return $list;
}
// 电子兑换券订单
function ticket_orders($filter) {
	$where = 'WHERE 1 ';
	if ($filter ['order_status'] > - 1) {
		if ($filter ['order_status'] == 1) {
			$where .= " and o.order_status = 0";
		}
		if ($filter ['order_status'] == 2) {
			$where .= " and o.order_status = 1";
		}
		if ($filter ['order_status'] == 3) {
			$where .= " and o.pay_status = 0";
		}
		if ($filter ['order_status'] == 4) {
			$where .= " and o.pay_status = 2";
		}
		if ($filter ['order_status'] == 5) {
			$where .= " and o.shipping_status = 0";
		}
		if ($filter ['order_status'] == 6) {
			$where .= " and o.shipping_status = 2";
		}
	}
	
	if ($filter ['start_time'] != '' && $filter ['end_time'] != '') {
		$where .= " and o.add_time >= '" . $filter ['start_time'] . "' and o.add_time <= '" . $filter ['end_time'] . "' ";
	}
	
	$sql = "select o.*  from  " . $GLOBALS ['ecs']->table ( 'dzq_order' ) . " as o   $where order by o.add_time ASC";
	
	$res = $GLOBALS ['db']->getAll ( $sql );
	
	$list = array ();
	foreach ( $res as $key => $rows ) {
		$list [$rows ['order_sn']] ['order_sn'] = $rows ['order_sn'];
		$list [$rows ['order_sn']] ['user_name'] = $rows ['user_name'];
		$list [$rows ['order_sn']] ['mobile'] = $rows ['mobile'];
		$list [$rows ['order_sn']] ['TicketName'] = $rows ['TicketName'];
		$list [$rows ['order_sn']] ['CinemaName'] = $rows ['CinemaName'];
		$list [$rows ['order_sn']] ['TicketYXQ'] = $rows ['TicketYXQ'];
		$list [$rows ['order_sn']] ['number'] = $rows ['number'];
		$list [$rows ['order_sn']] ['price'] =  $rows ['sjprice'];
		$list [$rows ['order_sn']] ['goods_amount'] = price_format ( $rows ['goods_amount'] );
		$list [$rows ['order_sn']] ['add_time'] = $rows ['add_time'];
		
		if ($rows ['order_status'] == 1 && $rows ['pay_status'] <= 1 && $rows ['shipping_status'] <= 1) {
			$order_status_cn = '已确认';
		}
		if ($rows ['order_status'] == 1 && $rows ['pay_status'] == 2 && $rows ['shipping_status'] <= 1) {
			$order_status_cn = '已付款';
		}
		if ($rows ['order_status'] == 1 && $rows ['pay_status'] == 2 && $rows ['shipping_status'] == 2) {
			$order_status_cn = '已兑换';
		}
		if ($rows ['order_status'] == 2) {
			$order_status_cn = '未确认';
		}
		if ($rows ['order_status'] == 3) {
			$order_status_cn = '无效';
		}
		$list [$rows ['order_sn']] ['order_status_cn'] = $order_status_cn;
		// 成本价
		//$list [$rows ['order_sn']] ['cost_price'] = $rows ['extInfo'];
		// 成本比例
		//$list [$rows ['order_sn']] ['cost_ratio'] = 1;
		// 销售比例
		$list [$rows ['order_sn']] ['shop_ratio'] = $rows['shop_ratio'];
		// 市场价（= 接口价）
		$list [$rows ['order_sn']] ['market_cost_price'] = $rows ['price'];
		// 卡规则比例
		$list [$rows ['order_sn']] ['card_ratio'] = $rows ['card_ratio'];
		// 上调浮比
		$list [$rows ['order_sn']] ['raise'] = $rows ['raise'];
		// 商城销售策略
		$list [$rows ['order_sn']] ['ext'] = $rows ['ext'];
	}
	return $list;
}
// 演出订单
function yanchu_orders($filter) {
	$where = 'WHERE 1 ';
	if ($filter ['order_status'] > - 1) {
		if ($filter ['order_status'] == 1) {
			$where .= " and o.order_status = 0";
		}
		if ($filter ['order_status'] == 2) {
			$where .= " and o.order_status = 1";
		}
		if ($filter ['order_status'] == 3) {
			$where .= " and o.pay_status = 0";
		}
		if ($filter ['order_status'] == 4) {
			$where .= " and o.pay_status = 2";
		}
		if ($filter ['order_status'] == 5) {
			$where .= " and o.shipping_status = 0";
		}
		if ($filter ['order_status'] == 6) {
			$where .= " and o.shipping_status = 2";
		}
	}
	
	if ($filter ['start_time'] != '' && $filter ['end_time'] != '') {
		$where .= " and o.add_time >= '" . $filter ['start_time'] . "' and o.add_time <= '" . $filter ['end_time'] . "' ";
	}
	
	$sql = "select o.*  from  " . $GLOBALS ['ecs']->table ( 'yanchu_order' ) . " as o   $where order by o.add_time ASC";
	
	$res = $GLOBALS ['db']->getAll ( $sql );

	$list = array ();
	foreach ( $res as $key => $rows ) {
		$list [$rows ['order_sn']] ['order_sn'] = $rows ['order_sn'];
		$list [$rows ['order_sn']] ['user_name'] = $rows ['user_name'];
		$list [$rows ['order_sn']] ['mobile'] = $rows ['mobile'] . ' ' . $rows ['tel'];
		$list [$rows ['order_sn']] ['itemname'] = $rows ['itemname'];
		$list [$rows ['order_sn']] ['regionname'] = $rows ['regionname'] . ' ' . $rows ['address'];
		$list [$rows ['order_sn']] ['consignee'] = $rows ['consignee'];
		
		if ($rows ['order_status'] == 1 && $rows ['pay_status'] == 0 && $rows ['shipping_status'] == 0) {
			$order_status_cn = '已确认';
		}
		if ($rows ['order_status'] == 1 && $rows ['pay_status'] == 2 && $rows ['shipping_status'] <= 0) {
			$order_status_cn = '已付款';
		}
		if ($rows ['order_status'] == 1 && $rows ['pay_status'] == 2 && $rows ['shipping_status'] == 2) {
			$order_status_cn = '已兑换';
		}
		if ($rows ['order_status'] != 1) {
			$order_status_cn = '未确认';
		}
		if ($rows ['shipping_status'] == 3) {
			$order_status_cn = '已退票';
		}
		
		$list [$rows ['order_sn']] ['order_status_cn'] = $order_status_cn;
		$list [$rows ['order_sn']] ['number'] = $rows ['number'];
		$list [$rows ['order_sn']] ['price'] = $rows ['price'];
		$list [$rows ['order_sn']] ['goods_amount'] = $rows ['goods_amount'];
		$list [$rows ['order_sn']] ['shipping_fee'] = $rows ['shipping_fee'];
		$list [$rows ['order_sn']] ['add_time'] = $rows ['add_time'];
		$list [$rows ['order_sn']] ['market_price'] = $rows ['market_price'];
		$list [$rows ['order_sn']] ['api_order_sn'] = $rows ['api_order_sn'];	
		
		// 销售比例
		$list [$rows ['order_sn']] ['shop_ratio'] = $rows['shop_ratio'];
		// 市场价（= 接口价）
		$list [$rows ['order_sn']] ['market_cost_price'] = $rows ['api_price'];
		// 卡规则比例
		$list [$rows ['order_sn']] ['card_ratio'] = $rows ['card_ratio'];
		// 上调浮比
		$list [$rows ['order_sn']] ['raise'] = $rows ['raise'];
		// 商城销售策略
		$list [$rows ['order_sn']] ['ext'] = $rows ['ext'];
		
	}
	return $list;
}

// 动网订单
function venues_orders($filter)
{
    $where = 'WHERE 1 ';
    
    if ($filter ['start_time'] != '' && $filter ['end_time'] != '') {
        $where .= " and o.add_time >= '" . $filter ['start_time'] . "' and o.add_time <= '" . $filter ['end_time'] . "' ";
    }
    
    $sql = "select o.*  from  " . $GLOBALS ['ecs']->table ( 'venues_order' ) . " as o   $where order by o.add_time ASC";
    
    $res = $GLOBALS ['db']->getAll ( $sql );

    $list = array ();
    foreach ( $res as $key => $rows ) {
        $list [$rows ['order_sn']] ['order_sn'] = $rows ['order_sn'];
        $list [$rows ['order_sn']] ['user_name'] = $rows ['username'];
        $list [$rows ['order_sn']] ['link_phone'] = $rows ['link_phone'];
        $list [$rows ['order_sn']] ['venueName'] = $rows ['venueName'];
        //$list [$rows ['order_sn']] ['regionname'] = $rows ['regionname'] . ' ' . $rows ['address'];
        $list [$rows ['order_sn']] ['link_man'] = $rows ['link_man'];
    
        if ($rows ['state'] == 0 ) {
            $order_status_cn = '未付款';
        }
        if ($rows ['state'] == 1 ) {
            $order_status_cn = '已付款';
        }
        if ($rows ['state'] == 2 ) {
            $order_status_cn = '已退款';
        }
        if ($rows ['state'] == 3) {
            $order_status_cn = '已完成';
        }
        if ($rows ['state'] == 4) {
            $order_status_cn = '退票中';
        }
    
        $list [$rows ['order_sn']] ['order_status_cn'] = $order_status_cn;
        $list [$rows ['order_sn']] ['total'] = $rows ['total'];
        $list [$rows ['order_sn']] ['money'] = $rows ['money'];
        $list [$rows ['order_sn']] ['unit_price'] = $rows ['unit_price'];
        $list [$rows ['order_sn']] ['add_time'] = $add_time;
    
        // 销售比例
        $list [$rows ['order_sn']] ['shop_ratio'] = $rows['shop_ratio'];
        // 市场价（= 接口价）
        $list [$rows ['order_sn']] ['market_cost_price'] = $rows ['market_price'];
        // 卡规则比例
        $list [$rows ['order_sn']] ['card_ratio'] = $rows ['card_ratio'];
        // 上调浮比
        $list [$rows ['order_sn']] ['raise'] = $rows ['raise'];
        // 商城销售策略
        $list [$rows ['order_sn']] ['ext'] = $rows ['ext'];
    }
    return $list;
}

// 票工厂订单
function piaoduoduo_orders($filter) {
	$result = get_filter();

	if ($result === false){
		/* 过滤信息 */
		$filter['order_sn'] = empty($filter['order_sn']) ? '' : trim($filter['order_sn']);
		$filter['user_id'] = empty($filter['user_id']) ? '' : intval($filter['user_id']);
		$filter['user_name'] = empty($filter['user_name']) ? '' : trim($filter['user_name']);
		$filter['sort_by'] = empty($filter['sort_by']) ? 'pay_time' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);
		$where = 'WHERE 1 ';
		if ($filter ['start_time'] != '' ) {
			$where .= " and p.pay_time >= '" . $filter ['start_time'] . "'";
		}
		if ($filter ['end_time'] != '') {
			$where .= " and p.pay_time <= '" . $filter ['end_time'] . "' ";
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
		$filter['page'] = empty($filter['page']) || (intval($filter['page']) <= 0) ? 1 : intval($filter['page']);

		if (isset($filter['page_size']) && intval($filter['page_size']) > 0)
		{
			$filter['page_size'] = intval($filter['page_size']);
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
	
		$filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;
		/* 查询 */
		$sql = "SELECT p.* FROM " . $GLOBALS['ecs']->table('piaoduoduo_order') . " AS p " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('users'). " AS u ON u.user_id=p.user_id ". $where .
				" ORDER BY $filter[sort_by] $filter[sort_order] ".
				" LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",$filter[page_size]";

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

// 供应商折扣信息
function supplier_cost($row, $supplier) {
	$new_price = $row ['market_cost_price'];
	
	if ($row ['spec_price'] > 0) {
		$new_price = $row ['market_cost_price']; // + $row['spec_price'];
	}
	if (in_array ( $supplier ['cost_ratio'], array (0,1))) {
		return array (
				'cost_price' => $new_price * $row ['goods_number'],
				'cost_ratio' => 1 
		);
	}
	
	return array (
			'cost_price' => ($new_price * $supplier ['cost_ratio']) * $row ['goods_number'],
			'cost_ratio' => $supplier ['cost_ratio'] 
	);
}

// 卡合并读取数据格式化
function card_excel($filter) {
	$where = ' WHERE 1 ';
	
	if ($filter ['start_time'] != '' && $filter ['end_time'] != '') {
		$where .= " and o.pay_time >= '" . $filter ['start_time'] . "' and o.pay_time <= '" . $filter ['end_time'] . "' ";
	}
	$sql = "select * from  " . $GLOBALS ['ecs']->table ( 'card_log' ) . " as o " . $where . " order by o.pay_time DESC";
	$res = $GLOBALS ['db']->getAll ( $sql );
	// $list = array();
	// foreach($res as $key => $rows)
	// {
	// $list[$rows['card_sn']]['log_id'] = $rows['log_id'];
	// $list[$rows['card_sn']]['card_from'] = $rows['card_from'];
	// $list[$rows['card_sn']]['card_to'] = $rows['card_to'];
	// $list[$rows['card_sn']]['card_money'] = $rows['card_money'];
	// $list[$rows['card_sn']]['pay_time'] = $rows['pay_time'];
	// $list[$rows['card_sn']]['is_paid'] = $rows['is_paid'];
	
	// }
	
	return $res;
}

// 会员充值读取数据格式化
function chongzhi_excel($filter) {
	$where = ' WHERE 1 ';
	if ($filter ['start_time'] != '' && $filter ['end_time'] != '') {
		$where .= " and ua.add_time >= '" . $filter ['start_time'] . "' and ua.add_time <= '" . $filter ['end_time'] . "' ";
	}
	/* 查询数据 */
	$sql = 'SELECT ua.*, u.user_name FROM ' . $GLOBALS ['ecs']->table ( 'user_account' ) . ' AS ua LEFT JOIN ' . $GLOBALS ['ecs']->table ( 'users' ) . ' AS u ON ua.user_id = u.user_id' . $where . "ORDER by ua.add_time DESC";
	$res = $GLOBALS ['db']->getAll ( $sql );
	return $res;
}
function mkdirs($dir, $mode = 0777) {
	if (is_dir ( $dir ) || @mkdir ( $dir, $mode )) {
		return TRUE;
	}
	if (! mkdirs ( dirname ( $dir ), $mode )) {
		return FALSE;
	}
	return @mkdir ( $dir, $mode );
}
function uploadFile($filename, $tmp_name) {
	// 自己设置的上传文件存放路径
	$filePath = dirname ( dirname ( __FILE__ ) ) . '/temp/upload/';
	$fileNamePath = $filePath . $filename;
	// 首先需要检测目录是否存在
	// move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
	if (is_dir ( $filePath )) {
		if (file_exists ( $fileNamePath )) {
			// echo "文件已经存在，无法提交！";
		} else {
			$result = move_uploaded_file ( $tmp_name, $fileNamePath );
		}
	} else {
		mkdir ( $filePath );
		
		$result = move_uploaded_file ( $tmp_name, $fileNamePath );
	}
	return $fileNamePath;
}

/*  商品消费比例 / 单价  */
function get_spec_ratio($spec)
{
	// 是否是改版后的规格商品
	$is_spec_nember = false;
	if (strpos($spec['spec_number'],'S_') !== false)
	{
		$is_spec_nember = true;
		$spec_temp = explode('_', $spec['spec_number']);
		//return $spec_temp[1];
	}
	// 如果是改版后的规格商品，
	if ($is_spec_nember == true)
	{
		// 当前规格的信息
		$spec_info = $GLOBALS['db']->getRow("SELECT gs.*,g.cat_id,g.supplier_id FROM ".$GLOBALS['ecs']->table('goods_spec')." AS gs ".
				"LEFT JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON g.goods_id = gs.goods_id ".
				"WHERE gs.goods_id=".$spec['goods_id']." AND gs.spec_nember ='".$spec_temp[1]."'");
		
		// 如果规格删除了，就得不到规格信息了，返回 -1
		if (empty($spec_info))
		{
			return array('shop_ratio'=>"0   (-1)", 'unit_price' => '' );
		}
		
		// 分类id对应的导航id
		$nav_id = $GLOBALS['db']->getOne("SELECT id FROM ".$GLOBALS['ecs']->table('nav')." WHERE cid=".$spec_info['cat_id']);
		// 下单用户所在的卡规则
		$card_info = get_card_info( $spec['user_id'], $nav_id);
		
		// 1、 卡规则里有没有对这个规格设置的比例

		if($card_info['card_id']){
			$card_spec = $GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['ecs']->table('card_discount')." WHERE card_id=".$card_info['card_id']." AND spec_id = ".$spec_info['id']);
		}
		if (!empty($card_spec))
		{
			return array('shop_ratio'=>$card_spec['discount']."   (1)", 'unit_price' => $card_info['unit_price'] );
		}
		
		// 2、 导航折扣
		if ($card_info['ratio'] > 0)
		{
			return array('shop_ratio'=>$card_info['ratio']."   (2)", 'unit_price' => $card_info['unit_price'] );
		}
		
		// 3、单个规格有折扣
		if ($spec_info['discount'] > 0)
		{
			return array('shop_ratio'=>$spec_info['discount']."   (3)", 'unit_price' => $card_info['unit_price'] );
		}
		
		// 4、 供应商折扣
		$shop_ratio = $GLOBALS['db']->getOne("SELECT shop_ratio FROM " . $GLOBALS['ecs']->table("supplier") ." WHERE supplier_id = ".$spec_info['supplier_id']);
		if ($shop_ratio != 0)
		{			
			return array('shop_ratio'=>$shop_ratio."   (4)", 'unit_price' => $card_info['unit_price'] );
		}	
		
	}
	// 改版之前的订单 （改版日期：2015-08-15 左右）
	else
	{

		if($spec['goods_id']){
			$spec_info = $GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id=".$spec['goods_id']);
		}
		// 如果商品被删除了，不在执行下面的操作
		if ( $spec_info == false)
		{
			return -1;
		}
		// 分类id对应的导航id
		$nav_id = $GLOBALS['db']->getOne("SELECT id FROM ".$GLOBALS['ecs']->table('nav')." WHERE cid=".$spec_info['cat_id']);
		// 下单用户所在的卡规则
		$card_info = get_card_info( $spec['user_id'], $nav_id);
		
		// 1、 设置了导航折扣
		if ($card_info['ratio'] > 0)
		{
			return array('shop_ratio'=>$card_info['ratio']."   (2)", 'unit_price' => $card_info['unit_price'] );
		}
		
		// 2、 设置供应商价格
		$shop_ratio = $GLOBALS['db']->getOne("SELECT shop_ratio FROM " . $GLOBALS['ecs']->table("supplier") ." WHERE supplier_id = ".$spec_info['supplier_id']);
		if ($shop_ratio != 0)
		{
			return array('shop_ratio'=>$shop_ratio."   (4)", 'unit_price' => $card_info['unit_price']);
		
		}		
	}
	
	return array( 'shop_ratio'=> -1 , 'unit_price' => 0);
}

function card_ratio_price($username, $nav_id)
{
	// 下单用户所在的卡规则
	$card_info = get_card_info( $username, $nav_id, true);
	// 1、 设置了导航折扣
	if ($card_info['ratio'] > 0)
	{
		return array('shop_ratio'=>$card_info['ratio']."   (2)", 'unit_price' => $card_info['unit_price'] );
	}
	
	// 2、 设置供应商价格
	$shop_ratio = $GLOBALS['db']->getOne("SELECT shop_ratio FROM " . $GLOBALS['ecs']->table("supplier") ." WHERE supplier_id = ".$spec_info['supplier_id']);
	if ($shop_ratio != 0)
	{
		return array('shop_ratio'=>$shop_ratio."   (4)", 'unit_price' => $card_info['unit_price']);
	
	}
}

/** 接口的销售比例和产品原价
 * 
 * @param string 	$username	用户名
 * @param int	 	$nav_id		导航id
 * @return array	销售比例和卡单价  
 */
function interface_ratio_price($username, $nav_id)
{
	// 设置不是导航的id对应真正的导航id
	$yes_nav_id = array( '1217'=>'25' ,'1220'=>'29', '1218'=>'30', '1211'=>'31', '1227'=>'32', '1224'=>'33');
	if ( array_key_exists($nav_id, $yes_nav_id))
	{
		$nav_id = $yes_nav_id[$nav_id];
	}		
	// 下单用户所在的卡规则
	$card_info = get_card_info($username, $nav_id, true);
	
	// 1、 设置了导航折扣
	if ($card_info['ratio'] > 0)
	{
		return array('shop_ratio'=>$card_info['ratio']."   (2)", 'unit_price' => $card_info['unit_price'] );
	}
	
	/* // 2、 设置供应商价格
	$shop_ratio = $GLOBALS['db']->getOne("SELECT shop_ratio FROM " . $GLOBALS['ecs']->table("supplier") ." WHERE supplier_id = ".$spec_info['supplier_id']);
	if ($shop_ratio != 0)
	{
		return array('shop_ratio'=>$shop_ratio."   (4)", 'unit_price' => $card_info['unit_price']);
	} */
	
	return array( 'shop_ratio'=> '0 ' , 'unit_price' => '0');
	
}
/**
 * 通过卡号找到它的卡规则id和分类销售折扣
 * @param unknown_type $uid
 * @param unknown_type $nid
 * @return multitype:mixed unknown
 */
function get_card_info($uid, $nid ,$is_username = false)
{
	// 保存在session中的卡信息是否存在！
	$card_user_session = isset($_SESSION['_card_user_info']) ? $_SESSION['_card_user_info'] : array();
	if ( !empty ($card_user_session[$uid]))
	{
		$card_id = $card_user_session[$uid]['card_id'];
	}
	else if ($is_username==false)
	{
		$card_id = $GLOBALS['db']->getOne(" SELECT card_id FROM ".$GLOBALS['ecs']->table('users')." WHERE user_id = ".$uid);
	}
	else 
	{
		$card_id = $GLOBALS['db']->getOne(" SELECT card_id FROM ".$GLOBALS['ecs']->table('users')." WHERE user_name = ".$uid);
	}
	
	// 没有卡规则，返回0
	if ($card_id <=0)
	{
		$arr_homeInfo['card_id'] = 0;
		$arr_homeInfo['ratio'] = 0;
		$arr_homeInfo['unit_price'] = 0;
	}
	else
	{
		if ( !empty ($card_user_session[$uid]))
		{
			$row = $_SESSION['_card_user_info'][$uid]['row'];
		}
		else {
			$row = $GLOBALS['db']->getRow('SELECT shop_ratio, id, price FROM '.$GLOBALS['ecs']->table('card_rule'). " WHERE id = ".$card_id);
			// 保存session中，同样的数据不再重复查找
			$_SESSION['_card_user_info'][$uid] = array( 'card_id'=> $card_id, 'row'=> $row);
		}
		
		$shop_ratio = unserialize($row['shop_ratio']);
		$arr_homeInfo['ratio'] = $shop_ratio[$nid];
		$arr_homeInfo['card_id']    = $row['id'];
		$arr_homeInfo['unit_price'] = $row['price'];
	}
	return $arr_homeInfo;		
}

//退换货列表
function back_list($filter)
{

	$where = ' WHERE 1 ';

	if($filter ['start_time']>$filter ['end_time']){
		sys_msg('开始时间不能大于结束时间');
	}
	if ($filter ['start_time'] != '') {
		$where .= " and b.return_time >= '" . $filter ['start_time'] . "'";
	}
	if ($filter ['end_time'] != '') {
		$where .= " and b.return_time <= '" . $filter ['end_time'] . "'";
	}	
    /* 查询 */
    $sql = "SELECT b.back_id, b.delivery_sn, b.order_sn, b.order_id, b.add_time, b.action_user, b.consignee, b.country,
                   b.province, b.city, b.district, b.tel, b.status, b.update_time, b.email, b.return_time,b.postscript, b.refund_money,u.user_name,s.supplier_name
            FROM " . $GLOBALS['ecs']->table("back_order") . " as b LEFT JOIN ". $GLOBALS['ecs']->table("users")." as u ON b.user_id = u.user_id LEFT JOIN ". $GLOBALS['ecs']->table("supplier")." as s ON b.supplier_id = s.supplier_id $where
            ORDER BY b.return_time";

    $row = $GLOBALS['db']->getAll($sql);

    /* 格式化数据 */
    foreach ($row AS $key => $value)
    {
        $row[$key]['return_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['return_time']);
        $row[$key]['add_time'] = local_date('Y-m-d', $value['add_time']);
        $row[$key]['update_time'] = local_date($GLOBALS['_CFG']['time_format'], $value['update_time']);
        if ($value['status'] == 1)
        {
            $row[$key]['status_name'] = $GLOBALS['_LANG']['delivery_status'][1];
        }
        else
        {
        $row[$key]['status_name'] = $GLOBALS['_LANG']['delivery_status'][0];
        }
    }
    // $arr = array('back' => $row, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $row;
}
?>