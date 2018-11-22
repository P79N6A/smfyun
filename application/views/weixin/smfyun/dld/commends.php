<?php
$news = array(
    "fTitle" =>"来自".$nickname."的推荐",
    "fDescription"=>"来自".$shop->shopname."的好物推荐，快来看看哦~",
    "fPicUrl" =>$shop->shoplogo,//logo
    // "Url" =>'',
    "Title"=>"来自".$nickname."的推荐，快来".$shop->shopname."看看哦~",
    "PicUrl" =>$shop->shoplogo,//logo
    );
 ?>
<div class="bd">
        <div class="weui_panel weui_panel_access">
            <!-- <a href="/dld/shop/<?=$result['openid']?>/<?=$bid?>"><div class="weui_panel_hd">点击进入分享店铺</div></a> -->
            <img src="/qwt/dld/img/banner.png" style="width:100%;height:100%">
            <div class="weui_panel_hd" style="margin-top:-7px;background-color:#f8f9fb;"><span><?=$result['title']?></span></div>
            <div class="weui_panel_bd">
            <?php foreach ($result['commends'] as $v):?>
              <?php
              if($v->type==1){//多规格
                if($user->group_id>0){//有分组
                  $sku_commends = ORM::factory('qwt_dldsmoney')->where('bid','=',$bid)->where('sid','=',$user->group_id)->where('item_id','=',$v->num_iid)->find_all();
                }else{
                  $sku_commends = ORM::factory('qwt_dldgoodsku')->where('bid','=',$bid)->where('item_id','=',$v->num_iid)->find_all();
                }
                foreach ($sku_commends as $key => $value) {
                  $sku = ORM::factory('qwt_dldgoodsku')->where('bid','=',$bid)->where('sku_id','=',$value->sku_id)->find();
                  $profit[$key] = $sku->price-$value->money;
                }
                // echo '<pre>';
                // var_dump($profit);
                $min_profit = $profit[array_search(min($profit),$profit)];
              }else{
                if($user->group_id>0){
                  $smoney = ORM::factory('qwt_dldsmoney')->where('bid','=',$bid)->where('sid','=',$user->group_id)->where('sku_id','=',0)->where('item_id','=',$commend->num_iid)->find();
                        $v->money = $smoney->money?$smoney->money:'0.00';
                }else{

                }
                $min_profit = $v->price-$v->money;
              }
              ?>
              <a href="/qwtdld/shareopenid/<?=base64_encode($result['openid'])?>/<?=$v->id?>/<?=$bid?>" class="weui_media_box weui_media_appmsg" style="padding:10px;">
                    <div class="weui_media_hd" style="height:100px;width:100px;">
                        <img class="weui_media_appmsg_thumb" src="<?=$v->pic?>" alt="" style="border-radius:5px;">
                    </div>
                    <div class="weui_media_bd" style="height:100px;font-family:Microsoft YaHei;">
                        <h4 class="weui_media_title" style="white-space:normal;line-height:20px;display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 2;overflow: hidden;color:#3f3f3f;margin-top:5px;height:40px;font-size:18ox"><?=$v->title?></h4>
                        <p class="weui_media_desc" style="color:#FF3030;font-size:15px;margin-top:10px;;"><span style="color:#9b9b9b;">售价：</span>￥<?=$v->price?><span style="color:#9b9b9b;margin-left:20px;">利润：￥<?=number_format($min_profit,2)?></span></p>
                        <p class="weui_media_desc" style="color:#9b9b9b;font-size:15px;margin-top:5px;">库存：<?=$v->num?></p>
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
