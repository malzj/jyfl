// mui 遮罩
function createMask(mui,ext){	
	if( ext == 'show'){
		mask.show();
		$('.mui-backdrop').html('<span class="loading-mui"><div class="mui-scroll"><div class="mui-loading"><div class="mui-spinner"></div></div></div></span>');
	}else{
		mask.close();
	}
}

/**
 * 动态引入js
 * js	array	引入的js文件
 */ 
function insertJs(js){
	for(var i=0; i<js.length; i++){
		$('body').append("<script src='"+web_url+"/mobileh5/"+js[i]+"'><\/script>"); 
	}
	
}

/**
 *  内容替换
 *  将jQuery内容替换封装，有利于在内容替换前做一些特效处理
 */
function replaceHtml( dom, html){
	jQuery(dom).html(html);
}
/**
 * 封装的自动消失的提示框
 * @param message
 */
function toast(message){
	mui.toast(message);
}