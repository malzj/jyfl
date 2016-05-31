<link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/hy_master.css">
<link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/fixed.css">
<link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/reset.css">
<link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/public.css">
<link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/login_layer.css">
<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.nicescroll.js"></script>

<div class="header">
    <ul class="header_nav">
        <li class="m_left"><img src="<?php echo $this->_var['app_path']; ?>images/juyoufuli/img_login/logo.png" alt="聚优福利"></li>
        <li><span class="adress"><span class="glyphicon glyphicon-map-marker"></span><a href="javascript:;"><?php echo $this->_var['cityinfo']['region_name']; ?></a></span></li>
        <ul>
            <li class="username">
                <img  src="<?php echo $this->_var['usernames']['pic']; ?>" alt="用户头像">
                <span><?php echo $this->_var['usernames']['user_name']; ?></span>
            </li>
            <li class="balance">余额：<span><?php echo $this->_var['usernames']['card_money']; ?></span>点</li>
            <li class="exit">【<a href="user.php?act=logout">退出</a>】</li>
        </ul>
    </ul>
    <div class="bg_top"></div>
</div>
<div id="ui_city_plugs" class="ui_city_plugs none clear" style="position: absolute; left: 180px; top:50px; z-index:120;">
    <div style="position:relative;" class="inner"> <span title="关闭" class="ui_close"></span>
        <div class="ui_city_cType clear">
            <ul>
               <li style="color:#27C7AC;">全国热门城市</li>
            </ul>
        </div>
        <?php $_from = $this->_var['citys']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'city');$this->_foreach['city'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['city']['total'] > 0):
    foreach ($_from AS $this->_var['key'] => $this->_var['city']):
        $this->_foreach['city']['iteration']++;
?>
        <?php if ($this->_var['key'] == 'hot'): ?>
        <div id="city_<?php echo $this->_var['key']; ?>_content" class="ui_city_List clear">
            <div class="inner clear">
                <ul class="clear">
                    <?php $_from = $this->_var['city']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'c');if (count($_from)):
    foreach ($_from AS $this->_var['c']):
?>
                    <li><a href="<?php echo $this->_var['app_path']; ?>user.php?cityid=<?php echo $this->_var['c']['region_id']; ?>"<?php if ($this->_var['c']['region_id'] == $this->_var['cityid']): ?> class="select"<?php endif; ?> hidefocus="true"><?php echo $this->_var['c']['region_name']; ?></a></li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </ul>
            </div>
        </div>
        <?php else: ?>
        <div id="city_<?php echo $this->_var['key']; ?>_content" class="ui_city_List clear">
            <div class="inner clear">
                <?php $_from = $this->_var['city']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('k', 'c');$this->_foreach['c'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['c']['total'] > 0):
    foreach ($_from AS $this->_var['k'] => $this->_var['c']):
        $this->_foreach['c']['iteration']++;
?>
                <dl class="<?php if (($this->_foreach['c']['iteration'] <= 1)): ?>mt0<?php endif; ?>">
                    <dt><span><?php echo $this->_var['k']; ?></span></dt>
                    <dd>
                        <ul>
                            <?php $_from = $this->_var['c']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
                            <li><a<?php if ($this->_var['item']['region_id'] == $this->_var['cityid']): ?> class="select"<?php endif; ?> href="index.php?cityid=<?php echo $this->_var['item']['region_id']; ?>" hidefocus="true"><?php echo $this->_var['item']['region_name']; ?></a></li>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </ul>
                    </dd>
                </dl>
                <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </div>
</div>

<script>
$("#ui_city_plugs").niceScroll({  
	cursorcolor:"#BFB1B1",  
	cursoropacitymax:1,  
	touchbehavior:false,  
	cursorwidth:"5px",  
	cursorborder:"0",  
	cursorborderradius:"5px"  
}); 

$('.adress').click(function(event){
	event.stopPropagation();
	$('#ui_city_plugs').toggleClass('none');
	if($('#ui_city_plugs').hasClass('none')){
		$('.adress a').css('background','url(<?php echo $this->_var['app_path']; ?>images/juyoufuli/img_login/area-01.png) no-repeat 45px');
	}else{
		$('.adress a').css('background','url(<?php echo $this->_var['app_path']; ?>images/juyoufuli/img_login/area-02.png) no-repeat 45px');
	}
})
				$(document).click(function(){
					$('#ui_city_plugs').addClass('none');
						if($('#ui_city_plugs').hasClass('none')){
						$('.adress a').css('background','url(<?php echo $this->_var['app_path']; ?>images/juyoufuli/img_login/area-01.png) no-repeat 45px');
					}else{
						$('.adress a').css('background','url(<?php echo $this->_var['app_path']; ?>images/juyoufuli/img_login/area-02.png) no-repeat 45px');
					}
									}) 

</script>