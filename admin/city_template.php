<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ($_REQUEST['act'] == 'list'){
	//$arr_cityList = get_regions();
	$smarty->assign('ur_here',      $_LANG['city_template_manage']);
	$smarty->assign('full_page',    1);

	$arr_cityList = getCityList();

	$smarty->assign('citylist',     $arr_cityList['city']);
	$smarty->assign('filter',       $arr_cityList['filter']);
	$smarty->assign('record_count', $arr_cityList['record_count']);
	$smarty->assign('page_count',   $arr_cityList['page_count']);
	$sort_flag  = sort_flag($arr_cityList['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);

	assign_query_info();
	$smarty->display('city_template.htm');
}


/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query'){
	$arr_cityList = getCityList();
	$smarty->assign('citylist',     $arr_cityList['city']);
	$smarty->assign('filter',       $arr_cityList['filter']);
	$smarty->assign('record_count', $arr_cityList['record_count']);
	$smarty->assign('page_count',   $arr_cityList['page_count']);
	$sort_flag  = sort_flag($arr_cityList['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	make_json_result($smarty->fetch('city_template.htm'), '', array('filter' => $arr_cityList['filter'], 'page_count' => $arr_cityList['page_count']));
}

/*------------------------------------------------------ */
//-- 编辑城市内容
/*------------------------------------------------------ */
else if ($_REQUEST['act'] == 'edit'){
	$smarty->assign('ur_here',      $_LANG['city_template_manage']);
	$smarty->assign('full_page',    1);

	$int_cityid = intval($_REQUEST['id']);
	$arr_cityInfo = $db->getRow('SELECT r.*, IFNULL(c.city_sort, 0) as city_sort, c.city_desc, IFNULL(c.is_hot, 0) as is_hot, IFNULL(c.is_home, 0) as is_home, IFNULL(c.time, 0) as time FROM '.$GLOBALS['ecs']->table('region').' r LEFT JOIN '. $GLOBALS['ecs']->table('city_template')." c ON c.area_id = r.region_id WHERE region_id = '$int_cityid'");
	
	//导航信息配载
	if (empty($arr_cityInfo['navinfo'])){
		$arr_cityInfo['navinfo'] = array('0' => array('nav_id'=>'', 'start_xshd'=>'', 'end_xshd'=>'', 'start_ychd'=>'', 'end_ychd'=>''));
	}else{
		$arr_cityInfo['navinfo'] = unserialize($arr_cityInfo['navinfo']);
		foreach ($arr_cityInfo['navinfo'] as $key=>$var){
			if (!empty($var['nav_id'])){
				$var['arr_nav_id'] = explode(',', $var['nav_id']);
				foreach($var['arr_nav_id'] as $k=>$v){
					$var['selected'][$v] = ' selected="selected"';
				}
			}
			$arr_cityInfo['navinfo'][$key] = $var;
		}
	}
	$smarty->assign('cityinfo',   $arr_cityInfo);

	$arr_nav = $db->getAll('SELECT * FROM '.$ecs->table('nav')." WHERE ifshow = 1 ORDER BY type DESC, vieworder ASC");
	$smarty->assign('navlist', $arr_nav);

	

	/* 创建 html editor */
	include_once(ROOT_PATH . 'includes/fckeditor/fckeditor.php'); // 包含 html editor 类文件
	$editor = new FCKeditor('city_desc');
	$editor->BasePath   = '../includes/fckeditor/';
	$editor->ToolbarSet = 'Normal';
	$editor->Width      = '100%';
	$editor->Height     = '320';
	$editor->Value      = $arr_cityInfo['city_desc'];
	$FCKeditor = $editor->CreateHtml();
	$smarty->assign('FCKeditor', $FCKeditor);


	assign_query_info();
	$smarty->display('city_template_info.htm');
}

/*------------------------------------------------------ */
//-- 编辑操作城市内容
/*------------------------------------------------------ */
else if ($_REQUEST['act'] == 'update'){
	$int_cityid   = intval($_POST['id']);
	$int_order    = intval($_POST['city_sort']);
	$str_citydesc = $_POST['city_desc'];
	$int_hot      = intval($_POST['is_hot']);
	$int_home     = intval($_POST['is_home']);
	$time         = !empty($_POST['time']) ? $_POST['time'] : 3;
	$int_areaid = (int) $db->getOne('SELECT area_id FROM '.$ecs->table('city_template')." WHERE area_id = '$int_cityid'");
	
	/*$arr_navSetting = array();
	if (!empty($_POST['nav_id'])){
		foreach ($_POST['nav_id'] as $key=>$var){
			$arr_navSetting[$key]['nav_id']     = implode(',', $var);
			$arr_navSetting[$key]['start_xshd'] = $_POST['start_xshd'][$key];
			$arr_navSetting[$key]['end_xshd']   = $_POST['end_xshd'][$key];
			$arr_navSetting[$key]['start_ychd'] = $_POST['start_ychd'][$key];
			$arr_navSetting[$key]['end_ychd']   = $_POST['end_ychd'][$key];
		}
	}*/
	
	if (!empty($int_areaid)){
		$query = $db->query('UPDATE '.$ecs->table('city_template')." SET city_sort = '$int_order', is_hot = '$int_hot', is_home = '$int_home', city_desc = '$str_citydesc', time = '$time' WHERE area_id = '$int_cityid'");
	}else{
		$str_sql = "INSERT INTO ".$ecs->table('city_template')." (area_id, city_sort, is_hot, is_home, city_desc, time) VALUES ('$int_cityid','$int_order', '$int_hot', '$int_home', '$int_citydesc', '$time')";
		$query = $db->query($str_sql);
	}

	if ($query){
		/* 清除缓存 */
		clear_cache_files();
		$link[0]['text'] = '列表页';
		$link[0]['href'] = 'city_template.php?act=list&' . list_link_postfix();
		sys_msg('编辑成功', 0, $link);
	}else{
		die($db->error());
	}
}

/*------------------------------------------------------ */
//-- 编辑排序序号
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_sort_order')
{
	$int_id     = intval($_POST['id']);
	$int_order  = intval($_POST['val']);


	$int_areaid = (int)$db->getOne('SELECT area_id FROM '.$ecs->table('city_template')." WHERE area_id = '$int_id'");
	if (!empty($int_areaid)){
		$query = $db->query('UPDATE '.$ecs->table('city_template')." SET city_sort = '$int_order' WHERE area_id = '$int_id'");
	}else{
		$str_sql = "INSERT INTO ".$ecs->table('city_template')." (area_id, city_sort) VALUES ('$int_id','$int_order')";
		$query = $db->query($str_sql);
	}

	if ($query){
		/* 清除缓存 */
		clear_cache_files();
		make_json_result($int_order);
	}else{
		$str_name = $db->getOne('select region_name from '.$ecs->table('region')." WHERE region_id = '$int_id'");
		make_json_error(sprintf($_LANG['cityedit_fail'], $str_name));
	}
}

/*------------------------------------------------------ */
//-- 是否热门
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_hot')
{
	$int_id     = intval($_POST['id']);
	$int_status = intval($_POST['val']);

	$int_areaid = (int)$db->getOne('select area_id from '.$ecs->table('city_template')." WHERE area_id = '$int_id'");
	if (!empty($int_areaid)){
		$db->query('update '.$ecs->table('city_template')." set is_hot = '$int_status' where area_id = '$int_id'");
	}else{
		$str_sql = "INSERT INTO ".$ecs->table('city_template')." (area_id, is_hot) VALUES ('$int_id', '$int_status')";
		$db->query($str_sql);
	}
	/* 清除缓存 */
	clear_cache_files();
	make_json_result($int_status);
}

/*------------------------------------------------------ */
//-- 是否home页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_home')
{
	$int_id     = intval($_POST['id']);
	$int_status = intval($_POST['val']);

	$int_areaid = (int)$db->getOne('select area_id from '.$ecs->table('city_template')." WHERE area_id = '$int_id'");
	if (!empty($int_areaid)){
		$db->query('update '.$ecs->table('city_template')." set is_home = '$int_status' where area_id = '$int_id'");
	}else{
		$str_sql = "INSERT INTO ".$ecs->table('city_template')." (area_id, is_home) VALUES ('$int_id', '$int_status')";
		$db->query($str_sql);
	}
	make_json_result($int_status);
}


function getCityList(){
	$result = get_filter();
	if ($result === false){
		/* 分页大小 */
		$filter = array();
		$filter['city_name'] = !empty($_REQUEST['city_name']) ? trim($_REQUEST['city_name']) : '';
		$where = 'r.parent_id = 0';
		if (!empty($filter['city_name'])){
			$where .= " AND region_name like '%". mysql_like_quote($filter['city_name']) . "%'";
		}
		$str_sql = 'SELECT COUNT(*) FROM '.$GLOBALS['ecs']->table('region').' r LEFT JOIN '. $GLOBALS['ecs']->table('city_template')." c ON c.area_id = r.region_id WHERE $where";
		$filter['record_count'] = $GLOBALS['db']->getOne($str_sql);
		$filter['sort_by']      = empty($_REQUEST['sort_by'])    ? 'region_id' : trim($_REQUEST['sort_by']);
		$filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'ASC'    : trim($_REQUEST['sort_order']);

		$filter = page_and_size($filter);

		$str_sql = 'SELECT r.*, IFNULL(c.city_sort, 0) as city_sort, c.is_hot, c.is_home, c.city_desc FROM '.$GLOBALS['ecs']->table('region').' r LEFT JOIN '. $GLOBALS['ecs']->table('city_template')." c ON c.area_id = r.region_id WHERE $where ORDER BY $filter[sort_by] $filter[sort_order]";
		set_filter($filter, $str_sql);
	}
	else
	{
		$str_sql    = $result['sql'];
		$filter = $result['filter'];
	}
	$res = $GLOBALS['db']->selectLimit($str_sql, $filter['page_size'], $filter['start']);

	$arr = array();
	while ($rows = $GLOBALS['db']->fetchRow($res)){
		$arr[] = $rows;
	}

	return array('city' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}