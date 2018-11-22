<style>
.nav-tabs-custom>.nav-tabs>li.active {
  border-top-color: #00a65a;
}
.nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: #00a65a;
  }
  .reduce,.add{
    font-size: 14px;
    position: relative;
    bottom: 10px;
  }
  .add{
    margin-left: 20px;
    margin-right: 30px;
  }
  .loc{
    margin-top: 5px;
    margin-bottom: 5px;
  }
</style>

<section class="content-header">
  <h1>
    基础设置
    <small>核心参数配置</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">基础设置</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">

          <ul class="nav nav-tabs">
            <li id="cfg_wx_li"><a href="#cfg_wx" data-toggle="tab">微信参数</a></li>
            <li id="cfg_account_li"><a href="#cfg_account" data-toggle="tab">密码修改</a></li>
          </ul>

          <?php
          if (!$_POST || $_POST['wx']) $active = 'wx';
          if (isset($_POST['password'])) $active = 'account';
          ?>

          <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
          </script>

          <div class="tab-content">
            <div class="tab-pane" id="cfg_wx">

                <?php if ($result['ok'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>微信配置保存成功!</div>
                <?php endif?>

                <?php if ($result['err1']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err1']?></div>
                <?php endif?>

                <!-- form start -->
                <form role="form" method="post" enctype="multipart/form-data">
                  <div class="box-body">

                   <!--  <div class="form-group">
                      <label for="name">微信公众号名称</label>
                      <input type="text" class="form-control" id="name" placeholder="输入公众号名称" maxlength="20" name="cfg[name]" value="<?=$config['name']?>">
                    </div> -->

                    <div class="form-group">
                      <label for="appid">微信公众号App Id<!--(填写后不可修改） --></label>
                      <input type="text" class="form-control" id="appid" placeholder="输入 App Id" maxlength="18" name="cfg[appid]" value="<?=$config['appid']?>"<?php if($config['appid']) echo 'readonly=""'?>>
                    </div>

                    <div class="form-group">
                      <label for="appsecret">微信公众号App Secret</label>
                      <input type="text" class="form-control" id="appsecret" placeholder="输入 App Secret" maxlength="32" name="cfg[appsecret]" value="<?=$config['appsecret']?>" <?php if($config['appsecret']) echo 'readonly=""'?>>
                    </div>

                  </div>

                 <!--  <div class="box-footer">
                    <button type="submit" class="btn btn-success">保存微信配置</button>
                  </div> -->
                </form>
            </div>

            <div class="tab-pane" id="cfg_account">

                <?php if ($result['ok4'] > 0):
                $_SESSION['snsa'] = null;
                ?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>新密码已生效，请重新登录</div>
                <?php endif?>

                <?php if ($result['err4']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err4']?></div>
                <?php endif?>

                <form role="form" method="post">
                  <div class="box-body">

                  <div class="form-group">
                      <label for="password">旧密码</label>
                      <input type="password" class="form-control" id="password" placeholder="请输入旧密码" maxlength="16" name="password">
                  </div>

                  <div class="form-group">
                      <label for="newpassword">新密码</label>
                      <input type="password" class="form-control" id="newpassword" placeholder="请输入新密码" maxlength="16" name="newpassword">
                  </div>

                  <div class="form-group">
                      <label for="newpassword2">重复新密码</label>
                      <input type="password" class="form-control" id="newpassword2" placeholder="请再次输入新密码" maxlength="16" name="newpassword2">
                  </div>

                  <div class="box-footer">
                    <input type="hidden" name="yz" value="1">
                    <button type="submit" class="btn btn-success">修改登录密码</button>
                  </div>
                </form>
            </div>

          </div>
      </div>

    </div><!--/.col (left) -->

</section><!-- /.content -->

