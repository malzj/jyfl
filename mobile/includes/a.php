<?php 
define('IN_ECS', true);

define('ROOT_PATH', str_replace('mobile/includes/a.php', '', str_replace('\\', '/', __FILE__)));

if (file_exists(ROOT_PATH . 'data/config.php'))
{
    include(ROOT_PATH . 'data/config.php');
}
else
{
    include(ROOT_PATH . 'includes/config.php');
}

require(ROOT_PATH . 'includes/cls_ecshop.php');
$ecs = new ECS($db_name, $prefix);

require(ROOT_PATH . 'includes/cls_mysql.php');
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);

$row = $GLOBALS['db']->getRow('SELECT * FROM '.$GLOBALS['ecs']->table('users'). ' limit 1');
error_log(var_export($row,true),'3','error.log');


?>