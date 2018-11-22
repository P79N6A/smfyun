
  <style type="text/css">
    /*body
    {
      background-color: #172e39;
      color: #fff;
    }

    div.dialog
    {
      width: 500px;
      padding: 25px;
      margin: 4em auto;
      opacity: 0;
    }

    div.dialog .icon
    {
      text-align: center;
    }

    div.fun
    {
      margin-top: 500px;
    }

    h1
    {
      font-size: 22px;
      text-shadow: 0 2px 0 #000;
      margin-bottom: 5px;
    }

    p
    {
      font-size: 16px;
      color: #a2bbc8;
      margin-top: 10px;
      line-height: 22px;
    }

    a
    {
      color: #fff;
    }

    p.goback
    {
      margin-top: 40px;
      text-align: center;
    }
    p.goback a
    {
        color: #333;
        font-size: 14px;
        padding: 8px 15px;
        text-decoration: none;
        background: #fff;
        text-shadow: 0 1px 0 #fff;

        border-radius: 5px;
        background-image: -webkit-gradient(linear, center top, center bottom, from(white), to(#999));
        background-image: -webkit-linear-gradient(top, white, #999);
        background-image: -moz-linear-gradient(top, white, #999);
        background-image: -o-linear-gradient(top, white, #999);
        background-image: -ms-linear-gradient(top, white, #999);
        background-image: linear-gradient(to bottom, white, #999);
        -webkit-box-shadow: inset 0 -1px 0 white;
        -moz-box-shadow: inset 0 -1px 0 white;
        box-shadow: inset 0 -1px 0 white;
    }
    p.goback a:active
    {
      background-image: -webkit-gradient(linear, center top, center bottom, from(#999), to(#fff));
      background-image: -webkit-linear-gradient(top, #999, #fff);
      background-image: -moz-linear-gradient(top, #999, #fff);
      background-image: -o-linear-gradient(top, #999, #fff);
      background-image: -ms-linear-gradient(top, #999, #fff);
      background-image: linear-gradient(to bottom, #999, #fff);
    }*/
    body{
      background-color: #4bb1f4;
      margin: 0;
      padding: 0;
      text-align: center;
      position: fixed;
      width: 100%;
      height: 100%;
    }
    .container{
      width: 100%;
      height: 100%;
      max-width: 1200px;
      margin: auto;
      position: relative;
    }
    .img{
      position: relative;
    }
    .container img{
      max-width: 100%;
      max-height: 100%;
    }
    .mainpage{
      position: absolute;
      width: 9%;
      height: 5%;
      left: 42%;
      top: 70%;
    }
  </style>

  <!-- from https://www.rewardli.com/404 -->

  <!-- <div class="dialog">
    <div class="icon">
        <img src="/_img/error.png" />
    </div>
    <h1>非常抱歉，此路不通！您是怎么来到这里的？</h1>
    <p class="info">这里没有袜子，您走错地地方了。您访问的页面不存在，或者已经被删除，如果您有问题，<a href="/qa">请到这里</a> 反馈。</p>
    <p class="goback"><a href="/">&larr; 男人袜首页</a></p>

  </div> -->
  <div class="container">
    <!-- <div class="img"> -->
      <img src="http://yingyong.smfyun.com/qwt/images/404.jpg">
      <a class="mainpage" href="http://yingyong.smfyun.com"></a>
    <!-- </div> -->
  </div>

  <script src="../qwt/js/jquery.min.js"></script>
  <script src="/min/f=_js/404.js&amp;<?=VER?>"></script>
  <script type="text/javascript">
var h = $(window).height();
var w = $(window).width();
var r = h / w;
  console.log(h);
  console.log(w);
  console.log(r);
if (r > 0.6) { //移动端
  var cw = $('.container').width();
  var ch = cw/1.67;
  console.log(cw);
  console.log(ch);
    $('.container').css({
        "max-height": ch
    });
} else { //pc端
    $('.bg').css({
        "max-width": '719px'
    });
}
  </script>

  <!--[if lt IE 7.]>
  <script defer type="text/javascript" src="/_js/pngfix.js"></script>
  <![endif]-->
