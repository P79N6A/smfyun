<?php
$action = Request::instance()->action;
function isActive($need) {
  //当前页
  $action = Request::instance()->action;
  if ($action == $need) return 'active';

  if (is_array($need)) foreach ($need as $v) {
    if ($action == $v) return 'active';
  }
}
?>
<?php
function convert($a){
   switch ($a) {
     case 1:
       echo "qwthbba/home";
       break;
     case 2:
       echo "qwtwdba/home";
       break;
     case 3:
       echo "qwtwfba/home";
        break;
      case 4:
       echo "qwtrwba/home";
        break;
     default:
       echo "";
       break;
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <meta name="keywords" content="admin, dashboard, bootstrap, template, flat, modern, theme, responsive, fluid, retina, backend, html5, css, css3">
  <meta name="description" content="">
  <meta name="author" content="ThemeBucket">
  <link rel="shortcut icon" href="#" type="image/png">

  <title><?=$title?>|有呗</title>

  <!--icheck-->
<!--   <link href="/qwt/js/iCheck/skins/minimal/minimal.css" rel="stylesheet">
  <link href="/qwt/js/iCheck/skins/square/square.css" rel="stylesheet">
  <link href="/qwt/js/iCheck/skins/square/red.css" rel="stylesheet">
  <link href="/qwt/js/iCheck/skins/square/blue.css" rel="stylesheet"> -->

  <!--dashboard calendar-->
  <!-- <link href="/qwt/css/clndr.css" rel="stylesheet"> -->

  <!--Morris Chart CSS -->
<!--   <link rel="stylesheet" href="/qwt/js/morris-chart/morris.css"> -->

  <!--common-->
  <link href="/qwt/css/style.css" rel="stylesheet">
  <link href="/qwt/css/style-responsive.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="/qwt/css/font-awesome.css" />
  <link rel="stylesheet" type="text/css" href="/qwt/css/simditor.css" />
  <link href="/qwt/css/xiangqing.css" rel="stylesheet">

  <link rel="stylesheet" type="text/css" href="/qwt/js/bootstrap-datepicker/css/datepicker-custom.css" />
  <link rel="stylesheet" type="text/css" href="/qwt/js/bootstrap-timepicker/css/timepicker.css" />
  <link rel="stylesheet" type="text/css" href="/qwt/js/bootstrap-colorpicker/css/colorpicker.css" />
  <link rel="stylesheet" type="text/css" href="/qwt/js/bootstrap-daterangepicker/daterangepicker-bs3.css" />
  <link rel="stylesheet" type="text/css" href="/qwt/js/bootstrap-datetimepicker/css/datetimepicker-custom.css" />

  <link href="/qwt/css/style.css" rel="stylesheet">
  <link href="/qwt/css/style-responsive.css" rel="stylesheet">
  <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!--[if lt IE 9]>
  <script src="js/html5shiv.js"></script>
  <script src="js/respond.min.js"></script>
  <![endif]-->
</head>

<body class="sticky-header">

<section>
    <!-- left side start-->
    <div class="left-side sticky-left-side">

        <!--logo and iconic logo start-->
        <div class="logo">
            <a href="#" style="margin-left:60px"><img src="/qwt/images/logo2.png" alt=""></a>
        </div>

        <div class="logo-icon text-center">

        </div>
        <!--logo and iconic logo end-->

        <div class="left-side-inner">

            <!-- visible to small devices only -->


            <!--sidebar nav start-->
            <ul class="nav nav-pills nav-stacked custom-nav">
                <!-- <li class="active"><a href="index.html"><i class="fa fa-home"></i> <span>Dashboard</span></a></li> -->
                <li class="menu-list <?=isActive(array('userinfo', 'order','rebuy'))?>"><a href=""><i class="fa fa-laptop"></i> <span>会员中心</span></a>
                    <ul class="sub-menu-list">
                        <li class="<?=isActive('userinfo')?>"><a href="/qwta/userinfo" class="active">账户信息</a></li>
                        <li class="<?=isActive('order')?>"><a href="/qwta/order">订购记录</a></li>
                        <li class="<?=isActive('rebuy')?>"><a href="/qwta/rebuy">续费信息</a></li>
                    </ul>
                </li>
                <li class="menu-list <?=isActive(array('oauth', 'wxpay'))?>"><a href=""><i class="fa fa-book"></i> <span>绑定我们</span></a>
                    <ul class="sub-menu-list">
                        <li class="<?=isActive('oauth')?>"><a href="/qwta/oauth">一键授权</a></li>
                        <li class="<?=isActive('wxpay')?>"><a href="/qwta/wxpay">微信支付</a></li>
                        <!-- <li class="<?=isActive('userinfo')?>"><a href="#">待定...</a></li>
                        <li class="<?=isActive('userinfo')?>"><a href="#">待定...</a></li> -->
                    </ul>
                </li>
                <li><a href="/qwta/products"><i class="fa fa-cogs"></i> <span>产品中心</span></a>
                </li>
                <li class="menu-list"><a href=""><i class="fa fa-bullhorn"></i> <span>插件中心</span></a>
                <?php foreach ($todo['hasbuy'] as $hasbuy): ?>
                <ul class="sub-menu-list">
                      <li><a href="http://<?=$_SERVER['HTTP_HOST']?>/<?=convert($hasbuy->iid)?>"><?=$hasbuy->item->name?></a></li>
                </ul>
                    <?php endforeach ?>
                </li>
                <?php  if ($_SESSION['qwta']['admin'] >1):?>
                <li><a href="/qwta/logins"><i class="fa fa-sign-in"></i> <span>账号管理</span></a></li>
              <?php endif?>
            </ul>
            <!--sidebar nav end-->
        </div>
    </div>

    <!-- left side end-->

    <!-- main content start-->
    <div class="main-content" >

        <!-- header section start-->
        <div class="header-section">
            <!--toggle button start-->

            <!--toggle button end-->

        <div class="menu-right">
            <ul class="notification-menu">
                <li>
                    <a href="/qwta/layout" class="btn btn-default dropdown-toggle">
                      <i class="fa fa-sign-in"></i>
                        退出登录
                    </a>
                </li>

            </ul>
        </div>
      </div>
        <!-- header section end-->
        <section style="float:left;width:12%;height:100%;background:#2E3A4E;position:fixed;overflow:none">
            <div class="logo">
            <a href="index.html"><img src="images/logo.png" alt=""></a>
            </div>

            <div class="logo-icon text-center">
            <a href="index.html"><img src="images/logo_icon.png" alt=""></a>
            </div>
            <ul class="nav nav-pills nav-stacked custom-nav">
        <li><a href="/qwtrwba/home"><i class="fa fa-sign-in"></i> <span>基础设置</span></a></li>
         <li class="menu-list <?=isActive(array('items', 'tasks','orders'))?>"><a href=""><i class="fa fa-bullhorn"></i> <span>任务设置</span></a>
        <ul class="sub-menu-list">
                <li class="<?=isActive('items')?>"><a href="/qwtrwba/items">奖品管理</a></li>
                <li class="<?=isActive('tasks')?>"><a href="/qwtrwba/tasks">任务管理</a></li>
                <li class="<?=isActive('orders')?>"><a href="/qwtrwba/orders">领取记录</a></li>
            </ul>
        </li>
         <li><a href="/qwtrwba/stats_totle"><i class="fa fa-sign-in"></i> <span>数据统计</span></a></li>
        <li><a href="/qwtrwba/qrcodes"><i class="fa fa-sign-in"></i> <span>用户明细</span></a></li>
         <li class="menu-list <?=isActive(array('zero', 'area'))?>"><a href=""><i class="fa fa-bullhorn"></i> <span>可选功能</span></a>
        <ul class="sub-menu-list">
                <li class="<?=isActive('area')?>"><a href="/qwtrwba/area">选择可参与地区</a></li>
            </ul>
        </li>
    </ul>
        </section>

      <!-- <section style="float:left;width:15%;height:100%;background:#7a7676;position:fixed;overflow:none">
    <div class="logo">
    <a href="index.html"><img src="/qwt/images/logo.png" alt=""></a>
    </div>

    <div class="logo-icon text-center">
    <a href="index.html"><img src="/qwt/images/logo_icon.png" alt=""></a>
    </div>
    <ul class="nav nav-pills nav-stacked custom-nav">
        <li><a href="home"><i class="fa fa-sign-in"></i> <span>基础设置</span></a></li>
         <li class="menu-list"><a href="fontawesome.html"><i class="fa fa-bullhorn"></i> <span>奖品设置</span></a>
        <ul class="sub-menu-list">
                <li><a href="orders">兑换记录</a></li>
                <li><a href="items">奖品管理</a></li>
            </ul>
        </li>
         <li><a href="stats_totle"><i class="fa fa-sign-in"></i> <span>数据统计</span></a></li>
        <li><a href="qrcodes"><i class="fa fa-sign-in"></i> <span>用户明细</span></a></li>
         <li class="menu-list"><a href="fontawesome.html"><i class="fa fa-bullhorn"></i> <span>可选功能</span></a>
        <ul class="sub-menu-list">
                <li ><a href="">积分清零</a></li>
                <li><a href="">选择可参与地区</a></li>
            </ul>
        </li>
    </ul>
      </section> -->

        <!-- page heading end-->

        <!--body wrapper start-->
        <?=$content?>
        <!--body wrapper end-->

        <!--footer section start-->




        <!--footer section end-->


    </div>
    <!-- main content end-->
</section>

<!-- Placed js at the end of the document so the pages load faster -->
<script src="/qwt/js/jquery-1.10.2.min.js"></script>
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script src="/qwt/js/jquery-ui-1.9.2.custom.min.js"></script>
<script src="/qwt/js/jquery-migrate-1.2.1.min.js"></script>
<script src="/qwt/js/bootstrap.min.js"></script>
<script src="/qwt/js/modernizr.min.js"></script>
<script src="/qwt/js/jquery.nicescroll.js"></script>

<!--easy pie chart-->
<!-- <script src="/qwt/js/easypiechart/jquery.easypiechart.js"></script>
<script src="/qwt/js/easypiechart/easypiechart-init.js"></script> -->

<!--Sparkline Chart-->
<!-- <script src="/qwt/js/sparkline/jquery.sparkline.js"></script>
<script src="/qwt/js/sparkline/sparkline-init.js"></script> -->

<!--icheck -->
<!-- <script src="/qwt/js/iCheck/jquery.icheck.js"></script>
<script src="/qwt/js/icheck-init.js"></script>
 -->
<!-- jQuery Flot Chart-->
<!-- <script src="/qwt/js/flot-chart/jquery.flot.js"></script>
<script src="/qwt/js/flot-chart/jquery.flot.tooltip.js"></script>
<script src="/qwt/js/flot-chart/jquery.flot.resize.js"></script> -->


<!--Morris Chart-->
<!-- <script src="/qwt/js/morris-chart/morris.js"></script>
<script src="/qwt/js/morris-chart/raphael-min.js"></script> -->

<!--Calendar-->
<!-- <script src="/qwt/js/calendar/clndr.js"></script>
<script src="/qwt/js/calendar/evnt.calendar.init.js"></script>
<script src="/qwt/js/calendar/moment-2.2.1.js"></script>
<script src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js"></script> -->

<!--common scripts for all pages-->
<script src="/qwt/js/scripts.js"></script>

<!--Dashboard Charts-->
<!-- <script src="/qwt/js/dashboard-chart-init.js"></script> -->

<!-- datepicker -->
<script type="text/javascript" src="/qwt/js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/qwt/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="/qwt/js/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="/qwt/js/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- <script type="text/javascript" src="/qwt/js/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="/qwt/js/bootstrap-timepicker/js/bootstrap-timepicker.js"></script> -->

<!--pickers initialization-->


<script src="/qwt/js/pickers-init.js"></script>

<script type="text/javascript" src="/qwt/js/jquery.min.js"></script>
<script type="text/javascript" src="/qwt/js/module.js"></script>
<script type="text/javascript" src="/qwt/js/uploader.js"></script>
<script src="http://cdn.jfb.smfyun.com/wdy/plugins/citySelect/jquery.cityselect.js"></script>
<script type="text/javascript" src="/qwt/js/simditor.js"></script>

</body>
</html>
