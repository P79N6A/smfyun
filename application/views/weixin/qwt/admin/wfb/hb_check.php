

    <style type="text/css">
    .am-selected-content{
        max-height: 180px;
        overflow: scroll;
    }
    .switch-content{
        overflow: hidden !important;
    }
    .hide{height: 0;}
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">


        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
    红包发送审核
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">积分宝服务号版</a></li>
                <li>可选功能</li>
                <li class="am-active">红包发送审核</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
    红包发送审核
                        </div>
                </div>
                <?php if ($result['ok8'] > 0):?>
                <div class="am-u-sm-12 am-u-md-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 保存成功！</p>
                            </div>
                        </div>
                </div>
                <?php endif?>
                <div class="am-u-sm-12 am-u-md-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 此功能开启后，用户兑换奖品时，可以在后台奖品兑换界面对进行【兑换红包】的用户进行审核，审核成功之后再发送红包</p>
                            </div>
                        </div>
                </div>
                <form role="form" method="post">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="actions">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$config['hb_check'] == 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off" class="red <?=$config['hb_check'] == 0 ||!$config['hb_check']? 'red-on' : ''?>">关闭</li>
                                    <input type="hidden" value="<?=$config['hb_check']?>" name="hb_check[hb_check]" id="flock">
                                </ul>
                            </div>
                </div>
                        <div class="am-u-sm-12" style="padding:0">
                        <hr>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-danger">保存设置</button>
                        </div>
                </div>
                </div>
                </form>
            </div>
        </div>

    </div>

    <script type="text/javascript">

$('#switch-on').on('click', function() {
    $('#switch-on').addClass('green-on');
    $('#switch-off').removeClass('red-on');
    $('#flock').val(1);
})
$('#switch-off').on('click', function() {
    $('#switch-on').removeClass('green-on');
    $('#switch-off').addClass('red-on');
    $('#flock').val(0);
})
    </script>
