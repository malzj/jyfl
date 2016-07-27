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