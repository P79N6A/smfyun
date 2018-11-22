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
                个性化设置 <small>核心参数配置</small>
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">有赞订单下发模板消息</a></li>
                <li class="am-active">个性化设置</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>个性化设置</span>
                            </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab2">
                                    <div id="wrapperB" class="wrapper">
                <div class="tpl-block tpl-amazeui-form">

                    <div class="am-g">
                        <div class="tpl-form-body am-form-horizontal">
                            <form method="post" class="am-form " enctype='multipart/form-data'>
                                    <?php if ($result['ok3'] > 0):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 个性化信息更新成功! </p>
                </div>
            </div>
          <?php endif?>
                                <div class="am-form-group">
                                  <label for="mgtpl" class="am-u-sm-12 am-form-label">模板消息ID（行业：IT科技 - 互联网|电子商务，模板标题「购买成功通知」模板编号：TM00001）</label>
                                  <div class="am-u-sm-12">
                                  <input type="text" class="form-control" id="mgtpl" name="text[mgtpl]" value='<?=$config['mgtpl']?>'>
                                  </div>
                                </div>
                                <!-- <div class="am-form-group">
                                    <label for="ticker_lifetime" class="am-u-sm-12 am-form-label">模板消息标题</label>
                                    <div class="am-u-sm-12">
                <input type="text" class="form-control" id="qw" name="text[title]" value="<?=$config['title']?>">
                                    </div>
                                </div> -->
                                <div class="am-form-group">
                                    <label for="ticker_lifetime" class="am-u-sm-12 am-form-label">模板消息内容</label>
                                    <div class="am-u-sm-12">
                <input type="text" class="form-control" id="qw" name="text[content]" value="<?=$config['content']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="text_follow_url" class="am-u-sm-12 am-form-label">点击模板消息跳转的链接（可不填）</label>
                                    <div class="am-u-sm-12">
              <input type="text" class="tpl-form-input" class="form-control" id="href" name="text[href]" placeholder="http://" value="<?=$config['href']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">更新个性化设置</button>
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
                            <?php

        if (!$_POST||$_POST['menu']) $active = 'tab1';
        if ($_POST['text']) $active = 'tab2';
        ?>
        <script src="http://cdn.bootcss.com/jquery/2.0.1/jquery.js"></script>
        <script>

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
          $(function () {
            $('#<?=$active?>,#<?=$active?>-bar').addClass('am-active');
          });
  $(function() {
    $('#pic').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic').html(fileNames);
    });
  });
  $(function() {
    $('#pic2').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic2').html(fileNames);
    });
  });
        </script>
<?php if($hasover==1):?>
<script type="text/javascript">
  swal({
    title: "有赞模板消息下发已过期",
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
           window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/product/26";
      }
  })
</script>
<?endif?>
