<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta http-equiv="cache-control" content="no-cache, must-revalidate">
    <title><?=$title?></title>
    <link rel="stylesheet" type="text/css" href="https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css">
    <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
    <style>
    /*body{
      text-align: center;
    }
    button{
      margin-top: 300px;
    }*/
    .weui_btn + .weui_btn {
    margin-top: 15px;
}
.weui_btn.weui_btn_mini {
    line-height: 1.9;
    font-size: 14px;
    padding: 0 .75em;
    display: inline-block;
}
.weui_btn_primary {
    background-color: #04BE02;
}
.weui_btn {
    position: relative;
    display: block;
    margin-left: auto;
    margin-right: auto;
    padding-left: 14px;
    padding-right: 14px;
    box-sizing: border-box;
    font-size: 18px;
    text-align: center;
    text-decoration: none;
    color: #FFFFFF;
    line-height: 2.33333333;
    border-radius: 5px;
    -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
    overflow: hidden;
}
    </style>
  <body>
    <div class="page_msg">
        <div class="inner">
          <span class="msg_icon_wrp">
            <i class="icon80_smile">
            </i>
          </span>
          <div class="msg_content">
          <?php if($over==1):?>
            <h4>您的二维码已过期</h4>
          <?else:?>
            <?php if($href==1):?>
              <!-- <a style="text-decoration: none;" href="<?=$subhref?>" class="weui_btn weui_btn_mini weui_btn_primary"><?=$bindcon?></a> -->
              <script type="text/javascript">
              window.location.href="<?=$subhref?>";
              </script>
            <?else:?>
              <span class="weui_btn weui_btn_mini weui_btn_primary"><?=$bindcon?></span>
          <?endif?>
         <?endif?>
          </div>
          <?php if($qr_img):?>
            <img style="width: 70%;height: auto;" src="<?=$qr_img?>">
            <div style="text-align: center;">长按二维码识别进入</div>
          <?php endif?>
        </div>
      </div>
  </body>
</html>
