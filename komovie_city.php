<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
	$smarty->caching = true;
}

$str_action = !empty($_REQUEST['act']) ? $_REQUEST['act'] : 'city';
assign_template();

//根据城市id获取影院区域编号
$int_areaNo = getAreaNo();

include_once(ROOT_PATH . 'includes/lib_cardApi.php');

// 电影列表
if ($str_action == 'moveList'){	

	$in_city_id = $_GET['cid'];
	$arr_param = array(
			'action'=>'movie_Query',
			'city_id'=>$in_city_id
	);
	$arr_result = getCDYapi($arr_param);

	if(count($arr_result['movies'])){
		foreach($arr_result as $city){
			foreach($city as $k=>$c){
				echo "<a href='komovie_city.php?act=show&cid=".$in_city_id."&movieid=".$c['movieId']."' style='display:block; height:25px;'>".$c['movieName'].'</a><br>';
			}
				
		}
	}
	echo '<pre>';
	print_r($arr_result['movies']);
	echo '</pre>';
	// 城市列表
}else if ($str_action == 'city'){
	
	$sql = 'SELECT *'.
			' FROM ' .$ecs->table('region').
			" WHERE komovie_id > 0";
	$img_list = $db->getAll($sql);
	$komovieIds = array();
	foreach($img_list as $lists){
		$komovieIds[] = $lists['komovie_id'];
	}
	
	$arr_param = array(
			'action'=>'city_Query'
	);
	$arr_result = getCDYapi($arr_param);
	
	$total = count($arr_result['cities']);
	$total2 = count($komovieIds);
	$total3 = $total-$total2;
	echo '<pre>';
	echo '全部：'.$total.'<br>';
	echo '已有：'.$total2.'<br>';	
	echo '还差：'.$total3;	
	echo '</pre>';
	
	if(count($arr_result['cities'])){
		$i = 0;
		foreach($arr_result['cities'] as $k=>$c){
			//if(!in_array($c['cityId'], $komovieIds)){				
				echo "<a href='komovie_city.php?act=moveList&cid=".$c['cityId']."' style='display:inline-block;width:170px; height:25px;'>".$c['cityName'].'</a>';
				$i++;
			//}				
			if($i%5==0) echo '<br>';
		}
				
	}

	// 影片详情
}else if($str_action == 'show'){

	$in_city_id = $_GET['cid'];
	$in_movie_id = $_GET['movieid'];
	// 影片详细
	$arr_param = array(
			'action'=>'movie_Query',
			'movie_id'=>$in_movie_id
	);
	
	$str_cacheName3 = 'movie_Query_'.$in_movie_id;  //缓存名称   接口名称+电影ID
	$arr_result = F($str_cacheName3, '', 1800, $in_city_id.'/');
	if (empty($arr_result)){
		$arr_result3_tmp = getCDYapi($arr_param);
		$arr_result = $arr_result3_tmp['movie'];
		F($str_cacheName3, $arr_result, 1800, $in_city_id.'/');//缓存半小时
	}

	if(count($arr_result)){

		echo "<span style='display:block'> 影片名称：".$arr_result['movieName']."</span>";
		echo "<span style='display:block'> 演员列表：".$arr_result['actor']."</span>";
		echo "<span style='display:block'> 上映国家：".$arr_result['country']."</span>";

	}

	//通过影片id和城市id找到所有影院
	$arr_param2 = array(
			'action'=>'cinema_Query',
			'city_id'=>$in_city_id,
			'movie_id'=>$in_movie_id
	);
	$str_cacheName = 'cinema_Query_'.$in_movie_id;  //缓存名称   接口名称+电影ID
	$arr_result2 = F($str_cacheName, '', 1800, $in_city_id.'/');
	if (empty($arr_result2)){
		$arr_result2_tmp = getCDYapi($arr_param2);
		$arr_result2 = $arr_result2_tmp['cinemas'];
		F($str_cacheName, $arr_result2, 1800, $in_city_id.'/');//缓存半小时
	}


	// 整理影院信息
	$districts = $day = $cinemas = array();

	//参数解析
	$tday = 1;
	$district_id = isset($_GET['districtid']) ? $_GET['districtid'] : 0 ;
	$cinema_id = isset($_GET['cinemaid']) ? $_GET['cinemaid'] : 0 ;

	if(count($arr_result2)){
		foreach($arr_result2 as $cinema){
			// 把城区保存在districts里面
			$districts[] = array(
					'name'=>$cinema['districtName'],
					'id'=>$cinema['districtId']
			);
			// 如果选择了城区，则筛选城区
			if($district_id > 0 && $cinema['districtId']==$district_id){
				$cinemas[] = $cinema;
			}
			if($district_id == 0){
				$cinemas[] = $cinema;
			}
		}
	}


	// 不存在区id就设置城市id
	if($district_id == 0) {
		$district_id = $in_city_id;
	}

	$default_cinema = current($cinemas);
	// 显示影片排期
	$arr_param3 = array(
			'action'=>'plan_Query',
			'cinema_id'=>$cinema_id > 0 ? $cinema_id : $default_cinema['cinemaId'],
			'movie_id'=>$in_movie_id
	);
	$cinema_id = $cinema_id > 0 ? $cinema_id : $default_cinema['cinemaId'];
	$str_cacheName2 = 'plan_Query_'.$cinema_id.'_'.$in_movie_id;  //缓存名称   接口名称+影院ID+电影ID
	$arr_result3 = F($str_cacheName2, '', 1800, $in_city_id.'/');
	$arr_result3_tmp = getCDYapi($arr_param3);
	echo '<pre>';
	print_r($arr_result3_tmp);
	echo '</pre>';
	if(empty($arr_result3)){
		$arr_result3_tmp = getCDYapi($arr_param3);
		$arr_result3 = $arr_result3_tmp['plans'];
		F($str_cacheName2, $arr_result3, 1800, $in_city_id.'/');
	}

	//======== 显示内容 ==============//
	foreach($districts as $dis){
		echo '<span style="display:inline-block; width:70px;margin:10px 0px;"><a href="komovie_city.php?act=show&cid='.$in_city_id.'&movieid='.$in_movie_id.'&districtid='.$dis['id'].'">'.$dis['name'].'</a></span>';
	}
	echo '<br>';
	foreach($cinemas as $val){
		echo '<span style="display:inline-block; margin:10px 10px; 10px 0"><a href="komovie_city.php?act=show&cid='.$in_city_id.'&movieid='.$in_movie_id.'&districtid='.$district_id.'&cinemaid='.$val['cinemaId'].'">'.$val['cinemaName'].'</a></span>';
	}

	echo '<table><tr><th width="100">场次</th> <th width="150">语言版本</th> <th width="100">放映厅</th> <th width="100">价格</th><th width="100">操作</th></tr>';
	foreach($arr_result3 as $val2){
		echo '<tr>';
		echo '<td align="center">'.date('Y-m-d H:i',strtotime($val2['featureTime'])).'</td>';
		echo '<td align="center">'.$val2['language'].'</td>';
		echo '<td align="center">'.$val2['hallName'].'</td>';
		echo '<td align="center">'.$val2['vipPrice'].'</td>';
		echo '<td align="center"><a href="komovie_city.php?act=xuanzuo&hallno='.$val2['hallNo'].'&planid='.$val2['planId'].'&movieid='.$val2['movieId'].'&cinemaid='.$val2['cinemaId'].'">在线选座</a></td>';
		echo '</tr>';
	}
}else if($str_action == 'xuanzuo'){
	
	$hallno = isset($_GET['hallno']) ? $_GET['hallno'] : 0 ; 		// 厅号
	$planid = isset($_GET['planid']) ? intval($_GET['planid']) : 0 ;		// 场次
	$cinemaid = isset($_GET['cinemaid']) ? intval($_GET['cinemaid']) : 0 ;	// 影院
	$movieid = isset($_GET['movieid']) ? intval($_GET['movieid']) : 0 ;		// 电影
	
	// 座位类图
	include_once(ROOT_PATH . 'includes/lib_seatQuery.php');
	$arr_param = array(
			'action'=>'seat_Query',
			'plan_id'=> $planid,
			'hall_id'=>$hallno,
			'cinema_id'=>$cinemaid
	);
	
	$arr_result = getCDYapi($arr_param);
	$setField = array(
			'hallName'=>'hallId',			// 厅号属性名
			'graphRowName'=>'graphRow',  	// 排号属性名     X
			'graphColName'=>'graphCol',		// 座位号属性名  	Y
			'seatRowName' =>'seatRow',
			'width'		  =>699					// 影院的总宽
	);
	$seatQuery = new seatQueryInfo($setField);
	$seatInfo = $seatQuery->getSeatInfo($arr_result['seats']);
	
}
// 订单查询
elseif($str_action == 'order')
{
	$arr_result = getCDYapi(array('action'=>'order_Query', 'order_id'=>'a1457761842024236921') );
	echo '<Pre>';
	print_r($arr_result);
	echo 123;
	echo '</pre>';
}
elseif ($str_action == 'cinema_list')
{
	$arr_result = getCDYapi( array('action'=>'cinema_Channel', 'page'=>30) );
	echo '<pre>';
	print_r($arr_result);
	echo '</pre>';
}
elseif ($str_action == 'cinema_query')
{
    $arr_result = getCDYapi( array('action'=>'cinema_Query', 'cinema_id'=>69) );
    echo '<pre>';
    print_r($arr_result);
    echo '</pre>';
}
?>