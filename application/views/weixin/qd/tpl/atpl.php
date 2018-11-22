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
    <title><?=$title?> | <?=$config['name']?> | 喜临门</title>

    <?php require 'ahead.php';?>

    <link rel="stylesheet" href="/qd/dist/css/skins/skin-green.min.css">
  </head>

  <script src="/qd/plugins/jQuery/jQuery-2.1.4.min.js"></script>

  <body class="hold-transition skin-green sidebar-mini">
    <div class="wrapper">

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <a href="/qda/orders" class="logo">
          <span class="logo-mini"><b>喜</b></span>
          <span class="logo-lg"><i class="fa fa-wechat"></i>&nbsp;<b>喜临门</b></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">

          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

              <!-- User Account Menu -->
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <a href="/qda/logout" class="dropdown-toggle">
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
            <li class="treeview <?=isActive(array('items', 'orders','create'))?>">
              <a href="#"><i class="fa fa-shopping-cart"></i> <span>奖品设置</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('orders')?>"><a href="/qda/orders"><i class="fa fa-circle-o"></i> <span>兑换记录</span> </a></li>
                <li class="<?=isActive('items')?>"><a href="/qda/items"><i class="fa fa-circle-o"></i> <span>奖品管理</span> </a></li>
              </ul>
            </li>
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

    <script src="/qd/bootstrap/js/bootstrap.min.js"></script>
    <script src="/qd/dist/js/app.min.js"></script>

    <script type="text/javascript" src="/qd/plugins/simditor-2.2.4/scripts/module.js"></script>
    <script type="text/javascript" src="/qd/plugins/simditor-2.2.4/scripts/hotkeys.js"></script>
    <script type="text/javascript" src="/qd/plugins/simditor-2.2.4/scripts/simditor.js"></script>

    <script type="text/javascript" src="/qd/plugins/datetimepicker/js/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="/qd/plugins/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js"></script>

    <script src="/qd/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <script src="/qd/plugins/fastclick/fastclick.min.js"></script>

    <!-- memory_usage: {memory_usage} -->
    <!-- execution_time: {execution_time} -->

  </body>
</html>
