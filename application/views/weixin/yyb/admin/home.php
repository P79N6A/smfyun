<style>
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
           <li id="cfg_yz_li"><a href="#cfg_yz" data-toggle="tab">绑定有赞</a></li>
          <li id="cfg_wx_li"><a href="#cfg_wx" data-toggle="tab">微信参数</a></li>
          <li id="cfg_account_li"><a href="#cfg_account" data-toggle="tab">密码修改</a></li>
        </ul>

        <?php
         if (!$_POST) $active = 'yz';
        if ($_POST['cfg']) $active = 'wx';
        if ($_POST['menu']) $active = 'menu';
        if ($_POST['text']) $active = 'text';
        if ($_POST['area']) $active = 'area';

        if (isset($_POST['password'])) $active = 'account';
        ?>

        <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
        </script>

        <div class="tab-content">
          <div class="tab-pane" id="cfg_yz">
            <form role="form" method="post">
              <div class="box-body">
              <?php if($oauth==1):?>
                  <div class="lab4">
                    <a href='/yyba/oauth'><button type="button" class="btn btn-warning">点击一键授权有赞</button></a>
                  </div>
                  <br>
                <?php else:?>
                <div class="lab4">
                  <a href='/yyba/oauth'><button type="button" class="btn btn-warning">点击重新授权有赞</button></a>
                </div>
                <?php endif?>
                </div>
            </form>
          </div>
          <div class="tab-pane" id="cfg_wx">

            <?php if ($result['ok'] > 0):?>
              <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>微信配置保存成功!</div>
            <?php endif?>
            <?php if ($result['err']):?>
              <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err']?></div>
            <?php endif?>
            <!-- <div class="callout callout-success">
              <p><b>模板消息编号：OPENTM406626567</b></p>
            </div> -->
            <!-- form start -->
            <form role="form" method="post" enctype='multipart/form-data'>
              <div class="box-body">
                <div class="form-group">
                  <label for="name">微信公众号名称</label>
                  <input type="text" class="form-control" id="name" placeholder="输入公众号名称" maxlength="20" name="cfg[name]" value="<?=$config['name']?>">
                </div>

                <div class="form-group">
                  <label for="appid">微信公众号App Id（填写后不可修改）</label>
                  <input type="text" class="form-control" id="appid" placeholder="输入 App Id" maxlength="18" name="cfg[appid]" value="<?=$config['appid']?>"<?php if($config['appid']) echo 'readonly=""'?>>
                </div>

                <div class="form-group">
                  <label for="appsecret">微信公众号App Secret</label>
                  <input type="text" class="form-control" id="appsecret" placeholder="输入 App Secret" maxlength="32" name="cfg[appsecret]" value="<?=$config['appsecret']?>">
                </div>
                <div class="form-group">
                  <label for="mbtpl">模板消息ID（行业：IT科技 - 互联网|电子商务，模板消息标题「新预约通知」，模板消息编号：OPENTM406122078）</label>
                  <input type="text" class="form-control" id="mbtpl" placeholder="输入模板ID" maxlength="64" name="cfg[mbtpl]" value="<?=$config['mbtpl']?>">
                </div>

              <?php
                    //默认头像
              if ($result['2dimage']):
                ?>
              <div class="form-group">
                <a href="/yyba/images/cfg/<?=$result['2dimage']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/yyba/images/cfg/<?=$result['2dimage']?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
              </div>
            <?php endif?>

            <div class="form-group">
              <label for="pic">公众号二维码（未关注公众号的用户预约时会提示用户扫码关注）</label>
              <input type="file" class="form-control" id="pic" name="pic" accept="image/jpeg">
              <p class="help-block">只能为 JPEG 格式，正方形，最大不超过 100K。</p>
            </div>

              </div><!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-success">保存微信配置</button>
              </div>
            </form>
          </div>

<div class="tab-pane" id="cfg_account">

  <?php if ($result['ok2'] > 0):
  $_SESSION['xgba'] = null;
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

