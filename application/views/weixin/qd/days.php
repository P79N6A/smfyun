<!DOCTYPE html>
<html>
<head>
 <meta charset=utf-8>
 <meta name ="viewport" content ="initial-scale=1, maximum-scale=3, minimum-scale=1, user-scalable=no">
 <title>15天甜蜜任务大考验</title>
 <style>
  *{
   margin:0;
   padding:0;
  }
  body{
   color:#fff;
  }
  /* ceshi */
  .imgbg:after{
   content:"";
   display:block;
   padding-bottom: 54%;
  }
  .headbox{
   width:90%;
   height:60px;
   margin:0 auto;
   margin-top:-80px;
   border:1px solid transparent;
   position:relative;
  }
  .box1{
   width:172px;
   margin-top:18px;
  }
  .num{
   font-size: 50px;
   float:left;
   text-shadow: 1px 1px 1px #fff;
  }
  .text1{
   font-size: 15px;
   margin-left: 5px;
   /*margin-top:25px;*/
  }
  .text2{
   display: inline;
  }
  .shuxian{
   width:2px;
   height:45px;
   background-color:#fff;
   margin-left:2px;
   display: inline-block;
   vertical-align: text-bottom;
  }
  .boxl1{
   font-size: 12px;
   margin-left:10px;
   width:150px;
   height:40px;
   display: inline-block;
   position:absolute;
   line-height: 24px;
  }
  .box2{
   position:absolute;
   top: 0px;
   right:0px;
   width:68px;
   height:68px;
   border-radius: 34px;
   background-color: rgba(0,0,0,.4);
   line-height: 25px;
  }
  .img2{
   height:50px;
   width:40px;
   position:absolute;
   top:7px;
   left:22px;
  }
  .box3{
   float:left;
   display:inline-block;
   position:absolute;
   bottom: 0px;
   left:0px;
  }
  .text4{
   font-size: 13px;
   color:#fff;
  }
  .bk{
   height:20px;
   width:44px;
   border:1px solid #fff;
   border-radius: 10px;
   display: inline-block;
   text-align: center;
  }
  a{
   text-decoration:none;
  }
  .box4{
   position:absolute;
   bottom:0px;
   right:0px;
  }
  .img3{
   width:19px;
   height:17px;
   vertical-align: middle;
  }
  .canjia{
   width:100%;
   height: 33px;
   overflow: auto;
   margin-top: 4px;
  }
  .img4{
   margin:0 auto;
   height:25px;
   width: 55%;
   margin-left: 3%;
   overflow: hidden;
   float: left;
  }
  ul{
   list-style: none;
   display: flex;
   float: left;
  }
  li{
   background-color: #f1f1f1;
   margin-left: 1.5%;
   width:25px;
   height:25px;
   float:left;
   line-height: 25px;
   margin-left: 2px;
  }
  /*邀请卡设置*/
  .yaoqing{
   width:30%;
   height: 25px;
   line-height: 25px;
   border:1px solid #7d7d7d;
   border-radius:5px;
   text-align: center;
   float: right;
   margin-right: 5%;
   vertical-align: middle;
  }
  .yaoqing img{
   width:20px;
  }
  .yaoqing div{
   font-size: 13px;
   color:#7d7d7d;
   vertical-align: top;
   display: inline-block;
  }

  /*活动说明*/
  .text5{
   margin-top: 25px;
   margin-left:3%;
   font-size: 11px;
   color: #A4A4A4;
  }
  .fenge{
   width:100%;
   height:9px;
   background-color: #f1f1f1;
  }
  .shuoming{
   width:90%;
   /*height:105px;*/
   line-height: 16px;
   color:#A4A4A4;
   padding-left: 5%;
   margin-top: 10px;
   padding-bottom: 8px;
  }
  .text6{
   font-size: 12px;
   color:#888;
  }
  .rule{
   font-size: 11px;
  }
  #box{
   color:#888;
  }
  .text8{
   text-align: center;
   height:32px;
   line-height: 32px;
   background-color: #b6b6b6;
   color:#F1F1F1;
   margin-top: 20px;
  }
  .container{
   margin:0 auto;
   margin-top: 18px;
   width:90%;
  }

  /*列表更改*/
  .qiandao1{
   width:100%;
   height:50px;
   position: relative;
   overflow: auto;
  }
  .qiandao1::before{
   content:"";
   display: inline-block;
   height:100%;
   width:1%;
   vertical-align: middle;
  }
  .yuan{
   width:50px;
   height:50px;
   border-radius: 25px;
   float: left;
   display: inline-block;
   border:1px solid #A4A4A4;
   font-size: 13px;
   text-align: center;
   color: #A4A4A4;
   padding-top:16px;
   box-sizing: border-box;
  }
  .text9{
   display: inline-block;
   vertical-align: middle;
   width:44%;
   margin-left: 2%;
   color:#8E8D8D;
   font-size: 15px;
   line-height: 1.7;
  }
  .anniu{
   float: right;
   margin-top: 10px;
   display: inline-block;
   text-align: center;
   font-size: 13px;
   background-color: #FFC274;
   height:27px;
   line-height: 27px;
   width: 90px;
   border-radius: 13px;
  }
  .ydk{
   float: right;
   margin-top: 10px;
   display: inline-block;
   text-align: center;
   font-size: 13px;
   background-color: #FFC274;
   height:27px;
   line-height: 27px;
   width: 90px;
   border-radius: 13px;
  }
  .anniu2{
   /*background-color: #5ac500;*/
   font-size: 15px;
  }
  .anniu3, .anniu4{
   background-color: #e2e2e2;
  }
  .shuxian2{
   display: block;
   width:2px;
   height:33px;
   background-color:#A4A4A4;
   margin-left: 7.5%;
  }
  .dibu{
   width:100%;
   height:60px;
   line-height: 60px;
   text-align: center;
   color: rgb(208,208,208);
   background-color: #f1f1f1;
   font-size: 12px;
   margin-bottom: 30px;
  }
  .touxiang{
   width:25px;
  }
  .container span:last-of-type{
   display:none;
  }
  .gd{
   width:20px;
   float: right;
   margin-top: 8px;
  }
        .timedk{
         background-color: #5ac500;
   font-size: 15px;
        }
        .exit{
           width: 88%;
     height: 30px;
     line-height: 30px;
     background-color: #fd9e25;
     margin: 0 auto;
     color: #fff;
     text-align: center;
     border-radius: 5px;
     margin-bottom: 28px;
     font-size: 13px;
        }
        .start{
         width: 100%;
      height: 45px;
      line-height: 30px;
      background-color: #fd9e25;
      margin: 0 auto;
      color: #fff;
      text-align: center;
      border-radius: 5px;
      bottom: 4px;
      font-size: 13px;
      border-color: transparent;
      position: fixed;
        }
  }
</style>
<style type="text/css">
body {
    margin: 0px;
    background-color: #E7EAE1;
}
a{
 color: white;
 text-decoration:none;
}
a:link{
text-decoration:none;
}
a:visited{
text-decoration:none;
}
a:hover{
text-decoration:none;
}
a:active{
text-decoration:none;
}
.menu{
 margin-top: 20px;
}
.tab{
 width: 45%;
 display: inline-block;
 text-align: center;
 height: 50px;
 background: #78bcb7;
 line-height: 50px;
 font-size: 18px;
 border-radius: 10px;
}
.pic img{
 width: 80%;
}
.taba{
 /*float: left;*/
 margin-left: 10px;
}
.tabb{
 float: right;
 margin-right: 10px;
}
img{
   -webkit-user-select: none;
  pointer-events: none;
  -webkit-touch-callout: none;
}
.items{
  background-color: white;
  margin-top: 20px;
  width: 80%;
  margin-left: 10%;
  border: 1px solid #AFAFAF;
  border-radius: 5px;
  box-shadow: 5px 5px 22px #888888;
}
.items img{
 width: 100%;
}
.name{
  height: 40px;
  line-height: 40px;
  padding: 10px;
  font-size: 20px;
  color: #526B82;
}
.name span{
 float: right;
}
.condition{
 height: 30px;
 line-height: 30px;
 padding: 10px;
 font-size: 15px;
 border-top: 1px solid #9E9E9E;
 color: red;
}
</style>
</head>
<body>
 <script>
  function show(){
   var box = document.getElementById("box");
   var text = box.innerHTML;
   var newBox = document.createElement("div");
   var btn = document.createElement("a");
   btn.style.color="#FFC274";
   btn.style.float="right";
   newBox.innerHTML = text.substring(0,100);
   btn.innerHTML = text.length > 100 ? "...显示全部" : "";
   btn.href = "###";
   btn.onclick = function(){
    if (btn.innerHTML == "...显示全部"){
     btn.innerHTML = "收起";
     newBox.innerHTML = text;
    }else{
     btn.innerHTML = "...显示全部";
     newBox.innerHTML = text.substring(0,100);
    }
   }
   box.innerHTML = "";
   box.appendChild(newBox);
   box.appendChild(btn);
  }
  show();
 </script>
 <div style="margin-top:10px;text-align:center;color:black;">您现在拥有<?=$user->score?>积分</div>
<div class="menu">
        <a class="tab taba" href='../qd/prize'>兑换奖品</a>
        <a class="tab tabb" href='../qd/days'>任务完成详情</a>
    </div>

 <div class="text8">任务完成详情</div>


 <div class="container">

        <?php foreach ($days as $k => $v):?>
          <div class="qiandao1">
              <div class="yuan">
               <div class="text11">
                 <?=$k+1?>
               </div>
              </div>
              <div class="text9">
                  <?=date('Y-m-d H:i',$v->time)?>
              </div>
              <?php if($v->score>0):?>
                <div class="anniu anniu1">获得<?=$v->score?>积分</div>
              <?php else:?>
                <div class="anniu anniu1">已完成任务</div>
              <?php endif?>

          </div>

          <span class="shuxian2"></span>
      <?php endforeach?>
</div>
<div style="height:30px;"></div>
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.js"></script>
</body>
</html>













