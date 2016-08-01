<?php

/**
 * ECSHOP 地区切换程序
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: region.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);
define('INIT_NO_USERS', true);
//define('INIT_NO_SMARTY', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/cls_json.php');

header('Content-type: text/html; charset=' . EC_CHARSET);
error_log(var_export(12313,true),'3','error.log');
$type   = !empty($_REQUEST['type'])   ? intval($_REQUEST['type'])   : 0;
$parent = !empty($_REQUEST['parent']) ? intval($_REQUEST['parent']) : 0;
$supp = !empty($_REQUEST['supp']) ? intval($_REQUEST['supp']) : 0;

$cityInfo = get_regions($type, $parent);

$content = '';

if ( $supp == 1 && $type == 2){
	$cityIds = array();
	foreach ($cityInfo as $city){  $cityIds[] = $city['region_id'];}
	array_push($cityIds, $parent);	
	
	$sql = "SELECT a.region_id, r.region_name" .
			" FROM " . $GLOBALS['ecs']->table('area_region'). " AS a" .
			" LEFT JOIN " . $GLOBALS['ecs']->table('region'). " AS r" .
			" ON r.region_id = a.region_id" .
			" LEFT JOIN " . $GLOBALS['ecs']->table('shipping_area'). " AS s" .
			" ON a.shipping_area_id = s.shipping_area_id" .
			" WHERE a.region_id IN (".implode(',',$cityIds).")" .
			" ORDER by a.region_id DESC";
	$regionInfo = $GLOBALS['db']->getAll($sql);
	
	if( $regionInfo != false)
	{
		$content = $regionInfo;
	}
	
	$first = current($regionInfo);
	if ( $first['region_id'] == $parent){
		$content = array(array( 'region_id' => -2 , 'region_name' => '所有地区'));
	}	
}
else {
	$content = $cityInfo;
}

$arr['regions'] = $content;
$arr['supp'] = $supp;
$arr['type']    = $type;
$arr['target']  = !empty($_REQUEST['target']) ? stripslashes(trim($_REQUEST['target'])) : '';
$arr['target']  = htmlspecialchars($arr['target']);

$json = new JSON;
echo $json->encode($arr);

?>