<?php
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

?>