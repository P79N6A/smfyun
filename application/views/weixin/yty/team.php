<?php
$flag=0;
$maxs=ORM::factory('yty_sku')->where('bid','=',$bid)->order_by('order', 'DESC')->find()->order;
$mys = ORM::factory('yty_qrcode')->where('bid', '=', $bid)->where('openid','=',$user['openid'])->find()->s;

if($mys>=$maxs)
{
    $flag=1;
}
?>

<style>
    #rankpage{
      position: relative;
      width: 100%;
      /*max-width: 1170px;*/
      font-size: 14px;
      font-size: 0.875rem;
      margin-top:10px;
        }
    .block-item{
      padding:3px;
    }
    #ranking {
        /*width: 90%;*/
        left: 5%;
        top: 10%;
        background-color: #fff;
        border-radius: 5px;
        /*padding: 30px 15px;*/
    }
    #ranking_list {
        display: block;
        width: 100%;
        /*height: 100%;*/
        overflow: hidden;
        overflow-y:auto;
    }

    #ranking_list::-webkit-scrollbar-track {
     -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,0.3);
     background-color: #F5F5F5;
     border-radius: 10px;
    }
     #ranking_list::-webkit-scrollbar {
     width: 5px;
     background-color: #F5F5F5;
    }
     #ranking_list::-webkit-scrollbar-thumb {
     border-radius: 10px;
     background-image: -webkit-gradient(linear,  left bottom,  left top,  color-stop(0.44, rgb(122,153,217)),  color-stop(0.72, rgb(73,125,189)),  color-stop(0.86, rgb(28,58,148)));
    }

    #ranking_title, #play_game, #return_back {
        display: block;
        height: 40px;
        line-height: 40px;
        text-align: center;
        color: #fff;
        font-size: 18px;
        border-radius: 4px;
        position: absolute;
    }

    #ranking_title {
        width: 50%;
        left: 25%;
        top: -21px;
        background-color: #06bf04;
    }

    .box {
        width: 100%;
        /*background-color: #eee;*/
        height: 50px;
        line-height: 50px;
        display: -webkit-box;
        display: -moz-box;
        display: box;
        /*border-radius: 10px;*/
        /*margin:5px auto 5px;*/
    }
    .cur{background-color: #dd5964;}
    .col_1 {
        width: 50px;
        text-align: center;
        font-size:20px;
        color:#06bf04;
        background-repeat:no-repeat; background-position:center center; background-size:40px 40px;
    }

    .col_1[title="1"]{ color:#fff; background-image:url("/wdy/front/images/r1.png"); line-height:38px;}
    .col_1[title="2"]{ color:#fff; background-image:url("/wdy/front/images/r2.png"); line-height:38px;}
    .col_1[title="3"]{ color:#fff;  background-image:url("/wdy/front/images/r3.png");line-height:38px;}
    .col_2 {
        -webkit-box-flex: 1;
        -moz-box-flex: 1;
        box-flex: 1;
        /*padding: 5px 0;*/
        color: #06bf04;
        font-size: 14px;
        text-align:center;
    }
    .col_2 img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    .col_3 {
        width:35%;
        /*color:#f9820d;*/
        font-size:14px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        text-align:left;
    }
    .col_4 {
        width:25%;
        text-align:center;
        color:#06bf04;
        font-size:14px;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }
    .cur section{color: #fff;}
    .menu2{
      width: 100%;
    font-size: 1.1em;
    text-align: center;
    /* width: 100%; */
    display: inline-block;
    }
    .mask{
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.63);
      text-align: center;
      position: absolute;
      z-index: 100;
    }
    .ccontent{
        position: absolute;
    top: 41%;
    width: 80%;
    margin-left: 10%;
    height: 100px;
    border-radius: 20px;
    padding: 20px 0px;
    line-height: 100px;
    font-size: 18px;
    /* background: rgba(255, 255, 255, 0.73); */
    color: white;
    }
</style>
<?php
$lv_name=ORM::factory('yty_qrcode')->where('id','=',$user['id'])->find()->agent->skus->name;
?>
<?php if($member==0):
?>
<div class='mask'>
  <div class="ccontent">转发此页面给你的代理商完成注册</div>
</div>
<?php endif;?>
<div class="block" style="margin-top:0px;">
    <div class="name-card name-card-directseller clearfix" style="padding:20px">
        <a class="thumb"><img src="<?=$user['headimgurl']?>"></a>
        <div class="detail">
            <p class="font-size-16" style="color:#000"><?=$user['nickname']?></p>
            <p class="font-size-14"><?=$lv_name?></p>
        </div>
    </div>
</div>

<div class="block" >
        <div class="ui two border-bottom overview">
            <div class="item">
               <a href="/yty/team">
                <div class="value ellipsis">
                    <span class="font-size-28 c-green"><?=(int)$result['num2']?></span>
                    <span class="corner">个</span>
                </div>
                <div class="label ellipsis">下级经销商申请请求</div>
              </a>
            </div>
            <div class="item">
            <a href="/yty/team?member=1">
                <div class="value ellipsis">
                    <span class="font-size-28 c-green"><?=(int)$result['num1']?></span>
                    <span class="corner">个</span>
                </div>
                <div class="label ellipsis">我的下级经销商</div>
            </a>
            </div>
        </div>
    </a>
</div>
<div class="row">
<div class="col-lg-8">
  <div class="form-group">
  <?php if($flag==0):?>
    <label for="menu2" class='menu2'>转发此页面给你的代理商，即可完成邀请</label>
    <!--div for="menu2"><?//='http://'.$_SERVER["HTTP_HOST"].'/yty/index_oauth/'.$bid.'/'.base64_encode($user['openid']).'/form'?></div-->
  <?php endif?>
  </div>
</div>
</div>
<div id="rankpage">
    <section id="ranking">
        <section id="ranking_list">

         <section class="box block-item">
                <section class="col_4">排名</section>
                <section class="col_4">头像</section>
                <section class="col_4">昵称</section>
                <section class="col_4">金额</section>
                <?php if($member==0):?>
                <section class="col_4">操作</section>
              <?php endif;?>
        </section>
        <?php
        foreach ($qrcode as $qrc):
        $rank++;
        ?>
                <section class="box block-item">
                  <section class="col_4"<?=$rank<=3 ? ' title="1"' : ''?>><?=$rank?></section>
                  <section class="col_2"><img src="<?=$qrc->headimgurl?>" /></section>
                  <section class="col_4"><?=$qrc->nickname?></section>
                  <section class="col_4">&yen;<?=$qrc->agent->money?></section>
                  <?php if($member==0):?>
                  <section class="col_4"><button class="btn btn-danger shenhe" data-id='<?=$qrc->id?>' data-lv='1' >审核</button></section>
                   <?php endif;?>
                </section>
        <?php endforeach?>

        </section>
    </section>
</div>
 <div class="box-footer clearfix">
                <?=$page?>
  </div>
    <footer class="page-footer fixed-footer">
    <ul style="height: 0px">
      <li>
        <a href="/yty/home">
          <img src="/yty/images/footer002.png"/>
          <p style="font-size: 14px;line-height:14px;">个人中心</p>
        </a>
      </li>

      <li >
        <a href="<?='http://'.$_SERVER["HTTP_HOST"].'/yty/storefuop/'.$bid?>">
          <img src="/yty/images/footer004.png"/>
          <p style="font-size: 14px;line-height:14px;">推荐商品</p>
        </a>
      </li>
    </ul>
  </footer>
<?php
$team_title=sprintf($config['team_title'],$user['nickname']);
$tpl = ORM::factory('yty_cfg')->where('bid', '=', $bid)->where('key', '=', 'tplhead')->find()->id;
$news = array(
    "fTitle" =>$team_title,
    "fDescription"=>$config['team_desc'],
    "fPicUrl" =>"http://".$_SERVER['HTTP_HOST']."/yty/images/cfg/".$tpl.".v".time().".jpg",//logo
    "Title"=>$team_title,
    "PicUrl" =>"http://".$_SERVER['HTTP_HOST']."/yty/images/cfg/".$tpl.".v".time().".jpg",//logo
    "llink"=>'http://'.$_SERVER["HTTP_HOST"].'/yty/index_oauth/'.$bid.'/'.base64_encode($user['openid']).'/form',
    );
 ?>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script>

window.flag=<?php echo $flag ?>;
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
        //'getLocation',
        'onMenuShareTimeline',
        'onMenuShareAppMessage',
        'hideMenuItems'
      ],
      success: function (res) {

      }
    });

    wx.onMenuShareAppMessage({//好友
      title: '<?php echo $news['fTitle'];?>',
      desc: '<?php echo $news['fDescription'];?>',
      //link: "<?='http://'.$_SERVER["HTTP_HOST"].'/yty/index_oauth/'.$bid.'/'.base64_encode($user['openid']).'/form'?>",
      link:'<?php echo $news['llink']?>',
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
      link: 'http://jfb.dev.smfyun.com/yty/',
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
    //alert(flag);
    if(window.flag==1)
    {
        wx.hideMenuItems({
        menuList: [
        "menuItem:share:appMessage",
        "menuItem:share:timeline",
        "menuItem:share:qq",
        "menuItem:share:weiboApp",
        "menuItem:favorite",
        "menuItem:share:facebook",
        "menuItem:share:QZone" ,
        'menuItem:copyUrl' // 复制链接

        ],
        success: function (res) {
           // alert('已隐藏“阅读模式”，“分享到朋友圈”，“复制链接”等按钮');
          },
        fail: function (res) {
           // alert(JSON.stringify(res));
          } // 要隐藏的菜单项，只能隐藏“传播类”和“保护类”按钮，所有menu项见附录3

        });
    }



  });

  wx.error(function (res) {
    alert(res.errMsg);
  });

</script>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf8'>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
 <title>alert</title>
 <link rel="stylesheet" type="text/css" href="/yty/css/sweetalert.css">
</head>
<style type="text/css">
/* .btn{
  display: none;
 }*/
</style>
<body>
</body>
<script type="text/javascript" src="/yty/js/sweetalert.min.js"></script>
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript">
$('.mask').click(function(event) {
  $('.mask').css({
    'display': 'none'
  });
});
$('.shenhe').click(function(){
    console.log($(this).data('id'));
    console.log($(this).data('lv'));
    var that = this;
    sweetAlert({
      title: "确定?",
      text: "审核一旦通过就不可撤回!",
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "确定",
      closeOnConfirm: false
    }, function(){
    var data = {
        id:$(that).data('id'),
        lv:$(that).data('lv')
    }
    $.ajax({
      url: '/yty/team',
      type: 'post',
      dataType: 'json',
      data: {data: data},
    })
    .done(function(res) {
      if(res.flag=='success'){
        swal('成功',res.echo,'success');
      }else{
        swal('失败',res.echo,'error');
      }
      swal({
        title: "提示",
        text: res.echo,
        type: res.flag,
        closeOnConfirm: false,
        }, function() {
            window.location.reload();
        });

    })
    .fail(function() {
      console.log("error");
    })
    .always(function() {
      console.log("complete");
    });
      // swal("Deleted!",
      // "Your imaginary file has been deleted.",
      // "success");
    });
})

</script>
</html>
<link rel="stylesheet" type="text/css" href="/yty/css/loaders.min.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/loading.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/base.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/style.css"/>
