<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php $_from = $this->_var['featureTimes']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'featureTime');if (count($_from)):
    foreach ($_from AS $this->_var['featureTime']):
?>
<div class="<?php if ($this->_var['featureTime'] == $this->_var['currentTime']): ?>active<?php endif; ?>"><span class="date" data-cinemaid="<?php echo $this->_var['cinemaid']; ?>"><?php echo $this->_var['featureTime']; ?></span></div>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
