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
        
        
        <script type="text/javascript">
			<?php $_from = $this->_var['lang']['password_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
			var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</script>
        <form id="formCart" name="formCart" method="post" action="flow.php">
        <div class="w_1200">
			<div class="buy_car_tips"></div>
			<div class="cake_tips"><a href="user.php" class="color_zhuti next_buy">继续购物>></a></div>
			<div class="buy_car_list">
                <div class="burcar_all">                
                    <div class="f_l burcar_all1" style="width:710px;">商品信息</div>
                    <div class="f_l burcar_all2">单价(点)</div>
                    <div class="f_l burcar_all3">购买数量</div>         
                    <div class="f_l burcar_all5">合计(点)</div>
                    <div class="f_l burcar_all6">操作</div>
                </div>
			</div>
            <?php if ($this->_var['goods_list']): ?>
			
			<div class="gyshang_all">
            	<?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'supplier');$this->_foreach['supplier'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['supplier']['total'] > 0):
    foreach ($_from AS $this->_var['supplier']):
        $this->_foreach['supplier']['iteration']++;
?>
				<div class="gyshang_item">
					
					<div class="gyshang_title">
						<span><?php echo $this->_var['supplier']['supplier_name']; ?></span>
					</div>
					
                    <?php $_from = $this->_var['supplier']['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
					<div class="gyshang_form">
						<div class="f_l check_item">
							<!--<input type="checkbox" name="checkbox_1">-->
                            <input type="hidden" name="sel_cartgoods[]" value="<?php echo $this->_var['goods']['rec_id']; ?>" class="yi_checkbox" />
						</div>
						<div class="f_l burcar_all1">
							<a href="goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>" class="p_img">
								<img src="<?php echo $this->_var['goods']['goods_thumb']; ?>" alt="<?php echo htmlspecialchars($this->_var['goods']['goods_name']); ?>" >
							</a>		
							<a href="goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>" class="p_details f_l"><?php echo $this->_var['goods']['goods_name']; ?></a><br/>
							<span><?php echo $this->_var['goods']['goods_attr']; ?> </span>
						</div>
						<div class="f_l burcar_all2"><span class="line_h"><?php echo $this->_var['goods']['goods_price']; ?></span></div>
						<div class="f_l burcar_all3">
							<div class="quantity">
								<a id="decrement" class="decrement" onclick="changeNumber('<?php echo $this->_var['goods']['rec_id']; ?>')">-</a>
								<input name="goods_number[<?php echo $this->_var['goods']['rec_id']; ?>]" id="goods_number_<?php echo $this->_var['goods']['rec_id']; ?>" value="<?php echo $this->_var['goods']['goods_number']; ?>" class="itxt" value="1" type="text">
								<a id="increment" class="increment" onclick="changeNumber('<?php echo $this->_var['goods']['rec_id']; ?>', 1)">+</a>				
							</div>
						</div>						
						<div class="f_l burcar_all5"><span class="line_h"><?php echo $this->_var['goods']['subtotal']; ?></span></div>
						<div class="f_l burcar_all6"><span class="line_h"><a href="javascript:if (confirm('<?php echo $this->_var['lang']['drop_goods_confirm']; ?>')) location.href='flow.php?step=drop_to_collect&amp;id=<?php echo $this->_var['goods']['rec_id']; ?>'">删除</a></span></div>
					</div>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>				
				</div>	
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                
                
			</div>
			<div class="buy_car_bottom o_hidden">
				<div class="buy_car_remove f_l"><a href="javascript:;" class="color_zhuti" onclick="location.href='flow.php?step=clear'">全部删除</a></div>
				<div class="f_r buy_car_price">
					<span>商品总金额（不含运费）：</span>
					<span class="color_zhuti font-16"><?php echo $this->_var['shopping_money']; ?></span>点
				</div>	
			</div>
			<input type="button" value="立即结算" class="buy_car_jiesuan f_r zhuti_a_hover" onclick="location.href='flow.php?step=checkout'">
            <input type="hidden" name="step" value="update_cart" />
            <input type="submit" name="updateCartSub" id="updateCartSub" value="<?php echo $this->_var['lang']['update_cart']; ?>" style="display:none;" />
            <?php else: ?>    
            <div class="buy_car_none o_hidden">
            	<div class="f_l buy_car_img">
            		<img src="/images/juyoufuli/img_login/buy_car_no.png">
            	</div>
            	<div class="f_l buy_car_content">
            		<div class="buy_car_font">您的购物车还是空的，赶紧行动吧！</div>
            		<div class="buy_car_btn"><a href="#" class="bg_color zhuti_a_hover">去购物</a></div>
            	</div>
            </div>
            <?php endif; ?>
		</div>
        </form>

        
	 	<?php echo $this->fetch('library/page_footer.lbi'); ?>
	    
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
        
        <script type="text/javascript">
		<!--
		
			
			function drop_allgoods() {
				if(window.confirm('确定删除选中商品吗？')){
					document.formCart.action='flow.php?step=drop_allgoods';
					document.formCart.submit();
					return true;
				}else{
					return false;
				}
				return false;
			}
			function changeNumber(id, state){
				var number = document.getElementById('goods_number_'+id);
				if(isNaN(number.value)){
					alert('请输入数字');
					return false;
				}
				if(state == 1){
					numbers = parseInt(number.value) + 1;
				}else if(state == 2){
					numbers = parseInt(number.value);
				}else{
					numbers = parseInt(number.value) - 1;
				}
				if(numbers < 1 || number.value == ''){
					numbers = 1;
				}
				number.value = numbers;
				document.getElementById('updateCartSub').click();
			}
		//-->
		</script>
    </body>
</html>