var data_url = "/jyflapi/";
var ecs_url ="/";

var timeOut;

$(function () {
    $('#saf').on('click', function () {
        showSafeCenter();
    });
    /**
     * 安全中心渲染
     * @author zhaoyingchao
     */
    function showSafeCenter() {
        var user_id = $('#user_id').val();
        var data = showSafe(user_id);
        if (data.result == "true") {
            layer.open({
                type: 1,
                title: false,
                area: '570px',
                shadeClose: false, //点击遮罩关闭
                content: '<div class="saf"><h3>安全中心</h3><table class="table"><tr class="tr_1"><td><span></span>登录密码</td><td class="td_2">' + data.password.msg + '</td><td class="td_3">设置登陆密码，降低盗号风险；</td><td class="td_4">立即修改</td></tr><tr class="tr_2"><td><span></span>手机号</td><td class="td_2">' + data.phone.msg + '</td><td class="td_3">绑定手机，可直接使用手机号登陆；</td><td class="td_4">'+(data.phone.result=='true'?'修改绑定':'立即绑定')+'</td></tr><tr class="tr_3"><td><span></span>安全问题</td><td class="td_2">' + data.answer.msg + '</td><td class="td_3">保护账户安全，验证您身份的工具之一；</td><td class="td_4">'+(data.answer.result=='true'?'修改答案':'立即设置')+'</td></tr></table></div>'
            });
        } else {
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
    $('#reg').on('click', function () {
        var index;
        $.ajax({
            type: 'post',
            url: ecs_url + 'user.php',
            data: {act: 'account_deposit'},
            dataType: 'json',
            beforeSend:function () {
                index=layer.load();
            },
            success: function (data) {
                // console.log(data);
                layer.close(index);
                var info = data.info;
                var i=0;
                var html = '<div class="reg">' +
                    '<form name="formSurplus" id="formSurplus" method="post" onsubmit="return submitOp(false)">' +
                    '<h3>卡充值</h3><div style="overflow:hidden">' +
                    '<div class="reg_title">充值金额:</div>' +
                    '<div class="reg_num">';
                $.each(info.priceList, function (k, val) {
                    html += '<span class="radio_img '+(k==30?'on':'')+'"><input type="radio" name="amount" value="' + val + '"'+(k==30?'checked':'')+'></span>' + k + '点<span>人民币' + val + '元</span><br>';
                });
                html += '</div></div>' +
                    '<div class="pay_title">支付方式</div><table class="table"><thead><tr><td>名称</td><td>描述</td></tr></thead>' +
                    '<tbody>';
                $.each(info.payment, function (k, val) {
                    if (val.pay_id > 2) {
                        i++
                        if (i == 1) {
                            var status = 'on';
                            var check = 'checked';
                        }
                        html += '<tr><td style="width:60px"><span class="radio_img1 ' + status + '"><input type="radio" name="payment_id" value="' + val.pay_id + '"' + check + ' /></span>' + val.pay_name + '</td>' +
                            '<td>' + val.pay_desc + '</td></tr>' +
                            '<tr>';
                    }
                });
                html += '<!--<tr><td bgcolor="#ffffff">{$lang.process_notic}:</td><td align="left" bgcolor="#ffffff"><textarea name="user_note" cols="55" rows="6" style="border:1px solid #ccc;">{$order.user_note|escape}充值</textarea></td></tr>-->' +
                    '</tbody></table>' +
                    '<div class="btn_all">' +
                    '<input type="hidden" name="surplus_type" value="0" />' +
                    '<input type="hidden" name="rec_id" value="' + info.order.id + '" />' +
                    '<input type="hidden" name="act" value="act_account" />' +
                    '<button class="btn_reg">立即充值</button> ' +
                    '<button class="btn_1" name="reset" type="reset">重置金额</button> ' +
                    '<button class="btn_2">充值记录</button>' +
                    '</div></form>' +
                    '</div>';
                layer.open({
                    type: 1,
                    title: false,
                    area: '570px',
                    shadeClose: false, //点击遮罩关闭
                    content: html,
                });

            }
        });
    });
    $(document).delegate('.btn_reg', 'click', function () {
        var index;
        $.ajax({
            type: 'post',
            url: ecs_url + 'user.php',
            // async: false,
            data:$('#formSurplus').serialize(),
            dataType: 'json',
            beforeSend:function(){
                index = layer.load();
            },
            success: function (data) {
                //console.log(data);
                layer.close(index);
                if(data.result == 'true'){
                    var info = data.info;
                    var html = '<div class="reg">' +
                        '<h3>卡充值</h3><div style="overflow:hidden">' +
                        '<table class="table">' +
                        '<tbody>' +
                        '<tr>' +
                        '<td style="width:115px">您的充值金额为:</td>' +
                        '<td>'+info.amount+'</td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td style="width:115px">您选择的支付方式为:</td>' +
                        '<td>'+info.payment.pay_name+'</td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td style="width:115px">支付方式描述:</td>' +
                        '<td>'+info.payment.pay_desc+'</td>' +
                        '</tr>' +
                        '</tbody></table></div></div>' +info.payment.pay_button;
                    $('.layui-layer-content').html(html);
                }else{
                    layer.alert(data.msg);
                }
            }
        });
    })
    $(document).on('click','.bnt_blue_1',function(){
    	layer.closeAll();
    	layer.open({
    		type: 1,
            title: false,
            area: '470px',
            shadeClose: false, //点击遮罩关闭
            closeBtn:0,
            content:'<div class="pay_tishi"><div class="pay_tishi_title">支付提示：</div><div class="pay_tishi_content"><div class="pay_tishiAll"><div class="pay_tishi_1"><span class="pay_title_img"><img src="/images/juyoufuli/img_login/pay_tishi.png" width="20"></span>支付完成前，请不要关闭此支付验证窗口</div><div class="pay_tishi_2">支付完成后，请根据您支付的情况点击下面按钮。</div></div><div class="pay_tishi_btnAll"><div class="pay_tishi_btn"><a href="javascript:;" class="pay_tishi_question">支付遇到问题</a><a href="javascript:;" class="pay_tishi_ok">支付完成</a></div></div></div></div>'
    	})
    })
    //  	点击支付完成按钮关闭弹层
    $(document).delegate('.pay_tishi_ok', 'click', function () {
        layer.closeAll();
        rechargeLog();
    })
    // 支付遇到问题
    $(document).delegate('.pay_tishi_question', 'click', function () {
        layer.closeAll();
        rechargeLog();
    })
    $('#red_packet').on('click', function () {
        layer.open({
            type: 1,
            title: false,
            area: '570px',
            shadeClose: false, //点击遮罩关闭
            content: '<div class="red_packet"><h3>我的红包</h3><div><table class="table"><thead><tr><td>红包名称</td><td>红包金额</td><td>最小订单金额</td><td>获取时间</td><td>截至使用日期</td><td>红包状态</td></tr></thead><tbody><tr><td>你现在还没有红包</td></tr></tbody></table><div class="add">添加红包</div><div class="form-group"><label>红包序列号：</label><span class="red_card"><input type="text"></span><label>红包密码：</label><span class="red_num"><input type="password"></span> <button class="btn_add">添加红包</button></div></div></div>'
        });
    });
    $('#merge').on('click', function () {
        var uname = $(this).attr('data-uname');
        var html = '<div class="merge"><h3>卡合并</h3>' +
            '<form name="mergeForm" onsubmit="return submitOp(false)" >' +
            '<input type="hidden" name="act" value="act_card_merge" />' +
            '<div class="form-group" style="margin-top:20px">' +
            '<label>转出（需清空）的卡号：</label><span class="qk"><input type="text" name="from_card" value="" id="from_card" class="inputBg" /></span>' +
            '<label>密码：</label><span class="qk"><input type="password" name="from_card_pwd" value="" id="from_card_pwd" /></span>' +
            '</div>' +
            '<div class="form-group">' +
            '<label>转入（合并到）的卡号：</label><span class="qk"><input type="text" name="to_card" value="' + uname + '" id="to_card" readonly/></span>' +
            '<label>密码：</label><span class="qk"><input type="password" name="to_card_pwd" value="" id="to_card_pwd" /></span>' +
            '</div>' +
            '<div class="btn_merge"><button>确认合并</button></div>' +
            '</form>' +
            '<div class="tip"><p>温馨提示：</p>' +
            '<span class="tip_1">1.卡合并后，有效期以转入（合并到）的卡号为准：</span>' +
            ' <span>2.3个月内只能合并一次；</span> ' +
            '<span class="tip_1">3.不支持多张（2张以上）卡合并；</span> ' +
            '<span>4.卡合并后，业务以转入卡业务为准；</span>' +
            '</div></div>';
        layer.open({
            type: 1,
            title: false,
            area: '570px',
            shadeClose: false, //点击遮罩关闭
            content: html,
        });
    });
    $(document).delegate('.btn_merge button', 'click', function () {
        var from_card = $('#from_card').val();
        var from_card_pwd = $('#from_card_pwd').val();
        var to_card = $('#to_card').val();
        var to_card_pwd = $('#to_card_pwd').val();
        cardMerge(from_card, from_card_pwd, to_card, to_card_pwd);

    });
// $(document).delegate('.add_new', 'click', function(){
// 	$('.layui-layer-content').html('<div class="set_add"><h3>添加地址</h3><div class="form-group"><label><span class="xing">*</span>所在地区：</label><select name="country" id="country" onchange="region.changed(this, 1, \'province\')" style="width:100px;background-color:transparent;" ></select><select name="province" id="province" onchange="region.changed(this, 2, \'city\')" style="width:100px;background-color:transparent;" ></select><select name="city" id="city" style="width:100px;background-color:transparent;"  ></select></div><div class="form-group"><label><span class="xing">*</span>街道地址：</label><input type="text"></div><div class="form-group"><label><span class="xing">*</span>邮政编码：</label><input type="text"></div><div class="form-group"><label><span class="xing">*</span>收货人姓名：</label><input type="text"></div><div class="form-group"><label><span class="xing">*</span>电话号码：</label><input type="text"></div><div class="set_add_btn"><button class="yes">保存</button><button class="no">取消</button></div></div>');
// });
    $(document).delegate('.add_new', 'click', function () {
        $('.layui-layer-content').html(
            '<div class="set_add"><h3>添加地址</h3><div class="form-group"><label>' +
            '<span class="xing">*</span>所在地区：</label>' +
            '<select name="country" id="country" onchange="region.changed(this, 1, \'province\')" style="width:100px;background-color:transparent;" ></select>' +
            '<select name="province" id="province" style="width:100px;background-color:transparent;" ></select>' +
            // '<select name="province" id="province" onchange="region.changed(this, 2, \'city\')" style="width:100px;background-color:transparent;" ></select>' +
            // '<select name="city" id="city" style="width:100px;background-color:transparent;"  ></select>
            '</div>' +
            '<div class="form-group"><label><span class="xing">*</span>街道地址：</label><input type="text" id="address"></div>' +
            '<div class="form-group"><label>邮政编码：</label><input type="text" id="zipcode"></div>' +
            '<div class="form-group"><label><span class="xing">*</span>收货人姓名：</label><input type="text" id="consignee"></div>' +
            '<div class="form-group"><label><span class="xing">*</span>电话号码：</label><input type="text" id="mobile"></div>' +
            '<div class="set_add_btn"><button class="yes">保存</button><button class="no">取消</button></div></div>'
        );
        showprovince();
    });
    //充值记录
    $(document).delegate('.btn_all .btn_2', 'click', function () {
        layer.closeAll();
        rechargeLog();
    });
    //充值列表删除点击
    $(document).delegate('.layui-layer-content .log .cancel','click',function(){
        var url = $(this).attr('data-href');
        var $cancelOb=$(this).parents('tr');
        if (!confirm('确认删除？')) return false;
        console.log($cancelOb);
        $.ajax({
           type:'get',
            url:url,
            dataType:'json',
            success:function(data){
                console.log(data);
                if(data.result=='true'){
                    $cancelOb.remove();
                    alert(data.msg);
                }else{
                    alert(data.msg);
                }
            }
        });
    });
    //充值列表付款点击
    $(document).delegate('.deposit_pay','click',function(){
        var url = $(this).attr('data-href');
        var index;
        $.ajax({
            type:'get',
            url:url,
            dataType:'json',
            beforeSend:function(){
                index=layer.load();
            },
            success:function(data){
                //console.log(data);
                layer.close(index);
                var info = data.info;
                if(data.result=='true'){
                    var html = '<div class="reg">' +
                        '<h3>卡充值</h3><div style="overflow:hidden">' +
                        '<table class="table">' +
                        '<tbody>' +
                        '<tr>' +
                        '<td style="width:115px">您的充值金额为:</td>' +
                        '<td>'+info.amount+'</td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td style="width:115px">您选择的支付方式为:</td>' +
                        '<td>'+info.payment.pay_name+'</td>' +
                        '</tr>' +
                        '<tr>' +
                        '<td style="width:115px">支付方式描述:</td>' +
                        '<td>'+info.payment.pay_desc+'</td>' +
                        '</tr>' +
                        '</tbody></table></div></div>' +info.payment.pay_button;
                }else if(data.result=='false'){
                    if(info.action=='account_log'){
                        alert(data.msg);
                    }else if(info.action=='account_deposit'){
                        alert(data.msg);
                        var html = '<div class="reg">' +
                            '<form name="formSurplus" id="formSurplus" method="post" onsubmit="return submitOp(false)">' +
                            '<h3>卡充值</h3><div style="overflow:hidden">' +
                            '<div class="reg_title">充值金额:</div>' +
                            '<div class="reg_num">';
                        $.each(info.priceList, function (k, val) {
                             if(parseFloat(info.order.amount)==parseFloat(val)){
                                 var checked ='checked="checked"';
                                 var on = 'on';
                            }else{
                                 var checked = '';
                                 var on='';
                             }
                            html += '<span class="radio_img '+on+'"><input type="radio" name="amount" value="' + val + '" '+checked+'></span><span>'+ k +'点</span><span>人民币' + val + '元</span><br>';
                        });
                        html += '</div></div>' +
                            '<div class="pay_title">支付方式</div><table class="table"><thead><tr><td>名称</td><td>描述</td></tr></thead>' +
                            '<tbody>';
                        $.each(info.payment, function (k, val) {
                            if (val.pay_id > 2)
                                html += '<tr><td style="width:60px"><span class="radio_img1"><input type="radio" name="payment_id" value="' + val.pay_id + '" /></span>' + val.pay_name + '</td>' +
                                    '<td>' + val.pay_desc + '</td></tr>' +
                                    '<tr>';
                        });
                        html += '<!--<tr><td bgcolor="#ffffff">{$lang.process_notic}:</td><td align="left" bgcolor="#ffffff"><textarea name="user_note" cols="55" rows="6" style="border:1px solid #ccc;">{$order.user_note|escape}充值</textarea></td></tr>-->' +
                            '</tbody></table>' +
                            '<div class="btn_all">' +
                            '<input type="hidden" name="surplus_type" value="0" />' +
                            '<input type="hidden" name="rec_id" value="' + info.order.id + '" />' +
                            '<input type="hidden" name="act" value="act_account" />' +
                            '<button class="btn_reg">立即充值</button> ' +
                            '<button class="btn_1" name="reset" type="reset">重置金额</button> ' +
                            '<button class="btn_2">充值记录</button>' +
                            '</div></form>' +
                            '</div>';
                    }
                }
                $('.layui-layer-content').html(html);
            }
        })
    });
    $(document).delegate('.btn_all .btn_1','click',function(){
        if($('.radio_img').hasClass('on')) $('.radio_img').removeClass('on');
        if($('.radio_img1').hasClass('on')) $('.radio_img1').removeClass('on');
    });
    $(document).delegate('.tr_1 .td_4', 'click', function () {
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
    $(document).delegate('.set .set_btn .edit_password', 'click', function () {
        var user_id = $("#user_id").val();
        var old_password = $('#old_password').val();
        var new_password = $('#tbPassword').val();
        var con_password = $('#con_password').val();
        userLoginPass(user_id, old_password, new_password, con_password);
    });
    //个人信息页手机绑定
    $(document).delegate('#phone_bound', 'click', function () {
        var uid = $('#user_id').val();
        var phone = $(this).siblings('span').find("input").val();
        var index;
        $('.layui-layer-content').html('<div class="tel"><h3>绑定手机号</h3><div class="form-group"><label>手机号：</label><input id="tel" type="text" value="'+phone+'"><div class="ach"><input type="button" id="getverification" value="获取验证码"></div></div><div class="form-group"><label>动态码：</label><input id="captcha" type="text"></div><div class="btn_tel"><button class="btn_bound">绑定</button> <button class="btn_phone_cancel">取消</button></div></div>');
    });
    //取消绑定
    $(document).delegate('.layui-layer-content .tel .btn_phone_cancel', 'click', function () {
        layer.closeAll();
        userShow();
    });

//安全中心绑定手机
    $(document).delegate('.tr_2 .td_4', 'click', function () {
        var uid = $('#user_id').val();
        var index;
        $.ajax({
            type:'post',
            url:api_url+'index.php/Users/User/showSafe',
            data:{
                user_id:uid,
            },
            dataType:'json',
            beforeSend:function () {
                index = layer.load();
            },
            success:function (data) {
                layer.close(index);

                $('.layui-layer-content').html('<div class="tel"><h3>绑定手机号</h3><div class="form-group"><label>手机号：</label><input id="tel" type="text" value="'+data.phone.num+'"><div class="ach"><input type="button" id="getverification" value="获取验证码"></div></div><div class="form-group"><label>动态码：</label><input id="captcha" type="text"></div><div class="btn_tel"><button class="btn_bound">绑定</button> <button class="btn_cancel">取消</button></div></div>');
            }
        })
    });
    /**
     * 获取验证码
     * @author zhaoyingchao
     */
    $(document).delegate('#getverification', 'click', function () {
        var user_id = $('#user_id').val();
        var tel = $('#tel').val();
        if(tel == ''||tel==null||tel=='undefined'){
            layer.alert('请输入手机号！');
            return false;
        }
        clearTime(timeOut);
        timeOut = settime(this);
        var data = getverification(user_id, tel);
    });
    /**
     * 绑定手机
     * @author zhaoyingchao
     */
    $(document).delegate('.btn_tel .btn_bound', 'click', function () {
        var user_id = $('#user_id').val();
        var tel = $('#tel').val();
        var captcha = $('#captcha').val();
        //alert(user_id+'='+tel+'='+captcha);
        if(tel == ''||tel==null||tel=='undefined'){
            layer.alert('请输入手机号！');
            return false;
        }
        if(captcha == ''||captcha==null||captcha=='undefined'){
            layer.alert('请输入验证码！');
            return false;
        }

        boundPhone(user_id, tel, captcha);
    });
    /**
     * 绑定手机取消
     * @author zhaoyingchao
     */
    $(document).delegate('.btn_tel .btn_cancel', 'click', function () {
        clearTime(timeOut);
        layer.closeAll();
        showSafeCenter();
    });
    /**
     * 安全问题页面渲染
     */
    $(document).delegate('.tr_3 .td_4', 'click', function () {
        var user_id = $('#user_id').val();
        showQuestionAnswer(user_id);
    });
    /**
     * 安全问题提交
     * @author zhaoyingchao
     */
    $(document).delegate('.btn_question .yes', 'click', function () {
        var user_id = $('#user_id').val();
        var answerone = $('#answerone').val();
        var answertwo = $('#answertwo').val();
        var answerthree = $('#answerthree').val();
        if(answertwo==''||answerone==''||answerthree==''){
            layer.alert('答案不能为空！');
            return false;
        }
        editLoginQues(user_id, answerone, answertwo, answerthree);
    });
//单选按钮点击样式
    $(document).delegate('.radio_img', 'click', function () {
        $(this).addClass("on").siblings().removeClass("on");
    })
//多选按钮点击效果
    $(document).delegate('.checkbox_img', 'click', function () {
        $(this).toggleClass("on_check")
    })

    $(document).delegate('.radio_img1', 'click', function () {
        $(this).addClass("on").parents('tr').siblings().children().find('.radio_img1').removeClass("on");
    })

    $(document).delegate('.set_btn .no', 'click', function () {
        layer.closeAll();
        showSafeCenter();
    })
//返回安全中心
    $(document).delegate('.btn_question .no', 'click', function () {
        layer.closeAll();
        showSafeCenter();
    })
//添加地址保存
    $(document).on('click', '.set_add_btn .yes', function () {
        addAddress();
    });
//添加地址取消
    $(document).on('click', '.set_add_btn .no', function () {
        // $('.layui-layer-content').html('<div class="shouhuo"><h3>收货信息</h3><div class="table-responsive"><table class="table"><thead><tr><td>收件人</td><td>地址/邮编</td><td>电话/手机</td><td>操作</td></tr></thead><tbody><tr><td>王某某</td><td>北京市被北京北京南方国际化的减肥</td><td>13820906181</td><td class="gn_btn"><span>删除</span><span class="xiugai" onclick="saveAddress(4)">修改</span></td></tr></tbody></table></div><div class="add_new" onclick="showprovince()">添加新地址</div></div>')
        layer.closeAll();
        showAddress();
    })
//更新地址保存
    $(document).on('click', '.set_add_btn .edit', function () {
        updateAddress();
    });
//设置默认地址
    $(document).delegate('.shouhuo_left .table tr','click',function(){
        var address_id = $(this).attr('data-address-id');
        var index;
        $.ajax({
           type:'post',
           url:'/address.php',
           data:{act:'AjaxAddressDefault',address_id:address_id},
            dataType:'json',
            beforeSend:function(){
                index=layer.load();
            },
            success:function(data){
                layer.close(index);
                if(data.error==0){
                    alert('设置默认成功！');
                    layer.closeAll();
                    showAddress();
                }else{
                    alert(data.content);
                    layer.closeAll();
                    showAddress();
                }
            }
       })
    });
    $(document).delegate('.gn_btn .xiugai', 'click', function (e) {
        e.stopPropagation();
        var address_id=$(this).attr('data-addressid');
        $('.layui-layer-content').html('<div class="set_add"><h3>修改地址</h3><div class="form-group"><input type="hidden" name="address_id" id="address_id" value=""><label><span class="xing">*</span>所在地区：</label><select name="country" id="country" onchange="region.changed(this, 1, \'province\')" style="width:100px;background-color:transparent;" ></select><select name="province" id="province" style="width:100px;background-color:transparent;" ></select></div><div class="form-group"><label><span class="xing">*</span>街道地址：</label><input type="text" id="address"></div><div class="form-group"><label>邮政编码：</label><input type="text" id="zipcode"></div><div class="form-group"><label><span class="xing">*</span>收货人姓名：</label><input type="text" id="consignee"></div><div class="form-group"><label><span class="xing">*</span>电话号码：</label><input type="text" id="mobile"></div><div class="set_add_btn"><button class="edit">保存</button><button class="no">取消</button></div></div>')
        saveAddress(address_id)
    })
    $(document).on('focus', '#tbPassword', function () {
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
            if (len > 16) {
                $(pwdinput).val(str.match(cat)[0]);
            }
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

            if (len < 6) {
                return 1;
            }

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
                if (!maths_01 && !smalls_01 && !bigs_01) {
                    return true;
                }
            }
            return false;
        }
    })

    /**
     * 手机输入框绑定手机号判断
     * 如未绑定提示是否绑定输入的手机号
     * 如绑定则不做提示
     */
    $(document).delegate('.judgeBound','input propertychange',function(){
        var uid = $('#user_id').val();
        var phone = $(this).val();
        if(phone.length!=11) return false;
        var index;
        var partten = /^1[3,5,7,8]\d{9}$/;
        if(partten.test(phone)) {
            $.ajax({
                type: 'post',
                url: api_url + 'index.php/Users/User/showSafe',
                data: {
                    user_id: uid,
                },
                dataType: 'json',
                beforeSend:function(){
                    index=layer.load();
                },
                success: function (data) {
                    layer.close(index);
                    if (data.phone.result == 'false') {
                        layer.confirm('是否绑定手机？', function () {
                            layer.open({
                                type: 1,
                                title: false,
                                area: '570px',
                                shadeClose: false, //点击遮罩关闭
                                content: '<div class="tel"><h3>绑定手机号</h3><div class="form-group"><label>手机号：</label><input id="tel" type="text" value="' + phone + '"><div class="ach"><input type="button" id="getverification" value="获取验证码"></div></div><div class="form-group"><label>动态码：</label><input id="captcha" type="text"></div><div class="btn_tel"><button class="btn_bound">绑定</button> <button class="global_bound_cancel">取消</button></div></div>'
                            });
                        });
                    }
                }
            })
        }
    });

    //全局手机绑定取消
    $(document).delegate('.global_bound_cancel','click',function () {
        layer.closeAll();
    })
})


//获取验证码
var countdown = 120;
function settime(obj) {
    if (countdown == 0) {
        obj.removeAttribute("disabled");
        obj.value = "获取验证码";
        $(obj).css({'background': '#f2f2f2', 'color': 'black'});
        countdown = 120;
        return;
    } else {
        obj.setAttribute("disabled", true);
        obj.value = "重新发送(" + countdown + ")";
        $(obj).css({'background': '#8C8686', 'color': '#3C3838'});
        countdown--;
    }
    return setTimeout(function () {
            settime(obj)
        }
        , 1000)
}

function clearTime(set) {
    countdown = 120;
    clearTimeout(set);
}

function showprovince() {
    var option = "<option style='width:100px;background-color:black;' display='inline' value='0' >请选择市</option>";
    var province_op = "<option style='width:100px;background-color:black;' display='inline' value='0' >请选择区</option>";
    // var city_op = "<option style='width:100px;background-color:black;' display='inline' value='0' >请选择区</option>";
    var index;
    $.ajax({
        type: "post",
        url: data_url + "index.php/Users/User/showCity",
        data: "",
        dataType: "json",
        beforeSend:function(){
            index=layer.load();
        },
        success: function (data) {
            layer.close(index);
            var address_list = data.business;
            for (var i = 0; i < address_list.length; i++) {
                option += "<option style='width:100px;background-color:black;' display='inline'   value=" + address_list[i].region_id + ">" + address_list[i].region_name + "</option>";
            }
            $("#country").html(option);
            $('#province').html(province_op);
            // $('#city').html(city_op);
        }
    });

}

function submitOp(flag) {
    return flag;
}

function cardMerge(from_card, from_card_pwd, to_card, to_card_pwd) {
    if (from_card == '') {
        alert('要合并的卡号不能为空！');
        return false;
    }
    if (from_card_pwd == '') {
        alert('要合并的卡号密码不能为空！');
        return false;
    }
    if (to_card == '') {
        alert('合并到的卡号不为空！');
        return false;
    }
    if (to_card_pwd == '') {
        alert('合并到的卡号密码不为空！');
        return false;
    }
    if (from_card == to_card) {
        alert('要合并的卡不能一样！');
        return false;
    }
    if (confirm('确定要将这两张卡合并？')) {
        var index;
        $.ajax({
            type: 'post',
            url: ecs_url + 'user.php',
            async: false,
            data: {
                act: 'act_card_merge',
                fromcard: from_card,
                fromcardpwd: from_card_pwd,
                tocard: to_card,
                tocardpwd: to_card_pwd
            },
            dataType: 'json',
            beforeSend: function () {
                index = layer.load();
            },
            success: function (data) {
                if (data.msg.substr(0, 7) == 'success') {
                    layer.close(index);
                    alert('合并成功');
                    location.reload();
                } else {
                    alert(data.msg);
                }
            }
        });
    } else {
        return false;
    }
}
//充值记录函数
function rechargeLog(){
    var index;
    $.ajax({
        type: 'post',
        url: ecs_url + 'user.php',
        data: {act: 'account_log'},
        dataType: 'json',
        beforeSend:function(){
            index=layer.load();
        },
        success: function (data) {
            layer.close(index);
            // console.log(data);
            var info = data.info;
            var html = '<div class="log"><h3>充值记录</h3><div class="chongzhijilu"><table class="table">' +
                '<thead><tr><td class="jilu_1">操作时间</td><td class="jilu_2">类型</td><td class="jilu_3">金额</td><td class="jilu_4">管理员备注</td><td class="jilu_5">状态</td><td class="jilu_6">操作</td></tr></thead>' +
                '<tbody>';
            $.each(info.account_log, function (k, val) {
                if ((val.is_paid == 0 && val.process_type == 1) || val.handle){
                    var cancel = '<a class="cancel" href="javascript:void(0);" data-href="user.php?act=cancel&id='+val.id+'">删除</a>';
                }else{
                    var cancel = '';
                }

                html += '<tr><td class="jilu_1">' + val.add_time + '</td><td class="jilu_2">' + val.type + '</td><td class="jilu_3">' + val.amount + '</td><td class="jilu_4">' + val.short_admin_note + '</td><td class="jilu_5">' + val.pay_status + '</td><td class="jilu_6">' + val.handle+' '+cancel+ '</td></tr>';
            });
            html += '</tbody></table></div></div>';
            // $('.layui-layer-content').html(html);
            layer.open({
                type: 1,
                title: false,
                area: '570px',
                shadeClose: false, //点击遮罩关闭
                content: html,
            });
           $(".chongzhijilu").niceScroll({
				cursorcolor: "#BFB1B1",
				cursoropacitymax: 1,
				touchbehavior: false,
				cursorwidth: "5px",
				cursorborder: "0",
				cursorborderradius: "5px"
			}); 
        }
    })
}



