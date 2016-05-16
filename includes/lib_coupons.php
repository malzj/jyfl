<?php
//查询提货券相关信息
//根据goods_id,coupons_state,number查询
//根据id查询
//$arr查询条件数组
//$number限制数量
function sel_coupons($arr,$number=0){
    $where.=" where 1";
    if(empty($arr)){
        return false;
    }
    foreach ($arr as $key => $value) {
        $where.= " and c.$key='".$value."'"; 
    }
    $sql="SELECT c.*,s.goods_name,s.limit_num from ".$GLOBALS['ecs']->table('coupons')."as c LEFT JOIN ". $GLOBALS['ecs']->table('goods') ."as s ON c.goods_id=s.goods_id".$where;
    $sql .= ' ORDER BY c.id asc';
    if($number){
        $sql .= ' limit 0,'.$number;
    }
    // echo $sql;die;
    $res = $GLOBALS['db']->getAll($sql);
    return $res;

} 

//更新提货券状态
function update_coupons($id='',$coupons_state){
    if(empty($id)){
        return false;
    }
    $sql_suo_jie="update ".$GLOBALS['ecs']->table('coupons')." set coupons_state='$coupons_state' where id='".$id."'";
    // echo $sql_suo_jie;die;
    $res= $GLOBALS['db']->query($sql_suo_jie);
    return $res;
}

//查询提货券订单相关信息
function sql_coupons_order($orderid,$data,$arr){
    
    if($data=="select"){
        if(empty($orderid)){
            $where.=" where=1";
            foreach ($arr as $key => $value) {
                $where.= " and c.$key='".$value."'"; 
            }
            $sql_sel_coupons_order="select * from ".$GLOBALS ['ecs']->table ('coupons_order').$where;
            //echo $sql_sel_coupons_order;die;
            $res=$GLOBALS ['db']->getAll($sql_sel_coupons_order);
        }else{
            $sql_sel_coupons_order="select * from ".$GLOBALS ['ecs']->table ('coupons_order')." where orderid='".$orderid."'";
            //echo $sql_sel_coupons_order;die;
            $res=$GLOBALS ['db']->getRow($sql_sel_coupons_order);
        }
    
    
    }elseif($data=="insert"){
        $array_key=array('orderid','userid','goods_id','user_name','mobile','coupons_id','unit_price','number','total_price','market_price','order_state','add_time');
        $key=implode(",",$array_key) ;
        $arr1=sortTable('coupons_order',$arr);
        $arrInsert=generateInsert($arr1);
        $insert_sql="insert INTO ".$GLOBALS ['ecs']->table ('coupons_order')."(".$key.") "." VALUES ".$arrInsert;

        // $insert_coupons_order="insert INTO ".$GLOBALS ['ecs']->table ('coupons_order')."(orderid,userid,goods_id,username,mobile,coupons_id,unit_price,number,total_price,market_price,order_state,add_time) VALUES('".$arr['orderid']."','".$arr['userid']."','".$arr['goods_id']."','".$arr['user_name']."','".$arr['mobile']."','".$arr['youhui']."','".$arr['unit_price']."','".$arr['number']."','".$arr['total_price']."','".$arr['market_price']."','".$arr['order_state']."','".$arr['add_time']."')";
        // echo $insert_sql;die;
        $res=$GLOBALS ['db']->query($insert_sql);
        
    }elseif($data=="update"){

        if(empty($orderid)) {
            return false;
        }
        $sets=array();
        
        foreach (array_merge(array('add_time'=>gmtime()),$arr) as $key => $value) {
            if($key=='orderid'){
                unset($value);
            }
            if($value){
              $sets[]= "$key='$value'"; 
            }
        }
        $set=implode(",", $sets);
 
        $update_coupons_order="UPDATE ".$GLOBALS['ecs']->table('coupons_order')." set ".$set." where orderid='$orderid'";
        // echo $update_coupons_order;die;
        $res= $GLOBALS['db']->query($update_coupons_order);
    }else{
        return false;
    }

    return $res;
}

//数量变化处理，增加$orderid,订单号，$number，现在的数量
function number_add($orderid,$number){
    //查询原有的提货券
    if(empty($orderid)){
        return false;
    }
    $sel_coupons_number=sql_coupons_order($orderid,'select','');
    if($sel_coupons_number['coupons_id']){
        $coupons_number3=array_filter(explode(",", $sel_coupons_number['coupons_id']));
        $numberOld=count($coupons_number3);        
    }else{
        $numberOld=0;
    }
    $numberNew=$number-$numberOld;
    if($numberNew<=0){
        return false;
    }
    $goods_id=$sel_coupons_number['goods_id'];
   
    $sql_spec_nember = 'SELECT g.*,gs.spec_nember,gs.spec_price FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .'LEFT JOIN ' . $GLOBALS['ecs']->table('goods_spec') . ' AS gs ' .  "ON gs.goods_id = g.goods_id " ."WHERE g.goods_id='".$goods_id."'";
        $res_spec_nember = $GLOBALS['db']->getRow($sql_spec_nember);
    $spec_price=$res_spec_nember['spec_price'];
    // $sel_coupons1=sel_coupons($spec_price,'',0,$numberNew,$goods_id);
    $sel_coupons1=sel_coupons(array('goods_id' =>$goods_id ,'coupons_state'=>0 ),$numberNew);
     
    if($sel_coupons1){
    
        foreach ($sel_coupons1 as $key1 =>$value1) {
            $coupons_number1[$key1]=$value1['id'];
        }
        //添加的提货券,拼接旧提货券号码

        $coupons_number4=$coupons_number3?implode(",",array_merge($coupons_number1, $coupons_number3)):implode(",", $coupons_number1); 
        $coupons_number5=array_filter(explode(",", $coupons_number4));
        //更新提货券订单信息
        $arr_coupons=array(
            'mobile'=>$sel_coupons_number['mobile'],
            'orderid'=>$orderid,
            'coupons_id'=>$coupons_number4,
            'unit_price'=>$sel_coupons_number['unit_price'],
            'market_price'=>price_format($spec_price),
            'number'=>count($coupons_number5),                           
            'total_price'=>price_format($spec_price*count($coupons_number5))
        );

        $update_new_coupons=sql_coupons_order($orderid,"update",$arr_coupons);
    
        //拼接成新的提货券数组
        // $coupons_number4=explode(",", $coupons_number4);
        foreach ($coupons_number5 as $key4 => $value4) {
            if($value4){
                //锁提货券
                $sql_suo=update_coupons($value4,2); 
            }

        }
        if($update_new_coupons&&$sql_suo){
            return true;
        }

    }else{
        echo 0;die;
        //echo"<script>alert('提货券已售完，请改日再来！');history.go(-1);</script>";  
    }
    //传值，单号1，商品名称，单价1，数量，总价
                
}
//数量变化处理，减少$orderid,订单号，$number，现在的数量
function number_del($orderid,$number){
    //查询提货券订单中，截取提货券号码，更新订单信息，更新提货券状态，传值（单号，单价，数量，总价）
    $sel_coupons_order=sql_coupons_order($orderid,"select","");
   
    $coupons_number=$sel_coupons_order['coupons_id'];
    $sql_spec_nember = 'SELECT g.*,gs.spec_nember,gs.spec_price FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .'LEFT JOIN ' . $GLOBALS['ecs']->table('goods_spec') . ' AS gs ' .  "ON gs.goods_id = g.goods_id " ."WHERE g.goods_id='".$sel_coupons_order['goods_id']."'";
    $res_spec_nember = $GLOBALS['db']->getRow($sql_spec_nember);    
    //$supplier_id=$res_spec_nember['supplier_id'];
    $spec_price=$res_spec_nember['spec_price'];
    $coupons_number1=array_filter(explode(",", $coupons_number));

    //$numberOld=$sel_coupons_order['number'];
    $qudiao=count($coupons_number1)-$number;
    if($qudiao<=0){
        return false;
    }else{
        $coupons_number6=array_slice($coupons_number1,1,$number);//留下的数组
        $coupons_number7=array_diff($coupons_number1,$coupons_number6);//取差集 
        //留下的数组处理
        $coupons_number8=implode(",", $coupons_number6);
        $arr_coupons9=array(
            'mobile'=>$sel_coupons_order['mobile'],
            'orderid'=>$orderid,
            'coupons_id'=>$coupons_number8,
            'unit_price'=>price_format($sel_coupons_order['unit_price']),
            'market_price'=>price_format($spec_price),
            'number'=>$number,                           
            'total_price'=>price_format($sel_coupons_order['unit_price']*$number)//所在地区Code
        );    
        $sel_coupons4=sql_coupons_order($orderid,"update",$arr_coupons9);
        //去掉的数组处理
        if(count($coupons_number7)>0){
            foreach ($coupons_number7 as $key7 => $value7) {
                if($value7){
                    //更新状态，解锁
                    $sql_jiesuo=update_coupons($value7,0);              
                }

            }
            if($sel_coupons4&&$sql_jiesuo){
                return true;
            }
        }
    }
        
}
//数组排序,根据键名排序
function sortTable($table,$arr){
    if(empty($arr)||empty($table)){
        return false;
    }
    if($table=='coupons_order'){
        $array_key=array('orderid','userid','goods_id','user_name','mobile','coupons_id','unit_price','number','total_price','market_price','order_state','add_time');
    }
    if($table=='coupons'){
        $array_key=array('coupons_card','coupons_number','goods_id','supplier_id','price','start_time','end_time','coupons_state');
    }
    $res=arrayOnlySort($arr,$array_key);
    return $res;
}
//根据键名排序，一维、二维都可以
function arrayOnlySort( $sortArray, $sortKey)
{
 
    foreach ($sortArray as $key=>$val)
    {
        if ( is_array($val))
        {
            foreach ($sortKey as $sk=>$sv)
            {
                $returnArray[$key][$sv] = @$val[$sv];
            }
        }else{
            
            foreach ($sortKey as $sk=>$sv)
            {   
                $returnArray[$sv] = @$sortArray[$sv];
            }
        }       
    }
  
    return $returnArray;
}
/* 数组生成insert格式的数据，一维、二维都可以  */
function generateInsert( $insert )
{
    $insertString = '';
    foreach ( $insert as $value)
    {
        if(is_array($value)){
            $insertString .= '(';
            foreach ($value as $key=>$val)
            {
                $insertString .= "'$val',";
            }
            $insertString = substr($insertString, 0 , strlen($insertString)-1);
            $insertString .='),';
        }else{
             $insertString .= "'$value',";
        }
    }
    if(preg_match("/^[(]/",$insertString)){     //使用绝对等于
        //包含
        return substr($insertString, 0 , strlen($insertString)-1);
        
    }else{
        //不包含
        return "(".substr($insertString, 0 , strlen($insertString)-1).")";
    }
    
}

//生成新订单
function newOrder($data){
    if(empty($data['goods_id'])){
        return false;
    }
    require_once(ROOT_PATH. '/includes/lib_order.php');
    unset($_SESSION[$data['goods_id']]);
    $orderid=get_order_sn();//生成的订单号
    $_SESSION[$data['goods_id']]=$orderid;
    //查询提取提货券,用商品id搜索
    $res_coupons=array();
    $res_coupons=sel_coupons(array('goods_id'=>$data['goods_id'],'coupons_state'=>0),1);
    $coupons_number01=array();
    $coupons_number02='';

    if($res_coupons){
        foreach ($res_coupons as $k => $v){
            if($v['id']){
                $coupons_number01[$k]=$v['id'];
                //锁提货券
                $sql_suo=update_coupons($v['id'],"2");
            }
        }
        $coupons_number02=implode(",", $coupons_number01);
        $data['number']=count($coupons_number01);
        $data['total_price']=price_format($data['unit_price']*count($coupons_number01));
        if($sql_suo){
            $arr_insert=array(
                'orderid'=>$orderid,
                'goods_id'=>$data['goods_id'],
                'userid'=>$data['userid'],
                'user_name'=>$data['user_name'],
                'mobile'=>$data['mobile'],
                'coupons_id'=>$coupons_number02,
                'unit_price'=>$data['unit_price'],
                'number'=>$data['number'],
                'total_price'=>$data['total_price'],
                'market_price'=>$data['market_price'],
                'order_state'=>0,
                'add_time'=>gmtime()                        
                );

            $sql_coupons_order=sql_coupons_order($orderid,"insert",$arr_insert);
        }else{
            show_message('提货券已售完，请改日再来！','',"/coupons_list.php?id=$goods_id");
        }
    }else{
         show_message('提货券已售完，请改日再来！','',"/coupons_list.php?id=$goods_id");
    }    
}
//多维数组转为一维数组
function makeOne($arr){  //rebuild a array
  static $tmp=array();
  for($i=0; $i<count($arr); $i++){
    if(is_array($arr[$i])) makeOne($arr[$i]);
    else $tmp[]=$arr[$i];
  }

  return $tmp;
}
//根据订单号$orderid，查询短信格式，规格化提货券，有效期
function guigeSms($orderid){
    if(empty($orderid)){
        return false;
    }
    $sqlOrderid = 'SELECT co.*,g.goods_name,s.value FROM ' . $GLOBALS['ecs']->table('coupons_order') . ' AS co ' .'LEFT JOIN ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .   "ON co.goods_id = g.goods_id " .'LEFT JOIN ' . $GLOBALS['ecs']->table('sms_content') . ' AS s ' .   "ON s.id = g.sms_content_id " ."WHERE co.orderid='".$orderid."'";
    // echo $sqlOrderid;die;
    $resOrderid = $GLOBALS['db']->getRow($sqlOrderid);
    if(!$resOrderid){
       return false; 
    }
    //处理提货券
    $coupons_arr=explode(",",$resOrderid['coupons_id']);
    foreach ($coupons_arr as $key_arr => $value_arr) {

        if($value_arr&&($resOrderid['order_state']!=1)){
            //更新提货券为已使用
            $update_coupons1=update_coupons($value_arr,1);  
        }
        $sqlCoupons="SELECT c.* from ".$GLOBALS['ecs']->table('coupons')."as c "."where c.id='".$value_arr."'";
            //echo $sql;die;
        $resCoupons = $GLOBALS['db']->getRow($sqlCoupons);
        if($resCoupons['coupons_card']&&$resCoupons['coupons_number']){
            $content_arr[$key_arr]=array(
                    'coupons_card'=>$resCoupons['coupons_card'],
                    'coupons_number'=>$resCoupons['coupons_number'],
                    'end_time'=>local_date("Y-m-d", $resCoupons['end_time']));
        }else{
            $content_arr[$key_arr]=array('coupons_card'=>$resCoupons['coupons_card'],'end_time'=>local_date("Y-m-d", $resCoupons['end_time']));
        }
    }
    //拼接短信，提货券的卡号，密码循环，数量，名称，有效期等替换
    $arr1=explode('，', $resOrderid['value']);
    $str1=explode("@", $arr1[1]);
    foreach ($content_arr as $key_arr => $value_arr) {
        foreach ($str1 as $key1 => $value1){
            if($key1==0){
                if($value1){
                    $datas[].=$value1.$value_arr['coupons_card'];
                }
            }
            if($key1==1){
                if($value1){
                    $datas[].=$value1.$value_arr['coupons_number'];
                }
            }
        }
    }
    @$res['tihuo']=implode('，', $datas);
    foreach ($arr1 as $key => $value) {
        if($key==0){
            $str0=explode("@", $value);
            foreach ($str0 as $key0 => $value0) {
                if($key0==0){
                    $data[$key].=$value0.$resOrderid['number'];
                }
                if($key0==1){
                    $data[$key].=$value0.$resOrderid['goods_name'];
                }
                if($key0==2){
                    $data[$key].=$value0;
                }
            }
        }
        if($key==1){
            $data[$key]=$res['tihuo'];
        }
        if($key==2){
            $data[$key]=str_replace('@',$content_arr[0]['end_time'],$value);
            $res['end_time']=$content_arr[0]['end_time'];
        }
        if($key>2){
            $data[$key]=$value;
        }
    }
    $res['content']=implode('，', $data);
    return $res;     
}
?>