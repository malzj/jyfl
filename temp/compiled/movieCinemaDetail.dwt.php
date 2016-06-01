<!DOCTYPE html>
<html class="movie_1">
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
    <meta charset="UTF-8">
    <title></title>
    <link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/reset.css">
    <link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/public.css">
    <script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.min.js"></script>
    <script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.SuperSlide.2.1.1.source.js"></script>
    <?php echo $this->smarty_insert_scripts(array('files'=>'utils.js')); ?>
</head>
<body>
	
	<?php echo $this->fetch('library/page_top.lbi'); ?>
     
	
    
    <div class="w_1200">
        <div class="reying_show">
                <div class="reying_img">
                    <img src="<?php echo $this->_var['movieDetail']['thumb']; ?>" style="width: 100%;height: 100%;">
                </div>
                <div class="reying_show1">
                    <ul>
                        <li class="title"><?php echo $this->_var['movieDetail']['movieName']; ?></li>
                        <li class="border_bottom"></li>
                        <li>类型：<span><?php echo $this->_var['movieDetail']['movieType']; ?></span></li>
                        <li>导演：<span><?php echo $this->_var['movieDetail']['director']; ?></span></li>
                        <li>主演：<span><?php echo $this->_var['movieDetail']['actor']; ?></span></li>
                        <li class="juqingA">剧情：<span class="juqing_left"><?php echo $this->_var['movieDetail']['intro']; ?></span></li>
                        <li>片长：<span><?php echo $this->_var['movieDetail']['movieLength']; ?>分钟</span></li>
                        <li>上映时间：<span><?php echo $this->_var['movieDetail']['publishTime']; ?></span></li>
                        <li class="reying_icon"></li>
                    </ul>
                </div>
                <div class="reying_show2">
                    <div class="mark"><i><?php echo $this->_var['movieDetail']['score']; ?></i></div>
                </div>
                
        </div>	
        <div class="xuanzuo">
            <div class="reying_title">
                <div class="reying_bg">在线选座</div>
            </div>
            <div class="movie_list">
                <div class="hd">
                    <span class="prev"></span>
                    <span class="next"></span>
                </div>
                <div class="bd">
                    <ul class="picList" style="height:270px;">
                        <center style="line-height:220px;">LOADING ...</center>
                    </ul>
                </div>
            </div>
        </div>
        <div class="movie_xiangqing">
            <div class="date_select" style="height:47px; line-height:47px;"> LOADING ... </div>
            <div class="cinema_all">
                
                <div class="town">
                    <h3>选择城区</h3>
                    <ul>
                        <?php $_from = $this->_var['districts']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'dis');$this->_foreach['dis'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['dis']['total'] > 0):
    foreach ($_from AS $this->_var['dis']):
        $this->_foreach['dis']['iteration']++;
?>
                        <li data-id="<?php echo $this->_var['dis']['id']; ?>" class="<?php if (($this->_foreach['dis']['iteration'] - 1) == 0): ?>active<?php endif; ?>"><?php echo $this->_var['dis']['name']; ?></li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>                   
                    </ul>
                </div>
                
                <div class="select_cinema">
                    <h3>选择影院</h3>
                    <ul>
                        <li>LOADING ...</li>
                    </ul>
                </div>
                
                <div class="cinema_del">
                	<div class="cinema_del_title">
                    	<span>放映时间</span>
                        <span>语言版本</span>
                        <span>放映厅</span>
                        <span>价格</span>
                        <span>选座购票</span>
                    </div>
                    <ul id="planList">
                        <center>LOADING ... </center>
                    </ul>
                </div>
            </div>
            
            <div class="reying_title">
                <div class="reying_bg">影城介绍</div>
            </div>
            <div class="jieshao_img">
                <img src="<?php echo $this->_var['app_path']; ?>images/dongwang/null.jpg" width="200" height="150" class="cinemaThumb">
            </div>
            <div class="jieshao_details">
                <ul>
                    <li>
                        <h3 class="cinemaName"> --- </h3>                        
                    </li>
                    <li>地址：<span class="cinemaAddress"> ---- </span></li>
                    <li>电话：<span class="cinemaTel"> ---- </span></li>
                    <li>营业时间：<span class="opentime"> ---- </span></li>                   
                </ul>
            </div>
        </div>
    </div>
    
    
    
	<?php echo $this->fetch('library/page_footer.lbi'); ?>
    
    
	<?php echo $this->fetch('library/page_left.lbi'); ?>
            
    
    <?php echo $this->fetch('library/page_right.lbi'); ?>
    
        
    <script>
		
		function initMovieList(){
			jQuery(".movie_list").slide({
				titCell: ".hd ul",
				mainCell: ".bd ul",
				autoPage: true,
				effect: "left",
				autoPlay: false,
				vis: 5,
				pnLoop: false				
			});
		}
		
		var movieid = "<?php echo $this->_var['movieid']; ?>";
		var thisCinemaid = "<?php echo $this->_var['cinemaid']; ?>";
		var thisCityid = "<?php echo $this->_var['cityid']; ?>";
		
		// 加载影院
		var currentDistricts = $('.town').find('.active').attr('data-id');
		if(!Utils.isEmpty(currentDistricts) && thisCityid == '0'){
			loadCinema( movieid, currentDistricts, 0);
		}
		// 影片跳转过来的
		if(!Utils.isEmpty(thisCityid)){
			$('.town ul li').each(function(index,dom){		
				if(parseInt($(dom).attr('data-id')) == parseInt(thisCityid)){									
					loadCinema( movieid, thisCityid, thisCinemaid);
					$('.town ul').find('li').removeClass('active');
					$(this).addClass('active');
				}
			});
		}

		// 选择区域
		$('.town ul li').click(function(){
			var disId = $(this).attr('data-id');
			$('.town ul').find('li').removeClass('active');
			$(this).addClass('active');
			loadCinema( movieid, disId, 0);			
		});
		
		// 选择影院		
		$(document).delegate('.select_cinema ul li', 'click', function(){
			var cinemaId = $(this).attr('data-id');
			var cinemaName = $(this).attr('data-name');
			var cinemaAddress = $(this).attr('data-address');
			var cinemaTel = $(this).attr('data-tel');
			var openTime = $(this).attr('data-opentime');	
			var thumb = $(this).attr('data-thumb');	
			
			$('.select_cinema ul').find('li').removeClass('active');
			$(this).addClass('active');
			
			// 设置影院信息
			setCinemaInfo(cinemaId, cinemaName, cinemaAddress, cinemaTel, openTime, thumb);
			// 加载影院的影片列表
			loadMovies(cinemaId);	
			// 加载排期和时间
			loadPlan(cinemaId, movieid, null, '#planList');
						
		});
		
		// 选择排期时间
		$(document).delegate('.date_select div', 'click', function(){
			$('.date_select').find('div').removeClass('active');
			$(this).addClass('active');
			var featuretime = $(this).find('span').html();
			var cinemaid = $(this).find('span').attr('data-cinemaid');
			loadPlan( cinemaid, movieid, featuretime, '#planList');
			
		});		

		// 加载影院列表， movieid、dis 必填， cinemaid为0的话，默认选中第一个
		function loadCinema( movieid, dis, cinemaid){
			$.ajax({
				url:'movie.php',
				type:'POST',
				data:'step=cinemaList&movieid='+movieid+'&dis='+dis+'&cinemaid='+cinemaid,
				beforeSend:function(){
					$('.select_cinema ul').html("<li>LOADING ... </li>");
				},
				success:function( data ){
					var obj = jQuery.parseJSON(data);
					if(obj.error > 0){
						$('.select_cinema ul').html('<li>'+obj.message+'</li>');
					}else{						
						$('.select_cinema ul').html(obj.html);
						
						var cinemaInfo = $('.select_cinema').find('.active');
						var cinemaId = cinemaInfo.attr('data-id');
						var cinemaName = cinemaInfo.attr('data-name');
						var cinemaAddress = cinemaInfo.attr('data-address');
						var cinemaTel = cinemaInfo.attr('data-tel');
						var openTime = cinemaInfo.attr('data-opentime');	
						var thumb = cinemaInfo.attr('data-thumb');							
						
						// 设置影院信息
						setCinemaInfo(cinemaId, cinemaName, cinemaAddress, cinemaTel, openTime, thumb);	
						// 加载影院的影片列表
						loadMovies(obj.cinemaId);	
						// 加载排期和时间
						loadPlan(obj.cinemaId, movieid, null, '#planList');
					}
				}
			});
		}
		// 影片列表
		function loadMovies(cinemaId){
			$.ajax({
				url:'movie.php',
				type:'POST',
				data:'step=movieList&cinemaid='+cinemaId,
				beforeSend:function(){
					$('.picList').html("<center style='line-height:220px; width:1150px;'>LOADING ...</center>");
				},
				success:function( data ){
					var obj = jQuery.parseJSON(data);
					if(obj.error > 0){
						$('.picList').html(obj.message);
					}else{
						$('.picList').html(obj.html);
						initMovieList();						
					}
				}
			});
		}
		
		// 加载影院指定影片排期
		function loadPlan(cinemaid, movieid, currentTime, idClass){
			if(Utils.isEmpty(currentTime)){
				currentTime = 0;
			}
			if(Utils.isEmpty(idClass)){
				idClass = '#plan';
			}
			$.ajax({
				url:'movie.php',
				type:'POST',
				data:'step=planList&cinemaid='+cinemaid+'&movieid='+movieid+'&currentTime='+currentTime,
				beforeSend:function(){
					$(idClass).html("<tr><td colspan='5'> LOADING ... </td></tr>");
					if(currentTime == 0){
						$('.date_select').html('LOADING ...');
					}
				},
				success:function( data ){
					var obj = jQuery.parseJSON(data);
					if(obj.error > 0){
						$(idClass).html(obj.message);
					}else{
						$(idClass).html(obj.html);
					}
					// 时间
					if(currentTime == 0){
						$('.date_select').html(obj.date);
					}
				}
			})
		}
		
		function setCinemaInfo(cinemaId, cinemaName, cinemaAddress, cinemaTel, openTime, thumb){
			$('.cinemaThumb').attr('src',thumb);
			$('.cinemaName').html(cinemaName);
			$('.cinemaAddress').html(cinemaAddress);
			if(!Utils.isEmpty(cinemaTel)){
				$('.cinemaTel').html(cinemaTel);
			}else{
				$('.cinemaTel').html('无');
			}
			if(!Utils.isEmpty(openTime)){
				$('.opentime').html(openTime);
			}else{
				$('.opentime').html('无');
			}
		}

		// 影片跳转
		function goMovie(movie){

			var currentCityid = $('.town').find('.active').attr('data-id');
			var currentCinema = $('.select_cinema').find('.active').attr('data-id');
			window.location.href="movie.php?step=planCinema&movieid="+movie+"&city="+currentCityid+'&cinemaid='+currentCinema;
		}
	
		
	</script>
        
</body>    
    