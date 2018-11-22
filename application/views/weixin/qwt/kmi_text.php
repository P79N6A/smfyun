
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0">
 <title>卡密详情</title>
 <style type="text/css">
 *{
  margin: 0;
  padding: 0;
 }
 body{
  background-color: #fff;
  font-size: 15px;
  color: #666;
  text-shadow: 1px 1px 5px #e5e5e5;
 }
 .container{
  max-width: 330px;
  margin: 30px auto 0;
 }
 .title{
  padding: 10px 20px;
 }
 .line{
  padding: 5px 10px;
 }
 .line label{
  width: 20%;
  text-align: right;
  /*display: inline-block;*/
 }
 .line input{
  color: #999;
  font-size: 14px;
  padding: 5px 10px;
  border-radius: 5px 0 0 5px;
  border: 1px solid #dedede;
  border-right-width: 0;
  width:
 }
 .line button{
  color: #fff;
  background-color: #17d0ef;
  font-size: 14px;
  padding: 5px 10px;
  border-radius: 0 5px 5px 0;
  border: 1px solid #00a6c1;
  font-weight: bold;
 }
 .line small{
  width: 100%;
  display: inline-block;
  padding: 5px 20px;
  color: #ff195d;
 }
 .hint{
  padding: 10px 20px;
  text-align: right;
 }
 footer{
    position: absolute;
    bottom: 10px;
    width: 100%;
    text-align: center;
    color: #999;
    font-size: 12px;
 }
 </style>
</head>
<body>

<section>
 <div class="container">
  <!-- <div class="title"><p>亲,您的：</p></div> -->
  <?php if($password1):?>
  <div class="line kh">
   <label><?=$result[0]?></label>
   <div style="margin-top:10px;">
    <input type="text" value="<?=$password1?>" id="khv" readonly><button id="khb" type="button" data-clipboard-action="copy" data-clipboard-target="#khv">复制</button>
    </div>
    <small id="khh" style="opacity:0;">复制成功！</small>
  </div>
 <?php endif?>
  <?php if($password2):?>
  <div class="line km">
   <label><?=$result[1]?></label>
   <div style="margin-top:10px;">
    <input type="text" value="<?=$password2?>" id="kmv" readonly><button id="kmb" type="button" data-clipboard-action="copy" data-clipboard-target="#kmv">复制</button>
    </div>
    <small id="kmh" style="opacity:0;">复制成功！</small>
  </div>
 <?php endif?>
  <?php if($password3):?>
  <div class="line sc">
   <label><?=$result[2]?></label>
   <div style="margin-top:10px;">
    <input type="text" value="<?=$password3?>" id="scr" readonly><button id="scb" type="button" data-clipboard-action="copy" data-clipboard-target="#scr">复制</button>
    </div>
    <small id="sch" style="opacity:0;">复制成功！</small>
  </div>
 <?php endif?>
  <label><?=$result[3]?></label>
 </div>
 <footer>自动发卡工具 @ 神码浮云</footer>
</section>
<!-- <script type="text/javascript" src="js/clipboard.min.js"></script> -->
  <script src="http://jfb.dev.smfyun.com/qwt/clipboard/clipboard.min.js"></script>

<!-- 3. Instantiate clipboard -->
<script type="text/javascript">
var clipboard1 = new Clipboard('#khb');
var clipboard2 = new Clipboard('#kmb');
var clipboard3 = new Clipboard('#scb');
var hint = document.getElementsByTagName('small');
var hint1 = document.getElementById('khh');
var hint2 = document.getElementById('kmh');
var hint3 = document.getElementById('sch');

clipboard1.on('success', function(e) {
 for (var i = hint.length - 1; i >= 0; i--) {
  hint[i].style.opacity = 0;
 };
 hint1.style.opacity = 1;
 console.log(e);
});

clipboard1.on('error', function(e) {
 console.log(e);
});
var clipboard2 = new Clipboard('#kmb');

clipboard2.on('success', function(e) {
 for (var i = hint.length - 1; i >= 0; i--) {
  hint[i].style.opacity = 0;
 };
 hint2.style.opacity = 1;
 console.log(e);
});

clipboard2.on('error', function(e) {
 console.log(e);
});
var clipboard3 = new Clipboard('#scb');

clipboard3.on('success', function(e) {
 for (var i = hint.length - 1; i >= 0; i--) {
  hint[i].style.opacity = 0;
 };
 hint3.style.opacity = 1;
 console.log(e);
});

clipboard3.on('error', function(e) {
 console.log(e);
});
</script>

</body>
</html>


