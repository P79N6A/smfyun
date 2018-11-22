<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>结算账户设置</title>
    <link rel="stylesheet" href="/qfx/weiui/css/weui.css"/>
    <link rel="stylesheet" href="/qfx/weiui/css/example.css"/>
</head>
<form action="" method="post" onsubmit="check()">
<div class="weui_cells_title">请真实填写（确保以下信息作为有效的收款账户！）</div>
<div class="weui_cells weui_cells_form">

    <!-- <div class="weui_cell">
        <div class="weui_cell_hd"><label class="weui_label">手机号</label></div>
        <div class="weui_cell_bd weui_cell_primary">
            <input name="form[tel]" class="weui_input form1" type="tel" maxlength="12" placeholder="请输入手机号" value="<?=$user->tel?>" />
        </div>
    </div> -->

    <div class="weui_cell">
        <div class="weui_cell_hd"><label class="weui_label">姓名</label></div>
        <div class="weui_cell_bd weui_cell_primary">
            <input name="form[name]" class="weui_input form2" type="text" maxlength="10" placeholder="请输入姓名" value="<?=$user->name?>" />
        </div>
    </div>
    <div class="weui_cell">
    <div class="weui_cell_bd weui_cell_primary">
        <div class="weui_cell_hd"><label class="weui_label">支付宝账号</label></div>
        <input name="form[zfb]" class="weui_textarea form3" placeholder="您的支付宝账号" value="<?=$user->alipay_name?>" />
    </div>
    </div>
</div>
<div class="weui_cells_tips"></div>
<div class="weui_btn_area">
    <button class="weui_btn weui_btn_primary" type="submit">确定</button>
</div>
</form>
<script src="//cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<script type="text/javascript">
    function check(){
        if(!$('.form2').val()||!$('.form3').val()){
            alert('请务必填写完整哟');
        }
    }
</script>
