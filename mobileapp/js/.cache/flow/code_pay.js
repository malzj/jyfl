/*TMODJS:{"version":1,"md5":"b81994ddd43f5c74e93a01369b2321c1"}*/
template('flow/code_pay',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,orders=$data.orders,mobile_phone=$data.mobile_phone,$out='';$out+='<div class="mui-row mui-text-center color_ff781e remainder_time"> <span class="pay_time_icon"></span>支付剩余时间：<span id="times"></span> </div> <ul class="mui-table-view margin_top_15"> <li class="mui-table-view-cell"><h4 class="mui-pull-left">总价</h4><p class="mui-pull-right">';
$out+=$escape(orders.order_amount);
$out+='点</p></li> <li class="mui-table-view-cell"><h4 class="mui-pull-left">商品名称</h4><p class="mui-pull-right">';
$out+=$escape(orders.goods_name);
$out+='</p></li> <li class="mui-table-view-cell"><h4 class="mui-pull-left">规格</h4><p class="mui-pull-right">';
$out+=$escape(orders.goods_attr);
$out+='</p></li> <li class="mui-table-view-cell"><h4 class="mui-pull-left">数量</h4><p class="mui-pull-right">';
$out+=$escape(orders.goods_number);
$out+='</p></li> </ul> <ul class="mui-table-view margin_top_15"> <li class="mui-table-view-cell"><h4 class="mui-pull-left">聚优卡号</h4><p class="mui-pull-right">';
$out+=$escape(orders.user_name);
$out+='</p></li> <li class="mui-table-view-cell"> <div class="mui-input-row"> <label class="mui-pull-left"><h4>请输入手机号</h4></label> <input type="text" name="mobile_phone" id="mobile_phone" value="';
$out+=$escape(mobile_phone);
$out+='" class="mui-pull-right dianziquan_mima" /> </div> </li> <li class="mui-table-view-cell"> <div class="mui-input-row"> <label class="mui-pull-left"><h4>请输入密码</h4></label> <input type="password" name="password" id="password" class="mui-pull-right dianziquan_mima" /> </div> </li> </ul>  <p class="color_2fd0b5 mui-text-center margin_top_15">温馨提示：请确认商品码信息再支付，商品码一经售出不予退换</p> <input type="hidden" id="orderid" name="order_id" value="';
$out+=$escape(orders.id);
$out+='"/> <button id="act-pays" class="btn_next margin_top_15 margin_bottom_10">确认支付</button>';
return new String($out);
});