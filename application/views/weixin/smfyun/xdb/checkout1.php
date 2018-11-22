<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes"/>
<title>兑换</title>
<link href="../qwt/xdb/css/bootstrap.css" rel="stylesheet" type="text/css"/>
<link href="../qwt/xdb/css/home.css" rel="stylesheet" type="text/css"/>
<link href="http://at.alicdn.com/t/font_6688xdquixljif6r.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
.tb960x90 {display:none!important;display:none}
.one img{
	width: 100%;
}
</style>
</head>
<body style="background:#fff;">
<!--top-->
<div class="top_c">
	<p class="titi">
		兑换
	</p>
</div>
<div class="login">
	<div class="container">
		<div class="one">
				<img src="../qwt/xdb/images/ewm.png"/>
		</div>
		<div class="one">
			<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/order">
			<p class="biao">
				<span class="iconfont"></span>兑换记录<i class="iconfont icon-jiantou"></i>
			</p>
			</a>
		</div>
		<div class="one">
			<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxdb/order">
			<p class="biao">
				<span class="iconfont"></span>兑换记录<i class="iconfont icon-jiantou"></i>
			</p>
			</a>
		</div>
		<div class="zhu">
			<div class="ru">
				<input type="text" name="address" size="60" maxlength="60" style="color:#ccc" value="请输入您的手机号" onfocus="if(this.value=='请输入您的手机号'){this.value=''};this.style.color='black';" onblur="if(this.value==''||this.value=='请输入您的手机号'){this.value='请输入您的手机号';this.style.color='#ccc';}">
			</div>
			<div class="ru">
				<input type="text" name="address" size="60" maxlength="60" style="color:#ccc" value="请设置您的登录密码" onfocus="if(this.value=='请设置您的登录密码'){this.value=''};this.style.color='black';" onblur="if(this.value==''||this.value=='请设置您的登录密码'){this.value='请设置您的登录密码';this.style.color='#ccc';}">
			</div>
			<div class="ru">
				<input type="text" name="address" size="60" maxlength="60" style="color:#ccc" value="请输入手机号" onfocus="if(this.value=='请输入手机号'){this.value=''};this.style.color='black';" onblur="if(this.value==''||this.value=='请输入推荐人的手机号'){this.value='请输入推荐人的手机号';this.style.color='#ccc';}">
			</div>
			<!-- <div class="ru">
				<input type="text" name="address" size="60" maxlength="60" style="color:#ccc" value="请输入验证码" onfocus="if(this.value=='请输入验证码'){this.value=''};this.style.color='black';" onblur="if(this.value==''||this.value=='请输入验证码'){this.value='请输入验证码';this.style.color='#ccc';}">
				<div class="yan">
					<input type="button" id="btn" class="btn_mfyzm" value="获取验证码"/>
					<script type="text/javascript">
						var wait=60;
						document.getElementById("btn").disabled = false;
						function time(o) {
								if (wait == 0) {
									o.removeAttribute("disabled");
									o.value="获取验证码";
									wait = 60;
								} else {
									o.setAttribute("disabled", true);
									o.value="重新发送(" + wait + ")";
									wait--;
									setTimeout(function() {
										time(o)
									},
									1000)
								}
							}
						document.getElementById("btn").onclick=function(){time(this);}
					</script>
				</div>
			</div> -->
		</div>
		<a href="#" class="deng" style="margin-top:3em;">免费注册</a>
	</div>
</div>
<script src="../qwt/xdb/js/jquery.min.js" type="text/javascript"></script>
<script src="../qwt/xdb/js/index.js" type="text/javascript"></script>
</body>
</html>
