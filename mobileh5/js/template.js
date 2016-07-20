/*TMODJS:{"version":"1.0.1"}*/
!function(){function a(a,b){return(/string|function/.test(typeof b)?h:g)(a,b)}function b(a,c){return"string"!=typeof a&&(c=typeof a,"number"===c?a+="":a="function"===c?b(a.call(a)):""),a}function c(a){return l[a]}function d(a){return b(a).replace(/&(?![\w#]+;)|[<>"']/g,c)}function e(a,b){if(m(a))for(var c=0,d=a.length;d>c;c++)b.call(a,a[c],c,a);else for(c in a)b.call(a,a[c],c)}function f(a,b){var c=/(\/)[^\/]+\1\.\.\1/,d=("./"+a).replace(/[^\/]+$/,""),e=d+b;for(e=e.replace(/\/\.\//g,"/");e.match(c);)e=e.replace(c,"/");return e}function g(b,c){var d=a.get(b)||i({filename:b,name:"Render Error",message:"Template not found"});return c?d(c):d}function h(a,b){if("string"==typeof b){var c=b;b=function(){return new k(c)}}var d=j[a]=function(c){try{return new b(c,a)+""}catch(d){return i(d)()}};return d.prototype=b.prototype=n,d.toString=function(){return b+""},d}function i(a){var b="{Template Error}",c=a.stack||"";if(c)c=c.split("\n").slice(0,2).join("\n");else for(var d in a)c+="<"+d+">\n"+a[d]+"\n\n";return function(){return"object"==typeof console&&console.error(b+"\n\n"+c),b}}var j=a.cache={},k=this.String,l={"<":"&#60;",">":"&#62;",'"':"&#34;","'":"&#39;","&":"&#38;"},m=Array.isArray||function(a){return"[object Array]"==={}.toString.call(a)},n=a.utils={$helpers:{},$include:function(a,b,c){return a=f(c,a),g(a,b)},$string:b,$escape:d,$each:e},o=a.helpers=n.$helpers;a.get=function(a){return j[a.replace(/^\.\//,"")]},a.helper=function(a,b){o[a]=b},"function"==typeof define?define(function(){return a}):"undefined"!=typeof exports?module.exports=a:this.template=a,/*v:9*/
a("b",function(a){"use strict";var b=this,c=(b.$helpers,b.$escape),d=a.title,e=a.time,f=a.b,g="";return g+="<br> ",g+=c(d),g+="<br> ",g+=c(e),g+="<br> ",g+=c(f),g+="444",new k(g)}),/*v:9*/
a("index",function(a,b){"use strict";var c=this,d=(c.$helpers,function(d,e){e=e||a;var f=c.$include(d,e,b);return i+=f}),e=c.$escape,f=a.title,g=c.$each,h=a.list,i=(a.$value,a.$index,"");return d("./public/header"),i+=' <div id="main"> <h3>',i+=e(f),i+="1123</h3> <ul> ",g(h,function(a){i+=' <li><a href="',i+=e(a.url),i+='">',i+=e(a.title),i+="</a></li> "}),i+=" </ul> </div> ",new k(i)}),/*v:54*/
a("person",function(a){"use strict";var b=this,c=(b.$helpers,b.$escape),d=a.nickname,e=a.sex,f=a.birthday,g=a.basic,h="";return h+='  <div id="yonghuming" class="mui-page"> <div class="mui-navbar-inner mui-bar mui-bar-nav"> <button type="button" id="username" class="mui-left mui-action-back mui-btn mui-btn-link mui-btn-nav mui-pull-left"> <span class="mui-icon mui-icon-left-nav"></span> </button> <h1 class="mui-center mui-title">\u7528\u6237\u540d</h1> </div> <div class="mui-page-content"> <div class="mui-scroll-wrapper"> <div class="mui-scroll"> <div class="mui-input-row margin_top_20"> <input type="text" name="nickname" value="',h+=c(d),h+='" class="mui-input-clear"> </div> </div> </div> </div> </div>  <div id="xingbie" class="mui-page"> <div class="mui-navbar-inner mui-bar mui-bar-nav"> <button type="button" class="mui-left mui-action-back mui-btn mui-btn-link mui-btn-nav mui-pull-left"> <span class="mui-icon mui-icon-left-nav"></span> </button> <h1 class="mui-center mui-title">\u6027\u522b</h1> </div> <div class="mui-page-content"> <div class="mui-scroll-wrapper"> <div class="mui-scroll"> <ul id="data_sex" class="mui-table-view mui-table-view-radio"> <li class="mui-table-view-cell ',1==e&&(h+="mui-selected"),h+='" data-sex="1"> <a class="mui-navigate-right"> \u7537 </a> </li> <li class="mui-table-view-cell ',2==e&&(h+="mui-selected"),h+='" data-sex="2"> <a class="mui-navigate-right"> \u5973 </a> </li> </ul> </div> </div> </div> </div>  <div id="person_birthday" class="mui-page"> <div class="mui-navbar-inner mui-bar mui-bar-nav"> <button type="button" class="mui-left mui-action-back mui-btn mui-btn-link mui-btn-nav mui-pull-left"> <span class="mui-icon mui-icon-left-nav"></span> </button> <h1 class="mui-center mui-title">\u751f\u65e5</h1> </div> <div class="mui-page-content"> <div class="mui-scroll-wrapper"> <div class="mui-scroll"> <button id=\'birth_date\' data-options=\'{"type":"date","beginYear":1970,"endYear":2016}\' class="btn mui-btn mui-btn-block margin_top_20">',h+=""==f||null==f||void 0==f?"\u9009\u62e9\u65e5\u671f":c(f),h+='</button> </div> </div> </div> </div>  <div id="qinggan" class="mui-page"> <div class="mui-navbar-inner mui-bar mui-bar-nav"> <button type="button" class="mui-left mui-action-back mui-btn mui-btn-link mui-btn-nav mui-pull-left"> <span class="mui-icon mui-icon-left-nav"></span> </button> <h1 class="mui-center mui-title">\u60c5\u611f\u72b6\u6001</h1> </div> <div class="mui-page-content"> <div class="mui-scroll-wrapper"> <div class="mui-scroll"> <select name="basic" id="basic" class="margin_top_20"> <option value="\u4fdd\u5bc6" ',"\u4fdd\u5bc6"==g&&(h+="selected"),h+='>\u4fdd\u5bc6</option> <option value="\u5355\u8eab" ',"\u5355\u8eab"==g&&(h+="selected"),h+='>\u5355\u8eab</option> <option value="\u604b\u7231\u4e2d" ',"\u604b\u7231\u4e2d"==g&&(h+="selected"),h+='>\u604b\u7231\u4e2d</option> <option value="\u5df2\u5a5a" ',"\u5df2\u5a5a"==g&&(h+="selected"),h+='>\u5df2\u5a5a</option> </select> </div> </div> </div> </div>  <div id="xingqu" class="mui-page"> <div class="mui-navbar-inner mui-bar mui-bar-nav"> <button type="button" class="mui-left mui-action-back mui-btn mui-btn-link mui-btn-nav mui-pull-left"> <span class="mui-icon mui-icon-left-nav"></span> </button> <h1 class="mui-center mui-title">\u751f\u65e5</h1> </div> <div class="mui-page-content"> <div class="mui-scroll-wrapper"> <div class="mui-scroll"> <div class="mui-input-group margin_top_15"> <div class="mui-input-row mui-checkbox"> <label>\u7f8e\u98df</label> <input id="meishi" name="favorite" value="\u7f8e\u98df" type="checkbox"> </div> <div class="mui-input-row mui-checkbox"> <label>\u7535\u5f71</label> <input id="dianying" name="favorite" value="\u7535\u5f71" type="checkbox"> </div> <div class="mui-input-row mui-checkbox"> <label>\u9152\u5e97</label> <input id="jiudian" name="favorite" value="\u9152\u5e97" type="checkbox"> </div> <div class="mui-input-row mui-checkbox"> <label>\u4f11\u95f2\u5a31\u4e50</label> <input id="xiuxian" name="favorite" value="\u4f11\u95f2\u5a31\u4e50" type="checkbox"> </div> <div class="mui-input-row mui-checkbox"> <label>\u4e3d\u4eba</label> <input id="liren" name="favorite" value="\u4e3d\u4eba" type="checkbox"> </div> <div class="mui-input-row mui-checkbox"> <label>\u65c5\u6e38</label> <input id="lvyou" name="favorite" value="\u65c5\u6e38" type="checkbox"> </div> </div> </div> </div> </div> </div>',new k(h)}),/*v:9*/
a("public/footer",function(a,b){"use strict";var c=this,d=(c.$helpers,a.time),e=c.$escape,f=function(d,e){e=e||a;var f=c.$include(d,e,b);return g+=f},g="";return g+='<div id="footer"> ',d&&(g+=" <p class='time'>",g+=e(d),g+="</p> "),g+=" ",f("../copyright"),g+=" </div>",new k(g)}),/*v:9*/
a("public/header",function(a,b){"use strict";var c=this,d=(c.$helpers,function(d,f){f=f||a;var g=c.$include(d,f,b);return e+=g}),e="";return e+=' <div id="header"> ',d("./logo"),e+=' <ul id="nav"> <li><a href="http://www.qq.com">\u9996\u9875</a></li> <li><a href="http://news.qq.com/">\u65b0\u95fb</a></li> <li><a href="http://pp.qq.com/">\u56fe\u7247</a></li> <li><a href="http://mil.qq.com/">\u519b\u4e8b</a></li> </ul> </div>  ',new k(e)}),/*v:9*/
a("public/logo",' <h1 id="logo"> <a href="http://www.qq.com"> <img width=\'134\' height=\'44\' src="http://mat1.gtimg.com/www/images/qq2012/qqlogo_1x.png" alt="\u817e\u8baf\u7f51" /> </a> </h1> '),/*v:3*/
a("safe_center","")}();