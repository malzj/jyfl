<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/8/31
 * Time: 9:11
 */
class wpwMovie
{
    //用户名
    private $username = 'testName';
    //秘钥
    private $key = 'testKey';

    public function __construct(){

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
        var_dump($global_params);
    }
    
}