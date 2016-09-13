/*TMODJS:{"version":1,"md5":"beea5f145a3421f7d4450502b4c21583"}*/
template('tpl/yanchu/tmp_yanchu_list',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$each=$utils.$each,list=$data.list,val=$data.val,k=$data.k,$escape=$utils.$escape,$out='';$out+='<div class="mui-content"> <div class="mui-scroll-wrapper" style="padding-top: 50px;"> <div class="mui-scroll"> <ul class="mui-table-view yanchu_list"> ';
$each(list,function(val,k){
$out+=' <li class="mui-table-view-cell mui-media"> <a href="javascript:;" class="href_click" data-href="./details.html?id=1217&itemid=';
$out+=$escape(val.item_id);
$out+='"> <img class="mui-media-object mui-pull-left yanchu_img" src="';
$out+=$escape(val.thumb);
$out+='"> <div class="mui-media-body"> <h4 class="mui-ellipsis">';
$out+=$escape(val.item_name);
$out+='</h4> <p class="mui-ellipsis">';
$out+=$escape(val.data_ext);
$out+='</p> <p class="mui-ellipsis">';
$out+=$escape(val.site_name);
$out+='</p> </div> </a> </li> ';
});
$out+=' </ul> </div> </div> </div>';
return new String($out);
});