/*TMODJS:{"version":52,"md5":"5d37fcb79386f34386e852725723125d"}*/
template('order/tmp_order_cinema',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,hasOrders=$data.hasOrders,$each=$utils.$each,orders=$data.orders,order=$data.order,key=$data.key,$escape=$utils.$escape,$out='';$out+='<div class="mui-content"> ';
if(hasOrders==1){
$out+=' ';
$each(orders,function(order,key){
$out+=' <div class="mui-row order_cinemaBox margin_top_10"> <div class="mui-pull-left order_cinema_itemLeft"> <div class="order_ciema_number">订单号：<span class="order_sn">';
$out+=$escape(order.order_sn);
$out+='</span></div> <div class="order_ciema_name"><span>';
$out+=$escape(order.movie_name);
$out+='</span><span>';
$out+=$escape(order.count);
$out+='张</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.featuretime);
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.cinema_name);
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.seat_info);
$out+='</span></div> <div class="order_ciema_details"><span>';
$out+=$escape(order.money);
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