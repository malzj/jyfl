/*TMODJS:{"version":1,"md5":"0b751ec89d883d3933bd1456de403c66"}*/
template('tpl/order/tmp_order_shiwu_detail',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,order=$data.order,$each=$utils.$each,goods_list=$data.goods_list,goods=$data.goods,$index=$data.$index,web_url=$data.web_url,user_name=$data.user_name,$out='';$out+=' <div class="mui-page-content"> <div class="mui-scroll-wrapper"> <div class="mui-scroll"> <div class="order_shiwu_address"> <h4>';
$out+=$escape(order.consignee);
$out+=' ';
$out+=$escape(order.mobile);
$out+='</h4> <p class="mui-ellipsis">';
$out+=$escape(order.address);
$out+='</p> </div> <div class="order_shiwu_item margin_top_10"> <div class="order_shiwu_boxTitle mui-clearfix"> <span class="mui-pull-left">';
$out+=$escape(order.order_sn);
$out+='</span> <span class="color_coral mui-pull-right">';
$out+=$escape(order.pay_status_cn);
$out+='</span> </div>  ';
$each(goods_list,function(goods,$index){
$out+=' <div class="order_shiwu_item_top mui-clearfix"> <div class="shiwu_item_top_img mui-pull-left"> <img src="';
$out+=$escape(web_url);
$out+='/';
$out+=$escape(goods.goods_thumb);
$out+='" alt="" /> </div> <div class="shiwu_item_top_details mui-pull-left"> <div class="mui-ellipsis">';
$out+=$escape(goods.goods_name);
$out+='</div> <p class="shiwu_item_top_guige"><span>';
$out+=$escape(goods.goods_attr);
$out+='</span></p> </div> <div class="shiwu_item_top_price"> <div>';
$out+=$escape(goods.goods_price);
$out+='点</div> <p>X<span>';
$out+=$escape(goods.goods_number);
$out+='</span></p> </div> </div> ';
});
$out+=' </div> <ul class="mui-table-view margin_top_10"> <li class="mui-table-view-cell">支付方式<span class="mui-pull-right">';
$out+=$escape(order.pay_name);
$out+='</span></li> ';
if(order.pay_statuses==2){
$out+=' <li class="mui-table-view-cell">付款时间<span class="mui-pull-right">';
$out+=$escape(order.pay_time);
$out+='</span></li> ';
}
$out+=' <li class="mui-table-view-cell">下单时间<span class="mui-pull-right">';
$out+=$escape(order.formated_add_time);
$out+='</span></li> <li class="mui-table-view-cell">运费<span class="mui-pull-right">';
$out+=$escape(order.formated_shipping_fee);
$out+='</span></li> <li class="mui-table-view-cell">实付点数<span class="mui-pull-right">';
$out+=$escape(order.formated_total_fee);
$out+='</span></li> </ul> ';
if(order.pay_statuses==0&&order.order_status!=3){
$out+=' <ul class="mui-table-view margin_top_10"> <li class="mui-table-view-cell">聚优卡号<span class="mui-pull-right">';
$out+=$escape(user_name);
$out+='</span></li> <li class="mui-table-view-cell mui-input-row"> <label>请输入密码</label> <input name="password" type="password"/> <input name="order_id" type="hidden" value="';
$out+=$escape(order.order_id);
$out+='"/> </li> </ul> <div class="mui-button-row"> <button type="button" class="mui-btn order_shiwu_fukuan">付款</button> </div> ';
}
$out+=' </div> </div> </div>';
return new String($out);
});