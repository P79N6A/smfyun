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
    <meta name="keywords" content="任务宝">
    <meta name="description" content="任务宝是由武汉神马浮云开发的微信平台活动工具，是微信运营人员必备的吸粉神器。">
    <title><?=$title?></title>

    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/rwb/front/css/ui.css?v<?=date('Ymd')?>">
    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/rwb/front/css/style.css?v<?=date('Ymd')?>">
  <!-- Gem style -->
  <style>
.text_menu
{
  font-size: 15px;
  color: #607fa6;
  opacity: 0.6;
}
  </style>
  </head>

  <script src="http://cdn.jfb.smfyun.com/rwb/plugins/jQuery/jQuery-2.1.4.min.js"></script>

  <body>

    <div class="wrp">

      <!-- UserInfo -->
      <div id="js_main" class="profile_hd">
        <div class="inner">
          <img class="profile_logo" src="<?=$user['headimgurl']?>">
          <div class="profile_name">
            <?=$user['nickname']?>
          </div>

            <div class="profile_extra">
                <span class="verify_name">关注时间：<?=date('Y-m-d H:i', $user['subscribe_time'])?></span>
            </div>

          <div class="data_overview">
            <ul class="overview_list">
              <li class="overview_item">
                <p class="desc r_line">
                  总下线数量
                </p>
                <p class="number">
                  <?=$user['follows']?>
                </p>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <?=$content?>
      <div id="copyright"><?=$config['copyright']?></div>



    </div>

    <!-- <div class="clearfix"></div> -->

  </body>
</html>
