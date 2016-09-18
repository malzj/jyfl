<?php

function getHQCapi( $param, $action)
{

    if ( empty($action)) return array();

    /*  $str_apiUrl     = 'http://ota.smartoct.com/ota/httpApi/xml';//接口地址
     $str_sUser      = 'distri546';// OTA编号（渠道id）
    $str_sKey       = 'MECMFUULBQBXCFSLV8H77ROS';//OTA密钥  */

    $str_apiUrl     = 'http://112.74.165.142:8083/sc-ota-war/httpApi/xml ';//接口地址
    $str_sUser      = 'distri546';// OTA编号（渠道id）
    $str_sKey       = 'MECMFUULBQBXCFSLV8H77ROS';//OTA密钥

    require_once(ROOT_PATH . 'includes/httpRequest.php');
    $http = new HttpRequest;

    require_once(ROOT_PATH . 'includes/des.class.php');
    $des = new DES($str_sKey);
    // 当前时间
    $timeStamp = local_date('Y-m-d H:i:s');
    // 序列号
    $sequenceId = local_date('YmdHis').mt_rand(10000000, 99999999);
    // 消息签名
    $bodys = getBodys($param);
    $Signed = base64_encode(md5($sequenceId.$str_sUser.mb_strlen($bodys, 'UTF-8')));

    // 消息头
    $str_xml = "xmlContent=<?xml version='1.0' encoding='UTF-8'?>
    <OTM>
    <Head>
    <Version>2.1.0</Version>
    <SequenceId>$sequenceId</SequenceId>
    <TimeStamp>$timeStamp</TimeStamp>
    <DistributorId>$str_sUser</DistributorId>";

    // 交易码
    switch ( $action )
    {
        // 景区详情
        case 'scenery':      $str_xml .= "<TransactionCode>01</TransactionCode><Signed>$Signed</Signed></Head><Body>";    break;
        // 景区商品
        case 'goods':        $str_xml .= "<TransactionCode>02</TransactionCode><Signed>$Signed</Signed></Head><Body>";    break;
        // 预付款订单确认
        case 'confirm':      $str_xml .= "<TransactionCode>03</TransactionCode><Signed>$Signed</Signed></Head><Body>";    break;
        // 订单详情
        case 'detail':       $str_xml .= "<TransactionCode>05</TransactionCode><Signed>$Signed</Signed></Head><Body>";    break;
        // 短信凭证重发
        case 'resend':       $str_xml .= "<TransactionCode>08</TransactionCode><Signed>$Signed</Signed></Head><Body>";    break;
        // 订单退款
        case 'refund':       $str_xml .= "<TransactionCode>09</TransactionCode><Signed>$Signed</Signed></Head><Body>";    break;
    }

    // 加密消息体
    $desBody = $des->encrypt($bodys);
    $str_xml .= '<![CDATA['.$desBody.']]>';
    // 结尾
    $str_xml .="</Body></OTM>";

    $result = $http->post($str_apiUrl, $str_xml, 'xml', 'curl', 'application/x-www-form-urlencoded');

    if((int)$result['otm']['head']['statuscode'] == 200 &&  (string)$result['otm']['head']['message'] == 'SUCCESS'){
        $hqc_postData_body = object2array(simplexml_load_string($des->decrypt($result['otm']['body'])));
         
        return $hqc_postData_body;
    }else{
        return (int)$result['otm']['head']['statuscode'].'|'.(string)$result['otm']['head']['message'];
    }
}

function getBodys( $param=array() )
{
    $str_xml = "<?xml version='1.0' encoding='UTF-8'?><Body>";
    if ( !empty($param) )
    {
        foreach ($param as $key=>$val)
        {
            $str_xml .=('<'.$key.'>'.$val.'</'.$key.'>');;
        }
    }
    $str_xml .= '</Body>';

    return $str_xml;
}

//卡接口方法
function getCardApi($arr_param, $tradeId, $dataSource = '', $method = 'post'){
	if (empty($arr_param) || empty($tradeId)) return array();
		if($tradeId=="VOUCHER-MODIFY"||$tradeId=="VOUCHER-INFO"){
			$str_apiUrl     = "http://card.douyou100.com:9004/scp/voucherService";//接口地址
		}else{
			$str_apiUrl     = $GLOBALS['_CFG']['apiurl'];//接口地址
		}
	$str_sKey       = $GLOBALS['_CFG']['skey'];  //接口密钥
	$int_dataSource = $dataSource ? $dataSource : intval($GLOBALS['_CFG']['dsource']);//请求来源
	$str_saleId     = $GLOBALS['_CFG']['saleId'];//售卡机构编码
	
	require_once(ROOT_PATH . 'includes/httpRequest.php');
	$http = new HttpRequest;
	$des = new Crypt3Des($str_sKey);

	$arr_data = array(
		'DataSource' => $int_dataSource,
		'TradeId'    => $tradeId,
	);

	$str_xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$str_questXml = $str_xml.
		'<message>
			<DataSource>'.$int_dataSource.'</DataSource>
			<TradeId>'.$tradeId.'</TradeId>
			<Signatures>{SIGN}</Signatures>
			<BusinessData>{DATA}</BusinessData>
		</message>';

	switch ($tradeId){
		//卡信息查询
		case 'VOUCHER-INFO':
			//业务数据xml
			$str_xml .= '
			<QueryVoucherInfo>
				<voucherId>'.$arr_param['voucherId'].'</voucherId>
				<checkPwd>1</checkPwd>
				<password>'.$arr_param['password'].'</password>
			</QueryVoucherInfo>';
			
			$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;		
			//8.9凭证修改
		case 'VOUCHER-MODIFY':
			//业务数据xml

			$str_xml .= '<VoucherModify>';
			foreach ($arr_param as $key=>$var){
				if($var!=''){
					$str_xml .= ('<'.$key.'>'.$var.'</'.$key.'>');
				}
			}
			$str_xml .= '</VoucherModify>';
/*
参数名	必/可选	类型	长度	参数说明	约束
voucherId	必选	字符	32	凭证号	
payType	必选	字符	1	消费类型	1：现金卡
2：点卡
3：电影兑换卡（次卡）
4：通兑票
5：现金卷
6：兑换券
7：点劵
serviceType	必选	字符	1	操作类型	1：冻结 2：延期 3：作废
expDate	可选	字符	8	有效截止日期	延期必选，yyyyMMdd
tradeJournal	必选	字符		交易流水	业务系统保证唯一
extendInfo	可选	字符		接口扩展字段信息
http://ip:port/scp/voucherService
http://card.douyou100.com:9004/scp/oldCardService	
*/ 
// echo $str_saleId;die;
		$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;
		//卡信息查询
		case 'CARD-INFO':
			//业务数据xml
			$str_xml .= '
			<CardQueryInfo>
				<cardId>'.$arr_param['cardId'].'</cardId>
				<cardPwd>'.$arr_param['cardPwd'].'</cardPwd>
			</CardQueryInfo>';
			
			$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;
		//修改卡密码
		case 'CARD-MODIFY-PWD':
			//业务数据xml
			/*$str_xml .= '
			<CardModifyPwd>
				<cardId>'.$arr_param['cardId'].'</cardId>
				<cardPwd>'.$arr_param['cardPwd'].'</cardPwd>
				<newPwd>'.$arr_param['newPwd'].'</newPwd>
			</CardModifyPwd>';*/
			$str_xml .= '<CardModifyPwd>';
			foreach ($arr_param as $key=>$var){
				$str_xml .= ('<'.$key.'>'.$var.'</'.$key.'>');
			}
			$str_xml .= '</CardModifyPwd>';

			$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;
		//卡消费
		case 'CARD-PAY':
			/*$str_xml .= '
			<CardPay>
				<cardId>'.$arr_param['cardId'].'</cardId>
				<cardPwd>'.$arr_param['cardPwd'].'</cardPwd>
				<posJournal>'.$arr_param['posJournal'].'</posJournal>
				<payTime>'.$arr_param['payTime'].'</payTime>
				<payAmount>'.$arr_param['payAmount'].'</payAmount>
				<deviceId>'.$arr_param['deviceId'].'</deviceId>
				<operId>'.$arr_param['cardId'].'</operId>
				<merchantId>'.$arr_param['cardId'].'</merchantId>
				<goodName>'.$arr_param['cardId'].'</goodName>
				<price>'.$arr_param['cardId'].'</price>
				<storeId>'.$arr_param['cardId'].'</storeId>
				<cardTrack>'.$arr_param['cardId'].'</cardTrack>
				<batchNumber>'.$arr_param['cardId'].'</batchNumber>
				<serialNumber>'.$arr_param['cardId'].'</serialNumber>
				<productType>'.$arr_param['cardId'].'</productType>
				<extendInfo>'.$arr_param['cardId'].'</extendInfo>
			</CardPay>';*/

			//业务数据xml
			$str_xml .= '<CardPay>';
			foreach ($arr_param as $key=>$var){
				$str_xml .= ('<'.$key.'>'.$var.'</'.$key.'>');
			}
			$str_xml .= '</CardPay>';

			$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;
		//卡消费撤销
		case 'CARD-PAY-ROLLBACK':
			//业务数据xml
			$str_xml .= '<CardPayRollback>';
			foreach ($arr_param as $key=>$var){
				$str_xml .= ('<'.$key.'>'.$var.'</'.$key.'>');
			}
			$str_xml .= '</CardPayRollback>';

			$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;
		//卡充值
		case 'CARD-RECHARGE':
			//业务数据xml
			$str_xml .= '<CardRecharge>';
			foreach ($arr_param as $key=>$var){
				$str_xml .= ('<'.$key.'>'.$var.'</'.$key.'>');
			}
			$str_xml .= '</CardRecharge>';

			$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;
		//卡充值审核
		case 'CARD-AUTH-RECHARGE':
			//业务数据xml
			$str_xml .= '<CardRecharge>';
			foreach ($arr_param as $key=>$var){
				$str_xml .= ('<'.$key.'>'.$var.'</'.$key.'>');
			}
			$str_xml .= '</CardRecharge>';

			$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;
	}

	//数字签名
	$str_sign = md5($str_businessData.local_date('Ymd').$str_sKey);
	//组成请求报文
	$str_questXml = str_replace(array('{SIGN}', '{DATA}'), array($str_sign, $str_businessData), $str_questXml);

	//发送post请求
	$result = $http->post($str_apiUrl, $str_questXml, 'xml', 'fopen');
	$str_businessdata = $des->decrypt($result['message']['businessdata']);

	if (!empty($str_businessdata)){
		$arr_res = simplexml_load_string($str_businessdata);
		return (array)$arr_res;
	}
	return false;
}


//影院接口方法
function getYYApi($arr_param, $tradaId, $method = 'post'){
	if (empty($tradaId)) return array();

	$str_apiUrl     = $GLOBALS['_CFG']['yyapiurl'];//接口地址
	$str_sUser      = $GLOBALS['_CFG']['yyappUser'];//接口客户端编码
	$str_sKey       = $GLOBALS['_CFG']['yyappKey'];//接口客户端密码
	
	require_once(ROOT_PATH . 'includes/httpRequest.php');
	$http = new HttpRequest;

	$int_curtime = local_date('YmdHis');
	$str_xml = '<mopon>
					<head>
						<appKey>'.$str_sUser.'</appKey>
						<valiCode>'.md5($int_curtime.$str_sKey).'</valiCode>
						<timeStamp>'.$int_curtime.'</timeStamp>
						<tradaId>'.$tradaId.'</tradaId>
					</head>
					<body>';
	switch ($tradaId){
		case 'getAreaList':
			//区域列表
		case 'getHotFilms':
			//热映电影
		case 'getComingFilms':
			//即将上映
		default :
			if (!empty($arr_param)){
					foreach ($arr_param as $key=>$var){
					$str_xml .= ('<'.$key.'>'.$var.'</'.$key.'>');
				}
			}
			break;
	}
	
	$str_xml .= '</body></mopon>';
	$str_questXml = 'param='.$str_xml;
	//发送post请求
//var_dump($str_questXml);
	$result = $http->post($str_apiUrl, $str_questXml, '', 'fopen');
	if (!empty($result)){
		$obj_xmlRoot = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
		if (!empty($obj_xmlRoot->body->item)){
			$int_itemCount = count($obj_xmlRoot->body->item);
		}
		$arr_result = array();
		$arr_result = object2array($obj_xmlRoot);
		if ($arr_result['body']['item'] && $int_itemCount == 1){
			$arr_tempItem = $arr_result['body']['item'];
			unset($arr_result['body']['item']);
			$arr_result['body']['item'][0] = $arr_tempItem;
		}

		//$obj_xmlRoot = (array)$obj_xmlRoot;
		/*foreach ($obj_xmlRoot as $key=>$var){
			$var = (array) $var;
			if (!empty($var['item'])){
				foreach ($var['item'] as $k=>$v){
					//$var['item'][$k] = (array) $v;
					$v = (array)$v;
					foreach ($v as $ck => $cv){
						if (is_object($cv)){
							$v[$ck] = (string)$cv;
						}
					}
					$var['item'][$k] = $v;
				}
			}else{
				foreach ($var as $k=>$v){
					if (is_object($v)){
						$v = (array) $v;
					}
					$var[$k] = $v;
				}
			}
			$arr_result[$key] = $var;
		}
		unset($obj_xmlRoot);*/
		return $arr_result;
	}
	return false;
}

function getZYapi( $method, $param=array(), $dateType = 'json' )
{
    $str_apiurl = 'http://cs.mopon.cn:7052/api/ticket/v2?';
    $str_apiuser = '0004';                 // 渠道id
    $str_apikey = 'dx3sdxBI4i2beF5J';      // key
    
    $default = array(
        'channelCode'   => $str_apiuser        
    );
    
    require_once(ROOT_PATH . 'includes/httpRequest.php');
    $http = new HttpRequest;
    
    // 合并公共参数和用户请求参数
    $default = array_merge($default, $param);
    // 生成签名
    ksort($default);
    $str_url = urlencode($http->buildUrlQuery($default));
    $default = array_merge( $default, array('sign'=>md5($str_apikey.$str_url.$str_apikey), 'method'=> $method.'.'.$dateType));
    $result = $http->post($str_apiurl, $default,'','fopen');
    $data = urldecode($result);
    return json_decode($data,true);
    
}
// 抠电影
function getCDYapi($arr_param, $method="post"){
	$str_apiUrl     = "http://api.komovie.cn/movie/service";//接口地址
	$str_sUser      = "158";//接口商户用户名
	$str_sKey       = "fdhJKy";//接口密钥
	//$str_apiUrl     = "http://test.komovie.cn/api_movie/service";//接口地址
	//$str_sUser      = "158";//接口商户用户名
	//$str_sKey       = "mq3CwYZL";//接口密钥
	
	require_once(ROOT_PATH . 'includes/httpRequest.php');
	$http = new HttpRequest;
	
	$inc_time = local_date('YmdHis');
	$str_questXml = array('time_stamp'=>$inc_time);
	$str_questXml = array_merge($str_questXml , $arr_param);
	ksort($str_questXml);	
	$temp_questXml = array('enc'=>strtolower(md5(urlencode(implode('',$str_questXml).$str_sKey))));
	$str_questXml = array_merge($temp_questXml, $str_questXml);
	$result = $http->post($str_apiUrl, $str_questXml,'','curl','',array("channel_id:".$str_sUser));
	return json_decode($result,true);
}

// 动网场地通接口
function getDongSite( $site, $param=array())
{
    $appid      = 247640;
    $nonce      = mt_rand();
    $timestamp  = local_gettime();   
    $appSercert = '8D36F9E70356360470A32EF16226A165'; 
	
	// 签名计算
	$array = array( strval($appid), strval($appSercert), strval($timestamp), strval($nonce) );
	sort($array, SORT_STRING);
	$sign = implode($array);	
	
	$default = array(
	    'appId' => $appid,
	    'nonce' =>  $nonce,
	    'timestamp'=> $timestamp,
	    'sign' => sha1($sign)
	);
	
	require_once(ROOT_PATH . 'includes/httpRequest.php');
	$http = new HttpRequest;
	switch($site)
	{
		// 场馆列表
		case 'list': 		$str_apiUrl = 'http://api.dong24.com/open/venue/list/'; break;
		// 场馆详情
		case 'detail': 		$str_apiUrl = 'http://api.dong24.com/open/venue/detail/'; break;
		// 场馆产品
		case 'priceList': 	$str_apiUrl = 'http://api.dong24.com/open/order/priceList/'; break;
		// 门票日历价格
		case 'ticketPrice': $str_apiUrl = 'http://api.dong24.com/open/order/ticketPrice/'; break;
		// 场馆时段价格
		case 'venuePrice': 	$str_apiUrl = 'http://api.dong24.com/open/order/venuePrice/'; break;
		// 下单
		case 'add': 		$str_apiUrl = 'http://api.dong24.com/open/order/add/'; break;
		// 退款
		case 'refund': 		$str_apiUrl = 'http://api.dong24.com/open/order/refund/'; break;
		// 订单状态
		case 'status': 		$str_apiUrl = 'http://api.dong24.com/open/order/orderStatus/'; break;	
		// 订单支付
		case 'pay': 		$str_apiUrl = 'http://api.dong24.com/open/order/pay/'; break;
	}
	
	$str_request = array_merge($default,$param);	
	$result = $http->get($str_apiUrl, $str_request,'', 'curl');
	return json_decode($result,true);
	
}

// 动网接口
function getDongapi($tradaId="list",$arr_param=array(), $method="post"){
	$default = array(
		'custId' => '247640',
		'apikey' => '8D36F9E70356360470A32EF16226A165'
	);
	
	require_once(ROOT_PATH . 'includes/httpRequest.php');
	$http = new HttpRequest;
	
	switch($tradaId){
		// 列表
		case 'list': 		$str_apiUrl = 'http://b2b.dong24.com/api/list.jsp'; break;
		// 标签
		case 'tag':			$str_apiUrl = 'http://b2b.dong24.com/api/tag.jsp';	break;
		// 详情
		case 'detail':		$str_apiUrl = 'http://b2b.dong24.com/api/detail.jsp';  break;
		// 批量获取产品状态
		case 'pstate':		$str_apiUrl = 'http://b2b.dong24.com/api/getProductState.jsp'; break;
		// 产品价格日历
		case 'price': 		$str_apiUrl = 'http://b2b.dong24.com/api/price.jsp'; break;
		// 城市接口
		case 'city': 		$str_apiUrl = 'http://b2b.dong24.com/api/city.jsp'; break;
		
		// 保存订单
		case 'order': 		$str_apiUrl = 'http://b2b.dong24.com/api/order.jsp'; break;
		// 订单详情
		case 'odetail':		$str_apiUrl = 'http://b2b.dong24.com/api/orderDetail.jsp'; break;
		// 订单列表
		case 'olist':		$str_apiUrl = 'http://b2b.dong24.com/api/orderlist.jsp'; break;
		// 订单支付
		case 'pay':			$str_apiUrl = 'http://b2b.dong24.com/api/pay.jsp'; break;
		// 订单取消
		case 'corder':		$str_apiUrl = 'http://b2b.dong24.com/api/chancelOrder.jsp'; break;
		// 订单退改
		case 'caorder':		$str_apiUrl = 'http://b2b.dong24.com/api/changeApplyOrder.jsp'; break;
		// 批量获取订单状态
		case 'ostate':		$str_apiUrl = 'http://b2b.dong24.com/api/getOrderState.jsp'; break;
		// 订单短信重发
		case 'resend':		$str_apiUrl = 'http://b2b.dong24.com/api/resend.jsp'; break;
		default:
	}
	
	$str_request = array_merge($default,$arr_param);
	$result = $http->post($str_apiUrl, $str_request,'','fopen');
	if (!empty($result)){
		$obj_xmlRoot = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
		$arr_result = object2array($obj_xmlRoot);
	}
	return $arr_result;
	
}

function getYCApi($arr_param, $str_act, $method = 'post'){
	if (empty($str_act)) return array();
	$str_apiUrl     = $GLOBALS['_CFG']['ycapiurl'];//接口地址
	$str_sUser      = $GLOBALS['_CFG']['ycappUser'];//接口商户用户名
	$str_sKey       = $GLOBALS['_CFG']['ycappKey'];//接口密钥
	

	require_once(ROOT_PATH . 'includes/httpRequest.php');
	$http = new HttpRequest;
	
	if ($str_act == 'apiorder'){
		$arr_quest = array(
			'app' => $str_act,
			'u'   => $str_sUser
		);
		//$result = file_get_contents($arr_param);
		//var_dump($result);
		//return $result;
	}else{
		$arr_quest = array(
			'act' => $str_act,
			'u'   => $str_sUser
		);
	}

	if (!empty($arr_param)){
		$arr_quest = array_merge($arr_quest, $arr_param);
	}

	$result = $http->get($str_apiUrl, $arr_quest);
	$obj_res = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
	return $obj_res;
}


function F($name, $value='', $time = 0, $path = '') {
	$int_time = $time ? $time : $GLOBALS['_CFG']['cache_time'];//判断文件存活时间
	static $_cache  = array();
	$filename       = ROOT_PATH . 'temp/films_caches/' . ($path ? $path : '') . $name . '.php';
	if ('' !== $value) {
		if (is_null($value)) {
			// 删除缓存
			return false !== strpos($name,'*')?array_map("unlink", glob($filename)):unlink($filename);
		} else {
			// 缓存数据
			$dir            =   dirname($filename);
			// 目录不存在则创建
			if (!is_dir($dir))
				mkdir($dir,0755,true);
			$_cache[$name]  =   $value;
			return file_put_contents($filename, "<?php\treturn " . var_export($value, true) . ";?>");
		}
	}

	if (isset($_cache[$name])){
		return $_cache[$name];
	}
	// 获取缓存数据
	if (is_file($filename)) {
		$int_fileTime = filemtime($filename);//获取文件创建时间
		if ($int_time < 0){
			$value         = include $filename;
			$_cache[$name] = $value;
		}else{
			if ($int_fileTime + $int_time > gmtime()) {
				$value         = include $filename;
				$_cache[$name] = $value;
			}else{
				$value = false;
			}
		}
	} else {
		$value = false;
	}
	return $value;
}


function object2array($object) {
	return @json_decode(@json_encode($object), 1);
}

// 短信接口
function smsvrerify($mobile,$verify,$start){
	//var_dump($mobile,$verify);
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
// 		http://web.cr6868.com/asmx/smsservice.aspx?name=test&pwd=112345&cont
// ent=testmsg&mobile=13566677777,18655555555&stime=2012-08-01
// 8:20:23&sign=testsign&type=pt&extno=123
	//同一个号码，1 分钟/1 次，1 小时/5 次，超过可能拦截禁发；
	$url = "http://web.cr6868.com/asmx/smsservice.aspx?".$params; //提交的url地址
	//0,20130821110353234137876543,0,500,0,提交成功
	//依次为：状态、发送编号、,无效号码数、成功提交数、黑名单数、消息
	$data=curl_file_get_contents($url);
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
//卡系统服务接口V3.0.0
function getCardApi3($arr_param, $tradeId, $dataSource = '', $method = 'post'){

	if (empty($arr_param) || empty($tradeId)) return array();
	// $str_apiUrl     = $GLOBALS['_CFG']['apiurl3'];	
	$str_apiUrl     = "http://card.douyou100.com:9004/scp/voucherService";//接口地址
	$str_sKey       = $GLOBALS['_CFG']['skey'];  //接口密钥
	$int_dataSource = $dataSource ? $dataSource : intval($GLOBALS['_CFG']['dsource']);//请求来源
	$str_saleId     = $GLOBALS['_CFG']['saleId'];//售卡机构编码
	
	require_once(ROOT_PATH . 'includes/httpRequest.php');
	$http = new HttpRequest;
	$des = new Crypt3Des($str_sKey);

	$arr_data = array(
		'DataSource' => $int_dataSource,
		'TradeId'    => $tradeId,
	);

	$str_xml = '<?xml version="1.0" encoding="UTF-8"?>';
	$str_questXml = $str_xml.
		'<message>
			<DataSource>'.$int_dataSource.'</DataSource>
			<TradeId>'.$tradeId.'</TradeId>
			<Signatures>{SIGN}</Signatures>
			<BusinessData>{DATA}</BusinessData>
		</message>';

	switch ($tradeId){
		//卡信息查询
		case 'VOUCHER-INFO':
			//业务数据xml
			$str_xml .= '
			<QueryVoucherInfo>
				<voucherId>'.$arr_param['voucherId'].'</voucherId>
				<checkPwd>1</checkPwd>
				<password>'.$arr_param['password'].'</password>
			</QueryVoucherInfo>';
			
			$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;		
			//8.9凭证修改
		case 'VOUCHER-MODIFY':
			//业务数据xml

			$str_xml .= '<VoucherModify>';
			foreach ($arr_param as $key=>$var){
				if($var!=''){
					$str_xml .= ('<'.$key.'>'.$var.'</'.$key.'>');
				}
			}
			$str_xml .= '</VoucherModify>';
		$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;
		//卡信息查询
		case 'CARD-INFO':
			//业务数据xml
			$str_xml .= '
			<CardQueryInfo>
				<cardId>'.$arr_param['cardId'].'</cardId>
				<cardPwd>'.$arr_param['cardPwd'].'</cardPwd>
			</CardQueryInfo>';
			
			$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;
		//修改卡密码
		case 'CARD-MODIFY-PWD':

			$str_xml .= '<CardModifyPwd>';
			foreach ($arr_param as $key=>$var){
				$str_xml .= ('<'.$key.'>'.$var.'</'.$key.'>');
			}
			$str_xml .= '</CardModifyPwd>';

			$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;
		//卡消费
		case 'CARD-PAY':
			//业务数据xml
			$str_xml .= '<CardPay>';
			foreach ($arr_param as $key=>$var){
				$str_xml .= ('<'.$key.'>'.$var.'</'.$key.'>');
			}
			$str_xml .= '</CardPay>';

			$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;
		//卡消费撤销
		case 'CARD-PAY-ROLLBACK':
			//业务数据xml
			$str_xml .= '<CardPayRollback>';
			foreach ($arr_param as $key=>$var){
				$str_xml .= ('<'.$key.'>'.$var.'</'.$key.'>');
			}
			$str_xml .= '</CardPayRollback>';

			$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;
		//卡充值
		case 'CARD-RECHARGE':
			//业务数据xml
			$str_xml .= '<CardRecharge>';
			foreach ($arr_param as $key=>$var){
				$str_xml .= ('<'.$key.'>'.$var.'</'.$key.'>');
			}
			$str_xml .= '</CardRecharge>';

			$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;
		//卡充值审核
		case 'CARD-AUTH-RECHARGE':
			//业务数据xml
			$str_xml .= '<CardRecharge>';
			foreach ($arr_param as $key=>$var){
				$str_xml .= ('<'.$key.'>'.$var.'</'.$key.'>');
			}
			$str_xml .= '</CardRecharge>';

			$str_businessData = $des->encrypt($str_xml);//加密业务数据
			break;
	}

	//数字签名
	$str_sign = md5($str_businessData.local_date('Ymd').$str_sKey);
	//组成请求报文
	$str_questXml = str_replace(array('{SIGN}', '{DATA}'), array($str_sign, $str_businessData), $str_questXml);

	//发送post请求
	$result = $http->post($str_apiUrl, $str_questXml, 'xml', 'fopen');
	$str_businessdata = $des->decrypt($result['message']['businessdata']);

	if (!empty($str_businessdata)){
		$arr_res = simplexml_load_string($str_businessdata);
		return (array)$arr_res;
	}
	return false;
}

//票务吧系统代理商接口标准--票工厂
function piaoduoduo($jiekouId,$arr_param=array(), $method="post"){
	//测试地址
	// $str_apiUrl   = "http://debug.open.piaoduoduo.cn/PlatForm/API?method=".$jiekouId;//接口地址
	// $str_pid      = "3162289690@qq.com";//伙伴id
	// $str_Key      = "16071689425d63fd437d8c77";//接口密钥
	//正式地址
	$str_apiUrl   = "http://bridge.piao58.cn/PlatForm/API?method=".$jiekouId;//接口地址
	$str_pid      = "18513819787";//伙伴id
	$str_Key      = "h586cs25chs4kdl1b2202f7q";//接口密钥
	require_once(ROOT_PATH . 'includes/httpRequest.php');
	$http = new HttpRequest;
	$des = new Crypt3Des($str_Key);//解密
	$arr_questXml = array('_pid'=>$str_pid,'format'=>'xml','_ts'=>gmtime(),'payType'=>64);//参数数组
	$arr_questXml=array_merge($arr_questXml,$arr_param);//合并参数
	foreach ($arr_questXml as $key => $value) {
		$arr_questXml1[strtolower($key)]=$value;
	}
	ksort($arr_questXml1);
	$new_str = '';
	foreach($arr_questXml1 as $key=>$val){
		if($val!=''){
			$new_str.=strtolower($key).'='.$val.'&';
		}
	}
	$str2 = substr($new_str,0,strlen($new_str)-1);
	$jiami=md5($str2.$str_Key);
	$arr_questXml1['_sign'] = $jiami;
	$result = $http->post($str_apiUrl,$arr_questXml1,'xml');//发送请求
	$data = $des->decrypt($result['response']['body']);//解密 getXmlRes
	@$obj_xmlRoot=simplexml_load_string($data);
	$arr_result = object2array($obj_xmlRoot);
	$arr_result['head'] =$result['response']['head'];
	return $arr_result;

}

//票务吧系统代理商接口标准--票工厂-接收消息
function piaoduoduo_arg($parameter){
	require_once(ROOT_PATH . 'includes/httpRequest.php');
	$http = new HttpRequest;
	$arr_result = $http->getXmlRes($parameter);//解析返回的xml文件
	$arr_result['head'] =$arr_result['request']['head'];
	$des = new Crypt3Des();//解密
	$arr_result = $des->decrypt($arr_result['request']['body']);//解密 getXmlRes
	return $arr_result;

}