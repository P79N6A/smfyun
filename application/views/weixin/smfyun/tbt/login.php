<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Admin Template">
    <meta name="keywords" content="admin dashboard, admin, flat, flat ui, ui kit, app, web app, responsive">
    <link rel="shortcut icon" href="img/ico/favicon.png">
    <title>登录</title>

    <!-- Base Styles -->
    <link href="../qwt/tbt/css/style.css" rel="stylesheet">
    <link href="../qwt/tbt/css/style-responsive.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.min.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <?php if($_SERVER['HTTP_HOST']=='jfb.dev.smfyun.com'):?>
    <script type="text/javascript" src="https://www.w3cways.com/demo/vconsole/vconsole.min.js?v=2.2.0"></script>
    <?php endif?>
    <style type="text/css">
    .bar{
      height: 20px;
      margin-bottom: 10px;
      font-size: 16px;
      font-weight: bold;
      color: #666;
    }
    .left{
      width: calc(50% - 4em);
      height: 20px;
      display: inline-block;
      border-bottom: 2px solid #dedede;
    }
    .middle{
    width: 8em;
    display: inline-block;
    text-align: center;
    /* margin-bottom: 10px; */
    height: 20px;
    line-height: 20px;
    }
    .right{
    display: inline-block;
    width: calc(50% - 4em);
    height: 20px;
    border-bottom: 2px solid #dedede;
    }
    .top{
      width: 100%;
    }
    .logo1{
      width: 6em;
      height: 6em;
      position: absolute;
      left: 50%;
      margin-left: -3em;
      margin-top: -6em;
      border-radius: 3em;
    }
    .passwdbox{
      position: relative;
    }
    #tooglepasswd{
      position: absolute;
      top: 0;
      right: 0;
      height: 42px;
    }
    #tooglepasswd img{
      height: 42px;
      padding: 6px;
    }
    </style>


</head>

  <body class="login-body">
      <img class="top" src="../qwt/tbt/img/top2.png">
      <!-- <img class="logo1" src="../qwt/tbt/img/logo.png"> -->

      <!-- <div class="login-logo">
          <img src="../qwt/tbt/img/login_logo.png" alt=""/>
      </div>

      <h2 class="form-heading">登录</h2> -->
      <div class="container log-row">
          <form class="form-signin" method="post" id='onlogin' onsubmit="return check()">
              <div class="login-wrap">
                  <?php if($result['error']):?>
                    <p id="warn" style="color:#ff7e7e;"><?=$result['error']?></p>
                  <?php endif?>
                  <input id="user" name="user" value="<?=$user->rem==1?$user->telphone:''?>" type="number" class="form-control" placeholder="请输入您的电话号码" autofocus>
                  <div class="passwdbox">
                    <input id="passwd" name="passwd" value="<?=$user->rem==1?$user->password:''?>" type="password" class="form-control" placeholder="请输入您的密码"><a id="tooglepasswd"><img src="../qwt/tbt/img/eyeclose.png"><img src="../qwt/tbt/img/eyeopen.png" style="display:none"></a>
                  </div>
                  <button class="btn btn-lg btn-success btn-block" type="submit" style="background-color:#ff9d00;border-color:#ff9d00;border-radius:4em;">登录</button>
                  <!-- <div class="login-social-link">
                      <a href="index.html" class="facebook">
                          Facebook
                      </a>
                      <a href="index.html" class="twitter">
                          Twitter
                      </a>
                  </div> -->
                  <label class="checkbox-custom check-success">
                      <input id="rem" type="checkbox" name="rem" value="1" <?=$user->rem==1?'checked="checked"':''?>> <label for="rem">记住密码</label>
                      <!-- <input id="tooglepasswd" type="checkbox"> <label for="tooglepasswd">显示密码</label> -->
                      <a class="forforgot pull-right" data-toggle="modal" href="#forgotPass">忘记密码</a>
                  </label>

                  <div class="registration" style="text-align:center;margin-bottom:10px;">
                      还没有注册？
                      <a class="" href="registration">
                          立即注册
                      </a>
                  </div>
                  <div class="registration" style="text-align:center;">
                      密码太简单？
                      <a class="forchange" data-toggle="modal" href="#forgotPass">
                          修改密码
                      </a>
                  </div>

              </div>

          </form>
              <!-- Modal -->
              <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="forgotPass" class="modal fade">
                  <div class="modal-dialog">
                      <div class="modal-content">
                          <div class="modal-header" style="background-color:#352826;border-bottom-color:#fdd303;">
                              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                              <h4 class="fadetitle modal-title" style="color:#f28414;font-weight:bold;">忘记密码 ?</h4>
                          </div>
                          <div class="modal-body telEnter">
                              <p>输入您的姓名</p>
                              <input id="name" type="text" name="name" placeholder="姓名" autocomplete="off" class="form-control placeholder-no-fix" style="background-color:#fff;border:1px solid #ddd;color:#333;">
                              <p>输入您的手机号</p>
                              <input id="tel" type="number" name="tel" placeholder="手机号" autocomplete="off" class="form-control placeholder-no-fix" style="background-color:#fff;border:1px solid #ddd;color:#333;">
                              <p>输入您的新密码</p>
                              <input id="password" type="password" placeholder="新密码" class="form-control placeholder-no-fix" style="background-color:#fff;border:1px solid #ddd;color:#333;">
                              <p>再输入一次新密码</p>
                              <input id="passwordconfirm" type="password" name="password" placeholder="确认新密码" class="form-control placeholder-no-fix" style="background-color:#fff;border:1px solid #ddd;color:#333;">
                          </div>
                          <div class="modal-footer">
                              <button data-dismiss="modal" id="cancel" class="btn btn-default" type="button" style="margin-bottom:0;">取消</button>
                              <button class="posttel btn btn-success" type="button" style="background-color:#ff9d00;border-color:#ff9d00;">提交</button>
                              <!-- <button class="changepass btn btn-success" type="button" style="display:none;background-color:#ff9d00;border-color:#ff9d00;">修改</button> -->
                          </div>
                      </div>
                  </div>
              </div>
              <!-- modal -->

      </div>
      <div class="bar">
        <div class="left"></div><div class="middle">服务支持</div><div class="right"></div>
      </div>
      <div class="container log-row" style="margin-bottom:0;text-align:center;">
        <img style="width:8em;" src="../qwt/tbt/img/qrcode.png">
      </div>
      <h4 style="text-align:center;color:#999;font-size:14px;" class="form-heading">全国统一客服：<a href="tel:4008787616">400-8787-616</a></h4>
      <!--jquery-1.10.2.min-->
      <script src="../qwt/tbt/js/jquery-1.11.1.min.js"></script>
      <!--Bootstrap Js-->
      <script src="../qwt/tbt/js/bootstrap.min.js"></script>
      <script src="../qwt/tbt/js/jquery.toggle-password.js"></script>
      <script type="text/javascript">
      //显示密码
$(function(){
  $('#passwd').togglePassword({
    el: '#tooglepasswd'
  });
});
$('#tooglepasswd').click(function(){
  $('#tooglepasswd img').toggle();
})
      function check(){
        if($('#rem').is(':checked')==true){
          $('#rem').val(1);
        }else{
          $('#rem').val(0);
        }
        localStorage.setItem("rem", $('#rem').val());
        // alert(localStorage.getItem("rem"));
        if($('#user').val()&&$('#passwd').val()&&$('#rem').val()==1){
          localStorage.setItem("user", $('#user').val());
          localStorage.setItem("passwd", $('#passwd').val());
          var timestamp = Math.ceil(Date.parse(new Date())/1000);
          localStorage.setItem("time", timestamp+30*24*3600);
        }
        return true;
      }
      $(document).ready(function() {
        if($('#rem').is(':checked')==true){
          $('#rem').val(1);
        }else{
          $('#rem').val(0);
        }
        // alert(localStorage.getItem("user"));
        // alert(localStorage.getItem("passwd"));
        // alert(localStorage.getItem("rem"));
        // alert(localStorage.getItem("time"));
        // alert(Math.ceil(Date.parse(new Date())/1000));
        // alert($('#user').val());
        // alert($('#passwd').val());
        // alert($('#rem').val());
        if(!$('#user').val()&&!$('#passwd').val()&&$('#rem').val()==0){
          var rem = localStorage.getItem("rem");
          if(rem == 1){
            var time = localStorage.getItem("time");
            var now = Math.ceil(Date.parse(new Date())/1000);
            if(now<=time){
              $('#user').val(localStorage.getItem("user"));
              $('#passwd').val(localStorage.getItem("passwd"));
              $('#rem').val(localStorage.getItem("rem"));
              $('#rem').prop('checked',true);
            }
          }
        }
        // var error = '<?=$result['error']?>';
        // alert($('#user').val());
        // alert($('#passwd').val());
        // alert(error);
        // if($('#user').val()&&$('#passwd').val()&&error!=''){
        //   document.getElementById("onlogin").submit()
        // }
      });
      $('.posttel').click(function(){
        var tel = $('#tel').val();
        var name = $('#name').val();
        var pass1 = $('#password').val();
        var pass2 = $('#passwordconfirm').val();
        if (pass1 == pass2) {
          $.ajax({
            url: 'login',
            type: 'post',
            dataType: 'json',
            data: {tel: tel,editpwd: pass2,name: name},
          })
          .done(function(res){
            if (res.state==1) {
              alert('密码修改成功');
              $('#cancel').click();
            }else{
              alert('手机号或姓名错误，请重试！');
            }
          })
        }else{
          alert('两次输入密码不一致，请重试！');
        }
      })
      $('.forchange').click(function(){
        $('.fadetitle').text('修改密码');
      })
      $('.forforgot').click(function(){
        $('.fadetitle').text('忘记密码？');
      })
      </script>

  </body>
</html>
