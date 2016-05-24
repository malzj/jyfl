<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/5/18
 * Time: 17:55
 */

namespace Games\Controller;

use Think\Controller;
use Think\Page;
class GradeController extends Controller
{
    public function gradeList(){
        $GradeModel = M('Grade');
        $count = $GradeModel->count();
        $Page = new Page($count,10);
        $pages = $Page -> show();
        $gradeList = $GradeModel -> limit($Page->firstRow.','.$Page->listRows) -> select();
        $this -> assign('pages',$pages);
        $this -> assign('grade_list',$gradeList);
        $this -> display();
    }

    /**
     * 新增等级
     * @author  zhaoyingchao
     * @date    2016-5-19
     * @time    9:40
     * @since   v1.0
     */
    public function gradeAdd(){
        $this -> display();
    }

    /**
     * 保存等级
     * @author  zhaoyingchao
     * @date    2016-5-19
     * @time    9:50
     * @since   v1.0
     */
    public function gradeSave(){
        if(IS_POST){
            $GradeModel = M('Grade');
            if($GradeModel -> field('grade_name,remark') -> Create()){
                $res = $GradeModel -> add();
                if($res){
                    $this -> redirect('Grade/gradeList');
                }else{
                    $this ->error('新增失败！');
                }
            }
        }else{
            $this -> error('请填写数据！');
        }
    }

    /**
     * 编辑等级
     * @author  zhaoyingchao
     * @data    2016-5-19
     * @time    10:00
     * @since   v1.0
     */
    public function gradeEdit(){
        $id = $_REQUEST['id'];
        $GradeModel = M('Grade');
        $gradeInfo = $GradeModel -> where(array('id'=>$id)) -> find();
        if(!empty($gradeInfo)){
            $this -> assign('grade_info',$gradeInfo);
            $this -> display();
        }else{
            $this -> error('未找到该等级');
        }
    }

    /**
     * 更新等级
     * @author  zhaoyingchao
     * @date    2016-5-19
     * @time    10:17
     * @since   v1.0
     */
    public function gradeUpdate(){
        if(IS_POST){
            $GradeModel = M('Grade');
            if($GradeModel -> field('id,grade_name,remark') -> create()){
                $result = $GradeModel -> save();
                if($result !== false){
                    $this -> redirect('Grade/gradeList');
                }else{
                    $this -> error('更新失败！');
                }
            }
        }else{
            $this -> error('请填写数据！');
        }
    }

    /**
     * 删除等级
     * @author  zhaoyingchao
     * @date    2016-5-19
     * @time    10:38
     * @since   v1.0
     */
    public function gradeDelete(){
        $id = $_REQUEST['id'];
        $GradeModel = M('Grade');
        $result = $GradeModel -> where(array('id'=>$id)) -> delete();
        if($result){
            $this -> success('删除成功！');
        }else{
            $this -> error('删除失败！');
        }
    }
}