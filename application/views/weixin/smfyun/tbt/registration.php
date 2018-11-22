<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="description" content="Admin Template">
    <meta name="keywords" content="admin dashboard, admin, flat, flat ui, ui kit, app, web app, responsive">
    <link rel="shortcut icon" href="img/ico/favicon.png">
    <title>注册</title>

    <!-- Base Styles -->
    <link href="../qwt/tbt/css/style.css" rel="stylesheet">
    <link href="../qwt/tbt/css/style-responsive.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="js/html5shiv.min.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
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
    #shopimgimg, #userimgimg{
      width: 2.7em;
      height: 2.7em;
      border-radius: 4px;
      font-size: 18px;
      margin-bottom: 10px;
    }
    #shopimgbox, #userimgbox{
      width: 2.7em;
      font-size: 18px;
      position: relative;
      display: inline-block;
    }
    .x{
      position: absolute;
      width: 20px;
      top: -10px;
      right: -10px;
    }
    .shop, .user{
      width: 100%;
      text-align: center;
    }
    .form-signin input[type="tel"]{
    margin-bottom: 15px;
    border-radius: 4px;
    /*border: none;*/
    /*background: #222224;*/
    box-shadow: none;
    font-size: 13px;
    /*color: #fff;*/
    padding: 12px;
    }

    </style>
</head>

  <body class="login-body">
      <img class="top" src="../qwt/tbt/img/top2.png">
      <!-- <img class="logo1" src="../qwt/tbt/img/logo.png"> -->

      <!-- <div class="login-logo">
          <img src="../qwt/tbt/img/login_logo.png" alt=""/>
      </div>

      <h2 class="form-heading">注册</h2> -->
      <div class="container log-row">
          <form class="form-signin" method="post" onsubmit="return toCheck()">
          <?php if($result['error']):?>
              <p style="color:#ff7e7e;"><?=$result['error']?></p>
            <?php endif?>
              <p>请上传您的门店照片</p>
              <!-- <input type="file" class="form-control" id="shopimg" name="shopimg" style="display:none"> -->
              <div class="shop">
                <button class="btn btn-lg btn-info btn-block" id='shopimg' type="button" style="color:#d9d9d9;font-weight:bold;background-color:#fff;border-color:#d9d9d9;width:2.7em;height:2.7em;border-radius:4px;display:inline-block">+</button>
                <div id="shopimgbox" style="display:none;position:relative">
                  <img id="shopimgimg" src="">
                  <img class="shopx x" src="../qwt/tbt/img/x.png">
                </div>
                <input type="hidden" value="" id="shopimgipt" name="tbt[shopimg]">
              </div>
              <p>请上传您的身份证照片或本人照片</p>
              <!-- <input type="file" class="form-control" id="userimg" name="userimg" style="display:none"> -->
              <div class="user">
                <button class="btn btn-lg btn-info btn-block" id='userimg' type="button" style="color:#d9d9d9;font-weight:bold;background-color:#fff;border-color:#d9d9d9;width:2.7em;height:2.7em;border-radius:4px;display:inline-box;display:inline-block">+</button>
                <div id="userimgbox" style="display:none;position:relative">
                  <img id="userimgimg" src="">
                  <img class="userx x" src="../qwt/tbt/img/x.png">
                </div>
                <input type="hidden" value="" id="userimgipt" name="tbt[userimg]">
              </div>
              <p>请选择您的行业类型</p>
              <div class="radio-custom radio-success">
                  <input type="radio" value="销售商" checked="checked" name="tbt[type]" id="seller">
                  <label for="seller">销售商</label>
                  <input type="radio"  value="车队" name="tbt[type]" id="driveteam">
                  <label for="driveteam">车队</label>
                  <input type="radio"  value="修理厂" name="tbt[type]" id="repair">
                  <label for="repair">修理厂</label>
                  <input type="radio"  value="服务站" name="tbt[type]" id="service">
                  <label for="service">服务站</label>
              </div>

              <!-- <p> Enter your account details below</p> -->
              <input type="text" class="form-control" name="tbt[address]" placeholder="请输入您的地址" autofocus>
              <input type="text" class="form-control" name="tbt[name]" placeholder="请输入您的姓名">
              <input type="tel" class="form-control" maxlength="11" name="tbt[tel]" placeholder="请输入您的手机号码">
              <input type="password" class="form-control" id="passwordconfirm" placeholder="请输入您的密码">
              <p id="warn" style="color:#ff7e7e;display:none;">与密码不同，请重试</p>
              <input type="password" class="form-control" id="password" name="tbt[passwd]" placeholder="请再输入一次密码">
              <!-- <label class="checkbox-custom check-success">
                  <input type="checkbox" value="agree this condition" id="checkbox1"> <label for="checkbox1">I agree to the Terms of Service and Privacy Policy</label>
              </label> -->


              <button id="submit" class="btn btn-lg btn-success btn-block" type="submit"  style="background-color:#ff7c00;border-color:#ff7c00;border-radius:4em;">注册</button>

              <div class="registration m-t-20 m-b-20">
                  已有账号？
                  <a class="" href="login">
                      登录
                  </a>
              </div>
          </form>
      </div>

    <!--jquery-1.10.2.min-->
    <script src="../qwt/tbt/js/jquery-1.11.1.min.js"></script>
    <!--Bootstrap Js-->
    <script src="../qwt/tbt/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>

    <script type="text/javascript">
      wx.config({
          debug: 0,
          appId: '<?php echo $jsapi["appId"];?>',
          timestamp: '<?php echo $jsapi["timestamp"];?>',
          nonceStr: '<?php echo $jsapi["nonceStr"];?>',
          signature: '<?php echo $jsapi["signature"];?>',
          jsApiList: [
            // 所有要调用的 API 都要加到这个列表中
            'checkJsApi',
            'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage'
          ]
      });
      $('#password').on('blur',function(){
        var check = $('#password').val();
        var password = $('#passwordconfirm').val();
        if (check == password) {
          $('#submit').attr('disabled',false);
          $('#warn').hide();
        }else{
          $('#submit').attr('disabled',true);
          $('#warn').show();
        }
      })
      $('.shopx').click(function(){
        $('#shopimgbox').hide();
        $('#shopimg').show();
        $('#shopimgipt').val("");
      })
      $('.userx').click(function(){
        $('#userimgbox').hide();
        $('#userimg').show();
        $('#userimgipt').val("");
      })
      $('#shopimg').click(function() {
        /* Act on the event */
        wx.chooseImage({
          count: 1, // 默认9
          sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
          sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
          success: function (res) {
            var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
            // alert(localIds);
            $('#shopimgimg').attr('src',res.localIds);
            $('#shopimg').hide();
            $('#shopimgbox').show();
            wx.uploadImage({
              localId: localIds.toString(), // 需要上传的图片的本地ID，由chooseImage接口获得
              isShowProgressTips: 1, // 默认为1，显示进度提示
              success: function (res) {
              var serverId = res.serverId; // 返回图片的服务器端ID
              $('#shopimgipt').val(serverId);
              // alert(serverId);
              }
            });
          }
        });
      });
      $('#userimg').click(function() {
        /* Act on the event */
        wx.chooseImage({
          count: 1, // 默认9
          sizeType: ['compressed'], // 可以指定是原图还是压缩图，默认二者都有
          sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
          success: function (res) {
            var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
            // alert(localIds);
            $('#userimgimg').attr('src',res.localIds);
            $('#userimg').hide();
            $('#userimgbox').show();
            wx.uploadImage({
              localId: localIds.toString(), // 需要上传的图片的本地ID，由chooseImage接口获得
              isShowProgressTips: 1, // 默认为1，显示进度提示
              success: function (res) {
                var serverId = res.serverId; // 返回图片的服务器端ID
                $('#userimgipt').val(serverId);
                // alert(serverId);
              }
            });
          }
        });
      });
      function toCheck(){
        var $input = $('input');
        var flag = false;
        $input.each(function(){
          var value = $(this).val().replace(/(^\s*)|(\s*$)/g, "");
          if (value=='') {
            flag = true;
            return false;
          };
        })
        if (flag == true) {
          alert('请填写完整');
          return false;
        }
        if (flag == false) {
          return true;
        };
      }
    </script>
  </body>
</html>
