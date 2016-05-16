<?php

/**
 * ECSHOP 管理中心供货商管理
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop120.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: ecshop120 $
 * $Id: suppliers.php 15013 2009-05-13 09:31:42Z ecshop120 $
 */

define('IN_ECS', true);

require_once (dirname(__FILE__) . '/includes/init.php');
require_once (ROOT_PATH . 'languages/' .$_CFG['lang']. '/admin/supplier.php');
$smarty->assign('lang', $_LANG);


/*------------------------------------------------------ */
//-- 供货商列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
     /* 检查权限 */
     admin_priv('supplier_manage');

    /* 查询 */
    $result = suppliers_list();

    /* 模板赋值 */
	$ur_here_lang = $_REQUEST['status'] =='1' ? $_LANG['supplier_list'] : $_LANG['supplier_reg_list'];
    $smarty->assign('ur_here', $ur_here_lang); // 当前导航
	 $smarty->assign('action_link',  array('text' => $_LANG['add_supplier'], 'href'=>'supplier.php?act=add'));

    $smarty->assign('full_page',        1); // 翻页参数

    $smarty->assign('supplier_list',    $result['result']);
    $smarty->assign('filter',       $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count',   $result['page_count']);
    $smarty->assign('sort_suppliers_id', '<img src="images/sort_desc.gif">');

    /* 显示模板 */
    assign_query_info();
    $smarty->display('supplier_list.htm');
}

/*------------------------------------------------------ */
//-- 排序、分页、查询
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    check_authz_json('supplier_manage');

    $result = suppliers_list();

    $smarty->assign('supplier_list',    $result['result']);
    $smarty->assign('filter',       $result['filter']);
    $smarty->assign('record_count', $result['record_count']);
    $smarty->assign('page_count',   $result['page_count']);

    /* 排序标记 */
    $sort_flag  = sort_flag($result['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('supplier_list.htm'), '',
        array('filter' => $result['filter'], 'page_count' => $result['page_count']));
}


/*------------------------------------------------------ */
//-- 查看、编辑供货商
/*------------------------------------------------------ */
elseif ($_REQUEST['act']== 'edit')
{
    /* 检查权限 */
    admin_priv('supplier_manage');
    $suppliers = array();

     /* 取得供货商信息 */
     $id = $_REQUEST['id'];
	 $status = intval($_REQUEST['status']);
     $sql = "SELECT * FROM " . $ecs->table('supplier') . " WHERE supplier_id = '$id'";
     $supplier = $db->getRow($sql);
     if (count($supplier) <= 0)
     {
          sys_msg('该供应商不存在！');
     }

	 $sql = "SELECT user_name FROM " . $ecs->table('users') . " WHERE user_id = '$supplier[user_id]'";
     $supplier_user = $db->getRow($sql);

	 $supplier['user_name'] = $supplier_user['user_name'];
     
	/* 省市县 */
	$supplier_country = $supplier['country'] ?  $supplier['country'] : $_CFG['shop_country'];
	$smarty->assign('country_list',       get_regions());	
	$smarty->assign('province_list', get_regions(1, $supplier_country));
	$smarty->assign('city_list', get_regions(2, $supplier['province']));
	$smarty->assign('district_list', get_regions(3, $supplier['city']));
	$smarty->assign('supplier_country', $supplier_country);
	 /* 供货商等级 */
	$sql="select rank_id,rank_name from ". $ecs->table('supplier_rank') ." order by sort_order";
	$supplier_rank=$db->getAll($sql);
	$smarty->assign('supplier_rank', $supplier_rank);

     $smarty->assign('ur_here', $_LANG['edit_supplier']);
	 $lang_supplier_list = $status=='1' ? $_LANG['supplier_list'] :  $_LANG['supplier_reg_list'];
     $smarty->assign('action_link', array('href' => 'supplier.php?act=list&status=1', 'text' =>$_LANG['supplier_list'] ));

     $smarty->assign('form_action', 'update');
     $smarty->assign('supplier', $supplier);
     $smarty->assign('card_rule', card_rule());
    
     assign_query_info();

     $smarty->display('supplier_info.htm');
   

}

elseif ($_REQUEST['act']== 'add' )
{
    /* 检查权限 */
    admin_priv('supplier_manage');
    $suppliers = array();

     /* 取得供货商信息 */
    
	/* 省市县 */
	$supplier_country = $supplier['country'] ?  $supplier['country'] : $_CFG['shop_country'];
	$smarty->assign('country_list',       get_regions());	
	$smarty->assign('province_list', get_regions(1, $supplier_country));
	$smarty->assign('city_list', get_regions(2, $supplier['province']));
	$smarty->assign('district_list', get_regions(3, $supplier['city']));
	$smarty->assign('supplier_country', $supplier_country);
	 /* 供货商等级 */
	 $sql="select rank_id,rank_name from ". $ecs->table('supplier_rank') ." order by sort_order";
	$supplier_rank=$db->getAll($sql);
	$smarty->assign('supplier_rank', $supplier_rank);
	/* 供货商用户名 */
	$sql="select user_id,user_name from ". $ecs->table('users') ." where is_supplier = 1 order by reg_time";
	$supplier_user=$db->getAll($sql);
	$smarty->assign('supplier_user', $supplier_user);


     $smarty->assign('ur_here', $_LANG['edit_supplier']);
	 $lang_supplier_list = $status=='1' ? $_LANG['supplier_list'] :  $_LANG['supplier_reg_list'];
     $smarty->assign('action_link', array('href' => 'supplier.php?act=list&status=1', 'text' =>$_LANG['supplier_list'] ));

     $smarty->assign('form_action', 'insert');
     $smarty->assign('supplier', $supplier);
	 $smarty->assign('card_rule', card_rule());
	 
     assign_query_info();

     $smarty->display('supplier_info.htm');
   

}

/*------------------------------------------------------ */
//-- 提交添加、编辑供货商
/*------------------------------------------------------ */
elseif ($_REQUEST['act']=='remove')
{
    /* 检查权限 */
    admin_priv('supplier_manage');   
   
   /* 提交值 */
   $supplier_id =  intval($_GET['id']);
  
   $sql = "DELETE FROM " . $ecs->table('supplier') . "
            WHERE supplier_id = '$supplier_id'";
   $db->query($sql);


	/* 清除缓存 */
	clear_cache_files();
	/* 提示信息 */
	$links[] = array('href' => ($status_url >0 ? 'supplier.php?act=list&status=1' : 'supplier.php?act=list&status=1'), 'text' => ($status_url >0 ? $_LANG['back_supplier_list'] : $_LANG['back_supplier_reg']));
	sys_msg($_LANG['edit_supplier_ok'], 0, $links);    

}


elseif ($_REQUEST['act']=='update')
{
    /* 检查权限 */
    admin_priv('supplier_manage');   
    
  
 

   /* 提交值 */
   $supplier_id =  intval($_POST['id']);
   $supplier = array(
							'rank_id'   		=> intval($_POST['rank_id']),  
							'supplier_name' 	=> trim($_POST['supplier_name']),						
						    'address'   		=> trim($_POST['address']),
                            'tel'   			=> trim($_POST['tel']),
							'email'   			=> trim($_POST['email']),							
							'supplier_remark'   => trim($_POST['supplier_remark']),
							'status'   			=> intval($_POST['status']),
   							'open_time' 		=> intval($_POST['open_time']),
   							'cost_ratio' 		=> trim($_POST['cost_ratio']),
   							'shop_ratio' 		=> trim($_POST['shop_ratio']),
   							'is_entity'			=> intval($_POST['is_entity']),
   							'is_tickets'		=> intval($_POST['is_tickets']),
   							'show_ordinary'		=> intval($_POST['show_ordinary'])
                           );

  /* 取得供货商信息 */
  $sql = "SELECT * FROM " . $ecs->table('supplier') . " WHERE supplier_id = '" . $supplier_id ."' ";
  $supplier_old = $db->getRow($sql);
  if (empty($supplier_old['supplier_id']))
  {
        sys_msg('该供货商信息不存在！');
  }

/* 保存供货商信息 */
$db->autoExecute($ecs->table('supplier'), $supplier, 'UPDATE', "supplier_id = '" . $supplier_id . "'");

if ($_POST['status']!='1')
{
	$sql="update ". $ecs->table('goods') ." set is_on_sale=0 where supplier_id='$supplier_id' ";
	$db->query($sql);
}

 /* 清除缓存 */
clear_cache_files();

/* 提示信息 */
$links[] = array('href' => ($status_url >0 ? 'supplier.php?act=list&status=1' : 'supplier.php?act=list'), 'text' => ($status_url >0 ? $_LANG['back_supplier_list'] : $_LANG['back_supplier_reg']));
sys_msg($_LANG['edit_supplier_ok'], 0, $links);    

}

elseif ($_REQUEST['act']=='insert')
{
    /* 检查权限 */
    admin_priv('supplier_manage');   
    
  
 	/* 提交值 */

   	$rank_id   = intval($_POST['rank_id']); 
	$user_id   = intval($_POST['user_id']); 
	$supplier_name   = trim($_POST['supplier_name']);						
	$address   = trim($_POST['address']);
    $tel   = trim($_POST['tel']);
	$email   = trim($_POST['email']);	
	$supplier_remark   = trim($_POST['supplier_remark']);
	$status   = intval($_POST['status']);
	// TODO 商城比例、成本比例、销售方式。
	$open_time  = intval($_POST['open_time']);
	$cost_ratio = trim($_POST['cost_ratio']) > 0 ? trim($_POST['cost_ratio']) : 0 ;
	$shop_ratio = trim($_POST['shop_ratio']) > 0 ? trim($_POST['shop_ratio']) : 0 ;	
	$is_entity  = intval($_POST['is_entity']);
	$is_tickets  = intval($_POST['is_tickets']);
	$show_ordinary  = intval($_POST['show_ordinary']);
	
	  /* 取得供货商信息 */
	$sql = "SELECT * FROM " . $ecs->table('supplier') . " WHERE user_id = '" . $user_id ."' ";
	$supplier_old = $db->getRow($sql);
	if ($supplier_old['supplier_id'])
	{
		sys_msg('该供货商用户名已经存在！');
		exit;
	 }

	$sql = "INSERT INTO " .$ecs->table('supplier'). " (user_id, rank_id, supplier_name, address, tel, email, supplier_remark, status, open_time, cost_ratio, shop_ratio,is_entity,is_tickets,show_ordinary)".
			"VALUES ('$user_id', '$rank_id', '$supplier_name', '$address', '$tel', '$email', '$supplier_remark', '$status', '$open_time', '$cost_ratio', '$shop_ratio', '$is_entity', '$is_tickets', '$show_ordinary')";
	
	$db->query($sql);


	/* 清除缓存 */
	clear_cache_files();

	/* 提示信息 */
	$links[] = array('href' => ($status_url >0 ? 'supplier.php?act=list&status=1' : 'supplier.php?act=list'), 'text' => ($status_url >0 ? $_LANG['back_supplier_list'] : $_LANG['back_supplier_reg']));
	sys_msg($_LANG['edit_supplier_ok'], 0, $links);  
		
}
// 规格折扣设置
elseif($_REQUEST['act']=='spec_ratio'){

	$updateDiscount = array();
	foreach( (array)$_POST['discount'] as $spicId=>$discount)
	{
		if ($discount > 0)
		{
			$updateDiscount[$spicId] = $discount;
		}
	}
	if (!empty($updateDiscount))
	{
		foreach($updateDiscount as $sid=>$dis)
		{
			$GLOBALS['db']->query("UPDATE ".$GLOBALS['ecs']->table('goods_spec')." SET discount=".$dis." WHERE id =".$sid);	
		}
	}
	$links[] = array('href' => 'supplier.php?act=edit&id='.$_POST['id'], 'text'=>'返回继续设置');
	sys_msg('设置完成！', 0, $links);
	
}
// 客户显示设置
elseif($_REQUEST['act']=='spec_show')
{
	if (empty($_POST['rule_id']) || empty($_POST['spec_id']))
	{
		$links[] = array('href' => 'supplier.php?act=edit&id='.$_POST['id'], 'text'=>'返回继续设置');
		sys_msg('缺少设置项，请从新设置！', 0, $links);
	}	
	
	foreach($_POST['rule_id'] as $rid)
	{
		foreach ($_POST['spec_id'] as $goods_id=>$spec_ids)
		{	
			foreach($spec_ids as $sid)
			{
				// 如果存在了，跳出本次循环
				$res = $GLOBALS['db']->query("SELECT id FROM ".$GLOBALS['ecs']->table('card_out')." WHERE spec_id=".$sid." AND card_id=".$rid);
				//exit($GLOBALS['db']->num_rows($res));
				if ($GLOBALS['db']->num_rows($res) > 0 )
				{
					continue;
				}
					
				$GLOBALS['db']->query("INSERT INTO ".$GLOBALS['ecs']->table('card_out'). " (spec_id, card_id, supplier_id,goods_id) VALUES('".$sid."', '".$rid."', '".$_POST['id']."', '".$goods_id."')");
			}
			// 如果一个商品的所有规格都被这个卡规则排除了，那这个商品对卡规则里面的会员就不显示了。
			if(check_out_spec_num($goods_id,$rid) == true)
			{
				$rule_ids = $GLOBALS['db']->getOne("SELECT rule_ids FROM ".$GLOBALS['ecs']->table('goods')." WHERE goods_id=".$goods_id);
				if (empty($rule_ids))
				{
					$new_rule_ids = ','.$rid.',';
				}
				else
				{
					$new_rule_ids = $rule_ids.$rid.',';
				}
				$GLOBALS['db']->query('UPDATE '.$GLOBALS['ecs']->table('goods').' SET rule_ids = "'.$new_rule_ids.'" WHERE goods_id='.$goods_id);
			
			}
		}	
	}
	
	$links[] = array('href' => 'supplier.php?act=edit&id='.$_POST['id'], 'text'=>'返回继续设置');
	sys_msg('设置完成！', 0, $links);

}
// 商品搜索
elseif($_REQUEST['act']=='search_goods'){

	$supplier_id   = empty($_REQUEST['supplier_id']) ? 0 : intval($_REQUEST['supplier_id']);
	$type = empty($_REQUEST['type']) ? 0 : intval($_REQUEST['type']);
	$goods_name = empty($_REQUEST['goods_name']) ? '' : addslashes_deep($_REQUEST['goods_name']);
	
	$where = ' g.supplier_id = '.$supplier_id;
	// 处理逗号
	if(strpos($goods_name, ',') !==false)
	{
		$tmp_goods_name = explode(',', $goods_name);
		$new_goods_name = array_filter($tmp_goods_name);		
		$where .= " AND s.spec_nember IN('".implode('\',\'',$new_goods_name)."')";
		
		$sql = "SELECT g.goods_name,g.goods_id as gid , s.* FROM ".$ecs->table('goods_spec')." AS s" .
				" LEFT JOIN ".$ecs->table('goods')." AS g ON g.goods_id = s.goods_id" .
				" WHERE ".$where;
		
	}else {

		$where .= " AND g.goods_name LIKE '%".$goods_name."%'";
		$sql = "SELECT g.goods_name,g.goods_id as gid , s.* FROM ".$ecs->table('goods')." AS g" .
				" LEFT JOIN ".$ecs->table('goods_spec')." AS s ON g.goods_id = s.goods_id" .
				" WHERE ".$where;
	}
	$data = $db->getAll($sql);
	// 同一个商品的规格放在一起
	$new_info = array();
	foreach((array)$data as $info)
	{
		$new_info[$info['gid']]['goods_name'] = $info['goods_name'];
		$new_info[$info['gid']]['spec'][] = $info;	
	}
	
	$html = '';
	
	// 模板输出
	if ($type == 1 || $type == 4)
	{	
		foreach($new_info as $ni)
		{
			if ($ni['spec'][0]['id'] == NULL)
			{
				continue;
			}
			
			$html .= '<table class="table-row" width="100%"><tbody>';
			foreach($ni['spec'] as $ni2)
			{
				
				
				$html .= '<tr><td width="60%">'.$ni['goods_name'].'，[规格：'.$ni2['spec_name'].'，价格：'.$ni2['spec_price'].'，编号：'.$ni2['spec_nember'].']</td>';
				if($type==4){
					$html .= '<td width="40%">折扣比例：<input type="text" name="discount['.$ni2['id'].']" size="10" value="0"> &nbsp;&nbsp;&nbsp;<font color=red>('.$ni2['discount'].')</font></td></tr>';
				}else{
					$html .= '<td width="40%">折扣比例：<input type="text" name="discount['.$ni2['id'].']" size="10" value="'.$ni2['discount'].'"></td></tr>';
				}
			}		
			$html .= '</tbody></table>';
		}		
	}
	else if($type == 2)
	{
		foreach($new_info as $ni)
		{
			if ($ni['spec'][0]['id'] == NULL)
			{
				continue;
			}
			$html .='<div class="goods_list">';
			$html .='<span>'.$ni['goods_name'].'</span>';
			$html .='<ul>';
			foreach($ni['spec'] as $ni2)
			{
				$html .='<li><input type="checkbox" name="spec_id['.$ni2['goods_id'].'][]" value="'.$ni2['id'].'" id="s'.$ni2['id'].'"><label for="s'.$ni2['id'].'">规格：'.$ni2['spec_name'].'，价格：'.$ni2['spec_price'].'，编号：'.$ni2['spec_nember'].'</label></li>';
			}			
			$html .='</ul>';			
			$html .='</div>';
		}
	}
	else if ($type == 3)
	{
		$i = 1;
		foreach($new_info as $ni)
		{
			if ($ni['spec'][0]['id'] == NULL)
			{
				continue;
			}
			
			$html .="<table class='table-row' ><tr><th style='padding-bottom:0;'>".$ni['goods_name']."</th></tr>";
			foreach($ni['spec'] as $ni2)
			{
				$html .= '<tr bgcolor="">';
				$html .= '<td><input type="checkbox" name="spec_id['.$ni2['goods_id'].'][]" value="'.$ni2['id'].'" id="s'.$ni2['id'].'" ><label for="s'.$ni2['id'].'">&nbsp;规格：'.$ni2['spec_name'].'，&nbsp;&nbsp;&nbsp;&nbsp;编号：'.$ni2['spec_nember'].'，&nbsp;&nbsp;&nbsp;&nbsp;价格：'.$ni2['spec_price'].'</label></td>';
				$html .= '</tr>';
			}
			$html .="</table>";
			$i++;			
		}
	}	
	
	if (empty($html))
	{
		$html = '<table class="table-row" width="100%"><tbody><tr><td align="center" colspan="2"><font color=red>没有你想要的数据</font></td></tr></tbody></table>';
	}
	
	//error_log(var_export($new_info,true),'3','error.log');
	make_json_result($html);
}

/**
 *  检查卡规则是否存在该商品里，存在返回false， 不存在返回商品id
 * 	$sid	规格id
 *  $rid	卡规则id
 */  

function check_rule_ids($sid,$rid)
{
	$return = array('goods_id'=>0, 'rule_ids'=>'');
	if (empty($sid) || empty($rid))
	{
		return false;
	}

	// 商品信息
	$sql = "SELECT gs.*,g.rule_ids FROM ".$GLOBALS['ecs']->table('goods_spec')." AS gs ".
		   "LEFT JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON g.goods_id = gs.goods_id ".
		   "WHERE gs.id = ".$sid." GROUP BY gs.goods_id";	
	$goods_info = $GLOBALS['db']->getAll($sql);
	// 商品信息为空，返回空数据
	if (empty($goods_info))
	{
		return $return;
	}
	$goods_row = current($goods_info);
	$return['goods_id'] = $goods_row['goods_id'];
	if (!empty($goods_row['rule_ids']))
	{
		if (!in_array($rid,explode(',',$goods_row['rule_ids'])))
		{
			$return['rule_ids'] = $goods_row['rule_ids'].','.$rid;
		}		
	}
	else
	{
		$return['rule_ids'] = $rid;
	}
	
	return $return;
}
/**
 *  获取供应商列表信息
 *
 * @access  public
 * @param
 *
 * @return void
 */
function suppliers_list($status = 0)
{
    $result = get_filter();
    if ($result === false)
    {
        $aiax = isset($_GET['is_ajax']) ? $_GET['is_ajax'] : 0;

        /* 过滤信息 */
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'supplier_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'ASC' : trim($_REQUEST['sort_order']);
		$filter['status'] = empty($_REQUEST['status']) ? '0' : intval($_REQUEST['status']);
		if($status !=0){
			$filter['status'] = $status;
		}
        $where = 'WHERE 1 ';
		$where .= $filter['status'] ? " AND status = '". $filter['status']. "' " : " AND status in('0','-1') ";

        /* 分页大小 */
        $filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page']) <= 0) ? 1 : intval($_REQUEST['page']);

        if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0)
        {
            $filter['page_size'] = intval($_REQUEST['page_size']);
        }
        elseif (isset($_COOKIE['ECSCP']['page_size']) && intval($_COOKIE['ECSCP']['page_size']) > 0)
        {
            $filter['page_size'] = intval($_COOKIE['ECSCP']['page_size']);
        }
        else
        {
            $filter['page_size'] = 15;
        }

        /* 记录总数 */
        $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('supplier') . $where;
        $filter['record_count']   = $GLOBALS['db']->getOne($sql);
        $filter['page_count']     = $filter['record_count'] > 0 ? ceil($filter['record_count'] / $filter['page_size']) : 1;

        /* 查询 */
        $sql = "SELECT supplier_id,u.user_name, rank_id, supplier_name, tel, system_fee, supplier_bond, supplier_rebate, supplier_remark, cost_ratio, shop_ratio, is_entity, is_tickets, show_ordinary ".
			    "status ".
                "FROM " . $GLOBALS['ecs']->table("supplier") . " as s left join " . $GLOBALS['ecs']->table("users") . " as u on s.user_id = u.user_id 
                $where
                ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']. "
                LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'] . " ";

        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    
	$rankname_list =array();
	$sql2 = "select * from ". $GLOBALS['ecs']->table("supplier_rank") ;
	$res2 = $GLOBALS['db']->query($sql2);
	while ($row2=$GLOBALS['db']->fetchRow($res2))
	{
		$rankname_list[$row2['rank_id']] = $row2['rank_name'];
	}

	$list=array();
	$res = $GLOBALS['db']->query($sql);
    while ($row = $GLOBALS['db']->fetchRow($res))
	{
		$row['rank_name'] = $rankname_list[$row['rank_id']];
		$row['status_name'] = $row['status']=='1' ? '通过' : ($row['status']=='0' ? "未审核" : "未通过");
		$list[]=$row;
	}

    $arr = array('result' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

// 卡规则列表
function card_rule(){	
	return $GLOBALS['db']->getAll("SELECT * FROM ".$GLOBALS['ecs']->table('card_rule'));
}
?>