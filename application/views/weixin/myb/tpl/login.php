<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>芝麻开门 | 美业宝@神码浮云</title>

    <?php require 'ahead.php';?>

  </head>
  <body class="hold-transition login-page">
    <div class="login-box">
      <div class="login-logo">
        <b>美业宝</b> @神码浮云
      </div><!-- /.login-logo -->
      <div class="login-box-body">

        <?php if($error):?>
          <div class="alert alert-danger alert-dismissable"><?=$error?></div>
        <?php endif?>

        <p class="login-box-msg">输入密码登录</p>
        <form method="post">
          <div class="form-group has-feedback">
            <input name="username" maxlength="20" type="text" class="form-control" placeholder="用户名">
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input name="password" maxlength="32" type="password" class="form-control" placeholder="密码">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <div class="row">
            <div class="col-xs-8">
            </div><!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">登录</button>
            </div><!-- /.col -->
          </div>
        </form>

      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <!-- jQuery 2.1.4 -->
    <script src="/wdy/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <!-- Bootstrap 3.3.5 -->
    <script src="/wdy/bootstrap/js/bootstrap.min.js"></script>
    <!-- iCheck -->
    <script src="/wdy/plugins/iCheck/icheck.min.js"></script>
    <script>
      $(function () {
        $('input').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });
      });
    </script>
  </body>
</html>
