/*TMODJS:{"version":2,"md5":"13943dcfe5b56b82d7437d4742911c6c"}*/
template('tpl/sports/tmp_details',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$each=$utils.$each,imgList=$data.imgList,img=$data.img,key=$data.key,$escape=$utils.$escape,detail=$data.detail,venues=$data.venues,venue=$data.venue,$index=$data.$index,venueId=$data.venueId,ticket=$data.ticket,tic=$data.tic,$out='';$out+='<div class="mui-content"> <div class="sports_details_top bg_white mui-clearfix"> <div class="mui-content-padded mui-pull-left"> ';
$each(imgList,function(img,key){
$out+=' <div class="sports_top_img" ';
if(key > 0){
$out+='style="display: none;"';
}
$out+='> <img src="';
$out+=$escape(img);
$out+='" data-preview-src="" data-preview-group="1" /> </div> ';
});
$out+='  <div class="sports_top_imgNum">相册(<span>';
$out+=$escape(imgList.length);
$out+='</span>)</div> </div> <div class="mui-pull-left sports_top_right mui-ellipsis"> <h4 class="mui-ellipsis">';
$out+=$escape(detail.venueName);
$out+='</h4> <p>营业时间：<span>';
$out+=$escape(detail.stime);
$out+='~';
$out+=$escape(detail.etime);
$out+='</span></p> </div> </div> <div class="mui-table-view"> <div class="mui-table-view-cell mui-ellipsis"><span class="mui-icon iconfont icon-dizhi"></span>';
$out+=$escape(detail.place);
$out+='</div> <div class="mui-table-view-cell"><span class="mui-icon iconfont icon-dianhua"></span><a href="tel:400-662-5170" style="display: inline-block;">400-662-5170 ';
if(detail.tel400){
$out+='转 ';
$out+=$escape(detail.tel400);
}
$out+='</a></div> </div> <div class="margin_top_10"> <div class="bg_white"> <div id="segmentedControl" class="mui-segmented-control mui-segmented-control-inverted"> <a class="mui-control-item mui-active" href="#item1">场地</a> <a class="mui-control-item" href="#item2">门票</a> </div> </div> <div id="item1" class="mui-control-content sports_details_item1 mui-active bg_white"> <div class="mui-scroll-wrapper mui-slider-indicator mui-segmented-control mui-segmented-control-inverted"> <div class="mui-scroll"> ';
if(venues.length){
$out+=' ';
$each(venues,function(venue,$index){
$out+=' <a class="mui-control-item href_click" data-href="./sports_venue.html?venueId=';
$out+=$escape(venueId);
$out+='&infoId=';
$out+=$escape(venue.infoId);
$out+='&orderDate=';
$out+=$escape(venue.date);
$out+='"> <span>';
$out+=$escape(venue.date_mt);
$out+='</span> <div class="color_8f8f94"><span class="sports_details_date">';
$out+=$escape(venue.week);
$out+='</span><span class="color_coral">';
$out+=$escape(venue.salePrice);
$out+='</span>点起</div> </a> ';
});
$out+=' ';
}else{
$out+=' <div class="mui-row"> 亲，这里没有场地，看看门票吧！ </div> ';
}
$out+=' </div> </div> </div> <div id="item2" class="mui-control-content sports_details_item2"> <ul class="mui-table-view"> ';
if(ticket.length){
$out+=' ';
$each(ticket,function(tic,$index){
$out+=' <li class="mui-table-view-cell"> <div class="mui-row"> <div class="mui-col-xs-6 mui-ellipsis">';
$out+=$escape(tic.infoTitle);
$out+='</div> <div class="mui-col-xs-2 mui-text-right color_8f8f94"><i class="color_coral">';
$out+=$escape(tic.salePrice);
$out+='</i>点</div> <div class="mui-col-xs-4 mui-text-right"><button class="sports_details_ticket href_click" data-href="./ticket_buy.html?productno=';
$out+=$escape(tic.infoId);
$out+='" type="button">立即预定</button></div> </div> </li> ';
});
$out+=' ';
}else{
$out+=' <li class="mui-table-view-cell"> <div class="mui-row"> 亲，这里没有门票！ </div> </li> ';
}
$out+=' </ul> </div> </div> <div class="margin_top_10 mui-table-view"> <div class="mui-table-view-cell"> <h4>场馆信息</h4> </div> <div class="mui-table-view-cell">';
$out+=$escape(detail.feature);
$out+='</div> </div> <div class="sports_details_middle"></div> <div class="sports_details_bottom"></div> </div> ';
return new String($out);
});