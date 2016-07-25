<?php
// 本类由系统自动生成，仅供测试用途
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $url='http://a.piaowutong.cc:9001/encryptvoucher/encode';
        $httpRe=new \Ext\card\HttpRequest();
        $jsonstr=$httpRe->get($url,array('voucherno'=>7110011));
        $data=json_decode($jsonstr);
        echo '<pre>';
        print_r($data);
        echo '</pre>';
//        $this->display();
    }
}