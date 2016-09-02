<?php
/**
 *  试听盛宴 ----》 演出
 *  
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(dirname(__FILE__) . '/includes/lib_yanchu.php');
require(dirname(__FILE__) . '/includes/lib_cinema.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');

$int_areaId = getAreaNo(0, 'yanchu');
$int_cateId = intval($_REQUEST['id']);

$page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
$size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 200;

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

if (!isset($_REQUEST['act']))
{
	$_REQUEST['act'] = "list";
}

assign_template();

$ratio = get_card_rule_ratio($int_cateId);

// 演出票列表
if ($_REQUEST['act'] == 'list')
{
	if (empty($int_cateId)){
	    $jsonArray['state'] = 'false';
		$jsonArray['message'] = '演出类型不正确';
	    JsonpEncode($jsonArray);
	}
	
	$count = get_yanchu_count($int_cateId, $int_areaId);
	$goods_list = get_ticket_list($int_cateId, $int_areaId, $size, $page);
	$pagebar = get_wap_pager($count, $size, $page, 'yanchu.php?id='.$int_cateId);
	
	$jsonArray['data']['list'] = $goods_list;
	$jsonArray['data']['pages'] = array( 'count'=>$count, 'size'=>$size, 'page'=>$page);
	JsonpEncode($jsonArray);
	
}

// 演出票详情
elseif ($_REQUEST['act'] == 'show')
{	
	$itemid = intval($_REQUEST['itemid']);	
	$obj_result   = getYCApi( array('itemId'=>$itemid), 'getItem');
	$int_showTimeCount = count($obj_result->showtimes->showtime);
	$int_specCount     = count($obj_result->showtimes->showtime->specs->spec);
	$arr_itemInfo = object2array($obj_result);
	
	$arr_itemInfo['startDate'] = !empty($arr_itemInfo['startDate']) ? local_date('Y-m-d', $arr_itemInfo['startDate'] + 8 * 3600) : '';
	$arr_itemInfo['endDate']   = !empty($arr_itemInfo['endDate'])   ? local_date('Y-m-d', $arr_itemInfo['endDate'] + 8 * 3600)   : '';
	
	// 如果不存在就跳转到网站首页
	if (empty($arr_itemInfo)){
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '产品不存在';
	    JsonpEncode($jsonArray);
	}
	
	// 如果下架了，返回上一页
	if($arr_itemInfo['ifShow'] == 0)
	{		
		$jsonArray['state'] = 'false';
		$jsonArray['message'] = '您访问的项目已经下架';
		JsonpEncode($jsonArray);
	}
	else 
	{
		$arr_week = array('日','一','二','三','四','五','六');
		$arr_showtime = array();
		if($arr_itemInfo['showtimes']){
			if ($int_showTimeCount > 1){
				$arr_showtime = $arr_itemInfo['showtimes']['showtime'];
			}else{
				$arr_showtime[0] = $arr_itemInfo['showtimes']['showtime'];
			}
			foreach ($arr_showtime as $key=>$var){
				$var['shStartDateFormat'] = !empty($var['shStartDate']) ? array(
						local_date('Y.m.d', $var['shStartDate'] + 8 * 3600),
						local_date('H:i', $var['shStartDate'] + 8 * 3600).'(周'.$arr_week[local_date('w', $var['shStartDate'] + 8 * 3600)].')'
					):array();
				$var['shEndDateFormat']   = !empty($var['shEndDate']) ? array(
						local_date('Y.m.d', $var['shEndDate'] + 8 * 3600),
						local_date('H:i', $var['shEndDate'] + 8 * 3600).'（周'.$arr_week[local_date('w', $var['shEndDate'] + 8 * 3600)].'）'
					):array();
//				$var['shStartDateFormat'] = !empty($var['shStartDate']) ? local_date('Y-m-d H:i', $var['shStartDate'] + 8 * 3600).'（周'.$arr_week[local_date('w', $var['shStartDate'] + 8 * 3600)].'）' : '';
//				$var['shEndDateFormat']   = !empty($var['shEndDate']) ? local_date('Y-m-d H:i', $var['shEndDate'] + 8 * 3600).'（周'.$arr_week[local_date('w', $var['shEndDate'] + 8 * 3600)].'）' : '';
				if ($var['specs']['spec']){
					$arr_spec = array();
					$arr_temp = array();
					if ($int_specCount > 1){
						$arr_temp = $var['specs']['spec'];
					}else{
						$arr_temp[0] = $var['specs']['spec'];
					}
					foreach ($arr_temp as $k=>$v){
						$arr_spec[$k]['specId']   = $v['@attributes']['specId'];
						$arr_spec[$k]['specType'] = $v['@attributes']['specType'];
						$arr_spec[$k]['market_price'] = $v['@attributes']['price'];
							
						if ($ratio !== false)
						{
							$arr_spec[$k]['price']    = ceil($v['@attributes']['price']*$ratio);
							$layout = !empty($v['@attributes']['layout']) ? $v['@attributes']['layout'] : 0 ;
							if ($layout !== 0)
							{
								preg_match('/\d+/', $layout, $arr);
								$layout2 = $arr[0]*$ratio;
								$new_layout = str_replace($arr[0], $layout2, $layout);
								$arr_spec[$k]['layout'] = $new_layout;
							}
						}
						else
						{
							$arr_spec[$k]['price']    = interface_price($v['@attributes']['price'],'piao',false);
							$arr_spec[$k]['layout']   = $v['@attributes']['layout'];
						}
						$arr_spec[$k]['stock']    = $v['@attributes']['stock'];
						//$arr_spec[$k]['layout']   = $v['@attributes']['layout'];
						$arr_spec[$k]['say']      = $v['@attributes']['say'];
					}
					unset($var['specs']['spec']);
					unset($arr_temp);
					$var['specs'] = $arr_spec;
				}
				$var['shStartDate'] = $var['shStartDate']  + 8 * 3600;
				$var['shEndDate'] = $var['shEndDate']   + 8 * 3600;
				$arr_showtime[$key] = $var;
			}
		}		
	}


	$jsonArray['data']['iteminfo'] = $arr_itemInfo;
	$jsonArray['data']['showtime'] = $arr_showtime;
	$jsonArray['data']['str_showtime'] = json_encode($arr_showtime);	
	JsonpEncode($jsonArray);
}


