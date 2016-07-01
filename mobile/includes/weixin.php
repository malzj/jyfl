<?php

	/* 取得当前ecshop所在的根目录 */
define('ROOT_PATH', str_replace('mobile/includes/weixin.php', '', str_replace('\\', '/', __FILE__)));

if (file_exists(ROOT_PATH . 'data/config.php'))
{
    include(ROOT_PATH . 'data/config.php');
}
else
{
    include(ROOT_PATH . 'includes/config.php');
}

error_log( "2222" , 3 , 'error.log');
//require_once ROOT_PATH . "includes/cls_ecshop.php";
 /* require(ROOT_PATH . 'includes/lib_goods.php');
require(ROOT_PATH . 'includes/lib_base.php');
require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . 'includes/lib_time.php');
require(ROOT_PATH . 'includes/lib_main.php');
require(ROOT_PATH . 'includes/inc_constant.php'); */

/* 创建 ECSHOP 对象 */
/* $ecs = new ECS($db_name, $prefix);
define('DATA_DIR', $ecs->data_dir());
define('IMAGE_DIR', $ecs->image_dir()); */

/* 初始化数据库类 */
// require(ROOT_PATH . 'includes/cls_mysql.php');
// $db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
// $db->set_disable_cache_tables(array($ecs->table('sessions'), $ecs->table('sessions_data'), $ecs->table('cart')));
// $db_host = $db_user = $db_pass = $db_name = NULL;

/* 创建错误处理对象 */
//$err = new ecs_error('message.html');


/* 载入系统参数 */
//$_CFG = load_config();

/* 载入语言文件 */
//require(ROOT_PATH . 'languages/' . $_CFG['lang'] . '/common.php');

/* 初始化session */
/* require(ROOT_PATH . 'includes/cls_session.php');
$sess = new cls_session($db, $ecs->table('sessions'), $ecs->table('sessions_data'), 'ecsid');
define('SESS_ID', $sess->get_session_id()); */