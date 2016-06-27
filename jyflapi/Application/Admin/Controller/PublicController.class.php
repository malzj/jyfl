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
            $this->redirect('Games/gameList');
        }else{
            $this->display();
        }
    }

    public function islogin()
    {
        $model = D('Admin');
        $model->login();
        if (session(C('ADMIN_UID'))) {
            $this->success('登录成功', U('Games/gameList'));
        } else {
            $this->error('登录失败');
        }
    }

    /**
     * logout 退出登录
     * @author chao
     **/
    public function logout()
    {
        session(C('ADMIN_UID'), null);
        $this->redirect('Public/login');
    }

    public function _rule(){
        $res = session(C('ADMIN_UID'))?true:false;
        return $res;
    }
}