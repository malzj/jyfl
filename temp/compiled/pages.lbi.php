
<form name="selectPageForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
<?php if ($this->_var['pager']['styleid'] == 0): ?>
<div id="pager">
	<?php echo $this->_var['lang']['pager_1']; ?><?php echo $this->_var['pager']['record_count']; ?><?php echo $this->_var['lang']['pager_2']; ?><?php echo $this->_var['lang']['pager_3']; ?><?php echo $this->_var['pager']['page_count']; ?><?php echo $this->_var['lang']['pager_4']; ?> <span> <a href="<?php echo $this->_var['pager']['page_first']; ?>"><?php echo $this->_var['lang']['page_first']; ?></a> <a href="<?php echo $this->_var['pager']['page_prev']; ?>"><?php echo $this->_var['lang']['page_prev']; ?></a> <a href="<?php echo $this->_var['pager']['page_next']; ?>"><?php echo $this->_var['lang']['page_next']; ?></a> <a href="<?php echo $this->_var['pager']['page_last']; ?>"><?php echo $this->_var['lang']['page_last']; ?></a> </span>
<<<<<<< HEAD
	<?php $_from = $this->_var['pager']['search']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item_0_16174800_1463982788');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item_0_16174800_1463982788']):
?>
	<?php if ($this->_var['key'] == 'keywords'): ?>
	<input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo urldecode($this->_var['item_0_16174800_1463982788']); ?>" />
	<?php else: ?>
	<input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo $this->_var['item_0_16174800_1463982788']; ?>" />
=======
	<?php $_from = $this->_var['pager']['search']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item_0_27854100_1463965099');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item_0_27854100_1463965099']):
?>
	<?php if ($this->_var['key'] == 'keywords'): ?>
	<input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo urldecode($this->_var['item_0_27854100_1463965099']); ?>" />
	<?php else: ?>
	<input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo $this->_var['item_0_27854100_1463965099']; ?>" />
>>>>>>> 417bc1f7583b2d9bb44e4019b7d69a568226f5f2
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	<select name="page" id="page" onchange="selectPage(this)">
	<?php echo $this->html_options(array('options'=>$this->_var['pager']['array'],'selected'=>$this->_var['pager']['page'])); ?>
	</select>
</div>
<?php else: ?>


<div id="pager" class="manu">
	<?php if ($this->_var['pager']['page_count'] != 1): ?>
	<!-- <span class="f_l f6" style="margin-right:10px;"><?php echo $this->_var['lang']['pager_1']; ?><b><?php echo $this->_var['pager']['record_count']; ?></b> <?php echo $this->_var['lang']['pager_2']; ?></span> -->
	<?php if ($this->_var['pager']['page_first']): ?>
	<a href="<?php echo $this->_var['pager']['page_first']; ?>">1 ...</a>
	<?php endif; ?>
	<?php if ($this->_var['pager']['page_prev']): ?>
	<a class="prev" href="<?php echo $this->_var['pager']['page_prev']; ?>"> < </a>
	<?php else: ?>
	<a class="prev" href="javascript:;"> < </a>
	<?php endif; ?>
	<?php if ($this->_var['pager']['page_count'] != 1): ?>
<<<<<<< HEAD
	<?php $_from = $this->_var['pager']['page_number']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item_0_16214200_1463982788');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item_0_16214200_1463982788']):
=======
	<?php $_from = $this->_var['pager']['page_number']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item_0_27911900_1463965099');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item_0_27911900_1463965099']):
>>>>>>> 417bc1f7583b2d9bb44e4019b7d69a568226f5f2
?>
	<?php if ($this->_var['pager']['page'] == $this->_var['key']): ?>
	<span class="current"><?php echo $this->_var['key']; ?></span>
	<?php else: ?>
<<<<<<< HEAD
	<a href="<?php echo $this->_var['item_0_16214200_1463982788']; ?>"><?php echo $this->_var['key']; ?></a>
=======
	<a href="<?php echo $this->_var['item_0_27911900_1463965099']; ?>"><?php echo $this->_var['key']; ?></a>
>>>>>>> 417bc1f7583b2d9bb44e4019b7d69a568226f5f2
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	<?php endif; ?>
	<?php if ($this->_var['pager']['page_next']): ?>
	<a class="next" href="<?php echo $this->_var['pager']['page_next']; ?>"> > </a>
	<?php else: ?>
	<a class="next" href="javascript:;"> > </a>
	<?php endif; ?>
	<?php if ($this->_var['pager']['page_last']): ?><a class="last" href="<?php echo $this->_var['pager']['page_last']; ?>">...<?php echo $this->_var['pager']['page_count']; ?></a><?php endif; ?>
	<?php if ($this->_var['pager']['page_kbd']): ?>
<<<<<<< HEAD
	<?php $_from = $this->_var['pager']['search']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item_0_16240300_1463982788');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item_0_16240300_1463982788']):
?>
	<?php if ($this->_var['key'] == 'keywords'): ?>
	<input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo urldecode($this->_var['item_0_16240300_1463982788']); ?>" />
	<?php else: ?>
	<input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo $this->_var['item_0_16240300_1463982788']; ?>" />
=======
	<?php $_from = $this->_var['pager']['search']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item_0_27949500_1463965099');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item_0_27949500_1463965099']):
?>
	<?php if ($this->_var['key'] == 'keywords'): ?>
	<input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo urldecode($this->_var['item_0_27949500_1463965099']); ?>" />
	<?php else: ?>
	<input type="hidden" name="<?php echo $this->_var['key']; ?>" value="<?php echo $this->_var['item_0_27949500_1463965099']; ?>" />
>>>>>>> 417bc1f7583b2d9bb44e4019b7d69a568226f5f2
	<?php endif; ?>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	<kbd style="float:left; margin-left:8px; position:relative; bottom:3px;"><input type="text" name="page" onkeydown="if(event.keyCode==13)selectPage(this)" size="3" class="B_blue" /></kbd>
	<?php endif; ?>
	<?php endif; ?>
</div>


<?php endif; ?>
</form>
<script type="Text/Javascript" language="JavaScript">
<!--

function selectPage(sel)
{
  sel.form.submit();
}

//-->
</script>