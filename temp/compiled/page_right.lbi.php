<div class="pop_right">
			<div class="right"><span class="glyphicon glyphicon-chevron-left"></span></div>
			<div class="message">
				<div class="border_radio">
					<img src="/images/juyoufuli/img_login/02.png" alt="可口可乐" width="80%">
				</div>	
				<div class="text-center">可口可乐有限公司</div>
			</div>
			
			<div class="scroll_msg">
			<div class="act_title"></div>	
			<div class="message_1">
				<div class="message_1_content">
					<a href="#" class="pop_right_goods">
					<div class="act_item1 f_l">
						<img src="/images/juyoufuli/img_login/pop_right_bottombtn1.png">
					</div>
					<div class="act_item2 f_l">
							<div>iPhone6</div>
							<div>共1000点</div>
							<div class="pop_right_jindutiao">
		                        <span class="jindu"><span class="jindu1"></span></span>
		                        <span style="float: left;">30%</span>
		                	</div>
					</div>
					</a>
					<div class="act_item3 f_l">
						<a href="#" class="jinbi">
							<img src="/images/juyoufuli/img_login/jieshu.png" class="end">
						</a>
					</div>
				</div>
			</div>	
			<div class="message_1">
				<div class="message_1_content">
					<a href="#" class="pop_right_goods">
					<div class="act_item1 f_l">
						<img src="/images/juyoufuli/img_login/pop_right_bottombtn1.png">
					</div>
					<div class="act_item2 f_l">
							<div>iPhone6</div>
							<div>共1000点</div>
							<div class="pop_right_jindutiao">
		                        <span class="jindu"><span class="jindu1"></span></span>
		                        <span style="float: left">30%</span>
		                	</div>
					</div>
					</a>
					<div class="act_item3 f_l">
						<a href="#" class="jinbi">
							<img src="/images/juyoufuli/img_login/duo.png" class="duo">
						</a>
					</div>
				</div>
			</div>	
			
			<div class="act_title1"></div>
			<div class="message_1">
				<div class="message_1_content">
					<a href="#" class="pop_right_goods">
					<div class="act_item1 f_l">
						<img src="/images/juyoufuli/img_login/pop_right_bottombtn1.png">
					</div>
					<div class="act_item2 f_l">
							<div>iPhone6</div>
							<div>共1000点</div>
							<div class="pop_right_jindutiao">
		                        <span class="jindu"><span class="jindu1"></span></span>
		                        <span style="float: left;">30%</span>
		                	</div>
					</div>
					</a>
					<div class="act_item3 f_l">
						<a href="#" class="jinbi">
							<img src="/images/juyoufuli/img_login/jieshu.png" class="end">
						</a>
					</div>
				</div>
			</div>	
			<div class="message_1">
				<div class="message_1_content">
					<a href="#" class="pop_right_goods">
					<div class="act_item1 f_l">
						<img src="/images/juyoufuli/img_login/pop_right_bottombtn1.png">
					</div>
					<div class="act_item2 f_l">
							<div>iPhone6</div>
							<div>共1000点</div>
							<div class="pop_right_jindutiao">
		                        <span class="jindu"><span class="jindu1"></span></span>
		                        <span style="float: left;">30%</span>
		                	</div>
					</div>
					</a>
					<div class="act_item3 f_l">
						<a href="#" class="jinbi">
							<img src="/images/juyoufuli/img_login/jieshu.png" class="end">
						</a>
					</div>
				</div>
			</div>	
			<div class="message_1">
				<div class="message_1_content">
					<a href="#" class="pop_right_goods">
					<div class="act_item1 f_l">
						<img src="/images/juyoufuli/img_login/pop_right_bottombtn1.png">
					</div>
					<div class="act_item2 f_l">
							<div>iPhone6</div>
							<div>共1000点</div>
							<div class="pop_right_jindutiao">
		                        <span class="jindu"><span class="jindu1"></span></span>
		                        <span style="float: left;">30%</span>
		                	</div>
					</div>
					</a>
					<div class="act_item3 f_l">
						<a href="#" class="jinbi">
							<img src="/images/juyoufuli/img_login/jieshu.png" class="end">
						</a>
					</div>
				</div>
			</div>	
		</div>
		<div class="pop_right_bottom">
	    	<div class="f_l pop_right_bottombtn"><a href="#" id="guize">游戏规则</a></div>
	    	<div class="f_l pop_right_bottombtn"><a href="#" id="xieyi">服务协议</a></div>
	    	<div class="f_l pop_right_bottombtn"><a href="#" id="old_win" data_uid='<?php echo $this->_var['usernames']['user_id']; ?>'>往期中奖</a></div>
	    </div>
		</div>
<script>
		$('#per').click(function(){
			var user_id = $('#user_id').val();
			var data =userShow(user_id);
		});
		</script>
		<script>
		$(document).on('click','#save',function(){
			var user_id=$('#card_id').text();
			var data = userSave();
//			console.log(data);
		});
		
		</script>
		<script>
		
		$(function(){
            var id = $('#user_id').val();
            var cnum = <?php echo $this->_var['usernames']['user_name']; ?>;

            showRight(id,cnum);

			if(window.location.pathname == '/user.php'){
				$('.pop_right .right span').removeClass('glyphicon-chevron-left').addClass('glyphicon-chevron-right');
			}
			$('.pop_right .right span').click(function(){						
				if($(this).hasClass('glyphicon-chevron-right')){
					$('.pop_right').css('transform','translateX(100%)');
					$(this).removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-left');
				}else{
					$('.pop_right').css('transform','translateX(0)');
					$(this).removeClass('glyphicon-chevron-left').addClass('glyphicon-chevron-right');
					showRight(id,cnum);
				}
			})

            // 右侧渲染函数
				function showRight(id,cnum){
            		//var api_url = 'http://jy.com/jyflapi/';
            		var api_url = 'http://192.168.1.161/jyflapi/';
            		$.ajax({
            			type:'post',
            			url:api_url+'index.php/Games/GamesApi/gameList',
            			data:{
            				user_id:id,
            			},
            			dataType:'json',
            			success:function(data){
            				console.log(data);
            				var game_glo = data.game_global;
            				var game_com = data.game_company;

            				var game_list_html = '<div class="act_title"></div>';
            				for(var i=0;i<game_glo.length;i++){
                                var img = '';
                                var iclass = '';
                                if(game_glo[i].buy_status==0){
                                    img = "duo.png";
                                    iclass = 'class="duo"';
                                }else{
                                    img = "jieshu.png";
                                    iclass = 'class="end"';
                                }
            					game_list_html += '<div class="message_1">'+
            						'<div class="message_1_content">'+
            						'<a href="#" class="pop_right_goods" data-id="'+game_glo[i].id+'" data-cid="'+data.company_info.card_company_id+'">'+
            						'<div class="act_item1 f_l">'+
            						'<img src="'+api_url+'Public/games/upload/'+game_glo[i].thumbnail+'">'+
            						'</div>'+
            						'<div class="act_item2 f_l">'+
            						'<div>'+game_glo[i].game_name+'</div>'+
            						'<div>共'+game_glo[i].total_point+'点</div>'+
            						'<div>'+
            						'<span class="jindu"><span class="jindu1" style="width:'+game_glo[i].percent+'px"></span></span>'+
            						'<span style="float: left;">'+game_glo[i].percent+'%</span>'+
            						'</div>'+
            						'</div></a>'+
            						'<div class="act_item3 f_l">'+
            						'<a href="#" class="jinbi" data-id="'+game_glo[i].id+'" data-cid="'+data.company_info.card_company_id+'" data-cnum="'+cnum+'">'+
            						'<img src="/images/juyoufuli/img_login/'+img+'" '+iclass+'>'+
            						'</a>'+
            						'</div>'+
            						'</div>'+
            						'</div>';
            				}
            				game_list_html+='<div class="act_title1"></div>';
            				for(var i=0;i<game_com.length;i++){
                                var img1 = '';
                                var iclass1 = '';
                                if(game_com[i].buy_status==0){
                                    img1 = "duo.png";
                                    iclass1 = 'class="duo"';
                                }else{
                                    img1 = "jieshu.png";
                                    iclass1 = 'class="end"';
                                }
            					game_list_html += '<div class="message_1">'+
            						'<div class="message_1_content">'+
            						'<a href="#" class="pop_right_goods" data-id="'+game_com[i].id+'" data-cid="'+data.company_info.card_company_id+'">'+
            						'<div class="act_item1 f_l">'+
            						'<img src="'+api_url+'Public/games/upload/'+game_com[i].thumbnail+'">'+
            						'</div>'+
            						'<div class="act_item2 f_l">'+
            						'<div>'+game_com[i].game_name+'</div>'+
            						'<div>共'+game_com[i].total_point+'点</div>'+
            						'<div>'+
            						'<span class="jindu"><span class="jindu1" style="width:'+game_com[i].percent+'px"></span></span>'+
            						'<span style="float: left;">'+game_com[i].percent+'%</span>'+
            						'</div>'+
            						'</div></a>'+
            						'<div class="act_item3 f_l">'+
            						'<a href="#" class="jinbi" data-id="'+game_com[i].id+'" data-cid="'+data.company_info.card_company_id+'" data-cnum="'+cnum+'">'+
            						'<img src="/images/juyoufuli/img_login/'+img1+'" '+iclass1+'>'+
            						'</a>'+
            						'</div>'+
            						'</div>'+
            						'</div>';
            				}
            				var com_html = '<div class="border_radio">'+
            					'<img src="'+api_url+'Public/company/upload/'+data.company_info.logo_img+'" alt="'+data.company_info.company_name+'" width="80%"></div>'+
            					'<div class="text-center">'+data.company_info.company_name+'</div>';
            				$('.pop_right .message').html(com_html);
            				$('.pop_right .scroll_msg').html(game_list_html);
            				var img_url = "<?php echo $this->_var['app_path']; ?>jyflapi/Public/company/upload/"+data.company_info.back_img;
            				$('#back_img').css("background-image","url("+img_url+")");
            			}
            		});
            	}

		})	
			//		右侧滚动条美化
			$(".scroll_msg").niceScroll({
				cursorcolor:"#BFB1B1",
				cursoropacitymax:1,
				touchbehavior:false,
				cursorwidth:"5px",
				cursorborder:"0",
				cursorborderradius:"5px"
			});
            //		弹窗滚动条美化
            $(".qianggou_box").niceScroll({
                cursorcolor:"#BFB1B1",
                cursoropacitymax:1,
                touchbehavior:false,
                cursorwidth:"5px",
                cursorborder:"0",
                cursorborderradius:"5px"
            });

			$('.pop_left ul.list_main>li').hover(function(){
				$(this).children('div:nth-child(2)').toggle().parents('li').siblings().children('div:nth-child(2)').css('display','none');
					
			})
			$('.pop_left ul.list_main>li').hover(function(){
				$(this).addClass('active').siblings().removeClass('active');
			})
			$('ul.list_1 li div').click(function(){
				$(this).addClass('active').parents('li').siblings().children('div').removeClass('active');
			})
		
			$('.pop_left .per_1 ul.list_2 li').hover(function(){
						$(this).addClass('active').siblings().removeClass('active');
					})	
					
//			地区选择		
				$(document).delegate('#city','click',function (e) {
					SelCity(this,e);
				});
				
			</script>
