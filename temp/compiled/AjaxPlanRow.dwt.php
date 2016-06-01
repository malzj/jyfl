
    <ul>
    <?php $_from = $this->_var['moviePlan']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'plan');if (count($_from)):
    foreach ($_from AS $this->_var['plan']):
?>
    <?php if ($this->_var['plan']['is_cut'] == 1): ?>
     <li> <a href="javascript:;" target="_blank"><span class="time"><?php echo $this->_var['plan']['time']; ?></span> <span class="ting"><?php echo $this->_var['plan']['screenType']; ?>/<?php echo $this->_var['plan']['hallName']; ?>/<?php echo $this->_var['plan']['language']; ?></span> <span class="mprice"><?php echo $this->_var['plan']['price']; ?></span><span class="tbtn" style="background:#f4f4f4; color:#8E8E8E;">已过场</span></a></li>
    <?php else: ?>
     <li> <a href="movie.php?step=cinemaSeats&hallno=<?php echo $this->_var['plan']['hallNo']; ?>&planid=<?php echo $this->_var['plan']['planId']; ?>&movieid=<?php echo $this->_var['plan']['movieId']; ?>&cinemaid=<?php echo $this->_var['plan']['cinemaId']; ?>" target="_blank"><span class="time"><?php echo $this->_var['plan']['time']; ?></span> <span class="ting"><?php echo $this->_var['plan']['screenType']; ?>/<?php echo $this->_var['plan']['hallName']; ?>/<?php echo $this->_var['plan']['language']; ?></span> <span class="mprice"><?php echo $this->_var['plan']['price']; ?></span><span class="tbtn">选座购票</span></a>									</li>
    <?php endif; ?>
       
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
    </ul>

