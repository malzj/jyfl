<?php

	/**
	 * 设置诺心扩展的信息。
	 * 
	 */
function get_extension_info($config, $day)
{
	$timeConfig = array('shipping_start'=>0);
	
	// 如果是明天就不做任何操作
	if ($day == 'tomorrow')
	{
		return $timeConfig;
	}
	
	$afterTime    = strtotime(local_date('Y-m-d').' 12:30:00');
	$currentTime  = strtotime(local_date('Y-m-d H:i:s'));
	// 下单时间超过了，当天中午的12:30, 就不能选择当天的配送时间，将最早配送时间改成最晚配送时间即可
	if($currentTime > $afterTime)
	{
		$timeConfig['shipping_start'] = strtotime(local_date('Y-m-d'))+$config['shipping_end'];
	}
	return $timeConfig;
}
	
