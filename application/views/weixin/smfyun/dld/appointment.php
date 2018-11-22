<?php
$fDescription = $config['appmessage']?$config['appmessage']:"快来加入我的团队成为".$shop->shopname."的代理商吧！";
$Title = $config['timeline']?$config['timeline']:"快来加入我的团队成为".$shop->shopname."的代理商吧！";
$news = array(
    "fTitle" =>$result['title'],
    "fDescription"=>$fDescription,//朋友
    "fPicUrl" =>$fuser->headimgurl,//logo
    "Url" => 'http://'.$_SERVER["HTTP_HOST"].'/qwtdld/appointment/'.$fuser->openid.'/'.$bid,
    "Title"=>$Title,//朋友圈
    "PicUrl" =>$fuser->headimgurl,//logo
    );
 ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title><?=$result['_title']?$result['_title']:"代理申请"?></title>
    <link rel="stylesheet" href="/qwt/dld/weiui/css/weui.css"/>
    <link rel="stylesheet" href="/qwt/dld/weiui/css/example.css"/>
    <link rel="stylesheet" href="/qwt/dld/weiui/css/weui1.css"/>
    <link rel="stylesheet" href="/qwt/dld/weiui/css/example1.css"/>
    <style type="text/css">
    .page{
        opacity: 1 !important;
    }
    </style>
</head>
<?php if($result['lv']==0):?>
<div class="page"><!-- 自己打开 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_success weui_icon_msg"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">操作成功</h2>
            <p class="weui_msg_desc"><?=$result['content']?></p>
        </div>
    </div>
</div>
<?php endif?>
<?php if($result['lv']==1):?>
<div class="page"><!-- 申请成功 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_success weui_icon_msg"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">操作成功</h2>
            <p class="weui_msg_desc"><?=$result['content']?></p>
        </div>
    </div>
</div>
<?php endif?>
<?php if($result['lv']==2):?>
<div class="page"><!-- 审核中 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_waiting"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">还差一步</h2>
            <p class="weui_msg_desc"><?=$result['content']?></p>
            <div class="weui-cells__title" style="color:red;">输入的手机号将作为今后的账号，注册成功后无法更改及重复申请，请确认输您入了正确的手机号</div>
            <form method="post" action="" onsubmit="retrun sheck()">
            <div class="weui-cells weui-cells_form" style="margin-left:-20px;margin-right:-20px;">
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">手机号</label></div>
                <div class="weui-cell__bd">
                    <input id="tel" class="weui-input" name="tel" type="number" pattern="[0-9]*" placeholder="请输入手机号">
                    <input class="weui-input" name="openid" type="hidden" value="<?=$result['openid']?>">
                </div>
            </div>
            </div>
            <?php if($result['error']):?>
            <div class="weui-cells__title" style="color:red;">对不起，该手机号已经注册过了</div>
        <?php endif?>
          <?php if($config['buy_url']):?>
            <button type="submit" style="margin-top:20px"  class="weui_btn weui_btn_plain_primary">确认提交手机号并前往购买激活</button>
          <?php endif?>
          </form>
        </div>
    </div>
</div>
<?php endif?>
<?php if($result['lv']==3):?>
<div class="page"><!-- 被取消 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_waiting"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">很遗憾</h2>
            <p class="weui_msg_desc"><?=$result['content']?></p>
        </div>
    </div>
</div>
<?php endif?>
<?php if($result['lv']==4):?>
<div class="page"><!-- 被取消 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_waiting"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">很遗憾</h2>
            <p class="weui_msg_desc">您已退款，无法再申请成为代理</p>
        </div>
    </div>
</div>
<?php endif?>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script>
function check(){
  if ($('#tel').val()==""){
    alert('请填写手机号');
    return false;
  }else{
    return true;
  }
}
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
      'onMenuShareAppMessage',
      'hideMenuItems'
      ]
  });
  wx.ready(function () {

    //自动执行的
    wx.checkJsApi({
      jsApiList: [
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'hideMenuItems'
      ],
      success: function (res) {
        console.log(res);
      }
    });
    wx.hideMenuItems({
            menuList: [
            <?php if($result['lv']!=0):?>
              'menuItem:share:appMessage',
              'menuItem:share:timeline',
            <?php endif?>
            'menuItem:copyUrl',
            "menuItem:editTag",
            "menuItem:delete",
            "menuItem:copyUrl",
            "menuItem:originPage",
            "menuItem:readMode",
            "menuItem:openWithQQBrowser",
            "menuItem:openWithSafari",
            "menuItem:share:email",
            "menuItem:share:brand",
            "menuItem:share:qq",
            "menuItem:share:weiboApp",
            "menuItem:favorite",
            "menuItem:share:facebook",
            "menuItem:share:QZone"
            ],
            success: function (res) {
              console.log(res);
            },
            fail: function (res) {
              console.log(res);
            }
        });
    wx.onMenuShareAppMessage({//好友
      title: '<?php echo $news['fTitle'];?>',
      desc: '<?php echo $news['fDescription'];?>',
      // link: '<?php echo $news['Url'];?>',
      imgUrl: '<?php echo $news['fPicUrl'];?>',
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
      // link: '<?php echo $news['Url'];?>',
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
