<?php
/**
 * 
 * 演出 计划任务
 * 
 * 根据城市选择去更新数据，每个接口执行完，休眠5秒在去执行，避免频繁调用接口，被列入黑名单。
 * 
 * 
 * 
 */

define('IN_ECS', true);
define('EXT_TIME', 5);		// 每次接口执行后休眠多久在执行

include_once(dirname(__FILE__) . '/lib/_init.php');
include_once(dirname(__FILE__) . '/lib/function.php');

include_once(ROOT_PATH . 'includes/lib_cardApi.php');

set_time_limit(0);

$regions = get_yanchu_region();

if (!empty($regions))
{
	// 项目类型ID
	$itemType = array_keys(get_title());
	// 针对每个城市单独更新，
	foreach ($regions as $region)
	{
		// 以城市为单位更新数据，将一个城市所有的数据加载完后，执行insert一次
		$insertData = array();
		foreach ($itemType as $k=>$type)
		{
			// search params
			$params = get_search_param($region['yanchu_id'], $type);
			$obj_result = getYCApi($params, 'search');			
			$arr_result = object2array($obj_result);
			// 检查并合并数据
			$insertData = merge_data($insertData, $arr_result['items'],$type);			
			// 一共多少页
			$pageCount = (int)$arr_result['@attributes']['PageSize'];
			// 当前多少页
			$currentPage = (int)$arr_result['@attributes']['PageIdx'];
			// 如果不只一页，就循环得到剩余页数的数据
			if ($pageCount > $currentPage)
			{
				for($i=$currentPage+1; $i<=$pageCount; $i++)
				{
					$params = get_search_param($region['yanchu_id'], $type, $i);
					$obj_result = getYCApi($params, 'search');
					$arr_result = object2array($obj_result);

					// 检查并合并数据
					$insertData = merge_data($insertData, $arr_result['items'], $type);
					// 每个接口调用需要停止5秒钟
					sleep(EXT_TIME);					
				}
			}			
		}
		
		
		// 当前城市在数据库中的演出数据
		$yanchuList = get_yanchu_list('city_id = '.$region['yanchu_id']);
		// 数据库中的item_id集合
		$data_ids = $post_ids = array();
		
		foreach ($yanchuList as $da)
		{
			$data_ids[] = $da['item_id'];
		} 
		foreach ($insertData as $up)
		{
			$post_ids[] = $up['item_id'];
		}
		// 要删除的item_ids 		
		$deleteItemids = array_diff($data_ids, $post_ids);
		// 更新的item_ids
		$updateItemids = array_diff($post_ids, $data_ids);

		// 如果有删除的演出，则删除
		if (!empty($deleteItemids))
		{
			dropItme($deleteItemids);
		}
		if (!empty($updateItemids))
		{
			// 删除数组中存在的数据
			foreach ($insertData as $key=>&$val)
			{
				if (!in_array($key, $updateItemids))
				{
					unset($insertData[$key]);
				}
				// 把城市id加入到数组中
				$val['city_id'] = $region['yanchu_id'];
				
			}
			// 更新数据
			updateData($insertData);
		}		
	}
}


