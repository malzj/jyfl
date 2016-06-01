<!DOCTYPE html>
<html class="login">
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
    <meta charset="UTF-8">
    <title>用户中心</title>
    <link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/reset.css">
    <link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/public.css">
     <link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/login_layer.css">
    <script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.min.js"></script>	
   <?php echo $this->smarty_insert_scripts(array('files'=>'utils.js,jquery.region.js,jquery.shoppingflow.js')); ?>
        
</head>
<body>
    <div class="content">
    	
		<?php echo $this->fetch('library/page_header.lbi'); ?>
                
   
        <div class="wrap" style="background: url(./hy/images/C-_Users_user_Desktop_01.png) no-repeat;background-size: 100% 100%;">
		</div>	
        
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
        
        
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
        
    </div>
</body>
</html>

