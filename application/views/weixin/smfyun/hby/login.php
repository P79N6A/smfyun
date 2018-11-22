<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
<title>红包雨||登录</title>
<style type="text/css">
 @charset "utf-8";
html,body,div,p,form,label,ul,li,dl,dt,dd,ol,img,button,b,em,strong,small,h1,h2,h3,h4,h5,h6{margin:0;padding:0;border:0;list-style:none;font-style:normal;}
body{font-family:SimHei,'Helvetica Neue',Arial,'Droid Sans',sans-serif;font-size:14px;color:#333;background:#f2f2f2;}
a, a.link{color:#666;text-decoration:none;font-weight:500;}
a, a.link:hover{color:#666;}
h1,h2,h3,h4,h5,h6{font-weight: normal;}

.login{width:100%;height:100%;background:url(../qwt/hby/login-bg.png) no-repeat;background-size:cover;position:fixed;z-index:-10;}
.welcome{width:100%;margin:25% 0;}
.welcome img{width:100%;}
.login-inp{margin:0 30px 15px 30px;border:1px solid #fff;border-radius:25px;}
.login-inp label{width:4em;text-align:center;display:inline-block;color:#fff;}
.login-inp input{width: calc(100% - 4em - 60px);line-height:40px;color:#fff;background-color:transparent;border:none;outline: none;}
.login-inp a{display:block;width:100%;text-align:center;line-height:40px;color:#fff;font-size:16px;letter-spacing:5px;}
.login-txt{text-align:center;color:#fff;}
.login-txt a{color:#fff;padding:0 5px;}
</style>
</head>

<body>
<div class="login">
	<div class="welcome"><img src="../qwt/hby/welcome.png"></div>
 <form id="form" method="post">
	<div class="login-form">
		<div class="login-inp"><label>账号</label><input type="text" name="name" placeholder=""></div>
		<div class="login-inp"><label>密码</label><input type="password" name="passwd" placeholder=""></div>
		<div class="login-inp"><a id="submit">立即登录</a></div>
	</div>
 </form>
</div>
<script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.js"></script>
<script type="text/javascript">
<?php if ($result['error']):?>
$(document).ready(function(){
 alert("<?=$result['error']?>");
})
<?php endif?>
 $('#submit').click(function(){
  $('#form').submit();
 })
</script>
</body>
</html>
