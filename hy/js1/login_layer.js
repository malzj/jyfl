
$(function(){
//$('#per').on('click',function(){
//	 var user_id  = $('#user_id').val();
//			var data =userShow(user_id);
//      console.log(a);
//  layer.open({
//      type: 1,
//      title:false,
//      area:'570px',
//      shadeClose:false, //点击遮罩关闭
//     
//      content:'<div class="per"><h3>个人信息</h3><div class="wrap"><div class="left"><div class="form-group">卡号：<span class="card_num" id="card_id">9999999999999999999</span></div><div class="form-group"><label>用户名：</label><span class="username">dfsdafsfdf</span></div><div class="form-group"><label>性别：</label><span class="radio_img on radio_left"><input id="male" type="radio" checked name="Sex" value="male"></span>男 <span class="radio_img"><input id="female" type="radio" name="Sex" value="female"></span>女</div><div class="form-group" style="position:relative">生日： <input onclick="laydate()" class="birth" id="birth"><label class="date_ico laydate-icon" for="birth"></label></div><div class="form-group">个人情况： <span class="radio_img"><input type="radio" checked name="per"></span>单身 <span class="radio_img"><input type="radio" name="per"></span>恋爱中 <span class="radio_img"><input type="radio" name="per"></span>已婚 <span class="radio_img"><input type="radio" name="per"></span>保密</div><form action="" method="get">兴趣： <span class="checkbox_img radio_left"><input name="favorite" type="checkbox" value=""></span>美食 <span class="checkbox_img"><input name="favorite" type="checkbox" value=""></span>电影 <span class="checkbox_img"><input name="favorite" type="checkbox" value=""></span>酒店 <span class="checkbox_img"><input name="favorite" type="checkbox" value=""></span>休闲娱乐 <span class="checkbox_img"><input name="favorite" type="checkbox" value=""></span>丽人 <span class="checkbox_img"><input name="favorite" type="checkbox" value=""></span>旅游</form></div><div class="right"><div>亲爱的<span>哈哈哈</span>，来上传一张头像吧</div><div class="right_img" id="preview"><img id="imghead" src="images/img_login/touxiang_shang.png"></div><span class="file"><input type="file" name="uploadFile" onchange="previewImage(this)"></span></div></div><div style="text-align:center"><button>保存</button></div></div>'
//  });
//});
$('#saf').on('click',function(){
    layer.open({
        type: 1,
        title:false,
        area:'570px',
        shadeClose: false, //点击遮罩关闭
        content:'<div class="saf"><h3>安全中心</h3><table class="table"><tr class="tr_1"><td><span></span>登录密码</td><td class="td_2">未设置</td><td class="td_3">设置登陆密码，降低盗号风险；</td><td class="td_4">立即设置</td></tr><tr class="tr_2"><td><span></span>手机号</td><td class="td_2">未设置</td><td class="td_3">绑定手机，可直接使用手机号登陆；</td><td class="td_4">立即设置</td></tr><tr class="tr_3"><td><span></span>安全问题</td><td class="td_2">未设置</td><td class="td_3">保护账户安全，验证您身份的工具之一；</td><td class="td_4">立即设置</td></tr></table></div>'
    });
    var user_id=$('#user_id');
    var data=userShow(user_id);
    console.log(data+"66666666666666666666666666");
});
$('#shouhuo').on('click',function(){
    layer.open({
        type: 1,
        title:false,
        area: '570px',
        shadeClose: false, //点击遮罩关闭
        content:'<div class="shouhuo"><h3>收货信息</h3><div class="table-responsive"><table class="table"><thead><tr><td>收件人</td><td>地址/邮编</td><td>电话/手机</td><td>操作</td></tr></thead><tbody><tr><td class="td_1">王某某</td><td>北京市被北京北京南方国际化的减肥的复古风发生的防守打法萨法似懂非懂是发顺丰说的</td><td class="td_3">13820906181</td><td class="gn_btn"><span>删除</span><span class="xiugai">修改</span></td></tr></tbody></table></div><div class="add_new">添加新地址</div></div>'
    });
});
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
$(document).delegate('.add_new', 'click', function(){
	$('.layui-layer-content').html('<div class="set_add"><h3>添加地址</h3><div class="form-group"><label><span class="xing">*</span>所在地区：</label><input type="text" id="city"></div><div class="form-group"><label><span class="xing">*</span>街道地址：</label><input type="text"></div><div class="form-group"><label><span class="xing">*</span>邮政编码：</label><input type="text"></div><div class="form-group"><label><span class="xing">*</span>收货人姓名：</label><input type="text"></div><div class="form-group"><label><span class="xing">*</span>电话号码：</label><input type="text"></div><div class="set_add_btn"><button class="yes">保存</button><button class="no">取消</button></div></div>');
});
$(document).delegate('.btn_all .btn_2','click',function(){
	$('.layui-layer-content').html('<div class="log"><h3>充值记录</h3><table class="table"><thead><tr><td>操作时间</td><td>类型</td><td>金额</td><td>会员备注</td><td>管理员备注</td><td>状态</td><td>操作</td></tr></thead><tbody><tr></tr></tbody></table></div>');
});
$(document).delegate('.tr_1 .td_4','click',function(){
	$('.layui-layer-content').html('<div class="set"><h3>设置密码</h3><div class="form-group ps"><form name="form1" method="post" action="" id="form1" style="overflow:hidden"><div class="ywz_zhucexiaobo"><div class="ywz_zhuce_youjian">密码：</div><div class="ywz_zhuce_xiaoxiaobao"><div class="ywz_zhuce_kuangzi"><input name="tbPassword" type="password" id="tbPassword" class="ywz_zhuce_kuangwenzi1"></div><div class="ywz_zhuce_huixian" id="pwdLevel_1"></div><div class="ywz_zhuce_huixian" id="pwdLevel_2"></div><div class="ywz_zhuce_huixian" id="pwdLevel_3"></div><span class="ywz_zhuce_hongxianwenzi">弱</span> <span class="ywz_zhuce_hongxianwenzi">中</span> <span class="ywz_zhuce_hongxianwenzi">强</span></div></div></form></div><div class="form-group ps"><label style="margin-left:-20px">确认密码：</label><span class="password"><input style="width:200px" type="password"></span></div><div class="set_btn"><button>确定</button><button>取消</button></div></div>')
});
$(document).delegate('.tr_2 .td_4','click',function(){
	$('.layui-layer-content').html('<div class="tel"><h3>绑定手机号</h3><div class="form-group"><label>手机号：</label><input type="text"><div class="ach"><span>获取动态码</span></div></div><div class="form-group"><label>动态码：</label><input type="text"></div><div class="btn_tel"><button class="btn_bound">绑定</button> <button class="btn_cancel">取消</button></div></div>');
});
$(document).delegate('.tr_3 .td_4','click',function(){
	$('.layui-layer-content').html('<div class="question"><h3>绑定手机号</h3><div class="form-group"><div><label>问题一：</label><select><option value="">你第一次坐飞机去哪里</option></select></div><div><label>答案：</label><input type="text"></div></div><div class="form-group"><div><label>问题一：</label><select><option value="">你第一次坐飞机去哪里</option></select></div><div><label>答案：</label><input type="text"></div></div><div class="form-group"><div><label>问题一：</label><select><option value="">你第一次坐飞机去哪里</option></select></div><div><label>答案：</label><input type="text"></div></div><div class="btn_question"><button class="yes">确认提交</button> <button class="no">返回安全中心</button></div></div>');
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
				$('.layui-layer-content').html('<div class="saf"><h3>安全中心</h3><table class="table"><tr class="tr_1"><td><span></span>登录密码</td><td class="td_2">未设置</td><td class="td_3">设置登陆密码，降低盗号风险；</td><td class="td_4">立即设置</td></tr><tr class="tr_2"><td><span></span>手机号</td><td class="td_2">未设置</td><td class="td_3">绑定手机，可直接使用手机号登陆；</td><td class="td_4">立即设置</td></tr><tr class="tr_3"><td><span></span>安全问题</td><td class="td_2">未设置</td><td class="td_3">保护账户安全，验证您身份的工具之一；</td><td class="td_4">立即设置</td></tr></table></div>')
				})
$(document).delegate('.btn_tel .btn_cancel','click',function(){
				$('.layui-layer-content').html('<div class="saf"><h3>安全中心</h3><table class="table"><tr class="tr_1"><td><span></span>登录密码</td><td class="td_2">未设置</td><td class="td_3">设置登陆密码，降低盗号风险；</td><td class="td_4">立即设置</td></tr><tr class="tr_2"><td><span></span>手机号</td><td class="td_2">未设置</td><td class="td_3">绑定手机，可直接使用手机号登陆；</td><td class="td_4">立即设置</td></tr><tr class="tr_3"><td><span></span>安全问题</td><td class="td_2">未设置</td><td class="td_3">保护账户安全，验证您身份的工具之一；</td><td class="td_4">立即设置</td></tr></table></div>')
				})
//返回安全中心
$(document).delegate('.btn_question .no','click',function(){
				$('.layui-layer-content').html('<div class="saf"><h3>安全中心</h3><table class="table"><tr class="tr_1"><td><span></span>登录密码</td><td class="td_2">未设置</td><td class="td_3">设置登陆密码，降低盗号风险；</td><td class="td_4">立即设置</td></tr><tr class="tr_2"><td><span></span>手机号</td><td class="td_2">未设置</td><td class="td_3">绑定手机，可直接使用手机号登陆；</td><td class="td_4">立即设置</td></tr><tr class="tr_3"><td><span></span>安全问题</td><td class="td_2">未设置</td><td class="td_3">保护账户安全，验证您身份的工具之一；</td><td class="td_4">立即设置</td></tr></table></div>')
				})
//添加地址取消
$(document).on('click','.set_add_btn .no',function(){
				$('.layui-layer-content').html('<div class="shouhuo"><h3>收货信息</h3><div class="table-responsive"><table class="table"><thead><tr><td>收件人</td><td>地址/邮编</td><td>电话/手机</td><td>操作</td></tr></thead><tbody><tr><td>王某某</td><td>北京市被北京北京南方国际化的减肥</td><td>13820906181</td><td class="gn_btn"><span>删除</span><span class="xiugai">修改</span></td></tr></tbody></table></div><div class="add_new">添加新地址</div></div>')
				})
$(document).delegate('.gn_btn .xiugai','click',function(){
				$('.layui-layer-content').html('<div class="set_add"><h3>添加地址</h3><div class="form-group"><label><span class="xing">*</span>所在地区：</label><input type="text" id="city"></div><div class="form-group"><label><span class="xing">*</span>街道地址：</label><input type="text"></div><div class="form-group"><label><span class="xing">*</span>邮政编码：</label><input type="text"></div><div class="form-group"><label><span class="xing">*</span>收货人姓名：</label><input type="text"></div><div class="form-group"><label><span class="xing">*</span>电话号码：</label><input type="text"></div><div class="set_add_btn"><button class="yes">保存</button><button class="no">取消</button></div></div>')
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