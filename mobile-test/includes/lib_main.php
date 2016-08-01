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
 * $Author: testyang $
 * $Id: lib_main.php 15013 2008-10-23 09:31:42Z testyang $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 对输出编码
 *
 * @access  public
 * @param   string   $str
 * @return  string
 */
function encode_output($str)
{
//    if (EC_CHARSET != 'utf-8')
//    {
//        $str = ecs_iconv(EC_CHARSET, 'utf-8', $str);
//    }
    return htmlspecialchars($str);
}

/**
 * wap分页函数
 *
 * @access      public
 * @param       int     $num        总记录数
 * @param       int     $perpage    每页记录数
 * @param       int     $curr_page  当前页数
 * @param       string  $mpurl      传入的连接地址
 * @param       string  $pvar       分页变量
 */
function get_wap_pager($num, $perpage, $curr_page, $mpurl,$pvar='page')
{
    $multipage = '<div class="container next"><div class="row">';
    if($num > $perpage)
    {
        $page = 2;
        $offset = 1;
        $pages = ceil($num / $perpage);
        $all_pages = $pages;
        $tmp_page = $curr_page;
        $setp = strpos($mpurl, '?') === false ? "?" : '&amp;';
        if($curr_page > 1)
        {
            $multipage .= "<div class='col-xs-5'><h5 class='text-right'><a href=\"$mpurl${setp}${pvar}=".($curr_page-1)."\">上一页</a></h5></div>";
        }
        else {
        	$multipage .= "<div class='col-xs-5'><h5 class='text-right'><font color='#999'>上一页</font></h5></div>";
        }
        $multipage .= "<div class='col-xs-2'><div class='fanye'>|</div></div>";
        if(($curr_page++) < $pages)
        {
            $multipage .= "<div class='col-xs-5'><h5><a href=\"$mpurl${setp}${pvar}=".$curr_page++."\">下一页</a></h5></div>";
        }
        else{
        	$multipage .= "<div class='col-xs-5'><h5><font color='#999'>上一页</font></h5></div>";
        }   
    }
    $multipage .='</div></div>';    
    
    return $multipage;
}

/**
 * 返回尾文件
 *
 * @return  string
 */
function get_footer()
{
    if ($_SESSION['user_id'] > 0)
    {
        $footer = "<br/><a href='user.php?act=user_center'>用户中心</a>|<a href='user.php?act=logout'>退出</a>|<a href='javascript:scroll(0,0)' hidefocus='true'>回到顶部</a><br/>Copyright 2009<br/>Powered by ECShop v2.7.2";
    }
    else
    {
        $footer = "<br/><a href='user.php?act=login'>登陆</a>|<a href='user.php?act=register'>免费注册</a>|<a href='javascript:scroll(0,0)' hidefocus='true'>回到顶部</a><br/>Copyright 2009<br/>Powered by ECShop v2.7.2";
    }

    return $footer;
}

function show_wap_message($content, $links = '', $hrefs = '', $type = 'info', $auto_redirect = true)
{

	assign_template();

	$msg['content'] = $content;
	if (is_array($links) && is_array($hrefs))
	{
		if (!empty($links) && count($links) == count($hrefs))
		{
			foreach($links as $key =>$val)
			{
				$msg['url_info'][$val] = $hrefs[$key];
			}
			$msg['back_url'] = $hrefs['0'];
		}
	}
	else
	{
		$link   = empty($links) ? $GLOBALS['_LANG']['back_up_page'] : $links;
		$href    = empty($hrefs) ? 'javascript:history.back()'       : $hrefs;
		$msg['url_info'][$link] = $href;
		$msg['back_url'] = $href;
	}

	$msg['type']    = $type;
	$position = assign_ur_here(0, $GLOBALS['_LANG']['sys_msg']);
	$GLOBALS['smarty']->assign('page_title', $position['title']);   // 页面标题
	$GLOBALS['smarty']->assign('ur_here',    $position['ur_here']); // 当前位置

	if (is_null($GLOBALS['smarty']->get_template_vars('helps')))
	{
		$GLOBALS['smarty']->assign('helps', get_shop_help()); // 网店帮助
	}

	$GLOBALS['smarty']->assign('auto_redirect', $auto_redirect);
	$GLOBALS['smarty']->assign('message', $msg);
	$GLOBALS['smarty']->display('message.html');

	exit;
}

/**
 *  处理子类数据，如果没有子类，或子类都被屏蔽掉了，将返回空，否则返回第一个子类数据
 *  主导航的链接地址，默认是第一个子类的链接地址，
 */
function array_shift_nav( $child )
{
	// 不可在wap显示的导航（导航ID）
	$notWapShownav = array( 94, 22); // 删除指定的二级栏目
	$returnArray = array();

	if (empty($child))
	{
		return array();
	}

	$shiftChild = array_shift($child);

	if ( in_array( $shiftChild['id'] , $notWapShownav ))
	{
		$returnArray = array_shift_nav($child);
	}
	else
	{
		$returnArray = $shiftChild;
	}

	return $returnArray;
}

/**
 * 数组按照您指定的顺序排序，如果数组是array(1,2,5,80,50,21),如果我想把第21放到数组的开头，其他的都不变，可以使用这个函数。
 *
 * 简单：（简单是以一维数组的键名排序的）
 * $sortArray = array(1, 2, 5, 80, 50, 21)
 * $sortKey   = array(5, 0, 1, 2, 3, 4);
 * $sortType  = key;
 * 返回的结果：array(21, 1, 2, 5, 80, 50);
 *
 * 复杂： （是以一维或多维数组的值排序的）
 *
 * @param array  	$sortArray		要排序的数组
 * @param array  	$sortKey		排序的顺序（一维或多为数组）
 * @param string 	$sortType		排序类型，( key / val ) 默认是key，
 * 									key：$sortKey必须是一维数组，值必须是$sortArray的keys。（只支持一维数组，多维数组暂不支持）
 * 									val: $sortKey不限制，但值必须是和$sortArray结构对应，排序是按$sortArray中的某个值为准。
 * @return array    				返回一个排序后的数组
 */
function array_only_sort( $sortArray, $sortKey, $sortType="key")
{
	$returnArray = array();

	foreach ($sortKey as $key=>$val)
	{
		if ($sortType == 'key')
		{
			// 跳过空的 key
			if ( empty($sortArray[$val]) )
			{
				continue;
			}
			$returnArray[$val] = $sortArray[$val];
		}

	}

	/* 处理按val值排序的时候  */
	if ($sortType == 'val')
	{
		$keyId = $sortKey[0];
		$value = $sortKey[1];
		if ( is_string($keyId) && is_array($value) )
		{
			foreach ($value as $key=>$val)
			{
				foreach ($sortArray as $sortK=>$sortV)
				{
					if (is_array($val))
					{
						$tmpSortKey = array_merge(array($keyId), $val);
						$returnArray[$sortK] = array_only_sort($sortV, $tmpSortKey, $sortType);
					}
					else
					{
						if ( $val == $sortV[$keyId])
						{
							$returnArray[$sortK] = $sortV;
						}
					}
				}
			}
		}
	}
	return $returnArray;
}
/**
 *  得到固定的底部导航  
 */
function get_fixed( $ext = 0 )
{
	$active1 = $active2 = $active3 = $active4 = '';
	switch ( $ext )
	{
		case 1: $active1 = 'active'; break;
		case 2: $active2 = 'active'; break;
		case 3: $active3 = 'active'; break;
		case 4: $active4 = 'active'; break;
	}
	
	$html  = '<div class="container footer header-color">';
	$html .= '<div class="row text-center ">';
	
	$html .= '<div class="col-xs-3 '.$active1.'">
				<a href="/mobile/index.php"><span class="glyphicon glyphicon-home icon-footer"></span><span>首页</span></a>
			  </div>';
	$html .= '<div class="col-xs-3 '.$active2.'">
				<a href="/mobile/cat_all.php"><span class="glyphicon glyphicon-th-list icon-footer"></span><span>分类</span></a>
			  </div>';
	
	$html .= '<div class="col-xs-3 '.$active3.'">
				<a href="/mobile/flow.php"><span class="glyphicon glyphicon-shopping-cart icon-footer"></span><span>购物车</span></a>
			  </div>';
	$html .= '<div class="col-xs-3 '.$active4.'">
				<a href="/mobile/user.php"><span class="glyphicon glyphicon-user icon-footer"></span><span>个人中心</span></a>
			  </div>';
	$html .= '</div></div>';
	$html .= '<div style="width: 100%; height: 61px;"></div>';
	return $html;	
}

/**  
 *  得到固定的头（非搜索的头）
 *  @param	title   str     头信息
 *  @param  back	booler  是否显示返回按钮， 输入地址将跳转到制定的地址
 *  @param  $link   array   连接数组（默认是 null，如果传入数组，遍历数组，如果是true，将显示预设数据）
 */
function get_header($title, $back=true, $links=null)
{
	$html = '<div class="container header-color header-height"><div class="row">';
	
	$html .= '<div class="col-xs-2">';
			
	// 返回按钮
	if ($back === true)
	{
		$html .= '<a href="javascript:history.go(-1)"><span class="glyphicon glyphicon-menu-left tubiao"><img src="/mobile/images/img/logo1.png"></span></a>';
	}
	elseif (is_bool($back) == false)
	{
		$html .= '<a href="'.$back.'"><span class="glyphicon glyphicon-menu-left tubiao"><img src="/mobile/images/img/logo1.png"></span></a>';
	}
	
	$html .='</div>';
	
	// 标题
	$html .='<div class="col-xs-8 text-center"><h4 style="white-space: nowrap;text-overflow: ellipsis;">'.$title.'</h4></div>';
	// 连接数组处理
	if ( is_array($links) && count($links) > 1)
	{
		foreach ($links as $link)
		{
			$html .= '';
		}
	}
	else if (is_array($links) && count($links) == 1)
	{
		$html .= '<div class="col-xs-2 text-right"><h5><a href="'.$links[0]['link'].'" style="color: white;">'.$links[0]['title'].'</a></h5></div>';
	}
	// 当$links === true的时候，显示默认数据
	else if ( $links === true)
	{
		$html .= '<div class="col-xs-2"><a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-menu-hamburger icon_more"></span></a><ul class="dropdown-menu" role="menu" aria-labelledby="drop1">';
		$html .= '<li><a tabindex="-1" href="index.php">首页</a></li><li><a tabindex="-1" href="user.php">个人中心</a></li><li><a tabindex="-1" href="user.php?act=logout">退出</a></li>';
		$html .= '</ul></div>';
	}
	
	
	$html .= '</div></div>';
	
	return $html;
}
/*
 *功能：php完美实现下载远程图片保存到本地
*参数：文件url,保存文件目录,保存文件名称，使用的下载方式
*当保存文件名称为空时则使用远程文件原来的名称
*/

function getImage($url,$save_dir='',$filename='',$type=0){
	if(trim($url)==''){
		return array('file_name'=>'','save_path'=>'','error'=>1);
	}
	if(trim($save_dir)==''){
		$save_dir='./';
	}
	if(trim($filename)==''){//保存文件名
		$ext=strrchr($url,'.');
		if($ext!='.gif'&&$ext!='.jpg'){
			return array('file_name'=>'','save_path'=>'','error'=>3);
		}
		$filename=time().$ext;
	}
	if(0!==strrpos($save_dir,'/')){
		$save_dir.='/';
	}
	//创建保存目录
	if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
		return array('file_name'=>'','save_path'=>'','error'=>5);
	}
	//获取远程文件所采用的方法
	if($type){
		$ch=curl_init();
		$timeout=5;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$img=curl_exec($ch);
		curl_close($ch);
	}else{
		ob_start();
		readfile($url);
		$img=ob_get_contents();
		ob_end_clean();
	}
	//$size=strlen($img);
	//文件大小
	$fp2=@fopen($save_dir.$filename,'a');
	fwrite($fp2,$img);
	fclose($fp2);
	unset($img,$url);
	return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
}

// 整理手机端显示的导航信息
function init_wap_middle(&$middle)
{
	foreach($middle as $key=>&$val){
		if ($val['wap_show'] == 0)
		{
			unset($middle[$key]);
		}
		if ( !empty($val['child']) )
		{
			init_wap_middle($val['child']);
		}
	}
}
?>