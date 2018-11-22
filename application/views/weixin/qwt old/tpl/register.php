<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <link href="../qwt/images/logo.ico" rel="Shortcut Icon">

    <title>注册|@有呗</title>

    <link href="/qwt/css/style.css" rel="stylesheet">
    <link href="/qwt/css/style-responsive.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
</head>

<body class="login-body">

<div class="container">

    <form class="form-signin" method="post">
        <div class="form-signin-heading text-center">
            <h1 class="sign-title">注册账号</h1>
            <img src="/qwt/images/logo.png" alt=""/>
        </div>
        <?php if($error):?>
          <div class="alert alert-danger alert-dismissable"><?=$error?></div>
        <?php endif?>
        <div class="login-wrap">
            <p>注册个人信息</p>
            <input name='userid' type="text" autofocus="" placeholder="手机号" class="form-control">
            <input name='pass' type="text" autofocus="" placeholder="密码" class="form-control">
            <input name='code' type="text" autofocus="" placeholder="邀请码" class="form-control">
            <button type="submit" class="btn btn-lg btn-login btn-block">
                <i class="fa fa-check"></i>
            </button>

            <div class="registration">
                已经注册.
                <a href="/qwta/login" class="">
                    登录
                </a>
            </div>

        </div>

    </form>

</div>



<!-- Placed js at the end of the document so the pages load faster -->

<!-- Placed js at the end of the document so the pages load faster -->
<script src="/qwt/js/jquery-1.10.2.min.js"></script>
<script src="/qwt/js/bootstrap.min.js"></script>
<script src="/qwt/js/modernizr.min.js"></script>

</body>
</html>
