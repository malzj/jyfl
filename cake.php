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

    // 口味数组
    $attrGoods = array();
    $attrName = get_cake_attr(2);
    foreach ($attrName as $key=>$val)
    {
        $goodsIds = array();
        $attrGoods[$key]['url'] = getCakeAttrUrl($val);
        $attrGoods[$key]['attrName'] = $val; 
        $attrGoods[$key]['attrNo'] = $key+1;
        $goodsAttr = $GLOBALS['db']->getAll('SELECT goods_id FROM '.$GLOBALS['ecs']->table('goods_attr')." WHERE attr_value = '".$val."' ORDER BY goods_attr_id DESC LIMIT 10");
        foreach ($goodsAttr as $_K=>$_v){ $goodsIds[] = $_v['goods_id']; }

        if ( !empty($goodsIds)) 
        {
            $sql = 'SELECT g.goods_id, g.goods_name,g.region_ids, g.rule_ids, g.goods_name_style, g.market_price, g.is_new, g.is_best, g.is_hot,g.goods_num, g.shop_price, ' .
                "g.promote_price, g.goods_type, g.promote_start_date, g.promote_end_date, g.goods_brief, g.goods_thumb , g.goods_img, " .
                'gs.* ' .
                'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
                'LEFT JOIN ' . $GLOBALS['ecs']->table('goods_spec') . ' AS gs ' .
                "ON gs.goods_id = g.goods_id " .
                "WHERE g.goods_id IN(".implode(',', $goodsIds).") ORDER BY g.goods_id DESC";
            
            $res = $GLOBALS['db']->selectLimit($sql, 10, 0);
            $arr = array();
            $i = 0;
            while ($row = $GLOBALS['db']->fetchRow($res))
            {
                $arr[$i]['name']             = $row['goods_name'];
                $arr[$i]['goods_brief']      = $row['goods_brief'];
                $arr[$i]['goods_style_name'] = add_style($row['goods_name'],$row['goods_name_style']);
                $arr[$i]['market_price']     = price_format($row['market_price']);
                // 商品规格价格处理
                // TODO 当前卡规则不显示这个规格的情况下，去最低的规格价格、（未做）
                $spec_array = array('spec_nember'=> $row['spec_nember'], 'goods_id'=>$row['goods_id']);
                $arr[$i]['shop_price']       = get_spec_ratio_price($spec_array);
                
                $arr[$i]['spec_name']       = $row['spec_name'];
                $arr[$i]['spec_nember']       = $row['spec_nember'];
                
                $arr[$i]['type']             = $row['goods_type'];
                $arr[$i]['goods_num']        = $row['goods_num'];
                $arr[$i]['goods_thumb']      = get_image_path($row['goods_id'], $row['goods_thumb'], true);
                $arr[$i]['goods_img']        = get_image_path($row['goods_id'], $row['goods_img']);
                $arr[$i]['url']              = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
                $i++;
            }
            
            // 加入广告信息
           $arr[0] = attrAd($val, 1);
           $arr[7] = attrAd($val, 2);
  
           ksort($arr);
           $attrGoods[$key]['goods'] = $arr;            
        }        
    }
    $smarty->assign('attrGoods', $attrGoods);
    // 当前城市支持的蛋糕品牌（导航信息）
    $smarty->assign('cakeNav', $cakeNav);
    $smarty->assign('banner', getNavadvs(15));
    $smarty->assign('text', getNavadvs(16));
    $smarty->display('cake/cakeIndex.dwt');    
}

function getCakeAttrUrl($val)
{
    $value = array(
        '奶油口味'=>'category.php?id=4&filter_attr=0.12',
        '巧克力味'=>'category.php?id=4&filter_attr=0.6',
        '慕斯口味'=>'category.php?id=4&filter_attr=0.4',
        '拿破仑味'=>'category.php?id=4&filter_attr=0.8',
        '芝士口味'=>'category.php?id=4&filter_attr=0.2',
        '鲜果口味'=>'category.php?id=4&filter_attr=0.10',
    );
    
    return $value[$val];
}