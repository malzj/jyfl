/**
 * Created by chao on 2016/7/19.
 */
(function($){//点击跳转
    $('body').on('tap','.click_btn',function(event){
        event.stopPropagation();
        var url = this.getAttribute('data-href');
        window.location.href=url;
    })
})(mui);