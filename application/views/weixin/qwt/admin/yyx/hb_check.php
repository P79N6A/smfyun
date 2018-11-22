

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
    历史订单同步
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">数据大屏幕</a></li>
                <li class="am-active">历史订单同步</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
    历史订单同步
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
                                <p> 只能拉取三个月以内的订单</p>
                            </div>
                        </div>
                </div>
                <form role="form" method="post">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="actions">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$config['his_rsync'] == 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off" class="red <?=$config['his_rsync'] == 0 ||!$config['his_rsync']? 'red-on' : ''?>">关闭</li>
                                    <input type="hidden" value="<?=$config['his_rsync']?>" name="his_rsync[his_rsync]" id="flock">
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
