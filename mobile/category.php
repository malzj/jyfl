<?php

/**
 * ECSHOP 商品分类
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: category.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_basic.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

/* 获得请求的分类 ID */
if (isset($_REQUEST['id']))
{
    $cat_id = intval($_REQUEST['id']);
}
elseif (isset($_REQUEST['category']))
{
    $cat_id = intval($_REQUEST['category']);
}
else
{
    /* 如果分类ID为0，则返回首页 */
    ecs_header("Location: ./\n");

    exit;
}

$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

/* 初始化分页信息 */
$page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
$size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 20;
$brand = isset($_REQUEST['brand']) && intval($_REQUEST['brand']) > 0 ? intval($_REQUEST['brand']) : 0;
$price_max = isset($_REQUEST['price_max']) && intval($_REQUEST['price_max']) > 0 ? intval($_REQUEST['price_max']) : 0;
$price_min = isset($_REQUEST['price_min']) && intval($_REQUEST['price_min']) > 0 ? intval($_REQUEST['price_min']) : 0;
$filter_attr_str = isset($_REQUEST['filter_attr']) ? htmlspecialchars(trim($_REQUEST['filter_attr'])) : '0';

$filter_attr_str = trim(urldecode($filter_attr_str));
$filter_attr_str = preg_match('/^[\d\.]+$/',$filter_attr_str) ? $filter_attr_str : '';
$filter_attr = empty($filter_attr_str) ? '' : explode('.', $filter_attr_str);


/* 排序、显示方式以及类型 */
$default_display_type = $_CFG['show_order_type'] == '0' ? 'list' : ($_CFG['show_order_type'] == '1' ? 'grid' : 'text');
$default_sort_order_method = $_CFG['sort_order_method'] == '0' ? 'ASC' : 'DESC';
$default_sort_order_type   = $_CFG['sort_order_type'] == '0' ? 'sort_order' : ($_CFG['sort_order_type'] == '1' ? 'shop_price' : 'last_update');

$sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('goods_id', 'shop_price', 'last_update', 'sales_num'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
$order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC')))                              ? trim($_REQUEST['order']) : $default_sort_order_method;
$display  = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'text'))) ? trim($_REQUEST['display'])  : (isset($_COOKIE['ECS']['display']) ? $_COOKIE['ECS']['display'] : $default_display_type);
$display  = in_array($display, array('list', 'grid', 'text')) ? $display : 'text';
setcookie('ECS[display]', $display, gmtime() + 86400 * 7);

// 搜索关键字
$keyword = isset($_REQUEST['word']) ? htmlspecialchars($_REQUEST['word']) : '';
/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */

/* 页面的缓存ID */
$cache_id = sprintf('%X', crc32($cat_id . '-' . $display . '-' . $sort  .'-' . $order  .'-' . $page . '-' . $size . '-' . $_SESSION['user_rank'] . '-' .
    $_CFG['lang'] .'-'. $brand. '-' . $price_max . '-' .$price_min . '-' . $filter_attr_str));

    // 分类列表只能使用底层分类，非底层分类将跳转到第一个分类
    $childrenCategory = findData('category',"parent_id='".$cat_id."'",'cat_id');
    if (!empty($childrenCategory))
    {
        $childIds = array();
        foreach ($childrenCategory as $_v){ $childIds[] = $_v['cat_id'];}
        
        $mobble = get_navigator();
        foreach ((array)$mobble['middle'] as $mval)
        {
            foreach ($mval['child'] as $cval)
            {
                if ( in_array($cval['cid'], $childIds))
                {
                    $cat_id = $cval['cid'];
                    //ecs_header("Location: ".$cval['url'].substr($_SERVER['REQUEST_URI'], strpos($_SERVER['REQUEST_URI'], '&')));
                }
            }
        }
    }
    
    
    /* 如果页面没有被缓存则重新获取页面的内容 */
    $children = get_children($cat_id);

    $cat = get_cat_info($cat_id);   // 获得分类的相关信息
	
	$smarty->assign('cat', $cat);
	$smarty->assign('cat_id', $cat_id);

	

    if (!empty($cat))
    {
        $smarty->assign('keywords',    htmlspecialchars($cat['keywords']));
        $smarty->assign('description', htmlspecialchars($cat['cat_desc']));
        $smarty->assign('cat_style',   htmlspecialchars($cat['style']));
    }
    else
    {
        /* 如果分类不存在则返回首页 */
        ecs_header("Location: ./\n");

        exit;
    }

    /* 赋值固定内容 */
    if ($brand > 0)
    {
        $sql = "SELECT brand_name FROM " .$GLOBALS['ecs']->table('brand'). " WHERE brand_id = '$brand'";
        $brand_name = $db->getOne($sql);
    }
    else
    {
        $brand_name = '';
    }

    /* 获取价格分级 */
    if ($cat['grade'] == 0  && $cat['parent_id'] != 0)
    {
        $cat['grade'] = get_parent_grade($cat_id); //如果当前分类级别为空，取最近的上级分类
    }

    if ($cat['grade'] > 1)
    {
        /* 需要价格分级 */

        /*
            算法思路：
                1、当分级大于1时，进行价格分级
                2、取出该类下商品价格的最大值、最小值
                3、根据商品价格的最大值来计算商品价格的分级数量级：
                        价格范围(不含最大值)    分级数量级
                        0-0.1                   0.001
                        0.1-1                   0.01
                        1-10                    0.1
                        10-100                  1
                        100-1000                10
                        1000-10000              100
                4、计算价格跨度：
                        取整((最大值-最小值) / (价格分级数) / 数量级) * 数量级
                5、根据价格跨度计算价格范围区间
                6、查询数据库

            可能存在问题：
                1、
                由于价格跨度是由最大值、最小值计算出来的
                然后再通过价格跨度来确定显示时的价格范围区间
                所以可能会存在价格分级数量不正确的问题
                该问题没有证明
                2、
                当价格=最大值时，分级会多出来，已被证明存在
        */

        $sql = "SELECT min(g.shop_price) AS min, max(g.shop_price) as max ".
               " FROM " . $ecs->table('goods'). " AS g ".
               " WHERE ($children OR " . get_extension_goods($children) . ') AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1  ';
               //获得当前分类下商品价格的最大值、最小值

        $row = $db->getRow($sql);

        // 取得价格分级最小单位级数，比如，千元商品最小以100为级数
        $price_grade = 0.0001;
        for($i=-2; $i<= log10($row['max']); $i++)
        {
            $price_grade *= 10;
        }

        //跨度
        $dx = ceil(($row['max'] - $row['min']) / ($cat['grade']) / $price_grade) * $price_grade;
        if($dx == 0)
        {
            $dx = $price_grade;
        }

        for($i = 1; $row['min'] > $dx * $i; $i ++);

        for($j = 1; $row['min'] > $dx * ($i-1) + $price_grade * $j; $j++);
        $row['min'] = $dx * ($i-1) + $price_grade * ($j - 1);

        for(; $row['max'] >= $dx * $i; $i ++);
        $row['max'] = $dx * ($i) + $price_grade * ($j - 1);

        $sql = "SELECT (FLOOR((g.shop_price - $row[min]) / $dx)) AS sn, COUNT(*) AS goods_num  ".
               " FROM " . $ecs->table('goods') . " AS g ".
               " WHERE ($children OR " . get_extension_goods($children) . ') AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 '.
               " GROUP BY sn ";

        $price_grade = $db->getAll($sql);

        foreach ($price_grade as $key=>$val)
        {
            $temp_key = $key + 1;
            $price_grade[$temp_key]['goods_num'] = $val['goods_num'];
            $price_grade[$temp_key]['start'] = $row['min'] + round($dx * $val['sn']);
            $price_grade[$temp_key]['end'] = $row['min'] + round($dx * ($val['sn'] + 1));
            $price_grade[$temp_key]['price_range'] = $price_grade[$temp_key]['start'] . '&nbsp;-&nbsp;' . $price_grade[$temp_key]['end'];
            $price_grade[$temp_key]['formated_start'] = price_format($price_grade[$temp_key]['start']);
            $price_grade[$temp_key]['formated_end'] = price_format($price_grade[$temp_key]['end']);
            $price_grade[$temp_key]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>$price_grade[$temp_key]['start'], 'price_max'=> $price_grade[$temp_key]['end'], 'filter_attr'=>$filter_attr_str), $cat['cat_name']);

            /* 判断价格区间是否被选中 */
            if (isset($_REQUEST['price_min']) && $price_grade[$temp_key]['start'] == $price_min && $price_grade[$temp_key]['end'] == $price_max)
            {
                $price_grade[$temp_key]['selected'] = 1;
            }
            else
            {
                $price_grade[$temp_key]['selected'] = 0;
            }
        }

        $price_grade[0]['start'] = 0;
        $price_grade[0]['end'] = 0;
        $price_grade[0]['price_range'] = $_LANG['all_attribute'];
        $price_grade[0]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>0, 'price_max'=> 0, 'filter_attr'=>$filter_attr_str), $cat['cat_name']);
        $price_grade[0]['selected'] = empty($price_max) ? 1 : 0;

        $smarty->assign('price_grade',     $price_grade);

    }

    /* 属性筛选 */
    $ext = ''; //商品查询条件扩展
    if ($cat['filter_attr'] > 0)
    {
        $cat_filter_attr = explode(',', $cat['filter_attr']);       //提取出此分类的筛选属性
        $all_attr_list = array();

        foreach ($cat_filter_attr AS $key => $value)
        {
            $sql = "SELECT a.attr_name FROM " . $ecs->table('attribute') . " AS a, " . $ecs->table('goods_attr') . " AS ga, " . $ecs->table('goods') . " AS g WHERE ($children OR " . get_extension_goods($children) . ") AND a.attr_id = ga.attr_id AND g.goods_id = ga.goods_id AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 AND a.attr_id='$value'";
            if($temp_name = $db->getOne($sql))
            {
                $all_attr_list[$key]['filter_attr_name'] = $temp_name;

                $sql = "SELECT a.attr_id, MIN(a.goods_attr_id ) AS goods_id, a.attr_value AS attr_value FROM " . $ecs->table('goods_attr') . " AS a, " . $ecs->table('goods') .
                       " AS g" .
                       " WHERE ($children OR " . get_extension_goods($children) . ') AND g.goods_id = a.goods_id AND g.is_delete = 0 AND g.is_on_sale = 1 AND g.is_alone_sale = 1 '.
                       " AND a.attr_id='$value' ".
                       " GROUP BY a.attr_value";

                $attr_list = $db->getAll($sql);

                $temp_arrt_url_arr = array();

                for ($i = 0; $i < count($cat_filter_attr); $i++)        //获取当前url中已选择属性的值，并保留在数组中
                {
                    $temp_arrt_url_arr[$i] = !empty($filter_attr[$i]) ? $filter_attr[$i] : 0;
                }

                $temp_arrt_url_arr[$key] = 0;                           //“全部”的信息生成
                $temp_arrt_url = implode('.', $temp_arrt_url_arr);
                $all_attr_list[$key]['attr_list'][0]['attr_value'] = $_LANG['all_attribute'];
                $all_attr_list[$key]['attr_list'][0]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$temp_arrt_url), $cat['cat_name']);
                $all_attr_list[$key]['attr_list'][0]['selected'] = empty($filter_attr[$key]) ? 1 : 0;

                foreach ($attr_list as $k => $v)
                {
                    $temp_key = $k + 1;
                    $temp_arrt_url_arr[$key] = $v['goods_id'];       //为url中代表当前筛选属性的位置变量赋值,并生成以‘.’分隔的筛选属性字符串
                    $temp_arrt_url = implode('.', $temp_arrt_url_arr);

                    $all_attr_list[$key]['attr_list'][$temp_key]['attr_value'] = $v['attr_value'];
                    $all_attr_list[$key]['attr_list'][$temp_key]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$temp_arrt_url), $cat['cat_name']);

                    if (!empty($filter_attr[$key]) AND $filter_attr[$key] == $v['goods_id'])
                    {
                        $all_attr_list[$key]['attr_list'][$temp_key]['selected'] = 1;
                    }
                    else
                    {
                        $all_attr_list[$key]['attr_list'][$temp_key]['selected'] = 0;
                    }
                }
            }

        }

        $smarty->assign('filter_attr_list',  $all_attr_list);
        /* 扩展商品查询条件 */
        if (!empty($filter_attr))
        {
            $ext_sql = "SELECT DISTINCT(b.goods_id) FROM " . $ecs->table('goods_attr') . " AS a, " . $ecs->table('goods_attr') . " AS b " .  "WHERE ";
            $ext_group_goods = array();

            foreach ($filter_attr AS $k => $v)                      // 查出符合所有筛选属性条件的商品id */
            {
                if (is_numeric($v) && $v !=0 &&isset($cat_filter_attr[$k]))
                {
                    $sql = $ext_sql . "b.attr_value = a.attr_value AND b.attr_id = " . $cat_filter_attr[$k] ." AND a.goods_attr_id = " . $v;
                    $ext_group_goods = $db->getColCached($sql);
                    $ext .= ' AND ' . db_create_in($ext_group_goods, 'g.goods_id');
                }
            }
        }
    }

    assign_template('c', array($cat_id));

    $count = get_cagtegory_goods_count($children, $brand, $price_min, $price_max, $ext);
    $max_page = ($count> 0) ? ceil($count / $size) : 1;
    if ($page > $max_page)
    {
        $page = $max_page;
    }
    
    $goodslist = category_get_goods($children, $brand, $price_min, $price_max, $ext, $size, $page, $sort, $order,$keyword);
	
    if($display == 'grid')
    {
        if(count($goodslist) % 2 != 0)
        {
            $goodslist[] = array();
        }
    }   
    
    $pager = assign_pager_wap('category',            $cat_id, $count, $size, $sort, $order, $page, '', $brand, $price_min, $price_max, $display, $filter_attr_str); // 分页
    
    // 所有导航信息
    $childrenNavigator = get_navigator();
    
    // 商品列表
    $jsonArray['data']['list'] = $goodslist;
    // 分页信息
    $jsonArray['data']['pager'] = $pager;
    // 属性列表
    $jsonArray['data']['attrList'] = $all_attr_list;
    // 子类列表
    $jsonArray['data']['navigator'] = $childrenNavigator['middle'];
    
    JsonpEncode($jsonArray);


/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */

/**
 * 获得分类的信息
 *
 * @param   integer $cat_id
 *
 * @return  void
 */
function get_cat_info($cat_id)
{
    return $GLOBALS['db']->getRow('SELECT cat_name,measure_unit, keywords, cat_desc, style, grade, filter_attr, parent_id FROM ' . $GLOBALS['ecs']->table('category') .
        " WHERE cat_id = '$cat_id'");
}

/**
 * 获得分类下的商品
 *
 * @access  public
 * @param   string  $children
 * @return  array
 */
function category_get_goods($children, $brand, $min, $max, $ext, $size, $page, $sort, $order, $keyword)
{
	
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
    
    if ( !empty($keyword))
    {
        $where .= " AND g.goods_name like '%".$keyword."%'";
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

/**
 * 获得分类下的商品总数
 *
 * @access  public
 * @param   string     $cat_id
 * @return  integer
 */
function get_cagtegory_goods_count($children, $brand = 0, $min = 0, $max = 0, $ext='')
{
    $where  = "is_on_sale = 1 AND is_alone_sale = 1  AND supplier_status = 1 AND is_delete = 0 AND ($children OR " . get_extension_goods($children) . ')';

    if ($brand > 0)
    {
        $where .=  " AND brand_id = $brand ";
    }

   /*  if ($min > 0)
    {
        $where .= " AND shop_price >= $min ";
    }

    if ($max > 0)
    {
        $where .= " AND shop_price <= $max ";
    } */

	$where .= ' AND FIND_IN_SET('.intval($GLOBALS['int_cityId']).', g.region_ids)';
	
	if (!empty($_SESSION['card_id']))
	{
		$where .= ' AND LOCATE(",'.$_SESSION['card_id'].',",g.rule_ids) = 0';
	}

	//$arr_data = $GLOBALS['db']->getAll('SELECT g.goods_id, ga.region_ids FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g LEFT JOIN ' . $GLOBALS['ecs']->table('goods_huohao') . " AS ga ON ga.goods_id = g.goods_id WHERE $where $ext AND CASE WHEN region_ids is null THEN 1 ELSE FIND_IN_SET(".intval($GLOBALS['int_cityId']).", ga.region_ids) END");
	//return count($arr_data);

    /* 返回商品总数 */
    return $GLOBALS['db']->getOne('SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g WHERE '.$where.' '.$ext);
}

/**
 * 取得最近的上级分类的grade值
 *
 * @access  public
 * @param   int     $cat_id    //当前的cat_id
 *
 * @return int
 */
function get_parent_grade($cat_id)
{
    static $res = NULL;

    if ($res === NULL)
    {
        $data = read_static_cache('cat_parent_grade');
        if ($data === false)
        {
            $sql = "SELECT parent_id, cat_id, grade ".
                   " FROM " . $GLOBALS['ecs']->table('category');
            $res = $GLOBALS['db']->getAll($sql);
            write_static_cache('cat_parent_grade', $res);
        }
        else
        {
            $res = $data;
        }
    }

    if (!$res)
    {
        return 0;
    }

    $parent_arr = array();
    $grade_arr = array();

    foreach ($res as $val)
    {
        $parent_arr[$val['cat_id']] = $val['parent_id'];
        $grade_arr[$val['cat_id']] = $val['grade'];
    }

    while ($parent_arr[$cat_id] >0 && $grade_arr[$cat_id] == 0)
    {
        $cat_id = $parent_arr[$cat_id];
    }

    return $grade_arr[$cat_id];

}


?>
