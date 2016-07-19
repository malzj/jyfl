/**
 * Created by chao on 2016/7/18.
 */
//获取url参数
function getUrlParam(name){
    //构造一个含有目标参数的正则表达式对象
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    //匹配目标参数
    var r = window.location.search.substr(1).match(reg);
    console.log(r);
    //返回参数值
    if (r!=null) return unescape(r[2]);
    return null;
}

//跳转至url
function jumpToUrl(url){
    window.location.href=url;
}
