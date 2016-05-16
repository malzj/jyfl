<?php 
define('IN_ECS', TRUE);
require_once 'lib/db.php';
$db = new DB();
echo $abgdsfd
session_start();

$row = $db->get_one('SELECT * FROM ecs_pay_log WHERE log_id = 8875');

// 未支付，继续操作
if ($row && $row['is_paid']==0 && $row['order_type'] == 1)
{
	// 更新支付状态为已支付
	$db->update('ecs_pay_log', array( 'is_paid'=> 1 ), ' log_id = 8875 ');
	// 会员充值记录
	$account = $db->get_one('SELECT * FROM ecs_user_account WHERE `id` = '.$row['order_id']);
	
	// 修改充值记录状态 
	if ($account && $account['is_paid'] == 0)
	{
		$db->update('ecs_user_account', array('paid_time'=> time(), 'is_paid'=>1, 'pay_no'=>'123456'), ' id = '.$row['order_id']);
		
		/** TODO 充值操作 **/
		// 通过卡规则设置的充值售比计算出充值的点数，默认是1.3
		$pay_than = 1.3;
		// 得到卡规则id，如果由的话
		if( is_null($_SESSION['card_id']) )
		{
			$userinfo = $db->get_one('SELECT * FROM ecs_users where user_id = '.$account['user_id']);
			$card_id = $userinfo['card_id'];
		}
		else {
			$card_id = $_SESSION['card_id'];
		}
				
		// 如果存在卡规则，得到充值比例
		if (floatval($card_id) > 0)
		{
			$pay_price = $db->get_one('SELECT pay_than FROM ecs_card_rule where id = '.$card_id);
			if ( !empty($pay_price['pay_than']) && $pay_price['pay_than'] > 0.001)
			{
				$pay_than = $pay_price['pay_than'];
			}
		}
		
		// 实际充值点数
		$int_sjMoney = $account['amount'] / $pay_than;	
		
		// 中影卡，审核充值操作
		if (!empty($account['order_sn']))
		{
			include_once('../../includes/lib_cardApi.php');			
			if ($int_sjMoney > 0 ){
				//审核充值操作
				$arr_param = array(
						'orderId'    => $account['order_sn'],
						'operId'     => $GLOBALS['_CFG']['operId'],
						'extendInfo' => ''
				);
				$arr_cardInfo = getCardApi($arr_param, 'CARD-AUTH-RECHARGE', 7);
				if ($arr_cardInfo['ReturnCode'] != '0'){
					return $arr_cardInfo['ReturnMessage'];
				}
			}
		}
		// 华影卡，充值操作
		else
		{
			// 卡密码和卡号
			if (empty($userinfo))
			{
				$userinfo = $db->get_one('SELECT * FROM ecs_users where user_id = '.$account['user_id']);
			}
			include_once('../../includes/lib_huayingcard.php');
			include_once('../../includes/httpRequest.php');
			// 实例化卡系统接口
			$cardPay = new huayingcard();
			$arr_param = array(
					'CardInfo' => array( 'CardNo'=> $userinfo['user_name']),
					'TransationInfo' => array( 'TransRequestPoints'=>$int_sjMoney)
			);
			$state = $cardPay->action($arr_param, 6);
			if ($state == 1)
			{
				var_dump($cardPay->getMessage());
			}
		}		
		
		$db->update(
				'ecs_users',
				array( 
						'user_money '=> $int_sjMoney+$userinfo['card_money'],
						'card_money '=> $int_sjMoney+$userinfo['card_money'],
						'frozen_money'=> 0,
						'rank_points' =>0,
						'pay_points'=>0,						
					 ),
				' user_id = '.$account['user_id']
		);		
		
		$data = $db->get_one('SELECT * FROM ecs_users where user_id = '.$account['user_id']);
		
	}
}
else {
	
}
