

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
    同步有赞积分
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">积分宝服务号版</a></li>
                <li>可选功能</li>
                <li class="am-active">同步有赞积分</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
    同步有赞积分
                        </div>
                </div>
          <?php if ($result['ok7'] > 0):?>
                <div class="am-u-sm-12 am-u-md-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 开启成功！</p>
                            </div>
                        </div>
                </div>
                <?php endif?>
                <div class="am-u-sm-12 am-u-md-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 是否开启本功能？开启有赞积分同步功能之后，不能关闭。
                注意：因为有赞积分只支持整数，积分宝和有赞积分兑换比例默认为1比1，请权衡好积分宝和有赞的积分奖励规则。</p>
                            </div>
                        </div>
                </div>
                <form role="form" method="post">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="actions">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$config['switch'] == 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off" class="red <?=$config['switch'] === "0" ||!$config['switch']? 'red-on' : ''?>">关闭</li>
                                    <input type="hidden" value="<?=$config['switch'] === "0" ||!$config['switch']? '0' : '1'?>" name="rsync[switch]" id="flock">
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
<?php if($result['error7']):?>
$(document).ready(function(){
    swal({
        title: "失败",
        text: "您还没有进行有赞授权，请先前往【绑定我们】-【有赞一键授权】进行授权",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "立即前往",
        closeOnConfirm: false,
        showCancelButton: true,
        cancelButtonText: "取消",
        closeOnCancel: true,
    },
    function(isConfirm){
      if (isConfirm) {
                window.location.href='http://<?=$_SERVER["HTTP_HOST"]?>/qwta/yzoauth';
      }
  })
})
<?php endif?>

$('#switch-on').on('click', function() {
    $('#switch-on').addClass('green-on');
    $('#switch-off').removeClass('red-on');
    $('#flock').val(1);
})
<?php if($config['switch'] === "0" ||!$config['switch']):?>
$('#switch-off').on('click', function() {
    $('#switch-on').removeClass('green-on');
    $('#switch-off').addClass('red-on');
    $('#flock').val(0);
})
<?php endif?>
    </script>
