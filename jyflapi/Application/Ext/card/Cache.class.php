<?php
/**
 * Created by PhpStorm.
 * User: chao
 * Date: 2016/5/26
 * Time: 11:35
 */

namespace Ext\card;


//缓存
class Cache{

    public $_path;		//缓冲路径

    public $_name;		//设置缓冲文件文名称

    public $_time;		//缓冲时间

    public $_states;	//缓冲状态

    public $_filename;	//缓冲文件名

    //初始化
    public function Cache($path){

        //设置缓冲路径
        if(!isset($path)){
            $this->_path = 'Cache/';
        }else{
            $this->_path = $path."/";
        }

        //检测路径是否存在
        if(!is_dir($this->_path)){
            @mkdir($this->_path,0777);
        }
    }

    //启用缓冲
    public function Start ($name, $time) {
        $this->_name = md5($name);	//MD5加密缓冲文件名
        $this->_time = $time;		//文件存活时间

        $file_name = $this->_path.$this->_name.'.MyCache';
        $this->_filename = $file_name;

        /* 检测文件的时间和设置缓冲状态 */
        if (file_exists($file_name)) {
            //文件创建的时间
            $file_time = filemtime($file_name);

            //判断文件存活时间
            if ($file_time + $this->_time > time()) {
                $this->Read();
                $this->_states = 'false';
            }else{
                ob_start();
                $this->_states = 'true';
            }
        }else{
            ob_start();
            $this->_states = 'true';
        }

        if ($this->_states == 'true') {
            return true;
        }else if ($this->_states == 'false'){
            return false;
        }
    }

    //创建缓冲文件
    public function Build(){
        if($this->_states == true){
            $content = ob_get_contents();
            ob_clean();
            $cache_file = fopen($this->_filename, 'w+');
            fwrite ($cache_file, $content);
            $this->Read();
        }else{
            ob_clean();
            $this->Read();
        }
    }

    //读取文件
    private function Read(){
        readfile($this->_filename);
    }

    //删除文件
    public function Remove($rfilename){
        @unlink($this->_path.md5($rfilename).'.MyCache');
    }
}