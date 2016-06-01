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
         
        
        <div class="tg_n" style="margin-top: 90px;padding: 10px 0;">
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
				</div>
			</div>
			<div class="img">
				<div class="img_left">
                    <img src="<?php if ($this->_var['imgList']['0']): ?><?php echo $this->_var['imgList']['0']; ?><?php else: ?>/images/dongwang/null.jpg<?php endif; ?>" width="464" height="360" />
                </div>
                <ul class="img_right">
                    <li><img src="<?php if ($this->_var['imgList']['3']): ?><?php echo $this->_var['imgList']['3']; ?><?php else: ?>/images/dongwang/null.jpg<?php endif; ?>" width="235" height="175"></li>
                    <li><img src="<?php if ($this->_var['imgList']['2']): ?><?php echo $this->_var['imgList']['2']; ?><?php else: ?>/images/dongwang/null.jpg<?php endif; ?>" width="235" height="175"></li>
                    <li><img src="<?php if ($this->_var['imgList']['1']): ?><?php echo $this->_var['imgList']['1']; ?><?php else: ?>/images/dongwang/null.jpg<?php endif; ?>" width="235" height="175"></li>
                    <li><img src="<?php if ($this->_var['imgList']['6']): ?><?php echo $this->_var['imgList']['6']; ?><?php else: ?>/images/dongwang/null.jpg<?php endif; ?>" width="235" height="175"></li>
                    <li><img src="<?php if ($this->_var['imgList']['5']): ?><?php echo $this->_var['imgList']['5']; ?><?php else: ?>/images/dongwang/null.jpg<?php endif; ?>" width="235" height="175"></li>
                    <li><img src="<?php if ($this->_var['imgList']['4']): ?><?php echo $this->_var['imgList']['4']; ?><?php else: ?>/images/dongwang/null.jpg<?php endif; ?>" width="235" height="175"></li>	
                </ul>
			</div>
			
			<div class="tab">
                <ul>
                <?php if ($this->_var['venues']): ?>
                    <li class="active" data-tab='week'><?php echo $this->_var['detail']['sportName']; ?></li>
                <?php endif; ?>
                <?php if ($this->_var['ticket']): ?>
                    <li data-tab='tickets' <?php if (! $this->_var['venues']): ?> class="active" <?php endif; ?>>门票</li>
                <?php endif; ?>
                </ul>
                <?php if ($this->_var['venues']): ?>
                <div class="week">
                    <?php $_from = $this->_var['venues']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'venue');if (count($_from)):
    foreach ($_from AS $this->_var['venue']):
?>
                        <a href="venues_show.php?venueId=<?php echo $this->_var['venueId']; ?>&infoId=<?php echo $this->_var['venue']['infoId']; ?>&orderDate=<?php echo $this->_var['venue']['date']; ?>">
                            <p><span class="day"><?php echo $this->_var['venue']['week']; ?></span><span class="date"><?php echo $this->_var['venue']['date_mt']; ?></span></p>
                            <p><span class="money"><?php echo $this->_var['venue']['salePrice']; ?></span><span>点起</span></p>
                            <!--<p><span class="num">剩余 </span><span><?php echo $this->_var['venue']['venueNum']; ?></span></p>-->
                        </a>
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </div>
                <?php endif; ?>
                <?php if ($this->_var['ticket']): ?>
                <div class="tickets" <?php if ($this->_var['venues']): ?> style="display:none"<?php endif; ?>>
                      <table>
                      <?php $_from = $this->_var['ticket']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'tic');if (count($_from)):
    foreach ($_from AS $this->_var['tic']):
?>
                        <tr class="table_1 border_down">
                            <td class="ticket_span1"><a href="ticket_show.php?productno=<?php echo $this->_var['tic']['infoId']; ?>"><?php echo $this->_var['tic']['infoTitle']; ?></a></td>
                            <td class="ticket_span2"><span class="font-18"><?php echo $this->_var['tic']['salePrice']; ?></span><span class="font-12">点</span></td>
                            <td class="ticket_span3"><a href="ticket_show.php?productno=<?php echo $this->_var['tic']['infoId']; ?>">立即预订</a></td>
                        </tr>   
                       <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>               
                      </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="help">
                <div style="border-bottom: 2px solid #fda501;">
                    <span class='active xinxi'>场馆信息</span>
                </div>
                <p><?php echo $this->_var['detail']['feature']; ?></p>
            </div>
		</div>
        
         
	 	<?php echo $this->fetch('library/page_footer.lbi'); ?>
	    
        
		<?php echo $this->fetch('library/page_left.lbi'); ?>
                
        
		<?php echo $this->fetch('library/page_right.lbi'); ?>
        
        
        <script type="text/javascript">
		$(function(){
			$('.tab ul li').click(function(){
				var _that = $(this);
				$(this).addClass('active').siblings().removeClass('active');
				var thatClick = $(this).attr('data-tab');
				var tabs = ['tickets', 'week'];
				for(var i=0; i<tabs.length; i++){
					if(tabs[i] == thatClick){
						_that.parents('ul').siblings('.'+tabs[i]).show();
					}else{
						_that.parents('ul').siblings('.'+tabs[i]).hide();
					}
				}	
			})	
		})
		</script>
        
    </body>
</html>