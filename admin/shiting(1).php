<?php

/**
 * ECSHOP 管理中心文章处理程序文件
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: article.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require_once(ROOT_PATH . "includes/fckeditor/fckeditor.php");
require_once(ROOT_PATH . 'includes/lib_order.php');
require_once(ROOT_PATH . 'includes/cls_image.php');


/*初始化数据交换对象 */
$exc   = new exchange($ecs->table("yingyuan"), $db, 'article_id', 'title');
//$image = new cls_image();

/* 允许上传的文件类型 */
$allow_file_types = '|GIF|JPG|PNG|BMP|SWF|DOC|XLS|PPT|MID|WAV|ZIP|RAR|PDF|CHM|RM|TXT|';

/*------------------------------------------------------ */
//-- 文章列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('cat_select',  article_cat_list(0));
    $smarty->assign('ur_here',      $_LANG['03_article_list']);
    $smarty->assign('action_link',  array('text' => $_LANG['yingyuan_add'], 'href' => 'shiting.php?act=add'));
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $article_list = get_articleslist();

    $smarty->assign('article_list',    $article_list['arr']);
    $smarty->assign('filter',          $article_list['filter']);
    $smarty->assign('record_count',    $article_list['record_count']);
    $smarty->assign('page_count',      $article_list['page_count']);

    $smarty->assign('cinema_nav',   4);
    $sort_flag  = sort_flag($article_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    assign_query_info();
    $smarty->display('shiting_list.htm');
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('article_manage');

    $article_list = get_articleslist();

    $smarty->assign('article_list',    $article_list['arr']);
    $smarty->assign('filter',          $article_list['filter']);
    $smarty->assign('record_count',    $article_list['record_count']);
    $smarty->assign('page_count',      $article_list['page_count']);

    $sort_flag  = sort_flag($article_list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('shiting_list.htm'), '',
        array('filter' => $article_list['filter'], 'page_count' => $article_list['page_count']));
}

/*------------------------------------------------------ */
//-- 添加文章
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('article_manage');

    /* 创建 html editor */
    create_html_editor('FCKeditor1');

    /*初始化*/
    $article = array();
    $article['is_open'] = 1;

    /* 取得分类、品牌 */
    $smarty->assign('goods_cat_list', cat_list());
    $smarty->assign('brand_list',     get_brand_list());

    /* 清理关联商品 */
    $sql = "DELETE FROM " . $ecs->table('goods_article') . " WHERE article_id = 0";
    $db->query($sql);

    if (isset($_GET['id']))
    {
        $smarty->assign('cur_id',  $_GET['id']);
    }
    $smarty->assign('article',     $article);
    $smarty->assign('cat_select',  article_cat_list(0));
    $smarty->assign('ur_here',     $_LANG['article_add']);
    $smarty->assign('action_link', array('text' => $_LANG['03_shiting_list'], 'href' => 'shiting.php?act=list'));
    $smarty->assign('form_action', 'insert');



	 /* 取得每个收货地址的省市区列表 */
	$province_list = array();
	$city_list = array();
	$district_list = array();
	
	$consignee['country']  = isset($consignee['country'])  ? intval($consignee['country'])  : 1;
	$consignee['province'] = isset($consignee['province']) ? intval($consignee['province']) : 0;
	$consignee['city']     = isset($consignee['city'])     ? intval($consignee['city'])     : 0;

	$province_list = get_regions(0, 0);
	$city_list     = get_regions(1, $consignee['province']);
	$district_list = get_regions(3, $consignee['city']);
  
	$smarty->assign('province_list', $province_list);
	$smarty->assign('city_list',     $city_list);
	$smarty->assign('district_list', $district_list);



    assign_query_info();
    $smarty->display('shiting_info.htm');
}

/*------------------------------------------------------ */
//-- 添加文章
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert')
{
    /* 权限判断 */
    admin_priv('article_manage');
	/*插入数据*/

    $add_time = gmtime();
    
    $sql = "INSERT INTO ".$ecs->table('yingyuan')."(title, province, city, district, address,2d,3d, is_open,add_time) ".
            "VALUES ('$_POST[title]', 0, '$_POST[province]', '$_POST[city]', '$_POST[address]', '$_POST[liangdi]', '$_POST[sandi]', '$_POST[is_open]', '$add_time')";
    $db->query($sql);

   
    $link[0]['text'] = $_LANG['continue_add'];
    $link[0]['href'] = 'shiting.php?act=add';

    $link[1]['text'] = $_LANG['back_list'];
    $link[1]['href'] = 'shiting.php?act=list';

    admin_log($_POST['title'],'add','article');

    clear_cache_files(); // 清除相关的缓存文件

	$msg = "添加成功";

    sys_msg($msg,0, $link);
}

/*------------------------------------------------------ */
//-- 编辑
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('article_manage');

    /* 取文章数据 */
    $sql = "SELECT * FROM " .$ecs->table('yingyuan'). " WHERE article_id='$_REQUEST[id]'";
    $article = $db->GetRow($sql);

	$province_list = get_regions(0, 0);
	$city_list     = get_regions(1, $article['city']);
	//$district_list = get_regions(3, $article['city']);
  
	$smarty->assign('province_list', $province_list);
	$smarty->assign('city_list',     $city_list);
	$smarty->assign('district_list', $district_list);
	
	//var_dump($city_list);
	//var_dump($article);

    $smarty->assign('article',     $article);
    $smarty->assign('cat_select',  article_cat_list(0, $article['cat_id']));
    $smarty->assign('ur_here',     $_LANG['article_edit']);
    $smarty->assign('action_link', array('text' => $_LANG['03_shiting_list'], 'href' => 'shiting.php?act=list&' . list_link_postfix()));
    $smarty->assign('form_action', 'update');	

    assign_query_info();
    $smarty->display('shiting_info.htm');
}

if ($_REQUEST['act'] =='update')
{
    /* 权限判断 */
    admin_priv('article_manage');
    
    if ($exc->edit("title='$_POST[title]', province = '$_POST[province]', city='$_POST[city]', district='$_POST[district]', address='$_POST[address]', is_open='$_POST[is_open]', 2d='$_POST[liangdi]', 3d='$_POST[sandi]'", $_POST['id']))
    {
        $link[0]['text'] = '返回列表';
        $link[0]['href'] = 'shiting.php?act=list';
        clear_cache_files();
        sys_msg('编辑成功', 0, $link);
    }
    else
    {
        die($db->error());
    }
}

/*------------------------------------------------------ */
//-- 编辑文章主题
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit_title')
{
    check_authz_json('article_manage');

    $id    = intval($_POST['id']);
    $title = json_str_iconv(trim($_POST['val']));

    /* 检查文章标题是否重复 */
    if ($exc->num("title", $title, $id) != 0)
    {
        make_json_error(sprintf($_LANG['title_exist'], $title));
    }
    else
    {
        if ($exc->edit("title = '$title'", $id))
        {
            clear_cache_files();
            admin_log($title, 'edit', 'article');
            make_json_result(stripslashes($title));
        }
        else
        {
            make_json_error($db->error());
        }
    }
}

/*------------------------------------------------------ */
//-- 切换是否显示
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_show')
{
    check_authz_json('article_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("is_open = '$val'", $id);
    clear_cache_files();

    make_json_result($val);
}

/*------------------------------------------------------ */
//-- 切换文章重要性
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'toggle_type')
{
    check_authz_json('article_manage');

    $id     = intval($_POST['id']);
    $val    = intval($_POST['val']);

    $exc->edit("article_type = '$val'", $id);
    clear_cache_files();

    make_json_result($val);
}



/*------------------------------------------------------ */
//-- 删除文章主题
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    check_authz_json('article_manage');

    $id = intval($_GET['id']);


    /* 删除原来的文件 */
   $sql = "DELETE FROM " . $GLOBALS['ecs']->table('yingyuan') .
            " WHERE article_id = '$id' LIMIT 1";
    $GLOBALS['db']->query($sql);


    $url = 'shiting.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

    ecs_header("Location: $url\n");
    exit;
}



/*------------------------------------------------------ */
//-- 搜索商品
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'get_goods_list')
{
    include_once(ROOT_PATH . 'includes/cls_json.php');
    $json = new JSON;

    $filters = $json->decode($_GET['JSON']);

    $arr = get_goods_list($filters);
    $opt = array();

    foreach ($arr AS $key => $val)
    {
        $opt[] = array('value' => $val['goods_id'],
                        'text' => $val['goods_name'],
                        'data' => $val['shop_price']);
    }

    make_json_result($opt);
}
/*------------------------------------------------------ */
//-- 批量操作
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'batch')
{
    /* 批量删除 */
    if (isset($_POST['type']))
    {
        if ($_POST['type'] == 'button_remove')
        {
            admin_priv('article_manage');

            if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
            {
                sys_msg($_LANG['no_select_article'], 1);
            }

            /* 删除原来的文件 */
            

            foreach ($_POST['checkboxes'] AS $key => $id)
            {
             
			   $sql = "DELETE FROM " . $GLOBALS['ecs']->table('yingyuan') . " WHERE article_id = '$id' LIMIT 1";
				 $GLOBALS['db']->query($sql);
			admin_log(addslashes($name),'remove','article');
           
            }

        }

        /* 批量隐藏 */
        if ($_POST['type'] == 'button_hide')
        {
            check_authz_json('article_manage');
            if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
            {
                sys_msg($_LANG['no_select_article'], 1);
            }

            
			foreach ($_POST['checkboxes'] AS $key => $id)
            {
         
			  $sql = "UPDATE " . $ecs->table('yingyuan') .
                    " SET is_open = '0' " .
                    " WHERE article_id = '$id' LIMIT 1";
             $db->query($sql);

            }
        }

        /* 批量显示 */
        if ($_POST['type'] == 'button_show')
        {
            check_authz_json('article_manage');
            if (!isset($_POST['checkboxes']) || !is_array($_POST['checkboxes']))
            {
                sys_msg($_LANG['no_select_article'], 1);
            }

            foreach ($_POST['checkboxes'] AS $key => $id)
            {
              $sql = "UPDATE " . $ecs->table('yingyuan') .
                    " SET is_open = '1' " .
                    " WHERE article_id = '$id' LIMIT 1";
             $db->query($sql);
            }
        }

       
    }

    /* 清除缓存 */
    clear_cache_files();
    $lnk[] = array('text' => $_LANG['back_list'], 'href' => 'shiting.php?act=list');
	$msg="返回列表页";
    sys_msg($msg, 0, $lnk);
}

/*------------------------------------------------------ */
//-- 将影院管理到影院列表
/*------------------------------------------------------ */

elseif ($_REQUEST['act'] == 'cinemapush')
{
	$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0 ;
	$cinema = $GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['ecs']->table('yingyuan')." WHERE article_id =".$id);
	assign_query_info();
	$smarty->assign('cinema',     $cinema);
	$smarty->display('cinema_brush_info.htm');

}
elseif ($_REQUEST['act'] == 'cinemapushadd')
{

	$searchCinemaid = empty($_REQUEST['search_cinema_name']) ? 0 : trim($_REQUEST['search_cinema_name']);
	$id = empty($_REQUEST['id']) ? 0 : intval($_REQUEST['id']);
	$insert = empty($_REQUEST['insert']) ? 2 : intval($_REQUEST['insert']);
	$source = 2;

	if ( $insert == 1 && $searchCinemaid != -1 )
	{
		$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
		sys_msg('搜索结果中没有数据，才可以选择是否添加（是）！', 0, $link);
	}

	if ($searchCinemaid == 0 || ($insert == 2 && $searchCinemaid == -1))
	{
		$link[] = array('text' => $_LANG['go_back'], 'href' => 'javascript:history.back(-1)');
		sys_msg('请在搜索结果中选择一个影院，如果没有影院请搜索!', 0, $link);
	}

	$cinema = $GLOBALS['db']->getRow('SELECT * FROM '.$GLOBALS['ecs']->table('yingyuan')." WHERE article_id = ".$id);

	// 更新影院状态
	if ($insert == 1)
	{
		// 基本信息
		$update['cinema_name'] 		= $cinema['title'];
		$update['cinema_address'] 	= $cinema['address'];
		$update['source'] 			= $source;
		$update['region_id'] 		= $cinema['city'];
		$update['area_id'] 			= $cinema['district'];
		$update['update_time'] 		= gmtime();

		// 设置在线选座信息
		if ($source == 2)
		{
			$update['brush_cinema_id'] 	= $cinema['article_id'];
			$update['is_brush'] 		= 1;
		}

		$GLOBALS['db']->query("INSERT INTO ".$GLOBALS['ecs']->table('cinema_list')." (".implode(',',array_keys($update)).") VALUES('".implode('\',\'',array_values($update))."')");
	}
	else
	{

		$set = ' is_brush = 1, brush_cinema_id = "'.$cinema['article_id'].'"';
		$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('cinema_list')." SET ".$set." WHERE id = '$id'");
	}

	$exc->edit('is_update = 2',$id);

	$link[] = array('text' => $_LANG['go_back'], 'href' => 'shiting.php?act=list');
	sys_msg('操作完成', 0, $link);

}

/* 把商品删除关联 */
function drop_link_goods($goods_id, $article_id)
{
    $sql = "DELETE FROM " . $GLOBALS['ecs']->table('goods_article') .
            " WHERE goods_id = '$goods_id' AND article_id = '$article_id' LIMIT 1";
    $GLOBALS['db']->query($sql);
    create_result(true, '', $goods_id);
}

/* 取得文章关联商品 */
function get_article_goods($article_id)
{
    $list = array();
    $sql  = 'SELECT g.goods_id, g.goods_name'.
            ' FROM ' . $GLOBALS['ecs']->table('goods_article') . ' AS ga'.
            ' LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ON g.goods_id = ga.goods_id'.
            " WHERE ga.article_id = '$article_id'";
    $list = $GLOBALS['db']->getAll($sql);

    return $list;
}

/* 获得文章列表 */
function get_articleslist()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'a.article_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if (!empty($filter['keyword']))
        {
            $where = " AND a.title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }
        
        $where .= ' AND is_update = 0';
   
        /* 文章总数 */
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('yingyuan'). ' AS a '.             
               'WHERE 1 ' .$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        /* 获取文章数据 */
        $sql = 'SELECT a.*  '.
               'FROM ' .$GLOBALS['ecs']->table('yingyuan'). ' AS a '.
               'WHERE 1 ' .$where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];

        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $arr = array();
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);

    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['date'] = local_date($GLOBALS['_CFG']['time_format'], $rows['add_time']);
		
		 $rows['province'] = get_add_cn($rows['province']);
		 $rows['city'] = get_add_cn($rows['city']);
		 $rows['district'] = get_add_cn($rows['district']);
        $arr[] = $rows;
    }




    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}



?>
