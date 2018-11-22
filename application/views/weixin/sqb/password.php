<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>修改账号信息</title>
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
		<h3>账号密码修改（不修改的部分请勿填写）</h3>
	</div>
	<form method="post">
		<input type="text" class="text" value="请填写账号" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = '请填写账号';}" name="confirm[account]">
  <input type="text" class="text" value="新账号" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = '新账号';}" name="new[account]">
		<input type="password" value="" placeholder="请填写密码" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = '';}" name="confirm[password]">
  <input type="password" value="" placeholder="新密码" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = '';}" name="new[password]">
  <input type="password" value="" placeholder="确认新密码" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = '';}" name="check[password]">
		<input type="submit" onClick="myFunction()" value="确认修改" >
	</form>
</div>
 <!--/SIGN UP-->
 <!--SIGN IN-->
    <script src="js/jquery.min.js"></script>
    <script type="text/javascript">
    <?php if($result['err']==1):?>
    $(document).ready(function(){
     alert('修改失败，请先保证填入正确的老账号密码！');
    })
    <?php endif?>
    <?php if($result['err']==2):?>
    $(document).ready(function(){
     alert('密码修改失败，两次输入的新密码不一致！');
    })
    <?php endif?>
    <?php if($result['err']==3):?>
    $(document).ready(function(){
     alert('修改成功！');
    })
    <?php endif?>
    </script>

</body>
</html>
