$(function(){
	$('.nav_box').hover(
		function(){
			$(this).find('b').show();
			$(this).find('.inbox').show();
		},
		function(){
			$(this).find('b').hide();
			$(this).find('.inbox').hide();
		}
	);
	$('.nav_box').each(function(index, element) {
        $(this).find('span').eq(1).css({color:'#fff'});
    }).last().css({'border-right':'none'});
	$('#footer').corner('4px');
	if($('#gla')){
		$('.gla_inbox').corner('8px');
		$('#gla_box>ul').roundabout({
			minOpacity:1,
			btnNext: ".next",
			duration: 1000,
			reflect: false,
			btnPrev: '.prev',
//			autoplay:false,
//			autoplayDuration:5000,
			tilt:0,
			shape: 'figure8',
//			autoplayPauseOnHover: true,
			minScale:0.6, //最后面的图片大小
//			dots:true,
//			arrows:true
			dropAnimateTo:"next"
		});
	}
	$('#sidebar li').eq(0).css({'border-top-color':''});
	$('#sidebar li').each(function(index){
		$(this).hover(
			function(){
				$(this).addClass('li_hover').find('a').css({'color':'#03F'});
			},
			function(){
				$(this).removeClass('li_hover').find('a').css({'color':'#fff'});
			}
		);
	});
	$('.con li:even').css({'background':'#f8fbfc'});
//	 控制图片显示位置
	 $('.sliderSwitch li').on('click', function() {
        var $elem = $(this);
        var index = $elem.index();

        $('#gla_box>ul').roundabout('animateToChild', index);

        return false;
    });

    $('#gla_box>ul').bind({
        animationEnd: function(e) {
            var index = $('#gla_box>ul').roundabout('getChildInFocus');
            $('.sliderSwitch li').removeClass('active');
            $('.sliderSwitch li').eq(index).addClass('active');
        }
    });
    
    $('.business .items li').click(function(){
  		$('.mengc').css({'opacity':'.9','left':'0'});	
    })
    $('.pc').click(function(){   
    	$('.mengc i').html('<h3>电影</h3>全国4000余家高端院线、北京所有影院4-6折观影，支持影院现场刷卡、在线兑换电子码及网上选座等兑换方式，让您方便、快捷、实惠的观影。');
    })
    $('.mobi').click(function(){
    	$('.mengc i').html('<h3>演出</h3>支持全国千余场馆，音乐会、话剧、演唱会、戏曲、综艺、赛事、杂技、亲子儿童等场馆门票及VIP席位，免费送票，安全便捷，让您享受足不出户的一站式娱乐购票方式。');
    })
    $('.sys').click(function(){
    	$('.mengc i').html('<h3>蛋糕</h3>覆盖全国主流城市，整合多种优质品牌，蛋糕品类千余种，支持门店和线上选购，满足员工不同口味的需求，最快3小时送达，让您轻松与家人、朋友分享生日的喜悦。');
    })
    $('.app').click(function(){
    	$('.mengc i').html('<h3>鲜花</h3>提供在线鲜花选购及配送服务，品种多样，花材新鲜，及时、准确速递，满足员工各种用花需求。');
    })
    $('.shenghuo').click(function(){
    	$('.mengc i').html('<h3>生活</h3>为您提供上万种优质商品，包含水果、蔬菜、禽肉、海鲜、粮油蛋奶、方便速食、进口食品、各种饮品、家居用品、厨房用具、家用电器、家纺用品等众多品类，便捷、诚信的服务，安全更放心。');
    })
    $('.changguan').click(function(){
    	$('.mengc i').html('<h3>运动场馆</h3>包含千余家运动场所，员工可根据个人喜好和地理位置选择适合自己的运动项目及场地，可提前预定时间，为企业员工提供健康的生活方式。');
    })
    $('.zhuangbei').click(function(){
    	$('.mengc i').html('<h3>运动装备</h3>包括Nike、Adidas、Reebok Converse、Puma、The North Face、Toread等知名国际品牌，满足员工多种的运动、户外、休闲需求。');
    })
    $('.ticket').click(function(){
    	$('.mengc i').html('<h3>景点门票</h3>涵盖全国千余家景点门票，包含温泉、滑雪、游乐园、公园、旅游景区等，丰富员工的业余生活，健康出行。');
    })
    $('.xiyi').click(function(){
    	$('.mengc i').html('<h3>洗衣</h3>与荣昌、伊尔萨、e袋洗三大品牌重磅合作，支持门店使用及上门服务。将服务标准化，专业的洗涤保养技术、周到的售后服务。覆盖一线城市，近万家门店。');
    })
    $('.tijian').click(function(){
    	$('.mengc i').html('<h3>体检</h3>爱康国宾携手聚优福利为员工提供优质的体检和就医服务，覆盖全国主要城市的合作医院网络和强大的客户服务体系，可在聚优福利平台预订体检套餐，预防疾病、提高整体健康水平。');
    })
    $('.mengc span').click(function(){
    	$('.mengc').css({'opacity':'0','left':'-50%'})
    })
    
    
    var _flag = false; // 全局变量，用于记住鼠标是否在DIV上
 $('.business .items li').mouseover(function(){
 	_flag = true;
 })
 $('.business .items li').mouseout(function(){
 	 _flag = false;
 })
document.body.onclick = function (){
    if(_flag){
     $('.mengc').css({'opacity':'.9','left':'0'})
    }else{
        $('.mengc').css({'opacity':'0','left':'-50%'})
    }
};
});