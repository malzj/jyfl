<?php

/**
 * 用户身份验证
 * @var unknown
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

// 导航信息
if ($_REQUEST['act'] == 'navList')
{
    $navList = get_navigator();
    $jsonArray['data'] = $navList['middle'];
    JsonpEncode($jsonArray); 
}

// 城市切换
elseif ($_REQUEST['act'] == 'city')
{
    JsonpEncode($jsonArray);    
}

// 当前登录的城市id
elseif ($_REQUEST['act'] == 'getCityId')
{
    $jsonArray['data']['cityid'] = $_SESSION['cityid']; 
    JsonpEncode($jsonArray); 
}
// 城市列表
elseif ($_REQUEST['act'] == 'getCityList')
{
    $jsonArray['data'] = getCityList();
    JsonpEncode($jsonArray); 
}
/*手机城市列表*/
elseif($_REQUEST['act'] == 'getMobileCities'){
    $jsonArray['data'] = getMobileCities($_REQUEST['only_country']);
    JsonpEncode($jsonArray);
}
/* 修改会员密码 */
elseif ($_REQUEST['act'] == 'act_edit_password')
{
    include_once(ROOT_PATH . 'includes/lib_passport.php');

    $old_password = isset($_REQUEST['old_password']) ? trim($_REQUEST['old_password']) : null;
    $new_password = isset($_REQUEST['new_password']) ? trim($_REQUEST['new_password']) : '';
    $user_id      = isset($_REQUEST['uid'])  ? intval($_REQUEST['uid']) : $user_id;
    $code         = isset($_REQUEST['code']) ? trim($_REQUEST['code'])  : '';
    $post_user_name    = isset($_REQUEST['user_name']) ? trim($_REQUEST['user_name']) : '';
    if (strlen($new_password) < 6)
    {
        $jsonArray['state']='false';
        $jsonArray['message']='密码长度不能少于6位';
        JsonpEncode($jsonArray);
    }

    $user_name = $_SESSION['user_name'];
    if (empty($_SESSION['user_id']))
    {
        if (empty($post_user_name))
        {
            $jsonArray['state']='false';
            $jsonArray['message']='卡号是不能为空';
            JsonpEncode($jsonArray);
        }
        else{
            $user_name = $post_user_name;
        }
    }

    //修改卡密码
    $arr_param = array(	'CardInfo'=>array('CardNo'  => $user_name, 'CardPwd' => $old_password, 'CardNewPwd'=>$new_password) );
    $state = $cardPay->action($arr_param, 2);

    // 查询成功
    if ($state == 0)
    {
        $sql="UPDATE ".$ecs->table('users'). "SET `ec_salt`='0' WHERE user_id= '".$user_id."'";
        $db->query($sql);

        if (empty($_SESSION['user_id']))
        {
            $jsonArray['state']='true';
            $jsonArray['message']='密码修改成功！';
            JsonpEncode($jsonArray);
        }else{
            $jsonArray['state']='true';
            $jsonArray['message']='密码修改成功！';
            JsonpEncode($jsonArray);
        }
    }
    else
    {
        $jsonArray['state']='false';
        $jsonArray['message']=$cardPay->getMessage();
        JsonpEncode($jsonArray);
    }
}
