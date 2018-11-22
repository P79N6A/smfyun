<?php $agent = Session::instance()->get('agent');//$_SESSION["agent"];
function isActive($need) {   //当前页
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
    <title><?=$title?> | <?=$config['name']?> | 包你看</title>

    <?php require 'ahead.php';?>

    <link rel="stylesheet" href="/wdy/dist/css/skins/skin-red.min.css">
  </head>

  <script src="/wdy/plugins/jQuery/jQuery-2.1.4.min.js"></script>

  <body class="hold-transition skin-red sidebar-mini">
    <div class="wrapper">

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <a href="/bnka/home" class="logo">
          <span class="logo-mini"><b>看</b></span>
          <span class="logo-lg"><i class="fa fa-wechat"></i>&nbsp;<b>看样子</b></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation"><!-- <div style="font-size: 16px;margin-top: 13px;color: #fff;position: absolute;left: 50px; ">公告：为了配合有赞接口的调整，点击菜单【基础设置】->【绑定有赞】->【点击重新授权有赞】来重新授权，以保证系统的正常使用。</div> -->
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">收起导航</span>
          </a>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- User Account Menu -->
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <?php if($_SESSION['bnka']['bid']&&$todo['flag']==1):?>
                <a href="/bnka/logout" class="dropdown-toggle">
                  <span class="hidden-xs">退出系统 <i class="fa fa-sign-out"></i></span>
                <?php endif?>
                <?php if($_SESSION['bnka']['aid']&&$todo['flag']==2):?>
                <a href="/bnkb/logout" class="dropdown-toggle">
                  <span class="hidden-xs">退出系统 <i class="fa fa-sign-out"></i></span>
                <?php endif?>
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
            <li class="<?=isActive('qrcodes')?>"><a href="/bnka/qrcodes"><i class="fa fa-user"></i> <span>用户管理</span></a></li>
            <li class="<?=isActive('analyze')?>"><a href="/bnka/analyze"><i class="fa fa-list"></i> <span>发送记录</span></a></li>
            <li class="<?=isActive('lottery_history')?>"><a href="/bnka/lottery_history"><i class="fa fa-list-alt"></i> <span>领取记录</span> </a></li>
            <li class="<?=isActive('buymentrecord')?>"><a href="/bnka/buymentrecord"><i class="fa fa-list-ol"></i> <span>收支明细</span> </a></li>
            <li class="<?=isActive('general')?>"><a href="/bnka/general"><i class="fa fa-list-ul"></i> <span>概况</span> </a></li>

            <li class="treeview <?=isActive(array('rsync','orders','score'))?>">
              <a href="#"><i class="fa fa-users"></i> <span>提现管理</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
            <li class="<?=isActive('rsync')?>"><a href="/bnka/rsync"><i class="fa fa-circle-o"></i> <span>提现模式</span> </a></li>
            <li class="<?=isActive('orders')?>"><a href="/bnka/orders"><i class="fa fa-circle-o"></i> <span>提现申请</span> </a></li>
            <li class="<?=isActive('score')?>"><a href="/bnka/score"><i class="fa fa-circle-o"></i> <span>提现记录</span> </a></li>
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
