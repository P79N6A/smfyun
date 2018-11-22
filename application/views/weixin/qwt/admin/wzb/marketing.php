

    <style type="text/css">
    .am-selected-content{
        max-height: 180px;
        overflow: scroll;
    }
    .switch-content{
        overflow: -webkit-paged-y !important;
    }
    .hide{height: 0;}
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">


        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                首次进直播间送优惠券
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">神码云直播</a></li>
                <li>营销模块</li>
                <li class="am-active">首次进直播间送优惠券</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            首次进直播间送优惠券
                        </div>
                </div>
                <?php if ($result['ok3'] > 0):?>
                <div class="am-u-sm-12 am-u-md-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 保存成功！</p>
                            </div>
                        </div>
                </div>
                <?php endif?>
                <form role="form" method="post" enctype="multipart/form-data">
                <div class="am-u-sm-12 am-u-md-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 是否开启首次进入直播间赠送优惠券功能</p>
                            </div>
                        </div>
                </div>
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="actions">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$config['coupon'] == 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off" class="red <?=$config['coupon'] == 1 ? '' : 'red-on'?>">关闭</li>
                          <input type="hidden" name="text[coupon]" id="pshow1" value="<?=$config['coupon']?>">
                                </ul>
                            </div>
                </div>
                        <div class="am-u-sm-12 switch-content <?=$config['coupon'] == 1 ? '' : 'hide'?>" style="padding:0;">
                    <hr>
                                <div class="am-form-group">
                                    <label for="user-phone" class="am-u-sm-3 am-form-label">选择优惠券</label>
                                    <div class="am-u-sm-9">
                                        <select name="text[couponid]" data-am-selected="{searchBox: 1}">
                      <?php if($coupon['response']['coupons']){?>
                        <?php foreach ($coupon['response']['coupons'] as $coupon):?>
                            <option <?=$coupon['group_id']==$config['couponid']?'selected':''?> value="<?=$coupon['group_id']?>"><?=$coupon['title']?></option>
                        <?php endforeach;?>
                      <?php }?>
</select>
                                    </div>
                                </div>
                        <div class="am-u-sm-12" style="padding:0">
                        <hr>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-danger">保存</button>
                        </div>
                </div>
                </div>
                </form>
            </div>
        </div>

    </div>

    <script type="text/javascript">
<?php if($result['err3']):?>
$(document).ready(function(){
    swal({
        title: "失败",
        text: "<?=$result['err3']?>",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "我知道了",
        closeOnConfirm: true,
    })
})
<?php endif?>

$('#switch-on').on('click', function() {
    $('#switch-on').addClass('green-on');
    $('#switch-off').removeClass('red-on');
    $('.switch-content').removeClass('hide');
    $('#pshow1').val(1);
})
$('#switch-off').on('click', function() {
    $('#switch-on').removeClass('green-on');
    $('#switch-off').addClass('red-on');
    $('.switch-content').addClass('hide');
    $('#pshow1').val(0);
})
    </script>
