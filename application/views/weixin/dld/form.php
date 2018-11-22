<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>代理申请</title>
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
            <form method="post" action="">
            <div class="weui-cells weui-cells_form" style="margin-left:-20px;margin-right:-20px;">
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">手机号</label></div>
                <div class="weui-cell__bd">
                    <input class="weui-input" name="tel" type="number" pattern="[0-9]*" placeholder="请输入手机号">
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
