<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/8/23
 * Time: 10:33
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

/*获取二维码字串*/
if($_REQUEST['act'] == 'get_code_str')
{
    //登录成功请求获取加密卡号
    $code = getCode($_SESSION['user_name']);
    $jsonArray['data'] = $_SESSION;
    $jsonArray['data']['code'] = !empty($code)?$code:$_SESSION['user_name'];
    JsonpEncode($jsonArray);
}
/*余额刷新*/
elseif($_REQUEST['act'] == 'refresh_cash')
{
    
}