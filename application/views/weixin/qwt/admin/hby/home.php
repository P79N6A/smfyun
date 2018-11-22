

    <style type="text/css">
    .am-selected-content{
        max-height: 180px;
        overflow: scroll;
    }
    .switch-content{
        overflow: hidden !important;
    }
    label{
        text-align: left !important;
    }
    .hide{height: 0;}
    .am-badge{
        background-color: green;
    }
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">


        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                基础设置
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">红包雨</a></li>
                <li id="name1" class="am-active">基础设置</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
                            <form role="form" method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                        <?php if ($result['ok2']>0):?>
                    <div class="tpl-content-scope">
                            <div class="note note-info" style="color:green">
                                <p> 配置保存成功！</p>
                            </div>
                        </div>
                      <?php endif?>
                        <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 扫码规则</p>
                            </div>
                        </div>

                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">单个用户最大扫码数量（最大为10）</label>
                                    <div class="am-u-sm-12">
                                    <input type="number" class="form-control" id="moneyMin" name="cus[ct]" value="<?=$config["ct"]?>">
                                    </div>
                                </div>

                                <!-- <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">单个红包领取概率</label>
                                    <div class="am-u-sm-12">
                  <input type="number" class="form-control" id="rate" name="cus[rate]" placeholder="rate" value="<?=$config["rate"]?>">
                                        <small>填入0~100的整数，比如填写99，即领取概率为99%。建议领取概率不低于90%</small>
                                    </div>
                                </div> -->
                                <hr>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-danger">保存配置</button>
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


    <script type="text/javascript">
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

$('#tab2-bar').on('click', function() {
    $('#name1').text('二维码获取、红包素材下载');
})
$('#tab1-bar').on('click', function() {
    $('#name1').text('个性化设置');
})
$('#switch-on').on('click', function() {
    $('#switch-on').addClass('green-on');
    $('#switch-off').removeClass('red-on');
    $('.switch-content').removeClass('hide');
    $('#show1').val(1);
})
$('#switch-off').on('click', function() {
    $('#switch-on').removeClass('green-on');
    $('#switch-off').addClass('red-on');
    $('.switch-content').addClass('hide');
    $('#show1').val(0);
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
  $(function() {
    $('#pic2').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic2').html(fileNames);
    });
    $('#pic3').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic3').html(fileNames);
    });
  });
    </script>
<?php if($hasover==1):?>
<script type="text/javascript">
  swal({
    title: "二维码红包已过期",
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
           window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/product/1";
      }
  })
</script>
<?endif?>
