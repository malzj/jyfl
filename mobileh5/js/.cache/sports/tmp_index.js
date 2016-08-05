/*TMODJS:{"version":37,"md5":"267d0bd533830e0a688a18a0eb6079f9"}*/
template('sports/tmp_index',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$each=$utils.$each,area_list=$data.area_list,area=$data.area,$index=$data.$index,$escape=$utils.$escape,list=$data.list,row=$data.row,$out='';$out+='<div class="mui-content"> <div class="sports_top mui-clearfix"> <div class="sports_item"> <a href="#" data-code="0L20"><img src="../../mobileh5/images/sports/yumaoqiu.svg" alt="" />羽毛球</a> </div> <div class="sports_item"> <a href="#" data-code="0L11"><img src="../../mobileh5/images/sports/pinpangqiu.svg" alt="" />乒乓球</a> </div> <div class="sports_item"> <a href="#" data-code="0L18"><img src="../../mobileh5/images/sports/wangqiu.svg" alt="" />网球</a> </div> <div class="sports_item"> <a href="#" data-code="0L06"><img src="../../mobileh5/images/sports/lanqiu.svg" alt="" />篮球</a> </div> <div class="sports_item"> <a href="#" data-code="0L22"><img src="../../mobileh5/images/sports/zuqiu.svg" alt="" />足球</a> </div> <div class="sports_item"> <a href="#" data-code="0L16"><img src="../../mobileh5/images/sports/taiqiu.svg" alt="" />台球</a> </div> <div class="sports_item"> <a href="#" data-code="0L04"><img src="../../mobileh5/images/sports/gaoerfu.svg" alt="" />高尔夫</a> </div> <div class="sports_item"> <a href="#" data-code="0N07"><img src="../../mobileh5/images/sports/youyong.svg" alt="" />游泳</a> </div> <div class="sports_item"> <a href="#" data-code="0P02"><img src="../../mobileh5/images/sports/jianshen.svg" alt="" />有氧健身</a> </div> <div class="sports_item"> <a href="#" data-code="0P09"><img src="../../mobileh5/images/sports/yujia.svg" alt="" />瑜伽</a> </div> </div> <div class="mui-table-view margin_top_20 sports_area_btn"> <div class="mui-table-view-cell mui-text-center"> <span>区域</span><span class="mui-icon mui-icon-arrowdown"></span> </div> </div>  <div class="sports_area mui-row mui-text-center" style="display: none;"> ';
$each(area_list,function(area,$index){
$out+=' <div class="mui-col-xs-3 sports_area_item" data-dongwang-id="';
$out+=$escape(area.dongwang_id);
$out+='">';
$out+=$escape(area.region_name);
$out+='</div> ';
});
$out+=' </div>  <div class="mui-table-view sports_list"> ';
$each(list,function(row,$index){
$out+=' <div class="mui-table-view-cell"> <div class="sports_list_img mui-pull-left"> <img src="';
$out+=$escape(row.venue.signImg);
$out+='" alt="" /> </div> <div class="sports_list_center mui-pull-left"> <h4 class="mui-ellipsis">';
$out+=$escape(row.venue.venueName);
$out+='</h4> <div class="mui-ellipsis color_8f8f94">';
$out+=$escape(row.venue.sportName);
$out+='</div> <div class="color_8f8f94">400-662-5170 ';
if(row.venue.tel400){
$out+='转 ';
$out+=$escape(row.venue.tel400);
}
$out+='</div> <div class="mui-ellipsis color_8f8f94">';
$out+=$escape(row.venue.place);
$out+='</div> </div> <div class="sports_list_right mui-pull-right"> <div class="sports_list_price"><span class="color_coral">';
$out+=$escape(row.venue.salePrice);
$out+='</span>点起</div> <button type="button" class="sports_list_btn href_click" data-href="./details.html?venueId=';
$out+=$escape(row.venue.venueId);
$out+='">查看场馆</button> </div> </div> ';
});
$out+=' </div> </div> <script> </script>';
return new String($out);
});