/*TMODJS:{"version":1,"md5":"34923df8cd320c8b4186aca8eecd1374"}*/
template('tpl/cake/glist',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$each=$utils.$each,data=$data.data,list=$data.list,$index=$data.$index,$escape=$utils.$escape,$out='';$out+='<ul class="mui-table-view"> ';
$each(data.list,function(list,$index){
$out+=' <li class="mui-table-view-cell mui-media"> <a data-href="details.html?id=';
$out+=$escape(list.goods_id);
$out+='" class="href_click"> <img class="mui-media-object mui-pull-left" src="';
$out+=$escape(list.goods_thumb);
$out+='"> <div class="mui-media-body"> <h4 class="goods_name">';
$out+=$escape(list.goods_name);
$out+='</h4> <p class="goods_font mui-ellipsis-2">';
$out+=$escape(list.goods_brief);
$out+='</p> <div class="goods_price"><span>';
$out+=$escape(list.shop_price);
$out+='</span><span class="mui-icon iconfont icon-gouwuche" data-id="';
$out+=$escape(list.goods_id);
$out+='"></span></div> </div> </a> </li> ';
});
$out+=' </ul>';
return new String($out);
});