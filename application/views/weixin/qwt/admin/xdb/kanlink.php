<style type="text/css">
    label{
        text-align: left !important;
    }
    .am-badge{
        background-color: green;
    }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                活动链接
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">推荐有礼</a></li>
                <li class="am-active">活动链接</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>用于投放的活动链接</span>
                            </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab2">
                                    <div id="wrapperB" class="wrapper">
                <div class="tpl-block tpl-amazeui-form">

                    <div class="am-g">
                        <div class="tpl-form-body am-form-horizontal">
                                <div class="am-form-group">
                                    <label for="shopname" class="am-u-sm-12 am-form-label">活动链接</label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="form-control" id="khv" value="http://<?=$_SERVER['HTTP_HOST']?>/smfyun/user_snsapi_userinfo/<?=$bid?>/xdb/qrcode" readonly="">
                                        <!-- <input type="text" class="form-control" id="khv" name="shop[name]" value="http://<?=$_SERVER['HTTP_HOST']?>/smfyun/user_snsapi_userinfo/<?=$bid?>/kjb/list" readonly=""> -->
                                    </div>
                                    <div class="am-u-sm-3">
                                        <button type="button" id="khb" data-clipboard-action="copy" data-clipboard-target="#khv">复制</button>
                                    </div>
                                </div>
                                    <small style="color:red;display:none">复制成功！</small>

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



        </div>
  <script src="http://jfb.dev.smfyun.com/qwt/clipboard/clipboard.min.js"></script>
<script type="text/javascript">
var clipboard1 = new Clipboard('#khb');
clipboard1.on('success', function(e) {
    $('small').show();
});
</script>
