<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<title><?=$config['name']?>派红包</title>

<style>
* {
    margin: 0;
    padding: 0;

}
body {
    width: 100%;
    background: #e03030;
}

#top {
    height: 240px;
    overflow: hidden;
}

#top img {
    width: 100%;
}

#desc {
    width: 100%;
    background: url(/_img/weixin/hongbao/bg2.png) 0 140px no-repeat;
    background-size: 100%;
    height: 370px;
}

#tip {
    width: 280px;
    margin: 0 auto;
    background: #fff;
    border-radius: 5px;
    padding: 10px;
    box-shadow: 0 3px 8px #333;
}
#tip h3 {
    color: #9c0007;
    font-size: 15px;
    margin-bottom: 5px;
}
#tip p {
    font-size: 13px;
}

#act-rules {
    width: 280px;
    margin: 0 auto;
    /*background: rgba(255,255,255,0.3);*/
    border-radius: 5px;
    padding: 15px 10px 5px;
}
#act-rules dt {
    color: #9c0007;
    font-size: 14px;
    margin-bottom: 10px;
    font-weight: bold;
}
#act-rules dd {
    font-size: 12px;
    width: 240px;
    margin: 0 auto 10px;
}
#action {
    /*width: 100%;*/
    background: rgba(119, 11, 11, 0.7);
    padding: 8px 10px;
    margin: 20px 0 10px 0;
    text-align: center;
    /*position: fixed;bottom: 0;*/
}
#action a {
    display: inline-block;
    width: 180px;
    height: 35px;
    line-height: 37px;
    background: #f9c86f;
    font-size:16px;
    color: #9c0007;
    text-align: center;
    border: 1px solid #f9c86f;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    text-decoration: none;
}
#action a:last-child {
/*      float: right;   */
}

#share {
    width:300px;
    position:absolute;
    top:20px;
    right:10px;
    background:#fff;
    border-radius:10px;
    display:none;
}
#share:after {
    content:"";
    width:0;
    height:0;
    border-width:0px 10px 18px 10px;
    border-style:solid;
    border-color:#fff transparent;
    position:absolute;
    right:10px;
    top:-18px;
    display:block;
}
#share p {
    margin:10px;
}

.topa {
    position: absolute;
    top: 150px;
    left: 150px;
    border:1px solid #FFF;
    width: 68px !important;
    height: 68px !important;
}

.avatar {
    display: block;
    margin: 0 auto;
    border-radius: 50%;
    width: 32px;
    height: 32px;
}

/* Friend List */
.friend-content {
    /*display: none;*/
    background: #eeeff0;
    padding: 20px;
    margin: 0 auto;
}

.friend-content .friend-luck {
    font-size:14px;
    background-color:#ccc;
    height:1px;
    text-align:center;
    margin-bottom:20px;
}

.friend-content .friend-luck span {
    position:relative;
    background-color:#eeeff0;
    top:-10px;
    padding:0 10px;

}

.friend-content li {
    line-height:16px;
    text-align:left;
    list-style:none;
    color:#999;
    font-size:12px;
    padding:8px 0;
    border-bottom:1px solid #e5e5e5;
}

.friend-content li .head-img {
    float:left;
    text-align:center;
}

.friend-content li .friend-info-box {
    margin-left:60px;
    line-height:25px;
}

.friend-content li .friend-info-box .friend-basic {
    position:relative;
}

.friend-content li .friend-info-box .friend-name{
    font-size:14px;
    font-weight:bold;
    color:#333;
}

.friend-content li .friend-info-box .datetime{
    font-size:12px;
}

.friend-content li .friend-info-box  .amount {
    position:absolute;
    right:0;
    width:25%;
    color:#ff8a01;
    text-align:right;
}

.friend-content li .friend-info-box  .status.used{
    background-position:0 -45px;
}

.friend-content li .friend-info-box .friend-comment{
    font-size:14px;
}

.friend-content li:last-child {
    border-bottom:none;
}
</style>

</head>

<?php
if ($result['error'] == 1) {

    if ($result['have'] == 1) {
        $title = "{$user['nickname']} 您已成功领取过！";
        $desc = '我们还为你的朋友准备了红包，快发给他们吧！';
    }

    if ($result['limit'] == 1) {
        $title = "{$user['nickname']} 今天的红包已经发完了！";
        $desc = '请明天再来继续抢吧。';
    }

    if ($result['needfollow'] == 1) {
        $title = "您的好友{$fuser['nickname']}邀请您参与「{$config['name']}」红包派送活动！";
        // $desc = '你还可以邀请你的好友一起抢现金红包，成功邀请好友参与，你还将获得额外红包。';
        $needfollow = 1;
    }

} else {

    if ($result['success'] == 1) {
        $title = "{$user['nickname']} 恭喜您成功领取！";
        $desc = '我们还为你准备了 5 个红包，快发给朋友们吧！';
        $desc = '我们还为你的朋友准备了红包，快发给他们吧！';
    } else {
        $title = "{$user['nickname']} 哇哦，出错了！";

        if ($result['return_msg'] == '权限错误:不能在0点到8点间发红包.') $result['return_msg'] = '0-8 点不能领红包，天亮了再试吧，晚安！';
        if ($result['return_msg'] == '帐号余额不足，请用户充值或更换支付卡后再支付.') $result['return_msg'] = '红包已经被领完了，过会再来碰碰运气吧！';

        $desc = $result['return_msg'];
    }

}
?>

<body>

    <div id="top"><img src="<?=$config['img_top'] ?: '/_img/weixin/hongbao/top0.jpg'?>" /></div>

    <?php if($user['headimgurl']):?><img src="<?=$user['headimgurl']?>" class="topa avatar" /><?php endif?>

    <div id="desc">
        <div id="tip">
            <div>
                <h3><?=$title?></h3>
                <p><?=$desc?></p>
            </div>
        </div>

        <div id="action">
            <?php if ($needfollow == 1):?>
                <a href="<?=$config['follow_url']?>">一键抢红包</a>
            <?php else:?>
                <a onclick="callFriends();">发红包给好友</a>
            <?php endif?>
        </div>

        <div id="act-rules">
            <dl>
                <dt>活动规则：</dt>
                <dd>1.点击上方的按钮，即可抢现金红包！</dd>
                <!-- <dd>2.点击右上角分享给朋友们，给你的朋友们发红包啦！</dd> -->
                <dd>2.红包有限期三天，未领取会自动收回。</dd>
            </dl>
        </div>
    </div>

    <?php if($result['hbs']):?>
    <div class="friend-content">
        <div class="friend-luck">
            <span>壕，他们领了你的红包</span>
        </div>

        <ul>
        <?php
        foreach($result['hbs'] as $ffb):
            if (!$ffb->headimgurl) continue;
        ?>
            <li class="clearfix">
                <div class="head-img"><img src="<?=$ffb->headimgurl?>" class="avatar" /></div>

                <div class="friend-info-box">
                    <div class="friend-basic">
                        <span class="friend-name"><?=$ffb->nickname?></span>
                        <span class="datetimea"><?=date('m-d H:i', $ffb->lastupdate)?></span>
                        <span class="amount">已领取</span>
                    </div>
                    <span class="friend-comment">谢谢壕！</span>
                </div>
            </li>
        <?php endforeach?>
        </ul>
    </div>
    <?php endif?>

    <div id="share"><p>点击这里分享给你的朋友们，他们也可以领红包啦！</p></div>

<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<script>
    function callFriends(){
        $('#share').fadeIn();
        setTimeout(function(){
            $('#share').fadeOut();
        }, 5000);
    }
</script>

<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>

<?php
$share_url = "http://{$_SERVER["HTTP_HOST"]}/weixin/hongbao/{$bid}?fopenid={$user['openid']}";
//$share_url = shorturl($share_url);

//短网址
function shorturl($url) {
    $api = 'http://api.weibo.com/2/short_url/shorten.json?url_long=%s&source=202714284';
    $rs = file_get_contents(sprintf($api, urlencode($url)));

    if ($rs = json_decode($rs)) {
        $url = $rs->urls[0]->url_short;
    }

    return $url;
}

$share_url2 = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$config['appid']}&redirect_uri=". urlencode($share_url) ."&response_type=code&scope=snsapi_base&state=123&connect_redirect=1#wechat_redirect";
?>

<script>
var title = '<?=$user['nickname'] ?: '我'?>给你发了一个红包';
var link = '<?//=shorturl($share_url2)?>';
var link = '<?=$share_url?>';
var img = 'http://<?=$_SERVER["HTTP_HOST"]?>/_img/weixin/hongbao/icon.jpg';
var desc = '我给你发了一个红包，赶紧去拆！祝：恭喜发财，大吉大利！';

// alert(link);

wx.config({
    debug: false,
    appId: '<?=$jsapi['appid']?>', // 必填，公众号的唯一标识
    timestamp: <?=$jsapi['timestamp']?>, // 必填，生成签名的时间戳
    nonceStr: '<?=$jsapi['noncestr']?>', // 必填，生成签名的随机串
    signature: '<?=$jsapi['signature']?>',// 必填，签名，见附录1
    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
});

wx.ready(function(){
    //分享到朋友圈
    wx.onMenuShareTimeline({
        title: '<?=$user['nickname']?>邀请您来<?=$config['name']?>领现金红包！', // 分享标题
        link: link, // 分享链接
        imgUrl: img, // 分享图标
        success: function () {},
        cancel: function () {}
    });

    //发送给朋友
    wx.onMenuShareAppMessage({
        title: title, // 分享标题
        desc: desc, // 分享描述
        link: link, // 分享链接
        imgUrl: img, // 分享图标
        type: 'link', // 分享类型,music、video或link，不填默认为link
        dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
        success: function () {},
        cancel: function () {}
    });
});
</script>

</body>
</html>