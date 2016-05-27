var data_url ="http://192.168.1.161/jyflapi/";
$(function(){
$('#saf').on('click',function(){
	showSafeCenter();
});
	/**
	 * 安全中心渲染
	 * @author zhaoyingchao
	 */
	function showSafeCenter(){
		var user_id=$('#user_id').val();
		var data=showSafe(user_id);
		if(data.result == "true"){
			layer.open({
				type: 1,
				title:false,
				area:'570px',
				shadeClose: false, //点击遮罩关闭
				content:'<div class="saf"><h3>安全中心</h3><table class="table"><tr class="tr_1"><td><span></span>登录密码</td><td class="td_2">'+data.password.msg+'</td><td class="td_3">设置登陆密码，降低盗号风险；</td><td class="td_4">立即设置</td></tr><tr class="tr_2"><td><span></span>手机号</td><td class="td_2">'+data.phone.msg+'</td><td class="td_3">绑定手机，可直接使用手机号登陆；</td><td class="td_4">立即设置</td></tr><tr class="tr_3"><td><span></span>安全问题</td><td class="td_2">'+data.answer.msg+'</td><td class="td_3">保护账户安全，验证您身份的工具之一；</td><td class="td_4">立即设置</td></tr></table></div>'
			});
		}else{
			layer.msg("获取数据失败，请重试！");
		}
	}
// $('#shouhuo').on('click',function(){
//     layer.open({
//         type: 1,
//         title:false,
//         area: '570px',
//         shadeClose: false, //点击遮罩关闭
//         content:'<div class="shouhuo"><h3>收货信息</h3><div class="table-responsive"><table class="table"><thead><tr><td>收件人</td><td>地址/邮编</td><td>电话/手机</td><td>操作</td></tr></thead><tbody><tr><td class="td_1">王某某</td><td>北京市被北京北京南方国际化的减肥的复古风发生的防守打法萨法似懂非懂是发顺丰说的</td><td class="td_3">13820906181</td><td class="gn_btn"><span>删除</span><span class="xiugai" onclick="saveAddress(4)">修改</span></td></tr></tbody></table></div><div class="add_new" onclick="showprovince()">添加新地址</div></div>'
//     });
// });
$('#reg').on('click',function(){
    layer.open({
        type: 1,
        title:false,
        area:'570px',
        shadeClose: false, //点击遮罩关闭
        content:'<div class="reg"><h3>卡充值</h3><div style="overflow:hidden"><div class="reg_title">充值金额:</div><div class="reg_num"><span class="radio_img"><input type="radio" name="money"></span>30点<span>人民币39.0元</span><br><span class="radio_img"><input type="radio" name="money"></span>50点<span>人民币65.0元</span><br><span class="radio_img"><input type="radio" name="money"></span>100点<span class="rmb">人民币130.0元</span><br></div></div><div class="pay_title">支付方式</div><table class="table"><thead><tr><td>名称</td><td>描述</td></tr></thead><tbody><tr><td style="width:60px"><span class="radio_img1"><input type="radio" name="choose"></span>支付宝</td><td>国内先进的网上支付平台国内先进的网上支付平台国内先进的网上支付平台国内先进的网上</td></tr><tr><td><span class="radio_img1"><input type="radio" name="choose"></span>支付宝</td><td>国内先进的网上支付平台国内先进的网上支付平台国内先进的网上支付平台国内先进的网上</td></tr></tbody></table><div class="btn_all"><button class="btn_reg">立即充值</button> <button class="btn_1">充值金额</button> <button class="btn_2">充值记录</button></div></div>'
    });
});
$('#red_packet').on('click',function(){
    layer.open({
        type: 1,
        title:false,
        area:'570px',
        shadeClose: false, //点击遮罩关闭
        content:'<div class="red_packet"><h3>我的红包</h3><div><table class="table"><thead><tr><td>红包名称</td><td>红包金额</td><td>最小订单金额</td><td>获取时间</td><td>截至使用日期</td><td>红包状态</td></tr></thead><tbody><tr><td>你现在还没有红包</td></tr></tbody></table><div class="add">添加红包</div><div class="form-group"><label>红包序列号：</label><span class="red_card"><input type="text"></span><label>红包密码：</label><span class="red_num"><input type="password"></span> <button class="btn_add">添加红包</button></div></div></div>'
    });
});
$('#merge').on('click',function(){
    layer.open({
        type: 1,
        title:false,
        area:'570px',
        shadeClose: false, //点击遮罩关闭
        content:'<div class="merge"><h3>卡合并</h3><div class="form-group" style="margin-top:20px"><label>转出（需清空）的卡号：</label><span class="qk"><input type="text"></span><label>密码：</label><span class="qk"><input type="password"></span></div><div class="form-group"><label>转入（合并到）的卡号：</label><span class="qk"><input type="text"></span><label>密码：</label><span class="qk"><input type="password"></span></div><div class="btn_merge"><button>确认合并</button></div><div class="tip"><p>温馨提示：</p><span class="tip_1">1.卡合并后，有效期以转入（合并到）的卡号为准：</span> <span>2.3个月内只能合并一次；</span> <span class="tip_1">3.不支持多张（2张以上）卡合并；</span> <span>4.卡合并后，业务以转入卡业务为准；</span></div></div>'
    });
});
// $(document).delegate('.add_new', 'click', function(){
// 	$('.layui-layer-content').html('<div class="set_add"><h3>添加地址</h3><div class="form-group"><label><span class="xing">*</span>所在地区：</label><select name="country" id="country" onchange="region.changed(this, 1, \'province\')" style="width:100px;background-color:transparent;" ></select><select name="province" id="province" onchange="region.changed(this, 2, \'city\')" style="width:100px;background-color:transparent;" ></select><select name="city" id="city" style="width:100px;background-color:transparent;"  ></select></div><div class="form-group"><label><span class="xing">*</span>街道地址：</label><input type="text"></div><div class="form-group"><label><span class="xing">*</span>邮政编码：</label><input type="text"></div><div class="form-group"><label><span class="xing">*</span>收货人姓名：</label><input type="text"></div><div class="form-group"><label><span class="xing">*</span>电话号码：</label><input type="text"></div><div class="set_add_btn"><button class="yes">保存</button><button class="no">取消</button></div></div>');
// });
$(document).delegate('.add_new', 'click', function(){
	$('.layui-layer-content').html(
		'<div class="set_add"><h3>添加地址</h3><div class="form-group"><label>' +
		'<span class="xing">*</span>所在地区：</label>' +
		'<select name="country" id="country" onchange="region.changed(this, 1, \'province\')" style="width:100px;background-color:transparent;" ></select>' +
		'<select name="province" id="province" onchange="region.changed(this, 2, \'city\')" style="width:100px;background-color:transparent;" ></select>' +
		'<select name="city" id="city" style="width:100px;background-color:transparent;"  ></select></div>' +
		'<div class="form-group"><label><span class="xing">*</span>街道地址：</label><input type="text" id="address"></div>' +
		'<div class="form-group"><label><span class="xing">*</span>邮政编码：</label><input type="text" id="zipcode"></div>' +
		'<div class="form-group"><label><span class="xing">*</span>收货人姓名：</label><input type="text" id="consignee"></div>' +
		'<div class="form-group"><label><span class="xing">*</span>电话号码：</label><input type="text" id="mobile"></div>' +
		'<div class="set_add_btn"><button class="yes">保存</button><button class="no">取消</button></div></div>'
	);
});
$(document).delegate('.btn_all .btn_2','click',function(){
	$('.layui-layer-content').html('<div class="log"><h3>充值记录</h3><table class="table"><thead><tr><td>操作时间</td><td>类型</td><td>金额</td><td>会员备注</td><td>管理员备注</td><td>状态</td><td>操作</td></tr></thead><tbody><tr></tr></tbody></table></div>');
});
$(document).delegate('.tr_1 .td_4','click',function(){
	$('.layui-layer-content').html(
		'<div class="set"><h3>设置密码</h3>' +
		'<div class="form-group ps">' +
		'<div class="ywz_zhucexiaobo">' +
		'<div class="ywz_zhuce_youjian">旧密码：</div>' +
		'<div class="ywz_zhuce_xiaoxiaobao">' +
		'<div class="ywz_zhuce_kuangzi">' +
		'<input name="oldPassword" type="password" id="old_password" class="ywz_zhuce_kuangwenzi1">' +
		'</div>' +
		'</div>' +
		'</div>' +
		'</div>' +
		'<div class="form-group ps">' +
		'<div class="ywz_zhucexiaobo">' +
		'<div class="ywz_zhuce_youjian">新密码：</div>' +
		'<div class="ywz_zhuce_xiaoxiaobao">' +
		'<div class="ywz_zhuce_kuangzi">' +
		'<input name="tbPassword" type="password" id="tbPassword" class="ywz_zhuce_kuangwenzi1">' +
		'</div>' +
		'<div class="ywz_zhuce_huixian" id="pwdLevel_1"></div>' +
		'<div class="ywz_zhuce_huixian" id="pwdLevel_2"></div>' +
		'<div class="ywz_zhuce_huixian" id="pwdLevel_3"></div>' +
		'<span class="ywz_zhuce_hongxianwenzi">弱</span>' +
		' <span class="ywz_zhuce_hongxianwenzi">中</span> ' +
		'<span class="ywz_zhuce_hongxianwenzi">强</span>' +
		'</div>' +
		'</div>' +
		'</div>' +
		'<div class="form-group ps">' +
		'<label style="margin-left:-20px">确认密码：</label>' +
		'<span class="password"><input style="width:200px" id="con_password" type="password"></span>' +
		'</div>' +
		'<div class="set_btn"><button class="edit_password">确定</button><button>取消</button></div></div>'
	)
});
	/**
	 * 修改密码确定
	 * @author zhaoyingchao
	 */
	$(document).delegate('.set .set_btn .edit_password','click',function(){
		var user_id = $("#user_id").val();
		var old_password = $('#old_password').val();
		var new_password = $('#tbPassword').val();
		var con_password = $('#con_password').val();
		userLoginPass(user_id,old_password,new_password,con_password);
	});
    $(document).delegate('.tr_2 .td_4','click',function(){
	$('.layui-layer-content').html('<div class="tel"><h3>绑定手机号</h3><div class="form-group"><label>手机号：</label><input id="tel" type="text"><div class="ach"><input type="button" id="getverification" value="获取验证码"></div></div><div class="form-group"><label>动态码：</label><input id="captcha" type="text"></div><div class="btn_tel"><button class="btn_bound">绑定</button> <button class="btn_cancel">取消</button></div></div>');
});
	/**
	 * 获取验证码
	 * @author zhaoyingchao
	 */
	$(document).delegate('#getverification','click',function(){
		var user_id = $('#user_id').val();
		var tel = $('#tel').val();
		alert(user_id+'-'+tel);
		settime(this);
		var data = getverification(user_id,tel);
	});
	/**
	 * 绑定手机
	 * @author zhaoyingchao
	 */
	$(document).delegate('.btn_tel .btn_bound','click',function(){
		var user_id = $('#user_id').val();
		var tel = $('#tel').val();
		var captcha = $('#captcha').val();
		boundPhone(user_id,tel,captcha);
	});
	/**
	 * 绑定手机取消
	 * @author zhaoyingchao
	 */
	$(document).delegate('.btn_tel .btn_cancel','click',function(){
		layer.closeAll();
		showSafeCenter();
	});
	/**
	 * 安全问题页面渲染
	 */
	$(document).delegate('.tr_3 .td_4','click',function(){
		var user_id = $('#user_id').val();
		showQuestionAnswer(user_id);
});
	/**
	 * 安全问题提交
	 * @author zhaoyingchao
	 */
	$(document).delegate('.btn_question .yes','click',function(){
		var user_id = $('#user_id').val();
		var answerone = $('#answerone').val();
		var answertwo = $('#answertwo').val();
		var answerthree = $('#answerthree').val();
		editLoginQues(user_id,answerone,answertwo,answerthree);
	});
//单选按钮点击样式
			$(document).delegate('.radio_img','click',function(){
				  $(this).addClass("on").siblings().removeClass("on");
				})
//多选按钮点击效果
$(document).delegate('.checkbox_img','click',function(){
				$(this).toggleClass( "on_check" )
				})

$(document).delegate('.radio_img1','click',function(){
				 $(this).addClass("on").parents('tr').siblings().children().find('.radio_img1').removeClass("on");
				})

$(document).delegate('.set_btn .no','click',function(){
	layer.closeAll();
	showSafeCenter();
})
//返回安全中心
$(document).delegate('.btn_question .no','click',function(){
	layer.closeAll();
	showSafeCenter();
})
//添加地址保存
$(document).on('click','.set_add_btn .yes',function(){
	addAddress();
});
//添加地址取消
$(document).on('click','.set_add_btn .no',function(){
				// $('.layui-layer-content').html('<div class="shouhuo"><h3>收货信息</h3><div class="table-responsive"><table class="table"><thead><tr><td>收件人</td><td>地址/邮编</td><td>电话/手机</td><td>操作</td></tr></thead><tbody><tr><td>王某某</td><td>北京市被北京北京南方国际化的减肥</td><td>13820906181</td><td class="gn_btn"><span>删除</span><span class="xiugai" onclick="saveAddress(4)">修改</span></td></tr></tbody></table></div><div class="add_new" onclick="showprovince()">添加新地址</div></div>')
	layer.closeAll();
	showAddress();
})
//更新地址保存
$(document).on('click','.set_add_btn .edit',function(){
	updateAddress();
});

	$(document).delegate('.gn_btn .xiugai','click',function(){
				$('.layui-layer-content').html('<div class="set_add"><h3>修改地址</h3><div class="form-group"><input type="hidden" name="address_id" id="address_id" value=""><label><span class="xing">*</span>所在地区：</label><select name="country" id="country" onchange="region.changed(this, 1, \'province\')" style="width:100px;background-color:transparent;" ></select><select name="province" id="province" onchange="region.changed(this, 2, \'city\')" style="width:100px;background-color:transparent;" ></select><select name="city" id="city" style="width:100px;background-color:transparent;"  ></select></div><div class="form-group"><label><span class="xing">*</span>街道地址：</label><input type="text" id="address"></div><div class="form-group"><label><span class="xing">*</span>邮政编码：</label><input type="text" id="zipcode"></div><div class="form-group"><label><span class="xing">*</span>收货人姓名：</label><input type="text" id="consignee"></div><div class="form-group"><label><span class="xing">*</span>电话号码：</label><input type="text" id="mobile"></div><div class="set_add_btn"><button class="edit">保存</button><button class="no">取消</button></div></div>')
				})
$(document).on('focus','#tbPassword',function(){
//密码安全强度显示
$('#tbPassword').focus(function () {
		$('#pwdLevel_1').attr('class', 'ywz_zhuce_hongxian');
		$('#tbPassword').keyup();
	});
	$('#tbPassword').keyup(function () {
		var __th = $(this);

		if (!__th.val()) {
			$('#pwd_tip').hide();
			$('#pwd_err').show();
			Primary();
			return;
		}
		if (__th.val().length < 6) {
			$('#pwd_tip').hide();
			$('#pwd_err').show();
			Weak();
			return;
		}
		var _r = checkPassword(__th);
		if (_r < 1) {
			$('#pwd_tip').hide();
			$('#pwd_err').show();
			Primary();
			return;
		}

		if (_r > 0 && _r < 2) {
			Weak();
		} else if (_r >= 2 && _r < 4) {
			Medium();
		} else if (_r >= 4) {
			Tough();
		}

		$('#pwd_tip').hide();
		$('#pwd_err').hide();
	});
	function Primary() {
		$('#pwdLevel_1').attr('class', 'ywz_zhuce_huixian');
		$('#pwdLevel_2').attr('class', 'ywz_zhuce_huixian');
		$('#pwdLevel_3').attr('class', 'ywz_zhuce_huixian');
	}

	function Weak() {
		$('#pwdLevel_1').attr('class', 'ywz_zhuce_hongxian');
		$('#pwdLevel_2').attr('class', 'ywz_zhuce_huixian');
		$('#pwdLevel_3').attr('class', 'ywz_zhuce_huixian');
	}

	function Medium() {
		$('#pwdLevel_1').attr('class', 'ywz_zhuce_hongxian');
		$('#pwdLevel_2').attr('class', 'ywz_zhuce_hongxian2');
		$('#pwdLevel_3').attr('class', 'ywz_zhuce_huixian');
	}

	function Tough() {
		$('#pwdLevel_1').attr('class', 'ywz_zhuce_hongxian');
		$('#pwdLevel_2').attr('class', 'ywz_zhuce_hongxian2');
		$('#pwdLevel_3').attr('class', 'ywz_zhuce_hongxian3');
	}
	function checkPassword(pwdinput) {
		var maths, smalls, bigs, corps, cat, num;
		var str = $(pwdinput).val()
		var len = str.length;

		var cat = /.{16}/g
		if (len == 0) return 1;
		if (len > 16) { $(pwdinput).val(str.match(cat)[0]); }
		cat = /.*[\u4e00-\u9fa5]+.*$/
		if (cat.test(str)) {
			return -1;
		}
		cat = /\d/;
		var maths = cat.test(str);
		cat = /[a-z]/;
		var smalls = cat.test(str);
		cat = /[A-Z]/;
		var bigs = cat.test(str);
		var corps = corpses(pwdinput);
		var num = maths + smalls + bigs + corps;

		if (len < 6) { return 1; }

		if (len >= 6 && len <= 8) {
			if (num == 1) return 1;
			if (num == 2 || num == 3) return 2;
			if (num == 4) return 3;
		}

		if (len > 8 && len <= 11) {
			if (num == 1) return 2;
			if (num == 2) return 3;
			if (num == 3) return 4;
			if (num == 4) return 5;
		}

		if (len > 11) {
			if (num == 1) return 3;
			if (num == 2) return 4;
			if (num > 2) return 5;
		}
	}
	function corpses(pwdinput) {
		var cat = /./g
		var str = $(pwdinput).val();
		var sz = str.match(cat)
		for (var i = 0; i < sz.length; i++) {
			cat = /\d/;
			maths_01 = cat.test(sz[i]);
			cat = /[a-z]/;
			smalls_01 = cat.test(sz[i]);
			cat = /[A-Z]/;
			bigs_01 = cat.test(sz[i]);
			if (!maths_01 && !smalls_01 && !bigs_01) { return true; }
		}
		return false;
	}
})

})


//获取验证码
	var countdown=120; 
function settime(obj) { 
    if (countdown == 0) { 
        obj.removeAttribute("disabled");
        obj.value="获取验证码"; 
        $(obj).css({'background':'#f2f2f2','color':'black'});
        countdown = 120; 
        return;
    } else { 
        obj.setAttribute("disabled", true);
        obj.value="重新发送(" + countdown + ")";
        $(obj).css({'background':'#8C8686','color':'#3C3838'});
        countdown--; 
    } 
setTimeout(function() { 
    settime(obj) }
    ,1000) 
}

function showprovince(){
	var option = "<option style='width:100px;background-color:black;' display='inline' value='0' >请选择省</option>";
	var province_op = "<option style='width:100px;background-color:black;' display='inline' value='0' >请选择市</option>";
	var city_op = "<option style='width:100px;background-color:black;' display='inline' value='0' >请选择区</option>";

	$.ajax({
		type:"post",
		url:data_url+"index.php/Users/User/showCity",
		data:"",
		dataType:"json",
		success:function(data){
			var address_list = data.business;
			for(var i=0;i<address_list.length;i++){
				option += "<option style='width:100px;background-color:black;' display='inline'   value="+address_list[i].region_id+">"+address_list[i].region_name+"</option>";
			}
			$("#country").html(option);
			$('#province').html(province_op);
			$('#city').html(city_op);
		}
	});
	
}





