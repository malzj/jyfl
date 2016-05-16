	
	function sub(){
		if($('#real_name').val()==''){
			alert('请输入姓名');
			return;
		}
		if($('#mobile').val()==''){
			alert('请输入手机号');
			return;
		}
		if($('#mobile').val()!=''){
			var regs = /^\d\d*$/;
			if (regs.test($('#mobile').val())==false) {
				alert('必须为正确的手机号');
				return;
			}
		}
		update_content();
		var num=cart.total;
		if (num=='' || parseInt(num)==0){
			alert('没有选择场地，无法预订。');
			return false;
		}
		$('#num').val(cart.total);
		$('#amount').val(cart.amount);

		var stime = [];
		$(".pk_dd2 span ").each(function (index, row){
			if($(row).attr("class")=='cursor2') {
				if ($.inArray($(row).attr("data-s"), stime) == -1) {
					stime.push($(row).attr("data-s"));
				}
			}
		});
		var d;
		var json=[];
		for(var i=0;i<stime.length;i++){
			var snum=0;
			var sprice=0;
			var amount=0;
			$(".pk_dd2 span").each(function (index, row){
				if($(row).attr("class")=='cursor2') {
					if ($(row).attr("data-s") == stime[i]) {
						snum++;
						sprice = $(row).attr("data-sale-price");
						amount += parseInt($(row).attr("data-sale-price"));
					}
				}
			});
			//alert(stime[i]+"-"+snum+"-"+sprice+"-"+amount);
			d=stime[i]+"-"+snum+"-"+sprice+"-"+amount;
			json.push(d);
		}
		$('#param').val(escape(json.join("|")));
		$('#f1').submit();
	}
	
	var oldhtml='无法预订该天的产品，可能是产品已经售完。';
	var address_null=true;
	$(function(){
		oldhtml=$('#prod_list').html();
		$(".pk_dd2 span").click(function (){
			var _class = $(this).attr("class");
			if (_class == "cursor")
			{
				return false;
			}

			var _price = $(this).attr("data-sale-price");
			if (_price<= 0)
			{
				return false;
			}
			if (_class == "cursor2")
			{
				$(this).removeClass("cursor2");
				var id = $(this).attr("data-id");
//                $(".selebg[data-id="+id+"]").remove();
				// 删除记录
				$("#_id_contianer li").each(function (index, row){
					if ($(row).attr("id") == id)
					{
						$(row).remove();
					}
				});
				$("#_id_contianer li[data-id="+id+"]").remove();
				update_content();
				$("#_id_total").html(cart.total);
				$("#_id_amount").html(cart.amount.toFixed(2));
				return false;
			}
			else
			{
				if (cart.total>9)
				{
					alert('抱歉，最多您只能选择10块场地');
					return false;
				}
				var id = $(this).attr('data-id');
				var date = $(this).parent().parent().attr("data-date");
				var price = $(this).attr('data-sale-price');
				var sclock= $(this).attr('data-s');
				var eclock= $(this).attr('data-e');
				var no = $(this).attr("data-no");
				var fee = $(this).attr("data-fee");
				var venue_no=$(this).parent().parent().parent().attr("venue-no");
				$(this).addClass("cursor2");
				var html=add_record(date, id, price, sclock,eclock, no,venue_no,fee);
				var shopOffset = $("#dhpos").offset();
				var cloneDiv = $(html).clone();
				var proOffset = $(this).offset();
				cloneDiv.css({ "position": "absolute", "top": proOffset.top, "left": proOffset.left });
				cloneDiv.css({"background":"#FFF","border":"1px solid #bee5f2", "line-height":"24px","width":"180px", "margin-bottom":"5px"," padding-left":"5px"," color":"#f00"});
				$('body').append(cloneDiv);
				cloneDiv.animate({width:180,height:26,left:shopOffset.left,top:shopOffset.top,opacity:1},800,function(){
					cloneDiv.remove();
					$("#_id_contianer").append(html);
				});
				update_content();
				$("#_id_total").html(cart.total);
				$("#_id_amount").html(cart.amount.toFixed(2));
			}
		});
		/* $(function(){
		 $('#cstdate').datepick();
		 }); */
	});
	// 更新购物车数量和金额
	function update_content()
	{
		var _amount = 0, _total = 0;
		$(".pk_dd2 span.cursor2").each(function (index, row){
			_amount += parseFloat($(row).attr("data-sale-price"));
			if($(row).attr("data-fee")>0){
				_amount += $(row).attr("data-sale-price")*$(row).attr("data-fee")/100;
			}
			_total ++;
		});
		cart.amount = _amount;
		cart.total = _total;
		count_num();
	}
	function count_num(){
		//统计剩余场地块数
		/*var span_total = $(".pk_dd2 span").size();
		var span_cursor = $(".pk_dd2 span.cursor").size();
		var span_cursor2 = $(".pk_dd2 span.cursor2").size();
		$(".weekday div.right").html("剩余"+(span_total - span_cursor - span_cursor2)+"块");*/
	}
	function add_record(date, id, price, sclock,eclock, no,venue_no,fee)
	{
		var _st = sclock;
		if (_st < 10)
		{
			_st = '0' + _st;
		}
		var s_clock = _st+':00';
		var _et = eclock;
		if (_et < 10)
		{
			_et = '0' + _et;
		}
		var e_clock = _et+':00';
		var html = '';
		html += ' <li data-id="'+id+'">';
		html += '<p>'+s_clock+'--'+e_clock+' ';
		html += ' '+venue_no+'号场地  '+price+'点</p>';
		if(fee>0){
			html+='<p>手续费'+(price*fee/100).toFixed(2)+'点</p>'
		}
		html += ' </li>';
		return html;
	}
	function del_record(obj, id)
	{
		$(".pk_dd2 span.cursor2 ").each(function (index, row){
			if ($(row).attr("data-id") == id)
			{
				$(row).removeClass("vencur").html("");
				$(obj).parents("div.selebg").remove();
				update_content();
				$("#_id_total").html(cart.total);
				$("#_id_amount").html(cart.amount.toFixed(2));
			}
		});
	}
	
//	点击添加动画
	function MoveBox(obj) {
		var divTop = $(obj).offset().top;
		var divLeft = $(obj).offset().left;
		$(obj).css({
			"position": "absolute",
			"z-index": "500",
			"left": divLeft + "px",
			"top": divTop + "px"
		});
		$(obj).animate({
					"left": ($("#_id_contianer").offset().left - $("#_id_contianer").width()) + "px",
					"top": ($(document).scrollTop() + 30) + "px",
					"width": "80px",
					"height": "30px"
				},
				500,
				function() {
					$(obj).animate({
						"left": $("#_id_contianer").offset().left + "px",
						"top": $("#_id_contianer").offset().top + "px",
						"width": "50px",
						"height": "25px"
					},500).fadeTo(0, 0.1).hide(0);
				});
	}