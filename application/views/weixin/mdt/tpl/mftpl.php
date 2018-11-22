<html>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="author" content="m.178hui.com" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<meta name="format-detection" content="telephone=no">

<title>麻袋团 - <?=$title?></title>

<link href="/mdt/css/owl.carousel.css" rel="stylesheet">
<link href="/mdt/css/public.css" rel="stylesheet" type="text/css" />
<link href="/mdt/css/index.css" rel="stylesheet" type="text/css" />
<link href="/mdt/css/baoliao.css?v=33" rel="stylesheet" type="text/css" />

<!-- JavaScript includes -->
<script src="/mdt/js/jquery-1.8.3.min.js"></script>
<script src="/mdt/js/owl.carousel.min.js"></script>
<script src="/mdt/layer/layer.js"></script>

<script type="text/javascript">
function countDown(time,id){
  var day_elem = $(id).find('.day');
  var hour_elem = $(id).find('.hour');
  var minute_elem = $(id).find('.minute');
  var second_elem = $(id).find('.second');
  var end_time = new Date(time).getTime(),//月份是实际月份-1
  sys_second = (end_time-new Date().getTime())/1000;
  var timer = setInterval(function(){
    if (sys_second > 1) {
      sys_second -= 1;
      var day = Math.floor((sys_second / 3600) / 24);
      var hour = Math.floor((sys_second / 3600) % 24);
      var minute = Math.floor((sys_second / 60) % 60);
      var second = Math.floor(sys_second % 60);
      day_elem && $(day_elem).text(day);//计算天
      $(hour_elem).text(hour<10?"0"+hour:hour);//计算小时
      $(minute_elem).text(minute<10?"0"+minute:minute);//计算分钟
      $(second_elem).text(second<10?"0"+second:second);//计算秒杀
    } else {
      clearInterval(timer);
    }
  }, 1000);
}
</script>

</head>

<body>
<div class="mobile">

  <!--Begin Banna-->
  <div class="top w">
    <div class="m_banner" id="owl">
            <a href="/mdt/miaosha" class="item"><img src="/mdt/img/1.jpg"></a>
            <a href="/mdt/index" class="item"><img src="/mdt/img/2.jpg"></a>
      </div>
      <div class="m_nav">
          <a href="/mdt/index"><img src="/mdt/images/nav1<?=$tid == 1 ? '1' : ''?>.png"></a>
          <a href="/mdt/index/2"><img src="/mdt/images/nav2<?=$tid == 2 ? '2' : ''?>.png"></a>
          <a href="/mdt/miaosha"><img src="/mdt/images/nav3<?=$tid == 3 ? '3' : ''?>.png"></a>
      </div>
  </div>
  <!--End Banna-->

<?=$content?>

  <div class="copyright">Copyright ©2015 麻袋团 版权所有</div>
</div>

<div class="gotop backtop" style="display:none;"></div>

</body>
</html>
<script type="text/javascript">
//返回顶部
$(document).ready(function(){
  $(window).scroll(function () {
    var scrollHeight = $(document).height();
    var scrollTop = $(window).scrollTop();
    var $windowHeight = $(window).innerHeight();
    scrollTop > 75 ? $(".gotop").fadeIn(200).css("display","block") : $(".gotop").fadeOut(200).css({"background-image":"url(/mdt/images/iconfont-fanhuidingbu.png)"});
  });
  $('.backtop').click(function (e) {
    $(".gotop").css({"background-image":"url(/mdt/images/iconfont-fanhuidingbu_up.png)"});
    e.preventDefault();
    $('html,body').animate({ scrollTop:0});
  });
});
</script>