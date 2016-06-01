<!DOCTYPE html>
<html class="movie_2">
	<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
		<meta charset="UTF-8">
		<title></title>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.min.js"></script>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.SuperSlide.2.1.1.source.js"></script>
        
        <style type="text/css">			

			.tips .seat-intro {padding-right: .5em;text-align: center;}			
			.tips {overflow: hidden;min-height: 34px;line-height: 34px;}
			.tips .seat-intro li {float: left;width: 25%;}			
			.tips .seat-intro .seat {margin: 0 3px;vertical-align: -2px;}
			
			.seat,.empty-col {display: inline-block;width: 30px;height: 30px;line-height: 18px;margin: 0 2px 2px 0;background-size: 30px 30px;}
			
			.active {background-image: url(/mobile/images/img/tips_1.png);}
			.seat-intro .select,.seat-row .select {background-image: url(/mobile/images/img/tips_2.png);}
			.disabled {background-image: url(/mobile/images/img/tips_3.png);}
			.love {background-image: url(/mobile/images/img/tips_4.png);}
			.main_a {position: relative;z-index: 1; background: #f0f0f0;text-align: center;overflow: hidden;border-top: 1px solid #dfdfdf;}
			.main_a h6 {margin: 0;position: absolute;top: 0;left: 50%;z-index: 5;margin-left: -84px;width: 168px;height: 20px; line-height:20px;color: #ffe4a5;background: transparent url(/mobile/images/img/tips_5.png) no-repeat center top;background-size: 167px 19px;}
			.wrapper {position: relative;top: 0;left: 0;z-index: 3;height: 100%;-webkit-overflow-scrolling: touch;}
			.scroller {position: relative; margin-top: 20px;padding: 0 23px;}
			 
			.c-tips {margin-top:25px;margin-bottom:10px;height: 16px;line-height: 16px;background: #f0f0f0;}
				
			.map-row .number-row{width:20px;   font-weight: 600; font-size:15px;}
			.map-row .seat-row{overflow:scroll; width:auto;}
			.act-phone{padding-top:15px; border-bottom:1px #dfdfdf solid;}
			::-webkit-scrollbar{display: none;}
			
			.plan-price{position:absolute; top:0; right:0; height:60px; line-height:60px; margin:0; color:#FC7E2C; font-size:16px;}
			.disabled-seat{height: 62px;line-height: 60px;font-size: 16px; text-align:center;}
			.disabled-seat span{border:1px solid #ff6900; padding:5px 5px; margin:0 5px; font-size:14px; background:#fff;}
			.wenxin_tips{background:#fffdd3;padding: 0 15px;}
			.yinmu{width: 610px;height: 40px;text-align: center;margin: 20px auto;background: url(/images/juyoufuli/img_login/yinmu.png) no-repeat;}
 			.cf{margin: 20px 0;}  
 			.select-seat{padding: 5px;border: 1px solid #ddd;border-radius: 5px;display: inline-block;margin-right: 5px;margin-top: 5px;}
 			.btn-danger{margin-top: 10px;}
 			     
        </style>
    

	</head>

	<body>
		
        
		<?php echo $this->fetch('library/page_top.lbi'); ?>
         
		
        <div class="w_1200">
			<div class="pay_del">
            
            
            </div>
			<div class="content_left">
				<div class="xuanzuo">
					<div class="tips">
						<div class="wenxin_tips"><span style="color: orangered;">温馨提示：</span>如果您在观看3D版本的影片时，中途离场请携带3D眼镜，以免工作人员禁止您正常出入影厅。</div>
                        <div class="yinmu">银幕方向</div>
                        <div class="tips cf margin_0">
                            <ul class="seat-intro">
                                <li><span class="seat active"></span>可选</li>
                                <li><span class="seat select"></span>已选</li>
                                <li><span class="seat disabled"></span>已售</li>
                                <li><span class="seat love"></span>情侣座</li>
                            </ul>
                        </div>
						
                            
    					<div class='seatmap' style="overflow:scroll;">
                            <center style="height:150px; line-height:150px;">加载中...</center>
                        </div>
            
					</div>
				</div>
				<div class="zhuyi">
					<div>注意事项：</div>
					<ul>
						<li>1、选择你要预订的座位单击选中，重复点击取消所选座位；</li>
						<li>2、每笔订单最多可选购4张电影票；情侣座不单卖；</li>
						<li>3、选座时，请尽量选择相邻座位，请不要留下单个座位；</li>
						<li>4、部分影院3D眼镜需要押金，请观影前准备好现金；</li>
						<li>5、进入付款页面后，请15分钟内完成支付，超时系统将释放你选定的座位；</li>
						<li>6、选座购票为特殊商品，出票成功后，如无使用问题，不得退换；</li>
						<li>7、付款后若没有出票成功，我们将自动为您退款。出票成功后，请牢记取票密码，</li>
                        <li>若没有收到取票短信，请到订票中心查询，或拨打客服电话400-010-0689。</li>
					</ul>
				</div>
			</div>
			<div class="content_right">
				<div class="querenxinxi"><span>确认购票信息</span></div>
				<div class="pay_box">
					<div style="overflow: hidden;">
					<div class="img_left">
						<img src="<?php echo $this->_var['planInfo']['movie']['pathVerticalS']; ?>" width="90">
					</div>
					<ul class="xiangqing">
						<li style="width:110px;"><?php echo $this->_var['planInfo']['movie']['movieName']; ?></li>
						<li class="">
							语言版本：:<span><?php echo $this->_var['planInfo']['language']; ?></span>
						</li>
						<li>
						时长：<span><?php echo $this->_var['planInfo']['movie']['movieLength']; ?> 分钟</span>
						</li>
					</ul>
					</div>
					<ul class="yingyuan_del">
						<li>影院：<span><?php echo $this->_var['planInfo']['cinema']['cinemaName']; ?></span></li>
						<li>影厅：<span><?php echo $this->_var['planInfo']['hallName']; ?></span></li>
						<li>售价：<span style="color:orange;"><?php echo $this->_var['planInfo']['price']; ?></span>点</li>
						<li>场次：<span style="color:orange;"><?php echo $this->_var['planInfo']['featureTimeStr']; ?></span></li>
						<li class="zuowei_on2">所选座位：<div class="zuowei_on"></div></li>
					</ul>
					<div style="overflow: hidden;">
					<div class="heji" style="line-height: 30px;">
						合计：<span><?php echo $this->_var['planInfo']['price']; ?></span>点 * <span class="seatsCount">0</span>
					</div>
					<div class="zongjia" style="line-height: 30px;">总价：<span class="init-price" style="color:orange;"> 0 </span>点</div>
					</div>
					<div class="tel_shuru">
						<div>请输入取票手机号码</div>
                        <form action="movie_order.php" method="post" name="orderForm" onsubmit="return checkOrderForm(this);">
						<input type="text" class="form-control" name="mobile" id="mobile" placeholder="请输入手机号，接收取票密码">
                        <input type="hidden" name="act" value="order" />
                        <input type="hidden" name="planId" value="<?php echo $this->_var['planInfo']['planId']; ?>" />								
                        <input type="hidden" name="hallName"  value="<?php echo $this->_var['planInfo']['hallName']; ?>" />								
                        <input type="hidden" name="featureTimeStr" value="<?php echo $this->_var['planInfo']['featureTimeStr']; ?>" />   			
                        <input type="hidden" name="movieName"  value="<?php echo $this->_var['planInfo']['movie']['movieName']; ?>" />				
                        <input type="hidden" name="cinemaName" value="<?php echo $this->_var['planInfo']['cinema']['cinemaName']; ?>" />				
                        <input type="hidden" name="language" value="<?php echo $this->_var['planInfo']['language']; ?> / <?php echo $this->_var['planInfo']['screenType']; ?>" />			
                        
                        <input type="hidden" name="seatsNo" id="seatsNo" value="" />					
                        <input type="hidden" name="seatsName" id="seatsName" value="" />				
                        <input type="hidden" name="seatsCount" id="seatsCount" value="" />				
                        <input type="hidden" name="vipPrice" id="vipPrice" value="<?php echo $this->_var['planInfo']['price']; ?>" />	
                        <input type="hidden" name="seatParam" id="seatParam" value='<?php echo $this->_var['seatParam']; ?>' />
                        <input type="hidden" name="movieId" id="mvoieId" value='<?php echo $this->_var['planInfo']['movie']['movieId']; ?>' />
                        <input class="btn btn-danger" type="submit" value=" &nbsp;提交座位 &nbsp; ">
                		</form>
					</div>
				</div>
			</div>
		</div>
     
     	
		<?php echo $this->fetch('library/page_footer.lbi'); ?>
    	
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
    
     <script>
		loadSeat();
		function loadSeat(){
			var seatParam = $('#seatParam').val();
			$.ajax({
				type: "POST",
				url: "movie.php?step=seatAjax",
				data: jQuery.param( jQuery.parseJSON(seatParam) ),		
				success: function(info){
					$('.seatmap').html(info);
				}
			});
		}
		
		function checkOrderForm(obj){
			if ($('.zuowei_on span').length == 0){
				alert('请先选择座位！');
				return false;
			}
			var cardMoney = '<?php echo $this->_var['usernames']['card_money']; ?>';
			var amount    = parseFloat($(".init-price").text());	
			
			if (amount > cardMoney){
				alert('您的卡余额不足');
				return false;
			}
		
			if ($('#mobile').val() == ''){
				alert('手机号码不能为空！');
				return false;
			}else{
				var reg = /^1[3,5,7,8]\d{9}$/;
				if (!reg.test( $('#mobile').val() )){
					alert('手机号码格式不正确！');
					return false;
				}
			}
		
			//return true;
		}
	</script>

	</body>

</html>