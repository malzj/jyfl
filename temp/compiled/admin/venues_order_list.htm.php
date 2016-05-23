<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<style>
	.order-msgs{
		border:1px #BBDDE5  solid;
		margin:10px 0;
		padding:10px;
		width:578px;
	}
	.order-msgs a,.order-msgs span{
		height:20px; 
		line-height:20px;
		display:display:inline-block;
	}

	
</style>
<!-- 订单搜索 -->
<div class="form-div">
  <form action="javascript:searchOrder()" name="searchForm">
    <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
    订单号：<input name="order_id" type="text" id="order_id" size="15">
	卡号：<input name="user_name" type="text" id="user_name" size="20">
    <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" />
    
    <a href="venues_order.php?act=list&return=1" style="display:inline-block; height:24px; line-height:24px; background: lightcoral; text-align:center; width:100px;color: #fff;">需退点订单</a>
  </form>
</div>

<!-- 订单列表 -->
<form method="post" action="dongpiao_order.php?act=operate" name="listForm" onsubmit="return check()">
  <div class="list-div" id="listDiv">
<?php endif; ?>

<table cellpadding="3" cellspacing="1">
  <tr>
    <th>
      <input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox" /><a href="javascript:listTable.sort('id', 'DESC'); ">订单号</a><?php echo $this->_var['sort_order_sn']; ?>
    </th>
    <th>接口订单号</th>
    <th><a href="javascript:listTable.sort('add_time', 'DESC'); ">下单时间</a><?php echo $this->_var['sort_order_time']; ?></th>
    <th>订单信息</th>
	<th>数量</th>
	<th>订单类型</th>
	<th>订单金额</th>
    <th><a href="javascript:listTable.sort('state', 'DESC'); ">订单状态</a></th>
    <th>操作</th>
  <tr>
  <?php $_from = $this->_var['order_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('okey', 'order');if (count($_from)):
    foreach ($_from AS $this->_var['okey'] => $this->_var['order']):
?>
  <tr>
    <td valign="top" nowrap="nowrap"><input type="checkbox" name="checkboxes" value="<?php echo $this->_var['order']['order_id']; ?>" /><?php echo $this->_var['order']['order_sn']; ?></td>
    <td valign="top"><?php echo $this->_var['order']['api_order_id']; ?></td>
    <td align="left" valign="top"> <?php echo $this->_var['order']['add_time']; ?></td>
    <td align="left" valign="top" >
    	<?php echo $this->_var['order']['username']; ?><br>
    	场馆：<?php echo $this->_var['order']['venueName']; ?><br>
    	地址：<?php echo $this->_var['order']['place']; ?><br>
    	购买人：<?php echo $this->_var['order']['link_man']; ?> （<?php echo $this->_var['order']['link_phone']; ?>）<br>
    	游玩时间：<?php echo $this->_var['order']['date']; ?><br>
    	时间段：<?php $_from = $this->_var['order']['times_mt']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'time');if (count($_from)):
    foreach ($_from AS $this->_var['time']):
?> <?php echo $this->_var['time']; ?>, <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </td>
	<td align="center" valign="top" nowrap="nowrap"><?php echo $this->_var['order']['total']; ?></td>
	<td align="center" valign="top" nowrap="nowrap"><?php if ($this->_var['order']['source'] == 0): ?> 场地订单 <?php else: ?> 门票订单 <?php endif; ?></td>
	<td align="right" valign="top" nowrap="nowrap"><?php echo $this->_var['order']['money']; ?></td>
	<td align="center" valign="top" nowrap="nowrap"><?php echo $this->_var['order']['order_state_sn']; ?></td>
    <td align="center" valign="top"  nowrap="nowrap">
    	<?php if ($this->_var['order']['return_point'] == 1): ?> 卡点已退 <br><?php endif; ?>
    	<!-- 退票操作 -->
    	<?php if ($this->_var['order']['state'] == 1 || $this->_var['order']['state'] == 3): ?>
    	<a href="venues_order.php?act=returnTicket&id=<?php echo $this->_var['order']['id']; ?>" onclick="if(confirm('退票属于单方面行为，与接口无关，只有与动网客服协商之后，给予退票的情况下，才可操作') == false){return false}"><font color=green title="退票属于单方面行为，与接口无关，只有与动网客服协商之后，给予退票的情况下，才可操作">退票</font></a><br>
    	<?php endif; ?>
    	
    	<!-- 已退票操作 -->
    	<?php if ($this->_var['order']['state'] == 4): ?>
    	<a href="venues_order.php?act=ticketSuccess&id=<?php echo $this->_var['order']['id']; ?>" onclick="if(confirm('只有在动网成功退票后，才可以操作“成功退票”') == false){return false}"><font color=green title="">成功退票</font></a><br>
    	<?php endif; ?>
    	<?php if ($this->_var['order']['state'] == 2 && $this->_var['order']['return_point'] == 0): ?>
    	<a href="venues_order.php?act=returnPoint&id=<?php echo $this->_var['order']['id']; ?>"><font color=red>退卡点</font></a><br>
    	<?php endif; ?>
    	<?php if ($this->_var['order']['state'] == 0 && $this->_var['order']['is_pay'] == 1 && $this->_var['order']['return_point'] == 0): ?>
    	<a href="venues_order.php?act=returnPoint&id=<?php echo $this->_var['order']['id']; ?>"><font color=red>退卡点</font></a><br>
    	<?php endif; ?>
    </td>
  </tr>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>

<!-- 分页 -->
<table id="page-table" cellspacing="0">
  <tr>
    <td align="right" nowrap="true">
    <?php echo $this->fetch('page.htm'); ?>
    </td>
  </tr>
</table>

<?php if ($this->_var['full_page']): ?>
  </div>
  <div>
    <input name="remove" type="hidden" id="btnSubmit3" value="<?php echo $this->_var['lang']['remove']; ?>" class="button" disabled="true" onclick="this.form.target = '_self'" />
    <input name="batch" type="hidden" value="1" />
    <input name="order_id" type="hidden" value="" />
  </div>
</form>
<script language="JavaScript">
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

    /**
     * 搜索订单
     */
    function searchOrder()
    {
        listTable.filter['order_id'] = Utils.trim(document.forms['searchForm'].elements['order_id'].value);
		listTable.filter['user_name'] = document.forms['searchForm'].elements['user_name'].value;
        listTable.filter['page'] = 1;
        listTable.loadList();
    }

    function check()
    {
      var snArray = new Array();
      var eles = document.forms['listForm'].elements;
      for (var i=0; i<eles.length; i++)
      {
        if (eles[i].tagName == 'INPUT' && eles[i].type == 'checkbox' && eles[i].checked && eles[i].value != 'on')
        {
          snArray.push(eles[i].value);
        }
      }
      if (snArray.length == 0)
      {
        return false;
      }
      else
      {
        eles['order_id'].value = snArray.toString();
        return true;
      }
    }
    
    
</script>


<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>