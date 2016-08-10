/*TMODJS:{"version":1,"md5":"3a525e24880a5ecea48c9ca5e65f6158"}*/
template('tpl/flow/pay',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,data=$data.data,$out='';$out+=' <nav class="mui-bar mui-bar-tab mui-row"> <div class="mui-col-xs-7"><a class="mui-tab-item">合计：<span class="color_2fd0b5">';
$out+=$escape(data.order.order_amount);
$out+='</span>点</a></div> <div class="mui-col-xs-5"> <a class="mui-tab-item footer_jiaru" href="#"><span class="mui-tab-label">确认支付</span></a></div> </nav> <div class="mui-content"> <div class="mui-row confirm_img"> <h4 class="mui-text-center">订单已提交成功，共计<span> ';
$out+=$escape(data.order.order_amount);
$out+=' </span>点，请尽快付款</h4> </div> <ul class="mui-table-view confirm_list"> <li class="mui-table-view-cell"> <h4 class="mui-pull-left">聚优卡号</h4> <p class="mui-pull-right">';
$out+=$escape(data.users.user_name);
$out+='</p> </li> <li class="mui-table-view-cell mui-input-row"> <label><h4>请输入密码</h4></label> <input type="password" placeholder="请输入密码"/> </li> </ul> </div> <script> var lock = false; jQuery(\'nav\').on(\'tap\',\'.footer_jiaru\',function(){ var password = jQuery(\'input[type=password]\').val(); var order_id = \'';
$out+=$escape(data.order.order_id);
$out+='\'; if(lock == true){ mui.alert(\'支付中，请耐心等待\'); return false; } if(jQuery.trim(password) == \'\'){ mui.alert(\'密码不能为空\'); return false; } lock = true; jQuery.ajaxJsonp( web_url+"/mobile/flow.php", { step:\'act_pay\', order_id:order_id, password:password }, function(data){ if(data.state == \'false\'){ jQuery.errorJudge(data, data.message); lock = false; }else{ mui.alert(\'支付成功\',function(){ mui.openWindow({ url:\'../jy_index.html\', id:\'jyindex\' }); }); } } ); }); </script> ';
return new String($out);
});