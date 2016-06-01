<!DOCTYPE html>
<html>
	<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
		<meta charset="UTF-8">
		<title></title>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.min.js"></script>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.SuperSlide.2.1.1.source.js"></script>
	</head>
	<body id='body'>
        
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
								<?php $_from = $this->_var['text']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 't');if (count($_from)):
    foreach ($_from AS $this->_var['t']):
?>
								<li><a href="<?php echo $this->_var['t']['ad_link']; ?>" target="_blank"><?php echo $this->_var['t']['ad_name']; ?></a></li>
                                <?php endforeach; else: ?>
                                    <li> 暂无广播 </li>
                                <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
							</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="search_input f_r">
					<form>
					<input type="text" placeholder="输入蛋糕名称">
					</form>
					<i></i>	
				</div>
			</div>
		</div>
        
        
        <div class="w_1200">
			<div class="cake_pinpai o_hidden">
				<ul class="cake_pinpai_all">
					<li><img src="<?php echo $this->_var['app_path']; ?>images/juyoufuli/img_login/nuoxin.png"></li>
					<li><img src="<?php echo $this->_var['app_path']; ?>images/juyoufuli/img_login/nuoxin.png"></li>
					<li><img src="<?php echo $this->_var['app_path']; ?>images/juyoufuli/img_login/nuoxin.png"></li>
					<li><img src="<?php echo $this->_var['app_path']; ?>images/juyoufuli/img_login/nuoxin.png"></li>
					<li><img src="<?php echo $this->_var['app_path']; ?>images/juyoufuli/img_login/nuoxin.png"></li>
				</ul>
			</div>
            
            
            <?php $_from = $this->_var['attrGoods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'attr');if (count($_from)):
    foreach ($_from AS $this->_var['attr']):
?>
			<div class="floor" id="floor_<?php echo $this->_var['attr']['attrNo']; ?>">
				<div class="floor_title">
					<h4>
						<a href="#"><i>F<?php echo $this->_var['attr']['attrNo']; ?></i><?php echo $this->_var['attr']['attrName']; ?></a>
					</h4>
				</div>
				<div class="floor_item">
                	<?php $_from = $this->_var['attr']['goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'good');if (count($_from)):
    foreach ($_from AS $this->_var['good']):
?>
                    	<?php if ($this->_var['good']['is_ad'] == 'true'): ?>
						<div class="floor_content_item wide f_l">
								<a href="<?php echo $this->_var['good']['ad_link']; ?>" target="_blank">
									<img src="<?php echo $this->_var['app_path']; ?>data/afficheimg/<?php echo $this->_var['good']['ad_code']; ?>">
								</a>
						</div>
                        <?php else: ?>
                        <div class="floor_content_item f_l">
							<div class="floor_box">
								<a href="<?php echo $this->_var['good']['url']; ?>" target="_blank">
									<img src="<?php echo $this->_var['good']['goods_thumb']; ?>">
								</a>	
							</div>
							<a class="margin_top_10 dis_block" href="<?php echo $this->_var['good']['url']; ?>" target="_blank">
							  <span><?php echo $this->_var['good']['name']; ?></span><br>	
							  <span class="color_CF5926"><?php echo $this->_var['good']['shop_price']; ?>点</span>
							</a>
						</div>
                        <?php endif; ?>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>						
			    </div>		
			</div>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			
			<div class="floor-guide" style="display:none;">
			    <div class="mui-nav">
                <?php $_from = $this->_var['attrGoods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'attr2');if (count($_from)):
    foreach ($_from AS $this->_var['attr2']):
?>
                    <a href="#floor_<?php echo $this->_var['attr2']['attrNo']; ?>">
                        <b class="icon_01_channelhome">F<?php echo $this->_var['attr2']['attrNo']; ?></b>
                        <em><?php echo $this->_var['attr2']['attrName']; ?></em>
                        <i>&nbsp;</i>
                    </a>
			    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>     
			    </div>
			</div>
			
		</div>
        
        
        
        <div class="sidebar" id="sidebar">
        <a href="#body" class="s-btn goTop"></a>
        </div>
            
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
				delayTime: 1000,
				interTime: 4000,
				startFun: function(i) {
					var curLi = jQuery(".fullSlide .bd li").eq(i);
					if (!!curLi.attr("_src")) {
						curLi.css("background-image", curLi.attr("_src")).removeAttr("_src")
					}
				}
			});
			jQuery(".txtMarquee-top").slide({mainCell:".bd ul",autoPlay:true,effect:"topMarquee",interTime:100,trigger:"click"});
			
			
			//侧边悬浮导航
			jQuery.fn.anchorGoWhere = function(options){
				 var obj = jQuery(this);
				 var defaults = {target:0, timer:500};
				 var o = jQuery.extend(defaults,options);
				 obj.each(function(i){
					 jQuery(obj[i]).click(function(){
						 var _rel = jQuery(this).attr("href").substr(1);
						 switch(o.target){
							 case 1: 
								 var _targetTop = jQuery("#"+_rel).offset().top;
								 jQuery("html,body").animate({scrollTop:_targetTop},o.timer);
								 break;
							 case 2:
								 var _targetLeft = jQuery("#"+_rel).offset().left;
								 jQuery("html,body").animate({scrollLeft:_targetLeft},o.timer);
								 break;
						 }
						 return false;
					 });                  
				 });
			};
			$(document).ready(function(){
				$(".goTop").anchorGoWhere({target:1});	
			});
			$(function () {
				var gao=($(window).height()/2);
					$(window).scroll(function () {
						var totop = $(this).scrollTop();
						if (totop >= 500) {
							$('.goTop').css('display','block');
						}
						else{
							$('.goTop').css('display','none');
						};
					})	
			});
			
			$(document).ready(function(){
				$(window).scroll(function(){
					var top = $(document).scrollTop();
					var menu = $(".floor-guide");
					var items = $(".floor");
			
					var curId = ""; 
			
					items.each(function(){
						var m = $(this);
						var itemsTop = m.offset().top;
						if(top > itemsTop-200){
							curId = "#" + m.attr("id");
						}else{
							return false;
						}
					});
			
					//给相应的楼层设置cur,取消其他楼层的cur
					var curLink = menu.find(".current");
					if( curId && curLink.attr("href") != curId ){
						curLink.removeClass("current");
						menu.find( "[href=" + curId + "]" ).addClass("current");
					}
					// console.log(top);
				});
			});
			$(document).ready(function(){
				$(".floor-guide a").anchorGoWhere({target:1});	
			});
			$(function () {
				var gao=($(window).height()/2);
					$(window).scroll(function () {
						var totop = $(this).scrollTop();
						if (totop >= 700) {
							$('.floor-guide').show();
						}
						else{
							$('.floor-guide').hide();
						};
					})	
			});
		</script>
        
        
	 	<?php echo $this->fetch('library/page_footer.lbi'); ?>
	    
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
    </body>
</html>