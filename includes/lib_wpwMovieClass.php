<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/8/31
 * Time: 9:11
 */
require_once(dirname(__FILE__).'/httpRequest.php');

class wpwMovie
{
    private $url = 'http://test.api.wangpiao.com';
    //用户名
    private $username = 'test1';
    //秘钥
    private $key = '4vYwB5csWrdLbFkG';
    //实例化httpRequest类
    private $httpRequest;
    //

    public function __construct(){
        $this -> httpRequest = new HttpRequest();
    }

    /**
     * 请求接口函数
     * @param $res_params   请求参数
     * @return array
     */
    private function sendRequest($res_params){
//        $start=microtime(true);
        $str_param = $this -> httpRequest -> buildUrlQuery($res_params);
        $result = $this -> httpRequest -> curl($this->url,$str_param,'POST');
//        $end = microtime(true);
//        echo round($end-$start,3);
        return json_decode($result,true);
    }
    /**
     * 生成签名后参数
     * @param $target   操作命令
     * @return string
     */
    protected function makeParams($params){
        $params['UserName'] = $this -> username;
        ksort($params,SORT_STRING);
        $str_params = implode($params);
        $str_params = $str_params.$this->key;
        $sign = md5($str_params);
        $params['Sign'] = $sign;
        return $params;
    }

    /**
     * 执行请求接口命令
     * TODO:以下方法皆可用此方法实现
     * @param $target   操作命令
     * @param $params   参数
     * @return array
     */
    public function doTarget($target,$params=array()){
        $params['Target'] = $target;
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 影院、非合作影院信息查询
     * @param  $target  操作命令：影院（Base_Cinema）【默认】，非合作影院（Base_CinemaUnCooperation）
     * @return array
     */
    public function getCinemas($target='Base_Cinema'){
        $params['Target'] = $target;
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 根据城市，（影片），日期查询有放映计划的影院
     * @param $city_id  城市标志，数值型
     * @param $date     日期，格式yyyy-MM-dd HH:mm:ss
     * @param $film_id  影片标识
     * @return array
     */
    public function baseCinemaQuery($city_id,$date,$film_id=''){
        $params['Target'] = 'Base_CinemaQuery';
        $params['CityID'] = $city_id;
        $params['Date'] = $date;
        $params['FilmID'] = empty($film_id)?'':$film_id;
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 所有城市查询
     * @return array
     */
    public function baseCity(){
        $params['Target'] = 'Base_City';
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 已有业务城市查询
     * @return array
     */
    public function baseCityBll(){
        $params['Target'] = 'Base_CityBll';
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 地区查询
     * @return array
     */
    public function baseDistrict(){
        $params['Target'] = 'Base_District';
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 商圈查询
     * @return array
     */
    public function baseTradingArea(){
        $params['Target'] = 'Base_TradingArea';
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 地铁查询
     * @return array
     */
    public function baseSubWay(){
        $params['Target'] = 'Base_SubWay';
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 院线查询
     * @return array
     */
    public function baseCinemaLine(){
        $params['Target'] = 'Base_CinemaLine';
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }
    /**
     * 影厅信息查询
     * @return array
     */
    public function baseHall($cinema_id){
        $params['Target'] = 'Base_Hall';
        $params['CinemaId'] = $cinema_id;
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }
    /**
     * 座位信息查询
     * @param $hall_id      影厅ID
     * @param $cinema_id    影院ID
     * @return array
     */
    public function baseHallSeat($hall_id,$cinema_id){
        $params['Target'] = 'Base_HallSeat';
        $params['CinemaId'] = $cinema_id;
        $params['HallID']   = $hall_id;
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 已售出座位信息查询
     * @param $show_index   场次号
     * @param $cinema_id    影院标识
     * @return array
     */
    public function baseSellSeat($show_index,$cinema_id){
        $params['Target'] = 'Base_SellSeat';
        $params['ShowIndex']   = $show_index;
        $params['CinemaId'] = $cinema_id;
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 正在上映的影片信息查询
     * @param $city_id      城市标识(可空）
     * @param $date         日期，如（2012-06-06）,默认为当天（可空）
     * @param $cinema_id    影院标识（可空）
     * @return string
     */
    public function baseFilm($city_id='',$date='',$cinema_id=''){
        $params['Target'] = 'Base_Film';
        $params['CityID']   = $city_id;
        $params['Date']     = $date;
        $params['CinemaId'] = $cinema_id;
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 即将上映的影片信息查询
     * @param $date         日期，如（2012-06-06）,默认为当天(可空）
     * @param $cinema_id    影院标识(可空）
     * @return string
     */

    public function baseFilmIM($date='',$cinema_id=''){
        $params['Target'] = 'Base_FilmIM';
        $params['Date']     = $date;
        $params['CinemaId'] = $cinema_id;
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 影片预告查询
     * @param $film_id  影片标识
     * @return array
     */
    public function baseFilmHE($film_id){
        $params['Target'] = 'Base_FilmHE';
        $params['FilmID']     = $film_id;
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 放映计划查询
     * @param $cinema_id        影院ID
     * @param $date             日期，格式：yyyy-MM-dd HH:mm:ss
     * @param string $film_id   影片ID,可为空（默认空）
     * @return array
     */
    public function baseFilmShow($cinema_id,$date,$film_id=''){
        $params['Target'] = 'Base_FilmShow';
        $params['CinemaID']     = $cinema_id;
        $params['Date']     = $date;
        $params['FilmID']     = $film_id;
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 影片剧照查询
     * @param $film_id  电影ID
     * @return array
     */
    public function baseFilmPhotos($film_id){
        $params['Target'] = 'Base_FilmPhotos';
        $params['FilmID'] = $film_id;
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 全国影讯查询
     * @param $cinema_id    影院标识（可空）
     * @param $city_id      城市标识(可空）
     * @return array
     */
    public function baseFilmView($cinema_id='',$city_id=''){
        $params['Target'] = 'Base_FilmView';
        $params['CinemaID'] = $cinema_id;
        $params['CityID'] = $city_id;
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    /**
     * 在线影讯有效性
     * @param $cinema_id        影院标识
     * @param $date             放映日期
     * @param $show_index       放映流水号，多个用逗号隔开
     * @return array
     */
    public function baseFilmShowCheck($cinema_id,$date,$show_index){
        $params['Target'] = 'Base_FilmShowCheck';
        $params['CinemaID'] = $cinema_id;
        $params['Date'] = $date;
        $params['ShowIndex'] = $show_index;
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }

    public function baseWLanCheck($sTime,$des){
        $params['Target'] = 'Base_WLanCheck';
        $params['STime'] = $sTime;
        $params['Des'] = $des;
        $res_params = $this -> makeParams($params);
        $result = $this -> sendRequest($res_params);
        return $result;
    }
}