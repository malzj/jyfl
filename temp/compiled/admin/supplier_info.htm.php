<!-- $Id: agency_info.htm 14216 2008-03-10 02:27:21Z testyang $ -->
<?php echo $this->fetch('pageheader.htm'); ?> 
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,selectzone_bd.js,colorselector.js,listtable.js')); ?>
<script type="text/javascript" src="../js/calendar.php?lang=<?php echo $this->_var['cfg_lang']; ?>"></script>
<link href="../js/calendar/calendar.css" rel="stylesheet" type="text/css" />
<style>
	#ratio-table tr th, #show-table tr th {
		text-align:left;
		text-indent:30px;
		border-bottom:1px #ccc solid;
		padding-bottom:10px;
	}
	#ratio-table tr th .goods_name, #show-table tr th .goods_name{width:250px; height:20px; line-height:20px; text-indent:10px;}
	#ratio-table tr th .search_goods, #show-table tr th .search_goods{width:50px; height:28px; line-height:28px; text-align:center; margin-left:10px;}
	#ratio-table tr td.p0, #show-table tr td.p0{padding:20px 0;}
	#ratio-table tr td .table-row{border:1px #ccc dotted; padding:10px; margin-bottom:10px;}
	.card_rule{min-height:200px; max-height:800px; height:100%; display:inline-block}
	.goods_list ul,.card_rule ul{list-style:none; padding:0 0 0 10px;}
	.card_rule ul li{width:290px; min-height:30px; line-height:20px; float:left;}
</style>
<!-- start goods form -->
<div class="tab-div">
    <!-- tab bar -->
    <div id="tabbar-div">
      <p>
        <span class="tab-front" id="detail-tab">基本信息</span>        
        <span class="tab-back" id="ratio-tab">商品折扣设置</span>
        <span class="tab-back" id="show-tab">客户显示设置</span>            
      </p>
	</div>

	<div id="tabbody-div">		
		<table align="center" id="detail-table" style="width: 100%" border="0" cellpadding="3" cellspacing="1" bgcolor="#ffffff">
			<form method="post" action="supplier.php?act=<?php echo $this->_var['form_action']; ?>" name="theForm" enctype="multipart/form-data" onsubmit="return validate()">
			<tr>
				<td width="28%" align="right" bgcolor="#FFFFFF"><span
					style="color: #FF0000"> *</span>供应商等级：</td>
				<td align="left" bgcolor="#FFFFFF"><select name="rank_id"
					size=1>
						<option value="0">请选择</option> 
						<?php $_from = $this->_var['supplier_rank']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'rank');if (count($_from)):
    foreach ($_from AS $this->_var['rank']):
?>
						<option value="<?php echo $this->_var['rank']['rank_id']; ?>" <?php if ($this->_var['supplier']['rank_id'] == $this->_var['rank']['rank_id']): ?>selected<?php endif; ?>><?php echo $this->_var['rank']['rank_name']; ?></option>
						<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				</select></td>
			</tr>
			<tr>
				<?php if ($this->_var['supplier_user']): ?>
				<td width="28%" align="right" bgcolor="#FFFFFF"><span
					style="color: #FF0000"> *</span>账号：</td>
				<td align="left" bgcolor="#FFFFFF"><select name="user_id"
					size=1>
						<option value="0">请选择</option> 
						<?php $_from = $this->_var['supplier_user']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'user');if (count($_from)):
    foreach ($_from AS $this->_var['user']):
?>
						<option value="<?php echo $this->_var['user']['user_id']; ?>" <?php if ($this->_var['supplier']['user_id'] == $this->_var['user']['user_id']): ?>selected<?php endif; ?>><?php echo $this->_var['user']['user_name']; ?></option>
						<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
				</select></td>
			</tr>
			<?php endif; ?>

			<tr>
				<td width="28%" align="right" bgcolor="#FFFFFF">供货商名称：</td>
				<td align="left" bgcolor="#FFFFFF"><input name="supplier_name"
					type="text" size="45" value="<?php echo $this->_var['supplier']['supplier_name']; ?>" /></td>
			</tr>
			<tr>
			<tr>
				<td width="28%" align="right" bgcolor="#FFFFFF"><span
					style="color: #FF0000"> *</span>公司地址：</td>
				<td align="left" bgcolor="#FFFFFF"><input name="address"
					type="text" size="45" value="<?php echo $this->_var['supplier']['address']; ?>" /></td>
			</tr>
			<tr bgcolor="#ffffff">
				<td width="28%" align="right"><span style="color: #FF0000">
						*</span>公司电话：</td>
				<td align="left"><input type="text" name="tel"
					value="<?php echo $this->_var['supplier']['tel']; ?>" /></td>
			</tr>

			<tr bgcolor="#ffffff">
				<td width="28%" align="right"><span style="color: #FF0000">
						*</span>电子邮箱：</td>
				<td align="left"><input type="text" name="email" size=45
					value="<?php echo $this->_var['supplier']['email']; ?>" /></td>
			</tr>
			<tr bgcolor="#ffffff">
				<td width="28%" align="right"><span style="color: #FF0000">*</span>是否开启派送时间：</td>
				<td align="left">
					<input type="radio" name="open_time" value="1" <?php if ($this->_var['supplier']['open_time'] == '1'): ?> checked <?php endif; ?> />开启 
					<input type="radio" name="open_time" value="0" <?php if ($this->_var['supplier']['open_time'] == '0' || $this->_var['supplier'] == ''): ?> checked <?php endif; ?> />关闭
				</td>
			</tr>
			<tr bgcolor="#ffffff">
				<td width="28%" align="right"><span style="color: #FF0000">*</span>商城比例：</td>
				<td align="left"><input type="text" name="shop_ratio" size=10 value="<?php echo $this->_var['supplier']['shop_ratio']; ?>" /> <font color="#ccc">例：输入 0.8，商城显示的扣点价是，市场价 X 0.8</font></td>
			</tr>
			<tr bgcolor="#ffffff">
				<td width="28%" align="right"><span style="color: #FF0000">*</span>成本比例：</td>
				<td align="left"><input type="text" name="cost_ratio" size=10 value="<?php echo $this->_var['supplier']['cost_ratio']; ?>" /> <font color="#ccc">例：输入 0.7，供应商给我们的供货价是，市场价 X 0.7</font></td>
			</tr>
			<tr bgcolor="#ffffff">
				<td width="28%" align="right"><span style="color: #FF0000">*</span>销售方式：</td>
				<td align="left">
					<input type="radio" name="is_entity" value="1" <?php if ($this->_var['supplier']['is_entity'] == '1'): ?> checked <?php endif; ?> />实体
					<input type="radio" name="is_entity" value="2" <?php if ($this->_var['supplier']['is_entity'] == '2' || $this->_var['supplier'] == ''): ?> checked <?php endif; ?> />在线
				</td>
			</tr>
			<tr bgcolor="#e2e9e7">
				<td width="28%" align="right"><span style="color: #FF0000">*</span>是否有门票：</td>
				<td align="left">
					<input type="radio" name="is_tickets" value="1" <?php if ($this->_var['supplier']['is_tickets'] == '1'): ?> checked <?php endif; ?> />有门票
					<input type="radio" name="is_tickets" value="2" <?php if ($this->_var['supplier']['is_tickets'] == '2' || $this->_var['supplier'] == ''): ?> checked <?php endif; ?> />没门票  <font color="red">&nbsp;&nbsp;&nbsp;&nbsp;只有销售方式是 “实体” 的时候，才需要设置此项。</font>
				</td>
			</tr>
			<tr bgcolor="#e2e9e7">
				<td width="28%" align="right"><span style="color: #FF0000">*</span>是否显示客户类型（普通）：</td>
				<td align="left">
					<input type="radio" name="show_ordinary" value="2" <?php if ($this->_var['supplier']['show_ordinary'] == '2'): ?> checked <?php endif; ?> />是
					<input type="radio" name="show_ordinary" value="1" <?php if ($this->_var['supplier']['show_ordinary'] == '1' || $this->_var['supplier'] == ''): ?> checked <?php endif; ?> />否 <font color="red">&nbsp;&nbsp;&nbsp;&nbsp;只有销售方式是 “实体” 的时候，才需要设置此项。</font>
				</td>
			</tr>
			
			
			<tr>
				<td width="20%" align=right valign=top>供货商备注：</td>
				<td><textarea name="supplier_remark" rows=4 cols=50><?php echo $this->_var['supplier']['supplier_remark']; ?></textarea></td>
			</tr>
			<tr>
				<td width="20%" align=right>审核状态：</td>
				<td><select name="status" size=1><option value="0"
							<?php if ($this->_var['supplier']['status'] == '0'): ?>selected<?php endif; ?>>未审核</option>
						<option value="1" <?php if ($this->_var['supplier']['status'] == '1'): ?>selected<?php endif; ?>>审核通过</option>
						<option value="-1" <?php if ($this->_var['supplier']['status'] == '-1'): ?>selected<?php endif; ?>>审核不通过</option></select></td>
			</tr>
			<tr>
				<td colspan=2>
					<div class="button-div">
		 				<input type="submit" class="button" value="<?php echo $this->_var['lang']['button_submit']; ?>" /> 
					 	<input type="reset" class="button" value="<?php echo $this->_var['lang']['button_reset']; ?>" /> 		 	
					 	<input type="hidden" name="id" value="<?php echo $this->_var['supplier']['supplier_id']; ?>" />
					 </div>
				</td>
			</tr>
			</form>	
		</table>
		<form method="post" action="supplier.php?act=spec_ratio">
		<table id="ratio-table" style="display:none; width:100%">		   
			<tr>
				<th><span>商品名称：<input type="text" class="goods_name" name="goods_name"><input type="button" name="submit" class="search_goods" value="搜索" onclick="searchGoods(this,1);"></span></th>
			</tr>
			<tr>
				<td class="p0" id="ratio-html">
					<center>请输入商品名称，搜索！</center>  
				</td>
			</tr>
			<tr>
				<td colspan=2>
					<div class="button-div">
		 				<input type="submit" class="button" value="<?php echo $this->_var['lang']['button_submit']; ?>" /> 
					 	<input type="reset" class="button" value="<?php echo $this->_var['lang']['button_reset']; ?>" /> 		 	
					 	<input type="hidden" name="id" value="<?php echo $this->_var['supplier']['supplier_id']; ?>" />
					 </div>
				</td>
			</tr>			
		</table>
		</form>	
		
		<form method="post" action="supplier.php?act=spec_show">
		<table id="show-table" style="display:none; width:100%">
			<tr>
				<th colspan="2"><span>商品名称：<input type="text" class="goods_name" name="goods_name"><input type="button" name="submit" class="search_goods" value="搜索" onclick="searchGoods(this,2);"></span></th>
			</tr>
			<tr>
				<td class="p0" width="400px" id="show-html">
                	<center>请输入商品名称，搜索！</center>     
                </td>
                <td style="border-left:1px #ccc solid;">
                	<div class="card_rule">
                    	<ul>
                    	<?php $_from = $this->_var['card_rule']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'rule');if (count($_from)):
    foreach ($_from AS $this->_var['rule']):
?>
                        	<li><input type="checkbox" name="rule_id[]" value="<?php echo $this->_var['rule']['id']; ?>" id='r<?php echo $this->_var['rule']['id']; ?>'/><label for="r<?php echo $this->_var['rule']['id']; ?>"><?php echo $this->_var['rule']['title']; ?></label></li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                        </ul>
                    </div>
                </td>
            </tr>
			<tr>
				<td colspan=2>
					<div class="button-div">
		 				<input type="submit" class="button" value="<?php echo $this->_var['lang']['button_submit']; ?>" /> 
					 	<input type="reset" class="button" value="<?php echo $this->_var['lang']['button_reset']; ?>" /> 		 	
					 	<input type="hidden" name="id" value="<?php echo $this->_var['supplier']['supplier_id']; ?>" />
					 </div>
				</td>
			</tr>			
		</table>	
		</form>		 
	</div>
</div>
<?php echo $this->smarty_insert_scripts(array('files'=>'validator.js,tab.js')); ?>

<script language="JavaScript">
<!--
	onload = function() {
		// 开始检查订单
		startCheckOrder();
	}	
	 // 商品规格搜索
	 function searchGoods(obj,e){
		 //最外层的th
		 var thObj = obj.parentNode.parentNode;
		 //搜索的商品名
		 var goods_name = thObj.getElementsByTagName('input')[0].value;
		 if(e==1){
			 Ajax.call('supplier.php?is_ajax=1&act=search_goods', 'goods_name=' + goods_name + "&type="+ e +"&supplier_id=<?php echo $this->_var['supplier']['supplier_id']; ?>", setGoodsList, "GET", "JSON");
		 }else{
			 Ajax.call('supplier.php?is_ajax=1&act=search_goods', 'goods_name=' + goods_name + "&type="+ e +"&supplier_id=<?php echo $this->_var['supplier']['supplier_id']; ?>", setGoodsShowList, "GET", "JSON");
		 }
		 
	 }
	 function setGoodsList(result, text_result){
		 document.getElementById('ratio-html').innerHTML = result.content;
	 }
	 function setGoodsShowList(result, text_result){
		 document.getElementById('show-html').innerHTML = result.content;
	 }
//-->
</script>
 <?php echo $this->fetch('pagefooter.htm'); ?>


