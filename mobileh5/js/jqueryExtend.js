(function($){
   $.extend({
       /**
        * 封装jquery jsonp操作，
        * @param u  url
        * @param d  data参数
        * @param b  回调函数
        */
       ajaxJsonp:function(u,d,b){
           var defaults = {
               url:u||'',
               data:d||'',
               success:function(){
                   $.noop();
               }
           }
           defaults.success=b;
           $.ajax({
               url:defaults.url,
               type:'get',
               data:defaults.data,
               dataType:'jsonp',
               jsonp:'jsoncallback',
               success:function(result){
                   defaults.success(result);
               }
           })
       },
       /**
        * 获取Url中params
        * @param name   参数名称
        * @returns {str|null}
        */
       getUrlParam:function (name){
            //构造一个含有目标参数的正则表达式对象
            var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
            //匹配目标参数
            var r = window.location.search.substr(1).match(reg);
            //返回参数值
            if (r!=null) return unescape(r[2]);
            return null;
        },
       /**
        * ajaxJsonp返回数据为false时进行判断
        * @param data       返回数据
        * @param message    错误信息
        * @param func       回调函数
        */
       errorJudge:function(data,message,func){
           if(data.isLogin==1){
               mui.alert(message,function(){
                   $.openWindow({
                       url:'./index.html'
                   });
               });
           }else{
               mui.alert(message,func);
           }
       }
   })
})(jQuery);