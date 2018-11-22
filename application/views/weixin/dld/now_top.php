<!DOCTYPE html>
<!-- saved from url=(0044)http://m.yizhibo.com/l/R2ZWUMTk2FgOC02Z.html -->
<html lang="ch" data-dpr="1" style="font-size: 12px;">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width,maximum-scale=1.0,user-scalable=no">
  <link href="http://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
    <link rel="stylesheet" href="/wdy/bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="/wdy/plugins/datetimepicker/js/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="/wdy/plugins/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js"></script>
    <link rel="stylesheet" type="text/css" href="/wdy/plugins/datetimepicker/css/bootstrap-datetimepicker.min.css" />
  <title>销售排行榜</title>
  <style type="text/css">
  body{
    margin: 0;
    font-family: "Bentonsans";
    src: "BentonSansCond-Bold_0.otf";
    /*text-shadow: 1px 1px 1px #efefef;*/
    cursor: pointer;
  }
  .nodata{
    text-align: center;
    margin-top: 50px;
    color: #969cb2;
  }
  .rankhead{
    width: 100%;
    height: 70px;
    background-color: #00b2da;
    /*border-top-left-radius: 10px;*/
    /*border-top-right-radius: 10px;*/
  }
  .headcontent{
    display: inline-block;
    color: #fff;
    margin-top: 22px;
    margin-bottom: 22px;
    width: 100%;
    height: 26px;
    position: relative;
    font-family: "Arial","Microsoft YaHei","黑体","宋体",sans-serif;
  }
  .headcontent img{
    height: 100%;
    float: left;
    display: inline-block;
    margin-left: 25px;
  }
  .ranktitle{
    display: inline-block;
    margin-left: 21px;
  }
  .rankdate{
    line-height: 26px;
    right: 21px;
    display: inline-block;
    position: absolute;
    float: right;
    font-family: "BMWTypeLight16by9";
  }
  .titlename{
    font-weight: bold;
    font-size: 20px;
  }
  .ranksubtitle{
    font-size: 10px;
    color: #fff;
    opacity: .9;
    line-height: 10px;
    height: 10px;
    vertical-align: bottom;
  }
  .rank{
    display: inline-block;
    height: 70px;
    width: 100%;
    border-bottom: 1px solid #dfdfdf;
  }
  .rankcontent{
    display: inline-block;
    position: relative;
    margin: 8px 0 8px 0;
    height: 54px;
    width: 100%;
    font-family: "Arial","Microsoft YaHei","黑体","宋体",sans-serif;
  }
  .avator{
    height: 54px;
    margin-left: 16px;
    /*border: 4px solid #f5f5f5;*/
    display: inline-block;
    position: relative;
    float: left;
    /*border-radius: 50%;*/
  }
  .avator img{
    height: 46px;
    display: inline-block;
    border: 4px solid #f5f5f5;
    border-radius: 50%;
  }
  .ranknum{
    display: inline-block;
    position: absolute;
    left: 50%;
    margin-left: -12px;
    bottom: 0;
    width: 24px;
    height: 14px;
    border-radius: 7px;
    background-color: #00b2da;
    color: #eaeaea;
    line-height: 14px;
    font-size: 10px;
    text-align: center;
    /*font-family: monospace;*/
  }
  .desc{
    display: inline-block;
    margin-left: 14px;
    height: 100%;
    position: relative;
    /*min-width: 100px;*/
  }
  .name{
    margin-top: 20px;
    font-size: 16px;
    line-height: 16px;
    color: #969cb2;
  }
  .we{
    color: #bcbcbc;
    font-size: 10px;
    line-height: 12px;
    position: absolute;
    bottom: 7px;
  }
  .point{
    position: absolute;
    right: 21px;
    top: 7px;
  }
  .pointdesc{
    color: #d6f1bb;
  }
  .pointnum{
    font-family: "Roboto Condensed Bold";
    font-size: 18px;
    color: #00b2da;
    margin-top: 12px;
    text-align: center;
  }
  .days{
    position: absolute;
    right: 100px;
    top: 10px;
  }
  .daysdesc{
    color: #bcbcbc;
  }
  .daysnum{
    color: #9f9f9f;
    text-align: center;
    margin-top: 5px;
  }
  .top1{
    background-color: #fcfcfc;
  }
  .logo{
    position: absolute;
    height: 60px;
    top: 5px;
    right: 5px;
    opacity: .4;
  }
  .rule{
    display: inline-block;
    position: absolute;
    left: 25px;
    top: 50px;
    font-size: 13px;
  }
  .rule a{
    color: #fff;
    text-decoration: none;
  }

  .tab-bar{
    display: block;
    position: fixed;
    bottom: 0;
    background-color: #fff;
    z-index: 10000;
    height: 60px;
    border: 1px solid #e9e9e9;
  }
  .menubox{
    display: inline-block;
    width: 33.3%;
    color: #999999;
    height: 100%;
    text-align: center;
  }
  .tab-bar div img{
    max-height: 30px;
    margin-top: 10px;
  }
  .menu{
    width: 100%;
  }
  .menu-active{
    color: #da0000;
  }
  .topbox{
    width: 100%;
    /*overflow: scroll;*/
  }
  .formdatetime1{
    text-align: center;
    height: 27px;
    margin-top: 3px;
    margin-left: 20px;
    border-radius: 5px;
    width: 35%;
    border: 2px solid #e9e9e9;
    max-width: 200px;
  }
  .formdatetime2{
    text-align: center;
    height: 27px;
    width: 35%;
    margin-top: 3px;
    border-radius: 5px;
    border: 2px solid #e9e9e9;
    max-width: 200px;
  }
  .search-btn{
    width: 15%;
    height: 27px;
    color: #666666;
    margin-top: 3px;
    margin-left: 10px;
    border-radius: 5px;
    border: 2px solid #e9e9e9;
    background-color: #fff;
    max-width: 100px;
  }
  </style>
</head>

<body>
  <div class="rankhead">
    <div class="headcontent">
      <img src="../../dld/img/jiangbei.png">
      <div class="ranktitle">
        <div class="titlename">销售排行榜</div>
      </div>
    </div>
  </div>
  <div class="topbox">
<?php if($user[0]):?>
  <!-- <div class="rankbody">
    <div class="rank top1" style="height:40px;border-bottom:0">
      <div class="rankcontent" style="height:24px;">
        <div class="desc" style="margin-left:74px;">
          <div class="name" style="margin-top:0;line-height:24px;">起始时间</div>
        </div>
        <div class="desc" style="margin-left:25%;">
          <div class="name" style="margin-top:0;line-height:24px;">终止时间</div>
        </div>
      </div>
    </div>
  </div> -->
  <div class="rankbody">
    <div class="rank top1" style="height:50px;">
      <div class="rankcontent" style="height:34px;">
      <form method="post">
        <input class="formdatetime1" name="start" value="<?=$_POST['start']?>" placeholder="起始时间" readonly="">
        <span style="
    color: #e9e9e9;">-</span>
        <input class="formdatetime2" name="end" placeholder="终止时间" value="<?=$_POST['end']?>" readonly="">
        <button type="submit" class="search-btn"><i class="fa fa-search"></i></button>
      </div>
      </form>
    </div>
  </div>
  <div class="rankbody">
    <div class="rank top1" style="height:40px;">
      <div class="rankcontent" style="height:24px;">
        <div class="desc" style="margin-left:74px;">
          <div class="name" style="margin-top:0;line-height:24px;">昵称</div>
        </div>
        <div class="point" style="top:0">
          <div class="pointnum" style="margin-top:0;line-height:24px;">销量(单位:元)</div>
        </div>
      </div>
    </div>
  </div>
<?php foreach ($user as $k => $v):?>
  <div class="rankbody">
    <div class="rank"?>
      <div class="rankcontent">
        <div class="avator">
          <img src="<?=$v['headimgurl']?>">
          <div class="ranknum"><?=$k+1?></div>
        </div>
        <div class="desc">
          <div class="name"><?=$v['nickname']?></div>
        </div>
        <div class="point">
          <div class="pointnum"><?=$v['payment']?$v['payment']:'0.00'?></div>
        </div>
      </div>
    </div>
  </div>
<?php endforeach?>
<?php else:?>
    <div class="nodata">
    暂无数据
    </div>
  <?php endif?>
  </div>
<script type="text/javascript">

$(function () {
  $(".formdatetime1").datetimepicker({
    format: "yyyy-mm-dd",
    language: "zh-CN",
    autoclose: true,
    minView:'month',
    todayBtn:true,
    // startDate: "<?=$result['begin']?>",
    // endDate: "<?=$result['over']?>",

  });

  $(".formdatetime2").datetimepicker({
    format: "yyyy-mm-dd",
    language: "zh-CN",
    autoclose: true,
    minView:'month',
    todayBtn:true,
    // startDate: "<?=$result['begin']?>",
    // endDate: "<?=$result['over']?>",
  });


});
  $(document).ready(function(){
    var e =$(window).outerHeight();
    var f =$('.rankhead').outerHeight();
    var g =$('.tab-bar').outerHeight();
    var h =(e-f-g)/2;
    $('.rankbox').css('height',h);
    var d =$('body').outerWidth();
    $('.tab-bar').css('width',d-2+'px');
    var a =$('.rankbox').outerHeight();
    console.log(a);
    var b =$('.rankboxhead').outerHeight();
    console.log(b);
    var c = a-b-1;
    $('.rankboxbody').css('height',c+'px');
    $('.topbox').css('height',e-f-g);
//限制字符个数
$('.shopname').each(function(){
var maxwidth=6;
if($(this).text().length>maxwidth){
$(this).text($(this).text().substring(0,maxwidth-1));
$(this).html($(this).html()+'…');
}
});
  })
</script>
</body>

</html>
