/**
 * Created by chao on 2016/6/30.
 */
var message = {
    messageAlert:function(msgstr){
        $('body').append(
            '<div id="message-cover" style="width: 100%;height: 100%;background-color: #000;filter:alpha(opacity=80); opacity: 0.8;position: absolute;top:0;left: 0;z-index: 1;"></div>'+
            '<div id="message-box" style="position: absolute;top:50%;left: 50%;width:0rem;height:0;-webkit-transform: translateX(-50%) translateY(-50%);transform: translateX(-50%) translateY(-50%);background-color: #FFF;z-index: 10;text-align: center;border-radius: 10px;">'+
            '<div id="message" style="display:none;padding: 1.15rem 0;font-size: 0.6rem;">'+msgstr+'</div>'+
            '<div id="message-btn" style="display:none;padding: 0.65rem;font-size: 0.75rem;color: #30d0b6;border-top: 1px solid #30d0b6;" onclick="javascript:message.closeMessageBox();">确定</div>'+
            '</div>'
        )
        $('#message-box').animate({width:'12.95rem',height:'5.45rem'},300,function(){
            $('#message').css('display','block');
            $('#message-btn').css('display','block');

        })
    },
    messageShow:function(msgstr){
        $('body').append(
            '<div id="message-cover" style="width: 100%;height: 100%;background-color: #000;filter:alpha(opacity=80); opacity: 0.8;position: absolute;top:0;left: 0;z-index: 1;"></div>'+
            '<div id="message-box" style="position: absolute;top:50%;left: 50%;width:13.5rem;height:9.1rem;-webkit-transform: translateX(-50%) translateY(-50%);transform: translateX(-50%) translateY(-50%);z-index: 10;text-align: center;">'+
            '<div id="message-img" style="text-align: center;"><img src="/mobile/images/right.png" style="width: 6.6rem;height:6.6rem;" /></div>'+
            '<div id="message" style="padding: 1.15rem 0;font-size: 1rem;color:#FFF;text-align: center">'+msgstr+'</div>'+
            '</div>'
        )
        var setTime = setTimeout("message.closeMessageShow()",3000);
    },
    closeMessageBox:function () {
        $('#message').css('display','none');
        $('#message-btn').css('display','none');
        $('#message-box').animate({width:'0rem',height:'0rem'},300,function(){
            $(this).remove();
            $('#message-cover').remove();
        })
    },
    closeMessageShow:function () {
        $('#message-box').remove();
        $('#message-cover').remove();
    }
}