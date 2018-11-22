<link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/wdy/front/css/ui.css?v<?=date('Ymd')?>">
<link rel="stylesheet" type="text/css" href="http://cdn.jfb.smfyun.com/wdy/front/css/style.css?v<?=date('Ymd')?>">
<script src="http://cdn.bootcss.com/jquery/2.0.1/jquery.js"></script>
<?php if($_SERVER['HTTP_HOST']=='jfb.dev.smfyun.com'):?>
<script type="text/javascript" src="https://www.w3cways.com/demo/vconsole/vconsole.min.js?v=2.2.0"></script>
<?php endif?>
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
.add{
    background-color: #fff;
    position: relative;
    height: 60px;
    border-bottom: 1px solid #f4f4f4;
}
.addimg{
    float: left;
    width: 40px;
    height: 40px;
    padding: 10px;
}
.addtext{
    line-height: 60px;
    color: #666666;
    font-size: 14px;
    float: left;
}
.goin{
    float: right;
    width: 20px;
    height: 20px;
    padding: 20px;
}
.edit{
    background-color: #fff;
    position: relative;
    border-bottom: 1px solid #f4f4f4;
}
.position{
    width: 4%;
    height: 15px;
    padding-left: 3%;
    padding-right: 2%;
    padding-top: 15px;
    display: inline-block;
    float: left;
}
.address{
    display: inline-block;
    width: 84%;
    padding: 10px 0 10px 0;
}
.basic{
    font-size: 14px;
    color: #333;
}
.phone{
    float: right;
}
.location{
    margin-top: 10px;
    font-size: 12px;
    color: #999;
}
.goin2{
    float: right;
    width: 3%;
    height: 10px;
    padding-left: 1%;
    padding-right: 2%;
    padding-top: 30px;
}

</style>
<form method="post" id="newForm">
<input type="hidden" name='csrf' value="<?=Security::token(true)?>">
<ul class="cd-gallery">

        <li>
            <div class="cd-single-item">
                <ul class="cd-slider-wrapper">
                    <li class="selected"><img src="<?=$config['cdn']?>/qwtwfb/images/item/<?=$item->id?>.v<?=$item->lastupdate?>.jpg" alt="<?=$item->name?>"></li>
                </ul>
            </div> <!-- .cd-single-item -->
            <div class="cd-item-info">
                <b><a href="/qwtwfb/neworder/<?=$item->id?>"><?=$item->name?></a></b>
                <em><?=$item->score?> <?=$config['score2']?><?=$item->need_money>0?'+'.($item->need_money/100).'元':''?></em>
            </div> <!-- cd-item-info -->


    <?php
    //虚拟产品
    if($item->url&&$item->type!=5&&$item->type!=6)://虚拟 卡券
    ?>
        <input id="url" type="hidden" name="url" value="1" />

    <?php elseif(!$item->type||$item->type==0):?>

        <div class="product-info">
        <?php if ($bid):?>
            <div class="edit" style="display:none">
                <image class="position" src="/qwt/wfb/position.png"></image>
                <div class="address">
                    <div class="basic">
                        <text class="name">收货人：</text>
                        <text class="phone">电话：</text>
                    </div>
                    <div class="location">收货地址：</div>
                </div>
                <image class="goin2" src="/qwt/wfb/goin.png"></image>
            </div>
            <div class="add">
                <image class="addimg" src="/qwt/wfb/add.png"></image>
                <div class="addtext">点击添加地址</div>
                <image class="goin" src="/qwt/wfb/goin.png"></image>
            </div>
            <input id="name"  type="hidden" name="data[name]" >
            <input id="tel" type="hidden" name="data[tel]" >
            <input id="prov" class='prov' type="hidden" name="s_province" >
            <input id="city" class='city' type="hidden" name="s_city" >
            <input id="dist" class='dist' type="hidden" name="s_dist">
            <input id="address" type="hidden" name="data[address]">
        <?php else:?>
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
        <?php endif?>
            <label for="memo">其它留言：</label>
            <input class="true_memo" id="memo" type="text" name="data[memo]" maxlength="30">
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
            <? if($item->type==1):?>
                <input  id="memo" type="hidden" name="data[type]" maxlength="30" value='1'>
                <button type="submit" class="add-to-cart">确认兑换</button>
            <? elseif($item->type==4):?>
                <input  id="memo" type="hidden" name="data[type]" maxlength="30" value='4'>
                <button type="submit" class="add-to-cart">确认兑换</button>
            <? elseif($item->type==5):?>
                <input  id="memo" type="hidden" name="data[type]" maxlength="30" value='5'>
                <button type="submit" class="add-to-cart">确认兑换</button>
            <? elseif($item->type==6):?>
                <input  id="memo" type="hidden" name="data[type]" maxlength="30" value='6'>
                <button type="submit" class="add-to-cart">确认兑换</button>
            <? else:?>
                <button type="submit" class="add-to-cart">提交登记</button>
             <?endif;?>
            </div> <!-- .cd-customization -->
        </li>

</ul>

</form>
<!-- <script type="text/javascript" src="https://www.w3cways.com/demo/vconsole/vconsole.min.js?v=2.2.0"></script> -->
<script src="http://cdn.jfb.smfyun.com/wdy/plugins/citySelect/jquery.cityselect.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
<?php
if ($_POST['data']['city']) {
    list($prov, $city, $dist) = explode(' ', $_POST['data']['city']);
}
?>

<script>
$(function(){
    var need_pay = parseInt(<?=$item->need_money?$item->need_money:0?>);
    var iid = '<?=$item->id?$item->id:0?>';
    wx.config({
        debug: 0,
        appId: '<?php echo $jsapi["appId"];?>',
        timestamp: '<?php echo $jsapi["timestamp"];?>',
        nonceStr: '<?php echo $jsapi["nonceStr"];?>',
        signature: '<?php echo $jsapi["signature"];?>',
        jsApiList: [
          // 所有要调用的 API 都要加到这个列表中
          'checkJsApi',
          'chooseWXPay',
          'openAddress'
          ]
    });
    var type = <?=$item->type?>;
    $("#citys").citySelect({
        required: false,
        prov: "<?=$prov?>",
        city: "<?=$city?>",
        dist: "<?=$dist?>"
    });
    $('.add,.edit').click(function() {
        wx.openAddress({
            success: function (res) {
                $('#name').val(res.userName);
                $('#tel').val(res.telNumber);
                $('.prov').val(res.provinceName);
                $('.city').val(res.cityName);
                $('.dist').val(res.countryName);
                $('#address').val(res.detailInfo);
                var userName = res.userName; // 收货人姓名
                // var postalCode = res.postalCode; // 邮编
                var provinceName = res.provinceName; // 国标收货地址第一级地址（省）
                var cityName = res.cityName; // 国标收货地址第二级地址（市）
                var countryName = res.countryName; // 国标收货地址第三级地址（国家）
                var detailInfo = res.detailInfo; // 详细收货地址信息
                // var nationalCode = res.nationalCode; // 收货地址国家码
                var telNumber = res.telNumber; // 收货人手机号码
                if(userName&&provinceName&&cityName&&detailInfo&&telNumber){
                    $('.name').text('收货人：'+userName);
                    $('.phone').text('电话：'+telNumber);
                    $('.location').text('地址：'+provinceName+cityName+countryName+detailInfo);
                    $('.add').css({
                        'display': 'none'
                    });
                    $('.edit').css({
                        'display': 'block'
                    });
                }
            },
            complete:function(res){
                console.log(JSON.stringify(res));
            }
        });
    });
    $('#newForm').submit(function() {
        //0实物 1卡券 2虚拟奖品 3话费 4红包 5优惠券 6赠品
        if(type == 1||type==2||type==4||type==5||type==6){
            return true;
        }
        if(type == 0){
            if ( (!$('#name').val() || !$('#tel').val() || !$('#address').val() || !$('.prov').val() || !$('.city').val()) && !$('#url').val()) {//实物
                alert('请填写完整收货信息哦！');
                return false;
            }else{
                if(need_pay>0){
                    $.ajax({
                        url: '/qwtwfb/wxpay',//支付时候插入订单
                        type: 'post',
                        dataType: 'json',
                        data: {
                            data:{
                                iid:iid,
                                name:$('#name').val(),
                                tel:$('#tel').val(),
                                city:$('.prov').val()+$('.city').val()+$('.dist').val(),
                                address:$('#address').val(),
                                memo:$('.true_memo').val()
                            }
                        },
                    })
                    .done(function(res) {
                        console.log(res);
                        console.log("success");
                        if(res.error){
                            alert(res.error);
                            return;
                        }else{
                            wx.chooseWXPay({
                                timestamp: res.timeStamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                                nonceStr: res.nonceStr, // 支付签名随机串，不长于 32 位
                                package: res.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=\*\*\*）
                                signType: res.signType, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                                paySign: res.paySign, // 支付签名
                                success: function (res2) {
                                    // 支付成功后的回调函数
                                    // a = 1
                                    // $.ajax({
                                    //     url: '/qwta/wait_order',
                                    //     type: 'post',
                                    //     dataType: 'json',
                                    //     data: {wait_id: res.wait_id},
                                    // })
                                    // .done(function() {
                                    //     console.log("success");

                                    //     var form = document.getElementById("newForm");
                                    //     form.submit();
                                    // })
                                    // .fail(function() {
                                    //     console.log("error");
                                    // })
                                    // .always(function() {
                                    //     console.log("complete");
                                    // });
                                    window.location.href = 'http://<?=$_SERVER['HTTP_HOST']?>/qwtwfb/orders';
                                }
                            });
                        }
                    })
                    .fail(function(res) {
                        console.log(JSON.stringify(res));
                        console.log("error");
                        return false;
                    })
                    .always(function() {
                        console.log("complete");
                        return false;
                    });
                    return false;
                }else{
                    return true;
                }
            }
            return false;
        }
        if(type == 3){
            if(!$('#name').val() || !$('#tel').val()){
                alert('请填写完整信息哦！');
                return false;
            }
            return true;
        }
      //  if(type!=4){//4 红包

      //       if(type==3){//话费
      //           if(!$('#name').val() || !$('#tel').val()){
      //               alert('请填写完整信息哦！');
      //               return false;
      //           }
      //           return true;
      //       }else if (type==5 || type==6) {//5 优惠券  6赠品
      //           return true;
      //       }else if ( (!$('#name').val() || !$('#tel').val() || !$('#address').val() || !$('.prov').val() || !$('.city').val()) && !$('#url').val()) {//实物
      //           alert('请填写完整收货信息哦！');
      //           return false;
      //       }
      // }
      //   return true;
    });
    //接受红包发送后返回数据
   // var return_code=$hbresult['return_code'];
   // var result_code=$hbresult['result_code'];
   // if (return_code==result_code) {
   //  $("#ceshi").text("红包发送成功，请前往对话框查收！");
   //  $(".add-to-cart").remove();
   // }
});
</script>
