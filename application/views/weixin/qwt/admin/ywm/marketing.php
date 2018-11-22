    <style type="text/css">
    .am-selected-content{
        max-height: 180px;
        overflow: scroll;
    }
    label{
        text-align: left !important;
    }
    .hide{display: none;}
    .am-badge{
        background-color: green;
    }
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">


        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                营销模块
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">一物一码</a></li>
                <li id="name1" class="am-active">营销模块</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>


                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper" style="overflow: -webkit-paged-x;">
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
                            <form role="form" method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                            <?php if($result['err3']):?>
                                <div class="tpl-content-scope">
                            <div class="note note-info" style="color:red">
                                <p> $result['err3']</p>
                            </div>
                        </div>
                            <?php endif?>
                        <?php if ($result['ok2']>0):?>
                    <div class="tpl-content-scope">
                            <div class="note note-info" style="color:green">
                                <p> 配置保存成功！</p>
                            </div>
                        </div>
                        <?php endif?>
                        <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 是否开启领取优惠券再来一单</p>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <div class="actions am-u-sm-12">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$config['ifyzcoupons'] >0 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off" class="red <?=$config['ifyzcoupons'] ==0 ? 'red-on' : ''?>">不开启</li>
                                    <input type="hidden" name="market[ifyzcoupons]" id="show0" value="<?=$config['ifattention']?>">
                                </ul>
                            </div>
                        </div>
                        <div class="switch-content <?=$config['ifyzcoupons'] == 1 ? '' : 'hide'?>" style="padding:0;">
                        <div class="am-form-group">
                                    <label for="user-phone" class="am-u-sm-12 am-form-label">选择优惠券</label>
                                    <div class="am-u-sm-12">
                                        <select name="market[yzcoupons]" data-am-selected="{searchBox: 1}">
                                  <?php if($result['yzcoupons']):?>
                                  <?php foreach ($result['yzcoupons'] as $yzcoupon):?>
                                  <option <?=$config['yzcoupons']==$yzcoupon['group_id']?"selected":""?> value="<?=$yzcoupon['group_id']?>"><?=$yzcoupon['title']?></option>
                                  <?php endforeach; ?>
                                   <?php endif;?>
                                        </select>
                                    </div>
                                    </div>
                                    </div>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-danger">保存</button>
                        </div>
                </div>
                </form>
                </div>
                </div>
                </div>
                </div>
                </div>
                </div>
                </div>
                </div>
                </div>
                </div>
<script type="text/javascript">
$('#switch-on').click(function(){
    $('#switch-on').addClass('green-on');
    $('#switch-off').removeClass('red-on');
    $('.switch-content').removeClass('hide');
    $('#show0').val(1);
})
$('#switch-off').click(function(){
    $('#switch-on').removeClass('green-on');
    $('#switch-off').addClass('red-on');
    $('.switch-content').addClass('hide');
    $('#show0').val(0);
})
</script>
