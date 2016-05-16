<!-- $Id: order_operate_info.htm 14216 2008-03-10 02:27:21Z testyang $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<ul style="padding:0; margin: 0; list-style-type:none; color: #CC0000;">
  <li style="border: 1px solid #CC0000; background: #FFFFCC; padding: 10px; margin-bottom: 5px;" ><?php echo $this->_var['order_info']; ?></li>
</ul>
<!-- 订单列表 -->
<form method="post" action="order.php?act=operate" name="listForm" onsubmit="return check()">
  <div class="list-div" id="listDiv">

<table cellpadding="3" cellspacing="1">
  <tr>
    <th><?php echo $this->_var['lang']['order_sn']; ?></th>
    <th><?php echo $this->_var['lang']['all_status']; ?></th>
    <th><?php echo $this->_var['lang']['op_you_can']; ?></th>
    <th><?php echo $this->_var['lang']['handler']; ?></th>
  <tr>
  <?php $_from = $this->_var['order_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'order');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['order']):
?>
  <tr>
    <td valign="top" nowrap="nowrap"><input type="checkbox" name="checkboxes" value="<?php echo $this->_var['order']['order_sn']; ?>" /><?php echo $this->_var['order']['order_sn']; ?></td>
    <td align="center" valign="top" nowrap="nowrap"><?php echo $this->_var['lang']['os'][$this->_var['order']['order_status']]; ?>,<?php echo $this->_var['lang']['ps'][$this->_var['order']['pay_status']]; ?>,<?php echo $this->_var['lang']['ss'][$this->_var['order']['shipping_status']]; ?></td>
    <td align="center" valign="top" nowrap="nowrap"><?php echo $this->_var['order']['operable']; ?></td>
    <td align="center" valign="top"  nowrap="nowrap">
     <a href="order.php?act=info&order_id=<?php echo $this->_var['order']['order_id']; ?>"><?php echo $this->_var['lang']['detail']; ?></a>
    </td>
  </tr>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>
</div>
</form>
<script language="JavaScript">


    onload = function()
    {
        // 开始检查订单
        startCheckOrder();
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
