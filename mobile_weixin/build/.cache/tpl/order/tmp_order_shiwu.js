/*TMODJS:{"version":1,"md5":"a184995326daaaedd777252b9b6acfef"}*/
template('tpl/order/tmp_order_shiwu',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$each=$utils.$each,orders=$data.orders,porder=$data.porder,$index=$data.$index,$escape=$utils.$escape,c_order=$data.c_order,web_url=$data.web_url,goods=$data.goods,$out='';$out+=' <div class="mui-scroll-wrapper"> <div class="mui-scroll"> ';
$each(orders,function(porder,$index){
$out+=' <div class="mui-row order_shiwu_box margin_top_10"> <div class="order_shiwu_boxTitle">';
$out+=$escape(porder.p_order_sn);
$out+='</div> ';
$each(porder.c_orders,function(c_order,$index){
$out+=' <div class="order_shiwu_item"> ';
if(c_order.goods.length == 1){
$out+=' <div class="order_shiwu_item_top mui-clearfix"> <div class="shiwu_item_top_img mui-pull-left"> <img src="';
$out+=$escape(web_url);
$out+='/';
$out+=$escape(c_order.goods[0].goods_thumb);
$out+='" alt="" /> </div> <div class="shiwu_item_top_details mui-pull-left"> <div class="mui-ellipsis">';
$out+=$escape(c_order.goods[0].goods_name);
$out+='</div> <p class="shiwu_item_top_guige"><span>';
$out+=$escape(c_order.goods[0].goods_attr);
$out+='</span></p> </div> <div class="shiwu_item_top_price"> <div>';
$out+=$escape(c_order.goods[0].goods_price);
$out+='点</div> <p>X<span>';
$out+=$escape(c_order.goods[0].goods_number);
$out+='</span></p> </div> </div> ';
}else if(c_order.goods.length > 1){
$out+=' <div class="order_shiwu_item_top mui-clearfix"> <div class="mui-scroll-wrapper mui-slider-indicator mui-segmented-control mui-segmented-control-inverted"> <div class="mui-scroll"> <div class="shiwu_item_top_img mui-pull-left"> ';
$each(c_order.goods,function(goods,$index){
$out+=' <img src="';
$out+=$escape(web_url);
$out+='/';
$out+=$escape(goods.goods_thumb);
$out+='" alt="" /> ';
});
$out+=' </div> </div> </div> </div> ';
}
$out+=' <div class="order_shiwu_item_middle mui-clearfix"> <p class="mui-pull-left">订单号：<span>';
$out+=$escape(c_order.order_sn);
$out+='</span></p> <div class="mui-pull-right"> ';
if(c_order.goods.length > 1){
$out+=' <span class="shiwu_item_middle_foodsNum">共<span>';
$out+=$escape(c_order.goods.length);
$out+='</span>件商品</span> ';
}
$out+=' 总金额：<span>';
$out+=$escape(c_order.total_fee);
$out+='</span> </div> </div> <div class="order_shiwu_item_bottom mui-row"> <div class="color_coral mui-pull-left">';
$out+=$escape(c_order.order_status);
$out+='</div> <div class="mui-pull-right shiwu_item_bottom_btn"> <a id="order_detail" href="#order_details_page" data-id="';
$out+=$escape(c_order.order_id);
$out+='">订单详情</a> </div> </div> </div> ';
});
$out+=' </div> ';
});
$out+=' </div> </div>';
return new String($out);
});