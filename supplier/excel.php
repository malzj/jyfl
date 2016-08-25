<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once (ROOT_PATH . 'includes/lib_goods.php');

if ($_REQUEST['act'] == 'order_excel')
{	 
	 $smarty->display('excel.htm');
}
elseif($_REQUEST['act'] == 'excel')
{

	$filename='orderexcel';
    header("Content-type: application/vnd.ms-excel; charset=gbk");
    header("Content-Disposition: attachment; filename=$filename.xls");
	 
	 $order_status = $_REQUEST['order_status'];
	 $start_time = empty($_REQUEST['start_time']) ? '' : (strpos($_REQUEST['start_time'], '-') > 0 ?  local_strtotime($_REQUEST['start_time']) : $_REQUEST['start_time']);
	 $end_time = empty($_REQUEST['end_time']) ? '' : (strpos($_REQUEST['end_time'], '-') > 0 ?  local_strtotime($_REQUEST['end_time']) : $_REQUEST['end_time']);
	 $order_sn1 = $_REQUEST['order_sn1'];
	 $order_sn2 = $_REQUEST['order_sn2'];
	 
	 //$where = 'WHERE 1 ';
	 $where = "WHERE o.supplier_id='".$_SESSION['supplier_id']."' ";
	 if($order_status > -1)
	 {
		 if ($order_status == 5){
			$where .= " and o.shipping_status = 0 AND o.pay_status = 2";
		 }else{
			$where .= " and o.order_status = '$order_status' "; 
		 }
	 }

	 
	 if($start_time != '' && $end_time != '')
	 {
		 $where .= " and o.add_time >= '$start_time' and o.add_time <= '$end_time' "; 
	 }
	 
	 if($order_sn1 != '' && $order_sn2 != '')
	 {
		 $where .= " and o.order_sn >= '$order_sn1' and o.order_sn <= '$order_sn2' "; 
	 }
	 
	 $sql="select o.*,g.goods_id,g.goods_name,g.goods_attr,g.goods_attr_id,g.goods_number,g.goods_sn,g.market_price,g.goods_price ,g.goods_number,g.goods_price*g.goods_number as money,u.user_name,gs.spec_nember from  ". $GLOBALS['ecs']->table('order_info'). " as o left join " . $GLOBALS['ecs']->table('users')." as u on o.user_id=u.user_id "."left join  ". $GLOBALS['ecs']->table('order_goods')." as g on o.order_id=g.order_id "." left join  ". $GLOBALS['ecs']->table('goods_spec')." as gs on gs.goods_id=g.goods_id $where ";

    $res=$db->getAll($sql);

//order_status 订单状态。0，未确认；1，已确认；2，已取消；3，无效；4，退货；5,
//return_status 2为已退货，1为部分退货；
//shipping_status 商品配送情况，0，未发货；1，已发货；2，已收货；3，备货中
//pay_status 支付状态；0，未付款；1，付款中；2，已付款
    $list = array();
    $exportInfo = array ();
    $row = 2;
    switch ($_SESSION['supplier_id']) {
	//易果生鲜
	case '15':
		foreach($res as $key => $rows)
		{
			$list[$rows['order_sn']]['goods_amount'] = $rows['goods_amount'];//商品总金额
			$list[$rows['order_sn']]['shipping_fee'] = $rows['shipping_fee'];//配送费用
			$list[$rows['order_sn']]['money_paid'] = $rows['money_paid'];//已付款金额
			$list[$rows['order_sn']]['pay_name'] = $rows['pay_name'];
			$list[$rows['order_sn']]['order_sn'] = $rows['order_sn'];
			$list[$rows['order_sn']]['user_name'] = $rows['user_name'];
			$list[$rows['order_sn']]['consignee'] = $rows['consignee'];
			$list[$rows['order_sn']]['tel'] = $rows['tel'] ? $rows['tel']: '';
			$list[$rows['order_sn']]['mobile'] = $rows['mobile'] ? $rows['mobile']:$rows['tel'];
			$list[$rows['order_sn']]['country'] = getRegion($rows['country']);
			$list[$rows['order_sn']]['province'] = getRegion($rows['province']);
			$list[$rows['order_sn']]['address'] = $rows['address'];
			$list[$rows['order_sn']]['add_time'] =local_date('Y-m-d', $rows['add_time']);
			$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_sn'] = $rows['goods_sn'];
			$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_name'] = $rows['goods_name'];
			$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['market_price'] = $rows['market_price'];
			$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_price'] = $rows['goods_price'];
			$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_number'] = $rows['goods_number'];
			$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['spec_nember'] = $rows['spec_nember'];
			$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['money'] = $rows['money'];
	 	}
	 	
		foreach($list as $key => $val)
		{
		    $datas=array();
		    $goods=array();
		    $goods_name=array();
		    $goodsName='';
		    
		    foreach($val['goods'] as $goods)
		    {
		        $datas[]=$goods['spec_nember'].'['.$goods['goods_number'].']';
		        $goods_name[]=$goods['goods_name'];
		    
		    }
		    $exportInfo[] = array(
		        'A' . $row => ' ' . $val['pay_name'],
		        'B' . $row => ' ' . $val['order_sn'],
		        'C' . $row => ' '.$val ['user_name'],
		        'D' . $row => $val ['tel'],
		        'E' . $row => $val ['consignee'],
		        'F' . $row => $val['mobile'],
		        'G' . $row => $val['country'],
		        'H' . $row => $val['province'],
		        'I' . $row => $val['address'],
		        'J' . $row => $val['add_time'],
		        'K' . $row => implode(',', $datas),
		        'L' . $row => implode(',', $goods_name),
		        'M' . $row => $val['goods_amount'],
		         
		        'N' . $row => $val['shipping_fee'], 
		        'O' . $row => $val['money_paid']		        
		  );	
		  
		  $row ++;
		}
		
		break;

	default:
		
		foreach($res as $key => $rows)
		{
		    $list[$rows['order_sn']]['goods_amount'] = $rows['goods_amount'];//商品总金额
		    $list[$rows['order_sn']]['shipping_fee'] = $rows['shipping_fee'];//配送费用
		    $list[$rows['order_sn']]['money_paid'] = $rows['money_paid'];//已付款金额
		    $list[$rows['order_sn']]['pay_name'] = $rows['pay_name'];
		    $list[$rows['order_sn']]['order_sn'] = $rows['order_sn'];
		    $list[$rows['order_sn']]['user_name'] = $rows['user_name'];
		    $list[$rows['order_sn']]['consignee'] = $rows['consignee'];
		    $list[$rows['order_sn']]['tel'] = $rows['tel'] ? $rows['tel']: '';
		    $list[$rows['order_sn']]['mobile'] = $rows['mobile'] ? $rows['mobile']:$rows['tel'];
		    $list[$rows['order_sn']]['country'] = getRegion($rows['country']);
		    $list[$rows['order_sn']]['province'] = getRegion($rows['province']);
		    $list[$rows['order_sn']]['address'] = $rows['address'];
		    $list[$rows['order_sn']]['add_time'] =local_date('Y-m-d', $rows['add_time']);
		    $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_sn'] = $rows['goods_sn'];
		    $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_name'] = $rows['goods_name'];
		    $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['market_price'] = $rows['market_price'];
		    $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_price'] = $rows['goods_price'];
		    $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_number'] = $rows['goods_number'];
		    $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['spec_nember'] = $rows['spec_nember'];
		    $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['money'] = $rows['money'];
		    
		    // 属性价格
		    $goods_attrs = array();
		    if (strpos($rows['goods_attr_id'], ',') !== false)
		    {
		        $goods_attrs = explode(',', $rows['goods_attr_id']);
		    }
		    $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['spec_price'] = spec_price ( $goods_attrs );
		    // 订单状态
		    $list[$rows['order_sn']]['order_state_sn'] = get_order_state($rows);
		}
		
		foreach ( $list as $val ) {
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
		            'D' . $row => $val ['mobile'],
		            'E' . $row => $val ['country'],
		            'F' . $row => $val ['province'],
		            'G' . $row => $val ['address'],
		            'H' . $row => $val ['add_time'],
		            'I' . $row => $val ['goods_sn'],
		            'J' . $row => $goods ['goods_name'],
		            'K' . $row => $goods ['market_price'],
		            'L' . $row => $goods ['goods_number'],
		         
		
		            'M' . $row => $goods ['spec_price'], // 配件价格
		            'N' . $row => $shipping_fee, // 配送费用
		            'O' . $row => $val['order_state_sn']
		        );
		        $g ++;
		        $row ++;
		    }
		}
		
		break;
    }
    
    // 导入excel类
    require (dirname ( __FILE__ ) . '/../admin/includes/lib_autoExcels.php');
    $autoExcels = new autoExcels ( 'Excel2007' );
    $filename = 'ORDER-' . date ( 'mdHis', time () ) . '.xlsx';
    $autoExcels->setSaveName (iconv('utf-8', "gb2312", $filename) );
    
    // 易果生鲜导出格式
    if ($_SESSION['supplier_id'] == 15)
    {
        $exportContent = array (
            array (
                'sheetName' => '当单信息',
                'title' => array (
                    'A1' => '用户名',      'B1' => '订单号',  'C1' => '订货人', 'D1' => '订货人手机', 'E1' => '收货人',
                    'F1' => '收货人手机',  'G1' => '省份',    'H1' => '城市', 'I1' => '详细地址-路/大道',
                    'J1' => '下单时间',    'K1' => '商品代码', 'L1' => '商品名称', 'M1' => '商品总金额', 'N1' => '配送费用',
                    'O1' => '已付款金额',
                ),
                 
                'widths' => array ( 'A' => '20',  'B' => '20', 'C' => '10',  'D' => '10', 'E' => '10', 'F' => '15',
                    'G' => '7',   'H' => '7',  'I' => '40',   'J' => '15', 'K' => '30', 'L' => '30',
                    'M' => '10',  'N' => '10', 'O' => '10'
                )
            )
        );
    }
    // 其他供应商走这个标准
    else 
    {
        $exportContent = array (
            array (
                'sheetName' => '当单信息',
                'title' => array (
                    'A1' => '订单号',      'B1' => '卡号',       'C1' => '收货人',      'D1' => '联系电话',     'E1' => '城市',
                    'F1' => '区县',        'G1' => '详细地址',    'H1' => '下单时间',     'I1' => '商品代码',     'J1' => '商品名称',
                    'K1' => '商品原价',     'L1' => '数量',       'M1' => '配件价格',      'N1' => '配送费用',     'O1' => '订单状态',        
                ),
                 
                'widths' => array ( 'A' => '20',  'B' => '20', 'C' => '10',  'D' => '15', 'E' => '15', 'F' => '10',
                    'G' => '25',   'H' => '7',  'I' => '7',   'J' => '40', 'K' => '13', 'L' => '13',
                    'M' => '13',  'N' => '13', 'O' => '13'
                )
            )
        );
    }
    
    
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
    
    

}

function get_order_state($rows)
{
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
    
    return $order_status;
}


//获取状态
function getOrder_status($status){

	switch ($status) {
		case '0':
			return "未确认";
			break;
		case '1':
			return "已确认";
			break;
		case '2':
			return "已取消";
			break;
		case '3':
			return "无效";
			break;						
		case '4':
			return "退货";
			break;
		case '5':
			return "已分单";
			break;
		default:
			break;
	}
}
function getShipping_status($status){
	switch ($status) {
		case '0':
			# code...
			return "未发货";
			break;
		case '1':
			# code...
			return "已发货";
			break;
		case '2':
			# code...
			return "已退货";
			break;
		case '3':
			# code...
			return "备货中";
			break;						
	
		default:
			# code...
			break;
	}
}
function getReturn_status($status){

	switch ($status) {
		case '0':
			break;
		case '1':
			# code...
			return "部分退货";
			break;
		case '2':
			# code...
			return "已收货";
			break;

		default:
			# code...
			break;
	}
}

function getPay_status($status){

	switch ($status) {
		case '0':
			return "未付款";
			break;
		case '1':
			# code...
			return "付款中";
			break;
		case '2':
			# code...
			return "已付款";
			break;

		default:
			# code...
			break;
	}
}
function getRegion($region_id){
	if($region_id){
        $sql = "SELECT region_name FROM " . $GLOBALS['ecs']->table('region') . " WHERE region_id = '$region_id'";
        $region = $GLOBALS['db']->getRow($sql);
        // echo $region['region_name'];
        return $region['region_name'];		
	}else{
		return null;
	}

}

?>