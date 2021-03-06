<?php

/**
 * 用户身份验证
 * @var unknown
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_movie_times.php');

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
    init_wap_middle($navList['middle']);

    $sql = "SELECT region_name FROM ".$GLOBALS['ecs']->table('region')." WHERE region_id = '".$_SESSION['cityid']."'";
    $city_cn = $GLOBALS['db']->getOne($sql);
    $user_sql = 'SELECT u.nickname,u.pic,u.company_id,u.card_money,u.mobile_phone,c.grade_id,c.company_name,c.logo_img,c.back_img,c.m_back_img FROM '.$GLOBALS['ecs']->table('users').' AS u LEFT JOIN '.$GLOBALS['ecs']->table('company').
        ' AS c ON u.company_id = c.card_company_id WHERE user_id='.$_SESSION['user_id'];
    $user_info = $GLOBALS['db']->getRow($user_sql);
    
    if (is_times_card())
    {
        $maxBuyCount = getMaxBuyCount();
        $jsonArray['data']['isTimes'] = '1';
        $jsonArray['data']['maxBuy'] = $maxBuyCount;
    }
    else {
        $jsonArray['data']['isTimes'] = '0';
    }
    
    $jsonArray['data']['nav_list'] = $navList['middle'];
    $jsonArray['data']['user_info'] = $user_info;
    $jsonArray['data']['city_cn'] = $city_cn;
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
    sort($jsonArray['data']);
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
