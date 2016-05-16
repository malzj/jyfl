<?php


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
$exc = new exchange($ecs->table('shipping'), $db, 'shipping_code', 'shipping_name');

/*------------------------------------------------------ */
//-- 获取系统安装配送方式列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list'){
	$arr_modules = read_modules('../includes/modules/shipping');
	foreach ($arr_modules as $key=>$var){
		$str_langFile = ROOT_PATH.'languages/' .$_CFG['lang']. '/shipping/' .$var['code']. '.php';
		if (file_exists($str_langFile)){
			include_once($str_langFile);
		}

		//检查该插件是否已经安装并启用
		$str_sql = "SELECT shipping_id, shipping_name, shipping_desc, insure, support_cod,shipping_order FROM " .$ecs->table('shipping'). " WHERE shipping_code='" .$var['code']. "' AND enabled = 1 ORDER BY shipping_order";
		$row = $db->GetRow($str_sql);
		if (!empty($row)){
			/* 插件已经安装了，获得名称以及描述 */
			$var['id']      = $row['shipping_id'];
			$var['name']    = $row['shipping_name'];
			$var['desc']    = $row['shipping_desc'];
			$var['insure_fee']  = $row['insure'];
			$var['cod']     = $row['support_cod'];
			$var['shipping_order'] = $row['shipping_order'];
			$var['install'] = 1;

			if (isset($var['insure']) && ($var['insure'] === false)){
				$var['is_insure']  = 0;
			}else{
				$var['is_insure']  = 1;
			}
			$arr_modules[$key] = $var;
		}else{
			unset($arr_modules[$key]);
		}
	}

	$smarty->assign('ur_here', $_LANG['03_shipping_list']);
	$smarty->assign('modules', $arr_modules);

	assign_query_info();
	$smarty->display('shipping_list.htm');
}