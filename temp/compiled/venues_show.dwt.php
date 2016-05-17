<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="Keywords" content="<?php echo $this->_var['keywords']; ?>" />
<meta name="Description" content="<?php echo $this->_var['description']; ?>" />
<title><?php echo $this->_var['page_title']; ?></title>
<link href="/css/hy_master.css" rel="stylesheet" type="text/css" />
<link href="/css/hy_layout.css" rel="stylesheet" type="text/css" />
<link href="/css/reset.css" rel="stylesheet" type="text/css" />
<link href="/css/venues.css" rel="stylesheet" type="text/css" />
<link href="/css/sports.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/jquery-1.4.2.min.js"></script>
<style>
img{display:inline-block;}
</style>

</head>
<body>

<?php echo $this->fetch('library/page_header.lbi'); ?>


<div class="main">
    		
    <div class="hy_position">
        <p class="position01"><?php echo $this->fetch('library/ur_here.lbi'); ?></p>					
    </div>
</div>

	<div class="wrap">
		<div class="buy-warp">
			<div class="title">
                <span><?php echo $this->_var['detail']['venueName']; ?></span>
                <span class="facico">
                <?php if ($this->_var['detail']['equipment']): ?>
                     <?php $_from = $this->_var['detail']['equipment']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'equipment');if (count($_from)):
    foreach ($_from AS $this->_var['equipment']):
?>
                     <img src="/images/dongwang/<?php echo $this->_var['equipment']['equipmentId']; ?>.jpg" width="20" height="20" title="<?php echo $this->_var['equipment']['equipmentName']; ?>"/>
                     <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                <?php endif; ?>   
                </span>
                <div style="overflow: hidden; margin-bottom:20px;line-height: 2;">
                    <div class="adress">详细地址：<?php echo $this->_var['detail']['place']; ?>     
                        <p>营业时间：<?php echo $this->_var['detail']['stime']; ?>—<?php echo $this->_var['detail']['etime']; ?></p>
                    </div>
                   <!-- <div class="yuding">
                        <a href="#">立即预定</a>
                    </div>-->
                </div>
            </div>
			<div class="buy-warp-left">
				<div class="StatusBar">
					<div class="bastab left">
						<span class="bastabcur"><a href="#">全场</a></span>
					</div>
					<div class="Statusright">
						<span><em class="k1"></em>可预订</span>
						<span><em class="k2"></em>不可订</span>
						<span><em class="k3"></em>已预订</span>
					</div>
				</div>
				<div class="" id="subtype">
					<div class="week">
                      <?php $_from = $this->_var['venues']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'venue');if (count($_from)):
    foreach ($_from AS $this->_var['venue']):
?>
                        <a <?php if ($this->_var['venue']['active'] == 1): ?> class="active" <?php endif; ?> href="venues_show.php?venueId=<?php echo $this->_var['venueId']; ?>&infoId=<?php echo $this->_var['venue']['infoId']; ?>&orderDate=<?php echo $this->_var['venue']['date']; ?>">
                            <p><span class="day"><?php echo $this->_var['venue']['week']; ?></span><span class="date"><?php echo $this->_var['venue']['date_mt']; ?></span></p>
                            <p><span class="money"><?php echo $this->_var['venue']['salePrice']; ?></span><span>点起</span></p>
                            <!--<p><span class="num">剩余 </span><span><?php echo $this->_var['venue']['venueNum']; ?></span></p>-->
                        </a>
                     <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						
						
					</div>
					<div class="subtimes">
						<ul>
                        <?php $_from = $this->_var['timeData']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'time');if (count($_from)):
    foreach ($_from AS $this->_var['time']):
?>
							<li><span><?php echo $this->_var['time']; ?></span></li>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>							
						</ul>
					</div>
					<div class="ros-list" style="width:<?php echo $this->_var['width']; ?>px;float: left;overflow: hidden;">
                    	<?php $_from = $this->_var['priceData']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'price');if (count($_from)):
    foreach ($_from AS $this->_var['price']):
?>
						<dl data-date="<?php echo $this->_var['date']; ?>" venue-no="<?php echo $this->_var['price']['rows']; ?>">
							<dt><?php echo $this->_var['price']['rows']; ?>号场地</dt>
                            <?php $_from = $this->_var['price']['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'list');if (count($_from)):
    foreach ($_from AS $this->_var['list']):
?>
							<dd class="pk_dd2">
								<div class="ros">
                                	<?php if ($this->_var['list']['num'] == 0): ?>
                                    <span class="cursor"></span>
                                    <?php else: ?>                                    
									<span data-price="<?php echo $this->_var['list']['salePrice']; ?>" data-fee="0" data-sale-price="<?php echo $this->_var['list']['salePrice']; ?>" data-id="<?php echo $this->_var['price']['rows']; ?><?php echo $this->_var['list']['sTime']; ?>" data-clock="<?php echo $this->_var['list']['sTime_mt']; ?>" data-s="<?php echo $this->_var['list']['sTime']; ?>" data-e="<?php echo $this->_var['list']['eTime']; ?>" title="<?php echo $this->_var['list']['sTime_mt']; ?>-<?php echo $this->_var['list']['eTime_mt']; ?> <?php echo $this->_var['list']['rows']; ?>号场地 <?php echo $this->_var['list']['salePrice']; ?>点"></span>
                                    <?php endif; ?>
								</div>
							</dd>
                            <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						</dl>
                        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
						
					</div>
				</div>
			</div>
			<form name="f1" id="f1" action="venues_order.php" method="post">
				<div class="buy-warpright" style="float:right">
					<div class="buy-warp-right" style="float:none">
						<div class="yd_venname"><?php echo $this->_var['detail']['venueName']; ?></div>
						<div class="selectvenues">
							<div class="ydxx_vstyle"><span>全场</span></div>
							<div class="once">场次</div>
							<div>
								<div class="selsected" id="_id_contianer">
								</div>
								<span id="dhpos"></span>
								<div style="line-height:24px; height:24px; margin-top:10px;">已选择<em style="color:#f60; margin:0 3px; font-size:18px;" id="_id_total">0</em>个场地
									<span style="color:#51B5E7">，再次单击取消</span></div>
								<div class="tj_main" style="padding:5px 0">共计<b id="_id_amount" style="color:#F60; margin:0 5px; font-size:22px;">0</b>点</div>
							</div>
						</div>
						<div>
							<div class="input-t">姓名</div>
							<div class="mobiled">
								<label class="inpt">
									<input class="G_input" type="text" name="link_man" id="real_name" value="" alt="姓名">
								</label>
							</div>
							<div class="input-t">手机号码</div>
							<div class="mobiled">
								<label class="inpt">
									<input class="G_input" type="text" name="link_phone" id="mobile" alt="手机号码">
								</label>
							</div>
							<div class="mobiled">
								<a href="javascript:void(0);" class="T_btn_g" id="sub1" name="sub1" onclick="sub();">提交订单</a>
							</div>
						</div>
					</div>
				</div>
				<input type="hidden" name="info_id" value="<?php echo $this->_var['infoId']; ?>">			
				<input type="hidden" name="travel_date" id="travel_date" value="<?php echo $this->_var['date']; ?>">
				<input type="hidden" name="num" id="num" value="">
				<input type="hidden" name="amount" id="amount" value="">
				<input type="hidden" name="param" id="param" value="">	
                <input type="hidden" name="venue_id" id="venue_id" value="<?php echo $this->_var['venueId']; ?>">	
                <input type="hidden" name="secret" id="secret" value="<?php echo $this->_var['secret']; ?>">		
				<input type="hidden" name="action" id="action" value="saveOrder">
			</form>
			<div class="clear"></div>
			<div class="title_1">
				<div class="subtitle">
					<div class="subtitleic"></div>
					<div class="subtitletxt">预订说明</div>
				</div>
				<div class="ven-text">
					<?php echo $this->_var['detail']['feature']; ?>
				</div>
			</div>
		</div>
		</div>
        <script>
        	var cart = {id: "<?php echo $this->_var['infoId']; ?>", total:0, amount:0, date:"<?php echo $this->_var['date']; ?>"};
        </script>
		<script src="/js/venues.js"></script>
        
        <?php echo $this->fetch('library/page_footer.lbi'); ?>
        
	</body>
</html>