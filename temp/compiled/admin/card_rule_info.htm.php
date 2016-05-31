<!-- $Id: brand_info.htm 14216 2008-03-10 02:27:21Z testyang $ -->
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/transport.js,../js/region.js')); ?>

<div class="main-div">
<form method="post" action="card_rule.php" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
<table cellspacing="1" cellpadding="3" width="100%">
  <tr>
    <td class="label">说明：</td>
    <td><input type="text" name="title" value="<?php echo $this->_var['cardinfo']['title']; ?>" /></td>
  </tr>
  <tr>
    <td class="label">卡号：</td>
    <td>
	<input  name="file" type="FILE" style="width:180px;border:0;"/>(文件不能超过2m.如果超过,可以多次导入)</td>
  </tr>
  <tr>
    <td class="label">城市hom页内容：</td>
    <td><?php echo $this->_var['FCKeditor']; ?></td>
  </tr>
   <tr>
    <td class="label">售价：</td>
    <td><input type="text" name="price" value="<?php echo $this->_var['cardinfo']['price']; ?>" /> 元</td>
  </tr> 
  
  <tr>
    <td class="label">支付比例：</td>
    <td><input type="text" name="pay_than" value="<?php echo $this->_var['cardinfo']['pay_than']; ?>" /> 会员中心 => 卡充值的时候用于计算需要支付的金额。</td>
  </tr> 
  <tr>
    <td class="label">卡类型：</td>
    <td>
    	<input type="radio" name="type" value="1" <?php if ($this->_var['cardinfo']['type'] == 1): ?>checked<?php endif; ?>> 文化卡
    	<input type="radio" name="type" value="2" <?php if ($this->_var['cardinfo']['type'] == 2): ?>checked<?php endif; ?>> 生日卡
    	<input type="radio" name="type" value="3" <?php if ($this->_var['cardinfo']['type'] == 3): ?>checked<?php endif; ?>> 生活卡
    	<input type="radio" name="type" value="4" <?php if ($this->_var['cardinfo']['type'] == 4): ?>checked<?php endif; ?>> 运动卡
    	<input type="radio" name="type" value="5" <?php if ($this->_var['cardinfo']['type'] == 5): ?>checked<?php endif; ?>> 通卡
    </td>
  </tr
  <tr>
    <td class="label">折扣调整：</td>
    <td><input type="checkbox" name="zhekou" value="1" <?php if ($this->_var['cardinfo']['zhekou']): ?> checked ='checked' <?php endif; ?>/><br/></td>
  </tr> 
     <tr>
    <td class="label">商品调整：</td>
    <td><input type="checkbox" name="shop" value="1" <?php if ($this->_var['cardinfo']['shop']): ?> checked ='checked' <?php endif; ?>/><br/></td>
  </tr> 
 
  <tr>
    <td class="label">城市导航设置：<br/><span style="color:#666;line-height:2em;">不设置该项表示该城市默认显示系统导航</span></td>
    <td>
		<table width="100%" id="nav_setting">
			<?php $_from = $this->_var['cardinfo']['navinfo']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key0', 'setting');$this->_foreach['setting'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['setting']['total'] > 0):
    foreach ($_from AS $this->_var['key0'] => $this->_var['setting']):
        $this->_foreach['setting']['iteration']++;
?>			
			<tr>
				<td align="left" valign="top">
					<?php if ($this->_foreach['setting']['iteration'] == 1): ?>
					<a href="javascript:;" onclick="addNavSetting(this)" style="float:left;">[+]</a>
					<?php else: ?>
					<a href="javascript:;" onclick="removeNavSetting(this)" style="float:left;">[−]</a>
					<?php endif; ?>
				<!-- </div> -->
					<div style="clear:both;"></div>
					<table style="float:left;width:90%;border:none;" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td valign="top" width="50" style="border-bottom:1px solid #ccc;">导航</td>
							<td valign="top" width="110" style="border-bottom:1px solid #ccc;">
								<select id="nav_id_<?php echo ($this->_foreach['setting']['iteration'] - 1); ?>" name="nav_id[<?php echo ($this->_foreach['setting']['iteration'] - 1); ?>]" style="width:100px;">
									<option value="0">请选择</option>
									<?php $_from = $this->_var['navlist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key1', 'nav');if (count($_from)):
    foreach ($_from AS $this->_var['key1'] => $this->_var['nav']):
?>
									<option value="<?php echo $this->_var['nav']['id']; ?>"<?php if ($this->_var['nav']['id'] == $this->_var['setting']['nav_id']): ?> selected<?php endif; ?>><?php echo $this->_var['nav']['name']; ?></option>
									<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
								</select>
								<br><br>
								<div style="position:relative;">
									<span style="position:absolute; top:3px; left:-55px;">商城比例：</span>
									<span><input type="text" name="shop_ratio[<?php echo ($this->_foreach['setting']['iteration'] - 1); ?>]" size="10" value="<?php echo $this->_var['setting']['shop_ratio']; ?>"></span>
								</div>
															
							</td>
							<td valign="top" width="60" style="border-bottom:1px solid #ccc;">
								<label><input type="checkbox" name="checkall" value="" onclick="checkAreaAll(this,<?php echo $this->_var['key0']; ?>)" />区域</label>：

							   <br>
								<span id="checkshow_<?php echo $this->_var['key0']; ?>" onclick="checkshow(<?php echo $this->_var['key0']; ?>);">【展开】</span>
							
								<span id="checkhidden_<?php echo $this->_var['key0']; ?>" onclick="checkhidden(<?php echo $this->_var['key0']; ?>);" style="display:none;">【收起】</span>
								</td>
							<td valign="top" style="border-bottom:1px solid #ccc;">
								<?php $_from = $this->_var['region_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key2', 'region');if (count($_from)):
    foreach ($_from AS $this->_var['key2'] => $this->_var['region']):
?>

								<?php if ($this->_var['key2'] > 19): ?>
								<div id="check_<?php echo $this->_var['key0']; ?>_<?php echo $this->_var['key2']; ?>" style="width:80px; display:inline-block;height:20px;line-height:20px;overflow:hidden;display:none;">
								<label style="width:80px; display:inline-block;height:20px;line-height:20px;overflow:hidden;" title="<?php echo $this->_var['region']['region_name']; ?>">
								<input type="checkbox" name="region[<?php echo ($this->_foreach['setting']['iteration'] - 1); ?>][]" value="<?php echo $this->_var['region']['region_id']; ?>" <?php echo $this->_var['setting']['check'][$this->_var['region']['region_id']]; ?> />
								<?php echo $this->_var['region']['region_name']; ?></label></div>
								<?php else: ?>
								<label style="width:80px; display:inline-block;height:20px;line-height:20px;overflow:hidden;" title="<?php echo $this->_var['region']['region_name']; ?>">
								<input type="checkbox" name="region[<?php echo ($this->_foreach['setting']['iteration'] - 1); ?>][]" value="<?php echo $this->_var['region']['region_id']; ?>" <?php echo $this->_var['setting']['check'][$this->_var['region']['region_id']]; ?> />
								<?php echo $this->_var['region']['region_name']; ?></label>
								<?php endif; ?>
								<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
		</table>
	</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><br />
      <input type="submit" class="button" value="<?php echo $this->_var['lang']['button_submit']; ?>" />
      <input type="reset" class="button" value="<?php echo $this->_var['lang']['button_reset']; ?>" />
      <input type="hidden" name="act" value="update" />
      <input type="hidden" name="id" value="<?php echo $this->_var['cardinfo']['id']; ?>" />
    </td>
  </tr>
</table>
</form>
</div>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,validator.js')); ?>

<script language="JavaScript">
<!--
region.isAdmin = true;

onload = function()
{
    // 开始检查订单
    startCheckOrder();
}


function addNavSetting(obj){
	var src      = obj.parentNode.parentNode;
	var tbl      = document.getElementById('nav_setting');
	var rowl = tbl.rows.length;
	var validator  = new Validator('theForm');
	/*checkGoodsSnData("0", validator, rowl);
	if (!validator.passed()){
		return false;
	}*/
	var row  = tbl.insertRow(tbl.rows.length);
	var cell = row.insertCell(-1);
	cell.align = 'center';

	cell.innerHTML = src.cells[0].innerHTML.replace(/(.*)(addNavSetting)(.*)(\[\+\])([\s\S]*?)(nav_id\[\d*\])/igm, "$1removeNavSetting$3\[−\]$5nav_id\["+rowl+"\]");
	cell.innerHTML = cell.innerHTML.replace(/<select(.*)(nav_id_\d*)/gi, "<select$1nav_id_"+rowl+"");
	cell.innerHTML = cell.innerHTML.replace(/<input(.*)(checkAreaAll\(\w+\,\d*\))/gi, "<input$1checkAreaAll(this,"+rowl+")");
	//cell.innerHTML = cell.innerHTML.replace(/<input(.*)(region\[\d*\])/igm, "<input$1region["+rowl+"]");
	//cell.innerHTML = cell.innerHTML = cell.innerHTML.replace(/region\[\d*\]/ig,'region['+rowl+']');
	//清空新增加导航的值
	var objSelect = document.getElementById('nav_id_'+rowl);
	objSelect.selectedIndex = 0;

	var checkall  = document.getElementsByName("checkall");
	checkall[rowl].checked = false;

	//清空新增加地区的值
	var region_list  = document.getElementsByName("region["+(rowl)+"][]");
	for (var i=0; i<region_list.length; i++){
		region_list[i].checked = false;
	}
}

/**
* 鍒犻櫎浼樻儬浠锋牸
*/
function removeNavSetting(obj)
{
var row = rowindex(obj.parentNode.parentNode);
var tbl = document.getElementById('nav_setting');

tbl.deleteRow(row);
}

/**
 * 检查表单输入的数据
 */
function validate()
{
	if(document.getElementById("radio").checked) {
		
		
	}
}

function checkAreaAll(obj,sn){
	var obj_c = document.getElementsByName('region['+sn+'][]')
	for(var i=0; i<obj_c.length; i++){
		obj_c[i].checked = obj.checked;
	}
}
function checkshow(key)
{
	document.getElementById("checkshow_"+key).style.display="none";
	document.getElementById("checkhidden_"+key).style.display="block";
	// alert(key);
	for (var i=20; i<300; i++)
	  {
	 	check='check_'+key+"_"+i;
	 	document.getElementById(check).style.display="block";
	 	document.getElementById(check).style.display="inline-block";
 		// alert(check);
	  }
	  
}
function checkhidden(key)
{
	document.getElementById("checkhidden_"+key).style.display="none";
	document.getElementById("checkshow_"+key).style.display="block";
	for (var i=21; i<300; i++)
	  {
	 	check='check_'+key+"_"+i;
	 	document.getElementById(check).style.display="none";
 		// alert(check);
	  }
}
//-->
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>