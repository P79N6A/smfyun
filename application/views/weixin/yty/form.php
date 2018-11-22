<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>云商申请</title>
    <link rel="stylesheet" href="/qfx/weiui/css/weui.css"/>
    <link rel="stylesheet" href="/qfx/weiui/css/example.css"/>
</head>
<?php if($result['lv']==0&&!$result['content']):?>
<form method="post" onsubmit="return check();">
<div class="weui_cells_title">请真实的填写以下信息</div>
<div class="weui_cells weui_cells_form">
    <div class="weui_cell">
        <div class="weui_cell_hd"><label class="weui_label">姓名：</label></div>
        <div class="weui_cell_bd weui_cell_primary">
            <input name="form[name]" class="weui_input form2" type="text" maxlength="10" placeholder="请输入姓名"/>
        </div>
    </div>

    <div class="weui_cell">
        <div class="weui_cell_hd"><label class="weui_label">手机号：</label></div>
        <div class="weui_cell_bd weui_cell_primary">
            <input name="form[tel]" class="weui_input form1" type="tel" maxlength="12" placeholder="请输入手机号"/>
        </div>
    </div>

    <div class="weui_cell">
        <div class="weui_cell_hd"><label class="weui_label">身份证号：</label></div>
        <div class="weui_cell_bd weui_cell_primary">
            <input name="form[id_card]" class="weui_textarea form3" placeholder="您的身份证号"></input>
        </div>
    </div>

    <div class="weui_cell">
        <div class="weui_cell_hd"><label class="weui_label">地址：</label></div>
        <div class="weui_cell_bd weui_cell_primary">
            <input name="form[address]" class="weui_textarea form4" placeholder="您的联系地址"></input>
        </div>
    </div>
    <div class="weui_cell">
    <div class="weui_cell_hd"><label class="weui_label">申请的经销商等级：</label></div>
    <div class="weui_cell_bd weui_cell_primary">
        <input name="form[sid]" type="hidden" value="<?=$skus->id?>"/>
        <input class="weui_input form1" value="<?=$skus->name?>" readonly="true" type='hidden'/>
        <span class="weui_input form1"  style='display:inline-block;'><?=$skus->name?></span>
    </div>
    </div>

     <div class="weui_cell">
     <div class="weui_cell_hd"><label class="weui_label">需缴纳的代理金：</label></div>
    <div class="weui_cell_bd weui_cell_primary">
        <input name="form[money]" class="weui_input form1" placeholder="" readonly="true" value='<?=$skus->money?>'type="hidden"/>
        <span class="weui_input form1" style='display:inline-block;'><?=$skus->money?></span>
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
        if(!$('.form1').val()||!$('.form2').val()||!$('.form3').val()||!$('.form4').val()){
            alert('请务必填写完整哟');
            return false;
        }else{
            return true;
        }
    }
</script>
<?php endif?>
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
            <h2 class="weui_msg_title">审核中</h2>
            <p class="weui_msg_desc"><?=$result['content']?></p>
            <img style="width:50%;" src="/yty/images/cfg/<?=$tpl?>.v<?=time()?>.jpg">
            <p class="weui_msg_desc">长按识别二维码<br>及时接收审核信息</p>
        </div>
    </div>
</div>
<?php endif?>
<?php if($result['lv']==3):?>
<div class="page"><!-- 被取消 -->
    <div class="weui_msg">
        <div class="weui_icon_area"><i class="weui_icon_msg weui_icon_warn"></i></div>
        <div class="weui_text_area">
            <h2 class="weui_msg_title">很遗憾</h2>
            <p class="weui_msg_desc"><?=$result['content']?></p>
        </div>
    </div>
</div>
<?php endif?>
