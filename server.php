<?php
class Server{
    private $curl;
    private $url='http://api.komovie.cn/movie/service';
	private $channel_id="158";//填写分配的channel_id
    private $md5key="fdhJKy";//填写分配的md5key
    public function __construct() {
        $this->curl= curl_init();     
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array("channel_id:{$this->channel_id}"));
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 180 ); 
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, TRUE );
    }
    public function query($params){
        $params['time_stamp'] = time().floor(microtime()*1000);
        ksort($params);
        $encodeParams = array();
        $encode = '';
        foreach($params as $key => $value) {
            $encode .= $value;
            $encodeParams[] = $key.'='.str_replace('+','%20',urlencode($value));
        }
        $encodeParams[] = 'enc='.  strtolower(md5(str_replace('+','%20',urlencode($encode.$this->md5key))));
        $url = $this->url.'?'.implode('&', $encodeParams);
        curl_setopt($this->curl, CURLOPT_URL, $url ); 
        $result=curl_exec($this->curl);
        return json_decode($result,TRUE);
    }
}

//调用方法
$server=new Server();
$citys=$server->query(array('action'=>'movie_Query','city_id'=>36));
var_dump($citys);
?>
