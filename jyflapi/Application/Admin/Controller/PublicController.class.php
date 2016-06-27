<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/6/27
 * Time: 11:49
 */
namespace Admin\Controller;
use Think\Controller;
class PublicController extends Controller
{
    public function login(){
        if(session(C('ADMIN_UID'))){
            $this->redirect(U('Index/index'));
        }else{
            $this->display();
        }
    }
}