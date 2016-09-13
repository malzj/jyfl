/*TMODJS:{"version":1,"md5":"cb0f9c89208a113ea132899e871d158c"}*/
template('tpl/movie/tmp_cinema_list',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$each=$utils.$each,data=$data.data,value=$data.value,key=$data.key,$escape=$utils.$escape,val=$data.val,k=$data.k,is_brush=$data.is_brush,$out='';$out+='<ul class="mui-table-view mui-table-view-chevron"> ';
$each(data,function(value,key){
$out+=' <li class="mui-table-view-cell mui-collapse"><a class="mui-navigate-right" href="#">';
$out+=$escape(value.area_name);
$out+='</a> <ul class="mui-table-view mui-table-view-chevron"> ';
$each(value.cinemas,function(val,k){
$out+=' <li class="mui-table-view-cell "> <a data-href="./cinema_details.html?cinemaid=';
$out+=$escape(val.komovie_cinema_id);
$out+='" class="mui-row href_click"> <div class="mui-table-cell mui-col-xs-10"> <h4 class="mui-ellipsis">';
$out+=$escape(val.cinema_name);
$out+='(';
if(val.is_komovie==1){
$out+='座';
}
if(val.is_dzq==1){
$out+='券';
}
if(is_brush==1){
$out+='卡';
}
$out+=')</h4> <p class="mui-ellipsis">';
$out+=$escape(val.cinema_address);
$out+='</p> </div> </a> </li> ';
});
$out+=' </ul> </li> ';
});
$out+=' </ul>';
return new String($out);
});