function userShow(data){
	var user_id = data;
	var rudata ="";
	var user_id = $('#user_id').val();
            var username ;
            var nickname ;//用户名
            var sex ;//性别
            var birthday;//生日
            var basic;//个人情况
            var xingqu; //兴趣
            var xingqu_1;
            var img;
	var url="http://192.168.1.161/jyflapi/index.php?s=Users/User/userShow"
    $.ajax({
		type:"post",
		url:url,
		async:false,
		data:{user_id:user_id},
		dataType:"json",
		success:function(data){
			if(data.result=='true'){
			  username = data.business.user_name;
             nickname = data.business.nickname;//用户名
             sex = data.business.sex;//性别
             birthday=data.business.birthday;//生日
            basic=data.business.basic;//个人情况
             xingqu=data.business.xingqu //兴趣
             xingqu_1=xingqu.split(',');
             img=data.business.pic;	
			rudata=data;
			return ;
			}
	},
	});
layer.open({
        type: 1,
        title:false,
        area:'570px',
        shadeClose:false, //点击遮罩关闭
        content:'<div class="per"><h3>个人信息</h3><div class="wrap_1"><div class="left"><div class="form-group">卡号：<span class="card_num" id="card_id"></span></div><div class="form-group"><label>用户名：</label><span class="username"><input type="text" id="username"></span></div><div class="form-group"><label>性别：</label><span id="male" class="radio_img radio_left"><input type="radio" name="Sex" value="1"></span>男 <span id="female" class="radio_img"><input type="radio" name="Sex" value="2"></span>女</div><div class="form-group" style="position:relative">生日： <input onclick="laydate()" class="birth" id="birth"><label class="date_ico laydate-icon" for="birth"></label></div><div class="form-group" id="geren">个人情况： <span class="radio_img" id="danshen"><input type="radio" name="per" value="单身"></span>单身 <span class="radio_img" id="love"><input type="radio" name="per" value="恋爱中"></span>恋爱中 <span class="radio_img" id="yihun"><input type="radio" name="per" value="已婚"></span>已婚 <span class="radio_img" id="baomi"><input type="radio" name="per" value="保密"></span>保密</div><form action="" method="get">兴趣： <span id="meishi" class="checkbox_img radio_left"><input name="favorite" type="checkbox" value="美食"></span>美食 <span id="dianying" class="checkbox_img"><input name="favorite" type="checkbox" value="电影"></span>电影 <span id="jiudian" class="checkbox_img"><input name="favorite" type="checkbox" value="酒店"></span>酒店 <span id="xiuxian" class="checkbox_img"><input name="favorite" type="checkbox" value="休闲娱乐"></span>休闲娱乐 <span id="liren" class="checkbox_img"><input name="favorite" type="checkbox" value="丽人"></span>丽人 <span id="lvyou" class="checkbox_img"><input name="favorite" type="checkbox" value="旅游"></span>旅游</form></div><div class="right"><form id="uploadForm"><div class="right_img" id="localImag"><img id="preview" src="images/img_login/touxiang_shang.png" width="120" height="120"></div><span class="file"><input type="file" name="file" id="doc" onchange="setImagePreview(this)"></span><span class="file_yes"><input type="button" value="确认" onclick="upload()" /></span></form></div></div><div style="text-align:center"><button id="save">保存</button></div></div>'
    });
               $('#preview').attr('src',img);
            
            $('#card_id').text(username);
            $('#username').val(nickname);
//			性别
			if(sex==1){
		      $('#male').addClass('on');
		      $('#male>input').attr('checked',true);
			}else{
				 $('#female').addClass('on');
				  $('#female>input').attr('checked',true);
			}
			$('#birth').val(birthday);
//			个人情况
             switch (basic){
				case '已婚':
				 $('#yihun').addClass('on');
								$('#yihun>input').attr('checked',true);
								break;
				case '单身':
				 $('#danshen').addClass('on');
								$('#danshen>input').attr('checked',true);
				  break;
				case '恋爱中':
				  $('#love').addClass('on');
								$('#love>input').attr('checked',true);
				  break;
				case '保密':
				 $('#baomi').addClass('on');
								$('#baomi>input').attr('checked',true);
				  break;	
				};
				//个人情况点击增加checked
				$('#geren span').click(function(){
					$(this).children('input').attr('checked',true).parent('span').siblings().children('input').attr('checked',false);
				})
//				兴趣点击增加checked
				$('.checkbox_img').click(function(){
					if($(this).children('input').is(":checked")){
						$(this).children('input').attr("checked",true);
					}else{
						$(this).children('input').removeAttr("checked",true);
					}
				})
				
				//兴趣
				
				$("input:checkbox").click(function() {
					var xingqu = "";
					$("input:checkbox[name='favorite']:checked").each(function() {
						xingqu += $(this).val() + ",";
						
					});
					$('#xingqu').val(xingqu);
					console.log(("已选择兴趣："+xingqu));
					
				});
				
					$(":checkbox[name='favorite']").prop("checked",false);
					var ck_val =xingqu_1;
					for(var i=0;i<ck_val.length;i++){
						switch(ck_val[i]){
					     case '美食':
				         $('#meishi').addClass('on_check');
						 $('#meishi>input').attr('checked','checked');
				         break;
				         case '电影':
				         $('#dianying').addClass('on_check');
						 $('#dianying>input').attr('checked', 'checked');
				         break;	
				         case '酒店':
				         $('#jiudian').addClass('on_check');
						 $('#jiudian>input').attr('checked', 'checked');
				         break;	
				         case '休闲娱乐':
				         $('#xiuxian').addClass('on_check');
						 $('#xiuxian>input').attr('checked', 'checked');
				         break;	
				         case '丽人':
				         $('#liren').addClass('on_check');
						 $('#liren>input').attr('checked', 'checked');
				         break;	
				         case '旅游':
				         $('#lvyou').addClass('on_check');
						 $('#lvyou>input').attr('checked', 'checked');
				         break;	
						}
					}
	
	
};
function upload(data){
	 var formData = new FormData($( "#uploadForm" )[0]);  
	 var rudata ='';
     $.ajax({  
          url: 'http://192.168.1.161/jyflapi/index.php?s=Upload/Upload/upload' ,  
          type: 'POST',  
          data: formData,  
          async: false,  
          cache: false,  
          contentType: false,  
          processData: false,  
          success: function (returndata) {
        	$('#img').val(returndata.img)
                 rudata= returndata;
          },  
          error: function (returndata) {  
        		console.log(returndata)
              
          }  
     });  
	return rudata;
}
function userSave(){
//	var rudata='';
    var user_id=$('#user_id').val();
    var nickname=$('#username').val();
    var sex=$('input[name="Sex"]:checked').val();
    var birthday=$('#birth').val();
    var basic=$('input[name="per"]:checked').val();
    var xingqu;
    var xingqu_1=$('#xingqu').val().split(',');
    if (xingqu_1==""||xingqu_1==null){
    	xingqu=$('input[type="checkbox"]:checked').val();
    	console.log(xingqu+'999');
    }else{
    	xingqu =$('#xingqu').val();
    }
//  判断是否有图片
    var pic ='';
    var img = $('#img').val();
    if(img==""||img==null){
    	pic= $('#preview').attr('src');
    }else{
    	pic=$('#img').val();
    	    }
    console.log(sex,basic,xingqu);
	var url="http://192.168.1.161/jyflapi/index.php?s=Users/User/userUpdate"
    $.ajax({
		type:"post",
		url:url,
		async:false,
		data:{user_id:user_id,nickname:nickname,sex:sex,birthday:birthday,xingqu:xingqu,basic:basic,pic:pic},
		dataType:"json",
		success:function(data){
			console.log(data.result)
			if(data.result=='true'){
              console.log(data.result)
			return ;
			}
	},
	});
//	return rudata;
	
	
}
