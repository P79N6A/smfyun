<!doctype html>
<html lang="zh">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
  <title>语音红包</title>

<link rel="stylesheet" type="text/css" href="../qwt/yyhb/css/font-awesome.min.css">
<!-- <link rel="stylesheet" type="text/css" href="css/demo.css"> -->
<link rel="stylesheet" type="text/css" href="../qwt/yyhb/css/icons.css">
<style type="text/css">
	html,body{
		margin: 0;
		padding: 0;
		background-color: #fcfdff;
    -moz-user-select:none;-webkit-user-select:none;
}
	.title{
		color: #fff;
		background-color: red;
		width: 100%;
		text-align: center;
		font-size: 18px;
		line-height: 24px;
		padding: 4px 0;
	}
	.top{
		width: 100%;
		background-color: red;
		text-align: center;
		/*padding-bottom: 40px;*/
	}
	.top img{
		width: 80px;
		height: 80px;
		border-radius: 40px;
		border: 2px solid #ffc4b7;
		margin-top: 8px;
	}
	.from{
		color: #ffc4b7;
		margin-top: 12px;
		font-size: 18px;
	}
	.code{
		color: #ffe4ac;
		height: 24px;
		margin-top: 12px;
	}
	.code img{
		height: 20px;
		border: 0;
		width: auto;
		margin: 0;
	}
	.code span{
		font-size: 18px;
		height: 24px;
	}
	.result, .result1{
		z-index: 1;
		width: 90%;
		margin: 0 5%;
		margin-top: 20px;
		background-color: #fff;
		color: green;
		text-align: center;
		padding: 20px 0;
		font-size: 14px;
		border-radius: 20px;
  box-shadow: 0 3px 6px rgba(0,0,0,0.2);
  border: 0;
	}
	.red{
		color: red;
	}
	.count{
		color: #999;
		padding: 10px 20px;
		font-size: 12px;
	}
	.count span{
		/*float: right;*/
		margin-left: 5px;
	}
	aside{
		padding:  0 20px;
		width: calc(100% - 40px);
		margin-top: 0;
	}
	li{
		background-color: #fff;
		border: 1px solid #eee;
		list-style-type: none;
		border-radius: 5px;
		margin-bottom: 10px;
		padding: 10px;
		height: 50px;
		position: relative;
	}
	li img{
		width: 50px;
		height: 50px;
		border-radius: 5px;
		/*border: 1px solid #eee;*/
		float: left;
	}
	.name{
		font-weight: bold;
		font-size: 13px;
		margin-left: 60px;
		/*float: left;*/
	}
	.bubble, .bubbleold{
		color: #777;
		background-color: #f8f8f8;
		border-radius: 5px;
		border: 1px solid #c6c6c6;
		float: left;
		padding: 6px 10px;
		margin-left: 10px;
		width: 60px;
		text-align: left;
		margin-top: 5px;
	}
	.zan{
		float: right;
	}
	.zaned, .selected{
		color: #F35186;
	}
	.more{
  text-align: center;
  padding: 5px 0 20px;
  color: #888;
	}
	.gift{
		position: absolute;
		top: 20px;
		left: 160px;
		color: #333;
		font-size: 12px;
		font-weight: bold;
	}
	.time{
		position: absolute;
		top: 40px;
		left: 160px;
		color: #999;
		font-size: 12px;
	}
	.count a{
		text-decoration: none;
		color: blue;
	}
	.eventtime{
		color: #ababab;
		text-align: center;
		/*margin-top: 10px;*/
		font-size: 12px;
    padding-bottom: 10px;
    /*border-bottom: 1px solid #ababab;*/
	}
  .muilty{
    width: calc(25% - 1px);
    color: #878787;
    /*margin-left: 20px;*/
    display: inline-block;
    border-bottom: 1px solid #efefef;
    border-right: 1px solid #efefef;
    padding: 20px 0;
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 10px;
  }
  .muilty img{
    height: 12px;
    margin-right: 5px
  }
	.luyin{
		position: fixed;
		display: none;
    left: 50%;
    top: 1em;
    z-index: 2;
    margin-left: -2em;
    /*margin-top: -4em;*/
    width: 4em;
    height: 4em;
	}
 .fadeloading{
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    z-index: 1;
 }
 .fadeloading .fadeshibie{
    position: fixed;
    left: 20%;
    top: 30%;
    width: 40%;
    background-color: #fff;
    border-radius: 5px;
    padding: 30px 10%;
    color: #666;
    text-align: center;
 }
 .fademsg{
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    z-index: 1;
 }
 .fademsg .fadebox{
    position: fixed;
    left: 10%;
    top: 30%;
    width: 60%;
    background-color: #fff;
    border-radius: 5px;
    padding: 30px 10%;
    color: #666;
    text-align: center;
    border: 3px solid #ff5200;
 }
 .fadebox .fadetitle{

    /*position: absolute;*/
    /*top: -10px;*/
    /*left: 10px;*/
    margin: -40px 0 40px 0;
    color: #fff;
    background-color: #ffd331;
    padding: 5px 10px;
    border-radius: 5px;
    /* border: 1px solid #ededed; */
    font-weight: bold;
    font-size: 18px;
 }
 .fadebox .fadecontent{
  margin-bottom: 30px;
 }
 .fadebox .fadebutton{
    width: 100%;
    padding: 5px 20px;
    background-color: #4fe7ff;
    color: #fff;
    /* margin-top: 30px; */
    border-radius: 8px;
    border: 1px solid rgba(0,0,0,.2);
 }
.add{
    background-color: #fff;
    position: relative;
    height: 60px;
    border-bottom: 1px solid #f4f4f4;
}
.addimg{
    float: left;
    width: 40px;
    height: 40px;
    padding: 10px;
}
.addtext{
    line-height: 60px;
    color: #666666;
    font-size: 14px;
    float: left;
}
.goin{
    float: right;
    width: 20px;
    height: 20px;
    padding: 20px;
}
.edit{
    background-color: #fff;
    position: relative;
    border-bottom: 1px solid #f4f4f4;
}
.position{
    width: 4%;
    height: 15px;
    padding-left: 3%;
    padding-right: 2%;
    padding-top: 15px;
    display: inline-block;
    float: left;
}
.address{
    display: inline-block;
    width: 84%;
    padding: 10px 0 10px 0;
}
.basic{
    font-size: 14px;
    color: #333;
}
.phone{
    float: right;
}
.location{
    margin-top: 10px;
    font-size: 12px;
    color: #999;
}
.goin2{
    float: right;
    width: 3%;
    height: 10px;
    padding-left: 1%;
    padding-right: 2%;
    padding-top: 30px;
}
.submit{
    width: 100%;
    height: 40px;
    line-height: 40px;
    font-size: 16px;
    background-color: #04be02;
    color: #fff;
    border: 0;
}
.fadeadress{
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,.5);
    z-index: 1;
}
.wodeshiwu{
  position: absolute;
  top: 0;
  right: 0;
  color: white;
  line-height: 20px;
  font-size: 14px;
  padding: 5px;
}
.moreshop{
  border-right-width: 0;
}
.hold{
  background-color: #c5c5c5;
}
.eventdetailbox{
  /*text-align: center;*/
  padding: 0 30px;
}
.eventdetailbox h3{
  margin: 10px;
  font-size: 14px;
  color: #333;
  font-weight: bold;
  text-align: center;
}
.myprizebox h3{
  margin: 10px;
  font-size: 14px;
  color: #333;
  font-weight: bold;
}
.grid h3{
  margin: 10px;
  font-size: 14px;
  color: #333;
  font-weight: bold;
  text-align: center;
}
.eventdetailbox span{
  font-size: 12px;
  color: #666;
}
.myprizebox{
  /*text-align: center;*/
  padding: 0 20px;
}
.onemyprize{

    width: calc(100% - 20px);
    padding: 10px 10px;
    border-bottom: 1px solid #efefef;
}
.prizename{
    display: inline-block;
    color: #878787;
    font-size: 13px;
    line-height: 15px;
}
.prizetime{

    display: inline-block;
    float: right;
    font-size: 12px;
    color: #acacac;
    line-height: 15px;
}
.fadeunstart{
  z-index: 3;
  width: 100%;
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  background-color: #000;
  color: #fff;
}
.fadeunstart div{
  top: 45%;
  width: 100%;
  text-align: center;
  font-size: 18px;
  font-weight: bold;
  position: absolute;
}
</style>
</head>
<body>

<!-- <div class="title">
	语音红包
</div> -->
<?php if (!$result['task']->id):?>
<div class="fadeunstart">
<div>尚未开始，敬请期待</div>
</div>
<?php endif?>
<div class="fadeloading" style="display:none">
 <div class="fadeshibie">
  识别中，请稍候……
 </div>
</div>
<div class="fademsg" style="display:none">
 <div class="fadebox">
  <div class="fadetitle">标题</div>
  <div class="fadecontent">内容</div>
  <a class="fadebutton">按钮</a>
 </div>
</div>
<!-- <div class="fadeadress" style="display:none">
  <form method="post">
            <div class="edit" style="display:none">
                <image class="position" src="/qwt/wfb/position.png"></image>
                <div class="address">
                    <div class="basic">
                        <text class="name">收货人：</text>
                        <text class="phone">电话：</text>
                    </div>
                    <div class="location">收货地址：</div>
                </div>
                <image class="goin2" src="/qwt/wfb/goin.png"></image>
            </div>
            <div class="add">
                <image class="addimg" src="/qwt/wfb/add.png"></image>
                <div class="addtext">点击添加地址</div>
                <image class="goin" src="/qwt/wfb/goin.png"></image>
            </div>
            <input id="name"  type="hidden" name="receive_name" >
            <input id="tel" type="hidden" name="tel" >
            <input id="prov" class='prov' type="hidden" name="s_province" >
            <input id="city" class='city' type="hidden" name="s_city" >
            <input id="dist" class='dist' type="hidden" name="s_dist">
            <input id="address"  type="hidden" name="address">
            <input id="oid"  type="hidden" name="oid">
            <button class="submit" type="submit">提交</button>
  </form>
</div> -->
<div class="top">
  <!-- <a href="http://yingyong.smfyun.com/qwtyyhb/wodeshiwu"><div class="wodeshiwu">我的实物奖品</div></a> -->
	<img src='<?=$headimg?>'>
	<div class='from'><span><?=$weixin_name?></span>发的语音红包</div>
	<div class="code"><img src="../qwt/yyhb/yuyin.png"><span>口令：<?=$result['task']->keyword?></span></div>
<?php if ($prized->id):?>
<a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtyyhb/shiwu/<?=$prized->id?>" style="text-decoration:none"><div class="result1" style="color:#ff812f;">您获得了：<?=$prized->item_name?></div></a>
<?php elseif ($yihan->id):?>
<div class="result1" style="color:#ff812f;">很遗憾，您没有中奖</div>
<?php else:?>
<div class="result" style="border:2px solid">长按此处，说出包含口令的句子即有机会领取奖品</div>
<?php endif?>
</div>
<img class="luyin" src="../qwt/yyhb/luyin.png"></img>
<div class="bottom">
 <div class="eventtime">
  <div class="muilty recordlist"><img src="/qwt/yyhb/list.png">中奖语音</div><div class="muilty eventdetail"><img src="/qwt/yyhb/detail.png">活动说明</div><div class="muilty myprize"><img src="/qwt/yyhb/record.png">我的奖品</div><a href="<?=$result['task']->shopurl?>" style="text-decoration:none;"><div class="muilty moreshop"><img src="/qwt/yyhb/more.png">更多福利</div></a>
  活动时间：<?=date('Y-m-d H:i',$result['task']->begintime)?> ~ <?=date('Y-m-d H:i',$result['task']->endtime)?>
 </div>
 <div class="mainbox myprizebox" style="display:none">
  <?php if ($prize[0]->id):?>
    <?php foreach ($prize as $k => $v):?>
      <?php if ($v->item->type==1):?>
        <a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtyyhb/shiwu/<?=$v->id?>">
      <div class="onemyprize">
        <div class="prizename"><?=$v->item_name?></div>
        <div class="prizetime"><?=date("Y-m-d H:i:s",$v->lastupdate)?></div>
      </div>
      </a>
    <?php else:?>
      <div class="onemyprize">
        <div class="prizename"><?=$v->item_name?></div>
        <div class="prizetime"><?=date("Y-m-d H:i:s",$v->lastupdate)?></div>
      </div>
    <?php endif?>
  <?php endforeach?>
<?php else:?>
  <h3 style="text-align:center;">暂无中奖记录</h3>
<?php endif?>
</div>
 <div class="mainbox eventdetailbox" style="display:none">
   <h3>活动说明</h3>
   <span style="white-space:pre"><?=$result['task']->detail?></span>
</div>
 <div class="recodebox mainbox">
	<div class="count"><a style="<?=$_GET['order_by']=='jointime'||!$_GET['order_by']?'color:#F35186':'color:#999'?>" href="?order_by=jointime"><span class="bytime">按最新↓</span></a><span>|</span><a style="<?=$_GET['order_by']=='zan'?'color:#F35186':'color:#999'?>" href="?order_by=zan"><span class="byhot">按热度↓</span></a></div>
	<aside class="grid">
  <?php if ($result['recodes'][0]->id):?>
  <?php foreach ($result['recodes'] as $k => $v):?>
		<li>
			<img src="<?=$v->user->headimgurl?>">
			<div class="name"><?=$v->user->nickname?></div>
			<div class="bubbleold" data-id="<?=$v->id?>"><?=intval($v->audioTime + 1)?>"</div>
   <audio autobuffer preload="auto" id='audio<?=$v->id?>' src="http://<?=$_SERVER['HTTP_HOST']?>/qwtyyhb/audio/record/<?=$v->id?>"></audio>
			<div class="gift"><?=$v->prize_name?></div>
			<div class="time"><?=date("Y-m-d H:i",$v->jointime)?></div>
			<div class="zan">
				<button data-zan="1" data-id="<?=$v->id?>" class="icobutton zan icobutton--heart" style="font-size:1em;margin-right:1em;"><span class="fa fa-heart"></span><span class="icobutton__text icobutton__text--side"><?=$v->zan?></span></button>
			</div>
		</li>
 <?php endforeach?>
<?php else:?>
  <h3>暂无记录</h3>
<?php endif?>
	</aside>
<div class="more" data-page="2">
</div>
</div>
</div>

<!-- <script src="js/mo.min.js"></script>
<script src="js/demo.js"></script> -->
		<script src="http://jfb.dev.smfyun.com/sqb/finishing/js/jquery.min.js"></script>
		<script src="http://jfb.dev.smfyun.com/sqb/finishing/js/imagesloaded.pkgd.min.js"></script>
  <script src="http://jfb.dev.smfyun.com/sqb/finishing/js/masonry.pkgd.min.js"></script>
  <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
  <script type="text/javascript" src="https://www.w3cways.com/demo/vconsole/vconsole.min.js?v=2.2.0"></script>
<script type="text/javascript">
$('.eventdetail').click(function(){
  $('.mainbox').hide();
  $('.eventdetailbox').show();
})
$('.recordlist').click(function(){
  $('.mainbox').hide();
  $('.recordlistbox').show();
})
$('.myprize').click(function(){
  $('.mainbox').hide();
  $('.myprizebox').show();
})
    $('.add,.edit').click(function() {
        wx.openAddress({
            success: function (res) {
                $('#name').val(res.userName);
                $('#tel').val(res.telNumber);
                $('.prov').val(res.provinceName);
                $('.city').val(res.cityName);
                $('.dist').val(res.countryName);
                $('#address').val(res.detailInfo);
                var userName = res.userName; // 收货人姓名
                // var postalCode = res.postalCode; // 邮编
                var provinceName = res.provinceName; // 国标收货地址第一级地址（省）
                var cityName = res.cityName; // 国标收货地址第二级地址（市）
                var countryName = res.countryName; // 国标收货地址第三级地址（国家）
                var detailInfo = res.detailInfo; // 详细收货地址信息
                // var nationalCode = res.nationalCode; // 收货地址国家码
                var telNumber = res.telNumber; // 收货人手机号码
                if(userName&&provinceName&&cityName&&detailInfo&&telNumber){
                    $('.name').text('收货人：'+userName);
                    $('.phone').text('电话：'+telNumber);
                    $('.location').text('地址：'+provinceName+cityName+countryName+detailInfo);
                    $('.add').css({
                        'display': 'none'
                    });
                    $('.edit').css({
                        'display': 'block'
                    });
                }
            }
        });
    });
 var tid = <?=$result['task']->id?>;
 wx.config({
    debug: 0,
    appId: '<?php echo $jsapi["appId"];?>',
    timestamp: '<?php echo $jsapi["timestamp"];?>',
    nonceStr: '<?php echo $jsapi["nonceStr"];?>',
    signature: '<?php echo $jsapi["signature"];?>',
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中
      'checkJsApi',
      'startRecord',
      'stopRecord',
      'playVoice',
      'uploadVoice',
      'downloadVoice',
      'onMenuShareTimeline',
      'onMenuShareAppMessage'
      ]
});
wx.ready(function(){
    wx.onMenuShareTimeline({//朋友圈分享
        title: "<?=$result['task']->sharetitle?>", // 分享标题
        link: "http://<?=$_SERVER['HTTP_HOST']?>/smfyun/user_snsapi_userinfo/<?=$result['task']->bid?>/yyhb/yyhb", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: "http://<?=$_SERVER['HTTP_HOST']?>/qwtyyhb/images/task/<?=$result['task']->id?>?time=<?=time()?>", // 分享图标
        success: function () {
        // 用户确认分享后执行的回调函数
        // alert('分享成功');
        },
        cancel: function () {
         // alert('用户取消');
        // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareAppMessage({//分享给朋友
      title: "<?=$result['task']->sharetitle?>", // 分享标题
      desc: "<?=$result['task']->sharetext?>", // 分享描述
      link: "http://<?=$_SERVER['HTTP_HOST']?>/smfyun/user_snsapi_userinfo/<?=$result['task']->bid?>/yyhb/yyhb", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
      imgUrl: "http://<?=$_SERVER['HTTP_HOST']?>/qwtyyhb/images/task/<?=$result['task']->id?>?time=<?=time()?>", // 分享图标
      success: function () {
      // 用户确认分享后执行的回调函数
      // alert('分享成功');
      },
      cancel: function () {
       // alert('用户取消');
      // 用户取消分享后执行的回调函数
      }
      });
  })
// var luyin = 0;
// $('.result').click(function(){
// 	if (luyin==0) {
// 		console.log('start');
// 		$(this).addClass('red');
// 		$(this).text('点击停止录音');
// 		$('.luyin').show();
// 		wx.startRecord();
// 		luyin = 1;
// 	}else{
// 		console.log('end');
// 		$(this).removeClass('red');
// 		$(this).text('按下开始录音，说出包含关键词的句子有机会获奖');
// 		$('.luyin').hide();
// 		wx.stopRecord({
// 			success: function (res) {
// 				var localId = res.localId;
// 				wx.uploadVoice({
//       localId: localId, // 需要上传的音频的本地ID，由stopRecord接口获得
//       isShowProgressTips: 1, // 默认为1，显示进度提示
//       success: function (res) {
//         $('.fadeshibie').text('识别中，请稍候……')
//         $('.fadeloading').show();
//         var serverId = res.serverId; // 返回音频的服务器端ID
//         $.ajax({
//          url: '/qwtyyhb/yydown',
//          type: 'POST',
//          dataType: 'json',
//          data: {serverId: serverId,tid:tid},
//         })
//         .done(function(res) {
//          console.log("success");
//          var interval=self.setInterval(
//           function ajax(){
//            $.ajax({
//             url: '/qwtyyhb/ajax',
//             type: 'POST',
//             dataType: 'json',
//             data: {request_id: res.request_id},
//            })
//            .done(function(res){
//             if (res.status!='no') {
//              console.log(res);
//              clearInterval(interval);
//              console.log('finish');
//              if (res.error) {
//                $('.fadeloading').hide();
//               $('.fademsg').show();
//               $('.fadetitle').text('出错了');
//               $('.fadecontent').text(res.error);
//               $('.fadebutton').text('我知道了');
//               $('.fadebutton').click(function(){
//                 $('.fademsg').hide();
//               })
//              }else{
//                $('.fadeloading').hide();
//               $('.fademsg').show();
//               $('.fadetitle').text('恭喜您');
//               $('.fadecontent').text(res.content);
//               $('.fadebutton').text('我知道了');
//               $('.fadebutton').click(function(){
//                 $('.fademsg').hide();
//               })
//               if (res.type==7||res.type==6) {
//                 $('.fadebutton').text('点击领取');
//                 $('.fadebutton').click(function(){
//                   window.location.href = res.log;
//                 })
//               };
//                if (res.type==1) {
//                 $('.fadebutton').text('点击领取');
//                 $('.fadebutton').click(function(){
//                   window.location.href = "http://yingyong.smfyun.com/qwtyyhb/shiwu/"+res.oid;
//                 })
//                }
//              }
//             };
//            })
//           },3000)
//         })
//         .fail(function() {
//          console.log("error");
//         })
//         .always(function() {
//          console.log("complete");
//         });
//         // alert(serverId);
//       }
//     });
// 			}
// 		});
// 		luyin = 0;
// 	}
// })


		var page = 2;
		$(function(){
    /*瀑布流初始化设置*/
	var $grid = $('.grid');
    // layout Masonry after each image loads
	// $grid.imagesLoaded().done( function() {
	// 	console.log('uuuu===');
	//   $grid.masonry('layout');
	// });
	   var pageIndex = 0 ; var dataFall = [];
	   var totalItem = 10;
	   $(window).scroll(function(){
	   	// $grid.masonry('layout');
                var scrollTop = $(this).scrollTop();var scrollHeight = $(document).height();var windowHeight = $(this).height();
                if(scrollTop + windowHeight == scrollHeight){
                 $('.more').text('加载中……');
                        $.ajax({
	               		dataType:"json",
            						data: {page:page},
				        type:'post',
				        url:'../qwtyyhb/yyhb',
			            success:function(result){
                if (result==null) {
                 $('.more').text('没有更多了');
                }else{
                 page++;
                 dataFall = result;
                 setTimeout(function(){
                  appendFall();
                 },500);
                 $('.more').text('加载更多');
                }
			            },
			            error:function(e){
			            	console.log('请求失败');
                $('.more').text('没有更多了');
			            }

	                   })

                }

         })
        function appendFall(){
          $.each(dataFall, function(index ,value) {
           console.log(value);
          	var dataLength = dataFall.length;
         //  	$grid.imagesLoaded().done( function() {
	        // $grid.masonry('layout');
	        //    });
	      var detailUrl;
         var $griDiv = $("<li>")
      	  var $img = $("<img class='item-img'>");
      	  $img.attr('src',value.headimgurl).appendTo($griDiv);
      	  var $p1 = $("<div class='name'>");
      	  $p1.html(value.nickname).appendTo($griDiv);
      	  var $p2 = $("<div class='bubble' data-id='"+value.id+"'>");
      	  $p2.html(Math.ceil(value.audioTime + 1)+'"').appendTo($griDiv);
         var $audio = $('<audio autobuffer preload="auto" id="audio'+value.id+'" src="'+value.mp3+'">');
         $audio.appendTo($griDiv);
      	  var $p3 = $("<div class='gift'>");
      	  $p3.html(value.prize_name).appendTo($griDiv);
         var $p4 = $("<div class='time'>");
         $p4.html(value.jointime).appendTo($griDiv);
         var $section = $('<div class="zan">');
         $section.appendTo($griDiv);
         var $button = $('<button data-zan="1" data-id="'+value.id+'" class="icobutton zan icobutton--heart" style="font-size:1em;margin-right:1em;">');
         $button.appendTo($section);
         var $heart = $('<span class="fa fa-heart">');
         $heart.appendTo($button);
         var $text = $('<span class="icobutton__text icobutton__text--side">');
         $text.html(value.zan).appendTo($button);
      	  var $items = $griDiv;
		  $items.imagesLoaded().done(function(){
				 // $grid.masonry('layout');
	             $grid.append( $items )
              // .masonry('appended', $items);
         $('.bubble').click(function(){
          console.log(this);
          var aidnew = $(this).data('id');
          console.log(aidnew);
          var audionew = document.getElementById('audio'+aidnew);
          console.log(audionew);
          audionew.play();
         })
$('.icobutton').click(function(){
 var zan = $(this).data('zan');
 var id = $(this).data('id');
 if (zan == 1) {
      var num = $(this).children('span.icobutton__text')[0];
      var val = Number(num.innerHTML)
      num.innerHTML = val + 1;
      $(this).addClass('zaned');
      $(this).data('zan',2);
  $.ajax({
   dataType:"json",
   type:"post",
   url:'../qwtyyhb/yyhb',
   data:{zan: zan,rid: id},
    success:function(result){
      console.log('请求成功')
    },
    error:function(e){
      console.log('请求失败')
    }
  })
 // }else{
 //  $(this).removeClass('zaned')
 //  $(this).data('zan',1);
 //  var num = $(this).children('span.icobutton__text')[0];
 //  var val = Number(num.innerHTML)
 //  num.innerHTML = val - 1;
 }
})
			})
           });
        }
})
         $('.bubbleold').click(function(){
          var aid = $(this).data('id');
          console.log(aid);
          var audio = document.getElementById('audio'+aid);
          console.log(audio);
          audio.play();
         })


$('.icobutton').click(function(){
	var zan = $(this).data('zan');
 var id = $(this).data('id');
	if (zan == 1) {
      var num = $(this).children('span.icobutton__text')[0];
      var val = Number(num.innerHTML)
      num.innerHTML = val + 1;
      $(this).addClass('zaned');
      $(this).data('zan',2);
  $.ajax({
   dataType:"json",
   type:"post",
   url:'../qwtyyhb/yyhb',
   data:{zan: zan,rid: id},
    success:function(result){
      console.log('请求成功')
    },
    error:function(e){
      console.log('请求失败')
    }
  })
	// }else{
	// 	$(this).removeClass('zaned')
	// 	$(this).data('zan',1);
	// 	var num = $(this).children('span.icobutton__text')[0];
	// 	var val = Number(num.innerHTML)
	// 	num.innerHTML = val - 1;
	}
})
$('.bytime').click(function(){
	$('.bytime').addClass('selected');
	$('.byhot').removeClass('selected');
})
$('.byhot').click(function(){
	$('.byhot').addClass("selected");
	$('.bytime').removeClass('selected');
})

var t;
var pointer = document.querySelector('.result');
var cancelTimeout = function() {
    if(t) {
        clearTimeout(t);
        t = null;
    }
};
<?php if ($result['notsub']==1):?>
$('.result').click(function(){
  $('.fademsg').show();
  $('.fadetitle').text('请先关注');
  $('.fadecontent').html("<img style='width:100%;' src='http://<?=$_SERVER['HTTP_HOST']?>/qwta/images/<?=$result['task']->bid?>/wx_qr_img'>识别图中的二维码关注我们就能参与活动");
  $('.fadebutton').hide();
  $('.fadebox').css('top','10%');
})
<?php else:?>
pointer.addEventListener('touchstart', function(e) {
    $('.result').addClass('hold');
    t = setTimeout(function() {
    $('.luyin').show();
    wx.startRecord();
        cancelTimeout();
    }, 500);
    e.preventDefault();
    return false;
});
pointer.addEventListener('touchend', function(e){
    $('.result').removeClass('hold');
    $('.luyin').hide();
    wx.stopRecord({
      success: function (res) {
        var localId = res.localId;
        wx.uploadVoice({
      localId: localId, // 需要上传的音频的本地ID，由stopRecord接口获得
      isShowProgressTips: 1, // 默认为1，显示进度提示
      success: function (res) {
        $('.fadeshibie').text('识别中，请稍候……')
        $('.fadeloading').show();
        var serverId = res.serverId; // 返回音频的服务器端ID
        $.ajax({
         url: '/qwtyyhb/yydown',
         type: 'POST',
         dataType: 'json',
         data: {serverId: serverId,tid:tid},
        })
        .done(function(res) {
         console.log("success");
         var interval=self.setInterval(
          function ajax(){
           $.ajax({
            url: '/qwtyyhb/ajax',
            type: 'POST',
            dataType: 'json',
            data: {request_id: res.request_id},
           })
           .done(function(res){
            if (res.status!='no') {
             console.log(res);
             clearInterval(interval);
             console.log('finish');
             if (res.error) {
                $('.fadeloading').hide();
                $('.fademsg').show();
                $('.fadetitle').text('很遗憾');
                $('.fadecontent').text(res.error);
                $('.fadebutton').text('我知道了');
                if (res.error=='很遗憾，没有中奖') {
                  $('.fadebutton').click(function(){
                    window.location.reload()
                  })
                }else{
                  $('.fadebutton').click(function(){
                    $('.fademsg').hide();
                  })
                }
             }else{
               $('.fadeloading').hide();
              $('.fademsg').show();
              $('.fadetitle').text('恭喜您');
              $('.fadecontent').text(res.content);
              $('.fadebutton').text('我知道了');
              $('.fadebutton').click(function(){
                    window.location.reload();
              })
              if (res.type==7||res.type==6) {
                $('.fadebutton').text('点击领取');
                $('.fadebutton').click(function(){
                  window.location.href = res.log;
                })
              };
               if (res.type==1) {
                $('.fadebutton').text('点击领取');
                $('.fadebutton').click(function(){
                  window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwtyyhb/shiwu/"+res.oid;
                })
               }
             }
            };
           })
          },3000)
        })
        .fail(function() {
         console.log("error");
        })
        .always(function() {
         console.log("complete");
        });
        // alert(serverId);
      }
    });
      }
    });

  cancelTimeout;
});
pointer.addEventListener('touchcancel', cancelTimeout);
<?php endif?>


</script>

</body>
</html>
