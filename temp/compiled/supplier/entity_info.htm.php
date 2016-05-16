<!-- $Id: order_info.htm 17060 2010-03-25 03:44:42Z liuhui $ -->

<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'topbar.js,../js/utils.js,listtable.js,selectzone.js,../js/common.js')); ?>
<style>
	.list-div td {
		text-align:center;
	}
	.confirm-total{
		margin:10px 0;
		line-height:50px;
		height:50px;
		padding:0 10px;
		background:antiquewhite;
		text-align: right;
	}
	.confirm-total .f16{
		font-size:16px;
		color:red;
	}
</style>
<form action="order.php?act=operate" method="post" name="theForm">
<div class="list-div" style="margin-bottom: 5px">

	<table  class="confirm-table">
    	
    	<tr>
        	<td width="100">卡支付</td>
            <td>
            	<table class="confirm-table-row">
                	<tr><th width="60%">产品名称</th><th width="15%">单价</th> <th width="10%">数量</th> <th width="15$">总价</th></tr>
                    <?php $_from = $this->_var['row']['card_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
                    <tr><td><?php echo $this->_var['goods']['name']; ?></td><td><?php echo $this->_var['goods']['show_price']; ?></td><td><?php echo $this->_var['goods']['num']; ?></td><td><?php echo $this->_var['goods']['total_price']; ?></td></tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan='4'><center>没有数据</center></td></tr>
                    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </table>
            </td>
        </tr>
       
       <!-- <tr>
        	<td colspan="2" class="confirm-right">卡支付共：125014 点&nbsp;&nbsp;&nbsp;&nbsp;</td>
        </tr>-->
        <tr>
        	<td width="100">现金支付</td>
            <td>
            	<table class="confirm-table-row">
                	<tr><th width="60%">产品名称</th><th width="15%">单价</th> <th width="10%">数量</th> <th width="15$">总价</th></tr>
                    <?php $_from = $this->_var['row']['money_goods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
                    <tr><td><?php echo $this->_var['goods']['name']; ?></td><td><?php echo $this->_var['goods']['show_price']; ?></td><td><?php echo $this->_var['goods']['num']; ?></td><td><?php echo $this->_var['goods']['total_price']; ?></td></tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan='4'><center>没有数据</center></td></tr>
                    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </table>
            </td>
        </tr>
        <!-- <tr>
        	<td colspan="2" class="confirm-right">现金支付共：125014 元&nbsp;&nbsp;&nbsp;&nbsp;</td>
        </tr>-->
    </table>
    <div class="confirm-total">
    	客户类型：<font class="f16" color=red><?php echo $this->_var['row']['utype']; ?></font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    	卡点共支付：<font class="f16" color=red><?php echo $this->_var['row']['card_total']; ?></font> 点&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
       	 现金共支付：<font class="f16" color=red><?php echo $this->_var['row']['money_total']; ?></font> 元
    </div>
</div>

</form>

<script language="JavaScript">

  var oldAgencyId = <?php echo empty($this->_var['order']['agency_id']) ? '0' : $this->_var['order']['agency_id']; ?>;

  onload = function()
  {
    // 开始检查订单
    startCheckOrder();
  }

  /**
   * 把订单指派给某办事处
   * @param int agencyId
   */
  function assignTo(agencyId)
  {
    if (agencyId == 0)
    {
      alert(pls_select_agency);
      return false;
    }
    if (oldAgencyId != 0 && agencyId == oldAgencyId)
    {
      alert(pls_select_other_agency);
      return false;
    }
    return true;
  }
</script>


<?php echo $this->fetch('pagefooter.htm'); ?>