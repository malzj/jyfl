<!-- $Id -->
<?php echo $this->fetch('pageheader.htm'); ?>
<form name="theForm" method="get" action="order.php" onsubmit="return check()">
<div class="list-div" style="margin-bottom: 5px">
<table width="100%" cellpadding="3" cellspacing="1">
	<tr>
		<th><?php echo $this->_var['lang']['fee_info']; ?></th>
	</tr>
	<tr>
		<td>
			<div align="right">
				<?php echo $this->_var['lang']['label_goods_amount']; ?><strong><?php echo $this->_var['order']['formated_goods_amount']; ?></strong>
				- <?php echo $this->_var['lang']['label_discount']; ?><strong><?php echo $this->_var['order']['formated_discount']; ?></strong>     + <?php echo $this->_var['lang']['label_tax']; ?><strong><?php echo $this->_var['order']['formated_tax']; ?></strong>
				+ <?php echo $this->_var['lang']['label_shipping_fee']; ?><strong><?php echo $this->_var['order']['formated_shipping_fee']; ?></strong>
				+ <?php echo $this->_var['lang']['label_insure_fee']; ?><strong><?php echo $this->_var['order']['formated_insure_fee']; ?></strong>
				+ <?php echo $this->_var['lang']['label_pay_fee']; ?><strong><?php echo $this->_var['order']['formated_pay_fee']; ?></strong>
				+ <?php echo $this->_var['lang']['label_pack_fee']; ?><strong><?php echo $this->_var['order']['formated_pack_fee']; ?></strong>
				+ <?php echo $this->_var['lang']['label_card_fee']; ?><strong><?php echo $this->_var['order']['formated_card_fee']; ?></strong>
			</div>
		</td>
	<tr>
		<td><div align="right"> = <?php echo $this->_var['lang']['label_order_amount']; ?><strong><?php echo $this->_var['order']['formated_total_fee']; ?></strong></div></td>
	</tr>
	<tr>
		<td>
			<div align="right">
				- <?php echo $this->_var['lang']['label_money_paid']; ?><strong><?php echo $this->_var['order']['formated_money_paid']; ?></strong> - <?php echo $this->_var['lang']['label_surplus']; ?> <strong><?php echo $this->_var['order']['formated_surplus']; ?></strong>
				- <?php echo $this->_var['lang']['label_integral']; ?> <strong><?php echo $this->_var['order']['formated_integral_money']; ?></strong>
				- <?php echo $this->_var['lang']['label_bonus']; ?> <strong><?php echo $this->_var['order']['formated_bonus']; ?></strong>
			</div>
		</td>
	<tr>
		<td>
			<div align="right">
				= 
				<?php if ($this->_var['order']['order_amount'] >= 0): ?>
				<?php echo $this->_var['lang']['label_money_dues']; ?><strong><?php echo $this->_var['order']['formated_order_amount']; ?></strong>
				<?php else: ?>
				<?php echo $this->_var['lang']['label_money_refund']; ?><strong><?php echo $this->_var['order']['formated_money_refund']; ?></strong>
				<?php endif; ?>
				<?php if ($this->_var['order']['extension_code'] == "group_buy"): ?><br /><?php echo $this->_var['lang']['notice_gb_order_amount']; ?><?php endif; ?>
			</div>
		</td>
	</tr>
</table>
</div>
<div class="list-div">
<table>
	<tr>
		<th colspan="7">请选择要退货的商品</th>
	</tr>
	<tr>
		<td scope="col"><input type="checkbox" name="checkAll" value="1" onclick="checkGidsAll(this);" />全部退货</td>
		<td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_name_brand']; ?></strong></div></td>
		<td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_sn']; ?></strong></div></td>
		<td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_price']; ?></strong></div></td>
		<td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_number']; ?></strong></div></td>
		<td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['goods_attr']; ?></strong></div></td>
		<td scope="col"><div align="center"><strong><?php echo $this->_var['lang']['subtotal']; ?></strong></div></td>
	</tr>
	<?php $_from = $this->_var['ordergoods']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
	<tr>
		<td><input type="checkbox" name="gids[]" value="<?php echo $this->_var['goods']['goods_id']; ?>" money="<?php echo $this->_var['goods']['subtotal']; ?>" onclick="changeMoney(this);" /></td>
		<td><?php echo $this->_var['goods']['goods_name']; ?></td>
		<td><?php echo $this->_var['goods']['goods_sn']; ?></td>
		<td><div align="right"><?php echo $this->_var['goods']['goods_price']; ?></div></td>
		<td><div align="right"><?php echo $this->_var['goods']['goods_number']; ?></div></td>
		<td><?php echo nl2br($this->_var['goods']['goods_attr']); ?></td>
		<td><div align="right"><?php echo $this->_var['goods']['subtotal']; ?></div></td>
	</tr>
	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
	
</table>
</div>
<div class="list-div" style="margin-bottom: 5px">
<table cellpadding="3" cellspacing="1">
	<tr>
		<th colspan="6"><?php echo $this->_var['lang']['action_info']; ?></th>
	</tr>
	<tr>
		<th><div align="right"><strong>退还金额：</strong></div></th>
		<td>
			<input type="text" name="refund_money" id="refund_money" value="" />
		</td>
	</tr>
	<tr>
		<th><div align="right"><strong><?php echo $this->_var['lang']['label_refund_note']; ?></strong></div></th>
		<td><textarea name="refund_note" cols="60" rows="3" id="refund_note"><?php echo $this->_var['refund_note']; ?></textarea></td>
	</tr>
	
	<tr>
		<th><div align="right"><strong><?php echo $this->_var['lang']['label_action_note']; ?></strong></div></th>
		<td colspan="5"><textarea name="action_note" cols="60" rows="3"></textarea></td>
	</tr>
	<tr>
		<td colspan="2">
			<div align="center">
				<input type="hidden" name="refund" value="1" />
				<input type="submit" name="submit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button" onclick="return confirm('确定退货吗？');" />
				<input type="button" name="back" value="<?php echo $this->_var['lang']['back']; ?>" class="button" onclick="history.back()" />
				<input type="hidden" name="order_id" value="<?php echo $this->_var['order']['order_id']; ?>" />
				<input type="hidden" name="operation" value="<?php echo $this->_var['operation']; ?>" />
				<input type="hidden" name="act" value="operate_post" />
			</div>
		</td>
	</tr>
</table>
</div>
</form>

<script language="JavaScript">
var money = 0;
function checkGidsAll(obj,sn){
	var obj_c = document.getElementsByName('gids[]')
	for(var i=0; i<obj_c.length; i++){
		obj_c[i].checked = obj.checked;
		if (obj_c[i].checked == true){
			money += Number(obj_c[i].getAttribute("money"));
		}else{
			money -= Number(obj_c[i].getAttribute("money"));
		}
	}
	money = money < 0 ? 0 : money;
	//document.getElementById('refund_money').value = money;
}

function changeMoney(obj){
	/*var obj_c = document.getElementsByName('gids[]')
	money = obj.checked == true ? money + Number(obj.getAttribute("money")) : money - Number(obj.getAttribute("money"));
	document.getElementById('refund_money').value = money;*/
}
function check(){
	var checked = false;
	var money = document.getElementById('refund_money').value;
	var note  = document.getElementById('refund_note').value;
	
	var obj_c = document.getElementsByName('gids[]')
	for(var i=0; i<obj_c.length; i++){
		if (obj_c[i].checked){
			checked = true;
			break;
		}
	}
	if (!checked){
		alert('请选择要退货的商品');
		return false;
	}
	if (money == ''){
		alert('退款金额不能为空');
		return false;
	}
	if (note == ''){
		alert('退款说明不能为空');
		return false;
	}

	
	return true;
}
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>