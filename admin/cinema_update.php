<?php

/**
 * 更新列表，未归档列表
 * @var unknown_type
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$exc   = new exchange($ecs->table("cinema_update"), $db, 'id', 'cinema_name');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 更新列表 页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',    '更新列表');  
    $smarty->assign('full_page',  1);
 
    $list = get_cinema_update(1);
	$region = get_regions(0);
	
    $smarty->assign('cinema_list',     $list['cinema']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
	$smarty->assign('cinema_nav',   2);
	$smarty->assign('region',		$region);
    assign_query_info();
    $smarty->display('cinema_update_list.htm');
}
/*------------------------------------------------------ */
//-- 未归档列表 页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'archiveList')
{
	$smarty->assign('ur_here',    '未归档列表');
	$smarty->assign('full_page',  1);

	$list = get_cinema_update(0);
	$region = get_regions(0);

	$smarty->assign('cinema_list',     $list['cinema']);
	$smarty->assign('filter',       $list['filter']);
	$smarty->assign('record_count', $list['record_count']);
	$smarty->assign('page_count',   $list['page_count']);
	$smarty->assign('cinema_nav',   3);
	$smarty->assign('region',		$region);
	assign_query_info();
	$smarty->display('cinema_update_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{	
	// 通过来源，判断要得到的是什么数据（未归档列表，更新列表）
	$refer = $_SERVER['HTTP_REFERER'];
	$arr = explode('=', $refer);
	if ($arr[1] == 'archiveList')
		$is_update = 0;
	else 
		$is_update = 1;
		
	$list = get_cinema_update($is_update);

	$smarty->assign('cinema_list',  $list['cinema']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    
    make_json_result($smarty->fetch('cinema_update_list.htm'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 影院编辑页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    $id = $_REQUEST['id'] ? intval($_REQUEST['id']) : 0 ;
    $cinema = $GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['ecs']->table('cinema_update')." WHERE id=".$id);
    if (empty($cinema))
    {
    	$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
    	sys_msg('非法操作', 0, $link);
    }
    
    $province_list = get_regions(0, 0);
    $city_list     = get_regions(1, $cinema['region_id']);
    
    $smarty->assign('province_list', $province_list);
    $smarty->assign('city_list',     $city_list);
    
	$smarty->assign('cinema', $cinema);
    assign_query_info();
    $smarty->display('cinema_update_info.htm');
}

/*------------------------------------------------------ */
//-- 影院编辑的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update')
{
	$searchCinemaid = empty($_REQUEST['search_cinema_name']) ? 0 : trim($_REQUEST['search_cinema_name']);
	$id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
	$insert = empty($_REQUEST['insert']) ? 2 : intval($_REQUEST['insert']);
	$source = in_array($_REQUEST['source'], array(0 ,1, 2)) ? intval($_REQUEST['source']) : -1 ;
	
	if ( $insert == 1 && $searchCinemaid != -1 )
	{
		$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
		sys_msg('搜索结果中没有数据，才可以选择是否添加（是）！', 0, $link);
	}	
	
	if ($searchCinemaid == 0 || ($insert == 2 && $searchCinemaid == -1))
	{
		$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
		sys_msg('请在搜索结果中选择一个影院，如果没有影院请搜索!', 0, $link);
	}
	
	$cinema = $GLOBALS['db']->getRow('SELECT * FROM '.$GLOBALS['ecs']->table('cinema_update')." WHERE id = ".$id);
	
	// 来源不一样，输入非法操作
	if ($cinema['source'] != $source)
	{
		$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
		sys_msg('非法操作!', 0, $link);
	}
	// 更新影院状态
	if ($insert == 1)
	{
		// 基本信息
		$update['cinema_name'] 		= $cinema['cinema_name'];
		$update['cinema_address'] 	= $cinema['cinema_address'];
		$update['cinema_tel'] 		= $cinema['cinema_tel'];
		$update['latitude'] 		= $cinema['latitude'];
		$update['longitude'] 		= $cinema['longitude'];
		$update['area_name'] 		= $cinema['area_name'];
		$update['source'] 			= $cinema['source'];
		$update['region_id'] 		= $cinema['region_id'];
		$update['area_id'] 			= $cinema['area_id'];
		$update['drive_path'] 		= $cinema['drive_path'];
		$update['update_time'] 		= gmtime();
		
		// 设置电子券信息
		if ($source == 1)
		{
			$update['dzq_cinema_id'] 	= $cinema['cinema_id'];
			$update['dzq_region_id'] 	= $cinema['api_region_id'];
			$update['dzq_area_id'] 		= $cinema['api_area_id'];
			$update['is_dzq'] 			= 1;
		}
		// 设置线下刷卡信息
		if ($source == 2)
		{
			$update['brush_cinema_id'] 		= $cinema['cinema_id'];			
			$update['is_brush'] 			= 1;
		}
		// 设置在线选座信息
		if ($source == 0)
		{
			$update['komovie_cinema_id'] 	= $cinema['cinema_id'];
			$update['komovie_region_id'] 	= $cinema['api_region_id'];
			$update['komovie_area_id'] 		= $cinema['api_area_id'];
			$update['is_komovie'] 			= 1;
		}
		
		$GLOBALS['db']->query("INSERT INTO ".$GLOBALS['ecs']->table('cinema_list')." (".implode(',',array_keys($update)).") VALUES('".implode('\',\'',array_values($update))."')");
	}
	else
	{
		switch ($source)
		{
			case 0:		$set = ' is_komovie = 1, komovie_cinema_id = "'.$cinema['cinema_id'].'"'; break;
			case 1:		$set = ' is_dzq = 1, dzq_cinema_id = "'.$cinema['cinema_id'].'"'; break;
			case 2:		$set = ' is_brush = 1, brush_cinema_id = "'.$cinema['cinema_id'].'"'; break;
		}		
		
		$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('cinema_list')." SET ".$set." WHERE id = '$searchCinemaid'");
	}
	
	$exc->edit('is_update = 2',$id);
	
	$link[] = array('text' => $_LANG['go_back'], 'href' => 'cinema_update.php?act=list');
	sys_msg('操作完成', 0, $link);
}

elseif ($_REQUEST['act'] == 'searchCinema')
{
	$jsonArray = array( 'html'=> '', 'error' => 0, 'message' => '' );	
	$key = $_REQUEST['key'] ? trim($_REQUEST['key']) : '';
	
	if (empty($key))
	{
		$jsonArray = array( 'error'=> 1, 'message'=>'请输入影院关键字！' );
		exit(json_encode($jsonArray));
	}	
	
	$html = '';
	
	// 在线选座
	$cinema = $GLOBALS['db']->getAll("SELECT * FROM ".$GLOBALS['ecs']->table('cinema_list')." WHERE cinema_name like '%".$key."%'");
	foreach ($cinema as $value)
	{
		$seat = '';
		if ( !empty($value['is_komovie']) )
			$seat .= '<font color=red>【座】</font>';
		if ( !empty($value['is_dzq']) )
			$seat .= '<font color=red>【券】</font>';
		if ( !empty($value['is_brush']) )
			$seat .= '<font color=red>【刷】</font>';
			
		$html .='<option value="'.$value['id'].'">'.$value['cinema_name'].'----【'.$value['cinema_address'].'】----'.$seat.'</option>';
	}
	$jsonArray = array('html' => $html);
	exit(json_encode($jsonArray));
}
/*------------------------------------------------------ */
//-- 设置影院为未归档
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'cinemaState')
{
	$id = empty($_REQUEST['id']) ? '' : trim($_REQUEST['id']);
	
	if ( !empty( $id ) )   // 设置影院状态为未归档
	{		
		
		$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('cinema_update').' SET is_update = 0 WHERE id =' .$id);
	
		ecs_header('Location:cinema_update.php?act=list');
	} 
	else
	{
		$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
		sys_msg('非法操作', 0, $link);
	}
}
/*------------------------------------------------------ */
//-- 保存城市、地区
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'saveRegion')
{
	$province = !empty($_REQUEST['province']) ? intval($_REQUEST['province']) : 0 ;
	$city = !empty($_REQUEST['city']) ? intval($_REQUEST['city']) : 0 ;
	$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;

	$jsonArray = array( 'html'=> '', 'error' => 0, 'message' => '' );

	if ( empty($province) || empty($city) || empty($id))
	{
		$jsonArray = array( 'error'=>1, 'message'=>'缺少参数！');
		exit(json_encode($jsonArray));
	}
	if(!$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('cinema_update')." SET region_id = '".$province."', area_id = '".$city."' WHERE id = ".$id))
	{
		$jsonArray = array( 'error'=>1, 'message'=>'操作失败');
		exit(json_encode($jsonArray));
	}
	$jsonArray = array( 'error'=>0, 'message'=>'保存成功');
	exit(json_encode($jsonArray));
}
/* 获取影院列表 */
function get_cinema_update($ext)
{
    /* 过滤查询 */

    $filter = array();
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? ' add_time ' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    $filter['source'] =  $_REQUEST['is_ajax'] ? intval($_REQUEST['source']) : -1 ;
    $filter['keyword'] = !empty($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
    $filter['region_id'] = !empty($_REQUEST['region_id']) ? trim($_REQUEST['region_id']) : '';
    $filter['is_update'] = $ext;

    $where = 'WHERE 1 ';
    if ($filter['source'] !=-1)
    {
        $where .= " AND source = ".$filter['source'];
    }
	
    if (!empty($filter['keyword']))
    {
    	$where .= " AND cinema_name like '%".$filter['keyword']."%'";
    }
    
	if (!empty($filter['region_id']))
    {
    	$where .= " AND region_id = ".$filter['region_id'];
    }
    
    $where .= " AND is_update = " . $filter['is_update'];
    
    /* 获得总记录数据 */
    $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('cinema_update') . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    $filter = page_and_size($filter);

    /* 获得广告数据 */
    $arr = array();
    $sql = 'SELECT * FROM ' .$GLOBALS['ecs']->table('cinema_update'). $where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];

    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    $region = adjustRegion();
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
    	$rows['region_name'] = !empty($region[$rows['region_id']]) ? $region[$rows['region_id']] : '<font color=red>无</font>';
    	$rows['area_name'] = !empty($region[$rows['area_id']]) ? $region[$rows['area_id']] : '<font color=red>无</font>';
        $arr[] = $rows;
    }
	
    return array('cinema' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

function adjustRegion()
{
	$returnRegion = array();
	$region = $GLOBALS['db']->getAll('SELECT region_id, region_name FROM ' . $GLOBALS['ecs']->table('region'));
	foreach ($region as $list)
	{
		$returnRegion[$list['region_id']] = $list['region_name'];
	}
	return $returnRegion;
}
?>