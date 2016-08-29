<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if($_REQUEST['act'] == 'code_import'){
    $smarty->display('code_import.htm');
}
elseif($_REQUEST['act'] == 'done_import'){
    if($_FILES['code_file']['type']=='application/octet-stream'||$_FILES['code_file']['type']=='application/vnd.ms-excel'){
        $tmp_name = $_FILES['code_file']['tmp_name'];
        $filename = "code-".local_date("YmdHis",time()).".xlsx";
        $msg = uploadFile($filename,$tmp_name);
    }else{
        sys_msg("文件格式错误或者过大！请上传excel2007的*.xlsx格式文件");
        die;
    }

    require (dirname ( __FILE__ ) . '/../admin/includes/lib_autoExcels.php');
    $PHPExcel = new autoExcels('Excel2007');		//实例化类并传入导出格式（可以不传，默认是2007）
    $PHPExcel->setSaveName($msg);		//要保存的文件名（必须）
    $colsTitle = array('0'=>array('A'=>'price','B'=>'code','C'=>'account','D'=>'password','E'=>'validity'));
    $PHPExcel->setColsTitle($colsTitle);
    @$list = $PHPExcel->execExcel('import');
    $list=$list[0];
    unset($list[0]);
// echo "<pre>";
// print_r($list);
// echo "</pre>";
// die;
    if(empty($list))
        die("上传文件内容不能为空！");
    $sql="SELECT account FROM ".$ecs -> table('code')." WHERE supplier_id = '".$_SESSION['supplier_id']."'";
    $code_result = $db->getAll($sql);
    $code_array = array();
    foreach($code_result as $code){
        $code_array[] = $code['account'];
    }
    $str_array = array();

    foreach($list as $key => $value){
        if($_REQUEST['operate'] != 'update') {
            if (in_array($value['account'], $code_array)) {
                echo '商品码' . $value['account'] . '已存在请勿重复导入！</br>';
                continue;
            }
        }
        $time = strtr($value['validity'],array('"'=>'','年'=>'-','月'=>'-','日'=>'','时'=>':','分'=>':','秒'=>''));
        $value['validity'] = strtotime($time);
        $value['status'] = 0;
        $value['supplier_id'] = $_SESSION['supplier_id'];
        $value['add_time'] = time();
        if($_REQUEST['operate'] == 'update'){
            $sql = "UPDATE ".$ecs -> table('code')." SET `price`='".$value['price']."',`code`='".$value['code']."',`password`='".$value['password']."',`validity`='".$value['validity']."',`supplier_id`='".$value['supplier_id']."'
             WHERE account = '".$value['account']."' AND supplier_id = '".$value['supplier_id']."' AND status = 0";
            $result = $db -> query($sql);
            echo '更新码'.$value['account'].','.($result==1?'成功！':'失败！').'</br>';
        }else{
            $str_array[]="('".implode("','",$value)."')";
        }
    }

    if(!empty($str_array)){
        $insert_sql = "INSERT INTO ".$ecs->table('code')." (`price`,`code`,`account`,`password`,`validity`,`status`,`supplier_id`,`add_time`) VALUE "
            .implode(',',$str_array);
        $result = $db->query($insert_sql);
    }

    if(isset($result)&&$result==1){
        echo "<p style='color:green'>导入商品码成功！</p>";
        delDir(ROOT_PATH .'temp/upload/');
    }else{
        echo "<p style='color:red'>导入商品码失败！</p>";
        delDir(ROOT_PATH .'temp/upload/');
    }
}
//上传类，如果目录不存在就创建
function uploadFile($filenameUTF,$tmp_name)
{
    //自己设置的上传文件存放路径
    $filePath =ROOT_PATH .'temp/upload/';
    $filenameGB=iconv("UTF-8","gb2312", $filenameUTF);
    $fileNamePath=$filePath.$filenameGB;

    // 首先需要检测目录是否存在
    //move_uploaded_file() 函数将上传的文件移动到新位置。若成功，则返回 true，否则返回 false。
    if(is_dir($filePath)){
        if(file_exists($fileNamePath)){
            //echo "文件已经存在，不能重复提交！";
        }else{
            $result=move_uploaded_file($tmp_name,$fileNamePath);
        }
    }else{
        mkdir($filePath);

        $result=move_uploaded_file($tmp_name,$fileNamePath);

    }
    return $fileNamePath;
}