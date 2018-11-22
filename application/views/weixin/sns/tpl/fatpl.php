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
    <title><?=$title?> | <?=$config['name']?> | 浪莎SNS</title>

    <?php require 'ahead.php';?>

    <link rel="stylesheet" href="/wdy/dist/css/skins/skin-red.min.css">
  </head>

  <script src="/wdy/plugins/jQuery/jQuery-2.1.4.min.js"></script>

  <body class="hold-transition skin-red sidebar-mini">
    <div class="wrapper">

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <a href="/snsa/home" class="logo">
          <span class="logo-mini"><b>单</b></span>
          <span class="logo-lg"><i class="fa fa-wechat"></i>&nbsp;<b>浪莎SNS</b></span>
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

              <!-- Tasks Menu -->
              <li class="dropdown tasks-menu<?=$todo['all'] ? '' : ' hidden'?>">
                <!-- Menu Toggle Button -->
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-flag-o"></i>
                  <span class="label label-warning"><?=$todo['all']?></span>
                </a>

                <ul class="dropdown-menu">
                  <li class="head


                  er"><?=$todo['all']?> 条请求待处理</li>
                  <li>
                    <ul class="menu">
                    <?php if($todo['users']):?>
                    <li><a href="/snsa/qrcodes"><i class="fa fa-users text-green"></i> <?=$todo['users']?> 个用户参与</a></li>
                    <li><a href="/snsa/qrcodes?ticket=1"><i class="fa fa-heart text-green"></i> <?=$todo['tickets']?> 个用户已生成海报</a></li>
                    <?php endif?>

                    <?php if($todo['items']):?>
                    <li><a href="/snsa/orders"><i class="fa fa-shopping-cart text-green"></i> <?=$todo['items']?> 条待处理兑换</a></li>
                    <?php endif?>

                    </ul>
                  </li>
                  <!-- <li class="footer">
                    <a href="/snsa/orders">查看所有</a>
                  </li> -->
                </ul>
              </li>

              <!-- User Account Menu -->
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <a href="/snsa/logout" class="dropdown-toggle">
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

            <!-- <li class="<?=isActive('stats')?>"><a href="/snsa/stats"><i class="fa fa-bar-chart"></i> <span>活动统计</span></a></li> -->
            <li class="<?=isActive('home')?>"><a href="/snsa/home"><i class="fa fa-dashboard"></i> <span>基础设置</span></a></li>

            <li class="<?=isActive('group')?>"><a href="/snsa/group"><i class="fa fa-dashboard"></i> <span>成团详情</span></a></li>

            <li class="<?=isActive('group')?>"><a href="/snsa/customer"><i class="fa fa-dashboard"></i> <span>用户管理</span></a></li>
            <li class="<?=isActive('group')?>"><a href="/snsa/order"><i class="fa fa-circle-o"></i> <span>领取纪录</span></a></li>
            <li class="<?=isActive('group')?>"><a href="/snsa/item"><i class="fa fa-dashboard"></i> <span>奖品管理</span></a></li>
            <li class="<?=isActive('group')?>"><a href="/snsa/statistics"><i class="fa fa-bar-chart"></i> <span>数据统计</span></a></li>
<!--             <li class="treeview <?=isActive(array('items', 'shorders'))?>">
              <a href="#"><i class="fa fa-bar-chart"></i> <span>兑换商城</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('items')?>"><a href="/snsa/items"><i class="fa fa-circle-o"></i> <span>奖品设置</span> </a></li>
                <li class="<?=isActive('shorders')?>"><a href="/snsa/shorders"><i class="fa fa-circle-o"></i> <span>兑换记录</span> </a></li>
              </ul>
            </li> -->
           <!--  <li class="treeview <?=isActive(array('qrcodes_m','qrcodes','skus'))?>">
              <a href="#"><i class="fa fa-bar-chart"></i> <span>用户管理</span> <i class="fa fa-angle-left pull-right"></i></a> -->
              <!-- <ul class="treeview-menu">
                <li class="<?=isActive('qrcodes')?>"><a href="/snsa/qrcodes"><i class="fa fa-circle-o"></i> <span>分销商审核</span> </a></li>
                <li class="<?=isActive('qrcodes_m')?>"><a href="/snsa/qrcodes_m"><i class="fa fa-circle-o"></i> <span>分销商管理</span> </a></li>
                <li class="<?=isActive('skus')?>"><a href="/snsa/skus"><i class="fa fa-circle-o"></i> <span>分销商等级管理</span> </a></li>
              </ul> -->
           <!--  </li>
             <li class="treeview <?=isActive(array('stats_totle', 'history_trades','history_withdrawals','stats_goods'))?>">
              <a href="#"><i class="fa fa-bar-chart"></i> <span>订单详情</span> <i class="fa fa-angle-left pull-right"></i></a> -->
             <!--  <ul class="treeview-menu">
                <li class="<?=isActive('stats_totle')?>"><a href="/snsa/stats_totle"><i class="fa fa-circle-o"></i> <span>概况</span> </a></li>
                <li class="<?=isActive('history_trades')?>"><a href="/snsa/history_trades"><i class="fa fa-circle-o"></i> <span>订单记录</span> </a></li>
                <li class="<?=isActive('history_withdrawals')?>"><a href="/snsa/history_withdrawals"><i class="fa fa-circle-o"></i> <span>提现记录</span> </a></li>
                <li class="<?=isActive('stats_goods')?>"><a href="/snsa/stats_goods"><i class="fa fa-circle-o"></i> <span>商品统计</span> </a></li>
              </ul> -->
            </li>
            <?php if($_SESSION['snsa']['admin'] >= 1):?>
              <li class="<?=isActive('admin')?>"><a href="/snsa/logins"><i class="fa fa-user"></i> <span>账号管理</span></a></li>
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
