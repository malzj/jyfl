/*TMODJS:{"version":1,"md5":"0654df180945b684be41d1c1bde64c45"}*/
template('tpl/sports/tmp_ticket_submit',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,list=$data.list,validit=$data.validit,user_name=$data.user_name,$out='';$out+='<div class="mui-content"> <div class="margin_top_15 mui-table-view"> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">订单编号</h4> <p class="mui-pull-right">';
$out+=$escape(list.api_order_id);
$out+='</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">数量</h4> <p class="mui-pull-right">';
$out+=$escape(list.num);
$out+='</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">单价</h4> <p class="mui-pull-right color_2fd0b5">';
$out+=$escape(list.price);
$out+='点</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">有效期</h4> <p class="mui-pull-right">';
$out+=$escape(validit);
$out+='</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">状态</h4> <p class="mui-pull-right">';
$out+=$escape(list.start);
$out+='</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">电话</h4> <p class="mui-pull-right">';
$out+=$escape(list.phone);
$out+='</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">共计</h4> <p class="mui-pull-right color_2fd0b5">';
$out+=$escape(list.money);
$out+='点</p> </div> </div> <div class="mui-input-group margin_top_15"> <div class="mui-input-row"> <h4 class="mui-pull-left">聚优卡号</h4> <p class="mui-pull-right">';
$out+=$escape(user_name);
$out+='</p> </div> <div class="mui-input-row"> <label>密码</label> <input type="password" name="password" placeholder="请输入聚优卡密码"> </div> </div> <input type="hidden" name="order_amount"/> <div class="color_2fd0b5 ticket_submit_tips">温馨提示:需提前1天预订，要求必须1440分钟内完成在线支付</div> </div> ';
return new String($out);
});