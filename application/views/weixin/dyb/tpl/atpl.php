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
    <title><?=$title?> | <?=$config['name']?> | 积分宝订阅号版</title>

    <?php require 'ahead.php';?>

    <link rel="stylesheet" href="/dyb/dist/css/skins/skin-green.min.css">
  </head>

  <script src="/dyb/plugins/jQuery/jQuery-2.1.4.min.js"></script>

  <body class="hold-transition skin-green sidebar-mini">
    <div class="wrapper">

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <a href="/dyba/home" class="logo">
          <span class="logo-mini"><b>订</b></span>
          <span class="logo-lg"><i class="fa fa-wechat"></i>&nbsp;<b>积分宝订阅号版</b></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation"><div style="font-size: 16px;margin-top: 13px;color: #fff;position: absolute;left: 50px; "><!-- 公告：为配合微信接口政策的调整，请<a target="_blank" style="color: #00c0ef;font-weight: bold;" href='https://h5.youzan.com/v2/feature/pbz2iy7e'>点击这里</a>查看教程，尽快开启IP白名单，以保证系统的正常使用。 --></div>
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">收起导航</span>
          </a>
          <?php if($agent!='android'&&$agent!='iphone'):?>
          <!-- <span style='display:inline-block;padding-top:10px;color:#f6f6f6;'><a href="http://shop1128733.koudaitong.com/v2/feature/nbo89d92" style="color:black;font-weight:bold;font-size:20px;" target="_blank">首次使用，请 点击这里查看订阅宝系统使用说明书及功能更新日志</a></span> -->
          <? else:?>
          <span style='display:inline-block;padding-top:15px;color:#f6f6f6;'><a href="https://wap.koudaitong.com/v2/feature/14c61rz7m" style="color:white;" target="_blank"> 点击这里查看积分宝订阅号版系统使用说明书</a></span>
          <? endif;?>
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
                    <li><a href="/dyba/qrcodes"><i class="fa fa-users text-green"></i> <?=$todo['users']?> 个用户参与</a></li>
                    <li><a href="/dyba/qrcodes?ticket=1"><i class="fa fa-heart text-green"></i> <?=$todo['tickets']?> 个用户已生成海报</a></li>
                    <?php endif?>

                    <?php if($todo['items']):?>
                    <li><a href="/dyba/orders"><i class="fa fa-shopping-cart text-green"></i> <?=$todo['items']?> 条待处理兑换</a></li>
                    <?php endif?>

                    </ul>
                  </li>
                  <li class="footer">
                    <a href="/dyba/orders">查看所有</a>
                  </li>
                </ul>
              </li>

              <!-- User Account Menu -->
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <a href="/dyba/logout" class="dropdown-toggle">
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

            <li class="">
              <a href="#"><i class="fa fa-user"></i> <span>使用说明及系统更新</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class=""><a target="_blank" href="https://wap.koudaitong.com/v2/feature/14c61rz7m"><i class="fa fa-circle-o"></i> <span>使用说明书</span> </a></li>
                <li class=""><a target="_blank" href="https://wap.koudaitong.com/v2/feature/14c61rz7m"><i class="fa fa-circle-o"></i> <span>系统更新日志</span> </a></li>
              </ul>
            </li>

            <li class="<?=isActive('home')?>"><a href="/dyba/home"><i class="fa fa-dashboard"></i> <span>基础设置</span></a></li>

            <li class="treeview <?=isActive(array('items', 'orders'))?>">
              <a href="#"><i class="fa fa-shopping-cart"></i> <span>奖品设置</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('orders')?>"><a href="/dyba/orders"><i class="fa fa-circle-o"></i> <span>兑换记录</span> </a></li>
                <li class="<?=isActive('items')?>"><a href="/dyba/items"><i class="fa fa-circle-o"></i> <span>奖品管理</span> </a></li>
              </ul>
            </li>
            <?php if($_SESSION['dyba']['admin'] >= 1):?>
            <li class="<?=isActive('stats_totle')?>"><a href="/dyba/stats_totle"><i class="fa fa-bar-chart"></i> <span>数据统计</span> </a></li>
            <?php endif?>
            <!-- <li class="<?=isActive('stats')?>"><a href="/dyba/stats"><i class="fa fa-bar-chart"></i> <span>活动统计</span></a></li> -->
            <li class="<?=isActive('qrcodes')?>"><a href="/dyba/qrcodes"><i class="fa fa-user"></i> <span>用户明细</span></a></li>
            <li class="treeview <?=isActive(array('zero', 'lab','area','rsync','cancle'))?>">
              <a href="#"><i class="fa fa-user"></i> <span>可选功能</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('zero')?>"><a href="/dyba/zero"><i class="fa fa-circle-o"></i> <span>积分清零</span> </a></li>
                <li class="<?=isActive('lab')?>"><a href="/dyba/lab"><i class="fa fa-circle-o"></i> <span>批量打标签</span> </a></li>
                <li class="<?=isActive('area')?>"><a href="/dyba/area"><i class="fa fa-circle-o"></i> <span>选择可参与地区</span> </a></li>
                <li class="<?=isActive('rsync')?>"><a href="/dyba/rsync"><i class="fa fa-circle-o"></i> <span>有赞积分同步</span> </a></li>
              </ul>
            </li>

            <!--<li class="treeview <?=isActive(array('stats_totle', 'history_trades','history_withdrawals','stats_goods'))?>">
              <a href="#"><i class="fa fa-bar-chart"></i> <span>数据统计</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('stats_totle')?>"><a href="/dyba/stats_totle"><i class="fa fa-circle-o"></i> <span>概况</span> </a></li>
                <li class="<?=isActive('history_tradess')?>"><a href="/fxba/history_trades"><i class="fa fa-circle-o"></i> <span>订单记录</span> </a></li>
                <li class="<?=isActive('history_withdrawals')?>"><a href="/fxba/history_withdrawals"><i class="fa fa-circle-o"></i> <span>提现记录</span> </a></li>
                <li class="<?=isActive('stats_goods')?>"><a href="/fxba/stats_goods"><i class="fa fa-circle-o"></i> <span>商品统计</span> </a></li>
              </ul>
            </li>
            -->
            <?php if($_SESSION['dyba']['admin'] >= 1):?>
              <li class="<?=isActive('admin')?>"><a href="/dyba/logins"><i class="fa fa-user"></i> <span>账号管理</span></a></li>
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

    <script src="/dyb/bootstrap/js/bootstrap.min.js"></script>
    <script src="/dyb/dist/js/app.min.js"></script>

    <script type="text/javascript" src="/dyb/plugins/simditor-2.2.4/scripts/module.js"></script>
    <script type="text/javascript" src="/dyb/plugins/simditor-2.2.4/scripts/hotkeys.js"></script>
    <script type="text/javascript" src="/dyb/plugins/simditor-2.2.4/scripts/simditor.js"></script>

    <script type="text/javascript" src="/dyb/plugins/datetimepicker/js/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="/dyb/plugins/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js"></script>

    <script src="/dyb/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <script src="/dyb/plugins/fastclick/fastclick.min.js"></script>

    <!-- memory_usage: {memory_usage} -->
    <!-- execution_time: {execution_time} -->

  </body>
</html>
