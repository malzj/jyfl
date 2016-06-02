<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div class="shouhuo_title">
    <span class="shouhuorenxinxi f_l">收货人信息</span>
    <span class="add_adress f_r" onclick="selectConsignee(0)">新增收货地址</span>
</div>
<div class="shouhuoren_item">
    <ul>
        <?php $_from = $this->_var['consignee_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('sn', 'consignee');if (count($_from)):
    foreach ($_from AS $this->_var['sn'] => $this->_var['consignee']):
?>
        <li>
            <div class="f_l shouhuoren_name <?php if ($this->_var['consignee']['selected'] == 1): ?>selected<?php endif; ?>" onClick="javascript:setDefaultConsignee('<?php echo $this->_var['consignee']['address_id']; ?>');">
                <span><?php echo $this->_var['consignee']['consignee']; ?></span>
            </div>
            <div class="f_l shouhuoren_xinxi">
                <span class="name"><?php echo $this->_var['consignee']['consignee']; ?></span>
                <span class="addr"><?php echo htmlspecialchars($this->_var['consignee']['country_cn']); ?>&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($this->_var['consignee']['province_cn']); ?>&nbsp;&nbsp;<?php echo htmlspecialchars($this->_var['consignee']['city_cn']); ?>&nbsp;&nbsp;<?php echo htmlspecialchars($this->_var['consignee']['district_cn']); ?>&nbsp;&nbsp;<?php echo htmlspecialchars($this->_var['consignee']['address']); ?></span>
                <span class="addr_tel"><?php if ($this->_var['consignee']['mobile'] && $this->_var['consignee']['tel']): ?><?php echo htmlspecialchars($this->_var['consignee']['tel']); ?>(<?php echo htmlspecialchars($this->_var['consignee']['mobile']); ?>)<?php elseif ($this->_var['consignee']['tel']): ?><?php echo htmlspecialchars($this->_var['consignee']['tel']); ?><?php else: ?><?php echo htmlspecialchars($this->_var['consignee']['mobile']); ?><?php endif; ?></span>
            </div>
            <div class="f_r shouhuoren_hover">
                <a href="javascript:setDefaultConsignee('<?php echo $this->_var['consignee']['address_id']; ?>');">设为默认地址</a>
                <a href="javascript:selectConsignee('<?php echo $this->_var['consignee']['address_id']; ?>');">编辑</a>
                <a href="javascript:dropConsignee('<?php echo $this->_var['consignee']['address_id']; ?>');">删除</a>
            </div>
        </li>
        <?php endforeach; else: ?>
        <li> 没有收货地址，添加一个去吧！</li>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>            
    </ul>
</div> 

<div class="more">
   <span id="more">更多地址<span class="glyphicon glyphicon-chevron-down jiantou"></span></span>
</div>

<div class="add_item" id="add_item">
    
</div>
<script>
$('.shouhuoren_item li').hover(function(){
	$(this).children('.shouhuoren_hover').toggle();
});
$('#more').click(function(){
	var height=$('.shouhuoren_item').height();
	if(height=='40'){
		$('html.huaju2 .w_1200 .shouhuoren .more span.jiantou').css('transform','rotate(180deg)');
		$('.shouhuoren_item').css('height','auto');
	}else{
		$('html.huaju2 .w_1200 .shouhuoren .more span.jiantou').css('transform','rotate(0deg)');
		$('.shouhuoren_item').css('height','40px');
	}
})
</script>