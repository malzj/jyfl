<?php 
	
require_once 'lib/db.php';

$db = new DB();
$row = $db->get_one('SELECT * FROM ecs_users order by user_id desc LIMIT 1');
var_dump($row);
