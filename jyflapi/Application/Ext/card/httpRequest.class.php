<?php
namespace Ext\card;

use Ext\card;
class HttpRequest{

	static public $str_contentType = '';//传输数据类型

	private $arr_ext = array('json', 'xml');//默认处理的返回信息格式
	
	//构造方法，默认启用curl
	public function __construct(){

	}

	/**
	 * buildUrlQuery
	 * 拼接url
	 * @param mix    $mix_param   url参数
	 * @param string $baseURL     基于的url
	 * @return string             返回拼接的url
	 */
	public function buildUrlQuery($mix_param, $str_baseUrl = ''){
		$str_param = '';
		if (is_array($mix_param)){
			foreach ($mix_param as $key=>$var){
				$str_param .= !empty($str_param) ? '&' : '';
				$str_param .= $key . '=' . urlencode($var);
			}
		}else{
			$str_param = $mix_param;
		}

		return (!empty($str_baseUrl) ? $str_baseUrl . '?' : '') . $str_param;
	}
	
	/**
	 * post
	 * post请求
	 * @param string $str_url            请求url链接地址
	 * @param mix    $mix_data           url参数
	 * @param string $str_ext            返回的数据类型,默认json
	 * @param string $str_type           执行请求方法默认curl
	 * @param string $str_contentType    发送请求内容类型text/xml
	 * @return string                    返回请求结果集
	 */
	public function post($str_url, $mix_data, $str_ext = '', $str_type = '', $str_contentType = '', $httpheader=array()){
		switch ($str_type){
			//fopen请求方法
			case 'fopen':
				$response = $this->fopen($str_url, $mix_data, 'POST', $str_contentType);
				break;
			//curl请求方法
			case 'curl':
				$response = $this->curl($str_url, $mix_data, 'POST', $str_contentType, $httpheader);
				break;
			//默认执行curl没有结果在使用fopen
			default :
				if ($response = $this->curl($str_url, $mix_data, 'POST', $str_contentType)){
				}else if ($response = $this->fopen($str_url, $mix_data, 'POST', $str_contentType)){
				}
		}
		return $this->parseResponse($response, $str_ext);
	}
	
	/**
	 * get
	 * get
	 * @param string $str_url            请求url链接地址
	 * @param mix    $mix_data           url参数
	 * @param string $str_ext            返回的数据类型,默认json
	 * @param string $str_type           执行请求方法默认curl
	 * @param string $str_contentType    发送请求内容类型text/xml
	 * @return string                    返回请求结果集
	 */
	public function get($str_url, $mix_data, $str_ext = '', $str_type = '', $str_contentType = '', $httpheader=array()){
		//var_dump($mix_data);
		$str_url = $this->buildUrlQuery($mix_data, $str_url);//拼接url
		//var_dump($str_url);
		switch ($str_type){
			//fopen请求方法
			case 'fopen':
				$response = $this->fopen($str_url, $mix_data, 'GET', $str_contentType);
				break;
			//curl请求方法
			case 'curl':
				$response = $this->curl($str_url, $mix_data, 'GET', $str_contentType, $httpheader);
				break;
			//默认执行file_get_contents没有结果在使用curl没有结果在使用fopen
			default :
				if (ini_get("allow_url_fopen") == "1" && $response = file_get_contents($str_url)){
				}else if ($response = $this->curl($str_url, $mix_data, 'GET', $str_contentType)){
				}else if ($response = $this->fopen($str_url, $mix_data, 'GET', $str_contentType)){
				}
		}
		return $this->parseResponse($response, $str_ext);
	}

	public function curl($str_url, $mix_data, $str_method = 'POST', $str_contentType = '',$httpheader=array()){
	    //echo $this->buildUrlQuery($mix_data, $str_url).'<br>';//拼接url
	    
	    $ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
		curl_setopt($ch, CURLOPT_TIMEOUT, 180 );
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if (!empty($str_contentType)){
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type:'.$str_contentType));
		}

		switch (strtoupper($str_method)){
			case 'POST':
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $mix_data);
				break;
		}

		curl_setopt($ch, CURLOPT_URL, $str_url);

		$reponse = curl_exec($ch);

		curl_close($ch);

		return $reponse;
	}

	public function fopen($str_url, $mix_data, $str_method = 'POST', $str_contentType = ''){
		$arr_url = parse_url($str_url);
		switch ($arr_url['scheme']) {
			case 'https':
				$int_port = 443;
				break;
			case 'http':
			default:
				$int_port = 80;
		}
		$int_port = $arr_url['port'] ? $arr_url['port'] : $int_port;
		$str_path = (isset($arr_url['path']) ? $arr_url['path'] : '/') . (isset($arr_url['query']) ? '?' . $arr_url['query']: '');
		$str_url = $arr_url['scheme'] . '://' . $arr_url['host'] . ':' . $int_port . $str_path;
		$arr_opts = array(
			'http' => array(
				'method'  => strtoupper($str_method),
				'header'  => "Content-type: ".(!empty($str_contentType) ? $str_contentType : 'application/x-www-form-urlencoded')."\r\n"
			)
		);
		switch (strtoupper($str_method)){
			case 'POST':
				$str_param = $this->buildUrlQuery($mix_data);
				$arr_opts['http']['header'] .= "Content-Length: " . strlen($str_param) . "\r\n";
				$arr_opts['http']['content'] = $str_param;
				break;
		}

		$context = stream_context_create($arr_opts);
		$fp = fopen($str_url, 'rb', false, $context);
		$response = @stream_get_contents($fp);
		return $response;
	}
	
	//解析返回的文件
	private function parseResponse($response, $str_ext){
		//$str_ext = !in_array($str_ext, $this->arr_ext) ? 'json' : $str_ext;
		switch ($str_ext){
			case 'xml':
				$response = $this->getXmlRes($response);
				break;
			case 'json':
				$response = json_decode($response);
				break;
			default:
		}
		return $response;
	}
	
	//解析返回的xml文件
	private function getXmlRes($response){
		//return simplexml_load_string($response);
		
		$xml_parser = xml_parser_create();
		xml_parse_into_struct($xml_parser, $response, $vals, $index);
		xml_parser_free($xml_parser);
		if (!empty($vals)){
			$params = array();
			$level = array();
			foreach ($vals as $xml_elem) {
				if ($xml_elem['type'] == 'open') {
					if (array_key_exists('attributes',$xml_elem)) {
						list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
					} else {
						$level[$xml_elem['level']] = $xml_elem['tag'];
					}
				}
				if ($xml_elem['type'] == 'complete') {
					$start_level = 1;
					$php_stmt = '$params';
					while($start_level < $xml_elem['level']) {
						$php_stmt .= '[strtolower($level['.$start_level.'])]';
						$start_level++;
					}
					$php_stmt .= '[strtolower($xml_elem[\'tag\'])] = $xml_elem[\'value\'];';
					eval($php_stmt);
				}
			}
		}
		return $params;
	}
}