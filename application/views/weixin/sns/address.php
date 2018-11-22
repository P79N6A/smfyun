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
<style type="text/css">
body{
 margin: 0px;
 padding: 0px;
 background: url(/sns/img/address.jpg) top no-repeat;
 background-position: 0px -60px;
 height: 100%;
 width: 100%;
 background-size: cover;
 font-family:'SimHei';
}
.logo{
  width: 12%;
  margin-left: 2.8%;
  display: inline-block;
}
.liucheng{
  width: 90%;
  margin-left: 5%;
  margin-top: 111%;
}
.yuyue{
    width: 40%;
    margin-left: 30%;
    top: 95%;
    position: absolute;

}
.bobao{
  display: inline-block;
}
.name{
  margin-left: 5px;
}
.name,.gift{
  color:#EFE96E;
}
.text{
  color: white;
}
.laba{
  height: 10px;
}
.bobao{
    display: flex;
    position: fixed;
    top: 0px;
    left: 16%;
    font-size: 10px;
    background: rgba(0, 0, 0, 0.27);
    width: 61%;
    padding: 10px;
    text-align: center;
}
.rule{
      color: white;
    position: fixed;
    top: 15px;
    right: 5px;
    font-size: 13px;
    border-bottom: 1px solid white;
}
.confirm{
  display: inline-block;
  width: 80%;
  margin-left: 10%;
  background: -webkit-gradient(linear, 0 0, 100% 100%, from(#E95DFA), to(#5BCBEE));
  text-align: center;
  color: white;
  font-size: 20px;
  position: relative;
  line-height: 40px;
}
.rules{
  width: 100%;
  position: absolute;
  top: 0px;
  background: rgba(0, 0, 0, 0.85);
  display: none;
}
.form{
  width: 80%;
  margin-left: 10%;
  margin-top: 45%;
  color: white;
}
.formdiv{
 width: 80%;
 margin:35px 10%;
 line-height: 25px;
 height: 25px;
 color: #bdbaba;
}
.formdiv >label{
  display: inline-block;
  width: 30%;
  text-align: right;
}
.formdiv >input{
  width: 60%
}
input{
  height: 25px;
  border:0px;
  padding:0px;
  margin-left: 3%;
  padding: 3px;
  padding-left: 10px;
  background: rgba(255, 255, 255, 0);
  border:1px solid #bdbaba;
  color: white;
}
.tips{
    width: 80%;
    margin-left: 10%;
    margin-top: 10px;
}

.address{
  width: 70%;
  margin-left: 15%;
  margin-top: 30%;
}
.close{
    display: inline-block;
    width: 40px;
    height: 40px;
    position: absolute;
    top: 20%;
    left: 78%;
}
</style>
<body>
<img src="/sns/img/logo.png" class="logo">
<div class='form'>
  <div class='formdiv'><label><span class='fright'>收货人</span></label><input placeholder='姓名' class='inname' type="text"></div>
  <div class='formdiv'><label><span class='fright'>联系方式</span></label><input placeholder='手机或电话号码' class='inname' type="tel"></div>
  <div class='formdiv'><label><span class='fright'>省</span></label><input placeholder='省份区域' class='inname'></div>
  <div class='formdiv'><label><span class='fright'>详细地址</span></label><input placeholder='街道地址' class='inname'></div>
  <input type="hidden" class='inname' value='<?=$goodid?>'>
  <span class='confirm'>确认></span>
  <img src="/sns/img/tips.png" class='tips'>
</div>
<div class="rules">
  <img src="/sns/img/address.png" class="address">
  <span class='close'></span>
</div>
</body>
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
  var h1 = $(window).height();
  var h2 = $('body').height();
  var H = Math.max(h1,h2);
  $('.rules').height(H);
});
$('.confirm').click(function() {
  if($('.inname').eq(0).val()&&$('.inname').eq(1).val()&&$('.inname').eq(2).val()&&$('.inname').eq(3).val()){

    var data = {
      name:$('.inname').eq(0).val(),
      tel:$('.inname').eq(1).val(),
      pro:$('.inname').eq(2).val(),
      address:$('.inname').eq(3).val(),
      goodid:$('.inname').eq(4).val()
    };
    console.log(data);
    $.ajax({
      url: '/sns/address',
      type: 'post',
      dataType: 'text',
      data: {data: data},
    })
    .done(function(res) {
      if(res=='success'){
        $('.rules').fadeIn(500);
      }
    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete");
    });

  }else{
    alert('请填写完整!');
  }
});
$('.rules').click(function() {
  $('.rules').fadeOut(500);
  window.location.replace('/sns/join');
});
$('.close').click(function() {
  $('.rules').fadeOut(500);
});
</script>
</html>
