<!DOCTYPE html>
<html class="huaju1">
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
        	<form name="ycForm" id="ycForm" method="post" action="yanchu_order.php" onsubmit="return checkYcForm();">
			<div class="huaju_box">
				<div class="img_left f_l">
					<img src="<?php echo $this->_var['iteminfo']['imageUrl']; ?>">
				</div>
				<div class="box_xinxi f_l">
					<ul>
						<li><h3 class="juqingA"><?php echo $this->_var['iteminfo']['itemName']; ?></h3></li>
						<li>
							<div class="f_l changci_title">选择场次</div>
							<div class="changci f_l">
                           	 	<?php $_from = $this->_var['showtime']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'showtime_0_37640000_1463450922');$this->_foreach['showtime'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['showtime']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['showtime_0_37640000_1463450922']):
        $this->_foreach['showtime']['iteration']++;
?>
								<div class="changci_item f_l" onclick="changeAttr(this, '<?php echo $this->_var['key']; ?>', 'time');">
                                    <span><?php echo $this->_var['showtime_0_37640000_1463450922']['monthDay']; ?></span><br>
                                    <span><?php echo $this->_var['showtime_0_37640000_1463450922']['week']; ?> <?php echo $this->_var['showtime_0_37640000_1463450922']['hours']; ?></span>
								</div>
                                <input type="radio" name="time" id="time_<?php echo $this->_var['key']; ?>" value="<?php echo $this->_var['showtime_0_37640000_1463450922']['shEndDate']; ?>" style="display:none;" />
								<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							</div>
                            <div class="more f_l"><span class="glyphicon glyphicon-chevron-down"></span><span class="more_text">更多</span></div>
						</li>
						<li>
							<div class="f_l piaojia_title">选择票价</div>
							<div class="piaojia f_l" id="showTimePrice">
                            
                            	<?php $_from = $this->_var['showtime']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'showtime_0_37651300_1463450922');$this->_foreach['showtime'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['showtime']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['showtime_0_37651300_1463450922']):
        $this->_foreach['showtime']['iteration']++;
?>
								<?php if (($this->_foreach['showtime']['iteration'] <= 1)): ?>
								<?php $_from = $this->_var['showtime_0_37651300_1463450922']['specs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'spec');$this->_foreach['spec'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['spec']['total'] > 0):
    foreach ($_from AS $this->_var['spec']):
        $this->_foreach['spec']['iteration']++;
?>
								<div class="piaojia_item f_l <?php if ($this->_var['spec']['stock'] == 0): ?>null<?php endif; ?>" onclick="changeAttr(this, '<?php echo $this->_var['key']; ?>_<?php echo $this->_var['spec']['specId']; ?>', 'price')">
									<span> <?php if (( $this->_var['spec']['layout'] != '' )): ?><?php echo $this->_var['spec']['layout']; ?><?php else: ?><?php echo $this->_var['spec']['price']; ?><?php endif; ?> 点 </span>
								</div>
                                <input type="radio" name="price" value="<?php echo $this->_var['spec']['price']; ?>" id="price_<?php echo $this->_var['key']; ?>_<?php echo $this->_var['spec']['specId']; ?>" stock="<?php echo $this->_var['spec']['stock']; ?>" style="display:none;" />
                        		<input type="radio" name="market_price" value="<?php echo $this->_var['spec']['market_price']; ?>" id="market_price_<?php echo $this->_var['key']; ?>_<?php echo $this->_var['spec']['specId']; ?>" style="display:none;" />
								<input type="radio" name="specid" value="<?php echo $this->_var['spec']['specId']; ?>" id="specid_<?php echo $this->_var['key']; ?>_<?php echo $this->_var['spec']['specId']; ?>" style="display:none;" />                        
								<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                                <?php endif; ?>
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							</div>
						</li>
						<li>
							<div class="f_l zhangshu_title">选择张数</div>
                            <div class="quantity ver_bottom ">
                                <a id="decrement" class="decrement" onclick="del()">-</a>
                                <input name="number" id="number" class="itxt" value="1" type="text" onblur="checkNum(this.value);" >
                                <a id="increment" class="increment" onclick="add()">+</a>				
                            </div>
                                
						</li>
						<li>
                            <p class="mg2_de_t5" id="showTimeStatus" style="line-height:40px;">
                                <?php $_from = $this->_var['showtime']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'showtime_0_37692800_1463450922');$this->_foreach['showtime'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['showtime']['total'] > 0):
    foreach ($_from AS $this->_var['showtime_0_37692800_1463450922']):
        $this->_foreach['showtime']['iteration']++;
?>
                                <?php if (($this->_foreach['showtime']['iteration'] <= 1)): ?>
                                <input type="hidden" name="status" id="status" value="<?php echo $this->_var['showtime_0_37692800_1463450922']['status']; ?>" />
                                <?php endif; ?>
                                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                            </p>
                            <input type="hidden" name="id" value="<?php echo $this->_var['iteminfo']['itemId']; ?>" />
                            <input type="hidden" name="cateid" value="<?php echo $this->_var['cateid']; ?>" />
                            <input type="hidden" name="act" value="order" />
                            <input type="hidden" name="storeId" value="<?php echo $this->_var['iteminfo']['store']['@attributes']['storeId']; ?>" />
                            <input type="hidden" name="storeName" value="<?php echo $this->_var['iteminfo']['store']['@attributes']['storeName']; ?>" />
                			<input type="button" value="立即购买" class="act-submit input_submit zhuti_a_hover" onclick="submitYcForm();" />
							<span class="tips">演出前三天仅支持上门自取</span>
						</li>
					</ul>
				</div>
			</div>
            </form>
			<div class="jieshao">
				<div class="jieshao_title">
					<span class="tip"></span>
					<span class="title_jieshao">详细介绍</span>
				</div>
				
                <div class="jieshao_box">
					<ul>
						<li>
							<div class="time f_l">
								<span class="tip">时间</span>
								<span class="tips" style="border-right: 1px solid #ddd;"><?php if ($this->_var['iteminfo']['startDate']): ?><?php echo $this->_var['iteminfo']['startDate']; ?> ~ <?php endif; ?> <?php echo $this->_var['iteminfo']['endDate']; ?></span>
							</div>
							<div class="piaojia f_l">
								<span class="tip">票价</span>
								<span class="tips"><?php echo $this->_var['priceString']; ?></span>
							</div>
						</li>
						<li>
							<div class="zhuangtai f_l">
								<span class="tip">售票状态</span>
								<span class="tips" style="border-right: 1px solid #ddd;">正在热售</span>
							</div>
							<div class="leixing f_l">
								<span class="tip" >类型</span>
								<span class="tips"><?php echo $this->_var['title']; ?></span>
							</div>
						</li>
						<li>
							<div class="adress">
								<span class="tip">场馆</span>
								<span class="tips" style="width:1000px;"><?php echo $this->_var['iteminfo']['site']['@attributes']['siteName']; ?></span>
							</div>
						</li>
					</ul>
				</div>
			</div>
			<div class="yanchu_jieshao">				
				<div style="font-weight: bold;">
                	<?php echo $this->_var['iteminfo']['description']; ?>
                </div>
			</div>
		</div>
        
	 	<?php echo $this->fetch('library/page_footer.lbi'); ?>
	            
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
        
        <script>	
		
				
		var showtime = '<?php echo $this->_var['str_showtime']; ?>';
		function changeAttr(obj, id, type){
			var child = obj.parentNode.children;
			for (var i = 0; i<child.length;i++) {
				if (child[i]){
					if ($(child[i]).hasClass('active')){
						$(child[i]).removeClass('active');
						break;
					}
				}
			}
			$(obj).addClass('active');
			document.getElementById(type+'_'+id).click();	
			if (type == 'price'){
				document.getElementById('specid_'+id).click();
				document.getElementById('market_price_'+id).click();
			}
			if (type == 'time'){				
				var objShowTime = eval(showtime);
				for (var i=0; i<objShowTime.length; i++){
					if (i == id){
						
						document.getElementById('showTimeStatus').innerHTML = '<input type="hidden" name="status" id="status" value="'+objShowTime[i]['status']+'" />';
						var specs = objShowTime[i]['specs'];
						var priceHtml = '';
						for (var j=0; j<specs.length; j++){
							//alert(typeof(specs[j]['layout']));
							if(jQuery.trim(specs[j]['layout']) != ""){
								var priceSpec = specs[j]['layout'];
							}else{
								var priceSpec = specs[j]['price'];
							}
							
							priceHtml +='<div class="piaojia_item  f_l '+(specs[j]['stock'] == 0 ? 'null':'')+'" '+(specs[j]['stock'] > 0 ? ' onclick="changeAttr(this, \''+i+'_'+specs[j]['specId']+'\', \'price\');"' : '')+'><span>'+priceSpec+' 点</span></div><input type="radio" name="price" value="'+specs[j]['price']+'" id="price_'+i+'_'+specs[j]['specId']+'" stock="'+specs[j]['stock']+'" style="display:none;" /><input type="radio" name="market_price" value="'+specs[j]['market_price']+'" id="market_price_'+i+'_'+specs[j]['specId']+'" stock="'+specs[j]['stock']+'" style="display:none;" /><input type="radio" name="specid" value="'+specs[j]['specId']+'" id="specid_'+i+'_'+specs[j]['specId']+'" style="display:none;" />';
							
						}
						document.getElementById('showTimePrice').innerHTML = priceHtml;
					}
				}
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
		}
		function add(){
			var stock = parseInt($('#showTimePrice input[name="price"]:checked').attr('stock'));
			if (!stock){
				loadMsg('请先选择价格');
				return false;
			}
			var num = document.getElementById("number");
			var n = parseInt(num.value);
			if(n+1 > stock ){
				loadMsg('只能购买'+stock+'张');
				num.value = stock;
			}else{
				num.value = n+1;
			}
		}
		function checkNum(num){
			if (isNaN(num)){
				alert('只能是数字');
				document.getElementById("number").value = 1;
				return false;
			}
			var stock = parseInt($('#showTimePrice input[name="price"]:checked').attr('stock'));
			if (!stock){
				alert('请先选择价格');
				document.getElementById("number").value = 1;
				return false;
			}
			if (num > stock){
				document.getElementById("number").value = stock;
			}else if (num < 1){
				document.getElementById("number").value = 1;
			}
		}
		
		
		function submitYcForm(){
			var stock = parseInt($('#showTimePrice input[name="price"]:checked').attr('stock'));
			if (!stock){
				alert('请选择价格');
				return false;
			}
			if ($('#status').val() > 3){
				alert('抱歉，该演出不能购买');
				return false;
			}

			var cardMoney = '<?php echo $this->_var['usernames']['card_money']; ?>';
			var amount    = parseFloat($("#number").val() * $('#showTimePrice input[name="price"]:checked').val());
			if (amount > cardMoney){
				alert('您的卡余额不足');
				return false;
			}

			$('#ycForm').submit();
		}
		function checkYcForm(){
			var stock = parseInt($('#showTimePrice input[name="price"]:checked').attr('stock'));
			if (!stock){
				alert('请先选择价格');
				return false;
			}

			if ($('#status').val() > 3){
				alert('抱歉，该演出不能购买');
				return false;
			}
			return true;
		}
		
		//点击更多按钮
		var num=$('.changci .changci_item').length;
		// 判断场次数量
		if(num>5){
				$('.more').show();
				$('.more').click(function(){
					$('.changci').toggleClass('height_auto');
					if($('.changci').hasClass('height_auto')){
					$('.changci').height('auto');
					$('.more span.glyphicon').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
					$('.more_text').text('收起');}else{
					$('.changci').height('57px');
					$('.more span.glyphicon').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
					$('.more_text').text('更多');
						}
					})
				}else{
					$('.more').hide();
					}
		
		
	</script>
    
     </body>
</html>