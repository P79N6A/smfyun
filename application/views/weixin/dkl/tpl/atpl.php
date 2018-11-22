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
    <title><?=$title?> | <?=$config['name']?> | 多客来</title>

    <?php require 'ahead.php';?>

    <link rel="stylesheet" href="/dkl/dist/css/skins/skin-green.min.css">
  </head>

  <script src="/dkl/plugins/jQuery/jQuery-2.1.4.min.js"></script>

  <body class="hold-transition skin-green sidebar-mini">
    <div class="wrapper">

      <!-- Main Header -->
      <header class="main-header">

        <!-- Logo -->
        <?php if(!$_SESSION['dkla']['tel']):?>
        <a href="/dkla/home" class="logo">
      <?php endif;?>
      <?php if($_SESSION['dkla']['tel']):?>
        <a href="/dkla/myhexiao/<?=$bid?>" class="logo">
      <?php endif;?>
          <span class="logo-mini"><b>多</b></span>
          <span class="logo-lg"><i class="fa fa-wechat"></i>&nbsp;<b>多客来</b></span>
        </a>

        <!-- Header Navbar -->
        <nav class="navbar navbar-static-top" role="navigation">
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">收起导航</span>
          </a>
          <?php if($agent!='android'&&$agent!='iphone'):?>
          <? else:?>
          <span style='display:inline-block;padding-top:15px;color:#f6f6f6;'><a href="http://shop1128733.koudaitong.com/v2/feature/nbo89d92" style="color:white;" target="_blank"> 点击这里查看多客来系统使用说明书</a></span>
          <? endif;?>
          <!-- Navbar Right Menu -->
          <div class="navbar-custom-menu">
            <?php if(!$_SESSION['dkla']['tel']):?>
            <ul class="nav navbar-nav">
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
                    <li><a href="/dkla/qrcodes"><i class="fa fa-users text-green"></i> <?=$todo['users']?> 个用户参与</a></li>
                    <li><a href="/dkla/qrcodes?ticket=1"><i class="fa fa-heart text-green"></i> <?=$todo['tickets']?> 个用户已生成海报</a></li>
                    <?php endif?>

                    <?php if($todo['items']):?>
                    <li><a href="/dkla/orders"><i class="fa fa-shopping-cart text-green"></i> <?=$todo['items']?> 条待处理兑换</a></li>
                    <?php endif?>

                    </ul>
                  </li>
                  <li class="footer">
                    <a href="/dkla/orders">查看所有</a>
                  </li>
                </ul>
              </li>

              <!-- User Account Menu -->
              <li class="dropdown user user-menu">
                <!-- Menu Toggle Button -->

                <a href="/dkla/logout" class="dropdown-toggle">
                  <span class="hidden-xs">退出系统 <i class="fa fa-sign-out"></i></span>
                </a>

              </li>
            </ul>
            <?php endif;?>
          </div>
        </nav>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar Menu -->
          <ul class="sidebar-menu">
           <?php if(!$_SESSION['dkla']['tel']):?>
            <li class="header">主菜单</li>
            <li class="">
              <a href="#"><i class="fa fa-book"></i> <span>使用说明及系统更新</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class=""><a target="_blank" href="https://shop1128733.koudaitong.com/v2/feature/nbo89d92"><i class="fa fa-circle-o"></i> <span>使用说明书</span> </a></li>
                <li class=""><a target="_blank" href="https://wap.koudaitong.com/v2/feature/x14nli85"><i class="fa fa-circle-o"></i> <span>系统更新日志</span> </a></li>
              </ul>
            </li>
            <li class="<?=isActive('home')?>"><a href="/dkla/home"><i class="fa fa-dashboard"></i> <span>基础设置</span></a></li>
           <li class="treeview <?=isActive(array('veri', 'orders'))?>">
              <a href="#"><i class="fa fa-cog"></i> <span>核销管理</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('veri')?>"><a href="/dkla/veri"><i class="fa fa-circle-o"></i> <span>核销员管理</span> </a></li>
                <li class="<?=isActive('orders')?>"><a href="/dkla/orders"><i class="fa fa-circle-o"></i> <span>核销记录</span> </a></li>
              </ul>
            </li>
            <li class="<?=isActive('items')?>"><a href="/dkla/items"><i class="fa fa-gift"></i> <span>奖品管理</span> </a></li>
            <li class="<?=isActive('stats_totle')?>"><a href="/dkla/stats_totle"><i class="fa fa-bar-chart"></i> <span>数据统计</span> </a></li>
            <li class="<?=isActive('qrcodes')?>"><a href="/dkla/qrcodes"><i class="fa fa-user"></i> <span>用户明细</span></a></li>
            <li class="treeview <?=isActive(array('zero', 'lab','area','rsync','cancle','hb_check'))?>">
              <a href="#"><i class="fa fa-plus-square"></i> <span>高级应用</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?=isActive('zero')?>"><a href="/dkla/zero"><i class="fa fa-circle-o"></i> <span>积分清零</span> </a></li>
                <li class="<?=isActive('lab')?>"><a href="/dkla/lab"><i class="fa fa-circle-o"></i> <span>批量打标签</span> </a></li>
                <li class="<?=isActive('area')?>"><a href="/dkla/area"><i class="fa fa-circle-o"></i> <span>选择可参与地区</span> </a></li>
                <li class="<?=isActive('rsync')?>"><a href="/dkla/rsync"><i class="fa fa-circle-o"></i> <span>有赞积分同步</span> </a></li>
                <li class="<?=isActive('cancle')?>"><a href="/dkla/cancle"><i class="fa fa-circle-o"></i> <span>取消关注后扣积分</span> </a></li>
                <li class="<?=isActive('hb_check')?>"><a href="/dkla/hb_check"><i class="fa fa-circle-o"></i> <span>红包发送审核</span> </a></li>
              </ul>
            </li>
            <?php if($_SESSION['dkla']['admin'] >= 1):?>
              <li class="<?=isActive('logins')?>"><a href="/dkla/logins"><i class="fa fa-credit-card"></i> <span>账号管理</span></a></li>
            <?php endif?>
            <?php  endif;?>
            <?php if($_SESSION['dkla']['tel']):?>
            <li class="<?=isActive('dohexiaos')?>"><a href="/dkla/dohexiaos/<?=$bid?>"><i class="fa fa-user"></i> <span>我要核销</span></a></li>
            <li class="<?=isActive('myhexiao')?>"><a href="/dkla/myhexiao/<?=$bid?>"><i class="fa fa-bar-chart"></i> <span>我的核销记录</span> </a></li>
            <?php  endif;?>
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

    <script src="/dkl/bootstrap/js/bootstrap.min.js"></script>
    <script src="/dkl/dist/js/app.min.js"></script>

    <script type="text/javascript" src="/dkl/plugins/simditor-2.2.4/scripts/module.js"></script>
    <script type="text/javascript" src="/dkl/plugins/simditor-2.2.4/scripts/hotkeys.js"></script>
    <script type="text/javascript" src="/dkl/plugins/simditor-2.2.4/scripts/simditor.js"></script>

    <script type="text/javascript" src="/dkl/plugins/datetimepicker/js/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="/dkl/plugins/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js"></script>

    <script src="/dkl/plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <script src="/dkl/plugins/fastclick/fastclick.min.js"></script>
  </body>
</html>
