/**
 *  百度地图 js 
 */
var baidumap = {
	
	options :{		
		// 显示多边形的api地址
		showUrl:'http://www.juyoufuli.com/jyflapi/index.php?s=Peisong/Peisong/showmap_wap',
		// 设置配送费用和供应商信息
		setPeisongUrl:'http://jy.com/jyflapi/index.php?s=Peisong/Peisong/savaPeiSongMap',
		// 设置坐标信息的
		setZuobiaoUrl:'http://jy.com/jyflapi/index.php?s=Peisong/Peisong/savePeiSongMapZuoBiao',
		//供应商运费保存的input表单ID, 默认是sup
		idClassFirst:'sup',
		//是否搜索运费
		isYunfei:false,
		//是否设置运费到对应的input表单
		isSetYunfei:false,
		// 当前城市
		currentCity:'北京',
		//显示地图的元素类
		showMapId:'map',
		//是否是时间数据, 1 配送运费，2配送时间
		isTime:1,
		//回调
		afterFunction:function(d){
			jQuery.noop();
		}
		
	},
	
	setOptions:function(options){
		this.options.idClassFirst = options.idClassFirst || this.options.idClassFirst;
		this.options.isYunfei = options.isYunfei || this.options.isYunfei;
		this.options.isSetYunfei = options.isSetYunfei || this.options.isSetYunfei;
		this.options.currentCity = options.currentCity || this.options.currentCity;
		this.options.showMapId = options.showMapId || this.options.showMapId;
		this.options.afterFunction = options.afterFunction || this.options.afterFunction;
		this.options.isTime = options.isTime || this.options.isTime;
	},
	
	// 显示指定供应商配送范围的地图
	showMap:function(supplierid,address){		
		_this = this;
		var ply;
		var ply1 = []; //多边形
		var address = address || '';		
		
		var total, currentNum; 
		
		var map = new BMap.Map(_this.options.showMapId);
		map.centerAndZoom(this.options.currentCity,10);
		map.enableScrollWheelZoom(); //开启滚动缩放
		map.enableContinuousZoom(); //开启缩放平滑
		
		
		// ajax 获取当前供应商的地图坐标信息
		jQuery.ajax({
			type: "get",
			url: _this.options.showUrl,
			async: false,
			data: {gongyingshang_id:supplierid,isTime:_this.options.isTime},
			dataType: "jsonp",
			jsonp:'jsoncallback',
			success: function(data) {				
				if(data.list == null){
					data.list = [];
				}
				var mapnum = total = data.list.length;
					
				var list = data.list;
				var num = 0;
				for (num; num < mapnum; num++) {
					currentNum = num;
					var yanse = list[num]['yanse']
					var pts = [];
					var zuobiaolist = list[num]['zuobiao'];
					var zuobiaonum = list[num]['dianshu'];
					// 价格
					var jiage = list[num]['jiage'];
					// 最晚配送时间
					var shipping_end = list[num]['shipping_end'];
					// 最早配送时间
					var shipping_start = list[num]['shipping_start'];
					// 提前预订时间
					var shipping_waiting = list[num]['shipping_waiting'];
					// 间隔时间段
					var shipping_booking = list[num]['shipping_booking'];
					// 配送时间还是运费
					var isTime = list[num]['isTime'];
					
					var zbnum = 0;
					for (zbnum; zbnum < zuobiaonum; zbnum++) {
						var lng = zuobiaolist[zbnum]['lng'];
						var lat = zuobiaolist[zbnum]['lat'];
						var pt1 = new BMap.Point(lng, lat);
						pts.push(pt1);
					};
					ply = new BMap.Polygon(pts, {
						strokeColor: yanse,
						fillColor: yanse,
						strokeWeight: 3, //边线的宽度，以像素为单位。
						strokeOpacity: 0.8, //边线透明度，取值范围0 - 1。
						fillOpacity: 0.6, //填充的透明度，取值范围0 - 1。
						strokeStyle: 'solid' //边线的样式，solid或dashed。
					});
					var jieguo = [];
					jieguo['ply'] = ply;
					jieguo['jiage'] = jiage;
					jieguo['shipping_start'] = shipping_start;
					jieguo['shipping_end'] = shipping_end;
					jieguo['shipping_waiting'] = shipping_waiting;
					jieguo['shipping_booking'] = shipping_booking;
					jieguo['isTime'] = isTime;
										
					ply1.push(jieguo);
					
					//演示：将面添加到地图上					
					map.addOverlay(ply);
				}
			},
		});	
		
		var b = setInterval(function(){
			if(currentNum+1 == total){
				// 如果有地址的话，搜索运费
				if(address !='' && _this.options.isYunfei == true){
					_this.searchYunfei(address,map,ply1,supplierid);
				}
				clearTimeout(b);
			}			
		},100);		

	},
	
	// 搜索地址
	searchYunfei:function(address,map,ply1,supplierid){		
		_this = this;	
		var myGeo = new BMap.Geocoder();
		myGeo.getPoint(address, function(point) {
			if (point) {
				map.centerAndZoom(point, 11);
				map.addOverlay(new BMap.Marker(point));
				// 如果是配送时间数据，使用回调处理这些数据。
				if(_this.options.isTime == 2){
					_this.backTimes(point.lng, point.lat, ply1,supplierid);
				// 是运费的话，计算并设置运费。
				}else{
					_this.InOrOutPolygon(point.lng, point.lat, ply1,supplierid);
				}			
				
			} else {
				mui.alert('您选择地址没有解析到结果!');
			}
		}, "中国");		
	},
	
	// 配送时间数据
	backTimes:function(lng, lat, ply1, supplierid){
		_this = this;
		var backData = [];
		var pt = new BMap.Point(lng, lat);
		var result;
		for (var resultnum = 0; resultnum < ply1.length; resultnum++) {			
			backData['shipping_start'] = ply1[resultnum]['shipping_start'];
			backData['shipping_end'] = ply1[resultnum]['shipping_end'];
			backData['shipping_waiting'] = ply1[resultnum]['shipping_waiting'];
			backData['shipping_booking'] = ply1[resultnum]['shipping_booking'];
			backData['supplier_id'] = supplierid;
			result = BMapLib.GeoUtils.isPointInPolygon(pt, ply1[resultnum]['ply']);
			if (result) {
				break;
			};
		}

		if (result == true) {
			this.options.afterFunction(backData);
		} else {
			mui.alert('当前地址不支持配送！');
		}
	},
	// 计算运费
	InOrOutPolygon:function(lng, lat, ply1, supplierid){
		_this = this;
		var jiage;
		var pt = new BMap.Point(lng, lat);
		var result;
		for (var resultnum = 0; resultnum < ply1.length; resultnum++) {			
			jiage = ply1[resultnum]['jiage'];
			result = BMapLib.GeoUtils.isPointInPolygon(pt, ply1[resultnum]['ply']);
			if (result) {
				break;
			};
		}
		if (result == true) {
			// 是否需要将运费设置到input框中
			_this.options.isSetYunfei == true ? _this.setYunfei(jiage, supplierid):'';
		} else {
			_this.options.isSetYunfei == true ? _this.setYunfei(-1, supplierid):'';
		}
	},
	
	// 设置运费模板
	setYunfei:function(jiage, supplierid){
		jQuery('#'+this.options.idClassFirst+"_"+supplierid).val(jiage);
		if(jiage == -1){
			jQuery('#yunfei'+supplierid).html(0);		
			jQuery('#address-'+supplierid).css({'border':'1px solid red'});
		}else{
			jQuery('#yunfei'+supplierid).html(jiage);
			jQuery('#address-'+supplierid).css({'border':'0'});
		}

		// 每次执行都要执行的操作
		this.options.afterFunction(jiage);
	}
	
}