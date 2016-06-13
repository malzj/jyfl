<?php
/**
 * Created by PhpStorm.
 * User: malmemeda
 * Date: 2016/4/13
 * Time: 17:33
 */

namespace Users\Controller;


use Think\Controller;
use Home\HttpClient;
use Home\Huayingcard;
use Home\smsvrerifyApi;
class UserController extends Controller
{
  public  function __construct(){
      header("Access-Control-Allow-Origin: *");
      header("Access-Control-Allow-Headers: X-Requested-With");
      header('content-type:application/json;charset=utf8');
  }

  public function userShow(){
    $id=$_POST['user_id'];
    $Dao = M('users');
    $business = $Dao ->where('user_id = %d',$id)->find();
      $data=array();
      if($business){
          $data['result']="true";
          $data['business']=$business;
         $data['msg']="成功";
      }else{
          $data['result']="false";
          $data['business']=$business;
          $data['msg']="失败";
      };
    $jsondData = json_encode($data);
    echo $jsondData;
  }
    public  function userUpdate(){
        $data=array();
        $rudata=array();
        $id=$_POST['user_id'];
        $data['nickname']=$_POST['nickname'];
        $data['sex']=$_POST['sex'];
        $data['birthday']=$_POST['birthday'];
        $data['basic']=$_POST['basic'];
        $data['xingqu']=$_POST['xingqu'];
        $data['pic']=$_POST['pic'];
        $Dao = M('users');
        $result =$Dao->where('user_id='.$id)->save($data);
        if($result !== false){
            $rudata['result']="true";
            $rudata['user']=$data;
        }else{
            $rudata['result']="false";
        }
        $jsondData = json_encode($rudata);
        echo $jsondData;
    }
    public function userPassWordstatus(){
        $id=$_POST['user_id'];
        $password=$_POST['password'];
        $Dao = M('users');
        $business = $Dao ->where('user_id = %d',$id)->find();
        $data=array();
        if($business['password']==$password){
            $data['result']="true";
        }else{
            $data['result']="false";
        };
        $jsondData = json_encode($data);
        echo $jsondData;
    }
    public  function  userPassWord(){
        $data=array();
        $rudata=array();
        $id=$_POST['user_id'];
        $data['password']=$_POST['password'];
        $Dao = M('users');
        $result =$Dao->where('user_id='.$id)->save($data);
        if($result){
            $rudata['result']="true";
        }else{
            $rudata['result']="false";
        }
        $jsondData = json_encode($rudata);
        echo $jsondData;
    }

    /**
     * 安全设置的状态
     */
    public function showSafe(){
      $id = $_POST['user_id'];
      $Dao = M("users");
      $result = $Dao->where(array("user_id"=>$id))->find();
      $rudata = array();
      if($result['pass_edit'] == 0){
        $rudata['password']['result'] = false;
        $rudata['password']['msg'] = "未修改";
      }else{
        $rudata['password']['result'] = true;
        $rudata['password']['msg'] = "已修改";
      }

      if(empty($result['mobile_phone'])){
        $rudata['phone']['result'] = false;
        $rudata['phone']['msg'] = "未绑定";
      }else{
        $rudata['phone']['result'] = true;
        $rudata['phone']['msg'] = "已绑定";
      }

      if(!empty($result['answerone']) && !empty($result['answertwo']) && !empty($result['answerthree'])){
        $rudata['answer']['result'] = true;
        $rudata['answer']['msg'] = "已设置";
      }else{
        $rudata['answer']['result'] = false;
        $rudata['answer']['msg'] = "未设置";
      }
      $rudata['result']="true";
      $rudata['msg'] = "成功";
      $jsondData = json_encode($rudata);
      echo $jsondData;
    }


    /**
     * 修改登录密码
     */

    public function userLoginPass(){
      $cardPay = new Huayingcard();
      $id = $_POST['user_id'];
      $old_password = $_POST['old_password'];
      $password = $_POST['new_password'];
      $con_password = $_POST['con_password'];

      $rudata = array();
      $rudata['new_password'] = $password;
      $rudata['con_password'] = $con_password;

      $Dao = M("users");
      $result = $Dao->where(array("user_id"=>$id))->find();

      if($password == $con_password){
        if(strlen($password) < 6){
          $rudata['result'] = "false";
          $rudata['msg'] = "密码不能小于6位！";
        } else {
          $arr_param = array( 'CardInfo' =>array('CardNo'=>$result['user_name'], "CardPwd"=>$old_password,'CardNewPwd'=>$password));
          $state = $cardPay->action($arr_param, 2);
          if($state == 0){
			$Dao->where(array("user_id"=>$id))->data(array("pass_edit"=>1))->save();
            $rudata['result'] = "true";
            $rudata['msg'] = "密码修改成功！";
          }elseif($state == 1){
            $rudata['result'] = "false";
            $rudata['msg'] = $cardPay->getMessage();
          }
        }
      }else{
        $rudata['result'] = "false";
        $rudata['msg'] = "新密码与确认密码不匹配！";
      }

      $jsondData = json_encode($rudata);
      echo $jsondData;
    }


    /**
     * 获取验证码 js
     */

    public function smsvrerifyJs(){
      $id = $_POST['user_id'];
      $telphone = $_REQUEST['tel'];
      $verify = mt_rand(123456,999999);
      $smsvrerifyapi = new smsvrerifyApi();
      $data = $smsvrerifyapi->smsvrerify($telphone,$verify,1,'聚优福利');
      if($data == 0){
        $rudata['data'] = $data;
        $data = array();
        $data['verify'] = $verify;
        $data['verifytime'] = time();
        $Dao = M("users");
        if($Dao->create($data)){
          $result = $Dao->where("user_id=".$id)->save();
          if($result){
            $rudata['result'] = "true";
            $rudata['msg'] = "成功";
          }else{
            $rudata['result'] = "false";
            $rudata['msg'] = "失败";
          }
        }else{
          $rudata['result'] = "false";
          $rudata['msg'] = "失败";
        }
      }
      $jsondData = json_encode($rudata);
      echo $jsondData;
    }

    /**
     * 修改手机号
     */
    public function editphone(){

      $id = $_POST['user_id'];
      $telphone = $_REQUEST['tel'];
      $captcha = $_REQUEST['captcha'];
      $Dao = M("users");
      $result = $Dao->where("user_id=".$id)->find();
      if($captcha == $result['verify'] && time()-$result['verifytime'] < 1800){
        $data['mobile_phone'] = $telphone;
        if($Dao->create($data)){
          $Dao->where("user_id=".$id)->save();
          $rudata['result'] = "true";
          $rudata['msg'] = "绑定手机成功！";
        }else{
          $rudata['result'] = "false";
          $rudata['msg'] = "绑定手机失败！";
        }
      }else{
        $rudata['result'] = "false";
        $rudata['msg'] = "验证码错误！";
      }
      $jsondData = json_encode($rudata);
      echo $jsondData;
    }

    /**
     * 查看安全问题
     */
    public function saveLoginQues(){
      $user_id = $_POST['user_id'];
      $Dao = M("users");
      $result = $Dao->where("user_id=".$user_id)->find();
      $rudata = array();
      $rudata['answerone'] = $result['answerone'];
      $rudata['answertwo'] = $result['answertwo'];
      $rudata['answerthree'] = $result['answerthree'];
      $rudata['result']= 'true';
      $rudata['msg'] = "成功";
      $jsondData = json_encode($rudata);
      echo $jsondData;
    }

    /**
     * 修改密码安全问题
     */
    public function editLoginQues(){
      $id = $_REQUEST['user_id'];
      $one_answer = $_REQUEST['answerone'];
      $two_answer = $_REQUEST['answertwo'];
      $three_answer = $_REQUEST['answerthree'];
      $Dao = M("users");
      $data['answerone'] = $one_answer;
      $data['answertwo'] = $two_answer;
      $data['answerthree'] = $three_answer;

      if($Dao->create($data)){
        if($Dao->where("user_id=".$id)->save() !== false){
          $rudata['result'] = "true";
          $rudata['msg'] = "修改安全问题成功！";
        }else{
          $rudata['result'] = "false";
          $rudata['msg'] = "修改安全问题失败！";
        }
      }else{
        $rudata['result'] = "false";
        $rudata['msg'] = "修改安全问题失败！";
      }

      $jsondData = json_encode($rudata);
      echo $jsondData;
    }

    /**
     * 查看城市列表
     */
    public function showCity(){
      $Dao = M("region");
      $result = $Dao->field("region_id,region_name")->where("parent_id=0")->select();
      $rudata['result'] = "true";
      $rudata['business'] = $result;
      $rudata['msg'] = "成功";
      $jsondData = json_encode($rudata);
      echo $jsondData;
    }

    /**
     * 查看乡镇列表
     */
    public function showProvince(){
      $parent_id = $_POST['parent_id'];
      $Dao = M("region");
      $result = $Dao->field("region_id,region_name")->where("parent_id=".$parent_id)->select();
      $rudata['result'] = "true";
      $rudata['business'] = $result;
      $rudata['msg'] = "成功";
      $jsondData = json_encode($rudata);
      echo $jsondData;
    }

    /*
     * 获取编辑内容
     * function getEditAddress
     * @param user_id
     * @param address_id
     */
    public function getEditAddress(){
      $id = $_REQUEST['user_id'];
      $address_id = $_REQUEST['address_id'];
      $Dao =M("user_address");
      $Regoin = M("region");
      $addressInfo = $Dao->where(array('userid_id'=>$id,'address_id'=>$address_id))->find();
      $countryList = $Regoin -> field("region_id,region_name") -> where(array('parent_id'=>0)) -> select(); //获取省或直辖市列表
      if(!empty($addressInfo)){
        $provinceList = $Regoin -> field("region_id,region_name") -> where(array('parent_id'=>$addressInfo['country'])) -> select(); //获取地址同属市列表
        $cityList = $Regoin -> field("region_id,region_name") -> where(array('parent_id'=>$addressInfo['province'])) -> select(); //获取地址同属区列表
      }
      if(!empty($countryList)&&!empty($provinceList)&&!empty($cityList)){
        $rudata['result']="true";
        $rudata['countryList']=$countryList;
        $rudata['provinceList']=$provinceList;
        $rudata['cityList']=$cityList;
        $rudata['addressInfo']=$addressInfo;
        $rudata['msg'] = "成功！";
      }else{
        $rudata['result']="false";
        $rudata['msg'] = "失败！";
      }

      $jsondData = json_encode($rudata);
      echo $jsondData;
    }

    /**
     * 查看收货地址
     */
    public function showAddress(){
      $id = $_REQUEST['user_id'];
      $Dao = M("user_address");
      $page = $_REQUEST['p']?$_REQUEST['p']:1;
      
      $result = $Dao->where("user_id=".$id)->select();
      $regoin = M("region");
      //处理收货的三级地址
      foreach($result as $key=>$value){
        $city = $result[$key]['city'];
        $province = $result[$key]['province'];
        $country = $result[$key]['country'];
        $region_arr = $regoin->where("region_id=".$city)->find();
        $result[$key]["city"] = empty($region_arr['region_name'])?$region_arr['region_name']:'';
        $region_arr = $regoin->where("region_id=".$province)->find();
        $result[$key]["province"] = empty($region_arr['region_name'])?$region_arr['region_name']:'';
        $region_arr = $regoin->where("region_id=".$country)->find();
        $result[$key]["country"] = empty($region_arr['region_name'])?$region_arr['region_name']:'';
      }

      if($result !== ""){
        $rudata['result'] = "true";
        $rudata['business']['result'] = $result;
        $rudata['business']['show'] = $show;
        $rudata['msg'] = "成功";
      }else{
        $rudata['result'] = "false";
        $rudata['business'] = "";
        $rudata['msg'] = "失败";
      }

      $jsondData = json_encode($rudata);
      echo $jsondData;
    }

    /**
     * 添加收货地址
     */
    public function addAddress(){
      $data = array();
      $id = $_REQUEST['user_id'];
      $hcountryid = $_REQUEST['hcountryid'];    //省
      $hprovinceid = $_REQUEST['hprovinceid'];      //市
      $hcityid = $_REQUEST['hcityid'];          //区
      $hstreet = $_REQUEST['hstreet'];      //街道
      $zipcode = $_REQUEST['zipcode'];      //邮编
      $consignee = $_REQUEST['consignee'];  //联系人
      $tel = $_REQUEST['mobile'];              // 联系方式
     
      // 测试使用
      // $hareaid = 108;
      // $hproperid = 1;
      // $hcityid = 1;

      $data['country'] = $hcountryid;
      $data['province'] = $hprovinceid;
      $data['city'] = $hcityid;
      $data['address'] = $hstreet;
      $data['zipcode'] = $zipcode;
      $data['consignee'] = $consignee;
      $data['mobile'] = $tel;
      $data['user_id'] = $id;

      $Dao = M("user_address");
      if($Dao->create($data)){
        $result = $Dao->add();
        if($result){
          $rudata['result'] = "true";
          $rudata['msg'] = "成功";
        }else{
          $rudata['result'] = "false";
          $rudata['msg'] = "失败";
        }
      }else{
        $rudata['result'] = "false";
        $rudata['msg'] = "失败";
      }
     

      $jsondData = json_encode($rudata);
      echo $jsondData;
    }

    /**
     * 修改地址的界面
     */
    public function saveAddress(){
      $address_id = $_REQUEST['address_id'];
      $user_id = $_REQUEST['user_id'];
      $Dao = M("user_address");
      $result = $Dao->where(array("user_id"=>$user_id,"address_id="=>$address_id))->find();
      $regoin = M("region");
      $city = $result['city'];
      $province = $result['province'];
      $country = $result['country'];
      $region_arr = $regoin->where("region_id=".$city)->find();
      $result["cityname"] = $region_arr['region_name'];
      $region_arr = $regoin->where("region_id=".$province)->find();
      $result["provincename"] = $region_arr['region_name'];
      $region_arr = $regoin->where("region_id=".$country)->find();
      $result["countryname"] = $region_arr['region_name'];
      if($result){
        $rudata['result'] = "true";
        $rudata['business'] = $result;
        $rudata['msg'] = "成功";
      }else{
        $rudata['result'] = 'false';
        $rudata['business'] = "";
        $rudata['msg'] = '失败';
      }
      $jsondData = json_encode($rudata);
      echo $jsondData;
    }

    /**
     * 修改收货地址
     */
    public function editAddress(){
      $address_id = $_REQUEST['address_id'];
      $user_id = $_REQUEST['user_id'];
      $Dao = M("user_address");
      $arr_address = $Dao->where("address_id =".$address_id)->find();
      

      $hareaid = $_REQUEST['hareaid'];          //县区
      $hproperid = $_REQUEST['hproperid'];      //市
      $hcityid = $_REQUEST['hcityid'];          //省
      $hstreet = $_REQUEST["hstreet"];      //街道
      $zipcode = $_REQUEST['zipcode'];      //邮编
      $consignee = $_REQUEST['consignee'];  //联系人
      $tel = $_REQUEST['tel'];              //联系方式

      // 测试使用
      // $hareaid = 108;
      // $hproperid = 1;
      // $hcityid = 1;

      $data['city'] = $hareaid;
      $data['province'] = $hproperid;
      $data['country'] = $hcityid;
      $data['address'] = $hstreet;
      $data['zipcode'] = $zipcode;
      $data['consignee'] = $consignee;
      $data['mobile'] = $tel;
      if($user_id != $arr_address['user_id']){
        $rudata['result'] = "false";
        $rudata['msg'] = "失败";
      }else{
        if($Dao->create($data)){
          $result = $Dao->where("address_id=".$address_id)->save();
          $rudata['result'] = "true";
          $rudata['msg'] = "成功";
        }else{
          $rudata['result'] = "false";
          $rudata['msg'] = "失败";
        }
      }
      $jsondData = json_encode($rudata);
      echo $jsondData;
    }

  /*
   * 更新收货地址
   * function updateAddress
   *
   * version 1.0
   * author chao
   * time 2016/5/13
   */
    public function updateAddress(){
      $data = array();
      $id = $_REQUEST['user_id'];
      $address_id = $_REQUEST['address_id'];
      $hcountryid = $_REQUEST['hcountryid'];    //省
      $hprovinceid = $_REQUEST['hprovinceid'];      //市
      $hcityid = $_REQUEST['hcityid'];          //区
      $hstreet = $_REQUEST['hstreet'];      //街道
      $zipcode = $_REQUEST['zipcode'];      //邮编
      $consignee = $_REQUEST['consignee'];  //联系人
      $tel = $_REQUEST['mobile'];              // 联系方式

      $data['country'] = $hcountryid;
      $data['province'] = $hprovinceid;
      $data['city'] = $hcityid;
      $data['address'] = $hstreet;
      $data['zipcode'] = $zipcode;
      $data['consignee'] = $consignee;
      $data['mobile'] = $tel;

      $Dao = M('user_address');
      $result = $Dao -> where(array('user_id'=>$id,'address_id'=>$address_id)) -> save($data);
      if($result !== false){
        $rudata['result'] = "true";
        $rudata['msg'] = "成功！";
      }else{
        $rudata['result'] = "false";
        $rudata['msg'] = "失败！";
      }

      $jsondData = json_encode($rudata);
      echo $jsondData;
    }
    /**
     * 删除收货地址
     */
    public function delAddress(){
      $address_id = $_POST['address_id'];
      $user_id = $_POST['user_id'];
      $Dao = M("user_address");
      $result = $Dao->where("address_id=".$address_id)->find();
      if($result['user_id'] == $user_id){
        if($Dao->where("address_id=".$address_id)->delete()){
          $rudata['result'] = "true";
          $rudata['msg'] = "成功";
        }else{
          $rudata['result'] = "false";
          $rudata['msg'] = "失败";
        }
      }else{
        $rudata['result'] = "false";
        $rudata['msg'] = "失败";
      }
      $jsondData = json_encode($rudata);
      echo $jsondData;  
    }

    /**
     * 查看红包列表
     */
    public function showPack(){
      $user_id = $_REQUEST['user_id'];
      $bonus = M("user_bonus");
      $page = isset($_REQUEST['p'])?$_REQUEST['p']:1;
      $row = $bonus->join("ecs_bonus_type on ecs_user_bonus.bonus_type_id = ecs_bonus_type.type_id ")->where("user_id = ".$user_id)->select();
      
      $day = getdate();
      $cur_date = mktime(23, 59, 59, $day['mon'], $day['mday'], $day['year']);
      if($row){
        $rudata['result'] = "true";
        $rudata['msg'] = "成功";

        foreach($row as $key=>$value){
          $date = $row[$key]['endday']*24*3600;
          $row[$key]['end_time'] = date("Y-m-d", $row[$key]['add_time']+$date);
          $row[$key]['add_time'] = date("Y-m-d H:i:s",$row[$key]['add_time']);
          $row[$key]['send_start_date'] = date("Y-m-d H:i:s",$row[$key]['send_start_date']);
          $row[$key]['send_end_date'] = date("Y-m-d H:i:s",$row[$key]['send_end_date']);
          $row[$key]['use_start_date'] = date("Y-m-d H:i:s",$row[$key]['use_start_date']);
          $row[$key]['use_end_date'] = date("Y-m-d H:i:s",$row[$key]['use_end_date']);

          if(empty($value['order_id'])){
            //还没使用
            if($value['use_start_date'] > $cur_date){
              $row[$key]['status'] = "还没开始";
            }elseif($value['use_end_date'] < $cur_date){
              $row[$key]['status'] = "已经结束";
            }else{
              $row[$key]['status'] = "还未使用";
            }
          
          }else{
            $order_info = M("order_info");
            $arr_order_info = $order_info->where("order_id=".$value['order_id'])->find();
            if($arr_order_info['pay_status'] == 2){
              $row[$key]['status'] = "订单完成";
            }else{
              $row[$key]['status'] = "订单中";
            }
          }
          
        }
        $rudata["business"] = $row;
      }else{
        $rudata['result'] = "false";
        $rudata['msg'] = "失败";
        $rudata['business'] = "";
      }

      $jsondData = json_encode($rudata);
      echo $jsondData;

    }

    /**
     * 添加红包(暂时可不做)
     */
    public function addPack(){
    }

}

