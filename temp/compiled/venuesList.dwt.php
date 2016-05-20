
<!DOCTYPE html>
<html>
	<head>
<meta name="Generator" content="ECSHOP v2.7.3" />
		<meta charset="UTF-8">
		<title></title>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.min.js"></script>
		<script src="<?php echo $this->_var['app_path']; ?>js/juyoufuli/jquery.SuperSlide.2.1.1.source.js"></script>
        <link rel="stylesheet" href="<?php echo $this->_var['app_path']; ?>css/juyoufuli/sport1.css">
	</head>
	<body>
        
        <?php echo $this->fetch('library/page_top.lbi'); ?>
                
        
        <div class="tg_sx" style="border-top:none;margin-top: 90px;">
            <div class="tg_area">
                <div class="tg_areaheadline"><strong>项目</strong></div>
                <ul>
                	<li><a href="venues.php?area=<?php echo $this->_var['areaId']; ?>&page=<?php echo $this->_var['page']; ?>" <?php if (! $this->_var['typeId']): ?> class="active" <?php endif; ?>>全部</a></li>
                    <?php $_from = $this->_var['venues_type']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'types');if (count($_from)):
    foreach ($_from AS $this->_var['types']):
?>
                    <li><a href="venues.php?type=<?php echo $this->_var['types']['code']; ?>&area=<?php echo $this->_var['areaId']; ?>&page=<?php echo $this->_var['page']; ?>" <?php if ($this->_var['types']['active'] == 1): ?>class="active" <?php endif; ?>><?php echo $this->_var['types']['name']; ?></a></li>           
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>                   
                </ul>
            </div>
            <div class="tg_area">

                <div class="tg_areaheadline"><strong>区域</strong></div>
                <ul>
                    <li><a href="venues.php?type=<?php echo $this->_var['typeId']; ?>&page=<?php echo $this->_var['page']; ?>" <?php if (! $this->_var['areaId']): ?> class="active" <?php endif; ?>>全部</a></li>
                    <?php $_from = $this->_var['area_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'alist');if (count($_from)):
    foreach ($_from AS $this->_var['alist']):
?>
                    <li><a href="venues.php?type=<?php echo $this->_var['typeId']; ?>&area=<?php echo $this->_var['alist']['dongwang_id']; ?>&page=<?php echo $this->_var['page']; ?>" <?php if ($this->_var['alist']['active'] == 1): ?>class="active" <?php endif; ?>><?php echo $this->_var['alist']['region_name']; ?></a></li>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>        
                  
                </ul>
            </div>
        </div>
        
        <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'row');if (count($_from)):
    foreach ($_from AS $this->_var['row']):
?>
        <div class="bg" style="background: rgb(242, 242, 242);">
            <div class="list">
                <div class="list-img">
                    <a href="venues_detail.php?venueId=1602" target="_blank"><img src="<?php echo $this->_var['row']['venue']['signImg']; ?>" width="210" height="210"></a>
                </div>
                <div class="list-title">
                    <h1><a href="venues_detail.php?venueId=<?php echo $this->_var['row']['venue']['venueId']; ?>" target="_blank"><?php echo $this->_var['row']['venue']['venueName']; ?></a></h1>
                    <ul class="sport1_li_margin">
                        <li class="adress">详细地址：<?php echo $this->_var['row']['venue']['place']; ?></li>
                        <li>联系电话：<b>00-662-5170</b><?php if ($this->_var['row']['venue']['tel400']): ?> 转 <span style="color: #ff610b;"><?php echo $this->_var['row']['venue']['tel400']; ?></span><?php endif; ?></li>
                        <li>场馆标签： 羽毛球</li>
                        <li>
                            <div class="list-tab">
                            <?php if ($this->_var['row']['venueSite']): ?>
                                <span class="cur" data-tab='week'><?php echo $this->_var['row']['venue']['sportName']; ?></span>
                            <?php endif; ?>
                            <?php if ($this->_var['row']['venueTicket']): ?>
                                <span data-tab="tickets" <?php if (! $this->_var['row']['venueSite']): ?> class="cur"<?php endif; ?>>门票</span>
                            <?php endif; ?>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="price">
                    <div class="price_1">
                        <span><em><?php echo $this->_var['row']['venue']['salePrice']; ?></em>点起</span>
                    </div>
                    <div class="price_2">
                        <span><a href="venues_detail.php?venueId=<?php echo $this->_var['row']['venue']['venueId']; ?>">查看场馆</a></span>
                    </div>
                </div>
                <div style="clear: both;"></div>

                <div class="week">
                   <?php $_from = $this->_var['row']['venueSite']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'site');if (count($_from)):
    foreach ($_from AS $this->_var['site']):
?>
                    <a href="venues_show.php?venueId=<?php echo $this->_var['row']['venue']['venueId']; ?>&orderDate=<?php echo $this->_var['site']['date']; ?>&infoId=<?php echo $this->_var['site']['infoId']; ?>">
                        <p><span class="day"><?php echo $this->_var['site']['week']; ?></span><span class="date"><?php echo $this->_var['site']['date_mt']; ?></span></p>
                        <p><span class="money"><?php echo $this->_var['site']['salePrice']; ?></span><span>点起</span></p>
                    </a>  
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?> 
                </div>

				
                <?php if ($this->_var['row']['venueTicket']): ?>
                <div class="tickets" <?php if ($this->_var['row']['venueSite']): ?> style="display:none;" <?php endif; ?>>
                    <table>
                        <tbody>
                            <?php $_from = $this->_var['row']['venueTicket']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'ticket');if (count($_from)):
    foreach ($_from AS $this->_var['ticket']):
?>
                            <tr class="table_1">
                                <td class="ticket_span1"><a href="ticket_show.php?productno=<?php echo $this->_var['ticket']['infoId']; ?>"><?php echo $this->_var['ticket']['infoTitle']; ?></a></td>
                                <td class="ticket_span2"><span class="font-18"><?php echo $this->_var['ticket']['salePrice']; ?></span><span class="font-12">点</span></td>
                                <td class="ticket_span3"><a href="ticket_show.php?productno=<?php echo $this->_var['ticket']['infoId']; ?>">立即预订</a></td>
                            </tr>     
                        	<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>   
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div> 
        <?php endforeach; else: ?>
        <div class="bg">
            <div class="list">
            	<center style="height:100px; line-height:100px; color:red;">暂时没有场馆信息</center>
            </div>
        </div>
        <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
        
        <div class="clear"></div>
		<div class="flickr" style="text-align:center;">
			<?php echo $this->fetch('library/pages.lbi'); ?>
		</div>
		<div class="clear"></div>
        
            
        
	 	<?php echo $this->fetch('library/page_footer.lbi'); ?>
	    
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
        
        <script>
			$(function(){
				$('.list-tab span').click(function(){
					var _that = $(this);
					$(this).addClass('cur').siblings().removeClass('cur');
					var thatClick = $(this).attr('data-tab');
					var tabs = ['tickets', 'week'];
					for(var i=0; i<tabs.length; i++){
						if(tabs[i] == thatClick){
							_that.closest('.list-title').siblings('.'+tabs[i]).show();
						}else{
							_that.closest('.list-title').siblings('.'+tabs[i]).hide();
						}
					}	
				})
				
				$('.bg').mouseover(function(){
					$(this).css('background','#f2f2f2');
				})
				$('.bg').mouseout(function(){
					$(this).css('background','transparent');
				})
				$('.tg_area ul li a').click(function(){
					$(this).addClass('active').parents('li').siblings().children().removeClass('active');
				})
			})
		</script>
    </body>
</html>