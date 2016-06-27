//设置为默认收货地址
function setDefaultConsignee(id){
	$.get('address.php', {act:'AjaxAddressDefault', address_id:id, t:Math.random()}, function(result){
		if(result.error>0){
			alert(result.content);
		}else{
			location.reload();
		}		
	}, 'json');
}
//设置选中的收货地址
function setConsigneeSelect(that,id,sid){
	if($(that).hasClass('select')){
		return true;
	}
	$.get('address.php', {act:'AjaxAddressSelect', address_id:id,sid:sid, t:Math.random()}, function(result){
		if(result.error>0){
			alert(result.content);
		}else{
//			$(that).closest('ul').find('.checkedall').removeClass('select').html('选择');
//			$(that).addClass('select').html('<font color=green>已选择</font>');  
			$(that).addClass('selected').find('.xuanzhong').addClass('selected').parents('li').siblings().removeClass('selected').find('.xuanzhong').removeClass('selected');
		}		
	}, 'json');
}

//选择收货人信息
function selectConsignee(id){
	$.get('address.php', {act:'AjaxAddressEdit', address_id:id, t:Math.random()}, function(result){
		$('#add_item').html((typeof result == "object") ? result.content : result).show();
	}, 'json');
}

//修改收货人信息
function updateConsignee(){
	$.get('address.php', {act:'AjaxAddressList', t:Math.random()}, function(result){
		$('#order_consignee').html((typeof result == "object") ? result.content : result);
	}, 'json');
}

//删除收货人信息
function dropConsignee(id, from){
	$.get('address.php', {act:'AjaxAddressDorp', address_id:id, t:Math.random()}, function(result){
		if(result.error){
			location.reload();			
		}else{
			alert('删除失败！');
		}
	
	}, 'json');
}

//保存收货人信息
function saveConsignee(id, from){
	
	from = from ? from : 'flow';
	var country    = $("#country").val();	// 市
	var province   = $("#province").val();	// 区
	var city       = $("#city").val(); 		// 街道
	//var district   = $("district").val();	
	var consignee  = $("#consignee").val();  // 名称
	var address    = $("#address").val();	// 地址
	var mobile     = $("#mobile").val();		// 电话
	var email     = $("#email").val();		// 电话
	
	var address_id = id;
	var partten    = /^1[3,5,8]\d{9}$/;
	var msg        = new Array();
	var err        = false;	
	
	//收货人不能为空
	if (Utils.isEmpty(consignee)){		
		alert('收货人不能为空');
		return false;
	}	
	
	//收货人联系方式不能为空
	if ( Utils.isEmpty(mobile)){
		alert('手机不能为空！');
		return false;
	}else{
		if (mobile.length > 0 && !partten.test(mobile)){
			alert('手机号不合法！');
			return false;
		}		
	}
	
	if (province && province == 0 && province.length > 1){		
		alert('请正确填写收货人所在地区');
		return false;
	}	

	//收货人详细地址不能为空
	if (Utils.isEmpty(address)){
		alert('收货人详情地址不能为空！');
		return false;
	}	
	
	$.ajax({
		url:'address.php?act=ajaxAddressSave',
		data:{
			consignee:consignee,
			country:country,
			province:province,
			city:city,
			address:address,
			mobile:mobile,			
			address_id:address_id,
			email:email,
			t:Math.random()
		},
		success:function(result){
			var data = jQuery.parseJSON(result);
			if(data.error > 0){	
				alert(data.content);
			}else{
				location.reload();
			}
		}
	});
}

//修改支付及配送方式
function updatePayShipping(check, type, from){
	type = type ? type : 0;
	from = from ? from : 'flow';
	$.get('flow.php', {step:'payshipping_ajax_update', check:check, type:type, from:from, t:Math.random()}, function(result){
		updatePayShippingResponse(result);
	}, 'json');
}

//保存支付及配送方式
function savePayShipping(from){
	from = from ? from : 'flow';

	frm = document.getElementById('theForm');
	var paymentSelected  = false;//初始化选中支付方式
	var paymentValue     = '';
	var shippingSelected = false;//初始化选中配送方式
	var shippingValue    = '';
	var besttimeSelected = false;//初始化最佳送货时间
	var besttimeValue    = '';

	//先判断是否保存收货人信息
	if(document.getElementById("consignee_check")){
		var consignee_check = document.getElementById("consignee_check").value;

		if(consignee_check != 1){
			alert('请保存收货人信息');
			$('html,body').animate({scrollTop:$('#order_consignee').offset().top},500);
			return false;
		}
	}

	// 检查是否选择了支付配送方式
	for (i = 0; i < frm.elements.length; i ++ ){
		if (frm.elements[i].name == 'shipping' && frm.elements[i].checked){
			shippingSelected = true;
			shippingValue = frm.elements[i].value;
		}

		if (frm.elements[i].name == 'payment' && frm.elements[i].checked){
			paymentSelected = true;
			paymentValue = frm.elements[i].value;
		}
	}

	if (!paymentSelected){
		alert(flow_no_payment);
		return false;
	}
	
	if (!shippingSelected){
		alert(flow_no_shipping);
		return false;
	}

	$.get('flow.php', {
		step:'payshipping_ajax_save',
		payment:paymentValue,
		shipping:shippingValue,
		from:from,
		t:Math.random()
	}, function(result){
		updatePayShippingResponse(result);
	}, 'json');
}

//支付及配送方式回调函数
function updatePayShippingResponse(result){
	if (result.type == 2){
		location.reload();
		$('#order_payshipping').html((typeof result == "object") ? result.content : result);
	}else{
		$('#order_payshipping').html((typeof result == "object") ? result.content : result);
	}
}

//改变支付方式
function selectPayment(obj){
	$.get('flow.php', {step:'select_payment', payment:obj.value, t:Math.random()}, function(result){
		orderSelectedResponse(result);
	}, 'json');
}

//改变配送方式
function selectShipping(obj){
	var supportCod = obj.attributes['supportCod'].value + 0;
	var theForm = obj.form;
	for (i = 0; i < theForm.elements.length; i ++ ){
		if (theForm.elements[i].name == 'payment' && theForm.elements[i].attributes['isCod'].value == '1'){
			if (supportCod == 0){
				theForm.elements[i].checked = false;
				theForm.elements[i].disabled = true;
			}else{
				theForm.elements[i].disabled = false;
			}
		}
	}
	
	//配送是否需要保价(不启用)
	/*if (obj.attributes['insure'].value + 0 == 0){
		document.getElementById('ECS_NEEDINSURE').checked = false;
		document.getElementById('ECS_NEEDINSURE').disabled = true;
	}else{
		document.getElementById('ECS_NEEDINSURE').checked = false;
		document.getElementById('ECS_NEEDINSURE').disabled = false;
	}*/

	$.get('flow.php', {step:'select_shipping', shipping:obj.value, t:Math.random()}, function(result){
		orderSelectedResponse(result);
	}, 'json');
}

//改变积分
function changeIntegral(val){
	$.get('flow.php', {step:'change_integral', points:val, t:Math.random()}, function(obj){
		if (obj.error){
			try{
				if (obj.error_sn == 1){
					$('html,body').animate({scrollTop:$('#order_consignee').offset().top},500);
				}
				document.getElementById('ECS_INTEGRAL_NOTICE').innerHTML = obj.error;
				document.getElementById('ECS_INTEGRAL').value = '0';
				document.getElementById('ECS_INTEGRAL').focus();
			}
			catch (ex) { }
		}else{
			try{
				document.getElementById('ECS_INTEGRAL_NOTICE').innerHTML = '';
			}
			catch (ex) { }
			orderSelectedResponse(obj.content);
		}
	}, 'json');
}

//改变红包
function changeBonus(val){
	if (document.getElementById('consignee_check').value == 0){
		alert('请先填写收货人信息');
		$('html,body').animate({scrollTop:$('#order_consignee').offset().top - 130},500);
		$('#ECS_BONUS').val(0);
		return false;
	}


	$.get('flow.php', {step:'change_bonus', bonus:val, t:Math.random()}, function(obj){
		if (obj.error){
			alert(obj.error);
			if (obj.error_sn == 1){
				$('html,body').animate({scrollTop:$('#order_consignee').offset().top},500);
			}
			try{
				document.getElementById('ECS_BONUS').value = '0';
			}catch (ex) { }
		}else{
			if (obj.bonus > 0){
				$('#supplier_bonus').hide();
			}else{
				$('#supplier_bonus').show();
			}
			orderSelectedResponse(obj.content);
		}
	}, 'json')
}

//改变供货商红包
function changeSupplierBonus(val, suppid){
	if (document.getElementById('consignee_check').value == 0){
		alert('请先填写收货人信息');
		$('html,body').animate({scrollTop:$('#order_consignee').offset().top - 130},500);
		$('#ECS_SUPPLIER_BONUS_'+suppid).val(0);
		return false;
	}

	$.get('flow.php', {step:'change_supplier_bonus', bonus:val, supplier:suppid, t:Math.random()}, function(obj){
		if (obj.error){
			alert(obj.error);
			if (obj.error_sn == 1){
				$('html,body').animate({scrollTop:$('#order_consignee').offset().top},500);
			}
			try{
				document.getElementById('ECS_SUPPLIER_BONUS_'+suppid).value = '0';
			}catch (ex) { }
		}else{
			if (obj.bonus > 0){
				$('#bonus').hide();
			}else{
				$('#bonus').show();
			}
			orderSelectedResponse(obj.content);
		}
	}, 'json')
}

//验证红包序列号
function validateBonus(bonusSn){
	if (document.getElementById('consignee_check').value == 0){
		alert('请先填写收货人信息');
		$('html,body').animate({scrollTop:$('#order_consignee').offset().top - 130},500);
		$('#bonus_sn').val('');
		return false;
	}

	var frm = document.forms['theForm'];
	var lqcheck = document.getElementById('ECS_BONUS') ? document.getElementById('ECS_BONUS').value : 0;
	var lpkcheck = 0;
	for (var i = 0; i < frm.elements.length; i++ ){
		if (frm.elements[i].name == 'bonuscard[]' && frm.elements[i].checked){
			lpkcheck = 1;
		}
	}

	$.get('flow.php', {step:'validate_bonus', bonus_sn:bonusSn, lqstatus:lqcheck, lqcheck:lpkcheck, t:Math.random()}, function(obj){
		var frm = document.forms['theForm'];
		if (obj.error){
			alert(obj.error);
			if (obj.error_sn == 1){
				$('html,body').animate({scrollTop:$('#order_consignee').offset().top},500);
			}
			orderSelectedResponse(obj.content);
			try{
				document.getElementById('ECS_BONUSN').value = '0';
			}catch (ex) { }
		}else{
			orderSelectedResponse(obj.content);
			if (obj.lqstatus){
				for (var i = 0; i < frm.elements.length; i++ ){
					if (frm.elements[i].name == 'bonuscard[]' && frm.elements[i].checked){
						frm.elements[i].checked = false;
					}
				}
				document.getElementById('bonus').style.display = 'block';
				document.getElementById('bonuscard').style.display = 'none';
			}
			if (obj.lpkstatus){
				var o = document.getElementById("ECS_BONUS").options;
				for(var i=0,n=o.length ;i<n;i++){
					if(0==o[i].value){
						o[i].selected=true;
					}
				}
				document.getElementById('ECS_BONUS').value = '0';
				document.getElementById('bonus').style.display = 'none';
				document.getElementById('bonuscard').style.display = 'block';
			}
		}
	
	}, 'json');
}

//改变发票的方式
function changeNeedInv(){
	var obj        = document.getElementById('ECS_NEEDINV');
	var objType    = document.getElementById('ECS_INVTYPE');
	var objPayee   = document.getElementById('ECS_INVPAYEE');
	var objStatus  = document.getElementById('ECS_INVSTATUS');
	var objContent = document.getElementById('ECS_INVCONTENT');
	var needInv    = obj.checked ? 1 : 0;
	var invType    = obj.checked ? (objType != undefined ? objType.value : '') : '';
	var invPayee   = obj.checked ? objPayee.value : '';
	var invContent = obj.checked ? objContent.value : '';
	var invStatus  = obj.checked ? objStatus.value : '';

	$.get('flow.php', {step:'change_needinv', need_inv:needInv, inv_type:encodeURIComponent(invType), inv_payee:encodeURIComponent(invPayee), inv_content:encodeURIComponent(invContent), t:Math.random()}, function (result){
		orderSelectedResponse(result);
	});
}

//改变发票的方式
function changeNeedInv(){
	var obj        = document.getElementById('ECS_NEEDINV');
	var objType    = document.getElementById('ECS_INVTYPE');
	var objPayee   = document.getElementById('ECS_INVPAYEE');
	var objContent = document.getElementById('ECS_INVCONTENT');
	var needInv    = obj.checked ? 1 : 0;
	var invType    = obj.checked ? (objType != undefined ? objType.value : '') : '';
	var invPayee   = obj.checked ? objPayee.value : '';
	var invContent = obj.checked ? objContent.value : '';

	$.get('flow.php', {step:'change_needinv', need_inv:needInv, inv_type:encodeURIComponent(invType), inv_payee:encodeURIComponent(invPayee), inv_content:encodeURIComponent(invContent), t:Math.random()}, function (result){
		orderSelectedResponse(result);
	});
}

//编辑发票信息
function updateInv(check){
	$.get('flow.php', {step:'inv_ajax_update', check:check, t:Math.random()}, function(result){
		$('#user_tax').html((typeof result == "object") ? result.content : result);
	}, 'json');
}

/* *
 * 添加常用发票
 */
function saveInv(id, from){
	from = from ? from : 'flow';

	var frm = document.forms['theForm'];
	var inv_type, inv_content, inv_payee, inv_tax;

	$.each( $('input:radio[name="inv_type"]'), function(k, v){
		if ($(v).attr('checked')){
			inv_type = $(v).val();
			inv_tax  = $(v).attr('tax');
		}
	});

	$.each( $('input:radio[name="inv_content"]'), function(k, v){
		if ($(v).attr('checked')){
			inv_content = $(v).val();
		}
	});
	
	inv_payee = $('#ECS_INVPAYEE').val();


	var msg = new Array();
	var err = false;

	if (Utils.isEmpty(inv_type)){
		err = true;
		msg.push('请选择发票类型');
	}
	
	if (Utils.isEmpty(inv_payee)){
		err = true;
		msg.push('请填写发票抬头');
	}

	if (Utils.isEmpty(inv_content)){
		err = true;
		msg.push('请选择发票内容');
	}

	if (err){
		message = msg.join("\n");
		alert(message);
	}else{
		$.get('flow.php', {step:'inv_ajax_save', inv_type:inv_type, inv_content:inv_content, inv_payee:inv_payee, inv_tax:inv_tax, inv_id:id, from:from, t:Math.random()}, function (result){
			if (result.error){
				alert(result.error);
			}else{
				var layer = document.getElementById("user_tax");
				layer.innerHTML = (typeof result == "object") ? result.inv : result;
				orderSelectedResponse(result);
			}
		}, 'json');
	}
}

//回调函数
function orderSelectedResponse(result){
	if (result.error){
		alert(result.error);
		if (result.error_sn == 1){
			$('html,body').animate({scrollTop:$('#order_consignee').offset().top},500);
		}else{
			location.href = './';
		}
	}

	try{
		var layer = document.getElementById("ECS_ORDERTOTAL");
		layer.innerHTML = (typeof result == "object") ? result.content : result;

		if (result.payment != undefined){
			var surplusObj = document.forms['theForm'].elements['surplus'];
			if (surplusObj != undefined){
				surplusObj.disabled = result.pay_code == 'balance';
			}
		}
	}catch (ex) { }
}

//检查提交的订单表单
function checkOrderForm(frm, from){
	var shippingMsg = '';
	$('.supplier-one').each(function(){
		if($(this).val() == ''){
			shippingMsg = '每个供应商需要设置一个收货地址，请设置！';
			return false;
		}
		
		if($(this).val() == '-1'){
			shippingMsg = '收货地址不支持配送，请返回修改！';
			return false;
		}	
	});
	
	if(shippingMsg != ''){
		alert(shippingMsg);
		return false;
	}
	
	var st = 0;
	$('.peisong_check').each(function(){	
		if($(this).val() == '配送日期' || $(this).val() == 0 || $(this).val() == ''){
			st = 1;
			$(this).css('border','1px solid red');
		}
	});
	if(st==1){
		alert('配送时间填写不完整！');		
		return false;
	}
}

//提交订单
$(document).ready(function(){
	$('input:radio[name="inv_status"]').click(function(){
		if ($(this).prop('checked') && $(this).val() == '个人'){
			$('#ECS_INVPAYEE').hide();
		}else{
			$('#ECS_INVPAYEE').show();
		}
	
	});
});

//切换状态
function ycxs(id){
	if ($("#"+id+'_').css('display') == 'none'){
		$('.'+id).find('.slideup').hide();
		$('.'+id).find('.slidedown').show();
		$("#"+id+'_').show();
	}else{
		$('.'+id).find('.slideup').show();
		$('.'+id).find('.slidedown').hide();
		$("#"+id+'_').hide();
	}
}

/* *
 * 检查收货地址信息表单中填写的内容
 */
function checkConsignee(frm){
	var msg = new Array();
	var err = false;

	if (Utils.isEmpty(frm.elements['consignee'].value)){
		err = true;
		msg.push(consignee_not_null);
	}
	
	if (frm.elements['country'] && frm.elements['country'].value == 0){
		msg.push(country_not_null);
		err = true;
	}
	if (frm.elements['province'] && frm.elements['province'].value == 0 && frm.elements['province'].length > 1){
		err = true;
		msg.push(province_not_null);
	}
	if (frm.elements['city'] && frm.elements['city'].value == 0 && frm.elements['city'].length > 1){
		err = true;
		msg.push(city_not_null);
	}

	if (frm.elements['district'] && frm.elements['district'].length > 1){
		if (frm.elements['district'].value == 0){
			err = true;
			msg.push(district_not_null);
		}
	}

	if (frm.elements['address'] && Utils.isEmpty(frm.elements['address'].value)){
		err = true;
		msg.push(address_not_null);
	}


	if (Utils.isEmpty(frm.elements['mobile'].value) && Utils.isEmpty(frm.elements['tel'].value)){
		err = true;
		msg.push('手机或电话至少填写一项');
	}

	if (frm.elements['mobile'] && frm.elements['mobile'].value.length > 0 && (!Utils.isMobile(frm.elements['mobile'].value))){
		err = true;
		msg.push(mobile_invaild);
	}

	if (frm.elements['tel'] && frm.elements['tel'].value.length > 0 && (!Utils.isTel(frm.elements['tel'].value))){
		err = true;
		msg.push(tel_invaild);
	}

	if (Utils.isEmpty(frm.elements['email'].value)){
		err = true;
		msg.push('邮箱不能为空');
	}
	
	if ( ! Utils.isEmail(frm.elements['email'].value)){
		err = true;
		msg.push(invalid_email);
	}

	if (err){
		message = msg.join("\n");
		alert(message);
	}
	return !err;
}