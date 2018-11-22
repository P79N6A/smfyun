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

<ul class="cd-gallery">

        <li>
            <div class="cd-single-item">
                <ul class="cd-slider-wrapper">
                    <li class="selected"><img src="<?=$config['cdn']?>/wfb/images/item/<?=$item->id?>.v<?=$item->lastupdate?>.jpg" alt="<?=$item->name?>"></li>
                </ul>
            </div> <!-- .cd-single-item -->
            <div class="cd-item-info">
                <b><a href="/wfb/neworder/<?=$item->id?>"><?=$item->name?></a></b>
                <em><?=$item->score?> <?=$config['score2']?></em>
            </div> <!-- cd-item-info -->


    <?php
    //虚拟产品
    if($item->url&&$item->type!=5):
    ?>
        <input id="url" type="hidden" name="url" value="1" />

    <?php elseif(!$item->type||$item->type==2):?>

        <div class="product-info">
            <label for="name">真实姓名：</label>
            <input id="name" maxlength="5" type="text" name="data[name]" value="<?=$_POST['data']['name']?>">

            <label for="tel">收货手机：</label>
            <input id="tel" maxlength="11" type="tel" name="data[tel]" value="<?=$_POST['data']['tel']?>">

            <label for="province">收货城市：</label><br />

            <div id="citys">
                <select class="prov" name="s_province"></select>
                <select class="city" disabled="" name="s_city"></select>
                <select class="dist" disabled="" name="s_dist"></select>
            </div>

            <label for="address">收货地址：</label>
            <input id="address" maxlength="30" type="text" name="data[address]" value="<?=$_POST['data']['address']?>">

            <label for="memo">其它留言：</label>
            <input id="memo" type="text" name="data[memo]" maxlength="30">
        </div>
    <? elseif($item->type==3):?>
        <div class="product-info">
            <label for="name">真实姓名：</label>
            <input id="name" maxlength="5" type="text" name="data[name]" value="<?=$_POST['data']['name']?>">

            <label for="tel">手机号：</label>
            <input id="tel" maxlength="11" type="tel" name="data[tel]" value="<?=$_POST['data']['tel']?>">

            <label for="memo">其它留言：</label>
            <input id="memo" type="text" name="data[memo]" maxlength="30">
        </div>
            <input style="display:none" id="memo" type="text" name="data[type]" maxlength="30" value='3'>
        </div>
   <!--  <? //elseif($item->type==4):?>
         <input style="display:none" id="memo" type="text" name="data[type]" maxlength="30" value='4'>
 -->

    <?php endif?>

            <div class="cd-customization">
            <? if($item->type==4):?>
                <input style="display:none" id="memo" type="text" name="data[type]" maxlength="30" value='4'>
                <button type="submit" class="add-to-cart">确认兑换</button>
            <? elseif($item->type==5):?>
                <input style="display:none" id="memo" type="text" name="data[type]" maxlength="30" value='5'>
                <button type="submit" class="add-to-cart">确认兑换</button>
            <? else:?>
                <button type="submit" class="add-to-cart">提交登记</button>
             <?endif;?>
            </div> <!-- .cd-customization -->
        </li>

</ul>

</form>

<script src="http://cdn.jfb.smfyun.com/wfb/plugins/citySelect/jquery.cityselect.js"></script>

<?php
if ($_POST['data']['city']) {
    list($prov, $city, $dist) = explode(' ', $_POST['data']['city']);
}
?>

<script>
$(function(){
    var type =<?if($item->type){echo $item->type;}else{echo 1;}?>;
    $("#citys").citySelect({
        required: false,
        prov: "<?=$prov?>",
        city: "<?=$city?>",
        dist: "<?=$dist?>"
    });

    $('#newForm').submit(function() {
        if(type!=4){

        if(type==3){
            if(!$('#name').val() || !$('#tel').val()){
                alert('请填写完整信息哦！');
                return false;
            }
            return true;
        }else if (type==5) {
            return true;
        }
        else if ( (!$('#name').val() || !$('#tel').val() || !$('#address').val() || !$('.prov').val() || !$('.city').val()) && !$('#url').val()) {
            alert('请填写完整收货信息哦！');
            return false;
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
