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
    <title><?=$title?> | <?=$config['name']?> | </title>

    <?php require 'ahead.php';?>

    <link rel="stylesheet" href="/wdy/dist/css/skins/skin-red.min.css">
  </head>

  <script src="/wdy/plugins/jQuery/jQuery-2.1.4.min.js"></script>

  <body class="hold-transition skin-red sidebar-mini">
    <div class="wrapper">

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <a href="/ytya/home" class="logo">
          <span class="logo-mini"><b>单</b></span>
          <span class="logo-lg"><i class="fa fa-wechat"></i>&nbsp;<b>云商</b></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation"><div style="font-size: 16px;margin-top: 13px;color: #fff;position: absolute;left: 50px; ">公告：为了配合有赞接口的调整，点击菜单【基础设置】->【绑定有赞】->【点击重新授权有赞】来重新授权，以保证系统的正常使用。</div>
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
                <a href="/ytya/logout" class="dropdown-toggle">
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

            <!--
            <li class="treeview <?=isActive(array('items', 'orders'))?>">
              <a href="#"><i class="fa fa-shopping-cart"></i> <span>奖品设置</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('orders')?>"><a href="/ytya/orders"><i class="fa fa-circle-o"></i> <span>兑换记录</span> </a></li>
                <li class="<?=isActive('items')?>"><a href="/ytya/items"><i class="fa fa-circle-o"></i> <span>奖品管理</span> </a></li>
              </ul>
            </li>
            -->

            <!-- <li class="<?=isActive('stats')?>"><a href="/ytya/stats"><i class="fa fa-bar-chart"></i> <span>活动统计</span></a></li> -->
            <li class="<?=isActive('home')?>"><a href="/ytya/home"><i class="fa fa-dashboard"></i> <span>基础设置</span></a></li>
            <li class="treeview <?=isActive(array('qrcodes_m','qrcodes','skus'))?>">
              <a href="#"><i class="fa fa-bar-chart"></i> <span>经销商管理</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('qrcodes')?>"><a href="/ytya/qrcodes"><i class="fa fa-circle-o"></i> <span>经销商审核</span> </a></li>
                <li class="<?=isActive('qrcodes_m')?>"><a href="/ytya/qrcodes_m"><i class="fa fa-circle-o"></i> <span>经销商列表</span> </a></li>
                <li class="<?=isActive('skus')?>"><a href="/ytya/skus"><i class="fa fa-circle-o"></i> <span>经销商等级设置</span> </a></li>
              </ul>
            </li>

            <li class="<?=isActive('setgoods')?>"><a href="/ytya/setgoods"><i class="fa fa-dashboard"></i> <span>商品管理</span></a></li>
            <li class="<?=isActive('history_trades')?>"><a href="/ytya/history_trades"><i class="fa fa-dashboard"></i> <span>订单记录</span></a></li>
             <li class="treeview <?=isActive(array('stock','stock_history'))?>">
              <a href="#"><i class="fa fa-bar-chart"></i> <span>补货管理</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('stock')?>"><a href="/ytya/stock"><i class="fa fa-dashboard"></i> <span>补货申请</span></a></li>
              <li class="<?=isActive('stock_history')?>"><a href="/ytya/stock_history"><i class="fa fa-circle-o"></i> <span>补货记录</span> </a></li>
              </ul>
            </li>
                <li class="<?=isActive('qrcode_p')?>"><a href="/ytya/qrcode_p"><i class="fa fa-dashboard"></i> <span>客户管理</span></a></li>
             <li class="treeview <?=isActive(array('stats_totle', 'stats_item'))?>">
              <a href="#"><i class="fa fa-bar-chart"></i> <span>数据统计</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('stats_totle')?>"><a href="/ytya/stats_totle"><i class="fa fa-circle-o"></i> <span>概况</span> </a></li>
                <li class="<?=isActive('stats_item')?>"><a href="/ytya/stats_item"><i class="fa fa-circle-o"></i> <span>对账单</span> </a></li>
              </ul>
            </li>
             <li class="treeview <?=isActive(array('money','history_withdrawals'))?>">
              <a href="#"><i class="fa fa-bar-chart"></i> <span>提现管理</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('money')?>"><a href="/ytya/money"><i class="fa fa-dashboard"></i> <span>提现概况</span></a></li>
              <li class="<?=isActive('history_withdrawals')?>"><a href="/ytya/history_withdrawals"><i class="fa fa-circle-o"></i> <span>提现记录</span> </a></li>
              </ul>
            </li>
            <?php if($_SESSION['ytya']['admin'] >= 1):?>
              <li class="<?=isActive('admin')?>"><a href="/ytya/logins"><i class="fa fa-user"></i> <span>账号管理</span></a></li>
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
