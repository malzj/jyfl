<?php
/**
 * 蛋糕
 * @var unknown_type
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_basic.php');

if (!isset($_REQUEST['act']))
{
	$_REQUEST['act'] = "index";
}

assign_template();

// 蛋糕首页
if($_REQUEST['act'] == "index")
{
    $adList = array();
    $navList = get_navigator();
    $cakeNav = $navList['middle'][16];
    if ( empty($cakeNav) )
    {
        show_message('没有可显示的内容！');
    }    
    
    // 卡规则过滤后的蛋糕分类id
    $cat_ids = array();
    foreach ( (array)$cakeNav['child'] as $nav){
        $cat_ids[]=$nav['cid'];
    }
    
    // 后台推荐的热卖商品
    $goodsIds = array();
    $goods_list = category_get_goods('g.cat_id '.db_create_in($cat_ids), 'g.sort_order ASC');
    foreach ($goods_list as $value){
        $goodsIds[] = $value['goods_id'];
    }
    
    // 口味数组
    $attrGoods = array();
    $attrName = get_cake_attr(347);
    
    // 将商品和口味关联起来
    if(!empty($goodsIds))
    {
        $goodsAttr = array();
        $sql = 'SELECT * FROM '.$GLOBALS['ecs']->table('goods_attr')." ". 
               "WHERE goods_id IN(".implode(',', $goodsIds).") ORDER BY goods_attr_id DESC";
        $goodsAttr = $GLOBALS['db']->getAll($sql);
      
        foreach ($attrName as $key=>$val)
        {
            $attrGoods[$key]['url'] = getCakeAttrUrl($val);
            $attrGoods[$key]['attrName'] = $val; 
            $attrGoods[$key]['attrNo'] = $key+1;
            
            $i = 0;
            foreach ($goodsAttr as $akey=>$aval)
            {
                $arr = array();
                
                // 商品数量够8个了就不处理了
                if (count($attrGoods[$key]['goods']) >=8)
                    continue;
                // 如果是广告位置，$i 就加2
                if (in_array($i, array(0,7)))
                    $i++;
                
                if ($val == $aval['attr_value']) 
                {
                    $attrGoods[$key]['goods'][$i] = $goods_list[$aval['goods_id']];
                    $i++;
                }            
            }
            
            // 加入广告信息
            $attrGoods[$key]['goods'][0] = attrAd($val, 1);
            $attrGoods[$key]['goods'][7] = attrAd($val, 2);        
            
            ksort($attrGoods[$key]['goods']);       
        }    
    }
    $smarty->assign('attrGoods', $attrGoods);
    // 当前城市支持的蛋糕品牌（导航信息）
    $smarty->assign('cakeNav', $cakeNav['child']);
    $smarty->assign('banner', getNavadvs(15));
    $smarty->assign('text', getNavadvs(16));
    $smarty->display('cake/cakeIndex.dwt');    
}

function getCakeAttrUrl($val)
{
    $value = array(
        '奶油口味'=>'category.php?id=4&filter_attr=29393.0',
        '巧克力味'=>'category.php?id=4&filter_attr=15636.0',
        '慕斯口味'=>'category.php?id=4&filter_attr=17328.0',
        '拿破仑味'=>'category.php?id=4&filter_attr=16622.0',
        '芝士口味'=>'category.php?id=4&filter_attr=15679.0',
        '鲜果口味'=>'category.php?id=4&filter_attr=25556.0',
        '冰淇淋味'=>'category.php?id=4&filter_attr=30471.0.0',
    );
    
    return $value[$val];
}

function category_get_goods($children, $sort)
{

    $display = $GLOBALS['display'];
    $where = "g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.supplier_status = 1 AND ".
        " g.is_delete = 0 AND ($children) ".
        " AND g.is_hot = 1 ";
   
    $where .= ' AND FIND_IN_SET('.intval($GLOBALS['int_cityId']).', g.region_ids) AND gs.default_show = 1 ';

    if (!empty($_SESSION['card_id']))
    {
        $where .= ' AND LOCATE(",'.$_SESSION['card_id'].',",g.rule_ids) = 0';
    }
   
    $sql = 'SELECT g.goods_id, g.goods_name,g.region_ids, g.rule_ids, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot,g.goods_num, g.shop_price, ' .
        "g.promote_price, g.goods_type, g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img, " .
        'gs.* ' .
        'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
        'LEFT JOIN ' . $GLOBALS['ecs']->table('goods_spec') . ' AS gs ' .
        "ON gs.goods_id = g.goods_id " .
        "WHERE $where $ext GROUP BY g.goods_name ORDER BY $sort ";

    $res = $GLOBALS['db']->getAll($sql);

    $arr = array();
    foreach ($res as $key=>$row)
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
    return $arr;
}