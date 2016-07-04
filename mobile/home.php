<?php
	define('IN_ECS', true);
	require(dirname(__FILE__) . '/includes/init.php');
	
	
	assign_template();
	
	$_SESSION['is_home'] = 1;
	header('location:index.php');
?>