<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>绑定至门店账号</title>
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
<?php if (!$result['error']):?>
<div class="page"><!-- 绑定成功 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_success weui_icon_msg"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">绑定成功</h2>
            <p class="weui_msg_desc"></p>
        </div>
    </div>
</div>
<?php endif?>
<?php if($result['error']):?>
<div class="page"><!-- 被取消 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_warn"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">绑定失败</h2>
            <p class="weui_msg_desc"><?=$result['error']?></p>
        </div>
    </div>
</div>
<?php endif?>
