/*TMODJS:{"version":1,"md5":"7ae3e596b635f2468e56c8a0a1e6f24c"}*/
template('tpl/yanchu/tmp_yanchu_detail',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,iteminfo=$data.iteminfo,$each=$utils.$each,showtime=$data.showtime,time=$data.time,key=$data.key,val=$data.val,k=$data.k,$string=$utils.$string,$out='';$out+='<div id="yuanchu_detail" class="mui-content"> <ul class="mui-table-view yanchu_list yanchu_details_top" style="background-image: url(';
$out+=$escape(iteminfo.imageUrl);
$out+=');"> <li class="mui-table-view-cell mui-media"> <img class="mui-media-object mui-pull-left yanchu_img" src="';
$out+=$escape(iteminfo.imageUrl);
$out+='"> <div class="mui-media-body"> <h4 class="yanchu_top_name">';
$out+=$escape(iteminfo.itemName);
$out+='</h4> <p class="mui-ellipsis">';
$out+=$escape(iteminfo.startDate);
if(iteminfo.startDate){
$out+='~';
}
$out+=$escape(iteminfo.endDate);
$out+='</p> <p class="mui-ellipsis">';
$out+=$escape(iteminfo['site']['@attributes']['siteName']);
$out+='</p> </div> </li> </ul> <div class="bg_white"> <div id="segmentedControl" class="mui-segmented-control mui-segmented-control-inverted"> <a class="mui-control-item mui-active" href="#item1">快速购票</a> <a class="mui-control-item" href="#item2">演唱会详情</a> </div> </div> <div id="item1" class="mui-control-content mui-active"> <form id="yanchu_form" name="yanchu_form" onclick="return false"> <ul class="mui-table-view"> <li class="mui-table-view-cell">选择时间</li> <li class="mui-table-view-cell"> <div class="mui-scroll-wrapper mui-slider-indicator mui-segmented-control mui-segmented-control-inverted yanchu_time"> <div class="mui-scroll"> ';
$each(showtime,function(time,key){
$out+=' <a class="time mui-control-item ';
if(key==0){
$out+='mui-active';
}
$out+='" data-key="';
$out+=$escape(key);
$out+='"> <span>';
$out+=$escape(time.shEndDateFormat[0]);
$out+='</span> <span>';
$out+=$escape(time.shEndDateFormat[1]);
$out+='</span> <input type="radio" name="time" id="time" ';
if(key==0){
$out+='checked';
}
$out+=' value="';
$out+=$escape(time.shEndDate);
$out+='" style="display:none;"/> <input type="radio" name="status" id="status" value="';
$out+=$escape(time.status);
$out+='" ';
if(key==0){
$out+='checked';
}
$out+=' style="display:none;"/> </a> ';
});
$out+=' </div> </div> </li> </ul> <ul class="mui-table-view margin_top_10"> <li class="mui-table-view-cell">选择价格</li> <li class="mui-table-view-cell"> <div class="mui-scroll-wrapper mui-slider-indicator mui-segmented-control mui-segmented-control-inverted"> <div id="yanchu_time" class="mui-scroll yanchu_time"> ';
$each(showtime,function(time,key){
$out+=' ';
if(key==0){
$out+=' ';
$each(time.specs,function(val,k){
$out+=' <a class="mui-control-item ';
if(val.stock == 0){
$out+='stock-off';
}
$out+='"> <span>';
if((val.layout != undefined||val.layout != null)){
$out+=$escape(val.layout);
}else{
$out+=$escape(val.price);
}
$out+='点</span> <input type="radio" name="price" value="';
$out+=$escape(val.price);
$out+='" style="display:none;"/> <input type="radio" name="market_price" value="';
$out+=$escape(val.market_price);
$out+='" style="display:none;"/> <input type="radio" name="specid" value="';
$out+=$escape(val.specId);
$out+='" style="display:none;"/> <input type="radio" name="stock" value="';
$out+=$escape(val.stock);
$out+='" style="display:none;"/> </a> ';
});
$out+=' ';
}
$out+=' ';
});
$out+=' </div> </div> </li> </ul> <div class="mui-table-view margin_top_10"> <div class="mui-table-view-cell"> <span class="vertical_align_sub">购买数量</span> <div class="mui-numbox mui-pull-right" data-numbox-min="1"> <button class="mui-btn mui-btn-numbox-minus" type="button">-</button> <input id="number" class="mui-input-numbox" type="number" name="number"/> <button class="mui-btn mui-btn-numbox-plus" type="button">+</button> </div> </div> </div> <input type="hidden" name="id" value="';
$out+=$escape(iteminfo.itemId);
$out+='"/> <input type="hidden" name="storeId" value="';
$out+=$escape(iteminfo['store']['@attributes']['storeId']);
$out+='"/> <input type="hidden" name="storeName" value="';
$out+=$escape(iteminfo['store']['@attributes']['storeName']);
$out+='"/> </form> </div>  <div id="item2" class="mui-control-content public_xiangqing_img"> <p> ';
$out+=$string(iteminfo.description);
$out+=' </p> <p></p> </div> </div>';
return new String($out);
});