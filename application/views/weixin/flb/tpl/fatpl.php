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
    <title><?=$title?> | <?=$config['name']?> | 福利宝</title>

    <?php require 'ahead.php';?>

    <link rel="stylesheet" href="/wdy/dist/css/skins/skin-red.min.css">
  </head>

  <script src="/wdy/plugins/jQuery/jQuery-2.1.4.min.js"></script>

  <body class="hold-transition skin-red sidebar-mini">
    <div class="wrapper">

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <a href="/flba/home" class="logo">
          <span class="logo-mini"><b>福</b></span>
          <span class="logo-lg"><i class="fa fa-wechat"></i>&nbsp;<b>福利宝</b></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation"><div style="font-size: 16px;margin-top: 13px;color: #fff;position: absolute;left: 50px; "><!-- 公告：为配合微信接口政策的调整，请<a target="_blank" style="color: #00c0ef;font-weight: bold;" href='https://h5.youzan.com/v2/feature/pbz2iy7e'>点击这里</a>查看教程，尽快开启IP白名单，以保证系统的正常使用。 --></div>
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">收起导航</span>
          </a>
          <?php if($agent!='android'&&$agent!='iphone'):?>
          <!-- <span style='display:inline-block;padding-top:15px;color:#f6f6f6;'><a href="https://wap.koudaitong.com/v2/showcase/feature?alias=j2avl2px" style="color:white;" target="_blank">首次使用，请 点击这里查看全员分销系统使用说明书及功能更新日志</a></span> -->
          <? else:?>
          <span style='display:inline-block;padding-top:15px;color:#f6f6f6;'><a href="https://wap.koudaitong.com/v2/showcase/feature?alias=j2avl2px" style="color:white;" target="_blank">点击这里查看全员分销系统使用说明书</a></span>
          <? endif;?>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

              <!-- Tasks Menu -->
              <li class="dropdown tasks-menu<?=$todo['all'] ? '' : ' hidden'?>">
                <!-- Menu Toggle Button -->
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-flag-o"></i>
                  <span class="label label-warning"><?=$todo['all']?></span>
                </a>

              </li>

              <!-- User Account Menu -->
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <a href="/flba/logout" class="dropdown-toggle">
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
            <!--<li class="treeview">
              <a href="#"><i class="fa fa-user"></i> <span>使用说明及系统更新</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class=""><a target="_blank" href="https://wap.koudaitong.com/v2/showcase/feature?alias=j2avl2px"><i class="fa fa-circle-o"></i> <span>使用说明书</span> </a></li>
                <li class=""><a target="_blank" href="https://wap.koudaitong.com/v2/feature/6ojfqnl3"><i class="fa fa-circle-o"></i> <span>系统更新日志</span> </a></li>
              </ul>
            </li>-->
            <li class="<?=isActive('home')?>"><a href="/flba/home"><i class="fa fa-dashboard"></i> <span>基础设置</span></a></li>
            <li class="<?=isActive('sku')?>"><a href="/flba/skus"><i class="fa fa-dashboard"></i> <span>返还设置</span></a></li>
            <li class="treeview <?=isActive(array('has_send', 'sending'))?>">
              <a href="#"><i class="fa fa-user"></i> <span>发送记录</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('sending')?>"><a href="/flba/sending"><i class="fa fa-circle-o"></i> <span>待发送</span> </a></li>
                <li class="<?=isActive('has_send')?>"><a href="/flba/has_send"><i class="fa fa-circle-o"></i> <span>已发送</span> </a></li>
              </ul>
            </li>
            <li class="<?=isActive('order')?>"><a href="/flba/orders"><i class="fa fa-dashboard"></i> <span>订单记录</span></a></li>
            <?php if($_SESSION['flba']['admin'] >= 1):?>
              <li class="<?=isActive('admin')?>"><a href="/flba/logins"><i class="fa fa-user"></i> <span>账号管理</span></a></li>
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
        <div class="pull-right hidden-xs"><b>Version</b> 1.1.0</div>
        <strong>Copyright &copy; 2015 <a href="#">神码浮云</a>.</strong> All rights reserved.
      </footer>

      <div class="control-sidebar-bg"></div>
    </div><!-- ./wrapper -->

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
