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

assign_template();

$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

/* 初始化分页信息 */
$page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
$size = 200;//isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 200;
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

$sort  = (isset($_REQUEST['sort'])  && in_array(trim(strtolower($_REQUEST['sort'])), array('sort_order', 'shop_price', 'last_update', 'sales_num'))) ? trim($_REQUEST['sort'])  : $default_sort_order_type;
$order = (isset($_REQUEST['order']) && in_array(trim(strtoupper($_REQUEST['order'])), array('ASC', 'DESC'))) ? trim($_REQUEST['order']) : $default_sort_order_method;
$display  = (isset($_REQUEST['display']) && in_array(trim(strtolower($_REQUEST['display'])), array('list', 'grid', 'text'))) ? trim($_REQUEST['display'])  : (isset($_COOKIE['ECS']['display']) ? $_COOKIE['ECS']['display'] : $default_display_type);
$display  = in_array($display, array('list', 'grid', 'text')) ? $display : 'text';
setcookie('ECS[display]', $display, gmtime() + 86400 * 7);

// 搜索关键字
$keyword = isset($_REQUEST['word']) ? htmlspecialchars($_REQUEST['word']) : '';
/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */


    $mobble = get_navigator();
    
    $childrenCategory = findData('category',"parent_id='".$cat_id."'",'cat_id');
    
    // 顶级分类
    $top_cat_id = 0;
    
    // 分类列表只能使用底层分类，非底层分类的，设置当前分类是子类
    if (!empty($childrenCategory))
    {
        $childIds = array();
        foreach ($childrenCategory as $_v){ $childIds[] = $_v['cat_id'];}        
        
        foreach ((array)$mobble['middle'] as $mval)
        {
            foreach ($mval['child'] as $cval)
            {
                if ( in_array($cval['cid'], $childIds))
                {
                    $cat_id = $cval['cid'];
                    $filter_attr = '';
                }
            }
        }
    }   
    
    foreach ((array)$mobble['middle'] as $mval)
    {
        foreach ($mval['child'] as $cval)
        {
            if ( $cval['cid'] == $cat_id)
            {
                $top_cat_id = $mval['id'];
            }
        }
    }
    
    
    /* 如果页面没有被缓存则重新获取页面的内容 */
    $children = get_children($cat_id);

    $cat = get_cat_info($cat_id);   // 获得分类的相关信息	

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
                $all_attr_list[$key]['attr_list'][0]['attr_id'] = $temp_arrt_url;
                
                foreach ($attr_list as $k => $v)
                {
                    $temp_key = $k + 1;
                    $temp_arrt_url_arr[$key] = $v['goods_id'];       //为url中代表当前筛选属性的位置变量赋值,并生成以‘.’分隔的筛选属性字符串
                    $temp_arrt_url = implode('.', $temp_arrt_url_arr);

                    $all_attr_list[$key]['attr_list'][$temp_key]['attr_value'] = $v['attr_value'];
                    $all_attr_list[$key]['attr_list'][$temp_key]['url'] = build_uri('category', array('cid'=>$cat_id, 'bid'=>$brand, 'price_min'=>$price_min, 'price_max'=>$price_max, 'filter_attr'=>$temp_arrt_url), $cat['cat_name']);
                    $all_attr_list[$key]['attr_list'][$temp_key]['attr_id'] = $temp_arrt_url;
                    
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

       /*  $smarty->assign('filter_attr_list',  $all_attr_list); */
        /* 扩展商品查询条件 */
        if (!empty($filter_attr))
        {
            $ext_sql = "SELECT DISTINCT(b.goods_id) FROM " . $ecs->table('goods_attr') . " AS a, " . $ecs->table('goods_attr') . " AS b " .  "WHERE ";
            $ext_group_goods = array();

            foreach ($filter_attr AS $k => $v)                      
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

    /* assign_template('c', array($cat_id)); */

    $count = get_cagtegory_goods_count($children, $brand, $price_min, $price_max, $ext);
    $max_page = ($count> 0) ? ceil($count / $size) : 1;
    if ($page > $max_page)
    {
        $page = $max_page;
    }
    
    $goodslist = category_get_goods($children, $brand, $price_min, $price_max, $ext, $size, $page, $sort, $order,$keyword);
	
    // 商品列表
    $jsonArray['data']['list'] = $goodslist;
    // 属性列表
    $jsonArray['data']['attrList'] = $all_attr_list;
    // 子类列表
    $jsonArray['data']['navigator'] = $mobble['middle'][$top_cat_id];
    // 当前分类id
    $jsonArray['data']['cat'] = $cat;
    
    $jsonArray['data']['filter_attr'] = $_REQUEST['filter_attr'];
    
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
    return $GLOBALS['db']->getRow('SELECT cat_name,cat_id,measure_unit, keywords, cat_desc, style, grade, filter_attr, parent_id FROM ' . $GLOBALS['ecs']->table('category') .
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

	if ($sort){
	    $sort = "g.".$sort;
	}
	$sql = 'SELECT g.goods_id,g.sort_order, g.goods_name, g.extension_code,g.region_ids, g.rule_ids, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot,g.goods_num, g.shop_price, ' .
			"g.promote_price, g.goods_type, g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img, " .
			'gs.* ' .
			'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
			'LEFT JOIN ' . $GLOBALS['ecs']->table('goods_spec') . ' AS gs ' .
			"ON gs.goods_id = g.goods_id " .
			"WHERE $where $ext GROUP BY g.goods_name ORDER BY $sort $order";
	
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
            //$arr[$row['goods_id']]['watermark_img'] =  $watermark_img;
            $temp['watermark_img'] =  $watermark_img;
        }

        ///$arr[$row['goods_id']]['goods_id']         = $row['goods_id'];
         $temp['goods_id']         = $row['goods_id'];
        if($display == 'grid')
        {
             $temp['goods_name']       = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        }
        else
        {
            $arr[$row['goods_id']]['goods_name']       = $row['goods_name'];
        }
        $temp['name']             = $row['goods_name'];
        $temp['goods_brief']      = $row['goods_brief'];
        $temp['goods_style_name'] = add_style($row['goods_name'],$row['goods_name_style']);
        $temp['market_price']     = price_format($row['market_price']);
        // 商品规格价格处理
        // TODO 当前卡规则不显示这个规格的情况下，去最低的规格价格、（未做）
        $spec_array = array('spec_nember'=> $row['spec_nember'], 'goods_id'=>$row['goods_id']);
        $temp['shop_price']       = get_spec_ratio_price($spec_array);
      
        $temp['spec_name']       = $row['spec_name'];
        $temp['spec_nember']       = $row['spec_nember'];
                
        $temp['type']             = $row['goods_type'];
        $temp['sort_order']       = $row['sort_order'];
		$temp['goods_num']        = $row['goods_num'];
        $temp['extension_code']   = $row['extension_code'];
        $temp['promote_price']    = ($promote_price > 0) ? price_format($promote_price) : '';
        $temp['goods_thumb']      = getImagePath($row['goods_thumb']);//get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $temp['goods_img']        = get_image_path($row['goods_id'], $row['goods_img']);
        
        $arr[] = $temp;
    }
	
    //error_log(var_export($arr,true),'3','error.log');
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
