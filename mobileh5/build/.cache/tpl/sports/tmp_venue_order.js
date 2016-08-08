/*TMODJS:{"version":1,"md5":"2b728610966559c180b9168d557e8c96"}*/
template('tpl/sports/tmp_venue_order',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,amount=$data.amount,num=$data.num,travel_date=$data.travel_date,$each=$utils.$each,venues=$data.venues,venue=$data.venue,$index=$data.$index,$out='';$out+='<div class="mui-content"> <div class="margin_top_15 mui-table-view"> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">总价</h4> <p class="mui-pull-right color_2fd0b5">';
$out+=$escape(amount);
$out+='点</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">数量</h4> <p class="mui-pull-right">';
$out+=$escape(num);
$out+='</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">时间</h4> <p class="mui-pull-right">';
$out+=$escape(travel_date);
$out+='</p> </div> <div class="mui-table-view-cell"> <h4 class="mui-pull-left">预定信息</h4> <p class="mui-pull-right sport_venue_reserve"> ';
$each(venues,function(venue,$index){
$out+=' <span>';
$out+=$escape(venue);
$out+='</span> ';
});
$out+=' </p> </div> </div> <div class="mui-input-group margin_top_15"> <div class="mui-input-group"> <div class="mui-input-row"> <label>你的名字</label> <input type="text" name="link_man" placeholder="凭有效证件的姓名填写"> </div> <div class="mui-input-row"> <label>手机号码</label> <input type="number" name="link_phone" placeholder="请输入手机号"> </div> </div> </div> </div>';
return new String($out);
});