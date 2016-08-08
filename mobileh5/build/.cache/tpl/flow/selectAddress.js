/*TMODJS:{"version":1,"md5":"500a2de867e3ca8375225e4471d8faf2"}*/
template('tpl/flow/selectAddress',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$each=$utils.$each,data=$data.data,row=$data.row,$index=$data.$index,$escape=$utils.$escape,$out='';$out+='<div class="adress_select_title"><h4 class="mui-text-center">配送地址</h4></div> <ul id="OA_task_2" class="mui-table-view"> ';
$each(data,function(row,$index){
$out+=' <li class="mui-table-view-cell"> <div class="mui-slider-right mui-disabled" data-id="';
$out+=$escape(row.address_id);
$out+='"> <a class="mui-btn mui-btn-grey">编辑</a> <a class="mui-btn mui-btn-red add_adress_remove">删除</a> </div> <div class="mui-slider-handle"> <div class="mui-table-cell" style="display:block"> <p class="color_2fd0b5"> <span>';
$out+=$escape(row.consignee);
$out+='</span> <span class="mui-pull-right">';
$out+=$escape(row.mobile);
$out+='</span> </p> <p> ';
if(row.selected == 1){
$out+='<em class="adress_default">默认</em>';
}
$out+=' <span>';
$out+=$escape(row.country_cn);
$out+=' ';
$out+=$escape(row.province_cn);
$out+=' ';
$out+=$escape(row.address);
$out+='</span> </p> </div> </div> </li> ';
});
$out+=' </ul> <div class="add_adress">添加新地址</div>';
return new String($out);
});