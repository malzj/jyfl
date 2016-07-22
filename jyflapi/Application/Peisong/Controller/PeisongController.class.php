<?php
namespace Peisong\Controller;
/**
 * Created by PhpStorm.
 * User: malmemeda
 * Date: 2016/4/29
 * Time: 9:21
 */
use Think\Controller;

class PeisongController extends Controller
{
    public  function __construct(){
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-Requested-With");
        header('content-type:application/json;charset=utf8');
    }
    public function savaPeiSongMap(){
        $data = array();
        $result= array();
        $Dao = M('peisongmap');
        $data["gongyingshang_id"] = $_POST["gongyingshang_id"] ;
        $data["jiage"] =  $_POST["jiage"];
        $data["yanse"] = $_POST["yanse"];
        $data["dianshu"] = $_POST["dianshu"];
        $data["cityid"] = $_POST["cityid"];
        $data["isTime"] = $_POST["isTime"];
        $data["shipping_start"] = $_POST["shipping_start"];
        $data["shipping_end"] = $_POST["shipping_end"];
        $data["shipping_waiting"] = $_POST["shipping_waiting"];
        $data["shipping_booking"] = $_POST["shipping_booking"];

        $data["addDate"] =date( 'Y-m-d',time());
        $id=$Dao->add($data);
        if($id){
            $result['result']="true";
            $result['id']=$id;
        }else{
            $result['result']="false";
        }
        $jsondData = json_encode($result);
        echo $jsondData;
    }

    public  function  savePeiSongMapZuoBiao(){
        $data = array();
        $result= array();
        $Dao = M('peisongmapzuobiao');
        $data["lng"] = $_POST["lng"] ;
        $data["lat"] =  $_POST["lat"];
        $data["peisongmap_id"] = $_POST["peisongmap_id"];
        $id=$Dao->add($data);
        if($id){
            $result['result']="true";
            $result['id']=$id;
        }else{
            $result['result']="false";
        }
        $jsondData = json_encode($result);
        echo $jsondData;
    }
    public  function showmap(){
        $id = $_POST['gongyingshang_id'];
        $istime = isset($_POST['isTime'])? $_POST['isTime'] : 1 ;
        $result=array();
        $data=array();
        $data['gongyingshang_id']=$id;
        $data['isTime'] = $istime;
        $Dao = M('peisongmap');
        $Dao1 = M('peisongmapzuobiao');
        $list=$Dao->where($data)->select();
        $size =$Dao->where($data)->count();
        $number = 0;
        for($number;$number<$size;$number++){
            $mapid=$list[$number]['id'];
            $map=array();
            $map['peisongmap_id']=$mapid;
            $list[$number]['zuobiao']=$Dao1->where($map)->select();
        }

        $result['list']=$list;
        $result['result']=true;
        $jsondData = json_encode($result);
        echo $jsondData;
    }
    
    public  function showmap_wap(){
        $id = $_REQUEST['gongyingshang_id'];
        $istime = isset($_REQUEST['isTime'])? $_REQUEST['isTime'] : 1 ;
        $result=array();
        $data=array();
        $data['gongyingshang_id']=$id;
        $data['isTime'] = $istime;
        $Dao = M('peisongmap');
        $Dao1 = M('peisongmapzuobiao');
        $list=$Dao->where($data)->select();
        $size =$Dao->where($data)->count();
        $number = 0;
        for($number;$number<$size;$number++){
            $mapid=$list[$number]['id'];
            $map=array();
            $map['peisongmap_id']=$mapid;
            $list[$number]['zuobiao']=$Dao1->where($map)->select();
        }
    
        $result['list']=$list;
        $result['result']=true;
        exit($_GET['jsoncallback']."(".json_encode($result).")");
        
    }

}