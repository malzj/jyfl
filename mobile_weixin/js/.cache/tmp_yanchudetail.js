/*TMODJS:{"version":5,"md5":"1399fc59c37eea9d7332e8aaa602f4f4"}*/
template('tmp_yanchudetail',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,iteminfo=$data.iteminfo,$iteminfo=$data.$iteminfo,$each=$utils.$each,showtime=$data.showtime,time=$data.time,key=$data.key,$out='';$out+='<ul class="mui-table-view yanchu_list yanchu_details_top"> <li class="mui-table-view-cell mui-media"> <img class="mui-media-object mui-pull-left yanchu_img" src="';
$out+=$escape(iteminfo.imageUrl);
$out+='"> <div class="mui-media-body"> <h4 class="yanchu_top_name">';
$out+=$escape(iteminfo.itemName);
$out+='</h4> <p class="mui-ellipsis">';
$out+=$escape(iteminfo.startDate);
if(iteminfo.startDate){
$out+='~';
}
$out+=$escape($iteminfo.endDate);
$out+='</p> <p class="mui-ellipsis">';
$out+=$escape(iteminfo['site']['@attributes']['siteName']);
$out+='</p> </div> </li> </ul> <div class="bg_white"> <div id="segmentedControl" class="mui-segmented-control mui-segmented-control-inverted"> <a class="mui-control-item mui-active" href="#item1">快速购票</a> <a class="mui-control-item" href="#item2">演唱会详情</a> </div> </div> <div id="item1" class="mui-control-content mui-active"> <ul class="mui-table-view"> <li class="mui-table-view-cell">选择时间</li> <li class="mui-table-view-cell"> <div class="mui-scroll-wrapper mui-slider-indicator mui-segmented-control mui-segmented-control-inverted yanchu_time"> <div class="mui-scroll"> ';
$each(showtime,function(time,key){
$out+=' <a class="mui-control-item mui-active"> <span>';
if(time.shStartDate){
$out+=$escape(time.shStartDateFormat);
}
$out+='</span> <span>';
$out+=$escape(time.shEndDateFormat);
$out+='</span> </a> ';
});
$out+=' </div> </div> </li> </ul> <ul class="mui-table-view margin_top_10"> <li class="mui-table-view-cell">选择价格</li> <li class="mui-table-view-cell"> <div class="mui-scroll-wrapper mui-slider-indicator mui-segmented-control mui-segmented-control-inverted"> <div class="mui-scroll yanchu_time"> <a class="mui-control-item mui-active"> <span>180点</span> </a> <a class="mui-control-item"> <span>180点</span> </a> <a class="mui-control-item"> <span>180点</span> </a> <a class="mui-control-item"> <span>180点</span> </a> <a class="mui-control-item"> <span>180点</span> </a> <a class="mui-control-item"> <span>180点</span> </a> <a class="mui-control-item"> <span>180点</span> </a> </div> </div> </li> </ul> <div class="mui-table-view margin_top_10"> <div class="mui-table-view-cell"> <span class="vertical_align_sub">购买数量</span> <div class="mui-numbox mui-pull-right" data-numbox-min=\'1\'> <button class="mui-btn mui-btn-numbox-minus" type="button">-</button> <input id="test" class="mui-input-numbox" type="number"/> <button class="mui-btn mui-btn-numbox-plus" type="button">+</button> </div> </div> </div> </div>  <div id="item2" class="mui-control-content"> <p>演唱会演唱会演唱会演唱会演唱会演唱会</p> </div>';
return new String($out);
});