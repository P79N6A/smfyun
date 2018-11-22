<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>大转盘抽奖</title>
	<meta name="viewport" content="width=device-width,initial-scale=1, maximum-scale=1, user-scalable=no">
	<!--<link rel="stylesheet" type="text/css" href="css/app.css">-->
	<link rel="stylesheet" type="text/css" href="/qwt/dka/dist/css/animate.min.css">
	<script type="text/javascript" src="/qwt/dka/dist/js/zepto.js"></script>
	<!--<script type="text/javascript" src="js/app.js"></script>-->
	<script type="text/javascript">
		(function(window){
			var score = <?=$res['killscore']?>;
			var time = <?=$res['limittime']?>-<?=$res['usertime']?>;
			var mine={};
			mine.alertArr=new Array(4);
			//积分消耗弹窗
			mine.alertArr[0]="<div class='show'><div class='bg'></div><div class='content_1'><div class='top_1'>参加本次活动将扣除您"+score+"积分。</div><div class='bottom_1'><button class='button confirm'>确定</button><button class='button cancel'>取消</button></div></div></div>";
			mine.alertArr[1]="<div class='show'><div class='bg'></div><div class='content_1'><div class='top_1'>%s</div><div class='bottom_1'><button class='button get'>领取</button></div></div></div>";//领取奖品弹窗
			mine.alertArr[2]="<div class='show'><div class='bg'></div><div class='content_1'><div class='top_1'>可惜啦，积分不够。这次抽奖要消耗您"+score+"积分！</div><div class='bottom_1'><button class='button close'>关闭</button></div></div></div>";//积分不够
			mine.alertArr[3]="<div class='show'><div class='bg'></div><div class='content_1'><div class='top_1'>%s</div><div class='bottom_1'><button class='button knew'>朕知道了</button></div></div></div>";
			//提醒还剩下的抽奖次数,未中奖弹窗,其他弹窗

			mine.show=function(){
				var width=document.documentElement.clientWidth;
				var height=document.documentElement.clientHeight;
				$(this).css(
					{'left':(width-$(this).width())/2,'top':'300'});
				// $('.show').css('height',20000);
				// $('.bg').css('height',20000);
				$('.show').addClass('animated fadeIn');
			}
			mine.close=function(){
				$('.show').removeClass('animated fadeIn').addClass('animated fadeOut');
				$('.show').remove();
			}
			window.mine=mine;
		})(window)
	</script>
	<!-- <script type="text/javascript" src="/qwt/dka/dist/js/global.js"></script> -->
	<script type="text/javascript">
	var _width=640;
var _height=1700;
var scale=_width/_height;
function resize(width){
	var scaleX=width/_width;
	var scaleY=width/scale/_height;
	//$('body').css('height',width/scale+30);
	$('.top_word').css({'width':523*scaleX,'height':91*scaleY});
	$('.top_word img').css({'width':523*scaleX,'height':91*scaleY});
	$('.stage').css({'width':527*scaleX,'height':532*scaleY});
	$('.huodong').css('width',508*scaleX);
	$('.huodong_top').css('height',85*scaleY);
	// $('.huodong_bottom').css('height',570*scaleY);
	$('.arrow').css({'width':39*scaleX,'height':36*scaleY});
	$('.zhizhen').css({'width':131*scaleX,'height':161*scaleY}).css({'left':($('.stage').width()-$('.zhizhen').width())/2,'top':($('.stage').height()-$('.zhizhen').height())/2});
}
function change(){
	$('.arrow_top').click(function(){
			$('.huodong_bottom').addClass("animated fadeOutUp").css('display','none');
			$(this).hide();
			$('.arrow_bottom').show();
			$('.huodong_top').css('border-radius',5);
		});
	$('.arrow_bottom').click(function(){
			$('.huodong_bottom').removeClass("animated fadeOutUp").css("display",'block').addClass("animated fadeInDown");
			$(this).hide();
			$('.arrow_top').show();
			$('.huodong_top').css('border-radius',0);
		});
}
function close(){
			$('.bg').click(function(){
				mine.close();
			});
			$('.cancel').on('click',function(){
				mine.close();
			});
		}
function show(arg,obj,msg){
	mine.close();
	$('body').append(mine.alertArr[arg].replace(/%s/,msg));
	$(obj).css('width',$('.container').width()*0.8);
	$('.bg').css({'height':30000,'margin-top':-10000});
	mine.show.call($(obj));
	$(window).on('resize',function(){
		$('.bg').css({'height':30000,'margin-top':-10000});
		mine.show.call($(obj));
	});
}
function _alert(msg){
	$('.alert').html('');
	$('.alert').show();
	$('.alert').html(msg);
}
function _animate(angle){
	$('.rotate_button').stopRotate();
	$(".rotate_button").rotate({
		angle:0,
		duration: 3000,
		animateTo: angle+1440, //angle是图片上各奖项对应的角度，1440是我要让指针旋转4圈。所以最后的结束的角度就是这样子^^
	});
}
function sleep(numberMillis) {
	var now = new Date();
	var exitTime = now.getTime() + numberMillis;
	while (true) {
		now = new Date();
		if (now.getTime() > exitTime)
		return;
	}
}
$(function(){
	var width=$('.container').width();
	var height=114;
	var num=5;
	var ball=$('.rotate_button');
	resize(width);
	change();
	window.totalscore=<?=$res['total_score']?>;
	window.usedtime=<?=$res['usertime']?>;
	window.prizeAngle=<?=$deg_angle?>;
	window.limitime=<?=$res['limittime']?>;
	window.signal = 0;
	ball.on('click',function(){
		window.signal ++;
		//点击抽奖按钮之后先更新一遍奖品设置和个人资料
		$.ajax({
			url:'/qwtdka/draw',
			type:'POST',
			data:{
				sign:1
			},
			dataType:'json',
			success:function(res){
				window.totalscore = res.total_score;
				window.usedtime = res.usertime;
				window.prizeAngle=res.angle;
				window.ptype=res.getprize.ptype;
				window.iid=res.getprize.iid;
			},
			error:function(){
				alert("当前参与人数众多，请稍后再试");
			}
		});
		if(<?=$res['limittime']?>-window.usedtime>0){
			// alert("限次"+window.limitime);
			// alert("已用"+window.usedtime);
			show(3,'.content_1','您今日还有'+(window.limitime-window.usedtime)+'次抽奖机会！');
			$('.knew').on('click',function(){  //点击‘我知道了’后事件
				$('.show').remove();
				$.ajax({
					url:'/qwtdka/draw',
					type:'POST',
					data: {
						flag: 1
					},
					dataType:'json',
					success:function(res){
						var json1=res[0];
						var json2=res[1];
						if(res[0] == false){
							show(3,'.content_1','您今日的抽奖次数已经用完，请明天再来吧！');
							$(document).on('click','.knew',function(){
								$('.show').remove();
								window.location.href="/qwtdka/score";
							});
						}else if(res[1] == false){
							show(2,'.content_1');//提示积分不足无法抽奖
							$('.close').on('click',function(){
								$('.show').remove();
							});
						}else{
							if(window.signal==0){
								window.ptype=res.getprize.ptype;
								window.iid=res.getprize.iid;
								window.prizeAngle=res.angle;
								window.qid=res.qid;
							}
							_animate(window.prizeAngle);//中奖动画
						}
						window.signal=0;
						if(ptype != 0){
						setTimeout(function(){show(1,'.content_1',"恭喜你获得"+ptype+"等奖！")},3000);
						$(document).on('click','.get',function(){
							$('.show').remove();
							window.location.href="/qwtdka/neworder/"+iid+"?rank="+ptype;
						});
						}else{
							setTimeout(function(){show(3,'.content_1',"很遗憾这次没有中奖，再来一次吧！")},3000);
							$(document).on('click','.knew',function(){
								$('.show').remove();
							});
						}
						//close();
					},
					error:function(){
						alert("当前参与人数众多，请稍后再试！");
					}
				});
				//判断是否中奖
				//获取奖品的iid和奖项等级

			});

		}else {
			show(3,'.content_1',"您今日的抽奖次数已经用完，请明天再来吧！");//提示抽奖次数已经用完
			$(document).on('click','.knew',function(){
				$('.show').remove();
				window.location.href="/qwtdka/score";
			});
		}
	});
});
	</script>
	<script type="text/javascript" src="/qwt/dka/dist/js/jquery.min.js"></script>
	<script type="text/javascript" src="/qwt/dka/dist/js/jQueryRotate.2.2.js"></script>
	<script type="text/javascript" src="/qwt/dka/dist/js/jquery.easing.min.js"></script>
	<style>
		*,body{
			margin:0;
			padding:0;
			font-size:14px;
			font-family: 'Microsoft YaHei';
			font-color:white;
		}
		body{
			width:100%;
			height:auto;
			background-color: rgb(255,85,110);
		}
		.knew,.get{
			cursor:pointer;
		}
		.container{
			width:100%;
			height:auto;
			background-image: url('/qwt/dka/dist/img/choujiang/bg.png');
			background-repeat:no-repeat;
			background-size: 100%;
			position: relative;
			top:0;
			left:0;
			background-color: rgb(253,225,91);
		}
		.top_word{
			width:100%;
			height:100%;
			margin:0 auto;
			/*position:relative;*/
			padding-top: 70%;
			padding-bottom: 5%;
		}
		.top_word img{
			width:100%;
			height:100%;
		}
		.stage{
			width:100%;
			height:100%;
			position:relative;
			padding-bottom: 5%;
			margin:0 auto;
			overflow: hidden;
			background-image: url("/qwt/dka/dist/img/choujiang/zhuangpan.png");
			background-repeat:no-repeat;
			background-size: 100%;
		}
		.zhizhen{
			position:relative;
		}
		.zhizhen:hover{
			cursor: pointer;
		}
		.rotate_button{
			width:100%;
			height: 100%;
			-webkit-transform:rotate(0deg);
			-webkit-user-select:none;
		}
		.huodong{
			width:100%;
			height:auto;
			position:relative;
			padding-bottom: 5%;
			margin:0 auto;
		}
		.huodong_top,.huodong_bottom{
			width:100%;
			height: auto;
			overflow: hidden;
		}
		.huodong_top img:nth-child(1){
			width:100%;
			height:100%;
			position:relative;
			top:0;
			left:0;
		}
		.arrow{
			position: absolute;
			top:3%;
			left:68%;
			z-index:0;
		}
		.arrow_bottom{
			top:30%;
			display: none;
		}
		.arrow:hover{
			cursor:pointer;
		}
		.huodong_bottom{
			width:100%;
			position:relative;
			top:0;
			left:0;
			background-color: rgb(255,85,110);
			color:white;
			font-size: 16px;
			border-bottom-left-radius:5px;
			border-bottom-right-radius:5px;
			/*padding-bottom: 5%;*/
		}
		.intro,.endtime,.join,.tell,.prize{
			width:90%;
			/*height:18%;*/
			margin:0 auto;
			margin-bottom: 5px;
		}
		.intro{
			/* height:18%; */
		}
		.endtime,.join{
			/* height:5%; */
			margin-bottom: 15px;
		}
		.intro p:nth-child(1){
			float: left;
			/*width:30%;*/
			/* height:100%; */
			text-align: left;
		}
		.intro p:nth-child(2){
			float: right;
			width:70%;
			/* height:100%; */
			text-align: left;
		}
		.endtime p:nth-child(1),.join p:nth-child(1){
			float: left;
			/*width:25%;*/
			/* height:100%; */
			text-align: left;
		}
		.endtime p:nth-child(2),.join p:nth-child(2){
			float: right;
			width:70%;
			/* height:100%; */
			text-align: left;
		}
		.tell p:nth-child(1){
			float: left;
			/*width:25%;*/
			/* height:100%; */
			text-align: left;
		}
		.tell p:nth-child(n+2){
			float: right;
			width:70%;
			/* height:100%; */
			text-align: left;
		}
		.prize_set{
			color:rgb(255,199,120);
			font-size: 20px;
			font-weight: bold;
			margin-bottom: 10px;
		}
		.prize_content{
			margin-left:30px;
			margin-bottom: 10px;
		}
		.prize_content_li{
			width:100%;
			position: relative;
		}
		.prize_content_li span:nth-child(2){
			position: relative;
		    float:right;
		    text-align: right;
		}
		.show{
			width:100%;
			height:100%;
			position:fixed;
			top:0;
			left:0;
			z-index: 10;
		}
		.bg{
			opacity: 0.7;
			background-color: rgb(0, 0, 0);
			position: fixed;
			left: 0px;
			top: 0px;
			width: 100%;
			height: 100%;
			/*overflow: hidden;*/
			-webkit-user-select: none;
			z-index: 1024;
		}
		.content_1,.content_2,.content_3{
			width:80%;
			height:auto;
			border: none;
			border-radius: 5px;
			position: fixed;
			outline: 0;
			z-index: 1024;
			background-color:#fde15b;
		}
		.content_1{
			height:100px;
		}
		.content_2{
			height:175px;
		}
		.content_3{
			height:300px;
		}
		.top_1{
			color:white;
			height:50%;
			width:100%;
			line-height: 50px;
			text-align: center;
			background-color: red;
			border-top-left-radius: 5px;
			border-top-right-radius: 5px;
		}
		.top_2{
			color:red;
			width:100%;
			height:70%;
			border-bottom: 1px dashed black;
			margin-bottom:10px;
		}
		.top_3{
			background-color: red;
			color:white;
			height:33%;
			width:100%;
			text-align: center;
			border-top-left-radius: 5px;
			border-top-right-radius: 5px;
		}
		.bottom_1{
			/*background-color: #fde15b;*/
			color: white;
			text-align: center;
			height: 50%;
			width:100%;
			line-height: 50px;
			padding-top: 10px;
		}
		.bottom_2{
			color: white;
			text-align: center;
			height: 25%;
			width:100%;
		}
		.button{
			width:100px;
			height:30px;
			margin:0 auto;
			background-color:red;
			color:white;
			text-align: center;
			font-size: 14px;
			border-radius: 5px;
			border:none;
			box-shadow: none;
		}
		.button:hover{
			cursor:pointer;
		}
		::-webkit-input-placeholder{
			color:white;
		}
		.cancel,.confirm,.knew,.look,.more{
			margin:0 5px;
		}
		.knew{
			width:31%;
		}
		.phone,.code,.alert{
			width:90%;
			margin:0 auto;
			height:auto;
			color:red;
			padding-top:5px;
			text-align: left;
		}
		.alert{
			border:1px solid red;
			display: none;
			width:90%;
			height:20px;
			line-height: 20px;
			text-align: center;
			padding:0;
			margin-top:10px;
			border-radius: 5px;
		}
		.phone_num,.code_num{
			border:1px solid red;
			border-radius: 5px;
			height:30px;
			padding:0;
		    background-color: transparent;
		    margin-left:5px;
		}
		.phone_num{
			width:65%;
		}
		.code_num{
			width:30%;
		}
		.bottom_3{
			color: white;
			text-align: center;
			height: 67%;
			width:100%;
		}
		.word_top{
			padding-top: 2%;
			font-size: 16px;
			width:60%;
			margin:0 auto;
		}
		.word_middle{
			padding-top: 2%;
			font-size: 14px;
			width:40%;
			margin: 0 auto;
		}
		.word_bottom{
			padding-top: 2%;
			font-size: 10px;
			color:black;
			width:100%;
			margin:0 auto;
			text-align: left;
		}
		.bottom_top{
			padding-top: 3%;
			color: black;
			width:40%;
			margin: 0 auto;
			font-size: 14px;
		}
		.erweima{
			padding-top: 3%;
			width:120px;
			height:120px;
			margin:0 auto;
		}
		.erweima img{
			width:100%;
			height:100%;
		}
		.bottom_bottom{
			padding-top: 3%;
			font-size: 10px;
			color: black;
			width: 100%;
			margin:0 auto;
		}
		@media only screen and (min-width: 1000px)
			{
				.container
					{
						position: relative;
						width: 480px;
						margin: 0 auto;
					}
			}

	</style>
</head>
<body>
	<div class="container">
		<div class="top_word">
			<img src="/qwt/dka/dist/img/choujiang/round3.png"/>
		</div>
		<div class="stage">
			<div class="zhizhen"><img class="rotate_button" src="/qwt/dka/dist/img/choujiang/button.png"></div>
		</div>
		<div class="huodong">
			<div class="huodong_top">
			<img src="/qwt/dka/dist/img/choujiang/rule_top.png">
			<img class="arrow arrow_top" src="/qwt/dka/dist/img/choujiang/arrow.png">
			<img class="arrow arrow_bottom" src="/qwt/dka/dist/img/choujiang/arrow_bottom.png">
			</div>
			<div class="huodong_bottom">
				<div class="intro">
					<p>活动介绍：</p>
					<p><span>消耗<?=$res['killscore']?>积分即可参与“机会轮盘”游戏一次。</span></p>
				</div>
				<div style="clear:both"></div>
				<div class="endtime">
					<p>截止时间：</p>
					<p><?=$res['enddate']?></p>
				</div>
				<div style="clear:both"></div>
				<div class="join">
					<p>参与次数：</p>
					<p><?=$res['limittime']?></p>
				</div>
				<div style="clear:both"></div>
				<div class="tell">
					<p>活动说明：</p>
					<p><?=$res['exp']?></p>
				</div>
				<div style="clear:both"></div>
				<div class="prize">
					<div class="prize_set">奖品设置</div>
					<div class="prize_content">
					<?php foreach($res['prizeset'] as $v):?>
						<div class="prize_content_li">
							<span><?php foreach($res['itemset'] as $i){if($v->iid == $i->id) echo $i->name;}?></span><span>数量：<?=$v->stock?></span>
						</div>
					<?php endforeach;?>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
