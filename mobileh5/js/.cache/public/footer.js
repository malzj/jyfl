/*TMODJS:{"version":120,"md5":"afb9758068e7838bef3ac2de9775cb4a"}*/
template('public/footer',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,user_info=$data.user_info,$each=$utils.$each,nav_list=$data.nav_list,nav=$data.nav,$index=$data.$index,$out='';$out+='<script> var app_path = \'../\'; </script> <aside id="offCanvasSide" class="mui-off-canvas-right"> <div id="offCanvasSideScroll" class="mui-scroll-wrapper"> <div class="mui-scroll">  <ul class="mui-table-view mui-table-view-chevron mui-table-view-inverted"> <li class="mui-table-view-cell index_touxiang"><img src="';
$out+=$escape(user_info.pic);
$out+='" alt="头像"/></li> <li class="mui-table-view-cell"><a class="mui-navigate-right href_click" data-href="../personal/person.html" open-type="common">个人信息</a></li> <li class="mui-table-view-cell"><a class="mui-navigate-right href_click" data-href="../personal/safe_center.html">安全中心</a></li> <li class="mui-table-view-cell"><a class="mui-navigate-right href_click" data-href="../order/my_order.html">我的订单</a></li> <li class="mui-table-view-cell"><a class="mui-navigate-right href_click" data-href="../personal/shouhuoxinxi.html">收货信息</a></li> <li class="mui-table-view-cell"><a class="mui-navigate-right href_click" data-href="../personal/account_deposit.html">卡充值</a></li> <li class="mui-table-view-cell"><a class="mui-navigate-right href_click" data-href="../personal/card_merge.html">卡合并</a></li> <li class="mui-table-view-cell"><a id="logout" href="javascript:void(0);">退出登录</a></li> </ul> </div> </div> </aside> <nav class="mui-bar mui-bar-tab bg_white public_footer"> <a class="mui-tab-item href_click" data-href="../index/jy_index.html"> <span class="mui-icon iconfont icon-zhuye"></span> <span class="mui-tab-label">聚优</span> </a> <a class="mui-tab-item href_click" data-href="../order/my_order.html"> <span class="mui-icon iconfont icon-order"></span> <span class="mui-tab-label">订单</span> </a> <a class="mui-tab-item public_gongneng" style="z-index:10;"> <span class="mui-icon iconfont icon-gongneng"></span> <span class="mui-tab-label public_gongneng_title">功能</span> </a> <a class="mui-tab-item href_click" data-href="../flow/cart.html"> <span class="mui-icon iconfont icon-gouwuche"></span> <span class="mui-tab-label">购物车</span> </a> <a class="mui-tab-item" href="#offCanvasSide"> <span class="mui-icon iconfont icon-wode"></span> <span class="mui-tab-label">我的</span> </a> </nav>  <div class="public_popUp"></div>  <div class="popUp_row" style="z-index:10"> <div class="mui-row popUp_content"> ';
$each(nav_list,function(nav,$index){
$out+=' <div class="mui-col-xs-3 popUp_item"> <a class="popUp_item_bg" id="popUp_item_bg';
$out+=$escape(nav.id);
$out+='" data-id="';
$out+=$escape(nav.id);
$out+='"> <img src="../images/icon/nav/nav-';
$out+=$escape(nav.id);
$out+='.svg"/> </a> <p class="mui-text-center">';
$out+=$escape(nav.name);
$out+='</p> </div> ';
});
$out+=' </div> <div class="popUp_close"> <img src="../images/icon/nav/popUp_close.svg"/> </div> </div> <script> mui(\'#offCanvasSideScroll\').scroll(); mui(\'#offCanvasSideScroll1\').scroll(); mui(\'#logout\')[0].addEventListener(\'tap\',function(){ jQuery.ajax({ url: web_url +\'/mobile/userAuth.php\', data: { \'act\': \'logout\', }, type: \'get\', dataType: \'jsonp\', jsonp: \'jsoncallback\', success: function (result) { console.log(result); if (result.state == \'true\') { mui.alert("退出登录成功！",function(){ window.location.href=\'../index.html\'; }); } else { mui.alert("退出登录失败！"); } } }); return false; }); jQuery(\'.public_footer\').on(\'tap\',\'.public_gongneng\',function(){ jQuery(\'.public_popUp\').css({\'z-index\':\'10\',\'visibility\':\'visible\',\'transition-delay\':\'0s\'}); jQuery(\'.popUp_item\').addClass(\'active\'); jQuery(\'.popUp_row\').css({\'transform\':\'translateY(0)\',\'transition-delay\':\'0s\'}); jQuery(\'.popUp_item:nth-child(1),.popUp_item:nth-child(2),.popUp_item:nth-child(3),.popUp_item:nth-child(4)\').css(\'transition-delay\',\'.05s\'); jQuery(\'.popUp_item:nth-child(5),.popUp_item:nth-child(6),.popUp_item:nth-child(7),.popUp_item:nth-child(8)\').css(\'transition-delay\',\'.1s\'); jQuery(\'.popUp_item:nth-child(9),.popUp_item:nth-child(10),.popUp_item:nth-child(11),.popUp_item:nth-child(12)\').css(\'transition-delay\',\'.13s\'); jQuery(\'.popUp_item:nth-child(13),.popUp_item:nth-child(14),.popUp_item:nth-child(15),.popUp_item:nth-child(16)\').css(\'transition-delay\',\'.17s\'); }); jQuery(\'.popUp_row\').on(\'tap\',\'.popUp_close\',function(){ jQuery(\'.public_popUp\').css({\'z-index\':\'-1\',\'visibility\':\'hidden\',\'transition-delay\':\'.2s\'}); jQuery(\'.popUp_item\').removeClass(\'active\'); jQuery(\'.popUp_row\').css({\'transform\':\'translateY(100%)\',\'transition-delay\':\'.4s\',\'opcity\':\'0\'}); jQuery(\'.popUp_item:nth-child(1),.popUp_item:nth-child(2),.popUp_item:nth-child(3),.popUp_item:nth-child(4)\').css(\'transition-delay\',\'.12s\'); jQuery(\'.popUp_item:nth-child(5),.popUp_item:nth-child(6),.popUp_item:nth-child(7),.popUp_item:nth-child(8)\').css(\'transition-delay\',\'.09s\'); jQuery(\'.popUp_item:nth-child(9),.popUp_item:nth-child(10),.popUp_item:nth-child(11),.popUp_item:nth-child(12)\').css(\'transition-delay\',\'.06s\'); jQuery(\'.popUp_item:nth-child(13),.popUp_item:nth-child(14),.popUp_item:nth-child(15),.popUp_item:nth-child(16)\').css(\'transition-delay\',\'.03s\'); }); jQuery(\'.popUp_content\').on(\'tap\', \'a\', function(){ jQuery(\'.public_popUp\').css({\'z-index\':\'-1\',\'visibility\':\'hidden\',\'transition-delay\':\'.2s\'}); jQuery(\'.popUp_item\').removeClass(\'active\'); jQuery(\'.popUp_row\').css({\'transform\':\'translateY(100%)\',\'transition-delay\':\'.4s\',\'opcity\':\'0\'}); var navid = jQuery(this).attr(\'data-id\'); var jumpTohref = \'\'; switch(navid){ /* 电影 */ case \'4\': jumpTohref = app_path+\'movie/movie.html\'; break; /* 演唱会 */ case \'10\': jumpTohref = app_path+\'yanchu/index.html?id=1217\'; break; /* 话剧*/ case \'11\': jumpTohref = app_path+\'yanchu/index.html?id=1220\'; break; /* 音乐会 */ case \'12\': jumpTohref = app_path+\'yanchu/index.html?id=1218\'; break; /* 亲子儿童 */ case \'13\': jumpTohref = app_path+\'yanchu/index.html?id=1227\'; break; /* 戏曲 */ case \'14\': jumpTohref = app_path+\'yanchu/index.html?id=1224\'; break; /* 场馆 */ /* case \'15\': jumpTohref = \'/mobileh5/yanchu/index.html\'; break; */ /* 蛋糕 */ case \'16\': jumpTohref = app_path+\'cake/index.html\'; break; /* 舌尖 */ case \'17\': jumpTohref = app_path+\'life/index.html\'; break; /* 运动装备 */ case \'30\': jumpTohref = app_path+\'cake/list.html?id=17\'; break; /* 鲜花 */ case \'31\': jumpTohref = app_path+\'cake/list.html?id=18\'; break; /* 洗衣 */ case \'33\': jumpTohref = app_path+\'cake/list.html?id=20\'; break; /* 体检 */ case \'34\': jumpTohref = app_path+\'cake/list.html?id=19\'; break; /* 优品 */ case \'40\': jumpTohref = app_path+\'life/yindex.html\'; break; /* 味多美 */ case \'41\': jumpTohref = app_path+\'cake/list.html?id=27\'; break; } if(jQuery.trim(jumpTohref)){ mui.openWindow({ url:jumpTohref, id:\'cate-\'+navid }); } }); jQuery.hrefClick(); </script> ';
return new String($out);
});