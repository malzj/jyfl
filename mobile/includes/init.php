<?php

/**
 * ECSHOP mobile前台公共函数
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: init.php 15013 2008-10-23 09:31:42Z liuhui $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
define('ECS_WAP', true);

if (__FILE__ == '')
{
    die('Fatal error code: 0');
}

/* 取得当前ecshop所在的根目录 */
define('ROOT_PATH', str_replace('mobile/includes/init.php', '', str_replace('\\', '/', __FILE__)));

/* 初始化设置 */
@ini_set('memory_limit',          '64M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);
@ini_set("arg_separator.output","&amp;");

if (DIRECTORY_SEPARATOR == '\\')
{
    @ini_set('include_path',      '.;' . ROOT_PATH);
}
else
{
    @ini_set('include_path',      '.:' . ROOT_PATH);
}

if (file_exists(ROOT_PATH . 'data/config.php'))
{
    include(ROOT_PATH . 'data/config.php');
}
else
{
    include(ROOT_PATH . 'includes/config.php');
}

if (defined('DEBUG_MODE') == false)
{
    define('DEBUG_MODE', 7);
}

if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
}

$php_self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
if ('/' == substr($php_self, -1))
{
    $php_self .= 'index.php';
}
define('PHP_SELF', $php_self);

require(ROOT_PATH . 'includes/cls_ecshop.php');
require(ROOT_PATH . 'includes/lib_goods.php');
require(ROOT_PATH . 'includes/lib_base.php');
require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . 'includes/lib_time.php');
require(ROOT_PATH . 'includes/lib_main.php');
require(ROOT_PATH . 'mobile/includes/lib_main.php');
require(ROOT_PATH . 'includes/inc_constant.php');
require(ROOT_PATH . 'includes/cls_error.php');
require(ROOT_PATH . 'includes/lib_article.php');
require(ROOT_PATH . 'includes/lib_insert.php');
require(ROOT_PATH . 'includes/lib_huayingcard.php');
require(ROOT_PATH . 'includes/httpRequest.php');
require(ROOT_PATH . 'includes/lib_basic.php');

// 实例化卡系统接口
$cardPay = new huayingcard();

/* 对用户传入的变量进行转义操作。*/
if (!get_magic_quotes_gpc())
{
    if (!empty($_GET))
    {
        $_GET  = addslashes_deep($_GET);
    }
    if (!empty($_POST))
    {
        $_POST = addslashes_deep($_POST);
    }

    $_COOKIE   = addslashes_deep($_COOKIE);
    $_REQUEST  = addslashes_deep($_REQUEST);
}

/* 创建 ECSHOP 对象 */
$ecs = new ECS($db_name, $prefix);
define('DATA_DIR', $ecs->data_dir());
define('IMAGE_DIR', $ecs->image_dir());

/* 初始化数据库类 */
require(ROOT_PATH . 'includes/cls_mysql.php');
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
$db->set_disable_cache_tables(array($ecs->table('sessions'), $ecs->table('sessions_data'), $ecs->table('cart')));
$db_host = $db_user = $db_pass = $db_name = NULL;

/* 创建错误处理对象 */
$err = new ecs_error('message.html');


/* 载入系统参数 */
$_CFG = load_config();

/* 载入语言文件 */
require(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/common.php');

/* 初始化session */
require(ROOT_PATH . 'includes/cls_session.php');
$sess = new cls_session($db, $ecs->table('sessions'), $ecs->table('sessions_data'), 'ecsid');
define('SESS_ID', $sess->get_session_id());

if (!defined('INIT_NO_SMARTY'))
{
    header('Cache-control: private');
    header('Content-type: text/html; charset=utf-8');

    /* 创建 Smarty 对象。*/
    require(ROOT_PATH . 'includes/cls_template.php');
    $smarty = new cls_template;

    $smarty->cache_lifetime = $_CFG['cache_time'];
    $smarty->template_dir   = ROOT_PATH . 'mobile/templates';
    $smarty->cache_dir      = ROOT_PATH . 'temp/caches';
    $smarty->compile_dir    = ROOT_PATH . 'temp/compiled/mobile';

    if ((DEBUG_MODE & 2) == 2)
    {
        $smarty->direct_output = true;
        $smarty->force_compile = true;
    }
    else
    {
        $smarty->direct_output = false;
        $smarty->force_compile = false;
    }
}

require(ROOT_PATH . 'mobile/includes/mobile_init.php');

if (!defined('INIT_NO_USERS'))
{
    /* 会员信息 */
    $user =& init_users();
    if (empty($_SESSION['user_id']))
    {
        if ($user->get_cookie())
        {
            /* 如果会员已经登录并且还没有获得会员的帐户余额、积分以及优惠券 */
            if ($_SESSION['user_id'] > 0 && !isset($_SESSION['user_money']))
            {
                update_user_info();
            }
        }
        else
        {
            $_SESSION['user_id']     = 0;
            $_SESSION['user_name']   = '';
            $_SESSION['email']       = '';
            $_SESSION['user_rank']   = 0;
            $_SESSION['discount']    = 1.00;
        }
    }
}

if ((DEBUG_MODE & 1) == 1)
{
    error_reporting(E_ALL);
}
else
{
    error_reporting(E_ALL ^ E_NOTICE);
}
if ((DEBUG_MODE & 4) == 4)
{
   // include(ROOT_PATH . 'includes/lib.debug.php');
}

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

header("Content-Type:text/html; charset=utf-8");

/* if (empty($_CFG['wap_config']))
{
	echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' /><title>ECShop_mobile</title></head><body><p align='left'>对不起,{$_CFG['shop_name']}暂时没有开启手机购物功能</p></body></html>";
	exit();
} */

//没登陆用户强制登陆
//不需要登录的操作
$arr_noLogin = array('user', 'captcha', 'region', 'entity','topic', 'respond','userAuth');
$str_scriptName = substr($_SERVER['PHP_SELF'],  strrpos($_SERVER['PHP_SELF'],'/')+1 , -4);

if (empty($_SESSION['user_id']) && !in_array($str_scriptName, $arr_noLogin)){
	// 返回的数据
    $jsonArray = array(
        'state'=>'false',
        'data'=>array('isLogin'=>1),
        'message'=>'你未登录或登录超时，请重新登录！'
    );
    exit($_GET['jsoncallback']."(".json_encode($jsonArray).")");
}else{
	//城市id
	if ($_SESSION['user_id']){

		//访问者ip城市
		require(ROOT_PATH . 'includes/lib_getIpCity.php');

		$arr_cityList = getCityList();//获取城市列表
		//var_dump($arr_cityList);exit;
		$smarty->assign('citys', $arr_cityList);

		//获取get，session，cookie中的cityid
		//$int_cityId = (isset($_REQUEST['cityid']) && intval($_REQUEST['cityid']) > 0) ? intval($_REQUEST['cityid']) : (isset($_SESSION['cityid']) ? intval($_SESSION['cityid']) : (isset($_COOKIE['ECS']['cityid']) ? intval($_COOKIE['ECS']['cityid']) : 0));

		//获取get和session中的cityid
		$int_cityId = (isset($_REQUEST['cityid']) && intval($_REQUEST['cityid']) > 0) ? intval($_REQUEST['cityid']) : (isset($_SESSION['cityid']) ? intval($_SESSION['cityid']) : 0);

		$arr_cityInfo = getIpCity($int_cityId);//获取指定城市相关信息
		$smarty->assign('cityinfo', $arr_cityInfo);
		$smarty->assign('cityid', $arr_cityInfo['region_id']);

		setcookie("ECS[cityid]", intval($arr_cityInfo['region_id']), gmtime() + 86400 * 7, '/');
		$_SESSION['cityid'] = intval($arr_cityInfo['region_id']);

		//如果重新选择城市或第一次访问
		if (isset($_REQUEST['cityid']) || $arr_cityInfo['isFirst']){
			//清空购物车
			$db->query('DELETE FROM ' . $ecs->table('cart') . " WHERE session_id = '" . SESS_ID . "'");
			clear_all_files();//清除模板缓存
			unset($_SESSION['flow_consignee']); //清除session中保存的收货人信息
			unset($_SESSION['flow_order']);     //清除session中保存的订单信息
			unset($_SESSION['yc_flow_order']);  //清除session中保存的演出订单信息
			unset($_SESSION['direct_shopping']);				
			
			/* if ($arr_cityInfo['is_home'] && $str_scriptName != 'home'){
				ecs_header("Location:home.php\n");
				exit;
			}else{
				$arr_isJump = array('flow', 'yanchu_order', 'shiting_order');
				if (in_array($str_scriptName, $arr_isJump)){//更换城市前停留在订单页更换后跳到首页
					ecs_header("Location:index.php\n");
					exit;
				}
			} */
		}
	}
}

/* 判断是否支持gzip模式 */
if (gzip_enabled())
{
    ob_start('ob_gzhandler');
}

/* wap头文件 */
//if (substr($_SERVER['SCRIPT_NAME'], strrpos($_SERVER['SCRIPT_NAME'], '/')) != '/user.php')
//{}


?>