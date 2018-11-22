<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>订阅直播间</title>
    <link rel="stylesheet" href="/dld/weiui/css/weui.css"/>
    <link rel="stylesheet" href="/dld/weiui/css/example.css"/>
    <link rel="stylesheet" href="/dld/weiui/css/weui1.css"/>
    <link rel="stylesheet" href="/dld/weiui/css/example1.css"/>
    <style type="text/css">
    .page{
        opacity: 1 !important;
    }
    </style>
</head>
<?php if($user->sub==1):?>
<div class="page"><!-- 申请成功 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_success weui_icon_msg"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">订阅成功</h2>
            <p class="weui_msg_desc"><?=$result['content']?></p>
            <form method="post">
            <input type="hidden" name="cancel" value="1">
            <button type="submit" style="margin-top:20px"  class="weui_btn weui_btn_plain_default">取消订阅</button>
            <a href="<?='http://'.$_SERVER["HTTP_HOST"].'/wzb/index_oauth/'.$bid.'/live'?>" style="margin-top:20px"  class="weui_btn weui_btn_plain_primary">点此可进入直播间</a>
          </form>
        </div>
    </div>
</div>
<?php endif?>
<?php if($user->sub==0):?>
<div class="page"><!-- 审核中 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_info"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">取消订阅成功</h2>
            <p class="weui_msg_desc"><?=$result['content']?></p>
            <a href="<?='http://'.$_SERVER["HTTP_HOST"].'/wzb/index_oauth/'.$bid.'/live'?>" style="margin-top:20px"  class="weui_btn weui_btn_plain_primary">点此可进入直播间</a>
        </div>
    </div>
</div>
<?php endif?>
