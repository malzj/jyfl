/*TMODJS:{"version":2,"md5":"8ce1a1661b1ad766eee4c550f51956cb"}*/
template('tpl/sports/tmp_sports_venue',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$each=$utils.$each,venues=$data.venues,venue=$data.venue,$index=$data.$index,$escape=$utils.$escape,venueId=$data.venueId,priceData=$data.priceData,price=$data.price,date=$data.date,list=$data.list,timeData=$data.timeData,time=$data.time,infoId=$data.infoId,secret=$data.secret,$out='';$out+='<div class="mui-content"> <div class="mui-scroll-wrapper mui-slider-indicator mui-segmented-control mui-segmented-control-inverted sports_venue_scroll margin_top_15 bg_white"> <div class="mui-scroll"> ';
$each(venues,function(venue,$index){
$out+=' <a class="mui-control-item ';
if(venue.active==1){
$out+='mui-active';
}
$out+=' href_click" data-href="./sports_venue.html?venueId=';
$out+=$escape(venueId);
$out+='&infoId=';
$out+=$escape(venue.infoId);
$out+='&orderDate=';
$out+=$escape(venue.date);
$out+='"> <span>';
$out+=$escape(venue.week);
$out+='</span> <span class="color_8f8f94">';
$out+=$escape(venue.date_mt);
$out+='</span> </a> ';
});
$out+=' </div> </div> <div class="selectday margin_top_15"> <div class="seletimes" id="seletimes"> <div style="width: 10000px; padding-left: 5rem;"> ';
$each(priceData,function(price,$index){
$out+=' <dl data-date="';
$out+=$escape(date);
$out+='" venue-no="';
$out+=$escape(price.rows);
$out+='"> <dt>';
$out+=$escape(price.rows);
$out+='号场</dt> ';
$each(price.list,function(list,$index){
$out+=' <dd class="';
if(list.num == 0){
$out+='not';
}else{
$out+='vv_sel';
}
$out+='" data-price="';
$out+=$escape(list.salePrice);
$out+='" data-fee="0" data-sale-price="';
$out+=$escape(list.salePrice);
$out+='" data-id="';
$out+=$escape(price.rows);
$out+=$escape(list.sTime);
$out+='" data-clock="';
$out+=$escape(list.sTime_mt);
$out+='" data-s="';
$out+=$escape(list.sTime);
$out+='" data-e="';
$out+=$escape(list.eTime);
$out+='">';
if(list.num == 0){
$out+='已预订';
}else{
$out+=$escape(list.salePrice);
$out+='点';
}
$out+='</dd> ';
});
$out+=' </dl> ';
});
$out+=' </div> </div> <div class="selectnom"> <div style="height: 50px;"></div> ';
$each(timeData,function(time,$index){
$out+=' <em>';
$out+=$escape(time);
$out+='</em> ';
});
$out+=' </div> </div> <div class="selestatus"> <span><font></font>已选择</span> <span><i></i>可订</span> <span><em></em>不可订</span> </div> <form id="venueinfo"> <input type="hidden" name="info_id" value="';
$out+=$escape(infoId);
$out+='"> <input type="hidden" name="travel_date" id="travel_date" value="';
$out+=$escape(date);
$out+='"> <input type="hidden" name="num" id="num" value=""> <input type="hidden" name="amount" id="amount" value=""> <input type="hidden" name="param" id="param" value=""> <input type="hidden" name="venue_id" id="venue_id" value="';
$out+=$escape(venueId);
$out+='"> <input type="hidden" name="venues" id="venues" value=""> <input type="hidden" name="secret" id="secret" value="';
$out+=$escape(secret);
$out+='"> </form> </div> ';
return new String($out);
});