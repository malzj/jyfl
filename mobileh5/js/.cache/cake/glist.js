/*TMODJS:{"version":11,"md5":"ab724027c86d78c20aa8fdb52e09953c"}*/
template('cake/glist',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$each=$utils.$each,data=$data.data,list=$data.list,$index=$data.$index,$escape=$utils.$escape,$out='';$out+='<ul class="mui-table-view"> ';
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
$out+=' </ul>';
return new String($out);
});