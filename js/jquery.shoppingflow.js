//选择收货人信息
function selectConsignee(id, from){
	$.get('flow.php', {step:'consignee_ajax_edit', address_id:id, from:from, t:Math.random()}, function(result){
		$('#order_consignee').html((typeof result == "object") ? result.content : result);
	}, 'json');
}

//修改收货人信息
function updateConsignee(check, from){
	from = from ? from : 'flow';
	$.get('flow.php', {step:'consignee_ajax_update', check:check, from:from, t:Math.random()}, function(result){
		$('#order_consignee').html((typeof result == "object") ? result.content : result);
	}, 'json');
}

//删除收货人信息
function dropConsignee(id, from){
	$.get('flow.php', {step:'consignee_ajax_drop', address_id:id, t:Math.random()}, function(result){
		try{
			if (from == 'user'){
				location.reload();
			}else{
				var x=document.getElementById("consi"+result.id);
				if(result.check){
					x.parentNode.removeChild(x);
				}else{
					alert('删除失败！');
				}
			}
		}catch (ex) {
		}
	}, 'json');
}

//保存收货人信息
function saveConsignee(id, from){
	from = from ? from : 'flow';
	var country    = document.getElementById("country");
	var province   = document.getElementById("province");
	var city       = document.getElementById("city");
	var district   = document.getElementById("district");
	var consignee  = document.getElementById("consignee");
	var address    = document.getElementById("address");
	var mobile     = document.getElementById("mobile");
	var tel        = document.getElementById("tel");
	//var email      = document.getElementById("email");
	var address_id = id;
	var partten    = /^1[3,5,8]\d{9}$/;
	var msg        = new Array();
	var err        = false;

	//收货人不能为空
	if (Utils.isEmpty(consignee.value)){
		err = true;
		msg.push(consignee_not_null);
	}
	
	//收货人所在地区不能为空
	if (country && country.value == 0){
		//msg.push(country_not_null);
		msg.push('请正确填写收货人所在地区');
		err = true;
	}
	if (province && province.value == 0 &&province.length > 1){
		err = true;
		//msg.push(province_not_null);
		msg.push('请正确填写收货人所在地区');
	}
	/*if (city && city.value == 0 && city.length > 1){
		err = true;
		//msg.push(city_not_null);
		msg.push('请正确填写收货人所在地区');
	}
	if (district && district.length > 1){
		if (district.value == 0){
			err = true;
			//msg.push(district_not_null);
			msg.push('请正确填写收货人所在地区');
		}
	}*/

	//收货人详细地址不能为空
	if (address && Utils.isEmpty(address.value)){
		err = true;
		msg.push(address_not_null);
	}
	
	//收货人联系方式不能为空
	if (Utils.isEmpty(tel.value) && Utils.isEmpty(mobile.value)){
		err = true;
		msg.push('手机、固话电话选填一项！');
	}else{
		if (mobile.value.length > 0 && !partten.test(mobile.value)){
			err = true;
			msg.push('手机号不合法！');
		}
		if (tel.value.length > 0 && !Utils.isTel(tel.value)){
			err = true;
			msg.push('固定电话不合法！');
		}
	}

	//收货人邮箱不能为空
	/*if (Utils.isEmpty(email.value)){
		err = true;
		msg.push('邮箱不能为空');
	}else if (!Utils.isEmail(email.value)){
		err = true;
		msg.push(invalid_email);
	}*/
	
	if (err){
		message = msg.join("\n");
		alert(message);
	}else{
		$.get('flow.php', {
			step:'consignee_ajax_save',
			consignee:consignee.value,
			country:country.value,
			province:province.value,
			//city:city.value,
			//district:district.value,
			address:address.value,
			mobile:mobile.value,
			tel:tel.value,
			email:'',
			address_id:address_id,
			from:from,
			t:Math.random()
		}, function(result){
			try{
				if(result.error == 0){
					//updateConsignee(1, from);
					//updatePayShipping(0, 1, from);
					location.reload();
					if (from == 'dealers'){
						setTonggxx(email.value);
					}
				}else{
					alert('保存失败！');
				}
			}catch (ex) {
			
			}
		}, 'json');
	}
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
	if($('#load-status').is(':visible') == true){
		return false;
	}
	if (document.getElementById('consignee_check').value == 0){
		alert('请填写收货人信息');
		$('html,body').animate({scrollTop:$('#order_consignee').offset().top - 130},500);
		return false;
	}

	var st = 0;
	$('.st0').each(function(){		
		if($(this).val() == '' || $(this).val() == 0){
			st = 1;
		}
	});
	if(st==1){
		alert('配送时间填写不完整！，请填写完整后在提交！');
		return false;
	}
	/*if(document.getElementById("21cake")){
			var consignee_check = document.getElementById("21cake").value;
			if(consignee_check == 1){
				if(document.getElementById("riqi12")){
				var consignee_check1 = document.getElementById("riqi12").value;
					if(consignee_check1 == ''){
						alert('请填写送货时间');
						$('html,body').animate({scrollTop:$('#order_payshipping').offset().top - 130},500);
						return false;
					}
			    }

	  }

	}

	if(document.getElementById("21cake")){
			var consignee_check = document.getElementById("21cake").value;
			if(consignee_check == 1){
				if(document.getElementById("time12")){
				var consignee_check1 = document.getElementById("time12").value;
					if(consignee_check1 == ''){
						alert('请填写送货时间');
						$('html,body').animate({scrollTop:$('#order_payshipping').offset().top - 130},500);
						return false;
					}
			    }

	  }

	}*/
  

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