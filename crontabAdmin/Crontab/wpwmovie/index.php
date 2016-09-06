<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/9/1
 * Time: 10:06
 */
define('IN_ECS', true);

include_once(dirname(__FILE__) . '/lib/_init.php');
require(ROOT_PATH . 'includes/lib_wpwMovieClass.php');

$wpwMovie = new wpwMovie();

$cinma_list = $wpwMovie ->getCinemas();
echo "<pre>";
print_r($cinma_list);
echo "</pre>";