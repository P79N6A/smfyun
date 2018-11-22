<!doctype html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="renderer" content="webkit">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <link href="../qwt/images/logo.ico" rel="Shortcut Icon">

    <title>重置密码|神码浮云营销应用平台</title>

    <link href="/qwt/css/style.css" rel="stylesheet">
    <link href="/qwt/css/style-responsive.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
    .input-group-btn{
        bottom: 7px;
    }
    .click_change{
        height: 39px;
    }
    .error,.no_error{
        display: none;
    }
    </style>
</head>

<body class="login-body">

<div class="container">

    <form class="form-signin" method="post">
        <div class="form-signin-heading text-center">
            <h1 class="sign-title">忘记密码</h1>
            <img src="/qwt/images/logo.png" alt=""/>
        </div>
        <?php if($error):?>
          <div class="alert alert-danger alert-dismissable"><?=$error?></div>
        <?php endif?>
        <?php if($success):?>
          <div class="alert alert-success alert-dismissable"><?=$success?></div>
        <?php endif?>
        <div class="alert alert-success alert-dismissable no_error"></div>
        <div class="alert alert-danger alert-dismissable error"></div>
        <div class="login-wrap">
            <p style="text-align:center">重置密码</p>
            <div class="input-group">
              <span style='color:red'>*</span>
              <input style="display: inline-block;width: 95%;float: right;" id='tel' name='userid' type="text" autofocus="" placeholder="手机号" class="form-control">
              <span class="input-group-btn">
                <button class="btn btn-default click_change" type="button">
                  发送验证码
                </button>
              </span>
            </div>
            <!-- <input name='userid' type="text" autofocus="" placeholder="手机号" class="form-control"> -->
            <span style='color:red'>*</span>
            <input style="display: inline-block;width: 95%;" name='identify' type="text" autofocus="" placeholder="短信验证码" class="form-control">
            <span style='color:red'>*</span>
            <input style="display: inline-block;width: 95%;" name='pass' type="text" autofocus="" placeholder="新密码" class="form-control">
            <span style='color:red'>&nbsp;&nbsp;</span>
            <!-- <input style="display: inline-block;width: 95%;" name='code' type="text" autofocus="" placeholder="邀请码" class="form-control" onkeyup="value=value.replace(/[\W]/g,'') " onbeforepaste="clipboardData.setData('text',clipboardData.getData('text').replace(/[^\d]/g,''))"> -->
            <div style="text-align:center;color:red">带*为必填项</div>
            <!-- <div id="embed-captcha"></div>
            <p id="wait" class="show">正在加载验证码......</p>
            <p id="notice" class="hide">请先完成验证</p> -->
            <button type="submit" class="btn btn-lg btn-login btn-block">
                <i class="fa fa-check"></i>
            </button>

            <!-- <div class="registration">
                已经注册?
                <a href="/qwta/login" class="">
                    登录
                </a>
            </div> -->

        </div>

    </form>

</div>



<!-- Placed js at the end of the document so the pages load faster -->
<script src="/qwt/js/gt.js"></script>
<!-- Placed js at the end of the document so the pages load faster -->
<script src="/qwt/js/jquery-1.10.2.min.js"></script>
<script src="/qwt/js/bootstrap.min.js"></script>
<script src="/qwt/js/modernizr.min.js"></script>
<script>
    a = 0;
    $('.click_change').click(function(event) {
        if(a<0||a==0){
            $.ajax({
                // 获取id，challenge，success（是否启用failback）
                url: "/qwta/forget?createcode=1&tel="+$('#tel').val(), // 加随机数防止缓存
                type: "get",
                dataType: "json",
                success: function (res) {
                    if(res.error=='no_error'){
                        //没有错误进入60s倒计时
                        a = 60;
                        var timer = setInterval(function(){
                            a--;
                            $('.click_change').text(a+'s后获取');
                            if(a<0||a==0){
                                clearInterval(timer);
                                $('.click_change').text('发送验证码');
                            }
                        },1000);
                        $('.no_error').fadeIn(500);
                        $('.no_error').text(res.content);
                        setTimeout(function(){
                            $('.no_error').fadeOut(500);
                        },5000);
                    }else{
                        $('.error').fadeIn(500);
                        $('.error').text(res.content);
                        setTimeout(function(){
                            $('.error').fadeOut(500);
                        },5000);
                    }
                }
            });
        }
    });
    var handlerEmbed = function (captchaObj) {
        $("#embed-submit").click(function (e) {
            var validate = captchaObj.getValidate();
            if (!validate) {
                $("#notice")[0].className = "show";
                setTimeout(function () {
                    $("#notice")[0].className = "hide";
                }, 2000);
                e.preventDefault();
            }
        });
        // 将验证码加到id为captcha的元素里，同时会有三个input的值：geetest_challenge, geetest_validate, geetest_seccode
        captchaObj.appendTo("#embed-captcha");
        captchaObj.onReady(function () {
            $("#wait")[0].className = "hide";
        });
        // 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
    };
    $.ajax({
        // 获取id，challenge，success（是否启用failback）
        url: "/qwta/register?StartCaptchaServlet=1&t=" + (new Date()).getTime(), // 加随机数防止缓存
        type: "get",
        dataType: "json",
        success: function (data) {
            console.log(data);
            // 使用initGeetest接口
            // 参数1：配置参数
            // 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
            initGeetest({
                gt: data.gt,
                challenge: data.challenge,
                new_captcha: data.new_captcha,
                product: "embed", // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
                offline: !data.success // 表示用户后台检测极验服务器是否宕机，一般不需要关注
                // 更多配置参数请参见：http://www.geetest.com/install/sections/idx-client-sdk.html#config
            }, handlerEmbed);
            setTimeout(function(){
              $('.geetest_detect').css({
                'width': '100%'
              });
              $('.geetest_wind').css({
                'width': '100%'
              });
              $('.geetest_holder').css({
                'width': '100%'
              });
            },500);
        }
    });
</script>
</body>
</html>
