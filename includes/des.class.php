<?php

/**
 * @Author: anchen
 * @Date:   2015-09-09 09:54:19
 * @Last Modified by:   anchen
 * @Last Modified time: 2015-09-09 09:55:32
 */
class DES {
    public $key;
    function __construct($key) {
        $this->key = $key;
    }
    //����
    function encrypt($input) {
        $size = mcrypt_get_block_size('des', 'ecb');
        $input = $this->pkcs5_pad($input, $size);
        $key = $this->key;
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        $arr = $this->getBytes($data);
        $string = '';
        for ($i = 0; $i <= count($arr) - 1; $i++) {
            $trs = @dechex($arr[$i]);
            if (strlen($trs) == 1) {
                $string.='0' . $trs;
            } else {
                $string.=$trs;
            }
        }
        $data = strtoupper($string);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }
//asc
    public function getBytes($string) {
        $bytes = array();
        for ($i = 0; $i < strlen($string); $i++) {
            $bytes[] = ord($string[$i]);
        }
        return $bytes;
    }
//����
    function decrypt($encrypted) {
        $encrypted = base64_decode($encrypted);
        $encrypted = strtolower($encrypted);
        $arr = str_split($encrypted, 2);
        $bytes = array();
        for ($i = 0; $i < count($arr); $i++) {
            $bytes[] = @hexdec($arr[$i]);
        }
        $strings = '';
        for ($j = 0; $j < count($bytes); $j++) {
            $strings.=@chr($bytes[$j]);
        }
        $key = $this->key;
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $strings);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $y = $this->pkcs5_unpad($decrypted);
        return $y;
    }

    function pkcs5_pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    function pkcs5_unpad($text) {
        $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text))
            return false;
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return false;
        return substr($text, 0, -1 * $pad);
    }

}
