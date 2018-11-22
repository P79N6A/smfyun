<style type="text/css">
    label{
        text-align: left !important;
    }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                积分清零
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">推荐有礼</a></li>
                <li>可选功能</li>
                <li class="am-active">批量打标签</li>
            </ol>
            <div class="tpl-portlet-components">
          <?php if ($result['ok6'] > 0):?>
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            保存成功,前往有赞后台可以管理标签
                        </div>
                </div>
          <?php endif?>
                <div class="tpl-block">

                    <div class="am-g">
                        <div class="tpl-form-body tpl-amazeui-form">
                            <form role="form" method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                        <div class="am-u-sm-12">
          <?php if ($result['ok6'] > 0):?>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p>保存成功,前往有赞后台可以管理标签</p>
                            </div>
                        </div>
          <?php endif?>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">标签名称(填写后不可修改)：</label>
                                    <div class="am-u-sm-12">
                <input class="name3 form-control" type="text" name='tag[tag_name]' value="<?=$config['tag_name']?>" <?php if($config['tag_name']) echo 'readonly=""'?>>
                                    </div>
                                </div>
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
            </div>
        </div>

    </div>
<script type="text/javascript">
</script>
