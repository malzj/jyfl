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
<link href="/css/index.css" rel="stylesheet" type="text/css" />
<link href="/css/style.css" rel="stylesheet" type="text/css" />
<link href="/css/css.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script type="text/javascript"  src="/js/nav.js"></script>
<style>
img{display:inline-block;}
</style>
<script>
(function($){
    $.fn.hoverDelay = function(options){
        var defaults = {
            hoverDuring: 200,
            outDuring: 200,
            hoverEvent: function(){
                $.noop();
            },
            outEvent: function(){
                $.noop();    
            }
        };
        var sets = $.extend(defaults,options || {});
        var hoverTimer, outTimer;
        return $(this).each(function(){
            $(this).hover(function(){
                clearTimeout(outTimer);
                hoverTimer = setTimeout(sets.hoverEvent, sets.hoverDuring);
            },function(){
                clearTimeout(hoverTimer);
                outTimer = setTimeout(sets.outEvent, sets.outDuring);
            });    
        });
    }      
})(jQuery);
</script>
</head>
<body>

<?php echo $this->fetch('library/page_header.lbi'); ?>

<div style="position:relative;margin:0 auto; width:1200px; margin-top:10px;">
  
  <div id="carousel1" name="0">
	
    <?php echo $this->fetch('library/ad_position.lbi'); ?>
    
  </div>
  
  <div style="width:1200px;margin:0 auto; height:auto; position:relative; " class="outer">  
   <div style="width:1200px;">
  	<?php $_from = $this->_var['cenglist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'clist');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['clist']):
?>

     <div class='fl' id='ceng-<?php echo $this->_var['clist']['id']; ?>'>
	 
     <?php if ($this->_var['clist']['is_title'] == 1): ?>
     
<!--      <div style="margin:10px 0;" class="louceng">
     	<img src="/images/index/20151013135457.png" alt="" width="100%" height="auto"/>
     </div> -->
    
     
     <div class="wang-f1">
     	<div style="display:inline-block;float:left;"><img src="/images/index/tgzx.jpg" style="vertical-align: sub;"/></div>
        <div class="wang-option"><ul>
     	<?php $_from = $this->_var['clist']['child']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'childs');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['childs']):
        $this->_foreach['foo']['iteration']++;
?>
         <li <?php if (($this->_foreach['foo']['iteration'] - 1) == 0): ?> class="wang-ch1"<?php endif; ?> data-tp='<?php echo $this->_var['childs']['templates']; ?>' data-fu="<?php echo $this->_var['childs']['function']; ?>" data-ads="<?php echo $this->_var['childs']['ads']; ?>" data-fid="<?php echo $this->_var['childs']['fid']; ?>" data-height="<?php echo $this->_var['clist']['height']; ?>" data-size="<?php echo $this->_var['childs']['size']; ?>" data-show-title="<?php echo $this->_var['clist']['is_title']; ?>" style="display:none;"></li>
        <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
     </ul></div></div>
     <?php else: ?>
     
     
         <div class="wang-f1">
        <div style="display:inline-block;float:left;"><img src="/images/index/fasd_<?php echo $this->_var['clist']['id']; ?>.gif" width="21" height="22" style="vertical-align: sub;"/><span class="audio-visual"><?php echo $this->_var['clist']['name']; ?></span></div>
        <div class="wang-option">
          <ul>
          <?php $_from = $this->_var['clist']['child']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'childs');$this->_foreach['foo'] = array('total' => count($_from), 'iteration' => 0);
if ($this->_foreach['foo']['total'] > 0):
    foreach ($_from AS $this->_var['childs']):
        $this->_foreach['foo']['iteration']++;
?>
            <li <?php if (($this->_foreach['foo']['iteration'] - 1) == 0): ?> class="wang-ch1"<?php endif; ?> data-tp='<?php echo $this->_var['childs']['templates']; ?>' data-fu="<?php echo $this->_var['childs']['function']; ?>" data-ads="<?php echo $this->_var['childs']['ads']; ?>" data-fid="<?php echo $this->_var['childs']['fid']; ?>" data-height="<?php echo $this->_var['clist']['height']; ?>" data-size="<?php echo $this->_var['childs']['size']; ?>" data-show-title="<?php echo $this->_var['clist']['is_title']; ?>"><a target="_blank" href="<?php echo $this->_var['childs']['url']; ?>"><?php echo $this->_var['childs']['name']; ?></a></li>
          <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
          </ul>
        </div>
      </div>
      
     <?php endif; ?>
      <div class="wof" style="height:<?php echo $this->_var['clist']['height']; ?>;margin-bottom:20px;position: relative;">
       
      </div>
    </div>
<?php if ($this->_var['clist']['id'] == $this->_var['clist']['position_id']['nav_id']): ?>
<div style="margin:10px 0;" id="louceng">
<a href="<?php echo $this->_var['clist']['position_id']['ad_link']; ?>">
<img src="/data/afficheimg/<?php echo $this->_var['clist']['position_id']['ad_code']; ?>" alt="<?php echo $this->_var['clist']['position_id']['ad_name']; ?>" width="<?php echo $this->_var['clist']['position_id']['ad_width']; ?>" height="<?php echo $this->_var['clist']['position_id']['ad_height']; ?>"/>
</a>
</div>
<?php endif; ?>    
    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </div>   
 </div>
 
 <div style="width:1200px; display:block; margin-left:-650px; position:fixed; left:50%; top:25%; height:0;" class="fixed-ceng">
  	<div class="wang-cong">
      <?php $_from = $this->_var['cenglist']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'leftposition');if (count($_from)):
    foreach ($_from AS $this->_var['leftposition']):
?>
      <div class="ceng n-ceng-<?php echo $this->_var['leftposition']['id']; ?>" data-miao="ceng-<?php echo $this->_var['leftposition']['id']; ?>"><img src="/images/index/fasd_<?php echo $this->_var['leftposition']['id']; ?>.gif" title="<?php echo $this->_var['leftposition']['name']; ?>"></div>   
      <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>   
    </div>        
  </div>
  
</div>
 

<div style=" clear:both;"></div>

<?php echo $this->fetch('library/page_footer.lbi'); ?>

<script type="text/javascript"  src="/js/carouse.js?v=20160128"></script>
<script>
$( document ).ready( function (){	
	
	$(".wang-option ul li").each(function(){
		category_ajax_index($(this));
	});
	
	$(".wang-option ul li").each(function(){
		if($(this).hasClass('wang-ch1')){
			loading_index($(this));
		}
	});
		
	// 经过加载数据
	function category_ajax_index(that){
		that.hoverDelay({           
			hoverEvent: function(){
				loading_index(that);
			}
		});
	}
	
	// 默认加载第一个
	function loading_index(that){
		
		// 改变选项卡样式
		that.siblings().removeClass('wang-ch1');
		that.addClass('wang-ch1'); 
		// 得到模板、方法、广告、id 
		var attr = ['data-tp','data-fu','data-ads','data-fid','data-size','data-show-title'];
		var str = '';
		$.each(attr, function(index,name){
			str += eval('name')+'='+that.attr(eval('name'))+'&';
		}); 
		// ajax 加载
		$.ajax({
			type:'get',
			url:'index.php?',
			data:'act=index_category&'+str,
			dataType:'json',
			cache: true,
			beforeSend:function(){
				var cheight = that.attr('data-height'); 
				that.closest('.wang-f1').next('.wof').html('<center><img src="/images/load.gif" style="position: absolute;top:15%;left:35%;"></center>');
			},
			success:function(data){
				that.closest('.wang-f1').next('.wof').css('border',0);
				that.closest('.wang-f1').next('.wof').html(data.content);
			}
		});		
	}	
	// 页面定位
	//$(".wang-cong").pin();
	// 描点跳转
	$('.ceng').click(function(){
		var miao = $(this).attr('data-miao');
		$("html,body").animate({scrollTop:$('#'+miao).offset().top-40},300)
	});
	// 锚点滚动定位
	$(window).scroll(function () {
		var cengList = [];
		$(".wang-cong .ceng").each(function(index,dom){cengList.push($(dom).attr('data-miao'));});
		$.each(cengList , function(i,x){
			var divTop = $("#"+x).offset().top;
			var scTop = $(window).scrollTop();				
			if(  divTop-scTop < 250 && divTop-scTop > 20){	
				$(".wang-cong .ceng").each(function(index,dom){
					 $(dom).removeClass('cut');
				});			
				$('.n-'+x).addClass('cut');
			}
		});	
	})	
});		
</script>

<script>
	$('#carousel1 .car_bigp a:first').attr({"href":"javascript:void(0);","target":"_self"});
</script>
</body>
</html>