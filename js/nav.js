$(function(){		   
	$("#navul > li").not(".navhome").hover(function(){
		$(this).addClass("navmoon")
	},function(){
		$(this).removeClass("navmoon")
	});
	
}); 


(function($){
    $.fn.capacityFixed = function(options) {
        var opts = $.extend({},$.fn.capacityFixed.deflunt,options);
        var FixedFun = function(element) {
            var top = opts.top;
            element.css({
                "top":top
            });
            $(window).scroll(function() {
                var scrolls = $(this).scrollTop();
                if (scrolls > top) {

                    if (window.XMLHttpRequest) {
                        element.css({
                            position: "fixed",
                            top: 0							
                        });
                    } else {
                        element.css({
                            top: scrolls
                        });
                    }
                }else {
                    element.css({
                        position: "absolute",
                        top: top
                    });
                }
            });
            element.find(".close-ico").click(function(event){
                element.remove();
                event.preventDefault();
            })
        };
        return $(this).each(function() {
            FixedFun($(this));
        });
    };
    $.fn.capacityFixed.deflunt={
		right : 0,
        top:0
	};
})(jQuery);



$(document).ready(function(){ 
  $('li.mainlevel_1').mouseover(function(){
    $(this).children('a').addClass('a_highlight');
$(this).find('ul').stop(true,true).delay(100).slideDown();
  });
  $('li.mainlevel_1').mouseout(function(){
$(this).find('ul').stop(true,true).delay(100).slideUp();
$(this).children('a').removeClass('a_highlight');
  });
  
});