<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>个人中心</title>
	<link rel="stylesheet" type="text/css" href="css/ui.css">
	<link href="favicon.ico" type="image/x-icon" rel="icon">
	<link href="iTunesArtwork@2x.png" sizes="114x114" rel="apple-touch-icon-precomposed">
</head>
<style type="text/css">
	.redpoint{
    position: absolute;
    width: 10px;
    height: 10px;
    background-color: red;
    border-radius: 5px;
    left: 120px;
   }
</style>
<body>
	<div class="aui-container">
		<div class="aui-page">
			<div class="aui-page-my">
				<div class="aui-my-info">
					<div class="aui-my-info-back"></div>
					<a href="javascript:;" class="">
						<img src="<?=$avator?>" class="aui-my-avatar">
					</a>
					<!-- <div class="aui-mt-location aui-l-red"></div> -->
				</div>
				<div class="aui-l-content">
					<div class="aui-menu-list aui-menu-list-clear">
						<ul>
							<li class="b-line">
								<a>
								<marquee direction="left" align="bottom" scrollamount="2" scrolldelay="2" style="color:red;">公告：<?=$notice?></marquee>
								</a>
							</li>
							<li class="b-line">
								<a href="../sqb/shequnguangchang">
									<div class="aui-icon"><img src="images/icon-home/my-in2.png"></div>
									<h3>社群广场</h3>
									<div class="aui-time"><i class="aui-jump"></i></div>
								</a>
							</li>
							<li class="b-line">
								<a href="../sqb/wodefabu">
									<div class="aui-icon"><img src="images/icon-home/my-in8.png"></div>
									<h3>我的发布</h3>
									<div class="aui-time"><i class="aui-jump"></i></div>
								</a>
							</li>
							<li class="b-line">
								<a href="../sqb/gerenxinxi">
									<div class="aui-icon"><img src="images/icon-home/my-in1.png"></div>
									<h3>个人信息<?php if($user->admin==1):?>及公告<?php endif?></h3>
									<div class="aui-time"><i class="aui-jump"></i></div>
								</a>
							</li>
							<li class="b-line">
								<a href="../sqb/woyaozhaoren">
									<div class="aui-icon"><img src="images/icon-home/my-in7.png"></div>
									<h3>我要找人</h3>
									<?php if ($meto==1):?>
									<div class="redpoint"></div>
								<?php endif?>
									<div class="aui-time"><i class="aui-jump"></i></div>
								</a>
							</li>
							<li class="b-line">
								<a href="../sqb/yourenzhaowo">
									<div class="aui-icon"><img src="images/icon-home/my-in5.png"></div>
									<h3>有人找我</h3>
									<?php if ($tome==1):?>
									<div class="redpoint"></div>
								<?php endif?>
									<div class="aui-time"><i class="aui-jump"></i></div>
								</a>
							</li>
							<li class="b-line">
								<a href="../sqb/lianxiguanjia">
									<div class="aui-icon"><img src="images/icon-home/my-in6.png"></div>
									<h3>联系管家</h3>
									<div class="aui-time"><i class="aui-jump"></i></div>
								</a>
							</li>
							<!-- <li class="b-line">
								<a href="../sqb/password">
									<div class="aui-icon"><img src="images/icon-home/my-in4.png"></div>
									<h3>账号密码</h3>
									<div class="aui-time"><i class="aui-jump"></i></div>
								</a>
							</li> -->
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
