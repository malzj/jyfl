<?php 

/**
 * ==========================
 *    演出票函数
 * ==========================
 */

/**
 * 演出列表
 * @param unknown_type $cid		分类id
 * @param unknown_type $rid		城市id
 */
function get_ticket_list($cid, $rid, $size, $page)
{
	$sql = 'SELECT * FROM '.$GLOBALS['ecs']->table('yanchu_list'). " WHERE city_id = '".$rid."' AND type = '".$cid."' ORDER BY id ASC";
	$res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
	$arr = array();
	while ($row = $GLOBALS['db']->fetchRow($res))
	{	
		// 如果只有一场演出，直接显示当前价格
		if ($row['start_date'] == $row['end_date'])
		{
			$row['data_ext'] = date("Y-m-d",$row['end_date']);
		}
		else 
		{
			$row['data_ext'] = date("Y-m-d",$row['start_date']).' ~ '.date("Y-m-d",$row['end_date']);
		}
		$arr[$row['id']] = $row;
	}
	return $arr;
}

/**
 * 列表数据统计
 * @param unknown_type $cid		分类id
 * @param unknown_type $rid		城市id
 */
function get_yanchu_count($cid, $rid)
{
	$sql = 'SELECT count(*) FROM '.$GLOBALS['ecs']->table('yanchu_list'). " WHERE city_id = '".$rid."' AND type = '".$cid."' ORDER BY id ASC";
	return $GLOBALS['db']->getOne($sql);
}


function get_title($id){
	
	$title = array(
			'1217' => '演唱会',
			'1220' => '话剧',
			'1218' => '音乐会',
			'1211' => '体育赛事',
			'1227' => '亲子儿童',
			'1224' => '戏曲综艺'
	);
	return $title[$id];
}
?>