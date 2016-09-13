/*TMODJS:{"version":1,"md5":"91c579a25578e1a0ca83a1ac9296a2d4"}*/
template('tpl/sports/tmp_venue_pay',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,order=$data.order,detail=$data.detail,$each=$utils.$each,time=$data.time,$index=$data.$index,$out='';$out+='<div class="mui-content"> <div class="margin_top_15 mui-table-view"> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">总价</h4> <p class="mui-pull-right color_2fd0b5">';
$out+=$escape(order.money);
$out+='点</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">数量</h4> <p class="mui-pull-right">';
$out+=$escape(order.total);
$out+='</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">场馆</h4> <p class="mui-pull-right">';
$out+=$escape(order.venueName);
$out+='</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">地址</h4> <p class="mui-pull-right width_60">';
$out+=$escape(detail.place);
$out+='</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">时间</h4> <p class="mui-pull-right">';
$out+=$escape(order.date);
$out+='</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">预定信息</h4> <p class="mui-pull-right sport_venue_reserve"> ';
$each(order.times_mt,function(time,$index){
$out+=' <span>';
$out+=$escape(time);
$out+='</span> ';
});
$out+=' </p> </div> </div> <div class="mui-input-group margin_top_15"> <div class="mui-input-row"> <h4 class="mui-pull-left">聚优卡号</h4> <p class="mui-pull-right">71100010995430713</p> </div> <div class="mui-input-row"> <label>密码</label> <input type="password" name="password" placeholder="请输入聚优卡密码"> <input type="hidden" name="order_id" id="order_id" value="';
$out+=$escape(order.id);
$out+='" /> </div> </div> </div> ';
return new String($out);
});