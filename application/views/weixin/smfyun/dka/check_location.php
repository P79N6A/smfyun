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
      width:80%;
      padding:10px;
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
  </style>
</head>
<body>
  <div id="two">
  <ul>
    <li><div>本次活动的地区范围:<span id="location2"><?=implode(',',$p_location);?></span></div></li><br>
    <li><div>您的位置在:<span id="location1">正在获取</span></div></li><br>
        <div class="textshow"></div>
    </ul>
  </div><br>
  <div class="explain">活动说明</div><br>
  <div class="textarea"><pre><?=$area['info']?></pre></div>
  <div style="display:none;" id='false'><?=$area['reply']?></div>
  <div style="display:none;" id='true'><?=$area['isreply']?></div>

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
      'onMenuShareAppMessage'
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
              url: '/qwtdka/check_location?x='+latitude+'&y='+longitude,
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



