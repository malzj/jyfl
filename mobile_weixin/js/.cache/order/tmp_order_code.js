/*TMODJS:{"version":20,"md5":"3e23a1009f0b75fcc25ab560c41e45f8"}*/
template('order/tmp_order_code',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,hasOrders=$data.hasOrders,$each=$utils.$each,orders=$data.orders,order=$data.order,key=$data.key,$escape=$utils.$escape,$out='';$out+='<div class="mui-content"> ';
if(hasOrders==1){
$out+=' ';
$each(orders,function(order,key){
$out+=' <div class="mui-row order_cinemaBox margin_top_10"> <div class="mui-pull-left order_cinema_itemLeft"> <div class="order_ciema_number">订单号：<span class="order_sn">';
$out+=$escape(order.order_sn);
$out+='</span></div> <div class="order_ciema_name"><span>';
$out+=$escape(order.add_time);
$out+='</span></div> <div class="order_ciema_name">商品名称：<span>';
$out+=$escape(order.goods_name);
$out+='</span></div> <div class="order_ciema_details">';
$out+=$escape(order.goods_attr);
$out+='</div> <div class="order_ciema_details">数量：<span>';
$out+=$escape(order.goods_number);
$out+='个</span></div> <div class="order_ciema_details">单价：<span>';
$out+=$escape(order.sjprice);
$out+='点</span></div> <div class="order_ciema_details">总价：<span>';
$out+=$escape(order.total_fee);
$out+='点</span></div> </div> <div class="order_cinema_itemRight"> <div class="active">';
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