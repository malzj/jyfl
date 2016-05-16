<?php

/* 检查数据是否存在 */
function hasData( $table = 'weixin_pay', $find=null, $field="*" )
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
 *  删除影院
 */
function dropWeixinpay( $id )
{
	drop( 'weixin_pay', 'id ='.$id );
}

/**
 * 删除数据
 */
function drop( $table, $where)
{
	return $GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table($table)." WHERE ".$where);
}
