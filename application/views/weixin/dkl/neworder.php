<link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/wdy/front/css/ui.css?v<?=date('Ymd')?>">
<link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/wdy/front/css/style.css?v<?=date('Ymd')?>">
<script src="http://cdn.bootcss.com/jquery/2.0.1/jquery.js"></script>
<style>
form#newForm {
    line-height: 200%;
}

.profile_fun, .profile_hd {
    display: none;
}

.product-info input {
    width: 100%;
    font-size: 18px;
    clear: both;
    border: 1px solid #e6e7f1;
}

.product-info select {
    font-size: 18px;
    clear: both;
    border: 0;
    /*border-bottom: 2px solid #e6e7f1;*/
}

#citys {
    border: 1px solid #e6e7f1;
}

.product-info label {
    font-weight: bold;
    font-size: 16px;
    color: #369;
}
</style>
<form method="post" id="newForm">
<input type="hidden" name='csrf' value="<?=Security::token(true)?>">
<ul class="cd-gallery">

        <li>
            <div class="cd-single-item">
                <ul class="cd-slider-wrapper">
                    <li class="selected"><img src="http://<?=$_SERVER['HTTP_HOST']?>/dkl/images/item/<?=$item->id?>.v<?=$item->lastupdate?>.jpg" alt="<?=$item->name?>"></li>
                </ul>
            </div> <!-- .cd-single-item -->
            <div class="cd-item-info">
                <b><a href="/dkl/neworder/<?=$item->id?>"><?=$item->name?></a></b>
                <em><?=$item->score?> <?=$config['scorename']?></em>
            </div> <!-- cd-item-info -->
    <?php if($item->type==0):?>
        <div class="product-info">
            <label for="name">真实姓名：</label>
            <input id="name" maxlength="5" type="text" name="data[name]" value="<?=$_POST['data']['name']?>">

            <label for="tel">收货手机：</label>
            <input id="tel" maxlength="11" type="tel" name="data[tel]" value="<?=$_POST['data']['tel']?>">
        </div>
    <?php endif?>
            <div class="cd-customization">
            <? if($item->type!=0):?>
                <input style="display:none" id="memo" type="text" name="data[type]" maxlength="30" value="<?=$item->type?>">
                <button type="submit" class="add-to-cart">确认兑换</button>
            <? else:?>
                <input style="display:none" id="memo" type="text" name="data[type]" maxlength="30" value="<?=$item->type?>">
                <button type="submit" class="add-to-cart">提交登记</button>
             <?endif;?>
            </div> <!-- .cd-customization -->
        </li>
</ul>

</form>

<script src="http://cdn.jfb.smfyun.com/wdy/plugins/citySelect/jquery.cityselect.js"></script>

<?php
if ($_POST['data']['city']) {
    list($prov, $city, $dist) = explode(' ', $_POST['data']['city']);
}
?>

<script>
$(function(){
    var type =<?$item->type?>;
    $('#newForm').submit(function() {
        if(type==0){
            if(!$('#name').val() || !$('#tel').val()){
                alert('请填写完整信息哦！');
                return false;
            }
            return true;
        }
      }
        return true;
    });
    //接受红包发送后返回数据
   var return_code=$hbresult['return_code'];
   var result_code=$hbresult['result_code'];
   if (return_code==result_code) {
    $("#ceshi").text("红包发送成功，请前往对话框查收！");
    $(".add-to-cart").remove();
   }
});
</script>
