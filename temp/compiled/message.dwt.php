<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Keywords" content="<?php echo $this->_var['keywords']; ?>" />
<meta name="Description" content="<?php echo $this->_var['description']; ?>" />
<meta name="Description" content="<?php echo $this->_var['description']; ?>" />
<?php if ($this->_var['auto_redirect']): ?>
<meta http-equiv="refresh" content="3;URL=<?php echo $this->_var['message']['back_url']; ?>" />
<?php endif; ?>
<title><?php echo $this->_var['page_title']; ?></title>
<link href="/css/hy_master.css" rel="stylesheet" type="text/css" />
<link href="/css/hy_layout.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript"  src="/js/nav.js"></script>
</head>
<body>

<?php echo $this->fetch('library/page_header.lbi'); ?>


<div class="main_tiaozhuan">
	<div class="tiaozhuan">
		<!--<p class="tiao_img"><img src="/images/hy_tz_ren.jpg" width="334" height="148" alt="" /></p>-->
		<p class="tiao_t1" style="padding-top:20px;"><?php echo $this->_var['message']['content']; ?></p>
		<p class="tiao_t1" style="margin-top:15px;">
			<?php if ($this->_var['message']['url_info']): ?>
			<?php $_from = $this->_var['message']['url_info']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('info', 'url');if (count($_from)):
    foreach ($_from AS $this->_var['info'] => $this->_var['url']):
?>
			<a href="<?php echo $this->_var['url']; ?>"><?php echo $this->_var['info']; ?></a>&nbsp;&nbsp;&nbsp;
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
			<?php endif; ?>
		</p>
	</div>
	<div class="clear"></div>
</div>



<?php echo $this->fetch('library/page_footer.lbi'); ?>


</body>
</html>
