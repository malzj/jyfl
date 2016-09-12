/**
 * 动态引入js
 * js	array	引入的js文件
 */ 
function insertJs(js){
	for(var i=0; i<js.length; i++){
		$('body').append("<script src='"+js[i]+"'><\/script>"); 
	}
	
}
/**
 *  内容替换
 *  将jQuery内容替换封装，有利于在内容替换前做一些特效处理
 */
function replaceHtml( dom, html){
	jQuery(dom).after(html);
}
/**
 * 封装的自动消失的提示框
 * @param message
 */
function toast(message){
	mui.toast(message);
}