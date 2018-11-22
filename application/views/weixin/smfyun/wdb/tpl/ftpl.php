<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <!-- Mobile Devices Support @begin -->
    <meta content="application/xhtml+xml; charset=utf-8" http-equiv="Content-Type">
    <meta content="no-cache,must-revalidate" http-equiv="Cache-Control">
    <meta content="no-cache" http-equiv="pragma">
    <meta content="0" http-equiv="expires">
    <meta content="telephone=no, address=no" name="format-detection">
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <!-- apple devices fullscreen -->
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- Mobile Devices Support @end -->
    <meta name="keywords" content="多客来">
    <meta name="description" content="多客来是由武汉神马浮云开发的微信平台活动工具，是微信运营人员必备的吸粉神器。">
    <title><?=$title?></title>

    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/dkl/front/css/ui.css?v<?=date('Ymd')?>">
    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/dkl/front/css/style.css?v<?=date('Ymd')?>">
  <!-- Gem style -->
  <style>
/*.text_menu
{
  font-size: 15px;
  color: #607fa6;
  opacity: 0.6;
}*/
.menu_item{
  width: 25% !important;
}
.circle{
  display: inline-block;
  padding-top: 20px;
  background-color: #ff9900;
}
.desc{
  color: #fff !important;
}
.number{
  color: #fff !important;
}
.profile_logo{
  float: left;
}
.user{
  padding: 20px 0 20px 20px;
  height: 104px;
  background-color: #fff;
  /*border-top: 1px solid #dedede;
border-bottom: 1px solid #dedede;*/
}
.usertext{
  display: inline-block;
  width: 60%
}
.profile_name{
  text-align: left;
  font-size: 24px;
  font-weight: bold;
}
.profile_extra{
  margin-top: 20px;
}
.data_overview{
  /*margin-top: 10px;*/
  background-color: #fff;
  /*border-top: 1px solid #dedede;
  border-bottom: 1px solid #dedede;*/
}
.profile_fun{
  background-color: #fff;
  /*margin-top: 10px;
  margin-bottom: 10px;*/
}
.icon_menu{
  width: 30px !important;
}
.icon_menu img{
  width: 30px;
  height: 30px;
}
.text_menu{
  color: #666666;
  padding-top: 5px;
}
.menu_list:before{
    height: 0;
    border-top: 1px solid #dedede;
}
.menu_item:after{
    width: 0 !important;
}
.desc:after{
    border-right: 0 !important;
}
.header {
    height: 2.4rem;
    padding: 0;
    position: relative;
    text-align: center;
    color: #666;
    /*border-bottom: 1px solid #e5e5e5;
    border-top: 1px solid #e5e5e5;
    margin-bottom: 10px;*/
    background-color: #fff;
}
.header:before {
    content: '';
    border-bottom: 1px solid #e5e5e5;
    position: absolute;
    left: 0;
    top: 50%;
    width: 100%;
    height: 0;
    z-index: 0;
    -webkit-transform: translate3d(0,-2px,0);
    transform: translate3d(0,-2px,0);
}
.header .txt {
    position: relative;
    display: inline-block;
    padding: 0 .5rem;
    line-height: 2.4rem;
    background-color: #f0f0f0;
}
  </style>
  </head>

  <script src="http://cdn.jfb.smfyun.com/dkl/plugins/jQuery/jQuery-2.1.4.min.js"></script>

  <body style="background-color:#f3f3f3">

    <div class="wrp">

      <!-- UserInfo -->
      <div id="js_main" class="profile_hd" style="padding-top:0;">
        <div class="inner">
          <div class="user">
          <img class="profile_logo" src="<?=$user['headimgurl']?>" style="border: 2px solid rgba(210, 210, 210, 0.8);">
          <div class="usertext">
          <div class="profile_name">
            <?=$user['nickname']?>
          </div>

            <div class="profile_extra">
                <span class="verify_name">关注时间：<?=date('Y-m-d H:i', $user['subscribe_time'])?></span>
            </div>
            </div>
          </div>
          <div class="data_overview" style="padding:20px 0 20px;">
            <ul class="overview_list">
              <li class="overview_item">
                <div class="circle">
                <p class="desc r_line">
                  粉丝数
                </p>
                <p class="number">
                  <?=$user['follows']?>
                </p>
                </div>
              </li>
              <li class="overview_item">
                <div class="circle">
                <p class="desc r_line">
                  现有<?=$config['score']?>
                </p>
                <p class="number">
                  <?=$user['score']?>
                </p>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </div>

        <!-- MenuLiks -->
      <div class="profile_fun">

            <ul id="js_menu" class="menu_list">

                <li class="menu_item">
                    <a href="/qwtwdb/score" class="inner_item">
                        <span class="icon_menu"><img src="http://yingyong.smfyun.com/dkl/img/score.png"></span>
                        <p class="text_menu">我的<?=$config['score']?></p>
                     </a>
                </li>
                <li class="menu_item">
                    <a href="/qwtwdb/items" class="inner_item">
                        <span class="icon_menu"><img src="http://yingyong.smfyun.com/dkl/img/shop.png"></span>
                        <p class="text_menu"><?=$config['score']?>商城</p>
                     </a>
                </li>
                <li class="menu_item" >
                    <a href="/qwtwdb/orders" class="inner_item" style="border-right:1px solid #f6f6f6;">
                        <span class="icon_menu"><img src="http://yingyong.smfyun.com/dkl/img/list.png"></span>
                        <p class="text_menu">兑换明细</p>
                     </a>
                </li>
                <li class="menu_item" >
                    <a href="/qwtwdb/top" class="inner_item">
                        <span class="icon_menu"><img src="http://yingyong.smfyun.com/dkl/img/rank.png"></span>
                        <p class="text_menu"><?=$config['score']?>排行</p>
                     </a>
                </li>

            </ul>
      </div>

      <?=$content?>
      <div id="copyright"><?=$config['copyright']?></div>



    </div>

    <!-- <div class="clearfix"></div> -->

  </body>
    <script type="text/javascript">
    var a= $('.desc').height();
    var b= $('.number').height();
    $('.circle').css('width',a+b+40);
    $('.circle').css('height',a+b+20);
    $('.circle').css('border-radius',(a+b+40)/2);
        </script>
</html>

