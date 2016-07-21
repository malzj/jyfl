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
        /**
         * 排期列表渲染
         * @param cinemaid  影院id
         * @param movieid   电影id
         * @param times     时间列表
         * @returns {string}
         */
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
                    '<div class="mui-col-xs-3 mui-text-center">';
                if(pv.is_cut==1){
                    list += '<button class="btn_ticket" type="button">已过场</button></div></div>'
                }else {
                    list += '<button class="btn_ticket click_btn" data-href="./movie_seat.html?cinemaid=' + pv.cinemaId + '&movieid=' + pv.movieId + '&hallno=' + pv.hallNo + '&planid=' + pv.planId + '" type="button">选座购票</button></div></div>'
                }
            });
            return list;
        },
        /**
         * 电子券渲染
         * @param data  请求返回的所有数据数据
         * @returns {string}
         */
        dzqshow:function(data){
            var html = '<form id="dzqform" onclick="return false;"><div class="mui-row bg_white">';
            var firstpirce,firstticketno;
            var i=0;
            $.each(data.cinemaDzq,function(key,value){
                if(i==0){
                    firstpirce=value.SalePrice;
                    firstticketno=value.TicketNo;
                }
                html+='<div class="mui-col-xs-12">' +
                    '<span class="dianziquan '+(i==0?'active':'')+'" data-price="'+value.SalePrice+'" data-ticketno="'+value.TicketNo+'">'+value.TicketName+'('+value.SalePrice+'点)</span>' +
                    '</div>'
                i++;
            });
            html+= '<div class="mui-numbox" data-numbox-min="1">' +
                '<button class="mui-btn-numbox-minus" type="button">-</button>' +
                '<input class="mui-input-numbox" name="number" type="number" />' +
                '<button class="mui-btn-numbox-plus" type="button">+</button>' +
                '</div></div><div class="mui-input-row margin_top_10 bg_white"><label>手机号：</label>' +
                '<input type="hidden" name="areaNo" value="'+data.cinemaDetail.dzq_area_id+'" />' +
                '<input type="hidden" name="areaName" value="'+data.cinemaDetail.area_name+'" />' +
                '<input type="hidden" name="cinemaNo" value="'+data.cinemaDetail.dzq_cinema_id+'" />' +
                '<input type="hidden" name="cinemaName" value="'+data.cinemaDetail.cinema_name+'" />' +
                '<input type="hidden" name="ticketNo" id="ticketNo" value="'+firstticketno+'" />' +
                '<input type="hidden" name="price" id="price" value="'+firstpirce+'" />'+
                '<input name="mobile" type="text" placeholder="请输入手机号" />' +
                '</div>' +
                '<button type="button" class="mui-btn btn_next">下一步</button></form>';
            return html;
        }
    })
})(jQuery);