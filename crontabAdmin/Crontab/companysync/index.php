<?php
/**
 * 公司数据更新
 * @var unknown_type
 */
define('IN_ECS', true);

include_once(dirname(__FILE__) . '/lib/_init.php');

$lastTime = $GLOBALS['db']->getOne('SELECT create_time FROM '.$GLOBALS['ecs']->table('company').' ORDER BY create_time DESC');
//$dataInfo['Info']['Time'] = $lastTime;
$dataInfo['Info']['Time'] = '2012-1-1 00:00';
if(!strtotime($lastTime)){
    $dataInfo['Info']['Time'] = '2012-1-1 00:00';
}
include_once (ROOT_PATH.'includes/lib_huayingcard.php');
include_once (ROOT_PATH.'includes/httpRequest.php');

$Card = new huayingcard();
$no = $Card -> action($dataInfo,11);
$result = $Card -> getResult();
$dataList = $Card -> getDataList();
$time = strtotime($result['Time']);
$time = date('Y-m-d H:i:s',$time);
$sqlval = '';
if($no == 0){
    $companyList = array();
    foreach($dataList['Info'] as $key=>$val){
        $companyList[] = array('card_company_id'=>$val['CustomerID'],'company_name'=>$val['CompanyName'],'grade_id'=>2,'create_time'=>$time);
        if($key!=0){
            $dot = ',';
        }else{
            $dot = '';
        }

        $sqlval.=$dot."('".$val['CustomerID']."','".$val['CompanyName']."',2,'".$time."')";
    }
    $sql = "INSERT INTO ".$GLOBALS['ecs']->table('company')."(card_company_id,company_name,grade_id,create_time) values".$sqlval;
    $res = $GLOBALS['db']->query($sql);
}