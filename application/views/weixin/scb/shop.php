<?php
function convert($a){
  switch ($a) {
    case 2:
      echo '实物奖品';
      break;
    case 1:
      echo '微信卡券';
      break;
    case 0:
      echo '虚拟奖品';
      break;
    case 3:
      echo '话费流量';
      break;
    case 4:
      echo '微信红包';
      break;
    case 5:
      echo '有赞赠品';
      break;
    default:
      # code...
      break;
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<title>积分商城</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="stylesheet" type="text/css" href="css/didi.css">
<link rel="stylesheet" type="text/css" href="css/swiper.css">
<link rel="stylesheet" type="text/css" href="css/index.css">
<script type="text/javascript" src="js/didi.js"></script>
<script type="text/javascript" src="js/swiper.js"></script>
<script type="text/javascript" src="js/index.js"></script>
<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="js/jquery.event.drag-1.5.min.js"></script>
<script type="text/javascript" src="js/jquery.touchSlider.js"></script>
<script type="text/javascript">
$(document).ready(function(){
 $dragBln = false;

 $(".main_image").touchSlider({
  flexible : true,
  speed : 200,
  btn_prev : $("#btn_prev"),
  btn_next : $("#btn_next"),
  paging : $(".flicking_con a"),
  counter : function (e){
   $(".flicking_con a").removeClass("on").eq(e.current-1).addClass("on");
  }
 });

 $(".main_image").bind("mousedown", function() {
  $dragBln = false;
 });

 $(".main_image").bind("dragstart", function() {
  $dragBln = true;
 });

 $(".main_image a").click(function(){
  if($dragBln) {
   return false;
  }
 });

 $(".main_visual").hover(function(){
  clearInterval(timer);
 },function(){
  timer = setInterval(function(){
   $("#btn_next").click();
  },5000);
 });

});
</script>
<style>
a:visited{text-decoration:none;color:#666;}
a:link{text-decoration:none;color:#666;}

 body{margin:0;padding: 0;background: #f1f1f1}
}
.main_visual{height:100px;}
.main_image{height:100px;overflow:hidden;position:relative;}
.main_image ul{height:100px;overflow:hidden;top:0;left:0}
.main_image li{float:left;width:100%;height:100px;}
.main_image li span{display:block;width:100%;height:100px}
.main_image li a{display:block;width:100%;height:180px}
.main_image li .img_1
{
 background:url('http://<?=$_SERVER["HTTP_HOST"]?>/scb/banimages/ban1/<?=$bid?>');
  background-size: 100% 100%;
 }
.main_image li .img_2{background:url('http://<?=$_SERVER["HTTP_HOST"]?>/scb/banimages/ban2/<?=$bid?>');
 background-size: 100% 100%;}
.main_image li .img_3{background:url('http://<?=$_SERVER["HTTP_HOST"]?>/scb/banimages/ban3/<?=$bid?>') ;
background-size: 100% 100%;}
.main_image li .img_4{background:url('http://<?=$_SERVER["HTTP_HOST"]?>/scb/banimages/ban4/<?=$bid?>');
 background-size: 100% 100%;}
div.flicking_con{position:absolute;top:80px;left:50%;z-index:999;width:200px;height:21px;margin:0 0 0 -50px;}
div.flicking_con a{float:left;width:21px;height:21px;margin:0;padding:0;background:url('images/btn_main_img.png') 0 0 no-repeat;display:block;text-indent:-1000px}

.fenlitiao
{
 width: 100%;
 height: 10px;
}

</style>
</head>
<body>
    <div class="view js-page-view">
     <div id="banner" class="swiper-container with-img-holder swiper-container-horizontal"> <div class="main_visual">
 <div class="flicking_con">
<?php for($i=1;$i<=$num;$i++):?>
  <a <?= $i==1?"class='on'":''?>  href="#"></a>
<?php endfor?>
 </div>
 <div class="main_image">
  <ul style="width: 320px; overflow: visible;">
  <?php for ($i=1; $i<=4; $i++) {
     if(ORM::factory('scb_cfg')->where('bid', '=', $bid)->where('key', '=', 'ban'.$i)->find()->pic){?>
     <li style="float: none; display: block; position: absolute; top: 0px; left: 0px; width: 320px;"><a href="<?=$config['banurl'.$i]?>"><span class="img_<?=$i?>"></span></a></li>
     <?php }
  }?>
  </ul>

 </div>
</div>

 </div>
 <section id="top-menu">
  <div class="menu-item js-click" data-link="/imall/count.htm" data-bind="manually">
   <i class="ic-my-db"></i>
   <a href='/scb/score/1'><span>积分<em><?=$user->score?></em></span></a>
  </div>
  <!-- <div class="menu-item js-click" data-link="http://task.xiaojukeji.com/volcano/task/list">
   <i class="ic-peek-round"></i>
   <span>赚取积分</span>
  </div> -->
  <div class="menu-item js-click" data-link="/imall/record.htm" data-bind="manually">
   <i class="ic-my-record"></i>
   <a href='/scb/orders'><span>兑换记录</span></a>
  </div>
 </section>
 <!-- <section id="gold-area" class="gold-area mt9">
  <div class="area-main">
   <div class="img-holder js-click" data-link="//xmall.xiaojukeji.com/activity/lottery/index.html?bannerSeq=1&amp;entry=1&amp;_t=1466580127006" data-bind="true">
    <img src="http://xmall.didistatic.com/static/xmall/web_mis/banner_1466211429617" alt="">
   </div>
  </div>
   <div class="area-aside">
    <div class="row">
     <div class="goods js-click" >
      <div class="info">
       <span class="type">福利再来袭 </span>
       <span class="name">苹果6plus</span>
       <span class="price">    <em>8</em>积分      </span>
        <span class="state state-orange">抽奖</span>
       </div>
        <div class="media">
         <img src="images/2.jpg" alt="苹果6plus">
        </div>
       </div>
        </div>
        <div class="row">
         <div class="goods js-click" >
          <div class="info">
           <span class="type">车费升级礼包 </span>
            <span class="name">抽2000元车费礼包</span>
            <span class="price">    <em>8</em>积分      </span>
             <span class="state state-orange">抽奖</span>
           </div>
           <div class="media">
            <img src="images/right.jpg" alt="抽2000元车费礼包">
           </div>
          </div>
         </div>
        </div>
       </section> -->


         <section id="mods">
          <div class="mod">
           <div class="header">
            <span class="txt">限量抢兑</span>
           </div>
           <div class="bd clearfix">
           <?php foreach ($items as $item):
                $item = (object)$item;
           ?>
              <div class="goods js-click">
               <div class="media">
                <img src="http://<?=$_SERVER["HTTP_HOST"]?>/scb/images/item/<?=$item->id?>"  alt="3元快车券">
               </div>
               <div class="info">
                <span class="type"><?=convert($item->type)?></span>
                <span class="name"><?=$item->name?></span>
                <span class="price">    <em><?=$item->price?></em>积分      </span>
               </div>
              </div>
           <?php endforeach?>

          </div>
             </div>
             <div class="mod">
              <div class="header">
               <span class="txt">抽奖专区</span>
              </div>
              <div class="bd clearfix">
               <div class="goods js-click">
                <div class="media">
               <img src="images/goods.jpg"  alt="大疆航拍器无人机">
              </div>
              <div class="info">
               <span class="type">手气大比拼
                <span class="state state-orange">抽奖</span>
                 </span>
                 <span class="name">大疆航拍器无人机</span>
                  <span class="price">    <em>6</em>积分      </span>
                </div>
               </div>
               <div class="goods js-click">
                <div class="media">
                 <img src="images/goods.jpg"  alt="飞利浦空气净化器">
                </div>
                <div class="info">
                 <span class="type">手气大比拼
                  <span class="state state-orange">抽奖</span>
                   </span>
                    <span class="name">飞利浦空气净化器
                    </span>
                    <span class="price">    <em>6</em>积分      </span>
                  </div>
                 </div>
                </div>
               </div>
                <div class="mod"> <div class="header"> <span class="txt">新品优选</span> </div> <div class="bd clearfix">      <div class="goods js-click" > <div class="media"> <img src="images/goods.jpg"  alt="潮流时尚百搭男女墨镜">
               </div>
               <div class="info">
                <span class="type">觅格  </span>
                <span class="name">潮流时尚百搭男女墨镜</span>
                 <span class="price">    <em>39</em>积分
                 </span>
                </div>
                 </div>
                 <div class="goods js-click" >
                  <div class="media">
                   <img src="images/goods.jpg" alt="蜜卡儿钱包"> </div>
                   <div class="info">
                   <span class="type">帝森包包  </span>
                    <span class="name">蜜卡儿钱包</span>
                     <span class="price">    <em>49</em>积分      </span>
                      </div>
                   </div>
                    </div>
                   </div>
                    <div class="mod">
                     <div class="header">
                      <span class="txt">小滴推荐</span>
                       </div>
                       <div class="bd clearfix">
                        <div class="goods js-click" >
                         <div class="media">
                          <img src="images/goods.jpg"  alt="男士洗面爽护肤套装"> </div>
                          <div class="info">
                           <span class="type">韩洛依
                           </span>
                           <span class="name">男士洗面爽护肤套装</span> <span class="price">
                             <em>42</em>积分      </span>
                           </div>
                          </div>
                          <div class="goods js-click" >
                           <div class="media">
                            <img src="images/goods.jpg"  alt="朵酷自拍杆">
                           </div>
                            <div class="info"> <span class="type">朵酷
                            </span>
                            <span class="name">朵酷自拍杆</span> <span class="price">    <em>49</em>积分      </span> </div> </div>    </div> </div>   </section>
      <footer id="page-footer" class="page-footer">
        <div id="law-link" class="law"> <a href="./law.htm">神码商城法律声明</a> </div>
        <div class="concat"> 合作请联系：jifenshangcheng@神码浮云.com </div>
      </footer>
    </div>





</body>
</html>
