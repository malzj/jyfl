/*TMODJS:{"version":1,"md5":"5efed4d40f1d5db09d559a88ca6393e4"}*/
template('life/yindex','<!doctype html> <html> <head> <meta charset="UTF-8"> <title></title> <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" /> <link href="../css/mui.min.css" rel="stylesheet" /> <link rel="stylesheet" href="../css/index.css" /> <link href="../css/public.css" rel="stylesheet"/> <link rel="stylesheet" href="../css/iconfont.css" /> <link href="../css/mui.mian.css" rel="stylesheet"/> <script src="../js/connection.js"></script> <script src="../js/jquery-1.9.1.min.js"></script> <script src="../js/mui.min.js"></script> <script src="../js/template.js"></script> <script src="../js/jqueryExtend.js"></script> <script src="../js/rem.js"></script> <script src="../js/mui.mian.js"></script> <style> .mui-segmented-control.mui-scroll-wrapper{height:5.5rem;background-color: white;border-bottom: 1px solid #ddd;padding: .5rem 0;} .mui-segmented-control.mui-segmented-control-inverted .mui-control-item.mui-active{border-bottom: none;} </style> <script type="text/javascript"> mui.init(); </script> </head> <body> <div class="mui-off-canvas-wrap mui-draggable">  <div class="mui-inner-wrap">  <header class="mui-bar mui-bar-nav"> <a class="mui-action-back mui-icon mui-icon-left-nav mui-pull-left"></a> <h1 class="mui-title">优品生活</h1> </header>  <div class="mui-content mui-scroll-wrapper"> <div class="mui-scroll ajax-content"> </div> </div> <div class="mui-off-canvas-backdrop"></div> </div> </div> <script type="text/javascript"> // ajax调取数据 jQuery.ajaxJsonp(web_url+"/mobile/life.php",{act:\'getNSIndex\'},function(data){ if(data.state == \'false\'){ jQuery.errorJudge(data,data.message,function(){ mui.back(); }); } // 加载页面 jQuery(\'.ajax-content\').html(template(\'life/yindex\', data)); // 从新触发相册插件 var gallery = mui(\'#slider\'); gallery.slider({interval:5000}); mui(\'.mui-scroll-wrapper\').scroll({ deceleration: 0.0005 }); }); jQuery.ajaxJsonp(web_url+"/mobile/basic.php",{act:\'navList\'},function(result){ replaceHtml(\'.mui-draggable > .mui-inner-wrap header\', template(\'public/footer\', result.data)); }); </script> </body> </html>');