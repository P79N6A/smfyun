<?php
if($commend->pic){
  $picurl= "http://".$_SERVER['HTTP_HOST']."/yty/images/setgood/".$commend->id.".v".time().".jpg";
}else{
  $picurl=$commend->picurl;
}
$news = array(
    "fTitle" =>"来自".$nickname."的推荐",
    "fDescription"=>$commend->title,
    //"fPicUrl" =>$picurl,//logo
    // "Url" =>'',
    "Title"=>"来自".$nickname."的推荐：".$commend->title,
    //"PicUrl" =>$picurl,//logo

    );
 ?>
<?php if($status==2):?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>分享商品</title>
    <link rel="stylesheet" type="text/css" href="/yty/css/loaders.min.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/loading.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/base.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/style.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/swiper.min.css"/>
    <script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
    <script type="text/javascript">
      $(window).load(function(){
        $(".loading").addClass("loader-chanage")
        $(".loading").fadeOut(300)
      })
    </script>
</head>
<body>
  <div class="contaniner fixed-contb">
    <section class="detail">
      <figure class="swiper-container">
        <ul class="swiper-wrapper">
          <li class="swiper-slide">
            <a href="#">
            <?php if($commend->pic):?>
              <img src="/yty/images/setgood/<?=$commend->id?>.v<?=time()?>.jpg"/>
              <?endif;?>
            <?php if(!$commend->pic):?>
              <img src="<?=$commend->picurl?>"/>
               <?endif;?>
            </a>
          </li>
        </ul>
        <div class="swiper-pagination">
        </div>
      </figure>
      <dl class="jiage">
        <dt>
          <h3><?=$commend->title?></h3>
        </dt>
        <dd>
          <b>￥<?=$commend->price?></b>
        </dd>
      </dl>
      <article class="detail-article">
        <nav>
          <ul class="article">
            <li id="talkbox1" class="article-active">商品详情</li>
          </ul>
        </nav>

        <section class="talkbox1">
          <?=$commend->information?>
        </section>
      </article>
    </section>
  </div>

</body>
</html>
<?php endif?>
<?php if($status==1):?>
<script type="text/javascript">
    window.open('<?=$commend->url?>','_self');
</script>
<?php endif?>
  <footer class="page-footer fixed-footer">
    <ul style="height: 0px">
      <li>
        <a href="/yty/home">
          <img src="/yty/images/footer002.png"/>
          <p style="font-size: 14px;line-height:14px;" >个人中心</p>
        </a>
      </li>

      <li class="active">
        <a href="<?='http://'.$_SERVER["HTTP_HOST"].'/yty/storefuop/'.$bid?>">
          <img src="/yty/images/footer04.png"/>
          <p style="font-size: 14px;line-height:14px;" >推荐商品</p>
        </a>
      </li>
    </ul>
  </footer>
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
      desc: '<?php echo $news['fDescription'];?>',
      // link: '<?php echo $news['Url'];?>',
      imgUrl: '<?php echo $picurl;?>',
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
      // link: '<?php echo $news['Url'];?>',
      imgUrl: '<?php echo $picurl;?>',
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
<link rel="stylesheet" type="text/css" href="/yty/css/loaders.min.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/loading.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/base.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/style.css"/>
