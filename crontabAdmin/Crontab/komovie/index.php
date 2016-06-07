<?php
/**
 * 自动抓取 抠电影影院列表、电子兑换券影院列表
 * 
 * 计划任务规则：* * * * * 每周日的凌晨1点支持一次。
 * 
 * 电影的抓取：
 * 		1、抓取所有抠电影的影院。
 * 		2、cinema_list 为空的时候，把所有影院保存进去，并设置is_komovie = 1 (is_komovie=1支持在线选座，0不支持)。
 * 	    3、cinema_list 不为空的时候，对比cinema_list和api过来的影院，找到新增的影院和删除的影院。
 * 		4、将新增的影院添加的cinema_update 中，source = 0 。
 * 		5、检测删除的影院是否支持电子券或线下刷卡，只要有一样成立，就更新 is_komovie = 0 (0为不支持)。
 * 		6、删除的影院既不支持电子券，也不支持线下刷卡，删除该影院。
 * 
 * 电子券的抓取：
 * 		1、根据城市抓取所有中影支持电子券的影院。
 * 		2、得到cinema_list中支持电子券的影院。
 * 		3、循环api影院列表，得到新增的影院，条件：1、在cinema_list存在的《跳过》，2、在cinema_update中存在的《跳过》，3、不满足上面两个条件的就是新增的影院。
 * 		4、循环cinema_list中支持电子券的影院，得到删除的影院， 条件：1、只要在cinema_list中存在，在api中不存在的都视为已删除的影院。
 * 		5、保存新增的影院到ciname_update中，并设置is_update=1 (1代表新增的，0代表不是新增的)
 * 		6、对于删除的影院，我们只设置is_dzq=0 (0 代表不支持电子券兑换，1为支持)
 * 
 * @var unknown_type
 */
define('IN_ECS', true);

include_once(dirname(__FILE__) . '/lib/_init.php');
include_once(dirname(__FILE__) . '/lib/function.php');

include_once(ROOT_PATH . 'includes/lib_cardApi.php');

set_time_limit(0);
/* 来源  */
defined('CINEMA_SOURCE_KOMOVIE') or define('CINEMA_SOURCE_KOMOVIE', 0);	// 抠电影
defined('CINEMA_SOURCE_DZQ') or define('CINEMA_SOURCE_DZQ', 1);			// 电子兑换券

foreach ( array( 'komovie', 'dzq') as $api)
{
	switch ($api)
	{
		case 'komovie':
			
			// 抓取全国所有影院数据
			$cinema = array();
			$arr_result = getCDYapi( array('action'=>'cinema_Channel') );
			$page = 1;
			$totalPage = ceil( $arr_result['cinemasize'] / 40 );
			//$cinema = $arr_result['cinemas'];
			for($i=$page; $i<$totalPage; $i++)
			{
				$tmp_result = getCDYapi( array('action'=>'cinema_Channel', 'page'=>$page) );
				foreach ($tmp_result['cinemas'] as &$cinemaDetail)
				{
				    $resultDetail = getCDYapi( array('action'=>'cinema_Query', 'cinema_id'=>$cinemaDetail['cinemaId']) );
				    $cinemaDetail['logo']           = @$resultDetail['cinema']['logo'];
				    $cinemaDetail['galleries']      = serialize(@$resultDetail['cinema']['galleries']);
				    //$cinemaDetail['cinema_intro']   = @$resultDetail['cinema']['cinemaIntro'];
				    $cinemaDetail['drive_path']     = @$resultDetail['cinema']['drivePath'];
				    $cinemaDetail['open_time']      = @$resultDetail['cinema']['openTime'];
				}				
				cinema_list($tmp_result['cinemas'], $cinema);
				$page++;
			}		
				
			// 如果是第一次执行，就把抠电影的影院保存在数据库中。
			if ( hasData( 'cinema_list' ) == false )
			{
			    // 分批插入，一批1000条
			    // 总条数
			   /*  $total = count($cinema);
			    // 分多少批导入
			    $num = ceil($total/1000);
			    for ($i=0; $i<$num; $i++)
			    {
			        $startKey = $i*1000;
			        $newCinema = array_slice($cinema, $startKey, 1000);
			        saveKomovieData($newCinema);
			   } */
			   saveKomovieData($cinema);
			
			}else
			{
				// 所有影院列表（cinema_list）里面 komovie_cinema_id > 0 的
				$komovieData = findData('cinema_list', 'komovie_cinema_id > 0 ', 'komovie_cinema_id');
				$updateData = findData('cinema_update', 'is_update IN(0,1) AND source = 0 ', 'cinema_id');
				// $dataCinemaIds 数据库中的影院id集合， $apiCinemaIds 接口过来的影院id集合
				$dataCinemaIds = $apiCinemaIds = array();
				
				foreach ($komovieData as $cinemaId)
				{
					$dataCinemaIds[] = $cinemaId['komovie_cinema_id'];
				}
				foreach ($updateData as $cinemaId)
				{
					$dataCinemaIds[] = $cinemaId['cinema_id'];
				}
				/* 对比接口过来的数据和数据库里的数据  */
				// 新增的影院
				$updateCinemas = array();
				// 删除的影院
				$deleteCinemas = array();
				
				foreach ($cinema as $value)
				{
					$apiCinemaIds[] = $value['cinemaId'];
				}
				
				$updateCinemas = array_diff($apiCinemaIds, $dataCinemaIds);
				$deleteCinemas = array_diff($dataCinemaIds, $apiCinemaIds);
				
				// 处理删除的影院
				foreach ($deleteCinemas as $cid)
				{
					// 检查这个影院是否支持刷卡、电子券，有一项支持就返回true，否则 false
					if (checkState( $cid ))
					{
						// 设置在线选座状态为不支持
						updateKomovieState( $cid );
					}
					else
					{
						// 没有任何支持的项目，直接删除影院
						dropCinemas( $cid );
					}
				}
				
				// 处理新增的影院
				$apiCinemaArray = array();
				foreach ($cinema as $apiList)
				{
					if (in_array( $apiList['cinemaId'], $updateCinemas))
					{
						$apiCinemaArray[] = $apiList;
					}
				}
				if ( !empty($apiCinemaArray) )
				{
					saveUpdateData($apiCinemaArray, 'update');
				}				
			}			
			
			break;
		case 'dzq':
			// 影院列表
			$cinema = array();
			
			// 得到所有一线城市的影院
			$regionResult = findData('region', 'parent_id=0 AND dianying_id > 0');
			foreach ($regionResult as $region)
			{
				// 循环得到每个城市的所有影院
				$arr_result = getYYApi(array('AreaNo'=>$region['dianying_id'], 'filmNo'=>''), 'getCinemas');
				if ( empty($arr_result['body']['item']) ) 
					continue;
				
				foreach ($arr_result['body']['item'] as $ckey=>$cinemas)
				{
					// 删除不支持电子券的影院
					if (in_array($cinemas['SellFlag'], array(1,4)))
					{
						unset( $arr_result['body']['item'][$ckey]);
					}	
				}
				
				$cinema = array_merge($cinema, $arr_result['body']['item']);
			}
			
			// 所有影院列表（cinema_list）里面 dzq_cinema_id > 0 的
			$cinemaData = findData('cinema_list', 'dzq_cinema_id > 0 ', 'dzq_cinema_id');
			// $dataCinemaIds 数据库中的影院id集合， $apiCinemaIds 接口过来的影院id集合
			$dataCinemaIds = $apiCinemaIds = array();			
			// 新增的影院
			$updateCinemas = array();
			// 删除的影院
			$deleteCinemas = array();
			
			foreach ($cinemaData as $cinemaId)
			{
				$dataCinemaIds[] = $cinemaId['dzq_cinema_id'];
			}
			
			// 找到新增的影院
			foreach ($cinema as $list)
			{
				// 把接口过来的所有影院的id放到集合中
				$apiCinemaIds[] = $list['CinemaNo']; 
				
				// 如果在cinema_list存在，跳出本次循环
				if ( in_array( $list['CinemaNo'], $dataCinemaIds) )
				{
					continue;
				}	
				// 如果在cinema_update存在，跳出本次循环
				if ( hasData('cinema_update', 'cinema_id="'.$list['CinemaNo'].'" AND source ='.CINEMA_SOURCE_DZQ ))
				{
					continue;
				}
				
				$updateCinemas[] = $list;
			}
			
			// 找到删除的影院
			foreach ($dataCinemaIds as $cinemaid)
			{
				if ( !in_array($cinemaid, $apiCinemaIds ) )
				{
					$deleteCinemas[] = $cinemaid;
				}
			}
			
			// 更新操作
			if ( !empty($updateCinemas) )
			{
				saveUpdateData($updateCinemas);
			}
			
			// 删除的影院，设置电子券为不支持
			if ( !empty($deleteCinemas) )
			{
				foreach ($deleteCinemas as $deleteCinemaid)
				{
					updateDzqState($deleteCinemaid);
				}				
			}
			
			break;
		default:
			break;
	}
}
