<!-- $Id: start.htm 17216 2011-01-19 06:03:12Z liubo $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<!-- directory install start -->
<ul id="cloud_list" style="padding:0; margin: 0; list-style-type:none; color: #CC0000;">
 
</ul>


<!-- start personal message -->
<?php if ($this->_var['admin_msg']): ?>
<div class="list-div" style="border: 1px solid #CC0000">
  <table cellspacing='1' cellpadding='3'>
    <tr>
      <th><?php echo $this->_var['lang']['pm_title']; ?></th>
      <th><?php echo $this->_var['lang']['pm_username']; ?></th>
      <th><?php echo $this->_var['lang']['pm_time']; ?></th>
    </tr>
    <?php $_from = $this->_var['admin_msg']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'msg');if (count($_from)):
    foreach ($_from AS $this->_var['msg']):
?>
      <tr align="center">
        <td align="left"><a href="message.php?act=view&id=<?php echo $this->_var['msg']['message_id']; ?>"><?php echo sub_str($this->_var['msg']['title'],60); ?></a></td>
        <td><?php echo $this->_var['msg']['user_name']; ?></td>
        <td><?php echo $this->_var['msg']['send_date']; ?></td>
      </tr>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>
  </div>
<br />
<?php endif; ?>
<!-- end personal message -->
<!-- start order statistics -->
<div class="list-div">
<table cellspacing='1' cellpadding='3'>
  <tr>
    <th class="group-title">供应商公告：</th>
  </tr>
  <tr>
    <td width="100%" style="padding:20px 20px 30px 20px;"><?php echo $this->_var['supplier_notice']; ?></td>    
  </tr>

  <tr>
    <th class="group-title">通知文章：</th>
  </tr>
  <tr>
    <td width="100%" >
		<table cellpadding=1 cellspacing=1 width="100%" bgcolor="#f9fdff">
		<tr><th style="background:#f4f9fc;font-weight:bold;">文章标题</th><th style="background:#f4f9fc;font-weight:bold;">发布时间</th></tr>
		<?php $_from = $this->_var['supplier_article']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'sarticle');if (count($_from)):
    foreach ($_from AS $this->_var['sarticle']):
?>
		<tr><td><a href="<?php echo $this->_var['sarticle']['url']; ?>" ><?php echo $this->_var['sarticle']['title']; ?></a></td><td align=center> <?php echo $this->_var['sarticle']['formated_addtime']; ?></td></tr>
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</table>
	</td>    
  </tr>

</table>
</div>
<?php if ($this->_var['inventory']): ?>
  <div> <a href="/inventory.php" target="_blank"><span style="color:#444;margin-top:5px;background-color:#BBDDE5;line-height:2em;font-size:12px;">库存管理</span></a></div>
<?php endif; ?>  
<!-- end order statistics -->
<br />


<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js')); ?>


<?php echo $this->fetch('pagefooter.htm'); ?>
