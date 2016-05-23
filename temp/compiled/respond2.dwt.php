<!DOCTYPE html>
<html class="movie_4">
	<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
		<meta charset="UTF-8">
		<title></title>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.min.js"></script>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.SuperSlide.2.1.1.source.js"></script>
    </head>
  	<body>
    	 
		<?php echo $this->fetch('library/page_top.lbi'); ?>
         
 
        <div class="w_1200">
			<div class="pay_del">
			</div>
			<div class="pay_success">
				<h3><img src="/images/index/img_login/yes_icon.png">订单支付成功</h3>
				<div class="btn1" style="display:block;"><a href="<?php echo $this->_var['app_path']; ?>" style="margin-right: 20px;">返回首页</a><a href="javascript:;" style="color: #2fd0b5;">我的订单</a></div>
				<div class="tips">
						<ul>
							<li>温馨提示：</li>	
							<li>订单支付成功后，电子码将以短信方式发送到您的手机，请注意查收！</li>							
						</ul>
				</div>
			</div>
		</div>
    
        
        
		<?php echo $this->fetch('library/page_footer.lbi'); ?>
    	
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
    </body>
</html>