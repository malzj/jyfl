<?php

/**  
 *  得到所有城市
 */
function get_yanchu_region()
{
	return findData('region', 'yanchu_id > 0');
}

/** 
 *  得到所有演出数据（数据里里）
 */
function get_yanchu_list($where)
{
	return findData('yanchu_list', $where);
}

/**
 * 配置接口调用参数
 * 
 * @param	$rid	int		城市id
 * @param	$cid	int		项目id
 * @param   $page	int		当前页数
 */
function get_search_param($rid, $cid, $page=1)
{
	return array('cityId'=> $rid,'cateId' => $cid,'nowsale' => '','page' => $page);
}

/** 
 * 得到指定数据列表 
 * 
 * @param 	$table	string	数据表
 * @param 	$find	string	查询条件
 * @param	$field	string	返回字段
 */
function findData( $table='yanchu_list', $find=null, $field="*")
{
	$where = ' WHERE 1 ';
	if ( !is_null($find) )
	{
		$where .= " AND ".$find;
	}		
	
	return $GLOBALS['db']->getAll( 'SELECT '.$field.' FROM '.$GLOBALS['ecs']->table($table). $where);
}

/**  
 *  检查数组 $update 是否在 $insert 中， 如果存在不做合并，不存在合并。 
 */

function merge_data($insert, $update, $type)
{
	// 返回的完整数据
	$returnData = $insert;
	// 整理数据
	$init = _initDate($update, $type);
	// 如果当前更新的数据一条信息都没有，返回 $update数据
	if (empty($insert))
	{
		return $init; 
	}
	
	// 合并$insert 个 $update 数据
	foreach ($init as $itemid=>$items)
	{
		// 不存在insert中，添加
		if(!array_key_exists($itemid, $insert))
		{
			$returnData[$itemid] = $items;
		}
		// 存在，就合并 endDate;
		else{
			$returnData[$itemid]['endDate'] = array_merge($returnData[$itemid]['endDate'], $items['endDate']);
		}
	}
	
	return $returnData;	
	
}

/**
 *  整理数据，只要有用的数据
 */
function _initDate($data, $type)
{
	$returnData = array();
	if ( isset($data['itemName']) )
	{
		$data = array(0 => $data);
	}
	foreach ($data as $key=>$val)
	{
		if (array_key_exists( $val['itemId'], $returnData))
		{
			$returnData[$val['itemId']]['endDate'][] 	= $val['endDate'];
		}			
		else
		{
			$returnData[$val['itemId']]['item_id'] 		= $val['itemId'];
			$returnData[$val['itemId']]['item_name'] 	= $val['itemName'];
			$returnData[$val['itemId']]['time_type'] 	= $val['timeType'];
			$returnData[$val['itemId']]['site_name'] 	= $val['site']['@attributes']['siteName'];
			$returnData[$val['itemId']]['site_id'] 		= $val['site']['@attributes']['siteId'];
			$returnData[$val['itemId']]['thumb'] 		= $val['imageUrl'];
			$returnData[$val['itemId']]['ifShow'] 		= $val['ifShow'];
			$returnData[$val['itemId']]['status'] 		= $val['status'];
			$returnData[$val['itemId']]['type'] 		= $type;
			
			$returnData[$val['itemId']]['endDate'][] 	= $val['endDate'];
		}		
	}
	
	return $returnData;	
}


/** 演出标题 */
function get_title( $id = null ){
	
	$title = array(
		'1217' => '演唱会',
		'1220' => '话剧',
		'1218' => '音乐会',
		'1211' => '体育赛事',
		'1227' => '亲子儿童',
		'1224' => '戏曲综艺'
	);
	
	if (is_null($id))
	{
		return $title;
	}
	
	return $title[$id];
}
/**
 *  删除影院
 */
function dropItme( $items )
{
	drop( 'yanchu_list', 'item_id IN ('.implode(',', $items).')' );
}

/**
 * 删除数据
 */
function drop( $table, $where)
{
	return $GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table($table)." WHERE ".$where);
}

/* 保存新增的演出 */
function updateData($data)
{
	$data = _substitute($data);
	// 数据列
	$cols = array( 'item_id', 'item_name', 'time_type', 'site_name', 'site_id', 'thumb', 'ifShow', 'status', 'start_date', 'end_date', 'city_id', 'type');
	// 数据按键排序
	$updata = arrayOnlySort($data, $cols);
	// 组织成sql语句
	$insert = generateInsert( $updata );
	// 执行插入操作
	$GLOBALS['db']->query(' INSERT INTO '.$GLOBALS['ecs']->table('yanchu_list')." ( ".implode(',', $cols)." ) VALUES ". $insert);
}

function _substitute($data)
{
	$returnDate = array();
	foreach ($data as $key=>$val)
	{
		$endDate = $val['endDate'];
		unset($val['endDate']);
		$val['start_date'] = min($endDate);
		$val['end_date'] = max($endDate);
		$returnDate[] = $val;
	}
	
	return $returnDate;
}



/* 数组生成insert格式的数据  */
function generateInsert( $insert )
{
	$insertString = '';
	foreach ( $insert as $value)
	{
		$insertString .= '(';
		foreach ($value as $key=>$val)
		{
			$insertString .=  !is_array($val) ? "'$val'," : "''," ;
		}
		$insertString = substr($insertString, 0 , strlen($insertString)-1);
		$insertString .='),';
	}

	return substr($insertString, 0 , strlen($insertString)-1);
}

function arrayOnlySort( $sortArray, $sortKey)
{
	$returnArray = array();

	foreach ($sortArray as $key=>$val)
	{
		if ( is_array($val) )
		{
			foreach ($sortKey as $sk=>$sv)
			{
				$returnArray[$key][$sv] = addslashes(@$val[$sv]);
			}
		}		
	}
	return $returnArray;
}
