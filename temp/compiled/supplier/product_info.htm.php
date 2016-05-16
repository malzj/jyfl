<!-- $Id: goods_list.htm 16529 2009-08-12 05:38:57Z wangleisvn $ -->

<?php if ($this->_var['full_page']): ?>

<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'validator.js')); ?>
<!-- 添加货品 -->
<div class="list-div" style="margin-bottom: 5px; margin-top: 10px;" id="listDiv">

<?php endif; ?>

<form method="post" action="goods.php" name="addForm" id="addForm" >
<input type="hidden" name="goods_id" value="<?php echo $this->_var['goods_id']; ?>" />
<input type="hidden" name="act" value="product_add_execute" />
  <table width="100%" cellpadding="3" cellspacing="1" id="table_list">
    <tr>
      <th colspan="20" scope="col"><?php echo $this->_var['goods_name']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->_var['goods_sn']; ?></th>
    </tr>
    <tr>
      <!-- start for specifications -->
      <?php $_from = $this->_var['attribute']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'attribute_value');if (count($_from)):
    foreach ($_from AS $this->_var['attribute_value']):
?>
        <td scope="col"><div align="center"><strong><?php echo $this->_var['attribute_value']['attr_name']; ?></strong></div></td>
      <?php endforeach; else: ?>
        <td scope="col">&nbsp;</td>
      <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
      <!-- end for specifications -->
      <td class="label_2"><?php echo $this->_var['lang']['goods_sn']; ?></td>
      <td class="label_2"><?php echo $this->_var['lang']['goods_number']; ?></td>
      <td class="label_2">&nbsp;</td>
    </tr>

    <?php $_from = $this->_var['product_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'product');if (count($_from)):
    foreach ($_from AS $this->_var['product']):
?>
    <tr>
      <?php $_from = $this->_var['product']['goods_attr']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods_attr');if (count($_from)):
    foreach ($_from AS $this->_var['goods_attr']):
?>
      <td scope="col"><div align="center"><?php echo $this->_var['goods_attr']; ?></div></td>
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
      <td class="td_1"><span onclick="listTable.edit(this, 'edit_product_sn', <?php echo $this->_var['product']['product_id']; ?>)"><?php echo empty($this->_var['product']['product_sn']) ? $this->_var['lang']['n_a'] : $this->_var['product']['product_sn']; ?></span></td>
      <td class="td_1"><span onclick="listTable.edit(this, 'edit_product_number', <?php echo $this->_var['product']['product_id']; ?>)"><?php echo $this->_var['product']['product_number']; ?></span></td>
      <td><input type="button" class="button" value=" - " onclick="listTable.remove(<?php echo $this->_var['product']['product_id']; ?>, '<?php echo $this->_var['lang']['trash_product_confirm']; ?>', 'product_remove')"/></td>
    </tr>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

    <tr id="attr_row">
    <!-- start for specifications_value -->
    <?php $_from = $this->_var['attribute']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('attribute_key', 'attribute_value');if (count($_from)):
    foreach ($_from AS $this->_var['attribute_key'] => $this->_var['attribute_value']):
?>
      <td align="center">
        <select name="attr[<?php echo $this->_var['attribute_value']['attr_id']; ?>][]">
        <option value="" selected><?php echo $this->_var['lang']['select_please']; ?></option>
        <?php $_from = $this->_var['attribute_value']['attr_values']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'value');if (count($_from)):
    foreach ($_from AS $this->_var['value']):
?>
        <option value="<?php echo $this->_var['value']; ?>"><?php echo $this->_var['value']; ?></option>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
        </select>
      </td>
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
    <!-- end for specifications_value -->

      <td class="label_2"><input type="text" name="product_sn[]" value="" size="20"/></td>
      <td class="label_2"><input type="text" name="product_number[]" value="<?php echo $this->_var['product_number']; ?>" size="10"/></td>
      <td><input type="button" class="button" value=" + " onclick="javascript:add_attr_product();"/></td>
    </tr>

    <tr>
      <td align="center" colspan="<?php echo $this->_var['attribute_count_3']; ?>">
        <input type="button" class="button" value="<?php echo $this->_var['lang']['button_save']; ?>" onclick="checkgood_sn()" />
		<input type="hidden" name="supplier_status" value="<?php echo $_REQUEST['supplier_status']; ?>">
      </td>
    </tr>
  </table>
</form>

<?php if ($this->_var['full_page']): ?>

</div>
<!-- end 添加货品 -->

<script type="text/javascript">

<?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
listTable.filter.supplier_status = '<?php echo $_REQUEST['supplier_status']; ?>';
listTable.query = 'product_query';

var _attr = new Array;
<?php $_from = $this->_var['attribute']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('attribute_key', 'attribute_value');if (count($_from)):
    foreach ($_from AS $this->_var['attribute_key'] => $this->_var['attribute_value']):
?>
_attr[<?php echo $this->_var['attribute_key']; ?>] = '<?php echo $this->_var['attribute_value']['attr_id']; ?>';
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>


onload = function()
{
  startCheckOrder(); // 开始检查订单
}

/**
 * 追加货品添加表格
 */
function add_attr_product()
{
  var table_list = document.getElementById('table_list');
  var new_tr_id = rand_str('t');
  var attr_row, attr_col, new_row, new_col;
  var index = table_list.rows.length - 2; //此行号为输入框所在行

  //创建新行
  attr_row = document.getElementById('attr_row');
  attr_col = attr_row.getElementsByTagName('td');

  new_row = table_list.insertRow(index);//新增行
  new_row.setAttribute("id", new_tr_id);

  //创建新行的列
  for (var i = 0; i < attr_col.length; i++)
  {
    new_col = new_row.insertCell(-1);
    new_col.setAttribute("align", attr_col[i].getAttribute("align"));
    new_col.setAttribute("class", attr_col[i].getAttribute("class"));
    new_col.setAttribute("className", attr_col[i].getAttribute("className"));

    if (i + 1 == attr_col.length)
    {
      new_col.innerHTML = '<input type="button" class="button" value=" - " onclick="javascript:minus_attr_product(\'' + new_tr_id + '\');"/>';
    }
    else
    {
      new_col.innerHTML = attr_col[i].innerHTML;
    }
  }

  //重置选项
//  var sel = new_row.getElementsByTagName('select');
//  if (sel.length > 0)
//  {
//    for (var j = 0; j < sel.length; j++)
//    {
//      sel[j].value = '';
//    }
//  }

  return true;
}

/**
 * 删除追加的货品表格
 */
function minus_attr_product(tr_number)
{
  if (tr_number.length > 0)
  {
    if (confirm("确定删除吗？") == false)
    {
      return false;
    }

    var table_list = document.getElementById('table_list');

    for (var i = 0; i < table_list.rows.length; i++)
    {
      if (table_list.rows[i].id == tr_number)
      {
        table_list.deleteRow(i);
      }
    }
  }

  return true;
}
function  checkgood_sn()
{
    var validator = new Validator('addForm');
    var volumePri = document.getElementsByName("product_sn[]");
	var check_sn='';
    for (i = 0 ; i < volumePri.length ; i ++)
    {
        if(volumePri.item(i).value == "")
        {
        }
		else
		{
		    check_sn=check_sn+'||'+volumePri.item(i).value;
		}
    }
			var callback = function(res)
			{
               if (res.error > 0)
               {
                  alert(res.message);
               }
			   else
		       {
			      document.forms['addForm'].submit();
		       }
		   }
           Ajax.call('goods.php?is_ajax=1&act=check_products_goods_sn', "goods_sn=" + check_sn, callback, "GET", "JSON");

}



</script>
<?php echo $this->fetch('pagefooter.htm'); ?>

<?php endif; ?>