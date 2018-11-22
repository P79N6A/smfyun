<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>登录 </title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="application/x-javascript"> addEventListener("load", function() { setTimeout(hideURLbar, 0); }, false); function hideURLbar(){ window.scrollTo(0,1); } </script>
<meta name="keywords" content="iOS 7 Login And Register App Responsive Templates, Iphone Widget Template, Smartphone login forms,Login form, Widget Template, Responsive Templates, a Ipad 404 Templates, Flat Responsive Templates" />
<link href="css/style.css" rel='stylesheet' type='text/css' />
<!--webfonts-->
<link href='http://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,300italic' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Calligraffitti' rel='stylesheet' type='text/css'>
<!--//webfonts-->
</head>
<body>
 <!--SIGN UP-->
<div class="login-form">
	<div class="head-info">
		<h2>社群管家</h2>
		<h3>请输入您的手机号码，提交后不可修改</h3>
	</div>
	<form method="post" onsubmit="return upperCase()">
		<input id="phone" type="text" name="tel" class="text" value="" placeholder="请输入手机号" onFocus="this.value = '';">
		<!-- <input type="password" name="password" value="" placeholder="密码" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = '';}"> -->
		<input type="submit" value="提交">
	</form>
</div>
 <!--/SIGN UP-->
 <!--SIGN IN-->

<script src="/sqb/js/jquery.min.js"></script>
<script type="text/javascript">
<?php if ($result['status']=='error'):?>
$(document).ready(function(){
	alert('该手机号已经被注册，请输入其他手机号');
})
<?php endif?>
function upperCase(){
	var phone = $('#phone').val();
	if(!(/^1[34578]\d{9}$/.test(phone))){
		alert("手机号码有误，请重新填写");
		return false;
	}
}
</script>
</body>
</html>
