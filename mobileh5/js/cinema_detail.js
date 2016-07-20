/**
 * Created by chao on 2016/7/19.
 */
(function($){
    $.extend({
        /**
         * 电影标题渲染
         * @param img   电影背景图片
         * @param name  名称
         * @param score 评分
         */
        movieTitle:function(img,name,score){
            $('#item1 .movie_select').css('background-image', 'url(' + img + ')');
            $('#item1 .movie_title').html('<div class="mui-text-center"><span>' + name + '</span></div>' +
                '<div class="mui-text-center">星星评分</div>');
        },
        timeList:function(cinemaid,movieid,times){
            var list='';
            jQuery.each(times, function (tk, tv) {
                list += '<a class="mui-control-item movie_paiqi" data-cinemaid="' + cinemaid + '" data-movieid="' + movieid + '" data-current="' + tv.strtotime + '">' + tv.strtotime + '</a>';
            });
            return list;
        },
        /**
         * 放映厅列表渲染函数
         * @param data  放映厅列表数据
         * @returns {string}
         */
        planList:function(data){
            var list='';
            $.each(data, function (pk, pv) {
                list += '<div class="mui-row movie_list">' +
                    '<div class="mui-col-xs-3 mui-text-center movie_time">' + pv.time + '</div>' +
                    '<div class="mui-col-xs-4 movie_type">' +
                    '<div>' + pv.language + pv.screenType + '</div>' +
                    '<p>' + pv.hallName + '</p>' +
                    '</div><div class="mui-col-xs-2 mui-text-center movie_price">' + pv.price + '</div>' +
                    '<div class="mui-col-xs-3 mui-text-center">' +
                    '<button class="btn_ticket click_btn" data-href="./movie_seat.html?cinemaid='+pv.cinemaId+'&movieid='+pv.movieId+'&hallno='+pv.hallNo+'&planid='+pv.planId+'" type="button">选座购票</button></div></div>'
            });
            return list;
        }
    })
})(jQuery);