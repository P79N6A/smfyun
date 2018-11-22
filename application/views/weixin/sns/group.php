<!DOCTYPE html>
<html>
<head>
<meta charset='utf8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
 <title>美腿万团大作战</title>
</head>
<style type="text/css">
body{
 margin: 0px;
 padding: 0px;
 background: url(/sns/img/stbg.jpg) top no-repeat;
 background-position: 0px -60px;
 height: 100%;
 width: 100%;
 background-size: cover;
 font-family:'SimHei';
}
.logo{
  width: 12%;
  margin-left: 2.8%;
  display: inline-block;
}
.liucheng{
  width: 90%;
  margin-left: 5%;
  margin-top: 111%;
}
.yuyue{
    width: 40%;
    margin-left: 30%;
    margin-top: 8%;
}
.bobao{
  display: inline-block;
}
.name{
  margin-left: 5px;
}
.name,.gift{
  color:#EFE96E;
}
.text{
  color: white;
}
.laba{
  height: 10px;
}
.bobao{
    display: flex;
    position: fixed;
    top: 0px;
    left: 16%;
    font-size: 10px;
    background: rgba(0, 0, 0, 0.27);
    width: 61%;
    padding: 10px;
    text-align: center;
}
.rule{
      color: white;
    position: fixed;
    top: 15px;
    right: 5px;
    font-size: 13px;
    border-bottom: 1px solid white;
}
.join{
  display: inline-block;
  width: 90%;
  margin-left: 5%;
  height: 10%;
  background: -webkit-gradient(linear, 0 0, 100% 100%, from(#E95DFA), to(#5BCBEE));
  text-align: center;
  color: white;
  height: 50px;
  font-size: 20px;
  position: relative;
  top: 20px;
  line-height: 50px;
}
.rules{
  width: 100%;
  position: absolute;
  top: 0px;
  background: rgba(0, 0, 0, 0.85);
  display: none;
}
</style>
<body>
<img src="/sns/img/logo.png" class="logo">
<div class="bobao">
<div class='hide'>
    <img src="/sns/img/laba.png" class="laba">
    <span class='name'><?=$bobao[0]['name']?></span>
    <span class='text'>已获得</span>
    <span class='gift'><?=$bobao[0]['gift']?></span>
    </div>
</div>
<span class='rule'>活动规则</span>
<img src="/sns/img/liucheng.png" class="liucheng">
<span class='join'>我要当团长></span>
<img src="/sns/img/yuyue.png" class="yuyue">
<div class="rules">
  <img class='close' src="/sns/img/close.png" style="width:20px;position:absolute;top:10px;right:10px;">
  <img src="/sns/img/rule.png" style="width:100%;position:absolute;top:30px;">
</div>
</body>
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  var h1 = $(window).height();
  var h2 = $('body').height();
  var H = Math.max(h1,h2);
  $('.rules').height(H);
  window.bobao = new Array();
  for (var i = 0; i <=19 ; i++) {
    window.bobao[i] = new Array();
  };
  window.rank = 1;
  <?php foreach ($bobao as $k => $v):?>
    window.bobao[<?=$k?>]['name'] = "<?=$bobao[$k]['name']?>";
    window.bobao[<?=$k?>]['gift'] = "<?=$bobao[$k]['gift']?>";
  <?php endforeach?>
  console.log(window.bobao);
  setInterval(function(){
  console.log(rank+window.bobao[window.rank]['name']+window.bobao[window.rank]['gift']);
  $('.name').text(window.bobao[window.rank]['name']);
  $('.gift').text(window.bobao[window.rank]['gift']);
  window.rank++;
  if(window.rank==20){
    window.rank=0
  }
 },1500)
});
$('.rule').click(function() {
  $('.rules').fadeIn(500);
});
$('.close').click(function() {
  $('.rules').fadeOut(500);
});
$('.join').click(function() {
  localStorage.setItem('first','first');
  window.location.replace('/sns/join?first=1');
});
</script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

<script>
  wx.config({
    debug: 0,
    appId: '<?php echo $jsapi["appId"];?>',
    timestamp: <?php echo $jsapi["timestamp"];?>,
    nonceStr: '<?php echo $jsapi["nonceStr"];?>',
    signature: '<?php echo $jsapi["signature"];?>',
    jsApiList: [
      'checkJsApi',
      'onMenuShareTimeline',
      'onMenuShareAppMessage',
      'hideMenuItems'
      ]
  });
      wx.ready(function () {
    wx.checkJsApi({
      jsApiList: [
        'getLocation',
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
      'hideMenuItems'
      ],
      success: function (res) {
      }
    });

    wx.onMenuShareAppMessage({
      title: "<?php echo $config['groupt'];?>",
      desc: '<?php echo $config['groupd'];?>',
      link: '<?php echo $config['Url'];?>',
      imgUrl: '<?php echo $config['PicUrl'];?>',
      trigger: function (res) {
      },
      success: function (res) {
        $('.shares').fadeOut(500);
        $('body').css({
          'overflow-y': 'auto',
          'position':'inherit'
        });
      $.ajax({
        url: '/sns/share?ShareApp=1',
        type: 'get',
        dataType: 'text',
      })
      .done(function() {
        console.log("success");
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
      },
      cancel: function (res) {
      },
      fail: function (res) {
      }
    });

    wx.onMenuShareTimeline({
      title: "<?php echo $config['groupt'];?>",
      link: '<?php echo $config['Url'];?>',
      imgUrl: '<?php echo $config['PicUrl'];?>',
      trigger: function (res) {
      },
      success: function (res) {
        $('.shares').fadeOut(500);
        $('body').css({
          'overflow-y': 'auto',
          'position':'inherit'
        });
        $.ajax({
          url: '/sns/share?Timeline=1',
          type: 'get',
          dataType: 'text',
        })
        .done(function() {
          console.log("success");
        })
        .fail(function() {
          console.log("error");
        })
        .always(function() {
          console.log("complete");
        });
      },
      cancel: function (res) {
      },
      fail: function (res) {
      }
    });
    wx.hideMenuItems({
        menuList: [
        'menuItem:copyUrl',
        "menuItem:editTag",
        "menuItem:delete",
        "menuItem:copyUrl",
        "menuItem:originPage",
        "menuItem:readMode",
        "menuItem:openWithQQBrowser",
        "menuItem:openWithSafari",
        "menuItem:share:email",
        "menuItem:share:brand",
        "menuItem:share:qq",
        "menuItem:share:weiboApp",
        "menuItem:favorite",
        "menuItem:share:facebook",
        "menuItem:share:QZone"
        ],
        success: function (res) {
        },
        fail: function (res) {
        }
    });
  });

  wx.error(function (res) {
    alert(res.errMsg);
  });
</script>
</html>
