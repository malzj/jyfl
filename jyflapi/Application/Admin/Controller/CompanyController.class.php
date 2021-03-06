<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/5/18
 * Time: 15:22
 */

namespace Admin\Controller;

use Think\Controller;
use Think\Page;
use Think\Upload;
class CompanyController extends Controller
{
    public function test(){
        $Card = new \Ext\card\huayingcard();
        $arr_param = array(	'CardInfo'=>array('CardNo'  => '7110010995430713', 'CardPwd' => '123456') );
        $no = $Card -> action($arr_param,8);
        $result = $Card -> getResult();
        var_dump($result);
    }

    /**
     * 同步卡系统公司列表
     */
    public function companySync(){
        $CompanyModel = M('Company');
//        $lastTime = $CompanyModel->order('create_time DESC')->getField('create_time');
//        $dataInfo['Info']['Time'] = $lastTime;
//        if(!strtotime($lastTime)){
            $dataInfo['Info']['Time'] = '2012-1-1 00:00';
//        }
        $Card = new \Ext\card\huayingcard();
        $no = $Card -> action($dataInfo,11);
        $result = $Card -> getResult();
        $dataList = $Card -> getDataList();
        $time = strtotime($result['Time']);
        $time = date('Y-m-d H:i:s',$time);
        $company_logo = 'Public/company/upload/logo.png';
        $company_bg = 'Public/company/upload/pc_background.jpg';
		$mobile_bg = 'Public/company/upload/m_background.jpg';
        if($no == 0){
            //查询本地所有公司卡系统ID
            $companys = $CompanyModel -> select();
            $company_ids = array();
            foreach ($companys as $com){
                $company_ids[] = $com['card_company_id'];
            }

            $companyList = array();
            foreach($dataList['Info'] as $key=>$val){
                if(in_array($val['CustomerID'],$company_ids)) continue;
                $companyList[] = array('card_company_id'=>$val['CustomerID'],'company_name'=>$val['CompanyName'],'grade_id'=>2,'create_time'=>$time,'logo_img'=>$company_logo,'back_img'=>$company_bg,'m_back_img'=>$mobile_bg);
            }
        }
        if(!empty($companyList)){
            $CompanyModel -> addAll($companyList);
        }
        $this->redirect('Company/companyList');
    }

    /**
     * 公司列表
     * @author  zhaoyingchao
     * @date    2016-5-18
     * @time    14:00
     * @since   v1.0
     */
    public function companyList(){
        $companyName = I('request.searchkey');
        $CompanyModel = M('Company');
        $GradeModel = M('Grade');
        if(!empty($companyName))
        $sqldata['company_name']=$companyName;
        $count = $CompanyModel ->where($sqldata) -> count();
        $Page = new Page($count,10);
        $pages = $Page -> show();
        $data = $CompanyModel->where($sqldata) -> order('id desc') -> limit($Page->firstRow.','.$Page -> listRows) -> select();
        $companyList = array();
        foreach($data as $key => $value){
            $gradeName = $GradeModel -> where(array('id'=>$value['grade_id'])) -> getField('grade_name');
            $companyList[$key]['id'] = $value['id'];
            $companyList[$key]['company_name'] = $value['company_name'];
            $companyList[$key]['grade_name'] = $gradeName;
        }
        $this -> assign('company_list',$companyList);
        $this -> assign('pages',$pages);
        $this -> display();
    }

    /**
     * 公司编辑
     */
    public function companyEdit(){
        $CompanyModel = M('Company');


        if(IS_POST){
            $cid = I('request.id');
            $data = array();
            $data['company_name'] = I('post.company_name');
            $data['grade_id'] = I('post.grade_id');

            if($_FILES['logo_img']['name']||$_FILES['back_img']['name']||$_FILES['m_back_img']['name']){
                //图片上传设置

                $config = array(
                    'maxSize' => 5145728,
                    'savePath' => 'Public/company/upload/',
                    'rootPath' => './',
                    'exts' => array('jpg','gif','png','jpeg'),
                    'autoSub' => true,
                    'subName' => $cid
                );

                $Upload = new Upload($config);
                $images = $Upload -> upload($_FILES);
                //判断是否有图
                if($images){
                    foreach($images as $key=>$file) {
                        if ($file['savename']) {
                            $data[$key] = $file['savepath'].$file['savename'];
                        }
                    }
                }else{
                    $this -> error($Upload->getError());//获取失败信息
                }
            }
            $result = $CompanyModel -> where(array('id'=>$cid))->data($data)->save();
            if($result !== false){
                $this -> redirect('Company/companyList');
            }else{
                $this->error('编辑失败！');
            }
        }else{
            $cid = I('request.id');
            $GradeModel = M('Grade');
            $gradeList = $GradeModel -> field('id,grade_name') -> select();
            $companyInfo = $CompanyModel->where(array('id'=>$cid))->find();
            $this -> assign('company_info',$companyInfo);
            $this -> assign('grade_list',$gradeList);
            $this -> display();
        }
    }
}