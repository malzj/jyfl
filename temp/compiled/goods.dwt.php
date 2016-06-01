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
        
        
        <div class="w_1200">
			<div class="sport4_top o_hidden margin_top_90">
				<div class="sport4_top_left f_l">
					<div class="cake2_img">                    	
						<img src="<?php echo $this->_var['goods']['goods_img']; ?>" alt="<?php echo $this->_var['goods']['goods_name']; ?>" />											
					</div>
					<div class="sport4_list">
						<div class="hd">
							<span class="next"></span>
							<span class="prev"></span>
						</div>
						<div class="bd">
							<ul class="sport4_img_item">
                            <?php $_from = $this->_var['pictures']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'picture');$this->_foreach['picture'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['picture']['total'] > 0):
    foreach ($_from AS $this->_var['picture']):
        $this->_foreach['picture']['iteration']++;
?>
                            <li class="f_l">
                            	<img src="<?php echo $this->_var['picture']['img_url']; ?>" alt="<?php echo $this->_var['goods']['goods_name']; ?>" width="90" />
                            </li>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							</ul>
						</div>
					</div>
				</div>
                <form action="javascript:addToCart(<?php echo $this->_var['goods']['goods_id']; ?>)" method="post" name="ECS_FORMBUY" id="ECS_FORMBUY" >
				<div class="cake2_top_right f_l">
					<ul>
						<li class="sport4_title"><?php echo $this->_var['goods']['goods_style_name']; ?></li>
						<li class="sport4_pri">价格：<span class="sport4_dianshu" id="ECS_GOODS_AMOUNT">0</span>点</li>
                        
                        <?php if (! empty ( $this->_var['specs'] )): ?>
						<li class="sport4_mar">
							<div class="f_l cake_top_name">规格：</div>
							<div class="cake2_top_item f_l">
                            	<?php $_from = $this->_var['specs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('specno_key', 'specno');$this->_foreach['specno'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['specno']['total'] > 0):
    foreach ($_from AS $this->_var['specno_key'] => $this->_var['specno']):
        $this->_foreach['specno']['iteration']++;
?>								
                                <a href="javascript:;"<?php if ($this->_var['specno_key'] == 0): ?> class="active"<?php endif; ?> onclick="changeAttr(this, 'S_<?php echo $this->_var['specno']['spec_nember']; ?>');"><?php echo $this->_var['specno']['spec_name']; ?></a>
                                <input type="radio" name="spec_100}" value="S_<?php echo $this->_var['specno']['spec_nember']; ?>" id="spec_value_S_<?php echo $this->_var['specno']['spec_nember']; ?>" <?php if ($this->_var['specno_key'] == 0): ?>checked<?php endif; ?> onclick="changePrice()" style="display:none;" />
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							</div>	
						</li>
                        <?php endif; ?>
                        
                        <?php $_from = $this->_var['specification']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('spec_key', 'spec');$this->_foreach['spec'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['spec']['total'] > 0):
    foreach ($_from AS $this->_var['spec_key'] => $this->_var['spec']):
        $this->_foreach['spec']['iteration']++;
?>
						<li class="sport4_mar">
							<div class="f_l cake_top_name"><?php echo $this->_var['spec']['name']; ?>：</div>
                            <?php if ($this->_var['spec']['attr_type'] == 1): ?>
							<div class="cake2_top_item f_l">
                            	<?php if ($this->_var['cfg']['goodsattr_style'] == 1): ?>
                                <?php $_from = $this->_var['spec']['values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'value');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['value']):
?>
								<a href="javascript:;"<?php if ($this->_var['key'] == 0): ?> class="active"<?php endif; ?> onclick="changeAttr(this, '<?php echo $this->_var['value']['id']; ?>');"><?php echo $this->_var['value']['label']; ?></a>
								<input type="radio" name="spec_<?php echo $this->_var['spec_key']; ?>" value="<?php echo $this->_var['value']['id']; ?>" id="spec_value_<?php echo $this->_var['value']['id']; ?>" <?php if ($this->_var['key'] == 0): ?>checked<?php endif; ?> onclick="changePrice()" style="display:none;" />
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                <input type="hidden" name="spec_list" value="<?php echo $this->_var['key']; ?>" />
                                <?php endif; ?>
							</div>	
                            <?php endif; ?>
						</li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						
						<li class="sport4_mar">
							<div class="f_l cake_top_name" style="margin-top: 5px;">购买数量：</div>
							<div class="quantity">
									<a id="decrement" class="decrement" onclick="del()">-</a>
                                    <input type="text" value="1" name="number" id="number" class="itxt" onblur="checkNum(this.value);" />
									<a id="increment" class="increment" onclick="add()">+</a>				
							</div>
						<li>
                            <input class="sport4_btn input_submit bg_color btn zhuti_a_hover" type="button" onclick="addToCart(<?php echo $this->_var['goods']['goods_id']; ?>,'',5)" value="立即购买"> 
                            <button type="submit" class="sport4_btn input_submit bg_color btn zhuti_a_hover" value="加入购物车">
                            <span class="glyphicon glyphicon-shopping-cart"></span>加入购物车</button>
                        </li>
					</ul>
				</div>
                </form>
			</div>
			<div class="cake2_top_tips color_zhuti"><?php echo $this->_var['goods']['goods_brief']; ?></div>
			<div class="cake2_shangpin_details"><span class="color_zhuti">商品详情</span></div>
                <ul class="cake_jieshao">
                	<?php $_from = $this->_var['properties']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'property_group');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['property_group']):
?>
					<?php $_from = $this->_var['property_group']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'property');if (count($_from)):
    foreach ($_from AS $this->_var['property']):
?>
                    <li class="f_l juqingA"><?php echo htmlspecialchars($this->_var['property']['name']); ?>：<span><?php echo $this->_var['property']['value']; ?></span></li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>                    
                </ul>
                
                <div class="details">
                	<?php echo $this->_var['goods']['goods_desc']; ?>
                </div>
			
			</div>
            
        
        
	 	<?php echo $this->fetch('library/page_footer.lbi'); ?>
	    
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
        
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
//			点击图片切换
			$('.sport4_img_item li img').click(function() {
				var img = $(this).attr('src');
				$('.cake2_img img').attr('src', img);
			})
//			选中样式
			/*$('.cake2_top_item a').click(function(){
				$(this).addClass('active').siblings().removeClass('active');
			})*/
			
			var goods_id = <?php echo $this->_var['goods_id']; ?>;
			var goodsattr_style = <?php echo empty($this->_var['cfg']['goodsattr_style']) ? '1' : $this->_var['cfg']['goodsattr_style']; ?>;
			var gmt_end_time = <?php echo empty($this->_var['promote_end_time']) ? '0' : $this->_var['promote_end_time']; ?>;
			<?php $_from = $this->_var['lang']['goods_js']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
			var <?php echo $this->_var['key']; ?> = "<?php echo $this->_var['item']; ?>";
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			var goodsId = <?php echo $this->_var['goods_id']; ?>;
			var now_time = <?php echo $this->_var['now_time']; ?>;
	
			onload = function(){
				changePrice();	
			}
			
			//设置选中规格的样式
			function changeAttr(obj, id){
				var child = obj.parentNode.children;
				for (var i = 0; i<child.length;i++) {
					if (child[i]){
						if (child[i].className == 'active'){
							child[i].className = '';
							break;
						}
					}
				}
				obj.className = "active";
				document.getElementById('spec_value_'+id).click();
			}
			
			/**
			 * 点选可选属性或改变数量时修改商品价格的函数
			 */
			function changePrice(){
				var attr = getSelectedAttributes(document.forms['ECS_FORMBUY']);	
				var qty = document.forms['ECS_FORMBUY'].elements['number'].value;	
				jQuery.get("goods.php",{act:"price", id:goods_id, attr:attr, number:qty}, function(data){
					changePriceResponse(data);
				}, 'json');
			}
			
			/**
			 * 接收返回的信息
			 */
			function changePriceResponse(res)
			{
				if (res.err_msg.length > 0)
				{
					alert(res.err_msg);
				}
				else
				{
					document.forms['ECS_FORMBUY'].elements['number'].value = res.qty;
					if (document.getElementById('ECS_GOODS_AMOUNT'))
						document.getElementById('ECS_GOODS_AMOUNT').innerHTML = res.result;
				}
			}
			
			function del(){
				var num = document.getElementById("number");
				var n = parseInt(num.value);
				if(n-1<=0){
					num.value = 1;
				}else{
					num.value = n-1;
				}
				changePrice();
			}
			function add(){
				var num = document.getElementById("number");
				var n = parseInt(num.value);
				num.value = n+1;
				changePrice();
			}
			function checkNum(num){
				changePrice();
			}

		</script>
        
    </body>
</html>