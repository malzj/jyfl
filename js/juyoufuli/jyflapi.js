var api_url ="/jyflapi/";
function userShow(data){
	var user_id = data;
	var rudata ="";
	var user_id = $('#user_id').val();
            var username ;
            var nickname ;//用户名
            var mobile_phone ;//手机号
			var bound_status;
            var sex ;//性别
            var birthday;//生日
            var basic;//个人情况
            var xingqu; //兴趣
            var xingqu_1;
            var img;
	var url=api_url+"index.php?s=Users/User/userShow"
    $.ajax({
		type:"post",
		url:url,
		async:false,
		data:{user_id:user_id},
		dataType:"json",
		success:function(data){
			if(data.result=='true'){
			 username = data.business.user_name;
             nickname = data.business.nickname;//用户名
			 mobile_phone = data.business.mobile_phone?data.business.mobile_phone:'';
			 bound_status = data.business.bound_status;
             sex = data.business.sex;//性别
             birthday=data.business.birthday;//生日
             basic=data.business.basic;//个人情况
             xingqu=data.business.xingqu?data.business.xingqu:''; //兴趣
             xingqu_1=xingqu.split(',');
             img=data.business.pic;	
			 rudata=data;
			 return ;
			}
	},
	});
layer.open({
        type: 1,
        title:false,
        area:'570px',
        shadeClose:false, //点击遮罩关闭
        content:'<div class="per"><h3>个人信息</h3><div class="wrap_1">' +
		'<div class="left"><div class="form-group">卡号：<span class="card_num" id="card_id"></span></div>' +
		'<div class="form-group"><label>用户名：</label><span class="username"><input type="text" id="username"></span></div>' +
		'<div class="form-group"><label>手机号：</label><span class="username"><input type="text" id="mobile_phone"></span>'+(bound_status==0?'<button id="phone_bound" style="margin-left: 10px;">绑定</button>':'')+'</div>' +
		'<div class="form-group"><label>性别：</label><span id="male" class="radio_img radio_left"><input type="radio" name="Sex" value="1"></span>男 <span id="female" class="radio_img"><input type="radio" name="Sex" value="2"></span>女</div>' +
		'<div class="form-group" style="position:relative">生日： <input onclick="laydate()" class="birth" id="birth"><label class="date_ico laydate-icon" for="birth"></label></div><div class="form-group" id="geren">个人情况： <span class="radio_img" id="danshen"><input type="radio" name="per" value="单身"></span>单身 <span class="radio_img" id="love"><input type="radio" name="per" value="恋爱中"></span>恋爱中 <span class="radio_img" id="yihun"><input type="radio" name="per" value="已婚"></span>已婚 <span class="radio_img" id="baomi"><input type="radio" name="per" value="保密"></span>保密</div><form action="" method="get">兴趣： <span id="meishi" class="checkbox_img radio_left"><input name="favorite" type="checkbox" value="美食"></span>美食 <span id="dianying" class="checkbox_img"><input name="favorite" type="checkbox" value="电影"></span>电影 <span id="jiudian" class="checkbox_img"><input name="favorite" type="checkbox" value="酒店"></span>酒店 <span id="xiuxian" class="checkbox_img"><input name="favorite" type="checkbox" value="休闲娱乐"></span>休闲娱乐 <span id="liren" class="checkbox_img"><input name="favorite" type="checkbox" value="丽人"></span>丽人 <span id="lvyou" class="checkbox_img"><input name="favorite" type="checkbox" value="旅游"></span>旅游</form></div><div class="right"><form id="uploadForm"><div class="right_img" id="localImag"><img id="preview" src="images/img_login/touxiang_shang.png" width="120" height="120"></div><span class="file"><input type="file" name="file" id="doc" onchange="setImagePreview(this)"></span><span class="file_yes"><input type="button" value="确认" onclick="upload()" /></span></form></div></div><div style="text-align:center"><button id="save">保存</button></div></div>'
    });
               $('#preview').attr('src',img);
            
            $('#card_id').text(username);
            $('#username').val(nickname);
            $('#mobile_phone').val(mobile_phone);
//			性别
			if(sex==1){
		      $('#male').addClass('on');
		      $('#male>input').attr('checked',true);
			}else{
				 $('#female').addClass('on');
				  $('#female>input').attr('checked',true);
			}
			$('#birth').val(birthday);
//			个人情况
             switch (basic){
				case '已婚':
				 $('#yihun').addClass('on');
								$('#yihun>input').attr('checked',true);
								break;
				case '单身':
				 $('#danshen').addClass('on');
								$('#danshen>input').attr('checked',true);
				  break;
				case '恋爱中':
				  $('#love').addClass('on');
								$('#love>input').attr('checked',true);
				  break;
				case '保密':
				 $('#baomi').addClass('on');
								$('#baomi>input').attr('checked',true);
				  break;	
				};
				//个人情况点击增加checked
				$('#geren span').click(function(){
					$(this).children('input').attr('checked',true).parent('span').siblings().children('input').attr('checked',false);
				})
//				兴趣点击增加checked
				$('.checkbox_img').click(function(){
					if($(this).children('input').is(":checked")){
						$(this).children('input').attr("checked",true);
					}else{
						$(this).children('input').removeAttr("checked",true);
					}
				})
				
				//兴趣
				$("input:checkbox").click(function() {
					var xingqu = "";
					$("input:checkbox[name='favorite']:checked").each(function() {
						xingqu += $(this).val() + ",";
						
					});
					$('#xingqu').val(xingqu);

				});
				
					$(":checkbox[name='favorite']").prop("checked",false);
					var ck_val =xingqu_1;
					for(var i=0;i<ck_val.length;i++){
						switch(ck_val[i]){
					     case '美食':
				         $('#meishi').addClass('on_check');
						 $('#meishi>input').prop('checked',true);
				         break;
				         case '电影':
				         $('#dianying').addClass('on_check');
						 $('#dianying>input').prop('checked', true);
				         break;	
				         case '酒店':
				         $('#jiudian').addClass('on_check');
						 $('#jiudian>input').prop('checked', true);
				         break;	
				         case '休闲娱乐':
				         $('#xiuxian').addClass('on_check');
						 $('#xiuxian>input').prop('checked', true);
				         break;	
				         case '丽人':
				         $('#liren').addClass('on_check');
						 $('#liren>input').prop('checked', true);
				         break;	
				         case '旅游':
				         $('#lvyou').addClass('on_check');
						 $('#lvyou>input').prop('checked', true);
				         break;	
						}
					}
	
	
};
function upload(data){
	 var formData = new FormData($( "#uploadForm" )[0]);  
	 var rudata ='';
     $.ajax({  
          url: api_url+'index.php?s=Upload/Upload/upload' ,
          type: 'POST',  
          data: formData,  
          async: false,  
          cache: false,  
          contentType: false,  
          processData: false,  
          success: function (returndata) {
        	$('#img').val(returndata.img)
                 rudata= returndata;
			  layer.msg('上传成功！');
          },  
          error: function (returndata) {  
        		//console.log(returndata)
              
          }  
     });  
	return rudata;
}
function userSave(){
//	var rudata='';
    var user_id=$('#user_id').val();
    var nickname=$('#username').val();
    var mobile_phone=$('#mobile_phone').val();
    var sex=$('input[name="Sex"]:checked').val();
    var birthday=$('#birth').val();
    var basic=$('input[name="per"]:checked').val();
	var partten    = /^1[3,5,7,8]\d{9}$/;
    var xingqu;
    var xingqu_1=$('#xingqu').val();
	if(mobile_phone&&!partten.test(mobile_phone)){
		layer.alert('请输入正确的手机号！');
		return false;
	}
    if (xingqu_1==""||xingqu_1==null){
    	xingqu=$('input[type="checkbox"]:checked').val();
    }else{
    	xingqu =$('#xingqu').val();
    }
//  判断是否有图片
    var pic ='';
    var img = $('#img').val();
    if(img==""||img==null){
    	pic= $('#preview').attr('src');
    }else{
    	pic=$('#img').val();
    	    }
	var url=api_url+"index.php?s=Users/User/userUpdate"
    $.ajax({
		type:"post",
		url:url,
		async:false,
		data:{user_id:user_id,nickname:nickname,mobile_phone:mobile_phone,sex:sex,birthday:birthday,xingqu:xingqu,basic:basic,pic:pic},
		dataType:"json",
		success:function(data){
			 var usertx=$('#img').val();
			  $('#usertx').attr('src',usertx);
			if(data.result=='true'){
				layer.msg('保存成功！')
			return ;
			}else if(data.result=='false'){
				layer.msg('保存失败！');
			}
	},
	});
};

/**
 * 安全设置的状态
 * function showSafe
 *
 * @param int user_id 用户id
 * @return array result
 */
function showSafe(user_id){
	var url =api_url+"index.php/Users/User/showSafe";
	var result = new Array();
	$.ajax({
		type:"post",
		url:url,
		async:false,
		data:{user_id:user_id},
		dataType:"json",
		success:function(data){
			//console.log(data);
			result = data;
		}
	});
	return result;
}

/**
 * 修改登录密码
 * @author zhaoyingchao
 * 
 * @param user_id  用户id
 * @param old_password	旧密码
 * @param new_password	新密码
 * @param con_password  确认密码
 */
function userLoginPass(user_id,old_password,new_password,con_password){
	var url = api_url+'index.php/Users/User/userLoginPass';
	$.ajax({
		type:"post",
		url:url,
		data:{
			user_id:user_id,
			old_password:old_password,
			new_password:new_password,
			con_password:con_password,
		},
		dataType:"json",
		success:function(data){
			if(data.result == "true"){
				layer.closeAll();
				layer.msg(data.msg);
			}else if(data.result == "false"){
				layer.msg(data.msg);
			}
		},
	});
}

/**
 * 获取验证码
 * @author zhaoyingchao
 *
 * @param user_id	用户id
 * @param tel		电话号码
 */
function getverification(user_id,tel){
	var url = api_url+'index.php/Users/User/smsvrerifyJs';
	//alert(user_id+'='+tel);
	var result = new Array();
	$.ajax({
		type:"post",
		url:url,
		data:{
			user_id:user_id,
			tel:tel,
		},
		dataType:"json",
		success:function (data) {
			//console.log(data);
			result = data;
		}
	});
	return result;
}

/**
 * 绑定手机
 * @author zhaoyingchao
 *
 * @param user_id		用户id
 * @param dynamic_code	动态码
 */
function boundPhone(user_id,tel,captcha){
	var url = api_url+'index.php/Users/User/editphone';
	// var result = new Array();
	$.ajax({
		type:"post",
		url:url,
		data:{
			user_id:user_id,
			tel:tel,
			captcha:captcha,
		},
		dataType:"json",
		success:function(data){
			//console.log(data);
			if(data.result == "true"){
				layer.closeAll();
				// showSafeCenter();
				layer.msg(data.msg,function(){
					location.reload();
				});
			}else{
				// layer.closeAll();
				// showSafeCenter();
				layer.msg("手机绑定失败！");
			}

		}
	});
}

/**
 * 显示安全问题答案
 * @author zhaoyingchao
 *
 * @param user_id	用户id
 */
function showQuestionAnswer(user_id){
	var url = api_url+'index.php/Users/User/saveLoginQues';
	$.ajax({
		type:"post",
		url:url,
		data:{
			user_id:user_id,
		},
		dataType:"json",
		success:function(data){
			if(data.result == "true"){
				$('.layui-layer-content').html('<div class="question"><h3>安全问题</h3><div class="form-group"><div><label>问题一：</label><span>您的姓名是？</span></div><div><label>答案：</label><input id="answerone" type="text" value="'+data.answerone+'"></div></div><div class="form-group"><div><label>问题二：</label><span>您的年龄是？</span></div><div><label>答案：</label><input id="answertwo" type="text" value="'+data.answertwo+'"></div></div><div class="form-group"><div><label>问题三：</label><span>您的身高是？</span></div><div><label>答案：</label><input id="answerthree" type="text" value="'+data.answerthree+'"></div></div><div class="btn_question"><button class="yes">确认提交</button> <button class="no">返回安全中心</button></div></div>');
			}else{
				$('.layui-layer-content').html('<div class="question"><h3>安全问题</h3><div class="form-group"><div><label>问题一：</label><span>您的姓名是？</span></div><div><label>答案：</label><input id="answerone" type="text"></div></div><div class="form-group"><div><label>问题二：</label><span>您的年龄是？</span></div><div><label>答案：</label><input id="answertwo" type="text"></div></div><div class="form-group"><div><label>问题三：</label><span>您的身高是？</span></div><div><label>答案：</label><input id="answerthree" type="text"></div></div><div class="btn_question"><button class="yes">确认提交</button> <button class="no">返回安全中心</button></div></div>');
			}

		}
	});
}


/**
 * 编辑安全问题
 * @author zhaoyingchao
 *
 * @param user_id		用户id
 * @param answerone		问题一
 * @param answertwo		问题二
 * @param answerthree	问题三
 */
function editLoginQues(user_id,answerone,answertwo,answerthree){
	var url = api_url+'index.php/Users/User/editLoginQues';
	$.ajax({
		type:"post",
		url:url,
		data:{
			user_id:user_id,
			answerone:answerone,
			answertwo:answertwo,
			answerthree:answerthree,
		},
		dataType:"json",
		success:function(data){
			if(data.result == "true"){
				layer.closeAll();
				layer.msg(data.msg);
			}else{
				layer.closeAll();
				layer.msg(data.msg);
			}
		}
	});
}

function showAddress(){
		var url =api_url+"index.php?s=Users/User/showAddress";
		var user_id = $('#user_id').val();
		var htmlshouhuolist="";
		$.ajax({
        type:"post",
		url:url,
		async:false,
		data:{user_id:user_id},
		dataType:"json",
		success:function(data){
			  console.log(data);
			if(data.result=='true'){
              var list = data.business.result;
				if(!jQuery.isEmptyObject(data.business.result)){
					var num = [data.business.result.length];

					  for (var i = 0; i <num; i++) {
						//list[i]
						  if(list[i].selected==1){
							  var selected = '<span class="checkbox_img on_check"></span>默认地址';
						  }else{
							  var selected = '<span class="checkbox_img"></span>设为默认'
						  }
							var html='<tr data-address-id="'+list[i].address_id+'" style="cursor:pointer;"><td class="td_1">'+list[i].consignee+'</td><td class="td_2">'+list[i].country+list[i].province+list[i].address+'</td><td class="td_3">'+list[i].mobile+'</td><td class="gn_btn"><a class="set_address" href="javascript:void(0);">'+selected+'</a><a href="javascript:void(0);" onclick="delAddress(this,'+list[i].address_id+')">删除</a><a href="javascript:void(0);" class="xiugai" data-addressid="'+list[i].address_id+'">修改</a></td></tr>';
						htmlshouhuolist+=html;

					  }
			}
			return ;
		  }
		},
		});
    var index = layer.open({
        type: 1,
        title:false,
        area:'570px',
        shadeClose: false, //点击遮罩关闭
        content:'<div class="shouhuo_left"><h3>收货信息</h3><div class="table-responsive"><table class="table"><thead><tr><td>收件人</td><td>地址/邮编</td><td>电话/手机</td><td>操作</td></tr></thead><tbody>'+htmlshouhuolist+'</tbody></table></div><div class="add_new" onclick="showprovince()">添加新地址</div></div>'
    });
	return index;
	}

/*
 获取地址列表
 getAddressHtml
 author chao
 time:2016/5/13
 */
function getAddressHtml(){
	var url =api_url+"index.php?s=Users/User/showAddress";
	var user_id = $('#user_id').val();
	var htmlshouhuolist="";
	$.ajax({
		type:"post",
		url:url,
		async:false,
		data:{user_id:user_id},
		dataType:"json",
		success:function(data){
			if(data.result=='true'){
				var num = [data.business.result.length];
				var list = data.business.result;
				for (var i = 0; i <num; i++) {
					//list[i]
					if(list[i].selected==1){
						var selected = '<span class="checkbox_img on_check"></span>默认地址';
					}else{
						var selected = '<span class="checkbox_img"></span>设为默认'
					}
					var html='<tr data-address-id="'+list[i].address_id+'" style="cursor:pointer;"><td class="td_1">'+list[i].consignee+'</td><td class="td_2">'+list[i].country+list[i].province+list[i].address+'</td><td class="td_3">'+list[i].mobile+'</td><td class="gn_btn"><a class="set_address" href="javascript:void(0);">'+selected+'</a><a href="javascript:void(0);" onclick="delAddress(this,'+list[i].address_id+')">删除</a><a href="javascript:void(0);" class="xiugai" data-addressid="'+list[i].address_id+'">修改</a></td></tr>';

					htmlshouhuolist+=html;

				}
				return ;
			}
		},
	});
	return htmlshouhuolist;
}

/*
 添加收货地址
 addAddress
 author chao
 time:2016/5/13
 */

function addAddress()
{
	var user_id = $("#user_id").val();
	var hcountryid = $("#country").val();
	var hprovinceid = $("#province").val();
	var hcityid = $("#city").val();
	var hstreet = $("#address").val();
	var zipcode = $("#zipcode").val();
	var consignee = $("#consignee").val();
	var mobile = $("#mobile").val();
	var partten    = /^1[3,5,7,8]\d{9}$/;
	// var msg = new Array();
	var err = false;
	//收货人所在地区不能为空
	if (hcountryid == 0){
		//msg.push(country_not_null);
		layer.msg('请正确填写收货人所在地区!');

		err = true;
	}
	if (hprovinceid == 0){
		err = true;
		//msg.push(province_not_null);
		// msg.push('请正确填写收货人所在地区!');
		layer.msg('请正确填写收货人所在地区!');
	}
	// if (hcityid == 0){
	// 	err = true;
	// 	//msg.push(city_not_null);
	// 	// msg.push('请正确填写收货人所在地区');
	// 	layer.msg('请正确填写收货人所在地区!');
	// }


	//收货人不能为空
	if (Utils.isEmpty(consignee)){
		err = true;
		// msg.push('收货人姓名不能为空！');
		layer.msg('收货人姓名不能为空!');
	}

	/*
	 if (district && district.length > 1){
	 if (district.value == 0){
	 err = true;
	 //msg.push(district_not_null);
	 msg.push('请正确填写收货人所在地区');
	 }
	 }*/
	//邮政编码不能为空
	// if (Utils.isEmpty(zipcode)){
	// 	err = true;
	// 	// msg.push('邮政编码不能为空!');
	// 	layer.msg('邮政编码不能为空!');
	// }

	//收货人详细地址不能为空
	if (Utils.isEmpty(hstreet)){
		err = true;
		// msg.push('街道地址不能为空！');
		layer.msg('街道地址不能为空!');
	}

	//收货人联系方式不能为空
	if (Utils.isEmpty(mobile)){
		err = true;
		// msg.push('手机不能为空！');
		layer.msg('手机不能为空!');
	}else{
		if (mobile.length > 0 && !partten.test(mobile)){
			err = true;
			// msg.push('手机号不合法！');
			layer.msg('手机号不合法!');
		}
	}
	if(err == true){
		return false;
	}else {
		$.ajax({
			type: "post",
			url: api_url+"index.php/Users/User/addAddress",
			data: {
				user_id: user_id,
				hcountryid: hcountryid,
				hprovinceid: hprovinceid,
				hcityid: hcityid,
				hstreet: hstreet,
				zipcode: zipcode,
				consignee: consignee,
				mobile: mobile,
			},
			datatype: "json",
			success: function (data) {
				if(data.result=='true'){
					var html = getAddressHtml();
					$('.layui-layer-content').html('<div class="shouhuo_left"><h3>收货信息</h3><div class="table-responsive"><table class="table"><thead><tr><td>收件人</td><td>地址/邮编</td><td>电话/手机</td><td>操作</td></tr></thead><tbody>'+html+'</tr></tbody></table></div><div class="add_new" onclick="showprovince()">添加新地址</div></div>');
					layer.msg('添加地址成功！');
				}else if(data.result=='false'){
					var html = getAddressHtml();
					$('.layui-layer-content').html('<div class="shouhuo_left"><h3>收货信息</h3><div class="table-responsive"><table class="table"><thead><tr><td>收件人</td><td>地址/邮编</td><td>电话/手机</td><td>操作</td></tr></thead><tbody>'+html+'</tr></tbody></table></div><div class="add_new" onclick="showprovince()">添加新地址</div></div>');
					layer.msg('添加地址失败!');
				}
			},
		});
	}
}


/*
 修改收货地址的页面
 saveAddress
 author chao
 version 1.0
 time:2016/5/13
 */

function saveAddress(address_id){
	var user_id = $("#user_id").val();
	var option="<option style='width:100px;background-color:black;' display='inline' value=0>请选择市</option>";
	var index;
	$.ajax({
		type:"post",
		url:api_url+"index.php/Users/User/getEditAddress",
		data:{user_id:user_id,address_id:address_id},
		dataType:"json",
		beforeSend:function(){
			index = layer.load();
		},
		success:function(data){
			console.log(data);
			layer.close(index);
			//渲染省市县select数据
			var country_list = data.countryList;
			for(var i=0;i<country_list.length;i++){
				option += "<option style='width:100px;background-color:black;' display='inline' value="+country_list[i].region_id+">"+country_list[i].region_name+"</option>";
			}
			$("#country").html(option);
			option="<option style='width:100px;background-color:black;' display='inline' value=0>请选择区</option>";
			var province_list = data.provinceList;
			for(var i=0;i<province_list.length;i++){
				option += "<option style='width:100px;background-color:black;' display='inline' value="+province_list[i].region_id+">"+province_list[i].region_name+"</option>";
			}
			$('#province').html(option);
			// option="<option style='width:100px;background-color:black;' display='inline' value=0>请选择区</option>";
			// var city_list = data.cityList;
			// for(var i=0;i<city_list.length;i++){
			// 	option += "<option style='width:100px;background-color:black;' display='inline' value="+city_list[i].region_id+">"+city_list[i].region_name+"</option>";
			// }
			// $('#city').html(option);

			//渲染地址数据
			$("option[value="+data.addressInfo.country+"]").attr("selected",true);
			$("option[value="+data.addressInfo.province+"]").attr("selected",true);
			// $("option[value="+data.addressInfo.city+"]").attr("selected",true);
			$("#address").val(data.addressInfo.address);
			$("#zipcode").val(data.addressInfo.zipcode);
			$("#consignee").val(data.addressInfo.consignee);
			$("#mobile").val(data.addressInfo.mobile);
			$("#address_id").val(data.addressInfo.address_id);

		}
	});
}
/*
 更新收货地址
 updateAddress
 author chao
 version 1.0
 time:2016/5/13
 */

function updateAddress(){
	var user_id = $('#user_id').val();
	var address_id = $('#address_id').val();
	var hcountryid = $("#country").val();
	var hprovinceid = $("#province").val();
	var hcityid = $("#city").val();
	var hstreet = $("#address").val();
	var zipcode = $("#zipcode").val();
	var consignee = $("#consignee").val();
	var mobile = $("#mobile").val();
	var partten    = /^1[3,5,7,8]\d{9}$/;
	// var msg = new Array();
	var err = false;
	//收货人所在地区不能为空
	if (hcountryid == 0){
		//msg.push(country_not_null);
		layer.msg('请正确填写收货人所在地区!');

		err = true;
	}
	if (hprovinceid == 0){
		err = true;
		//msg.push(province_not_null);
		// msg.push('请正确填写收货人所在地区!');
		layer.msg('请正确填写收货人所在地区!');
	}
	if (hcityid == 0){
		err = true;
		//msg.push(city_not_null);
		// msg.push('请正确填写收货人所在地区');
		layer.msg('请正确填写收货人所在地区!');
	}


	//收货人不能为空
	if (Utils.isEmpty(consignee)){
		err = true;
		// msg.push('收货人姓名不能为空！');
		layer.msg('收货人姓名不能为空!');
	}

	//邮政编码不能为空
	// if (Utils.isEmpty(zipcode)){
	// 	err = true;
	// 	// msg.push('邮政编码不能为空!');
	// 	layer.msg('邮政编码不能为空!');
	// }

	//收货人详细地址不能为空
	if (Utils.isEmpty(hstreet)){
		err = true;
		// msg.push('街道地址不能为空！');
		layer.msg('街道地址不能为空!');
	}

	//收货人联系方式不能为空
	if (Utils.isEmpty(mobile)){
		err = true;
		// msg.push('手机不能为空！');
		layer.msg('手机不能为空!');
	}else{
		if (mobile.length > 0 && !partten.test(mobile)){
			err = true;
			// msg.push('手机号不合法！');
			layer.msg('手机号不合法!');
		}
	}
	if(err == true){
		return false;
	}else {
		$.ajax({
			type: "post",
			url: api_url+"index.php/Users/User/updateAddress",
			data: {
				user_id: user_id,
				address_id:address_id,
				hcountryid: hcountryid,
				hprovinceid: hprovinceid,
				hcityid: hcityid,
				hstreet: hstreet,
				zipcode: zipcode,
				consignee: consignee,
				mobile: mobile,
			},
			datatype: "json",
			success: function (data) {
				console.log(data);
				if(data.result=='true'){
					var html = getAddressHtml();
					$('.layui-layer-content').html('<div class="shouhuo_left"><h3>收货信息</h3><div class="table-responsive"><table class="table"><thead><tr><td>收件人</td><td>地址/邮编</td><td>电话/手机</td><td>操作</td></tr></thead><tbody>'+html+'</tr></tbody></table></div><div class="add_new" onclick="showprovince()">添加新地址</div></div>');
					layer.msg('修改地址成功！');
				}else if(data.result=='false'){
					var html = getAddressHtml();
					$('.layui-layer-content').html('<div class="shouhuo_left"><h3>收货信息</h3><div class="table-responsive"><table class="table"><thead><tr><td>收件人</td><td>地址/邮编</td><td>电话/手机</td><td>操作</td></tr></thead><tbody>'+html+'</tr></tbody></table></div><div class="add_new" onclick="showprovince()">添加新地址</div></div>');
					layer.msg('修改地址失败!');
				}
			},
		});
	}
}

function delAddress(e,data){
	window.event? window.event.cancelBubble = true : e.stopPropagation();
	var user_id = $('#user_id').val();
	var address_id = data;
	if(confirm('确定删除该条收货地址？')) {
		var url = api_url + "index.php?s=Users/User/delAddress";
		$.ajax({
			type: "post",
			url: url,
			async: false,
			data: {user_id: user_id, address_id: address_id},
			dataType: "json",
			success: function (data) {
				if (data.result == 'true') {
					var html = getAddressHtml();
					$('.layui-layer-content').html('<div class="shouhuo_left"><h3>收货信息</h3><div class="table-responsive"><table class="table"><thead><tr><td>收件人</td><td>地址/邮编</td><td>电话/手机</td><td>操作</td></tr></thead><tbody>' + html + '</tr></tbody></table></div><div class="add_new" onclick="showprovince()">添加新地址</div></div>');
					return;
				}else{
					layer.msg(data.msg);
				}
			},
		});
	}else{
		return false;
	}
};
