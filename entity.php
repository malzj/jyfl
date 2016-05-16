<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');

assign_template();

if ( !isset($_REQUEST['step']))
{
	$_REQUEST['step'] = 'index';
}

// 会员类型：1 华影VIP  2 普通客户
$utype = isset($_REQUEST['utype']) ? intval($_REQUEST['utype']) : 1 ;

// 供应商id
$supplier_id = isset($_REQUEST['sid']) ? intval($_REQUEST['sid']) : 0;

// 检查是否登陆
if ($_REQUEST['step'] == 'loginRef')
{
	exit('<center style="height:50px; line-height:50px">你没有登录，没有权限进行以下操作！<a href="supplier/privilege.php">去登陆</a></center>');	
}

// 加载页面 
else if ($_REQUEST['step'] == 'loadhtml')
{
	if ($supplier_id == 0)
	{
		exit('<center style="height:50px; line-height:50px">你没有登录，没有权限进行以下操作！<a href="supplier/privilege.php">去登陆</a></center>');
	}
	// 把供应商id放到session中，在保存订单的时候使用。
	if(empty($_SESSION['entityCart']['user_id'])){
		$_SESSION['entityCart']['user_id'] = $supplier_id;
	}
	// 订单号，在最开始就把订单号生成，是为了，避免重复保存订单。
	if (empty($_SESSION['entityCart']['order_sn']))
	{
		$_SESSION['entityCart']['order_sn'] = local_date("YmdHis").mt_rand(1, 1000);
	}
	// 供应商信息
	$supplier_info = supplier_info($supplier_id);
	if ($supplier_info['show_ordinary'] == 1 )
	{
		$utype = 1;
	}
	// 供应商不是实体店，跳转到首页
	if ($supplier_info['is_entity'] == 2)
	{
		exit('<center style="height:50px; line-height:50px">你没有权限访问此页面！<a href="index.php">返回首页</a></center>');
	}
	// 变更供应商的时候清空购物车
	if ($supplier_id != $_SESSION['entityCart']['user_id'])
	{
		$_SESSION['entityCart']=array();
	}
	
	// 卡确认
	$cardno = $_SESSION['entityCart']['cardno'];
	$card_no = array_shift($cardno);
	$cardConfirm = floatval($card_no['cardpay']) > 0 ? 1 : 0;
	//var_dump($supplier_info);
	$smarty->assign('sinfo', $supplier_info);
	$smarty->assign('utype', $utype);
	$smarty->assign('entityCart', $_SESSION['entityCart']);
	$smarty->assign('cardConfirm', $cardConfirm);
	exit($smarty->fetch('library/entityPay.lbi'));
}

// 选择商品 
else if($_REQUEST['step'] == 'addProduct')
{
	if ($supplier_id == 0)
	{
		show_message('你没有登录，没有权限进行以下操作！', '去登陆', 'supplier/privilege.php');
		exit('<center style="height:50px; line-height:50px">你没有登录，没有权限进行以下操作！<a href="/supplier">去登陆</a></center>');
	}
		
	// 获得供应的分类
	$category_childs = category_childs($supplier_id);
	// 没有子类，联系华影客服
	if(empty($category_childs))
	{
		exit('<center style="height:50px; line-height:50px">分类信息不完善，请联系华影客服！</center>');
	}
	
	// 获得第一个分类id
	$default_cat_id = $category_childs[0]['cat_id'];
	// 商品列表
	$goods_lists = assign_cat_goods($default_cat_id,'','wap');
	// 价格处理 
	if (!empty($goods_lists['goods']))
	{
		foreach($goods_lists['goods'] as &$goods){
			// 如果是华影 VIP 价格就是 市场价
			if ($utype == 1)
			{
				$goods['show_price'] = price_format($goods['shop_price']);
				$unit = '点';
			}
			// 如果是普通价格就是 市场价
			else
			{
				$goods['show_price'] = price_format($goods['market_price']);
				$unit = '元';
			}
		}
	}

	$smarty->assign('category_list', $category_childs);
	$smarty->assign('goods_list', $goods_lists);
	$smarty->assign('utype', $utype);
	$smarty->assign('unit', $unit);
	$smarty->assign('sid', $supplier_id);
	$smarty->assign('default_cat_id', $default_cat_id);
	$smarty->display('library/entityAdd.lbi');

}
// 分类商品
else if ($_REQUEST['step'] == 'category_list')
{
	$cat_id = isset($_REQUEST['cat_id']) ? intval($_REQUEST['cat_id']) : 0 ;
	if ($cat_id == 0)
	{
		exit('<center style="height:50px; line-height:50px;">分类不能为空！</center>');
	}
	// 商品列表
	$goods_lists = assign_cat_goods($cat_id,'','wap');
	// 价格处理
	if (!empty($goods_lists['goods']))
	{
		foreach($goods_lists['goods'] as &$goods){
			// 如果是华影 VIP 价格就是 市场价
			if ($utype == 1)
			{
				$goods['show_price'] = price_format($goods['shop_price']);
				$unit = '点';
			}
			// 如果是普通价格就是 市场价
			else
			{
				$goods['show_price'] = price_format($goods['market_price']);
				$unit = '元';
			}
		}
	}
	$smarty->assign('goods_list', $goods_lists);
	$smarty->assign('unit', $unit);
	$smarty->display('library/entityAddList.lbi');	
}

// 付款页面
else if($_REQUEST['step'] == 'index')
{
	
	// 会员类型，发生变化的时候，清空购物车
	if ($utype != $_SESSION['entityCart']['utype'])
	{
		$_SESSION['entityCart'] = array();
	}

	$smarty->assign('utype', $utype);
	$smarty->display('entity.dwt');
}

/** 更新SESSION中的订单信息
 *  $_SESSION['entityCart'] => array(
 *  			// 购物车商品
 *  			'goods' => array(
 *  				0=> array( 'name', 'num', 'market_price', 'shop_price', 'goods_id'),
 *  				0=> array( ... )
 *  			)
 *  			// 订单信息
 *  			'order' => array(
 *  				// 商品统计
 * 					'goods'=> array( 'ticket_num', 'ticket_price', 'ticket_total', 'goods_num', 'goods_total', 'order_totals') 
 * 					// 扣点商品统计
 * 					'order'=> array( 'goods'=>array(), 'card_total', 'pay_ticket_num', 'unpay_ticket_num', 'pay_goods_num', 'unpay_goods_num', 'card_balance')
 * 					// 现金商品统计
 * 					'money'=> array( 'goods'=>array(), 'money') * 					 
 *  			)
 *  			'utype' => 
 *  )
 */
else if($_REQUEST['step'] == 'updateCart'){
	// 商品id
	$goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
	// 商品数量
	$goods_num = isset($_REQUEST['goods_num']) ? intval($_REQUEST['goods_num']) : 0;
	// 模板
	$template = isset($_REQUEST['template']) ? addslashes_deep($_REQUEST['template']) : 'entityCartCol';
	// 供应商id
	$supplier_id = isset($_REQUEST['sid']) ? intval($_REQUEST['sid']) : 0;
	
	$json_array = array('error'=>0, 'message'=>'');
	if ($goods_id == 0 || $goods_num == 0)
	{
		$json_array['error'] 	= 1;
		$json_array['message']	= '商品id不正确，或数量为空！';
		exit(json_encode($json_array));
	}
			
	// session 中的商品
	$session_cart = array();
	// 购物车有商品的话，追加或更新订单信息
	if (isset($_SESSION['entityCart']) && count($_SESSION['entityCart']) > 0)
	{
		$session_cart = $_SESSION['entityCart'];
		// 如果存在购物车中，更新购物车商品数量
		if (array_key_exists($goods_id, $session_cart['goods']))
		{
			if ($template == 'entityCartCol')
			{
				$session_cart['goods'][$goods_id]['num'] += $goods_num;
			}
			else
			{
				$session_cart['goods'][$goods_id]['num'] = $goods_num;
			}			
		}
		else
		{
			$goods = get_goods_info($goods_id);
			$session_cart['goods'][$goods_id]['name'] = $goods['goods_name'];
			$session_cart['goods'][$goods_id]['num'] = $goods_num;
			$session_cart['goods'][$goods_id]['market_price'] = price_format($goods['market_price']);
			$session_cart['goods'][$goods_id]['shop_price'] = price_format($goods['shop_price']);
			$session_cart['goods'][$goods_id]['goods_id'] = $goods_id; 
			$session_cart['goods'][$goods_id]['cat_id'] = $goods['cat_id'];
			$session_cart['goods'][$goods_id]['supplier_id'] = $goods['supplier_id'];
			$session_cart['goods'][$goods_id]['goods_thumb'] = $goods['goods_thumb'];
		
			if ($utype == 1)
			{
				$session_cart['goods'][$goods_id]['show_price'] = price_format($goods['shop_price']);
				$unit = '点';
			}
			else{
				$session_cart['goods'][$goods_id]['show_price'] = price_format($goods['market_price']);
				$unit = '元';
			}
			
		}
		
	}
	// 购物车商品为空，添加信息
	else {
		$goods = get_goods_info($goods_id);
		$session_cart['goods'][$goods_id]['name'] = $goods['goods_name'];
		$session_cart['goods'][$goods_id]['num'] = $goods_num;
		$session_cart['goods'][$goods_id]['market_price'] = price_format($goods['market_price']);
		$session_cart['goods'][$goods_id]['shop_price'] = price_format($goods['shop_price']);
		$session_cart['goods'][$goods_id]['goods_id'] = $goods_id;
		$session_cart['goods'][$goods_id]['cat_id'] = $goods['cat_id'];
		$session_cart['goods'][$goods_id]['supplier_id'] = $goods['supplier_id'];
		$session_cart['goods'][$goods_id]['goods_thumb'] = $goods['goods_thumb'];
		if ($utype == 1)
		{
			$session_cart['goods'][$goods_id]['show_price'] = price_format($goods['shop_price']);
			$unit = '点';
		}
		else{
			$session_cart['goods'][$goods_id]['show_price'] = price_format($goods['market_price']);
			$unit = '元';
		}
	}
	
	// 会员类型（选择的会员类型不一样，价格不一样）
	$session_cart['utype'] = $utype;

	// 把供应商id放到session中，在保存订单的时候使用。
	if (empty($session_cart['user_id']))
	{
		$session_cart['user_id'] = $supplier_id;
	}
	
	// 订单号，在最开始就把订单号生成，是为了，避免重复保存订单。
	if (empty($session_cart['order_sn']))
	{
		$session_cart['order_sn'] = local_date("YmdHis").mt_rand(1, 1000);
	}
	
	// 更新统计购物车数据
	$new_session_cart = cart_total($session_cart);
	
	$_SESSION['entityCart'] = $new_session_cart;		
	
	if ($template == 'entityCartCol')
	{
		$smarty->assign('goods_list', $_SESSION['entityCart']['goods']);
	}
	else {
		$smarty->assign('cart_list', $_SESSION['entityCart']);
	}
	$smarty->assign('goods_list', $_SESSION['entityCart']['goods']);
	$smarty->assign('unit', $unit);
	
	
	$json_array['error'] 	= 0;
	$json_array['message']	= $smarty->fetch('library/'.$template.'.lbi');
	exit(json_encode($json_array));
	
}

// 删除购物车商品
else if($_REQUEST['step'] == 'deleteCart')
{
	// 商品id
	$goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0;
	if ($goods_id == 0)
	{
		$json_array['error'] 	= 1;
		$json_array['message']	= '商品id不正确！';
		exit(json_encode($json_array));
	}
	
	$session_cart = $_SESSION['entityCart'];
	
	// 如果存在，直接删掉
	if (array_key_exists($goods_id, $session_cart['goods']))
	{
		unset($session_cart['goods'][$goods_id]);
		unset($session_cart['order']['order']['goods'][$goods_id]);
		unset($session_cart['order']['money']['goods'][$goods_id]);
	}
	
	// 更新统计购物车数据
	$new_session_cart = cart_total($session_cart);
	$_SESSION['entityCart'] = $new_session_cart;
	exit(json_encode(array('error'=>0)));
}

// 获得购物车信息
else if($_REQUEST['step'] == 'cart')
{
	$template = isset($_REQUEST['template']) ? addslashes_deep($_REQUEST['template']) : 'entityCartCol';
	$session_info = cart_total($_SESSION['entityCart']);
	$_SESSION['entityCart'] = $session_info;
	//error_log(var_export($_SESSION['entityCart'], true),'3','error.log');
	if ($template == 'entityCartCol')
	{
		$smarty->assign('goods_list', $session_info['goods']);
	}
	else {
		$smarty->assign('cart_list', $session_info);
	}
	exit($smarty->fetch('library/'.$template.'.lbi'));
}

// 刷新统计信息
else if($_REQUEST['step'] == 'cart_total')
{
	exit(json_encode($_SESSION['entityCart']['order']));
}
// 查询卡余额
elseif($_REQUEST['step'] == 'query_cart'){
	$cardNo = isset($_REQUEST['cardno']) ? addslashes_deep($_REQUEST['cardno']) : '';
	if ($cardNo == '')
	{
		$json_array=array( 'error'=> 1, 'message'=> '卡信息不能是空的！');
		exit(json_encode($json_array));
	}
	
	// 卡信息
	$cardInfo = array();
	
	//查询单个卡余额 
	$cardRow = explode('@',$cardNo);
	array_pop($cardRow);
	foreach( (array)$cardRow as $row)
	{
		$cardCol = explode('|',$row);
		//$arr_cardInfo = getCardApi(array('cardId' => trim($cardCol[0]),'cardPwd' => trim($cardCol[1])), 'CARD-INFO', 7);		
		/** TODO 卡基本信息（双卡版） */
		$arr_param = array(	'CardInfo' => array( 'CardNo'=> trim($cardCol[0]), 'CardPwd' => trim($cardCol[1])) );
		$state = $cardPay->action($arr_param, 8);
		
		if ($state == 0)
		{
			$cardResult = $cardPay->getResult();
			if ($cardPay->getCardType() == 1)
			{
				$Points = $cardResult['Points'];
			}
			else {
				$Points = $cardResult['BalanceCash'];
			}
			$cardInfo[$cardCol[2]] = array( 'cardno'=> trim($cardCol[0]), 'cardpass'=>trim($cardCol[1]), 'cartprice'=>floatval($Points));
		}
		else{
			$cardInfo[$cardCol[2]] = array( 'cardno'=> trim($cardCol[0]), 'cardpass'=>trim($cardCol[1]), 'cartprice'=>'<font color=red title="'.$cardPay->getMessage().'">X</font>');
		}
	}
	
	$cardno = array();
	// 验证是否有重复的卡
	foreach($cardInfo as $cardid=>$cardRow)
	{
		if (array_key_exists($cardRow['cardno'], $cardno))
		{
			$cardInfo[$cardid]['cartprice'] = '<font color=red title="重复的华影卡！">X</font>';	
		}
		$cardno[$cardRow['cardno']] = 1 ;		
	}
	
	$card_total = 0;
	// 统计卡余额
	foreach($cardInfo as $info){
		$card_total += floatval($info['cartprice']);
	}
	$_SESSION['entityCart']['order']['order']['card_total'] = $card_total;
	$_SESSION['entityCart']['cardno'] = $cardInfo;
	$_SESSION['entityCart'] = cart_total($_SESSION['entityCart']);
	//error_log(var_export($_SESSION['entityCart'],true),'3','error.log');
	exit(json_encode($_SESSION['entityCart']['cardno']));
	
}

// 删除卡信息
else if($_REQUEST['step'] == 'delete_cart'){
	$cardid = isset($_REQUEST['cardid']) ? intval($_REQUEST['cardid']) : 0 ;	
	$card_info = $_SESSION['entityCart']['cardno'][$cardid];
	if (!isset($card_info))
	{
		exit(json_encode(array( 'error'=>0)));
	}
	$cartprice = floatval($card_info['cartprice']);
	unset($_SESSION['entityCart']['cardno'][$cardid]);
	// 删除支付卡后，清除支付池
	$_SESSION['entityCart']['order']['order']['goods'] = $_SESSION['entityCart']['order']['money']['goods'] = array();
	$_SESSION['entityCart']['order']['order']['card_total'] -= $cartprice;	
	$_SESSION['entityCart'] = cart_total($_SESSION['entityCart']);
	exit(json_encode(array('error'=>0)));
	
}

//  确认卡支付点数
else if($_REQUEST['step'] == 'confirm_card')
{
	$cardNo = isset($_REQUEST['cardno']) ? addslashes_deep($_REQUEST['cardno']) : '';
	if ($cardNo == '')
	{
		$json_array=array( 'error'=> 1, 'message'=> '卡信息不能是空的！');
		exit(json_encode($json_array));
	}
	
	// 卡信息
	$cardInfo = array();
	$cardRow = explode('@',$cardNo);
	array_pop($cardRow);
	foreach( (array)$cardRow as $row)
	{
		$cardCol = explode('|',$row);
		if (isset($_SESSION['entityCart']['cardno'][$cardCol[0]]))
		{
			if(floatval($_SESSION['entityCart']['cardno'][$cardCol[0]]['cartprice']) > 0)
			{
				$_SESSION['entityCart']['cardno'][$cardCol[0]]['cardpay'] = $cardCol[1];
			}
			else {
				$json_array=array( 'error'=> 1, 'message'=> '有卡信息不正确，请修改后在”查询余额“操作！');
				exit(json_encode($json_array));
			}
			
		}
		else{
			$json_array=array( 'error'=> 1, 'message'=> '没有此卡信息，请先”查询余额“操作！');
			exit(json_encode($json_array));
		}
	}
	exit(json_encode(array('error'=>0)));
}

// 支付的商品
else if($_REQUEST['step'] == 'pay_goods')
{	
	$session_info = $_SESSION['entityCart'];
	$pay_goods   = $session_info['order']['order']['goods'];
	$smarty->assign('goods_list', $pay_goods);
	$smarty->assign('card_balance',$session_info['order']['order']['card_balance']);
	$html = $smarty->fetch('library/entityPayGoods.lbi');	
	exit($html);
}
// 现金支付的商品
else if($_REQUEST['step'] == 'money_goods')
{	
	// 支付商品列表
	$money_goods = $_SESSION['entityCart']['order']['money']['goods'];
	if (!empty($money_goods))
	{
		foreach($money_goods as $mk=>$mg)
		{
			if ($mg['num'] == 0)
			{
				unset($money_goods[$mk]);
			}
		}
	}
	$smarty->assign('goods_list', $money_goods);
	$html = $smarty->fetch('library/entityMoneyGoods.lbi');
	echo $html;
}
// 确认订单信息
else if($_REQUEST['step'] == 'confirm')
{
	$smarty->assign('entityCart', $_SESSION['entityCart']);
	$smarty->display('library/entityConfirm.lbi');
}
/** 验证是否可以支付的操作
 *  验证条件：
 *  	1、客户类型是  华影VIP (utype==1) AND 
 *  	2、卡支付点数已设置 (cardpay > 0) AND
 *  	3、卡支付点数总和等于卡实际支付的点数 ( add(cardpay) == pay_ticket_card+pay_goods_card)
 */
elseif ($_REQUEST['step'] == 'confirm_pay')
{
	$session_info = $_SESSION['entityCart'];
	if ($session_info['utype'] == 1)
	{
		// 卡支付没有确认，不可提交
		$card_no = current($session_info['cardno']);
		$cardConfirm = floatval($card_no['cardpay']) > 0 ? 1 : 0;
		if ($cardConfirm == 0)
		{
			$json_array=array( 'error'=> 1, 'message'=> '卡支付点数没有确认，请确认后在操作！');
			exit(json_encode($json_array));
		}
		// 支付的总点数，不等于应付点数的时候，不可提交
		$total_card_price = 0;
		$total_price_card = $session_info['order']['order']['pay_ticket_card']+$session_info['order']['order']['pay_goods_card'];
		foreach( $session_info['cardno'] as $cno){
			$total_card_price += floatval($cno['cardpay']); 
		}
		//error_log($total_card_price.'--'.$total_price_card,'3','error.log');
		if ($total_card_price<$total_price_card)
		{
			$json_array=array( 'error'=> 1, 'message'=> '实际支付点数没有用完，或您还没有点确定按钮！');
			exit(json_encode($json_array));
		}
		if ($total_card_price>$total_price_card)
		{
			$json_array=array( 'error'=> 1, 'message'=> '实际支付点数已变成负数，请重新设置，或您还没有点确认按钮！');
			exit(json_encode($json_array));
		}
		
		exit(json_encode(array('error'=>0)));
	}	
	// 如果是普通客户，跳转卡支付验证，
	else {
		exit(json_encode(array( 'error'=> 0)));
	}
}
// 卡支付
else if($_REQUEST['step'] == 'done')
{
	$session_info = $_SESSION['entityCart'];
	
	// 卡支付商品
	$card_goods = serialize(deleteEmptyNum($session_info['order']['order']['goods']));
	// 现金支付的商品
	$money_goods = serialize(deleteEmptyNum($session_info['order']['money']['goods']));
	// 卡支付总和
	$card_total = $session_info['order']['order']['pay_card_total'];
	// 现金支付总额
	$money_total = $session_info['order']['money']['money'];
	// 支付的卡信息
	$pay_cards = serialize($session_info['cardno']);
	// 支付 类型
	$utype = $session_info['utype'];	
	
	// 如果订单已经存在，跳过插入接口
	$order_sn = $GLOBALS['db']->getOne("SELECT order_sn FROM ".$GLOBALS['ecs']->table('entity_order')." WHERE order_sn = ".$session_info['order_sn']);
	if (empty($order_sn))
	{
		// 保存订到到数据库
		$str_sql = 'INSERT INTO '. $GLOBALS['ecs']->table('entity_order') ."(order_sn, status, card_goods, money_goods, card_total, money_total, pay_cards, add_time, utype, user_id)
				VALUES('".$session_info['order_sn']."','0','".$card_goods."','".$money_goods."','".$card_total."','".$money_total."','".$pay_cards."','".strtotime(local_date('Y-m-d H:i:s'))."','".$utype."','".$session_info['user_id']."')";
		$query = $GLOBALS['db']->query($str_sql);
	}	
	
	// 支付记录日志 @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
	$logMsg = "\r\n@@[start] ～ [order_id] ".$session_info['order_sn']." ～ [starttime] ".local_date('Y-m-d H:i:s')." \r\n";
	
	// 支付卡点
	foreach($session_info['cardno'] as $cardno){		
		// 订单号
		$cardOrderId = local_date('ymdHis').mt_rand(1,1000);	
		// 支付价格为0，跳过支付操作
		if ($cardno['cardpay'] == 0)
		{
			continue;
		}	
		
		/** TODO 支付 （双卡版） */
		$arr_param = array(
				'CardInfo' => array( 'CardNo'=> $cardno['cardno'], 'CardPwd' => $cardno['cardpass']),
				'TransationInfo' => array( 'TransRequestPoints'=>$cardno['cardpay'])
		);
		$state = $cardPay->action($arr_param, 1, $cardOrderId);
		
		//$arr_cardInfo['ReturnCode'] = 0;
		if ($state == 1){
			$json_array = array( 'error'=>1, 'message'=> $cardPay->getMessage());
			// 支付失败，记录这次失败消息并关闭这次支付  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
			$logMsg .= "[id] ".$cardno['cardno']." ～ [price] ".$cardno['cardpay']." ～ [error] ".$cardPay->getMessage()." \r\n";
			$logMsg .= "@@[end] ～ [order_id] ".$session_info['order_sn']." \r\n";
			error_log($logMsg,3,'entit_pay_log.log');
			exit(json_encode($json_array));
		}
		// 支付成功，记录单条支付记录
		$logMsg .="[id] ".$cardno['cardno']." ～ [price] ".$cardno['cardpay']." \r\n";
		
	}
	// 支付完成，记录全部日志 @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@//
	$logMsg .= "@@[end] ～ [order_id] ".$session_info['order_sn']." \r\n";
	error_log($logMsg,'3','entit_pay_log.log');
	
	$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('entity_order')." SET status = 1 WHERE order_sn = '".$session_info['order_sn']."'");
	$_SESSION['entityCart'] = array();
	exit(json_encode(array( 'error'=>0)));
	
}
// 改变支付的商品数量
else if($_REQUEST['step'] == 'change_goods')
{
	$num = isset($_REQUEST['num']) ? intval($_REQUEST['num']) : 0 ;
	$goods_id = isset($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0 ;
	if ($goods_id == 0)
	{
		$json_array = array( 'error'=>1, 'message'=>'未知的商品id！');
		exit(json_encode($json_array));
	}
	
	// session 信息
	$session_info = $_SESSION['entityCart'];
	/** 判断剩余点数是否够支付当前的商品
	 *  思路：
	 *  	修改后的商品数量小于修改前的，跳过验证
	 *  	修改后的商品数量大于修改前的，实际支付点数 = 修改后数量 - 修改前数量 * 单价
	 *  	实际支付点数 > 剩余点数，返回错误消息
	 */
	if ($num > $session_info['order']['order']['goods'][$goods_id]['num'])
	{
		$diffNum = $num - $session_info['order']['order']['goods'][$goods_id]['num'];
		$diffPrice = $session_info['order']['order']['goods'][$goods_id]['show_price'] * $diffNum;
		if ($diffPrice > $session_info['order']['order']['card_balance'])
		{
			$json_array = array( 'error'=>1, 'message'=>'余额不足！');
			exit(json_encode($json_array));
		}		
	}
	
	// 更改支付数量
	$session_info['order']['order']['goods'][$goods_id]['num'] = $num;
		
	$new_session_cart = cart_total($session_info);
	$_SESSION['entityCart'] = $new_session_cart;
	$json_array = array( 'error'=>0, 'content'=>$new_session_cart['order']['order']['card_balance']);
	exit(json_encode($json_array));
		
}
else if($_REQUEST['step'] == 'cart_show')
{
	echo '<pre>';
	print_r($_SESSION['entityCart']);
	echo '</pre>';
}
/** 更新购物车统计数据\
 * 				// 订单信息
 *  			'order' => array( 	
 *  				// 商品统计
 * 					'goods'=> array( 'ticket_num', 'ticket_price', 'ticket_total', 'goods_num', 'goods_total', 'order_totals') 
 * 					// 扣点商品统计
 * 					'order'=> array( 'goods'=>array(), 'card_total', 'pay_ticket_num', 'unpay_ticket_num', 'pay_ticket_card', 'pay_goods_num', 'unpay_goods_num', 'card_balance', 'pay_goods_card')
 * 					// 现金商品统计
 * 					'money'=> array( 'goods'=>array(), 'money')
 * 					 
 *  			)
 */
function cart_total($data){
	//error_log(var_export($data, true),'3','error.log');
	$session_info = array(
		'goods'=> $data['goods'],
		'order'=> array(
				'goods'=> array( 'ticket_num'=>0, 'ticket_price'=>0, 'ticket_total'=>0, 'goods_num'=>0, 'goods_total'=>0, 'order_totals'=>0),
				'order'=> $data['order']['order'],//array( 'goods'=>array(), 'card_total'=>0, 'pay_ticket_num'=>0, 'unpay_ticket_num'=>0, 'pay_goods_num'=>0, 'unpay_goods_num'=>0, 'card_balance'=>0),
				'money'=> $data['order']['money']//array( 'goods'=>array(), 'money'=>0)
		),
		'utype'=> $data['utype'],
		'cardno'=> $data['cardno'],
		'user_id'=> $data['user_id'],
		'order_sn'=> $data['order_sn']
	);
	// 购物车为空，清空所有统计数据
	if (empty($data['goods']))
	{	
		$session_info = array();
		return $session_info;
	}
	// 单个商品信息，只是为了获得供应商id
	$firstGoods = current($data['goods']);
	
	// $ticket_cat_id 	门票分类id： 0 没有。 	
	$ticket_cat_id = 0;
	// $ticket_goods_id 门票商品id： 空数组表示没有
	$ticket_goods_id = array();
	// 供应商分类信息
	$category = category_childs($firstGoods['supplier_id']);
	foreach ( (array)$category as $cate){
		if ($cate['cat_name'] == '门票')
		{
			$ticket_cat_id = $cate['cat_id'];
		}		
	}
	
	// 商品统计 （门票、商品）
	foreach ($session_info['goods'] as &$goods )
	{
		// 有门票统计门票信息
		if ($goods['cat_id'] == $ticket_cat_id)
		{
			$session_info['order']['goods']['ticket_num'] += $goods['num'];
			$session_info['order']['goods']['ticket_price'] += price_format($goods['show_price']);
			$session_info['order']['goods']['ticket_total'] += price_format($goods['show_price'] * $goods['num']);
			$ticket_goods_id[] = $goods['goods_id'];
			$goods['is_ticket'] = 1;
		}
		// 不是门票的统计商品信息
		else{			
			$session_info['order']['goods']['goods_num'] += $goods['num'];
			$session_info['order']['goods']['goods_total'] += price_format($goods['show_price']*$goods['num']);
			$session_info['order']['goods']['order_totals'] += $session_info['order']['goods']['goods_total'];
			$goods['is_ticket'] = 0;
		}
	}
	// 应付总金额
	$session_info['order']['goods']['order_totals'] = $session_info['order']['goods']['ticket_total']+$session_info['order']['goods']['goods_total'];
	
	// 现金支付的商品和数量
	$money_goods = array();
	
	// 更新卡支付商品
	$goods_keys = array_keys($session_info['order']['order']['goods']);
	foreach($session_info['goods'] as $goods2)
	{
		// 现金支付商品数量是0
		$money_goods[$goods2['goods_id']] = 0;
		
		//  如果商品没有在卡支付池里，就加入，数量是0
		if (!in_array($goods2['goods_id'], $goods_keys))
		{
			$session_info['order']['order']['goods'][$goods2['goods_id']] = $goods2;
			$session_info['order']['order']['goods'][$goods2['goods_id']]['max'] = $goods2['num'];
			$session_info['order']['order']['goods'][$goods2['goods_id']]['num'] = 0;	
			// 放现金支付池里的商品和数量
			$money_goods[$goods2['goods_id']] = $goods2['num'];
			continue;
		}
		// 如果商品在支付池里，并且数量减少了，修改数量为修改后的.
		// 如果支付的数量是商品的实际数量，修改数量为修改后的.
		if ($goods2['num'] < $session_info['order']['order']['goods'][$goods2['goods_id']]['max'] &&
			$session_info['order']['order']['goods'][$goods2['goods_id']]['num'] == $session_info['order']['order']['goods'][$goods2['goods_id']]['max'])
		{
			$session_info['order']['order']['goods'][$goods2['goods_id']]['num'] = $goods2['num'];			
			// 放现金支付池里的商品和数量
			$money_goods[$goods2['goods_id']] = 0;
		}
		// 实际数量大于卡支付数量时，现金支付数量 = 实际数量 - 卡支付数量
		if ($goods2['num'] > $session_info['order']['order']['goods'][$goods2['goods_id']]['num'])
		{			
			$money_goods[$goods2['goods_id']] = $goods2['num']-$session_info['order']['order']['goods'][$goods2['goods_id']]['num'];
		}		
		
		$session_info['order']['order']['goods'][$goods2['goods_id']]['total_price'] = $session_info['order']['order']['goods'][$goods2['goods_id']]['num']*$goods2['show_price'];
		$session_info['order']['order']['goods'][$goods2['goods_id']]['max'] = $goods2['num'];
	}
	
	//error_log(var_export($money_goods,true),'3','error.log');
	// 更新支付统计
	$cardTotal = array('pay_ticket_num'=>0, 'unpay_ticket_num'=>0, 'pay_ticket_card'=>0, 'pay_goods_num'=>0, 'unpay_goods_num'=>0, 'card_balance'=>0, 'pay_goods_card'=>0, 'pay_card_total'=>0);
	foreach( $session_info['order']['order']['goods'] as $orderGoods)
	{
		// 统计门票支付的数量和扣的卡点
		if ($orderGoods['is_ticket'] == 1)
		{
			$cardTotal['pay_ticket_num'] += $orderGoods['num'];	
			$cardTotal['unpay_ticket_num'] += $orderGoods['max']-$orderGoods['num'];
			$cardTotal['pay_ticket_card'] += $orderGoods['num']*$orderGoods['show_price'];
		}
		else {
			$cardTotal['pay_goods_num'] += $orderGoods['num'];
			$cardTotal['unpay_goods_num'] += $orderGoods['max']-$orderGoods['num'];
			$cardTotal['pay_goods_card'] += $orderGoods['num']*$orderGoods['show_price'];
		}
	}
	// 总支付点数
	$cardTotal['pay_card_total'] = price_format($cardTotal['pay_ticket_card']+$cardTotal['pay_goods_card']);
	// 剩余点数
	$cardTotal['card_balance'] = $session_info['order']['order']['card_total'] - $cardTotal['pay_card_total'];
	
	// 更新统计操作
	foreach ($cardTotal as $ckey=>$ctotal)
	{
		$session_info['order']['order'][$ckey] = $ctotal; 
	}
	//error_log(var_export($money_goods,true),'3','error.log');
	// 更新现金支付商品和统计
	if (!empty($money_goods))
	{
		$session_info['order']['money']['money'] = 0;
		foreach ($money_goods as $m_id=>$m_num)
		{
			$session_info['order']['money']['goods'][$m_id] = $session_info['goods'][$m_id];
			$session_info['order']['money']['goods'][$m_id]['max'] = $session_info['goods'][$m_id]['num']; 
			$session_info['order']['money']['goods'][$m_id]['num'] = $m_num; 
			$session_info['order']['money']['goods'][$m_id]['show_price'] = $session_info['goods'][$m_id]['market_price'];
			$session_info['order']['money']['goods'][$m_id]['total_price'] = $session_info['order']['money']['goods'][$m_id]['show_price']*$m_num;
			$session_info['order']['money']['money'] += $session_info['order']['money']['goods'][$m_id]['total_price'];
		}
	}
	
	return $session_info;
}

// 供应商信息
function supplier_info($sid=0){	
	$sql = "SELECT * FROM " . $GLOBALS['ecs']->table("supplier") ." WHERE status =1 AND supplier_id = ".$sid;
	return $GLOBALS['db']->getRow($sql);
}

// 供应商分类信息
function supplier_cat_list($name){
	$category_info = $GLOBALS['db']->getAll("SELECT cat_id, parent_id, cat_name FROM " . $GLOBALS['ecs']->table("category"));
	return $category_info;
	
}


function category_childs($supplier_id){	
	// 获得供应商的顶级分类
	$pid = 0;
	$supplier_info = supplier_info($supplier_id);
	$category_list = supplier_cat_list();
	foreach($category_list as $list){
		if ($supplier_info['supplier_name'] == $list['cat_name'])
		{
			$pid = $list['cat_id'];
		}
	}
	// 没有和供应商名称相同的分类，联系华影客服，添加供应商分类
	if($pid == 0)
	{
		exit('<center style="height:50px; line-height:50px">分类信息不完善，请联系华影客服！</center>');
	}
	
	// 获得子类
	$category_childs = array();
	foreach($category_list as $child){
		if ($child['parent_id'] == $pid)
		{
			$category_childs[] = $child;
		}
	}
	
	return $category_childs;
}

// 删除数量为空的
function deleteEmptyNum($data=array()){
	foreach($data as $key=>$value)
	{
		if ($value['num'] == 0)
		{
			unset($data[$key]);
		}
	}
	return $data;
}


