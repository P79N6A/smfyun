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
    <title><?=$title?> | <?=$config['name']?> | 有赞插件平台</title>
    <?php require 'ahead.php';?>
    <link rel="stylesheet" href="/wdy/dist/css/skins/skin-green.min.css">
  </head>
  <script src="/wdy/plugins/jQuery/jQuery-2.1.4.min.js"></script>
  <body class="hold-transition skin-green sidebar-mini">
    <div class="wrapper">
      <!-- Main Header -->
      <header class="main-header">
        <!-- Logo -->
        <a href="/yzba/home" class="logo">
          <span class="logo-mini"><b>粉</b></span>
          <span class="logo-lg"><i class="fa fa-wechat"></i>&nbsp;<b>有赞插件平台</b></span>
        </a>
        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">收起导航</span>
          </a>
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <li class="dropdown tasks-menu<?=$todo['all'] ? '' : ' hidden'?>">
                <!-- Menu Toggle Button -->
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-flag-o"></i>
                  <span class="label label-danger"><?=$todo['all']?></span>
                </a>
              </li>
              <!-- User Account Menu -->
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <a href="/wdya/logout" class="dropdown-toggle">
                  <span class="hidden-xs">退出系统 <i class="fa fa-sign-out"></i></span>
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <aside class="main-sidebar">
        <section class="sidebar">
          <ul class="sidebar-menu">
            <li class="header">主菜单</li>
            <li class="<?=isActive('youzan')?>"><a href="/yzba/youzan"><i class="fa fa-dashboard"></i> <span>绑定有赞</span></a></li>
            <li class="<?=isActive('weixin')?>"><a href="/yzba/weixin"><i class="fa fa-dashboard"></i> <span>微信参数</span></a></li>
            <li class="<?=isActive('produce')?>"><a href="/yzba/produce"><i class="fa fa-dashboard"></i> <span>产品中心</span></a></li>
            <li class="<?=isActive('plug')?>"><a href="/yzba/plug"><i class="fa fa-dashboard"></i> <span>插件中心</span></a></li>
          </ul>
        </section>
      </aside>
      <div class="content-wrapper">
      <?=$content?>
      </div>
      <footer class="main-footer">
        <div class="pull-right hidden-xs"><b>Version</b> 1.1.9</div>
        <strong>Copyright &copy; 2015 <a href="#">神码浮云</a>.</strong> All rights reserved.
      </footer>
      <div class="control-sidebar-bg"></div>
    </div>
    <?php if (isset($_GET['debug'])) echo View::factory('profiler/stats');?>
    <script src="/wdy/bootstrap/js/bootstrap.min.js"></script>
    <script src="/wdy/dist/js/app.min.js"></script>
    <script type="text/javascript" src="/wdy/plugins/simditor-2.2.4/scripts/module.js"></script>
    <script type="text/javascript" src="/wdy/plugins/simditor-2.2.4/scripts/hotkeys.js"></script>
    <script type="text/javascript" src="/wdy/plugins/simditor-2.2.4/scripts/simditor.js"></script>
    <script type="text/javascript" src="/wdy/plugins/datetimepicker/js/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="/wdy/plugins/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js"></script>
    <script src="/wdy/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <script src="/wdy/plugins/fastclick/fastclick.min.js"></script>
  </body>
</html>
