/*TMODJS:{"version":2,"md5":"467273f41b0d3d2beb24736192d1288a"}*/
template('tpl/flow/checkout',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,data=$data.data,$each=$utils.$each,supplier=$data.supplier,$index=$data.$index,goods=$data.goods,$out='';$out+='<nav class="mui-bar mui-bar-tab mui-row"> <div class="mui-col-xs-7"><a class="mui-tab-item">合计：<span class="color_2fd0b5 orderTotal" data-total="';
$out+=$escape(data.total.goods_price_formated);
$out+='">';
$out+=$escape(data.total.goods_price_formated);
$out+='</span>点</a></div> <div class="mui-col-xs-5"> <a class="mui-tab-item footer_jiaru" href="#"><span class="mui-tab-label">去结算</span></a></div> </nav> <div id="showmap" style="display:block;"></div> <div class="mui-content"> <div class="mui-table-view"> ';
if(data.consignee){
$out+=' <div class="mui-table-view-cell"> <a class="mui-navigate-right href_click" data-href="../personal/shouhuoxinxi.html?onlyCountry=1"> <h4>';
$out+=$escape(data.consignee.consignee);
$out+=' ';
$out+=$escape(data.consignee.mobile);
$out+='</h4> <p>';
$out+=$escape(data.consignee.country_cn);
$out+=' ';
$out+=$escape(data.consignee.province_cn);
$out+=' ';
$out+=$escape(data.consignee.address);
$out+='</p> </a> </div> ';
}else{
$out+=' <div class="mui-table-view-cell"> <a class="mui-navigate-right href_click" data-href="../personal/shouhuoxinxi.html?onlyCountry=1"> <h4 style="text-align: center;">选择收货地址</h4> </a> </div> ';
}
$out+=' </div> <form action="#" method="post" name="theForm" id="theForm"> ';
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
$out+='" data-sid="';
$out+=$escape(supplier[0].supplier_id);
$out+='"> <a class="mui-navigate-right"> 配送地址<span class="gys_adress mui-ellipsis mui-col-xs-7 supplier_shouhuoAddress">...</span> </a> </li> ';
if(supplier[0].open_time == 1){
$out+=' <li class="mui-table-view-cell"> <a class="mui-navigate-right select_date_event" data-options=\'{"type":"date"}\' data-sid="';
$out+=$escape(supplier[0].supplier_id);
$out+='"> <label>选择配送日期</label> <input class="btn mui-btn mui-btn-block select_date st0" name="riqi[';
$out+=$escape(supplier[0].supplier_id);
$out+=']" id="riqi';
$out+=$escape(supplier[0].supplier_id);
$out+='" placeholder="选择日期"> </a> </li> <li class="mui-table-view-cell"> <a class="mui-navigate-right"> 选择配送时间 <select name="time[';
$out+=$escape(supplier[0].supplier_id);
$out+=']" id="time';
$out+=$escape(supplier[0].supplier_id);
$out+='" class="mui-pull-right select_time st0"> <option value="">选择时间</option> </select> </a> </li> ';
}
$out+=' <li class="mui-table-view-cell"> <a href="#"> 运费<span class="mui-pull-right gys_yunfei" id="yunfei';
$out+=$escape(supplier[0].supplier_id);
$out+='">0</span> <input type="hidden" name="sup[';
$out+=$escape(supplier[0].supplier_id);
$out+=']" id="sup_';
$out+=$escape(supplier[0].supplier_id);
$out+='" class="supplier-one st0" value="-1"> </a> </li> </ul> </div> <input type="hidden" name="shipping" id="shipping" value="1" /> <input type="hidden" name="payment" id="payment" value="2" /> <input type="hidden" name="payshipping_check" id="payshipping_check" value="1"> ';
});
$out+=' </form> <ul class="mui-table-view margin_top_15"> <li class="mui-table-view-cell"> 支付方式<span class="mui-pull-right color_8f8f94">聚优卡支付</span> </li> <li class="mui-table-view-cell"> 配送方式<span class="mui-pull-right color_8f8f94">供货商物流</span> </li> <li class="mui-table-view-cell"> 运费合计<span class="mui-pull-right color_8f8f94 yunfeiTotal">0</span> </li> </ul> </div>  <div class="row adress_select bg_white" style="display: none;"></div> <input type="hidden" name="select-supplierid" value="0">  <script type="text/javascript"> /* 当检测不通过时，是否显示提示信息*/ var is_show_error = true; /*动态导入js*/ insertJs([\'../js/baidumap.js\',\'../js/jquery.common.js\']); /* 提交订单 */ jQuery(\'.mui-bar\').on(\'tap\',\'.footer_jiaru\',function(){ var form = jQuery(\'#theForm\').serialize(); var check = true; jQuery(\'.st0\').each(function(){ var _that = jQuery(this); if(_that.val() == \'-1\'){ mui.alert(\'当前地址不支持配送\'); check = false; return false; }else if( _that.val() == \'\'){ mui.alert(\'信息填写不完整\'); check = false; return false; } }); if(check == false){ return false; } jQuery.ajaxJsonp(web_url+"/mobile/flow.php?step=done",form,function(data){ if(data.state == \'false\'){ jQuery.errorJudge(data,data.message); }else{ mui.openWindow({ url:\'./pay.html?order_id=\'+data.data[0].order_id }); } }); }); /* 选择时间 */ var btns = mui(\'.select_date_event\'); btns.each(function(i,btn){ var _that = jQuery(this); btn.addEventListener(\'tap\', function() { var optionsJson = this.getAttribute(\'data-options\') || \'{}\'; var options = JSON.parse(optionsJson); var id = this.getAttribute(\'id\'); var picker = new mui.DtPicker(options); picker.show(function(rs) { _that.find(\'input\').val(rs.value); selectdate( rs.value, _that.attr(\'data-sid\'), _that.parents(\'ul\').find(\'.supplier_shouhuoAddress\').text() ); picker.dispose(); }); }, false); }); /* 拖拽后显示操作图标，点击操作图标删除元素； */ mui(\'#OA_task_2\').on(\'tap\', \'.add_adress_remove\', function(event) { var elem = this; var li = elem.parentNode.parentNode; mui.confirm(\'确认删除该条记录？\', \'提示\', [\'确认\', \'取消\'], function(e) { if (e.index == 0) { li.parentNode.removeChild(li); } else { setTimeout(function() { mui.swipeoutClose(li); }, 0); } }); }); /* 更改单个供应商的配送地址 */ var mask = mui.createMask(); jQuery(\'.mui-content\').on(\'tap\',\'.select_shouhuoAdress\',function(){ /* 设置供应商id，用于保存收货地址用 */ var selectId = jQuery(this).attr(\'data-sid\'); jQuery(\'input[name=select-supplierid]\').val(selectId); jQuery(\'.adress_select\').css(\'display\',\'block\'); mask.show(); jQuery.ajaxJsonp(web_url+"/mobile/address.php",{act:\'AjaxAddressList\'},function(data){ jQuery(\'.adress_select\').html(template(\'flow/selectAddress\', data)); }); }); jQuery(document).on(\'tap\',\'.mui-backdrop\',function(){ jQuery(\'.adress_select\').css(\'display\',\'none\'); jQuery(\'.adress_select\').html(\'\'); jQuery(\'input[name=select-supplierid]\').val(0); }); /* 编辑收货地址 */ jQuery(\'.adress_select\').on(\'tap\',\'.mui-btn-grey\',function(){ var address_id = jQuery(this).parents(\'div\').attr(\'data-id\'); mui.openWindow({ url:\'./editAddress.html?address_id=\'+address_id, id:\'editaddress\' }); }); /* 添加收货地址 */ jQuery(\'.adress_select\').on(\'tap\',\'.add_adress\',function(){ mui.openWindow({ url:\'./editAddress.html\', id:\'push_address\' }); }); /* 选择收货地址 */ jQuery(\'.adress_select\').on(\'tap\',\'.mui-slider-handle\',function(event){ var address_id = jQuery(this).prev(\'div\').attr(\'data-id\'); var sid = jQuery(\'input[name=select-supplierid]\').val(); jQuery.ajaxJsonp( web_url+"/mobile/address.php", { act:\'AjaxAddressSelect\', address_id:address_id, sid:sid }, function(data){ if(data.state == \'true\'){ /* 关闭遮罩 */ mask.close(); /* 关闭选择地址层 */ jQuery(\'.adress_select\').css(\'display\',\'none\'); jQuery(\'.adress_select\').html(\'\'); jQuery(\'input[name=select-supplierid]\').val(0); /* 从新加载收货地址 */ supplierYunfei( jQuery(\'#sup_\'+sid).parents(\'ul\').prev(\'div\').find(\'.psfanwei\') ); /* 情况配送日期和配送时间 */ jQuery(\'#riqi\'+sid).val(\'\'); jQuery(\'#time\'+sid).val(\'\'); } }); }); /* 查看运费 */ jQuery(\'.mui-content\').on(\'tap\',\'.shwo-yunfei\',function(){ var sid = jQuery(this).attr(\'data-id\'); mui.openWindow({ url:\'./map.html?sid=\'+sid, id:\'map.html\' }); }); /* 设置收货地址和运费 */ jQuery(\'.psfanwei\').each(function(index,dom){ supplierYunfei(jQuery(dom)); }); function supplierYunfei(dom){ var id = dom.attr(\'data-id\'); var addressHtml = dom.closest(\'.mui-row\').find(\'.supplier_shouhuoAddress\'); var yunfeiHtml = dom.closest(\'.mui-row\').find(\'.gys_yunfei\'); jQuery.ajaxJsonp(web_url+"/mobile/flow.php",{step:\'yunfei\',id:id},function(data){ /* 赋值 */ var _shippint_fee = data.data.shipping_fee; var _detail = data.data.detail; var _consignee = data.data.consignee; /* 设置配送地址 */ if(_consignee.country_cn == undefined && _consignee.province_cn == undefined && _consignee.address == undefined ) { var address = \'...\'; }else{ var address = _consignee.country_cn+" "+_consignee.province_cn+" "+_consignee.address; } addressHtml.html(address); /* 运费计算 , 百度地图*/ if(_detail.is_map == 1){ _initYunfei(_detail.supplier_id,address); /* 商城 */ }else{ yunfeiHtml.html(data.data.shipping_fee); jQuery(\'#sup_\'+id).val(data.data.shipping_fee); totalYunfei(); } }); } function _initYunfei(sid, address){ baidumap.setOptions({ isYunfei:true, isSetYunfei:true, isTime:1, showMapId:\'showmap\', afterFunction:function(d){ if(d == -1){ if(is_show_error == true){ mui.alert(\'当前地址不支持配送\'); is_show_error = false; } }else{ totalYunfei(d); } } }); baidumap.showMap(sid,address); } /* 统计运费 */ function totalYunfei(d){ var yunfeiTotal = 0; jQuery(\'.supplier-one\').each(function(index,dom){ var yunfei = jQuery(dom).val(); if(yunfei != -1){ yunfeiTotal = parseInt(yunfeiTotal)+parseInt(yunfei); } }); jQuery(\'.yunfeiTotal\').html(yunfeiTotal); orderTotal = (parseFloat(yunfeiTotal)+parseFloat(jQuery(\'.orderTotal\').attr(\'data-total\'))).toFixed(2); jQuery(\'.orderTotal\').html(orderTotal); } /* 选择时间 */ function selectdate(date, sid, address){ jQuery(\'.peisong_check\').each(function(){ jQuery(this).css(\'border\',\'1px solid #B1AFAF\'); }); baidumap.setOptions({ isYunfei:true, isSetYunfei:false, isTime:2, showMapId:\'showmap\', afterFunction:function(d){ jQuery.ajaxJsonp( web_url+"/mobile/flow.php", { step:\'shippingTime\', date:date, sid:sid, shipping_start:d.shipping_start, shipping_end:d.shipping_end, shipping_waiting:d.shipping_waiting, shipping_booking:d.shipping_booking }, function(data){ var ptime = jQuery(\'#time\'+sid); ptime.empty(); ptime.append(\'<option value="">选择时间</option>\'); if (data.state == \'false\'){ mui.alert(data.message); }else{ jQuery.each(data.data, function (k, v){ ptime.append(\'<option value="\'+k+\'">\'+v+\'</option>\'); }); } }); } }); baidumap.showMap(sid,address); } jQuery.hrefClick(); </script> ';
return new String($out);
});