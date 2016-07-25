/*TMODJS:{"version":227,"md5":"25348c5ef0e8689faa7c3fc193a45533"}*/
template('flow/checkout',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,data=$data.data,$each=$utils.$each,supplier=$data.supplier,$index=$data.$index,goods=$data.goods,$out='';$out+='<nav class="mui-bar mui-bar-tab mui-row"> <div class="mui-col-xs-7"><a class="mui-tab-item">合计：<span class="color_2fd0b5 orderTotal">';
$out+=$escape(data.total.goods_price_formated);
$out+='</span>点</a></div> <div class="mui-col-xs-5"> <a class="mui-tab-item footer_jiaru" href="javascript:alert(\'2222\')"><span class="mui-tab-label">去结算</span></a></div> </nav> <div id="showmap" style="display:none;"></div> <div class="mui-content"> <div class="mui-table-view"> <div class="mui-table-view-cell"> <a class="mui-navigate-right"> <h4>';
$out+=$escape(data.consignee.consignee);
$out+=' ';
$out+=$escape(data.consignee.mobile);
$out+='</h4> <p>';
$out+=$escape(data.consignee.country_cn);
$out+=' ';
$out+=$escape(data.consignee.province_cn);
$out+=' ';
$out+=$escape(data.consignee.address);
$out+='</p> </a> </div> </div> ';
$each(data.goodsList,function(supplier,$index){
$out+=' <div class="mui-row gys"> <div class="gys_name"> ';
$out+=$escape(supplier[0].seller);
$out+=' ';
if(supplier[0].is_map == 1){
$out+=' <span class="color_2fd0b5 mui-pull-right psfanwei shwo-yunfei" data-id="';
$out+=$escape(supplier[0].supplier_id);
$out+='">查看配送范围</span> ';
}else{
$out+=' <span class="psfanwei" data-id="';
$out+=$escape(supplier[0].supplier_id);
$out+='"></span> ';
}
$out+=' </div> <ul class="mui-table-view"> ';
$each(supplier,function(goods,$index){
$out+=' <li class="mui-table-view-cell mui-media"> <a href="javascript:;" class="mui-row"> <img class="mui-media-object mui-pull-left" src="http://www.juyoufuli.com/';
$out+=$escape(goods.goods_thumb);
$out+='"> <div class="mui-media-body mui-col-xs-5"> <h4 class="mui-ellipsis">';
$out+=$escape(goods.goods_name);
$out+='</h4> <p class="gys_guige">';
$out+=$escape(goods.goods_attr);
$out+='</p> </div> <div class="pull-right mui-text-right overflow_hidden"> <div class="gys_price"><span class="color_dd4223">';
$out+=$escape(goods.goods_price);
$out+='</span></div> <div class="gys_number"><span>x';
$out+=$escape(goods.goods_number);
$out+='</span></div> </div> </a> </li> ';
});
$out+=' <li class="mui-table-view-cell select_shouhuoAdress" id="address-';
$out+=$escape(supplier[0].supplier_id);
$out+='"> <a class="mui-navigate-right"> 配送地址<span class="gys_adress mui-ellipsis mui-col-xs-7 supplier_shouhuoAddress">...</span> </a> </li> ';
if(supplier[0].open_time == 1){
$out+=' <li class="mui-table-view-cell"> <a class="mui-navigate-right" id="select_date" data-options=\'{"type":"date"}\'> <label>选择配送日期</label> <input class="btn mui-btn mui-btn-block select_date" value=""> </a> </li> <li class="mui-table-view-cell"> <a class="mui-navigate-right"> 选择配送时间 <select name="" id="" class="mui-pull-right select_time"> <option value="">09:00-11:00</option> <option value="">09:00-12:00</option> <option value="">09:00-13:00</option> </select> </a> </li> ';
}
$out+=' <li class="mui-table-view-cell"> <a href="#"> 运费<span class="mui-pull-right gys_yunfei" id="yunfei';
$out+=$escape(supplier[0].supplier_id);
$out+='">0</span> <input type="hidden" name="sup[';
$out+=$escape(supplier[0].supplier_id);
$out+=']" id="sup_';
$out+=$escape(supplier[0].supplier_id);
$out+='" class="supplier-one" value="-1"> </a> </li> </ul> </div> ';
});
$out+=' <ul class="mui-table-view margin_top_15"> <li class="mui-table-view-cell"> 支付方式<span class="mui-pull-right color_8f8f94">聚优卡支付</span> </li> <li class="mui-table-view-cell"> 配送方式<span class="mui-pull-right color_8f8f94">供货商物流</span> </li> <li class="mui-table-view-cell"> 运费合计<span class="mui-pull-right color_8f8f94 yunfeiTotal">0</span> </li> </ul> </div>  <div class="row adress_select bg_white" style="display: none;"> </div>  <script type="text/javascript"> /*动态导入js*/ insertJs([\'js/baidumap.js\']); /* 选择时间 */ var result = mui(\'.select_date\')[0]; var btns = mui(\'#select_date\'); btns.each(function(i, btn) { btn.addEventListener(\'tap\', function() { var optionsJson = this.getAttribute(\'data-options\') || \'{}\'; var options = JSON.parse(optionsJson); var id = this.getAttribute(\'id\'); var picker = new mui.DtPicker(options); picker.show(function(rs) { result.value = rs.value; picker.dispose(); }); }, false); }); /* 拖拽后显示操作图标，点击操作图标删除元素； */ mui(\'#OA_task_2\').on(\'tap\', \'.add_adress_remove\', function(event) { var elem = this; var li = elem.parentNode.parentNode; mui.confirm(\'确认删除该条记录？\', \'提示\', [\'确认\', \'取消\'], function(e) { if (e.index == 0) { li.parentNode.removeChild(li); } else { setTimeout(function() { mui.swipeoutClose(li); }, 0); } }); }); /* 更改单个供应商的配送地址 */ var mask = mui.createMask(); jQuery(\'.mui-content\').on(\'tap\',\'.select_shouhuoAdress\',function(){ jQuery(\'.adress_select\').css(\'display\',\'block\'); mask.show(); jQuery.ajaxJsonp(web_url+"/mobile/address.php",{act:\'AjaxAddressList\'},function(data){ jQuery(\'.adress_select\').html(template(\'flow/selectAddress\', data)); }); }); jQuery(document).on(\'tap\',\'.mui-backdrop\',function(){ jQuery(\'.adress_select\').css(\'display\',\'none\'); jQuery(\'.adress_select\').html(\'\'); }); /* 删除一个地址 */ jQuery(\'.adress_select\').on(\'tap\',\'.add_adress_remove\',function(){ var _that = jQuery(this); var address_id = _that.parents(\'div\').attr(\'data-id\'); jQuery.ajaxJsonp(web_url+"/mobile/address.php",{act:\'AjaxAddressDorp\',address_id:address_id},function(data){ _that.parents(\'li\').remove(); }); }); /* 编辑收货地址 */ jQuery(\'.adress_select\').on(\'tap\',\'.mui-btn-grey\',function(){ var _that = jQuery(this); var address_id = _that.parents(\'div\').attr(\'data-id\'); mui.openWindow({ url:\'./editAddress.html?address_id=\'+address_id, id:\'editaddress\' }); /* jQuery.ajaxJsonp(web_url+"/mobile/address.php",{act:\'AjaxEditress\',address_id:address_id},function(data){ jQuery(\'.adress_select\').html(template(\'flow/editAddress\', data)); enablePopPicker(); }); */ }); /* 查看运费 */ jQuery(\'.mui-content\').on(\'tap\',\'.shwo-yunfei\',function(){ var sid = jQuery(this).attr(\'data-id\'); mui.openWindow({ url:\'./map.html?sid=\'+sid, id:\'map.html\' }); }); /* 设置收货地址和运费 */ jQuery(\'.psfanwei\').each(function(index,dom){ supplierYunfei($(dom)); }); function supplierYunfei(dom){ var id = dom.attr(\'data-id\'); var addressHtml = dom.closest(\'.mui-row\').find(\'.supplier_shouhuoAddress\'); var yunfeiHtml = dom.closest(\'.mui-row\').find(\'.gys_yunfei\'); jQuery.ajaxJsonp(web_url+"/mobile/flow.php",{step:\'yunfei\',id:id},function(data){ /* 赋值 */ var _shippint_fee = data.data.shipping_fee; var _detail = data.data.detail; var _consignee = data.data.consignee; /* 设置配送地址 */ var address = _consignee.consignee+" "+_consignee.country_cn+" "+_consignee.province_cn+" "+_consignee.address; addressHtml.html(address); /* 运费计算 */ if(_detail.is_map == 1){ _initYunfei(_detail.supplier_id,address); }else{ yunfeiHtml.html(data.data.shipping_fee); } console.log(data); }); } function _initYunfei(sid, address){ baidumap.setOptions({ isYunfei:true, isSetYunfei:true, isTime:1, showMapId:\'showmap\', afterFunction:function(d){ if(d == -1){ mui.alert(\'当前地址不支持配送\'); }else{ totalYunfei(d); } } }); baidumap.showMap(sid,address); } /* 统计运费 */ function totalYunfei(d){ var yunfeiTotal = 0; jQuery(\'.supplier-one\').each(function(index,dom){ var yunfei = jQuery(dom).val(); if(yunfei != -1){ yunfeiTotal = parseInt(yunfeiTotal)+parseInt(yunfei); } }); jQuery(\'.yunfeiTotal\').html(yunfeiTotal); orderTotal = (parseFloat(yunfeiTotal)+parseFloat(jQuery(\'.orderTotal\').text())).toFixed(2); jQuery(\'.orderTotal\').html(orderTotal); } </script> ';
return new String($out);
});