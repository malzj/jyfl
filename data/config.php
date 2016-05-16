<?php
// database host
$db_host   = "192.168.1.161:3306";

// database name
$db_name   = "juyoufuli";

// database username
$db_user   = "test";

// database password
$db_pass   = "123456";

// table prefix
$prefix    = "ecs_";

$timezone    = "UTC";

$cookie_path    = "/";

$cookie_domain    = "";

$session = "1440";

define('EC_CHARSET','utf-8');

if(!defined('ADMIN_PATH'))
{
define('ADMIN_PATH','admin');
}

define('AUTH_KEY', 'this is a key');

define('OLD_AUTH_KEY', '');

define('API_TIME', '2014-10-08 03:48:23');

?>