<?php
$name = $config['title1'];
if($user2['aid']){

  $agent = ORM::factory('yty_agent')->where('bid','=',$bid)->where('id','=',$user2['aid'])->find();
  $name=$agent->skus->name;
}
    $fuser[] = "{$user2['nickname']}";
//获取推荐用户
if ($user2['fopenid']){
  $fuser1= ORM::factory('yty_qrcode')->where('openid', '=', $user2['fopenid'])->find();
  $name1=$fuser1->agent->name;
}
//是否火种用户
// $id = ORM::factory('yty_qrcode')->where('bid','=',$bid)->where('lv','=',1)->where('id','<=',$user2['id'])->find_all()->count();
$id = $user2['fid'];
if ($fuser1) {
  $fusername =" 所属上级经销商：".$name1;
}else {
  $fusername ="所属上级经销商：品牌方";
}
$fuser[] = '经销商等级：'.$name;
if ($fuser) $fuser = join(' / ', $fuser);

$result['num2']=ORM::factory('yty_qrcode')->where('bid', '=', $bid)->where('lv','=',2)->where('fopenid','=',$user2['openid'])->count_all();
$result['num1']=ORM::factory('yty_trade')->where('bid', '=', $bid)->where('flag','=',0)->where('fopenid','=',$user2['openid'])->count_all();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>经销商中心</title>
    <link rel="stylesheet" type="text/css" href="/yty/css/loaders.min.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/loading.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/base.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/style.css"/>
    <script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
				<style type="text/css">
				.fixed-contb {
    	margin-bottom: 0px;
    	background: white;
				}
				html{
					background: white;
				}
				dt{
					/*margin-top: 20px;*/
				}
				.name{
					margin-top: 10px;
				}
				hr{
				    margin: 0px;
				    /*background-color: #e0a778;*/
				    width: 94%;
				    margin-left: 3%;
				    height: 0px;
				    border:0px;
				    border-bottom: 1px solid #e0a778;
				}
				.nname,.nfor,.nlv{
					display: block;
					color:white;
				}
				.ntext{
					width: 65%;
    text-align: left;
    color: white;
				}
				.nname{
				font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
				}
				.nfor,.nlv{
				margin-bottom: 5px;
    font-size: 13px;
				}
				.tu2{
					    width: 75px;
				    height: 75px;
				    margin: 0 auto;
				    text-align: center;
				    border-radius: 50%;
				    -webkit-border-radius: 50%;
				    overflow: hidden;
				    border: 4px solid #fdf3eb;
		   }
		   .tu{
		   	width: 70px;
		    height: 70px;
		    margin: 0 auto;
		    text-align: center;
		    border-radius: 50%;
		    -webkit-border-radius: 50%;
		    overflow: hidden;
		    /* border: 1px solid #56FF00; */
		    border-radius: 50%;
		    margin-top: 3px;
		    margin-left: 3px;
		   }
		   dl{
		   	margin: 10px 0px;
		   }
		   dt{
		   	display: flex;
    		align-items: center;
		   }
		   .copyright{
		   	text-align: center;
		   	line-height: 20px;
		   	font-size: 10px;
		   	color:#868282;
		   	background: white;
		   	padding-top: 20px;
		   }
		   .circle{
		   	    color: white;
    position: relative;
    bottom: 75%;
    left: 55%;
    border-radius: 15px;
    border: 1px solid #DC4000;
    width: 15px;
    background: #DC4000;
    height: 15px;
    margin: 0px;
    padding: 0px;
    line-height: 15px;
		   }
				</style>
    <script type="text/javascript">
    	$(window).load(function(){
    		$(".loading").addClass("loader-chanage")
    		$(".loading").fadeOut(300)
    	})
    </script>
</head>
<!--loading页开始-->
<?php if($user2['lv']==2):?>
<div class="page"><!-- 审核中 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_waiting"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">审核中</h2>
            <p class="weui_msg_desc">对不起，您的资格还在审核中</p>
        </div>
    </div>
</div>
<?php endif?>
<?php if($user2['lv']==3):?>
<div class="page"><!-- 被取消 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_warn"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">很遗憾</h2>
            <p class="weui_msg_desc">对不起，您的资格已被取消，请联系管理员</p>
        </div>
    </div>
</div>
<?php endif?>
<!--loading页结束-->
<?php if($user2['lv']==1):?>
<!--  <div style=" width:50px; height:50px; background-color:#F00; border-radius:25px;">
         <span style="height:50px; line-height:50px; display:block; color:#FFF; text-align:center">4</span>
    </div> -->
<body>
	<div class="p-top  clearfloat">
		<div class="tu2">
				<div class="tu">
				 <img src="<?=$user2['headimgurl']?>" style="width:75px" />
				 </div>
			</div>
			<div class='ntext'>
				<div clas='nname' style="font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;"><?=$user2['nickname']?></div>
				<div clas='nfor' style="margin-bottom: 5px;
    font-size: 13px;"><?=$fusername?></div>
				<div clas='nlv' style="margin-bottom: 5px;
    font-size: 13px;">经销商等级：<?=$name?></div>
			</div>
			<!-- <p class="name"><?=$fuser?></p> -->

	</div>

	<div class="contaniner fixed-contb">
		<section class="self">
			<dl>
				<dt >

						<img src="/yty/icon/zichan.png"/>
						<b style='font-size: 1.3em;
    			color: #909090;'>我的资产</b>
						<span style='color:#A2A2A2;'>业绩、收益、提现等</span>

				</dt>
				<hr>
				<dd>
						<ul>
							<li style="padding-left:10%;">
								<a href="/yty/inputmoney">
									<img src="/yty/icon/1.png"/>
									<!-- <p>进货额</p> -->
									<p><?=number_format($result['stock'], 2)?></p>
								</a>
							</li>
							<li>
								<img src="/yty/icon/2.png"/>
								<!-- <p>进货额</p> -->
								<p><?=number_format($result['money_nopaid'], 2)?></p>

							</li>
							<li style="padding-right:10%;">
								<a href="/yty/money">
									<img src="/yty/icon/3.png"/>
									<!-- <p>可icon提现收益</p> -->
									<p><?=number_format($result['money_now'], 2)?></p>
								</a>
							</li>
						</ul>
				</dd>
			</dl>

			<dl>
				<dt style='padding-top:5%'>

						<img src="/yty/icon/tuandui.png"/>
						<b style='font-size: 1.3em;
    			color: #909090;'>团队管理</b>
						<!-- <span><img src="/yty/images/right.png"/></span> -->

				</dt>
				<hr>
				<dd>
						<ul>
							<li>
								<a href="/yty/customer1">
									<img src="/yty/icon/4.png"/>
									<!-- <p>我的经销商</p> -->
								</a>
							</li>
							<li>
								<a href="/yty/team?member=1">
									<img src="/yty/icon/5.png"/>
									<div class='circle'><?=$result['num2']?></div>
									<!-- <p>邀请成为下级经销商</p> -->
								</a>
							</li>
							<li>
								<a href="/yty/team">
									<img src="/yty/icon/6.png"/>
									<!-- <p>待审核的经销商申请</p> -->
								</a>
							</li>
							<li>
								<a href="/yty/top">
									<img src="/yty/icon/9.png"/>
									<!-- <p>待处理的进货申请</p> -->
									<!-- <p><?=(int)$result['money']?></p> -->

								</a>
							</li>
						</ul>
				</dd>
			</dl>
			<dl>
				<dt style='padding-top:5%'>

						<img src="/yty/icon/dingdan.png"/>
						<b style='font-size: 1.3em;
    			color: #909090;'>进销管理</b>
						<!-- <span><img src="/yty/images/right.png"/></span> -->
				</dt>
				<hr>
				<dd>
						<ul>
							<li>
								<a href="/yty/orders">
									<img src="/yty/icon/7.png"/>
									<!-- <p><?=(int)$user2['trades']?></p> -->
									<!-- <p>我要进货</p> -->
								</a>
							</li>
							<li>
								<a href="/yty/mstock">
									<img src="/yty/icon/10.png"/>
									<!-- <p>我的订单</p> -->
								</a>
							</li>
							<li>
								<a href="/yty/stock?stock=1">
									<img src="/yty/icon/11.png"/>
									<!-- <p>我的客户</p> -->
								</a>
							</li>
							<li>
								<a href="<?=$config['dp_url']?>">
									<img src="/yty/icon/12.png"/>
									<!-- <p>待确认订单</p> -->
								</a>
							</li>
						</ul>
				</dd>
			</dl>
		</section>
	</div>
	<div class='copyright'>
				<!-- 经销查询&nbsp|&nbsp公司简介 -->
	</div>
	<div style="height:65px;background:white"></div>
	<footer class="page-footer fixed-footer">
		<ul style="height: 0px;">
			<li class="active" >
				<a href="/yty/home">
					<img src="/yty/images/footer02.png"/>
					<p style="font-size: 14px;line-height:14px;">个人中心</p>
				</a>
			</li>

			<li  style="font-size: 14px;line-height:14px;">
				<a href="<?='http://'.$_SERVER["HTTP_HOST"].'/yty/storefuop/'.$bid?>">
					<img src="/yty/images/footer004.png"/>
					<p style="font-size: 14px;line-height:14px;">推荐商品</p>
				</a>
			</li>
		</ul>
	</footer>

<?php endif?>
</body>
</html>
