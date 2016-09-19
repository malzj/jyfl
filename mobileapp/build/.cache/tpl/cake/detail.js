/*TMODJS:{"version":2,"md5":"ec1e9b856c1161b0522879e65b17913f"}*/
template('tpl/cake/detail',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,data=$data.data,$each=$utils.$each,$value=$data.$value,$index=$data.$index,$escape=$utils.$escape,spec=$data.spec,propertie=$data.propertie,value=$data.value,$string=$utils.$string,$out='';$out+=' <nav class="mui-bar mui-bar-tab mui-row"> <div class="mui-col-xs-5"> <a class="mui-tab-item border_right_ddd jump-home" href="#"> <span class="mui-icon mui-icon-home"></span> <span class="mui-tab-label">首页</span> </a> <a class="mui-tab-item border_right_ddd jump-cart" href="#"> <span class="mui-icon iconfont icon-gouwuche"></span> <span class="mui-tab-label">购物车</span> <span class="mui-badge mui-badge-danger buy_car_count">0</span> </a>  </div> <div class="mui-col-xs-7"> <a class="mui-tab-item footer_liji act-done" href="#"> <span class="mui-tab-label">立即结算</span> </a> <a class="mui-tab-item footer_jiaru act-cart" href="#"> <span class="mui-tab-label">加入购物车</span> </a> </div> </nav>  <div class="mui-content"> ';
if(data){
$out+=' <div id="slider" class="mui-slider"> <div class="mui-slider-group"> ';
$each(data.pictures,function($value,$index){
$out+=' <div class="mui-slider-item"> <a href="#"> <img src="';
$out+=$escape($value.img_url);
$out+='"> </a> </div> ';
});
$out+=' </div> <div class="mui-slider-indicator"> ';
$each(data.pictures,function($value,$index){
$out+=' <div class="mui-indicator ';
if($index == 0){
$out+=' mui-active';
}
$out+='"></div> ';
});
$out+=' </div> </div>  <div class="cake_details_title mui-row bg_white"> <div class="mui-col-xs-8"> <h4>';
$out+=$escape(data.goods.goods_name);
$out+='</h4> <span class="color_coral" id=\'price-total\'>--</span> </div> <div class="mui-col-xs-4 mui-text-right"> <div class="mui-numbox" data-numbox-min=\'1\'> <button class="mui-btn-numbox-minus" type="button">-</button> <input class="mui-input-numbox" type="number" name="number" id="number" /> <button class="mui-btn-numbox-plus" type="button">+</button> </div> </div> </div> <form action="javascript:addToCart({$goods.goods_id})" method="post" name="ECS_FORMBUY" id="ECS_FORMBUY" onsubmit="return false"> <div class="container xiangqing bg_white"> <div class="mui-row"> <div class="mui-col-xs-3"> <h5 class="mui-pull-right guige_1">规格：</h5> </div> <div class="mui-col-xs-9"> ';
$each(data.specs,function($value,$index){
$out+=' <label for="spec_value_S_';
$out+=$escape($value.spec_nember);
$out+='"><h6 class="spec-change';
if($index == 0){
$out+=' checked ';
}
$out+='" data-id="S_';
$out+=$escape($value.spec_nember);
$out+='">';
$out+=$escape($value.spec_name);
$out+='</h6> <input type="radio" name="spec_s_100" value="S_';
$out+=$escape($value.spec_nember);
$out+='" ';
if($index == 0){
$out+=' checked ';
}
$out+=' id="spec_value_S_';
$out+=$escape($value.spec_nember);
$out+='" style="display: none;" /></label> ';
});
$out+=' </div> </div> ';
$each(data.specification,function(spec,$index){
$out+=' <div class="mui-row"> <div class="mui-col-xs-3"> <h5 class="mui-pull-right guige_1">';
$out+=$escape(spec.name);
$out+='：</h5> </div> ';
if(spec.attr_type == 1){
$out+=' <div class="mui-col-xs-9 pt3"> ';
$each(spec.values,function($value,$index){
$out+=' <label for="spec_value_';
$out+=$escape($value.id);
$out+='"><h6 class="spec-change';
if($index ==0){
$out+=' checked ';
}
$out+='" data-id="';
$out+=$escape($value.id);
$out+='">';
$out+=$escape($value.label);
$out+='</h6> <input type="radio" name="spec_';
$out+=$escape(spec.attr_id);
$out+='" value="';
$out+=$escape($value.id);
$out+='" ';
if($index == 0){
$out+=' checked ';
}
$out+=' id="spec_value_';
$out+=$escape($value.id);
$out+='" style="display: none;" /></label> ';
});
$out+=' </div> ';
}
$out+=' </div> ';
});
$out+=' </div> <div class="bg_white color_2fd0b5 cake_tips">';
$out+=$escape(data.goods.goods_brief);
$out+='</div> <div style="padding: 10px 10px;" class="bg_white">   <div id="item1" class="mui-control-content mui-active"> <ul class="mui-table-view"> ';
$each(data.properties,function(propertie,$index){
$out+=' ';
$each(propertie,function(value,$index){
$out+=' <li class="mui-table-view-cell">';
$out+=$escape(value.name);
$out+='<p class="mui-pull-right" style="width: 60%;">';
$out+=$escape(value.value);
$out+='</p></li> ';
});
$out+=' ';
});
$out+=' </ul> </div>   <div id="item2" class="public_xiangqing_img"> ';
$out+=$string(data.goods.goods_desc);
$out+=' </div>  </div> ';
}
$out+=' </form> </div> <script> /*动态导入js*/ insertJs([\'../js/jquery.common.js\']); /*计算价格*/ function changePrice(){ var attr = getSelectedAttributes(document.forms[\'ECS_FORMBUY\']); var qty = jQuery(\'input[type=number]\').val(); jQuery.ajaxJsonp(web_url+"/mobile/goods.php",{act:\'price\',id:goods_id, attr:attr, number:qty},function(result){ $(\'#price-total\').html(result.data.shopPrice+\'点\'); }); } changePrice(); /*改变规格*/ mui(\'.xiangqing\').on(\'tap\',\'.spec-change\',function(event){ event.stopPropagation(); var _that = jQuery(this); if(_that.hasClass(\'checked\')){ return false; } _that.closest(\'.mui-row\').find(\'.spec-change\').each(function(){ jQuery(this).removeClass(\'checked\'); }); _that.addClass(\'checked\'); _that.siblings(\'input\').prop("checked",true).parents(\'label\').siblings().find(\'input\').prop(\'checked\',false); changePrice(); }); /* 修改数量的时候，从新计算价格 */ mui(\'.mui-input-numbox\')[0].addEventListener(\'change\',function(event){ event.stopPropagation(); changePrice(); }); /* 加入购物车 */ mui(\'.act-cart\')[0].addEventListener(\'tap\',function(event){ addToCart(goods_id); /*更新购物车气泡数量*/ var buyCar_num = jQuery(\'.buy_car_count\').text(); buyCar_num++; /*加入购物车动画*/ var offset = $(".jump-cart").offset(); var offset1=$(\'.act-cart\').offset(); var endedY=$(window).height(); var img= $(\'.mui-slider-item:first-child a img\').attr(\'src\'); var flyer = $(\'<img class="flyer-img" src="\' + img + \'">\'); jQuery(\'.buy_car_count\').removeClass(\'rubberBand\'); flyer.fly({ start:{ left: offset1.left, top:endedY-40 }, end: { left: offset.left+20, top: endedY-40, width: 30, height: 30}, onEnd: function(){ this.destroy(); jQuery(\'.buy_car_count\').text(buyCar_num); jQuery(\'.buy_car_count\').addClass(\'rubberBand\'); } }); }); /* 跳转到购物车 */ mui(\'.jump-cart\')[0].addEventListener(\'tap\',function(){ mui.openWindow({ url:\'../flow/cart.html\', id:\'cart.html\' }); }); /* 跳转到首页 */ mui(\'.jump-home\')[0].addEventListener(\'tap\',function(){ mui.openWindow({ url:\'../index/jy_index.html\', id:\'jy_index.html\' }); }); /* 立即购物 */ mui(\'.act-done\')[0].addEventListener(\'tap\',function(){ addToCart(goods_id,\'\',5); }); /*购物车数量气泡*/ function buy_car_count(){ jQuery.ajaxJsonp(web_url+"/mobile/flow.php",{step:"ajax_cart_count"},function(data){ if(data.state==\'true\'){ jQuery(\'.buy_car_count\').text(data.data); } }) } buy_car_count(); </script> ';
return new String($out);
});