<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/8/31
 * Time: 9:11
 */
require(dirname(__FILE__).'/httpRequest.php');

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
     * 生成签名后参数
     * @param $target   操作命令
     * @return string
     */
    protected function makeParams($params){
        ksort($params,SORT_STRING);
        $str_params = implode($params);
        $str_params = $this -> username.$str_params.$this->key;
        $sign = md5($str_params);
        $params['UserName'] = $this -> username;
        $params['Sign'] = $sign;
        return $params;
    }

    /**
     * 影院、非合作影院信息查询
     * @param  $target  操作命令：影院（Base_Cinema）【默认】，非合作影院（Base_CinemaUnCooperation）
     * @return array
     */
    public function getCinemas($target='Base_Cinema'){
        $params = array(
            'Target' => $target
        );
        $params = $this -> makeParams($params);
        $str_param = $this -> httpRequest -> buildUrlQuery($params);
        echo $str_param;
        $result = $this -> httpRequest -> post($this->url,$str_param,'json','curl');
        return $result;
    }

    /**
     * 根据城市，（影片），日期查询有放映计划的影院
     * @param $city_id  城市标志，数值型
     * @param $date     日期，格式yyyy-MM-dd HH:mm:ss
     * @param $film_id  影片标识
     * @return array
     */
    public function baseCinemaQuery($city_id,$date,$film_id){
        $global_params = $this -> makeParams('Base_CinemaQuery');
        $params['CityID'] = $city_id;
        $params['Date'] = $date;
        $params['FilmID'] = $film_id;
        $params = array_merge($global_params,$params);
        $str_param = $this -> httpRequest -> buildUrlQuery($params);
        $result = $this -> httpRequest -> post($this->url,$str_param,'json','curl');
        return $result;
    }

    /**
     * 所有城市查询
     * @return array
     */
    public function baseCity(){
        $global_params = $this -> makeParams('Base_City');
        $str_param = $this -> httpRequest -> buildUrlQuery($global_params);
        $result = $this -> httpRequest -> post($this->url,$str_param,'json','curl');
        return $result;
    }

    /**
     * 已有业务城市查询
     * @return array
     */
    public function baseCityBll(){
        $global_params = $this -> makeParams('Base_CityBll');
        $str_param = $this -> httpRequest -> buildUrlQuery($global_params);
        $result = $this -> httpRequest -> post($this->url,$str_param,'json','curl');
        return $result;
    }

    /**
     * 地区查询
     * @return array
     */
    public function baseDistrict(){
        $global_params = $this -> makeParams('Base_District');
        $str_param = $this -> httpRequest -> buildUrlQuery($global_params);
        $result = $this -> httpRequest -> post($this->url,$str_param,'json','curl');
        return $result;
    }

    /**
     * 商圈查询
     * @return array
     */
    public function baseTradingArea(){
        $global_params = $this -> makeParams('Base_TradingArea');
        $str_param = $this -> httpRequest -> buildUrlQuery($global_params);
        $result = $this -> httpRequest -> post($this->url,$str_param,'json','curl');
        return $result;
    }

    /**
     * 地铁查询
     * @return array
     */
    public function baseSubWay(){
        $global_params = $this -> makeParams('Base_SubWay');
        $str_param = $this -> httpRequest -> buildUrlQuery($global_params);
        $result = $this -> httpRequest -> post($this->url,$str_param,'json','curl');
        return $result;
    }

    /**
     * 院线查询
     * @return array
     */
    public function baseCinemaLine(){
        $global_params = $this -> makeParams('Base_CinemaLine');
        $str_param = $this -> httpRequest -> buildUrlQuery($global_params);
        $result = $this -> httpRequest -> post($this->url,$str_param,'json','curl');
        return $result;
    }
    /**
     * 影厅信息查询
     * @return array
     */
    public function baseHall($cinema_id){
        $global_params = $this -> makeParams('Base_Hall');
        $params['CinemaId'] = $cinema_id;
        $params = array_merge($global_params,$params);
        $str_param = $this -> httpRequest -> buildUrlQuery($params);
        $result = $this -> httpRequest -> post($this->url,$str_param,'json','curl');
        return $result;
    }
    /**
     * 座位信息查询
     * @param $hall_id      影厅ID
     * @param $cinema_id    影院ID
     * @return array
     */
    public function baseHallSeat($hall_id,$cinema_id){
        $global_params = $this -> makeParams('Base_HallSeat');
        $params['CinemaId'] = $cinema_id;
        $params['HallID']   = $hall_id;
        $params = array_merge($global_params,$params);
        $str_param = $this -> httpRequest -> buildUrlQuery($params);
        $result = $this -> httpRequest -> post($this->url,$str_param,'json','curl');
        return $result;
    }

    /**
     * 已售出座位信息查询
     * @param $show_index   场次号
     * @param $cinema_id    影院标识
     * @return array
     */
    public function baseSellSeat($show_index,$cinema_id){
        $global_params = $this -> makeParams('Base_SellSeat');
        $params['HallID']   = $show_index;
        $params['CinemaId'] = $cinema_id;
        $params = array_merge($global_params,$params);
        $str_param = $this -> httpRequest -> buildUrlQuery($params);
        $result = $this -> httpRequest -> post($this->url,$str_param,'json','curl');
        return $result;
    }

    /**
     * 正在上映的影片信息查询
     * @param $city_id      城市标识
     * @param $date         日期，如（2012-06-06）,默认为当天
     * @param $cinema_id    影院标识
     * @return string
     */
    public function baseFilm($city_id,$date,$cinema_id){
        $global_params = $this -> makeParams('Base_Film');
        $params['CityID']   = $city_id;
        $params['Date']     = $date;
        $params['CinemaId'] = $cinema_id;
        $params = array_merge($global_params,$params);
        $str_param = $this -> httpRequest -> buildUrlQuery($params);
        $result = $this -> httpRequest -> post($this->url,$str_param,'json','curl');
        return $result;
    }

    /**
     * 即将上映的影片信息查询
     * @param $city_id      城市标识
     * @param $date         日期，如（2012-06-06）,默认为当天
     * @param $cinema_id    影院标识
     * @return string
     */

    public function baseFilmIM($date,$cinema_id){
        $global_params = $this -> makeParams('Base_FilmIM');
        $params['CityID']   = $city_id;
        $params['Date']     = $date;
        $params['CinemaId'] = $cinema_id;
        $params = array_merge($global_params,$params);
        $str_param = $this -> httpRequest -> buildUrlQuery($params);
        $result = $this -> httpRequest -> post($this->url,$str_param,'json','curl');
        return $result;
    }

    /**
     * 执行请求接口命令
     * @param $target   操作命令
     * @param $params   参数
     * @return array
     */
    public function doTarget($target,$params=array()){
        $global_params = $this -> makeParams($target);
        $params = array_merge($global_params,$params);
        $str_params = $this -> httpRequest -> buildUrlQuery($params);
        $result = $this -> httpRequest -> post($this->url,$str_params,'json','curl');
        return $result;
    }
}