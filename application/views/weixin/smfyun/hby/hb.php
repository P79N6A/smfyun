<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
  <title>红包雨</title>
  <style type="text/css">
  body{
    margin: 0;
    padding: 0;
    background-color: #efefef;
  }
  .background{
    margin-top: 12px;
    padding: 20px 16px;
    background-color: #fff;
    border-radius: 2px;
    border: 1px solid #d2d2d2;
  }
  .bg{
    width: 100%;
  }
  .shadow,.shadowerror{
    position: fixed;
    background-color: rgba(0,0,0,.5);
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }
  .shadowshare{
    position: fixed;
    background-color: rgba(0,0,0,.7);
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }
  .share{
    width: 100%;
  }
  .hongbao{
    width: 100%;
  }
  .redmoney{
    width: 100%;
    position: relative;
  }
  .sharebox{
    width: 100%;
    position: relative;
  }
  .errorbox{
    width: 100%;
    position: relative;
  }
  .close{
    color: #fff;
    position: absolute;
    right: 13%;
    top: 17%;
    font-weight: bold;
    font-size: 20px;
  }
  .text1b{
    position: absolute;
    width: 100%;
    text-align: center;
    top: 36%;
    color: #f0ce93;
    font-size: 16px;
    font-weight: bold;
  }
  .text2b{
    position: absolute;
    width: 100%;
    text-align: center;
    top: 42%;
    color: #f0ce93;
    font-size: 12px;
  }
  .text3b{
    position: absolute;
    width: 100%;
    text-align: center;
    top: 50%;
    color: #f0ce93;
    font-size: 24px;
  }
  .open{
    position: absolute;
    left: 37%;
    width: 26%;
    height: 20%;
    top: 61%;
  }
  .iknow{
    position: absolute;
    left: 21%;
    width: 57%;
    height: 9%;
    top: 65%;
  }
  .errortext{
    position: absolute;
    width: 60%;
    padding: 0 20%;
    text-align: center;
    top: 46%;
    color: #fff;
  }
  .qrcode{
    position: absolute;
    width: 20%;
    left: 40%;
    top: 50%;
  }
  .error{
    width: 100%;
  }
  .head{
    background-color: #fff;
    width: 100%;
    position: relative;
  }
  .headbg{
    width: 100%;
  }
  .ava{
    width: 20%;
    position: absolute;
    left: calc(40% - 2px);
    bottom: -20px;
    border-radius: 5px;
    border:2px solid #eaa200;
  }
  .title{
  color: #fff;
  position: absolute;
  top: 0;
  line-height: 30px;
  width: 100%;
  text-align: center;
 }
 .body{
  background-color: #fff;
 }
 .text1{
  width: 100%;
  text-align: center;
  line-height: 30px;
  padding-top: 20px;
  color: #666
 }
 .text2{
  width: 100%;
  text-align: center;
  line-height: 20px;
  font-size: 12px;
  color: #999;
  margin-top: 10px;
 }
 .text3{
  width: 100%;
  text-align: center;
  line-height: 40px;
  font-weight: bold;
  color: red;
  font-size: 20px;
 }
 .text4{
  width: 100%;
  text-align: center;
  line-height: 40px;
  border-top: 1px solid #efefef;
  font-size: 14px;
  color: #666;
 }
 .text5{
  background-color: #efefef;
  padding-left: 8px;
  text-align: left;
  line-height: 40px;
  font-size: 15px;
  color: #999;
 }
 .leg{
  width: 100%;
  background-color: #fff;
 }
 .user{
  width: 100%;
  margin-left: 8px;
  border-bottom: 1px solid #efefef;
  padding-top: 8px;
  padding-bottom: 8px;
 }
 .avas{
  height: 40px;
  border-radius: 20px;
  border: 1px solid #efefef;
  float: left;
  margin-right: 10px;
 }
 .username{
  font-size: 12px;
  line-height: 20px;
 }
 .time{
  font-size: 12px;
  color: #666;
  line-height: 20px;
 }
 .amount{
  font-size: 12px;
  color: #666;
  float: right;
  margin-right: 20px;
  line-height: 40px;
 }
 .lingquhongbao{
    width: 60%;
    margin-left: 20%;
    margin-top: 20px;
  }
  .ava1{
    position: absolute;
    width: 12%;
    left: 44%;
    top: 24%;
    border-radius: 5px;
    border: 1px solid #f0ce93;
  }
  </style>
</head>
<body>
<div class="lingqu">
  <div class="background">
  <?php if ($result['bgpic']):?>
    <img class="bg" src="http://<?=$_SERVER['HTTP_HOST']?>/qwthby/images/rcfg/<?=$result['bgpic']?>?time=<?=time()?>">
  <?php else:?>
    <img class="bg" src="../qwt/hby/hongbaolaile.png">
  <?php endif?>
  </div>
  <?php if (!$result['error']):?>
  <img class="lingquhongbao" src="../qwt/ywm/lingquhongbao.png">
  <div class="shadow" style="display:none;">
    <div class="redmoney">
      <img class="ava1" src="http://<?=$_SERVER['HTTP_HOST']?>/qwthby/images/rcfg/<?=$result['logo']?>?time=<?=time()?>.png">
      <div class="text1b"><?=$config['logoname']?></div>
      <div class="text2b">给你发了一个红包</div>
      <div class="text3b">恭喜发财 大吉大利</div>
      <img class="hongbao" src="../qwt/hby/hongbao.png">
      <div class="close">x</div>
      <div class="open"></div>
    </div>
  </div>
<?php endif?>
  <div class="shadowshare" style="display:none;">
    <div class="sharebox">
      <img class="share" src="../qwt/hby/fenxiang.png">
    </div>
  </div>
  <div class="shadowerror" <?=$result['error']?'':'style="display:none;"'?>>
    <div class="errorbox">
      <img class="error" src="../qwt/hby/error.png">
      <img class="qrcode" <?=$result['wx_qr_img']?'':'style="display:none;"'?> src="http://<?=$_SERVER['HTTP_HOST']?>/qwta/images/<?=$result['wx_qr_img']?>/wx_qr_img?time=<?=time()?>">
      <div class="errortext"><?=$result['error']?></div>
      <div class="iknow"></div>
    </div>
  </div>
</div>
<div class="chenggong" style="display:none">
  <div class="head">
    <img class="headbg" src="../qwt/hby/head.png">
    <img class="ava" src="http://<?=$_SERVER['HTTP_HOST']?>/qwthby/images/rcfg/<?=$result['logo']?>?time=<?=time()?>.png">
    <div class="title">已抢红包</div>
  </div>
  <div class="body">
    <div class="text1"><?=$config['logoname']?>送的现金红包</div>
    <div class="text2">红包来了</div>
    <div class="text3"></div>
    <?php if ($config['shopurl']):?>
    <div class="text4">去<?=$config['logoname']?>抢优惠>></div>
  <?php endif?>
    <div class="text5">已抢<?=$result['used']+1?>个
    <!-- ，还剩<?=($result['all']-$result['used'])?>个 -->
    </div>
  </div>
  <div class="leg">
  <?php foreach ($result['users'] as $k => $v):?>
    <div class="user">
      <img class="avas" src="<?=$v->headimgurl?>">
      <div class="amount"><?=number_format($v->money/100,2)?>元</div>
      <div class="username"><?=$v->nickname?></div>
      <div class="time"><?=date('Y-m-d H:i:s',$v->sendtime)?></div>
    </div>
  <?php endforeach?>
  </div>
</div>

<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.js"></script>
<script type="text/javascript">
$('.lingquhongbao').click(function(){
  $('.shadow').show();
})
$('.close').click(function(){
  $('.shadow').hide();
})
$('.open').click(function(){
  $('.shadow').hide();
  $('.shadowshare').show();
})
$('.iknow').click(function(){
  $('.shadowerror').hide();
  <?php if($result['error']=='该二维码已经被扫了'):?>
  location.replace("<?=$config['shareurl']?>");
  <?php endif?>
})
$('.text4').click(function(){
  location.replace('<?=$config['shopurl']?>');
})
wx.config({
    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
    appId: "<?=$jsapi['appId']?>", // 必填，公众号的唯一标识
    timestamp: <?=$jsapi['timestamp']?>, // 必填，生成签名的时间戳
    nonceStr: "<?=$jsapi['nonceStr']?>", // 必填，生成签名的随机串
    signature: "<?=$jsapi['signature']?>", // 必填，签名，见附录1
    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage','hideMenuItems'] //
});
wx.ready(function(){
    // config信息验证后会执行ready方法，所有接口调用都必须在config接口获得结果之后，config是一个客户端的异步操作，所以如果需要在页面加载时就调用相关接口，则须把相关接口放在ready函数中调用来确保正确执行。对于用户触发时才调用的接口，则可以直接调用，不需要放在ready函数中。
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
        "menuItem:share:QZone",
        "menuItem:refresh",
        "menuItem:share:appMessage",
      <?php if($result['error']=='该二维码已经被扫了'||$result['error']=='红包不存在！'||$result['error']=='已达到最大的扫码次数'):?>
        "menuItem:share:timeline",
      <?php endif?>
        "menuItem:exposeArticle"
        ],
        success: function (res) {
        },
        fail: function (res) {
        }
    });
    wx.onMenuShareTimeline({//朋友圈分享
        title: "<?=$config['sharetitle']?>", // 分享标题
        link: "http://<?=$_SERVER['HTTP_HOST']?>/qwthby/shareurl?bid=<?=$bid?>&openid=<?=$user->openid?>&hb_code=<?=$hb_code?>", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
        imgUrl: "http://<?=$_SERVER['HTTP_HOST']?>/qwthby/images/rcfg/<?=$result['sharelogo']?>?time=<?=time()?>", // 分享图标
        success: function () {
        // 用户确认分享后执行的回调函数
        // alert('分享成功');
      $.ajax({
        url: '/qwthby/qr_hb',
        type: 'post',
        dataType: 'json',
        data: {'hasshare': 1,'hb_code':"<?=$hb_code?>"},
      })
      .done(function(res) {
        console.log(res);
        if (res.error) {
          $('.errortext').text(res.error);
          $('.shadow').hide();
          $('.shadowshare').hide();
          $('.shadowerror').show();
          if (res.wx_qr_img) {
            $('.qrcode').attr('src','http://<?=$_SERVER['HTTP_HOST']?>/qwta/images/'+res.wx_qr_img+'/wx_qr_img?time=<?=time()?>');
            $('.qrcode').show();
          };
        }else{
          if(res.couponid){
            self.location.href = 'http://<?=$_SERVER['HTTP_HOST']?>/qwthby/ticket/'+res.couponid;
          }
          if(res.money){
            $('.leg').prepend('<div class="user">'+
                  '<img class="avas" src="'+res.headimgurl+'">'+
                  '<div class="amount">'+res.money+'元</div>'+
                  '<div class="username">'+res.nickname+'</div>'+
                  '<div class="time">'+res.time+'</div>'+
            '</div>');
            $('.lingqu').hide();
            $('.chenggong').show();
          }
        }
        var money = Number(res.money)/100;
        $('.text3').text(res.money+'元');
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });
        },
        cancel: function () {
         alert('用户取消');
        // 用户取消分享后执行的回调函数
        }
    });
    wx.onMenuShareAppMessage({//分享给朋友
      title: "<?=$config['sharetitle']?>", // 分享标题
      desc: "<?=$config['sharedesc']?>", // 分享描述
      link: "http://<?=$_SERVER['HTTP_HOST']?>/qwthby/shareurl?bid=<?=$bid?>&openid=<?=$user->openid?>&hb_code=<?=$hb_code?>", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
      imgUrl: "http://<?=$_SERVER['HTTP_HOST']?>/qwthby/images/rcfg/<?=$result['sharelogo']?>?time=<?=time()?>", // 分享图标
      success: function () {
      // 用户确认分享后执行的回调函数
      // alert('分享成功');
      $.ajax({
        url: '/qwthby/qr_hb',
        type: 'post',
        dataType: 'json',
        data: {'hasshare': 1,'hb_code':"<?=$hb_code?>"},
      })
      .done(function(res) {
        console.log(res);
        if (res.error) {
          $('.errortext').text(res.error);
          $('.shadow').hide();
          $('.shadowshare').hide();
          $('.shadowerror').show();
          if (res.wx_qr_img) {
            $('.qrcode').attr('src','http://<?=$_SERVER['HTTP_HOST']?>/qwta/images/'+res.wx_qr_img+'/wx_qr_img?time=<?=time()?>');
            $('.qrcode').show();
          };
        }else{
          $('.lingqu').hide();
          $('.chenggong').show();
        }
        var money = Number(res.money)/100;
        $('.text3').text(res.money+'元');
      })
      .fail(function() {
        console.log("error");
      })
      .always(function() {
        console.log("complete");
      });

      },
      cancel: function () {
       alert('用户取消');
      // 用户取消分享后执行的回调函数
      }
      });
});
</script>
</body>
</html>


