<?php
ini_set('date.timezone','Asia/Shanghai');
error_reporting(E_ERROR);

require_once "../lib/WxPay.Api.php";
require_once '../lib/WxPay.Notify.php';
require_once 'log.php';

//初始化日志
$logHandler= new CLogFileHandler("../logs/".date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);		
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			require_once '../lib/db.php';
			$db = new DB();
			$row = $db->get_one('SELECT * FROM ecs_weixin_pay WHERE log_id ='.$result['out_trade_no']);
			if (!$row){
				$db->insert('ecs_weixin_pay', array(
										'id '			=> null,
										'log_id '		=> $result['out_trade_no'],
										'transaction_id'=> $result['transaction_id']));
			}
			return true;
			// 支付成功更新订单状态，
			/*require_once '/includes/lib_payment.php';
			require_once '/includes/lib_cardApi.php';
			error_log(dirname(__FILE__) . '../../../includes/init.php',3,'error.log');
			// 修改订单状态
			order_paid($result['out_trade_no'], '', '', $result['transaction_id']);
			// 卡延期操作，如果是中影卡执行延期，华影卡不执行
			
			 $pay_log = $GLOBALS['db']->getOne("SELECT order_id FROM " . $GLOBALS['ecs']->table('pay_log') ." WHERE log_id = '$result[out_trade_no]'");
			$user_id = $GLOBALS['db']->getOne('SELECT user_id FROM ' . $GLOBALS['ecs']->table('user_account') .  " WHERE `id` = '$pay_log' LIMIT 1");
			$user_name = $GLOBALS['db']->getOne('SELECT user_name FROM '.$GLOBALS['ecs']->table('users'). " WHERE user_id = ".$user_id);
			
			delay($user_name); */
			
		}
		return false;
	}
	
	// 数据操作操作
	public function dataProcess( $result ){		
		
		define('IN_ECS', TRUE);
		require_once '../lib/db.php';
		$db = new DB();	
		
		$row = $db->get_one('SELECT * FROM ecs_pay_log WHERE log_id = '.$result['out_trade_no']);
		
		// 未支付，继续操作
		if ($row && $row['is_paid']==0 && $row['order_type'] == 1)
		{
			// 更新支付状态为已支付
			$db->update('ecs_pay_log', array( 'is_paid'=> 1 ), ' log_id = '.$result['out_trade_no']);
			// 会员充值记录
			$account = $db->get_one('SELECT * FROM ecs_user_account WHERE `id` = '.$row['order_id']);
			
			// 修改充值记录状态 
			if ($account && $account['is_paid'] == 0)
			{
				$db->update('ecs_user_account', array('paid_time'=> time(), 'is_paid'=>1, 'pay_no'=> $result['transaction_id']), ' id = '.$row['order_id']);
				
				/** TODO 充值操作 **/
				// 通过卡规则设置的充值售比计算出充值的点数，默认是1.3
				$pay_than = 1.3;
				// 得到卡规则id，如果由的话				
				$userinfo = $db->get_one('SELECT * FROM ecs_users where user_id = '.$account['user_id']);
				$card_id = $userinfo['card_id'];
					
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
					include_once '../../../includes/lib_cardApi.php';			
					if ($int_sjMoney > 0 ){
						//审核充值操作
						$arr_param = array(
								'orderId'    => $account['order_sn'],
								'operId'     => $GLOBALS['_CFG']['operId'],
								'extendInfo' => ''
						);
						$arr_cardInfo = getCardApi($arr_param, 'CARD-AUTH-RECHARGE', 7);
						if ($arr_cardInfo['ReturnCode'] != '0'){
							Log::DEBUG("message:".$arr_cardInfo['ReturnMessage'].' cardno:'.$userinfo['user_name']. 'time:'.date('Y-m-d H:i:s', time()));
						}
					}
				}
				// 华影卡，充值操作
				else
				{
					// 卡密码和卡号					
					include_once '../../../includes/lib_huayingcard.php';
					include_once '../../../includes/httpRequest.php';
					// 实例化卡系统接口
					$cardPay = new huayingcard();
					$arr_param = array(
							'CardInfo' => array( 'CardNo'=> $userinfo['user_name']),
							'TransationInfo' => array( 'TransRequestPoints'=>$int_sjMoney)
					);
					$state = $cardPay->action($arr_param, 6);
					if ($state == 1)
					{
						Log::DEBUG("message:".$cardPay->getMessage().' cardno:'.$userinfo['user_name']. 'time:'.date('Y-m-d H:i:s', time()));
					}
					
					Log::DEBUG("state:".$state);
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
			}
		}
				
		return true;
	}
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{		
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}
		return true;
	}
}

$notify = new PayNotifyCallBack();
$notify->Handle(false);
