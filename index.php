<?php

/**
 * ECSHOP 首页文件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: index.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_cardApi.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

//	ecs_header("Location: shiting.php?act=skyy\n");
//	exit;
/*
$ua = strtolower($_SERVER['HTTP_USER_AGENT']);

$uachar = "/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile)/i";

if(($ua == '' || preg_match($uachar, $ua))&& !strpos(strtolower($_SERVER['REQUEST_URI']),'wap'))
{
    $Loaction = 'mobile/';

    if (!empty($Loaction))
    {
        ecs_header("Location: $Loaction\n");

        exit;
    }

}

*/

if (empty($_SESSION['user_id'])){
	$Loaction = 'user.php';
	ecs_header("Location: $Loaction\n");
	exit;
}

/*------------------------------------------------------ */
//-- Shopex系统地址转换
/*------------------------------------------------------ */
if (!empty($_GET['gOo']))
{
    if (!empty($_GET['gcat']))
    {
        /* 商品分类。*/
        $Loaction = 'category.php?id=' . $_GET['gcat'];
    }
    elseif (!empty($_GET['acat']))
    {
        /* 文章分类。*/
        $Loaction = 'article_cat.php?id=' . $_GET['acat'];
    }
    elseif (!empty($_GET['goodsid']))
    {
        /* 商品详情。*/
        $Loaction = 'goods.php?id=' . $_GET['goodsid'];
    }
    elseif (!empty($_GET['articleid']))
    {
        /* 文章详情。*/
        $Loaction = 'article.php?id=' . $_GET['articleid'];
    }

    if (!empty($Loaction))
    {
        ecs_header("Location: $Loaction\n");

        exit;
    }
}


//判断是否有ajax请求
$act = !empty($_GET['act']) ? $_GET['act'] : '';

if ($act == 'index_category')
{
	$id = !empty($_GET['data-fid']) ? intval($_GET['data-fid']) : 0 ;
	$function = !empty($_GET['data-fu']) ? $_GET['data-fu'] : '';
	$template = !empty($_GET['data-tp']) ? $_GET['data-tp'] : '';
	$ads = !empty($_GET['data-ads']) ? $_GET['data-ads'] : '';
	$size = !empty($_GET['data-size']) ? $_GET['data-size'] : '';
	$is_title = !empty($_GET['data-show-title']) ? intval($_GET['data-show-title']) : 0 ;
	
	include_once('includes/cls_json.php');
	$json = new JSON;	
	
	// 分类id为空，返回错误消息
	if (empty($function))
	{
		$result   = array('content' => '产品参数不全！');
		die($json->encode($result));
	} 
	
	$param = array('id'=>$id, 'function'=>$function, 'template'=>$template, 'ads'=>$ads, 'size'=>$size, 'is_title'=>$is_title);
	// error_log(var_export($param,true)."\r\n",'3','yanchu.log');
	switch ($function)
	{
		case 'komovie': 	$result   = array('content' => komovie_index_show($param)); 	break;
		case 'yanchu':
		    $cache_name='yanchu_'.$param['id'];
		    $data=read_static_cache($cache_name);
		    if(empty($data)){
		        $data=yanchu_index_show($param);
		        write_static_cache($cache_name,$data); 
		    }
			$result   = array('content' => $data);		break;
			// $result   = array('content' => yanchu_index_show($param));		break;
		case 'dongwang':	$result   = array('content' => dongwang_index_show($param));	break;
		case 'category':    $result   = array('content' => category_index_show($param));	break;
		case 'empty_fu': 	$result   = array('content' => empty_fu($param));	break;
		default: 			$result	  = array('content' => $function($param));	break;
	}
	die($json->encode($result));
	
   /*  $rec_array = array(1 => 'best', 2 => 'new', 3 => 'hot');
    $rec_type = !empty($_REQUEST['rec_type']) ? intval($_REQUEST['rec_type']) : '1';
    $cat_id = !empty($_REQUEST['cid']) ? intval($_REQUEST['cid']) : '0';
    include_once('includes/cls_json.php');
    $json = new JSON;
    $result   = array('error' => 0, 'content' => '', 'type' => $rec_type, 'cat_id' => $cat_id);

    $children = get_children($cat_id);
    $smarty->assign($rec_array[$rec_type] . '_goods',      get_category_recommend_goods($rec_array[$rec_type], $children));    // 推荐商品
    $smarty->assign('cat_rec_sign', 1);
    $result['content'] = $smarty->fetch('library/recommend_' . $rec_array[$rec_type] . '.lbi');
    die($json->encode($result)); */
}

/* --------------------------------------------------------
 * 首页页面
 * ---------------------------------------------------------
 */
    assign_template();

    $position = assign_ur_here();
    $smarty->assign('page_title',      $position['title']);    // 页面标题
    $smarty->assign('ur_here',         $position['ur_here']);  // 当前位置

    /* meta information */
    $smarty->assign('keywords',        htmlspecialchars($_CFG['shop_keywords']));
    $smarty->assign('description',     htmlspecialchars($_CFG['shop_desc']));
    $smarty->assign('flash_theme',     $_CFG['flash_theme']);  // Flash轮播图片模板

    $smarty->assign('feed_url',        ($_CFG['rewrite'] == 1) ? 'feed.xml' : 'feed.php'); // RSS URL


    /* 首页推荐分类 */
    /* $cat_recommend_res = $db->getAll("SELECT c.cat_id, c.cat_name, cr.recommend_type FROM " . $ecs->table("cat_recommend") . " AS cr INNER JOIN " . $ecs->table("category") . " AS c ON cr.cat_id=c.cat_id");
    if (!empty($cat_recommend_res))
    {
        $cat_rec_array = array();
        foreach($cat_recommend_res as $cat_recommend_data)
        {
            $cat_rec[$cat_recommend_data['recommend_type']][] = array('cat_id' => $cat_recommend_data['cat_id'], 'cat_name' => $cat_recommend_data['cat_name']);
        }
        $smarty->assign('cat_rec', $cat_rec);
    } */

    /* 页面中的动态内容 */
    assign_dynamic('index');

    // 接口数据调用方法 （function 程序调用的方法，templates 调用的模板， ads 广告位置（从0开始） fid 分类id（接口写0） size(显示多少条数据)）
    $interfase_function = array(
    	/* 特惠甄选 */
    	'n106'=> array('function'=>'promotion', 'templates'=>'index_promotion', 'fid'=>106,  'height'=>'280px', ),
    	/* 顶楼 */
    	'n104'=> array('function'=>'empty_fu', 'templates'=>'index_fanding', 'fid'=>104,  'height'=>'330px', ),
    	
    	/* 一楼 */
    	'n12'=> array('function'=>'komovie', 'templates'=>'index_komovie', 'fid'=>1000,  'height'=>'330px', ),			// 电影
    	'n25'=> array('function'=>'yanchu',  'templates'=>'index_yanchu', 'fid'=>1217, 'height'=>'330px', ),		// 演唱会
    	'n29'=> array('function'=>'yanchu',  'templates'=>'index_yanchu', 'fid'=>1220, 'height'=>'330px', ),		// 话剧
    	'n30'=> array('function'=>'yanchu',  'templates'=>'index_yanchu', 'fid'=>1218, 'height'=>'330px', ),		// 音乐会
    	'n32'=> array('function'=>'yanchu',  'templates'=>'index_yanchu', 'fid'=>1227,  'height'=>'330px', ),		// 儿童亲子
    	'n33'=> array('function'=>'yanchu',  'templates'=>'index_yanchu', 'fid'=>1224, 'height'=>'330px', ),		// 戏曲综艺
    	
    	 	
    	/* 二楼 */
    	'c63'=> array('function'=>'category','templates'=>'index_categorys','fid'=>63, 'height'=>'480px', 'ads'=>'0,7', 'size'=>'10'),	// 诺心蛋糕
    	'c13'=> array('function'=>'category','templates'=>'index_categorys','fid'=>13, 'height'=>'480px', 'ads'=>'1,8', 'size'=>'10'),	// 水果
    	'c10'=> array('function'=>'category','templates'=>'index_categorys','fid'=>10, 'height'=>'480px', 'ads'=>'2,5', 'size'=>'10'),	// 蔬菜
    	'c11'=> array('function'=>'category','templates'=>'index_categorys','fid'=>11, 'height'=>'480px', 'ads'=>'4', 'size'=>'11'),	// 禽肉
    	'c12'=> array('function'=>'category','templates'=>'index_categorys','fid'=>12, 'height'=>'480px', 'ads'=>'7', 'size'=>'11'),	// 海鲜
    	/* 三楼  */
    	'c77'=> array('function'=>'category','templates'=>'index_ydzb','fid'=>77, 'height'=>'422px', 'size'=>'6'),	// 运动装备
    	'n94'=> array('function'=>'dongwang','templates'=>'index_dongwang','fid'=>94, 'height'=>'422px', ), 			// 运动健身 
    	//'c15'=> array('function'=>'category','templates'=>'index_yd_defautl','fid'=>12, 'height'=>'422px', 'size'=>'12'),	// 运动装备
    	/* 四楼 */
    	'c17'=> array('function'=>'category','templates'=>'index_xianhua','fid'=>17, 'height'=>'480px', 'ads'=>'0,5', 'size'=>'10'),	// 鲜花
    	'c82'=> array('function'=>'category','templates'=>'index_xianhua','fid'=>82, 'height'=>'480px',  'size'=>'12'),	// 体检
    	
		/* 五楼 */
    	'n22'=> array('function'=>'empty_fu','templates'=>'index_tushu','fid'=>22, 'height'=>'400px'),	// 图书
    );

    /** 楼层数据 **/
    $clist = array();
    $navigator = get_navigator();
   
    foreach($navigator['middle'] as $key=>$val)
    {

    	
    	// 子类
    	$childrens = $val['child'];
    	// 暂时删除子类
    	unset($val['child']);
    	// 如果不在首页显示
    	if ($val['show_index'] == 0)
    	{
    		continue;
    	}
    	
    	$clist[$key] = $val;
    	// 一级导航的参数（不显示二级导航的情况下）
    	$nav_ids = implode('',array('n',$val['id']));
    	if ($val['cid'] > 0)
    	{
    		$nav_ids = implode('',array('c',$val['cid']));
    	}
    	if (empty($childrens))
    	{
    		$childrens[] = $val;
    		$clist[$key]['is_title'] = 1;
    	}
    	// 子类筛选
    	foreach( $childrens as $child)
    	{
    		if ($child['show_index'] == 1 )
    		{
    			$nav_id = implode('',array('n',$child['id']));
    			if ($child['cid'] > 0)
    			{
    				$nav_id = implode('',array('c',$child['cid']));
    			}
    			$child['function'] = isset($interfase_function[$nav_id]['function']) ? $interfase_function[$nav_id]['function'] : 'category' ;
    			$child['templates'] = isset($interfase_function[$nav_id]['templates']) ? $interfase_function[$nav_id]['templates'] : 'index_categorys' ;
    			$child['ads'] = isset($interfase_function[$nav_id]['ads']) ? $interfase_function[$nav_id]['ads'] : '' ;
    			$child['fid'] = isset($interfase_function[$nav_id]['fid']) ? $interfase_function[$nav_id]['fid'] : $child['cid'] ;
    			$child['size'] = isset($interfase_function[$nav_id]['size']) ? $interfase_function[$nav_id]['size'] : 0 ;
    			
    			// 单层的高度
    			$clist[$key]['height'] = isset($interfase_function[$nav_id]['height']) ? $interfase_function[$nav_id]['height'] : '480px' ;
    			$clist[$key]['child'][$child['id']] = $child;


    		}

		}
		$clist[$key]['position_id']="";
		$clist[$key]['position_id']=louceng($key);
		
	}
    $new_clist = array_reverse($clist);
 	$smarty->assign('cenglist', $new_clist);   
	$smarty->display('index.dwt');
/*------------------------------------------------------ */
//-- PRIVATE FUNCTIONS
/*------------------------------------------------------ */
//楼层广告
function louceng($nav_id){
	$position_id=10;
	$row1="";
	$sql1 = "SELECT * FROM " . $GLOBALS['ecs']->table('ad_position') . " AS a " .
				" LEFT JOIN " .$GLOBALS['ecs']->table('ad'). " AS ad ON ad.position_id=a.position_id where ad.enabled=1 and ad.position_id=".$position_id." and ad.nav_id=".$nav_id;

	$row1= $GLOBALS['db']->getRow($sql1);
	return $row1;die;
//  echo "<pre>";
// print_r($row1);
// echo "</pre>";
// die; 	
	$i="";
	foreach ($row1 as $key => $value) {
		# code...
		$i[$key]=explode("-", $value['ad_name']);

		if($str==$i[$key][1]){
			$value['id']=$num;
			$res[]=$value;
		}
	}
	return $res[0];
	
}
// 电影列表显示
function komovie_index_show($param){
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
	
	$arr_films = array_splice($arr_data, 0, 9);
	
	// 图片本地化
	include_once(ROOT_PATH . 'includes/cls_image.php');
	foreach ($arr_films as &$arr)
	{
		$image_path = explode('/', $arr['pathVerticalS']);
		$filenames = array_pop($image_path);
		if (!file_exists('temp/komovie/'.$filenames)){
			$new_images = getImage($arr['pathVerticalS'], 'temp/komovie', $filenames);			
		}	
		$arr['pathVerticalS'] = '/temp/komovie/'.$filenames;
	}
	
	
	$first_films = array_shift($arr_films);
	$GLOBALS['smarty']->assign('first', $first_films);
	$GLOBALS['smarty']->assign('films', $arr_films);
	//error_log(var_export($first_films,true),'3','error.log');
	return $GLOBALS['smarty']->fetch('library/komovie_index_show.lbi');
}

// 演出票
function yanchu_index_show($param)
{
	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	// 当前城市
	$int_areaNo = getAreaNo(0, 'yanchu');
	// 第一页开始
	$int_page = 1;
	// 最终的数据
	$data = array();
	while (count($data) < 7)
	{	
		# code...
		$temp = yanchu_fu($int_page,$int_areaNo,$param['id']);
		foreach($temp['items'] as $yanchu)
		{
			if (!array_key_exists($yanchu['itemId'], $data) && count($data) < 7)
			{
				$data[$yanchu['itemId']]['itemId'] = $yanchu['itemId'];
				$data[$yanchu['itemId']]['itemName'] = $yanchu['itemName'];
				$data[$yanchu['itemId']]['timeType'] = $yanchu['timeType'];
				$data[$yanchu['itemId']]['startDate'] = $yanchu['startDate'];
				$data[$yanchu['itemId']]['endDate'] = $yanchu['endDate'];
				$data[$yanchu['itemId']]['imageUrl'] = $yanchu['imageUrl'];
				$data[$yanchu['itemId']]['cateName'] = $yanchu['cate']['@attributes']['cateName'];
				$data[$yanchu['itemId']]['introduction'] = $yanchu['introduction'];
				$data[$yanchu['itemId']]['ifShow'] = $yanchu['ifShow'];
				$data[$yanchu['itemId']]['status'] = $yanchu['status'];
				$data[$yanchu['itemId']]['siteName'] = $yanchu['site']['@attributes']['siteName'];
			}
		}
		if($temp['page']['PageSize']==$temp['page']['PageIdx']){
			break;
		}
		$int_page++;
	}
	array_shift($data);
	$ad_yanchu = ads_fu(array('name'=>'pc-yanchu', 'id'=>$param['id']));	
	$GLOBALS['smarty']->assign('yanchu', $ad_yanchu);
	$GLOBALS['smarty']->assign('cateid', $param['id']);	
	$GLOBALS['smarty']->assign('list', $data);
	return $GLOBALS['smarty']->fetch('library/yanchu_index_show.lbi');
}

// 动网门票
function dongwang_index_show($param)
{
	$ad_dongwang = ads_fu(array('name'=>'dongwang'));
	$GLOBALS['smarty']->assign('ad', $ad_dongwang);
	return $GLOBALS['smarty']->fetch('library/index_dongwang.lbi');
}

/* 空数据 */
function empty_fu($param)
{
	return $GLOBALS['smarty']->fetch('library/'.$param['template'].'.lbi');
}

// 分类商品
function category_index_show($param)
{
	$data = array();
	$size = !empty($param['size']) ? $param['size'] : 12 ;
	$children = get_children($param['id']);
	$goodslist = category_get_goods($children, 0, 0, 0, '', 15, 1, 'sort_order', 'ASC');
	
	// 处理广告的位置
	if (!empty($param['ads']))
	{
		$ads = explode(',', $param['ads']);		
	}
	
	// 
	$i = 0;
	$position = 1;
	foreach ($goodslist as $goods)
	{
		if (in_array($i, $ads))
		{
			$data[] = ads_fu(array('name'=>'category','id'=>$param['id'], 'position'=>$position));
			$position++;
		}
		else
		{
			$data[] = $goods;
		}
		$i++;
	}
	//error_log(var_export($data,true),'3','error.log');
	$info = array_slice($data, 0, $size);	
	$GLOBALS['smarty']->assign('list', $info);
	return $GLOBALS['smarty']->fetch('library/'.$param['template'].'.lbi');
	
}

// 演出数据
function yanchu_fu($int_page, $int_areaId, $int_cateId ){

	include_once(ROOT_PATH . 'includes/lib_cardApi.php');
	$arr_param = array(
			'cityId'    => $int_areaId,
			'cateId'    => $int_cateId,
			'nowsale'   => '',
			'page'      => $int_page
	);

	//关键词搜索
	$str_keyword = $_GET['yckeyword'];
	if (!empty($str_keyword)){
		$arr_param['keyword'] = $str_keyword;
	}


	$str_regionName = $_GET['region'];
	if ($str_regionName){
		$arr_param['regionName'] = $str_regionName;
	}


	$obj_result = getYCApi($arr_param, 'search');

	$int_dataCount = count($obj_result->items);
	$arr_result = object2array($obj_result);
	$int_pageSize = (int)$arr_result['@attributes']['PageSize'];
	//$int_pageSize = (int)$obj_result->attributes()->PageSize;
	$int_count = $int_pageSize * 10;
	$arr_yanchuData = array();
	$int_index = 0;
	if ($arr_result['items']){
		if ($int_dataCount > 1){
			foreach ($arr_result['items'] as $key=>$var){
				if($var['ifShow'] == 0) continue;
				$var['startDate'] = !empty($var['startDate']) ? local_date('Y-m-d', $var['startDate']) : '';
				$var['endDate']   = !empty($var['endDate'])   ? local_date('Y-m-d', $var['endDate']) : '';
				$arr_yanchuData[$key] = $var;
			}
		}else{
			if($arr_result['items']['isShow'] == 1)
			{
				$arr_result['items']['startDate'] = !empty($arr_result['items']['startDate']) ? local_date('Y-m-d', $arr_result['items']['startDate']) : '';
				$arr_result['items']['endDate']   = !empty($arr_result['items']['endDate']) ? local_date('Y-m-d', $arr_result['items']['endDate']) : '';
				$arr_yanchuData[$int_index] = $arr_result['items'];
			}
		}
	}
	return array('page'=>$arr_result['@attributes'],'items'=>$arr_yanchuData);
	// return $arr_yanchuData;
}


/**
 * 获得分类下的商品
 *
 * @access  public
 * @param   string  $children
 * @return  array
 */
function category_get_goods($children, $brand, $min, $max, $ext, $size, $page, $sort, $order)
{
	/* echo '<pre>';
	 print_r($_SESSION);
	echo '</pre>'; exit; */
	$display = $GLOBALS['display'];
	$where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.supplier_status = 1 AND ".
			"g.is_delete = 0 AND ($children OR " . get_extension_goods($children) . ')';

	if ($brand > 0)
	{
		$where .=  "AND g.brand_id=$brand ";
	}

	if ($min > 0)
	{
		$where .= " AND g.shop_price >= $min ";
	}

	if ($max > 0)
	{
		$where .= " AND g.shop_price <= $max ";
	}

	$where .= ' AND FIND_IN_SET('.intval($GLOBALS['int_cityId']).', g.region_ids) AND gs.default_show = 1 ';

	if (!empty($_SESSION['card_id']))
	{
		$where .= ' AND LOCATE(",'.$_SESSION['card_id'].',",g.rule_ids) = 0';
	}

	/* 获得商品列表 */
	/* $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot,g.goods_num, g.shop_price AS org_price, ' .
	 "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, g.promote_price, g.goods_type, " .
	'g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img, mp.user_price ' .
	'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
	'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
	"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
	'LEFT JOIN '.$GLOBALS['ecs']->table('goods_huohao').' as ga ON ga.goods_id = g.goods_id '.
	"WHERE $where $ext ORDER BY $sort $order"; */
	//"WHERE $where $ext AND CASE WHEN region_ids is null THEN 1 ELSE FIND_IN_SET(".intval($GLOBALS['int_cityId']).", ga.region_ids) END ORDER BY $sort $order";
	$sql = 'SELECT g.goods_id, g.goods_name,g.region_ids, g.rule_ids, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot,g.goods_num, g.shop_price, ' .
			"g.promote_price, g.goods_type, g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img, " .
			'gs.* ' .
			'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
			//'LEFT JOIN ' . $GLOBALS['ecs']->table('member_price') . ' AS mp ' .
	//"ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
	'LEFT JOIN ' . $GLOBALS['ecs']->table('goods_spec') . ' AS gs ' .
	"ON gs.goods_id = g.goods_id " .
	//'LEFT JOIN '.$GLOBALS['ecs']->table('goods_spec_region').' as gsr ON gsr.spec_id = gs.id '.
	"WHERE $where $ext GROUP BY g.goods_name ORDER BY $sort $order";

	//echo $sql; exit;
	$res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);

	$arr = array();
	while ($row = $GLOBALS['db']->fetchRow($res))
	{
		if ($row['promote_price'] > 0)
		{
			$promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
		}
		else
		{
			$promote_price = 0;
		}

		/* 处理商品水印图片 */
		$watermark_img = '';

		if ($promote_price != 0)
		{
			$watermark_img = "watermark_promote_small";
		}
		elseif ($row['is_new'] != 0)
		{
			$watermark_img = "watermark_new_small";
		}
		elseif ($row['is_best'] != 0)
		{
			$watermark_img = "watermark_best_small";
		}
		elseif ($row['is_hot'] != 0)
		{
			$watermark_img = 'watermark_hot_small';
		}

		if ($watermark_img != '')
		{
			$arr[$row['goods_id']]['watermark_img'] =  $watermark_img;
		}

		$arr[$row['goods_id']]['goods_id']         = $row['goods_id'];
		if($display == 'grid')
		{
			$arr[$row['goods_id']]['goods_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
		}
		else
		{
			$arr[$row['goods_id']]['goods_name']       = $row['goods_name'];
		}
		$arr[$row['goods_id']]['name']             = $row['goods_name'];
		$arr[$row['goods_id']]['goods_brief']      = $row['goods_brief'];
		$arr[$row['goods_id']]['goods_style_name'] = add_style($row['goods_name'],$row['goods_name_style']);
		$arr[$row['goods_id']]['market_price']     = price_format($row['market_price']);
		// 商品规格价格处理
		// TODO 当前卡规则不显示这个规格的情况下，去最低的规格价格、（未做）
		$spec_array = array('spec_nember'=> $row['spec_nember'], 'goods_id'=>$row['goods_id']);
		$arr[$row['goods_id']]['shop_price']       = get_spec_ratio_price($spec_array);

		$arr[$row['goods_id']]['spec_name']       = $row['spec_name'];
		$arr[$row['goods_id']]['spec_nember']       = $row['spec_nember'];

		$arr[$row['goods_id']]['type']             = $row['goods_type'];
		$arr[$row['goods_id']]['goods_num']        = $row['goods_num'];
		$arr[$row['goods_id']]['promote_price']    = ($promote_price > 0) ? price_format($promote_price) : '';
		$arr[$row['goods_id']]['goods_thumb']      = get_image_path($row['goods_id'], $row['goods_thumb'], true);
		$arr[$row['goods_id']]['goods_img']        = get_image_path($row['goods_id'], $row['goods_img']);
		$arr[$row['goods_id']]['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
	}

	/*  echo '<pre>';
	 print_r($arr);
	echo '</pre>';  exit; */
	return $arr;
	
}

/* 首页特惠区 */
function promotion($param){
	$pics = ads_fu(array('name'=>'promotion'));
	$GLOBALS['smarty']->assign('pics', $pics);	
	return $GLOBALS['smarty']->fetch('library/'.$param['template'].'.lbi');
}

function ads_fu($param){
	$ad = array();
	
	// 找到对应的广告位
	
	$ad_position = $GLOBALS['db']->getAll("SELECT * FROM ".$GLOBALS['ecs']->table('ad_position'));
	foreach ($ad_position as $position)
	{
		if ( strpos($position['position_name'], $param['name']) !== false)
		{
			$position_id = $position['position_id'];
		}
	}
	
	$ad_images = $GLOBALS['db']->getAll("SELECT * FROM ".$GLOBALS['ecs']->table('ad')." WHERE position_id = ".$position_id." ORDER BY listorder ASC, ad_id ASC");
	switch($param['name'])
	{
		case "category":
			
			foreach ($ad_images as $images)
			{
				$ad_name = explode('-', $images['ad_name']);
				if (in_array($param['id'], $ad_name) && in_array($param['position'], $ad_name))
				{
					$ad = array(
						'is_ad' =>1,
						'images'=> $images['ad_code'],
						'url'	=> $images['ad_link']
					);					
				}	
			}
			
			if (empty($ad))
			{
				$ad = array('is_ad'=>1);
			}
			
			break;
		case "dongwang":
			
			foreach ($ad_images as $images)
			{
				$ad_name = explode('-', $images['ad_name']);
				$ad[$ad_name[1]] = array(
						'images'	=> $images['ad_code'],
						'url'		=> $images['ad_link']
				); 
			}
						
			break;
		case 'pc-yanchu':
			foreach ($ad_images as $images)
			{
				$ad_name = explode('-', $images['ad_name']);
				if (in_array($param['id'], $ad_name))
				{
					$ad = array(						
						'images'=> $images['ad_code'],
						'url'	=> $images['ad_link']
					);					
				}	
			}
			break;
			
		case 'promotion':
			foreach ($ad_images as $images)
			{
				$ad_name = explode('-', $images['ad_name']);
				$ad[$ad_name[1]] = array(
						'images'	=> $images['ad_code'],
						'url'		=> $images['ad_link'],
						'ad_id'		=> $images['ad_id']
				);				
			}
			ksort($ad);
			$ad = array_slice($ad, 0,4);				
			break;
			
	}
	
	
	return $ad;
}



?>