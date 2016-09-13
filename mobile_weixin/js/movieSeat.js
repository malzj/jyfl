/**
 * 需预先加载jQuery ，mui
 */
var allwidth = $('.seatmap').width();
var seatwidth = $('.seatmap2').width();
if(seatwidth < allwidth){
    $('.seatmap2').css('width','100%');
}
function changeSeatImg(obj){

    var _columnno = Number($(obj).attr('columnno'));//座
    var _seatno = $(obj).attr('seatno'); // 座位号
    var _prev_seatno = $(obj).prev('a').attr('seatno');		// 上一个座位号
    var _prev_prev_seatno = $(obj).prev('a').prev('a').attr('seatno');	// 上上一个座位号
    var _next_seatno = $(obj).next('a').attr('seatno');		// 下一个座位号
    var _next_next_seatno = $(obj).next('a').next('a').attr('seatno');	// 下下一个座位号

    // 删除座位
    if($(obj).hasClass('select')){
        if(($('#'+_prev_seatno).hasClass('select') && !$('#'+_next_seatno).hasClass('select'))|| 		// 左边选中右边没选中
            (!$('#'+_prev_seatno).hasClass('select') && $('#'+_next_seatno).hasClass('select'))||  		// 右边选中左边没选中
            (!$('#'+_prev_seatno).hasClass('select') && !$('#'+_next_seatno).hasClass('select'))||		// 左右都没有
            (_prev_seatno == undefined && $('#'+_next_seatno).hasClass('select'))||
            (!$('#'+_prev_seatno).hasClass('select') && _next_seatno == undefined)
        ){
            $(obj).removeClass('select').addClass('active');
        }else{
            mui.alert('亲，不要留下单个座位！');
            return false;
        }

        var conples = undefined;

        // 情侣座删除
        if($(obj).hasClass('couples')){
            var prev_td = $(obj).prev('a');
            var next_td = $(obj).next('a');

            if(prev_td.hasClass('couples')){
                prev_td.removeClass("couples").addClass('love');
                $(obj).removeClass("couples").addClass('love');
                conples = prev_td.attr('id');
            }else{
                next_td.removeClass("couples");
                next_td.removeClass("couples").addClass('love');
                $(obj).removeClass("couples").addClass('love');
                conples = next_td.attr('id');
            }
        }

        // 删除选中的座位
        if (!$(obj).hasClass("select")) {
            $(".disabled-seat span").each(function() {
                if ($(this).attr("id").substr(5) == $(obj).attr("id") || $(this).attr("id").substr(5) == conples ) {
                    $(this).remove();
                }
            });
        }
        // 如果选中的座位我空，替换默认内容
        if($(".disabled-seat span").length == 0){
            $('.disabled-seat').html('<center>请在下方座位图选择您满意的座位</center>');
        }

        // 添加座位
    }else{
        if($(".disabled-seat span").length == 0){
            $('.disabled-seat').empty();
        }
        if ($(".disabled-seat span").length >= 4) {
            mui.alert("每笔订单最多可购4个座位");
            return false;
        }else{
            var _selected  = new Array();
            var _locked    = new Array();

            $.each($(obj).closest('.seat-row').find('a'), function (k, v){
                if ($(this).hasClass('select')){//自己选择的座位
                    _selected.push($(this).attr('columnno'));
                }
                if ($(this).hasClass('disabled')){//系统已售出或维修的座位号
                    _locked.push($(this).attr('columnno'));
                }
            });

            var is_selected = _selected.length > 0 ? false : true;
            for (var i=0; i<_selected.length; i++){
                if (_columnno + 1 == _selected[i] || _columnno - 1 == _selected[i]){
                    is_selected = true;
                }
            }
            if (!is_selected && _selected.length > 0){
                for (var i=0; i<_locked.length; i++){
                    if (_columnno + 1 == _locked[i] || _columnno - 1 == _locked[i]){
                        is_selected = true;
                    }
                }
            }


            if (!is_selected){
                mui.alert('亲，不要留下单个座位！');
                return false;
            }
            // 情侣座位处理
            if($(obj).hasClass('couples')){
                var prev_td = $(obj).prev('a');
                var next_td = $(obj).next('a');

                if(prev_td.hasClass('couples')){
                    $(obj).addClass("select");
                    prev_td.addClass("select");

                    var _span_html = '<span class="select-seat" id="span_'+prev_td.attr("seatno")+'" locno="'+prev_td.attr('locno')+'">'+prev_td.attr("title")+'</span>';
                    $(".disabled-seat").append(_span_html);
                }else{
                    $(obj).addClass("select");
                    next_td.addClass("select");

                    var _span_html = '<span class="hy2_weizhi" id="span_'+next_td.attr("seatno")+'" locno="'+next_td.attr('locno')+'">'+next_td.attr("title")+'</span>';
                    $(".disabled-seat").append(_span_html);
                }
            }else{
                $(obj).addClass("select");
            }
        }
    }

    if ($(obj).hasClass('select')){
        var _span_html = '<span class="select-seat" id="span_'+$(obj).attr("seatno")+'" locno="'+$(obj).attr('locno')+'">'+$(obj).attr("title")+'</span>';
        $(".disabled-seat").append(_span_html);
    }
    //$('#price').html('<span class="hy2_yingyuan">合计：</span><span class="f14" id="total_praice">'+$("#vipPrice").val() + '点 * ' + $("#seatSelectedList span").length + '</span>');
    $('.init-price').html(parseFloat($(".disabled-seat span").length * $("#vipPrice").val()).toFixed(2));
    var selectedVal = "";
    var selectedName = '';

    $(".disabled-seat span").each(function() {
        if (selectedVal == "") {
            selectedVal = $(this).attr("id").substr(5);
            selectedName = $(this).text();
        } else {
            selectedVal = selectedVal + "|" + $(this).attr("id").substr(5);
            selectedName = selectedName + '|' + $(this).text();
        }
    });
    $('#seatsNo').val(selectedVal);
    $('#seatsName').val(selectedName);
    $('#seatsCount').val($(".disabled-seat span").length);

}