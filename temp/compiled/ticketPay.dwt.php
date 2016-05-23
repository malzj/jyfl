<!DOCTYPE html>
<html class="huaju3">
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
			<div class="yanchu_tip1">
				
			</div>
			<div class="dingdan_tip">
				<div class="tips">订单已提交成功，共计<span><?php echo $this->_var['list']['money']; ?>点</span> 请尽快付款！</div>
				<table class="table">
					<thead>
						<tr>
							<td>订单编号</td>
							<td>商品数量</td>
							<td>产品单价</td>
                            <td>有效期</td>
							<td>订单状态</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo $this->_var['list']['api_order_id']; ?></td>
							<td><?php echo $this->_var['list']['num']; ?></td>
							<td><?php echo $this->_var['list']['price']; ?>点</td>
                            <td><?php echo $this->_var['validit']; ?></td>
							<td><?php echo $this->_var['list']['start']; ?></td>
						</tr>						
					</tbody>
				</table>
				<div class="jiesuan">
					<div class="kahao f_l">
						<span>聚优卡号：</span>
						<span><?php echo $this->_var['usernames']['user_name']; ?></span>
					</div>
					<div class="password f_l">
						<span>聚优卡密码</span>
						<input type="password" name="password" id="password" placeholder="请输入密码">
					</div>
					<div class="jiesuan_btn f_l" onclick="checkPayForm()" >
						<span>结算</span>
					</div>
				</div>
				<!--<div style="color:#ff781e;">温馨提示：<?php echo $this->_var['list']['orderPolicy']; ?>。</div>-->
			</div>
		</div>
        
        
        
	 	<?php echo $this->fetch('library/page_footer.lbi'); ?>
	    
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
        
        <script>
        	var checkSubmitFlg = false;
			
			function checkPayForm(){	

				if(checkSubmitFlg == true){ 
					alert('已提交过，请耐心等待');
					return false;
				}
				
				var amount       = <?php echo $this->_var['list']['money']; ?>;
				var money        = <?php echo $this->_var['usernames']['card_money']; ?>;
				var order_id     = '<?php echo $this->_var['list']['id']; ?>';

				var pwd = document.getElementById('password').value;
				if (pwd.length == 0){
					alert('卡密码不能为空');
					return false;
				}
				
				var index2 = layer.open({
						type: 1,
						title: false,
						content: '<div style="width:100%; height:200px"><h2 style="font-size:20px; text-align:center; height:40px; line-height:40px; border-bottom:1px #ccc solid; color:red">温馨提示！</h2><div style="padding:15px; text-indent:2em; line-height:40px; font-size:16px; color:red;">注意：尊敬的用户，您好！因部分项目需提前预约，为保证您的正常使用，请认真查阅短信。<br >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;咨询请致电：400-010-0689</div></div>',
						btn:['确认','返回'],
						area:['400px','250px'],
						yes:function(){		
							layer.close(index2);		
							var index = layer.load(2,{shade:[0.3,'#393D49']}); 
							$.post('ticket_order.php', {step: 'pay', password:pwd, 'order_id':order_id, 'order_amount':amount}, function (result){
								if (result.error > 0){
									alert(result.message);
									layer.close(index) 
								}else{
									location.href="ticket_order.php?step=respond";
								}
							}, 'json');
						},						
					});
				
			}
        </script>
    </body>
</html>