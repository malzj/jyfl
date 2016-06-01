<!DOCTYPE html>
<html>
	<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
		<meta charset="UTF-8">
		<title></title>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.min.js"></script>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.SuperSlide.2.1.1.source.js"></script>
        
        <?php echo $this->smarty_insert_scripts(array('files'=>'jquery.common.js')); ?>
       
	</head>
	<body class='bg_white'>
        
        <?php echo $this->fetch('library/page_top.lbi'); ?>
        
        
         <style>
        	.order_tijiao_msg .clickEve{background:#ccc!important;}
        </style>
        <div class="w_1200">
            <div class="order_tijiao"></div>
            <div class="order_tijiao_content">
                <div class="order_tijiao_name">订单已提交成功，共计<span class="color_ff7900"><?php echo $this->_var['orders']['order_amount']; ?>点</span></div>
                <img src="<?php echo $this->_var['app_path']; ?>images/juyoufuli/img_login/order_submit_img.png">
                <div class="order_tijiao_msg">
                    <div class="order_tijiao_username">聚优卡号：<span><?php echo $this->_var['usernames']['user_name']; ?></span></div>
                    <div class="order_tijiao_password">聚优卡密码：<input type="password" name="password" id="password" placeholder="请输入密码"></div>
                    <button type="button" class="bg_color zhuti_a_hover" onclick="checkPayForm()">结算</button>
                </div>
            </div>
        </div>
            
		<script type="text/javascript">
		<!--
			var lock = false;
			function checkPayForm(){
				if(lock){					
					return false;
				}
				
				var amount       = <?php echo $this->_var['orders']['order_amount']; ?>;
				var money        = <?php echo $this->_var['usernames']['card_money']; ?>;		
				var order_id     = '<?php echo $this->_var['orders']['order_id']; ?>';		

				var pwd = document.getElementById('password').value;
				if (pwd.length == 0){
					alert('卡密码不能为空');
					return false;
				}
				
				if (amount > money){
					alert('抱歉，卡余额不足，请充值或换一张');
					return false;
				}				
				lock = true;
				// 支付状态
				$('.zhuti_a_hover').addClass('clickEve').html('支付中...');				
				$.post('flow.php', {step: 'act_pay', password:pwd, 'order_id':order_id}, function (result){
					if (result.error > 0){
						alert(result.message);
						lock = false;
						$('.zhuti_a_hover').removeClass('clickEve').html('提交');	
					}else{
						location.href="flow.php?step=respond";
					}
				}, 'json');
			}
		//-->
		</script>
        
        
	 	<?php echo $this->fetch('library/page_footer.lbi'); ?>
	    
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
    </body>
</html>