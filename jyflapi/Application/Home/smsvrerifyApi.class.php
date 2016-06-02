<?php
namespace Home;

/* Version 0.9, 6th April 2003 - Simon Willison ( http://simon.incutio.com/ )
   Manual: http://scripts.incutio.com/httpclient/
*/


/**
 * 短信的第三方调用接口
 */
class smsvrerifyApi{
    function smsvrerify($mobile,$verify,$start){
        // var_dump($mobile,$verify);
        $params='';//要post的数据
        //$start=1,发送随机数，$start=0,发送制定内容
        if($start==1){
            $content='短信验证码为：'.$verify.'，请勿将验证码提供给他人。(验证码30分钟内有效！)';
        }else{
            $content=$verify;//需要发送的内容,content：(发送内容（1-500 个汉字）UTF-8 编码)(必填参数)；
        }
        //以下信息自己填以下
        //$mobile='';//手机号
        // return 0;
        // die;
        $argv = array( 
            'name'=>'15321431385',     //必填参数。用户账号
            'pwd'=>'67A53170ED8C9F6A62C59DE38F26',     //必填参数。（web平台：基本资料中的接口密码）
            'content'=>$content,   //必填参数。发送内容（1-500 个汉字）UTF-8编码
            'mobile'=>$mobile,   //必填参数。手机号码。多个以英文逗号隔开
            'stime'=>'',   //可选参数。发送时间，填写时已填写的时间发送，不填时为当前时间发送
            'sign'=>'华影文化',    //必填参数。用户签名。
            'type'=>'pt',  //必填参数。固定值 pt
            'extno'=>''    //可选参数，扩展码，用户定义扩展码，只能为数字
        ); 
        //print_r($argv);exit;
        //构造要post的字符串 
        //echo $argv['content'];
        $flag = 0; 
        foreach ($argv as $key=>$value) { 
            if ($flag!=0) { 
                $params .= "&"; 
                $flag = 1; 
            } 
            $params.= $key."="; $params.= urlencode($value);// urlencode($value); 
            $flag = 1; 
        } 

    //      http://web.cr6868.com/asmx/smsservice.aspx?name=test&pwd=112345&cont
    // ent=testmsg&mobile=13566677777,18655555555&stime=2012-08-01
    // 8:20:23&sign=testsign&type=pt&extno=123
        //同一个号码，1 分钟/1 次，1 小时/5 次，超过可能拦截禁发；
        $url = "http://web.cr6868.com/asmx/smsservice.aspx?".$params; //提交的url地址

        //0,20130821110353234137876543,0,500,0,提交成功
        //依次为：状态、发送编号、,无效号码数、成功提交数、黑名单数、消息
        $data=$this->curl_file_get_contents($url);
        echo substr($data, 0, 1 );
    }
    function curl_file_get_contents($durl){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $durl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
        curl_setopt($ch, CURLOPT_REFERER,_REFERER_);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $r = curl_exec($ch);
        curl_close($ch);
        return $r;
    }

}
?>