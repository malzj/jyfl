<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.min.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.2"></script>
<script type="text/javascript" src="http://api.map.baidu.com/library/GeoUtils/1.2/src/GeoUtils_min.js"></script>
<script src="<?php echo $this->_var['app_path']; ?>js/baidumap.js"></script>
<?php if ($this->_var['yunfei']): ?>
<div style="float:left;width:640px;height:580px;border:1px solid gray; " id="showmap"></div>
<div style="float:left;width:120px;">
	<?php $_from = $this->_var['yunfei']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'yf');if (count($_from)):
    foreach ($_from AS $this->_var['yf']):
?>
	<div style="width:100%; margin:5px; font-size:12px;">
    	<span style="display:inline-block; width:50px; height:20px; vertical-align: bottom;margin:3px 0; background:<?php echo $this->_var['yf']['color']; ?>"></span>
        <span style="display:inline-block; width:60px; height:25px; line-height:25px;">运费<font color="red"><?php echo $this->_var['yf']['yunfei']; ?></font>元</span>
    </div>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</div>
<?php else: ?>
<center style="font-size:16px; height:100px; line-height:100px;"> 暂不支持配送 </center>
<?php endif; ?>

<script>
baidumap.setOptions({
	isYunfei:true,
	isSetYunfei:false,
	showMapId:'showmap',
	currentCity:'<?php echo $this->_var['cityinfo']['region_name']; ?>'
});

baidumap.showMap(<?php echo $this->_var['id']; ?>);
</script>