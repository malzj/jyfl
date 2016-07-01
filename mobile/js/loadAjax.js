/**
 *  封装jquery ajax操作，
 *  在 ajax 基础之上添加，执行前效果，执行后关闭
 *  u	url
 *  d	data参数
 *  b	回调函数
 *  e	扩展设置   closeMsg 加载提示，是否在回调函数调用之前关闭，true 是，false 否
 */
(function($){
	$.fn.loadAjax = function(u,d,b,e){ 
		
		var defaults = {
			url: u||'',
			data: d||'',			
			type : 1,
			closeMsg: false,
			success:function(){ 
				$.noop();
			}
		}
		
		defaults = $.extend( defaults,e || {});
		defaults.success = b;	
		
		if( defaults.type == 1 ){
			var index = layer.msg('<img src="js/layer/skin/default/loading-1.gif" style="vertical-align: middle;" width="20">&nbsp;<font font-size:12px;>加载中....</font>', {time:300000,shade:0.4,offset:'60px',shift:0});
		}else{
			var index = layer.load(3, {shade:0.4});		
		}
		$.post(defaults.url, defaults.data , function(resultData){				
			if(defaults.closeMsg == true){ layer.close(index);}
			var backfun = defaults.success(resultData);
			if(defaults.closeMsg == false){ layer.close(index);}			
		})
	}
})(jQuery);


var loadMsg = function(content,backfun){
	var bfun = backfun || null;
	layer.msg(content,{skin:"layui-layer-huimsg",shift:1, offset:'60px'},bfun);
}