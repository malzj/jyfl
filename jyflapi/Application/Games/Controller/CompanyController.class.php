<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/5/18
 * Time: 15:22
 */

namespace Games\Controller;

use Think\Controller;
use Think\Page;
class CompanyController extends Controller
{
    public function index(){
        
    }

    /**
     * 公司列表
     * @author  zhaoyingchao
     * @date    2016-5-18
     * @time    14:00
     * @since   v1.0
     */
    public function companyList(){
        $CompanyModel = M('Company');
        $GradeModel = M('Grade');
        $count = $CompanyModel -> count();
        $Page = new Page($count,10);
        $pages = $Page -> show();
        $data = $CompanyModel -> order('id desc') -> limit($Page->firstRow.','.$Page -> listRows) -> select();
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
}