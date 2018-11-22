<!DOCTYPE html>
<html>
    <head>
        <title>操作提示</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
        <link rel="stylesheet" type="text/css" href="http://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css">

        <style>
        @media all and (-webkit-min-device-pixel-ratio: 2) {
            .icon67_status {background-size:67px !important; }
            .icon80_smile{background-size:80px !important; }
        }
        </style>
    </head>

    <link rel="stylesheet" type="text/css" href="/yty/css/loaders.min.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/loading.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/base.css"/>
    <link rel="stylesheet" type="text/css" href="/yty/css/style.css"/>

    <body>

    <div class="page_msg">
        <div class="inner">
            <?php
            if ($type == 'suc') $class = 'icon67_status';
            if ($type == 'warn') $class = 'icon67_status warn';
            if ($type == 'noti') $class = 'icon80_smile';
            ?>
            <span class="msg_icon_wrp"><i class="<?=$class?>"></i></span>
            <div class="msg_content"><h4><?=$msg?></h4></div>
        </div>
    </div>

    </body>
</html>
