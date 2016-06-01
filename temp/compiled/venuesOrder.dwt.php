<!DOCTYPE html>
<html>
	<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
		<meta charset="UTF-8">
		<title></title>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.min.js"></script>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.SuperSlide.2.1.1.source.js"></script>        
        <link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/sport1.css">
        <link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/venues.css">
	</head>
	<body>
        
        <?php echo $this->fetch('library/page_top.lbi'); ?>
        
        
        <div class="w_1200" style="margin-top: 90px;">
			<div class="sport5_tips o_hidden">
				<div class="f_l sport5_title">场馆订购</div>
				<div class="sport5_tip f_l"></div>
			</div>
			<div class="sport5_bottom">
                <div class="sport5_table">
                    <table class="table sportAll_table table-bordered">
                        <thead>
                            <tr>
                                <td>场馆信息</td>
                                <td>用户信息</td>
                                <td>预定信息</td>
                                <td>总售点</td>
                                <td>数量</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="sport_5_td">
                                    <div class="sport5_cg">场馆：<span><?php echo $this->_var['order']['venueName']; ?></span></div>
                                    <div class="sport5_dz">地址：<span><?php echo $this->_var['detail']['place']; ?></span></div>
                                </td>
                                <td class="sport_5_td1">姓名：<?php echo $this->_var['order']['link_man']; ?>（<?php echo $this->_var['order']['link_phone']; ?>）</td>
                                <td class="sport_5_td2"><?php $_from = $this->_var['order']['times_mt']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'time');if (count($_from)):
    foreach ($_from AS $this->_var['time']):
?> <?php echo $this->_var['order']['date']; ?> <?php echo $this->_var['time']; ?><br /><?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>    </td>
                                <td class="sport_5_td3"><?php echo $this->_var['order']['money']; ?>点</td>
                                <td class="sport_5_td4"><?php echo $this->_var['order']['total']; ?>块</td>
                            </tr>                           
                        </tbody>
                    </table>
                </div>
                <div class="pay_password o_hidden">
                	<form action="komovie_seat.php" name="myDoneForm" method="post" onsubmit="return checkPayForm();">		
                    <div class="f_l jy_kahao">聚优卡号：<span><?php echo $this->_var['usernames']['user_name']; ?></span></div>
                    <div class="f_l jy_pass">聚优卡密码：<span><input type="password" name="password" id="password" placeholder="请输入密码"></span></div>
                    <div class="f_l jy_jiesuan">结算</div>
                    <input type="hidden" name="act" value="done" />
                    <input type="hidden" name="order_id" id="order_id" value="<?php echo $this->_var['order']['id']; ?>" />
                    </form>
                </div>
            </div>
		</div>
        
        <script type="text/javascript">
			var checkSubmitFlg = false;
			$('.jy_jiesuan').click(function(){
				var password = $('#password').val();
				var orderid = $('#order_id').val();
				if( password == ''){
					alert('密码不能为空！');
					return false;
				}
				if(checkSubmitFlg == true){ 
					alert('已提交过，请耐心等待');
					return false;
				}
				checkSubmitFlg = true;
				$.ajax({
					type : 'POST',
					url: 'venues_order.php?action=pay',
					data: "password="+password+"&orderid="+orderid,
					success:function(data){
						if(data.error == 1){
							alert(data.message);
							checkSubmitFlg = false;
						}else{
							window.location.href="venues_order.php?action=respond";
						}
					},
					dataType:'json'
				});
			});
		</script>
        
        
	 	<?php echo $this->fetch('library/page_footer.lbi'); ?>
	    
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
     </body>
</html>