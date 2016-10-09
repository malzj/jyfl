<?php
/**
 *  试听盛宴 ----》 演出
 *  
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_basic.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');
require(ROOT_PATH . 'mobile/includes/lib_yanchu.php');
require(ROOT_PATH . 'mobile/includes/lib_cinema.php');

$int_areaId = getAreaNo(0, 'yanchu');
$int_cateId = intval($_REQUEST['id']);

$page = isset($_REQUEST['page'])   && intval($_REQUEST['page'])  > 0 ? intval($_REQUEST['page'])  : 1;
$size = isset($_CFG['page_size'])  && intval($_CFG['page_size']) > 0 ? intval($_CFG['page_size']) : 10;

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
		ecs_header('location:index.php');
		exit;
	}
	// 搜索条件
	$find = null;
	$str_keyword = $_GET['yckeyword'];
	if (!empty($str_keyword))
	    $find = "item_name like '%$str_keyword%'";
	    
	$count = get_yanchu_count($int_cateId, $int_areaId, $find);
	$goods_list = get_ticket_list($int_cateId, $int_areaId, $size, $page,$find);
	$pager  = get_pager('yanchu.php', array('id'=>$int_cateId, 'keyword'=>$str_keyword), $count, $page);
	
	// 演出广告
	switch ($int_cateId)
	{
	    // 演唱会
	    case 1217:  
	        $imagAD = getNavadvs(2); 
	        $textAd = getNavadvs(8);
	        break;
	    // 话剧
	    case 1220:  
	        $imagAD = getNavadvs(4);
	        $textAd = getNavadvs(9);
	        break;
	    // 音乐会
	    case 1218:  
	        $imagAD = getNavadvs(5);
	        $textAd = getNavadvs(10);
	        break;
	       
	    // 亲子儿童
	    case 1227:  
	        $imagAD = getNavadvs(6);
	        $textAd = getNavadvs(11);
	        break;
	    // 戏曲综艺
	    case 1224:  
	        $imagAD = getNavadvs(7);
	        $textAd = getNavadvs(12);
	        break;
	}
	
	// 选择最后一页的五条数据
	$topTicket = get_ticket_list($int_cateId, $int_areaId, 5, 1,null,'id DESC');
	$smarty->assign('topticket', 		$topTicket);
	
	$smarty->assign('title',        get_title($int_cateId));
	$smarty->assign('cateid', 		$int_cateId);
	$smarty->assign('banner',       $imagAD);	
	$smarty->assign('text',         $textAd);
	$smarty->assign('pager', 		$pager);
	$smarty->assign('list', 		$goods_list);
	$smarty->display('yanchu/yanchuList.dwt');
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
	
	// 如果不存在就跳转到上一页
	if (empty($arr_itemInfo)){
		ecs_header('location:'.$_SERVER['HTTP_REFERER']);
		exit;
	}
	
	// 如果下架了，返回上一页
	if($arr_itemInfo['ifShow'] == 0)
	{
		show_message('您访问的项目已经售罄！请选择其他项目');
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
				$var['shStartDateFormat'] = !empty($var['shStartDate']) ? local_date('Y-m-d H:i', $var['shStartDate'] + 8 * 3600).'（周'.$arr_week[local_date('w', $var['shStartDate'] + 8 * 3600)].'）' : '';
				$var['shEndDateFormat']   = !empty($var['shEndDate']) ? local_date('Y-m-d H:i', $var['shEndDate'] + 8 * 3600).'（周'.$arr_week[local_date('w', $var['shEndDate'] + 8 * 3600)].'）' : '';
				
				// 时间处理
				$var['monthDay'] = local_date('m月d日', $var['shEndDate'] + 8 * 3600);				
				$var['week'] = '周'.$arr_week[local_date('w', $var['shEndDate'] + 8 * 3600)];
				$var['hours'] = local_date('H:i', $var['shEndDate'] + 8 * 3600);	
				
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

	// 产品所有的价格数据，以斜杠拼接
	$priceArray = array();
	foreach ((array) $arr_showtime as $key=>$val)
	{
	    if ( is_array($val))
	    {
	        foreach ($val['specs'] as $price)
	        {
	            $priceArray[] = $price['price'];
	        }
	        break;
	    }
	}
	
	sort($priceArray);

	$smarty->assign('priceString',        implode(' / ', $priceArray));
	$smarty->assign('title',              get_title($int_cateId)); 
	$smarty->assign('cateid',             $int_cateId);
	$smarty->assign('iteminfo',           $arr_itemInfo);
	$smarty->assign('showtime',       	  $arr_showtime);
	$smarty->assign('str_showtime',       json_encode($arr_showtime));
	$smarty->assign('backHtml',           getBackHtml( get_yanchu_back($int_cateId)));
	
	$smarty->display('yanchu/yanchuShow.dwt');
}


