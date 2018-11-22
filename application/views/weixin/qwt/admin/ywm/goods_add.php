
    <link rel="stylesheet" href="/qwt/assets/css/simditor.css">
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
    #datetimepicker{
      width: 160px;
      text-align: center;
      margin-top: 5px;
    }
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
                其他商品管理
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">神码云直播</a></li>
                <li>直播商品管理</li>
                <li class="am-active">其他商品管理</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            <?=$result['title']?>
                        </div>
                </div>
                <div class="am-u-sm-12 am-u-md-12">
                        <div class="tpl-form-body tpl-amazeui-form">
                            <form class="am-form am-form-horizontal" method="post" enctype="multipart/form-data" onsubmit="return check(this.form)" role="form">
<?php if ($result['error']):?>
            <div class="tpl-content-scope">
                <div class="note note-danger">
                    <p> <?=$result['error']?> </p>
                </div>
            </div>
          <?php endif?>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">商品名称 </label>
                                    <div class="am-u-sm-12">
        <input type="text" class="form-control" id="title" name="data[title]" placeholder="输入商品名称" value="<?=htmlspecialchars($_POST['data']['title'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">商品价格 </label>
                                    <div class="am-u-sm-12">
          <input type="number" class="form-control" id="price" name="data[price]" placeholder="市场价" value="<?=floatval($_POST['data']['price'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
        <?php if ($result['action'] == 'edit' && $result['item']['db_pic']):?>
                                    <label for="user-weibo" class="am-u-sm-12 am-form-label">商品图片（重新上传会覆盖原照片） </label>
        <?php else:?>
                                    <label for="user-weibo" class="am-u-sm-12 am-form-label">商品图片 </label>
        <?php endif?>
                                    <div class="am-u-sm-12">
        <?php if ($result['action'] == 'edit' && $result['item']['db_pic']):?>
                  <a href="/qwtywma/dbimages/setgood/<?=$result['item']['id']?>.v<?=$result['item']['time']?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                            <img class="img-thumbnail" src="/qwtywma/dbimages/setgood/<?=$result['item']['id']?>.v<?=$result['item']['time']?>.jpg" width="100">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传产品图片</button>
<div id="file-pic" style="display:inline-block;"></div>
                                            <input id="pic" type="file" name="pic" accept="image/jpeg" class="form-control" multiple>
          <input type="hidden" name="MAX_FILE_SIZE" value="204800" />
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格为正方形，建议为 600*600px，最大不超过 200KB。</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">商品购买地址 </label>
                                    <div class="am-u-sm-12">
          <input type="url" class="form-control" id="url" name="data[url]" placeholder="" value="<?=$_POST['data']['url']?>">
                                    </div>
                                </div>
                        <div class="am-u-sm-12" style="padding:0">
                        <hr>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-success"><i class="fa fa-edit"></i><?=$result['title']?></button>
      <?php if ($result['action'] == 'edit'):?>
      <a href="#" class="am-btn am-btn-danger" id="delete" data-toggle="modal" data-target="#deleteModel"><i class="fa fa-remove"></i> <span>删除该奖品</span></a>
    <?php endif?>
                        </div>
                </div>
                </div>
                </form>
            </div>
        </div>

    </div>

    <script src="/qwt/assets/js/module.min.js"></script>
    <script src="/qwt/assets/js/uploader.min.js"></script>
    <script src="/qwt/assets/js/hotkeys.min.js"></script>
    <script src="/qwt/assets/js/simditor.min.js"></script>
<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
<script>

    $('#switch-on').click(function(){
      $('#switch-on').addClass('green-on');
      $('#switch-off').removeClass('red-on');
      $('#show0').val(1);
    })
    $('#switch-off').click(function(){
      $('#switch-on').removeClass('green-on');
      $('#switch-off').addClass('red-on');
      $('#show0').val(0);
    })
    $('#delete').click(function(){
  swal({
    title: "确认要删除吗？",
    text: "该操作不可恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认删除',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwtywma/other_setgood/edit/<?=$result['item']['id']?>?DELETE=1";
    })
  })
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


