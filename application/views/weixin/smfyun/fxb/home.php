<?php
$name = $config['title1'];
if($user2['fopenid']){
  $fuser2 = ORM::factory('fxb_qrcode')->where('bid','=',$bid)->where('openid','=',$user2['fopenid'])->find();
  $name = $config['title1'];
  if($fuser2->fopenid){
    $ffuser2 = ORM::factory('fxb_qrcode')->where('bid','=',$bid)->where('openid','=',$fuser2->fopenid)->find();
    $name = $config['title2'];
    if($ffuser2->fopenid&&$config['kaiguan_needpay']==1){
      $fffuser2 = ORM::factory('fxb_qrcode')->where('bid','=',$bid)->where('openid','=',$ffuser2->fopenid)->find();
      $name = $config['titlen3'];
    }
  }
}
    $fuser[] = "{$user2['nickname']}";
//获取推荐用户
if ($user2['fopenid']) $fuser[] = ORM::factory('fxb_qrcode')->where('openid', '=', $user2['fopenid'])->find()->nickname . ' 推荐';
//是否火种用户
$id = ORM::factory('fxb_qrcode')->where('bid','=',$bid)->where('id','<=',$user2['id'])->find_all()->count();
if ($id) $fuser[] =" No.{$id} {$name}";

if ($fuser) $fuser = join(' / ', $fuser);


?>

   <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
  <meta name="description" content="">
  <meta name="author" content="ThemeBucket">
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
    height: 96px;
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
  </style>
</head>
<body>
      <div class="profile_fun">

            <ul id="js_menu" class="menu_list">

                <li class="menu_item width4">
                    <a href="/qwtfxb/money" class="inner_item">
                        <span class="icon_menu"><img src="../fxb/prize/images/total.png"></span>
                        <p class="text_menu">总<?=$config['title5']?></p>
                        <p class="text_menu"><?=number_format($result['money'], 2)?></p>
                     </a>
                </li>
                <li class="menu_item width4">
                    <a href="/qwtfxb/money" class="inner_item">
                        <span class="icon_menu"><img src="../fxb/prize/images/zhuanchu.png"></span>
                        <p class="text_menu">可转出<?=$config['title5']?></p>
                        <p class="text_menu"><?=number_format($result['money_now'], 2)?></p>
                     </a>
                </li>
                <li class="menu_item width4" >
                    <a href="/qwtfxb/shscore" class="inner_item" style="border-right:1px solid #f6f6f6;">
                        <span class="icon_menu"><img src="../fxb/prize/images/keyong.png"></span>
                        <p class="text_menu">可用<?=$config['scorename']?></p>
                        <p class="text_menu shscore-s"><?=number_format($result['shscore'])?></p>
                     </a>
                </li>
                <li class="menu_item width4" >
                    <a href="/qwtfxb/shscore" class="inner_item">
                        <span class="icon_menu"><img src="../fxb/prize/images/yiduihuan.png"></span>
                        <p class="text_menu">已兑换<?=$config['scorename']?></p>
                        <p class="text_menu"><?=number_format($result['useshscore'],2)?></p>
                     </a>
                </li>

            </ul>
            <ul id="js_menu" class="menu_list">

                <li class="menu_item width4">
                    <a href="/qwtfxb/orders" class="inner_item">
                        <span class="icon_menu"><img src="../fxb/prize/images/tuiguangdingdan.png"></span>
                        <p class="text_menu">推广订单</p>
                        <p class="text_menu"><?=(int)$user2['trades']?></p>
                     </a>
                </li>
                <li class="menu_item width4">
                    <a href="/qwtfxb/orders" class="inner_item">
                        <span class="icon_menu"><img src="../fxb/prize/images/leijijine.png"></span>
                        <p class="text_menu">累计金额</p>
                        <p class="text_menu"><?=number_format($result['paid'], 2)?></p>
                     </a>
                </li>
                <li class="menu_item width4" >
                    <a href="/qwtfxb/customer/month" class="inner_item" style="border-right:1px solid #f6f6f6;">
                        <span class="icon_menu"><img src="../fxb/prize/images/one.png"></span>
                        <p class="text_menu">本月新增客户</p>
                        <p class="text_menu"><?=(int)$user2['follows_month']?></p>
                     </a>
                </li>
                <li class="menu_item width4" >
                    <a href="/qwtfxb/customer" class="inner_item">
                        <span class="icon_menu"><img src="../fxb/prize/images/two.png"></span>
                        <p class="text_menu">累计客户</p>
                        <p class="text_menu"><?=(int)$user2['follows']?></p>
                     </a>
                </li>

            </ul>
      </div>
    <div class="container">
      <hr>
      <div class="block" style="padding:20px;line-height:150%;border-top:0px;border-bottom:0px;">
        <h1 class="font-size-18 c-green">活动说明</h1>
        <?=nl2br($config['qwt_fxbmoney_desc'])?>
      </div>
      <hr>
    </div>
    <script type="text/javascript">
    $(document).ready(function(){
      $('.shscore').html(<?=number_format($result['shscore'])?>);
    })
    </script>
</body>
</html>
