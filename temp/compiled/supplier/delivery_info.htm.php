<!-- $Id: order_info.htm 15544 2009-01-09 01:54:28Z zblikai $ -->

<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'topbar.js,../js/utils.js,listtable.js,selectzone.js,../js/common.js')); ?>

<div class="list-div" style="margin-bottom: 5px">
<table width="100%" cellpadding="3" cellspacing="1">
<form action="order.php" method="post" name="theForm">
  <tr>
    <th colspan="4"><?php echo $this->_var['lang']['base_info']; ?></th>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['delivery_sn_number']; ?></strong></div></td>
    <td><?php echo $this->_var['delivery_order']['delivery_sn']; ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_shipping_time']; ?></strong></div></td>
    <td><?php echo $this->_var['delivery_order']['formated_update_time']; ?></td>
  </tr>
  <tr>
    <td width="18%"><div align="right"><strong><?php echo $this->_var['lang']['label_order_sn']; ?></strong></div></td>
   <td width="34%"><?php echo $this->_var['delivery_order']['order_sn']; ?><?php if ($this->_var['delivery_order']['extension_code'] == "group_buy"): ?><a href="group_buy.php?act=edit&id=<?php echo $this->_var['delivery_order']['extension_id']; ?>"><?php echo $this->_var['lang']['group_buy']; ?></a><?php elseif ($this->_var['delivery_order']['extension_code'] == "exchange_goods"): ?><a href="exchange_goods.php?act=edit&id=<?php echo $this->_var['delivery_order']['extension_id']; ?>"><?php echo $this->_var['lang']['exchange_goods']; ?></a><?php endif; ?>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_order_time']; ?></strong></div></td>
    <td><?php echo $this->_var['delivery_order']['formated_add_time']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_user_name']; ?></strong></div></td>
    <td><?php echo empty($this->_var['delivery_order']['user_name']) ? $this->_var['lang']['anonymous'] : $this->_var['delivery_order']['user_name']; ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_how_oos']; ?></strong></div></td>
    <td><?php echo $this->_var['delivery_order']['how_oos']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_shipping']; ?></strong></div></td>
    <td><?php if ($this->_var['exist_real_goods']): ?><?php if ($this->_var['delivery_order']['shipping_id'] > 0): ?><?php echo $this->_var['delivery_order']['shipping_name']; ?><?php else: ?><?php echo $this->_var['lang']['require_field']; ?><?php endif; ?> <?php if ($this->_var['delivery_order']['insure_fee'] > 0): ?>（<?php echo $this->_var['lang']['label_insure_fee']; ?><?php echo $this->_var['delivery_order']['formated_insure_fee']; ?>）<?php endif; ?><?php endif; ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_shipping_fee']; ?></strong></div></td>
    <td><?php echo $this->_var['delivery_order']['shipping_fee']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_insure_yn']; ?></strong></div></td>
    <td><?php if ($this->_var['insure_yn']): ?><?php echo $this->_var['lang']['yes']; ?><?php else: ?><?php echo $this->_var['lang']['no']; ?><?php endif; ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_insure_fee']; ?></strong></div></td>
    <td><?php echo empty($this->_var['delivery_order']['insure_fee']) ? '0.00' : $this->_var['delivery_order']['insure_fee']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_invoice_no']; ?></strong></div></td>
    <td colspan="3"><?php if ($this->_var['delivery_order']['status'] != 1): ?><input name="invoice_no" type="text" value="<?php echo $this->_var['delivery_order']['invoice_no']; ?>" <?php if ($this->_var['delivery_order']['status'] == 0): ?> readonly="readonly" <?php endif; ?>><?php else: ?><?php echo $this->_var['delivery_order']['invoice_no']; ?><?php endif; ?></td>
  </tr>
  
  <tr>
    <th colspan="4"><?php echo $this->_var['lang']['consignee_info']; ?></th>
    </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_consignee']; ?></strong></div></td>
    <td><?php echo htmlspecialchars($this->_var['delivery_order']['consignee']); ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_email']; ?></strong></div></td>
    <td><?php echo $this->_var['delivery_order']['email']; ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_address']; ?></strong></div></td>
    <td>[<?php echo $this->_var['delivery_order']['region']; ?>] <?php echo htmlspecialchars($this->_var['delivery_order']['address']); ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_zipcode']; ?></strong></div></td>
    <td><?php echo htmlspecialchars($this->_var['delivery_order']['zipcode']); ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_tel']; ?></strong></div></td>
    <td><?php echo $this->_var['delivery_order']['tel']; ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_mobile']; ?></strong></div></td>
    <td><?php echo htmlspecialchars($this->_var['delivery_order']['mobile']); ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_sign_building']; ?></strong></div></td>
    <td><?php echo htmlspecialchars($this->_var['delivery_order']['sign_building']); ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_best_time']; ?></strong></div></td>
    <td><?php echo htmlspecialchars($this->_var['delivery_order']['best_time']); ?></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_postscript']; ?></strong></div></td>
    <td colspan="3"><?php echo $this->_var['delivery_order']['postscript']; ?></td>
  </tr>
</table>
</div>

<div class="list-div" style="margin-bottom: 5px">
<table width="100%" cellpadding="3" cellspacing="1">
  <tr>
    <th colspan="7" scope="col"><?php echo $this->_var['lang']['goods_info']; ?></th>
    </tr>
  <tr>
    <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_name_brand']; ?></strong></div></td>
    <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_sn']; ?></strong></div></td>
    <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['product_sn']; ?></strong></div></td>
    <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_attr']; ?></strong></div></td>
    <td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['label_send_number']; ?></strong></div></td>
  </tr>
  <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
  <tr>
    <td>
    <a href="../goods.php?id=<?php echo $this->_var['goods']['goods_id']; ?>" target="_blank"><?php echo $this->_var['goods']['goods_name']; ?> <?php if ($this->_var['goods']['brand_name']): ?>[ <?php echo $this->_var['goods']['brand_name']; ?> ]<?php endif; ?>
    </td>
    <td><div align="left"><?php echo $this->_var['goods']['goods_sn']; ?></div></td>
    <td><div align="left"><?php echo $this->_var['goods']['product_sn']; ?></div></td>
    <td><div align="left"><?php echo nl2br($this->_var['goods']['goods_attr']); ?></div></td>
    <td><div align="left"><?php echo $this->_var['goods']['send_number']; ?></div></td>
  </tr>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>
</div>

<div class="list-div" style="margin-bottom: 5px">
<table cellpadding="3" cellspacing="1">
  <tr>
    <th colspan="6"><?php echo $this->_var['lang']['op_ship']; ?><?php echo $this->_var['lang']['action_info']; ?></th>
  </tr>

  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['action_user']; ?></strong></div></td>
    <td><?php echo $this->_var['delivery_order']['action_user']; ?></td>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_agency']; ?></strong></div></td>
    <td colspan="5"><?php echo $this->_var['delivery_order']['agency_name']; ?></td>
  </tr>
  <?php if ($this->_var['delivery_order']['status'] != 1): ?>
  <!-- <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_action_note']; ?></strong></div></td>
  <td colspan="5"><textarea name="action_note" cols="80" rows="3"></textarea></td>
  </tr>
  <tr>
    <td><div align="right"><strong><?php echo $this->_var['lang']['label_operable_act']; ?></strong></div></td>
    <td colspan="6" align="left"><?php if ($this->_var['delivery_order']['status'] == 2): ?><input name="delivery_confirmed" type="submit" value="<?php echo $this->_var['lang']['op_ship']; ?>" class="button"/>&nbsp;&nbsp;<?php else: ?><input name="unship" type="submit" value="<?php echo $this->_var['lang']['op_cancel_ship']; ?>" class="button" /><?php endif; ?>
        <input name="order_id" type="hidden" value="<?php echo $this->_var['delivery_order']['order_id']; ?>">
        <input name="delivery_id" type="hidden" value="<?php echo $this->_var['delivery_order']['delivery_id']; ?>">
        <input name="act" type="hidden" value="<?php echo $this->_var['action_act']; ?>">
    </td>
  </tr> -->
  <?php endif; ?>

  <tr>
    <th><?php echo $this->_var['lang']['action_user']; ?></th>
    <th><?php echo $this->_var['lang']['action_time']; ?></th>
    <th><?php echo $this->_var['lang']['order_status']; ?></th>
    <th><?php echo $this->_var['lang']['pay_status']; ?></th>
    <th><?php echo $this->_var['lang']['shipping_status']; ?></th>
    <th><?php echo $this->_var['lang']['action_note']; ?></th>
  </tr>
  <?php $_from = $this->_var['action_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'action');if (count($_from)):
    foreach ($_from AS $this->_var['action']):
?>
  <tr>
    <td><div align="center"><?php echo $this->_var['action']['action_user']; ?></div></td>
    <td><div align="center"><?php echo $this->_var['action']['action_time']; ?></div></td>
    <td><div align="center"><?php echo $this->_var['action']['order_status']; ?></div></td>
    <td><div align="center"><?php echo $this->_var['action']['pay_status']; ?></div></td>
    <td><div align="center"><?php echo $this->_var['action']['shipping_status']; ?></div></td>
    <td><?php echo nl2br($this->_var['action']['action_note']); ?></td>
  </tr>
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </form>
</table>
</div>

<script language="JavaScript">

  var oldAgencyId = <?php echo empty($this->_var['delivery_order']['agency_id']) ? '0' : $this->_var['delivery_order']['agency_id']; ?>;

  onload = function()
  {
    // 开始检查订单
    startCheckOrder();
  }

</script>


<?php echo $this->fetch('pagefooter.htm'); ?>