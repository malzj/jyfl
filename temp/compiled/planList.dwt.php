<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php $_from = $this->_var['moviePlan']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'plan');if (count($_from)):
    foreach ($_from AS $this->_var['plan']):
?>
<li>
    <span class="span_1"><?php echo $this->_var['plan']['time']; ?></span>
    <span class="span_1"><?php echo $this->_var['plan']['screenType']; ?>/<?php echo $this->_var['plan']['language']; ?></span>
    <span class="span_1"><?php echo $this->_var['plan']['hallName']; ?></span>
    <span class="span_1"><span class="color_ff781e"><?php echo $this->_var['plan']['price']; ?></span>点</span>
    <span class="buy span_1">
    <?php if ($this->_var['plan']['is_cut'] == 1): ?>
    	<span style="background:#CCC; cursor: default;">&nbsp;&nbsp;已过场&nbsp;&nbsp;</span>
    <?php else: ?>
    	<span <?php if ($this->_var['plan']['is_cut'] == 0): ?> onclick="location.href='movie.php?step=cinemaSeats&hallno=<?php echo $this->_var['plan']['hallNo']; ?>&planid=<?php echo $this->_var['plan']['planId']; ?>&movieid=<?php echo $this->_var['plan']['movieId']; ?>&cinemaid=<?php echo $this->_var['plan']['cinemaId']; ?>'"<?php endif; ?> class="zhuti_a_hover">选座购票</span>
    <?php endif; ?>
    </span>
</li>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
