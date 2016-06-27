<?php
namespace Admin\Model;
use Think\Model;
class AdminModel extends Model
{
    private $_adminuser = 'admin';
    private $_password = 'huaying507';

    /**
     * $_validate 自动验证
     * @author 刘中胜
     * @time 2015-04-14
     **/
    protected $_validate = array(
        array('username', 'require', '帐号必须填写'),
        array('password', 'require', '密码必须填写'),
        array('name', 'require', '姓名必须填写'),
        array('email', 'require', '邮件必须填写'),
        array('email', 'email', '邮件格式错误'),
        array('phone', 'require', '电话必须填写'),
        array('sort', 'require', '排序方式必须填写'),
        array('sort', 'number', '排序只能是数字'),
        array('verify', 'checkcode', '验证码不正确', 0, 'function'),
    );

    /**
     * alogin　登录操作
     * @reutrn string
     * @author chao
     **/
    public function login()
    {
        $data=I('post.');
        if($data['username']==$this->_adminuser&&$data['password']==$this->_password){
            session(C('ADMIN_UID'), 1);
            session(C('USERNAME'), $this->_adminuser);
        }else{
            $this->error = '登陆出错,用户名或者密码错误';
            return false;
        }
    }

}