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
    //用户名
    private $username;
    //秘钥
    private $key;
    //实例化httpRequest类
    private $httpRequest;
    //

    public function __construct(){
        $this -> username = 'testName';
        $this -> key = 'testKey';
        $this -> httpRequest = new HttpRequest();
    }

    /**
     * 生成全局参数
     * @param $target   操作命令
     * @return string
     */
    protected function makeGlobalParams($target){
        $sign = md5($this->username.$target.$this->key);
        $params = array(
            'UserName'  =>  $this -> username,
            'Target'    =>  $target,
            'Sign'      =>  $sign,
        );
        return $params;
    }

    public function allCinema(){
        $global_params = $this -> makeGlobalParams('Base_Cinema');
        $str_param = $this -> httpRequest -> buildUrlQuery($global_params);
        $this -> httpRequest -> post();
    }
    
}