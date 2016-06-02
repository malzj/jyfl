<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        
<div class="order_grshang_bottom_adress f_l">配送地址：<span><?php echo htmlspecialchars($this->_var['consignee']['consignee']); ?>&nbsp;&nbsp;<?php echo htmlspecialchars($this->_var['consignee']['country_cn']); ?>&nbsp;&nbsp;<?php echo htmlspecialchars($this->_var['consignee']['province_cn']); ?>&nbsp;&nbsp;<?php echo htmlspecialchars($this->_var['consignee']['city_cn']); ?>&nbsp;&nbsp;<?php echo htmlspecialchars($this->_var['consignee']['district_cn']); ?>&nbsp;&nbsp;<?php echo htmlspecialchars($this->_var['consignee']['address']); ?></span></div>
<div class="f_r order_gyshang_operate">
    <?php if ($this->_var['detail']['open_time'] == 1): ?>
    <div class="f_l"><input class="peisong_date laydate-icon" onclick="laydate({choose:function(dates){ selectdate(dates,<?php echo $this->_var['detail']['supplier_id']; ?>,'<?php echo htmlspecialchars($this->_var['consignee']['country_cn']); ?><?php echo htmlspecialchars($this->_var['consignee']['province_cn']); ?><?php echo htmlspecialchars($this->_var['consignee']['city_cn']); ?><?php echo htmlspecialchars($this->_var['consignee']['district_cn']); ?><?php echo htmlspecialchars($this->_var['consignee']['address']); ?>');}})"  name="riqi[<?php echo $this->_var['detail']['supplier_id']; ?>]" value="配送日期"></div>
    <div class="f_l peisong_time" id="time<?php echo $this->_var['detail']['supplier_id']; ?>">
        <select name="time[<?php echo $this->_var['detail']['supplier_id']; ?>]">
            <option>配送时间</option>            
        </select>
    </div>
    <?php endif; ?>
    
    
    <div class="peisong f_l" id="zhichi<?php echo $this->_var['detail']['supplier_id']; ?>"> ... </div>
    
    <div class="bottom_operate f_l">
        <div class="btn-group">
          <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            操作<span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            <li><a href="javascript:if (confirm('您确实要把 《<?php echo $this->_var['detail']['supplier_name']; ?>》 的所有商品移出吗?')) location.href='flow.php?step=drop_supplier_goods&amp;id=<?php echo $this->_var['detail']['supplier_id']; ?>'">删除</a></li>
            <li><a href="javascript:void(0);" class="editAddress" data-id="<?php echo $this->_var['detail']['supplier_id']; ?>">修改</a></li>   
          </ul>
        </div>
    </div>
    
    <div class="goods_freight f_l">运费：<span><font id="yunfei<?php echo $this->_var['detail']['supplier_id']; ?>"> - </font>点</span></div>
    <input type="hidden" name="sup[<?php echo $this->_var['detail']['supplier_id']; ?>]" id="sup_<?php echo $this->_var['detail']['supplier_id']; ?>" class="supplier-one" value="">
    <div style="float:left;width:600px;height:0px;border:1px solid gray; display:none; " id="map<?php echo $this->_var['detail']['supplier_id']; ?>"></div>

</div>

<script>

_initYunfei();

// 运费计算
function _initYunfei(){
	baidumap.setOptions({
		isYunfei:true,
		isSetYunfei:true,
		isTime:1,
		showMapId:'map<?php echo $this->_var['detail']['supplier_id']; ?>',
		afterFunction:function(d){
			var yunfeiTotal = 0;
			$('.supplier-one').each(function(index,dom){
				var yunfei = $(dom).val();
				if(yunfei != -1){
					yunfeiTotal = parseInt(yunfeiTotal)+parseInt(yunfei);
				}
				
			});
			$('.yunfeiTotal').html(yunfeiTotal);
			orderTotal = (parseFloat(yunfeiTotal)+parseFloat($('.goodsTotal').text())).toFixed(2);
			$('.orderTotal').html(orderTotal);
		}	
	});
	baidumap.showMap(<?php echo $this->_var['detail']['supplier_id']; ?>,"<?php echo htmlspecialchars($this->_var['consignee']['country_cn']); ?><?php echo htmlspecialchars($this->_var['consignee']['province_cn']); ?><?php echo htmlspecialchars($this->_var['consignee']['city_cn']); ?><?php echo htmlspecialchars($this->_var['consignee']['district_cn']); ?><?php echo htmlspecialchars($this->_var['consignee']['address']); ?>");
}
</script>
