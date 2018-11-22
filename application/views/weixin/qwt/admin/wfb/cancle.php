

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
    取消关注扣除积分
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">积分宝服务号版</a></li>
                <li>可选功能</li>
                <li class="am-active">取消关注扣除积分</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
    取消关注扣除积分
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
                                <p> 1、粉丝取消关注公众号后，该粉丝的首次关注奖励积分，该粉丝上线、上线的上线获得的积分奖励，全部扣除；该粉丝重新关注公众号后，已扣除的积分不会恢复。<br>

2、用户点击菜单进入积分相关页面，会自动筛选下线、下线的下线是否有取消关注，有的话按照上述流程扣除积分；<br>
3、获取用户基本信息的微信接口调用量每天有上限，如果活动参与人数多，不建议开启本功能，很容易造成接口调用量达到上限，影响活动的正常进行；<br>
4、本功能开启后，会增加客服沟通压力，请谨慎考虑后再开启；</p>
                            </div>
                        </div>
                </div>
                <form role="form" method="post">
                <div class="am-u-sm-12 am-u-md-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 是否开启本功能？</p>
                            </div>
                        </div>
                </div>
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="actions">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$config['btnn'] == 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off" class="red <?=$config['btnn'] == 0 ||!$config['btnn']? 'red-on' : ''?>">关闭</li>
                                    <input type="hidden" value="<?=$config['btnn']?>" name="cancle[btnn]" id="flock">
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
