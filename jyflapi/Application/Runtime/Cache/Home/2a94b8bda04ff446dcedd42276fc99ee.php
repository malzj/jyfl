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
<a id="get_game" data-id="1">获取游戏详情</a>

<form name="buy">
    <div>游戏id<input type="hidden" id="game_id" value="12"/></div>
    <div>卡号<input type="hidden" id="card_num" value="7110"/></div>
    <div>公司id<input type="hidden" id="company_id" value="1"/></div>
    <div>购买数量<input type="text" id="num" /></div>
    <div>卡密<input type="text" id="card_pass" /></div>
    <a id="buy_btn">购买</a>
</form>
<?php
$sd = 526; $time = date('His',time()); $total = 20; echo $time.'<br>'; $pro = intval($sd)*intval($time); $tot_len = strlen($total); echo $pro.'<br>'; $cut = substr($pro,-$tot_len); echo $cut.'<br>'; $contrast = intval($total)-intval($cut); echo $contrast; if($contrast>0){ $winner = 10000000+$contrast; }else{ $cut = substr($cut,1-$tot_len); $winner = 10000000+intval($cut); } dump($winner); ?>
<script type="text/javascript">
    $(function(){
        $('#get_game').click(function(){
            var game_id = $(this).attr('data-id');
            $.ajax({
                type:"post",
                url:'http://jy.com/jyflapi/index.php/Games/GamesApi/getGame.html',
                data:{game_id:game_id},
                dataType:'json',
                success:function(data){

                    console.log(data);
                },
                error:function(XMLHttpRequest, textStatus, errorThrown){
                    alert(XMLHttpRequest.status);
                    alert(XMLHttpRequest.readyState);
                    alert(textStatus);
                }
            })
        });
    });
    $(function(){
        $('#buy_btn').click(function(){
            var game_id = $('#game_id').val();
            var company_id = $('#company_id').val();
            var card_num = $('#card_num').val();
            var num = $('#num').val();
            var card_pass = $('#card_pass').val();
            $.ajax({
                type:"post",
                url:'http://jy.com/jyflapi/index.php/Games/GamesApi/purchase',
                data:{
                    game_id:game_id,
                    card_num:card_num,
                    company_id:company_id,
                    num:num,
                    card_pass:card_pass,
                },
                dataType:'json',
                success:function(data){

                    console.log(data);
                },
                error:function(XMLHttpRequest, textStatus, errorThrown){
                    alert(XMLHttpRequest.status);
                    alert(XMLHttpRequest.readyState);
                    alert(textStatus);
                }
            })
        });
    });
</script>
</body>
</html>