<?php
/**  
 *  queryCinemas    影院列表
 *  queryHalls      影院的影厅列表
 *  querySeats      影厅的座位图
 *  
 *  queryFilms      影院列表
 *  queryShows      场次列表
 *  
 * @var unknown
 */
phpinfo();
exit;

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_cardApi.php');

if ((DEBUG_MODE & 2) != 2)
{
	$smarty->caching = true;

}


// 影院列表
if ($_GET['a'] == 'cinema')
{
    $cinemas = getZYapi('queryCinemas');
    foreach ( $cinemas['cinemas'] as $cinema)
    {
        echo '影院名称：'.$cinema['name'].'<br>';
        echo '影院地址：'.$cinema['address'].'<br>';
        echo '城市：'.$cinema['city'].'【'.$cinema['cityCode'].'】'.$cinema['county'].'['.$cinema['countyCode'].']<br>';
        echo '影厅数量：'.$cinema['hallCount'].'<br>';
        echo 'LOGO：'.$cinema['logo'].'<br>';
        echo '客服电话：'.$cinema['tel'].'<br>';
        echo '终端位置说明：'.$cinema['devicePos'].'<br>';
        echo '终端位置图片：'.$cinema['deviceImg'].'<br>';
        echo '综合评分：'.$cinema['grade'].'<br>';
        echo '简介：'.$cinema['intro'].'<br>';
        
        echo '公交路线：'.$cinema['busLine'].'<br>';
        echo '经度：'.$cinema['longitude'].'<br>';
        echo '纬度：'.$cinema['latitude'].'<br>';
        echo '特色：'.$cinema['feature'].'<br>';
        echo '商品类型：'.$cinema['goodsType'].'<br>';
        echo '短信类型：'.$cinema['printMode'].'<br>';
        echo '<a href="a.php?a=queryHalls&code='.$cinema['code'].'">影厅列表</a><br><br>';
        echo '<a href="a.php?a=queryShows&code='.$cinema['code'].'">查看场次</a><br><br>';
    }
}
// 影厅列表
elseif( $_GET['a'] == 'queryHalls')
{
    $cinemas = getZYapi('queryHalls', array('cinemaCode'=>$_GET['code']));
    foreach ( $cinemas['halls'] as $halls)
    {
        echo '影厅编号：'.$halls['code'].'<br>';
        echo '影厅名称：'.$halls['name'].'<br>';
        echo '座位数量：'.$halls['seatCount'].'<br>';
        echo '<a href="a.php?a=querySeats&code='.$_GET['code'].'&hall='.$halls['code'].'">座位图</a><br><br>';
        
    }
}
// 影厅座位列表
elseif ($_GET['a'] == 'querySeats')
{
    $cinemas = getZYapi('querySeats', array('cinemaCode'=>$_GET['code'], 'hallCode'=>$_GET['hall']));
    var_dump($cinemas);
}

// 影片列表
elseif( $_GET['a'] == 'movies')
{
    $cinemas = getZYapi('queryFilms', array('startDate'=>date('Y-m-d',time())));
    foreach ($cinemas['films'] as $films)
    {
        echo '编号：'.$films['code'].'<br>';
        echo '名称：'.$films['name'].'<br>';
        echo '时常：'.$films['duration'].'<br>';
        echo '上映时间：'.$films['publishDate'].'<br>';
        echo '发行商：'.$films['publisher'].'<br>';
        echo '导演：'.$films['director'].'<br>';
        echo '演员：'.$films['cast'].'<br>';
        echo '介绍：'.$films['intro'].'<br>';
        echo '放映类型：'.$films['showTypes'].'<br>';
        echo '发行国家：'.$films['country'].'<br>';
        echo '影片类型：'.$films['type'].'<br>';
        echo '语言：'.$films['language'].'<br>';
        echo '海报：<img src="'.$films['poster'].'" width="200"><br>';
        echo '看点：'.$films['highlight'].'<br>';
        //echo '<a href="a.php?a=queryShows&">查看排期</a><br><br>';
    }
}
// 影片列表
elseif( $_GET['a'] == 'queryShows')
{
    echo $_GET['code'];
    $cinemas = getZYapi('queryShows', array('cinemaNo'=>$_GET['code'], 'status'=>1, 'startDate'=>date('Y-m-d',time())));
    var_dump($cinemas);
}

?>