<?php
if (!defined('IN_ECS'))
{
	die('Hacking attempt');
}
define('ECS_WAP', true);

if (__FILE__ == '')
{
	die('Fatal error code: 0');
}

error_reporting(-1);

/* 取得当前ecshop所在的根目录 */
define('ROOT_PATH', str_replace('crontabAdmin/Crontab/weixin/lib/_init.php', '', str_replace('\\', '/', __FILE__)));

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
require(ROOT_PATH . 'includes/inc_constant.php');
require(ROOT_PATH . 'includes/cls_error.php');
require(ROOT_PATH . 'includes/lib_huayingcard.php');
require(ROOT_PATH . 'includes/httpRequest.php');

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

/* if ((DEBUG_MODE & 1) == 1)
{
	error_reporting(E_ALL);
}
else
{
	error_reporting(E_ALL ^ E_NOTICE);
}
if ((DEBUG_MODE & 4) == 4)
{
	include(ROOT_PATH . 'includes/lib.debug.php');
} */



header("Content-Type:text/html; charset=utf-8");
