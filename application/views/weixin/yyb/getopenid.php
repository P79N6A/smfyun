<!doctype html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>预约项目</title>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <style>
    body {
    line-height: 1.6;
    font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
}
    * {
    margin: 0;
    padding: 0;
}
   .container {
    height: 100%;
    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
}
.container > div {
    background-color: #fbf9fe;
}
.msg {
    background-color: #fff;
}
.weui_msg {
    padding-top: 36px;
    text-align: center;
}

.weui_icon_msg{
    font-size: 90px;
}
.weui_icon_success::before {
    font-size: 23px;
    color: #09BB07;
}
[class^="weui_icon_"]::before, [class*=" weui_icon_"]::before {
    margin: 0;
}
.weui_icon_success::before {
    content: "\EA06";
}
[class^="weui_icon_"]::before, [class*=" weui_icon_"]::before {
    font-family: "weui";
    font-style: normal;
    font-weight: normal;
    speak: none;
    display: inline-block;
    vertical-align: middle;
    text-decoration: inherit;
    width: 1em;
    margin-right: .2em;
    text-align: center;
    opacity: .8;
    font-variant: normal;
    text-transform: none;
    line-height: 1em;
    margin-left: .2em;
    font-size: 120%;
    text-shadow: 1px 1px 1px rgba(127, 127, 127, 0.3);
}
.weui_msg .weui_text_area {
    margin-bottom: 25px;
    padding: 0 20px;
}
.weui_msg .weui_msg_title {
    margin-bottom: 5px;
    font-weight: 400;
    font-size: 20px;
}
.weui_msg .weui_msg_desc {
    font-size: 14px;
    color: #888;
}
.weui_btn_primary {
    background-color: #04BE02;
}
.weui_btn_area {
    margin: 1.17647059em 15px 0.3em;
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
a {
    text-decoration: none;
}
.weui_btn::after {
    content: " ";
    width: 200%;
    height: 200%;
    position: absolute;
    top: 0;
    left: 0;
    border: 1px solid rgba(0, 0, 0, 0.2);
    -webkit-transform: scale(0.5);
    transform: scale(0.5);
    -webkit-transform-origin: 0 0;
    transform-origin: 0 0;
    box-sizing: border-box;
    border-radius: 10px;
}

@font-face {
  font-weight: normal;
  font-style: normal;
  font-family: "weui";
  src: url('data:application/octet-stream;base64,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') format('truetype');
}
    </style>
</head>
<body>
<script>
  wx.config({
    debug: false,
    appId: '<?php echo $jsapi["appId"];?>',
    timestamp: '<?php echo $jsapi["timestamp"];?>',
    nonceStr: '<?php echo $jsapi["nonceStr"];?>',
    signature: '<?php echo $jsapi["signature"];?>',
    jsApiList: [
      'closeWindow'
    ]
  });
</script>
<div class="container" id="container"><div class="msg">
<div class="weui_msg">
    <div class="weui_icon_area"><i class="weui_icon_success weui_icon_msg"></i></div>
    <div class="weui_text_area">
        <h2 class="weui_msg_title"><?=$result['text']?></h2>
        <p class="weui_msg_desc"><?=$result['title']?></p>
        <p class="weui_msg_desc"><?=$result['item']?></p>
        <p class="weui_msg_desc"><?=$result['content']?></p>
    </div>
    <?php if(!$result['subscribe']==1):?>
    <div class="weui_image_area" style="text-align:center;">
        <img src="http://<?=$_SERVER['HTTP_HOST']?>/yyb/images/cfg/<?=$result['2dimage']?>.v<?=time()?>.jpg" style="height:200px;">
        <p style="font-size:14px;color:#666;">长按扫描二维码，关注我们的微信公众号</p>
    </div>
    <?php endif?>
    <div class="weui_opr_area">
        <p class="weui_btn_area">
            <a onclick="WeixinJSBridge.call('closeWindow');" class="weui_btn weui_btn_primary">我知道了</a>

        </p>
    </div>
</div>
</div>
</div>
</body>
</html>
