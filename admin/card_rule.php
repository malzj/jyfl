<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . '/includes/cls_image.php');

if ($_REQUEST['act'] == 'list'){
	//$arr_cityList = get_regions();
	$smarty->assign('ur_here',      $_LANG['zcard_rule']);
	$smarty->assign('full_page',    1);

	$action_link = ($_REQUEST['act'] == 'list') ? array('href' => 'card_rule.php?act=add', 'text' => '添加新规则') : array('href' => 'card_rule.php?act=list', 'text' => '返回列表');
	$smarty->assign('action_link',  $action_link);

	$arr_cardList = getCardRuleList();

	$smarty->assign('cardlist',     $arr_cardList['city']);
	$smarty->assign('filter',       $arr_cardList['filter']);
	$smarty->assign('record_count', $arr_cardList['record_count']);
	$smarty->assign('page_count',   $arr_cardList['page_count']);
	$sort_flag  = sort_flag($arr_cardList['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);

	assign_query_info();
	$smarty->display('card_rule_list.htm');
}


/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query'){
	$arr_cardList = getCardRuleList();
	$smarty->assign('cardlist',     $arr_cardList['city']);
	$smarty->assign('filter',       $arr_cardList['filter']);
	$smarty->assign('record_count', $arr_cardList['record_count']);
	$smarty->assign('page_count',   $arr_cardList['page_count']);
	$sort_flag  = sort_flag($arr_cardList['filter']);
	$smarty->assign($sort_flag['tag'], $sort_flag['img']);
	make_json_result($smarty->fetch('card_rule_list.htm'), '', array('filter' => $arr_cardList['filter'], 'page_count' => $arr_cardList['page_count']));
}

/*------------------------------------------------------ */
//-- 添加,编辑规则内容
/*------------------------------------------------------ */
else if ($_REQUEST['act'] == 'edit' || $_REQUEST['act'] == 'add'){
	$smarty->assign('ur_here',      $_LANG['city_template_manage']);
	$smarty->assign('full_page',    1);

	$action_link = array('href' => 'card_rule.php?act=list', 'text' => '返回列表');
	$smarty->assign('action_link',  $action_link);

	$int_id = intval($_REQUEST['id']);
	if (!empty($int_id)){
		$arr_cardInfo = $db->getRow('SELECT * FROM '.$GLOBALS['ecs']->table('card_rule')." WHERE id = '$int_id'");
	}
	
	//导航信息配载
	if (empty($arr_cardInfo['navinfo'])){
		$arr_cardInfo['navinfo'] = array('0' => array('nav_id'=>'', 'region'=>'', 'shop_ratio'=>1));
	}else{
		$arr_cardInfo['navinfo'] = unserialize($arr_cardInfo['navinfo']);
		$shop_ratio = unserialize($arr_cardInfo['shop_ratio']);
		foreach ($arr_cardInfo['navinfo'] as $key=>$var){
			if (!empty($var['region'])){
				$var['arr_region'] = explode(',', $var['region']);
				foreach($var['arr_region'] as $k=>$v){
					$var['check'][$v] = ' checked="checked"';
				}
			}			
			$var['shop_ratio'] = $shop_ratio[$var['nav_id']];
			$arr_cardInfo['navinfo'][$key] = $var;
		}		
	}

	if (empty($int_id)){
		$arr_nav_moren = $db->getAll('SELECT * FROM '.$ecs->table('nav')." WHERE ifshow = 1 AND type = 'middle' AND is_city!='' ORDER BY type DESC, vieworder ASC");
        	
		foreach ($arr_nav_moren as $key=>$var){
				$var['nav_id'] = $var['id'];
				$var['arr_region'] = unserialize($var['is_city']);

					foreach((array)$var['arr_region'] as $k=>$v){
						$var['check'][$v] = ' checked="checked"';
					}
				//$var['is_city'] = unserialize($var['is_city']);
				$arr_cardInfo['navinfo'][$key] = $var;
			}
		
			//var_dump($arr_cardInfo['navinfo']);exit;

	}
	
	$smarty->assign('cardinfo',   $arr_cardInfo);
	//var_dump($arr_cardInfo);
	$arr_nav = $db->getAll('SELECT * FROM '.$ecs->table('nav')." WHERE ifshow = 1 AND type = 'middle' ORDER BY type DESC, vieworder ASC");
	$smarty->assign('navlist', $arr_nav);
	
	$region_list = get_region();
	$smarty->assign('region_list',$region_list);

	assign_query_info();
	$smarty->display('card_rule_info.htm');
}

/*------------------------------------------------------ */
//-- 编辑操作城市内容
/*------------------------------------------------------ */
else if ($_REQUEST['act'] == 'update'){
	$int_cardid   = intval($_POST['id']);
	$str_homedesc = '';
	$time         = !empty($_POST['time']) ? $_POST['time'] : 3;
	$int_id = (int) $db->getOne('SELECT id FROM '.$ecs->table('card_rule')." WHERE id = '$int_cardid'");
	$str_title = $_POST['title'];
	//$str_Card   = trim($_POST['card']);

	$Card1 = file($_FILES['file']['tmp_name']);
	foreach ($Card1 as $key=>$var){
		$Card[$key] = trim($var);
	}

//var_dump($Card);
//exit;
$str_Card = serialize($Card);
	
	$arr_navSetting = $shop_ratio = array();
	if (!empty($_POST['nav_id'])){
		foreach ($_POST['nav_id'] as $key=>$var){
			if (!empty($var)){
				$arr_navSetting[$key]['nav_id'] 	= $var;
				$arr_navSetting[$key]['region'] 	= implode(',', $_POST['region'][$key]);
				$ratios = trim($_POST['shop_ratio'][$key]);
				if(empty($ratios))
				{
					$ratios = 0;
				}
				$shop_ratio[$var] = $ratios ;
			}
		}
	}

	
	$str_navinfo = !empty($arr_navSetting) ? serialize($arr_navSetting) : '';
	$str_shopratio = !empty($shop_ratio) ? serialize($shop_ratio) : '';
	//var_dump($str_navinfo);exit;
	
	$zhekou = !empty($_REQUEST['zhekou']) ? trim($_REQUEST['zhekou']) : '0';
	$shop = !empty($_REQUEST['shop']) ? trim($_REQUEST['shop']) : '0';
	$pay_than = !empty($_REQUEST['pay_than']) ? trim($_REQUEST['pay_than']) : '0';
	$type = !empty($_REQUEST['type']) ? trim($_REQUEST['type']) : '0';
	
	$ext = !empty($_REQUEST['ext']) ? trim($_REQUEST['ext']) : '0';
	$price = !empty($_REQUEST['price']) ? trim($_REQUEST['price']) : '0.0';  // 实际卡售价
	$raise = !empty($_REQUEST['raise']) ? trim($_REQUEST['raise']) : '0.0';  // 上浮比例
	$merge_limit = !empty($_REQUEST['merge_limit']) ? trim($_REQUEST['merge_limit']) : '1';
	
	
	if (!empty($int_id)){
		if (!empty($Card)){
		$query = $db->query('UPDATE '.$ecs->table('card_rule')." SET title = '$str_title', card = '$str_Card', home_desc = '$str_homedesc',price = '$price', zhekou = '$zhekou', shop = '$shop', time = '$time', navinfo = '$str_navinfo', shop_ratio = '$str_shopratio', type = '$type', raise='$raise', merge_limit='$merge_limit'  WHERE id = '$int_id'");
		}else{
		$query = $db->query('UPDATE '.$ecs->table('card_rule')." SET title = '$str_title', home_desc = '$str_homedesc',price = '$price', zhekou = '$zhekou', shop = '$shop',  time = '$time', navinfo = '$str_navinfo', shop_ratio = '$str_shopratio', pay_than = '$pay_than', type = '$type', raise='$raise', merge_limit='$merge_limit' WHERE id = '$int_id'");
		}
	}else{
		$str_sql = "INSERT INTO ".$ecs->table('card_rule')." (title, card, home_desc,price,zhekou,shop,time, navinfo, shop_ratio, pay_than, type, raise,merge_limit) VALUES ('$str_title','$str_Card', '$str_homedesc', '$price','$zhekou', '$shop','$time', '$str_navinfo', '$str_shopratio', $pay_than, $type, $raise,$merge_limit)";
		$query = $db->query($str_sql);
	}

	if ($query){
		/* 清除缓存 */
		clear_cache_files();
		$link[0]['text'] = '列表页';
		$link[0]['href'] = 'card_rule.php?act=list&' . list_link_postfix();
		sys_msg('操作成功', 0, $link);
	}else{
		die($db->error());
	}
}

//删除操作
elseif ($_REQUEST['act'] == 'del')
{
	$int_id = intval($_GET['id']);
	if (!empty($int_id)){
		$db->query('DELETE FROM '.$ecs->table('card_rule')." WHERE id = '$int_id'");
		
		/* 清除缓存 */
		clear_cache_files();
		$link[0]['text'] = '列表页';
		$link[0]['href'] = 'card_rule.php?act=list&' . list_link_postfix();
		sys_msg('删除成功', 0, $link);
	}else{
		sys_msg('删除失败');
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
/*------------------------------------------------------ */
//-- 规格排除列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'rule_out')
{
	$smarty->assign('ur_here',      '规格排除列表');
	
	$card_id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;
	$supplier_id = !empty($_REQUEST['supplier_id']) ? intval($_REQUEST['supplier_id']) : 0 ;
	$goods_name = !empty($_REQUEST['goods_name']) ? addslashes_deep($_REQUEST['goods_name']) : NULL;
	
	if ($card_id == 0)
	{
		ecs_header('location:card_rule.php?act=list');
	}
	
	$where = 1;
	// 供应商和商品名称筛选
	if ($supplier_id > 0)
	{
		$where .= " AND o.supplier_id = ".$supplier_id;
	}
	if ($goods_name !== NULL)
	{
		$where .= " AND g.goods_name like '%".$goods_name."%'";
	}
	
	$sql = "SELECT g.goods_name,s.*,s.id AS spec_id ,o.* FROM ".$GLOBALS['ecs']->table('card_out')." AS o ".
			" LEFT JOIN ".$GLOBALS['ecs']->table('goods_spec'). " AS s ON s.id = o.spec_id ".
			" LEFT JOIN ".$GLOBALS['ecs']->table('goods'). " AS g ON g.goods_id = s.goods_id ".
			" WHERE ".$where." AND o.card_id = ".$card_id;
	$data = $GLOBALS['db']->getAll($sql);
	$new_data = array();
	if (!empty($data))
	{
		foreach($data as $row){
			$new_data[$row['goods_id']]['goods_name'] = $row['goods_name'];
			$new_data[$row['goods_id']]['spec'][] = $row;
		}
	}
	$smarty->assign('list', $new_data);	
	$smarty->assign('goods_name', $goods_name);
	$smarty->assign('supplier',supplier_list($supplier_id));
	$smarty->assign('card_id', $card_id);
	$smarty->display('rule_out.htm');
}
elseif ($_REQUEST['act'] == 'delete_spec_out')
{
	$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;
	$card_id = !empty($_REQUEST['card_id']) ? intval($_REQUEST['card_id']) : 0 ;
	$goods_id = !empty($_REQUEST['goods_id']) ? intval($_REQUEST['goods_id']) : 0 ;
	
	if ($id == 0 || $card_id == 0 || $goods_id == 0)
	{
		ecs_header('location:card_rule.php?act=rule_out&id='.$card_id);
	}
	// 删除
	$GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table('card_out')." WHERE id=".$id);
	// 不符合条件的商品，删除rule_ids里面的 卡规则id
	if (check_out_spec_num($goods_id,$card_id) == false)
	{
		$is_delete = false;
		$rule_ids = $GLOBALS['db']->getOne("SELECT rule_ids FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id =".$goods_id);
		$rule_ids2 = explode(',', $rule_ids);
		$rule_ids2 = array_filter($rule_ids2);
		$rule_ids2 = array_unique($rule_ids2);
		foreach($rule_ids2 as $kid=>$rid){
			if ($rid == $card_id)
			{
				$is_delete = true;
				unset($rule_ids2[$kid]);
			}
		}

		$new_rule_ids = ','.implode(',', $rule_ids2).',';
		// 如果rule_ids为空的时候，更新商品为空
		if (empty($rule_ids2))
		{
			$new_rule_ids = '';
		}
		
		if ($is_delete == true)
		{
			$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('goods').' SET rule_ids ="'.$new_rule_ids.'" WHERE goods_id='.$goods_id);
		}
	}
	$links[] = array('href'=>'card_rule.php?act=rule_out&id='.$card_id, 'text'=>'返回继续操作');
	sys_msg('删除成功！', 0, $links);
}
/*------------------------------------------------------ */
//-- 规格排除设置
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'set_rule_out')
{
	$smarty->assign('ur_here',      '规格排除设置');
	$card_id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;
	$supplier_id = !empty($_REQUEST['supplier_id']) ? intval($_REQUEST['supplier_id']) : 0 ;
	
	if ($card_id == 0)
	{
		ecs_header('location:card_rule.php?act=list');
	}
	//  添加操作
	if (!empty($_POST['spec_id']))
	{
		
		foreach ($_POST['spec_id'] as $goods_id=>$spec_ids)
		{
			foreach($spec_ids as $sid)
			{
				// 如果存在了，跳出本次循环
				$res = $GLOBALS['db']->query("SELECT id FROM ".$GLOBALS['ecs']->table('card_out')." WHERE spec_id=".$sid." AND card_id=".$card_id);
				//exit($GLOBALS['db']->num_rows($res));
				if ($GLOBALS['db']->num_rows($res) > 0 )
				{
					continue;
				}
					
				$GLOBALS['db']->query("INSERT INTO ".$GLOBALS['ecs']->table('card_out'). " (spec_id, card_id, supplier_id,goods_id) VALUES('".$sid."', '".$card_id."', '".$supplier_id."', '".$goods_id."')");
			}
			// 如果一个商品的所有规格都被这个卡规则排除了，那这个商品对卡规则里面的会员就不显示了。
			if(check_out_spec_num($goods_id,$card_id) == true)
			{
				$rule_ids = $GLOBALS['db']->getOne("SELECT rule_ids FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id=".$goods_id);
				if (empty($rule_ids))
				{
					$new_rule_ids = ','.$card_id.',';
				}
				else
				{
					$new_rule_ids = $rule_ids.$card_id.',';
				}
				$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('goods').' SET rule_ids = "'.$new_rule_ids.'" WHERE goods_id='.$goods_id);
			}
		}				
		
		$links[] = array('href' => 'card_rule.php?act=set_rule_out&id='.$card_id, 'text'=>'返回继续设置');
		sys_msg('设置完成！', 0, $links);
		
	}
	$smarty->assign('card_id', $card_id);
	$smarty->assign('title', card_detail($card_id,'title'));
	$smarty->assign('supplier',supplier_list($supplier_id));
	
	$smarty->display('set_rule_out.htm');
}
/*------------------------------------------------------ */
//-- 商品折扣列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'rule_ratio')
{
	$smarty->assign('ur_here',      '规格折扣列表');
	$card_id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;
	$supplier_id = !empty($_REQUEST['supplier_id']) ? intval($_REQUEST['supplier_id']) : 0 ;
	$goods_name = !empty($_REQUEST['goods_name']) ? addslashes_deep($_REQUEST['goods_name']) : NULL;
	
	if ($card_id == 0)
	{
		ecs_header('location:card_rule.php?act=list');
	}
	
	$where = 1;
	// 供应商和商品名称筛选
	if ($supplier_id > 0)
	{
		$where .= " AND d.supplier_id = ".$supplier_id;
	}
	if ($goods_name !== NULL)
	{
		$where .= " AND g.goods_name like '%".$goods_name."%'";
	}
	
	$sql = "SELECT g.goods_name,g.goods_id,s.*,s.id AS spec_id ,d.* FROM ".$GLOBALS['ecs']->table('card_discount')." AS d ".
			" LEFT JOIN ".$GLOBALS['ecs']->table('goods_spec'). " AS s ON s.id = d.spec_id ".
			" LEFT JOIN ".$GLOBALS['ecs']->table('goods'). " AS g ON g.goods_id = s.goods_id ".
			" WHERE ".$where." AND d.card_id = ".$card_id;
	$data = $GLOBALS['db']->getAll($sql);
	$new_data = array();
	if (!empty($data))
	{
		foreach($data as $row){
			$new_data[$row['goods_id']]['goods_name'] = $row['goods_name'];
			$new_data[$row['goods_id']]['spec'][] = $row;
		}
	}
	/* echo '<pre>';
	print_r($new_data);
	echo '</pre>'; */
	$smarty->assign('list', $new_data);	
	$smarty->assign('goods_name', $goods_name);
	$smarty->assign('supplier',supplier_list($supplier_id));
	$smarty->assign('card_id', $card_id);
	$smarty->display('spec_ratio.htm');
}
/*------------------------------------------------------ */
//-- 商品折扣设置
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'set_spec_ratio')
{
	$smarty->assign('ur_here',      '规格折扣设置');
	$card_id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;
	$supplier_id = !empty($_REQUEST['supplier_id']) ? intval($_REQUEST['supplier_id']) : 0 ;
	
	if ($card_id == 0)
	{
		ecs_header('location:card_rule.php?act=list');
	}
	//  添加操作
	if (!empty($_POST['discount']))
	{						
		foreach($_POST['discount'] as $spec_id=>$discount)
		{
			if ($discount == 0)
			{
				continue;
			}
			// 如果存在了，修改规则比例
			$res = $GLOBALS['db']->query("SELECT id FROM ".$GLOBALS['ecs']->table('card_discount')." WHERE spec_id=".$spec_id." AND card_id=".$card_id);
			//exit($GLOBALS['db']->num_rows($res));
			if ($GLOBALS['db']->num_rows($res) > 0 )
			{
				$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('card_discount'). " SET  discount='".$discount."'  WHERE spec_id ='".$spec_id."' AND card_id = ".$card_id);
			}else{
				// 通过规格找到商品id
				$goods_id = $GLOBALS['db']->getOne("SELECT goods_id FROM ".$GLOBALS['ecs']->table('goods_spec')." WHERE id =".$spec_id);
				$GLOBALS['db']->query("INSERT INTO ".$GLOBALS['ecs']->table('card_discount'). " (spec_id, card_id, discount,goods_id,supplier_id) VALUES('".$spec_id."', '".$card_id."', '".$discount."', '".$goods_id."', '".$supplier_id."')");
			}
		}
	
		$links[] = array('href' => 'card_rule.php?act=set_spec_ratio&id='.$card_id, 'text'=>'返回继续设置');
		sys_msg('设置完成！', 0, $links);
	
	}
	$smarty->assign('card_id', $card_id);
	$smarty->assign('title', card_detail($card_id,'title'));
	$smarty->assign('supplier',supplier_list($supplier_id));
	$smarty->display('set_spec_ratio.htm');
}
elseif ($_REQUEST['act'] == 'delete_spec_ratio')
{
	$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;
	$card_id = !empty($_REQUEST['card_id']) ? intval($_REQUEST['card_id']) : 0 ;
	if ($id == 0 || $card_id == 0)
	{
		ecs_header('location:card_rule.php?act=rule_ratio&id='.$card_id);
	}
	$GLOBALS['db']->query("DELETE FROM ".$GLOBALS['ecs']->table('card_discount')." WHERE id=".$id);
	$links[] = array('href'=>'card_rule.php?act=rule_ratio&id='.$card_id, 'text'=>'返回继续操作');
	sys_msg('删除成功！', 0, $links);
}


function getCardRuleList(){
	$result = get_filter();
	if ($result === false){
		/* 分页大小 */
		$filter = array();
		// $filter['card'] = !empty($_REQUEST['card']) ? trim($_REQUEST['card']) : '';
		$filter['title'] = !empty($_REQUEST['title']) ? trim($_REQUEST['title']) : '';
		$where = '1';
		// if (!empty($filter['card'])){
		// 	$where .= " AND start_card <= '".$filter['card']."' AND end_card >= '".$filter['card']."'";
		// }
		if (!empty($filter['title'])){
			$where .= " AND title like '%".$filter['title']."%'";
		}
		$str_sql = 'SELECT COUNT(*) FROM '.$GLOBALS['ecs']->table('card_rule')." WHERE $where";
		$filter['record_count'] = $GLOBALS['db']->getOne($str_sql);
		$filter['sort_by']      = empty($_REQUEST['sort_by'])    ? 'id' : trim($_REQUEST['sort_by']);
		$filter['sort_order']   = empty($_REQUEST['sort_order']) ? 'ASC'    : trim($_REQUEST['sort_order']);

		$filter = page_and_size($filter);

		$str_sql = 'SELECT * FROM '.$GLOBALS['ecs']->table('card_rule')." WHERE $where ORDER BY $filter[sort_by] $filter[sort_order]";
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
		if (!empty($rows['navinfo'])){
			$rows['navinfo'] = unserialize($rows['navinfo']);
			foreach ($rows['navinfo'] as $key=>$var){
				if ($var['region']){
					$arr_regionName = array();
					$query = $GLOBALS['db']->query('SELECT region_id,region_name FROM '.$GLOBALS['ecs']->table('region')." WHERE region_id IN (".$var['region'].")");
					while($row = $GLOBALS['db']->fetch_array($query)){
						$arr_regionName[$row['region_id']] = $row['region_name'];
					}
				}

				$var['navName']    = $GLOBALS['db']->getOne('SELECT name FROM '.$GLOBALS['ecs']->table('nav')." WHERE id = '".intval($var['nav_id'])."'");
				$var['regionName'] = implode(',', $arr_regionName);
				$rows['navinfo'][$key] = $var;
			}
		}
		$arr[] = $rows;
	}

	return array('city' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}


/*------------------------------------------------------ */
//-- 获得城市列表
/*------------------------------------------------------ */
function get_region(){
	$sql = "SELECT * FROM " .
			$GLOBALS['ecs']->table('region') .
			" WHERE region_type = 0 AND parent_id = 0";
	return $GLOBALS['db']->getAll($sql);
}

/*------------------------------------------------------ */
//-- 获得供应商列表
/*------------------------------------------------------ */
function supplier_list($id){
	
	$sql = "SELECT * FROM ".$GLOBALS['ecs']->table('supplier')." WHERE status=1 AND is_entity=2";
	$data = $GLOBALS['db']->getAll($sql);
	if (!empty($data))
	{
		foreach($data as &$row)
		{
			if ($row['supplier_id'] == $id)
			{
				$row['selected'] = true;
			}
			else {
				$row['selected'] = false;
			}	
		}
	}
	return $data;
}

/*------------------------------------------------------ */
//-- 获得卡规则信息
/*------------------------------------------------------ */
function card_detail($id,$cols=null){
	
	if ($cols == null)
		$cols = 'title';
	return $GLOBALS['db']->getOne("SELECT ".$cols." FROM ".$GLOBALS['ecs']->table('card_rule')." WHERE id = ".$id);
	
}


