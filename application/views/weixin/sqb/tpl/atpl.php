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
    <title><?=$title?> | <?=$config['name']?> | 社群管家</title>

    <?php require 'ahead.php';?>

    <link rel="stylesheet" href="/sqb/dist/css/skins/skin-green.min.css">
  </head>

  <script src="/sqb/plugins/jQuery/jQuery-2.1.4.min.js"></script>

  <body class="hold-transition skin-green sidebar-mini">
    <div class="wrapper">

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <a href="/sqba/home" class="logo">
          <span class="logo-mini"><b>社</b></span>
          <span class="logo-lg"><i class="fa fa-wechat"></i>&nbsp;<b>社群管家</b></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">收起导航</span>
          </a>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- <li class="dropdown user user-menu">

                <a target="_blank" href="https://wap.koudaitong.com/v2/showcase/feature?alias=nbo89d92" class="dropdown-toggle">
                  <span class="hidden-xs">点此查看功能更新日志 </span>
                </a>
              </li> -->
              <!-- Tasks Menu -->
              <li class="dropdown tasks-menu<?=$todo['all'] ? '' : ' hidden'?>">
                <!-- Menu Toggle Button -->
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-flag-o"></i>
                  <span class="label label-danger"><?=$todo['all']?></span>
                </a>

                <ul class="dropdown-menu">
                  <li class="header"><?=$todo['all']?> 条请求待处理</li>
                  <li>
                    <ul class="menu">
                    <?php if($todo['users']):?>
                    <li><a href="/sqba/qrcodes"><i class="fa fa-users text-green"></i> <?=$todo['users']?> 个用户参与</a></li>
                    <?php endif?>

                    <?php if($todo['items']):?>
                    <li><a href="/sqba/orders"><i class="fa fa-shopping-cart text-green"></i> <?=$todo['items']?> 条待处理兑换</a></li>
                    <?php endif?>

                    </ul>
                  </li>
                  <li class="footer">
                    <a href="/sqba/qrcodes">查看所有</a>
                  </li>
                </ul>
              </li>

              <!-- User Account Menu -->
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <a href="/sqba/logout" class="dropdown-toggle">
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

            <li class="<?=isActive('home')?>"><a href="/sqba/home"><i class="fa fa-dashboard"></i> <span>基础设置</span></a></li>

            <li class="<?=isActive('qrcodes')?>"><a href="/sqba/qrcodes"><i class="fa fa-user"></i> <span>用户明细</span></a></li>
            <?php if($_SESSION['sqba']['admin'] >= 1):?>
              <li class="<?=isActive('admin')?>"><a href="/sqba/logins"><i class="fa fa-user"></i> <span>账号管理</span></a></li>
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

    <script src="/sqb/bootstrap/js/bootstrap.min.js"></script>
    <script src="/sqb/dist/js/app.min.js"></script>

    <script type="text/javascript" src="/sqb/plugins/simditor-2.2.4/scripts/module.js"></script>
    <script type="text/javascript" src="/sqb/plugins/simditor-2.2.4/scripts/hotkeys.js"></script>
    <script type="text/javascript" src="/sqb/plugins/simditor-2.2.4/scripts/simditor.js"></script>

    <script type="text/javascript" src="/sqb/plugins/datetimepicker/js/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="/sqb/plugins/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js"></script>

    <script src="/sqb/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <script src="/sqb/plugins/fastclick/fastclick.min.js"></script>

    <!-- memory_usage: {memory_usage} -->
    <!-- execution_time: {execution_time} -->

  </body>
</html>
