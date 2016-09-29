(function($){
   $.extend({
       /**
        * 封装jquery jsonp操作，
        * @param u  url
        * @param d  data参数
        * @param b  回调函数
        * @param isload  判断是否使用动画默认false
        * @param prev  执行ajax前调用函数
        * @param after  执行ajax完成时调用函数
        */
       ajaxJsonp:function(u,d,b,isload,prev,after){
           var defaults = {
               url:u||'',
               data:d||'',
               success:function(){
                   $.noop();
               }
           }
           isload = isload?true:false;
           prev = (typeof prev === "function")?prev:function(){
               var html ='<div id="mui-loading-box" class="mui-scroll"><div class="mui-loading"><div class="mui-spinner"></div>正在加载</div></div>';
               $('body').append(html);
           }
           after = (typeof after === "function")?after:function(){
               $('#mui-loading-box').remove();
           }
           defaults.success=b;
           if(isload || !mui.os.plus){
                prev();
           }
           $.ajax({
               url:defaults.url,
               type:'get',
               data:defaults.data,
               dataType:'json',
               cache:false,
//             dataType:'jsonp',
//             jsonp:'jsoncallback',
               success:function(result){
                   defaults.success(result);
                   if(isload || !mui.os.plus){
                       after();
                   }
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
                   mui.openWindow({
                       url:'../index.html'
                   });
               });
           }else{
               mui.alert(message,function(){
                   if(typeof data.go!='undefined'&&!isNaN(data.go)){
                       window.history.go(data.go);
                   }else{
                       if(typeof func == "function") func();
                   }
               });
           }
       },
       /**
        * 点击链接跳转页面
        * @param container  非动态渲染的包含框选择器
        * @param selector   包含的元素选择器
        */
       hrefClick:function(container,selector){
           var defContain = 'body';
           var defSelector = '.href_click';
           container = container?container:defContain;
           selector = selector?selector:defSelector;

           mui(container).on('tap',selector,function(e){
               var url = this.getAttribute('data-href');
               if (!this.classList.contains('mui-disabled')) {
                   e.stopPropagation();
                   mui.openWindow({
                       url:url
                   });
               }else {
                   e.preventDefault();
                   e.stopPropagation();
               }
           });
       },
       /**
        * 页面跳转
        * @param url    跳转链接
        * @param id     新页面id
        * @param ext    扩展变量json对象（{id:'id'})
        * @param createnew    是否重复创建窗口默认false
        */
       jumpTo:function(url,id,ext,createnew){
           var data = new Object();
           data['url'] = url;
           if(id){
               data['id'] = id;
           }
           if(ext){
               data['extras'] = ext;
           }
           data['createNew'] = createnew?true:false;
           mui.openWindow(data);
       }
   })
})(jQuery);
