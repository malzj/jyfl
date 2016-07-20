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
    // 口味数组
    $attrGoods = array();
    $attrName = get_cake_attr(347);
    
    foreach ($attrName as $key=>$val)
    {
        $attrGoods[$key]['filterId'] = getCakeAttrUrl($val);
        $attrGoods[$key]['brandId'] = 4;
        $attrGoods[$key]['attrName'] = $val;
        $attrGoods[$key]['attrNo'] = $key+1;
        // 加入广告信息
        $attrGoods[$key]['goods'][] = attrAd($val, 1, 35);
        $attrGoods[$key]['goods'][] = attrAd($val, 2, 35);
        $attrGoods[$key]['goods'][] = attrAd($val, 3, 35);
        $attrGoods[$key]['goods'][] = attrAd($val, 4, 35);
    }
    
    // 图片替换为绝对地址
    foreach ($attrGoods as $key=>&$attr)
    {
        foreach ( $attr['goods'] as $key2=>&$goods)
        {
            $goods['ad_code'] = getImagePath($goods['ad_code'],'ad');
        }
    }
    
    $jsonArray['data']['cate'] = getCinemaCate(16,true);
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