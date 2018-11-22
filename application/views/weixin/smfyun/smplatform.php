<!DOCTYPE html>
<!-- saved from url=(0044)http://m.yizhibo.com/l/R2ZWUMTk2FgOC02Z.html -->
<html lang="ch" data-dpr="1" style="font-size: 12px;">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width,maximum-scale=1.0,user-scalable=no">
  <title>排行榜统计规则</title>
  <style type="text/css">
  body{
    margin: 0;
    cursor: pointer;
  }
  .rankhead{
    width: 100%;
    height: 55px;
    background-color: #da0000;
    /*border-top-left-radius: 10px;*/
    /*border-top-right-radius: 10px;*/
  }
  .headcontent{
    display: inline-block;
    color: #fff;
    margin-top: 15px;
    width: 100%;
    height: 26px;
    position: relative;
    font-family: "Arial","Microsoft YaHei","黑体","宋体",sans-serif;
  }
  .logo{
    position: absolute;
    height: 45px;
    top: 5px;
    right: 5px;
    opacity: .4;
  }
  .rankbox{
    width: 50%;
    height: 180px;
    border-left: 1px solid #dfdfdf;
    border-bottom: 1px solid #dfdfdf;
    margin-left: -1px;
    display: inline-block;
  }
  .headtitle{
    margin-left: 25px;
    font-size: 18px;
    font-weight: bold;
    line-height: 26px;
  }
  .rankboxhead{
    width: 100%;
    padding-top: 15px;
    padding-bottom: 15px;
    font-size: 13px;
    position: relative;
    display: inline-block;
  }
  .rankboxtitle{
    display: inline-block;
    padding-left: 15px;
    color: #666666;
    font-weight: bold;
  }
  .rankboxmore{
    display: inline-block;
    position: absolute;
    top: 15px;
    right: 15px;
    font-weight: bold;
    color: orange;
  }
  .rankboxbody{
    width: 100%;
    display: inline-block;
    height: 137px;
    overflow: scroll;
  }
  .rank1{
    width: 100%;
    padding: 2px 0;
    position: relative;
    height: 22px;
  }
  .avator{
    display: inline-block;
    /* float: left; */
    margin-left: 2px;
    position: absolute;
    left: 30px;
  }
  .avator img{
    height: 20px;
    border-radius: 50%;
    border: 1px solid #f5f5f5;
  }
  .shopname{
    /*display: inline-block;*/
    color: #969cb2;
    height: 100%;
    line-height: 22px;
    left: 60px;
    position: absolute;

  }
  .smfyunnum{
    display: inline-block;
    color: #b8e986;
    height: 100%;
    float: right;
    line-height: 22px;
    margin-right: 15px;
    font-size: 13px;
    font-weight: bold;
  }
  .top{
    display: inline-block;
    float: left;
    height: 24px;
    vertical-align: middle;
    margin-left: 10px;
    line-height: 22px;
    color: #cccccc;
    min-width: 18px;
    text-align: center;
  }
  .top img{
    height: 18px;
    margin-top: 2px;
  }
  .footbox{
    /*border-bottom: 1px solid #dfdfdf;*/
    height: 110px;
    width: 100%;
    display: inline-block;
  }
  .footbox2{
    /*border-bottom: 1px solid #dfdfdf;*/
    height: 80px;
    width: 100%;
    display: inline-block;
  }
  .bodyhead{
    height: 35px;
    width: 100%;
    background-color: #da0000;
    position: relative;
  }
  .bodytitle{
    padding: 5px 25px;
    color: #fff;
    height: 25px;
    line-height: 25px;
    font-size: 14px;
    font-weight: bold;
  }
  .footcontent{
    margin: 15px 0;
    padding: 0 15px;
    /*height: 80px;*/
    position: relative;
    overflow: scroll;
  }

  .smfyun{
    width: 200px;
    /* margin-left: 50%; */
    position: absolute;
    left: 50%;
    margin-left: -100px;
  }
  .smfyun img{
    width: 100%;
  }
  .desc{
    font-size: 13px;
    color: #666;
    line-height: 26px;
  }
  .newsbox{
    height: 16px;
    padding: 2px 0;
    position: relative;
  }
  .newstitle{
    display: inline-block;
    float: left;
    line-height: 16px;
    color: #bcbcbc;
  }
  .newscontent{
    display: inline-block;
    line-height: 16px;
    position: absolute;
    left: 20%;
    color: #9f9f9f;
    width: 80%;
    overflow: scroll;
  }
  .nodata{
    text-align: center;
    margin-top: 50px;
    color: #969cb2;
  }
  .rankmonth{
    margin-top: -3px;
    border-bottom: 0;
  }
  .ranksum{
    margin-top: -3px;
    border-bottom: 0;
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
    height: 90px;
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
  .copyright{    color: #999999;
    font-size: 14px;
    text-align: center;
    padding: 5px 0;
    font-size: 12px;
    position: fixed;
    z-index: 10001;
    text-align: center;
    background-color: #eaeaea;
    width: 100%;
    box-shadow: -1px -1px 1px;
    bottom: 0;}
  </style>
</head>

<body>
<div class="copyright">Copyright © 2015-2017 神码浮云</div>
<div class="smplatformbox">
<div class="rankhead">
  <div class="headcontent">
    <div class="headtitle">
      排行榜统计规则
    </div>
  </div>
  <img class="logo" src="../../smfyun_top/img/baimafuyun.png">
</div>
<div class="tab-bar">
  <a href="/smfyun/all_top"><div class="menubox" data-value="all_top"><img class="iconon" src="../../smfyun_top/img/top-on.png" style="display:inline-block;"><img class="icon" src="../../smfyun_top/img/top.png" style="display:none;"><div class="menu menu-active">风云榜</div></div></a><a href="http://mp.weixin.qq.com/mp/homepage?__biz=MjM5NDQ5MjUyMA==&hid=1&sn=b7b906273b837afc2361e0f5b9a9d9ca#wechat_redirect"><div class="menubox" data-value="paper"><img class="iconon" src="../../smfyun_top/img/paper-on.png" style="display:none;"><img class="icon" src="../../smfyun_top/img/paper.png" style="display:inline-block;"><div class="menu">有赞小报</div></div></a><a href="http://yingyong.smfyun.com/"><div class="menubox"><img class="iconon" src="../../smfyun_top/img/app-on.png" style="display:none;"><img class="icon" src="../../smfyun_top/img/app.png" style="display:inline-block;"><div class="menu">营销应用</div></div></a>
</div>
<div class="footbox2">
  <div class="footcontent">
  <div class="desc">1、数据来源有赞后台“他们做得不错”榜单，始于2017.1.1；<br>
2、上榜1天神码指数+1；<br>
3、连续上榜2天神码指数额外+0.5；<br>
4、连续上榜3天神码指数额外+0.8；<br>
5、榜单数据每日9点更新。
</div>
    </div>
  </div>
</div>
<script src="http://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript">
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
