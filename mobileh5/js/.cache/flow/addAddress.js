/*TMODJS:{"version":20,"md5":"693f2fc6d74d1f59b08203edda4450f9"}*/
template('flow/addAddress','<div class="mui-content"> <div class="mui-page-content"> <div class="mui-input-group margin_top_20"> <div class="mui-input-row"> <label>收货人姓名</label> <input type="text" id="consignee" name="consignee" class="mui-input-clear" value="" placeholder="请输入"> </div> <div class="mui-input-row"> <label>手机号码</label> <input type="text" name="mobile" id="mobile" class="mui-input-clear" value="" placeholder="请输入"> </div> <div class="mui-input-row"> <label>地区</label> <input id=\'showCityPicker\' class="cityResult" type="text" placeholder="请选择地区"> <input id="country" type="hidden" value=""/> <input id="province" type="hidden" value=""/> </div> <div class="mui-input-row xiangxi_adress"> <label>详细地址</label> <textarea id="address" name="address" placeholder="请输入"></textarea> </div> <div class="mui-input-row"> <label>邮政编码</label> <input type="text" id="zipcode" name="zipcode" class="mui-input-clear" value="" placeholder="请输入"> </div> </div> <div class="mui-button-row margin_top_20"> <button type="button" class="mui-btn mui-btn-primary login_password_btn add_btn">保存</button> </div> </div> </div> <script> /* 启用选择器插件 */ jQuery.ajaxJsonp(web_url+\'/mobile/basic.php?act=getMobileCities\',{onlyCountry:1},function(result){ console.log(result); if(result.state==\'true\'){ var cityPicker = new mui.PopPicker({ layer: 2 }); var cityData = result.data; cityPicker.setData(cityData); var showCityPickerButton = document.getElementById(\'showCityPicker\'); var cityResult = document.getElementById(\'showCityPicker\'); var hcountry = document.getElementById(\'country\'); var hprovince = document.getElementById(\'province\'); showCityPickerButton.addEventListener(\'tap\', function(event) { cityPicker.show(function(items) { cityResult.value = items[0].text + " " + items[1].text; hcountry.value = items[0].value; hprovince.value = items[1].value; }); }, false); } }); /* 保存收货地址 */ jQuery(\'.mui-content\').on(\'tap\', \'.add_btn\', function(){ var consignee = jQuery(\'#consignee\').val(); var mobile = jQuery(\'#mobile\').val(); var country = jQuery(\'#country\').val(); var province = jQuery(\'#province\').val(); var address = jQuery(\'#address\').val(); var zipcode = jQuery(\'#zipcode\').val(); if( !consignee.trim() || !mobile.trim() || !country.trim()|| !province.trim()|| !province.trim() || !address.trim() || !zipcode.trim() ){ mui.alert(\'信息填写不完整\'); return false; } jQuery.ajaxJsonp( web_url+\'/mobile/address.php\', { act:\'ajaxAddressSave\', consignee:consignee, mobile:mobile, country:country, province:province, address:address, zipcode:zipcode }, function(data){ if(data.state == \'true\'){ mui.alert(\'保存成功！\',\'提示\',function(){ /* 保存成功跳转到下单页面 */ mui.back(); }); } }); }) </script>');