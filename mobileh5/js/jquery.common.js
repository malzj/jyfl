/* *
 * 添加商品到购物车 
 */
function addToCart(goodsId, parentId, carttype){
	var goods        = new Object();
	var spec_arr     = new Array();
	var fittings_arr = new Array();
	var number       = 1;
	var formBuy      = document.forms['ECS_FORMBUY'];
	var quick		   = 0;

	// 检查是否有商品规格 
	if (formBuy){
		spec_arr = getSelectedAttributes(formBuy);

		if (formBuy.elements['number']){
			number = formBuy.elements['number'].value;
		}
		quick = 1;
	}

	goods.quick    = quick;
	goods.spec     = spec_arr;
	goods.goods_id = goodsId;
	goods.number   = number;
	goods.parent   = (typeof(parentId) == "undefined") ? 0 : parseInt(parentId);

	goods.carttype = (typeof(carttype) == "undefined") ? 0 : parseInt(carttype);
	jQuery.ajaxJsonp(web_url+"/mobile/flow.php",{step:"add_to_cart", goods:JSON.stringify(goods), t:Math.random()},function(data){
		addToCartResponse(data);
	});
}

/**
 * 获得选定的商品属性
 */
function getSelectedAttributes(formBuy){
	var spec_arr = new Array();
	var j = 0;

	for (i = 0; i < formBuy.elements.length; i ++ ){
		var prefix = formBuy.elements[i].name.substr(0, 5);

		if (prefix == 'spec_' && (((formBuy.elements[i].type == 'radio' || formBuy.elements[i].type == 'checkbox') && formBuy.elements[i].checked) || formBuy.elements[i].tagName == 'SELECT')){
			spec_arr[j] = formBuy.elements[i].value;
			j++ ;
		}
	}
	return spec_arr;
}

/* *
 * 处理添加商品到购物车的反馈信息
 */
function addToCartResponse(result){
	if (result.error > 0){
		if (result.error == 2){			
			mui.toast('对不起，该商品库存不足');
		}		
		else{
			mui.toast(result.message);
		}
	}
	else{
		var cart_url = 'flow.php?step=checkout&flowtype=5';
		
		if (result.carttype == '5'){
			location.href = cart_url;
		}else{
			mui.toast('加入购物车成功');
		}
		
		
	}
}

/* *
 * 评论的翻页函数
 */
function gotoPage(page, id, type){
	jQuery.get("comment.php", {act:"gotopage", id:id, page:page, type:type, t:Math.random()}, function(data){
		data = jQuery.parseJSON(data);
		gotoPageResponse(data);
	});
}

function gotoPageResponse(result){
	document.getElementById("ECS_COMMENT").innerHTML = result.content;
}

//收藏
function collect(goodsId){
	jQuery.get("user.php", {act:"collect", id:goodsId, t:Math.random()}, function(data){
		data = jQuery.parseJSON(data);
		collectResponse(data);
	});
}

function collectResponse(result){
	alert(result.message);
	if (result.error == 0){
		location.reload();
	}
}

//喜欢
function follow(goodsId){
	jQuery.get("user.php", {act:"follow", id:goodsId, t:Math.random()}, function(data){
		data = jQuery.parseJSON(data);
		followResponse(data);
	});
}

function followResponse(result){
	alert(result.message);
	if (result.error == 0){
		location.reload();
	}
}


/* 以下四个函数为属性选择弹出框的功能函数部分 */
//检测层是否已经存在
function docEle() {
	return document.getElementById(arguments[0]) || false;
}

//生成属性选择层
function openSpeDiv(message, goods_id, parent, carttype) {
	var _id = "speDiv";
	var m = "mask";
	if (docEle(_id)) document.removeChild(docEle(_id));
	if (docEle(m)) document.removeChild(docEle(m));
	//计算上卷元素值
	var scrollPos; 
	if (typeof window.pageYOffset != 'undefined'){
		scrollPos = window.pageYOffset; 
	}else if (typeof document.compatMode != 'undefined' && document.compatMode != 'BackCompat'){
		scrollPos = document.documentElement.scrollTop; 
	}else if (typeof document.body != 'undefined'){
		scrollPos = document.body.scrollTop; 
	}

	var i = 0;
	var sel_obj = document.getElementsByTagName('select');
	while (sel_obj[i]){
		sel_obj[i].style.visibility = "hidden";
		i++;
	}

	// 新激活图层
	var newDiv = document.createElement("div");
	newDiv.id = _id;
	newDiv.style.position = "absolute";
	newDiv.style.zIndex = "10000";
	newDiv.style.width = "300px";
	newDiv.style.height = "260px";
	newDiv.style.top = (parseInt(scrollPos + 200)) + "px";
	newDiv.style.left = (parseInt(document.body.offsetWidth) - 200) / 2 + "px"; // 屏幕居中
	newDiv.style.overflow = "auto"; 
	newDiv.style.background = "#FFF";
	newDiv.style.border = "3px solid #ad968b";
	newDiv.style.padding = "5px";

	//生成层内内容
	newDiv.innerHTML = '<h4 style="font-size:14; margin:15 0 0 15;">' + select_spe + "</h4>";

	for (var spec = 0; spec < message.length; spec++){
		newDiv.innerHTML += '<hr style="color: #EBEBED; height:1px;"><h6 style="text-align:left; background:#ffffff; margin-left:15px;">' +  message[spec]['name'] + '</h6>';

		if (message[spec]['attr_type'] == 1){
			for (var val_arr = 0; val_arr < message[spec]['values'].length; val_arr++){
				if (val_arr == 0){
					newDiv.innerHTML += "<input style='margin-left:15px;' type='radio' name='spec_" + message[spec]['attr_id'] + "' value='" + message[spec]['values'][val_arr]['id'] + "' id='spec_value_" + message[spec]['values'][val_arr]['id'] + "' checked /><font color=#555555>" + message[spec]['values'][val_arr]['label'] + '</font> [' + message[spec]['values'][val_arr]['format_price'] + ']</font><br />';
				}else{
					newDiv.innerHTML += "<input style='margin-left:15px;' type='radio' name='spec_" + message[spec]['attr_id'] + "' value='" + message[spec]['values'][val_arr]['id'] + "' id='spec_value_" + message[spec]['values'][val_arr]['id'] + "' /><font color=#555555>" + message[spec]['values'][val_arr]['label'] + '</font> [' + message[spec]['values'][val_arr]['format_price'] + ']</font><br />';
				}
			}
			newDiv.innerHTML += "<input type='hidden' name='spec_list' value='" + val_arr + "' />";
		}else{
			for (var val_arr = 0; val_arr < message[spec]['values'].length; val_arr++){
				newDiv.innerHTML += "<input style='margin-left:15px;' type='checkbox' name='spec_" + message[spec]['attr_id'] + "' value='" + message[spec]['values'][val_arr]['id'] + "' id='spec_value_" + message[spec]['values'][val_arr]['id'] + "' /><font color=#555555>" + message[spec]['values'][val_arr]['label'] + ' [' + message[spec]['values'][val_arr]['format_price'] + ']</font><br />';
			}
			newDiv.innerHTML += "<input type='hidden' name='spec_list' value='" + val_arr + "' />";
		}
	}
	newDiv.innerHTML += "<br /><center>[<a href='javascript:submit_div(" + goods_id + "," + parent + ", " + carttype + ")' class='f6' >" + btn_buy + "</a>]&nbsp;&nbsp;[<a href='javascript:cancel_div()' class='f6' >" + is_cancel + "</a>]</center>";
	document.body.appendChild(newDiv);

	// mask图层
	var newMask = document.createElement("div");
	newMask.id = m;
	newMask.style.position = "absolute";
	newMask.style.zIndex = "9999";
	newMask.style.width = document.body.scrollWidth + "px";
	newMask.style.height = document.body.scrollHeight + "px";
	newMask.style.top = "0px";
	newMask.style.left = "0px";
	newMask.style.background = "#ddd";
	newMask.style.filter = "alpha(opacity=30)";
	newMask.style.opacity = "0.40";
	document.body.appendChild(newMask);
}

//获取选择属性后，再次提交到购物车
function submit_div(goods_id, parentId, carttype) {
	var goods        = new Object();
	var spec_arr     = new Array();
	var fittings_arr = new Array();
	var number       = 1;
	var input_arr      = document.getElementsByTagName('input'); 
	var quick		   = 1;

	var spec_arr = new Array();
	var j = 0;

	for (i = 0; i < input_arr.length; i ++ ){
		var prefix = input_arr[i].name.substr(0, 5);
		if (prefix == 'spec_' && (((input_arr[i].type == 'radio' || input_arr[i].type == 'checkbox') && input_arr[i].checked))){
			spec_arr[j] = input_arr[i].value;
			j++ ;
		}
	}

	goods.quick    = quick;
	goods.spec     = spec_arr;
	goods.goods_id = goods_id;
	goods.number   = number;
	goods.parent   = (typeof(parentId) == "undefined") ? 0 : parseInt(parentId);
	goods.carttype = (typeof(carttype) == "undefined") ? 0 : parseInt(carttype);

	jQuery.post("flow.php", {step:"add_to_cart", goods:JSON.stringify(goods)}, function(data){
		//data = jQuery.parseJSON(data);
		addToCartResponse(data);
	}, 'json');

	document.body.removeChild(docEle('speDiv'));
	document.body.removeChild(docEle('mask'));

	var i = 0;
	var sel_obj = document.getElementsByTagName('select');
	while (sel_obj[i]){
		sel_obj[i].style.visibility = "";
		i++;
	}
}


//生成添加购物车成功图层
function openCartDiv(message) {
	var _id = "speDiv";
	var m = "mask";
	if (docEle(_id)) document.removeChild(docEle(_id));
	if (docEle(m)) document.removeChild(docEle(m));
	//计算上卷元素值
	var scrollPos; 
	if (typeof window.pageYOffset != 'undefined'){
		scrollPos = window.pageYOffset; 
	}else if (typeof document.compatMode != 'undefined' && document.compatMode != 'BackCompat'){
		scrollPos = document.documentElement.scrollTop; 
	}else if (typeof document.body != 'undefined'){
		scrollPos = document.body.scrollTop; 
	}

	var i = 0;
	var sel_obj = document.getElementsByTagName('select');
	while (sel_obj[i]){
		sel_obj[i].style.visibility = "hidden";
		i++;
	}

	// 新激活图层
	var newDiv = document.createElement("div");
	newDiv.id = _id;
	newDiv.className = 'hy_tk';
	newDiv.style.position = "absolute";
	newDiv.style.zIndex = "10000";
	newDiv.style.top = (parseInt(scrollPos + 200)) + "px";
	newDiv.style.left = (parseInt(document.body.offsetWidth)-250) / 2 + "px"; // 屏幕居中
	newDiv.style.overflow = "auto"; 


	//生成层内内容
	newDiv.innerHTML = '<p class="hy_tk_gb"><a href="javascript:cancel_div()"><img src="images/hy_guanbi.jpg" width="12" height="11" alt="" /></a></p>';
	newDiv.innerHTML += '<p class="hy_add_cg">添加成功。</p>';
	newDiv.innerHTML += '<p class="hy_add_jr_box"><input type="button" onclick="cancel_div();" value="继续购物" class="hy_tk_jr" /><a href="flow.php" class="hy_tk_js" >去购物车结算</a></p>';
	newDiv.innerHTML += '<div class="clear"></div>';
	
	document.body.appendChild(newDiv);

	// mask图层
	var newMask = document.createElement("div");
	newMask.id = m;
	newMask.style.position = "absolute";
	newMask.style.zIndex = "9999";
	newMask.style.width = document.body.scrollWidth + "px";
	newMask.style.height = document.body.scrollHeight + "px";
	newMask.style.top = "0px";
	newMask.style.left = "0px";
	newMask.style.background = "#111";
	newMask.style.filter = "alpha(opacity=30)";
	newMask.style.opacity = "0.40";
	document.body.appendChild(newMask);
}

// 关闭mask和新图层
function cancel_div() {
	document.body.removeChild(docEle('speDiv'));
	document.body.removeChild(docEle('mask'));

	var i = 0;
	var sel_obj = document.getElementsByTagName('select');
	while (sel_obj[i]){
		sel_obj[i].style.visibility = "";
		i++;
	}
}

function selectAll(name, obj){
	$('input[name="'+name+'"]').attr('checked', obj.checked);
}