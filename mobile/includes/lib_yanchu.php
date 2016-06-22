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
 * @param              $size    一页显示多少条数据
 * @param              $page    第几页
 * @param              $find    附加条件
 */
function get_ticket_list($cid, $rid, $size, $page, $find=null, $order='id ASC')
{
    if ($find !== null)
        $find = ' AND '.$find;
    
	$sql = 'SELECT * FROM '.$GLOBALS['ecs']->table('yanchu_list'). " WHERE city_id = '".$rid."' AND type = '".$cid."' $find  ORDER BY $order";
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
 * @param              $find    附加条件
 */
function get_yanchu_count($cid, $rid, $find)
{
    if ($find !== null)
        $find = ' AND '.$find;
    
	$sql = 'SELECT count(*) FROM '.$GLOBALS['ecs']->table('yanchu_list'). " WHERE city_id = '".$rid."' AND type = '".$cid."' $find ORDER BY id ASC";
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

// 演出类型对应的连接地址
function get_yanchu_back($id)
{
    $backUrl = array(
        '1217' => 'yanchu.php?id=1217',
        '1220' => 'yanchu.php?id=1220',
        '1218' => 'yanchu.php?id=1218',
        '1211' => 'yanchu.php?id=1211',
        '1227' => 'yanchu.php?id=1227',
        '1224' => 'yanchu.php?id=1224'
    );
    return $backUrl[$id];
}

/* 演出收货地址验证 */
function check_consignee($consignee)
{
    $region = findData('area_region', "region_id='".$consignee['province']."'");
    if (empty($region))
        return false;
    else
        return true; 
}
?>