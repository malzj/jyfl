<?php

/* 检查数据是否存在 */
function hasData( $table = 'venues', $find=null, $field="*" )
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
function findData( $table='venues', $find=null, $field="*")
{
	$where = ' WHERE 1 ';
	if ( !is_null($find) )
	{
		$where .= " AND ".$find;
	}		
	
	return $GLOBALS['db']->getAll( 'SELECT '.$field.' FROM '.$GLOBALS['ecs']->table($table). $where);
}

/**  
 * 更新多条件表
 * @param   array   要更新的数据
 * @param   array   要更新的字段
 * @param   string  要更新的数据表
 * @param   string  要更新的where条件
 * 
 */
 function editRows( $data, $cols, $table, $wh)
 {
     if ( !empty($data))
     {
         foreach ($data as $row)
         {            
             // 处理更新的数据
             $sets = '';
             foreach ($cols as $col)
             {
                 if ( empty($sets) )
                    $sets = $col.'="'.$row[$col].'"';
                 else 
                    $sets .= ','.$col.'="'.$row[$col].'"';
             }
             // 更新条件的处理
             $where = '';
             foreach ($wh as $whe)
             {
                 if (empty($where))
                    $where = $whe.'="'.$row[$whe].'"';
                 else 
                    $where .= ' AND '.$whe.'="'.$row[$whe].'"';
             }             
            update($sets, $where, $table);
         }
     }
 }

/**
 *  设置场馆状态 
 *  @param  string  $venueId    场馆id
 *  @param  string  $se         状态，yes上架，no下架
 */
 function setVenuesSale($venueId, $se)
 {
      switch ($se)
      {
          case 'yes': update('is_sale=0', 'venueId='.$venueId, 'venues');   break;
          case 'no':  update('is_sale=1', 'venueId='.$venueId, 'venues');   break;
      }
 }
/**
 * 更新数据
 * 
 * @param	$set		str		更新的数据（is_komovie=0, is_dzq=0, is_brush=0）
 * @param   $where		str		更新的条件
 * @param   $table      str     更新的数据表
 */
function update( $set, $where, $table )
{
	return $GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table($table)." SET ".$set." WHERE ".$where);
}
/**
 * 删除数据
 */
function drop( $table, $where)
{
	return $GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table($table)." WHERE ".$where);
}

/* 保存场馆信息 */
function saveData($data)
{
	// 数据列
	$cols = array( 
	    'venueId',     	    'venueName', 
	    'place',       	    'tel400', 
	    'sportType', 	    'sportName', 
	    'score',       	    'lon', 
	    'lat',         	    'cityId',      	    
	    'cityName',    	    'area_id', 
	    'signImg', 	        'filedDesc', 
	    'feature',     	    'content',
	    'img',         	    'stime',
	    'etime',       	    'trafficScore',
	    'serviceScore',	    'priceScore',
	    'environmentScore', 'equipment',
	    'is_ticket',	    'is_venue',
	    'is_sale',          'salePrice'
	);
	// 数据按键排序
	$updata = arrayOnlySort($data, $cols);
	// 组织成sql语句
	$insert = generateInsert( $updata );		
	// 执行插入操作	
	$GLOBALS['db']->query(' INSERT INTO '.$GLOBALS['ecs']->table('venues')." ( ".implode(',', $cols)." ) VALUES ". $insert);
}

/* 保存场馆产品信息 */
function saveVenueData($data)
{
    // 数据列
    $cols = array(
        'infoId',     	    'infoTitle',
        'isConfirm',       	'marketPrice',
        'salePrice', 	    'ticketDesc',
        'date',       	    'venueNum',
        'week',         	'type',
        'venueId'  	    
    );
    // 数据按键排序
    $updata = arrayOnlySort($data, $cols);
    // 组织成sql语句
    $insert = generateInsert( $updata );
    // 执行插入操作
    $GLOBALS['db']->query(' INSERT INTO '.$GLOBALS['ecs']->table('venues_ticket')." ( ".implode(',', $cols)." ) VALUES ". $insert);
}

/*
 * 获得指定数据的id
 */
function getIds( $data , $col)
{
    $return = array();
    foreach ( (array)$data as $row)
    {
        $return[] = $row[$col];
    }
    return $return;
}

/** 删除一条数据 
 * @param   array   $data     一维数组，值为要删除的id数据
 * @param   string  $col      条件字段
 * @param   string  $table    执行的数据库表名
 */
function dropRows( $data=array(), $col, $table, $wh='')
{
    if ( !empty($data))
    {
        foreach ($data as $id)
        {
            if (!empty($wh)){
                $where = $col.'="'.$id.'" AND '.$wh;
            }else{
                $where = $col.'='.$id;
            }
            drop($table, $where);
        }
    }
}
/**  
 * 添加一条数据
 * @param   array   $data     所有数据  
 * @param   array   $ids      一维数组，值为要更新的id数据
 * @param   string  $col      条件字段
 * @param   string  $fun      处理数据的字符串函数      
 */
function updateRows( $data=array(), $ids=array(), $col, $fun)
{
    $apiArray = array();
    if ( !empty($data) && !empty($ids) )
    {        
        foreach ($data as $row)
        {
            if (in_array( $row[$col], $ids))
            {
                $apiArray[] = $row;
            }
        }
        if ( !empty($apiArray) )
        {
            $fun($apiArray);
        }
    }
}
/**  
 *  通过地址抓取区信息，并返回对应的区id放到，否则返回0
 */ 
function getAreaId($cityid, $place, $col='dongwang_id')
{
    $returnAreaid = 0;
    $regions = findData('region','parent_id ='.$cityid);
    if (!empty($regions))
    {
        foreach ($regions as $region)
        {
            $searchValue = $region['region_name'];
            if (strpos($place, $searchValue) !== false)
            {
                $returnAreaid = $region[$col];
            }
        }        
    }
    return $returnAreaid;
}
/**  
 * 
 */ 
function initVenues( &$data, $push)
{
    foreach ($data as &$row)
    {
        $row = array_merge($row, $push);
    }
}

/**  
 * 处理数组中的指定数据，返回处理后的数据
 * @param   array   $data   要处理的数据
 * @param   array   $dorpData   处理的数据
 * @param   string  $col    条件字段
 */
function initDUData( $data=array(), $ids=array(), $col)
{
    $return = array();
    if (!empty($dorpData))
    {
        $return = $data;
    }
    else {
        foreach ($data as $row)
        {
            if (!in_array( $row[$col], $ids))
            {
                $return[] = $row;
            }
        }
    }
    return $return;
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
			$insertString .= "'$val',";
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
