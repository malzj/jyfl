<?php
class Fun{
     
    function isEmpty($val)
    {
        if (!is_string($val)) return false; //是否是字符串类型

        if (empty($val)) return false; //是否已设定

        if ($val=='') return false; //是否为空

        return true;
         
    }
    /*
     -----------------------------------------------------------
    函数名称：isNumber
    简要描述：检查输入的是否为数字
    输入：string
    输出：boolean
    修改日志：------
    -----------------------------------------------------------
    */
    function isNumber($val)
    {
        if(ereg("^[0-9]+$", $val))
            return true;
        return false;
    }
      
    /*
     -----------------------------------------------------------
    函数名称：isPhone
    简要描述：检查输入的是否为电话
    输入：string
    输出：boolean
    修改日志：------
    -----------------------------------------------------------
    */
    function isPhone($val)
    {
        //eg: xxx-xxxxxxxx-xxx | xxxx-xxxxxxx-xxx ...
        if(ereg("^((0\d{2,3})-)(\d{7,8})(-(\d{3,}))?$",$val))
            return true;
        return false;
    }

    /*
     -----------------------------------------------------------
    函数名称：isPostcode
    简要描述：检查输入的是否为邮编
    输入：string
    输出：boolean
    修改日志：------
    -----------------------------------------------------------
    */
    function isPostcode($val)
    {
        if(ereg("^[0-9]{4,6}$",$val))
            return true;
        return false;
    }
      
    /*
     -----------------------------------------------------------
    函数名称：isEmail
    简要描述：邮箱地址合法性检查
    输入：string
    输出：boolean
    修改日志：------
    -----------------------------------------------------------
    */
    function isEmail($val,$domain="")
    {
        if(!$domain)
        {
            if( preg_match("/^[a-z0-9-_.]+@[\da-z][\.\w-]+\.[a-z]{2,4}$/i", $val) )
            {
                return true;
            }
            else
                return false;
        }
        else
        {
            if( preg_match("/^[a-z0-9-_.]+@".$domain."$/i", $val) )
            {
                return true;
            }
            else
                return false;
        }
    }//end func
      
    /*
     -----------------------------------------------------------
    函数名称：isName
    简要描述：姓名昵称合法性检查，只能输入中文英文
    输入：string
    输出：boolean
    修改日志：------
    -----------------------------------------------------------
    */
    function isName($val)
    {
        if( preg_match("/^[\x80-\xffa-zA-Z0-9]{3,60}$/", $val) )//2008-7-24
        {
            return true;
        }
        return false;
    }//end func
      
     
    /*
     -----------------------------------------------------------
    函数名称:isStrLength($theelement, $min, $max)
    简要描述:检查字符串长度是否符合要求
    输入:mixed (字符串，最小长度，最大长度)
    输出:boolean
    修改日志:------
    -----------------------------------------------------------
    */
    function isStrLength($val, $min, $max)
    {
        $theelement= trim($val);
        if(ereg("^[a-zA-Z0-9]{".$min.",".$max."}$",$val))
            return true;
        return false;
    }
     
      
    /*
     -----------------------------------------------------------
    函数名称:isNumberLength($theelement, $min, $max)
    简要描述:检查字符串长度是否符合要求
    输入:mixed (字符串，最小长度，最大长度)
    输出:boolean
    修改日志:------
    -----------------------------------------------------------
    */
    function isNumLength($val, $min, $max)
    {
        $theelement= trim($val);
        if(ereg("^[0-9]{".$min.",".$max."}$",$val))
            return true;
        return false;
    }
      
    /*
     -----------------------------------------------------------
    函数名称:isNumberLength($theelement, $min, $max)
    简要描述:检查字符串长度是否符合要求
    输入:mixed (字符串，最小长度，最大长度)
    输出:boolean
    修改日志:------
    -----------------------------------------------------------
    */
    function isEngLength($val, $min, $max)
    {
        $theelement= trim($val);
        if(ereg("^[a-zA-Z]{".$min.",".$max."}$",$val))
            return true;
        return false;
    }
      
    /*
     -----------------------------------------------------------
    函数名称：isEnglish
    简要描述：检查输入是否为英文
    输入：string
    输出：boolean
    作者：------
    修改日志：------
    -----------------------------------------------------------
    */
    function isEnglish($theelement)
    {
        if( ereg("[\x80-\xff].",$theelement) )
        {
            Return false;
        }
        Return true;
    }
      
    /*
     -----------------------------------------------------------
    函数名称：isChinese
    简要描述：检查是否输入为汉字
    输入：string
    输出：boolean
    修改日志：------
    -----------------------------------------------------------
    */
    /*
    function isChinese($sInBuf)//有问题的函数
    {
        $iLen= strlen($sInBuf);
        for($i= 0; $i< $iLen; $i++)
        {
        if(ord($sInBuf{$i})>=0x80)
            {
            if( (ord($sInBuf{$i})>=0x81 && ord($sInBuf{$i})<=0xFE) && ((ord($sInBuf{$i+1})>=0x40 && ord($sInBuf{$i+1}) < 0x7E) || (ord($sInBuf{$i+1}) > 0x7E && ord($sInBuf{$i+1})<=0xFE)) )
            {
            if(ord($sInBuf{$i})>0xA0 && ord($sInBuf{$i})<0xAA)
                {
                    //有中文标点
                        return false;
            }
            }
            else
            {
            //有日文或其它文字
            return false;
            }
            $i++;
            }
                else
                    {
                        return false;
            }
            }
            return true;
        }*/
         
         
        function isChinese($sInBuf)//正确的函数
        {
            if (preg_match("/^[\x7f-\xff]+$/", $sInBuf)) { //兼容gb2312,utf-8
                 
                return true;
            }
            else
            {
                return false;
            }
        }
            /*
             -----------------------------------------------------------
            函数名称:isDomain($Domain)
            简要描述:检查一个（英文）域名是否合法
            输入:string 域名
            输出:boolean
            修改日志:------
            -----------------------------------------------------------
            */
            function isDomain($Domain)
            {
                if(!eregi("^[0-9a-z]+[0-9a-z\.-]+[0-9a-z]+$", $Domain))
                {
                    Return false;
                }
                if( !eregi("\.", $Domain))
                {
                    Return false;
                }
                  
                if(eregi("\-\.", $Domain) or eregi("\-\-", $Domain) or eregi("\.\.", $Domain) or eregi("\.\-", $Domain))
                {
                    Return false;
                }
                  
                $aDomain= explode(".",$Domain);
                if( !eregi("[a-zA-Z]",$aDomain[count($aDomain)-1]) )
                {
                    Return false;
                }
                  
                if(strlen($aDomain[0]) > 63 || strlen($aDomain[0]) < 1)
                {
                    Return false;
                }
                Return true;
            }
            /**
             * 验证是否日期的函数
             * @param unknown_type $date
             * @param unknown_type $format
             * @throws Exception
             * @return boolean
             */
            function validateDate( $date, $format='YYYY-MM-DD')
            {
                switch( $format )
                {
                    case 'YYYY/MM/DD':
                    case 'YYYY-MM-DD':
                        list( $y, $m, $d ) = preg_split( '/[-./ ]/', $date );
                        break;
             
                    case 'YYYY/DD/MM':
                    case 'YYYY-DD-MM':
                        list( $y, $d, $m ) = preg_split( '/[-./ ]/', $date );
                        break;
             
                    case 'DD-MM-YYYY':
                    case 'DD/MM/YYYY':
                        list( $d, $m, $y ) = preg_split( '/[-./ ]/', $date );
                        break;
             
                    case 'MM-DD-YYYY':
                    case 'MM/DD/YYYY':
                        list( $m, $d, $y ) = preg_split( '/[-./ ]/', $date );
                        break;
             
                    case 'YYYYMMDD':
                        $y = substr( $date, 0, 4 );
                        $m = substr( $date, 4, 2 );
                        $d = substr( $date, 6, 2 );
                        break;
             
                    case 'YYYYDDMM':
                        $y = substr( $date, 0, 4 );
                        $d = substr( $date, 4, 2 );
                        $m = substr( $date, 6, 2 );
                        break;
             
                    default:
                        throw new Exception( "Invalid Date Format" );
                }
                return checkdate( $m, $d, $y );
            }
             
             
            /*
            -----------------------------------------------------------
            函数名称：isDate
            简要描述：检查日期是否符合0000-00-00
                输入：string
                    输出：boolean
                    修改日志：------
                    -----------------------------------------------------------
                    */
                    function isDate($sDate)
                    {
                        if( ereg("^[0-9]{4}\-[][0-9]{2}\-[0-9]{2}$",$sDate) )
                        {
                        Return true;
            }
            else
            {
            Return false;
        }
        }
         /*
          -----------------------------------------------------------
          函数名称：isTime
          简要描述：检查日期是否符合0000-00-00 00:00:00
          输入：string
          输出：boolean
          修改日志：------
          -----------------------------------------------------------
          */
          function isTime($sTime)
          {
         if( ereg("^[0-9]{4}\-[][0-9]{2}\-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$",$sTime) )
            {
                Return true;
         }
            else
            {
            Return false;
         }
         }
           
         /*
         -----------------------------------------------------------
         函数名称:isMoney($val)
         简要描述:检查输入值是否为合法人民币格式
         输入:string
         输出:boolean
         修改日志:------
         -----------------------------------------------------------
         */
         function isMoney($val)
         {
         if(ereg("^[0-9]{1,}$", $val))
            return true;
         if( ereg("^[0-9]{1,}\.[0-9]{1,2}$", $val) )
            return true;
         return false;
        }
          
        /*
         -----------------------------------------------------------
        函数名称:isIp($val)
            简要描述:检查输入IP是否符合要求
            输入:string
                输出:boolean
                修改日志:------
                -----------------------------------------------------------
                */
                function isIp($val)
                {
                 return(bool) ip2long($val);
                }
                //-----------------------------------------------------------------------------
     
     
     
        /**
         * 验证手机号
         * @param int $mobile
         */
        function valid_mobile($mobile){
            if(strlen($mobile)!=11) return false;
            if(preg_match('/13[0-9]\d{8}|15[0|1|2|3|5|6|7|8|9]\d{8}|18[0|5|6|7|8|9]\d{8}|14[0|1|2|3|5|6|7|8|9]\d{8}|17[0|1|2|3|5|6|7|8|9]\d{8}/',$mobile)){
                return true;
            }else{
                return false;
            }
        }
         
        /**
         * 缩略图生成函数，最好使用GD2
         *
         * @param string $srcFile 要生成缩略图的文件
         * @param int $toW 缩略图宽度
         * @param int $toH 缩略图高度
         * @param string $toFile 缩略图文件
         */
        function ImageResize($srcFile,$toW,$toH,$toFile=""){
            if($toFile==""){ $toFile = $srcFile; }
            $info = "";
            $data = GetImageSize($srcFile,$info);
            switch ($data[2]){
                case 1:
                    if(!function_exists("imagecreatefromgif")){
                        //echo "你的GD库不能使用GIF格式的图片，请使用Jpeg或PNG格式！<a href='javascript:go(-1);'>返回</a>";
                        return false;
                    }
                    $im = ImageCreateFromGIF($srcFile);
                    break;
                case 2:
                    if(!function_exists("imagecreatefromjpeg")){
                        //echo "你的GD库不能使用jpeg格式的图片，请使用其它格式的图片！<a href='javascript:go(-1);'>返回</a>";
                        return false;
                    }
                    $im = ImageCreateFromJpeg($srcFile);
                    break;
                case 3:
                    $im = ImageCreateFromPNG($srcFile);
                    break;
            }
            $srcW=ImageSX($im);
            $srcH=ImageSY($im);
            $toWH=$toW/$toH;
            $srcWH=$srcW/$srcH;
            if($toWH<=$srcWH){
                $ftoW=$toW;
                $ftoH=$ftoW*($srcH/$srcW);
            }
            else{
                $ftoH=$toH;
                $ftoW=$ftoH*($srcW/$srcH);
            }
            if($srcW>$toW||$srcH>$toH)
            {
                if(function_exists("imagecreatetruecolor")){
                    @$ni = ImageCreateTrueColor($ftoW,$ftoH);
                    if($ni) ImageCopyResampled($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
                    else{
                        $ni=ImageCreate($ftoW,$ftoH);
                        ImageCopyResized($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
                    }
                }else{
                    $ni=ImageCreate($ftoW,$ftoH);
                    ImageCopyResized($ni,$im,0,0,0,0,$ftoW,$ftoH,$srcW,$srcH);
                }
                if(function_exists('imagejpeg')) ImageJpeg($ni,$toFile);
                else ImagePNG($ni,$toFile);
                ImageDestroy($ni);
            }else{
                ImageDestroy($im);
                return false;
            }
            ImageDestroy($im);
            return true;
        }
         
         
         
        //去除字符串空格
        static function strTrim($str)
        {
            return preg_replace("/\s/","",$str);
        }
         
        //验证用户名
        static function userName($str,$type,$len)
        {
            $str=self::strTrim($str);
            if($len<strlen($str))
            {
                return false;
            }else{
                switch($type)
                {
                    case "EN"://纯英文
                        if(preg_match("/^[a-zA-Z]+$/",$str))
                        {
                            return true;
                        }else{
                            return false;
                        }
                        break;
                    case "ENNUM"://英文数字
                        if(preg_match("/^[a-zA-Z0-9]+$/",$str))
                        {
                            return true;
                        }else{
                            return false;
                        }
                        break;
                    case "ALL":        //允许的符号(|-_字母数字)
                        if(preg_match("/^[\|\-\_a-zA-Z0-9]+$/",$str))
                        {
                            return true;
                        }else{
                            return false;
                        }
                        break;
                }
            }
        }
         
        //验证密码长度
        static function passWord($min,$max,$str)
        {
            $str=self::strTrim($str);
            if(strlen($str)>=$min && strlen($str)<=$max)
            {
                return true;
            }else{
                return false;
            }
        }
         
        //验证Email
        static function Email($str)
        {
            $str=self::strTrim($str);
              
            if(preg_match("/^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.){1,2}[a-z]{2,4}$/i",$str))
            {
                return true;
            }else{
                return false;
            }
              
        }
         
        //验证身份证(中国)
        static function idCard($str)
        {
            $str=self::strTrim($str);
            if(preg_match("/^([0-9]{15}|[0-9]{17}[0-9a-z])$/i",$str))
            {
                return true;
            }else{
                return false;
            }
        }
         
        //验证座机电话
        static function Phone($type,$str)
        {
            $str=self::strTrim($str);
            switch($type)
            {
                case "CHN":
                    if(preg_match("/^([0-9]{3}|0[0-9]{3})-[0-9]{7,8}$/",$str))
                    {
                        return true;
                    }else{
                        return false;
                    }
                    break;
                case "INT":
                    if(preg_match("/^[0-9]{4}-([0-9]{3}|0[0-9]{3})-[0-9]{7,8}$/",$str))
                    {
                        return true;
                    }else{
                        return false;
                    }
                    break;
            }
        }

        /**
         * 过滤二维数组的值
         * @param 二维数组 $arr_data
         * @param 一维数组 $field
         * @return Ambigous <multitype:, string, unknown>
         */
        function getarrayfield($arr_data, $field)
        {
            $resultArr = array();
            foreach ( $arr_data as $key => $value ) {
                foreach ($field as $k=>$v)
                {
                    if(array_key_exists($v, $value))//存在才添加
                    {
                        $resultArr[$key][$v] =$value[$v] ;
                    }
                    else
                    {
                        $resultArr[$key][$v]="不存在这个列";
                    }
                }
            }
                 
            return $resultArr;
        }
        /**
         * 获取客户端IP地址
         * @return ip
         */
        function get_client_ip(){
            if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
                $ip = getenv("HTTP_CLIENT_IP");
            else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
                $ip = getenv("HTTP_X_FORWARDED_FOR");
            else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
                $ip = getenv("REMOTE_ADDR");
            else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
                $ip = $_SERVER['REMOTE_ADDR'];
            else
                $ip = "unknown";
            return($ip);
        }
         
        function get_http_user_agent(){
            return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
        }
         
        /**
         * 从IP地址获取真实地址
         * @param IP $ip
         */
        function get_address_from_ip($ip){
            $url='http://www.youdao.com/smartresult-xml/search.s?type=ip&q=';
            $xml=file_get_contents($url.$ip);
            $data=simplexml_load_string($xml);
            return $data->product->location;
        }

         
        /**
         * 产生随机字串，可用来自动生成密码 默认长度6位 字母和数字混合
         * @param string $len 长度
         * @param string $type 字串类型
         * 0 字母 1 数字 2 大写字母 3 小写字母 默认混合 4中文
         * @param string $addChars 额外字符
         * @return string
         */
        function rand_string($len=6,$type='',$addChars='') {
            $str ='';
            switch($type) {
                case 0:
                    $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
                    break;
                case 1:
                    $chars= str_repeat('0123456789',3);
                    break;
                case 2:
                    $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
                    break;
                case 3:
                    $chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
                    break;
                case 4:
                    $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借".$addChars;
                    break;
                case 5:
                    $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'.$addChars;
                    break;
                default :
                    // 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
                    $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
                    break;
            }
            if($len>10 ) {//位数过长重复字符串一定次数
                $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
            }
         
            if($type == 4){ // 处理中文
                $chars = preg_replace('/[^\\x{4e00}-\\x{9fa5}]/u', '', $chars);     // //干掉非utf8中文字符
                $chars = chunk_split($chars,3,","); // //每隔3个字符插入一个“,”，转换为数组使用，使用strlen()测出php中一个中文霸占了3个
                $re = explode(",",$chars);
                shuffle($re);//随机重新排序数组
                $chars = implode($re);
                unset($re);
                $str     =   mb_substr($chars,0,$len,"utf-8");
            }else{
                $chars   =   str_shuffle($chars);
                $str     =   substr($chars,0,$len);
            }
         
            return $str;
        }
         
        //获取url中参数的值
        function geturlval($url,$name)
        {
            $arr = parse_url($url);
            $arr_query = $this->convertUrlQuery($arr['query']);
                 
            return $arr_query[$name];
         
        }
        function convertUrlQuery($query)
        {
            $queryParts = explode('&', $query);
         
            $params = array();
            foreach ($queryParts as $param)
            {
                $item = explode('=', $param);
                $params[$item[0]] = $item[1];
            }
         
            return $params;
        }
        /**
         * 抓取远程图片
         *
         * @param string $url 远程图片路径
         * @param string $filename 本地存储文件名
         */
        function grabImage($url, $savepath) {
            if($url =="") {
                return false; //如果 $url 为空则返回 false;
            }
            $ext_name = strrchr($url, '.'); //获取图片的扩展名
            if($ext_name != '.gif' && $ext_name != '.jpg' && $ext_name != '.bmp' && $ext_name != '.png') {
                return false; //格式不在允许的范围
            }
            //获取原图片名
            $filename = $savepath.'\\'.end(explode('/',$url));
            //开始捕获
            ob_start();
            readfile($url);
            $img_data = ob_get_contents();
            ob_end_clean();
            $size = strlen($img_data);
            $local_file = fopen($filename , 'a');
            echo $filename;
            if(fwrite($local_file, $img_data)== FALSE){
                echo '图片下载失败';
            }
            fclose($local_file);
            return $filename;
        }

}
?>