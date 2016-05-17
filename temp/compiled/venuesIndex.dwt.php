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
								<li><a href="#" target="_blank">欧美惊悚喜剧《夜莺别墅的死神》—北京站</a></li>
								<li><a href="#" target="_blank">欧美惊悚喜剧《夜莺别墅的死神》—北京站</a></li>
								<li><a href="#" target="_blank">欧美惊悚喜剧《夜莺别墅的死神》—北京站！</a></li>
								<li><a href="#" target="_blank">欧美惊悚喜剧《夜莺别墅的死神》—北京站</a></li>
								<li><a href="#" target="_blank">欧美惊悚喜剧《夜莺别墅的死神》—北京站</a></li>
								<li><a href="#" target="_blank">欧美惊悚喜剧《夜莺别墅的死神》—北京站</a></li>
								<li><a href="#" target="_blank">欧美惊悚喜剧《夜莺别墅的死神》—北京站</a></li>
								<li><a href="#" target="_blank">欧美惊悚喜剧《夜莺别墅的死神》—北京站</a></li>
								<li><a href="#" target="_blank">欧美惊悚喜剧《夜莺别墅的死神》—北京站</a></li>
							</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="search_input f_r">
					<input type="text" placeholder="选场馆">
					<i></i>	
				</div>
			</div>
		</div>
        
        <div class="sport">
            <ul class="sport_all">
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="#">
                            <div class="sport_pic pic_1"></div>
                            <div class="sport_title1">羽毛球场地</div>
                            <div class="sport_title2">
                                <span class="font_1">59</span>场馆
                                <span class="font_2">129</span>场地
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="#">
                            <div class="sport_pic pic_2"></div>
                            <div class="sport_title1">乒乓球场地</div>
                            <div class="sport_title2">
                                <span class="font_1">59</span>场馆
                                <span class="font_2">129</span>场地
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="#">
                            <div class="sport_pic pic_3"></div>
                            <div class="sport_title1">网球场地</div>
                            <div class="sport_title2">
                                <span class="font_1">59</span>场馆
                                <span class="font_2">129</span>场地
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="#">
                            <div class="sport_pic pic_4"></div>
                            <div class="sport_title1">篮球场地</div>
                            <div class="sport_title2">
                                <span class="font_1">59</span>场馆
                                <span class="font_2">129</span>场地
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="#">
                            <div class="sport_pic pic_5"></div>
                            <div class="sport_title1">足球场地</div>
                            <div class="sport_title2">
                                <span class="font_1">59</span>场馆
                                <span class="font_2">129</span>场地
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="#">
                            <div class="sport_pic pic_6"></div>
                            <div class="sport_title1">台球场地</div>
                            <div class="sport_title2">
                                <span class="font_1">59</span>场馆
                                <span class="font_2">129</span>场地
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="#">
                            <div class="sport_pic pic_7"></div>
                            <div class="sport_title1">羽毛球场地</div>
                            <div class="sport_title2">
                                <span class="font_1">59</span>场馆
                                <span class="font_2">129</span>场地
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="#">
                            <div class="sport_pic pic_8"></div>
                            <div class="sport_title1">羽毛球场地</div>
                            <div class="sport_title2">
                                <span class="font_1">59</span>场馆
                                <span class="font_2">129</span>场地
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="#">
                            <div class="sport_pic pic_9"></div>
                            <div class="sport_title1">羽毛球场地</div>
                            <div class="sport_title2">
                                <span class="font_1">59</span>场馆
                                <span class="font_2">129</span>场地
                            </div>
                            <div class="sport_btn">立即预定</div>
                        </a>
                    </div>
                </li>
                <li class="sport_item f_l">
                    <div class="sport_item_content">
                        <a href="#">
                            <div class="sport_pic pic_10"></div>
                            <div class="sport_title1">羽毛球场地</div>
                            <div class="sport_title2">
                                <span class="font_1">59</span>场馆
                                <span class="font_2">129</span>场地
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