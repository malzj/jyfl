<!-- $Id: start.htm 17216 2011-01-19 06:03:12Z liubo $ -->
<?php echo $this->fetch('pageheader.htm'); ?>


<!-- start order statistics -->
<div class="list-div">
<table cellspacing='1' cellpadding='3'>
  <tr>
    <th colspan="4" class="group-title"><?php echo $this->_var['lang']['order_stat']; ?></th>
  </tr>
  <tr>
    <td width="20%"><a href="order.php?act=list&composite_status=<?php echo $this->_var['status']['await_ship']; ?>"><?php echo $this->_var['lang']['await_ship']; ?></a></td>
    <td width="30%"><strong style="color: red"><?php echo $this->_var['order']['await_ship']; ?></strong></td>
    <td width="20%"><a href="order.php?act=list&composite_status=<?php echo $this->_var['status']['unconfirmed']; ?>"><?php echo $this->_var['lang']['unconfirmed']; ?></a></td>
    <td width="30%"><strong><?php echo $this->_var['order']['unconfirmed']; ?></strong></td>
  </tr>
  <tr>
    <td><a href="order.php?act=list&composite_status=<?php echo $this->_var['status']['await_pay']; ?>"><?php echo $this->_var['lang']['await_pay']; ?></a></td>
    <td><strong><?php echo $this->_var['order']['await_pay']; ?></strong></td>
    <td><a href="order.php?act=list&composite_status=<?php echo $this->_var['status']['finished']; ?>"><?php echo $this->_var['lang']['finished']; ?></a></td>
    <td><strong><?php echo $this->_var['order']['finished']; ?></strong></td>
  </tr>
  <tr>
    <td><a href="goods_booking.php?act=list_all"><?php echo $this->_var['lang']['new_booking']; ?></a></td>
    <td><strong><?php echo $this->_var['booking_goods']; ?></strong></td>
    <td><a href="user_account.php?act=list&process_type=1&is_paid=0"><?php echo $this->_var['lang']['new_reimburse']; ?></a></td>
    <td><strong><?php echo $this->_var['new_repay']; ?></strong></td>
  </tr>
  <tr>
    <td><a href="order.php?act=list&composite_status=<?php echo $this->_var['status']['shipped_part']; ?>"><?php echo $this->_var['lang']['shipped_part']; ?></a></td>
    <td><strong><?php echo $this->_var['order']['shipped_part']; ?></strong></td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>
<!-- end order statistics -->
<br />
<!-- start goods statistics -->
<div class="list-div">
<table cellspacing='1' cellpadding='3'>
  <tr>
    <th colspan="4" class="group-title"><?php echo $this->_var['lang']['goods_stat']; ?></th>
  </tr>
  <tr>
    <td width="20%"><?php echo $this->_var['lang']['goods_count']; ?></td>
    <td width="30%"><strong><?php echo $this->_var['goods']['total']; ?></strong></td>
    <td width="20%"><a href="goods.php?act=list&stock_warning=1"><?php echo $this->_var['lang']['warn_goods']; ?></a></td>
    <td width="30%"><strong style="color: red"><?php echo $this->_var['goods']['warn']; ?></strong></td>
  </tr>
  <tr>
    <td><a href="goods.php?act=list&amp;intro_type=is_new"><?php echo $this->_var['lang']['new_goods']; ?></a></td>
    <td><strong><?php echo $this->_var['goods']['new']; ?></strong></td>
    <td><a href="goods.php?act=list&amp;intro_type=is_best"><?php echo $this->_var['lang']['recommed_goods']; ?></a></td>
    <td><strong><?php echo $this->_var['goods']['best']; ?></strong></td>
  </tr>
  <tr>
    <td><a href="goods.php?act=list&amp;intro_type=is_hot"><?php echo $this->_var['lang']['hot_goods']; ?></a></td>
    <td><strong><?php echo $this->_var['goods']['hot']; ?></strong></td>
    <td><a href="goods.php?act=list&amp;intro_type=is_promote"><?php echo $this->_var['lang']['sales_count']; ?></a></td>
    <td><strong><?php echo $this->_var['goods']['promote']; ?></strong></td>
  </tr>
</table>
</div>
<br />
<!-- Virtual Card -->
<div class="list-div">
<table cellspacing='1' cellpadding='3'>
  <tr>
    <th colspan="4" class="group-title"><?php echo $this->_var['lang']['virtual_card_stat']; ?></th>
  </tr>
  <tr>
    <td width="20%"><?php echo $this->_var['lang']['goods_count']; ?></td>
    <td width="30%"><strong><?php echo $this->_var['virtual_card']['total']; ?></strong></td>
    <td width="20%"><a href="goods.php?act=list&amp;stock_warning=1&amp;extension_code=virtual_card"><?php echo $this->_var['lang']['warn_goods']; ?></a></td>
    <td width="30%"><strong style="color: red"><?php echo $this->_var['virtual_card']['warn']; ?></strong></td>
  </tr>
  <tr>
    <td><a href="goods.php?act=list&amp;intro_type=is_new&amp;extension_code=virtual_card"><?php echo $this->_var['lang']['new_goods']; ?></a></td>
    <td><strong><?php echo $this->_var['virtual_card']['new']; ?></strong></td>
    <td><a href="goods.php?act=list&amp;intro_type=is_best&amp;extension_code=virtual_card"><?php echo $this->_var['lang']['recommed_goods']; ?></a></td>
    <td><strong><?php echo $this->_var['virtual_card']['best']; ?></strong></td>
  </tr>
  <tr>
    <td><a href="goods.php?act=list&amp;intro_type=is_hot&amp;extension_code=virtual_card"><?php echo $this->_var['lang']['hot_goods']; ?></a></td>
    <td><strong><?php echo $this->_var['virtual_card']['hot']; ?></strong></td>
    <td><a href="goods.php?act=list&amp;intro_type=is_promote&amp;extension_code=virtual_card"><?php echo $this->_var['lang']['sales_count']; ?></a></td>
    <td><strong><?php echo $this->_var['virtual_card']['promote']; ?></strong></td>
  </tr>
</table>
</div>
<!-- end order statistics -->
<br />
<!-- start access statistics -->
<div class="list-div">
<table cellspacing='1' cellpadding='3'>
  <tr>
    <th colspan="4" class="group-title"><?php echo $this->_var['lang']['acess_stat']; ?></th>
  </tr>
  <tr>
    <td width="20%"><?php echo $this->_var['lang']['acess_today']; ?></td>
    <td width="30%"><strong><?php echo $this->_var['today_visit']; ?></strong></td>
    <td width="20%"><?php echo $this->_var['lang']['online_users']; ?></td>
    <td width="30%"><strong><?php echo $this->_var['online_users']; ?></strong></td>
  </tr>
  <tr>
    <td><a href="user_msg.php?act=list_all"><?php echo $this->_var['lang']['new_feedback']; ?></a></td>
    <td><strong><?php echo $this->_var['feedback_number']; ?></strong></td>
    <td><a href="comment_manage.php?act=list"><?php echo $this->_var['lang']['new_comments']; ?></a></td>
    <td><strong><?php echo $this->_var['comment_number']; ?></strong></td>
  </tr>
</table>
</div>
<!-- end access statistics -->

<?php echo $this->fetch('pagefooter.htm'); ?>
