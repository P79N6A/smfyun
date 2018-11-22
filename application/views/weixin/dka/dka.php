<!DOCTYPE html>
<html>
<head>
	<meta charset=utf-8>
	<meta name ="viewport" content ="initial-scale=1, maximum-scale=3, minimum-scale=1, user-scalable=no">
	<!-- <link href="dka.css" rel="stylesheet" type="text/css"> -->
	<title><?=$bidname?>打卡计划</title>
	<style>
		*{
			margin:0;
			padding:0;
		}
		body{
			color:#fff;
		}
		.imgbg{
			background: url(/dka/images/cfg/<?=$result['dka']?>.v<?=time()?>.jpg) no-repeat;
			background-size: 100% 100%;
			width:100%;
		}
		/* ceshi */
		.imgbg:after{
			content:"";
			display:block;
			padding-bottom: 54%;
		}
		.headbox{
			width:90%;
			height:60px;
			margin:0 auto;
			margin-top:-80px;
			border:1px solid transparent;
			position:relative;
		}
		.box1{
			width:172px;
			margin-top:18px;
		}
		.num{
			font-size: 50px;
			float:left;
			text-shadow: 1px 1px 1px #fff;
		}
		.text1{
			font-size: 15px;
			margin-left: 5px;
			/*margin-top:25px;*/
		}
		.text2{
			display: inline;
		}
		.shuxian{
			width:2px;
			height:45px;
			background-color:#fff;
			margin-left:2px;
			display: inline-block;
			vertical-align: text-bottom;
		}
		.boxl1{
			font-size: 12px;
			margin-left:10px;
			width:150px;
			height:40px;
			display: inline-block;
			position:absolute;
			line-height: 24px;
		}
		.box2{
			position:absolute;
			top: 0px;
			right:0px;
			width:68px;
			height:68px;
			border-radius: 34px;
			background-color: rgba(0,0,0,.4);
			line-height: 25px;
		}
		.img2{
			height:50px;
			width:40px;
			position:absolute;
			top:7px;
			left:22px;
		}
		.box3{
			float:left;
			display:inline-block;
			position:absolute;
			bottom: 0px;
			left:0px;
		}
		.text4{
			font-size: 13px;
			color:#fff;
		}
		.bk{
			height:20px;
			width:44px;
			border:1px solid #fff;
			border-radius: 10px;
			display: inline-block;
			text-align: center;
		}
		a{
			text-decoration:none;
		}
		.box4{
			position:absolute;
			bottom:0px;
			right:0px;
		}
		.img3{
			width:19px;
			height:17px;
			vertical-align: middle;
		}
		.canjia{
			width:100%;
			height: 33px;
			overflow: auto;
			margin-top: 4px;
		}
		.img4{
			margin:0 auto;
			height:25px;
			width: 55%;
			margin-left: 3%;
			overflow: hidden;
			float: left;
		}
		ul{
			list-style: none;
			display: flex;
			float: left;
		}
		li{
			background-color: #f1f1f1;
			margin-left: 1.5%;
			width:25px;
			height:25px;
			float:left;
			line-height: 25px;
			margin-left: 2px;
		}
		/*邀请卡设置*/
		.yaoqing{
			width:30%;
			height: 25px;
			line-height: 25px;
			border:1px solid #7d7d7d;
			border-radius:5px;
			text-align: center;
			float: right;
			margin-right: 5%;
			vertical-align: middle;
		}
		.yaoqing img{
			width:20px;
		}
		.yaoqing div{
			font-size: 13px;
			color:#7d7d7d;
			vertical-align: top;
			display: inline-block;
		}

		/*活动说明*/
		.text5{
			margin-top: 25px;
			margin-left:3%;
			font-size: 11px;
			color: #A4A4A4;
		}
		.fenge{
			width:100%;
			height:9px;
			background-color: #f1f1f1;
		}
		.shuoming{
			width:90%;
			/*height:105px;*/
			line-height: 16px;
			color:#A4A4A4;
			padding-left: 5%;
			margin-top: 10px;
			padding-bottom: 8px;
		}
		.text6{
			font-size: 12px;
			color:#888;
		}
		.rule{
			font-size: 11px;
		}
		#box{
			color:#888;
		}
		.text8{
			text-align: center;
			height:32px;
			line-height: 32px;
			background-color: #b6b6b6;
			color:#F1F1F1;
		}
		.container{
			margin:0 auto;
			margin-top: 18px;
			width:90%;
		}

		/*列表更改*/
		.qiandao1{
			width:100%;
			height:50px;
			position: relative;
			overflow: auto;
		}
		.qiandao1::before{
			content:"";
			display: inline-block;
			height:100%;
			width:1%;
			vertical-align: middle;
		}
		.yuan{
			width:50px;
			height:50px;
			border-radius: 25px;
			float: left;
			display: inline-block;
			border:1px solid #A4A4A4;
			font-size: 13px;
			text-align: center;
			color: #A4A4A4;
			padding-top:10px;
			box-sizing: border-box;
		}
		.text9{
			display: inline-block;
			vertical-align: middle;
			width:44%;
			margin-left: 2%;
			color:#D0D0D0;
			font-size: 10px;
			line-height: 1.7;
		}
		.anniu{
			float: right;
			margin-top: 10px;
			display: inline-block;
			text-align: center;
			font-size: 13px;
			background-color: #FFC274;
			height:27px;
			line-height: 27px;
			width: 90px;
			border-radius: 13px;
		}
		.ydk{
			float: right;
			margin-top: 10px;
			display: inline-block;
			text-align: center;
			font-size: 13px;
			background-color: #FFC274;
			height:27px;
			line-height: 27px;
			width: 90px;
			border-radius: 13px;
		}
		.anniu2{
			/*background-color: #5ac500;*/
			font-size: 15px;
		}
		.anniu3, .anniu4{
			background-color: #e2e2e2;
		}
		.shuxian2{
			display: block;
			width:2px;
			height:33px;
			background-color:#A4A4A4;
			margin-left: 8.5%;
		}
		.dibu{
			width:100%;
			height:60px;
			line-height: 60px;
			text-align: center;
			color: rgb(208,208,208);
			background-color: #f1f1f1;
			font-size: 12px;
			margin-bottom: 30px;
		}
		.touxiang{
			width:25px;
		}
		.container span:last-of-type{
			display:none;
		}
		.gd{
			width:20px;
			float: right;
			margin-top: 8px;
		}
        .timedk{
        	background-color: #5ac500;
			font-size: 15px;
        }
        .exit{
        	  width: 88%;
			  height: 30px;
			  line-height: 30px;
			  background-color: #fd9e25;
			  margin: 0 auto;
			  color: #fff;
			  text-align: center;
			  border-radius: 5px;
			  margin-bottom: 28px;
			  font-size: 13px;
        }
        /*.start{
        	  width: 88%;
			  height: 30px;
			  line-height: 30px;
			  background-color: #fd9e25;
			  margin: 0 auto;
			  color: #fff;
			  text-align: center;
			  border-radius: 5px;
			  margin-bottom: 28px;
			  font-size: 13px;
			  border-color:transparent;
			  margin-left: 6%;
        }*/
        .start{
        	width: 100%;
		    height: 45px;
		    line-height: 30px;
		    background-color: #fd9e25;
		    margin: 0 auto;
		    color: #fff;
		    text-align: center;
		    border-radius: 5px;
		    bottom: 4px;
		    font-size: 13px;
		    border-color: transparent;
		    position: fixed;
        }

		/*摇一摇*/
		.shake_box {
                background: gray;
                opacity: 0.8;
                position: fixed;
                top : 0;
                left: 0;
                width  : 100%;
                height : 100%;
            }
            .shakTop,.shakBottom {
                position : fixed;
                left  : 0;
                width : 100%;
                height: 50%;
            }
            .shakTop    {top    : 0px;}
            .shakBottom {bottom : 0px;}

            .shakTop span,.shakBottom span{
                background: url(dist/img/shakBox.png) no-repeat;
                position : absolute;
                left: 50%;
                width :225px;
                height: 127px;
                margin: 0 0 0 -100px;
            }
            .shakTop    span{bottom : -1px;}
            .shakBottom span{
                background-position: 0 -127px;
                top : 0px;
            }

            .shake_box_focus .shakTop{
                animation        : shakTop 1s 1 linear;
                -moz-animation   : shakTop 1s 1 linear;
                -webkit-animation: shakTop 1s 1 linear;
                -ms-animation    : shakTop 1s 1 linear;
                -o-animation     : shakTop 1s 1 linear;
            }
            .shake_box_focus .shakBottom{
                animation        : shakBottom 1s 1 linear;
                -moz-animation   : shakBottom 1s 1 linear;
                -webkit-animation: shakBottom 1s 1 linear;
                -ms-animation    : shakBottom 1s 1 linear;
                -o-animation     : shakBottom 1s 1 linear;
            }

            /* 向上拉动画效果 */
            @-webkit-keyframes shakTop   {
                0%   {top: 0;}
                50%  {top: -200px;}
                100% {top: 0;}
            }
            @-moz-keyframes shakTop      {
                0%   {top: 0;}
                50%  {top: -200px;}
                100% {top: 0;}
            }
            @-ms-keyframes shakTop       {
                0%   {top: 0;}
                50%  {top: -200px;}
                100% {top: 0;}
            }
            @-o-keyframes shakTop        {
                0%   {top: 0;}
                50%  {top: -200px;}
                100% {top: 0;}
            }

            /* 向下拉动画效果 */
            @-webkit-keyframes shakBottom   {
                0%   {bottom: 0;}
                50%  {bottom: -200px;}
                100% {bottom: 0;}
            }
            @-moz-keyframes shakBottom      {
                0%   {bottom: 0;}
                50%  {bottom: -200px;}
                100% {bottom: 0;}
            }
            @-ms-keyframes shakBottom       {
                0%   {bottom: 0;}
                50%  {bottom: -200px;}
                100% {bottom: 0;}
            }
            @-o-keyframes shakBottom        {
                0%   {bottom: 0;}
                50%  {bottom: -200px;}
                100% {bottom: 0;}
            }

        .yaoyiyao
        {
            background: url("img/yyy.png");
            background-size: 100% 100%;
            width: 60px;
            height: 60px;
            position: relative;
            margin:auto;
            bottom: 0px;

        }
        .shake_box,.ok2,.exit,#motaikuang,#zjd,.delegg {
          cursor:pointer
        }
        @media (min-width: 414px){
	        	.shuxian2 {
					  display: block;
					  width: 2px;
					  height: 33px;
					  background-color: #A4A4A4;
					  margin-left: 7.5%;
					  /*margin-top: 21px;*/
					}
			}

		/*模态框开始*/
        .message_box{
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            display: -webkit-box;
            -webkit-box-pack: center;
            -webkit-box-align: center;
            z-index: 10;
            background-color: rgba(0, 0, 0, .4);
            -webkit-animation: fadeIn .6s ease;
        }
		.imgbg00{
			width:300px;
			height:180px;
			position: absolute;
			top:40%;
			left:50%;
			margin-left:-150px;
			margin-top: -90px;
		}
		.imgbg00>img{
			width:300px;
			height:180px;
		}
		.btn00{
			position: absolute;
			top:67%;
			left:15%;
			width:80%;
		}
		input{
			outline: none;
			-webkit-appearance: none;
		}
		textarea {  -webkit-appearance: none;}
		.ok2, .notok2{
			width:40%;
			background-color: #58c401;
			border:1px solid transparent;
			color: #fff;
			height:30px;
			line-height: 24px;
			border-radius: 15px;
			font-size: 13px;
		}
		.notok2{
			background-color:#c8c8c8;
			margin-left: 10%;
		}

		/*我的收益和积分*/
		.box11{
			font-size: 15px;
			width:100%;
			height:70px;
			line-height: 70px;
			/*background: -moz-linear-gradient(315deg, #f0a196, #f180af);
		    background: -webkit-gradient(linear,0% 0%,100% 100%,from(#f0a196),to(#f180af));
		    background: -webkit-linear-gradient(315deg, #f0a196, #f180af);
		    background: -o-linear-gradient(315deg, #f0a196, #f180af);*/
		    background: -moz-linear-gradient(315deg, #FF3030, #f180af);
		    background: -webkit-gradient(linear,0% 0%,100% 100%,from(#FF3030),to(#f180af));
		    background: -webkit-linear-gradient(315deg, #FF3030, #f180af);
		    background: -o-linear-gradient(315deg, #FF3030, #f180af);
		}
		.box22{
			font-size: 15px;
			width:100%;
			height:70px;
			line-height: 70px;
			/*background: -moz-linear-gradient(315deg, #96c645, #97dbb7);
		    background: -webkit-gradient(linear,0% 0%,100% 100%,from(#96c645),to(#97dbb7));
		    background: -webkit-linear-gradient(315deg, #96c645, #97dbb7);
		    background: -o-linear-gradient(315deg, #96c645, #97dbb7);*/
		    background: -moz-linear-gradient(315deg, #C0FF3E, #97dbb7);
		    background: -webkit-gradient(linear,0% 0%,100% 100%,from(#C0FF3E),to(#97dbb7));
		    background: -webkit-linear-gradient(315deg, #C0FF3E, #97dbb7);
		    background: -o-linear-gradient(315deg, #C0FF3E, #97dbb7);
		}
		.text10{
			color:#fff;
			margin-left: 3%;
			position: absolute;
		}
		.num2{
			font-size:30px;
			color:#fff;
			margin-left: 4%;
			position: absolute;
			left: 20%;
		}
		.tixian, .jifen{
			display: inline-block;
			height:40px;
			line-height: 40px;
			text-align: center;
			float: right;
			margin-right: 5%;
			width:30%;
			border:1px solid #fff;
			border-radius: 5px;
			margin-top: 15px;
		}
		.img3{
			width:19px;
			height:17px;
			vertical-align: middle;
		}
		.sc{
			display: inline-block;
		}

		.text11{
			display: inline-block;
			width:100%;
			height:20px;
			/*margin-top: 10px;*/
		}

		/*打卡模态框*/
		.mtk{
			position:fixed;
			left:0px;
			top:0px;
			width: 100%;
			height: 100%;
			background-color: rgba(0,0,0,.5);
			-webkit-animation: fadeIn 0.6s ease;
			z-index: 100;
		}
		.mtkcont{
			width:300px;
			height:195px;
			position: absolute;
			top:40%;
			left:50%;
			margin-top:-90px;
			margin-left: -150px;
			background-color: #fff;
			text-align: center;
			border-radius: 15px;
		}
		.mtkpic{
			width:50px;
			margin-top:3%;
		}
		.textmtk{
			margin-top: 6%;
			color:#888;
			font-size: 13px;
			width: 92%;
    		margin-left: 4%;
		}
		.queding{
			width: 60%;
			height: 30px;
			line-height: 30px;
			border-radius: 15px;
			border:1px solid transparent;
			background-color: #58c401;
			color:#fff;
			margin-top:10%;
			font-size: 13px;
		}

		/*生成海报*/
		.imgload{
			position: absolute;
			top:24%;
			left:50%;
			margin-left: -50px;
		}
		.imgload~span{
			color:#fff;
			font-size: 18px;
			position: absolute;
			top:40%;
			left:13%;
		}
		/*a链接样式*/
		a, a:link, a:visited, a:hover, a:active{
			text-decoration:none;
			color:#fff;
		}
		/* css */
/*1 未加入
2 加入了 当天已打卡 显示已打卡 第二天显示多少小时开始 $flag=1
3 加入了 当天没打卡 并且处于打卡时间内 显示打卡 第二天显示多少小时开始 $gaptime=0 $flag=0
4 加入了 当天没打卡 因为打卡时间还没开始 显示多少小时开始  $other=0  $flag=0
5 加入了 当天没打卡 因为过了打卡时间 当天打卡直接断掉 直接从第二天显示多少小时开始
				$other=1 $flag=0*/
</style>
</head>
<body>
	<div class="imgbg"></div>
	<div class="headbox">
		<div class="box1">
			<span class="num"><?=$conday?></span>
			<span class="text1">天</span>
			<div class="shuxian"></div>
			<div class="boxl1">
				<div class="text2"><?=$bidname.$conday?>天早起计划</div>
				<div class="text3"><?=$startday?>开始</div>
			</div>
		</div>
		<?if($config['shakest']==1):?>
		<div class="box2" onclick="init()">
			<img class="img2" src="dist/img/yyy.png">
		</div>
		<?endif?>
	</div>

	<div class="text5"><?=$num?>位小伙伴已参加</div>
	<div class="canjia">
		<div class="img4">
	            <ul>
	                <?php foreach ($headimg as $item):?>
	                  <li><img class="touxiang" src="<?=$item->headimgurl?>"></li>
	                <?php endforeach;?>
	            </ul>
		</div>
		<div class="yaoqing">
			<img src="dist/img/yq.png">
			<div class="poster">邀请加入</div>
		</div>
	</div>
	<div class="fenge"></div>

<!-- 	<div class="box11">
		<span class="text10">我的收益</span>
		<span class="num2"><? if(!$result['cash']){echo '0.00';}else{echo $result['cash'];}?></span>
		<a href="/dka/home"><div class="tixian">立即提现</div></a>
	</div> -->
	<div class="fenge"></div>

	<div class="box22">
		<span class="text10">我的积分</span>
		<span class="num2 jf"><? if(!$score){echo '0';}else{echo $score;}?></span>
		<a href="/dka/score">
			<div class="jifen">
				<img class="img3" src="dist/img/jfsc.png">
				<div class="sc">积分商城</div>
			</div>
		</a>
	</div>
	<div class="fenge"></div>

	<div class="shuoming">
		<div class="text6">
			<span class="text6">积分奖励说明：<br>
			<div id="box" style="display: inline-block;">
				<?=$explain?>
			</div>
			<!-- <img class="gd" src="dist/img/gd.png"> -->
		</div>
	</div>

	<script>
		function show(){
			var box = document.getElementById("box");
			var text = box.innerHTML;
			var newBox = document.createElement("div");
			var btn = document.createElement("a");
			btn.style.color="#FFC274";
			btn.style.float="right";
			newBox.innerHTML = text.substring(0,100);
			btn.innerHTML = text.length > 100 ? "...显示全部" : "";
			btn.href = "###";
			btn.onclick = function(){
				if (btn.innerHTML == "...显示全部"){
					btn.innerHTML = "收起";
					newBox.innerHTML = text;
				}else{
					btn.innerHTML = "...显示全部";
					newBox.innerHTML = text.substring(0,100);
				}
			}
			box.innerHTML = "";
			box.appendChild(newBox);
			box.appendChild(btn);
		}
		show();
	</script>


	<div class="fenge"></div>
	<div class="text8">签到计划</div>


	<div class="container">
			<!-- 已经打卡-->
		    <?php if($joinstatus==1&&$flag==1){for($i=1;$i<=($countday+$flag)%$conday;$i++){?>
		        <!-- <div class="tiaoxingkuang"> -->
		        <div class="qiandao1">
		            <!-- <div class="pic"><?=($i)?></div> -->
		            <div class="yuan">
			            <div class="text11">
			           		 <?=date('m-d',strtotime('-'.(($countday+$flag)%$conday-$i).'day'));?> <?=$rangestart?>:00
			           	</div>
		            </div>

		            <div class="text9">
		                <?=$bidname.$conday?>天早起计划 第<?=floor(($countday/$conday))*$conday+$i?>天
		            </div>
		            <div class="anniu anniu1">已打卡</div>
		            <!-- 按钮 -->
		        </div>
		        <span class="shuxian2"></span>
		    <?php }?>

		    <!-- 未开始 -->
		    <?php for($i=1;$i<=($conday-($countday+$flag)%$conday);$i++){?>
		         <div class="qiandao1">
		            <!-- <div class="pic1"><?=($countday+$flag)%$conday+$i?></div> -->
		            <div class="yuan">
		                <?=date('m-d',strtotime('+'.$i.'day'));?> <?=$rangestart?>:00
		                <!-- <?=$rangestart?>:00 -->
		            </div>
		            <div class="text9">
		                <?=$bidname.$conday?>天早起计划 第<?=($countday+$flag+$i)?>天
		            </div>
		            <!--注释1 -->
		            <?php if($i==1):?>
		                <div class="anniu anniu3" ><?=$spacetime?>小时后开始</div>
		            <?php else:?>
		                <div class="anniu anniu3" >未开始</div>
		            <?php endif?>
		        </div>
		            <span class="shuxian2"></span>
		    <?php }}?>

		<!-- 加入了 当天没打卡  -->
		    <?php if($joinstatus==1&&$flag==0&&$other==0){for($i=0;$i<=($countday+$flag)%$conday;$i++){?>
		        <div class="qiandao1">
		            <!-- <div class="pic"><?=($i+1)?></div> -->
		            <div class="yuan">
		                <?=date('m-d',strtotime('-'.(($countday+$flag)%$conday-$i).'day'));?> <?=$rangestart?>:00
		                <!-- <?=$rangestart?>:00 -->
		            </div>
		            <div class="text9">
		                <?=$bidname.$conday?>天早起计划 第<?=floor(($countday/$conday))*$conday+$i+1?>天
		            </div>

		        <?php if($gaptime===0&&$i==($countday+$flag)%$conday):?><!-- 处于打卡范围内 -->
		            <div class="anniu timedk">打卡</div>
		        <?php endif?>
		        <?php if($gaptime!==0&&$i==($countday+$flag)%$conday):?><!-- 打卡时间之前 -->
		            <div class="anniu anniu3" ><?=$gaptime?>后开始</div>
		        <?php endif?>
		        <?php if($i<($countday+$flag)%$conday):?><!-- 已打卡 -->
		            <div class="anniu anniu2">已打卡</div>
		        <?php endif?>
		        </div>
		        <span class="shuxian2"></span>
		    <?php }?>

		    <!-- 没开始 -->
		    <?php for($i=1;$i<=($conday-($countday+$flag)%$conday-1);$i++){?>
		         <div class="qiandao1">
		            <!-- <div class="pic1"><?=($countday+$flag)%$conday+$i+1?></div> -->
		            <div class="yuan">
		                <?=date('m-d',strtotime('+'.$i.'day'));?> <?=$rangestart?>:00
		            </div>
		            <div class="text9">
		                <?=$bidname.$conday?>天早起计划 第<?=($countday+$flag+$i+1)?>天
		            </div>
		            <?php if($i==1&&$gaptime==0):?>
		                <div class="anniu anniu3" ><?=$spacetime?>小时后开始</div>
		            <?php else:?>
		                <div class="anniu anniu3" >未开始</div>
		            <?php endif?>
		        </div>
		            <span class="shuxian2"></span>
		    <?php }}?>

		<!-- 加入了 当天没打卡 因为过了打卡时间 当天打卡直接断掉 直接从第二天显示多少小时开始 -->
		    <?php if($joinstatus==1&&$flag==0&&$other==1){?>
		    <?php for($i=1;$i<=($conday);$i++){?>
		         <div class="qiandao1">
		            <!-- <div class="pic1"><?=$i?></div> -->
		            <div class="yuan">
		                <?=date('m-d',strtotime('+'.($i).'day'));?> <?=$rangestart?>:00
		                 <!-- <?=$rangestart?>:00<br> -->
		            </div>
		            <div class="text9">
		                <?=$bidname.$conday?>天早起计划 第<?=($i)?>天
		            </div>
		            <?php if($i==1):?>
		                <div class="anniu anniu3" ><?=$spacetime?>小时后开始</div>
		            <?php else:?>
		                <div class="anniu anniu3" >未开始</div>
		            <?php endif?>
		        </div>
		            <span class="shuxian2"></span>
		    <?php }}?>

		    <!-- 未加入打卡计划直接显示 总天数 -->
		    <?php if($joinstatus==0){for($i=1;$i<=$conday;$i++){?>
		            <div class="qiandao1">
		                <!-- <div class="pic1"><?=$i?></div> -->
		                <div class="yuan">
		                    <?=date('m-d',strtotime('+'.($i-1).'day'));?>
		                    <?=$rangestart?>:00
		                     <!-- <?=$rangestart?>:00<br> -->
		                </div>
		                <div class="text9">
		                <?=$bidname.$conday?>天早起计划 第<?=$i?>天
		                </div>
		               <!--  <div class="anniu anniu1">打卡</div> -->
		        	</div>
		            <span class="shuxian2"></span>
		    <?php }}?>
	</div>
	<br><br><br>

    <?php if($joinstatus==0):?>
        <form method="post">
        <div class="fix">
        <input type="submit" name='join' value="加入早起计划" class="start">
        </div>
        </form>
    <?php else:?>
        <div class="exit">退出早起计划</div>
    <?php endif?>

   	<div class="dibu">
		<?=$config['copyright']?>
	</div>

<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.js"></script>
<!-- 摇一摇 -->
<script type="text/javascript">
            //先判断设备是否支持HTML5摇一摇功能
			function init(){if (window.DeviceMotionEvent) {
                //获取移动速度，得到device移动时相对之前某个时间的差值比
                window.addEventListener('devicemotion', deviceMotionHandler, false);
            }else{
                alert('您好，你目前所用的设置好像不支持重力感应哦！');
            }
            $("body").append("<div class=\"shake_box\">"+
            "<div class=\"shakTop\"><span></span></div>"+
            "<div class=\"shakBottom\"><span></span></div>"+
            "</div>"
       		 );
          }
			//设置临界值,这个值可根据自己的需求进行设定，默认就3000也差不多了
			var shakeThreshold = 3000;
			//设置最后更新时间，用于对比
			var lastUpdate     = 0;
			//设置位置速率
			var curShakeX=curShakeY=curShakeZ=lastShakeX=lastShakeY=lastShakeZ=0;
			function deviceMotionHandler(event){
				//获得重力加速
				var acceleration =event.accelerationIncludingGravity;
				//获得当前时间戳
				var curTime = new Date().getTime();
				if ((curTime - lastUpdate)> 100) {
					//时间差
					var diffTime = curTime -lastUpdate;
						lastUpdate = curTime;
					//x轴加速度
					curShakeX = acceleration.x;
					//y轴加速度
					curShakeY = acceleration.y;
					//z轴加速度
					curShakeZ = acceleration.z;
					var speed = Math.abs(curShakeX + curShakeY + curShakeZ - lastShakeX - lastShakeY - lastShakeZ) / diffTime * 10000;
					if (speed > shakeThreshold) {
						//TODO 相关方法，比如：
						//播放音效
						shakeAudio.play();
						//播放动画
						$('.shake_box').addClass('shake_box_focus');
						clearTimeout(shakeTimeout);
						var shakeTimeout = setTimeout(function(){
							$('.shake_box').removeClass('shake_box_focus');
						},1000)
					}
					lastShakeX = curShakeX;
					lastShakeY = curShakeY;
					lastShakeZ = curShakeZ;
				}
			}
			//预加摇一摇声音
			var shakeAudio = new Audio();
			    shakeAudio.src = 'sound/shake_sound.mp3';
			var shake_options = {
			    preload  : 'auto'
			}
			for(var key in shake_options){
			    if(shake_options.hasOwnProperty(key) && (key in shakeAudio)){
			        shakeAudio[key] = shake_options[key];
			    }
			}
</script>
<script type="text/javascript">
            //先判断设备是否支持HTML5摇一摇功能
            function init(){
            if (window.DeviceMotionEvent) {
                //获取移动速度，得到device移动时相对之前某个时间的差值比
                window.addEventListener('devicemotion', deviceMotionHandler, false);
            }else{
                alert('您好，你目前所用的设置好像不支持重力感应哦！');
            }
            $("body").append("<div class=\"shake_box\">"+
            "<div class=\"shakTop\"><span></span></div>"+
            "<div class=\"shakBottom\"><span></span></div>"+
            "</div>"
        );
          }
            //设置临界值,这个值可根据自己的需求进行设定，默认就3000也差不多了
            var shakeThreshold = 3000;
            //设置最后更新时间，用于对比
            var lastUpdate     = 0;
            //设置位置速率
            var curShakeX=curShakeY=curShakeZ=lastShakeX=lastShakeY=lastShakeZ=0;
            function deviceMotionHandler(event){
                //获得重力加速
                var acceleration =event.accelerationIncludingGravity;
                //获得当前时间戳
                var curTime = new Date().getTime();
                if ((curTime - lastUpdate)> 100) {
                    //时间差
                    var diffTime = curTime -lastUpdate;
                        lastUpdate = curTime;
                    //x轴加速度
                    curShakeX = acceleration.x;
                    //y轴加速度
                    curShakeY = acceleration.y;
                    //z轴加速度
                    curShakeZ = acceleration.z;
                    var speed = Math.abs(curShakeX + curShakeY + curShakeZ - lastShakeX - lastShakeY - lastShakeZ) / diffTime * 10000;
                    if (speed > shakeThreshold) {
                        //TODO 相关方法，比如：
                        //播放音效
                        shakeAudio.play();
                        //播放动画
                        $('.shake_box').addClass('shake_box_focus');
                        clearTimeout(shakeTimeout);
                        var shakeTimeout = setTimeout(function(){
                            $('.shake_box').removeClass('shake_box_focus');
                        },1000)
                  window.setTimeout(function () {
                  $.ajax({
                  url: '/dka/dka?shake=true',
                  type: 'get',
                  dataType: 'json',
                  //
                  timeout:15000,
                  success: function (res){
                        $("body").append(
                        	// "<div class=\"message_box1 mask\" >" +
                         //        "<div>" +
                         //          "<div class=\"content\">" +
                         //            res.con+
                         //          "</div>" +
                         //          "<div class=\"btns border_dark b_top\" style=\"margin-top: 20px;\">"+"<a href=\"#\" class=\"ok2\">确定</a>"+"</div>" +
                         //        "</div>" +
                         //    "</div>"
                         "<div class=\"mtk\">"+
							"<div class=\"mtkcont\">"+
								"<img class=\"mtkpic\" src=\"dist/img/right.png\">"+
								"<div class=\"textmtk\">"+res.con+"</div>"+
								"<input type=\"button\" class=\"queding ok2\" value=\"本宫知道了\">"+
							"</div>"+
						"</div>"
                         );
                        $('.jf').html(res.score);
                    }
          });  },1500);
                    }
                    lastShakeX = curShakeX;
                    lastShakeY = curShakeY;
                    lastShakeZ = curShakeZ;
                }
            }
            //预加摇一摇声音
            var shakeAudio = new Audio();
                shakeAudio.src = 'dist/music/shake_sound.mp3';
            var shake_options = {
                preload  : 'auto'
            }
            for(var key in shake_options){
                if(shake_options.hasOwnProperty(key) && (key in shakeAudio)){
                    shakeAudio[key] = shake_options[key];
                }
            }
</script>

<script language="javascript">
    window.flag=1;
        $(document).on('click', '.ok2', function() {
             $(".mtk").remove();
	    	 $('.message_box').remove();
	    	 $('.shake_box').remove();
        });
        $(document).on('click','.shake_box',function(){
            $(".shake_box").remove();
            window.removeEventListener('devicemotion', deviceMotionHandler, false);
        });
        var countday = <?=$countday?>;//用户除今日连续打卡天数
        var conday = <?=$conday?>;//商家定义打卡上限天数
        //生成海报
        $('.yaoqing').click(function() {
            $.ajax({
              url: '../api/dka?bname=<?=$Bname?>&openid=<?=$openid?>',
              type: 'post',
              dataType: 'text',
              timeout:15000,
			  beforeSend:function(XMLHttpRequest){
	            $("body").append(
                    "<div class=\"mtk\">"+
						"<img class=\"imgload\" src=\"dist/img/loading.gif\">"+
						"<span>"+'稍等下，正在为您生成海报...'+"</span>"+
					"</div>"
                 );
          	  },
              success: function (){
                    // alert('1');
                    // 模态框
                    $("body").append(
                     "<div class=\"mtk\">"+
							"<div class=\"mtkcont\">"+
								"<img class=\"mtkpic\" src=\"dist/img/right.png\">"+
								"<div class=\"textmtk\">"+"邀请卡已经生成，请前往公众号首页查看"+"</div>"+
								"<input type=\"button\" class=\"queding ok2\" value=\"本宫知道了\">"+
							"</div>"+
						"</div>"
                     	);
                    },
                    //模态框结束
                     complete:function(XMLHttpRequest,textStatus){
                        $("#loading").empty();
                    },
                     error:function(XMLHttpRequest,textStatus,errorThrown){
                        alert('当前参与人数太多，请稍后再试。');
                        // alert('当前参与人数太多，请稍后再试。'+'error...状态文本值：'+textStatus+" 异常信息："+errorThrown);
                         $("#loading").empty();
                    }
             });
        });

        //退出早起计划
        $(document).on('click', '.exit',function(){
            $("body").append(
            	"<div class=\"message_box mask\">"+
								"<div class=\"imgbg00\">"+
									"<img src=\"dist/img/exit1.png\">"+
									"<div class=\"btn00\">"+
									"<form method=\"post\">"+
										"<input type=\"button\" class=\"ok2\" value=\"继续努力\">"+
										"<input type=\"submit\" name=\"exit\" class=\"notok2\" vlaue=\"挥泪离开\">"+
									"</form>"+
									"</div>"+
								"</div>"+
				"</div>")
        });

        $('.timedk').click(function() {
            var obj = this;
            $.ajax({
              url: '/dka/dka?dka=true',
              type: 'get',
              dataType: 'json',
              timeout:15000,
              beforeSend:function(XMLHttpRequest){
              //alert('远程调用开始...');
              // $("#loading").html("<img src='dist/img/loading.gif' />");
              $("#imgload").css({'z-index':'2','display':'block','height':$("html").height(),'width': $("html").width()});
          	  },
	           // 打卡模态框
	           success: function (res){
	                    // alert(res);
	                    $("body").append(
							"<div class=\"mtk\">"+
								"<div class=\"mtkcont\">"+
									"<img class=\"mtkpic\" src=\"dist/img/right.png\">"+
									"<div class=\"textmtk\">"+res.con+"</div>"+
									"<input type=\"button\" class=\"queding ok2\" value=\"本宫知道了\">"+
								"</div>"+
							"</div>"
	                    );
	                    $(obj).removeClass().addClass("ydk");
	                    $(obj).html('已打卡');
	                    $('.jf').html(res.score);
	            },
	            complete:function(XMLHttpRequest,textStatus){
	                    $("#loading").empty();
	            },
	            error:function(XMLHttpRequest,textStatus,errorThrown){
	                    // alert('当前参与人数太多，请稍后再试。'+errorThrown);
	                    alert('当前参与人数太多，请稍后再试。');
	                        $("#loading").empty();
	            }
	      	});
        });
        // function aa(){
        //     var go = document.getElementById("aaa");
        //     var to = document.getElementById("bbb");
        //     if(go.className == "rule")
        //     {
        //         go.className = "rule1";
        //         to.innerHTML = "收起";
        //         document.getElementById('pre').style.marginTop = '55px';
        //         return;
        //     }
        //     else{
        //         go.className = "rule";
        //     to.innerHTML = "查看更多";
        //     document.getElementById('pre').style.marginTop = '18px';
        //     return;
        // }
    </script>

    <!--砸金蛋部分-->
    <script>
        $(document).ready(function(){
        	// 模态框获取屏幕宽高
        	window.h=$(window).height();
			$('.bg00').css("height",h);
            var eggflag = <?=$eggflag?>;
            var eggst = <?=$config['eggst']?>;
            if(eggflag==1&&eggst==1){
                $("body").append(
                	"<div class=\"message_box\" >" +
                   		 "<img class=\"egg1\" src=\'dist/img/egg_1.png\' style=\'width:50%;height:auto;left:25%;top:20%;position:absolute;\' id=\'zjd\'/>"+
                    "</div>"
				);
                $(document).on('click', '#zjd', function() {
                    //请求后台数据
                    $('#zjd').attr('src','dist/img/egg_2.png');
                    // $(".message_box").remove();
                  window.setTimeout(function () {
                     $.ajax({
                      url: '/dka/dka?egg=true',
                      type: 'get',
                      dataType: 'json',
                      timeout:15000,
	                  success: function (res){
	                    $("body").append(
	                        "<div class=\"mtk\">"+
								"<div class=\"mtkcont\">"+
									"<img class=\"mtkpic\" src=\"dist/img/right.png\">"+
									"<div class=\"textmtk\">"+res.con+"</div>"+
									"<input type=\"button\" class=\"queding ok2\" value=\"本宫知道了\">"+
								"</div>"+
							"</div>"
	                    	);
	                    $('.jf').html(res.score);
	                  }
            });},500);
        });
    }
});
</script>
</body>
</html>













