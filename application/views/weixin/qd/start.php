<?php
$news = array("Title" =>"赶快来看看你的甜蜜指数！15天，你能坚持么？", "Description"=>"",
 "PicUrl" =>'http://games.smfyun.com/qd/bg/share.png', "Url" =>'http://games.smfyun.com/qd/storefuop',"fTitle"=>"我刚刚完成了一关甜蜜任务，赶快加入吧！");
 ?>
<!DOCTYPE html>
<html>
<head>
 <meta charset='utf-8'>
 <meta name ="viewport" content ="initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
 <title>15天甜蜜任务大考验</title>
 <link rel="stylesheet" href="../weiui/weui.css"/>
 <link rel="stylesheet" href="../weiui/example.css"/>
</head>
<style type="text/css">
 .left,.close{
  cursor:pointer;
 }
 body{
  background-color:#78bcb7;
  height: 100%;
  margin: 0px;
 }
 .bg{
  z-index: 1;
  -webkit-user-select: none;
  pointer-events: none;
  -webkit-touch-callout: none;
 }
 img {
  /*-webkit-user-select: none;*/
  /*pointer-events: none;*/
  /*-webkit-touch-callout: none;*/
 }
 .bg img{
  margin-left: 10%;
  width: 80%;
 }
 .text{
    height: 16px;
    width: 40%;
    margin-left: 20%;
    margin-top: 10px;
 }
 .in{
    margin-top: 10px;
    width: 60%;
    margin-left: 20%;
    height: 32px;
    border: 1px solid #FFB400;
 }
 .sub{
  margin-top: 15px;
  width:61%;
  margin-left: 20%;
  background-size: 100%;
  background-image: url('../bg/btn/btn<?=$day?>.png');
  height: 36px;
  border: 0px;
  border-radius: 5px;
 }
 .left{
  margin-top: 15px;
  margin-left: 20%;
  width: 25%;
  /*height: 25px;*/
  border: 0px;
  border-radius: 2px;
 }
 .right{
  margin-top: 15px;
  margin-left: 10%;
  width: 25%;
  /*height: 25px;*/
  border: 0px;
  border-radius: 2px;
 }
</style>
<body style="display:none;">
<div class="audio_div" style="background:url(../img/start.png) no-repeat center bottom;background-size:cover; z-index:2; position:absolute; height:50px; width:50px; margin-left:85%"></div>
<audio id="mp3Btn" autoplay loop>
 <source src="../dist/music.mp3" type="audio/mpeg" />
</audio>
<form method="post" onsubmit="return check()">
 <div class="bg"><img src="../bg/bg<?=$day?>_1.png"></div>
<img class="text" src="../bg/text.png">
 <input class="in" value="" type="text" maxlength="4" name='name'/>
 <button type="submit" class="sub"></button>
</form>
 <img src="../bg/btn/btn_left.png" class="left">
 <a href="../prize"><img src="../bg/btn/btn_right.png" class="right"></a>
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.js"></script>
<script type="text/javascript">
  function check(){
    var flag = <?=$flag?>;
    switch(flag){
     case 0:
        return true;
     break;
     case 1:
        return true;
     break;
     case 2:
     //当天可以重复参与
   //   $('body').append('<div class=\"weui_dialog_alert\" id=\"dialog2\" >'+
   //     '<div class=\"weui_mask\"></div>'+
   //     '<div class=\"weui_dialog\">'+
   //         '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\"></strong></div>'+
   //         '<div class=\"weui_dialog_bd\">您已经完成了今天的甜蜜任务哦！</div>'+
   //         '<div class=\"weui_dialog_ft\">'+
   //             '<a class=\"weui_btn_dialog primary close\">确定</a>'+
   //         '</div>'+
   //     '</div>'+
   // '</div>')
     return true;
     break;
     case 3:
   //   $('body').append('<div class=\"weui_dialog_alert\" id=\"dialog2\" >'+
   //     '<div class=\"weui_mask\"></div>'+
   //     '<div class=\"weui_dialog\">'+
   //         '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\"></strong></div>'+
   //         '<div class=\"weui_dialog_bd\">对不起，您的甜蜜任务已经中断了哦！，不能继续完成甜蜜任务了哦</div>'+
   //         '<div class=\"weui_dialog_ft\">'+
   //             '<a href="../prize" class=\"weui_btn_dialog primary close\">确定</a>'+
   //         '</div>'+
   //     '</div>'+
   // '</div>')
   //   $(".sub").attr("disabled", true);
     return true;
     break;
     case 4:
     return true;
     break;
     case 5:
     return true;
     break;
     case 6:
     $('body').append('<div class=\"weui_dialog_alert\" id=\"dialog2\" >'+
       '<div class=\"weui_mask\"></div>'+
       '<div class=\"weui_dialog\">'+
           '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\"></strong></div>'+
           '<div class=\"weui_dialog_bd\">对不起，活动已经结束！</div>'+
           '<div class=\"weui_dialog_ft\">'+
               '<a href="../prize" class=\"weui_btn_dialog primary close\">确定</a>'+
           '</div>'+
       '</div>'+
   '</div>')
     $(".sub").attr("disabled", true);
     return false;
     break;
     case 7:
   //   $('body').append('<div class=\"weui_dialog_alert\" id=\"dialog2\" >'+
   //     '<div class=\"weui_mask\"></div>'+
   //     '<div class=\"weui_dialog\">'+
   //         '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\"></strong></div>'+
   //         '<div class=\"weui_dialog_bd\">对不起，您已经兑换了奖品了哦！</div>'+
   //         '<div class=\"weui_dialog_ft\">'+
   //             '<a class=\"weui_btn_dialog primary close\">确定</a>'+
   //         '</div>'+
   //     '</div>'+
   // '</div>')
     // $(".sub").attr("disabled", true);
     return true;
     break;
     default:
     }
  }
</script>
<script type="text/javascript">
 $(document).ready(function() {
  //播放器控制
    var audio = document.getElementById('mp3Btn');
    $('.audio_div').click(function(){
        //防止冒泡
        //event.stopPropagation();   这个代码爆炸 防止点击不是内部div
        if(audio.paused) //如果当前是暂停状态

        {
            $('.audio_div').css("background","url(../img/start.png) no-repeat center bottom");
            $('.audio_div').css("background-size","cover");
            audio.play();//播放
            return;
        }

        //当前是播放状态

        $('.audio_div').css("background","url(../img/stop.png) no-repeat center bottom");
        $('.audio_div').css("background-size","cover");
        audio.pause(); //暂停

    });
  $('body').fadeIn(500);

  var flag = <?=$flag?>;
  switch(flag){
   case 0:

   break;
   case 1:

   break;
   case 2:
   $('body').append('<div class=\"weui_dialog_alert\" id=\"dialog2\" >'+
     '<div class=\"weui_mask\"></div>'+
     '<div class=\"weui_dialog\">'+
         '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\"></strong></div>'+
         '<div class=\"weui_dialog_bd\">您已经完成了今天的甜蜜任务哦</div>'+
         '<div class=\"weui_dialog_ft\">'+
             '<a class=\"weui_btn_dialog primary close\">确定</a>'+
         '</div>'+
     '</div>'+
 '</div>')
   // $(".sub").attr("disabled", true);
   break;
   case 3:
 //   $('body').append('<div class=\"weui_dialog_alert\" id=\"dialog2\" >'+
 //     '<div class=\"weui_mask\"></div>'+
 //     '<div class=\"weui_dialog\">'+
 //         '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\"></strong></div>'+
 //         '<div class=\"weui_dialog_bd\">对不起，您的甜蜜任务已经中断了哦！，不能继续完成甜蜜任务了哦</div>'+
 //         '<div class=\"weui_dialog_ft\">'+
 //             '<a href="../prize" class=\"weui_btn_dialog primary close\">确定</a>'+
 //         '</div>'+
 //     '</div>'+
 // '</div>')
 //   $(".sub").attr("disabled", true);
   break;
   case 4:

   break;
   case 5:

   break;
   case 6:
   $('body').append('<div class=\"weui_dialog_alert\" id=\"dialog2\" >'+
     '<div class=\"weui_mask\"></div>'+
     '<div class=\"weui_dialog\">'+
         '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\"></strong></div>'+
         '<div class=\"weui_dialog_bd\">对不起，活动已经结束！</div>'+
         '<div class=\"weui_dialog_ft\">'+
             '<a href="../prize" class=\"weui_btn_dialog primary close\">确定</a>'+
         '</div>'+
     '</div>'+
 '</div>')
   $(".sub").attr("disabled", true);
   break;
   case 7:
 //   $('body').append('<div class=\"weui_dialog_alert\" id=\"dialog2\" >'+
 //     '<div class=\"weui_mask\"></div>'+
 //     '<div class=\"weui_dialog\">'+
 //         '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\"></strong></div>'+
 //         '<div class=\"weui_dialog_bd\">对不起，您已经兑换了奖品了哦！</div>'+
 //         '<div class=\"weui_dialog_ft\">'+
 //             '<a class=\"weui_btn_dialog primary close\">确定</a>'+
 //         '</div>'+
 //     '</div>'+
 // '</div>')
   // $(".sub").attr("disabled", true);
   break;
   default:
   }
 });
 $(document).on('click', '.left', function() {
  $('body').append('<div class=\"weui_dialog_alert\" id=\"dialog2\" >'+
     '<div class=\"weui_mask\"></div>'+
     '<div class=\"weui_dialog\">'+
         '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\">玩法了解</strong></div>'+
         '<div class=\"weui_dialog_bd\" style=\'text-align:left;\'>'+
         '* &nbsp 参与互动，向你心仪的TA告白，领取神秘嘉宾给你的甜蜜任务！<br>'+
         '* &nbsp 每天1个甜蜜任务<br>'+
         '* &nbsp 连续完成5个任务奖励50积分；<br>'+
         '* &nbsp 连续完成10个任务奖励100积分；<br>'+
         '* &nbsp 连续完成15个任务奖励150积分<br>'+
         '* &nbsp 在任务奖励页面，可以用积分兑换喜临门蜜月定制抱枕、喜临门蜜月定制套杯和奥地利蜜月游抽奖资格（活动结束后，从获得抽奖资格的用户中随机抽取1位送出）；<br>'+
         '* &nbsp 连续完成任务中断后，再次参与则重新从第1关任务开始，已奖励的积分继续累加<br>'+
         '* &nbsp 同个微信单个奖品只能领取1个，不能重复和多次领取，奖品数量有限，先到先得，换完为止。<br>'+
         '* &nbsp 本活动最终解释权归喜临门所有。'+
         '</div>'+
         '<div class=\"weui_dialog_ft\">'+
             '<a class=\"weui_btn_dialog primary close\">确定</a>'+
         '</div>'+
     '</div>'+
 '</div>')
 });
 $(document).on('click', '.close', function() {
  $('.weui_dialog_alert').remove()
 });
</script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
  wx.config({
    debug: 0,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中
      'checkJsApi',
      'onMenuShareTimeline',
      'onMenuShareAppMessage'
      ]
  });
      wx.ready(function () {
    //自动执行的
    wx.checkJsApi({
      jsApiList: [
        'getLocation',
        'onMenuShareTimeline',
        'onMenuShareAppMessage'
      ],
      success: function (res) {

      }
    });
    wx.onMenuShareAppMessage({//好友
      title: '<?php echo $news['fTitle'];?>',
      desc: '<?php echo $news['Description'];?>',
      link: '<?php echo $news['Url'];?>',
      imgUrl: '<?php echo $news['PicUrl'];?>',
      trigger: function (res) {
      // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
      // alert('用户点击发送给朋友');
      },
      success: function (res) {
      // alert('已分享');
      },
      cancel: function (res) {
      // alert('已取消');
      },
      fail: function (res) {
      // alert(JSON.stringify(res));
      }
    });

    wx.onMenuShareTimeline({//朋友圈
      title: '<?php echo $news['Title'];?>',
      // desc: '<?php echo $news['Description'];?>',
      link: '<?php echo $news['Url'];?>',
      imgUrl: '<?php echo $news['PicUrl'];?>',
      trigger: function (res) {
      // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
      // alert('用户点击分享到朋友圈');
      },
      success: function (res) {
      // alert('已分享');
      },
      cancel: function (res) {
      // alert('已取消');
      },
      fail: function (res) {
      // alert(JSON.stringify(res));
      }
    });
  });

  wx.error(function (res) {
    alert(res.errMsg);
  });
</script>
</body>
</html>
