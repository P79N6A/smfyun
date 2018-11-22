
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
    #datetimepicker{
        display: inline-block;
        width: 150px;
        text-align: center;
        border: 1px solid #e5e5e5;
        border-radius: 5px;
        height: 38px;
    }
    label{
      text-align: left!important;
    }
</style>

  <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                订单拉取
            </div>
            <ol class="am-breadcrumb">
                <li><a href="/qwtyyxa/home" class="am-icon-home">数据大屏幕</a></li>
                <li class="am-active"><a href="/qwtyyxa/pull">订单拉取</a></li>
            </ol>
            <div class="tpl-portlet-components">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>订单拉取</span>
                            </div>
                        </div>
                <div class="am-u-sm-12 am-u-md-12">
                        <div class="tpl-form-body tpl-amazeui-form">
                            <form class="am-form am-form-horizontal" method="post" enctype="multipart/form-data">
                            <?php if ($status==0):?>
                <div class="am-u-sm-12 am-u-md-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 注意：一旦开始拉取则不能取消，不能撤回，不能重新拉取，由于有赞接口原因只能拉取三个月以内的订单</p>
                            </div>
                        </div>
                </div>
                <input type="hidden" value="1" name="pull">
                <div class="am-form-group">
                        <div class="am-u-sm-12">
                            <button type="submit" class="am-btn am-btn-success">开始拉取</button>
                        </div>
                </div>
              <?php endif?>
              <?php if ($status==2):?>
                                <div class="am-form-group">
                                    <label class="am-u-sm-12 am-form-label">订单还在拉取中，还需要大约<?=$time?>分钟。</label>
                                </div>
                              <?php endif?>
                            </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
    <script type="text/javascript">
    $('#datetimepicker').datetimepicker({
  language:  'zh-CN',
  format: 'yyyy-mm-dd',
  startView: 'month',
  minView: 'month'
});
    </script>

