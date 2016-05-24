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
                    编辑等级
                </header>
                <!-- page start-->
                <div class="panel-body">
                    <!--<form class="form-horizontal tasi-form" id="sv" enctype="multipart/form-data"  method="post" action="/jyflapi/index.php/Games/Grade/gradeAdd">-->
                    <form class="form-horizontal tasi-form" id="sv" enctype="multipart/form-data"  method="post" action="<?php echo U('Grade/gradeUpdate');?>">
                        <input name="id" type="hidden" value="<?php echo ($grade_info['id']); ?>" />
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-2 control-label">名称</label>
                            <div class="col-sm-10">
                                <input type="text" name="grade_name" class="form-control" value="<?php echo ($grade_info['grade_name']); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-2 control-label">备注</label>
                            <div class="col-sm-10">
                                <input type="text" name="remark" class="form-control" value="<?php echo ($grade_info['remark']); ?>">
                            </div>
                        </div>
                        <button type="submit" id="s" class="btn btn-info">提交</button>
                        <a href="<?php echo U('Grade/gradeList');?>" class="btn btn-danger">取消</a>

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


<!--<div class="result page"><?php echo ($page); ?></div>-->





<script src="/jyflapi/Public/games/js/jquery.js"></script>
<script src="/jyflapi/Public/games/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="/jyflapi/Public/games/js/jquery.dcjqaccordion.2.7.js"></script>
<script src="/jyflapi/Public/games/js/jquery.scrollTo.min.js"></script>
<script src="/jyflapi/Public/games/js/slidebars.min.js"></script>
<script src="/jyflapi/Public/games/js/jquery.nicescroll.js" type="text/javascript"></script>
<script src="/jyflapi/Public/games/js/respond.min.js" ></script>

<!--common script for all pages-->
<script src="/jyflapi/Public/games/js/common-scripts.js"></script>


</body>
</html>