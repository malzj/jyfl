/*TMODJS:{"version":51,"md5":"0a7fd9220fe98d6af0c95fe21a49dce2"}*/
template('movie_order_detail',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,money=$data.money,cinema_name=$data.cinema_name,movie_name=$data.movie_name,featuretime=$data.featuretime,language=$data.language,screen_type=$data.screen_type,hall_name=$data.hall_name,seat_info=$data.seat_info,mobile=$data.mobile,user_name=$data.user_name,id=$data.id,$out='';$out+='<div class="mui-row mui-text-center color_ff781e remainder_time"> 支付剩余时间：<span id="times"></span> </div> <ul class="mui-table-view margin_top_15"> <li class="mui-table-view-cell"><h4 class="mui-pull-left">总价</h4><p class="mui-pull-right">';
$out+=$escape(money);
$out+='点</p></li> <li class="mui-table-view-cell"><h4 class="mui-pull-left">影院</h4><p class="mui-pull-right">';
$out+=$escape(cinema_name);
$out+='</p></li> <li class="mui-table-view-cell"><h4 class="mui-pull-left">电影</h4><p class="mui-pull-right">';
$out+=$escape(movie_name);
$out+='</p></li> <li class="mui-table-view-cell"><h4 class="mui-pull-left">场次</h4><p class="mui-pull-right">';
$out+=$escape(featuretime);
$out+='</p></li> <li class="mui-table-view-cell"><h4 class="mui-pull-left">版本</h4><p class="mui-pull-right">';
$out+=$escape(language);
$out+='/';
$out+=$escape(screen_type);
$out+='</p></li> <li class="mui-table-view-cell"><h4 class="mui-pull-left">厅号</h4><p class="mui-pull-right">';
$out+=$escape(hall_name);
$out+='</p></li> <li class="mui-table-view-cell"><h4 class="mui-pull-left">座位</h4><p class="mui-pull-right">';
$out+=$escape(seat_info);
$out+='</p></li> <li class="mui-table-view-cell"><h4 class="mui-pull-left">手机</h4><p class="mui-pull-right">';
$out+=$escape(mobile);
$out+='</p></li> </ul> <ul class="mui-table-view margin_top_15"> <li class="mui-table-view-cell"><h4 class="mui-pull-left">聚优卡号</h4><p class="mui-pull-right">';
$out+=$escape(user_name);
$out+='</p></li> <li class="mui-table-view-cell"> <div class="mui-input-row"> <label class="mui-pull-left"><h4>请输入密码</h4></label> <input type="password" name="password" id="password" class="mui-pull-right dianziquan_mima" /> </div> </li> </ul>  <p class="color_2fd0b5 mui-text-center margin_top_15">温馨提示：请确认购票信息再支付，电影票一经售出不予退换</p> <input type="hidden" id="orderid" name="order_id" value="';
$out+=$escape(id);
$out+='"/> <button id="act-pays" class="btn_next margin_top_15 margin_bottom_10">确认支付</button>';
return new String($out);
});