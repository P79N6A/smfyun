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
    <meta name="keywords" content="打卡宝">
    <meta name="description" content="打卡宝是由武汉神马浮云开发的微信平台活动工具，是微信运营人员必备的吸粉神器。">
    <title><?=$title?></title>

    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/dka/front/css/ui.css?v<?=date('Ymd')?>">
    <link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/dka/front/css/style.css?v<?=date('Ymd')?>">
  <!-- Gem style -->
  </head>

  <script src="http://cdn.jfb.smfyun.com/dka/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <style>
    /*返回打卡界面按钮*/
     .btnBack{
        width:34px;
        height:34px;
        /*border-radius:4px; */
        overflow:hidden;
        position:absolute;
        top:17px;
        left:22px;
        /*background-color: rgba(170,170,170,.7);*/
        border:none;
        /*font-size:14px;*/
        padding:0px;
        /*color:#fff;*/
        outline:none;
        z-index: 100;
      }
      /*返回打卡界面按钮*/

      /*点击抽奖页面*/
      .btnCj{
        width:50px;
        height:50px;
        border-radius:50px;
        position:absolute;
        top:17px;
        right:22px;
        overflow:hidden;
        border:none;
        padding:0px;
        outline:none;
        z-index: 100;
      }
      /*点击抽奖页面 结束*/
    </style>
  <body>

    <div class="wrp">
      <!-- UserInfo -->
      <div id="js_main" class="profile_hd">
        <!-- 返回打卡界面按钮 -->
        <a href="/dka/dka"><img src="/dka/dist/img/dkhome.png" class="btnBack"></a>
        <!-- 跳转到抽奖按钮 -->
        <?php if($pstatus==1):?>
        <a href="/dka/draw"><img src="/dka/dist/img/cj.jpg" class="btnCj"></a>
        <?php endif?>
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
              <!-- <li class="overview_item">
                <p class="desc r_line">
                  粉丝数
                </p>
                <p class="number">
                  <?=$user['follows']?>
                </p>
              </li> -->
              <li class="overview_item">
                <p class="desc">
                  总<?=$scorename?>
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
                <li class="menu_item fans_record">
                    <a href="/dka/score" class="inner_item">
                        <span class="icon_menu"></span>
                        <p class="text_menu">我的<?=$scorename?></p>
                     </a>
                </li>

                 <li class="menu_item swap_record">
                    <a href="/dka/items" class="inner_item">
                        <span class="icon_menu"></span>
                        <p class="text_menu"><?=$scorename?>商城</p>
                     </a>
                </li>

                 <li class="menu_item rank_record">
                    <a href="/dka/top" class="inner_item">
                        <span class="icon_menu"></span>
                        <p class="text_menu"><?=$scorename?>排行</p>
                     </a>
                </li>

            </ul>
      </div>

      <?=$content?>

      <div id="copyright"><?=$config['copyright']?></div>

    </div>

    <div class="clearfix"></div>

  </body>
</html>
