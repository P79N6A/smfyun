<?php
$news = array("Title" =>"赶快来看看你的甜蜜指数！15天，你能坚持么？", "Description"=>"",
 "PicUrl" =>'http://games.smfyun.com/qd/bg/share.png', "Url" =>'http://games.smfyun.com/qd/storefuop',"fTitle"=>"我刚刚完成了一关甜蜜任务，赶快加入吧！");
 ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">
    <title>15天甜蜜任务大考验</title>
    <link rel="stylesheet" href="../qd/weiui/weui.css"/>
    <link rel="stylesheet" href="../qd/weiui/example.css"/>
</head>
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
  font-size: 15px;
  color: #526B82;
  padding-bottom: 0px;
  padding-top: 0px;
}
.name span{
 float: right;
}
.name2{
  height: 30px;
  line-height: 30px;
  padding: 10px;
  font-size: 15px;
  color: #526B82;
  padding-bottom: 0px;
  padding-top: 0px;
}
.name2 span{
 float: right;
}
.condition{
 height: 30px;
 line-height: 30px;
 padding: 10px;
 font-size: 13px;
 border-top: 1px solid #9E9E9E;
 color: red;
}
 .prize1,.prize2,.prize3,.prized,.nprize,.dprize,.close{
  cursor:pointer;
 }
 input{
  width: 80%;
  margin-top: 15px;
 }
 .confirm,.close{
  width: 50%;
  display: inline-block;
  height: 30px;
  border:0px;
 }
 .get{
    /*margin-left: 40px;*/
    background-color: red;
    width: auto;
    display: inline-block;
    color: white;
    text-align: center;
    border-radius: 5px;
    padding-left: 5px;
    padding-right: 5px;
    /*float: right;*/
 }
 .weui_dialog_ft,.close,.confirm{
  height: 40px;
 }
</style>

<body>
    <div style="margin-top:10px;text-align:center">您现在拥有<?=$user->score?>积分</div>
    <div class="menu">
        <a class="tab taba" href='../qd/prize'>兑换奖品</a>
        <a class="tab tabb" href='../qd/days'>任务完成详情</a>
    </div>
    <div class="content">
    <?php foreach ($result['items'] as $item):?>
      <?php
      $num=ORM::factory('qd_order')->where('iid','=',$item->id)->count_all();
      $has_order = ORM::factory('qd_order')->where('qid','=',$user->id)->where('iid','=',$item->id)->find();
        if($has_order->id){
          $class = 'prized';//已经领过本物品
          $end = '已兑换过啦';
        }else if($user->score<$item->score){
          $class = 'dprize';//分数不够
          $end = '积分不够';
        }else if($num>=$item->stock){
          $class = 'nprize';//没有库存
          $end = '木有库存啦';
        }else{
          $class = 'prize'.$item->id;//正常
          $end = '立即兑换';
        }

      ?>
        <div class="items <?=$class?>" data-id='<?=$item->id?>'>
            <div class="pic">
                <img src="http://cdn.jfb.smfyun.com/qd/images/item/<?=$item->id?>.v<?=$item->lastupdate?>0.jpg">
            </div>
            <div class="name">
                <?=$item->name?>
            </div>
            <?php if($item->id!=3):?>
            <div class="name2">
              剩余数量：<?=$item->stock-$num?>
              <span class='price'>价值￥<?=$item->price?></span>
            </div>
            <?php endif?>
            <div class="condition"><?=$item->score?>积分(连续完成<?=$item->score/10?>个任务可获得<?=$item->id==3?'抽奖资格':'';?>)</div>
            <div class="condition" style="text-align:center"><span class='get'><?=$end?></span></div>
            <?php if($item->id==3):?>
              <div class="condition" style="height:auto;">注意：该奖品兑换后将获得奥地利蜜月游的抽奖资格，活动结束后，会从获得抽奖资格的用户中随机抽取一位送出奥地利蜜月游。</div>
            <?php endif?>
        </div>
    <?php endforeach?>
    </div>
    <div style="height:20px;"></div>
</body>
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.js"></script>
<script type="text/javascript">
  $(document).on('click', '.prize1', function(event) {
    var id = $(this).data('id');
    $('body').append('<div class=\"weui_dialog_confirm\" id=\"dialog1\">'+
        '<div class=\"weui_mask\"></div>'+
        '<div class=\"weui_dialog\">'+
            '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\">填写收货信息</strong></div>'+
            '<div class=\"weui_dialog_bd\">您只有一次兑换奖品的机会，请慎重选择并填写下列信息！</div>'+
            '<form method=\'post\'>'+
            '<div class=\"weui_dialog_bd\">'+
              '<label>姓名:</label><input type=\'text\' name=\'form[name]\' maxlength=\'5\'/><br>'+
              '<label>电话:</label><input type=\'number\' name=\'form[tel]\' maxlength=\'11\'/><br>'+
              '<label>地址:</label><input type=\'text\' name=\'form[address]\' maxlength=\'30\'/><br>'+
              '<label>备注:</label><input type=\'text\' name=\'form[memo]\' maxlength=\'30\'/>'+
              '<input type=\'hidden\' name=\'form[type]\' value=\'1\'/>'+
            '</div>'+
            '<div class=\"weui_dialog_ft\">'+
                '<button type=\"button\" class=\"weui_btn_dialog default close\">取消</button>'+
                '<button type=\"submit\" class=\"weui_btn_dialog primary confirm\">确定</button>'+
            '</div>'+
            '</form>'+
        '</div>'+
    '</div>')
  });
$(document).on('click', '.prize2', function() {
    $('body').append('<div class=\"weui_dialog_confirm\" id=\"dialog1\">'+
        '<div class=\"weui_mask\"></div>'+
        '<div class=\"weui_dialog\">'+
            '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\">填写收货信息</strong></div>'+
            '<div class=\"weui_dialog_bd\">您只有一次兑换奖品的机会，请慎重选择并填写下列信息！</div>'+
            '<form method=\'post\'>'+
            '<div class=\"weui_dialog_bd\">'+
              '<label>姓名:</label><input type=\'text\' name=\'form[name]\' maxlength=\'5\'/><br>'+
              '<label>电话:</label><input type=\'number\' name=\'form[tel]\' maxlength=\'11\'/><br>'+
              '<label>地址:</label><input type=\'text\' name=\'form[address]\' maxlength=\'30\'/><br>'+
              '<label>备注:</label><input type=\'text\' name=\'form[memo]\' maxlength=\'30\'/>'+
              '<input type=\'hidden\' name=\'form[type]\' value=\'2\'/>'+
            '</div>'+
            '<div class=\"weui_dialog_ft\">'+
                '<button type=\"button\" class=\"weui_btn_dialog default close\">取消</button>'+
                '<button type=\"submit\" class=\"weui_btn_dialog primary confirm\">确定</button>'+
            '</div>'+
            '</form>'+
        '</div>'+
    '</div>')
  });
$(document).on('click', '.prize3', function(event) {
    $('body').append('<div class=\"weui_dialog_confirm\" id=\"dialog1\">'+
        '<div class=\"weui_mask\"></div>'+
        '<div class=\"weui_dialog\">'+
            '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\">填写收货信息</strong></div>'+
            '<div class=\"weui_dialog_bd\">您只有一次兑换奖品的机会，请慎重选择并填写下列信息！</div>'+
            '<form method=\'post\'>'+
            '<div class=\"weui_dialog_bd\">'+
              '<label>姓名:</label><input type=\'text\' name=\'form[name]\' maxlength=\'5\'/><br>'+
              '<label>电话:</label><input type=\'number\' name=\'form[tel]\' maxlength=\'11\'/><br>'+
              '<label>地址:</label><input type=\'text\' name=\'form[address]\' maxlength=\'30\'/><br>'+
              '<label>备注:</label><input type=\'text\' name=\'form[memo]\' maxlength=\'30\'/>'+
              '<input type=\'hidden\' name=\'form[type]\' value=\'3\'/>'+
            '</div>'+
            '<div class=\"weui_dialog_ft\">'+
                '<button type=\"button\" class=\"weui_btn_dialog default close\">取消</button>'+
                '<button type=\"submit\" class=\"weui_btn_dialog primary confirm\">确定</button>'+
            '</div>'+
            '</form>'+
        '</div>'+
    '</div>')
  });
$(document).on('click', '.prized', function(event) {//已经兑换
    $('body').append('<div class=\"weui_dialog_alert\" id=\"dialog2\" >'+
     '<div class=\"weui_mask\"></div>'+
     '<div class=\"weui_dialog\">'+
         '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\">已经兑换了哦</strong></div>'+
         '<div class=\"weui_dialog_bd\">不好意思，您已经兑换过了奖品！</div>'+
         '<div class=\"weui_dialog_ft\">'+
             '<a class=\"weui_btn_dialog primary close\">确定</a>'+
         '</div>'+
     '</div>'+
 '</div>')
  });
// $(document).on('click', '.dprize', function(event) {//分数不够
//     $('body').append('<div class=\"weui_dialog_alert\" id=\"dialog2\" >'+
//      '<div class=\"weui_mask\"></div>'+
//      '<div class=\"weui_dialog\">'+
//          '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\">很遗憾</strong></div>'+
//          '<div class=\"weui_dialog_bd\">很遗憾您的连续完成任务未到达资格哦，请兑换其他奖品试试看！</div>'+
//          '<div class=\"weui_dialog_ft\">'+
//              '<a class=\"weui_btn_dialog primary close\">确定</a>'+
//          '</div>'+
//      '</div>'+
//  '</div>')
//   });
$(document).on('click', '.nprize', function(event) {//没库存了
    $('body').append('<div class=\"weui_dialog_alert\" id=\"dialog2\" >'+
     '<div class=\"weui_mask\"></div>'+
     '<div class=\"weui_dialog\">'+
         '<div class=\"weui_dialog_hd\"><strong class=\"weui_dialog_title\">很遗憾</strong></div>'+
         '<div class=\"weui_dialog_bd\">很遗憾，该奖品已经被抢完啦!</div>'+
         '<div class=\"weui_dialog_ft\">'+
             '<a class=\"weui_btn_dialog primary close\">确定</a>'+
         '</div>'+
     '</div>'+
 '</div>')
  });
 $(document).on('click', '.close', function() {
  $('.weui_dialog_confirm').remove();
  $('.weui_dialog_alert').remove();
 });
</script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>
  wx.config({
    debug: 0,
    appId: '<?php echo $signPackage["appId"];?>',
    timestamp: <?php echo $signPackage["timestamp"];?>,
    nonceStr: '<?php echo $signPackage["nonceStr"];?>',
    signature: '<?php echo $signPackage["signature"];?>',
    jsApiList: [
      // 所有要调用的 API 都要加到这个列表中
      'checkJsApi',
      'onMenuShareTimeline',
      'onMenuShareAppMessage'
      ]
  });
      wx.ready(function () {
    //自动执行的
    wx.checkJsApi({
      jsApiList: [
        'getLocation',
        'onMenuShareTimeline',
        'onMenuShareAppMessage'
      ],
      success: function (res) {

      }
    });
    wx.onMenuShareAppMessage({//好友
      title: '<?php echo $news['fTitle'];?>',
      desc: '<?php echo $news['Description'];?>',
      link: '<?php echo $news['Url'];?>',
      imgUrl: '<?php echo $news['PicUrl'];?>',
      trigger: function (res) {
      // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
      // alert('用户点击发送给朋友');
      },
      success: function (res) {
      // alert('已分享');
      },
      cancel: function (res) {
      // alert('已取消');
      },
      fail: function (res) {
      // alert(JSON.stringify(res));
      }
    });

    wx.onMenuShareTimeline({//朋友圈
      title: '<?php echo $news['Title'];?>',
      // desc: '<?php echo $news['Description'];?>',
      link: '<?php echo $news['Url'];?>',
      imgUrl: '<?php echo $news['PicUrl'];?>',
      trigger: function (res) {
      // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
      // alert('用户点击分享到朋友圈');
      },
      success: function (res) {
      // alert('已分享');
      },
      cancel: function (res) {
      // alert('已取消');
      },
      fail: function (res) {
      // alert(JSON.stringify(res));
      }
    });
  });

  wx.error(function (res) {
    alert(res.errMsg);
  });
</script>
</html>
