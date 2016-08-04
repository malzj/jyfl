/*TMODJS:{"version":31,"md5":"a1dc758dcaa84109752678974589d141"}*/
template('sports/tmp_ticket_buy',function($data,$filename
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
$out+='</h4> <p>营业时间：<span>';
$out+=$escape(detail.businessHours);
$out+='</span></p> </div> </div> <div class="mui-table-view"> <div class="mui-table-view-cell mui-ellipsis"><span class="mui-icon iconfont icon-dizhi"></span>';
$out+=$escape(detail.viewAddress);
$out+='</div> <div class="mui-table-view-cell"><span class="mui-icon iconfont icon-dianhua"></span><a href="tel:400-662-5170" style="display: inline-block;">400-662-5170 ';
if(detail.tel400){
$out+='转 ';
$out+=$escape(detail.tel400);
}
$out+='</a></div> </div>  <div class="margin_top_10 calendarBox bg_white"> <div class="calendar_title">2016年五月价格日历（选择游玩日期预定）</div> <table border="0"> <tr><th>日</th><th>一</th><th>二</th><th>三</th><th>四</th><th>五</th><th>六</th></tr> <tr> <td><div>1<span class="calendar_price"></span></div></td> <td><div>2<span class="calendar_price">72点</span></div></td> <td><div>3<span class="calendar_price">72点</span></div></td> <td class="calendar_buy"><div>4<span class="calendar_price">72点</span></div></td> <td><div>5<span class="calendar_price">72点</span></div></td> <td><div>6<span class="calendar_price">72点</span></div></td> <td class="calendar_buy"><div>7<span class="calendar_price">172点</span></div></td> </tr> <tr> <td><div>1<span class="calendar_price">72点</span></div></td> <td><div>2<span class="calendar_price">72点</span></div></td> <td><div>3<span class="calendar_price"></span></div></td> <td class="calendar_buy"><div>4<span class="calendar_price"></span></div></td> <td class="calendar_buy"><div>5<span class="calendar_price"></span></div></td> <td class="calendar_buy"><div>6<span class="calendar_price"></span></div></td> <td class="calendar_buy"><div>7<span class="calendar_price"></span></div></td> </tr> <tr> <td class="calendar_buy active"><div>1<span class="calendar_price">72点</span></div></td> <td class="calendar_buy"><div>2<span class="calendar_price">72点</span></div></td> <td><div>3<span class="calendar_price"></span></div></td> <td><div>4<span class="calendar_price"></span></div></td> <td><div>5<span class="calendar_price"></span></div></td> <td><div>6<span class="calendar_price"></span></div></td> <td><div>7<span class="calendar_price"></span></div></td> </tr> <tr> <td><div>1<span class="calendar_price">72点</span></div></td> <td><div>2<span class="calendar_price">72点</span></div></td> <td><div>3<span class="calendar_price"></span></div></td> <td><div>4<span class="calendar_price"></span></div></td> <td><div>5<span class="calendar_price"></span></div></td> <td><div>6<span class="calendar_price"></span></div></td> <td><div>7<span class="calendar_price"></span></div></td> </tr> </table> </div> </div> <script> loadCalendar(productno,date); </script>';
return new String($out);
});