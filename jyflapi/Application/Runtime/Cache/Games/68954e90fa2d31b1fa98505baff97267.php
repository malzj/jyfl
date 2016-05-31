<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">

    <title>新建公司</title>

    <!-- Bootstrap core CSS -->
    <link href="/jyflapi/Public/games/css/bootstrap.min.css" rel="stylesheet">
    <link href="/jyflapi/Public/games/css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="/jyflapi/Public/games/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />

    <!--right slidebar-->
    <link href="/jyflapi/Public/games/css/slidebars.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/jyflapi/Public/games/css/style.css" rel="stylesheet">
    <link href="/jyflapi/Public/games/css/style-responsive.css" rel="stylesheet" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="/jyflapi/Public/js/html5shiv.js"></script>
    <script src="/jyflapi/Public/js/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<section id="container" class="">
    <!--header start-->
    <header class="header white-bg">
    <div class="sidebar-toggle-box">
        <div data-original-title="左侧菜单" data-placement="right" class="fa fa-bars tooltips"></div>
    </div>
    <!--logo start-->
    <a href="index.html" class="logo" >vk<span>admin</span></a>
    <!--logo end-->
    <div class="nav notify-row" id="top_menu">
        <!--  notification start -->
        <ul class="nav top-menu">

        </ul>
    </div>
    <div class="top-nav ">
        <ul class="nav pull-right top-menu">

            <!-- user login dropdown start-->
            <!--<li class="dropdown">-->
                <!--<a data-toggle="dropdown" class="dropdown-toggle" href="#">-->
                    <!--<img alt="" src="img/avatar1_small.jpg">-->
                    <!--<span class="username">风华</span>-->
                    <!--<b class="caret"></b>-->
                <!--</a>-->
                <!--<ul class="dropdown-menu extended logout">-->
                    <!--<div class="log-arrow-up"></div>-->
                    <!--<li><a href="#"><i class=" fa fa-suitcase"></i>个人资料</a></li>-->
                    <!--<li><a href="#"><i class="fa fa-cog"></i> 设置</a></li>-->
                    <!--<li><a href="#"><i class="fa fa-bell-o"></i> 通知</a></li>-->
                    <!--<li><a href="login.html"><i class="fa fa-key"></i> 安全退出</a></li>-->
                <!--</ul>-->
            <!--</li>-->

            <!-- user login dropdown end -->
            <li class="sb-toggle-right" style="display: none;">
                <i class="fa  fa-align-right"></i>
            </li>
        </ul>
    </div>
</header>

    <!--header end-->
    <!--sidebar start-->
    <aside>
    <div id="sidebar"  class="nav-collapse ">
        <!-- sidebar menu start-->
        <ul class="sidebar-menu" id="nav-accordion">


            <!--multi level menu start-->
            <li class="sub-menu">
                <a href="javascript:;" class="dcjq-parent active">
                    <i class="fa fa-sitemap "></i>
                    <span>活动</span>
                </a>
                <!--<ul class="sub">-->

                    <!--<li><a id="a1" class="activeColor" href="<?php echo U('User/userlist');?>">用户列表</a></li>-->

                <!--</ul>-->
                <ul class="sub">

                    <li><a id="a2" class="activeColor" href="<?php echo U('Company/companyList');?>">公司列表</a></li>

                </ul>
                <ul class="sub">

                    <li><a class="activeColor" href="<?php echo U('Games/gameList');?>">游戏列表</a></li>

                </ul>
                <ul class="sub">

                    <li><a id="a3" class="activeColor" href="<?php echo U('Grade/gradeList');?>">等级列表</a></li>

                </ul>

            </li>
            <!--multi level menu end-->

        </ul>
        <!-- sidebar menu end-->
    </div>
</aside>
<script src="/jyflapi/Public/games/js/jquery.js"></script>

    <!--sidebar end-->
    <!--main content start-->
    <section id="main-content">
        <section class="wrapper site-min-height" >
            <section class="panel">
                <header class="panel-heading">
                    编辑公司
                </header>
                <!-- page start-->
                <div class="panel-body">
                    <form class="form-horizontal tasi-form" id="sv" enctype="multipart/form-data"  method="post" action="<?php echo U('Company/companyEdit');?>">
                        <input name="id" value="<?php echo ($company_info['id']); ?>" type="hidden" />
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-2 control-label">名称</label>
                            <div class="col-sm-10">
                                <input type="text" name="company_name" class="form-control" value="<?php echo ($company_info['company_name']); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-2 control-label">等级</label>
                            <div class="col-sm-10">
                                <select name="grade_id" class="form-control">
                                    <option value="">请选择等级</option>
                                    <?php if(is_array($grade_list)): $i = 0; $__LIST__ = $grade_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($val["id"]); ?>" <?php if($val['id']==$company_info['grade_id']) echo 'selected="selected"'?>><?php echo ($val["grade_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-2 control-label">logo</label>
                            <div class="col-sm-10">
                                <input type="file" name="logo" />
                                <img src="/jyflapi/Public/company/upload/<?php echo ($company_info['logo_img']); ?>" width="200" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-2 control-label">背景图片</label>
                            <div class="col-sm-10">
                                <input type="file" name="background" />
                                <img src="/jyflapi/Public/company/upload/<?php echo ($company_info['back_img']); ?>" width="200" />
                            </div>
                        </div>

                        <button type="button" id="s" class="btn btn-info">提交</button>
                        <a href="javascript:history.go(-1);" class="btn btn-danger">取消</a>

                    </form>

                </div>



            </section>

            <!-- page end-->
        </section>
    </section>
    <!--main content end-->

    <!-- Right Slidebar start -->
    <div class="sb-slidebar sb-right sb-style-overlay">


    </div>
    <!-- Right Slidebar end -->

    <!--footer start-->
    <footer class="site-footer">
        <div class="text-center">
            2014 &copy; vkadmin by Kairos.
            <a href="#" class="go-top">
                <i class="fa fa-angle-up"></i>
            </a>
        </div>
    </footer>
    <!--footer end-->
</section>




</form>
</div>
<script src="/jyflapi/Public/js/jquery.js"></script>
<script src="/jyflapi/Public/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="/jyflapi/Public/js/jquery.dcjqaccordion.2.7.js"></script>
<script src="/jyflapi/Public/js/jquery.scrollTo.min.js"></script>
<script src="/jyflapi/Public/js/slidebars.min.js"></script>
<script src="/jyflapi/Public/js/jquery.nicescroll.js" type="text/javascript"></script>
<script src="/jyflapi/Public/js/respond.min.js" ></script>

<!--common script for all pages-->
<script src="/jyflapi/Public/js/common-scripts.js"></script>
<script type="text/javascript" src="/jyflapi/Public/assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/jyflapi/Public/assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<!--common script for all pages-->
<!--<script src="/jyflapi/Public/js/common-scripts.js"></script>-->
<script type="text/javascript">
    function time(){

        var s='<?php echo ($btime); ?>';
        var c='<?php echo ($otime); ?>';
        alert(s);
        $('#btime').val(s);
        $('#otime').val(c);
    }

    function getOptions(){
        var opt = {callback: pageselectCallback};

        var s='<?php echo ($btime); ?>';
        var c='<?php echo ($otime); ?>';

        $('#btime').val(s);
        $('#otime').val(c);
        opt['items_per_page']=10;
        opt['num_display_entries']=4;
        opt['num_edge_entries']=4;
        opt['prev_text']='上一页';
        opt['next_text']='下一页';
        opt['current_page']='<?php echo ($nowpage); ?>'-1;
        return opt;
    }
    $(function(){
//        console.log(<?php echo ($nowpage); ?>);
//        alert(<?php echo ($count); ?>);
        // 分页控件初始化
        var optInit = getOptions();
        $("#Pagination").pagination(<?php echo ($count); ?>, optInit);
    })
    var flag = false;
    function pageselectCallback(page_index){
        console.log(page_index);
        if(flag){
//            *//**//*请求数据*//**//*
//            //请求API
            var page =parseInt(page_index);
            var a='<?php echo ($btime); ?>';
            var b='<?php echo ($otime); ?>';
            location.href="/jyflapi/index.php/Games/Company/ylist/nowpage/"+page+"/btime/"+a+"/otime/"+b+"";

        }
        flag = true;
    }

</script>


<script>
    (function($){
        $.fn.datetimepicker.dates['zh-CN'] = {
            days: ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日"],
            daysShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六", "周日"],
            daysMin: ["日", "一", "二", "三", "四", "五", "六", "日"],
            months: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
            monthsShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],
            today: "今日",
            suffix: [],
            meridiem: ["上午", "下午"],
            format: "yyyy-mm-dd" /*控制显示格式,默认为空，显示小时分钟*/
        };
    }(jQuery));
    $(function(){





        $(".form_datetime").datetimepicker({
            language: 'zh-CN',
//            language: 'cn',
            format: 'yyyy-mm-dd',
//            autoclose: true,
//            todayBtn: true,
//            pickerPosition: "bottom-left"
            weekStart: 1,
            todayBtn: 1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0

        });

    })
</script>
<script>
    $(function(){
//        $("#otime").blur(function(){
//            var text;
//            var startDate=$('#btime').val();
//            var overDate=$('#otime').val();
//
////            alert(startDate);
////            alert(overDate);
//            if(startDate>overDate){
//                text="<font color='#FF0000'>结束时间需要大于开始时间！</font>"
//                $("#overDateemess").html(text);
//            }else{
//                $("#overDateemess").text("");
//            }
//        })
        $('#s').click(function(){

            var text;
            var startDate=$('#btime').val();
            var overDate=$('#otime').val();

//            alert(startDate);
//            alert(overDate);
            if(startDate>overDate){
                text="<font color='#FF0000'>结束时间需要大于开始时间！</font>"
                $("#overDateemess").html(text);
            }else{
                $("#overDateemess").text("");
                $('#sv').submit();
            }
        });
    });
</script>
</body>
</html>