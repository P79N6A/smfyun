<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <link rel="shortcut icon" href="#" type="image/png">

    <title>有呗|@神码浮云</title>

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
            <h1 class="sign-title">Sign In</h1>
            <img src="/qwt/images/login-logo.png" alt=""/>
        </div>
        <?php if($error):?>
          <div class="alert alert-danger alert-dismissable"><?=$error?></div>
        <?php endif?>
        <div class="login-wrap">
            <input name="username" type="text" class="form-control" placeholder="手机号" autofocus >
            <input name="password" type="password" class="form-control" placeholder="Password">

            <button class="btn btn-lg btn-login btn-block" type="submit">
                <i class="fa fa-check"></i>
            </button>
            <div class="registration">
                Not a member yet?
                <a class="" href="/qwta/register">
                    Signup
                </a>
            </div>
        </div>

        <!-- Modal -->

        <!-- modal -->

    </form>

</div>



<!-- Placed js at the end of the document so the pages load faster -->

<!-- Placed js at the end of the document so the pages load faster -->
<script src="/qwt/js/jquery-1.10.2.min.js"></script>
<script src="/qwt/js/bootstrap.min.js"></script>
<script src="/qwt/js/modernizr.min.js"></script>

</body>
</html>
