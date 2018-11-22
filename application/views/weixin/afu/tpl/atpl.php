<?php
$agent = Session::instance()->get('agent');//$_SESSION["agent"];
function isActive($need) {
  //当前页
  $action = Request::instance()->action;
  if ($action == $need) return 'active';

  if (is_array($need)) foreach ($need as $v) {
    if ($action == $v) return 'active';
  }
}
?><!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=$title?> | <?=$config['name']?> | <?=$name?>数据中心</title>

    <?php require 'ahead.php';?>

    <link rel="stylesheet" href="/yyx/dist/css/skins/skin-green.min.css">
  </head>

  <script src="/yyx/plugins/jQuery/jQuery-2.1.4.min.js"></script>

  <body class="hold-transition skin-green sidebar-mini">
    <div class="wrapper">

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <a href="/yyxa/home" class="logo">
          <span class="logo-mini"><b></b></span>
          <span class="logo-lg"><i class="fa fa-wechat"></i>&nbsp;<b><?=$name?>数据中心</b></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">收起导航</span>
          </a>
          <?php if($agent!='android'&&$agent!='iphone'):?>
          <!-- <span style='display:inline-block;padding-top:10px;color:#f6f6f6;'><a href="http://shop1128733.koudaitong.com/v2/feature/nbo89d92" style="color:black;font-weight:bold;font-size:20px;" target="_blank">首次使用，请 点击这里查看积分宝系统使用说明书及功能更新日志</a></span> -->
          <? else:?>
          <!-- <span style='display:inline-block;padding-top:15px;color:#f6f6f6;'><a href="http://shop1128733.koudaitong.com/v2/feature/nbo89d92" style="color:white;" target="_blank"> 点击这里查看云阳县数据中心系统使用说明书</a></span> -->
          <? endif;?>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- User Account Menu -->
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <a href="/yyxa/logout" class="dropdown-toggle">
                  <span class="hidden-xs">退出系统 <i class="fa fa-sign-out"></i></span>
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </header>

      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

          <!-- Sidebar Menu -->
          <ul class="sidebar-menu">
            <li class="header">主菜单</li>

            <!-- <li class="">
              <a href="#"><i class="fa fa-user"></i> <span>使用说明及系统更新</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class=""><a target="_blank" href="https://shop1128733.koudaitong.com/v2/feature/nbo89d92"><i class="fa fa-circle-o"></i> <span>使用说明书</span> </a></li>
                <li class=""><a target="_blank" href="https://wap.koudaitong.com/v2/feature/x14nli85"><i class="fa fa-circle-o"></i> <span>系统更新日志</span> </a></li>
              </ul>
            </li> -->
            <li class="<?=isActive('home')?>"><a href="/yyxa/home"><i class="fa fa-dashboard"></i> <span>基础设置</span></a></li>
            <li class="treeview <?=isActive(array('stats_totle', 'item_rank'))?>">
              <a href="#"><i class="fa fa-bar-chart"></i> <span>数据统计</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('stats_totle')?>"><a href="/yyxa/stats_totle"><i class="fa fa-circle-o"></i> <span>概况</span> </a></li>
                <li class="<?=isActive('item_rank')?>"><a href="/yyxa/item_rank"><i class="fa fa-circle-o"></i> <span>热销商品排行</span> </a></li>
              </ul>
            </li>
            <li class="<?=isActive('item')?>"><a href="/yyxa/items"><i class="fa fa-user"></i> <span>大客户订单</span></a></li>
            <li class="<?=isActive('qrcodes')?>"><a href="/yyxa/qrcodes"><i class="fa fa-user"></i> <span>用户明细</span></a></li>
            <?php if($_SESSION['yyxa']['admin'] >= 1):?>
              <li class="<?=isActive('admin')?>"><a href="/yyxa/logins"><i class="fa fa-user"></i> <span>账号管理</span></a></li>
            <?php endif?>


          </ul><!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
      </aside>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">

      <?=$content?>

      </div><!-- /.content-wrapper -->

      <!-- Main Footer -->
      <footer class="main-footer">
        <div class="pull-right hidden-xs"><b>Version</b> 1.1.9</div>
        <strong>Copyright &copy; 2015 <a href="#">神码浮云</a>.</strong> All rights reserved.
      </footer>

      <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->

    <?php if (isset($_GET['debug'])) echo View::factory('profiler/stats');?>

    <script src="/yyx/bootstrap/js/bootstrap.min.js"></script>
    <script src="/yyx/dist/js/app.min.js"></script>

    <script type="text/javascript" src="/yyx/plugins/simditor-2.2.4/scripts/module.js"></script>
    <script type="text/javascript" src="/yyx/plugins/simditor-2.2.4/scripts/hotkeys.js"></script>
    <script type="text/javascript" src="/yyx/plugins/simditor-2.2.4/scripts/simditor.js"></script>

    <script type="text/javascript" src="/yyx/plugins/datetimepicker/js/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="/yyx/plugins/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js"></script>

    <script src="/yyx/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <script src="/yyx/plugins/fastclick/fastclick.min.js"></script>

    <!-- memory_usage: {memory_usage} -->
    <!-- execution_time: {execution_time} -->

  </body>
</html>
