<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/lib_basic.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

if(!isset($_REQUEST['act'])){
    $action = $_REQUEST['act'];
}

if($action == 'order_details'){
    
}