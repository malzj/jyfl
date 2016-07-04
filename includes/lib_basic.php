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
 function findData( $table='users', $find=null, $field="*", $order='')
 {
     $where = ' WHERE 1 ';
     if ( !is_null($find) )
     {
         $where .= " AND ".$find;
     }
 
     return $GLOBALS['db']->getAll( 'SELECT '.$field.' FROM '.$GLOBALS['ecs']->table($table). $where. $order);
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
     $advs = findData('ad', "enabled = 1 AND position_id = $posid", '*',' order by listorder ASC,ad_id DESC');
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
                 $image[$key] = $val;
                 $image[$key]['no'] = $adPosition;
             }
             else {
                 $image[] = $val;
             }
                 
         }
     }
     
     return $image;
 }
 
 // 处理指定属性字段信息，为数组, 传入的是属性id
 function get_cake_attr($attr_id=null)
 {
     // 蛋糕属性 （attr_id = 2 是口味）
     $cakeAttr = $GLOBALS['db']->getAll('SELECT * FROM '.$GLOBALS['ecs']->table('attribute'). ' WHERE cat_id = 28');
 
     foreach ($cakeAttr as $key=>$val)
     {
         if ($val['attr_id'] == $attr_id)
         {
             return explode("\r\n", $val['attr_values']);
         }
     }
 }
 
 // 得到楼层广告 $name 唯一名字（分类名等）， $no 第几个广告图片
 
 function attrAd( $name, $no=0, $posid=17)
 {
 
     global $adList;
     $returnArray = $currentArray = $default = array();
 
     if ( empty($adList) ){
         $adList = getNavadvs($posid);
     }
 
     foreach ($adList as $fixed=>$list)
     {
         // 默认的广告图片
         if (strpos($list['ad_name'], 'default') !== false)
         {
             $default = $list;
         }
         // 得到指定的广告
         if (strpos($list['ad_name'], $name) !== false)
         {
             $currentArray[$list['no']] = $list;
         }
     }
 
     // 找到指定顺序的广告，如果没有就用默认的广告代替
     //$current = current(array_slice($currentArray, $no, 1));
     if (empty($currentArray[$no]))
         $returnArray = $default;
     else
         $returnArray = $currentArray[$no];
 
     $returnArray['is_ad'] = 'true';
     return $returnArray;
 }
 
 /**
  * 分类id对应的广告为id
  * 用于分类列表显示对应的广告位
  */
 function getAdid($catid)
 {
     $advs = array(
         // 蛋糕广告【banner广告，text广播】
         '4' => array( 'banner' => 15,'text'   => 16),
         '5' => array( 'banner' => 15,'text'   => 16),
         '6' => array( 'banner' => 15,'text'   => 16),
         '7' => array( 'banner' => 15,'text'   => 16),
         '8' => array( 'banner' => 15,'text'   => 16),
         '9' => array( 'banner' => 15,'text'   => 16),
         
         // 舌尖美食广告【banner广告，text广播】
         '11' => array('banner' => 20,'text'   => 19),
         '12' => array('banner' => 20,'text'   => 19),
         '13' => array('banner' => 20,'text'   => 19),
         '14' => array('banner' => 20,'text'   => 19),
         '15' => array('banner' => 20,'text'   => 19),
         '16' => array('banner' => 20,'text'   => 19),
         
         // 优品生活广告【banner广告，text广播】
         '22' => array('banner' => 30,'text'   => 31),
         '23' => array('banner' => 30,'text'   => 31),
         '24' => array('banner' => 30,'text'   => 31),
         '25' => array('banner' => 30,'text'   => 31),
         '26' => array('banner' => 30,'text'   => 31),
         
         // 运动装备
         '17' => array(
             'banner' => 21,
             'text'   => 22
         ),
         //鲜花
         '18' => array(
             'banner' => 24,
             'text'   => 23
         ),
         //洗衣
         '20' => array(
             'banner' => 26,
             'text'   => 25
         ),
         //体检
         '19' => array(
             'banner' => 28,
             'text'   => 27
         ),
     );
     
     return $advs[$catid];
 }
 
 /**
  *  分类商品，跳转首页对应的连接地址
  */
 function get_category_back($id)
 {
     $catUrl = array(
         // 蛋糕广告
         '4' => 'cake.php',
         '5' => 'cake.php',
         '6' => 'cake.php',
         '7' => 'cake.php',
         '8' => 'cake.php',
         '9' => 'cake.php',
          
         // 舌尖美食广告
         '11' => 'life.php',
         '12' => 'life.php',
         '13' => 'life.php',
         '14' => 'life.php',
         '15' => 'life.php',
         '16' => 'life.php',
          
         // 优品生活广告
         '22' => 'ylife.php',
         '23' => 'ylife.php',
         '24' => 'ylife.php',
         '25' => 'ylife.php',
         '26' => 'ylife.php',
          
         // 运动装备
         '17' => 'category.php?id=17',
         //鲜花
         '18' => 'category.php?id=18',
         //洗衣
         '20' => 'category.php?id=20',
         //体检
         '19' => 'category.php?id=19'
     );
     
     return $catUrl[$id];
 }
 
 /**  
  * 返回首页和上一页  
  */
 function getBackHtml( $back )
 {
     $html = '<div class="return_tip">';
     if (!empty($back))
	   $html .='<a href="'.$back.'" class="return_tip_index"><span class="return_index_img"></span><span>首页</span></a>';
	 
     $html .='<a href="javascript:history.go(-1);" class="return_tip_return"><span class="return_return_img"></span><span>返回</span></a></div>';
     $html .="<script>
                jQuery(function($) {
    				$(document).ready(function() {
    					$('.return_tip').stickUp();
    				});
			});</script>";
     
     return $html;
 }
 
/** 
 *  分类列表页面，模板渲染， 不同的分类显示不同的模板文件 
 */
 function categoryTemplate($cat_id)
 {
     $category = current(findData('category',"cat_id=$cat_id"));
     if ( !empty($category['parent_id'])){
         $category = current(findData('category', "cat_id=$category[parent_id]"));
     }  

     $templates = array(
         // 蛋糕
         '4'    =>'cake/cakeCategory.dwt',
         // 生活
         '10'   =>'life/lifeCategory.dwt',
         // 优品生活
         '21'   =>'ylife/ylifeCategory.dwt',
         // 运动装备
         '17'   =>'simple/sportsCategory.dwt',
         '18'   =>'simple/sportsCategory.dwt',
         '20'   =>'simple/sportsCategory.dwt',
         '19'   =>'simple/sportsCategory.dwt',
     );
     
     if (isset($templates[$category['cat_id']])) 
         return $templates[$category['cat_id']];
     else
        return 'category.dwt';
     
 }
 
/**
 * 商城售价策略
 */
 function getExt($ext)
 {
    $extArray = array(
        1   => '1',
        2   => '0.8'
    );     
    return $extArray[$ext];
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