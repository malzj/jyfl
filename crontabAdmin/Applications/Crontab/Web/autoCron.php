<?php

/**
 *   计划任务管理操作类
 */

require_once  __DIR__.'/_init.php';

date_default_timezone_set('PRC');

check_auth();

if(true)
{
	$fun = $_GET['fun'];
	if (strpos($fun, '/') !== false)
	{
		$data = array_filter(explode('/', $fun));
		$action = array_shift($data);
	}
	else
	{
		$data = array();
		$action = $fun;	
	}
	
	unset($_GET['fun']);

	//加载逻辑函数文件,仅仅调用当前模块
    if(!function_exists($action))
    {
        $php_file = WEB_ROOT.'/Modules/'.$action.'.php';
        if(file_exists($php_file))
        {
            require_once $php_file;
        }
    }
    
    //调用逻辑函数,由于无法在函数内使用global获取,因此注入$config到第一个参数,其他参数依次排列
    if(function_exists($action))
    {
        call_user_func_array($action, $data);
    }else{
        return _header('Location: /crontab/Applications/Web/autoCron.php?fun=index');
    }
    
}else{
    return false;
}
