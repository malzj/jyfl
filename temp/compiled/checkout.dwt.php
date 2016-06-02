<!DOCTYPE html>
<html>
	<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
		<meta charset="UTF-8">
		<title></title>
        <link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>js/juyoufuli/layer/skin/layer.css">
        <link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/bootstrap.min.css">
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.min.js"></script>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.SuperSlide.2.1.1.source.js"></script>   
        <script type="text/javascript" src="http://api.map.baidu.com/api?v=1.2"></script>
		<script type="text/javascript" src="http://api.map.baidu.com/library/GeoUtils/1.2/src/GeoUtils_min.js"></script>
		<script src="<?php echo $this->_var['app_path']; ?>js/baidumap.js"></script>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/bootstrap.min.js"></script>
        <script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/laydate/laydate.js"></script>
        <script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.shoppingflow.js"></script>
        <?php echo $this->smarty_insert_scripts(array('files'=>'jquery.region.js,utils.js')); ?>
        <?php echo $this->smarty_insert_scripts(array('files'=>'jquery.common.js')); ?>
        <style>
        	.show_baidumap .layui-layer-content, .show_baidumap {background-color:#FFF;}
        </style>
    </head>
	<body class='bg_white'>
        
        <?php echo $this->fetch('library/page_top.lbi'); ?>
        
        <form action="flow.php" method="post" name="theForm" id="theForm" onsubmit="return checkOrderForm(this)">
        <div class="w_1200">
        	<div class="order_title_tips"></div>
			<div class="order_title"></div>
			<div class="order_list">
				<div class="f_l order_list_name">商品信息</div>
				<div class="f_l order_list_price">单价（点）</div>
				<div class="f_l order_list_num">数量</div>
				<div class="f_l order_list_priceAll">合计（点）</div>
			</div>
			  
            <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'supplier');$this->_foreach['supplier'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['supplier']['total'] > 0):
    foreach ($_from AS $this->_var['supplier']):
        $this->_foreach['supplier']['iteration']++;
?>          
			<div class="order_grshang">
				<div class="order_gyshang_name">
					<div class="order_gyshang_title f_l"><?php echo $this->_var['supplier']['0']['seller']; ?></div>
					<div class="order_item_more color_zhuti f_l"><span class="more_text">收起</span><span class="glyphicon glyphicon-chevron-up margin_left_5"></span></div>
					<div class="adress_look f_r"><a href="javascript:void(0);" class="color_zhuti show_yunfei" data-id="<?php echo $this->_var['supplier']['0']['supplier_id']; ?>">查看配送范围</a></div>
				</div>
				<div class="order_gyshang_itemAll o_hidden">
					<?php $_from = $this->_var['supplier']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');$this->_foreach['goods'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['goods']['total'] > 0):
    foreach ($_from AS $this->_var['goods']):
        $this->_foreach['goods']['iteration']++;
?>
					
					<div class="order_gyshang_item o_hidden">
						<div class="order_gyshang_details1 f_l">
							<div class="order_gyshang_item_img f_l">
								<img src="<?php echo $this->_var['goods']['goods_thumb']; ?>">
							</div>
							<div class="order_gyshang_details f_l">
								<div class="order_item_name"><?php echo $this->_var['goods']['goods_name']; ?></div>
								<div class="order_item_guige"><?php echo $this->_var['goods']['goods_attr']; ?></div>
							</div>
						</div>
						<div class="order_gyshang_details2 f_l"><?php echo $this->_var['goods']['formated_goods_price']; ?></div>
						<div class="order_gyshang_details3 f_l"><?php echo $this->_var['goods']['goods_number']; ?></div>
						<div class="order_gyshang_details4 f_l"><?php echo $this->_var['goods']['formated_subtotal']; ?></div>
					</div>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				</div>
				
				<div class="order_grshang_bottom">
					LOADING ...
				</div>                 
			</div>            
            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			
			<div class="shouhuo">收货信息</div>
			<div class="shouhuoren" id="order_consignee"> LOADING ...</div>
			
			<div class="pay_way">
				<div class="pay_way_title">支付及配送方式</div>
				<div class="pay_way_details">
					<ul>
						<li><span>支付方式：</span><span>聚优文化卡支付</span></li>
						<li><span>配送方式：</span><span>供货商物流</span></li>
						<li><span>本网店 </span>运费：<span><font class="yunfeiTotal">0</font> 点</span></li>
					</ul>
				</div>
			</div>
			
			<div class="order_submit o_hidden">
				<div class="order_submit_box f_r">
					<div class="order_submit f_r">商品总金额：<span class="color_ff7900"><font class="goodsTotal"><?php echo $this->_var['total']['goods_price_formated']; ?></font>点 </span> + 运费：<span class="color_ff7900 yunfeiTotal"> - </span>点</div>
					<div class="order_submit1">
						<div class="f_l shiji_price">实际付款金额：<span class="color_zhuti"><font class="orderTotal"><?php echo $this->_var['total']['goods_price_formated']; ?></font>点</span></div>
						<button type="submit" class="zhuti_a_hover">提交订单</button>
					</div>
				</div>
			</div>
		</div>
        <input type="hidden" name="shipping" id="shipping" value="1" />
		<input type="hidden" name="payment" id="payment" value="2" />
		<input type="hidden" name="payshipping_check" id="payshipping_check" value="1">
        <input type="hidden" name="step" value="done">
        </form>
        <div style="float:left;width:600px;height:500px;border:1px solid gray; display:none; " id="baidumap"></div>
        
	 	<?php echo $this->fetch('library/page_footer.lbi'); ?>
	    
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
        <script>
			// 加载收货地址
			var checkconsignee = '<?php echo $this->_var['checkconsignee']; ?>';		
			updateConsignee(); 
			// 查看运费
			$('.show_yunfei').click(function(){
				var id = $(this).attr('data-id');		
				layer.open({
				  type: 2,
				  title: false,
				  skin: 'show_baidumap',
				  area:['800px','600px'],
				  shadeClose: true,
				  content: "<?php echo $this->_var['app_path']; ?>flow.php?step=showYunfei&id="+id
				});
			});
			// 运费验证
			$('.show_yunfei').each(function(index,dom){			
				supplierYunfei($(dom));			
			})
			// 修改单个供应商的配送地址
			$(document).delegate('.editAddress','click',function(){
				var id = $(this).attr('data-id');
				var layerIndex = layer.open({
					type: 2,
					title: false,
					skin: 'show_baidumap',
					area:['850px','600px'],
					shadeClose: true,
					content: "<?php echo $this->_var['app_path']; ?>flow.php?step=newAddress&id="+id,
					btn:['确认'],
					yes:function(index, layero){
						layer.close(layerIndex);
						$('.show_yunfei').each(function(index,dom){		
							if( $(dom).attr('data-id') == id){
								supplierYunfei($(dom));
							}										
						})
				    },
				  	cancel:function(){
						$('.show_yunfei').each(function(index,dom){		
							if( $(dom).attr('data-id') == id){
								supplierYunfei($(dom));
							}										
						})
				  	}
				});
			});
			// 选择时间
			function selectdate(date, sid, address){
				baidumap.setOptions({
					isYunfei:true,
					isSetYunfei:false,
					isTime:2,
					showMapId:'map'+sid,
					afterFunction:function(d){
						$.ajax({
							type:'GET',
							url:'flow.php',
							dataType:'json',
							data:{
								step:'shippingTime', 
								date:date, 
								sid:sid, 
								shipping_start:d.shipping_start,
								shipping_end:d.shipping_end,
								shipping_waiting:d.shipping_waiting,
								shipping_booking:d.shipping_booking
							},							
							success:function(data){
								var ptime = $('#time'+sid).find('select');
								ptime.empty();
								ptime.append('<option value="0">选择时间</option>');									
								if (data.error > 0){
									alert(data.message);
								}else{
									$.each(data.content, function (k, v){
										ptime.append('<option value="'+k+'">'+v+'</option>');
									});
								}
							}
						});
					}	
				});
				baidumap.showMap(sid,address);
			}
			$('.shouhuoren_item li').hover(function() {
				$(this).children('.shouhuoren_hover').toggle();
			});
			$('#more').click(function() {
				var height = $('.shouhuoren_item').height();
				if (height == '40') {
					$('.shouhuoren .more span.jiantou').css('transform', 'rotate(180deg)');
					$('.shouhuoren_item').css('height', 'auto');
				} else {
					$('.shouhuoren .more span.jiantou').css('transform', 'rotate(0deg)');
					$('.shouhuoren_item').css('height', '40px');
				}
			})
			//点击更多按钮
			var num=$('.order_gyshang_item ').length;
			// 判断订单数量
			if(num>2){
				$('.order_item_more').show();
				$('.order_item_more').click(function(){
					$('.order_gyshang_itemAll').toggleClass('order_gyshang_itemAll_height');
					if($('.order_gyshang_itemAll').hasClass('order_gyshang_itemAll_height')){
					$('.order_gyshang_itemAll').height('120px');
					$('.order_item_more span.glyphicon').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
					$('.more_text').text('更多');}else{
					$('.order_gyshang_itemAll').height('auto');
					$('.order_item_more span.glyphicon').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
					$('.more_text').text('收起');
						}
					})
				}else{
				$('.order_item_more').hide();
			}
			function supplierYunfei(dom){
				var id = dom.attr('data-id');
				var bottomhtml = dom.closest('.order_grshang').find('.order_grshang_bottom');
				$.ajax({
					type:'POST',
					url:'<?php echo $this->_var['app_path']; ?>flow.php',
					data:{step:'yunfei',id:id},
					async: false,
					beforeSend:function(){
						bottomhtml.html('LOADING ...');
					},
					success:function(data){						
						if(data.error>0){
							alert(data.message);
						}else{
							bottomhtml.html(data.html);
						}
					},
					dataType: "json"					
				});	
			}
		
		</script>
    </body>
</html>