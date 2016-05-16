<?php

	/**
	 * 设置味多美扩展的信息。
	 * 
	 * $config	array	配送参数
	 * 					包括：shipping_start		最早配送时间
	 * 						 shipping_end		最晚配送时间
	 * 						 shipping_booking	提前预约的时间
	 */
function get_extension_info($config, $day)
{
	$timeConfig = array('shipping_start'=>0);
	
	$afterTime    = strtotime(local_date('Y-m-d').' 16:00:00');
	$currentTime  = strtotime(local_date('Y-m-d H:i:s'));
	
	$post_date = strtotime($_REQUEST['date']);
	
	// 下单时间超过了，当天的16:00, 配送时间是后天的配送时间，否则是昨天的配送时间
	if($currentTime > $afterTime)
	{		
		$timeConfig['shipping_start'] = strtotime(local_date('Y-m-d'))+$config['shipping_start'];
		// 如果选择的时候，大于等于两天后的时间，显示配送时间段，否则显示不能配送，选择其他日期
		if (strtotime(local_date('Y-m-d')." +2 day") <= $post_date)
		{
			$timeConfig['shipping_end'] = strtotime(local_date('Y-m-d'))+$config['shipping_end'];
		}
		else
		{
			$timeConfig['shipping_end'] = strtotime(local_date('Y-m-d'))+$config['shipping_start'];
		}
	}
	else
	{
		$timeConfig['shipping_start'] = strtotime(local_date('Y-m-d'))+$config['shipping_start'];
		$timeConfig['shipping_end'] = strtotime(local_date('Y-m-d'))+$config['shipping_end'];
	}
	
	//error_log( var_export($timeConfig,true),'3','error.log');
	return $timeConfig;
}
	
