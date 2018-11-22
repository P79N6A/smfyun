<!DOCTYPE html>
<html>
<head>
<meta charset='utf8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
 <title>中国唯一款手工食盐</title>
<style type="text/css">
.body{
  overflow-y: hidden;
  height: 100%;
  position: absolute;
}
body{
 margin: 0px;
 padding: 0px;
 background: url(../qwt/ydd/bg.png) top no-repeat;
 height: 100%;
 width: 100%;
 background-size: cover;
 font-family:'SimHei';
 position: absolute;
 overflow-y: hidden;
 background-color: #726964;
}
.picker5{
  /*display: inline-block;*/
  height: 30px;
  text-align: center;
  color: #e2e2e2;
  background:rgba(147, 147, 150, 1);
  line-height: 30px;
  border-radius: 100px;
  padding: 0 15px;
  display: inline-block;
}
select,option{
  text-align: center;
}
.confirmbtn{
  width: 60%;
  margin-left: 20%;
  display: block;
  height: 40px;
  text-align: center;
  color: #cfcfcf;
  background: #fb006a;
  line-height: 40px;
  margin-top: 20px;
  /*font-size: 15px;*/
  border-radius: 10px;
}
.wheel-hook{
  text-align: center;
}
ul{
  padding-left: 0px;
}
.location, .loading{
      color: #e2e2e2;
    font-size: 85%;
    width: 100%;
    text-align: center;
    margin-top: 12em;
    padding-bottom: 15px;
}
#triangle-down1 {
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 8px solid #EAE7E7;
    display: inline-block;
    /*position: relative;*/
    /*left: 70%;*/
    margin-bottom: 2px;
    margin-left: 10px;
}
#triangle-down2 {
    width: 0;
    height: 0;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 8px solid #EAE7E7;
    display: inline-block;
    /*position: relative;*/
    margin-bottom: 2px;
    margin-left: 10px;
}
.none{
  padding: 20px 40px;
  color: #cfcfcf;
  font-size: 85%;
  line-height: 1.5em;
}
.container{
  /*padding: 10px 20px;*/
  font-size: 70%;
  overflow: scroll;
  height: calc(100% - 26em);
  cursor: pointer;
  /*z-index: 100;*/
    border-top: 1px solid rgba(140,133,128,.5);
    /*border-bottom: 1px solid rgba(140,133,128,.5);*/
}
.shop{
  padding: 10px 20px;
  color: #cfcfcf;
  /*text-decoration: none;*/
    border-bottom: 1px solid rgba(140,133,128,.5);
    /*margin-top: -1px;*/
}
.title{
  font-weight: bold;
}
.title img{
  height: 1em;
}
.address{
  font-size: 90%;
  margin-top: 0px;
  width: 90%;
  position: relative;
}
.address img{
  /*float: right;*/
  position: absolute;
  top: 0;
  right: -10%;
  height: 1em;
}
.top{
  position: fixed;
  width: 100%;
  height: 9em;
}
.bottom{
  padding: 20px;
  text-align: center;
  position: absolute;
  bottom: 0;
  width: calc(100% - 40px);
}
.bottom img{
  height: 1.8em;
}
</style>
</head>
<body class="body">
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript">
var _htmlFontSize = (function () {
  var clientWidth = document.documentElement ? document.documentElement.clientWidth : document.body.clientWidth;
  if (clientWidth > 640) clientWidth = 640;
  document.documentElement.style.fontSize = clientWidth * 1 / 20 + "px";
  return clientWidth * 1 / 20;
})();
</script>
<div class="loading">正在定位中，请稍候……</div>
<div class='location' style="display:none;">
  <div>您当前定位城市：
    <div id="picker1" class="picker5">
      <span class='pro'></span>
      <span id='triangle-down1'></span>
    </div>
  </div>
  <div id="picker2" class="picker5" style="margin-top:.6em;margin-left:8.4em">
    <span class='city'></span>
    <span id='triangle-down2'></span>
  </div>
</div>
<div class="none" style="display:none">
  您当前所在城市没有服务网点，点击确定进入总店购买（您还可以进入公众号申请成为盐多多本地服务点）
<a href="https://h5.youzan.com/v2/showcase/homepage?alias=bXb0I2bzpu&oid=21145531" class='confirmbtn' style="text-decoration:none">确定</a>
</div>
<div class="container">
<?php foreach ($area as $k => $v):?>
  <a href="<?=$v['url']?>" class="grid <?=$v['class']?>" style="text-decoration:none;">
    <div class="shop">
      <div class="title">
        <img src="../qwt/ydd/shop.png">
        <?=$v['name']?>
      </div>
      <div class="address">
        <?=$v['address']?>
        <img src="../qwt/ydd/more.png">
      </div>
    </div>
  </a>
<?php endforeach?>
</div>
<div class="bottom">
  <img src="../qwt/ydd/logo.png">
</div>
</body>
<script type="text/javascript">
  $('.confirmbtn').click(function() {
    window.location.href = window.localhref;
  });
</script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
//根据市生命数组
window.local = new Array();
for (var i = 0; i <=9 ; i++) {
  window.local[i] = new Array();
};
window.local[0][0] ='chengdu';
window.local[0][1] ='ziyang';
window.local[0][2] ='huaying';
window.local[1][0] ='chongqing';

window.mcity = new Array();
window.mcity[0] = '成都市';
window.mcity[1] = '资阳市';
window.mcity[2] = '华蓥市';
window.mcity[3] = '重庆市';

window.classname = new Array();
window.classname[0] = 'chengdu';
window.classname[1] = 'ziyang';
window.classname[2] = 'huaying';
window.classname[3] = 'chongqing';
var callback = '<?=$callback_url?>';
console.log(callback);
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
              url: '/qwtydd/check_location?x='+latitude+'&y='+longitude,
              type: 'get',
              dataType: 'json',
              success: function (res){
                  console.log(res.province);
                  console.log(res.city);
                  $('.pro').text(res.province);
                  $('.city').text(res.city);
                  var has = 0
                  //如果地址包含在数组中取得列表
                  for (var i = 3; i >= 0; i--) {
                    if(window.mcity[i]==res.city){
                      has = 1;
                      var c = window.classname[i];
                      $('.grid').hide();
                      $('.'+c).show();
                      $('.container').show();
                    }
                  };
                  //没有的话显示总店提示语
                  if (has==0) {
                    $('.none').show();
                    $('.grid').hide();
                    $('.container').hide();
                  }
                  $('.loading').hide();
                  $('.location').show();
                }
              })
          }
        });
});

wx.error(function (res) {
  alert(res.errMsg);
});
</script>
<script src="../qwt/ydd/picker.min.js" ></script>
<script type="text/javascript" src="../qwt/ydd/city.js"></script>
<script type="text/javascript">
//选择器js
    var nameEl1 = document.getElementById('picker1');
    var nameEl2 = document.getElementById('picker2');

    var first = []; /* 省，直辖市 */
    var second = []; /* 市 */
    // var third = []; /* 镇 */

    var selectedIndex = [0, 0, 0]; /* 默认选中的地区 */

    var checked = [0, 0, 0]; /* 已选选项 */

    function creatList(obj, list) {
        obj.forEach(function(item, index, arr) {
            var temp = new Object();
            temp.text = item.name;
            temp.value = index;
            list.push(temp);
        })
    }

    creatList(city, first);

    if (city[selectedIndex[0]].hasOwnProperty('sub')) {
        creatList(city[selectedIndex[0]].sub, second);
    } else {
        second = [{
            text: '',
            value: 0
        }];
    }

    if (city[selectedIndex[0]].sub[selectedIndex[1]].hasOwnProperty('sub')) {
        creatList(city[selectedIndex[0]].sub[selectedIndex[1]].sub, third);
    } else {
        third = [{
            text: '',
            value: 0
        }];
    }

    var picker = new Picker({
        data: [first, second],
        selectedIndex: selectedIndex,
        title: '地址选择'
    });

    picker.on('picker.select', function(selectedVal, selectedIndex) {
        var text1 = first[selectedIndex[0]].text;
        var text2 = second[selectedIndex[1]].text;
        $('.pro').text(text1);
        $('.city').text(text2);
        // nameEl.innerText = text1 + ' ' + text2;
    });

    picker.on('picker.change', function(index, selectedIndex) {
        if (index === 0) {
            firstChange();
        } else if (index === 1) {

        }

        function firstChange() {
            second = [];
            checked[0] = selectedIndex;
            var firstCity = city[selectedIndex];
            if (firstCity.hasOwnProperty('sub')) {
                creatList(firstCity.sub, second);

                var secondCity = city[selectedIndex].sub[0]
                if (secondCity.hasOwnProperty('sub')) {
                    creatList(secondCity.sub);
                } else {}
            } else {
                second = [{
                    text: '',
                    value: 0
                }];
                checked[1] = 0;
            }

            picker.refillColumn(1, second);
            picker.scrollColumn(1, 0)
        }

    });
//根据地址取列表
    picker.on('picker.valuechange', function(selectedVal, selectedIndex) {
        console.log(selectedVal);
        var c = window.local[selectedVal[0]][selectedVal[1]];
        $('.none').hide();
        $('.grid').hide();
        $('.'+c).show();
        $('.container').show();
        // if (selectedVal[0]==1) {
        //   $('#picker2').hide();
        // }else{
        //   $('#picker2').show();
        // }
    });

    nameEl1.addEventListener('click', function() {
        picker.show();
    });
    nameEl2.addEventListener('click', function() {
        picker.show();
    });
</script>
</html>
