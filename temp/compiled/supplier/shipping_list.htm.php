<!-- $Id: shipping_list.htm 17043 2010-02-26 10:40:02Z sxc_shop $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<!-- start payment list -->
<div class="list-div" id="listDiv">
<table cellspacing='1' cellpadding='3'>
  <tr>
    <th><?php echo $this->_var['lang']['shipping_name']; ?></th>
    <th><?php echo $this->_var['lang']['shipping_desc']; ?></th>
    <th nowrap="true"><?php echo $this->_var['lang']['insure']; ?></th>
    <th nowrap="true"><?php echo $this->_var['lang']['support_cod']; ?></th>
    <th nowrap="true"><?php echo $this->_var['lang']['shipping_version']; ?></th>
    <th><?php echo $this->_var['lang']['shipping_author']; ?></th>
    <th><?php echo $this->_var['lang']['sort_order']; ?></th>
    <th><?php echo $this->_var['lang']['handler']; ?></th>
  </tr>
  <?php $_from = $this->_var['modules']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'module');if (count($_from)):
    foreach ($_from AS $this->_var['module']):
?>
  <tr>
    <td class="first-cell" nowrap="true">
      <?php echo $this->_var['module']['name']; ?>
    </td>
    <td>
      <?php echo $this->_var['module']['desc']; ?>
    </td>
    <td align="right">
      <?php echo $this->_var['module']['insure_fee']; ?>
    </td>
    <td align='center'><?php if ($this->_var['module']['cod'] == 1): ?><?php echo $this->_var['lang']['yes']; ?><?php else: ?><?php echo $this->_var['lang']['no']; ?><?php endif; ?></td>
    <td nowrap="true"><?php echo $this->_var['module']['version']; ?></td>
    <td nowrap="true"><a href="<?php echo $this->_var['module']['website']; ?>" target="_blank"><?php echo $this->_var['module']['author']; ?></a></td>
    <td align="right" valign="top"> <?php if ($this->_var['module']['install'] == 1): ?> <span onclick="listTable.edit(this, 'edit_order', '<?php echo $this->_var['module']['code']; ?>'); return false;"><?php echo $this->_var['module']['shipping_order']; ?></span> <?php else: ?> &nbsp; <?php endif; ?> </td>
    <td align="center" nowrap="true">
      <a href="shipping_area.php?act=list&shipping=<?php echo $this->_var['module']['id']; ?>">查看<?php echo $this->_var['lang']['shipping_area']; ?></a>
    </td>
  </tr>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>
</div>
<!-- end payment list -->
<script type="Text/Javascript" language="JavaScript">
<!--


onload = function()
{
    // 开始检查订单
    startCheckOrder();
}

//-->
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>