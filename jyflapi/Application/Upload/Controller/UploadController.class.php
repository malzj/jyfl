<?php
namespace Upload\Controller;
/**
 * Created by PhpStorm.
 * User: malmemeda
 * Date: 2016/4/19
 * Time: 16:14
 */
use Think\Controller;
class UploadController extends Controller
{
    public  function __construct(){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-Requested-With");
        header('content-type:application/json;charset=utf8');
    }
    public  function upload(){
        header('content-type:application/json;charset=utf8');
        $upload = new \Think\Upload();// ʵ�����ϴ���
        $upload->maxSize = 31457282222 ;// ���ø����ϴ���С
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg','pdf','mp4');// ���ø����ϴ�����
        $upload->rootPath = './Uploads/'; // ���ø����ϴ���Ŀ¼
        $upload->savePath = ''; // ���ø����ϴ����ӣ�Ŀ¼
        $data=array();
        $info = $upload->upload();
        if(!$info) {// �ϴ�������ʾ������Ϣ
           $data['msg']="图片上传失败";
            $data['result']="false";
        }else{
            $data['result']="true";
            $sn= '/jyflapi/Uploads/'.$info['file']['savepath']. $info['file']['savename'];
            $ccc='http://'.$_SERVER['HTTP_HOST'].$sn;
            $data['msg']="图片上传成功";
            $data['img']=$ccc;
        }

        $jsondData = json_encode($data);
        echo $jsondData;
    }

}