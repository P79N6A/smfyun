<!DOCTYPE html>
<html>
<head>
<meta charset='utf8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
 <title>华硕品质·坚若磐石</title>
<style type="text/css">
body{
 margin: 0px;
 padding: 0px;
 background: url(/asus/bg.jpg) top no-repeat;
 height: 100%;
 width: 100%;
 background-size: cover;
 font-family:'SimHei';
}
.city,.pro{
  width: 60%;
  margin-left: 20%;
  display: block;
  height: 40px;
  text-align: center;
  color: white;
  background:rgba(147, 147, 150, 0.42);
  line-height: 40px;
  /*margin-top: 30px;*/
  font-size: 15px;
  border-radius: 10px;
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
  color: white;
  background:rgba(147, 147, 150, 0.42);
  line-height: 40px;
  margin-top: 30px;
  font-size: 15px;
  border-radius: 10px;
}
.wheel-hook{
  text-align: center;
}
ul{
  padding-left: 0px;
}
.location{
      color: white;
    font-size: 20px;
    width: 100%;
    text-align: center;
    margin-top: 80%;
}
#triangle-down1 {
    width: 0;
    height: 0;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-top: 15px solid #EAE7E7;
    display: inline-flex;
    position: relative;
    left: 70%;
    top: 30px;
}
#triangle-down2 {
    width: 0;
    height: 0;
    border-left: 10px solid transparent;
    border-right: 10px solid transparent;
    border-top: 15px solid #EAE7E7;
    display: inline-flex;
    position: relative;
    left: 70%;
    top: 30px;
}
</style>
</head>
<body>
<div class='location'>您当前定位城市：<span class='you'></span></div>
<div id='picker5'>
  <div id='triangle-down1'></div>
  <div class='pro'>上海市</div>
  <div id='triangle-down2'></div>
  <div class='city'>上海市</div>
</div>
<span class='confirmbtn'>确认</span>
</body>
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript">
  $('.confirmbtn').click(function() {
    window.location.href = window.localhref;
  });
</script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
window.local = new Array();
for (var i = 0; i <=9 ; i++) {
  window.local[i] = new Array();
};
window.local[0][0] ='https://h5.youzan.com/v2/category/huu54z9z';
window.local[1][0] ='https://h5.youzan.com/v2/category/ltuw333v';
window.local[1][1] ='https://h5.youzan.com/v2/category/dkk5lvpb';
window.local[1][2] ='https://h5.youzan.com/v2/category/k2scajtc';
window.local[1][3] ='https://h5.youzan.com/v2/category/c1nrjgqf';
window.local[1][4] ='https://h5.youzan.com/v2/category/ox80wynk';
window.local[1][5] ='https://h5.youzan.com/v2/category/1fajxv6ba';
window.local[1][6] ='https://h5.youzan.com/v2/category/13fkly2ak';
window.local[1][7] ='https://h5.youzan.com/v2/category/4h0yuhtw';
window.local[1][8] ='https://h5.youzan.com/v2/category/1dtsbz9qx';
window.local[1][9] ='https://h5.youzan.com/v2/category/dzp2nozu';
window.local[1][10] ='https://h5.youzan.com/v2/category/zvz4t8nj';

window.mcity = new Array();
window.mcity[0] = '上海市';
window.mcity[1] = '杭州市';
window.mcity[2] = '宁波市';
window.mcity[3] = '温州市';
window.mcity[4] = '嘉兴市';
window.mcity[5] = '湖州市';
window.mcity[6] = '绍兴市';
window.mcity[7] = '金华市';
window.mcity[8] = '衢州市';
window.mcity[9] = '舟山市';
window.mcity[10] = '台州市';
window.mcity[11] = '丽水市';
window.localhref = window.local[0][0];
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
              url: '/asus/asus?x='+latitude+'&y='+longitude,
              type: 'get',
              dataType: 'json',
              success: function (res){
                  console.log(res.pro);
                  console.log(res.city);
                  $('.you').text(res.city);
                  for (var i = 0; i<=11; i++) {
                    if(window.mcity[i]==res.city){
                      var isset = 1;
                      $('.city').text(window.mcity[i]);
                      if(i==0){
                        $('.pro').text('上海市');
                        window.localhref = window.local[0][0];
                      }else{
                        $('.pro').text('浙江省');
                        window.localhref = window.local[1][i-1];
                      }
                      break;
                    }
                  };
                  if(!isset&&i>=11){
                    window.localhref = window.local[0][0];
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
<script src="/asus/picker.min.js" ></script>
<script type="text/javascript" src="/asus/city.js"></script>
<script type="text/javascript">
    var nameEl = document.getElementById('picker5');

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

    picker.on('picker.valuechange', function(selectedVal, selectedIndex) {
        console.log(selectedVal);
        window.localhref = window.local[selectedVal[0]][selectedVal[1]];
    });

    nameEl.addEventListener('click', function() {
        picker.show();
    });
</script>
</html>
