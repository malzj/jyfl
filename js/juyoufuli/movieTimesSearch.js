/**
 *  电影栏目，搜索影、影院、排期
 */

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
	var featuretime = $(this).attr('data-strtotime');
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
		url:'movie_times.php',
		type:'POST',
		data: param,
		beforeSend:function(){
			$(idClass).html("<center><img src='/images/juyoufuli/img_login/loading.gif'></center>");
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
//jQuery(".picScroll-left").slide({titCell:".hd ul",mainCell:".bd ul",autoPage:true,effect:"left",autoPlay:true,scroll:3,vis:3});				

jQuery(".will_list").slide({
	titCell: ".hd ul",
	mainCell: ".bd ul",
	autoPage: true,
	effect: "left",
	autoPlay: false,
	vis: 3,
	pnLoop: false
});	

// 电影页面固定在顶部的头
jQuery(function($) {
	$(document).ready(function() {
		$('#ticketSearchFixDiv').stickUp();
	});
});
