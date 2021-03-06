<?php
$news = array(
    "fTitle" =>"来自".$nickname."的推荐",
    "fDescription"=>"来自".$shop->shopname."的好物推荐，快来看看哦~",
    "fPicUrl" =>$shop->shopimg,//logo
    // "Url" =>'',
    "Title"=>"来自".$nickname."的推荐，快来".$shop->shopname."看看哦~",
    "PicUrl" =>$shop->shopimg,//logo
    );
 ?>
<div class="bd">
        <div class="weui_panel weui_panel_access">
            <a href="/wsd/shop/<?=$result['openid']?>/<?=$bid?>"><div class="weui_panel_hd">点击进入分享店铺</div></a>
            <div class="weui_panel_hd"><?=$result['title']?></div>
            <div class="weui_panel_bd">
            <?php foreach ($result['commends'] as $v):?>
              <a href="/wsd/shareopenid/<?=base64_encode($result['openid'])?>/<?=$v->id?>/<?=$bid?>" class="weui_media_box weui_media_appmsg">
                    <div class="weui_media_hd">
                        <img class="weui_media_appmsg_thumb" src="<?=$v->pic?>" alt="">
                    </div>
                    <div class="weui_media_bd">
                        <h4 class="weui_media_title"><?=$v->title?></h4>
                        <p class="weui_media_desc" style="color:#FF3030"><?=$v->price?></p>
                    </div>
              </a>
            <?php endforeach?>
            </div>
        </div>
</div>
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
