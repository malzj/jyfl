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
							<div>
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
							<div>
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
							<div>
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
							<div>
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
							<div>
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
							<div>
		                        <span class="jindu"><span class="jindu1"></span></span>
		                        <span style="float: left;">30%</span>
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
		</div>
		<div class="pop_right_bottom">
	    	<div class="f_l pop_right_bottombtn"><a href="#" id="guize">游戏规则</a></div>
	    	<div class="f_l pop_right_bottombtn"><a href="#" id="xieyi">服务协议</a></div>
	    	<div class="f_l pop_right_bottombtn"><a href="#" id="old_win">往期中奖</a></div>
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
				}	
			})
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
