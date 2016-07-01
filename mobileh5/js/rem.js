//iphone6宽度作为标准 针对各种屏幕改变html 字体大小做适配
             (function(doc, win){
                 var docEl = doc.documentElement,
                    resizeEvt ='orientationchange' in window ? 'orientationchange' : 'resize' ,
                     recalc=function(){
                         var clientWidth=docEl.clientWidth;
                         if (! clientWidth) return;
                        docEl.style.fontSize=20*(clientWidth/375)+'px';
                      };
                 if (!doc.addEventListener) return ;
                win.addEventListener(resizeEvt,recalc,false);
                doc.addEventListener( 'DOMContentLoaded',recalc,false);
            })(document, window);
