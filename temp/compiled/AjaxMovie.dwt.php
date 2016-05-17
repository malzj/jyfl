<div class="searcher">
    <input id="movieSelectText_1461722661474" type="text" class="input c_a5" placeholder="输入影片名称快速定位"><i class="sub"></i></div>
<ul>
<?php $_from = $this->_var['data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'row');if (count($_from)):
    foreach ($_from AS $this->_var['row']):
?>
    <li><a href="javaScript:;" data-id="<?php echo $this->_var['row']['movieId']; ?>" data-type="movie"><b class="db_point"><?php echo $this->_var['row']['score']; ?></b><span><?php echo $this->_var['row']['movieName']; ?></span></a></li>
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</ul>