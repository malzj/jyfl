<!-- $Id: brand_list.htm 15898 2009-05-04 07:25:41Z liuhui $ -->

<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>


<form method="post" action="" name="listForm">
<!-- start brand list -->
<div class="list-div" id="listDiv">


  <table cellpadding="3" cellspacing="1">
    <tr>
      <th>订单号</th>
      <th>供应商</th>
      <th>卡点共支付</th>
      <th>现金共支付</th>
      <th>下单时间</th>
      <th>状态</th>
      <th>操作</th>
    </tr>
    <?php $_from = $this->_var['order_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'order');if (count($_from)):
    foreach ($_from AS $this->_var['order']):
?>
    <tr>
      <td class="first-cell"><?php echo $this->_var['order']['order_sn']; ?></td>
      <td><?php echo $this->_var['order']['supplier_name']; ?></td>
      <td align="left"><?php echo $this->_var['order']['card_total']; ?></td>
      <td align="right"><?php echo $this->_var['order']['money_total']; ?></td>
      <td align="center"><?php echo $this->_var['order']['add_time']; ?></td>
      <td align="center"><?php echo $this->_var['order']['status_name']; ?></td>
      <td align="center">
        <a href="entity.php?act=show&id=<?php echo $this->_var['order']['id']; ?>">查看详细</a>
      </td>
    </tr>
    <?php endforeach; else: ?>
    <tr><td class="no-records" colspan="10"><?php echo $this->_var['lang']['no_records']; ?></td></tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <tr>
      <td align="right" nowrap="true" colspan="7">
      <?php echo $this->fetch('page.htm'); ?>
      </td>
    </tr>
  </table>


<!-- end brand list -->
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
