/*TMODJS:{"version":1,"md5":"1e498893d930d63cf5098907f5be8e7d"}*/
template('tpl/order/tmp_order_venues',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,hasOrders=$data.hasOrders,$each=$utils.$each,orders=$data.orders,order=$data.order,key=$data.key,$escape=$utils.$escape,time=$data.time,$index=$data.$index,$string=$utils.$string,$out='';$out+='<div class="mui-content"> ';
if(hasOrders==1){
$out+=' ';
$each(orders,function(order,key){
$out+=' <div class="mui-row order_cinemaBox margin_top_10"> <div class="mui-pull-left order_cinema_itemLeft"> <div class="order_ciema_number">订单号：<span class="order_sn">';
$out+=$escape(order.order_sn);
$out+='</span></div> <div class="order_ciema_name"><span>';
$out+=$escape(order.venueName);
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.place);
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.date);
$out+='</span></div> <div class="order_ciema_details"><span>';
$each(order.times_mt,function(time,$index){
$out+='<span style="display:inline-block; color:green;">';
$out+=$escape(time);
$out+='</span>';
});
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.link_man);
$out+=$escape(order.link_phone);
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.money);
$out+='</span></div> </div> <div class="order_cinema_itemRight"> <div class="active">';
$out+=$string(order.order_state_sn);
$out+='</div> </div> </div> ';
});
$out+=' ';
}else{
$out+=' <div class="mui-row order_cinemaBox margin_top_10"> 还没有订单哦亲 </div> ';
}
$out+=' </div>';
return new String($out);
});