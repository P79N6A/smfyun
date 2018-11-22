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
    <title><?=$title?> | <?=$config['name']?> | 代理哆</title>

    <?php require 'ahead.php';?>

    <link rel="stylesheet" href="/wdy/dist/css/skins/skin-red.min.css">
  </head>

  <script src="/wdy/plugins/jQuery/jQuery-2.1.4.min.js"></script>

  <body class="hold-transition skin-red sidebar-mini">
    <div class="wrapper">

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <a href="/dlda/home" class="logo">
          <span class="logo-mini"><b>代</b></span>
          <span class="logo-lg"><i class="fa fa-wechat"></i>&nbsp;<b>代理哆</b></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">收起导航</span>
          </a>
          <?php if($agent!='android'&&$agent!='iphone'):?>
          <!-- <span style='display:inline-block;padding-top:15px;color:#f6f6f6;'><a href="https://wap.koudaitong.com/v2/showcase/feature?alias=j2avl2px" style="color:white;" target="_blank">首次使用，请 点击这里查看全员分销系统使用说明书及功能更新日志</a></span> -->
          <? else:?>
          <span style='display:inline-block;padding-top:15px;color:#f6f6f6;'><a href="https://h5.youzan.com/v2/feature/1hvs9h23g" style="color:white;" target="_blank">点击这里查看代理哆系统使用说明书</a></span>
          <? endif;?>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">

              <!-- User Account Menu -->
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <a href="/dlda/logout" class="dropdown-toggle">
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
                <li class="<?=isActive('orders')?>"><a href="/qfxa/orders"><i class="fa fa-circle-o"></i> <span>兑换记录</span> </a></li>
                <li class="<?=isActive('items')?>"><a href="/qfxa/items"><i class="fa fa-circle-o"></i> <span>奖品管理</span> </a></li>
              </ul>
            </li>
            -->

            <!-- <li class="<?=isActive('stats')?>"><a href="/qfxa/stats"><i class="fa fa-bar-chart"></i> <span>活动统计</span></a></li> -->
            <li class="treeview">
              <a href="#"><i class="fa fa-book"></i> <span>使用说明及系统更新</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class=""><a target="_blank" href="https://h5.youzan.com/v2/feature/1hvs9h23g"><i class="fa fa-circle-o"></i> <span>使用说明书</span> </a></li>              </ul>
            </li>
            <li class="<?=isActive('home')?>"><a href="/dlda/home"><i class="fa fa-dashboard"></i> <span>基础设置</span></a></li>



<!--             <li class="treeview <?=isActive(array('items', 'shorders'))?>">
              <a href="#"><i class="fa fa-bar-chart"></i> <span>兑换商城</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('items')?>"><a href="/qfxa/items"><i class="fa fa-circle-o"></i> <span>奖品设置</span> </a></li>
                <li class="<?=isActive('shorders')?>"><a href="/qfxa/shorders"><i class="fa fa-circle-o"></i> <span>兑换记录</span> </a></li>
              </ul>
            </li> -->
            <li class="treeview <?=isActive(array('qrcodes_m','skus','group','group_add'))?>">
              <a href="#"><i class="fa fa-users"></i> <span>代理设置</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('group')?>"><a href="/dlda/group"><i class="fa fa-circle-o"></i> <span>代理分组设置</span> </a></li>
                <li class="<?=isActive('skus')?>"><a href="/dlda/skus"><i class="fa fa-circle-o"></i> <span>代理设置</span> </a></li>
                <li class="<?=isActive('qrcodes_m')?>"><a href="/dlda/qrcodes_m"><i class="fa fa-circle-o"></i> <span>代理列表</span> </a></li>
                <li><a href="/dld/order_top/<?=$_SESSION['dlda']['bid']?>" target="_blank"><i class="fa fa-circle-o"></i> <span>销量排行</span> </a></li>
              </ul>
            </li>
            <li class="<?=isActive('setgood1s')?>"><a href="/dlda/setgood1s"><i class="fa fa-shopping-cart"></i> <span>商品管理</span></a></li>
            <li class="<?=isActive('customers')?>"><a href="/dlda/customers"><i class="fa fa-user-secret"></i> <span>客户管理</span></a></li>
            <li class="<?=isActive('history_trades')?>"><a href="/dlda/history_trades"><i class="fa fa-list"></i> <span>订单管理</span> </a></li>
           <li class="treeview <?=isActive(array('calculate','history_scores','profit'))?>">
            <a href="#"><i class="fa fa-calculator"></i> <span>结算管理</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li class="<?=isActive('calculate')?>"><a href="/dlda/calculate"><i class="fa fa-circle-o"></i> <span>个人团队奖励结算账单</span> </a></li>
              <li class="<?=isActive('profit')?>"><a href="/dlda/profit"><i class="fa fa-circle-o"></i> <span>销售利润结算账单</span></a></li>
              <li class="<?=isActive('history_scores')?>"><a href="/dlda/history_scores"><i class="fa fa-circle-o"></i> <span>账单结算记录</span> </a></li>
            </ul>
          </li>
            <?php if($_SESSION['dlda']['admin'] >= 1):?>
              <li class="<?=isActive('admin')?>"><a href="/dlda/logins"><i class="fa fa-credit-card"></i> <span>账号管理</span></a></li>
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
