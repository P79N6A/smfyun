<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,maximum-scale=1.0,user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="msapplication-tap-highlight" content="no">
	<title>核销员登录|多客来</title>
	<style type="text/css">
    body{
   font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;
    }
	div{
		display: block;
	}
		.logo{
			margin-top: 10%;
			width: 100%;
			left: 0;
			text-align: center;
		}
        .name{
            text-align: center;
            margin-top: 20px;
            font-size: 24px;
        }
        .dkl{
            font-size: 36px;
            font-weight: bold;
        }
        .checkbar{
            margin-top: 20px;
            text-align: center;
            font-size: 0;
        }
        .input{

    font-size: 14px;
    line-height: 14px;
    height: 36px;
    width: 30%;
    min-width: 200px;
    padding-left: 10px;
    border: 1px solid #b6b6b6;
    padding: 0;
    border-right: 0;
        }
        .login{

    border: 1px solid #b6b6b6;
    width: 80px;
    height: 38px;
    color: #fff;
    font-size: 15px;
    letter-spacing: 1px;
    background: #3385ff;
    border-bottom: 1px solid #2d78f4;
    outline: medium;
    -webkit-appearance: none;
    -webkit-border-radius: 0;
    border-left: 0;
        }
        .warning{
            margin-top: 20px;
            text-align: center;
        }
        .warntext{
            padding: 10px;
            border-radius: 1px;
            background-color: #ff0000;
            color: #fff;
        }
	</style>
</head>
<body>
<form role="form" method="post">
<div class="logo"><img src="/dkl/img/logo.png"></div>
<div class="name"><span class="dkl">多客来</span>@神码浮云</div>
<?php if($result['err']):?>
<div class="warning"><span class="warntext"><?=$result['err']?></span></div>
<?php endif;?>
<div class="checkbar">
<input class="input" name="telephone" type="tel" maxlength="11" onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" placeholder="请输入您的电话号码" style="padding-left:10px;">
<input class="login" type="submit" value="登录">
</div>
</form>
<script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
</body>
</html>
