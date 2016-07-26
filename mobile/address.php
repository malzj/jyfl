<?php
/**
 *  试听盛宴 ----》 演出
 *  
 */
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_order.php');
include_once(ROOT_PATH . 'includes/lib_transaction.php');

if (!isset($_REQUEST['act']))
{
    $_REQUEST['act'] = "empty";
}

// 返回的数据
$jsonArray = array(
    'state'=>'true',
    'data'=>'',
    'message'=>''
);

// 用户收货信息列表
if ($_REQUEST['act'] == 'AjaxAddressList')
{
    // 所有收货地址
    $consignee_list = get_consignee_list($_SESSION['user_id']);
    // 默认收货地址
    $defaultAddress = get_consignee($_SESSION['user_id']);
    // 地区编号转换为城市名称
    if (!empty($consignee_list))
    {
        $sortKey = array();
        foreach ($consignee_list as $key=>&$consignee)
        {
            // 设置默认选中的地址
            if ($defaultAddress['address_id'] == $consignee['address_id'])
                $consignee['selected'] = 1;
            else 
                $consignee['selected'] = 0;
            
            $sortKey[$key]['selected'] = $consignee['selected'];
        }
        
        // 排序，把默认地址放到第一个
        //array_multisort($sortKey,SORT_DESC,$consignee_list);
    }   
    
    $jsonArray['data'] = $consignee_list;
    JsonpEncode($jsonArray);
}
// 添加收货地址
elseif ($_REQUEST['act'] == 'AjaxAddress')
{
    $country = getIpCity($int_cityId);
    $jsonArray['data']['country'] = array(array('region_id'=>$int_cityId, 'region_name'=>$country['region_name']));
    $jsonArray['data']['province'] = get_regions(1, $int_cityId);
    JsonpEncode($jsonArray);
}
// 添加 、编辑 收货地址 
elseif ($_REQUEST['act'] == 'AjaxEditress')
{
    $address_id = $_REQUEST['address_id'] ? $_REQUEST['address_id'] : 0;
    /* 获得用户所有的收货人信息 */
    $consignee_list = get_consignee_list($_SESSION['user_id']);
    if($consignee_list){
        $consignees = $consignee_list[$address_id];
        $jsonArray['data']['consignee'] = $consignees;
        JsonpEncode($jsonArray);
    }

    $jsonArray['state'] = 'false';
    $jsonArray['message'] = '收货地址为空';
    JsonpEncode($jsonArray);    
}
// 保存收货地址
elseif ($_REQUEST['act'] == 'ajaxAddressSave')
{
    $address_id = $_REQUEST['address_id'] ? $_REQUEST['address_id'] : 0;  

    /* 保存为用户的默认收货地址 */
    if (!empty($address_id)){
        $address_id = (int)$db->getOne("SELECT address_id FROM ".$ecs->table('user_address')." WHERE address_id = '$address_id' AND user_id = '".$_SESSION['user_id']."'");
    }
    $consignee = array(
        'address_id'    => $address_id,
        'user_id'       => $_SESSION['user_id'],
        'consignee'     => empty($_REQUEST['consignee'])  ? '' : trim($_REQUEST['consignee']),
        'country'       => empty($_REQUEST['country'])    ? '' : $_REQUEST['country'],
        'province'      => empty($_REQUEST['province'])   ? '' : $_REQUEST['province'],
        'city'          => empty($_REQUEST['city'])       ? '' : $_REQUEST['city'],
        'district'      => empty($_REQUEST['district'])   ? '' : $_REQUEST['district'],
        'address'       => empty($_REQUEST['address'])    ? '' : $_REQUEST['address'],
        'tel'           => empty($_REQUEST['tel'])        ? '' : make_semiangle(trim($_REQUEST['tel'])),
        'mobile'        => empty($_REQUEST['mobile'])     ? '' : make_semiangle(trim($_REQUEST['mobile'])),
        'email'         => empty($_REQUEST['email'])     ? '' : trim($_REQUEST['email']),
        'zipcode'       => empty($_REQUEST['zipcode'])     ? '' : trim($_REQUEST['zipcode'])
    );
    if (!empty($_SESSION['user_id'])){
        $address_id = update_address($consignee);        
    }else{
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '您未登录，或登录超时，请从新登录后操作';
        JsonpEncode($jsonArray);
    }
    
    JsonpEncode($jsonArray);
}

// 设置默认的收货地址
elseif ( $_REQUEST['act'] == 'AjaxAddressDefault')
{
    $address_id = $_REQUEST['address_id'] ? $_REQUEST['address_id'] : 0;
    
    // 用户是登录状态，更新用户的默认收货地址
    if (!empty($_SESSION['user_id']) && !empty($address_id)){        
        $db->query("UPDATE " . $GLOBALS['ecs']->table('users') ." SET address_id = '$address_id' WHERE user_id = '$_SESSION[user_id]'");
        // 删除 session 中保存的默认收货地址，下单页面从新获取
        $_SESSION['flow_consignee'] = array();
        $jsonArray['message']='设置默认地址成功！';
    }else{
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '您未登录，或登录超时，请从新登录后操作';
        JsonpEncode($jsonArray);
    }
    JsonpEncode($jsonArray);
}

// 设置选中的收货地址，并保存到session中
elseif ( $_REQUEST['act'] == 'AjaxAddressSelect')
{
    $address_id = $_REQUEST['address_id'] ? $_REQUEST['address_id'] : 0;
    $supplier_id = $_REQUEST['sid'] ? $_REQUEST['sid'] : 0;
    
    // 用户是登录状态，更新用户的默认收货地址
    if (!empty($_SESSION['user_id']) && !empty($address_id)){        
        $consignee_list = get_consignee_list($_SESSION['user_id']);
        if($consignee_list){
            $consignee = $consignee_list[$address_id];
            $consignee['country_cn']  = get_add_cn($consignee['country']);
            $consignee['province_cn'] = get_add_cn($consignee['province']);
            $consignee['city_cn']     = get_add_cn($consignee['city']);
            $consignee['district_cn'] = get_add_cn($consignee['district']);
        }        
        if (!empty($consignee))
        {
            $_SESSION['supplier'][$supplier_id] = $consignee;
        }        
    }else{
        $jsonArray['state'] = 'false';
        $jsonArray['message'] = '您未登录，或登录超时，请从新登录后操作';
        JsonpEncode($jsonArray);
    }
    
    JsonpEncode($jsonArray);
}

// 删除一条收货地址
elseif($_REQUEST['act'] == 'AjaxAddressDorp')
{
    $address_id = $_REQUEST['address_id']?$_REQUEST['address_id']:0;
    $result = array();
    $result['id'] = $address_id;
    if($address_id){
        if( false == drop_consignee($address_id))
        {
            $jsonArray['state'] = 'false';
            $jsonArray['message'] = '删除失败';
        }
    }
    JsonpEncode($jsonArray);
}
else
{
    $jsonArray['state'] = 'false';
    $jsonArray['message'] = '无效的操作';
    JsonpEncode($jsonArray);
}


?>