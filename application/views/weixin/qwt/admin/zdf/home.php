
<style type="text/css">
    label{
        text-align: left !important;
    }
    .hide{height: 0;overflow: hidden;}
    [class*=am-u-]+[class*=am-u-]:last-child {
     float: left;
    }
    .selected{
    border-color: #00b900;
    border-width: 2px;
    background: #f5ffe9;
    }
    .am-badge{
        background-color: green;
    }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                关注下发
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">公众号自动下发小程序页工具</a></li>
                <li class="am-active">关注下发</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                    <div class="caption font-green bold">
                        关注下发
                    </div>
                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-active am-fade am-in" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g tpl-amazeui-form">
                        <div class="tpl-form-body">
                            <form method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
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
                                    <li id="switch-on" class="green <?=$config['status'] == 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off" class="red <?=$config['status'] === "0" || !$config['status'] ? 'red-on' : ''?>">关闭</li>
                        <input type="hidden" name="area[status]" id="show0" value="<?=$config['status']?>">
                                </ul>
                            </div>
                </div>
                        <div class="am-u-sm-12 switch-content <?=$config['status'] == 1 ? '' : 'hide'?>" style="padding:0;">
                    <hr>
                                <div class="am-form-group">
                                    <label for="ticker_lifetime" class="am-u-sm-12 am-form-label">消息标题</label>
                                    <div class="am-u-sm-12">
                                        <input type="number" class="tpl-form-input" placeholder="0" name="text[max_send]" value="<?=$config['max_send']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="ticker_lifetime" class="am-u-sm-12 am-form-label">小程序AppID</label>
                                    <div class="am-u-sm-12">
                                        <input type="number" class="tpl-form-input" placeholder="0" name="text[max_send]" value="<?=$config['max_send']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="ticker_lifetime" class="am-u-sm-12 am-form-label">消息预览图</label>
                                    <div class="am-u-sm-12">
              <?php
                    //默认头像
              if ($result['tplhead']):
                ?><a href="/qwtwfba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img class="avator" src="/qwtwfba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" alt="">
                                            </div>
                                            </a>
                                          <?php endif?>
                                    </div>
                        <div class="am-u-sm-12 am-u-md-12">
                            <div class="actions" style="float:left;">
                                <ul class="actions-btn">
                                    <li id="switch-on-2" class="green green-on">从已上传过的图中选取</li>
                                    <li id="switch-off-2" class="red">上传新图片</li>
                        <input type="hidden" name="area[status]" id="show2" value="<?=$config['status']?>">
                                </ul>
                            </div>
                </div>
                                </div>
                        <div class="am-u-sm-12 switch-content-1" style="padding:0;">
                        <input type="hidden">
                        <div class="tpl-table-images">
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4">
                                <div class="tpl-table-images-content selected">
                                    <a class="tpl-table-images-content-i">
                                        <img src="http://yingyong.smfyun.com/qwt/images/rwb.jpg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4">
                                <div class="tpl-table-images-content">
                                    <a class="tpl-table-images-content-i">
                                        <img src="http://yingyong.smfyun.com/qwt/images/rwb.jpg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4">
                                <div class="tpl-table-images-content">
                                    <a class="tpl-table-images-content-i">
                                        <img src="http://yingyong.smfyun.com/qwt/images/rwb.jpg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4">
                                <div class="tpl-table-images-content">
                                    <a class="tpl-table-images-content-i">
                                        <img src="http://yingyong.smfyun.com/qwt/images/rwb.jpg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4">
                                <div class="tpl-table-images-content">
                                    <a class="tpl-table-images-content-i">
                                        <img src="http://yingyong.smfyun.com/qwt/images/rwb.jpg" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="am-u-sm-12 am-u-md-6 am-u-lg-4">
                                <div class="tpl-table-images-content">
                                    <a class="tpl-table-images-content-i">
                                        <img src="http://yingyong.smfyun.com/qwt/images/rwb.jpg" alt="">
                                    </a>
                                </div>
                            </div>
                            </div>
                            <div class="am-u-sm-12">
                                <ul class="am-pagination tpl-pagination">
                                    <li><a>首页</a></li>
        <li><a>上一页</a></li>
            <li><a>1</a></li>
            <li class="am-active"><a>2</a></li>
            <li><a>3</a></li>
            <li><a>下一页</a></li>
            <li><a>尾页</a></li>
                        </div>
                </div>
                        <div class="am-u-sm-12 switch-content-2 hide" style="padding:0;">
                                    <div class="am-u-sm-12">
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传默认头像</button>
                                        <div id="file-pic" style="display:inline-block;"></div>
                                            <input id="pic"  type="file" name="pic" accept="image/jpeg" multiple>
                                        </div>
                                        <small>高宽比为高:宽=4:5,建议尺寸为692px*552px</small>
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
    title: "自动发已过期",
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
           window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/product/20";
      }
  })
</script>
<?php endif?>
<script type="text/javascript">

$('#switch-on').on('click', function() {
    $('#switch-on').addClass('green-on');
    $('#switch-off').removeClass('red-on');
    $('.switch-content').removeClass('hide');
    $('#show0').val(1);
});
$('#switch-off').on('click', function() {
    $('#switch-on').removeClass('green-on');
    $('#switch-off').addClass('red-on');
    $('.switch-content').addClass('hide');
    $('#show0').val(0);
});
$('#switch-on-2').on('click', function() {
    $('#switch-on-2').addClass('green-on');
    $('#switch-off-2').removeClass('red-on');
    $('.switch-content-1').removeClass('hide');
    $('.switch-content-2').addClass('hide');
    $('#show2').val(1);
});
$('#switch-off-2').on('click', function() {
    $('#switch-on-2').removeClass('green-on');
    $('#switch-off-2').addClass('red-on');
    $('.switch-content-1').addClass('hide');
    $('.switch-content-2').removeClass('hide');
    $('#show2').val(2);
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
  $('.tpl-table-images-content-i').click(function(){
    $('.tpl-table-images-content').removeClass('selected');
    $(this).parent().addClass('selected');
  })
</script>
