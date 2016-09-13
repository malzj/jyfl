/*TMODJS:{"version":1,"md5":"7e915e7d42b8fb1021399a18d0181163"}*/
template('tpl/yanchu/tmp_confirmationPay',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,order_amount=$data.order_amount,order_sn=$data.order_sn,number=$data.number,goods_amount=$data.goods_amount,shipping_fee=$data.shipping_fee,user_name=$data.user_name,order_id=$data.order_id,$out='';$out+='<nav class="mui-bar mui-bar-tab mui-row"> <div class="mui-col-xs-7"><a class="mui-tab-item">应付总额：<span class="color_2fd0b5">';
$out+=$escape(order_amount);
$out+='</span>点</a></div> <div class="mui-col-xs-5"> <a id="pay_submit" class="mui-tab-item footer_jiaru" href="#"><span class="mui-tab-label">确认支付</span></a> </div> </nav> <div class="mui-content"> <ul class="mui-table-view margin_top_15"> <li class="mui-table-view-cell"><h4 class="mui-pull-left">订单号</h4><p class="mui-pull-right">';
$out+=$escape(order_sn);
$out+='</p></li> <li class="mui-table-view-cell"><h4 class="mui-pull-left">数量</h4><p class="mui-pull-right">';
$out+=$escape(number);
$out+='</p></li> <li class="mui-table-view-cell"><h4 class="mui-pull-left">价格</h4><p class="mui-pull-right color_2fd0b5">';
$out+=$escape(goods_amount);
$out+='点</p></li> <li class="mui-table-view-cell"><h4 class="mui-pull-left">运费</h4><p class="mui-pull-right color_2fd0b5">';
$out+=$escape(shipping_fee);
$out+='点</p></li> <li class="mui-table-view-cell"><h4 class="mui-pull-left">状态</h4><p class="mui-pull-right">';
if(order_amount>0){
$out+='未付款';
}else{
$out+='已付款';
}
$out+='</p></li> </ul> <ul class="mui-table-view margin_top_15"> <li class="mui-table-view-cell"><h4 class="mui-pull-left">聚优卡号</h4><p class="mui-pull-right">';
$out+=$escape(user_name);
$out+='</p></li> <li class="mui-table-view-cell"> <div class="mui-input-row"> <label class="mui-pull-left"><h4>请输入密码</h4></label> <input type="password" name="password" class="mui-pull-right dianziquan_mima" /> <input type="hidden" name="order_id" value="';
$out+=$escape(order_id);
$out+='" /> <input type="hidden" name="order_sn" value="';
$out+=$escape(order_sn);
$out+='" /> <input type="hidden" name="order_sn" value="';
$out+=$escape(order_sn);
$out+='" /> <input type="hidden" name="order_amount" value="';
$out+=$escape(order_amount);
$out+='" /> </div> </li> </ul> </div>';
return new String($out);
});