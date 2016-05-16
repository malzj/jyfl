<?php
/********************
* getIpCity根据给定访问者ip得到相对应的城市
* $str_cityid 给定当前城市的id号
********************/

function getIpCity($int_cityid = 0){
	$is_first = 1;//是否为第一次访问
	$str_card = $_SESSION['user_name'];
	if (!empty($int_cityid)){
		//$arr_cityInfo = $GLOBALS['db']->getRow('SELECT r.*, IFNULL(c.city_sort, 0) as city_sort, c.is_hot, c.is_home, c.city_desc, c.time FROM '.$GLOBALS['ecs']->table('region').' r LEFT JOIN '. $GLOBALS['ecs']->table('city_template')." c ON c.area_id = r.region_id WHERE region_id = '$int_cityid'");
		$arr_cityInfo = $GLOBALS['db']->getRow('SELECT * FROM '.$GLOBALS['ecs']->table('region')." WHERE region_id = '$int_cityid'");
		$is_first = 0;
	}else{
		$str_realIp = real_ip();
		//$str_realIp = '114.80.166.240';//上海

		list($ip1, $ip2, $ip3, $ip4) = explode(".", $str_realIp); 
		$str_ips  = $ip1 * pow(256, 3) + $ip2 * pow(256, 2) + $ip3 * 256 + $ip4;
		$str_sql  = "SELECT city FROM ".$GLOBALS['ecs']->table('ip')." WHERE start <= $str_ips AND end >= $str_ips ORDER BY start DESC LIMIT 1";
		$str_cityName = $GLOBALS['db']->getOne($str_sql);
		$int_cityid = $GLOBALS['db']->getOne('SELECT region_id FROM '.$GLOBALS['ecs']->table('region')." WHERE region_name = '$str_cityName'");
		if (empty($int_cityid)){
			$int_cityid = $GLOBALS['db']->getOne('SELECT region_id FROM '.$GLOBALS['ecs']->table('region')." WHERE region_name = '北京'");//默认北京
		}
		//$arr_cityInfo = $GLOBALS['db']->getRow('SELECT r.*, IFNULL(c.city_sort, 0) as city_sort, c.is_hot, c.is_home, c.city_desc, c.time FROM '.$GLOBALS['ecs']->table('region').' r LEFT JOIN '. $GLOBALS['ecs']->table('city_template')." c ON c.area_id = r.region_id WHERE region_id = '$int_cityid'");
		$arr_cityInfo = $GLOBALS['db']->getRow('SELECT * FROM '.$GLOBALS['ecs']->table('region')." WHERE region_id = '$int_cityid'");
	}
	$arr_cityInfo['isFirst'] = $is_first;

	return $arr_cityInfo;
}

function getCityList(){
	$arr_cityList = Cache('city_list', '', 1800);
	if (empty($arr_cityList)){
		$arr_citys = $GLOBALS['db']->getAll('SELECT r.*, IFNULL(c.city_sort, 0) as city_sort, IFNULL(c.is_hot, 0) as is_hot, IFNULL(c.is_home, 0) as is_home FROM '.$GLOBALS['ecs']->table('region').' r LEFT JOIN '. $GLOBALS['ecs']->table('city_template')." c ON c.area_id = r.region_id WHERE parent_id = 0 ORDER BY r.region_english ASC, c.city_sort ASC, r.region_id ASC");
		if ($arr_citys){
			$arr_cityList = array(
				'hot'    => array(),
				'ABCDE' => array(),
				'FGHIJ'   => array(),
				'KLMNO'   => array(),
				'PQRST' => array(),
				'VWXYZ'   => array()
			);
			foreach ($arr_citys as $key=>$var){
				/*$str_defaulturl = $_SERVER['PHP_SELF'] ? substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'],'/')+1) : 'index.php';
				$str_queryParam = !empty($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
				if (strpos($str_queryParam, 'cityid') !== false){
					$str_queryParam = preg_replace('/^(.*)(cityid=\d+)$/i', '\\1cityid='.$var['region_id'], $str_queryParam);
				}else{
					$str_queryParam = !empty($str_queryParam) ? $str_queryParam . '&cityid=' . $var['region_id'] : 'cityid=' . $var['region_id'];
				}
				$var['url']     = $str_defaulturl.'?'.$str_queryParam;*/
				$var['firstZM'] = strtoupper(substr($var['region_english'], 0, 1));
				if (!empty($var['is_hot'])){
					$arr_cityList['hot'][$var['region_id']] = $var;
				}

				if (in_array($var['firstZM'], array('A', 'B', 'C', 'D', 'E'))){
					$arr_cityList['ABCDE'][$var['firstZM']][$var['region_id']] = $var;
				}else if (in_array($var['firstZM'], array('F', 'G', 'H', 'I', 'J'))){
					$arr_cityList['FGHIJ'][$var['firstZM']][$var['region_id']] = $var;
				}else if (in_array($var['firstZM'], array('K', 'L', 'M', 'N', 'O'))){
					$arr_cityList['KLMNO'][$var['firstZM']][$var['region_id']] = $var;
				}else if (in_array($var['firstZM'], array('P', 'Q', 'R', 'S', 'T'))){
					$arr_cityList['PQRST'][$var['firstZM']][$var['region_id']] = $var;
				}else if (in_array($var['firstZM'], array('V', 'W', 'X', 'Y', 'Z'))){
					$arr_cityList['VWXYZ'][$var['firstZM']][$var['region_id']] = $var;
				}
			}
			Cache('city_list', $arr_cityList);
		}
	}

	return $arr_cityList;
}

function Cache($name, $value='', $time = 0, $path = '') {
	$int_time = $time ? $time : $GLOBALS['_CFG']['cache_time'];//判断文件存活时间
	static $_cache  = array();
	$filename       = ROOT_PATH . 'temp/static_caches/' . ($path ? $path : '') . $name . '.php';
	if ('' !== $value) {
		if (is_null($value)) {
			// 删除缓存
			return false !== strpos($name,'*')?array_map("unlink", glob($filename)):unlink($filename);
		} else {
			// 缓存数据
			$dir            =   dirname($filename);
			// 目录不存在则创建
			if (!is_dir($dir))
				mkdir($dir,0755,true);
			$_cache[$name]  =   $value;
			return file_put_contents($filename, "<?php\treturn " . var_export($value, true) . ";?>");
		}
	}

	if (isset($_cache[$name])){
		return $_cache[$name];
	}
	// 获取缓存数据
	if (is_file($filename)) {
		$int_fileTime = filemtime($filename);//获取文件创建时间
		if ($int_time < 0){
			$value         = include $filename;
			$_cache[$name] = $value;
		}else{
			if ($int_fileTime + $int_time > gmtime()) {
				$value         = include $filename;
				$_cache[$name] = $value;
			}else{
				$value = false;
			}
		}
	} else {
		$value = false;
	}
	return $value;
}