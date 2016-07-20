/**
 *  封装jquery jsonp操作，
 *  u	url
 *  d	data参数
 *  b	回调函数
 */
(function($){
   $.extend({
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
       }
   })
})(jQuery);
