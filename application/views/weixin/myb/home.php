<?php
$name = $config['title1'];
if($user2['fopenid']){
  $fuser2 = ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('openid','=',$user2['fopenid'])->find();
  $name = $config['title1'];
  if($fuser2->fopenid){
    $ffuser2 = ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('openid','=',$fuser2->fopenid)->find();
    $name = $config['title2'];
    if($ffuser2->fopenid&&$config['kaiguan_needpay']==1){
      $fffuser2 = ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('openid','=',$ffuser2->fopenid)->find();
      $name = $config['titlen3'];
    }
  }
}
    $fuser[] = "{$user2['nickname']}";
//获取推荐用户
if ($user2['fopenid']) $fuser[] = ORM::factory('myb_qrcode')->where('openid', '=', $user2['fopenid'])->find()->nickname . ' 推荐';
//是否火种用户
$id = ORM::factory('myb_qrcode')->where('bid','=',$bid)->where('id','<=',$user2['id'])->find_all()->count();
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
    <link rel="stylesheet" href="http://<?=$_SERVER['HTTP_HOST']?>/myb/css/weui1.css"/>
    <!-- <link rel="stylesheet" href="http://<?=$_SERVER['HTTP_HOST']?>/myb/css/example1.css"/> -->
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
    <div class="header">
        <div class="pic"></div>
    </div>
    <div class="word">
        <p><?=$fuser?></p>
    </div>
    <div class="table">
        <table class="dj">
            <tr>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/money'><img src="http://<?=$_SERVER['HTTP_HOST']?>/myb/prize/images/total.png"></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/money'><img src="http://<?=$_SERVER['HTTP_HOST']?>/myb/prize/images/zhuanchu.png"></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/shscore'><img src="http://<?=$_SERVER['HTTP_HOST']?>/myb/prize/images/keyong.png"></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/shscore'><img src="http://<?=$_SERVER['HTTP_HOST']?>/myb/prize/images/yiduihuan.png"></a></th>
            </tr>
            <tr>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/money'>总<?=$config['title5']?></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/money'>可转出<?=$config['title5']?></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/shscore'>可用<?=$config['scorename']?></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/shscore'>已兑换<?=$config['scorename']?></a></th>
            </tr>
             <tr>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/money'><?=number_format($result['money'], 2)?></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/money'><?=number_format($result['money_now'], 2)?></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/shscore'><?=number_format(abs($result['shscore']),2)?></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/shscore'><?=number_format($result['useshscore'],2)?></a></th>
            </tr>
        </table>
    </div>
    <div class="table">
    <table class="dj1">
            <tr>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/orders'><img src="http://<?=$_SERVER['HTTP_HOST']?>/myb/prize/images/tuiguangdingdan.png"></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/orders'><img src="http://<?=$_SERVER['HTTP_HOST']?>/myb/prize/images/leijijine.png"></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/customer/month'><img src="http://<?=$_SERVER['HTTP_HOST']?>/myb/prize/images/one.png"></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/customer'><img src="http://<?=$_SERVER['HTTP_HOST']?>/myb/prize/images/two.png"></a></th>
            </tr>
            <tr>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/orders'>推广订单</a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/orders'>累计金额</a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/customer/month'>本月新增客户</a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/customer'>累计客户</a></th>
            </tr>
             <tr>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/orders'><?=(int)$user2['trades']?></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/orders'><?=number_format($result['paid'], 2)?></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/customer/month'><?=(int)$user2['follows_month']?></a></th>
               <th><a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/customer'><?=(int)$user2['follows']?></a></th>
            </tr>
        </table>
      </div>
    <div class="container">
      <hr>
      <a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/score'>
      <div class="nav">
        <img src="http://<?=$_SERVER['HTTP_HOST']?>/myb/prize/images/wodeshouyi.png" id="pic">
        <span class="name">我的<?=$config['title5']?></span>
      </div>
      </a>
      <hr>
      <a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/top'>
      <div class="nav">
        <img src="http://<?=$_SERVER['HTTP_HOST']?>/myb/prize/images/shouyipaihang.png" id="pic">
        <span class="name"><?=$config['title5']?>排行</span>
      </div>
      </a>
      <hr>
      <a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/items'>
      <div class="nav">
        <img src="http://<?=$_SERVER['HTTP_HOST']?>/myb/prize/images/duihuanzhongxin.png" id="pic">
        <span class="name">兑换中心</span>
      </div>
      </a>
      <hr>
      <a href='http://<?=$_SERVER['HTTP_HOST']?>/myb/account_set'>
      <div class="nav">
        <img src="http://<?=$_SERVER['HTTP_HOST']?>/myb/prize/images/jiesuanguize.png" id="pic">
        <span class="name">结算规则</span>
      </div>
      </a>
      <hr>
      <?php if($config['myb_url']):?>
      <a href='<?=$config['myb_url']?>'>
      <div class="nav">
        <img src="http://<?=$_SERVER['HTTP_HOST']?>/myb/prize/images/wenti.png" id="pic">
        <span class="name">常见问题</span>
      </div>
      </a>
      <hr>
      <?php endif?>
      <div class="block" style="padding:20px;line-height:150%;border-top:0px;border-bottom:0px;">
        <h1 class="font-size-18 c-green">活动说明</h1>
        <?=nl2br($config['myb_money_desc'])?>
      </div>
      <hr>
    </div>
    <div class="action-container">
            <span class="btn btn-block btn-green disabled2 haibao">生成海报</span>
    </div>
    <div id="toast" style="display: none;">
        <div class="weui-mask_transparent"></div>
        <div class="weui-toast">
            <i class="weui-icon-success-no-circle weui-icon_toast"></i>
            <p class="weui-toast__content">海报已生成，请前往公众号查看</p>
        </div>
    </div>
    <div id="loadingToast" style="display: none;">
        <div class="weui-mask_transparent"></div>
        <div class="weui-toast">
            <i class="weui-loading weui-icon_toast"></i>
            <p class="weui-toast__content">海报生成中</p>
        </div>
    </div>
    <!-- <div class="action-container">
            <a href="/myb/money" class="btn btn-block btn-green disabled2">转出<?=$config['title5']?></a>
    </div> -->
</body>
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.js"></script>
<script type="text/javascript">
  $('.haibao').click(function(event) {
    $.ajax({
      url: "http://<?=$_SERVER['HTTP_HOST']?>/api/myb?bname=<?=$result['bname']?>&openid=<?=$result['openid']?>",
      type: 'post',
      dataType: 'text',
      timeout:15000,
      beforeSend:function(){
        console.log('beforeSend');
        $('#loadingToast').fadeIn(100);
      },
      success:function(){
        console.log('success');
        $('#loadingToast').hide();
        $('#toast').fadeIn(100);
        setTimeout(function () {
                $('#toast').fadeOut(100);
            }, 2000);
      }
    })
  });
</script>
</html>
