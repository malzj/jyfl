<?php
namespace Home;

/* Version 0.9, 6th April 2003 - Simon Willison ( http://simon.incutio.com/ )
   Manual: http://scripts.incutio.com/httpclient/
*/
class Huayingcard {
    
    // wsdl 地址
    //public $wsdl = 'http://210.75.200.74:17020/services/InterFaceService.asmx?wsdl';
    public $wsdl = 'http://1.93.129.186:8089/InterFaceService.asmx?wsdl';

    // 编码
    protected $coding = 'UTF-8';
    
    // 验证账号
    protected $userName = 'admin';
    
    // 验证密码
    protected $userPad = 'admin123456';
    
    // 中影卡号段
    protected $zycardNo = array( 999011, 999013 );
    
    // 支付类型 （0：未识别，1：华影卡，2：中影卡） 未识别的原因可能是用户未登录
    protected $cardType = 0;
    
    // php soapClient 类
    protected $client;
    
    // 结果集
    protected $result;
    
    // 状态提示
    protected $message = NULL;

    // des 加密/解密
    protected $open3Des = true;
//    protected $open3Des = true;
    protected $desIV = 'ZS@zzOrc';
    protected $desKey = 'XAdsAxxs';

    /** 获取支付类型   0：未识别，1：华影卡，2：中影卡） 未识别的原因可能是用户未登录
     *  @author guoyunpeng
     */
    public function getCardType()
    {   
        return $this->cardType;
    }
    
    /**
     *  获取中影卡号段
     */
    public function getzycardNo()
    {
        return $this->zycardNo;
    }
    
    /**
     *  获取状态信息
     *  @author guoyunpeng
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    /**
     *  获取结果集
     */
    public function getResult()
    {
        return $this->result;   
    }
    
    /** 卡执行操作
     *  @author guoyunpeng
     *  @param  $param      array       业务参数
     *  @param  $ext        int         操作号  1.卡消费  2.修改卡密码  3.挂失  4.解挂  5.交易信息查询  6.卡充值  7.消费红冲  8.卡基本信息 9.退款 10.余额转移
     *  @param  $orderid    str         流水号（中影卡系统才支持）
     *  @return             int         返回状态码（0 ：成功、1：失败、1024：卡系统不识别）
     */
    public function action( $param, $ext, $orderid=null)
    {
        // 每一次执行 aciton 的时候就初始化一些信息，目的是清空上个接口执行时的缓存数据。
        $this->_init();
        
        // 通过传递的卡号，选择支付的平台（中影卡系统、华影卡系统）
        if ( !empty($param['CardInfo']['CardNo']) )
        {
            $this->_setCardType($param['CardInfo']['CardNo']);
        }
        // 華影卡余额转移，得到卡行的方式是 OldCardNo
        if ( !empty($param['CardInfo']['OldCardNo']) )
        {
            $this->_setCardType($param['CardInfo']['OldCardNo']);
        }
        
        // 调度支付方法
        switch ($this->cardType)
        {
            // 华影卡支付
            case '1':               return $this->_huayingCard( $param, $ext );             break;
            // 中影卡支付
            case '2':               return $this->_zhongyingCard( $param, $ext, $orderid);  break;
            // 返回错误码
            default:                return $this->_actionError(1);                          break;
        }
    }
    
    // 华影卡支付
    protected function _huayingCard( $param, $ext)
    {
        $no = 0;
        // xml头
        $strXml  = '<?xml version="1.0" encoding="UTF-8"?><Msg>';
        // 验证头
        $strXml .= $this->_headerXml( $ext );
        // 流程参数
        $strXml .= $this->_stringParams( $param );
        // 结束标签
        $strXml .='</Msg>';

        if ($this->open3Des === true)
        {
            $strXml = $this->_crypt3Des('encrypt', $strXml);
        }

        // 执行操作
        $result = $this->client->__soapCall('OperationInterface',array(array('Xml'=>$strXml)));
        $obj_xmlRoot = simplexml_load_string($result->OperationInterfaceResult, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
        $resultData = @json_decode(@json_encode($obj_xmlRoot), 1);

        foreach ( $resultData as $key=>$val)
        {
            switch ( $key )
            {
                // 状态信息
                case 'ResultInfo':
                    $no = $val['Flag'] 	== 1 ? 0 : 1 ;
                    $this->message 		= $val['MessageContent'];
                    // 消费和充值时的订单号，放到结果集里
                    $this->result 		= isset($val['OrderNumber']) ? $val['OrderNumber'] : NULL;
                    break;
                // 卡交易内容
                case 'TransationInfoList':
                    $this->result 		= $val['TransationInfo'];
                    break;
                // 卡信息内容
                case 'CardInfoList':
                    $this->result		= $val['CardInfo'];
                    break;
            }
        }

        return $no;
    }
    
    /** 中影卡支付
     * @param unknown_type $param
     * @param unknown_type $ext
     * @return Ambigous <multitype:, array, boolean>
     */
    protected function _zhongyingCard( $param, $ext, $orderid)
    {   
        $cardInfo = array();
        // 华影卡参数转换成中影的参数
        $newParam = $this->_h2zparam($param, $ext, $orderid);       
        switch ($ext)
        {
            // 卡消费
            case 1:     $cardInfo = $this->_zhongyingApi('CARD-PAY', $newParam);    break;
            // 密码修改     
            case 2:     $cardInfo = $this->_zhongyingApi('CARD-MODIFY-PWD', $newParam); break;
            // 卡充值 / 退款
            case 6:
            case 7:
            case 9:     
                        $cardInfo = $this->_recharge($newParam);    break;
            // 卡信息
            case 8:     $cardInfo = $this->_zhongyingApi('CARD-INFO', $newParam); break;
            // 余额转移
            case 10:    $cardInfo = $this->_zhongyingzy($newParam);
                    
                            
        }
        
        return $cardInfo;
    }
    
    /** des 字符串加密/解密
     *  @param  $crypt      str     加密/解密 ( encrypt/decrypt )
     *  @param  $str        str     加密/解密的字符串
     */
    protected function _crypt3Des( $crypt, $str)
    {
        $newStr = array();
        // include_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'httpRequest.php');
        $crypt3Des = new Crypt3Des($this->desKey, $this->desIV);
        if ( $this->open3Des !== true)
            return $str;
    
        if ($crypt === 'encrypt')
            $newStr = $crypt3Des->encrypt($str);
        else
            $newStr = $crypt3Des->decrypt($str);
    
        return $newStr;
            
    }
    
    
    /** 中影卡接口操作
     *  @param  $function   fun     执行的方法
     *  @param  $param      array   业务参数
     *
     *  @author guoyunepng
     */
    protected function _zhongyingApi($function, $param)
    {
        $no = 0;
        $resultArray = array();
        include_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'lib_cardApi.php');
    
        switch ($function)
        {
            // 消费
            case 'CARD-PAY':            $resultArray = getCardApi($param, 'CARD-PAY', 7);   break;
            // 密码修改
            case 'CARD-MODIFY-PWD':     $resultArray = getCardApi($param, 'CARD-MODIFY-PWD', 7);    break;
            // 卡充值
            case 'CARD-RECHARGE':       $resultArray = getCardApi($param, 'CARD-RECHARGE', 7);  break;
            // 卡充值审核
            case 'CARD-AUTH-RECHARGE':  $resultArray = getCardApi($param, 'CARD-AUTH-RECHARGE', 7);     break;
            // 卡信息
            case 'CARD-INFO':           $resultArray = getCardApi($param, 'CARD-INFO', 7);  break;
        }
        
        if ($resultArray['ReturnCode'] == '0')
        {
            $this->result = $resultArray;
            $this->message = $resultArray['ReturnMessage'];
        }
        else 
        {
            $no = 1;
            $this->message = $resultArray['ReturnMessage'];
        }
    
        return $no;
    }
    
    /** 中影卡充值操作
     * @param unknown_type $param
     * @return unknown|Ambigous <multitype:, array, boolean>
     */
    protected function _recharge( $param )
    {
        $no = 0;
        // 卡充值
        if ($this->_zhongyingApi('CARD-RECHARGE', $param) == 0)
        {
            // 审核充值
            $arr_param = array( 'orderId'=> $this->result['OrderId'], 'operId'=> $GLOBALS['_CFG']['operId'], 'extendInfo' => '');
            if ($this->_zhongyingApi('CARD-AUTH-RECHARGE', $arr_param) == 1)
            {
                $no = 1;
                $this->message = $this->result['ReturnMessage'];
            }
        }
        else
        {
            $no = 1;
        }
        
        return $no;
            
    }

    /**
     * 中影卡余额转移
     */
    protected function _zhongyingzy( $param )
    {       
        include_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'lib_time.php');
        
        $no = 0;
        // 查询卡余额
        if ($this->_zhongyingCard( array( 'CardInfo' => array('CardNo'=> $param['OldCardNo'], 'CardPwd'=>$param['OldPwd'])), 8) == 0)
        {
            $card_result = $this->getResult();
        }
        else {
            return 1;
        }       
        
        $OldParam = array( 
                'CardInfo' => array('CardNo'=> $param['OldCardNo'], 'CardPwd'=>$param['OldPwd']),
                'TransationInfo' => array('TransRequestPoints'=> floatval($card_result['BalanceCash']))
        );
        $DesParam = array(
                'CardInfo' => array('CardNo'=> $param['DesCardNo'], 'CardPwd'=>$param['DesPwd']),
                'TransationInfo' => array('TransRequestPoints'=> floatval($card_result['BalanceCash']))
        );
        // 消费参数
        $newOldParam = $this->_h2zparam($OldParam, 1);
        // 充值参数
        $newDesParam = $this->_h2zparam($DesParam, 6);
        
        // 扣除原卡点数成功后，执行充值操作
        if ( $this->_zhongyingApi('CARD-PAY', $newOldParam) == 0)
        {
            $logMsg .= "[卡号] ".$param['OldCardNo']." ～ [状态] 已消费  ～ [时间] ".local_date('Y-m-d H:i:s', gmtime())." \r\n";
            error_log($logMsg,3,'temp/card_merge/message_'.date('Ym',time()).'.log');
            
            $no = $this->_recharge($newDesParam) == 0 ? 0 : 2 ;
        }
        else{
            $no = 1;
        }
        
        return $no;
    }
    
    /**
     *  华影卡参数转成中影卡参数，统一接口调用
     *  
     */
    protected function _h2zparam( $param, $ext, $orderid)
    {
        $newParam = array();
        include_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'lib_order.php');
        include_once (dirname(__FILE__).DIRECTORY_SEPARATOR.'lib_time.php');            
        switch ($ext)
        {
            // 消费
            case '1':
                $newParam['cardId']     = $param['CardInfo']['CardNo'];
                $newParam['cardPwd']    = $param['CardInfo']['CardPwd'];
                $newParam['posJournal'] = $orderid==null ? get_order_sn() : $orderid ;
                $newParam['payTime']    = local_date('YmdHis');
                $newParam['payAmount']  = $param['TransationInfo']['TransRequestPoints'];
                $newParam['deviceId']   = $GLOBALS['_CFG']['deviceId'];
                $newParam['operId']     = $GLOBALS['_CFG']['operId'];
                $newParam['merchantId'] = $GLOBALS['_CFG']['merchantId'];
                $newParam['storeId']    = $GLOBALS['_CFG']['storeId'];              
                break;
            // 充值
            case '6':
            case '7':
            case '9':
                $newParam['cardSeq']   = $param['CardInfo']['CardNo'];//卡序号
                $newParam['orderType'] = 1;//1，单卡充值，2，批量充值
                $newParam['operId']    = $GLOBALS['_CFG']['operId'];//充值操作员(自助终端传终端编号)
                $newParam['cardNum']   = 1;//充值卡数量
                $newParam['saleId']    = $GLOBALS['_CFG']['saleId'];//售卡机构编号
                $newParam['timeStamp'] = local_date('YmdHis');//时间戳
                $newParam['singleSaveAmount'] = $param['TransationInfo']['TransRequestPoints'];//单张充值金额
                $newParam['singleRealAmount'] = $param['TransationInfo']['TransRequestPoints'];//单张实收金额
                break;          
            // 修改密码
            case '2':
                $newParam['cardId']  = $param['CardInfo']['CardNo'];
                $newParam['cardPwd'] = $param['CardInfo']['CardPwd'];
                $newParam['newPwd']  = $param['CardInfo']['CardNewPwd'];
                break;
            // 卡信息
            case '8':
                $newParam['cardId']  = $param['CardInfo']['CardNo'];
                $newParam['cardPwd'] = $param['CardInfo']['CardPwd'];
                break;
            // 余额转移
            case '10':
                $newParam['OldCardNo']  = $param['CardInfo']['OldCardNo'];
                $newParam['OldPwd']     = $param['CardInfo']['OldPwd'];
                $newParam['DesCardNo']  = $param['CardInfo']['DesCardNo'];
                $newParam['DesPwd']     = $param['CardInfo']['DesPwd'];
                break;
        }
        
        return $newParam;
    }
    // 返回错误消息
    protected function _actionError( $no )
    {       
        $errorNo = 0;
        switch ($no)
        {
            // 不识别卡系统
            case '1':       
                $this->message = "无法识别你的卡类型，可能你已退出登录！";     
                $errorNo = 1024;
                break;  
                
        }       
        return $errorNo;
    }
    
    // 华影卡支付的头
    protected function _headerXml( $ext )
    {
        return '<MsgHeader>
                    <Authorization>
                        <UserName>'.$this->userName.'</UserName>
                        <UserPwd>'.$this->userPad.'</UserPwd>
                        <Type>'.$ext.'</Type>
                    </Authorization>
            </MsgHeader>';
    }
    
    // 数组转成xml格式的字符串
    protected function _stringParams( $param )
    {
        $string = '<MsgBody>';
        
        if (empty($param))  return $string;
        
        foreach ( $param as $key=>$val)
        {
            if (is_array($val))
            {
                $string .= '<'.$key.'>';
                
                foreach($val as $k=>$v)
                {
                    $string .= "<".$k.">".$v."</".$k.">";
                }
                
                $string .= '</'.$key.'>';
            }
        }
        
        $string .="</MsgBody>";
        
        return $string;
    }
    
    
    // 设置支付类型
    protected function _setCardType($name)
    {
        if ( in_array(substr($name, 0,6), $this->zycardNo) )
        {                   
            $this->cardType = 2;
        }
        else
        {

            $this->cardType = 1;
            $this->client = new \SoapClient($this->wsdl);
            // vendor('soap.class#nusoap');
            // $client = new nusoap_client($this->wsdl);
            $this->client->soap_defencoding = $this->coding;
            $this->client->xml_encoding = $this->coding;
        }
    }
    
    // 初始化一些数据
    protected function _init()
    {
        // 清空数据集
        $this->result       = array();
        // 清空消息
        $this->message      = NULL;
        // 支付类型
        $this->cardType     = 0;
        
    }
    
    
}



class Crypt3Des {
    var $key;
    var $iv;
    function __construct($key,$iv=null){
        $this->key = $key;
        $this->iv = $iv;



    }

    function encrypt($input){

        $size = mcrypt_get_block_size(MCRYPT_3DES,'ecb');
        $input = $this->pkcs5_pad($input, $size);
        $key = str_pad($this->key,24,'0');
        if ($this->iv == null){
            $td = mcrypt_module_open(MCRYPT_3DES, '', 'ecb', '');
        }else{
            $td = mcrypt_module_open(MCRYPT_3DES, '', 'cbc', '');
        }
        @mcrypt_generic_init($td, $key, $this->iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        //$data = base64_encode($this->PaddingPKCS7($data));
        $data = base64_encode($data);
        return $data;
    }

    function decrypt($encrypted){
        $encrypted = base64_decode($encrypted);
        $key = str_pad($this->key,24,'0');

        if ($this->iv == null){
            $td = mcrypt_module_open(MCRYPT_3DES,'','ecb','');
        }else{
            $td = mcrypt_module_open(MCRYPT_3DES, '', 'cbc', '');
        }
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $this->iv);
        $decrypted = mdecrypt_generic($td, $encrypted);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $y=$this->pkcs5_unpad($decrypted);
        return $y;
    }

    function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    function pkcs5_unpad($text){
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad){
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }

    function PaddingPKCS7($data) {
        $block_size = mcrypt_get_block_size(MCRYPT_3DES, MCRYPT_MODE_CBC);
        $padding_char = $block_size - (strlen($data) % $block_size);
        $data .= str_repeat(chr($padding_char),$padding_char);
        return $data;
    }
}

?>