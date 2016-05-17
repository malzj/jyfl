<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js,../js/transport.js')); ?>

<div class="form-div">
  <form action="javascript:search_card()" name="searchForm">
    <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
    <!-- 卡号：<input type="text" name="card" size="15" /> -->
    描述：<input type="text" name="title" size="15" />
    <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" />
  </form>
</div>

<form method="post" action="" name="listForm">
<!-- start brand list -->
<div class="list-div" id="listDiv">
<?php endif; ?>

  <table cellpadding="3" cellspacing="1">
    <tr>
      <th width="100px">描述</th>
      <th width="50%">卡规则</th>
      <th align="center">单价（元）</th>
	  <th align="center">是否开启home页</th>
	  <th align="center">折扣调整</th>
	  <th align="center">商品调整</th>
      <th width="220px">操作</th>
    </tr>
    <?php $_from = $this->_var['cardlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'card');if (count($_from)):
    foreach ($_from AS $this->_var['card']):
?>
    <tr>
      <td class="first-cell"><?php echo htmlspecialchars($this->_var['card']['title']); ?></td>
	  <td align="center">
		<?php if ($this->_var['card']['navinfo']): ?>
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="10%"><b>导航</b></td>
		</tr><tr><td>
		<?php $_from = $this->_var['card']['navinfo']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'nav');if (count($_from)):
    foreach ($_from AS $this->_var['nav']):
?>
		
			
				<?php echo $this->_var['nav']['navName']; ?>&nbsp;&nbsp;&nbsp;
			
			
		
		<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</td>
		</tr>
		</table>
		<?php endif; ?>
		<td align="center"><?php if ($this->_var['card']['price']): ?><?php echo $this->_var['card']['price']; ?><?php else: ?>0<?php endif; ?></td>
		<td align="center">
			<img src="images/<?php if ($this->_var['card']['home_desc']): ?>yes<?php else: ?>no<?php endif; ?>.gif" />
		</td>
	  </td>
	  <td align="center"><img src="images/<?php if ($this->_var['card']['zhekou']): ?>yes<?php else: ?>no<?php endif; ?>.gif" /></td>
	  <td align="center"><img src="images/<?php if ($this->_var['card']['shop']): ?>yes<?php else: ?>no<?php endif; ?>.gif" /></td>
      <td align="center">
        <a href="card_rule.php?act=rule_out&id=<?php echo $this->_var['card']['id']; ?>" title="规格排除设置">规格排除设置</a>‖
        <a href="card_rule.php?act=rule_ratio&id=<?php echo $this->_var['card']['id']; ?>" title="商品折扣列表">商品折扣列表</a>‖
        <a href="card_rule.php?act=edit&id=<?php echo $this->_var['card']['id']; ?>" title="<?php echo $this->_var['lang']['edit']; ?>"><?php echo $this->_var['lang']['edit']; ?></a>‖
		<a href="card_rule.php?act=del&id=<?php echo $this->_var['card']['id']; ?>" title="删除" onclick="return confirm('确定删除该号段规则？')">删除</a>
      </td>
    </tr>
    <?php endforeach; else: ?>
    <tr><td class="no-records" colspan="7"><?php echo $this->_var['lang']['no_records']; ?></td></tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <tr>
      <td align="right" nowrap="true" colspan="7">
      <?php echo $this->fetch('page.htm'); ?>
      </td>
    </tr>
  </table>

<?php if ($this->_var['full_page']): ?>
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


onload = function(){
	// 开始检查订单
	startCheckOrder();
}

function search_card(){

	// listTable.filter['card'] = Utils.trim(document.forms['searchForm'].elements['card'].value);
	listTable.filter['title'] = document.forms['searchForm'].elements['title'].value;
	listTable.filter['page'] = 1;
	listTable.loadList();
}
//-->
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>