<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title>有人找我</title>
	<link rel="stylesheet" type="text/css" href="css/ui.css">
	<link href="favicon.ico" type="image/x-icon" rel="icon">
	<link href="iTunesArtwork@2x.png" sizes="114x114" rel="apple-touch-icon-precomposed">
</head>
<style type="text/css">
	.redpoint{
    width: 10px;
    height: 10px;
    background-color: red;
    border-radius: 5px;
    margin-left: 10px;
   }
</style>
<body>
	<div class="aui-container">
		<div class="aui-page">
			<div class="header header-color">
				<div class="header-background"></div>
				<div class="toolbar statusbar-padding">
					<button class="bar-button back-button"  onclick="history.go(-1);"><i class="icon icon-back-sx"></i></button>
					<div class="header-title">
						<div class="title">有人找我</div>
					</div>
				</div>
			</div>
			<div class="aui-text-top aui-l-content aui-l-content-clear">
				<div class="devider b-line"></div>
				<div class="aui-menu-list aui-menu-list-clear">
					<ul>
						<?foreach ($msg as $k => $v):
						$headimgurl = ORM::factory('sqb_qrcode')->where('id','=',$v->qid)->find()->headimgurl;
						$nickname = ORM::factory('sqb_qrcode')->where('id','=',$v->qid)->find()->nickname;
						?>
						<li class="b-line">
							<a href="../sqb/beiliao/<?=$v->qid?>">
								<div class="aui-icon"><img src="<?=$headimgurl?>"></div>
								<h3><?=$nickname?></h3>
								<?php if($v->status==1&&$v->type==1):?>
								<div class="redpoint"></div>
							<?php endif?>
								<div class="aui-time"><i class="aui-jump"></i></div>
							</a>
						</li>
					<?php endforeach?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</body>
	<script src="js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function () {
			var aMenuOneLi = $(".aui-fold-master > li");
			var aMenuTwo = $(".aui-fold-genre");
			$(".aui-fold-master > li > .aui-fold-title").each(function (i) {
				$(this).click(function () {
					if ($(aMenuTwo[i]).css("display") == "block") {
						$(aMenuTwo[i]).slideUp(300);
						$(aMenuOneLi[i]).removeClass("menu-show")
					} else {
						for (var j = 0; j < aMenuTwo.length; j++) {
							$(aMenuTwo[j]).slideUp(300);
							$(aMenuOneLi[j]).removeClass("menu-show");
						}
						$(aMenuTwo[i]).slideDown(300);
						$(aMenuOneLi[i]).addClass("menu-show")
					}
				});
			});
		});
	</script>
</html>
