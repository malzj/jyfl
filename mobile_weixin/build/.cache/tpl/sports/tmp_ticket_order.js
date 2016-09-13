/*TMODJS:{"version":1,"md5":"0cc072d677b9bca24bafdacdc3db7a59"}*/
template('tpl/sports/tmp_ticket_order',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,validity=$data.validity,detail=$data.detail,$each=$utils.$each,fields=$data.fields,field=$data.field,$index=$data.$index,select=$data.select,key=$data.key,tip=$data.tip,travelDate=$data.travelDate,$out='';$out+='<div class="mui-content"> <div class="ticket_order_tips mui-text-center color_coral"> ';
$out+=$escape(validity);
$out+='。 </div> <form id="subOrder" onsubmit="return false"> <div class="margin_top_15 mui-table-view"> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">产品名称</h4> <p class="mui-pull-right width_60">';
$out+=$escape(detail.productName);
$out+='</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">购买数量</h4> <div class="mui-numbox mui-pull-right" data-numbox-min=\'1\'> <button class="mui-btn mui-btn-numbox-minus" type="button">-</button> <input name="goods_number" class="mui-input-numbox" type="number" value="" /> <button class="mui-btn mui-btn-numbox-plus" type="button">+</button> </div> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">单价</h4> <p class="mui-pull-right color_2fd0b5">';
$out+=$escape(detail.salePrice);
$out+='点</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">产品总价</h4> <p class="mui-pull-right color_2fd0b5 dongPrice">';
if(detail.startNum == 0){
$out+=$escape(detail.salePrice);
}else{
$out+=$escape((detail.startNum*detail.salePrice).toFixed(1));
}
$out+='点</p> </div> </div> <div class="margin_top_15"> <div class="color_2fd0b5 ticket_buy_tips">游客资料录入(因为游客资料错误导致无法入场，网站不承担责任)</div> </div> <div class="mui-input-group"> ';
$each(fields,function(field,$index){
$out+=' ';
if(field.link == 'link_credit_type'){
$out+=' <div class="mui-input-row"> <label>手机号码</label> <select name="links[';
$out+=$escape(field.link);
$out+=']" style="border:1px #ccc solid; padding:3px;"> ';
$each(field.selects,function(select,key){
$out+=' <option value="';
$out+=$escape(key);
$out+='">';
$out+=$escape(select);
$out+='</option> ';
});
$out+=' </select> </div> ';
}else{
$out+=' ';
$out+=$escape(tip = field.tip.split('，'));
$out+=' <div class="mui-input-row"> <label>';
$out+=$escape(field.name);
$out+='</label> <input type="text" name="links[';
$out+=$escape(field.link);
$out+=']" placeholder="';
$out+=$escape(tip[1]);
$out+='"> </div> ';
}
$out+=' ';
});
$out+=' </div> <input type="hidden" name="productno" value="';
$out+=$escape(detail.productNo);
$out+='" /> <input type="hidden" name="traveldate" value="';
$out+=$escape(travelDate);
$out+='" /> </form> </div> ';
return new String($out);
});