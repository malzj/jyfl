<?php

/**
 *  函数库
 */

/** 卡类型检查
 *  @param      $username   string      卡号
 *  @param      $type       intval      登录类型
 */
function checkCardType( $username, $type)
{
    // 卡规则信息
    $cardRule = array();
   
    // 直接查找卡规则，而不是从用户表里查找，card_id ,因为如果这张卡修改了卡规则，那么验证就出错了
    $cardRules = $GLOBALS['db']->getAll('SELECT * FROM '.$GLOBALS['ecs']->table('card_rule'));
    if ( !empty($cardRules) )
    foreach ($cardRules as $key=>$var){
        if (!empty($var['card'])){
            $arr_card = unserialize($var['card']);
            if (in_array($username, $arr_card))
            {
                $cardRule = $var;
            }
        }
    }
    // 必须存在卡规则里，否则返回 false
    if (empty($cardRule))
    {
        return false;
    }
    
    // 卡类型判断
    if ($cardRule['type'] != $type)
    {
        return false;
    }
    
    return true;
}

/**
 * 用户信息
 * 
 * @param   $username   string      卡号
 * @return  array
 */
 function userinfo( $username )
 {
     return current(findData( 'users', 'user_name='.$username));
 }
 
 /**
  * 得到指定数据列表
  *
  * @param 	$table	string	数据表
  * @param 	$find	string	查询条件
  * @param	$field	string	返回字段
  */
 function findData( $table='users', $find=null, $field="*")
 {
     $where = ' WHERE 1 ';
     if ( !is_null($find) )
     {
         $where .= " AND ".$find;
     }
 
     return $GLOBALS['db']->getAll( 'SELECT '.$field.' FROM '.$GLOBALS['ecs']->table($table). $where);
 }
 
 /**
  * 获取一个导航的子导航，传入主导航id
  *
  * @param   $selected   导航id
  * @param   $is_wap     显示支持 false pc端， true 手机端
  */
 function getCinemaCate( $selected, $is_wap=false )
 {
     $returnArray = array();
     $navigator = get_navigator();
     if ($is_wap == true)
         init_wap_middle($navigator['middle']);
 
     foreach ($navigator['middle'] as $cateid=>$middle)
     {
         if ($cateid == $selected)
         {
             $returnArray = $middle['child'];
         }
     }
 
     return $returnArray;
 }
 
 /**  
  *  获得指定导航的广告列表
  *  
  *  @param $posid      广告位id
  */
 function getNavadvs($posid)
 {
     $image = array();
     $advs = findData('ad', "enabled = 1 AND position_id = $posid");
     if (empty($advs)) {
         return $image;
     }
     
     $currentTime = local_gettime();
     foreach ($advs as $key=>$val) {
         // 只返回在有效期范围内的广告列表
         if ($currentTime > $val['state_time'] && $currentTime < $val['end_time'])
         {
             // 广告名称里面有 -- 后面代表的是数字，
             if (strpos($val['ad_name'], '--'))
             {
                 $names = explode('--', $val['ad_name']);
                 $adPosition = array_pop($names);
                 $image[$adPosition] = $val;
             }
             else {
                 $image[] = $val;
             }
                 
         }
     }
     
     return $image;
 }
 
/**
 * 设置字符串的字符集
 * @param unknown $content          编码的字符串      
 * @param string $in_charset        输入的字符集
 * @param string $out_charset       输出的字符集
 **/
 function setCharset($content,$in_charset='UTF-8', $out_charset='GB2312')
 {
    return iconv($in_charset,$out_charset,$content);   
 }
 
 
 // 图片本地化
 function getImage($url,$save_dir='',$filename='',$type=0){
     if(trim($url)==''){
         return array('file_name'=>'','save_path'=>'','error'=>1);
     }
     if(trim($save_dir)==''){
         $save_dir='./';
     }
     if(trim($filename)==''){//保存文件名
         $ext=strrchr($url,'.');
         if($ext!='.gif'&&$ext!='.jpg'){
             return array('file_name'=>'','save_path'=>'','error'=>3);
         }
         $filename=time().$ext;
     }
     if(0!==strrpos($save_dir,'/')){
         $save_dir.='/';
     }
     //创建保存目录
     if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
         return array('file_name'=>'','save_path'=>'','error'=>5);
     }
     //获取远程文件所采用的方法
     if($type){
         $ch=curl_init();
         $timeout=5;
         curl_setopt($ch,CURLOPT_URL,$url);
         curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
         curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
         $img=curl_exec($ch);
         curl_close($ch);
     }else{
         ob_start();
         readfile($url);
         $img=ob_get_contents();
         ob_end_clean();
     }
     //$size=strlen($img);
     //文件大小
     $fp2=@fopen($save_dir.$filename,'a');
     fwrite($fp2,$img);
     fclose($fp2);
     unset($img,$url);
     return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
 }
?>