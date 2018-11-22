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
    <title><?=$title?> | <?=$config['name']?> | 神码云直播</title>

    <?php require 'ahead.php';?>

    <link rel="stylesheet" href="/wdy/dist/css/skins/skin-red.min.css">
  </head>

  <script src="/wdy/plugins/jQuery/jQuery-2.1.4.min.js"></script>

  <body class="hold-transition skin-red sidebar-mini">
    <div class="wrapper">

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <a href="/wzba/home" class="logo">
          <span class="logo-mini"><b>神</b></span>
          <span class="logo-lg"><i class="fa fa-wechat"></i>&nbsp;<b>神码云直播</b></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation"><div style="font-size: 16px;margin-top: 13px;color: #fff;position: absolute;left: 50px; ">公告：为了配合有赞接口的调整，点击菜单【基础设置】->【绑定有赞】->【点击重新授权有赞】来重新授权，以保证系统的正常使用。</div>
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">收起导航</span>
          </a>
          <?php if($agent!='android'&&$agent!='iphone'):?>
          <!-- <span style='display:inline-block;padding-top:15px;color:#f6f6f6;'><a href="https://wap.koudaitong.com/v2/showcase/feature?alias=j2avl2px" style="color:white;" target="_blank">首次使用，请 点击这里查看全员微直播系统使用说明书及功能更新日志</a></span> -->
          <? else:?>
          <span style='display:inline-block;padding-top:15px;color:#f6f6f6;'><a href="https://h5.koudaitong.com/v2/feature/ksdvyl88" style="color:white;" target="_blank">点击这里查看微直播系统使用说明书</a></span>
          <? endif;?>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
              <!-- User Account Menu -->
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->
                <?php if($_SESSION['wzba']['bid']&&$todo['flag']==1):?>
                <a href="/wzba/logout" class="dropdown-toggle">
                  <span class="hidden-xs">退出系统 <i class="fa fa-sign-out"></i></span>
                <?php endif?>
                <?php if($_SESSION['wzba']['aid']&&$todo['flag']==2):?>
                <a href="/wzbb/logout" class="dropdown-toggle">
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

            <!--
            <li class="treeview <?=isActive(array('items', 'orders'))?>">
              <a href="#"><i class="fa fa-shopping-cart"></i> <span>奖品设置</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('orders')?>"><a href="/wzba/orders"><i class="fa fa-circle-o"></i> <span>兑换记录</span> </a></li>
                <li class="<?=isActive('items')?>"><a href="/wzba/items"><i class="fa fa-circle-o"></i> <span>奖品管理</span> </a></li>
              </ul>
            </li>
            -->

            <!-- <li class="<?=isActive('stats')?>"><a href="/wzba/stats"><i class="fa fa-bar-chart"></i> <span>活动统计</span></a></li> -->
            <?php if($_SESSION['wzba']['bid']&&$todo['flag']==1):?>
            <li class="treeview">
              <a href="#"><i class="fa fa-user"></i> <span>使用说明及系统更新</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class=""><a target="_blank" href="https://h5.youzan.com/v2/feature/mfvuxdo2"><i class="fa fa-circle-o"></i> <span>使用说明书</span> </a></li>
              </ul>
            </li>
            <li class="treeview <?=isActive(array('information','flowcenter','flowcenter_history','buymentrecord'))?>">
              <a href="#"><i class="fa fa-dashboard"></i> <span>会员中心</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('information')?>"><a href="/wzba/information"><i class="fa fa-circle-o"></i> <span>基本信息</span> </a></li>
                <li class="<?=isActive(array('flowcenter','flowcenter_history'))?>"><a href="/wzba/flowcenter"><i class="fa fa-circle-o"></i> <span>直播流量中心</span> </a></li>
                <li class="<?=isActive('buymentrecord')?>"><a href="/wzba/buymentrecord"><i class="fa fa-circle-o"></i> <span>购买记录</span> </a></li>
              </ul>
            </li>
            <li class="<?=isActive('home')?>">
              <a href="/wzba/home">
                <i class="fa fa-dashboard"></i>
                <span>基础设置</span>
              </a>
            </li>
            <li class="treeview <?=isActive(array('setgoods','other_setgood'))?>">
              <a href="#"><i class="fa fa-dashboard"></i> <span>直播商品管理</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('setgoods')?>"><a href="/wzba/setgoods1"><i class="fa fa-circle-o"></i> <span>有赞商品管理</span> </a></li>
                <li class="<?=isActive('other_setgood')?>"><a href="/wzba/other_setgood"><i class="fa fa-circle-o"></i> <span>其他商品管理</span> </a></li>
              </ul>
            </li>
            <!-- <li class="<?=isActive('marketing')?>">
              <a href="/wzba/marketing">
                <i class="fa fa-dashboard"></i>
                <span>营销模块</span>
              </a>
            </li> -->
            <li class="treeview <?=isActive(array('marketing','lottery','lottery_history'))?>">
              <a href="#"><i class="fa fa-dashboard"></i> <span>营销模块</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('marketing')?>"><a href="/wzba/marketing"><i class="fa fa-circle-o"></i> <span>首次进直播间送优惠券</span> </a></li>
                <li class="<?=isActive(array('lottery','lottery_history'))?>"><a href="/wzba/lottery"><i class="fa fa-circle-o"></i> <span>幸运抽奖轮盘</span> </a></li>
              </ul>
            </li>
            <li class="<?=isActive('qrcodes')?>"><a href="/wzba/qrcodes"><i class="fa fa-user"></i> <span>用户管理</span></a></li>
            <li class="<?=isActive('analyze')?>"><a href="/wzba/analyze"><i class="fa fa-dashboard"></i> <span>直播分析</span></a></li>
            <li class="treeview <?=isActive(array('download','download_ios'))?>">
              <a href="#"><i class="fa fa-dashboard"></i> <span>商户端APP下载</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('download')?>"><a href="/wzba/download"><i class="fa fa-circle-o"></i> <span>安卓端APK下载</span> </a></li>
                <li class="<?=isActive('download_ios')?>"><a href="/wzba/download_ios"><i class="fa fa-circle-o"></i> <span>IOS端应用下载</span> </a></li>
              </ul>
            </li>
              <?php endif?>
              <?php if($_SESSION['wzba']['admin'] >= 1&&$_SESSION['wzba']['aid']&&$todo['flag']==2):?>
              <li class="<?=isActive('logins')?>"><a href="/wzbb/logins"><i class="fa fa-user"></i> <span>账号管理</span></a></li>
              <li class="<?=isActive('admins')?>"><a href="/wzbb/admins"><i class="fa fa-user"></i> <span>代理商管理</span></a></li>
              <li class="<?=isActive('sales')?>"><a href="/wzbb/sales"><i class="fa fa-user"></i> <span>销售管理</span></a></li>
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
