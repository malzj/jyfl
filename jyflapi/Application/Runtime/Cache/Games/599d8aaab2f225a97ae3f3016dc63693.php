<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">

    <title>编辑公司</title>

    <!-- Bootstrap core CSS -->
    <link href="/jyflapi/Public/weixinapp/css/bootstrap.min.css" rel="stylesheet">
    <link href="/jyflapi/Public/weixinapp/css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="/jyflapi/Public/weixinapp/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />

    <!--right slidebar-->
    <link href="/jyflapi/Public/weixinapp/css/slidebars.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/jyflapi/Public/weixinapp/css/style.css" rel="stylesheet">
    <link href="/jyflapi/Public/weixinapp/css/style-responsive.css" rel="stylesheet" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="/jyflapi/Public/js/html5shiv.js"></script>
    <script src="/jyflapi/Public/js/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<section id="container" class="">
    <!--header start-->
    
    <!--header end-->
    <!--sidebar start-->
    
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
                    <form class="form-horizontal tasi-form" id="sv" enctype="multipart/form-data"  method="post" action="/jyflapi/index.php/Games/Company/companyupdate">
                        <div class="form-group">
                            <input type="hidden" name="id" value="<?php echo ($business["id"]); ?>">
                            <label class="col-sm-2 col-sm-2 control-label">名称</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" value="<?php echo ($business["name"]); ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 col-sm-2 control-label">备注</label>
                            <div class="col-sm-10">
                                <input type="text" name="msg" class="form-control" value="<?php echo ($business["msg"]); ?>">
                            </div>
                        </div>
                        <button type="submit" id="s" class="btn btn-info">提交</button>
                        <a href="<?php echo U('company/companylist');?>" class="btn btn-danger">取消</a>

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





<script src="/jyflapi/Public/weixinapp/js/jquery.js"></script>
<script src="/jyflapi/Public/weixinapp/js/bootstrap.min.js"></script>
<script class="include" type="text/javascript" src="/jyflapi/Public/weixinapp/js/jquery.dcjqaccordion.2.7.js"></script>
<script src="/jyflapi/Public/weixinapp/js/jquery.scrollTo.min.js"></script>
<script src="/jyflapi/Public/weixinapp/js/slidebars.min.js"></script>
<script src="/jyflapi/Public/weixinapp/js/jquery.nicescroll.js" type="text/javascript"></script>
<script src="/jyflapi/Public/weixinapp/js/respond.min.js" ></script>

<!--common script for all pages-->
<script src="/jyflapi/Public/weixinapp/js/common-scripts.js"></script>


</body>
</html>