<?php

/* 将数组数据回调到指定变量中  */
function cinema_list( $list, &$cinema, $keep=false)
{
	foreach ( $list as $key=>$val)
	{
		if ($keep !== false)
		{
			$cinema[$key] = $val;
		}
		else {
			$cinema[] = $val;
		}
	}
	
	return true;
}

/* 检查数据是否存在 */
function hasData( $table = 'cinema_list', $find=null, $field="*" )
{
	$info = findData( $table, $find, $field);
	return count($info);	
}

/** 
 * 得到指定数据列表 
 * 
 * @param 	$table	string	数据表
 * @param 	$find	string	查询条件
 * @param	$field	string	返回字段
 */
function findData( $table, $find=null, $field="*")
{
	$where = ' WHERE 1 ';
	if ( !is_null($find) )
	{
		$where .= " AND ".$find;
	}		
	
	return $GLOBALS['db']->getAll( 'SELECT '.$field.' FROM '.$GLOBALS['ecs']->table($table). $where);
}

/**
 * 检查这个影院是否支持 刷卡、电子券，有一项支持就返回true，否则 false  
 **/
function checkState( $cinemaid)
{
	$state = false;
	$cinema = $GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['ecs']->table('cinema_list')." WHERE komovie_cinema_id=".$cinemaid);
	if ( $cinema['is_dzq'] || $cinema['is_brush'])
	{
		$state = true;
	}
	return $state;
}

/**
 * 将在线选座设置为不支持
 */
function updateKomovieState($cinemaid ,$state=0)
{
	updateState('is_komovie='.$state, 'komovie_cinema_id='.$cinemaid );
}

/**
 * 将影院的电子券设置为不支持
 */
function updateDzqState($cinemaid, $state=0)
{
	updateState('is_dzq='.$state, 'dzq_cinema_id="'.$cinemaid.'"' );
}

/**
 * 更新影院支持状态
 * 
 * @param	$cinemaid	int		影院id
 * @param	$set		str		更新的数据（is_komovie=0, is_dzq=0, is_brush=0）
 * @param   $where		str		更新的条件
 */
function updateState( $set, $where )
{
	return $GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('cinema_list')." SET ".$set." WHERE ".$where);
}

/**
 *  删除影院
 */
function dropCinemas( $cinemaid )
{
	drop( 'cinema_list', 'komovie_cinema_id='.$cinemaid );
}

/**
 * 删除数据
 */
function drop( $table, $where)
{
	return $GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table($table)." WHERE ".$where);
}

/* 保存抠电影影院 */
function saveKomovieData($data)
{
	// 数据列
	$cols = array( 'cinema_address', 'komovie_cinema_id', 'cinema_name', 'cinema_tel', 'komovie_region_id', 'komovie_area_id', 'area_name', 'latitude', 'longitude', 'region_id', 'area_id', 'is_komovie', 'update_time', 'source','logo', 'galleries', 'cinema_intro', 'drive_path', 'open_time' );
	// 整理数据
	$info = initDataKeys( 'komovie', $data);
	// 添加默认数据
	$info = defaultData( 'komovie', $info);
	// 数据按键排序
	$updata = arrayOnlySort($info, $cols);
	// 组织成sql语句
	$insert = generateInsert( $updata );
	
	//error_log(var_export($insert,true),'3','errorabc.log');
	// 执行插入操作	
	$GLOBALS['db']->query(' INSERT INTO '.$GLOBALS['ecs']->table('cinema_list')." ( ".implode(',', $cols)." ) VALUES ". $insert);
}

/* 保存新增的影院  */
function saveUpdateData($data, $ext='dzq')
{
	// 数据列
	$cols = array( 'cinema_name', 'cinema_address', 'cinema_id', 'region_id', 'area_id', 'api_region_id', 'api_area_id', 'cinema_tel', 'latitude', 'longitude', 'source', 'is_update', 'add_time', 'drive_path', 'area_name', 'logo', 'galleries', 'cinema_intro', 'drive_path', 'open_time' );
	// 整理数据
	$info = initDataKeys( $ext, $data);
	// 添加默认数据
	$info = defaultData( $ext, $info);
	// 数据按键排序
	$updata = arrayOnlySort($info, $cols);
	// 组织成sql语句
	$insert = generateInsert( $updata );

	// 执行插入操作
	$GLOBALS['db']->query(' INSERT INTO '.$GLOBALS['ecs']->table('cinema_update')." ( ".implode(',', $cols)." ) VALUES ". $insert);
}

/* 根据来源，将数组中的键名转成数据表中对应的标识  */
function initDataKeys( $table, $data)
{
	$returnData = array();
	// 数据库列名 = 数据列名
	$dataKeys = array();
	// 抠电影
	if ( $table == 'komovie' )
	{
		$dataKeys['cinema_name'] 		= 'cinemaName';			// 影院名称
		$dataKeys['cinema_address'] 	= 'cinemaAddress';		// 影院地址
		$dataKeys['komovie_cinema_id'] 	= 'cinemaId';			// 影院id
		$dataKeys['cinema_tel'] 		= 'cinemaTel';			// 影院电话
		$dataKeys['komovie_region_id'] 	= 'cityId';				// 城市
		$dataKeys['komovie_area_id'] 	= 'districtId';			// 地区
		$dataKeys['area_name'] 			= 'districtName';		// 地区名称 （在地区id没有值得时候，用地区名称去搜一下，还是没找到就返回0）
		$dataKeys['latitude'] 			= 'latitude';			// 维度
		$dataKeys['longitude'] 			= 'longitude';			// 经度
		$dataKeys['logo'] 			    = 'logo';			    // logo
		$dataKeys['galleries'] 			= 'galleries';			// 图集
		$dataKeys['cinema_intro'] 		= 'cinema_intro';		// 影院简介
		$dataKeys['drive_path'] 		= 'drive_path';			// 交通
		$dataKeys['open_time'] 			= 'open_time';			// 开放时间
		
	}
	// 电子券
	elseif( $table == 'dzq')
	{
		$dataKeys['cinema_name'] 		= 'CinemaName';			// 影院名称
		$dataKeys['cinema_address'] 	= 'Address';			// 影院地址
		$dataKeys['cinema_id'] 			= 'CinemaNo';			// 影院id
		$dataKeys['api_area_id'] 		= 'AreaNo';				// 城区id
		$dataKeys['cinema_tel'] 		= 'PhoneNo';
		$dataKeys['LatLng'] 			= 'LatLng';				// 维度/经度
		$dataKeys['drive_path'] 		= 'Traffic';			// 公交线路
		$dataKeys['area_name'] 			= 'AreaName';			// 区域名称
		$dataKeys['logo'] 			    = 'logo';			    // logo
	}
	// 新增抠电影影院
	elseif( $table == 'update')
	{
		$dataKeys['cinema_name'] 		= 'cinemaName';			// 影院名称
		$dataKeys['cinema_address'] 	= 'cinemaAddress';		// 影院地址
		$dataKeys['cinema_id'] 			= 'cinemaId';			// 影院id
		$dataKeys['api_area_id'] 		= 'districtId';			// 城区id
		$dataKeys['api_region_id'] 		= 'cityId';				// 城市id
		$dataKeys['cinema_tel'] 		= 'cinemaTel';			// 影院电话
		$dataKeys['latitude'] 			= 'latitude';			// 维度
		$dataKeys['longitude'] 			= 'longitude';			// 经度
		$dataKeys['area_name'] 			= 'districtName';		// 区域名称
		$dataKeys['drive_path'] 		= '';
		$dataKeys['logo'] 			    = 'logo';			    // logo
	}
	
	// 替换键名
	if ( empty($dataKeys) )
		return false;	
	
	foreach ($data as $key=>$val)
	{
		foreach ($val as $k=>$v)
		{
			$newKeys = array_search($k, $dataKeys);
			if ($newKeys !== false)
			{
				$returnData[$key][$newKeys] = !empty($v) ? $v : '' ;
			}	
		}
		
		/* 如果 data 缺少某个字段的时候，就设置为空 */
		foreach ($dataKeys as $keys=>$value)
		{
			if ( !array_key_exists($value, $val))
			{
				$returnData[$key][$keys] = '';
			}
		}
	}
	
	return $returnData;
}

/* 初始默认数据 */
function defaultData( $table, $data)
{
	$returnData = array();
	// 抠电影
	if ( $table == 'komovie' )
	{
		foreach ( $data as $key=>$val)
		{
			// 添加城市id
			$returnData[$key]['region_id'] = getRegionId( $table, $val['komovie_region_id']);
			// 添加地区id
			$returnData[$key]['area_id'] = getRegionId( $table, $val['komovie_area_id'], $val['area_name']);
			// 添加支持在线选座
			$returnData[$key]['is_komovie'] = 1;
			// 添加更新时间
			$returnData[$key]['update_time'] = gmtime();
			// 添加来源
			$returnData[$key]['source'] = CINEMA_SOURCE_KOMOVIE;
			// 合并数据
			$returnData[$key] = array_merge($val, $returnData[$key]);
		}
	// 电子券
	}elseif( $table == 'dzq')
	{
		foreach ( $data as $key=>$val)
		{
			$region_id = getPrentRegionId( $table, $val['api_area_id']);
			// 添加城市id
			$returnData[$key]['region_id'] = !empty($region_id) ? $region_id : 0 ;
			// api城市id
			$returnData[$key]['api_region_id'] = getApiRegionId( 'dianying_id', " WHERE region_id = ".$returnData[$key]['region_id']);
			// 添加地区id
			$returnData[$key]['area_id'] = getRegionId( $table, $val['api_area_id'], $val['area_name']);;
			// 添加更新时间
			$returnData[$key]['add_time'] = gmtime();
			// 添加来源
			$returnData[$key]['source'] = CINEMA_SOURCE_DZQ;
			// 添加更新标识
			$returnData[$key]['is_update'] = 1;
			
			// 处理精度和纬度
			if ( !empty($val['LatLng']) )
			{
				$latlng = explode(',', $val['LatLng']);
				$returnData[$key]['latitude'] = $latlng[0];
				$returnData[$key]['longitude'] = $latlng[1];
			}
			// 删除临时精度/维度字符串
			unset($val['LatLng']);
			
			// 合并数据
			$returnData[$key] = array_merge($val, $returnData[$key]);
		}
	
	// 新增抠电影影院
	}elseif( $table == 'update')
	{
		foreach ( $data as $key=>$val)
		{
			// 添加城市id
			$returnData[$key]['region_id'] = getRegionId( $table, $val['cinema_id']);
			// 添加地区id
			$returnData[$key]['area_id'] = getRegionId( $table, $val['cinema_id'], $val['area_name']);
			// 添加支持在线选座
			$returnData[$key]['is_komovie'] = 1;
			// 添加更新时间
			$returnData[$key]['add_time'] = gmtime();
			// 添加来源
			$returnData[$key]['source'] = CINEMA_SOURCE_KOMOVIE;
			// 添加更新标识
			$returnData[$key]['is_update'] = 1;
			// 合并数据
			$returnData[$key] = array_merge($val, $returnData[$key]);
		}
	}
	
	return $returnData;
}

/** 通过 api 的 城市id， 找到对应的城市id
 *  
 *  @param		string		$table	      表名
 *  @param		int			$rid	   api城市id
 *  @param		string		$area_name 城市名
 */
function getRegionId( $table, $rid, $area_name=null)
{
	// 城市为空，返回0；
	if (empty($rid))
		return array('region_id'=>0);
	
	if ($table == 'komovie')
		$where = ' WHERE komovie_id = '.$rid;
	else
		$where = ' WHERE dianying_id = '.$rid;
	
	$region = $GLOBALS['db']->getRow( 'SELECT * FROM '.$GLOBALS['ecs']->table('region'). $where );
	if ( empty($region))
	{
		$region = getRegionName( $area_name );
	}
	
	return $region['region_id'];
}


/* 通过城市名找城市id */
function getRegionName($name)
{
	/* 城市名为空返回城市id为0 */
	if ( $name == null )
	{
		return array( 'region_id'=>0);
	}
	
	$region = $GLOBALS['db']->getRow( 'SELECT * FROM '.$GLOBALS['ecs']->table('region'). ' WHERE region_name = "'.$name.'" AND region_type = 1' );
	if ( empty($region) )
	{
		$regionName = str_replace( array('区','县','镇'), '', $name);
		// 如果存在 县、区、镇，去掉从新获取城市数据
		if (strlen($regionName) !== strlen($name))
		{
			$region = $GLOBALS['db']->getRow( 'SELECT * FROM '.$GLOBALS['ecs']->table('region'). ' WHERE region_name = "'.$regionName.'" AND region_type = 1' );
		}
		else
		{
			$region = array( 'region_id'=>0);
		}
	}
	
	/* TODO 到这一步，其实就意味着没有保存对应的api城市id，是否要在这里更新呢，待续。。。。。  */
	return $region;
}

/* 得到上一级城市id */
function getPrentRegionId( $table, $rid )
{
	// 城市为空，返回0；
	if (empty($rid))
		return array('parent_id'=>0);
	
	if ($table == 'komovie')
		$where = ' WHERE komovie_id = '.$rid;
	else
		$where = ' WHERE dianying_id = '.$rid;
	
	return  $GLOBALS['db']->getOne( 'SELECT parent_id FROM '.$GLOBALS['ecs']->table('region'). $where );
}

/* 根据region_id 得到对应的 api城市id */
function getApiRegionId( $find, $where)
{
	return $GLOBALS['db']->getOne( 'SELECT '.$find.' FROM '.$GLOBALS['ecs']->table('region'). $where );
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
				$returnArray[$key][$sv] = @$val[$sv];
			}
		}		
	}
	return $returnArray;
}
