$(function(){
	//默认值
	//$("#carousel1").css("background-color","rgb(198, 198, 198)");	//第一张图的背景
	//$(".car_c_title p").html("<花千骨>霍建华教古琴迷倒赵丽颖订单订单");	//第一张图的标题
	//以下数组中的颜色与主图两边颜色同步的背景色(不同用户用脑分辨率不同导致图片无法铺满时,通过背景色的补充,起到铺满的作用)
	var rgb_arr = new Array();	
	//所有标题
	var title_arr = new Array();
	var t;
	//自动轮播方法
	function roll(rgb_arr,title_arr){
		t= setTimeout(function(){	
			for(var i=0;i<=$('.car_c_smallpic img').length-1;i++){
				var nowpic = $("#carousel1 .car_bigp img").eq(i);				
				if(nowpic.css("display") == "block"){
					var showpicnum = i*1+1*1;
					$("#carousel1 .car_bigp img").css("display","none");	//先隐藏所有主图
					$(".car_c_smallpic img").css("border","");	//先隐藏所有小图边框
					if(showpicnum == $('.car_c_smallpic img').length){
						//最后一张时,跳转到第一张
						$("#carousel1 .car_bigp img").eq(0).css("display","block");	
						$("#carousel1").css("background-color",rgb_arr[0]);
						$("#carousel1").attr("name",0);
						$(".car_c_smallpic img").eq(0).css("border","solid 2px Darkorange");
//						$(".car_c_title p").html(title_arr[0]);
					}else{
						$("#carousel1 .car_bigp img").eq(showpicnum).css("display","block");	//主图
						$("#carousel1").css("background-color",rgb_arr[showpicnum]);	//主图背景
						$("#carousel1").attr("name",showpicnum);	//给div一个name值,代表当前自动轮到到哪张图,鼠标轮播时会用到这个值
						$(".car_c_smallpic img").eq(showpicnum).css("border","solid 2px Darkorange");	//小图边框
//						$(".car_c_title p").html(title_arr[showpicnum]);	//标题	
					}
					break;
				}
			}
			roll(rgb_arr,title_arr);
		},3000);
	}
	
	$('#carousel1').hover(
		function(){
			clearTimeout(t)
		},function roll(rgb_arr,title_arr){
		t= setTimeout(function(){	
			for(var i=0;i<=$('.car_c_smallpic img').length-1;i++){
				var nowpic = $("#carousel1 .car_bigp img").eq(i);				
				if(nowpic.css("display") == "block"){
					var showpicnum = i*1+1*1;
					$("#carousel1 .car_bigp img").css("display","none");	//先隐藏所有主图
					$(".car_c_smallpic img").css("border","");	//先隐藏所有小图边框
					if(showpicnum == $('.car_c_smallpic img').length){
						//最后一张时,跳转到第一张
						$("#carousel1 .car_bigp img").eq(0).css("display","block");	
						$("#carousel1").css("background-color",rgb_arr[0]);
						$("#carousel1").attr("name",0);
						$(".car_c_smallpic img").eq(0).css("border","solid 2px Darkorange");
//						$(".car_c_title p").html(title_arr[0]);
					}else{
						$("#carousel1 .car_bigp img").eq(showpicnum).css("display","block");	//主图
						$("#carousel1").css("background-color",rgb_arr[showpicnum]);	//主图背景
						$("#carousel1").attr("name",showpicnum);	//给div一个name值,代表当前自动轮到到哪张图,鼠标轮播时会用到这个值
						$(".car_c_smallpic img").eq(showpicnum).css("border","solid 2px Darkorange");	//小图边框
//						$(".car_c_title p").html(title_arr[showpicnum]);	//标题	
					}
					break;
				}
			}
			roll(rgb_arr,title_arr);
		},3000);
	}
	)
	roll(rgb_arr,title_arr);
	//点击左箭头
	$(".car_c_inpt1").click(function(){
		var showpicnum = $("#carousel1").attr("name");	//当前选中的图片eq编号
		if(showpicnum <= 0){
			var eqnum = $('.car_c_smallpic img').length-1;
		}else{
			var eqnum = showpicnum-1;
		}
		mouseRoll(rgb_arr,title_arr,eqnum);
		
	});
	//点击右箭头
	$(".car_c_inpt2").click(function(){
		var showpicnum = $("#carousel1").attr("name");	//当前选中的图片eq编号
		if(showpicnum >= $('.car_c_smallpic img').length-1){
			var eqnum = 0;
		}else{
			var eqnum = showpicnum*1+1*1;
		}
		mouseRoll(rgb_arr,title_arr,eqnum);
	});
	//鼠标滑过小图
	$(".car_c_smallpic img").hover(function(){
		var eqnum = $(this).index();
		mouseRoll(rgb_arr,title_arr,eqnum);
	});
	//鼠标事件轮播方法
	function mouseRoll(rgb_arr,title_arr,eqnum){
		$(".car_c_smallpic img").css("border","");	//隐藏所有小图边框
		$(".car_c_smallpic img").eq(eqnum).css("border","solid 2px Darkorange");	//显示当前选中的小图边框
		$("#carousel1 .car_bigp img").css("display","none");	//隐藏所有主图
		$("#carousel1 .car_bigp img").eq(eqnum).css("display","block");	//显示当前选中的主图
		$("#carousel1").css("background-color",rgb_arr[eqnum]);	//显示当前选中的主图背景
		$(".car_c_title p").html(title_arr[eqnum]);	//标题
		$("#carousel1").attr("name",eqnum);	//最新的name值
	}


	
})