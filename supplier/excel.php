<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

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
	 
	 $sql="select o.order_sn,o.consignee,o.country,o.province,o.address,o.tel,o.mobile,o.add_time,o.goods_amount,o.shipping_fee,o.money_paid,o.shipping_name,o.pay_name, o.pay_status,o.order_status, o.return_status, o.shipping_status,g.goods_id,g.goods_name,g.goods_attr,g.goods_number,g.goods_sn,g.market_price,g.goods_price ,g.goods_number,g.goods_price*g.goods_number as money,u.user_name,gs.spec_nember from  ". $GLOBALS['ecs']->table('order_info'). " as o left join " . $GLOBALS['ecs']->table('users')." as u on o.user_id=u.user_id "."left join  ". $GLOBALS['ecs']->table('order_goods')." as g on o.order_id=g.order_id "." left join  ". $GLOBALS['ecs']->table('goods_spec')." as gs on gs.goods_id=g.goods_id $where ";

$res=$db->getAll($sql);

//order_status 订单状态。0，未确认；1，已确认；2，已取消；3，无效；4，退货；5,
//return_status 2为已退货，1为部分退货；
//shipping_status 商品配送情况，0，未发货；1，已发货；2，已收货；3，备货中
//pay_status 支付状态；0，未付款；1，付款中；2，已付款
$list = array();
switch ($_SESSION['supplier_id']) {
	//易果生鲜
	case '15':
		# code...
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
		$data .= "<table border='1'>";
		$data .= "<tr bgcolor='#999999'>
					<td>用户名</td>
					<td>订单号</td>
					<td>订货人</td>
					<td>订货人手机</td>
					<td>收货人</td>
					<td>收货人手机</td>
					<td>省份</td>
					<td>城市</td>
					<td>详细地址-路/大道</td>
					<td>下单时间</td>
					<td>商品代码</td>
					<td>商品名称</td>
					<td>商品总金额</td>
					<td>配送费用</td>
					<td>已付款金额</td>
				</tr>"; 
		foreach($list as $key => $val)
		{

			$data .= "<tr>
					<td>".$val['pay_name']."</td>
					<td>"."&nbsp;".$val['order_sn']."</td>
					<td>"."&nbsp;".$val['user_name']."</td>
					<td>".$val['tel']."</td>
					<td>".$val['consignee']."</td>
					<td>".$val['mobile']."</td>
					<td>".$val['country']."</td>
					<td>".$val['province']."</td>
					<td>".$val['address']."</td>
					<td>".$val['add_time']."</td>";

			
			$data.="<td>";
			$datas=array();
			$goods=array();
			$goods_name=array();
			$goodsName='';
			// $totalPrices=array();
			// $num[]=count($val['goods']);
			foreach($val['goods'] as $goods)
			{
				$datas[]=$goods['spec_nember'].'['.$goods['goods_number'].']';
				$goods_name[]=$goods['goods_name'];
				
			}
			$data.=implode(',', $datas);
			$goodsName=implode(',', $goods_name);
			$data.="</td><td>".$goodsName."</td>
			<td>".$val['goods_amount']."</td>
			<td>".$val['shipping_fee']."</td>
			<td>".$val['money_paid']."</td>
			</tr>";
		}
		$data .= "</table>";
		$data .= "<br>";	
		break;
	//北京奈斯贸易有限公司
	case '39':
		# code...
		$i=1;
		foreach($res as $key => $rows)
		{

			// $list[$rows['order_sn']]['status'] = getOrder_status($rows['order_status']);
			$list[$rows['order_sn']]['pay_name'] = $rows['pay_name'];
			 $list[$rows['order_sn']]['order_sn'] = $rows['order_sn'];
			 $list[$rows['order_sn']]['shipping_fee'] = $rows['shipping_fee'];//配送费用
			 // $list[$rows['order_sn']]['user_name'] = $rows['user_name'];
			 $list[$rows['order_sn']]['consignee'] = $rows['consignee'];
			 $list[$rows['order_sn']]['tel'] = $rows['tel'] ? $rows['tel']: '';
			 $list[$rows['order_sn']]['mobile'] = $rows['mobile'] ? $rows['mobile']:$rows['tel'];
			 $list[$rows['order_sn']]['country'] = getRegion($rows['country']);
			 $list[$rows['order_sn']]['province'] = getRegion($rows['province']);
			 $list[$rows['order_sn']]['address'] = $rows['address'];
			 $list[$rows['order_sn']]['add_time'] =local_date('Y-m-d H:i:s', $rows['add_time']);
			 $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_sn'] = $rows['goods_sn'];
			 $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_name'] = $rows['goods_name'];
			 $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['market_price'] = $rows['market_price'];
			 $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_price'] = $rows['goods_price'];
			 $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_number'] = $rows['goods_number'];
			 $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['spec_nember'] = $rows['spec_nember'];
			 $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['money'] = $rows['money'];
	 		// 订单状态
			$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['status'] = null;
			$o = $s = $p = $t = false;
			switch ($rows ['order_status']) {
				case '1' :
					$o = true;
					break;
				case '5' :
					$o = true;
					break;
				case '4' :
					$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['status'] = '退货';
					break;
				case '6' :
					$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['status'] = '部分发货';
					break;
				case '7' :
					$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['status'] = '部分退货';
					break;
			}
			// 发货状态
			switch ($rows ['shipping_status']) {
				case '1' :
					$s = true;
					break;
				case '2' :
					$s = true;
					$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['status'] = $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['status'] == null ? '收货确认' : $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['status'];
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
					$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['status'] = '未付款';
					break;
			}
			
			// 已完成状态
			if ($o == true && $p == true && $s == true) {
				$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['status'] = '已完成';
			}
			// 未发货状态
			if ($o == true && $p == true && $s == false) {
				$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['status'] = '未发货';
			}
			// 退货部分退货
			switch ($rows ['return_status']) {
				case '1' :
					$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['status'] = '部分退货';
					break;
				case '2' :
					$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['status'] = '退货';
					break;
			}
			
			// 再次验证是否是已确认收货
			if ($rows ['shipping_status'] == 2 && $rows ['return_status'] != 1) {
				$list[$rows['order_sn']]['goods'][$rows['goods_sn']]['status'] = '收货确认';
			}
		
	 	}

		$data .= "<table border='1'>";
		$data .= "<tr bgcolor='#999999'>
					<td>序号</td>
					<td>订单号</td>					
					<td>收货人</td>
					<td>联系电话</td>
					<td>送货地址</td>
					<td>下单时间</td>
					<td>商品名称</td>
					<td>本店价</td>					
		            <td>运费</td>
		            <td>购买数量</td>
					<td>小计</td>
					<td>订单状态</td>
				</tr>"; 
		$num='';		
		foreach($list as $key => $val)
		{
			$num=count($val['goods']);
			$data .= "<tr>
					<td rowspan=\"$num\">".$i."</td>
					<td rowspan=\"$num\">"."&nbsp;".$val['order_sn']."</td>
					
					<td rowspan=\"$num\">".$val['consignee']."</td>
					<td rowspan=\"$num\">"."&nbsp;".$val['mobile']."</td>
					<td rowspan=\"$num\">".$val['country'].$val['province'].$val['address']."</td>
					<td rowspan=\"$num\">".$val['add_time']."</td>";


			$goods=array();
			$jjj = 0;
			foreach($val['goods'] as $goods)
			{
				$data.="<td>".$goods['goods_name']."</td>
				        <td>".$goods['goods_price']."</td>";
				if ($jjj==0)
				    $data .="<td rowspan=\"$num\">".$val['shipping_fee']."</td>";
				
				$data .="<td>".$goods['goods_number']."</td>
				        <td>".$goods['goods_price']*$goods['goods_number']."</td>
				        <td>".$goods['status']."</td>";
				$data.=	"</tr>";
				$jjj++;
			}
			
			$i++;
		}
		$data .= "</table>";
		$data .= "<br>";	
		break;
		
	default:
		# code...
		foreach($res as $key => $rows)
		{
			 $list[$rows['order_sn']]['order_sn'] = $rows['order_sn'];
			 $list[$rows['order_sn']]['user_name'] = $rows['user_name'];
			 $list[$rows['order_sn']]['consignee'] = $rows['consignee'];
			 $list[$rows['order_sn']]['tel'] = $rows['mobile'] . ($rows['tel'] ? '('.$rows['tel'].')' : '');
			 $list[$rows['order_sn']]['address'] = $rows['address'];
			 $list[$rows['order_sn']]['add_time'] =local_date('y-m-d H:i', $rows['add_time']);
			 $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_sn'] = $rows['goods_sn'];
			 $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_name'] = $rows['goods_name'];
			 $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['market_price'] = $rows['market_price'];
			 $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_price'] = $rows['goods_price'];
			 $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['goods_number'] = $rows['goods_number'];
			 $list[$rows['order_sn']]['goods'][$rows['goods_sn']]['money'] = $rows['money'];
	 	}


		foreach($list as $key => $val)
		{
			$data .= "<table border='1'>";
			$data .= "<tr><td colspan='2'>订单号：".$val['order_sn']."</td><td>用户名：".$val['user_name']."</td><td colspan='2'>收货人：".$val['consignee']."</td><td colspan='2'>联系电话：".$val['tel']."</td></tr>"; 
			$data .= "<tr><td colspan='5'>送货地址：".$val['address']."</td><td colspan='2'>下单时间：".$val['add_time']."</td></tr>";
			$data .= "<tr bgcolor='#999999'><th>序号</th><th>货号</th><th>商品名称</th><th>市场价</th><th>本店价</th><th>购买数量</th><th>小计</th></tr>";
			$i = 1;
			foreach($val['goods'] as $goods)
			{
			$data .= "<tr><th>".$i."</th><th>".$goods['goods_sn']."</th><th>".$goods['goods_name']."</th><th>".$goods['market_price']."</th><th>".$goods['goods_price']."</th><th>".$goods['goods_number']."</th><th>".$goods['money']."</th></tr>";
			$i ++;
			}
			$data .= "</table>";
			$data .= "<br>";

		}	
		break;
}


	
	//echo ecs_iconv(EC_CHARSET, 'gb2312', $data) . "\t";
	echo $data. "\t";
	/*
    if (EC_CHARSET != 'gb2312')
    {
        echo ecs_iconv(EC_CHARSET, 'gb2312', $data) . "\t";
    }
    else
    {
        echo $data. "\t";
    }*/
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