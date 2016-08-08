/*TMODJS:{"version":1,"md5":"1827569d4d139bdd29fc6c1f6af7bb51"}*/
template('tpl/flow/map',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$each=$utils.$each,data=$data.data,yunfei=$data.yunfei,$index=$data.$index,$escape=$utils.$escape,$out='';$out+='<style> #supplier-showmap{position:absolute;height:100%;width:100%;} </style> <div class="mui-content"> <div class="mui-table-view" style="margin-top: 0;"> <li class="mui-table-view-cell"> ';
$each(data.yunfei,function(yunfei,$index){
$out+=' <span class="mui-pull-right">运费<span>';
$out+=$escape(yunfei.yunfei);
$out+='</span>点</span> <span class="mui-pull-right fanwei_block" style="background:';
$out+=$escape(yunfei.color);
$out+='"></span> ';
});
$out+=' </li> </div> <div id="supplier-showmap" style="height:100%; width:100%;"> </div> </div> <script> /*动态导入js*/ insertJs([\'../js/baidumap.js\']); baidumap.setOptions({ isYunfei:true, isSetYunfei:false, showMapId:\'supplier-showmap\', currentCity:\'';
$out+=$escape(data.cityname);
$out+='\' }); baidumap.showMap(';
$out+=$escape(data.id);
$out+='); </script>';
return new String($out);
});