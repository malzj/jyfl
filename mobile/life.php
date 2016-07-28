<?php
/**
 * 生活
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

// 舌尖美食
if($_REQUEST['act'] == "getYGIndex")
{    
    $jsonArray['data']['banner'] = getBanner(37);
    $jsonArray['data']['cate'] = getCinemaCate(17,true);
    $jsonArray['data']['goods'] = getIndex(17);
    JsonpEncode($jsonArray);
}

// 优品生活
elseif($_REQUEST['act'] == "getNSIndex")
{
    $jsonArray['data']['banner'] = getBanner(38);
    $jsonArray['data']['cate'] = getCinemaCate(40,true);
    $jsonArray['data']['goods'] = getIndex(40);
    JsonpEncode($jsonArray);
}

// 商品
function getIndex($catid)
{
    $reutrnGoods = array();
    $category = getCinemaCate($catid, true);
    foreach ( (array)$category as $nav){
        $reutrnGoods[$nav['id']]['name'] = $nav['name']; 
        $reutrnGoods[$nav['id']]['id'] = $nav['cid']; 
        $tempGoods = category_get_goods_wap('g.cat_id '.db_create_in(array($nav['cid'])), 'g.sort_order ASC');
        if(!empty($tempGoods)){
            foreach ($tempGoods as &$goods){
                $goods['goods_thumb'] = getImagePath($goods['goods_thumb']);       
            }
        }
        $reutrnGoods[$nav['id']]['goods'] = $tempGoods;
    }
    
    return $reutrnGoods;
}

function getBanner($id){
    $banner = getNavadvs($id);
    foreach ($banner as $key=>&$val)
    {
        $val['ad_code'] = getImagePath($val['ad_code'],'ad');
    }
    return $banner;
}
