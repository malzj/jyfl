<!DOCTYPE html>
<html class="movie">
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
		
		<div id="ticketSearchFixDiv" class="onlineticket" style="width: 100%;">
			<div class="midbox">
				<div class="nav_main">
					<ul class="header_left">
                    	<?php $_from = $this->_var['category']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cate');if (count($_from)):
    foreach ($_from AS $this->_var['cate']):
?>
						<li><a href="<?php echo $this->_var['cate']['url']; ?>"><?php echo $this->_var['cate']['name']; ?></a></li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
					</ul>
				</div>
				<div id="ticketSearchDiv" class="movieselectbox clearfix">
					<div class="moviemid m_movie __r_c_"><span>选影片</span>
						<a href="javaScript:;" class="ico_select"></a>
					</div>
					<div class="moviemid m_film __r_c_"><span>选影院</span>
						<a href="javaScript:;" class="ico_select"></a>
					</div>
					<div class="moviemid m_time nottime __r_c_"><span>选时间</span>
						<a href="javaScript:;" class="ico_select"></a>
					</div>
					
					<div class="moviestip __r_c_" style="display: none;" id="ajaxMovieList"> <center> LOADING ... </center> </div>
                    <div class="cinematip __r_c_" style="display: none;" id="ajaxCinemaList"> 请选择影片 </div>
					<div class="i_date __r_c_" style="display: none;" id="ajaxPlanList"> <center> 请选择影院</center> </div>
				</div>
			</div>
		</div>
		<div class="w_1200">
			<div class="reying">
				<div class="reying_title">
					<div class="reying_bg">正在热映</div>
					<div class="reying_news">今天共有<span> <?php echo $this->_var['count']; ?> </span>部影片</div>
				</div>
				<div class="reying_show">
					<div class="reying_img">
						<img src="<?php echo $this->_var['movies']['shifuMovie']['pathVerticalS']; ?>" style="width: 100%;height: 100%;">
					</div>
					<div class="reying_show1">
						<ul>
							<li class="title"><?php echo $this->_var['movies']['shifuMovie']['movieName']; ?></li>
							<li>类型：<span><?php echo $this->_var['movies']['shifuMovie']['movieType']; ?></span></li>
							<li>导演：<span><?php echo $this->_var['movies']['shifuMovie']['director']; ?></span></li>
							<li>主演：<span><?php echo $this->_var['movies']['shifuMovie']['actor']; ?></span></li>
							<li class="juqingA">剧情：<span class="juqing_left"><?php echo $this->_var['movies']['shifuMovie']['intro']; ?></span></li>
							<li>片长：<span><?php echo $this->_var['movies']['shifuMovie']['movieLength']; ?>分钟</span></li>
							<li>上映时间：<span><?php echo $this->_var['movies']['shifuMovie']['publishTime']; ?></span></li>
						</ul>
					</div>
					<div class="reying_show2">
						<div class="mark"><i><?php echo $this->_var['movies']['shifuMovie']['score']; ?></i></div>
						<div class="buy"><a href="<?php echo $this->_var['app_path']; ?>movie.php?step=planCinema&movieid=<?php echo $this->_var['movies']['shifuMovie']['movieId']; ?>" target="_blank" class="zhuti_a_hover">选座购票</a></div>
					</div>
				</div>
				<div class="movie_list">
					<div class="hd">
						<span class="prev"></span>
						<span class="next"></span>
					</div>
					<div class="bd">
						<ul class="picList">
                        <?php $_from = $this->_var['movies']['hot']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'hot');if (count($_from)):
    foreach ($_from AS $this->_var['hot']):
?>
							<li style="float: left;">
								<div class="li_top"></div>
								<div class="pic">
									<a href="<?php echo $this->_var['app_path']; ?>movie.php?step=planCinema&movieid=<?php echo $this->_var['hot']['movieId']; ?>" target="_blank"><img src="<?php echo $this->_var['hot']['pathVerticalS']; ?>" width="140" height="196"></a>
								</div>
								<div class="title"><a href="<?php echo $this->_var['app_path']; ?>movie.php?step=planCinema&movieid=<?php echo $this->_var['hot']['movieId']; ?>" target="_blank"><span><?php echo $this->_var['hot']['movieName']; ?></span></a>
								<div class="buy"> <a href="<?php echo $this->_var['app_path']; ?>movie.php?step=planCinema&movieid=<?php echo $this->_var['hot']['movieId']; ?>" target="_blank" class="zhuti_a_hover">选座购票</a></div>
							</a></div>
							</li>
						<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        
						</ul>
					</div>
				</div>
			</div>
			<div class="will">
				<div class="will_title">
					<div class="will_bg">即将上映</div>
				</div>
				<div class="will_show">
					<div class="will_img">
						<img src="<?php echo $this->_var['movies']['shifuComing']['pathVerticalS']; ?>" style="width: 100%;height: 100%;">
					</div>
					<div class="will_show1">
						<ul>
							<li class="title"><?php echo $this->_var['movies']['shifuComing']['movieName']; ?></li>
							<li>类型：<span><?php echo $this->_var['movies']['shifuComing']['movieType']; ?></span></li>
							<li>导演：<span><?php echo $this->_var['movies']['shifuComing']['director']; ?></span></li>
							<li>主演：<span><?php echo $this->_var['movies']['shifuComing']['actor']; ?></span></li>
							<li class="juqingA">剧情：<span class="juqing_left"><?php echo $this->_var['movies']['shifuComing']['intro']; ?></span></li>
							<li>片长：<span><?php echo $this->_var['movies']['shifuComing']['movieLength']; ?>分钟</span></li>
							<li>上映时间：<span><?php echo $this->_var['movies']['shifuComing']['publishTime']; ?></span></li>
						</ul>
					</div>
					<div class="will_show2">
						<div class="mark"><span class="date"><?php echo $this->_var['movies']['shifuComing']['publishTime_cn']; ?></span><span class="will_date">即将上映</span></div>
					</div>
				</div>
				<div class="will_list">
					<div class="hd">
						<span class="prev"></span>
						<span class="next"></span>
					</div>
					<div class="bd">
						<ul class="picList">
                        
                        <?php $_from = $this->_var['movies']['coming']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'coming');if (count($_from)):
    foreach ($_from AS $this->_var['coming']):
?>
							<li style="float: left;">
								<div class="li_top"></div>
								<div class="li_date"><span class="date"><?php echo $this->_var['coming']['publishTime_cn']; ?></span><span class="will_date">即将上映</span></div>
								<div class="pic">
									<img src="<?php echo $this->_var['coming']['pathVerticalS']; ?>" width="150" height="200">
								</div>
								<div class="title">
									<div class="title_1"><?php echo $this->_var['coming']['movieName']; ?></div>
									<div class="title_2">类型：<span><?php echo $this->_var['coming']['movieName']; ?></span></div>
									<div class="title_3">导演：<span><?php echo $this->_var['coming']['director']; ?></span></div>
									<div class="title_4">主演：<span><?php echo $this->_var['coming']['actor']; ?></span></div>
									<div class="title_5">片长：<span><?php echo $this->_var['coming']['movieLength']; ?>分钟</span></div>
									<div class="title_6">上映时间：<span><?php echo $this->_var['coming']['publishTime']; ?></span></div>
								</div>
							</li>
						<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
        
        
		<?php echo $this->fetch('library/page_footer.lbi'); ?>
        
        
         
		<?php echo $this->fetch('library/page_left.lbi'); ?>
        
        
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
        
        
		<script type="text/javascript">
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
			jQuery(".movie_list").slide({
				titCell: ".hd ul",
				mainCell: ".bd ul",
				autoPage: true,
				effect: "left",
				autoPlay: false,
				vis: 6,
				pnLoop: false
										});
			jQuery(".will_list").slide({
				titCell: ".hd ul",
				mainCell: ".bd ul",
				autoPage: true,
				effect: "left",
				autoPlay: false,
				vis: 3,
				pnLoop: false
										});						
		      $("body").bind("click",function(evt){
                if($(evt.target).parents("#ticketSearchDiv").length==0) {
                    $('.moviestip').hide();
					$('.cinematip').hide();
					$('.i_date').hide();
                	}	
           		 });
				
			  

		</script>
		<script type="text/javascript">
			jQuery(function($) {
				$(document).ready(function() {
					$('#ticketSearchFixDiv').stickUp();
				});
			});
			//点击选影片
			$('.m_movie').click(function() {
				$('.moviestip').toggle();
				$('.cinematip').css('display', 'none');
				$('.i_date').css('display', 'none');
				loacList("step=ajaxMovieList",'#ajaxMovieList');
				
			})
			// 选择的时候
			$(document).delegate('.movieselectbox a','click',function(){
				var type = $(this).attr('data-type');
				var id = $(this).attr('data-id');
				var cid = $(this).attr('data-cid');
	
				if(type == 'movie'){
					loacList("step=ajaxCinemaList&movieid="+id,'#ajaxCinemaList');
					$('.cinematip').toggle();
					$('.moviestip').css('display', 'none');
					$('.i_date').css('display', 'none');
					$('.m_movie > span').html($(this).find('span').html());
				}
				
				if(type == 'cinema'){
					loacList("step=ajaxPlanList&movieid="+id+"&cinemaid="+cid,'#ajaxPlanList');
					$('.i_date').toggle();
					$('.cinematip').css('display', 'none');
					$('.moviestip').css('display', 'none');
					$('.m_film  > span').html($(this).html());
				}
			});
			// 选择排期时间
			$(document).delegate('.i_dates .transition4 dd','click', function(){				
				$(this).addClass('active');
				var featuretime = $(this).html();
				var cinemaid = $(this).attr('data-cinemaid');
				var movieid = $(this).attr('data-movieid');		
				loacList("step=ajaxPlanList&movieid="+movieid+"&cinemaid="+cinemaid+"&currentTime="+featuretime,'.showdate');
				$(this).addClass('on').siblings().removeClass('on');
				
			})
			
			
			$('.m_film').click(function() {
				$('.cinematip').toggle();
				$('.moviestip').css('display', 'none');
				$('.i_date').css('display', 'none');
			})
			$('.m_time').click(function() {
				$('.i_date').toggle();
				$('.cinematip').css('display', 'none');
				$('.moviestip').css('display', 'none');
			})
			
			
			// 加载列表
			function loacList(param, idClass){			
				$.ajax({
					url:'movie.php',
					type:'POST',
					data: param,
					beforeSend:function(){
						$(idClass).html(" LOADING ... ");
					},
					success:function( data ){
						$(idClass).html(data);
						if(idClass == '#ajaxPlanList'){
							jQuery(".i_datetab").slide({
							mainCell: ".bd dl",
							autoPage: true,
							effect: "left",
							autoPlay: false,
							vis: 3,
							pnLoop: false
													});		
						}
						
					}
				})
			}
		</script>
	</body>

</html>