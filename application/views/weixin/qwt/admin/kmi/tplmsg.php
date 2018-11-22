<style type="text/css">
  .tpl-form-line-form .am-form-label{
    text-align: left !important;
  }
  label{
    text-align: left !important;
  }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                消息设置
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">自动发卡工具</a></li>
                <li class="am-active">消息设置</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>消息设置</span>
                            </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab2">
                                    <div id="wrapperB" class="wrapper">
                <div class="tpl-block tpl-amazeui-form">

                    <div class="am-g">
                        <div class="tpl-form-body tpl-form-line">
                            <form method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                                    <?php if ($result['ok'] > 0):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 个性化信息更新成功! </p>
                </div>
            </div>
          <?php endif?>
                                 <div class="am-form-group">
                                  <label for="mgtpl" class="am-u-sm-12 am-form-label">模板消息ID（行业：IT科技 - 互联网|电子商务，模板标题「任务审核成功通知」模板编号：OPENTM207173802）</label>
                                  <div class="am-u-sm-12">
                                  <input type="text" class="form-control" id="mgtpl" name="text[mgtpl]" value='<?=$config['kmitpl']?>'>
                                  </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">保存</button>
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



        </div>
<?php if($hasover==1):?>
<script type="text/javascript">
  swal({
    title: "自动发卡工具已过期",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#DD6B55",
    confirmButtonText: "前往续费",
    cancelButtonText: "取消",
    closeOnConfirm: false,
    closeOnCancel: true,
      },
    function(isConfirm){
      if (isConfirm) {
           window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/product/5";
      }
  })

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
</script>
<?endif?>
