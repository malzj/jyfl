	// 更新购物车数量和金额
	function update_content()
	{
		var _amount = 0, _total = 0;
		$(".vv_sel.cur").each(function (index, row){
			_amount += parseFloat($(row).attr("data-sale-price"));
			if($(row).attr("data-fee")>0){
				_amount += $(row).attr("data-sale-price")*$(row).attr("data-fee")/100;
			}
			_total ++;
		});
		cart.amount = _amount;
		cart.total = _total;
		// count_num();
	}
	function count_num(){
		//统计剩余场地块数
		/*var span_total = $(".pk_dd2 span").size();
		var span_cursor = $(".pk_dd2 span.cursor").size();
		var span_cursor2 = $(".pk_dd2 span.cursor2").size();
		$(".weekday div.right").html("剩余"+(span_total - span_cursor - span_cursor2)+"块");*/
	}