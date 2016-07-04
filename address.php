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


// 用户收货信息列表
if ($_REQUEST['act'] == 'AjaxAddressList')
{
    $ajaxArray = array( 'error'=>0, 'content'=>'' );
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
            
    $smarty->assign('consignee_list',      $consignee_list);  
    
    // 下单页面的列表
    if(strpos($_SERVER['HTTP_REFERER'], 'newAddress') !== false)
    {
        $ajaxArray['content'] = $smarty->fetch('address/addressListSelect.dwt');        
    }
    else
    { 
        $ajaxArray['content'] = $smarty->fetch('address/addressList.dwt');
    }
    exit(json_encode($ajaxArray));
}

// 添加 、编辑 收货地址 
elseif ($_REQUEST['act'] == 'AjaxAddressEdit')
{
    $ajaxArray = array( 'error'=>0, 'content'=>'' );
    $address_id = $_REQUEST['address_id'] ? $_REQUEST['address_id'] : 0;
    /* 获得用户所有的收货人信息 */
    $consignee_list = get_consignee_list($_SESSION['user_id']);
    if($consignee_list){
        $consignees = $consignee_list[$address_id];
        $smarty->assign('province_list',       get_regions(1, $consignees['country']));
        $smarty->assign('city_list',           get_regions(2, $consignees['province']));
        $smarty->assign('district_list',       get_regions(3, $consignees['city']));
        $smarty->assign('consignee_list', $consignee_list);
    }
    $consigness['country'] = $int_cityId;
    if(empty($address_id)){
        $smarty->assign('province_list',    get_regions(1, $consigness['country']));
    }
    
    $smarty->assign('consignees', $consignees);
    $smarty->assign('address_id', $address_id);
    
    $smarty->assign('shop_country',       $int_cityId);
 
    // 下单页面的列表
    if(strpos($_SERVER['HTTP_REFERER'], 'newAddress') !== false)
        $ajaxArray['content'] = $smarty->fetch('address/addressEditSelect.dwt');
    else
        $ajaxArray['content'] = $smarty->fetch('address/addressEdit.dwt');
    
    exit(json_encode($ajaxArray));
    
}
// 保存收货地址
elseif ($_REQUEST['act'] == 'ajaxAddressSave')
{
    $ajaxArray = array( 'error'=>0, 'content'=>'' );
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
        'email'         => empty($_REQUEST['email'])     ? '' : trim($_REQUEST['email'])
    );
    if (!empty($_SESSION['user_id'])){
        $address_id = update_address($consignee);
        // 如果不是 newAddress 过来的操作，就设置当前地址为默认地址。
        if(strpos($_SERVER['HTTP_REFERER'], 'newAddress') === false) 
        {
            $db->query("UPDATE " . $GLOBALS['ecs']->table('users') ." SET address_id = '$address_id' WHERE user_id = '$_SESSION[user_id]'");
            // 删除 session 中保存的默认收货地址，下单页面从新获取
            $_SESSION['flow_consignee'] = array();
        }
    }else{
        $ajaxArray['error'] = 1;
        $ajaxArray['content'] = '您未登录，或登录超时，请从新登录后操作！';
    }
    
    exit(json_encode($ajaxArray));
}

// 设置默认的收货地址
elseif ( $_REQUEST['act'] == 'AjaxAddressDefault')
{
    $ajaxArray = array( 'error'=>0, 'content'=>'' );
    $address_id = $_REQUEST['address_id'] ? $_REQUEST['address_id'] : 0;
    
    // 用户是登录状态，更新用户的默认收货地址
    if (!empty($_SESSION['user_id']) && !empty($address_id)){        
        $db->query("UPDATE " . $GLOBALS['ecs']->table('users') ." SET address_id = '$address_id' WHERE user_id = '$_SESSION[user_id]'");
        // 删除 session 中保存的默认收货地址，下单页面从新获取
        $_SESSION['flow_consignee'] = array();
    }else{
        $ajaxArray['error'] = 1;
        $ajaxArray['content'] = '您未登录，或登录超时，请从新登录后操作！';
    }
    exit(json_encode($ajaxArray));
}

// 设置选中的收货地址，并保存到session中
elseif ( $_REQUEST['act'] == 'AjaxAddressSelect')
{
    $ajaxArray = array( 'error'=>0, 'content'=>'' );
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
        $ajaxArray['error'] = 1;
        $ajaxArray['content'] = '您未登录，或登录超时，请从新登录后操作！';
    }
    exit(json_encode($ajaxArray));
}

// 删除一条收货地址
elseif($_REQUEST['act'] == 'AjaxAddressDorp')
{
    $ajaxArray = array( 'error'=>0, 'content'=>'' );
    $address_id = $_REQUEST['address_id']?$_REQUEST['address_id']:0;
    $result = array();
    $result['id'] = $address_id;
    if($address_id){
        $ajaxArray['error'] = drop_consignee($address_id);
    }
    exit(json_encode($ajaxArray));
}
else
{
    ecs_header('location:user.php');
}


?>