<?php

/**
 * ECSHOP 短信模块 之 模型（类库）
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: douqinghua $
 * $Id: cls_sms.php 17155 2010-05-06 06:29:05Z douqinghua $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
define('SOURCE_TOKEN', '814d4852d74f5914b41695ee7fa8508c');
define('SOURCE_ID', '863180');
require_once(ROOT_PATH . 'includes/cls_transport.php');
require_once(ROOT_PATH . 'includes/shopex_json.php');
require_once(ROOT_PATH . 'includes/lib_time.php');

/* 短信模块主类 */
class sms
{
    /**
     * 存放提供远程服务的URL。
     *
     * @access  private
     * @var     array       $api_urls
     */
    var $api_urls   = array(
                            'info'              =>      'http://api.sms.shopex.cn',
                            'send'              =>      'http://api.sms.shopex.cn',
                            'servertime'        =>      'http://webapi.sms.shopex.cn'
    
    );
    /**
     * 存放MYSQL对象
     *
     * @access  private
     * @var     object      $db
     */
    var $db         = null;

    /**
     * 存放ECS对象
     *
     * @access  private
     * @var     object      $ecs
     */
    var $ecs        = null;

    /**
     * 存放transport对象
     *
     * @access  private
     * @var     object      $t
     */
    var $t          = null;

    /**
     * 存放程序执行过程中的错误信息，这样做的一个好处是：程序可以支持多语言。
     * 程序在执行相关的操作时，error_no值将被改变，可能被赋为空或大等0的数字.
     * 为空或0表示动作成功；大于0的数字表示动作失败，该数字代表错误号。
     *
     * @access  public
     * @var     array       $errors
     */
    var $errors  = array('api_errors'       => array('error_no' => -1, 'error_msg' => ''),
                         'server_errors'    => array('error_no' => -1, 'error_msg' => ''));

    /**
     * 构造函数
     *
     * @access  public
     * @return  void
     */
    function __construct()
    {
        $this->sms();
    }

    /**
     * 构造函数
     *
     * @access  public
     * @return  void
     */
    function sms()
    {
        /* 由于要包含init.php，所以这两个对象一定是存在的，因此直接赋值 */
        $this->db = $GLOBALS['db'];
        $this->ecs = $GLOBALS['ecs'];

        /* 此处最好不要从$GLOBALS数组里引用，防止出错 */
        $this->t = new transport(-1, -1, -1, false);
        $this->json    = new Services_JSON;
    }
   
     /* 发送短消息
     *
     * @access  public
     * @param   string  $phone          要发送到哪些个手机号码，传的值是一个数组
     * @param   string  $msg            发送的消息内容
     */
    function send($phones,$msg,$send_date = '', $send_num = 1,$sms_type='',$version='1.0')
    {
       
        /* 检查发送信息的合法性 */
        $contents=$this->get_contents($phones, $msg);  

        if(!$contents)
        {
            $this->errors['server_errors']['error_no'] = 3;//发送的信息有误
            return false;
        }
        
         /* 获取API URL */
        $getSms = $this->getSms();
        $sms_url=$getSms['sms_domain'];

        if (!$sms_url)
        {
            $this->errors['server_errors']['error_no'] = 6;//URL不对

            return false;
        }
        $send_str['name']=$getSms['sms_user_name'];//必填参数。用户账号
        $send_str['pwd']=$getSms['sms_password'];  //必填参数。（web平台：基本资料中的接口密码）      
        $send_str['mobile']= $contents[0]['phones'];//必填参数。手机号码。多个以英文逗号隔开
        $send_str['content']= $contents[0]['content'];//必填参数。发送内容（1-500 个汉字）UTF-8编码
        $send_str['sign']='华影文化';//必填参数。用户签名。
        $send_str['type'] = 'pt'; //必填参数。固定值 pt
        $send_str['extno'] = '';//可选参数，扩展码，用户定义扩展码，只能为数字
        $send_str['stime'] = ''; //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
        /* 发送HTTP请求 */
        $response = $this->t->request($sms_url, $send_str,'POST');
        $result = substr($response['body'], 0, 1 );
  
        if($result['res'] == '0'){
            //短信发送成功，更新余额，增加已发送短信的条数，更新最后发送的时间
 
            $send_data['sms_balance']=$this->get_balance();
            $send_data['sms_count']=$getSms['sms_count']+1;
            $send_data['sms_last_request']=$this->getTime();
            $res=$this->updateSms($send_data);
            //更新短信记录
            $arr=array(
                'mobile'=>$contents[0]['phones'], 
                'value'=>$contents[0]['content'], 
                'number'=>1, 
                'add_time'=>$this->getTime(),
                'status'=>1    
                );
        
            $result=$this->set_sms_log('sms_log',$arr);
            return true;
        }else{
            return false;
        }
       
    }
   

    

    /**
     * 检测启用短信服务需要的信息
     *
     * @access  private
     * @param   string      $email          邮箱
     * @param   string      $password       密码
     * @return  boolean                     如果启用信息格式合法就返回true，否则返回false。
     */
    function check_enable_info($email, $password)
    {
        if (empty($email) || empty($password))
        {
            return false;
        }

        return true;
    }

    //查询是否已有通行证
    function has_registered()
    {
        // $sql = 'SELECT `value`
        //         FROM ' . $this->ecs->table('shop_config') . "
        //         WHERE `code` = 'ent_id'";

        // $result = $this->db->getOne($sql);

        // if (empty($result))
        // {
        //     return false;
        // }

        return true;
    }
    function get_site_info()
    {
        /* 获得当前处于会话状态的管理员的邮箱 */
        $email = $this->get_admin_email();
        $email = $email ? $email : '';
        /* 获得当前网店的域名 */
        $domain = $this->ecs->get_domain();
        $domain = $domain ? $domain : '';
        /* 赋给smarty模板 */
        $sms_site_info['email'] = $email;
        $sms_site_info['domain'] = $domain;

        return $sms_site_info;
    }
    function get_site_url()
    {
        $url = $this->ecs->url();
        $url = $url ? $url : '';
        return $url;
    }
    /**
     * 获得当前处于会话状态的管理员的邮箱
     *
     * @access  private
     * @return  string or boolean       成功返回管理员的邮箱，否则返回false。
     */
    function get_admin_email()
    {
        $sql = 'SELECT `email` FROM ' . $this->ecs->table('admin_user') . " WHERE `user_id` = '" . $_SESSION['admin_id'] . "'";
         $email = $this->db->getOne($sql);

         if (empty($email))
         {
            return false;
         }

         return $email;
    }
    // //用户短信账户信息获取
    // function getSmsInfo($certi_app='sms.info',$version='1.0', $format='json'){
    //     $send_str['certi_app'] = $certi_app;
    //     $send_str['entId'] = $GLOBALS['_CFG']['ent_id'];
    //     $send_str['entPwd'] = $GLOBALS['_CFG']['ent_ac'];
    //     $send_str['source'] = SOURCE_ID;
    //     $send_str['version'] = $version;
    //     $send_str['format'] = $format;
    //     $send_str['timestamp'] = $this->getTime();

    //     $send_str['certi_ac'] = $this->make_shopex_ac($send_str,SOURCE_TOKEN);
    //     $sms_url = $this->get_url('info');
    //     $response = $this->t->request($sms_url, $send_str,'POST');
    //     $result = $this->json->decode($response['body'],true);
    //     if($result['res'] == 'succ')
    //     {
    //         return $result;
    //     }
    //     elseif($result['res'] == 'fail')
    //     {
    //         return false;
    //     }
    // }
        // //用户短信账户信息获取

    
    //检查手机号和发送的内容并生成生成短信队列
     function get_contents($phones,$msg)
     {
        if (empty($phones) || empty($msg))
        {
            return false;
        }
        $phone_key=0;

        $phones=explode(',',$phones);
        foreach($phones as $key => $value)
        {
             if($i<200)
             {
                $i++;
             }
             else
             {
               $i=0;
               $phone_key++;
             }
             if($this->is_moblie($value))
             {
                $phone[$phone_key][]=$value;
             }
             else
             {
                 $i--;
             }
         }
         if(!empty($phone))
         {
             foreach($phone as $phone_key => $val)
             {
                   if (EC_CHARSET != 'utf-8')
                    {
                        $phone_array[$phone_key]['phones']=implode(',',$val);
                        $phone_array[$phone_key]['content']=iconv('gb2312','utf-8',$msg);
                    }
                  else
                   {
                        $phone_array[$phone_key]['phones']=implode(',',$val);
                        $phone_array[$phone_key]['content']=$msg;
                   }
                  
             }
             return $phone_array;
         }
         else
         {
            return false; 
         }
         
     }
    
    //获得服务器时间
    function getTime(){
        $Tsend_str['certi_app'] = 'sms.servertime';
        $Tsend_str['version'] = '1.0' ;
        $Tsend_str['format'] = 'json' ;
        $Tsend_str['certi_ac'] = $this->make_shopex_ac($Tsend_str,'SMS_TIME');
        $sms_url = $this->get_url('servertime');
        $response = $this->t->request($sms_url, $Tsend_str,'POST');
        
        $result = $this->json->decode($response['body'], true);
        return $result['info'];
        
    }
     /**
     * 返回指定键名的URL
     *
     * @access  public
     * @param   string      $key        URL的名字，即数组的键名
     * @return  string or boolean       如果由形参指定的键名对应的URL值存在就返回该URL，否则返回false。
     */
    function get_url($key)
    {
        $url = $this->api_urls[$key];

        if (empty($url))
        {
            return false;
        }

        return $url;
    }
    /**
     * 检测手机号码是否正确
     *
     */
    function is_moblie($moblie)
    {
       return  preg_match("/^0?1((3|5|8)[0-9]|5[0-35-9]|4[57])\d{8}$/", $moblie);
    }
   
    //加密算法
    function make_shopex_ac($temp_arr,$token)
    {
       ksort($temp_arr);
       $str = '';
       foreach($temp_arr as $key=>$value)
       {
            if($key!=' certi_ac') 
            {
               $str.= $value;
            }
        }
       return strtolower(md5($str.strtolower(md5($token))));
     }
    function base_encode($str)
    {
        $str = base64_encode($str);
        return strtr($str, $this->pattern());
    }
    function pattern()
    {
        return array(
        '+'=>'_1_',
        '/'=>'_2_',
        '='=>'_3_',
        );
    }
    function getSms()
    {
        $sql = 'SELECT * FROM ' . $this->ecs->table('shop_config') . " WHERE `parent_id` = 8";
        $arr = $this->db->getAll($sql);

         if (empty($arr))
         {
            return false;
         }
         foreach ($arr as $key => $value){
            //用户名
            if($value['code']=='sms_user_name'){
                $list['sms_user_name']= $value['value'];
            }
            //域名
            if($value['code']=='sms_domain'){
                $list['sms_domain']= $value['value'];
            }
            //密钥
            if($value['code']=='sms_password'){
                $list['sms_password']= $value['value'];
            }
            //总发送条数
            if($value['code']=='sms_count'){
                $list['sms_count']= $value['value'];
            }
            //总充值金额
            if($value['code']=='sms_total_money'){
                $list['sms_total_money']= $value['value'];
            }
            //余额
            if($value['code']=='sms_balance'){
                $list['sms_balance']= $value['value'];
            }
            //
            if($value['code']=='sms_last_request'){
                $list['sms_last_request']= $value['value'];
            }
         }
         
         return $list;
    }
    function get_balance(){
         /* 获取API URL */
        $getSms = $this->getSms();
        $sms_url=$getSms['sms_domain'];

        if (!$sms_url)
        {
            $this->errors['server_errors']['error_no'] = 6;//URL不对

            return false;
        }
        $send_str['name']=$getSms['sms_user_name'];//必填参数。用户账号
        $send_str['pwd']=$getSms['sms_password'];  //必填参数。（web平台：基本资料中的接口密码）      
        $send_str['sign']='华影文化';//必填参数。用户签名。
        $send_str['type'] = 'balance'; //必填参数。固定值 pt
        /* 发送HTTP请求 */
        $response = $this->t->request($sms_url, $send_str,'POST');
        $arr=explode(',', $response['body']);
        $start= substr($response['body'], 0, 1 );
        if($arr['0']=='0'){
            return $arr['1'];
        }        
        
    }
    //更新已发送短信条数，余额，最后发送的时间
    function updateSms($arr){
        if(empty($arr)){
            return false;
        }

        foreach ($arr as $key => $value) {
            # code...
            if($value){
                $sql = 'update ' . $this->ecs->table('shop_config')." set value = '$value'" . " WHERE code = '$key'";
                // echo $sql;die;
                $res = $this->db->query($sql);                
            }
        }

    }
    //获取短信发送记录
    function get_send_history(){
        $result = get_filter();
        if ($result === false)
        {
            /* 过滤条件 */
            $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
            if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
            {
                $filter['keyword'] = json_str_iconv($filter['keyword']);
            }
            // $filter['is_going']   = empty($_REQUEST['is_going']) ? 0 : 1;
            $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'id' : trim($_REQUEST['sort_by']);
            $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

            $where = "";
            if (!empty($filter['keyword']))
            {
                $where .= " AND sightname LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
            }


            $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('sms_log') ." WHERE 1"." $where";

            $filter['record_count'] = $GLOBALS['db']->getOne($sql);

            /* 分页大小 */
            $filter = page_and_size($filter);

            /* 查询 */
             $sql ="SELECT * FROM " . $GLOBALS ['ecs']->table('sms_log')." WHERE 1" . " $where "." ORDER BY $filter[sort_by] $filter[sort_order] "." LIMIT ". $filter['start'] .", $filter[page_size]";
            
            set_filter($filter, $sql);
        }
        else
        {
            $sql    = $result['sql'];
            $filter = $result['filter'];
        }
        $res = $GLOBALS['db']->query($sql);
        $list = array();
        while ($row = $GLOBALS['db']->fetchRow($res))
        {
            $row['add_time']=local_date('Y-m-d H:i:s',$row['add_time']);
            $list[] = $row;
        }

        $arr = array('item' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
        return $arr;


    }
    //插入短信记录
    function set_sms_log($table,$arr){
        
        if(empty($arr)){
            return false;
        }
        if($table=='sms_log'){
            $array_key=array('mobile','value','number','add_time','status');
        }
        $key=implode(",",$array_key) ;
        $arrInsert=$this->generateInsert($arr);
        $insert_sql="insert INTO ".$this->ecs->table ($table)."(".$key.") "." VALUES ".$arrInsert;
        // echo $insert_sql;die;
        $res=$GLOBALS ['db']->query($insert_sql);
        return $res;        

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
    //调用相应的模版发送短信
    //$arr 为 数组时可以调用模板，发送相应的模板内容
    //$sms_id 为空时直接返回$arr内容
    function sms_content($arr,$sms_id){
        if(empty($arr)){
            return false;
        }
        if(empty($sms_id)){
            return $arr;
        }
        $sql = "SELECT * FROM " . $GLOBALS['ecs']->table('sms_content') ." WHERE id= $sms_id";
        $res = $GLOBALS['db']->getRow($sql);
        $msg=explode('@', $res['value']);
        $content='';
        foreach ($msg as $key => $value) {
            $content.=$value.$arr[$key];
        }
        return $content;        
    }

}

?>