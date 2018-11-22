
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
                模板消息设置
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">预约宝</a></li>
                <li class="am-active">模板消息设置</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-active am-fade am-in" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g tpl-amazeui-form">
                        <div class="tpl-form-body">
                            <form method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                                    <?php if ($result['ok'] > 0):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 微信配置保存成功! </p>
                </div>
            </div>
          <?php endif?>
                                <div class="am-form-group">
                                    <label for="mbtpl" class="am-u-sm-12 am-form-label">模板消息ID（行业：IT科技 - 互联网|电子商务，模板消息标题「新预约通知」，模板消息编号：OPENTM406122078）</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="mbtpl" placeholder="输入模板ID" maxlength="64" name="cfg[mbtpl]" value="<?=$config['mbtpl']?>">
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
        <script type="text/javascript">
  $(function() {
    $('#pic').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic').html(fileNames);
    });
  });
  </script>
<?php if($hasover==1):?>
<script type="text/javascript">
  swal({
    title: "预约宝已过期",
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
           window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/product/10";
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
