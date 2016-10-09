<?php

/**
 * ECSHOP 商品详情
 * ============================================================================
 * * 版权所有 2005-2012 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: goods.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ((DEBUG_MODE & 2) != 2)
{
    $smarty->caching = true;
}

$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

/*------------------------------------------------------ */
//-- INPUT
/*------------------------------------------------------ */

$goods_id = isset($_REQUEST['id'])  ? intval($_REQUEST['id']) : 0;

/*------------------------------------------------------ */
//-- 改变属性、数量时重新计算商品价格
/*------------------------------------------------------ */

if (!empty($_REQUEST['act']) && $_REQUEST['act'] == 'price')
{

    $attr_id    = isset($_REQUEST['attr']) && is_array($_REQUEST['attr']) ? $_REQUEST['attr'] : (!empty($_REQUEST['attr']) ? explode(',', $_REQUEST['attr']) : array());
    $number     = (isset($_REQUEST['number'])) ? intval($_REQUEST['number']) : 1;

    if ($goods_id == 0)
    {
        $jsonArray['message'] = $_LANG['err_change_attr'];
        JsonpEncode($jsonArray);
    }
    else
    {
        if ($number == 0)
        {
            $number = 1;
        }        
        
        // 计算规格价格 TODO guoyunpeng 2015-08-04
        $spec_nember = '';
		foreach ($attr_id as $attr_k=>$attr_v)
		{
			if (strpos($attr_v, 'S_') !==false)
			{
				unset($attr_id[$attr_k]);
				$spec_nember = substr($attr_v, 2);
			}
		}
		$spec_array = array('spec_nember'=> $spec_nember, 'goods_id'=>$goods_id);
		$spec_price = get_spec_ratio_price($spec_array);		
        $shop_price  = get_final_price($goods_id, $number, true, $attr_id);
        $new_price = $spec_price+$shop_price;
        // ====================================================== 
        $jsonArray['data']['shopPrice'] = price_format($new_price * $number);
    }

    JsonpEncode($jsonArray);
}

/*------------------------------------------------------ */
//-- PROCESSOR
/*------------------------------------------------------ */


/* 获得商品的信息 */
$goods = get_goods_info($goods_id);       

if ($goods === false)
{
    $jsonArray['state'] = 'false';
    $jsonArray['message'] = '没有找到任何记录';
    JsonpEncode($jsonArray);
}
else
{

    $goods['goods_style_name'] = add_style($goods['goods_name'], $goods['goods_name_style']);

    $catlist = array();
    foreach(get_parent_cats($goods['cat_id']) as $k=>$v)
    {
        $catlist[] = $v['cat_id'];
    }

    assign_template('c', $catlist);

    // 图片地址处理
    $goods['goods_thumb'] = getImagePath($goods['goods_thumb']);
    $goods['goods_img'] = getImagePath($goods['goods_img']);
    $goods['original_img'] = getImagePath($goods['original_img']);
    
    $newGoodsDesc = preg_replace_callback(
                                        "|src=['\"]?(.*?)['\"]|", 
                                        function($matches){
                                            return 'src="'.getImagePath($matches[1]).'"';
                                        }, 
                                        $goods['goods_desc']);
    $goods['goods_desc'] = $newGoodsDesc;
    $properties = get_goods_properties($goods_id);  // 获得商品的规格和属性
    
    assign_dynamic('goods');

}

/* 更新点击次数 */
$db->query('UPDATE ' . $ecs->table('goods') . " SET click_count = click_count + 1 WHERE goods_id = '$_REQUEST[id]'");

$gallery = get_goods_gallery($goods_id); //商品相册
foreach ($gallery as $key=>&$val)
{
    $val['img_url'] = getImagePath($val['img_url']);
    $val['thumb_url'] = getImagePath($val['thumb_url']);
}

/* 规格整理 */
if ( !empty($properties['spe']))
{
    foreach ($properties['spe'] as $pkey=>&$pval)
    {
        $pval['attr_id'] = $pkey;
    }
}
//error_log(var_export($properties['spe'],true),'3','') 
$jsonArray['data']['goods'] = $goods;                           // 商品信息
$jsonArray['data']['pictures'] = $gallery;                      // 商品相册
$jsonArray['data']['specs'] = get_show_specs($goods_id);        // 商品规格
$jsonArray['data']['specification'] = $properties['spe'];       // 商品规格（其他）
$jsonArray['data']['properties'] = $properties['pro'];          // 商品属性

JsonpEncode($jsonArray);



/*------------------------------------------------------ */
//-- PRIVATE FUNCTION
/*------------------------------------------------------ */


/**
 * 获得商品选定的属性的附加总价格
 *
 * @param   integer     $goods_id
 * @param   array       $attr
 *
 * @return  void
 */
function get_attr_amount($goods_id, $attr)
{
    $sql = "SELECT SUM(attr_price) FROM " . $GLOBALS['ecs']->table('goods_attr') .
        " WHERE goods_id='$goods_id' AND " . db_create_in($attr, 'goods_attr_id');

    return $GLOBALS['db']->getOne($sql);
}


function get_img_url($img_id,$id){
	if($img_id){
		$sql = "SELECT thumb_url,img_url,img_original FROM " .$GLOBALS['ecs']->table('goods_gallery'). " WHERE img_id = " . $img_id;
		$url = $GLOBALS['db']->getRow($sql);
	}else{
		$sql = "SELECT thumb_url,img_url,img_original FROM " .$GLOBALS['ecs']->table('goods_gallery'). " WHERE goods_id = " . $id ." order by img_id limit 0,1";
		$url = $GLOBALS['db']->getRow($sql);
	}
	return $url;
}

/**
 * 获得分类的信息
 *
 * @param   integer $cat_id
 *
 * @return  void
 */
function get_cat_info($cat_id)
{
    return $GLOBALS['db']->getRow('SELECT cat_name,measure_unit, keywords, cat_desc, style, grade, filter_attr, parent_id FROM ' . $GLOBALS['ecs']->table('category') .
        " WHERE cat_id = '$cat_id'");
}

?>