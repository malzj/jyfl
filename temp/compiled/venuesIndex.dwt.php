<!DOCTYPE html>
<html class="huaju">
	<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
		<meta charset="UTF-8">
		<title></title>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.min.js"></script>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.SuperSlide.2.1.1.source.js"></script>
        
	</head>
	<body>
        
        <?php echo $this->fetch('library/page_top.lbi'); ?>
        
        
        <style> .sport_item_content{width:200px;}</style>
        
        <div class="newsimg">
			<div class="fullSlide">
				<div class="bd">
					<ul>						
						<?php $_from = $this->_var['banner']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'moviebanner');if (count($_from)):
    foreach ($_from AS $this->_var['moviebanner']):
?>
						<li _src="url(/data/afficheimg/<?php echo $this->_var['moviebanner']['ad_code']; ?>)" style="background:#<?php echo $this->_var['moviebanner']['bgcolor']; ?> center 0 no-repeat;">
							<a target="_blank" href="<?php echo $this->_var['moviebanner']['ad_link']; ?>"></a>
						</li>
						<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>	
					</ul>
				</div>
				<div class="hd">
					<ul></ul>
				</div>
				<span class="prev"></span>
				<span class="next"></span>
			</div>
		</div>
        
        <div class="search">
			<div class="search_1200">
				<div class="tips f_l">
					<span class="glyphicon glyphicon-volume-down tip"></span>
					<div class="txtMarquee-top">
						<div class="bd">
							<div class="tempWrap">
							<ul class="infoList">
								<?php $_from = $this->_var['category']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cate');if (count($_from)):
    foreach ($_from AS $this->_var['cate']):
?>
                                <li><a href="<?php echo $this->_var['cate']['url']; ?>"><?php echo $this->_var['cate']['name']; ?></a></li>
                                <?php endforeach; else: ?>
                                <li>暂无广播</li>
                                <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>								
							</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="search_input f_r">					
                    <form action="venues.php" method="get">
                    	<input type="text"  name="keywords" placeholder="搜索场馆">
                    </form>
					<i></i>	
				</div>
			</div>
		</div>
        
        <div class="sport">
            <ul class="sport_all">
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="venues.php?type=0L20">
                            <div class="sport_pic pic_1"></div>
                            <div class="sport_title1">羽毛球</div>
                            <div class="sport_title2">
                                <?php if ($this->_var['data']['0L20']['venuesTotal']): ?><span class="font_1"><?php echo $this->_var['data']['0L20']['venuesTotal']; ?></span>场馆<?php endif; ?>
                                <?php if ($this->_var['data']['0L20']['venueTotal']): ?><span class="font_2"><?php echo $this->_var['data']['0L20']['venueTotal']; ?></span>场地<?php endif; ?>
                                <?php if ($this->_var['data']['0L20']['ticketTotal']): ?><span class="font_2 font_3"><?php echo $this->_var['data']['0L20']['ticketTotal']; ?></span>门票<?php endif; ?>
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="venues.php?type=0L11">
                            <div class="sport_pic pic_2"></div>
                            <div class="sport_title1">乒乓球</div>
                            <div class="sport_title2">
                                <?php if ($this->_var['data']['0L11']['venuesTotal']): ?><span class="font_1"><?php echo $this->_var['data']['0L11']['venuesTotal']; ?></span>场馆<?php endif; ?>
                                <?php if ($this->_var['data']['0L11']['venueTotal']): ?><span class="font_2"><?php echo $this->_var['data']['0L11']['venueTotal']; ?></span>场地<?php endif; ?>
                                <?php if ($this->_var['data']['0L11']['ticketTotal']): ?><span class="font_2 font_3"><?php echo $this->_var['data']['0L11']['ticketTotal']; ?></span>门票<?php endif; ?>
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="venues.php?type=0L18">
                            <div class="sport_pic pic_3"></div>
                            <div class="sport_title1">网球</div>
                            <div class="sport_title2">
                                 <?php if ($this->_var['data']['0L18']['venuesTotal']): ?><span class="font_1"><?php echo $this->_var['data']['0L18']['venuesTotal']; ?></span>场馆<?php endif; ?>
                                <?php if ($this->_var['data']['0L18']['venueTotal']): ?><span class="font_2"><?php echo $this->_var['data']['0L18']['venueTotal']; ?></span>场地<?php endif; ?>
                                <?php if ($this->_var['data']['0L18']['ticketTotal']): ?><span class="font_2 font_3"><?php echo $this->_var['data']['0L18']['ticketTotal']; ?></span>门票<?php endif; ?>
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="venues.php?type=0L06">
                            <div class="sport_pic pic_4"></div>
                            <div class="sport_title1">篮球</div>
                            <div class="sport_title2">
                                <?php if ($this->_var['data']['0L06']['venuesTotal']): ?><span class="font_1"><?php echo $this->_var['data']['0L06']['venuesTotal']; ?></span>场馆<?php endif; ?>
                                <?php if ($this->_var['data']['0L06']['venueTotal']): ?><span class="font_2"><?php echo $this->_var['data']['0L06']['venueTotal']; ?></span>场地<?php endif; ?>
                                <?php if ($this->_var['data']['0L06']['ticketTotal']): ?><span class="font_2 font_3"><?php echo $this->_var['data']['0L06']['ticketTotal']; ?></span>门票<?php endif; ?>
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="venues.php?type=0L22">
                            <div class="sport_pic pic_5"></div>
                            <div class="sport_title1">足球</div>
                            <div class="sport_title2">
                                 <?php if ($this->_var['data']['0L22']['venuesTotal']): ?><span class="font_1"><?php echo $this->_var['data']['0L22']['venuesTotal']; ?></span>场馆<?php endif; ?>
                                <?php if ($this->_var['data']['0L22']['venueTotal']): ?><span class="font_2"><?php echo $this->_var['data']['0L22']['venueTotal']; ?></span>场地<?php endif; ?>
                                <?php if ($this->_var['data']['0L22']['ticketTotal']): ?><span class="font_2 font_3"><?php echo $this->_var['data']['0L22']['ticketTotal']; ?></span>门票<?php endif; ?>
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="venues.php?type=0L16">
                            <div class="sport_pic pic_6"></div>
                            <div class="sport_title1">台球</div>
                            <div class="sport_title2">
                                <?php if ($this->_var['data']['0L16']['venuesTotal']): ?><span class="font_1"><?php echo $this->_var['data']['0L16']['venuesTotal']; ?></span>场馆<?php endif; ?>
                                <?php if ($this->_var['data']['0L16']['venueTotal']): ?><span class="font_2"><?php echo $this->_var['data']['0L16']['venueTotal']; ?></span>场地<?php endif; ?>
                                <?php if ($this->_var['data']['0L16']['ticketTotal']): ?><span class="font_2 font_3"><?php echo $this->_var['data']['0L16']['ticketTotal']; ?></span>门票<?php endif; ?>
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="venues.php?type=0L04">
                            <div class="sport_pic pic_7"></div>
                            <div class="sport_title1">高尔夫</div>
                            <div class="sport_title2">
                                 <?php if ($this->_var['data']['0L04']['venuesTotal']): ?><span class="font_1"><?php echo $this->_var['data']['0L04']['venuesTotal']; ?></span>场馆<?php endif; ?>
                                <?php if ($this->_var['data']['0L04']['venueTotal']): ?><span class="font_2"><?php echo $this->_var['data']['0L04']['venueTotal']; ?></span>场地<?php endif; ?>
                                <?php if ($this->_var['data']['0L04']['ticketTotal']): ?><span class="font_2 font_3"><?php echo $this->_var['data']['0L04']['ticketTotal']; ?></span>门票<?php endif; ?>
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="venues.php?type=0N07">
                            <div class="sport_pic pic_8"></div>
                            <div class="sport_title1">游泳</div>
                            <div class="sport_title2">
                                <?php if ($this->_var['data']['0N07']['venuesTotal']): ?><span class="font_1"><?php echo $this->_var['data']['0N07']['venuesTotal']; ?></span>场馆<?php endif; ?>
                                <?php if ($this->_var['data']['0N07']['venueTotal']): ?><span class="font_2"><?php echo $this->_var['data']['0N07']['venueTotal']; ?></span>场地<?php endif; ?>
                                <?php if ($this->_var['data']['0N07']['ticketTotal']): ?><span class="font_2 font_3"><?php echo $this->_var['data']['0N07']['ticketTotal']; ?></span>门票<?php endif; ?>
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="venues.php?type=0P09">
                            <div class="sport_pic pic_9"></div>
                            <div class="sport_title1">瑜伽</div>
                            <div class="sport_title2">
                                 <?php if ($this->_var['data']['0P09']['venuesTotal']): ?><span class="font_1"><?php echo $this->_var['data']['0P09']['venuesTotal']; ?></span>场馆<?php endif; ?>
                                <?php if ($this->_var['data']['0P09']['venueTotal']): ?><span class="font_2"><?php echo $this->_var['data']['0P09']['venueTotal']; ?></span>场地<?php endif; ?>
                                <?php if ($this->_var['data']['0P09']['ticketTotal']): ?><span class="font_2 font_3"><?php echo $this->_var['data']['0P09']['ticketTotal']; ?></span>门票<?php endif; ?>
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="venues.php?type=0P02">
                            <div class="sport_pic pic_10"></div>
                            <div class="sport_title1">游泳健身</div>
                            <div class="sport_title2">
                                <?php if ($this->_var['data']['0P02']['venuesTotal']): ?><span class="font_1"><?php echo $this->_var['data']['0P02']['venuesTotal']; ?></span>场馆<?php endif; ?>
                                <?php if ($this->_var['data']['0P02']['venueTotal']): ?><span class="font_2"><?php echo $this->_var['data']['0P02']['venueTotal']; ?></span>场地<?php endif; ?>
                                <?php if ($this->_var['data']['0P02']['ticketTotal']): ?><span class="font_2 font_3"><?php echo $this->_var['data']['0P02']['ticketTotal']; ?></span>门票<?php endif; ?>
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                
            </ul>
        </div>
            
        
        
	 	<?php echo $this->fetch('library/page_footer.lbi'); ?>
	    
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
        
        <script>
			jQuery(".fullSlide").hover(function() {
				jQuery(this).find(".prev,.next").stop(true, true).fadeTo("show", 0.5)
			}, function() {
				jQuery(this).find(".prev,.next").fadeOut()
			});
			jQuery(".fullSlide").slide({
				titCell: ".hd ul",
				mainCell: ".bd ul",
				effect: "fold",
				autoPlay: true,
				autoPage: true,
				trigger: "click",
				delayTime:1000,
				interTime:4000,
				startFun: function(i) {
					var curLi = jQuery(".fullSlide .bd li").eq(i);
					if (!!curLi.attr("_src")) {
						curLi.css("background-image", curLi.attr("_src")).removeAttr("_src")
				}
				}
				});
				jQuery(".txtMarquee-top").slide({mainCell:".bd ul",autoPlay:true,effect:"topMarquee",interTime:100,trigger:"click"});
		</script>
        
    </body>
</html>