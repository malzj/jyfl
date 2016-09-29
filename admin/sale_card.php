<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/8/22
 * Time: 16:54
 */
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ($_REQUEST['act'] == '')
{
    $smarty->assign('shop_url', urlencode($ecs->url()));
    $smarty->display('index.htm');
}

/*导入售卡信息*/
if($_REQUEST['act'] == 'import')
{
    $smarty -> display('sale_card_import.htm');
}
/*执行导入操作*/
elseif($_REQUEST['act'] == 'done_import')
{
    set_time_limit(0);
    $start = microtime(true);
//  if($_FILES['sale_card_file']['type']=='application/octet-stream'||$_FILES['sale_card_file']['type']=='application/vnd.ms-excel'){
        $tmp_name = $_FILES['sale_card_file']['tmp_name'];
        $filename = "card-".local_date("YmdHis",time()).".xlsx";
        $msg = uploadFile($filename,$tmp_name);
//  }else{
//      sys_msg("文件格式错误或者过大！请上传excel2007的*.xlsx格式文件");
//      die;
//  }
    require (dirname ( __FILE__ ) . '/../admin/includes/lib_autoExcels.php');
    $PHPExcel = new autoExcels('Excel2007');		//实例化类并传入导出格式（可以不传，默认是2007）
    $PHPExcel->setSaveName($msg);		//要保存的文件名（必须）
    $colsTitle = array('0'=>array('A'=>'card_num','B'=>'price','C'=>'sale_man','D'=>'company','E'=>'sale_time'));
    $PHPExcel->setColsTitle($colsTitle);
    @$list = $PHPExcel->execExcel('import');
    $list=$list[0];
    array_shift($list);
    $listarray=array_chunk($list,1000,true);
    unset($list[0]);
    unset($list);
    $end = microtime(true);
    echo "读取excel时间".round($end-$start,3)."</br>";
//    echo count($listarray[10]);
//    die;
// echo "<pre>";
// print_r($list);
// echo "</pre>";
// die;

    $str_array = array();
    $time_array = array();
    $start = microtime(true);
    echo '分'.count($listarray).'组导入！</br>';
    foreach($listarray as $k => $val) {
        foreach ($val as $key => $value) {
            $time = strtr($value['sale_time'], array('"' => '', '年' => '-', '月' => '-', '日' => '', '时' => ':', '分' => ':', '秒' => ''));
            $time_array[]=$value['sale_time'] = empty($time) ? '' : strtotime($time);
            $value['price']=floatval($value['price']);
            $str_array[$value['card_num']] = "('" . implode("','", $value) . "')";
        }
        $time_array=array_unique($time_array);
        $sql="SELECT card_num FROM ".$ecs -> table('sale_card')." WHERE sale_time IN('" . implode("','", $time_array) . "')";
        $code_result = $db->getAll($sql);
        $code_num_list = array();
        foreach ($code_result as $ky => $code){
            $code_num_list[]=$code['card_num'];
        }
        $code_flip=array_flip($code_num_list);
        //过滤得到未导入的信息
        $str_array=array_diff_key($str_array,$code_flip);

        if (!empty($str_array)) {
            $insert_sql = "INSERT INTO " . $ecs->table('sale_card') . " (`card_num`,`price`,`sale_man`,`company`,`sale_time`) VALUE "
                . implode(',', $str_array);
            $result = $db->query($insert_sql);
            if($result){
                echo '第'.($k+1).'组导入成功！</br>';
            }else{
                echo '第'.($k+1).'组导入失败！</br>';
            }
        }else{
            echo '第'.($k+1).'已导入或文件为空！</br>';
        }
        ob_flush();
        flush();
        sleep(3);
    }
    $end = microtime(true);
    echo "循环插入时间".round($end-$start,3)."</br>";

    if(isset($result)&&$result==1){
        echo "<p style='color:green'>导入售卡信息成功！</p>";
        delDir(ROOT_PATH .'temp/upload/');
    }else{
        echo "<p style='color:red'>导入售卡信息失败！</p>";
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