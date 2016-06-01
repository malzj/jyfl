<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php $_from = $this->_var['cinemas']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'cinema');if (count($_from)):
    foreach ($_from AS $this->_var['cinema']):
?>
<li data-name="<?php echo $this->_var['cinema']['cinemaName']; ?>" data-address="<?php echo $this->_var['cinema']['cinemaAddress']; ?>" data-tel="<?php echo $this->_var['cinema']['cinemaTel']; ?>" data-opentime="<?php echo $this->_var['cinema']['openTime']; ?>" data-id="<?php echo $this->_var['cinema']['cinemaId']; ?>" data-thumb="<?php echo $this->_var['cinema']['logo']; ?>" class="<?php if ($this->_var['cinema']['cinemaId'] == $this->_var['cinemaId']): ?>active<?php endif; ?>"><p><?php echo $this->_var['cinema']['cinemaName']; ?></p><p><?php echo $this->_var['cinema']['cinemaAddress']; ?></p></li>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 