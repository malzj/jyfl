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
         
        
        <div class="w_1200" style="margin-top: 90px;" > 
			<div class="sport4_top o_hidden">
				<div class="sport4_top_left f_l">
					<div class="sport_img">
                    	<?php $_from = $this->_var['detail']['imgs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'img');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['img']):
?>
						<img src="<?php echo $this->_var['img']; ?>" alt="<?php echo $this->_var['detail']['productName']; ?>">
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					</div>
					<div class="sport4_list">
						<div class="hd">
							<span class="next"></span>
							<span class="prev"></span>
						</div>
						<div class="bd">
							<ul class="sport4_img_item">
                            	<?php $_from = $this->_var['detail']['imgs']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'img');if (count($_from)):
    foreach ($_from AS $this->_var['img']):
?>
								<li class="f_l">
									<img src="<?php echo $this->_var['img']; ?>" alt="<?php echo $this->_var['detail']['productName']; ?>" width="90">
								</li>
								<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							</ul>
						</div>
					</div>
				</div>
				<div class="sport4_top_right f_l">
					<ul>
						<li class="sport4_title"><?php echo $this->_var['detail']['productName']; ?></li>
						<li class="sport4_pri">价格：<span class="sport4_dianshu"><?php echo $this->_var['detail']['salePrice']; ?></span>点</li>
						<li class="sport4_mar">营业时间：<span><?php echo $this->_var['detail']['businessHours']; ?> </span></li>
						<li class="sport4_mar">详细地址：<span><?php echo $this->_var['detail']['viewAddress']; ?></span></li>
						<li><a href="#goumai"><span class="sport4_btn">立即购买</span></a></li>
                        
					</ul>
				</div>
			</div>
			<div class="h-inforbox">
				<div class="h-tabletitle" id="calendar" style="color:#F00;"> 
                	
                    <div>
						<table class="h-table">
							<tbody>
								<tr>
									<td>
										<a href="javascript:calendar(2);" class="h-left"><img src="themes/default/images/img_login/l8.gif"></a>
									</td>
									<td class="h-tr">2016年05月价格日历 (选择游玩日期预订) </td>
									<td>
										<a href="javascript:calendar(1);"><img src="themes/default/images/img_login/r8.gif"></a>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="h-table1">
						<table>
							<tbody>
								<tr>
									<td>星期日</td>
									<td>星期一</td>
									<td>星期二</td>
									<td>星期三</td>
									<td>星期四</td>
									<td>星期五</td>
									<td>星期六</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="h-table2" id="td">
						<p></p>
						<p></p>
						<p></p>
						<p></p>
						<p></p>
						<table>
							<tbody>
								<tr>
									<td class="t1" date="0">
										<p>01</p>
										<p class="h-p1">&nbsp;</p>
									</td>
									<td class="t1" date="0">
										<p>02</p>
										<p class="h-p1">&nbsp;</p>
									</td>
									<td class="t1" date="0">
										<p>03</p>
										<p class="h-p1">&nbsp;</p>
									</td>
									<td class="t1" date="2016-05-04">
										<p>04</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-05">
										<p>05</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-06">
										<p>06</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-07">
										<p>07</p>
										<p class="h-p1">61点</p>
									</td>
								</tr>
								<tr class="t1">
									<td class="t1" date="2016-05-08">
										<p>08</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-09">
										<p>09</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-10">
										<p>10</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-11">
										<p>11</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-12">
										<p>12</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-13">
										<p>13</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-14">
										<p>14</p>
										<p class="h-p1">61点</p>
									</td>
								</tr>
								<tr class="t1">
									<td class="t1" date="2016-05-15">
										<p>15</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-16">
										<p>16</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-17">
										<p>17</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-18">
										<p>18</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-19">
										<p>19</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-20">
										<p>20</p>
										<p class="h-p1">61点</p>
									</td>
									<td class="t1" date="2016-05-21">
										<p>21</p>
										<p class="h-p1">61点</p>
									</td>
								</tr>
								<tr class="t1">
									<td class="t1" date="0">
										<p>22</p>
										<p class="h-p1">&nbsp;</p>
									</td>
									<td class="t1" date="0">
										<p>23</p>
										<p class="h-p1">&nbsp;</p>
									</td>
									<td class="t1" date="0">
										<p>24</p>
										<p class="h-p1">&nbsp;</p>
									</td>
									<td class="t1" date="0">
										<p>25</p>
										<p class="h-p1">&nbsp;</p>
									</td>
									<td class="t1" date="0">
										<p>26</p>
										<p class="h-p1">&nbsp;</p>
									</td>
									<td class="t1" date="0">
										<p>27</p>
										<p class="h-p1">&nbsp;</p>
									</td>
									<td class="t1" date="0">
										<p>28</p>
										<p class="h-p1">&nbsp;</p>
									</td>
								</tr>
								<tr class="t1">
									<td class="t1" date="0">
										<p>29</p>
										<p class="h-p1">&nbsp;</p>
									</td>
									<td class="t1" date="0">
										<p>30</p>
										<p class="h-p1">&nbsp;</p>
									</td>
									<td class="t1" date="0">
										<p>31</p>
										<p class="h-p1">&nbsp;</p>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
                </div>
				<div class="h-bot">
					 <?php echo $this->_var['detailww']; ?>
				</div>
			</div>
			<div style="clear: both;"></div>
			<div class="sport4_jieshaotit"><span class="sport4_jieshaotitle">产品介绍</span></div>			
			<div>
				 <?php echo $this->_var['detail']['content']; ?>
			</div>
            
		</div>

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
		</script>
		<script>
			$('.sport4_img_item li img').click(function() {
				var img = $(this).attr('src');
				$('.sport_img img').attr('src', img);
			})
		</script>
        
        
	 	<?php echo $this->fetch('library/page_footer.lbi'); ?>
	    
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
    </body>
</html>