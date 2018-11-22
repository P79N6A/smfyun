<?php
$name = $config['title1'];
if($user2['sid']){
  $name = ORM::factory('qwt_qfxsku')->where('bid','=',$bid)->where('id','=',$user2['sid'])->find()->name;
}
    $fuser[] = "{$user2['nickname']}";
//获取推荐用户
if ($user2['fopenid']) $fuser[] = ORM::factory('qwt_qfxqrcode')->where('openid', '=', $user2['fopenid'])->find()->nickname . ' 推荐';
//是否火种用户
// $id = ORM::factory('qwtqfx_qrcode')->where('bid','=',$bid)->where('lv','=',1)->where('id','<=',$user2['id'])->find_all()->count();
$id = $user2['fid'];
if ($id) $fuser[] =" 编号：No.{$id} ";
$fuser[] = '分销商等级：'.$name;
if ($fuser) $fuser = join(' / ', $fuser);


?>

   <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="ThemeBucket">
  <link rel="stylesheet" href="/qwt/qfx/weiui/css/weui.css"/>
  <style>
  html,p,table,hr
  {
    padding: 0;
    margin: 0;
  }
  body
  {
    margin: 0;
    padding: 0;
    background: #eeeeee;
  }
  .header
  {
    width: 100%;
    height: 80px;
    background: #f8f8f8;
  }
  .pic
  {
    width: 52px;
    height: 52px;
    line-height: 100px;
    margin: auto;
    border-radius:30px;
    background-image: url('<?=$user2['headimgurl']?>');
    background-size:cover;
    position: relative;
    top: 20%;
  }
  .word
  {
    width: 100%;
    height: 30px;
    line-height: 30px;
    color:#ffffff;
    background: #5b5b5b;
    font-size: 14px;
    text-align: center;
  }
  .table
  {
    width: 100%;
    height: 75px;
    background: #ffffff;
    line-height: 20px;
    padding-top: 10px;
  }
  .dj
  {
    width: 100%;
    color: #414141;
    font-size: 14px;
  }
  .dj1
  {
    width: 100%;
    color: #414141;
    font-size: 14px;
    margin-left: -5px;
  }
  img
  {
    width: 20px;
    height: 20px;
  }
  .container
  {
    width: 100%;
    height: auto;
  }
  hr
  {
    width: 100%;
    height: 1px;
    background: #ececec;
    border-style:none;
  }
  .nav
  {
    width: 100%;
    height: 40px;
  }
  #pic
  {
    margin-left: 16px;
    margin-top: 10px;
  }
  .name
  {
    bottom: 5px;
    left: 15px;
    position: relative;
    font-size: 13px;
  }
  .jiange
  {
    width: 100%;
    height: 11px;
    background:#f8f8f8;
  }
  th{
    width:25%;
  }
  </style>
</head>
<body>
<?php if($user2['lv']==2):?>
<div class="page"><!-- 审核中 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_waiting"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">审核中</h2>
            <p class="weui_msg_desc">对不起，您的资格还在审核中</p>
        </div>
    </div>
</div>
<?php endif?>
<?php if($user2['lv']==3):?>
<div class="page"><!-- 被取消 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_warn"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">很遗憾</h2>
            <p class="weui_msg_desc">对不起，您的资格已被取消，请联系管理员</p>
        </div>
    </div>
</div>
<?php endif?>
<?php if($user2['lv']==1):?>
    <div class="header">
        <div class="pic"></div>
    </div>
    <div class="word">
        <p><?=$fuser?></p>
    </div>
    <div class="table">
        <table class="dj">
            <tr>
               <th style="width:25%"><a href='/qwtqfx/money'><img src="../qwt/qfx/prize/images/total.png"></a></th>
               <th><a href='/qwtqfx/money'><img src="../qwt/qfx/prize/images/zhuanchu.png"></a></th>
               <th><a href='/qwtqfx/score'><img src="../qwt/qfx/prize/images/wodeshouyi.png"></a></th>
               <th><a href='/qwtqfx/top'><img src="../qwt/qfx/prize/images/shouyipaihang.png"></a></th>
            </tr>
            <tr>
               <th><a href='/qwtqfx/money'>总<?=$config['title5']?></a></th>
               <th><a href='/qwtqfx/money'>可转出<?=$config['title5']?></a></th>
               <th><a href='/qwtqfx/score'><?=$config['title5']?>明细</a></th>
               <th><a href='/qwtqfx/top'><?=$config['title5']?>排行</a></th>
            </tr>
             <tr>
               <th><a href='/qwtqfx/money'><?=number_format($result['money'], 2)?></a></th>
               <th><a href='/qwtqfx/money'><?=number_format($result['money_now'], 2)?></a></th>
            </tr>
        </table>
    </div>
    <div class="table">
    <table class="dj1">
            <tr>
               <th style="width:25%"><a href='/qwtqfx/orders'><img src="../qwt/qfx/prize/images/tuiguangdingdan.png"></a></th>
               <th><a href='/qwtqfx/orders'><img src="../qwt/qfx/prize/images/leijijine.png"></a></th>
               <th><a href='/qwtqfx/customer/month'><img src="../qwt/qfx/prize/images/one.png"></a></th>
               <th><a href='/qwtqfx/customer'><img src="../qwt/qfx/prize/images/two.png"></a></th>
            </tr>
            <tr>
               <th><a href='/qwtqfx/orders'>推广订单</a></th>
               <th><a href='/qwtqfx/orders'>累计金额</a></th>
               <th><a href='/qwtqfx/customer/month'>本月新增客户</a></th>
               <th><a href='/qwtqfx/customer'>累计客户</a></th>
            </tr>
             <tr>
               <th><a href='/qwtqfx/orders'><?=(int)$user2['trades']?></a></th>
               <th><a href='/qwtqfx/orders'><?=number_format($result['paid'], 2)?></a></th>
               <th><a href='/qwtqfx/customer/month'><?=(int)$user2['follows_month']?></a></th>
               <th><a href='/qwtqfx/customer'><?=(int)$user2['follows']?></a></th>
            </tr>
        </table>
      </div>
    <div class="container">
      <hr>
      <!-- <a href='/qwtqfx/score'>
      <div class="nav">
        <img src="../qwt/qfx/prize/images/wodeshouyi.png" id="pic">
        <span class="name">我的<?=$config['title5']?></span>
      </div>
      </a>
      <hr>
      <a href='/qwtqfx/top'>
      <div class="nav">
        <img src="../qwt/qfx/prize/images/shouyipaihang.png" id="pic">
        <span class="name"><?=$config['title5']?>排行</span>
      </div>
      </a>
      <hr> -->
      <?php if($config['qfx_url']):?>
      <a href='<?=$config['qfx_url']?>'>
      <div class="nav">
        <img src="../qwt/qfx/prize/images/wenti.png" id="pic">
        <span class="name">常见问题</span>
      </div>
      </a>
      <hr>
      <?php endif?>
      <div class="block" style="padding:20px;line-height:150%;border-top:0px;border-bottom:0px;">
        <h1 class="font-size-18 c-green">活动说明</h1>
        <?=nl2br($config['qfx_money_desc'])?>
      </div>
      <hr>
    </div>
    <div class="action-container">
            <a href="/qwtqfx/money" class="btn btn-block btn-green disabled2">转出<?=$config['title5']?></a>
    </div>
<?php endif?>
</body>
</html>
