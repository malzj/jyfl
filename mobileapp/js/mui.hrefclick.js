/**
 * Created by chao on 2016/7/19.
 */
(function($){
    //点击跳转
    $('body').on('tap','.href_click',function(e){
        var url = this.getAttribute('data-href');
        if (!this.classList.contains('mui-disabled')) {
            e.stopPropagation();
            $.openWindow({
                url:url
            });
        }else {
            e.preventDefault();
            e.stopPropagation();
        }
    });
})(mui);