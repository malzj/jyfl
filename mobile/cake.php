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

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);


// 蛋糕首页
if($_REQUEST['act'] == "getIndex")
{
    $category = getCinemaCate(16,true);
    if ( empty($category) )
    {
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = $category;
        JsonpEncode($jsonArray);
    }
    
    // 卡规则过滤后的蛋糕分类id
    $cat_ids = array();
    foreach ( (array)$category as $nav){
        $cat_ids[]=$nav['cid'];
    }

    // 后台推荐的热卖商品
    $goodsIds = array();
    $goods_list = category_get_goods('g.cat_id '.db_create_in($cat_ids), 'g.sort_order ASC,g.goods_id DESC');
    foreach ($goods_list as $value){
        $goodsIds[] = $value['goods_id'];
    }
    
    // 口味数组
    $attrGoods = array();
    $attrName = get_cake_attr(347);
    
    // 记录显示在前台的商品id
    $showGoodsId = array();
    
    // 将商品和口味关联起来
    if(!empty($goodsIds))
    {
        $goodsAttr = array();
        $sql = 'SELECT * FROM '.$GLOBALS['ecs']->table('goods_attr')." ".
            "WHERE goods_id IN(".implode(',', $goodsIds).") ORDER BY goods_attr_id DESC";
        $goodsAttr = $GLOBALS['db']->getAll($sql);
        
        foreach ($attrName as $key=>$val)
        {
            $attrGoods[$key]['filterId'] = getCakeAttrUrl($val);
            $attrGoods[$key]['brandId'] = 4;
            $attrGoods[$key]['attrName'] = $val;
            $attrGoods[$key]['attrNo'] = $key+1;
            
            foreach ($goodsAttr as $akey=>$aval)
            {
                $arr = array();
            
                // 商品数量够4个了就不处理了
                if (count($attrGoods[$key]['goods']) >=4)
                    continue;
            
                if ($val == $aval['attr_value'])
                {
                    $attrGoods[$key]['goods'][] = $goods_list[$aval['goods_id']];
                    $showGoodsId[] = $aval['goods_id'];
                }
            }
        }
    }
    
    // 图片替换为绝对地址
    foreach ($attrGoods as $key=>&$attr)
    {
        foreach ( $attr['goods'] as $key2=>&$goods)
        {
            $goods['goods_thumb'] = getImagePath($goods['goods_thumb']);
        }
    }
    
    $jsonArray['data']['cate'] = $category;
    $jsonArray['data']['banner'] = getBanner(34);
    $jsonArray['data']['goods'] = $attrGoods;
    JsonpEncode($jsonArray);
}

function getCakeAttrUrl($val)
{
    $value = array(
        '奶油口味'=>'29393.0',
        '巧克力味'=>'15636.0',
        '慕斯口味'=>'17328.0',
        '拿破仑味'=>'16622.0',
        '芝士口味'=>'15679.0',
        '鲜果口味'=>'25556.0',
        '冰淇淋味'=>'30471.0.0',
    );
    
    return $value[$val];
}

function getBanner($id){
    $banner = getNavadvs($id);
    foreach ($banner as $key=>&$val)
    {
        $val['ad_code'] = getImagePath($val['ad_code'],'ad');
    }
    return $banner;
}