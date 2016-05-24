<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">

    <title>微宝</title>

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
            <div class="row">
                <div class="col-sm-12">
                    <section class="panel">
                        <!-- page start-->
                        <header class="panel-heading">
                            公司列表
                        </header>
                        <div class="panel-body">
                            <table class="table table-striped" cellpadding=3 cellspacing=5>
                                <thead>
                                <tr>
                                    <!--<th>#</th>-->
                                    <th>id</th>
                                    <th>名称</th>
                                    <th>等级</th>
                                    <th>操作</th>

                                </tr>
                                </thead>
                                <?php if(is_array($company_list)): $i = 0; $__LIST__ = $company_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                        <td><?php echo ($vo["id"]); ?></td>
                                        <td><?php echo ($vo["company_name"]); ?></td>
                                        <td><?php echo ($vo["grade_name"]); ?></td>
                                        <!--<td> <a  href="<?php echo U('redenvelopelist/create','actId='.$vo['id']);?>"><button class="btn btn-primary btn-xs">-->
                                            <!--奖品包</button></a>-->
                                        <!--<td> <a  href="<?php echo U('rulelist/create','actId='.$vo['id']);?>"><button class="btn btn-primary btn-xs">-->
                                            <!--规则</button></a>-->
                                        <td> <a  href="<?php echo U('company/companyshow','id='.$vo['id']);?>"><button class="btn btn-primary btn-xs">
                                            <i class="fa fa-pencil"></i></button></a>
                                            <a href="<?php echo U('company/companydelete','id='.$vo['id']);?>" onclick="return confirm('确定将此记录删除?')">
                                                <button class="btn btn-danger btn-xs"><i class="fa fa-trash-o "></i></button></a>
                                        </td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                <tr>
                                </tr>

                            </table>
                            <div class="dataTables_paginate paging_bootstrap pagination"><?php echo ($pages); ?></div>
                        </div>
                    </section>
                </div></div>
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
<body>

</body>
</html>