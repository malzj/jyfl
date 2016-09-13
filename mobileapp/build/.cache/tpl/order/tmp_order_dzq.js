/*TMODJS:{"version":1,"md5":"139fe81d5fa9b3feb76c2314c50b8015"}*/
template('tpl/order/tmp_order_dzq',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,hasOrders=$data.hasOrders,$each=$utils.$each,orders=$data.orders,order=$data.order,key=$data.key,$escape=$utils.$escape,$out='';$out+='<div class="mui-content"> ';
if(hasOrders==1){
$out+=' ';
$each(orders,function(order,key){
$out+=' <div class="mui-row order_cinemaBox margin_top_10"> <div class="mui-pull-left order_cinema_itemLeft"> <div class="order_ciema_number">订单号：<span class="order_sn">';
$out+=$escape(order.order_sn);
$out+='</span></div> <div class="order_ciema_name"><span>';
$out+=$escape(order.TicketName);
$out+='</span><span>';
$out+=$escape(order.number);
$out+='张</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.TicketYXQ);
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.CinemaName);
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.ProductSizeZn);
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.total_fee);
$out+='</span></div> </div> <div class="order_cinema_itemRight"> <div class="active">';
$out+=$escape(order.pay_status_cn);
$out+='</div> </div> </div> ';
});
$out+=' ';
}else{
$out+=' <div class="mui-row order_cinemaBox margin_top_10"> 还没有订单哦亲 </div> ';
}
$out+=' </div>';
return new String($out);
});