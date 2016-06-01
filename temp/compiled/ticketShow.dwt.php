<!DOCTYPE html>
<html>
	<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
		<meta charset="UTF-8">
		<title></title>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.min.js"></script>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.SuperSlide.2.1.1.source.js"></script>
	</head>
	<body>
        
        <?php echo $this->fetch('library/page_top.lbi'); ?>
         
        
        <div class="w_1200" style="margin-top: 90px;" > 
			<div class="sport4_top o_hidden">
				<div class="sport4_top_left f_l">
					<div class="sport_img">
                    	<?php $_from = $this->_var['detail']['imgs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'img');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['img']):
?>
						<img src="<?php echo $this->_var['img']; ?>" alt="<?php echo $this->_var['detail']['productName']; ?>">
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					</div>
					<div class="sport4_list">
						<div class="hd">
							<span class="next"></span>
							<span class="prev"></span>
						</div>
						<div class="bd">
							<ul class="sport4_img_item">
                            	<?php $_from = $this->_var['detail']['imgs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'img');if (count($_from)):
    foreach ($_from AS $this->_var['img']):
?>
								<li class="f_l">
									<img src="<?php echo $this->_var['img']; ?>" alt="<?php echo $this->_var['detail']['productName']; ?>" width="90">
								</li>
								<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							</ul>
						</div>
					</div>
				</div>
				<div class="sport4_top_right f_l">
					<ul>
						<li class="sport4_title"><?php echo $this->_var['detail']['productName']; ?></li>
						<li class="sport4_pri">价格：<span class="sport4_dianshu"><?php echo $this->_var['detail']['salePrice']; ?></span>点</li>
						<li class="sport4_mar">营业时间：<span><?php echo $this->_var['detail']['businessHours']; ?> </span></li>
						<li class="sport4_mar">详细地址：<span><?php echo $this->_var['detail']['viewAddress']; ?></span></li>
						<li><a href="#goumai"><span class="sport4_btn">立即购买</span></a></li>
                        <li id="goumai"></li>
					</ul>
				</div>
			</div>
			<div class="h-inforbox">
				<div class="h-tabletitle" id="calendar" style="color:#F00;"> 
                	
                    
                </div>
				<div class="h-bot">
					 <?php echo $this->_var['detailww']; ?>
				</div>
			</div>
			<div style="clear: both;"></div>
			<div class="sport4_jieshaotit"><span class="sport4_jieshaotitle">产品介绍</span></div>			
			<div>
				 <?php echo $this->_var['detail']['content']; ?>
			</div>
            
		</div>

		<script>
			jQuery(".sport4_list").slide({
				titCell: ".hd ul",
				mainCell: ".bd ul",
				autoPage: true,
				effect: "left",
				autoPlay: false,
				vis: 4,
				pnLoop: false
			});
		</script>
		<script>
			$('.sport4_img_item li img').click(function() {
				var img = $(this).attr('src');
				$('.sport_img img').attr('src', img);
			})
	
			var product = <?php echo $this->_var['detail']['productNo']; ?>;
			var addate = 0;
				loadPrice(0);			
			
			
			function calendar(e){
				if(e==1){
					addate++;
				}else{
					addate--;
				}
				if(addate == 0){
					loadPrice(0);
					
				}	
				var myDate = new Date();
				var year = myDate.getFullYear();
				var month = myDate.getMonth()+1;
				var day = myDate.getDate();
					month = month+addate;
				var str = year+'-'+month+'-'+day;
				loadPrice(str);
			}
			
			function loadPrice(date){
				$.ajax({
					type: "POST",
					url: "ticket_show.php?step=price",
					data:'productno='+product+'&date='+date,
					beforeSend : function(){
						var beforeHtml = "<span style='display:inline-block; margin-left:290px;line-height: 400px;'><img src='<?php echo $this->_var['app_path']; ?>images/loadSeat2.gif'></span>";
						$('#calendar').html(beforeHtml);
					},
					success: function(info){
						$('#calendar').html(info);
					}
				});
			}
			
			$(document).on('click', '#calendar .h-table2 tr td', function(){
				var date = $(this).attr('date');
				if(date != 0){
					 window.location.href='ticket_order.php?step=cart&product='+product+'&date='+date;
				}
			});

		</script>
        
        
	 	<?php echo $this->fetch('library/page_footer.lbi'); ?>
	    
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
    </body>
</html>