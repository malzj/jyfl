/*TMODJS:{"version":1,"md5":"bd64b63c1e10c49c505c794e18173002"}*/
template('tpl/sports/tmp_ticket_buy',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$each=$utils.$each,detail=$data.detail,img=$data.img,key=$data.key,$escape=$utils.$escape,$out='';$out+='<div class="mui-content"> <div class="sports_details_top bg_white mui-clearfix"> <div class="mui-content-padded mui-pull-left"> ';
$each(detail.imgs,function(img,key){
$out+=' <div class="sports_top_img" ';
if(key > 0){
$out+='style="display: none;"';
}
$out+='> <img src="';
$out+=$escape(img);
$out+='" data-preview-src="" data-preview-group="1" /> </div> ';
});
$out+='  <div class="sports_top_imgNum">相册(<span>';
$out+=$escape(detail.imgs.length);
$out+='</span>)</div> </div> <div class="mui-pull-left sports_top_right"> <h4 class="mui-ellipsis">';
$out+=$escape(detail.productName);
$out+='</h4> <p class="mui-ellipsis-2">营业时间：<span>';
$out+=$escape(detail.businessHours);
$out+='</span></p> </div> </div> <div class="mui-table-view"> <div class="mui-table-view-cell mui-ellipsis"><span class="mui-icon iconfont icon-dizhi"></span>';
$out+=$escape(detail.viewAddress);
$out+='</div> <div class="mui-table-view-cell"><span class="mui-icon iconfont icon-dianhua"></span><a href="tel:400-662-5170" style="display: inline-block;">400-662-5170 ';
if(detail.tel400){
$out+='转 ';
$out+=$escape(detail.tel400);
}
$out+='</a></div> </div>  <div class="margin_top_10 calendarBox bg_white"></div> </div> <script> loadCalendar(productno,date); </script>';
return new String($out);
});