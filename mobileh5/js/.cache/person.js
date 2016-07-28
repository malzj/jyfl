/*TMODJS:{"version":32,"md5":"502b3736f9d469a2feb5cd7f0d800d65"}*/
template('person',function($data,$filename
/**/) {
'use strict';var $utils=this,$helpers=$utils.$helpers,$escape=$utils.$escape,nickname=$data.nickname,sex=$data.sex,birthday=$data.birthday,basic=$data.basic,$out='';$out+='  <div id="yonghuming" class="mui-page"> <div class="mui-navbar-inner mui-bar mui-bar-nav"> <button type="button" id="username" class="mui-left mui-action-back mui-btn mui-btn-link mui-btn-nav mui-pull-left"> <span class="mui-icon mui-icon-left-nav"></span> </button> <h1 class="mui-center mui-title">用户名</h1> </div> <div class="mui-page-content"> <div class="mui-scroll-wrapper"> <div class="mui-scroll"> <div class="mui-input-row margin_top_20"> <input type="text" name="nickname" value="';
$out+=$escape(nickname);
$out+='" class="mui-input-clear"> </div> </div> </div> </div> </div>  <div id="xingbie" class="mui-page"> <div class="mui-navbar-inner mui-bar mui-bar-nav"> <button type="button" class="mui-left mui-action-back mui-btn mui-btn-link mui-btn-nav mui-pull-left"> <span class="mui-icon mui-icon-left-nav"></span> </button> <h1 class="mui-center mui-title">性别</h1> </div> <div class="mui-page-content"> <div class="mui-scroll-wrapper"> <div class="mui-scroll"> <ul id="data_sex" class="mui-table-view mui-table-view-radio"> <li class="mui-table-view-cell ';
if(sex==1){
$out+='mui-selected';
}
$out+='" data-sex="1"> <a class="mui-navigate-right"> 男 </a> </li> <li class="mui-table-view-cell ';
if(sex==2){
$out+='mui-selected';
}
$out+='" data-sex="2"> <a class="mui-navigate-right"> 女 </a> </li> </ul> </div> </div> </div> </div>  <div id="person_birthday" class="mui-page"> <div class="mui-navbar-inner mui-bar mui-bar-nav"> <button type="button" class="mui-left mui-action-back mui-btn mui-btn-link mui-btn-nav mui-pull-left"> <span class="mui-icon mui-icon-left-nav"></span> </button> <h1 class="mui-center mui-title">生日</h1> </div> <div class="mui-page-content"> <div class="mui-scroll-wrapper"> <div class="mui-scroll"> <button id=\'birth_date\' data-options=\'{"type":"date","beginYear":1970,"endYear":2016}\' class="btn mui-btn mui-btn-block margin_top_20">';
if(birthday==''||birthday==null||birthday==undefined){
$out+='选择日期';
}else{
$out+=$escape(birthday);
}
$out+='</button> </div> </div> </div> </div>  <div id="qinggan" class="mui-page"> <div class="mui-navbar-inner mui-bar mui-bar-nav"> <button type="button" class="mui-left mui-action-back mui-btn mui-btn-link mui-btn-nav mui-pull-left"> <span class="mui-icon mui-icon-left-nav"></span> </button> <h1 class="mui-center mui-title">情感状态</h1> </div> <div class="mui-page-content"> <div class="mui-scroll-wrapper"> <div class="mui-scroll"> <select name="basic" id="basic" class="margin_top_20"> <option value="保密" ';
if(basic=='保密'){
$out+='selected';
}
$out+='>保密</option> <option value="单身" ';
if(basic=='单身'){
$out+='selected';
}
$out+='>单身</option> <option value="恋爱中" ';
if(basic=='恋爱中'){
$out+='selected';
}
$out+='>恋爱中</option> <option value="已婚" ';
if(basic=='已婚'){
$out+='selected';
}
$out+='>已婚</option> </select> </div> </div> </div> </div>  <div id="xingqu" class="mui-page"> <div class="mui-navbar-inner mui-bar mui-bar-nav"> <button type="button" class="mui-left mui-action-back mui-btn mui-btn-link mui-btn-nav mui-pull-left"> <span class="mui-icon mui-icon-left-nav"></span> </button> <h1 class="mui-center mui-title">生日</h1> </div> <div class="mui-page-content"> <div class="mui-scroll-wrapper"> <div class="mui-scroll"> <div class="mui-input-group margin_top_15"> <div class="mui-input-row mui-checkbox"> <label>美食</label> <input id="meishi" name="favorite" value="美食" type="checkbox"> </div> <div class="mui-input-row mui-checkbox"> <label>电影</label> <input id="dianying" name="favorite" value="电影" type="checkbox"> </div> <div class="mui-input-row mui-checkbox"> <label>酒店</label> <input id="jiudian" name="favorite" value="酒店" type="checkbox"> </div> <div class="mui-input-row mui-checkbox"> <label>休闲娱乐</label> <input id="xiuxian" name="favorite" value="休闲娱乐" type="checkbox"> </div> <div class="mui-input-row mui-checkbox"> <label>丽人</label> <input id="liren" name="favorite" value="丽人" type="checkbox"> </div> <div class="mui-input-row mui-checkbox"> <label>旅游</label> <input id="lvyou" name="favorite" value="旅游" type="checkbox"> </div> </div> </div> </div> </div> </div>';
return new String($out);
});