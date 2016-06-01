<p class="clearfix pb9">
	<span class="searcher"><input type="text" class="input c_a5" placeholder="输入影院名称快速定位"><i class="sub"></i></span><span class="lh25">当前共有135家影院</span>
</p>
<div class="arealist">
<?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'row');if (count($_from)):
    foreach ($_from AS $this->_var['row']):
?>
    <p class="oftenlist alist"><span><?php echo $this->_var['row']['districtName']; ?></span><a href="javaScript:;" data-id="<?php echo $this->_var['movieid']; ?>" data-cid="<?php echo $this->_var['row']['cinemaId']; ?>" data-type="cinema"><?php echo $this->_var['row']['cinemaName']; ?></a></p>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
   
</div>