<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf8" />
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
    <title>33年，一味金佛</title>
</head>
    <link rel="stylesheet" href="http://jfb.smfyun.com/xiq/css/animation.css">
    <link rel="stylesheet" href="http://jfb.smfyun.com/xiq/css/index.css">
<style type="text/css">
body{
    margin: 0px;
    padding: 0px;
    border: 0px;
}
.p0,.p1,.p2,.p4,.p5{
    display: none;
    width: 100%;
    height: 100%;
    background-color: #000;
    position: fixed;
}
.p3{
    display: none;
    width: 100%;
    height: 100%;
    position: fixed;
}
.p4-ex{
    display: none;
    width: 100%;
    height: 100%;
    position: fixed;
    background-color: #fff;
}
.page{
    width: 100%;
    position: relative;
}
.s0-bg,.s1-bg,.s2-bg,.s4-bg,.s5-bg{
    width: 100%;
}
.s0-btn{
    position: absolute;
    bottom: 14%;
    right: 47%;
    width: 4%;
}
.s0-pot{
    position: absolute;
    bottom: 28%;
    right: 18%;
    width: 41.2%
}
.ratenum{
    width: 100%;
    text-align: center;
    position: absolute;
    font-size: .2rem;
    color: #e5e5e5;
    bottom: 65%;
    text-shadow: 0px 0px 1rem yellow;
}
.s1-btn{
    position: absolute;
    width: 9.7%;
    right: 14.7%;
    bottom: 15%;
}
.s1-icon1{
    position: absolute;
    width: 12.4%;
    width: 12.4%;
    right: 11.9%;
    bottom: 33.7%;
}
.s1-icon2{
    position: absolute;
    width: 10.8%;
    right: 14.1%;
    bottom: 34.4%;
}
.s2-btn{
    position: absolute;
    width: 12.8%;
    left: 73%;
    top: 49.1%;
}
.s4-btn1{
    position: absolute;
    width: 31.1%;
    left: 56%;
    top: 82%;
}
.s4-btn2{
    position: absolute;
    width: 57.2%;
    left: 33%;
    top: 64%;
}
.s4-ex-gif1{
    width: 100%;
    display: block;
}
.s4-ex-gif2{
    width: 100%;
}
.s4-ex-btn{
    position: fixed;
    width: 57.2%;
    left: 23%;
    bottom: 5%;
}
.s5-btn1{
    position: absolute;
    width: 52.3%;
    left: 24%;
    top: 67%;
}
.s5-btn2{
    position: absolute;
    width: 26.7%;
    left: 35%;
    top: 80%;
}
/*.image-wrapper .image.show {
  opacity: 1;
  -webkit-animation: mask-play 2.3s steps(109) forwards;
  animation: mask-play 2.3s steps(109) forwards
}

.image-wrapper .image.hide {
  -webkit-animation: mask-play-back 2.3s steps(109) forwards;
  animation: mask-play-back 2.3s steps(109) forwards
}*/
.image-wrapper {
  z-index: 2;
  position: absolute;
  bottom: 0rem;
  right: 0rem;
  width: 7.5rem;
  height: 6.1rem
}

.image-wrapper .image {
  opacity: 1;
  position: absolute;
  bottom: 0rem;
  right: 0rem;
  width: 7.5rem;
  height: 6.1rem;
  background-size: contain;
  /*-webkit-mask: url(http://jfb.smfyun.com/xiq/img/s3-image-mask.png);
  mask: url(http://jfb.smfyun.com/xiq/img/s3-image-mask.png);
  -webkit-mask-size: 11000% 100%;
  mask-size: 11000% 100%*/
}
.image-wrapper .image:nth-child(1) {
  background-image: url(http://jfb.smfyun.com/xiq/img/s3-s1-bg.png)
}

.image-wrapper .image:nth-child(2) {
  background-image: url(http://jfb.smfyun.com/xiq/img/s3-s2-bg.png)
}

.image-wrapper .image:nth-child(3) {
  background-image: url(http://jfb.smfyun.com/xiq/img/s3-s3-bg.png)
}

.image-wrapper .image:nth-child(4) {
  background-image: url(http://jfb.smfyun.com/xiq/img/s3-s4-bg.png)
}

.image-wrapper .image:nth-child(5) {
  background-image: url(http://jfb.smfyun.com/xiq/img/s3-s5-bg.png)
}

.image-wrapper .image:nth-child(6) {
  background-image: url(http://jfb.smfyun.com/xiq/img/s3-s6-bg.png)
}

.text-wrapper .text.show {
  opacity: 1;
  -webkit-animation: mask-play 2.3s steps(109) forwards;
  animation: mask-play 2.3s steps(109) forwards
}

.text-wrapper .text.hide {
  -webkit-animation: mask-play-back 2.3s steps(109) forwards;
  animation: mask-play-back 2.3s steps(109) forwards
}
.text-wrapper {
  z-index: 2;
  position: absolute;
  top: .25rem;
  right: 0rem;
  width: 5.8rem;
  height: 6.1rem
}

.text-wrapper .text {
  opacity: 0;
  position: absolute;
  top: 0rem;
  right: 0rem;
  width: 5.8rem;
  height: 6.1rem;
  background-size: contain;
  -webkit-mask: url(http://jfb.smfyun.com/xiq/img/s3-text-mask.png);
  mask: url(http://jfb.smfyun.com/xiq/img/s3-text-mask.png);
  -webkit-mask-size: 11000% 100%;
  mask-size: 11000% 100%
}
.text-wrapper .text:nth-child(1) {
  background-image: url(http://jfb.smfyun.com/xiq/img/s3-s1-desc.png)
}

.text-wrapper .text:nth-child(2) {
  background-image: url(http://jfb.smfyun.com/xiq/img/s3-s2-desc.png)
}

.text-wrapper .text:nth-child(3) {
  background-image: url(http://jfb.smfyun.com/xiq/img/s3-s3-desc.png)
}

.text-wrapper .text:nth-child(4) {
  background-image: url(http://jfb.smfyun.com/xiq/img/s3-s4-desc.png)
}

.text-wrapper .text:nth-child(5) {
  background-image: url(http://jfb.smfyun.com/xiq/img/s3-s5-desc.png)
}

.text-wrapper .text:nth-child(6) {
  background-image: url(http://jfb.smfyun.com/xiq/img/s3-s6-desc.png)
}

.container{
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
    background: url(http://jfb.smfyun.com/xiq/img/s3-bg.png) bottom center no-repeat;
    background-size: cover;
}
.title-wrapper {
  z-index: 2;
  position: absolute;
  top: .7rem;
  left: .72rem;
  width: .41rem;
  height: 4.2rem
}

.title {
  opacity: 0;
  position: absolute;
  top: 0rem;
  right: 0rem;
  width: .4rem;
  height: 1.12rem;
  overflow: hidden
}

.title img {
  display: block;
  margin: auto;
  width: 100%
}

.title.show {
  opacity: 1;
  -webkit-animation: flip .4s 3 ease;
  animation: flip .4s 3 ease;
  height: 4.2rem;
  transition: opacity 1s ease, height 1s 1s ease
}

.title.hide {
  height: 1.12rem;
  -webkit-animation: flip_2 .4s 1s 3 ease;
  animation: flip_2 .4s 1s 3 ease;
  opacity: 0;
  transition: height 1s ease, opacity 1s 1s ease
}
.s3-btn{
    width: .8rem;
    top: 70%;
    position: absolute;
    left: 10%;
    z-index: 3;
    cursor: pointer;
}
.s3-arrow{
    position: absolute;
    width: 10%;
    top: 55%;
    left: 10%;
}

</style>
<div class="p0" style="display:block">
    <div class="page">
        <div class="ratenum"></div>
        <img class="s0-bg" src="http://jfb.smfyun.com/xiq/img/s0-bg.png">
        <img class="s0-btn" src="http://jfb.smfyun.com/xiq/img/s0-btn.png" style="display:none">
        <img id="pot1" class="s0-pot" src="http://jfb.smfyun.com/xiq/img/s0-pot1.png">
        <img id="pot2" class="s0-pot" src="http://jfb.smfyun.com/xiq/img/s0-pot2.png" style="display:none;">
        <img id="pot3" class="s0-pot" src="http://jfb.smfyun.com/xiq/img/s0-pot3.png" style="display:none;">
        <img id="pot4" class="s0-pot" src="http://jfb.smfyun.com/xiq/img/s0-pot4.png" style="display:none;">
        <img id="pot5" class="s0-pot" src="http://jfb.smfyun.com/xiq/img/s0-pot5.png" style="display:none;">
        <img id="pot6" class="s0-pot" src="http://jfb.smfyun.com/xiq/img/s0-pot6.png" style="display:none;">
        <img id="pot7" class="s0-pot" src="http://jfb.smfyun.com/xiq/img/s0-pot7.png" style="display:none;width:55.9%;">
    </div>
</div>
<div class='p1'>
    <div class="page">
        <video class='start' x5-video-player-type="h5" x5-video-player-fullscreen="true" playsinline webkit-playsinline x-webkit-airplay="true" webkit-playsinline="true" preload="auto"  autobuffer  poster="" id='video1'  width="100%" height="100%" src="../xiq/video/v_1.mp4">
        </video>
        <!-- video最后定格图片和点击赴约按钮 -->
        <img class="s1-bg" src="http://jfb.smfyun.com/xiq/img/s1-bg.png" style="display:none">
        <img class="s1-btn" src="http://jfb.smfyun.com/xiq/img/s1-btn.png" style="display:none">
        <img class="s1-icon1" src="http://jfb.smfyun.com/xiq/img/s1-icon1.png" style="display:none">
        <img class="s1-icon2" src="http://jfb.smfyun.com/xiq/img/s1-icon2.png" style="display:none">
    </div>
</div>
<div class='p2'>
    <div class="page">
        <video playsinline webkit-playsinline x-webkit-airplay="true" webkit-playsinline="true" preload="auto"  autobuffer x5-video-player-type="h5" x5-video-player-fullscreen="true"  poster="" id='video2'  width="100%" height="100%" src="../xiq/video/v_2.mp4">
        </video>
        <!-- video最后定格图片和穿越33年按钮 -->
        <img class="s2-bg" src="http://jfb.smfyun.com/xiq/img/s2-bg.png" style="display:none">
        <img class="s2-btn" src="http://jfb.smfyun.com/xiq/img/s2-btn.png" style="display:none">
    </div>
</div>
<!-- 穿越33年时间轴 -->
<div class="p3">
    <div class="container">
        <audio autobuffer preload="auto" id='audio' src="../xiq/audio/s3-audio.mp3">
        </audio>
        <img class="s3-btn" src="http://jfb.smfyun.com/xiq/img/s3-btn.png" style="display:none">
        <img class="s3-arrow" src="http://jfb.smfyun.com/xiq/img/s3-arrow.png">
        <div class="text-wrapper">
            <div class="text"></div>
            <div class="text"></div>
            <div class="text"></div>
            <div class="text"></div>
            <div class="text"></div>
            <div class="text"></div>
        </div>
        <div class="title-wrapper">
            <div class="title">
                <img src="http://jfb.smfyun.com/xiq/img/s3-s1-title.png">
            </div>
            <div class="title">
                <img src="http://jfb.smfyun.com/xiq/img/s3-s2-title.png">
            </div>
            <div class="title">
                <img src="http://jfb.smfyun.com/xiq/img/s3-s3-title.png">
            </div>
            <div class="title">
                <img src="http://jfb.smfyun.com/xiq/img/s3-s4-title.png">
            </div>
            <div class="title">
                <img src="http://jfb.smfyun.com/xiq/img/s3-s5-title.png">
            </div>
            <div class="title">
                <img src="http://jfb.smfyun.com/xiq/img/s3-s6-title.png">
            </div>
        </div>
        <div class="image-wrapper">
            <div class="image" style="display:none"></div>
            <div class="image" style="display:none"></div>
            <div class="image" style="display:none"></div>
            <div class="image" style="display:none"></div>
            <div class="image" style="display:none"></div>
            <div class="image" style="display:none"></div>
        </div>
        <div class="interaction">
            <img class="mark" src="http://jfb.smfyun.com/xiq/img/s3-timeline.png">
            <input type="hidden" id="hintnum" value="1">
            <div class="hint">
                <div class="left"></div>
                <div class="middle"></div>
                <div class="right"></div>
            </div>
        </div>
    </div>
</div>
<div class="p4">
    <div class="page">
        <video playsinline webkit-playsinline x5-video-player-type="h5" x5-video-player-fullscreen="true" x-webkit-airplay="true" webkit-playsinline="true" preload="auto"  autobuffer poster="" id='video3'  width="100%" height="100%" src="../xiq/video/v_3.mp4">
        </video>
        <!-- video最后定格图片和穿越33年按钮 -->
        <img class="s4-bg" src="http://jfb.smfyun.com/xiq/img/s4-bg.png" style="display:none">
        <img class="s4-btn1" src="http://jfb.smfyun.com/xiq/img/s4-btn1.png" style="display:none">
        <img class="s4-btn2" src="http://jfb.smfyun.com/xiq/img/s4-btn2.png" style="display:none">
    </div>
</div>
<div class="p4-ex">
    <div class="page">
        <img class="s4-ex-gif1" src="http://jfb.smfyun.com/xiq/img/s4-ex-gif1.gif">
        <img class="s4-ex-gif2" src="http://jfb.smfyun.com/xiq/img/s4-ex-gif2.gif">
        <img class="s4-ex-btn" src="http://jfb.smfyun.com/xiq/img/s4-ex-btn.png">
    </div>
</div>
<div class="p5">
    <div class="page">
        <img class="s5-bg" src="http://jfb.smfyun.com/xiq/img/s5-bg.png">
        <img class="s5-btn1" src="http://jfb.smfyun.com/xiq/img/s5-btn1.png">
        <img class="s5-btn2" src="http://jfb.smfyun.com/xiq/img/s5-btn2.png">
    </div>
</div>
<!-- <div id="loading">
    <img src="https://www.baidu.com/img/baidu_logo.gif">
    <img src="http://jfb.smfyun.com/xiq/img/1.jpg">
    <img src="http://jfb.smfyun.com/xiq/img/2.jpg">
</div> -->
<script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script src="http://jfb.smfyun.com/xiq/js/animation.min.js"></script>
<script>
$('.container').on('touchstart',function(e) {
    console.log(e);
    var touch = e.originalEvent.changedTouches[0];

    start_x = touch.pageX;
    start_y = touch.pageY;
});
$('.container').on('touchend',function(e) {
    // console.log(e);
    var touch = e.originalEvent.changedTouches[0];

    var end_x = touch.pageX;
    var end_y = touch.pageY;
    var level = end_x - start_x;
    var vertical = start_y - end_y;
    var rate = level/vertical;
    console.log(rate);
    if (rate>2||rate<-2) {
        var hint = $('#hintnum').val();
        var hintnum = parseInt(hint);
        if (level>100 && hintnum<6) {
            $('.hint').removeClass('hint-'+hintnum);
            var hintnumnew = hintnum + 1;
            $('.hint').addClass('hint-'+hintnumnew);
            $('.show').addClass('hide');
            $('.show').removeClass('show');
            var hintnumimg = hintnum - 1;
            $('.image-wrapper div:eq('+hintnumimg+')').fadeOut(2300);
            $('#hintnum').val(hintnumnew);
            setTimeout('onestep()',2500);
            // if (hintnumnew == 6) {
            //     $('.s3-btn').fadeIn(2300);
            // };
            $('.s3-arrow').hide();
        };
        if (level<-100 && hintnum>1) {
            $('.hint').removeClass('hint-'+hintnum);
            var hintnumnew = hintnum - 1;
            $('.hint').addClass('hint-'+hintnumnew);
            $('#hintnum').val(hintnumnew);
            $('.show').addClass('hide');
            $('.show').removeClass('show');
            var hintnumimg = hintnum - 1;
            $('.image-wrapper div:eq('+hintnumimg+')').fadeOut(2300);
            setTimeout('onestep()',2500);
        };
    };
});
function onestep(){
    var hint = $('#hintnum').val();
    var hintnum = parseInt(hint)-1;
    $('.text-wrapper div:eq('+hintnum+')').removeClass('hide');
    $('.text-wrapper div:eq('+hintnum+')').addClass('show');
    $('.title-wrapper div:eq('+hintnum+')').removeClass('hide');
    $('.title-wrapper div:eq('+hintnum+')').addClass('show');
    $('.image-wrapper div:eq('+hintnum+')').fadeIn(2300);
    // $('.image-wrapper div:eq('+hintnum+')').addClass('show');
}
var imgarr = new Array
// imgarr[0] = "https://www.baidu.com/img/baidu_logo.gif";
// imgarr[1] = "http://jfb.smfyun.com/xiq/img/1.jpg";
// imgarr[2] = "http://jfb.smfyun.com/xiq/img/2.jpg";
imgarr[0] = "http://jfb.smfyun.com/xiq/img/s0-bg.png";
imgarr[1] = "http://jfb.smfyun.com/xiq/img/s0-btn.png";
imgarr[2] = "http://jfb.smfyun.com/xiq/img/s0-pot1.png";
imgarr[3] = "http://jfb.smfyun.com/xiq/img/s0-pot2.png";
imgarr[4] = "http://jfb.smfyun.com/xiq/img/s0-pot3.png";
imgarr[5] = "http://jfb.smfyun.com/xiq/img/s0-pot4.png";
imgarr[6] = "http://jfb.smfyun.com/xiq/img/s0-pot5.png";
imgarr[7] = "http://jfb.smfyun.com/xiq/img/s0-pot6.png";
imgarr[8] = "http://jfb.smfyun.com/xiq/img/s0-pot7.png";
imgarr[9] = "http://jfb.smfyun.com/xiq/img/s1-bg.png";
imgarr[10] = "http://jfb.smfyun.com/xiq/img/s1-btn.png";
imgarr[11] = "http://jfb.smfyun.com/xiq/img/s1-icon1.png";
imgarr[12] = "http://jfb.smfyun.com/xiq/img/s1-icon2.png";
imgarr[13] = "http://jfb.smfyun.com/xiq/img/s2-bg.png";
imgarr[14] = "http://jfb.smfyun.com/xiq/img/s2-btn.png";
imgarr[15] = "http://jfb.smfyun.com/xiq/img/s3-bg.png";
imgarr[16] = "http://jfb.smfyun.com/xiq/img/s3-btn.png";
imgarr[17] = "http://jfb.smfyun.com/xiq/img/s3-s1-bg.png";
imgarr[18] = "http://jfb.smfyun.com/xiq/img/s3-s1-desc.png";
imgarr[19] = "http://jfb.smfyun.com/xiq/img/s3-s1-title.png";
imgarr[20] = "http://jfb.smfyun.com/xiq/img/s3-s2-bg.png";
imgarr[21] = "http://jfb.smfyun.com/xiq/img/s3-s2-desc.png";
imgarr[22] = "http://jfb.smfyun.com/xiq/img/s3-s2-title.png";
imgarr[23] = "http://jfb.smfyun.com/xiq/img/s3-s3-bg.png";
imgarr[24] = "http://jfb.smfyun.com/xiq/img/s3-s3-desc.png";
imgarr[25] = "http://jfb.smfyun.com/xiq/img/s3-s3-title.png";
imgarr[26] = "http://jfb.smfyun.com/xiq/img/s3-s4-bg.png";
imgarr[27] = "http://jfb.smfyun.com/xiq/img/s3-s4-desc.png";
imgarr[28] = "http://jfb.smfyun.com/xiq/img/s3-s4-title.png";
imgarr[29] = "http://jfb.smfyun.com/xiq/img/s3-s5-bg.png";
imgarr[30] = "http://jfb.smfyun.com/xiq/img/s3-s5-desc.png";
imgarr[31] = "http://jfb.smfyun.com/xiq/img/s3-s5-title.png";
imgarr[32] = "http://jfb.smfyun.com/xiq/img/s3-s6-bg.png";
imgarr[33] = "http://jfb.smfyun.com/xiq/img/s3-s6-desc.png";
imgarr[34] = "http://jfb.smfyun.com/xiq/img/s3-s6-title.png";
imgarr[35] = "http://jfb.smfyun.com/xiq/img/s3-text-mask.png";
imgarr[36] = "http://jfb.smfyun.com/xiq/img/s3-image-mask.png";
imgarr[37] = "http://jfb.smfyun.com/xiq/img/s3-timeline.png";
imgarr[38] = "http://jfb.smfyun.com/xiq/img/s4-bg.png";
imgarr[39] = "http://jfb.smfyun.com/xiq/img/s4-btn1.png";
imgarr[40] = "http://jfb.smfyun.com/xiq/img/s4-btn2.png";
imgarr[41] = "http://jfb.smfyun.com/xiq/img/s4-ex-gif1.gif";
imgarr[42] = "http://jfb.smfyun.com/xiq/img/s4-ex-gif2.gif";
imgarr[43] = "http://jfb.smfyun.com/xiq/img/s4-ex-btn.png";
imgarr[44] = "http://jfb.smfyun.com/xiq/img/s5-bg.png";
imgarr[45] = "http://jfb.smfyun.com/xiq/img/s5-btn1.png";
imgarr[46] = "http://jfb.smfyun.com/xiq/img/s5-btn2.png";
imgarr[47] = "http://jfb.smfyun.com/xiq/img/s3-arrow.png";

var has = 0;
function intToFloat(val){
        return new Number(val).toFixed(0);
    }
function loadImage(url, callback) {
    var img = new Image(); //创建一个Image对象，实现图片的预下载
    img.src = url;
    if (img.complete) { // 如果图片已经存在于浏览器缓存，直接调用回调函数
        console.log('img_complete'+url);
        // callback.call(img);
        has++;
        console.log(has);
        var rate = intToFloat(has*100/48)+'%';
        // console.log(rate);
        $('.ratenum').html(rate);
        if (has==8) {
            $('#pot1').fadeOut();
            $('#pot2').fadeIn();
        };
        if (has==16) {
            $('#pot2').fadeOut();
            $('#pot3').fadeIn();
        };
        if (has==24) {
            $('#pot3').fadeOut();
            $('#pot4').fadeIn();
        };
        if (has==32) {
            $('#pot4').fadeOut();
            $('#pot5').fadeIn();
        };
        if (has==40) {
            $('#pot5').fadeOut();
            $('#pot6').fadeIn();
        };
        if (has==48) {
            $('#pot6').fadeOut();
            $('#pot7').fadeIn();
            $('.s0-btn').fadeIn(1000);
            $('.ratenum').fadeOut(1000);
        };
        return; // 直接返回，不用再处理onload事件
    }
    img.onload = function() { //图片下载完毕时异步调用callback函数。
        console.log('img_onload'+url);
        has++;
        console.log(has);
        var rate = intToFloat(has*100/48)+'%';
        // console.log(rate);
        $('.ratenum').html(rate);
        if (has==8) {
            $('#pot1').fadeOut();
            $('#pot2').fadeIn();
        };
        if (has==16) {
            $('#pot2').fadeOut();
            $('#pot3').fadeIn();
        };
        if (has==24) {
            $('#pot3').fadeOut();
            $('#pot4').fadeIn();
        };
        if (has==32) {
            $('#pot4').fadeOut();
            $('#pot5').fadeIn();
        };
        if (has==40) {
            $('#pot5').fadeOut();
            $('#pot6').fadeIn();
        };
        if (has==48) {
            $('#pot6').fadeOut();
            $('#pot7').fadeIn();
            $('.s0-btn').fadeIn(1000);
            $('.ratenum').fadeOut(1000);
        };
        // callback.call(img); //将回调函数的this替换为Image对象
    };
};
for (var i = 0; imgarr[i]; i++) {
   loadImage(imgarr[i]);
}
// setInterval(function(){
//    console.log(has/9);
//    if(has/9==1){
//       $('.p0').css({
//           display: 'block'
//       });
//    }
// },500)
videoElem1 = document.getElementById('video1');
videoElem2 = document.getElementById('video2');
audioElem = document.getElementById('audio');
videoElem3 = document.getElementById('video3');
videoElem1.addEventListener('canplay',function(){
    // has++;
});
$('.s0-btn').click(function(){
    $('.p0').fadeOut(500);
    $('.p1').fadeIn(500);
      videoElem1.play();
    // $('.p3').fadeIn(500);
    // $('.text-wrapper div:eq(0)').addClass('show');
    // $('.title-wrapper div:eq(0)').addClass('show');
    // $('.image-wrapper div:eq(0)').fadeIn(2300);
    // $('.hint').addClass('hint-1');
})
videoElem1.onended = function(){
    $('#video1').fadeOut(1000);
    $('.s1-bg').fadeIn(1000);
    $('.s1-btn').fadeIn(1000);
    $('.s1-icon1').fadeIn(1000);
    // $('.s1-icon2').fadeIn(1000);
    var icon = 1;
    var anime1 = setInterval(function(){
        if (icon==1) {
            $('.s1-icon2').fadeIn(1000);
            // $('.s1-icon2').show();
            $('.s1-icon1').fadeOut(1000);
            // $('.s1-icon1').hide();
            icon = 0;
        }else{
            $('.s1-icon1').fadeIn(1000);
            // $('.s1-icon1').show();
            $('.s1-icon2').fadeOut(1000);
            // $('.s1-icon2').hide();
            icon = 1;
        }
    },1000)
    $('.s1-btn').click(function(){
        clearInterval(anime1);
    })
}
$('.s1-btn').click(function(){
    $('.p1').fadeOut(500);
    $('.p2').fadeIn(500);
      videoElem2.play();
})
$('.s2-btn').click(function(){
    $('.p2').fadeOut(500);
    $('.p3').fadeIn(500);
    $('.text-wrapper div:eq(0)').addClass('show');
    $('.title-wrapper div:eq(0)').addClass('show');
    $('.image-wrapper div:eq(0)').fadeIn(2300);
    $('.hint').addClass('hint-1');
    $('.s3-btn').fadeIn(2300);
    setTimeout(function(){
      audioElem.play();
    },1000);
    var arrow = 1;
    var anime2 = setInterval(function(){
        if (arrow==1) {
            $('.s3-arrow').fadeIn(1000);
            arrow = 0;
        }else{
            $('.s3-arrow').fadeOut(1000);
            // $('.s1-icon2').hide();
            arrow = 1;
        }
    },1000);
    $('.container').on('touchend',function(e){
        clearInterval(anime2);
    })
})
$(document).on('click', '.s3-btn', function(event) {
    audioElem.pause();
    $('.p3').fadeOut(500);
    $('.p4').fadeIn(500);
    videoElem3.play();
});
$('.s4-btn2').click(function(){
    $('.p4').fadeOut(500);
    $('.p5').fadeIn(500);
})
$('.s4-btn1').click(function(){
    $('.p4').fadeOut(500);
    $('.p4-ex').fadeIn(500);
})
$('.s4-ex-btn').click(function(){
    $('.p4-ex').fadeOut(500);
    $('.p5').fadeIn(500);
})
$('.s5-btn2').click(function(){
    self.location = "http://<?=$_SERVER['HTTP_HOST']?>/xiq/xiqiu";
})
$('.s5-btn1').click(function(){
    window.location.href = "https://h5.youzan.com/v2/showcase/homepage?alias=gx6utdi5&sf=wx_menu";
})
// $(document).on('touchstart', '.start', function() {
//       videoElem1.play();
// })
videoElem2.onended = function(){
    $('#video2').fadeOut(1000);
    $('.s2-bg').fadeIn(1000);
    $('.s2-btn').fadeIn(1000);
}
videoElem3.onended = function(){
    $('#video3').fadeOut(1000);
    $('.s4-bg').fadeIn(1000);
    $('.s4-btn1').fadeIn(1000);
    $('.s4-btn2').fadeIn(1000);
}
</script>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.2.0.js"></script>
<script type="text/javascript">
    wx.config({
        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
        appId: "<?=$jsapi['appId']?>", // 必填，公众号的唯一标识
        timestamp: <?=$jsapi['timestamp']?>, // 必填，生成签名的时间戳
        nonceStr: "<?=$jsapi['nonceStr']?>", // 必填，生成签名的随机串
        signature: "<?=$jsapi['signature']?>", // 必填，签名，见附录1
        jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] //
    });
    wx.ready(function(){
        wx.onMenuShareTimeline({//朋友圈分享
            title: "33年，一味金佛", // 分享标题
            link: "", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: "http://jfb.smfyun.com/xiq/img/xiqlogo.png", // 分享图标
            success: function () {

            },
            cancel: function () {

            }
        });
        wx.onMenuShareAppMessage({//分享给朋友
            title: "33年，一味金佛", // 分享标题
            desc: "传承人的茶局 | 穿越33年，来自一位制茶女匠的茶局邀请。", // 分享描述
            link: "", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
            imgUrl: "http://jfb.smfyun.com/xiq/img/xiqlogo.png", // 分享图标
            success: function () {

            },
            cancel: function () {

            }
        });
    });
</script>
