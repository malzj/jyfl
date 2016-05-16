<?php

/**
 *   手机端首页
 *   -------------------------------------------------------------------
 *   一、banner 显示
 *   二、导航显示
 *   三、楼层数据（首页数据集合）
 *   四、为你精心挑选
 *   -------------------------------------------------------------------
 *   导航显示：
 *   	1、对导航的排序，修改 $sortKey 里面的值，将改变导航的显示顺序，它也是导航过滤的一个
 *   	2、主导航连接地址修改成二级导航第一个的地址
 *   	3、导航添加小图片，以导航ID命名，需手动添加到制定位置
 *   
 *   楼层数据（首页数据集合）：
 *   	1、$indexCheck 设置了首页要显示那些数据内容，添加新的到数组即可，参考他的写法。
 *   	2、广告数据的使用，大部分是广告数据*   
 */

define('IN_ECS', true);

/*   排序的keys 
 *   排序的顺序，一维数组的键名  6 视听盛宴 7 舌尖上的美食 8 运动激情 37 生活服务 105 团队建设  9智慧之门
 *   这里设置的导航也必会在首页显示，如果不想让某个导航不现实，这里不写就可以了
 */
$sortKeys = array(6,7,8,37,105,9,108);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');

assign_template();

$navigator = get_navigator();

// 导航数据
$middle = $navigator['middle'];

// 过滤不在手机端显示的导航
init_wap_middle($middle);

// 导航数组id
$nav_ids = array();

foreach($middle as $nav)
{
	/* 将所有的导航id放到 $nav_ids中， 以便于下面对首页块权限的控制。 **/
	$nav_ids[] = $nav['id'];
	if ( !empty($nav['child']) )
	{
		foreach ($nav['child'] as $nav2)
		{
			$nav_ids[] = $nav2['id'];			
		}
	}
}

// banner 广告图
$smarty->assign('indexslice',index_ad('indexslice', $nav_ids));

/* 首页导航菜单的排序 */
$sortKey = array( 'id' , $sortKeys );   
$navList = array_only_sort($middle, $sortKey, 'val');

/* 导航小图片 / 连接地址设置 */
foreach ($navList as $navKey=>&$list)
{
	$childs = array_shift_nav( $list['child'] );
	
	// 删除没有子类的主导航
	if ( empty($childs) )
	{
		unset($navList[$navKey]);
		continue;
	}
	// 设置主导航的连接地址
	$list['url'] = $childs['url'];
	if ($childs['id'] == 12)
	{
		$list['url'] = "cinema.php";
	}
	
	// 导航小图片设置
	$list['icon'] = 'images/icon/'.$list['id'].'.png';
	unset($list['child']);
}

/**
 *	首页数据集合，
 *	check   检测是否有该导航权限， true 有， false 否 （默认是false）
 *	list 	该导航的要显示的数据信息。
 */
$indexCheck = array(
	'movie' 	=> array( 'check' => false, 'list'=>array()),		// 电影
	'yanchu' 	=> array( 'check' => false, 'list'=>array()),		// 演出
	'tuan'		=> array( 'check' => false, 'list'=>array()),		// 团队建设
	'noxin'		=> array( 'check' => false, 'list'=>array()),		// 诺心蛋糕
	'yiguo'		=> array( 'check' => false, 'list'=>array()),		// 易果生鲜
	'tijia'		=> array( 'check' => false, 'list'=>array()),		// 鲜花
	'youpin'    => array( 'check' => false, 'list'=>array())		// 优品生活
);

// 电影     nid: 12

if ( in_array( 12, $nav_ids))
{
	$indexCheck['movie'] = array( 'check' => true , 'list' => komovie_index_show()); 
}

// 演出     nid: 25 (演唱会) 29（话剧）30（音乐会）32（亲子儿童）33（戏曲综艺）

if ( in_array( 25, $nav_ids))
{		
	$int_areaNo = getAreaNo(0, 'yanchu');	
	
	// $yanchuList = F('index-yanchu-'.$int_areaNo , '', 86400, 'yanchu/');//缓存半小时
	if ( empty($yanchuList) )
	{
		
		foreach ( array( 1220 , 1218, 1227, 1224) as $cate_id)
		{
			$yanchuList[] = yanchu_fu($int_areaNo, $cate_id);		
		}
		
		//F('index-yanchu-'.$int_areaNo , $yanchuList, 86400, 'yanchu/');//缓存半小时
	}
	$indexCheck['yanchu'] = array( 'check' => true , 'list' => $yanchuList, 'ad' => index_ad('yanchu', $nav_ids));
}


// 团队     nid：105（团队建设）

if ( in_array( 105, $nav_ids))
{
	$indexCheck['tuan'] = array( 'check' => true , 'list' => index_ad('tuan', $nav_ids));
}

// 蛋糕     nid： 81（蛋糕）

if ( in_array( 81, $nav_ids))
{
	$indexCheck['noxin'] = array( 'check' => true , 'list' => index_ad('noxin', $nav_ids));
}

// 易果     nid: 19（水果）15（蔬菜）16（禽肉）17（海鲜）92（方便素食）
if ( in_array( 19, $nav_ids))
{
	$indexCheck['yiguo'] = array( 'check' => true , 'list' => index_ad('yiguo', $nav_ids));
}

// 生活服务     nid: 102 (体检)

if ( in_array( 23, $nav_ids))
{
	$indexCheck['tijian'] = array( 'check' => true , 'list' => index_ad('tijian', $nav_ids));
}

// 优品生活   nid： 109
if ( in_array( 109, $nav_ids))
{
	$indexCheck['youpin'] = array( 'check' => true , 'list' => index_ad('youpin', $nav_ids));
}

$mobile_thumb = (string)$navigator['home']['mobile_thumb'];
$isopen = !empty($_SESSION['wap_openpic']) ? $_SESSION['wap_openpic'] : 0;
if (!empty($mobile_thumb)){
    $smarty->assign( 'isopen',  $isopen);
    $smarty->assign( 'time',  !empty($navigator['home']['time']) ? $navigator['home']['time']*1000 : 3000 );
    $smarty->assign( 'mobile_thumb', array('pic'=>$mobile_thumb, 'ispic'=>1));
    $_SESSION['wap_openpic'] = 1;
}else{
    $smarty->assign( 'isopen',  $isopen);
    $smarty->assign( 'mobile_thumb', array('pic'=>$mobile_thumb, 'ispic'=>0));
    $smarty->assign( 'time',  !empty($navigator['home']['time']) ? $navigator['home']['time']*1000 : 3000 );
}

$smarty->assign( 'get_fixed', get_fixed(1));
$smarty->assign( 'loveChoose' ,	getLoveChoose( $middle ));		// 为您精心挑选 
$smarty->assign( 'indexSlice' ,	index_ad('indexslice'));		// 首页banner图
$smarty->assign( 'index' ,		$indexCheck);					// 首页块显示
$smarty->assign( 'user_name', 	$_SESSION['user_name'] );		// 登录信息
$smarty->assign( 'category' ,   $navList);						// 导航数据

$smarty->display("index.html");


// 演出数据,读取数据库
function yanchu_fu($int_areaId, $int_cateId ){
	
	$arr_result = $GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['ecs']->table('yanchu_list')." WHERE ifShow=1 and city_id = ".$int_areaId." and type='$int_cateId' ORDER BY id ASC");
	return $arr_result;
}



// 电影列表显示
function komovie_index_show(){
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	$int_areaNo = getAreaNo(0,'komovie');
	$str_cacheName = 'komovie'.'_'.$int_areaNo;
	//$arr_param = array('action'=>'movie_Query','city_id'=>$int_areaNo);
	$arr_data = F($str_cacheName, '', 1800, $int_areaNo.'/');//缓存半小时
	if (empty($arr_data)){
		$arr_result = getCDYApi(array('action'=>'movie_Query','city_id'=>$int_areaNo));
		if (!empty($arr_result)){
			$arr_data = $arr_result['movies'];
			F($str_cacheName, $arr_data, 0, $int_areaNo.'/');//写入缓存
		}
	}

	$hotArr = array();
	foreach( $arr_data as $temp_k=>$temp_v){
		$hotArr[$temp_k]['hot'] = $temp_v['hot'];
	}

	array_multisort($hotArr, SORT_DESC, $arr_data);

	$arr_films = array_splice($arr_data, 0, 3);
	//error_log( var_export($arr_data,true),'3','error.log');
	// 图片本地化
	include_once(ROOT_PATH . 'includes/cls_image.php');
	foreach ($arr_films as &$arr)
	{
		$image_path = explode('/', $arr['pathVerticalS']);
		$filenames = array_pop($image_path);
		if (!file_exists('../temp/komovie/'.$filenames)){
			$new_images = getImage($arr['pathVerticalS'], ROOT_PATH. 'temp/komovie', $filenames);
		}
		$arr['pathVerticalS'] = '../temp/komovie/'.$filenames;
	}
	
	return $arr_films;
}

/**
 * 	获取广告数据 
 *  @param	$pos    str 		广告位置
 *  @param  $ids	str 		当前用户可访问的导航id
 */
function index_ad( $pos, $ids )
{
	$posId = 0;
	$position = $GLOBALS['db']->getAll("SELECT * FROM ".$GLOBALS['ecs']->table('ad_position'));
	foreach ($position as $row)
	{
		if ( strpos($row['position_name'], 'wap-'.$pos) !== false)
		{
			$posId = $row['position_id'];
		}
	}	
	
	$images = $GLOBALS['db']->getAll("SELECT * FROM ".$GLOBALS['ecs']->table('ad')." WHERE position_id = ".$posId." ORDER BY listorder ASC");	
	
	$returnArray = array();
	
	// 设置图片地址,并过滤掉不应该显示的广告
	foreach ($images as $key=>$img)
	{
		if ($img['nav_id'])
		{
			$nav_ids = explode(',', $img['nav_id']);
			foreach ($nav_ids as $nid)
			{
				if (in_array( $nid, $ids))
				{
					// 如果设置了顺序，就把顺序号，作为键
					if (strpos($img['ad_name'], '-') !== false)
					{
						$ad_name = explode('-', $img['ad_name']);
						$pkey = array_pop($ad_name);
					}else{
						$pkey = $key;
					}
					$returnArray[$pkey] = $img;
					$returnArray[$pkey]['ad_code'] = '/'.DATA_DIR."/afficheimg/".$img['ad_code'];
				}
			}
		}
	}
		
	return $returnArray;
}

/* 精心挑选，有权限的所有分类商品  */
function getLoveChoose( $category )
{
	$cates = getCategoryAuth( $category );
	if (empty($cates))
		return array();
	
	$catIds = false !== $cates ? substr($cates, 0 , strlen($cates)-1): $cates ;
	$sql = 'SELECT g.goods_id, g.goods_name,g.region_ids, g.rule_ids, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot,g.goods_num, g.shop_price, ' .
			"g.promote_price, g.goods_type, g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img, " .
			'gs.* ' .
			'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
			'LEFT JOIN ' . $GLOBALS['ecs']->table('goods_spec') . ' AS gs ON gs.goods_id = g.goods_id ' .	
			"WHERE g.cat_id IN (".$catIds.") AND g.is_on_sale = 1 GROUP BY g.goods_name ORDER BY g.sort_order ASC";
	
	$res = $GLOBALS['db']->selectLimit($sql, 10, 0);
	$arr = array();
	while ($row = $GLOBALS['db']->fetchRow($res))
    {
    	$arr[$row['goods_id']]['name']             = $row['goods_name'];
    	$arr[$row['goods_id']]['goods_brief']      = $row['goods_brief'];
    	// 商品规格价格处理
    	// TODO 当前卡规则不显示这个规格的情况下，去最低的规格价格、（未做）
    	$spec_array = array('spec_nember'=> $row['spec_nember'], 'goods_id'=>$row['goods_id']);
    	$arr[$row['goods_id']]['shop_price']       	= get_spec_ratio_price($spec_array);
    	
    	$arr[$row['goods_id']]['goods_thumb']      = get_image_path($row['goods_id'], $row['goods_thumb'], true);
    	$arr[$row['goods_id']]['spec_name']       	= $row['spec_name'];
    	$arr[$row['goods_id']]['spec_nember']       = $row['spec_nember'];
    	$arr[$row['goods_id']]['url']              	= build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
    }
	return $arr;
}

function getCategoryAuth($cates)
{
	$returnCates = '';
	foreach ($cates as $val)
	{
		if (isset($val['child']))
		{
			$returnCates .= getCategoryAuth($val['child']);
		}
		else 
		{
			if ($val['cid'] > 0)
			{
				$returnCates .= $val['cid'].',';
			}
		}
	}
	//return false !== $returnCates ? substr($returnCates, 0 , strlen($returnCates)-1): $returnCates ;
	return $returnCates;
}


?>
