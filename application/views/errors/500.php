<style stype="text/css">
h1 {
    font-size: 2em;
    margin:.5em;
}

p {
    margin: 1em;
}
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
</style>

<!-- <h1>Error!</h1>
<p>HTTP 1.0 500 Server Error!</p> -->


  <div class="container">
    <!-- <div class="img"> -->
      <img src="http://yingyong.smfyun.com/qwt/images/500.jpg">
      <a class="mainpage" href="http://yingyong.smfyun.com"></a>
    <!-- </div> -->
  </div>
  <script src="../qwt/js/jquery.min.js"></script>
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
