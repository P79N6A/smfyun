<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <title>领取卡券</title>
</head>

<body>
  <table><tr><td style="vertical-align:middle;height:300px;">加载中....</td></tr></table>

<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

<script>
  wx.config({
    debug: false,
    appId: '<?php echo $jsapi["appId"];?>',
    timestamp: '<?php echo $jsapi["timestamp"];?>',
    nonceStr: '<?php echo $jsapi["nonceStr"];?>',
    signature: '<?php echo $jsapi["signature"];?>',
    jsApiList: [
      'addCard',
      'chooseCard',
      'openCard',
      'onMenuShareTimeline'
    ]
  });

  wx.ready(function () {



    wx.addCard({
      cardList: [
        {
          cardId: '<?=$cardId?>',
          cardExt: '{"timestamp": "<?php echo $jsapi["timestamp"];?>", "signature":"<?php echo $sign;?>"}'
        },
      ],
      success: function (res) {
        //alert('已添加卡券：' + JSON.stringify(res.cardList));
      }
    });

  })


</script>

</body>
</html>
