<?php 

/**  
 * 百度地图画多边形，设置运费和配送时间
 * @var unknown
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_basic.php');

if (!isset($_REQUEST['act']))
{
    $_REQUEST['act'] = "index";
}

// 地图首页
if ($_REQUEST['act'] == 'index')
{
    $cityname = '';
    $cityid = isset($_REQUEST['cityid']) ? intval($_REQUEST['cityid']): 1 ;
    $citys = findData('region',"region_type=0 AND parent_id=0");
    foreach ($citys as &$city)
    {
        if ($cityid == $city['region_id']){
            $cityname = $city['region_name'];
            $city['active'] = 1;
        }else{
            $city['active'] = 0;
        }
    }
    $smarty->assign('cityname', $cityname);
    $smarty->assign('citys', $citys);
    $smarty->assign('supplier', findData('supplier',"status = 1"));
    $smarty->display('smap/smapIndex.dwt');
}

// 查看运费地图
elseif ($_REQUEST['act'] == 'showYunfei')
{
    $id = intval($_GET['id']);
    $isTime = intval($_GET['isTime']);
    $yunfei = array();
    $map = findData('peisongmap',"gongyingshang_id=$id AND isTime = ".$isTime." AND cityid=".$_SESSION['cityid']);
    foreach ((array)$map as $key=>$val){
        $yunfei[$key] = array(
            'yunfei'            =>$val['jiage'],
            'color'             =>$val['yanse'],
            'shipping_start'    =>$val['shipping_start'],
            'shipping_end'      =>$val['shipping_end'],
            'shipping_waiting'  =>$val['shipping_waiting'],
            'shipping_booking'  =>$val['shipping_booking']
        );
    }
    
    $smarty->assign('isTime', $isTime);
    $smarty->assign('yunfei', $yunfei);
    $smarty->assign('id', $id);
    exit($smarty->fetch('smap/smapYunfei.dwt'));
}
?>