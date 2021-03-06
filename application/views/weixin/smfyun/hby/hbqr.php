<!DOCTYPE html>
<html style="font-size: 40px;">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="no-transform">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="layoutmode" content="standard">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="renderer" content="webkit">
    <meta name="wap-font-scale" content="no">
    <meta content="telephone=no" name="format-detection">
    <meta http-equiv="Pragma" content="no-cache">
    <script type="text/javascript">
        var _htmlFontSize = (function () {
            var clientWidth = document.documentElement ? document.documentElement.clientWidth : document.body.clientWidth;
            if (clientWidth > 640) clientWidth = 640;
            document.documentElement.style.fontSize = clientWidth * 1 / 16 + "px";
            return clientWidth * 1 / 16;
        })();
    </script>
    <script type="text/javascript" src="http://wechatfe.github.io/vconsole/lib/vconsole.min.js?v=2.5.2"></script>
    <style type="text/css">

* {
    -webkit-tap-highlight-color: rgba(255, 255, 255, 0);
    margin: 0;
    -webkit-touch-callout: none; /* prevent callout to copy image, etc when tap to hold */
    -webkit-text-size-adjust: none; /* prevent webkit from resizing text to fit */
    /* make transparent link selection, adjust last value opacity 0 to 1.0 */
    -webkit-user-select: none; /* prevent copy paste, to allow, change 'none' to 'text' */
    /* -webkit-tap-highlight-color: rgba(0,0,0,0); */
}

html {
    height: 100%
}

input,
textarea {
    -webkit-user-select: text
}

a {
    color: #000;
    padding: 0;
    text-decoration: none;
    cursor: pointer;
    font-family: "\5FAE\8F6F\96C5\9ED1", Helvetica, "黑体", Arial, Tahoma
}

video {
    width: 100%
}

input[type="radio"],
input[type="checkbox"] {
    vertical-align: -2px
}

.main_box {
    position: relative;
    margin: 0 auto;
    width: 100%;
    height: 100%;
    background: #CD261D;
    /*background: url("../prize/images/hbqrtop.png") no-repeat;*/
    /*background-size: 100%;*/
    font-family: "\5FAE\8F6F\96C5\9ED1", Helvetica, "黑体", Arial, Tahoma
}

.main_box .box {
    width: 100%;
    /*padding-top:45%;*/
    /*background: #CD261D;*/
    border-radius: 10px;
    -webkit-border-radius: 10px;
}

.main_box .box .bg_in {
    border-radius: 6px;
    -webkit-border-radius: 6px;
}

.main_box .box .bg_in .title {
    color: #fff;
    font-size: 0.9rem;
    text-align: center;
    padding: 0.25rem 0;
}

.main_box .box .content {
    z-index: 10;
    position: relative;
    width: 8rem;
    height: 8rem;
    margin: 0 auto 0 auto;
    /*background: #CD261D;*/
    border-radius: 6px;
    -webkit-border-radius: 6px;
}

#mask_img_bg {
    position: absolute;
    left: 0.2rem;
    top: 0.2rem;
    bottom: 0.2rem;
    right: 0.2rem;
    background: #fff;
    border-radius: 6px;
    -webkit-border-radius: 6px;
}

#mask_img_bg img {
    width: 7rem;
    margin: 0 auto;
    display: block;
}

#redux {
    z-index: 22;
    position: absolute;
    padding: 0.2rem;
    box-sizing: border-box;
    width: 100%;
    height: 100%;
    border-radius: 6px;
    -webkit-border-radius: 6px;
}

.main_box .hint-show {
    display: none;
    position: absolute;
    left: 50%;
    top: 6rem;
    width: 286px;
    height: 245px;
    margin-left: -143px;
    z-index: 99;
}

.main_box .hint-show .hint-img {
    width: 286px;
    height: 245px;
}

.main_box .hint-show .colour-img {
    width: 239px;
    height: 138px;
    position: absolute;
    top: -20px;
    left: 50%;
    margin-left: -120px;
    z-index: 110;
}

.main_box .hint-show .prize-img {
    width: 116.5px;
    height: 121.5px;
    position: absolute;
    top: 74px;
    left: 50%;
    margin-left: -60px;
    z-index: 100;
}
.cont-span{
    display: block;
    width: 120px;
    height: 38px;
    text-align: center;
    position: absolute;
    top: 40%;
    left: 50%;
    margin-left: -60px;
    z-index: 10;
}
.main_box .hint-show .prize-span {
    display: block;
    width: 120px;
    height: 38px;
    text-align: center;
    position: absolute;
    top: 50%;
    left: 50%;
    margin-left: -60px;
    z-index: 100;
}



.main_box .hint-show .close {
    display: inline-block;
    width: 32px;
    height: 32px;
    position: absolute;
    top: 0;
    right: 0;
    z-index: 200;
}

.main_box .hint-show .btn {
    display: inline-block;
    width: 180px;
    height: 37px;
    position: absolute;
    bottom: 10px;
    left: 48px;
    z-index: 200;
}

.main_box .mask {
    display: none;
    position: absolute;
    left: 0;
    top: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 98;
}

.hint-num {
    width: 12.6rem;
    height: 1.2rem;
    line-height:1.2rem;
    margin: 0 auto;
    background: rgba(0,0,0,0.1);
}
.hint-num h4{
    width: 100%;
    color: #fff;
    font-weight: 200;
    font-size: 0.6rem;

    text-align: center;
}
.hint-num h4 strong {
    color: #FFDB16;
}
.hbqrtop{
    width: 100%;
}
.hbqrbot{
    width: 100%;
    bottom: 0;
}
.top{
    position: relative;
    width: 100%;
    z-index: 1;
}
.clean{
    position: absolute;
    width: 24%;
    height: 29%;
    top: 46%;
    left: 38%;
}
.onemore{
    width: 22%;
    position: absolute;
    height: 14%;
    right: 0;
    top: 0;
}
.detail{
    width: 13%;
    position: absolute;
    height: 14%;
    left: 0;
    top: 0;
}
.qrid{
    font-size: 12px;
    color: #000;
    text-align: center;
}
.shadow{
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    background-color: rgba(0,0,0,.7);
    z-index: 100;
}
.error{
    width: 100%;
    position: relative;
}
.error img{
    width: 100%;
}
.errtext{
    position: absolute;
    width: 69%;
    left: 14%;
    text-align: center;
    top: 50%;
    word-break: break-all;
    font-size: 70%;
    color: #fff;
}
.logo{
    position: absolute;
    width: 24%;
    height: 29%;
    top: 46%;
    left: 38%;
}
.logo img{
    width: 100%;
    border-radius: 50%;
    border: 1px solid #e5e5e5;
}
.selector{
    position: absolute;
    top: 6%;
    left: 6%;
    border-radius: 5px;
    border: 1px solid #efefef;
    box-shadow: 1px 1px 10px 1px #666;
    background-color: #fff;
    font-size: 14px;
    z-index: 2;
}
.hbqr{
    padding: 10px 20px;
    color: #999;
}
.record{
    padding: 10px 20px;
    color: #000;
    /*text-decoration: underline;*/
}
</style>

</head>

<body class="main_box" style="background-color: #fff">
<div class="shadow" <?=$result['error']?'':'style="display:none;"'?>>
    <div class="error">
        <img src="../qwt/hby/chucuole.png">
        <div class="errtext"><?=$result['error']?></div>
    </div>
</div>
<div class="top">
    <img class="hbqrtop" src="../qwt/hby/hbqrtop.png">
    <div class="logo">
        <img src="/qwthbya/images/rcfg/<?=$result['logo']?>.v<?=time()?>.jpg">
    </div>
    <div class="clean"></div>
    <div class="onemore"></div>
    <div class="detail"></div>
    <div class="selector" style="display:none;">
        <div class="hbqr">发红包</div>
        <div class="record">领取记录</div>
    </div>
</div>
<div class="box">
    <div class="content">
        <div id="mask_img_bg"><span class="cont-span"></span>
    <img class="" src="<?=$result['qr_img']?>">
        </div>
        <img id="redux" src="../qwt/hby/layer.png"/>
    </div>
</div>
<div class="qrid" style="display:none"><?=$result['no']?></div>
    <img class="hbqrbot" src="../qwt/hby/hbqrbot.png">

<script type="text/javascript" src="../qwt/hby/js/jquery.min.js"></script>
<!--<script type="text/javascript" src="js/zepto.m.js"></script>-->
<script type="text/javascript" src="../qwt/hby/js/jquery.eraser.js"></script>
<script type="text/javascript">
    $(window).load(function () {
            $('#redux').eraser({
                size: 50,   //设置橡皮擦大小
                completeRatio: .7, //设置擦除面积比例
                completeFunction: showResetButton   //大于擦除面积比例触发函数
            });
    })
    function showResetButton() {
        $('.qrid').show();
        $('#redux').hide();
    }
    $('.clean').click(function(){
        $('#redux').hide();
        $('.qrid').show();
    })
    $('.detail').click(function(){
        // window.location.replace('../qwthby/kl_detail');
        $('.selector').toggle();
    })
    $('.box, .qrid, .hbqrbot').click(function(){
        $('.selector').hide();
    })
    $('.record').click(function(){
        window.location.replace('../qwthby/kl_detail');
    })
    $('.onemore').click(function(){
        window.location.reload();
    })
</script>

</body>

</html>
