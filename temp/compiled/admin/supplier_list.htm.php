<!-- $Id: agency_list.htm 14216 2008-03-10 02:27:21Z testyang $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>

<form method="post" action="" name="listForm" onsubmit="return confirm(batch_drop_confirm);">
<div class="list-div" id="listDiv">
<?php endif; ?>

  <table cellpadding="3" cellspacing="1">
    <tr>
	  <th><?php echo $this->_var['lang']['supplier_username']; ?></th>
      <th><?php echo $this->_var['lang']['supplier_name']; ?></th>
      <th><?php echo $this->_var['lang']['supplier_rank']; ?></th>
      <th><?php echo $this->_var['lang']['supplier_tel']; ?></th>
	  <th><?php echo $this->_var['lang']['system_fee']; ?></th>
	  <th><?php echo $this->_var['lang']['supplier_bond']; ?></th>
	  <th><?php echo $this->_var['lang']['supplier_rebate']; ?></th>
	  <th><?php echo $this->_var['lang']['supplier_remark']; ?></th>
	  <th>状态</th>
      <th><?php echo $this->_var['lang']['handler']; ?></th>
    </tr>
    <?php $_from = $this->_var['supplier_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'supplier');if (count($_from)):
    foreach ($_from AS $this->_var['supplier']):
?>
    <tr>
	  <td ><?php echo $this->_var['supplier']['user_name']; ?> </td>
      <td class="first-cell" style="padding-left:10px;" ><?php echo $this->_var['supplier']['supplier_name']; ?></td>
      <td ><?php echo $this->_var['supplier']['rank_name']; ?> </td>
      <td><?php echo $this->_var['supplier']['tel']; ?></td>
	  <td align="center"><?php echo $this->_var['supplier']['system_fee']; ?></td>
	  <td align="center"><?php echo $this->_var['supplier']['supplier_bond']; ?></td>
	  <td align="center"><?php echo $this->_var['supplier']['supplier_rebate']; ?></td>
	  <td align="center"><?php echo $this->_var['supplier']['supplier_remark']; ?></td>
	  <td align="center"><?php echo $this->_var['supplier']['status_name']; ?></td>
      <td align="center">
        <a href="supplier.php?act=edit&id=<?php echo $this->_var['supplier']['supplier_id']; ?>" title="<?php echo $this->_var['lang']['view']; ?>"><?php echo $this->_var['lang']['view']; ?></a><?php if ($this->_var['supplier']['status'] > 0): ?>&nbsp;&nbsp;<a href="../supplier.php?suppId=<?php echo $this->_var['supplier']['supplier_id']; ?>" target="_blank">查看店铺</a><?php endif; ?>&nbsp;&nbsp;<a href="supplier.php?act=remove&id=<?php echo $this->_var['supplier']['supplier_id']; ?>">删除</a></td>
    </tr>
    <?php endforeach; else: ?>
    <tr><td class="no-records" colspan="9"><?php echo $this->_var['lang']['no_records']; ?></td></tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>
<table id="page-table" cellspacing="0">
  <tr>
    <td>&nbsp;</td>
    <td align="right" nowrap="true">
    <?php echo $this->fetch('page.htm'); ?>
    </td>
  </tr>
</table>

<?php if ($this->_var['full_page']): ?>
</div>
</form>

<script type="text/javascript" language="javascript">
  <!--
  listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
  listTable.pageCount = <?php echo $this->_var['page_count']; ?>;

  <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
  listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

  
  onload = function()
  {
      // 开始检查订单
      startCheckOrder();
  }
  
  //-->
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>