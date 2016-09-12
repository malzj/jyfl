/*TMODJS:{"version":1,"md5":"985c1a6cc1ca01d546c1fd95c728016c"}*/
template('tpl/flow/cart',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,data=$data.data,$each=$utils.$each,supplier=$data.supplier,$index=$data.$index,goods=$data.goods,$out='';$out+='<nav class="mui-bar mui-bar-tab mui-row"> <div class="mui-col-xs-7"><a class="mui-tab-item">合计：<span class="color_2fd0b5 total-price">';
$out+=$escape(data.total.goods_price);
$out+='</span>点</a></div> <div class="mui-col-xs-5"> <a class="mui-tab-item footer_jiaru act-done" href="#"><span class="mui-tab-label">去结算</span></a> </div> </nav> <div class="mui-content"> ';
$each(data.goods_list,function(supplier,$index){
$out+=' <div class="mui-row gys"> <div class="gys_name">';
$out+=$escape(supplier.supplier_name);
$out+='</div> <ul class="mui-table-view"> ';
$each(supplier.goods_list,function(goods,$index){
$out+=' <li class="mui-table-view-cell mui-media"> <div class="mui-row"> <div class="mui-col-xs-4"> <img class="mui-media-object mui-pull-left" src="';
$out+=$escape(goods.goods_thumb);
$out+='"> </div> <div class="mui-media-body mui-col-xs-5"> <h5 class="mui-ellipsis">';
$out+=$escape(goods.goods_name);
$out+='</h5> <p class="gys_guige">';
$out+=$escape(goods.goods_attr);
$out+='</p> <span class="color_dd4223">';
$out+=$escape(goods.goods_price);
$out+='点</span> </div> <div class="mui-col-xs-3"> <span class="mui-icon mui-icon-trash mui-pull-right cart-delete" data-id="';
$out+=$escape(goods.rec_id);
$out+='"></span> <div class="mui-numbox mui-pull-right" data-numbox-min=\'1\'> <button class="mui-btn-numbox-minus" type="button">-</button> <input class="mui-input-numbox" type="number" value="';
$out+=$escape(goods.goods_number);
$out+='" style="border: none!important;font-size: 12px;"/> <button class="mui-btn-numbox-plus" type="button">+</button> </div> </div> </div> </li> ';
});
$out+=' </ul> </div> ';
});
$out+=' <div class="empty-cart" style="display:none"> <div class="empty_cart_box"> <img src="../images/cart_null.png" alt="" /> <p>购物车空空如也~</p> <a href="../index/jy_index.html"><button type="button">去逛逛</button></a> </div> </div> </div> <script> /* 购物车为空显示 */ if( jQuery(\'.mui-content\').find(\'.mui-row\').length == 0){ jQuery(\'.empty-cart\').show(); } /* 购物车商品删除 */ jQuery(\'.mui-content\').on(\'tap\',\'.cart-delete\',function(e){ var _that = jQuery(this); var rec_id = _that.attr(\'data-id\'); mui.confirm(\'你确定要删除吗？\',\'提示\',[\'取消\',\'确认\'], function(e){ if(e.index == 1){ jQuery.ajaxJsonp(web_url+"/mobile/flow.php",{step:\'drop_to_collect\',id:rec_id},function(data){ var uls = _that.parents(\'ul\'); if(uls.find(\'li\').length > 1){ _that.parents(\'li\').remove(); initTotal(0,0); }else{ uls.parents(\'.mui-row\').remove(); if(jQuery(\'.mui-content\').find(\'.gys\').length == 0){ jQuery(\'.empty-cart\').show(); } initTotal(0,0); } }); } }); }); /* 修改购物车数量 */ jQuery(\'.mui-content\').on(\'change\',\'input[type=number]\',function(){ var goods_number = jQuery(this).val(); var rec_id = jQuery(this).parents(\'div\').prev().attr(\'data-id\'); initTotal(rec_id, goods_number); }); /* 统计价格 */ function initTotal(r,n){ jQuery.ajaxJsonp(web_url+"/mobile/flow.php",{step:\'update_cart\',rec_id:r,goods_number:n}, function(data){ jQuery(\'.total-price\').html(data.data.total.goods_price); }); } /* 去结算 */ jQuery(\'.act-done\')[0].addEventListener(\'tap\',function(){ mui.openWindow({ url:\'./checkout.html\', id:\'checkout.html\' }); }); </script>';
return new String($out);
});