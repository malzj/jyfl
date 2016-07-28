/*TMODJS:{"version":31,"md5":"80646cda90589a4f9307e80afa5941d7"}*/
template('cake/list',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$each=$utils.$each,data=$data.data,navigator=$data.navigator,$index=$data.$index,$escape=$utils.$escape,attr=$data.attr,alist=$data.alist,list=$data.list,$out='';$out+=' <div class="select_scroll">  <div class="mui-scroll-wrapper mui-slider-indicator mui-segmented-control mui-segmented-control-inverted"> <div class="mui-scroll pinpai_scroll"> ';
$each(data.navigator.child,function(navigator,$index){
$out+=' <a class="mui-control-item event ';
if(navigator.cid == data.cat.cat_id){
$out+='mui-active';
}
$out+='" data-id=\'{"id":"';
$out+=$escape(navigator.cid);
$out+='"}\'><span>';
$out+=$escape(navigator.name);
$out+='</span></a> ';
});
$out+=' </div> </div>  ';
$each(data.attrList,function(attr,$index){
$out+=' <div class="mui-scroll-wrapper mui-slider-indicator mui-segmented-control mui-segmented-control-inverted kouwei_scroll"> <div class="mui-scroll pinpai_scroll"> ';
$each(attr.attr_list,function(alist,$index){
$out+=' ';
if($index !=0){
$out+=' <a class="mui-control-item event ';
if(alist.selected){
$out+='mui-active';
}
$out+='" data-id=\'{"filter_attr":"';
$out+=$escape(alist.attr_id);
$out+='"}\'><span>';
$out+=$escape(alist.attr_value);
$out+='</span></a> ';
}
$out+=' ';
});
$out+=' </div> </div> ';
});
$out+=' </div>  <ul class="mui-table-view"> ';
$each(data.list,function(list,$index){
$out+=' <li class="mui-table-view-cell mui-media"> <a data-href="details.html?id=';
$out+=$escape(list.goods_id);
$out+='" class="href_click"> <img class="mui-media-object mui-pull-left" src="';
$out+=$escape(list.goods_thumb);
$out+='"> <div class="mui-media-body"> <h4 class="goods_name">';
$out+=$escape(list.goods_name);
$out+='</h4> <p class="goods_font">';
$out+=$escape(list.goods_brief);
$out+='</p> <div class="goods_price"><span>';
$out+=$escape(list.shop_price);
$out+='</span><span class="mui-icon iconfont icon-gouwuche"></span></div> </div> </a> </li> ';
});
$out+=' </ul> ';
return new String($out);
});