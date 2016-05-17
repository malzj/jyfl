<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.nicescroll.js"></script>
<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/layer/layer.js"></script>
<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/login_layer.js"></script>
<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/laydate/laydate.js"></script>
<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jyflapi.js"></script>
<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/file_img.js"></script>
<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/ajaxfileupload.js"></script>
<script src="<?php echo $this->_var['app_path']; ?>js/utils.js"></script>
<input type="hidden" value="1" id="user_id" />
<input type="hidden" value="" id="img" />
<div class="pop_left">
    <ul class="list_main">
    	<li class="shouye">
        	<a class="home_href" href="user.php">
        		<div class="li_img">
                <span class="glyphicon glyphicon-home"></span>
                <p>首页</p>
                </div>
            </a>            
        </li>
        <li class="gn">
            <div class="li_img">	
				<span class="glyphicon glyphicon-credit-card"></span>
				<p>功能</p>
			</div>
            <div class="gn_1">
                <ul class="list_1">
                <?php $_from = $this->_var['navigator_list']['middle']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['nav']):
        $this->_foreach['foo']['iteration']++;
?>
                <li class="movie" onClick="window.location='<?php echo $this->_var['nav']['url']; ?>'">
                	<div>
                    	<span class="movie_1"><img src="/images/juyoufuli/img_login/yundong.png"></span>
                		<i><?php echo $this->_var['nav']['name']; ?></i>
                	</div>
                </li>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>  
                </ul>
            </div>
        </li>
        
       <li class="per">
            <div class="li_img">
                <span class="glyphicon glyphicon-user"></span>	
                <p>个人信息</p>
            </div>
            <div class="per_1">
                <ul class="list_2">
                <li id="per"><div><span>个人资料</span></div></li>
                <li id="saf"><div><span>安全中心</span></div></li>
                <li id="shouhuo" onclick="showAddress()"><div><span>收货信息</span></div></li>
                <li id="red_packet" onclick="showPack()"><div><span>我的红包</span></div></li>
                <li id="reg"><div><span>卡充值</span></div></li>
                <li id="merge"><div><span>卡合并</span></div></li>
                </ul>
            </div>
        </li>
        <li class="car">
            <div class="li_img">
                <span class="glyphicon glyphicon-shopping-cart"></span>
                <p>购物车</p>
            </div>
            <div class="car_1">	</div>
        </li>
        <li class="order">
            <div class="li_img">
                <span class="glyphicon glyphicon-list-alt"></span>
                <p>订单</p>
            </div>
            <div class="order_1">
            </div>
        </li>
    </ul>
    <a class="switch off"></a>
</div>

<script>
//	首页左侧公共部分默认展开
	if(window.location.pathname == '/user.php'){
			$('.switch').removeClass('off');
	}
	$('.list_1 li').hover(function(){
		$(this).addClass('active').siblings().removeClass('active');
		})
//	公共左侧部分展示或者隐藏
	$('.switch').click(function(){
		$(this).toggleClass('off');
		if($(this).hasClass('off')){
			
			$('.list_main').animate({left:-100},'slow');	
			}else{
				$('.list_main').animate({left:0},'slow');	
			}
		})
</script>