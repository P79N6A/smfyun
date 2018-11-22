<?php
switch ($user2['lv']) {
  case 0:
    $name = $config['pool_name'];
    break;
  case 1:
    $name = $config['third'];
    break;
  case 2:
    $name = $config['second'];
    break;
  case 3:
    $name = $config['one'];
    break;
  default:
    # code...
    break;
}
    $fuser[] = "{$user2['nickname']}";
//获取推荐用户
if ($user2['fopenid']) $fuser[] = ORM::factory('ytb_qrcode')->where('openid', '=', $user2['fopenid'])->find()->nickname . ' 推荐';
$id = ORM::factory('ytb_qrcode')->where('bid','=',$bid)->where('id','<=',$user2['id'])->find_all()->count();

if ($id) $fuser[] =" 编号：No.{$id} ";
$fuser[] = $name;
if ($fuser) $fuser = join(' / ', $fuser);


?>

   <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="ThemeBucket">
  <link rel="stylesheet" href="/ytb/weiui/css/weui.css"/>
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
    width: 67%;
    color: #414141;
    font-size: 14px;
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
    <div class="header">
        <div class="pic"></div>
    </div>
    <div class="word">
        <p><?=$fuser?></p>
    </div>
    <div class="table">
        <table class="dj">
            <tr>
               <th style="width:33%"><a href='/ytb/score'><img src="../ytb/prize/images/total.png"></a></th>
               <th style="width:33%"><a href='/ytb/score'><img src="../ytb/prize/images/zhuanchu.png"></a></th>
               <th style="width:33%"><a href='/ytb/wait_score'><img src="../ytb/prize/images/tuiguangdingdan.png"></a></th>

            </tr>
            <tr>
               <th style="width:33%"><a href='/ytb/score'>累计<?=$config['score_name']?></a></th>
               <th style="width:33%"><a href='/ytb/score'>可用<?=$config['score_name']?></a></th>
               <th style="width:33%"><a href='/ytb/wait_score'>待结算<?=$config['score_name']?></a></th>
            </tr>
             <tr>
               <th style="width:33%"><a href='/ytb/score'><?=$user2['all_score']?></a></th>
               <th style="width:33%"><a href='/ytb/score'><?=$user2['score']?></a></th>
               <th style="width:33%"><a href='/ytb/wait_score'><?=$user2['wait_score']?></a></th>
            </tr>
        </table>
    </div>
    <div class="table">
    <table class="dj1">
            <tr>
               <th style="width:33%"><a href='/ytb/customer'><img src="../ytb/prize/images/two.png"></a></th>
               <th style="width:33%"><a href='/ytb/score'><img src="../ytb/prize/images/wodeshouyi.png"></a></th>
               <!-- <th style="width:33%"><a href='/ytb/top'><img src="../ytb/prize/images/shouyipaihang.png"></a></th> -->
            </tr>
            <tr>
               <th style="width:33%"><a href='/ytb/customer'>累计客户</a></th>
               <th style="width:33%"><a href='/ytb/score'><?=$config['score_name']?>明细</a></th>
               <!-- <th style="width:33%"><a href='/ytb/top'><?=$config['score_name']?>排行</a></th> -->
            </tr>
             <tr>
               <th style="width:33%"><a href='/ytb/customer'><?=$user2['follows']?></a></th>
            </tr>
        </table>
      </div>
    <div class="container">
      <hr>
      <?php if($config['ytb_url']):?>
      <a href='<?=$config['ytb_url']?>'>
      <div class="nav">
        <img src="../ytb/prize/images/wenti.png" id="pic">
        <span class="name">常见问题</span>
      </div>
      </a>
      <hr>
      <?php endif?>
      <a href='/ytb/items'>
      <div class="nav">
        <img src="../ytb/prize/images/wodeshouyi.png" id="pic">
        <span class="name"><?=$config['score_name']?>兑换中心</span>
      </div>
      </a>
      <hr>
      <a href='<?='http://'.$_SERVER["HTTP_HOST"].'/ytb/storefuop/'.$bid?>'>
      <div class="nav">
        <img src="../ytb/prize/images/shouyipaihang.png" id="pic">
        <span class="name">推荐商品</span>
      </div>
      </a>
      <hr>
      <div class="block" style="padding:20px;line-height:150%;border-top:0px;border-bottom:0px;">
        <h1 class="font-size-18 c-green">活动说明</h1>
        <?=nl2br($config['ytb_money_desc'])?>
      </div>
      <hr>
    </div>
</body>
</html>
