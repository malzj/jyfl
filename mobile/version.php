<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/9/1
 * Time: 10:16
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

//$jsonArray = array(
//    'appid'     =>  'JUYOU0711',
//    'iOS'       =>  array(
//        'version'   =>  '1.0.2',
//        'title'     =>  '版本更新',
//        'note'      =>  '修复BUG',
//        'url'       =>  'http://www.juyoufuli.com',
//    ),
//    'Android'   =>  array(
//        'version'   =>  '1.0.2',
//        'title'     =>  '版本更新',
//        'note'      =>  '修复BUG',
//        'url'       =>  'http://www.juyoufuli.com',
//    ),
//);
$appinfo = array(
    'appid'=> 'H531E8E9F',
    'version' => '1.1',
    'fileurl'=>'http://www.juyoufuli.com/app/H531E8E9F.wgt' //这个是相对服务器端网址url 下的根目录，升级包文件
 );
 //以上是每次制作好升级包都需要修改一下 version 这个值。值要和你在hbuilder里的app版本号一致。

 if(isset($_POST['ver']) && ($_POST['ver'] < $appinfo['version'])){   
    $ret = array('code'=>1, 'url'=> $appinfo['fileurl']); 
 }else{
    $ret = array('code'=>0, 'url'=>''); 
 }


JsonpEncode($ret);