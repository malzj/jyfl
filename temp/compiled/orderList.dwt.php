<!DOCTYPE html>
<html>
	<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
		<meta charset="UTF-8">
		<title></title>
		<link rel="stylesheet" href="css/public.css">
	</head>
	<body class="bg_white">
    
    <?php echo $this->fetch('library/page_top.lbi'); ?>
    
			<div class="w_1200">
				<div class="my_order_tips"></div>
				<div class="my_order_title o_hidden">
					<div class="my_order_details f_l">订单详情</div>
					<div class="my_order_price f_l">单价（点）</div>
					<div class="my_order_num f_l">数量</div>
					<div class="my_order_price_all f_l">总金额（点）</div>
					<div class="my_order_zhuangtai f_l">状态</div>
				</div>
				
				<div class="my_order_item o_hidden">
					<div class="my_order_item_title o_hidden">
						<div class="f_l my_order_dingdanhao">123456789123456</div>
						<div class="f_l">
							<span>2016-05-30</span>&nbsp;&nbsp;
							<span>12:20</span>
						</div>
					</div>
					<?php $_from = $this->_var['orders']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'order');if (count($_from)):
    foreach ($_from AS $this->_var['order']):
?>
					
					<?php $_from = $this->_var['order']['goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'good');if (count($_from)):
    foreach ($_from AS $this->_var['good']):
?>
					<div class="my_order_detailsAll o_hidden">
						<div class="my_order_details f_l">
							<div class="my_order_details_img f_l">
								<img src="<?php echo $this->_var['good']['goods_thumb']; ?>">
							</div>
							<div class="my_order_details_content f_l">
								<div class="my_order_details_name"><?php echo $this->_var['good']['goods_name']; ?></div>
								<div class="my_order_details_box"><?php echo $this->_var['good']['goods_attr']; ?></div>
							</div>
						</div>
						<div class="my_order_price f_l line_h_90"><?php echo $this->_var['good']['goods_price']; ?>点</div>
						<div class="my_order_num f_l line_h_90">x<span><?php echo $this->_var['good']['goods_number']; ?></span></div>
						<div class="my_order_price_all f_l line_h_90"><?php echo $this->_var['good']['goods_price*$good']['goods_number']; ?>点</div>
						<div class="my_order_zhuangtai f_l zhuangtai_margin_top">
							<div>已下单</div>
							<div class="active">未付款</div>
							<div>已取消</div>
						</div>
					</div>
					<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					
					<div class="my_order_caozuo o_hidden">
						<div class="my_order_dingdan f_l">订单号：<span><?php echo $this->_var['order']['order_sn']; ?></span></div>
						<div class="my_order_xiangqing f_r">
							<div class="f_l">商品总金额：<span class="color_ff7900">86.0点</span>+运费：<span class="color_ff7900">0.00点</span></div>
							<div class="f_l my_order_dingdanhao">
								<a href="user.php?act=cancel_order&order_id=<?php echo $this->_var['order']['order_id']; ?>" class="bg_color my_order_btn zhuti_a_hover">订单详情</a>
							</div>
						</div>
					</div>
					<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					
					<div class="my_order_detailsAll o_hidden">
						<div class="my_order_details f_l">
							<div class="my_order_details_img f_l">
								<img src="themes/default/images/img_login/2433_thumb_G_1458798828098.jpg">
							</div>
							<div class="my_order_details_content f_l">
								<div class="my_order_details_name">芒果雪域芝士蛋糕</div>
								<div class="my_order_details_box">规格:1磅【13*13cm】   生日牌:生日快乐  蜡烛:免费赠送（金色1支）  餐具:标准餐具（免费） </div>
							</div>
						</div>
						<div class="my_order_price f_l line_h_90">43.00点</div>
						<div class="my_order_num f_l line_h_90">x<span>1</span></div>
						<div class="my_order_price_all f_l line_h_90">43.00点</div>
						<div class="my_order_zhuangtai f_l zhuangtai_margin_top">
							<div>已下单</div>
							<div class="active">未付款</div>
							<div>已取消</div>
						</div>
					</div>
					
					<div class="my_order_detailsAll o_hidden">
						<div class="my_order_details f_l">
							<div class="my_order_details_img f_l">
								<img src="themes/default/images/img_login/2433_thumb_G_1458798828098.jpg">
							</div>
							<div class="my_order_details_content f_l">
								<div class="my_order_details_name">芒果雪域芝士蛋糕</div>
								<div class="my_order_details_box">规格1磅:【13*13cm】   生日牌:生日快乐  蜡烛:免费赠送（金色1支）  餐具:标准餐具（免费） </div>
							</div>
						</div>
						<div class="my_order_price f_l line_h_90">43.00点</div>
						<div class="my_order_num f_l line_h_90">x<span>1</span></div>
						<div class="my_order_price_all f_l line_h_90">43.00点</div>
						<div class="my_order_zhuangtai f_l zhuangtai_margin_top">
							<div>已下单</div>
							<div class="active">未付款</div>
							<div>已取消</div>
						</div>
					</div>
					
					<div class="my_order_caozuo o_hidden">
						<div class="my_order_dingdan f_l">订单号：<span>999999999999999</span></div>
						<div class="my_order_xiangqing f_r">
							<div class="f_l">商品总金额：<span class="color_ff7900">86.0点</span>+运费：<span class="color_ff7900">0.00点</span></div>
							<div class="f_l my_order_dingdanhao">
								<a href="#" class="bg_color my_order_btn zhuti_a_hover">订单详情</a>
							</div>
						</div>
					</div>
				</div>
                <div class="flickr" style="text-align:center;"><?php echo $this->fetch('library/pages.lbi'); ?></div>
			</div>
	</body>
</html>
