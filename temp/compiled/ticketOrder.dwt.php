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
         
        
       <div class="w_1200">
			<div class="sport6_tip margin_top_90"></div>
            <form id="formCart" name="formCart" method="post" action="ticket_order.php" onsubmit="return checkSubmit();">
			<div class="sport6_main">            
				<div class="color_or margin_15"><?php echo $this->_var['validity']; ?>。</div>
				<table class="table sport6_table sportAll_table">
					<thead>
						<tr>
							<td class="text_left margin_left_100">产品名称</td>
							<td>购买数量</td>
							<td>价格</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="text_left"><?php echo $this->_var['detail']['productName']; ?></td>
							<td>
								<div class="quantity">
									<a id="decrement" class="decrement" onclick="changeNumber()">-</a>
									<input name="goods_number" id="goods_number" class="itxt" value="<?php if ($this->_var['detail']['startNum'] == 0): ?>1<?php else: ?><?php echo $this->_var['detail']['startNum']; ?><?php endif; ?>" type="text" >
									<a id="increment" class="increment" onclick="changeNumber(1)">+</a>				
								</div>
							</td>
							<td><?php echo $this->_var['detail']['salePrice']; ?></td>
						</tr>
					</tbody>
				</table>
				<div class="sport6_xinxi">
					<div class="f_r sport6_dianshu">
						产品总价：<span class="color_zhuti font_24 dongPrice">0</span>点
					</div>
					<div class="f_r">
						数量：<span class="color_zhuti font_24 dongNum"><?php if ($this->_var['detail']['startNum'] == 0): ?>1<?php else: ?><?php echo $this->_var['detail']['startNum']; ?><?php endif; ?></span>
					</div>
				</div>
				<div class="sport6_tips">游客资料录入（因为游客资料错误导致无法入场，网站不承担责任）</div>
				<div class="sport6——shuru">
                
                <?php $_from = $this->_var['fields']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'field');if (count($_from)):
    foreach ($_from AS $this->_var['field']):
?>
                	<?php if ($this->_var['field']['link'] == 'link_credit_type'): ?>
                    <div class="yourName">
                    	<?php echo $this->_var['field']['name']; ?>：&nbsp;&nbsp;&nbsp;
                    	<select name="links[<?php echo $this->_var['field']['link']; ?>]" style="border:1px #ccc solid; padding:3px;">
                        	<?php $_from = $this->_var['field']['selects']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'select');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['select']):
?>
                            	<option value="<?php echo $this->_var['key']; ?>"><?php echo $this->_var['select']; ?></option>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </select>
                    </div>
                    <?php else: ?>
                	<div class="yourName"><?php echo $this->_var['field']['name']; ?>：&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="links[<?php echo $this->_var['field']['link']; ?>]" class="input_def"/> <font><?php echo $this->_var['field']['tip']; ?></font></div>
                	<?php endif; ?>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>                
				</div>
			</div>
			<div class="sport6_btn">				
                <input type="submit" value="立即结算" class="sport6_liji"/>	
                <input type="hidden" name="step" value="done"/>
                <input type="hidden" name="productno" value="<?php echo $this->_var['detail']['productNo']; ?>" />
                <input type="hidden" name="traveldate" value="<?php echo $this->_var['travelDate']; ?>" />		
                <input type="hidden" name="step" value="done" />
			</div>
            </form>
		</div>
		<script>
		
		<!--
			var cardMoney    = <?php echo $this->_var['usernames']['card_money']; ?>;
			var startNum 	 = <?php echo $this->_var['detail']['startNum']; ?>;
			var maxNum   	 = <?php echo $this->_var['detail']['maxNum']; ?>;
			var salePrice 	 = <?php echo $this->_var['detail']['salePrice']; ?>;	
			var expressPrice = <?php echo $this->_var['expressPrice']; ?>;		
			
			changeTotal();
			
			function changeNumber(state){
				
				var number = document.getElementById('goods_number');
				if(isNaN(number.value)){
					alert('请输入数字');
					return false;
				}
				if(state == 1){
					numbers = parseFloat(number.value) + 1;
				}else if(state == 2){
					numbers = parseFloat(number.value);
				}else{
					numbers = parseFloat(number.value) - 1;
				}
				if(numbers < 1 || number.value == ''){
					numbers = 1;
				}
				
				if(startNum > 0 && numbers < startNum){
					return false;
				}
				if(maxNum > 0 && numbers > startNum){
					return false;
				}
				number.value = numbers;
				changeTotal();
			}
			
			// 价格统计
			function changeTotal(){
				var number 		= parseFloat($('#goods_number').val());
				var expPrice    = parseFloat($('.dongExpress'));
				salePrice       = parseFloat(salePrice);
				if(isNaN(expPrice)){
					expPrice    = 0;
				}
				var total = ((number*salePrice)+expPrice).toFixed(1);
				
				$('.dongNum').text(number);
				$('.dongPrice').text(total);
			}
			
			function checkSubmit(){
				var totalPrice = parseFloat($('.dongPrice').text());
				cardMoney       = parseFloat(cardMoney);
				if(cardMoney < totalPrice ){
					alert('你的卡点数不足以支付当前订单，请选择 '+cardMoney+' 以内的产品！');
					return false;
				}
				var is_sub = true;
				$('.dong-name-phone input').each(function(){
					if($(this).val() == '')
					{
						alert('资料录入不完整，请完善！');
						$(this).focus();
						is_sub = false;
						return false;
					}
				});
				return is_sub;
				//return true;
			}
		//-->		
		</script>
        
        
	 	<?php echo $this->fetch('library/page_footer.lbi'); ?>
	    
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
    </body>
</html>