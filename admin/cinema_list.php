<?php

/**
 * 影院管理
 * 在线选座、电子兑换券、刷卡影院列表，支持的影院都会在这里显示
 * @var unknown_type
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

$exc   = new exchange($ecs->table("cinema_list"), $db, 'id', 'cinema_name');

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
//-- 影院列表页面
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    $smarty->assign('ur_here',     '影院列表');  
    $smarty->assign('full_page',  1);
 
    $list = get_cinema();
	$tmpRegion = get_regions(0);
	
    $smarty->assign('cinema_list',     $list['cinema']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
	$smarty->assign('cinema_nav',   1);
	$smarty->assign('region',		$tmpRegion);
    assign_query_info();
    $smarty->display('cinema_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $list = get_cinema();

    $smarty->assign('cinema_list',  $list['cinema']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    
    make_json_result($smarty->fetch('cinema_list.htm'), '',
        array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}

/*------------------------------------------------------ */
//-- 影院编辑页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    $id = $_REQUEST['id'] ? intval($_REQUEST['id']) : 0 ;
    $cinema = $GLOBALS['db']->getRow("SELECT *,komovie_cinema_id as cinema_id FROM ".$GLOBALS['ecs']->table('cinema_list')." WHERE id=".$id);
    if (empty($cinema))
    {
    	$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
    	sys_msg('非法操作', 0, $link);
    }
    
    // 支持的影院列表
    $cinemas = array();
    // 支持电子券
    if ($cinema['is_dzq'] == 1)
    {
    	$cinemas[] = array( 'typename'=> '电子券', 'type'=>'d', 'list'=> $GLOBALS['db']->getRow('SELECT * FROM '.$GLOBALS['ecs']->table('cinema_update')." WHERE cinema_id = '".$cinema['dzq_cinema_id']."' "));
    }
    // 支持刷卡影院
	if ($cinema['is_brush'] == 1)
    {
    	$cinemas[] = array( 'typename'=> '线下刷卡', 'type'=>'b', 'list'=> $GLOBALS['db']->getRow('SELECT *,title as cinema_name, address as cinema_address, article_id as cinema_id FROM '.$GLOBALS['ecs']->table('yingyuan')." WHERE article_id = '".$cinema['brush_cinema_id']."' "));
    }
    
    // 支持在线选座 TODO 如果支持在线选座，就直接调用cinema_list中的 cinema_name 和 cinema_address, 
    // 这样做存在一些问题，当这个影院是从电子券过来的，那它的cinema_name和cinema_address是电子券的，而不是在线选座的，
    if ($cinema['is_komovie'] == 1)
    {
    	$cinemas[] = array( 'typename'=> '在线选座', 'type'=>'k', 'list'=>$cinema );
    }
	
    $province_list = get_regions(0, 0);
    $city_list     = get_regions(1, $cinema['region_id']);
    
    $smarty->assign('province_list', $province_list);
    $smarty->assign('city_list',     $city_list);
    
	$smarty->assign('cinemas', $cinemas);
	$smarty->assign('cinema', $cinema);
    assign_query_info();
    $smarty->display('cinema_list_info.htm');
}

/*------------------------------------------------------ */
//-- 影院编辑的处理
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'update')
{
	$searchCinemaid = empty($_REQUEST['search_cinema_name']) ? 0 : trim($_REQUEST['search_cinema_name']);
	$source = in_array( $_REQUEST['source'], array(0, 1, 2) ) ? $_REQUEST['source'] : -1 ;
	$id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
	
	
	if ( empty($searchCinemaid) || empty($id) )
	{
		$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
		sys_msg('非法操作', 0, $link);
	}
	
	$cinema = $GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['ecs']->table('cinema_update')." WHERE cinema_id = '$searchCinemaid'");
	
	// 更新影院状态
	switch ( $source)
	{
		// 设置支持在线选座
		case 0:
			$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('cinema_list')." SET is_komovie = 1, komovie_region_id = '".$cinema['api_region_id']."', komovie_area_id = '".$cinema['api_area_id']."', komovie_cinema_id ='".$searchCinemaid."' WHERE id = '".$id."'");
			break;
		// 设置支持电子券
		case 1:	  
			$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('cinema_list')." SET is_dzq = 1, dzq_region_id = '".$cinema['api_region_id']."', dzq_area_id = '".$cinema['api_area_id']."', dzq_cinema_id ='".$searchCinemaid."' WHERE id = '".$id."'");  
			break;
		// 设置支持线下刷卡
		case 2:
			$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('cinema_list')." SET is_brush = 1, brush_cinema_id ='".$searchCinemaid."' WHERE id = '".$id."'");
			break;
		default:			
			break;
	}
	
	// 更新影院状态为已归档
	$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('cinema_update')." SET is_update = 2 WHERE cinema_id = '".$searchCinemaid."'");
	
	ecs_header('Location:cinema_list.php?act=edit&id='.$id);
}

/**
 *  TODO 影院搜索的都是 cinema_update中的影院，区别于 source（0 在线选座，1 电子券，2 线下刷卡）
 *  这样做是为了后期后维护，但要将原始的数据加入到 cinema_list中的话，就要未归档里面操作，才能加到cinema_list 中。
 *  否则是找不这个影院的
 */
elseif ($_REQUEST['act'] == 'searchCinema')
{
	$jsonArray = array( 'html'=> '', 'error' => 0, 'message' => '' );
	$source = in_array( $_REQUEST['source'], array(0, 1, 2) ) ? $_REQUEST['source'] : -1 ;
	$key = $_REQUEST['key'] ? trim($_REQUEST['key']) : '';
	$id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
	
	if (empty($key))
	{
		$jsonArray = array( 'error'=> 1, 'message'=>'请输入影院关键字！' );
		exit(json_encode($jsonArray));
	}
	if ($source == -1)
	{
		$jsonArray = array( 'error'=> 1, 'message'=>'请选择一个有效的来源！' );
		exit(json_encode($jsonArray));
	}
	
	if ($id > 0)
	{
		$cinema = $GLOBALS['db']->getRow("SELECT is_komovie,is_dzq,is_brush FROM ".$GLOBALS['ecs']->table('cinema_list')." WHERE id=".$id);
		if ($source == 0 && $cinema['is_komovie'] == 1)
		{
			$jsonArray = array( 'error'=> 1, 'message'=>'《在线选座》影院已经存在了，不能完成此操作！' );
			exit(json_encode($jsonArray));
		}
		if ($source == 1 && $cinema['is_dzq'] == 1)
		{
			$jsonArray = array( 'error'=> 1, 'message'=>'《电子券》影院已经存在了，不能完成此操作！' );
			exit(json_encode($jsonArray));
		}
		if ($source == 2 && $cinema['is_brush'] == 1)
		{
			$jsonArray = array( 'error'=> 1, 'message'=>'《线下刷卡》影院已经存在了，不能完成此操作！' );
			exit(json_encode($jsonArray));
		}			
	}
	
	$html = '';
	
	// 在线选座	
	$cinema = $GLOBALS['db']->getAll("SELECT * FROM ".$GLOBALS['ecs']->table('cinema_update')." WHERE source = ".$source." AND is_update IN(0,1) AND cinema_name like '%".$key."%'");
	foreach ($cinema as $value)
	{
		$html .='<option value="'.$value['cinema_id'].'">'.$value['cinema_name'].'-----【'.$value['cinema_address'].'】</option>';
	}
	$jsonArray = array('html' => $html);
	exit(json_encode($jsonArray));
	
	
}
/*------------------------------------------------------ */
//-- 设置影院支持的状态（在线选座、电子券、线下刷卡）
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'cinemaState')
{
	$sourceCinemaid = empty($_REQUEST['cinema_id']) ? '' : trim($_REQUEST['cinema_id']);
	$source_cinemaid = explode('-', $sourceCinemaid);
	
	if ( in_array( $source_cinemaid[0], array( 'd' ,'b', 'k')))   // d 电子券  b 线下刷卡 k 在线选座
	{
		// 电子券
		if ( $source_cinemaid[0] == 'd')
		{
			// 电子券影院号
			$ciname_id = $GLOBALS['db']->getOne("SELECT dzq_cinema_id FROM ".$GLOBALS['ecs']->table('cinema_list')." WHERE id = ".$source_cinemaid[1]);
			// 设置影院不支持电子券，并删除电子券影院号
			$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('cinema_list').' SET is_dzq = 0, dzq_cinema_id=0 WHERE id =' .$source_cinemaid[1]);
			// 修改电子券影院，为未归档
			$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('cinema_update').' SET is_update = 0 WHERE cinema_id ="'.$ciname_id.'"');
		}
		// 在线选座
		if ( $source_cinemaid[0] == 'k')
		{
			// 在线选座影院号
			$ciname_id = $GLOBALS['db']->getOne("SELECT komovie_cinema_id FROM ".$GLOBALS['ecs']->table('cinema_list')." WHERE id = ".$source_cinemaid[1]);
			// 设置影院不支持在线选座，并删除在线选座影院号
			$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('cinema_list').' SET is_komovie = 0, komovie_cinema_id = 0 WHERE id =' .$source_cinemaid[1]);
			// 修改在线选座影院，为未归档
			$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('cinema_update').' SET is_update = 0 WHERE cinema_id ="'.$ciname_id.'"');
		}
		// 线下刷卡 TODO 状态未修改呢
		if ( $source_cinemaid[0] == 'b')
		{
			$ciname_id = $GLOBALS['db']->getOne("SELECT brush_cinema_id FROM ".$GLOBALS['ecs']->table('cinema_list')." WHERE id = ".$source_cinemaid[1]);
			$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('cinema_list').' SET is_brush = 0 WHERE id =' .$source_cinemaid[1]);
			$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('yingyuan').' SET is_update = 0 WHERE article_id ="'.$ciname_id.'"');
		}
		
		ecs_header('Location:cinema_list.php?act=edit&id='.$source_cinemaid[1]);
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
	if(!$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('cinema_list')." SET region_id = '".$province."', area_id = '".$city."' WHERE id = ".$id))
	{
		$jsonArray = array( 'error'=>1, 'message'=>'操作失败');
		exit(json_encode($jsonArray));
	}
	$jsonArray = array( 'error'=>0, 'message'=>'保存成功');
	exit(json_encode($jsonArray));
}

/*------------------------------------------------------ */
//-- 删除广告位置
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    $id = intval($_GET['id']);
    $exc->drop($id);

    admin_log('', 'remove', 'cinema');

    $url = 'cinema_list.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}

/* 获取影院列表 */
function get_cinema()
{
    /* 过滤查询 */

    $filter = array();
    $filter['sort_by']    = empty($_REQUEST['sort_by']) ? ' update_time ' : trim($_REQUEST['sort_by']);
    $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
    $filter['source'] = !empty($_REQUEST['source']) ? intval($_REQUEST['source']) : -1;
    $filter['keyword'] = !empty($_REQUEST['keyword']) ? trim($_REQUEST['keyword']) : '';
    $filter['region_id'] = !empty($_REQUEST['region_id']) ? trim($_REQUEST['region_id']) : '';

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
    /* 获得总记录数据 */
    $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('cinema_list') . $where;
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    $filter = page_and_size($filter);

    /* 获得广告数据 */
    $arr = array();
    $sql = 'SELECT * FROM ' .$GLOBALS['ecs']->table('cinema_list'). $where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];

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