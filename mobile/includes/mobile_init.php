<?php
/* 
 *  为手机端专门初始一些东西
 */

// josn 数据返回，jsonp数据返回
function JsonpEncode($jsonArray)
{
    $jsonType = 'jsonp';
    $callback = 'jsoncallback';
    if ($jsonType == 'json')
        exit(json_encode($jsonArray));
    else
        exit($_GET[$callback]."(".json_encode($jsonArray).")");
}
