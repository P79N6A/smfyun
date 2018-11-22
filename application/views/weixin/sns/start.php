<!DOCTYPE html>
<html>
<head>
<meta charset='utf8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
 <title>美腿万团大作战</title>
</head>
<body>
<style type="text/css">
body{
 margin: 0px;
 padding: 0px;
 background: url(/sns/img/bg1.jpg) top no-repeat;
 height: 100%;
 width: 100%;
 background-size: cover;
 font-family:'SimHei';
}
.charge {
  height : 20px;
  width : 80%;
  position : absolute;
  top : 35%;
  left : 10%;
  background: url(/sns/img/load.png) repeat;
  background-size: 40px 100%;
  margin : 0px;
  animation : charge 4s infinite;
  -webkit-animation : charge 4s infinite;
  border-radius: 50px;
  z-index: 10;
}

@-webkit-keyframes charge {
  0% {width : 10%;
    border-radius: 50px;
      }
  100% {width : 80%;
    border-radius: 50px;
    }
}

@keyframes charge {
  0% {width : 10%;
      border-radius: 50px;
      }
  100% {width : 80%;
      border-radius: 50px;
      }
}
.load{
    width: 80%;
    left: 10%;
    position: absolute;
    text-align: center;
    top: 35%;
    font-size: 14px;
    line-height: 20px;
    color: #9F2F6F;
    border-radius: 20px;
    box-shadow: inset 0px 3px 0px 0px #2F0C32,3px 0px 0px 0px #8A2C8E,0px 3px 0px 0px #8A2C8E;
    background: #411444;
    letter-spacing: 5px;
}
.mado{
    width: 36px;
    position: absolute;
    top: 24%;
    left:6%;
    animation : mado 4s infinite;
  -webkit-animation : mado 4s infinite;
}
@-webkit-keyframes mado {
  0% {left : 6%; }
  100% {left : 80%; }
}

@keyframes mado {
  0% {left : 6%; }
  100% {left : 80%; }
}
.bottom{
      width: 100%;
    text-align: center;
    position: fixed;
    bottom: 50px;
    font-size: 12px;
    color: black;
}
.mask{
  display: none;
  width: 100%;
  background: white;
  z-index: 2000;
}
.bottom >img{
  width: 18%;
}
</style>
<div class="mask"></div>
<div class="charge"></div>
<img src="/sns/img/mado.gif" class='mado'>
<div class='load'>穿上浪莎&nbsp&nbsp&nbsp都是美腿</div>
<div class="bottom">
  <img src="/sns/img/jd.png">
  <img src="/sns/img/ls.png" style="width: 15%;margin-bottom: 4px;">
</div>
</body>
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  var w = $(window).width();
  var h = $(window).height();
  $('.mask').css({
    'height': h
  });
  $('.bottom').css({
    'font-size': (w/360)*12+'px'
  });
  setTimeout(function(){
    $('.mask').css({
      'display': 'block'
    });
    $('.load').css({
      'display': 'none'
    });
    $('.charge').css({
      'display': 'none'
    });
    $('.mado').css({
      'display': 'none'
    });
    $('.bottom').css({
      'display': 'none'
    });
    window.location.replace('/sns/<?=$type?>?fopenid=<?=$fopenid?>');
 },3000)
});
</script>
</html>
