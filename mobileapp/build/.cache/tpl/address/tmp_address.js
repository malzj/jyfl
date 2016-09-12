/*TMODJS:{"version":1,"md5":"9d87e87e3f6208f902cf405788e6a347"}*/
template('tpl/address/tmp_address',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$each=$utils.$each,data=$data.data,value=$data.value,key=$data.key,val=$data.val,k=$data.k,$escape=$utils.$escape,city=$data.city,cid=$data.cid,$out='';$out+='<div class="mui-indexed-list-inner"> <div class="mui-indexed-list-empty-alert">没有数据</div> <ul class="mui-table-view"> ';
$each(data,function(value,key){
$out+=' ';
if(key!="hot"){
$out+=' ';
$each(value,function(val,k){
$out+=' <li data-group="';
$out+=$escape(k);
$out+='" class="mui-table-view-divider mui-indexed-list-group">';
$out+=$escape(k);
$out+='</li> ';
$each(val,function(city,cid){
$out+=' <li data-value="" data-tags="';
$out+=$escape(city.region_english);
$out+='" data-id="';
$out+=$escape(city.region_id);
$out+='" class="mui-table-view-cell mui-indexed-list-item">';
$out+=$escape(city.region_name);
$out+='</li> ';
});
$out+=' ';
});
$out+=' ';
}
$out+=' ';
});
$out+=' </ul> </div> ';
return new String($out);
});