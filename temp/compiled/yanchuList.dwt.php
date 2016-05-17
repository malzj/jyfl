<!DOCTYPE html>
<html class="huaju">
	<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
		<meta charset="UTF-8">
		<title></title>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.min.js"></script>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.SuperSlide.2.1.1.source.js"></script>
        <script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/stickUp.js"></script>
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
		<div class="search" id="search_yanchu">
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
								<li><a href="<?php echo $this->_var['t']['ad_link']; ?>" target="_blank"><?php echo $this->_var['t']['ad_name']; ?><?php echo $this->_var['t']['ad_name']; ?><?php echo $this->_var['t']['ad_name']; ?></a></li>
                            <?php endforeach; else: ?>
                            	<li> 暂无广播 </li>
                            <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
							</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="search_input f_r">
					<form action="yanchu.php" method="get">
                    	<input type="hidden" name="id" value="<?php echo $this->_var['cateid']; ?>" />
						<input type="text" name="yckeyword" placeholder="搜索“<?php echo $this->_var['title']; ?>”">
						<input type="submit" value="" style="display:none;" />
					</form>
				</div>
				
			</div>
		</div>
		<div class="w_1200">
			<div class="big_img">
            	<?php $_from = $this->_var['topticket']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'top');if (count($_from)):
    foreach ($_from AS $this->_var['top']):
?>
				<div class="item f_l iwrap">
					<a href="yanchu.php?act=show&id=<?php echo $this->_var['cateid']; ?>&itemid=<?php echo $this->_var['top']['item_id']; ?>"><img src="<?php echo $this->_var['top']['thumb']; ?>" ></a>
                    <a href="yanchu.php?act=show&id=<?php echo $this->_var['cateid']; ?>&itemid=<?php echo $this->_var['top']['item_id']; ?>">
                        <div class="big_img_xin float"></div>
                        <div class="big_img_xinxi float">
                            <div class="big_img_box"><span class="big_img_title"><?php echo $this->_var['top']['item_name']; ?></span></div>
                            <div class="big_img_btn"><span class="zhuti_a_hover">详情点击</span></div>
                        </div>
                    </a>
				</div>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			</div>
            
			<div class="reying_bg"><?php echo $this->_var['title']; ?></div>
			<div class="huaju_box">
				<?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('id', 'listinfo');if (count($_from)):
    foreach ($_from AS $this->_var['id'] => $this->_var['listinfo']):
?>
					<div class="box">
						<div class="img_box f_l">
							<a href="yanchu.php?act=show&id=<?php echo $this->_var['cateid']; ?>&itemid=<?php echo $this->_var['listinfo']['item_id']; ?>"><img src="<?php echo $this->_var['listinfo']['thumb']; ?>" width="230" height="230"></a>
						</div>
						<div class="xinxi_box f_l">
							<ul>
								<li>
									<h3><a href="yanchu.php?act=show&id=<?php echo $this->_var['cateid']; ?>&itemid=<?php echo $this->_var['listinfo']['item_id']; ?>"><?php echo $this->_var['listinfo']['item_name']; ?></a></h3>
								</li>
								<li>"<span><?php echo $this->_var['listinfo']['item_name']; ?></span>"</li>
								<li><span><?php echo $this->_var['listinfo']['data_ext']; ?></span></li>
								<li><span>[<?php echo $this->_var['listinfo']['site_name']; ?>]</span></li>
								<li><span class="shoupiao f_l">售票中</span><a href="yanchu.php?act=show&id=<?php echo $this->_var['cateid']; ?>&itemid=<?php echo $this->_var['listinfo']['item_id']; ?>"><span class="f_r xuanzuo zhuti_a_hover">选座购票</span></a></li>
							</ul>
						</div>
					</div>
				<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				<div class="flickr" style="text-align:center;"><?php echo $this->fetch('library/pages.lbi'); ?></div>
			</div>
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
				//固定顶部
				jQuery(function($) {
				$(document).ready(function() {
					$('#search_yanchu').stickUp();
				});
			});
        </script>
       
			<script>
				//鼠标悬停动画
                $(function (){
                    var iwrap = $(".iwrap");
                    var float = $(".float");
            
                    $(".iwrap").hover(function(e){//mouse in
                        $(this).children().find('.float').css(moveForward($(this), e)).stop(true, true).animate({"left":0, "top":0}, 500);
                    },function(e){//mouse out
                       $(this).children().find('.float').animate(moveForward($(this), e), 500);
                    });
                });
            
                var moveForward = function(elem, e){
                    var w = elem.width(), h = elem.height(), direction=0, cssprop={};
                    var x = (e.pageX - elem.offset().left - (w / 2)) * (w > h ? (h / w) : 1);
                    var y = (e.pageY - elem.offset().top - (h / 2)) * (h > w ? (w / h) : 1);
            
                    direction = Math.round((((Math.atan2(y, x) * (180 / Math.PI)) + 180) / 90) + 3) % 4;
                    switch(direction)
                    {
                        case 0://from top
                            cssprop.left = 0;
                            cssprop.top = "-100%";
                            break;
                        case 1://from right
                            cssprop.left = "100%";
                            cssprop.top = 0;
                            break;
                        case 2://from bottom
                            cssprop.left = 0;
                            cssprop.top = "100%";
                            break;
                        case 3://from left
                            cssprop.left = "-100%";
                            cssprop.top = 0;
                            break;
                    }
                    return cssprop;
                }
            </script>

       
	</body>
</html>
