<!DOCTYPE html>
<html lang="en">

<link rel="stylesheet" type="text/css" href="/yty/css/loaders.min.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/loading.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/base.css"/>
<link rel="stylesheet" type="text/css" href="/yty/css/style.css"/>
<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>进货申请</title>
    <link rel="stylesheet" href="/qfx/weiui/css/weui.css"/>
    <link rel="stylesheet" href="/qfx/weiui/css/example.css"/>
</head>
<form action="" method="post" onsubmit="check()">
<div class="weui_cells_title">需要申请的进货金额（单位：元；请填写数字）</div>
<div class="weui_cells weui_cells_form" style="width:90%;margin-left:5%;">

    <div class="weui_cell">
    <div class="weui_cell_bd weui_cell_primary">
        <textarea name="form[money]" class="weui_textarea form4" placeholder=""></textarea>
    </div>
    </div>
</div>
<div class="weui_cells_tips"></div>
<div class="weui_btn_area">
    <button class="weui_btn weui_btn_primary" type="submit">确定</button>
</div>
</form>
    <footer class="page-footer fixed-footer">
        <ul>
            <li>
                <a href="/yty/home">
                    <img src="/yty/images/footer002.png"/>
                    <p>个人中心</p>
                </a>
            </li>

            <li >
                <a href="<?='http://'.$_SERVER["HTTP_HOST"].'/yty/storefuop/'.$bid?>">
                    <img src="/yty/images/footer004.png"/>
                    <p>推荐商品</p>
                </a>
            </li>
        </ul>
    </footer>
