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
    <meta name="keywords" content="商城宝">
    <meta name="description" content="商城宝是由武汉神马浮云开发的微信平台活动工具，是微信运营人员必备的吸粉神器。">
    <title><?=$title?></title>

    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/scb/front/css/ui.css?v<?=date('Ymd')?>">
    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/scb/front/css/style.css?v<?=date('Ymd')?>">
  <!-- Gem style -->
  </head>

  <script src="http://cdn.jfb.smfyun.com/scb/plugins/jQuery/jQuery-2.1.4.min.js"></script>

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
                <span class="verify_name">关注时间：<?=date('Y-m-d H:i', $user['jointime'])?></span>
            </div>

          <div class="data_overview">
            <ul class="overview_list">
              <li class="overview_item">
                <p class="desc r_line">
                  粉丝数
                </p>
                <p class="number">
                  <?=$user['follows']?>
                </p>
              </li>
              <li class="overview_item">
                <p class="desc">
                  现有<?=$scorename?>
                </p>
                <p class="number">
                  <?=$user['score']?>
                </p>
              </li>
            </ul>
          </div>
        </div>
      </div>

        <!-- MenuLiks -->
      <div class="profile_fun">

            <ul id="js_menu" class="menu_list">
                <? if(Session::instance()->get('scb')["bid"]==582):?>
                <li class="menu_item fans_record" style="width:50%">
                    <a href="/scb/score" class="inner_item">
                        <span class="icon_menu"></span>
                        <p class="text_menu">我的<?=$scorename?></p>
                     </a>
                </li>
                <!-- <li class="menu_item swap_record" style="height:92px;">
                  <a  class="inner_item">
                        <span ></span>
                        <p class="text_menu">&nbsp;</p>
                     </a>
                </li> -->
                <li class="menu_item rank_record" style="width:50%">
                    <a href="/scb/top" class="inner_item">
                        <span class="icon_menu"></span>
                        <p class="text_menu"><?=$scorename?>排行</p>
                     </a>
                </li>
              <? else:?>
                <li class="menu_item fans_record" >
                    <a href="/scb/score" class="inner_item">
                        <span class="icon_menu"></span>
                        <p class="text_menu">我的<?=$scorename?></p>
                     </a>
                </li>
                <li class="menu_item swap_record">
                    <a href="/scb/items" class="inner_item">
                        <span class="icon_menu"></span>
                        <p class="text_menu"><?=$scorename?>商城</p>
                     </a>
                </li>
                <li class="menu_item rank_record" >
                    <a href="/scb/top" class="inner_item">
                        <span class="icon_menu"></span>
                        <p class="text_menu"><?=$scorename?>排行</p>
                     </a>
                </li>
              <? endif;?>

            </ul>
      </div>
      <?=$content?>
      <div id="copyright"><?=$config['copyright']?></div>



    </div>

    <div class="clearfix"></div>

  </body>
</html>
