<!DOCTYPE html>
<html>
<head>
  <title>获取您的地理位置中</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <meta http-equiv="cache-control" content="no-cache, must-revalidate">
  <script src="//cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
  <style>
    body{
      margin:0;
      width:100%;
      height:100%;
      background-color:#D94600;
      font-size:18px;
    }
    .textarea{
      width:90%;
      padding-left:20px;
      min-height:300px;
      max-height:350px;
      margin:0 auto;
      margin-bottom:18%;
      background-color: #ffffff;
      text-align: left;
      border-radius: 8px;
      overflow-y: auto;
      overflow-x: inherit;
    }
    #two{
      width:80%;
      padding:10px;
      padding-left:25px;
      color:#ffffff;
      margin-top:8%;
    }
    .explain{
      text-align: center;
      color:#ffffff;
      /*margin-top:8%;*/
      font-size: 20px;
    }
    #dlocation1{
      font-weight:bold;
      color: yellow;
    }
    @import url(http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,600);

* {
  margin:0;
  padding:0;
  box-sizing:border-box;
  -webkit-box-sizing:border-box;
  -moz-box-sizing:border-box;
  -webkit-font-smoothing:antialiased;
  -moz-font-smoothing:antialiased;
  -o-font-smoothing:antialiased;
  font-smoothing:antialiased;
  text-rendering:optimizeLegibility;
}

body {
  font-family:"Open Sans", Helvetica, Arial, sans-serif;
  font-weight:300;
  font-size: 12px;
  line-height:30px;
  color:#ffffff;
  background:#D94600;
}

.container {
  max-width:400px;
  width:100%;
  margin:0 auto;
  position:relative;
}

#contact input[type="text"], #contact input[type="email"], #contact input[type="tel"], #contact input[type="url"], #contact textarea, #contact button[type="submit"] { font:400 12px/16px "Open Sans", Helvetica, Arial, sans-serif; }

#contact {
  background:none;
  padding:25px;
  margin:5px 0;
}

#contact h3 {
  color: #F96;
  display: block;
  font-size: 30px;
  font-weight: 400;
}

#contact h4 {
  margin:5px 0 15px;
  display:block;
  font-size:13px;
}

fieldset {
  border: medium none !important;
  margin: 0 0 10px;
  min-width: 100%;
  padding: 0;
  width: 100%;
}

#contact input[type="text"], #contact input[type="email"], #contact input[type="tel"], #contact input[type="url"], #contact textarea {
  width:100%;
  border:1px solid #CCC;
  background:#FFF;
  margin:0 0 5px;
  padding:10px;
}

#contact input[type="text"]:hover, #contact input[type="email"]:hover, #contact input[type="tel"]:hover, #contact input[type="url"]:hover, #contact textarea:hover {
  -webkit-transition:border-color 0.3s ease-in-out;
  -moz-transition:border-color 0.3s ease-in-out;
  transition:border-color 0.3s ease-in-out;
  border:1px solid #AAA;
}

#contact textarea {
  height:100px;
  max-width:100%;
  resize:none;
}

#contact button[type="submit"] {
  cursor:pointer;
  width:100%;
  border:none;
  background:#0CF;
  color:#FFF;
  margin:0 0 5px;
  padding:10px;
  font-size:15px;
}

#contact button[type="submit"]:hover {
  background:#09C;
  -webkit-transition:background 0.3s ease-in-out;
  -moz-transition:background 0.3s ease-in-out;
  transition:background-color 0.3s ease-in-out;
}

#contact button[type="submit"]:active { box-shadow:inset 0 1px 3px rgba(0, 0, 0, 0.5); }

#contact input:focus, #contact textarea:focus {
  outline:0;
  border:1px solid #999;
}
::-webkit-input-placeholder {
 color:#888;
}
:-moz-placeholder {
 color:#888;
}
::-moz-placeholder {
 color:#888;
}
:-ms-input-placeholder {
 color:#888;
}
  </style>
</head>
<body>

<div class="container">
  <form id="contact" action="" method="post">

     <fieldset>
      <h4> 本次活动的地区范围:<span id="location2"><?=implode(',',$p_location);?></span></h4>
    </fieldset><hr>
    <fieldset>
      <h4>您的位置在</h4>
      <div style="width:100%;height:35px;color:black;background:#ffffff;border-radius: 8px;padding-left:5px"><span id="location1">正在获取</span></div>

    </fieldset>
    <hr>
    <h4><div class="textshow"></div></h4><hr>

  </form>
<h4 style="text-align:center;font-size:16px">活动说明</h4>
    <div class="textarea" style="color:black"><?=$area['info']?></div>
  <div style="display:none;" id='false'><?=$area['reply']?></div>
  <div style="display:none;" id='true'><?=$area['isreply']?></div>
</div>
<div style="text-align:center;clear:both">
<script src="/gg_bd_ad_720x90.js" type="text/javascript"></script>
<script src="/follow.js" type="text/javascript"></script>
</div>






</body>




</body>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
  wx.config({
    debug: 0,
    appId: '<?php echo $jsapi["appId"];?>',
    timestamp: '<?php echo $jsapi["timestamp"];?>',
    nonceStr: '<?php echo $jsapi["nonceStr"];?>',
    signature: '<?php echo $jsapi["signature"];?>',
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中
      'checkJsApi',
      'onMenuShareTimeline',
      'onMenuShareAppMessage',
      'getLocation'
      ]
    });
  wx.ready(function () {
    //自动执行的

    wx.getLocation({
        type: 'wgs84', // 默认为wgs84的gps坐标，如果要返回直接给openLocation用的火星坐标，可传入'gcj02'
        success: function (res) {
            var latitude = res.latitude; // 纬度，浮点数，范围为90 ~ -90
            var longitude = res.longitude; // 经度，浮点数，范围为180 ~ -180。
            var speed = res.speed; // 速度，以米/每秒计
            var accuracy = res.accuracy; // 位置精度
            $.ajax({
              url: '/qwtwdb/check_location?x='+latitude+'&y='+longitude,
              type: 'get',
              dataType: 'text',
              //data: {param1: 'value1'},
              success: function (res){
                  $('#location1').html(res);
                  var p_loc=$("#location2").html();
                  var u_loc=$("#location1").html();

                  var p_locArray=new Array();
                  p_locArray=p_loc.split(",");

                  var count = 0;
                  for(var i=0;i<p_locArray.length;i++){
                    if(u_loc.indexOf(p_locArray[i])==-1){

                    }else{
                      count++;
                    }
                  }
                  if(count >0){
                    var t=$('#true').html();
                    // alert(t);
                    $('.textshow').html(t);
                  }else{
                    var f=$('#false').html();
                    // alert(f);
                    $('.textshow').html(f);
                  }
                }
              })
          }
        });
});

wx.error(function (res) {
  alert(res.errMsg);
});
</script>
</html>



