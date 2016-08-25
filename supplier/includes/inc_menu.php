<?php

/**
 * ECSHOP 管理中心菜单数组
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: inc_menu.php 17217 2011-01-19 06:29:08Z liubo $
*/

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$modules['02_cat_and_goods']['01_goods_list_pass1']       = 'goods.php?act=list&supplier_status=1';         // 商品列表
$modules['02_cat_and_goods']['01_goods_list_pass2']       = 'goods.php?act=list&supplier_status=0';         // 商品列表
$modules['02_cat_and_goods']['01_goods_list_pass3']       = 'goods.php?act=list&supplier_status=-1';         // 商品列表
//$modules['02_cat_and_goods']['01_goods_list']       = 'goods.php?act=list';         // 商品列表

//$modules['02_cat_and_goods']['01_goods_list']       = 'goods.php?act=list';         // 商品列表

$modules['02_cat_and_goods']['02_goods_add']        = 'goods.php?act=add';          // 添加商品

$modules['02_cat_and_goods']['05_comment_manage']   = 'comment_manage.php?act=list'; //评论

$modules['02_cat_and_goods']['05_shaidan_manage']   = 'shaidan.php?act=list';
$modules['02_cat_and_goods']['11_goods_trash']      = 'goods.php?act=trash';        // 商品回收站

//$modules['02_cat_and_goods']['04_category_list']    = 'category.php?act=list';

//$modules['02_cat_and_goods']['13_batch_add']        = 'goods_batch.php?act=add';    // 商品批量上传
//$modules['02_cat_and_goods']['14_goods_export']     = 'goods_export.php?act=goods_export';
//$modules['02_cat_and_goods']['15_batch_edit']       = 'goods_batch.php?act=select'; // 商品批量修改

$modules['02_cat_and_goods']['50_virtual_card_list']   = 'goods.php?act=list&extension_code=virtual_card';
$modules['02_cat_and_goods']['51_virtual_card_add']    = 'goods.php?act=add&extension_code=virtual_card';
//$modules['02_cat_and_goods']['52_virtual_card_change'] = 'virtual_card.php?act=change';
$modules['02_cat_and_goods']['53_code_import'] = 'import.php?act=code_import';
$modules['02_cat_and_goods']['54_code_list'] = 'code.php?act=code_list';
//$modules['02_cat_and_goods']['goods_auto']             = 'goods_auto.php?act=list';

//$modules['02_rebate_manage']['03_rebate_nopay']       = 'supplier_rebate.php?act=list&is_pay_ok=0'; 
//$modules['02_rebate_manage']['03_rebate_pay']       = 'supplier_rebate.php?act=list&is_pay_ok=1';

$modules['04_order']['13_entity_goods_order']       = 'entity.php?act=list';
$modules['04_order']['01_order_list']               = 'order.php?act=list';
$modules['04_order']['03_order_query']              = 'order.php?act=order_query';
$modules['04_order']['04_merge_order']              = 'order.php?act=merge';
$modules['04_order']['05_edit_order_print']         = 'order.php?act=templates';
//$modules['04_order']['06_undispose_booking']        = 'goods_booking.php?act=list_all';
//$modules['04_order']['07_repay_application']        = 'repay.php?act=list_all';
//$modules['04_order']['08_add_order']                = 'order.php?act=add';
$modules['04_order']['09_delivery_order']           = 'order.php?act=delivery_list';
//$modules['04_order']['10_back_order']               = 'order.php?act=back_list';
//$modules['04_order']['10_back_order']               = 'back.php?act=back_list';  //代码修改 By www.68ecshop.com

$modules['04_order']['12_order_excel']              = 'excel.php?act=order_excel';

//$modules['05_dianpu_manage']['01_base']               	= 	'shop_config.php?act=list_edit';
//$modules['05_dianpu_manage']['02_menu']               	= 	'navigator.php?act=list';
//$modules['05_dianpu_manage']['03_guanggao']             = 	'flashplay.php?act=list';
//$modules['05_dianpu_manage']['04_article']              = 	'article.php?act=list';
//$modules['05_dianpu_manage']['05_header']               = 	'shop_header.php?act=list_edit';
//$modules['05_dianpu_manage']['06_templates']            = 	'template.php?act=list';


$_LANG['01_order_list'] = '订单列表';
$_LANG['01_goods_list_pass1'] = '审核通过商品';
$_LANG['01_goods_list_pass2'] = '未审核商品';
$_LANG['01_goods_list_pass3'] = '审核未通过商品';



$_LANG['02_rebate_manage'] = '佣金管理';
$_LANG['03_rebate_nopay'] = '未处理佣金列表';
$_LANG['03_rebate_pay'] = '已完结佣金列表';


$_LANG['05_dianpu_manage'] 		= '店铺系统设置';
$_LANG['01_base'] 				= '店铺基本设置';
$_LANG['02_menu'] 				= '店铺导航栏';
$_LANG['03_guanggao'] 			= '店铺主广告';
$_LANG['04_article'] 			= '店铺文章';
$_LANG['05_header'] 			= '店铺头部自定义';
$_LANG['06_templates'] 			= '店铺模板选择';

$_LANG['53_code_import']        = '导入商品码';
$_LANG['54_code_list']        = '商品码管理';
?>
