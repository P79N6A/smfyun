<style type="text/css">
  .weui_icon_area > img{
      width: 50%;
  }
</style>
<?php
$news = array(
    "fTitle" =>"来自".$nickname."的推荐",
    "fDescription"=>$commend->title,
    "fPicUrl" =>$commend->pic,//logo
    // "Url" =>'',
    "Title"=>"来自".$nickname."的推荐：".$commend->title,
    "PicUrl" =>$commend->pic,//logo
    );
 ?>
<?php if($status==2):?>
<div class="" style="background-color:#f9f9f9;font-family:Microsoft YaHei;"><!-- 自己看自己 -->
    <img src="/qwt/dld/img/banner.png" style="width:100%;height:100%">
    <div class="weui_msg" style="padding:5px;margin: 5px 8px;border:1px solid #e3e3e3;background-color:#fff;">
        <div class="weui_icon_area" style="margin-bottom:5px;"><img src="<?=$commend->pic?>" style="width:100%;"/></div>
        <div class="weui_text_area" style="text-align:left;padding:0 5px;margin-bottom:0;">
            <h2 class="weui_msg_title" style="font-size:18px;line-height:18px;color:3f3f3f;margin-bottom:10px;font-weight:bold;"><?=$commend->title?>
          <?php if($user->suites->name):?>
            <span style="color:#ee5351;margin-left:5px;border:1px solid #ee5351;padding:2px;border-radius:5px;float:right;font-size:15px;"><?=$user->suites->name?></span>
          <?php endif?>
            </h2>
          <?php if($commend->type==0):?>
            <p class="weui_media_desc" style="color:#909090;font-size:15px;margin-bottom:2px;">零售价：<span style="color:#313131;">￥<?=$commend->price?></span></p>

            <p class="weui_media_desc" style="color:#313131;font-size:15px;margin-bottom:2px;">
              <span style="color:#909090;font-weight:normal;">拿货价：</span>￥<?=$commend->money?>
              <!--如果有分组折扣-->
          <?php if($user->suites->name):?>
              <span style="color:#ee5351;margin-left:5px;border:1px solid #ee5351;padding:2px;border-radius:5px;">分组折扣</span>
            <?php endif?>
              <!--如果结束-->
            </p>
            <p class="weui_media_desc" style="color:#FF3030;font-size:15px;font-weight:bold;margin-bottom:2px;"><span style="color:#909090;font-weight:normal;">销售利润：</span>￥<?=number_format($commend->price-$commend->money,2)?></p>
              <!--如果有分组折扣-->
           <!--  <p class="weui_media_desc" style="color:#909090;font-size:15px;margin-bottom:2px;">
            原价：<span style="color:#FF3030;font-weight:bold;">￥<?=$commend->price?></span>
            <span style="margin-left:20px;">利润：</span><span style="color:#FF3030;font-weight:bold;">￥<?=number_format($commend->price-$commend->money,2)?></span>
              <span style="margin-left:20px;">拿货价：</span>
              <span style="color:#313131;">￥<?=$commend->money?></span>
            </p> -->
          <?php endif?>
          <?php if($commend->type==1):?>
            <?php foreach ($sku_commends as $k => $v):?>
              <?php
              $sku = ORM::factory('qwt_dldgoodsku')->where('bid','=',$bid)->where('sku_id','=',$v->sku_id)->find();
              ?>
              <!-- <p class="weui_media_desc" style="color:#666666;font-size:16px;margin-bottom:2px;"><?=$sku->title?>：</p>
              <p class="weui_media_desc" style="color:#909090;font-size:15px;margin-bottom:2px;">
              原价：<span style="color:#FF3030;font-weight:bold;">￥<?=$sku->price?></span>
              <span style="margin-left:20px;">利润：</span><span style="color:#FF3030;font-weight:bold;">￥<?=number_format($sku->price-$v->money,2)?></span><span style="margin-left:20px;">拿货价：</span><span style="color:#313131;">￥<?=$v->money?></span></p> -->
              <?php
              $price[$k] = $sku->price;
              $profit[$k] = number_format($sku->price-$v->money,2);
              $money[$k] = $v->money;
              ?>
            <?php endforeach?>
            <?php
              $max_price = $price[array_search(max($price),$price)];
              $min_price = $price[array_search(min($price),$price)];

              $max_profit = $profit[array_search(max($profit),$profit)];
              $min_profit = $profit[array_search(min($profit),$profit)];

              $max_money = $money[array_search(max($money),$money)];
              $min_money = $money[array_search(min($money),$money)];
            ?>
            <p class="weui_media_desc" style="color:#909090;font-size:15px;margin-bottom:2px;">零售价：<span style="color:#313131;"><?php if($min_price==$max_price):?>￥<?=$min_price?><?php else:?>￥<?=$min_price?>~￥<?=$max_price?><?php endif?></span></p>
            <p class="weui_media_desc" style="color:#313131;font-size:15px;margin-bottom:2px;">
              <span style="color:#909090;font-weight:normal;">拿货价：</span><?php if($min_money==$max_money):?>￥<?=$min_money?><?php else:?>￥<?=$min_money?>~￥<?=$max_money?><?php endif?>
          <?php if($user->suites->name):?>
              <span style="color:#ee5351;margin-left:5px;border:1px solid #ee5351;padding:2px;border-radius:5px;">分组折扣</span>
          <?php endif?>
              <!--如果结束-->
            </p>
            <p class="weui_media_desc" style="color:#FF3030;font-size:15px;font-weight:bold;margin-bottom:2px;"><span style="color:#909090;font-weight:normal;">销售利润：</span>
          <?php if($min_profit==$max_profit):?>
            ￥<?=$min_profit?>
          <?php else:?>
            ￥<?=$min_profit?>~￥<?=$max_profit?>
          <?php endif?>
            </p>
              <!--如果有分组折扣-->
          <?php endif?>

            <!-- <p class="weui_media_desc" style="color:#FF3030;font-size:15px;font-weight:bold;margin-bottom:2px;"><span style="color:#909090;font-weight:normal;">利润：</span>￥<?=$commend->price?>~￥<?=$commend->price?></p>
            <p class="weui_media_desc" style="color:#313131;font-size:15px;margin-bottom:2px;">
              <span style="color:#909090;font-weight:normal;">成本：</span>
              ￥<?=$commend->price?>~￥<?=$commend->price?> -->
              <!--如果有分组折扣-->
             <!--  <span style="color:#ee5351;margin-left:5px;border:1px solid #ee5351;padding:2px;border-radius:5px;">分组折扣</span> -->
              <!--如果结束-->
            <!-- </p> -->
              <!--如果有分组折扣-->
            <!-- <p class="weui_media_desc" style="color:#909090;font-size:12px;">（原成本：￥1.00~￥2.00）</p>
          -->     <!--如果结束-->
            <!-- <p class="weui_msg_desc"><?=$result['content']?></p> -->
        </div>
    </div>
</div>
<?php endif?>
<?php if($status==1):?>
<script type="text/javascript">
    window.open('<?=$commend->url?>','_self');
</script>
<?php endif?>
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
