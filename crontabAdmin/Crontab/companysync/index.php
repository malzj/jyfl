<?php
/**
 * 公司数据更新
 * @var unknown_type
 */
define('IN_ECS', true);

include_once(dirname(__FILE__) . '/lib/_init.php');

//$lastTime = $GLOBALS['db']->getOne('SELECT create_time FROM '.$GLOBALS['ecs']->table('company').' ORDER BY create_time DESC');
//$dataInfo['Info']['Time'] = $lastTime;
//$dataInfo['Info']['Time'] = '2016-09-10 18:54:27';
//if(!empty($lastTime)){
    $dataInfo['Info']['Time'] = '2012-1-1 00:00';
//}
include_once (ROOT_PATH.'includes/lib_huayingcard.php');
include_once (ROOT_PATH.'includes/httpRequest.php');

$Card = new huayingcard();
$no = $Card -> action($dataInfo,11);
$result = $Card -> getResult();
$dataList = $Card -> getDataList();
$time = strtotime($result['Time']);
$time = date('Y-m-d H:i:s',$time);
$company_logo = 'Public/company/upload/logo.png';
$company_bg = 'Public/company/upload/pc_background.jpg';
$mobile_bg = 'Public/company/upload/m_background.jpg';
$sqlval = '';
if($no == 0){
    //查询本地所有公司卡系统ID
    $companys = $db -> getAll("SELECT card_company_id FROM ".$ecs -> table('company'));
    $company_ids = array();
    foreach ($companys as $com){
        $company_ids[] = $com['card_company_id'];
    }
    $companyList = array();
    foreach($dataList['Info'] as $key=>$val){
        if(in_array($val['CustomerID'],$company_ids)) continue;
        if(empty($sqlval)){
            $sqlval = "('".$val['CustomerID']."','".$val['CompanyName']."',2,'".$company_logo."','".$company_bg."','".$mobile_bg."','".$time."')";
        }else{
            $sqlval .= ",('".$val['CustomerID']."','".$val['CompanyName']."',2,'".$company_logo."','".$company_bg."','".$mobile_bg."','".$time."')";
        }
    }
    if(!empty($sqlval)) {
        $sql = "INSERT INTO " . $GLOBALS['ecs']->table('company') . "(`card_company_id`,`company_name`,`grade_id`,`logo_img`,`back_img`,`m_back_img`,`create_time`) values" . $sqlval;
        $res = $GLOBALS['db']->query($sql);
    }
}