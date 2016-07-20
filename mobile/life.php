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
    $jsonArray['data']['goods'] = getIndex(17,36);
    JsonpEncode($jsonArray);
}

// 优品生活
elseif($_REQUEST['act'] == "getNSIndex")
{
    $jsonArray['data']['banner'] = getBanner(38);
    $jsonArray['data']['cate'] = getCinemaCate(40,true);
    $jsonArray['data']['goods'] = getIndex(40,39);
    JsonpEncode($jsonArray);
}


/**
 * @param unknown $cid   分类id
 * @param unknown $aid   广告为id
 **/
function getIndex($cid, $aid){
    
    $list = array();
    $childNav = getCinemaCate($cid,true);
    
    // 楼层广告
    foreach ($childNav as $key=>$value)
    {
        $list[$key]['name'] = $value['name'];
        $list[$key]['id'] = $value['id'];
        $list[$key]['ad_list'][1] = attrAd($value['name'],1,$aid);
        $list[$key]['ad_list'][2] = attrAd($value['name'],2,$aid);
        $list[$key]['ad_list'][3] = attrAd($value['name'],3,$aid);
        $list[$key]['ad_list'][4] = attrAd($value['name'],4,$aid);
    }
    return $list;
}

/* banner  */
function getBanner($id){
    $banner = getNavadvs($id);
    foreach ($banner as $key=>&$val)
    {
        $val['ad_code'] = getImagePath($val['ad_code'],'ad');
    }
    return $banner;
}