/*TMODJS:{"version":1,"md5":"9b8e1ae98f5be18c4a01faabdb725ca7"}*/
template('tpl/order/tmp_order_yanchu',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,hasOrders=$data.hasOrders,$each=$utils.$each,orders=$data.orders,order=$data.order,key=$data.key,$escape=$utils.$escape,$out='';$out+='<div class="mui-content"> ';
if(hasOrders==1){
$out+=' ';
$each(orders,function(order,key){
$out+=' <div class="mui-row order_cinemaBox margin_top_10"> <div class="mui-pull-left order_cinema_itemLeft"> <div class="order_ciema_number">订单号：<span class="order_sn">';
$out+=$escape(order.order_sn);
$out+='</span></div> <div class="order_ciema_name"><span>';
$out+=$escape(order.itemname);
$out+='</span><span>';
$out+=$escape(order.number);
$out+='张</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.best_time);
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.sitename);
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.catename);
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.consignee);
$out+=$escape(order.regionname);
$out+=$escape(order.address);
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.mobile);
if(order.tel){
$out+='(';
$out+=$escape(order.tel);
$out+=')';
}
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.total_fee);
$out+='</span></div> </div> <div class="order_cinema_itemRight"> <div class="active">';
$out+=$escape(order.order_status_cn);
$out+='</div> </div> </div> ';
});
$out+=' ';
}else{
$out+=' <div class="mui-row order_cinemaBox margin_top_10"> 还没有订单哦亲 </div> ';
}
$out+=' </div>';
return new String($out);
});