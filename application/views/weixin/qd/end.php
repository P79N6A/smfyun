<?php
$news = array("Title" =>"赶快来看看你的甜蜜指数！15天，你能坚持么？", "Description"=>"",
 "PicUrl" =>'http://games.smfyun.com/qd/bg/share.png', "Url" =>'http://games.smfyun.com/qd/storefuop',"fTitle"=>"我刚刚完成了一关甜蜜任务，赶快加入吧！");
$time=time();
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
body{
 margin:0px;
 background: #E5E8E8;
}
.post{
 width: 100%;
width: 90%;
margin-left: 5%;
}
.close{
  cursor:pointer;
}
</style>
<body style="display:none;">
<!--<img class="post" src="<?=$newfile?>">-->

<img class="post" src="../himage/<?=$result['name']?>/<?=$result['time_name']?>?a=<?=$time?>">
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('body').fadeIn(500);
    $('body').append('<div class=\"weui_dialog_alert\" id=\"dialog2\" >'+
     '<div class=\"weui_mask\"></div>'+
     '<div class=\"weui_dialog\">'+
         '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\"></strong></div>'+
         '<div class=\"weui_dialog_bd\">连续生成海报完成任务可以获得对应奖励，海报长按可以保存。</div>'+
         '<div class=\"weui_dialog_ft\">'+
             '<a class=\"weui_btn_dialog primary close\">知道了</a>'+
         '</div>'+
     '</div>'+
 '</div>')
  });
  $(document).on('click', '.close', function() {
    $('.weui_dialog_alert').remove();
  })
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
    wx.onMenuShareAppMessage({
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

    wx.onMenuShareTimeline({
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
