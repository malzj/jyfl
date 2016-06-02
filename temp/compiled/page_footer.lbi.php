<div class="footer">
	<div class="footer_box">
		<div class="footer_content">
			<div class="footer_icon">
				<img src="/images/juyoufuli/img_login/footer_icon.png" style="margin-top: 15px;">
			</div>
			<div class="text-center footer_msg">copyright 2013 京ICP备1405071号-1</div>
		</div>
	</div>
</div>



<?php 
$k = array (
  'name' => 'cron_info',
);
echo $this->_echash . $k['name'] . '|' . serialize($k) . $this->_echash;
?>