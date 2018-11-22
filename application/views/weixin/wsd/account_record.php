<!DOCTYPE html>
<!-- saved from url=(0044)http://m.yizhibo.com/l/R2ZWUMTk2FgOC02Z.html -->
<html lang="ch" data-dpr="1" style="font-size: 12px;">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width,maximum-scale=1.0,user-scalable=no">
  <title>个人中心</title>
  <meta name="format-detection" content="telephone=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  <meta name="msapplication-tap-highlight" content="no">
  <link href="http://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/wsd/weiui/css/weui.css"/>
    <link rel="stylesheet" href="/wsd/weiui/css/example.css"/>
  <style type="text/css">
  body {
    margin: 0;
    text-rendering: optimizeLegibility;
    -webkit-font-smoothing: antialiased;
    font-family: "Segoe UI", "Lucida Grande", Helvetica, Arial, "Microsoft YaHei", FreeSans, Arimo, "Droid Sans", "wenquanyi micro hei", "Hiragino Sans GB", "Hiragino Sans GB W3", FontAwesome, sans-serif;
  }

  .background {
     overflow: scroll;
    position: fixed;
    width: 100%;
    height: 100%;
    background: #e0e0e0;
  }
.score1 {color:green;}
.score0 {color:red;}
#cd-table{
    margin-top: 0;
}
.cd-table-wrapper{

    background-color: #fff;
    padding: 10px 20px;
    border-top: 1px solid #dedede;
    border-bottom: 1px solid #dedede;
}
.event{
    padding: 5px 0;
    border-bottom: 1px solid #e5e5e5;
    position: relative;
}
.typeandtime{
    display: inline-block;
}
.type{
    font-size: 14px;
    color: #666666;
}
.time{
    font-size: 12px;
    color: #bbbbbb;
}
.score{

    display: inline-block;
    /*float: right;*/
    font-size: 14px;
    font-weight: bold;
    height: 19px;
    line-height: 19px;
    position: absolute;
    right: 0;
    bottom: 5px;
}
.score0{
    color: #33bb33;
}
.score1{
    color: #ff9900;
}
.nomore{
    text-align: center;
    font-size: 14px;
    color: #888888;
}
.menu_item{
  width: 25% !important;
}
.circle{
  display: inline-block;
  padding-top: 20px;
  background-color: #ff9900;
}
.desc{
  color: #fff !important;
}
.number{
  color: #fff !important;
}
.profile_logo{
  float: left;
}
.user{
  padding: 20px 0 20px 20px;
  height: 104px;
  background-color: #fff;
  border-top: 1px solid #dedede;
border-bottom: 1px solid #dedede;
}
.usertext{
  display: inline-block;
}
.profile_name{
  text-align: left;
  font-size: 24px;
  font-weight: bold;
}
.profile_extra{
  margin-top: 40px;
}
.data_overview{
  margin-top: 10px;
  background-color: #fff;
  border-top: 1px solid #dedede;
  border-bottom: 1px solid #dedede;
}
.profile_fun{
  background-color: #fff;
  margin-top: 10px;
  margin-bottom: 10px;
}
.icon_menu{
  width: 30px !important;
}
.icon_menu img{
  width: 30px;
  height: 30px;
}
.text_menu{
  color: #666666;
  padding-top: 5px;
}
.menu_list:before{
    height: 0;
    border-top: 1px solid #dedede;
}
.menu_item:after{
    width: 0 !important;
}
.desc:after{
    border-right: 0 !important;
}
.header {
    height: 2.4rem;
    padding: 0;
    position: relative;
    text-align: center;
    color: #666;
    border-bottom: 1px solid #e5e5e5;
    border-top: 1px solid #e5e5e5;
    margin-bottom: 10px;
    background-color: #fff;
}
.header:before {
    content: '';
    border-bottom: 1px solid #e5e5e5;
    position: absolute;
    left: 0;
    top: 50%;
    width: 100%;
    height: 0;
    z-index: 0;
    -webkit-transform: translate3d(0,-2px,0);
    transform: translate3d(0,-2px,0);
}
.header .txt {
    position: relative;
    display: inline-block;
    padding: 0 .5rem;
    line-height: 2.4rem;
    background-color: #f0f0f0;
}
</style>

    <!-- TableList -->
    <section id="cd-table">
            <div class="header" style="margin-top:7px;">
                <span class="txt" style='font-size:18px;background:#fff'><a>结算记录</a></span>
            </div>
        <div class="cd-table-container">
          <div class="cd-table-wrapper">
        <?php foreach ($records as $k=>$v):?>
            <div class="event">
                <div class="typeandtime">
                    <div class="type"><?=$v->getTypeName($v->type)?><?=$v->bz?'（'.$v->bz.'月）':''?></div>
                    <div class="time"><?=date('Y-m-d H:i:s',$v->lastupdate)?></div>
                </div>
                <div class="<?=$score->score > 0 ? 'score score1' : 'score score0'?>">
                    <?=-$v->score?>元
                </div>
            </div>

        <?php endforeach?>
<div class="nomore">没有更多了</div>
            <!--<ul>
                <li class="cd-table-column">
                <div class="scoreid">
                    <b>ID</b>
                </div>
                <div class="type">
                    <b><?=$scorename ?>来源</b>
                </div>
                <div class="score">
                    <b>数量</b>
                </div>
                <div class="update">
                <b>增加时间</b>
                </div>
                </li>

<?php
$id = count($scores)+1;
foreach ($scores as $score):
$id--;
?>
                <li class="cd-table-column">
                <div class="scoreid">
                    <?=$id?>
                </div>
                <div class="type">
                    <?=str_replace("积分", $scorename, $score->getTypeName($score->type));?>
                </div>
                <div class="<?=$score->score > 0 ? 'score score1' : 'score score0'?>">
                    <?=$score->score?>
                </div>
                <div class="update">
                    <?=date('Y-m-d', $score->lastupdate)?>
                </div>
                </li>
<?php endforeach?>

            </ul> -->
            </div> <!-- cd-table-wrapper -->
        </div> <!-- cd-table-container -->
    </section> <!-- cd-table -->

    <script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
    <script type="text/javascript">
    var h= $('.typeandtime').height();
    // $('.score').css('height',h);
    // $('.score').css('line-height',h+'px');
    $('.nomore').css('height',h);
    $('.nomore').css('line-height',h+'px');
        </script>
</body>

</html>
