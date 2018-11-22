<style type="text/css">
  .tpl-form-line-form .am-form-label{
    text-align: left !important;
  }
  label{
    text-align: left !important;
  }
</style>

          <?php
          if ($_POST['text']) $active = '1';
          if ($_POST['shopurl']) $active = '2';
          ?>

          <script>
          $(function () {
            $('#tab<?=$active?>').addClass('am-active');
            $('#tab<?=$active?>').addClass('am-in');
            $('#litab<?=$active?>').addClass('am-active');
          });
          </script>

    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                基础设置
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">代理哆</a></li>
                <li class="am-active">基础设置</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <ul class="am-tabs-nav am-nav am-nav-tabs" style="left:0;">
                                <li id="litab1" class="am-active"><a href="#tab1">公告设置</a></li>
                                <li id="litab2"><a href="#tab2">有赞店铺地址设置</a></li>
                            </ul>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g tpl-amazeui-form">
                        <div class="tpl-form-body tpl-form-line">
                            <form role="form" method="post" enctype="multipart/form-data" class="am-form am-form-horizontal">
                                    <?php if ($result['ok3'] > 0):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 个性化信息更新成功!</p>
                </div>
            </div>
          <?php endif?>
                                <div class="am-form-group">
                                    <label for="qfx_url" class="am-u-sm-12 am-form-label">公告文字： </label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="qwt_dlddesc" name="text[desc]" value="<?=$config['desc']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="qfx_url" class="am-u-sm-12 am-form-label">公告链接网址（没有则不设置）： </label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="qwt_dldurl" name="text[qwt_dldurl]" placeholder="http://" value="<?=$config['qwt_dldurl']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">更新公告设置</button>
                                    </div>
                                </div>
                            </form>
                            </div>
                            </div>
                            </div>
                            </div>
                            </div>
                                <div class="am-tab-panel am-fade" id="tab2">
                                    <div id="wrapperB" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g">
                        <div class="tpl-form-body tpl-form-line">
                            <form role="form" method="post" class="am-form tpl-form-line-form" enctype='multipart/form-data'>
                <?php if ($result['ok5'] > 0):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 配置保存成功!</p>
                </div>
            </div>
          <?php endif?>
                                <div class="am-form-group">
                                    <label for="qfx_url" class="am-u-sm-3 am-form-label">有赞店铺地址设置： </label>
                                    <div class="am-u-sm-9">
                                        <input type="text" class="tpl-form-input" id="surl" placeholder="http://"  name="shopurl" value="<?=$config['shopurl']?>">
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
    title: "代理哆已过期",
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
           window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/product/9";
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
