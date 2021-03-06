var api_url = '/jyflapi/';
$(function(){
//	游戏规则
	$('#guize').on('click',function(){
    layer.open({
        type: 1,
        title:false,
        area:'570px',
        shadeClose: false, //点击遮罩关闭
        content:'<div class="guize_all"><h3>游戏规则</h3><div class="guize_content"><div class="color_zhuti font-16">| 聚优夺宝活动规则</div><div>1、获得聚优点：聚优福利卡的点数即是相对应点数的聚优点；</div><div>2、所有支持者可参与“全民夺宝”，另外支持者可参与本平台为您所属公司独家定制的“专属夺宝”；</div><div>3、挑选喜欢的商品或平台：商品分配数量对应商品价值，使用聚优点，可在全平台及公司平台参与购买；</div><div>4、获得商品：当所有号码都被分配完毕后，系统会根据规则计算出1个幸运号码，该号码的支持者，直接获得该商品。</div><div class="font-16 color_zhuti">| 幸运号码计算规则</div><div>1、	商品的最后一个号码分配完毕后，将公示最后支持者的参与时间（得出数值A）（时间计算到分和秒，如17点57分27秒则为175727）；</div><div>2、	为保证公平、公正、公开，如遇开奖时当天3D福彩中奖号码未出，则取上一期3D福彩中奖号码；(一个三位数值B);</div><div>3、	（数值A×数值B）+1得出数值，根据“产品份数”位数，从结果数值末端取相同位数数值，做为中奖号码，幸运号码的持有者直接获得该商品。（例：产品份数是999，3D福彩第2016129期开奖结果641，最后支持者的参与时间175727，则641×175727+1=112641008，产品份数为三位数，则幸运号码为008.）</div></div></div>'
      })
  })
//	服务协议
	$('#xieyi').on('click',function(){
		layer.open({
        type: 1,
        title:false,
        area:['570px','415px'],
        shadeClose: false, //点击遮罩关闭
        content:'<div class="xieyi_all"><h3>聚优福利夺宝平台支持者协议</h3><div class="xieyi_content"><div style="text-indent:24px">欢迎访问聚优福利平台，为明确您（以下简称为“支持者”）的权利义务，保护支持者的合法权益，特制定本协议。申请使用聚优福利提供的夺宝平台（以下简称为平台）服务（包括夺宝等），请支持者仔细阅读以下全部内容（特别是粗体下划线标注的内容）。如支持者不同意本服务条款任意内容，请勿参与平台活动。如支持者通过进入注册程序并勾选“我同意平台服务协议”，即表示支持者与聚优福利已达成协议，自愿接受本服务条款的所有内容。此后，支持者不得以未阅读本服务条款内容作任何形式的抗辩。</div><div style="text-indent:24px">鉴于：平台以“众筹”模式为各类商品的销售提供网络空间。在本平台，商品被平分成若干等份，支持者可以使用点数支持一份或多份，当等份全部售完后，由系统根据平台规则计算出最终获得商品或服务的支持者。</div><div class="color_zhuti font-16">一、支持者使用平台服务的前提条件</div><div>1、支持者需拥有聚优福利平台认可的帐号；</div><div>2、支持者在使用平台服务时须具备相应的权利能力和行为能力，能够独立承担法律责任。如果支持者在18周岁以下，必须在父母或监护人的监护参与下才能使用本站。</div><div class="color_zhuti font-16">二、支持者管理</div><div>1、支持者ID支持者登录活动平台时，请先完成注册，卡号作为其使用平台服务的唯一身份标识，支持者需要对其帐户项下发生的所有行为负责。</div><div>2、支持者资料完善支持者应当在使用平台服务前完善个人资料，支持者资料包括但不限于个人手机号码、收货地址、帐号昵称、头像、密码、注册或更新聚优福利帐号时输入的所有信息。</div><div style="text-indent:24px">支持者在完善个人资料时承诺遵守法律法规、社会主义制度、国家利益、公民合法权益、公共秩序、社会道德风尚和信息真实性等七条底线，不得在资料中出现违法和不良信息，且支持者保证其在完善个人资料和使用帐号时，不得有以下情形：<br/>（1）违反宪法或法律法规规定的；<br/>（2）危害国家安全，泄露国家秘密，颠覆国家政权，破坏国家统一的；<br/>（3）损害国家荣誉和利益的，损害公共利益的；<br/>（4）煽动民族仇恨、民族歧视，破坏民族团结的；<br/>（5）破坏国家宗教政策，宣扬邪教和封建迷信的；<br/>（6）散布谣言，扰乱社会秩序，破坏社会稳定的；<br/>（7）散布淫秽、色情、赌博、暴力、凶杀、恐怖或者教唆犯罪的；<br/>（8）侮辱或者诽谤他人，侵害他人合法权益的；<br/>（9）含有法律、行政法规禁止的其他内容的。<br/></div><div style="text-indent:24px">若支持者提供给聚优福利的资料不准确，不真实，含有违法或不良信息的，聚优福利有权不予完善，并保留终止支持者使用平台服务的权利。若支持者以虚假信息骗取帐号ID或帐号头像、个人简介等注册资料存在违法和不良信息的，聚优福利有权采取通知限期改正、暂停使用、注销登记等措施。对于冒用关联机构或社会名人注册帐号名称的，聚优福利有权注销该帐号，并向政府主管部门进行报告。</div><div style="text-indent:24px">根据相关法律、法规规定以及考虑到平台服务的重要性，支持者同意：<br/>（1）提供及时、详尽及准确的支持者资料；<br/>（2）不断更新支持者资料，符合及时、详尽准确的要求，对完善个人资料时填写的身份证件信息不能更新。<br/><span class="text_under">（3）支持者有证明该帐号为本人所有的义务，需能提供聚优福利平台注册资料以证明该帐号为本人所有，否则聚优福利有权暂缓向支持者交付其所获得的商品。</span><br/>3、点数<br/>（1）点数必须通过聚优福利提供或认可的平台获得，从非聚优福利提供或认可的平台所获得的点数将被认定为来源不符合本服务协议。聚优福利有权拒绝从非聚优福利公司提供或认可的平台所获得的点数在平台中使用。<br/>（2）点数不能在平台之外使用或者转移给其他支持者。<br/>4、支持者应当保证在使用平台服务的过程中遵守诚实信用原则，不扰乱平台的正常秩序，<span class="text_under">不得通过使用他人帐户、一人注册多个帐户、使用程序自动处理等非法方式损害他人或聚优福利的利益。</span><br/>5、若支持者存在任何违法或违反本服务协议约定的行为，聚优福利有权视支持者的违法或违规情况适用以下一项或多项处罚措施：<br/><span class="text_under">（1）责令支持者改正违法或违规行为；<br/>（2）中止、终止部分或全部服务；<br/>（3）取消支持者夺宝订单并取消商品发放（若支持者已获得商品），且支持者已获得的点数不予退回；<br/>（4）冻结或注销支持者帐号及其帐号中的点数（如有）；<br/>（5）其他聚优福利公司认为合适在符合法律法规规定的情况下的处罚措施。若支持者的行为造成聚优福利公司及其关联公司损失的，支持者还应承担赔偿责任。<br/>6、若支持者发表侵犯他人权利或违反法律规定的言论，聚优福利公司有权注销支持者帐号及其帐号中的点数（如有），同时，聚优福利公司保留根据国家法律法规、相关政策向有关机关报告的权利。</span></div><div style="text-indent:24px">三、聚优福利夺宝众筹平台服务的规则<br/>1、释义<br/>（1）聚优福利点：指支持者在平台上购买商品的资金的呈现方式。<br/>（2）聚优福利号码：指支持者使用聚优福利点参与聚优福利购服务时所获取的随机分配号码。<br/>（3）幸运号码：指与某件商品的全部聚优福利号码分配完毕后，根据活动规则（详见聚优福利购方页面）计算出的一个号码。持有该幸运号码的支持者可直接获得该商品。<br/>2、聚优福利公司承诺遵循公平、公正、公开的原则运营平台，确保所有支持者在平台中享受同等的权利与义务，结果向所有支持者公示。<br/>3、支持者知悉，除本协议另有约定外，无论是否获得商品，支持者已用于参与平台活动的夺宝币不能退回；其完全了解参与平台活动存在的风险，聚优福利公司不保证支持者参与聚优福利购活动一定会获得商品。<br/><span class="text_under">4、支持者通过参与平台活动获得商品后，应在7天内登录平台提交或确认收货地址，否则视为放弃该商品，支持者因此行为造成的损失，聚优福利公司不承担任何责任。</span>商品由聚优福利公司或经聚优福利公司确认的第三方商家提供及发货。<br/>5、支持者通过参与平台活动获得的商品，享受该商品生产厂家提供的三包服务，具体三包规定以该商品生产厂家公布的为准。<br/><span class="text_under">6、如果下列情形发生，聚优福利公司有权取消支持者活动订单：<br/>（1）因不可抗力、平台系统发生故障或遭受第三方攻击，或发生其他聚优福利公司无法控制的情形；<br/>（2）根据聚优福利公司已经发布的或将来可能发布或更新的各类规则、公告的规定，聚优福利公司有权取消支持者订单的情形。聚优福利公司有权取消支持者的订单时，支持者可申请退还夺宝币，所退聚优福利点将在3个工作日内退还至支持者帐户中。</span><br/>7、若某件商品的聚优福利号码从开始分配之日起90天未分配完毕，则聚优福利公司有权取消该件商品的聚优福利购活动，并向支持者退还聚优福利点，所退还聚优福利点将在3个工作日内退还至支持者帐户中。</div><div style="text-indent:24px">四、本服务协议的修改</div><div style="text-indent:24px">支持者知晓聚优福利公司不时公布或修改的与本服务协议有关的其他规则、条款及公告等是本服务协议的组成部分。聚优福利公司有权在必要时通过在平台内发出公告等合理方式修改本服务协议，支持者在享受各项服务时，应当及时查阅了解修改的内容，并自觉遵守本服务协议。支持者如继续使用本服务协议涉及的服务，则视为对修改内容的同意，当发生有关争议时，以最新的服务协议为准；支持者在不同意修改内容的情况下，有权停止使用本服务协议涉及的服务。<br/>如支持者对本规则内容有任何疑问，可拨打客服电话（<span class="text_under">400-010-0689</span>）或登录帮助中心（<span style="color:red">www.huayingwenhua.com暂定网站  等具有开放后更换</span>）进行查询</div></div></div>'
      })
		 //		弹窗滚动条美化
                $(".layui-layer-content").niceScroll({
                    cursorcolor:"#BFB1B1",
                    cursoropacitymax:1,
                    touchbehavior:false,
                    cursorwidth:"5px",
                    cursorborder:"0",
                    cursorborderradius:"5px"
                });
	})
//	往期中奖
	$('#old_win').on('click',function(){
        var user_id = $(this).attr('data-uid');
        $.ajax({
            type: 'post',
            url: api_url + 'index.php/Games/GamesApi/getWinners',
            data:{user_id:user_id},
            dataType: 'json',
            success: function (data) {
                //console.log(data);
                var global_list = data.glist;
                var company_list = data.clist;
                var self_list = data.slist;
                var html = '<div class="old_winBox"><h3 style="margin-bottom:0">往期中奖</h3>' +
                    '<div class="old_win_item">' +
                    '<div class="old_win_title o_hidden">' +
                    '<div class="old_winTitle f_l">' +
                    '<a href="#" id="all_duobao" class="zhuti_a_hovercolor active">全民夺宝</a></div>' +
                    '<div class="old_winTitle1 f_l">' +
                    '<a href="#" id="person_duobao" class="zhuti_a_hovercolor">专属夺宝</a></div>' +
                    '<div class="old_winTitle2 f_l">' +
                    '<a href="#" id="self_duobao" class="zhuti_a_hovercolor">个人夺宝</a></div>' +
                    '</div><div class="winner_show" style="height: 296px;"><div class="all_duobao">' ;
                for(var i=0;i<global_list.length;i++){
                    html+='<div class="old_win_content"><div class="old_win_userimg f_l">' +
                        '<img src="'+global_list[i].user_img+'">' +
                        '</div><div class="f_l old_win_msg"><div class="old_win_qihao">期号<span>'+global_list[i].issue+'</span></div>' +
                        '<div class="old_win_name">恭喜&nbsp;<span class="old_win_username">'+global_list[i].user_name+'</span>&nbsp;获得本期商品</div>' +
                        '<div>中奖号码：<span>'+global_list[i].lottery+'</span></div>' +
                        '<div>用户卡号：<span>'+global_list[i].card_num+'</span></div>' +
                        '<div>本期参与：<span class="color_zhuti">'+global_list[i].peo_count+'人次</span></div></div>' +
                        '<div class="old_win_img f_l"><img src="'+api_url+"Public/games/upload/"+global_list[i].thumbnail+'"></div></div>';

                }
                html+='</div><div class="person_duobao" style="display:none">';

                for(var i=0;i<company_list.length;i++){

                    html+='<div class="old_win_content"><div class="old_win_userimg f_l">' +
                        '<img src="'+company_list[i].user_img+'">' +
                        '</div><div class="f_l old_win_msg"><div class="old_win_qihao">期号<span>'+company_list[i].issue+'</span></div>' +
                        '<div class="old_win_name">恭喜&nbsp;<span class="old_win_username">'+company_list[i].user_name+'</span>&nbsp;获得本期商品</div>' +
                        '<div>中奖号码：<span>'+company_list[i].lottery+'</span></div>' +
                        '<div>用户卡号：<span>'+company_list[i].card_num+'</span></div>' +
                        '<div>本期参与：<span class="color_zhuti">'+company_list[i].peo_count+'人次</span></div></div>' +
                        '<div class="old_win_img f_l"><img src="'+api_url+"Public/games/upload/"+company_list[i].thumbnail+'"></div></div>';
                }
                html+='</div><div class="self_duobao" style="display:none">';
                for(var i=0;i<self_list.length;i++){

                    html+='<div class="old_win_content"><div class="old_win_userimg f_l">' +
                        '<img src="'+self_list[i].user_img+'">' +
                        '</div><div class="f_l old_win_msg"><div class="old_win_qihao">期号<span>'+self_list[i].issue+'</span></div>' +
                        '<div class="old_win_name">恭喜&nbsp;<span class="old_win_username">'+self_list[i].user_name+'</span>&nbsp;获得本期商品</div>' +
                        '<div>中奖号码：<span>'+self_list[i].lottery+'</span></div>' +
                        '<div>用户卡号：<span>'+self_list[i].card_num+'</span></div>' +
                        '<div>本期参与：<span class="color_zhuti">'+self_list[i].peo_count+'人次</span></div></div>' +
                        '<div class="old_win_img f_l"><img src="'+api_url+"Public/games/upload/"+self_list[i].thumbnail+'"></div></div>';
                }

                html+='</div></div></div></div>';
                layer.open({
                    type: 1,
                    title:false,
                    area:['570px','415px'],
                    shadeClose: false, //点击遮罩关闭
                    content:html,
                })
                 //		弹窗滚动条美化
                $(".layui-layer-content .winner_show").niceScroll({
                    cursorcolor:"#BFB1B1",
                    cursoropacitymax:1,
                    touchbehavior:false,
                    cursorwidth:"5px",
                    cursorborder:"0",
                    cursorborderradius:"5px"
                });
            },
        });
      })
//		点击全民夺宝
    $(document).on('click','#all_duobao',function(){
        $('.all_duobao').css('display','block').siblings().css('display','none')
        $(this).addClass('active').parent().siblings().find('a').removeClass('active');
    })
//		点击专属夺宝
    $(document).on('click','#person_duobao',function(){
        $('.person_duobao').css('display','block').siblings().css('display','none')
        $(this).addClass('active').parent().siblings().find('a').removeClass('active');
    })
//		点击个人夺宝
    $(document).on('click','#self_duobao',function(){
        $('.self_duobao').css('display','block').siblings().css('display','none')
        $(this).addClass('active').parent().siblings().find('a').removeClass('active');
    })

//	详情介绍
	$('.scroll_msg').on('click','.pop_right_goods',function(){
        var id = $(this).attr('data-id');
        var uid = $('#user_id').val();
        var cid = $(this).attr('data-cid');
        $.ajax({
            type: 'post',
            url: api_url + 'index.php/Games/GamesApi/getGame',
            async:false,
            data: {game_id: id, user_id: uid,company_id:cid},
            dataType: 'json',
            success: function (data) {
                //console.log(data);
                var html = '<div class="pop_right_goods_details">' +
                    '<h3>详情介绍</h3>' +
                    '<div class="goods_details_box">' +
                    '<div class="goods_details_img f_l">' +
                    '<img src="'+api_url+"Public/games/upload/"+data.game_info.thumbnail+'">' +
                    '</div>' +
                    '<div class="f_l goods_details_content">' +
                    '<div class="goods_details_name">'+data.game_info.game_name+'</div>' +
                    '<div class="goods_details_jishao">'+data.game_info.description+'</div>' +
                    '</div></div>' +
                    '<div class="yigou_name"><div class="f_l">已购号：</div>' +
                    '<div class="f_l yigouhao_all">';
                var partinfo = data.part_info;
                if(partinfo != "false"){
                    for(var i=0;i<partinfo.length;i++){
                        html += '<span class="yigouhao">'+partinfo[i].lottery_num+'</span>';
                    }
                }else{
                    html+='<span class="yigouhao">您没有抢购！</span>';
                }
                html += '</div></div></div>';
                layer.open({
                    type: 1,
                    title: false,
                    area: ['570px', '415px'],
                    shadeClose: false, //点击遮罩关闭
                    content:html,
                });
                //		弹窗滚动条美化
                $(".layui-layer-content").niceScroll({
                    cursorcolor:"#BFB1B1",
                    cursoropacitymax:1,
                    touchbehavior:false,
                    cursorwidth:"5px",
                    cursorborder:"0",
                    cursorborderradius:"5px"
                });
            }
        })
	})

//	抢购页面
	$('.scroll_msg').on('click','.duo',function(){
		var id = $(this).parent('a').attr('data-id');
        var cid = $(this).parent('a').attr('data-cid');
        var uid = $('#user_id').val();
        var cnum = $(this).parent('a').attr('data-cnum');
        var phone = $(this).parent('a').attr('data-phone');
        if(phone==''||phone==null||phone==undefined){
            var aindex = layer.alert('为确保您及时收到中奖信息，请先绑定手机号！', function () {
                layer.close(aindex);
                layer.open({
                    type: 1,
                    title: false,
                    area: '570px',
                    shadeClose: false, //点击遮罩关闭
                    content: '<div class="tel"><h3>绑定手机号</h3><div class="form-group"><label>手机号：</label><input id="tel" type="text" value="' + phone + '"><div class="ach"><input type="button" id="getverification" value="获取验证码"></div></div><div class="form-group"><label>动态码：</label><input id="captcha" type="text"></div><div class="btn_tel"><button class="btn_bound">绑定</button> <button class="global_bound_cancel">取消</button></div></div>'
                });
            });
            return false;
        }
        $.ajax({
            type:'post',
            url:api_url+'index.php/Games/GamesApi/getGame',
            data:{game_id:id,user_id:uid,company_id:cid},
            dataType:'json',
            success:function (data) {
                var html = '<div class="qianggou_box">' +
                    '<h3>聚优夺宝</h3>' +
                    '<div class="qianggou_details">' +
                    '<div class="qianggou_img f_l">' +
                    '<img src="'+api_url+"Public/games/upload/"+data.game_info.thumbnail+'">' +
                    '</div>' +
                    '<div class="qianggou_details_content f_l">' +
                    '<div class="qianggou_details_name">'+data.game_info.game_name+'</div>' +
                    '<form name="purchase" id="purchase">' +
                    '<input name="user_id" value="'+uid+'" type="hidden" />' +
                    '<input name="game_id" value="'+id+'" type="hidden" />' +
                    '<input name="card_num" value="'+cnum+'" type="hidden" />' +
                    '<input name="company_id" value="'+cid+'" type="hidden" />' +
                    '<input name="point" id="point" value="'+data.game_info.point+'" type="hidden" />' +
                    '<div class="qianggou_num">' +
                    '<div class="f_l" style="margin-top:5px">数量：</div>' +
                    '<div class="quantity">' +
                    '<a id="decrement" class="decrement" onclick="numdel()">-</a>' +
                    '<input name="number" id="number" class="itxt" value="1" type="text">' +
                    '<a id="increment" class="increment" onclick="numadd()">+</a></div></div>' +
                    '<div class="all_price font-16 color_zhuti">共<span id="price">'+data.game_info.point+'</span>点</div>' +
                    '<div class="qianggou_password_box">' +
                    '<input type="password" name="password" class="qianggou_password" placeholder="请输入聚优密码"">' +
                    '<button id="">确定</button></div></form>' +
                    '</div></div>' +
                    '<div class="yigou_name">' +
                    '<div class="f_l">已购号：</div>' +
                    '<div class="f_l yigouhao_all">';
                var partinfo = data.part_info;
                if(partinfo != "false"){
                    for(var i=0;i<partinfo.length;i++){
                        html += '<span class="yigouhao">'+partinfo[i].lottery_num+'</span>';
                    }
                }else{
                    html += '<span class="yigouhao">您没有抢购！</span>';
                }
                html += '</div></div></div>';
                layer.open({
                    type: 1,
                    title:false,
                    area:['570px','415px'],
                    shadeClose: false, //点击遮罩关闭
                    content:html,

                })
                 //		弹窗滚动条美化
                $(".layui-layer-content").niceScroll({
                    cursorcolor:"#BFB1B1",
                    cursoropacitymax:1,
                    touchbehavior:false,
                    cursorwidth:"5px",
                    cursorborder:"0",
                    cursorborderradius:"5px"
                });
            }
        })
	});

    var submitStatus = true;
// $(document).on('click','.qianggou_box button',function(){
$(document).on('submit','#purchase',function(){
    // var user_id = $('input[name="user_id"]').val();
    // var game_id = $('input[name="game_id"]').val();
    // var card_num = $('input[name="card_num"]').val();
    // var company_id = $('input[name="company_id"]').val();
    // var number = $('input[name="number"]').val();
    // var password = $('input[name="password"]').val();
    var index;
    if(submitStatus==true) {
        submitStatus = false;
        $.ajax({
            type: 'post',
            url: api_url + 'index.php/Games/GamesApi/purchase',
            data: $('#purchase').serialize(),
            dataType: 'json',
            beforeSend: function () {
                index = layer.load();
            },
            success: function (data) {
                console.log(data);
                layer.close(index);
                if (data.result == 'true') {
                    layer.alert(data.msg, function () {
                        location.reload();
                    });
                } else {
                    layer.alert(data.msg, function () {
                        location.reload();
                    });
                }
            }
        });
    }
    return false;
});
//回车提交
// $(document).delegate('.qianggou_password','keydown',function(e){
//     var curKey=e.which;
//     if(curKey==13){
//         $('.qianggou_box button').click();
//         return false;
//     }
// });

//	已结束
	$('.scroll_msg').on('click','.end',function(){
        var id = $(this).parent('a').attr('data-id');
        var cid = $(this).parent('a').attr('data-cid');
        var uid = $('#user_id').val();
        $.ajax({
            type: 'post',
            url: api_url + 'index.php/Games/GamesApi/gameWinner',
            data: {game_id: id,user_id:uid,company_id:cid},
            dataType: 'json',
            success: function (data) {
                //console.log(data);
                var html = '<div class="old_winBox"><h3>已结束</h3>' +
                '<div class="old_win_item">' +
                '<div class="all_duobao">' +
                '<div class="old_win_content">' +
                '<div class="old_win_userimg f_l">' +
                '<img src="'+data.user_img+'">' +
                '</div><div class="f_l old_win_msg">' +
                '<div class="old_win_qihao">期号<span>'+data.issue+'</span></div>' +
                '<div class="old_win_name">恭喜&nbsp;<span class="old_win_username">'+data.user_name+'</span>&nbsp;获得本期商品</div>' +
                '<div>中奖号码：<span>'+data.lottery+'</span></div>' +
                '<div>用户卡号：<span>'+data.card_num+'</span></div>' +
                '<div>本期参与：<span class="color_zhuti">'+data.peo_count+'人次</span></div>' +
                '</div>' +
                '<div class="old_win_img f_l"><img src="'+api_url+"Public/games/upload/"+data.thumbnail+'"></div></div></div>' +
                '</div></div>'+
                '<div class="yigou_name">' +
                '<div class="f_l">已购号：</div>' +
                '<div class="f_l yigouhao_all">';
                var partinfo = data.part_info;
                if(partinfo != "false"){
                    for(var i=0;i<partinfo.length;i++){
                        html += '<span class="yigouhao">'+partinfo[i].lottery_num+'</span>';
                    }
                }
                html+='</div></div>';
                layer.open({
                    type: 1,
                    title:false,
                    area:['570px','415px'],
                    shadeClose: false, //点击遮罩关闭
                    content:html,
                })
                //		弹窗滚动条美化
                $(".layui-layer-content").niceScroll({
                    cursorcolor:"#BFB1B1",
                    cursoropacitymax:1,
                    touchbehavior:false,
                    cursorwidth:"5px",
                    cursorborder:"0",
                    cursorborderradius:"5px"
                });

            },
        });

	});
});
function numdel(){
    var num = $("#number").val();
    var pricespan = $("#price");
    var point = $('#point').val();

    var n = parseInt(num);
    if(n-1<=0){
        num = 1;
    }else{
        num = n - 1;
    }
    $("#number").val(num);
    var price = parseInt(num)*parseInt(point);
    pricespan.html(price);
}
function numadd(){
    var num = $("#number").val();
    var pricespan = $("#price");
    var point = $('#point').val();
    var game_id = $('input[name="game_id"]').val();
    var company_id = $('input[name="company_id"]').val();
    var surplus = checkSurplus(game_id,company_id);
    var n = parseInt(num);
    if(n>=surplus){
        num = surplus;
        alert(num);
        layer.msg('储量不够了，亲！！！');
    }else{
        num = n+1
    }
    $("#number").val(num);
    var price = parseInt(num)*parseInt(point);
    pricespan.html(price);
}
/**
 * 点选可选属性或改变数量时修改商品价格的函数
 */
function checkSurplus(game_id,company_id){
    var surplus;
    $.ajax({
        type:'post',
        url:api_url + 'index.php/Games/GamesApi/getSurplus',
        async:false,
        data:{game_id:game_id,company_id:company_id},
        dataType:'json',
        success:function(data){
            surplus = data;
        }
    });
    return surplus;
}
