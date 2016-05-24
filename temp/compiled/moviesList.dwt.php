<?php $_from = $this->_var['movies']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'mo');if (count($_from)):
    foreach ($_from AS $this->_var['mo']):
?>
<li style="float: left;" data-id="<?php echo $this->_var['mo']['movieId']; ?>" data-name="<?php echo $this->_var['mo']['movieName']; ?>" data-thumb="<?php echo $this->_var['mo']['thumb']; ?>" data-type="<?php echo $this->_var['mo']['movieType']; ?>" data-director="<?php echo $this->_var['mo']['director']; ?>" data-actor="<?php echo $this->_var['mo']['actor']; ?>" data-intro="<?php echo $this->_var['mo']['intro']; ?>" data-length="<?php echo $this->_var['mo']['movieLength']; ?>" data-time="<?php echo $this->_var['mo']['publishTime']; ?>">
    <div class="pic">
        <a href="javascript:goMovie(<?php echo $this->_var['mo']['movieId']; ?>);" target="_blank"><img src="<?php echo $this->_var['mo']['thumb']; ?>" width="140" height="196"></a>
    </div>
    <div class="title juqingA">
    	<a href="javascript:goMovie(<?php echo $this->_var['mo']['movieId']; ?>);" target="_blank"><span><?php echo $this->_var['mo']['movieName']; ?></span>  </a>  	
    	<a href="javascript:goMovie(<?php echo $this->_var['mo']['movieId']; ?>);" target="_blank"><div class="buy zhuti_a_hover">选座购票</div></a>
    </div>
</li>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
