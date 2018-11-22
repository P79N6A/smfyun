

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
                个性化设置
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">一物一码</a></li>
                <li id="name1" class="am-active">个性化设置</li>
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
                            <?php if($result['err3']):?>
                                <div class="tpl-content-scope">
                            <div class="note note-info" style="color:red">
                                <p> $result['err3']</p>
                            </div>
                        </div>
                            <?php endif?>
                        <?php if ($result['ok2']>0):?>
                    <div class="tpl-content-scope">
                            <div class="note note-info" style="color:green">
                                <p> 配置保存成功！</p>
                            </div>
                        </div>
                      <?php endif?>
                        <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 个性化配置</p>
                            </div>
                        </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">店铺名称</label>
                                    <div class="am-u-sm-12">
                                    <input type="text" class="form-control" id="logoname" name="cus[logoname]" value="<?=$config["logoname"]?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">店铺链接</label>
                                    <div class="am-u-sm-12">
                                    <input type="text" class="form-control" id="logoname" name="cus[shopurl]" value="<?=$config["shopurl"]?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">分享到朋友圈的标题</label>
                                    <div class="am-u-sm-12">
                                    <input type="text" class="form-control" id="logoname" name="cus[sharetitle]" value="<?=$config["sharetitle"]?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">分享到朋友圈的内容</label>
                                    <div class="am-u-sm-12">
                                    <input type="text" class="form-control" id="logoname" name="cus[sharecontent]" value="<?=$config["sharecontent"]?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">分享url（点击用户分享的朋友圈跳转的链接）</label>
                                    <div class="am-u-sm-12">
                                    <input type="text" class="form-control" id="logoname" name="cus[jumpurl]" value="<?=$config["jumpurl"]?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">单个用户最大扫码次数（最大为10）</label>
                                    <div class="am-u-sm-12">
                                    <input type="number" class="form-control" id="logoname" name="cus[mostscan]" value="<?=$config["mostscan"]?>">
                                    </div>
                                </div>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 图片设置</p>
                            </div>
                        </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">品牌logo</label>
                                    <div class="am-u-sm-12">
                <?php
                if ($result['logo']):
                  ?>
                  <a href="/qwtywma/images/cfg/<?=$result['logo']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwtywma/images/cfg/<?=$result['logo']?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传品牌logo</button>
                                        <div id="file-pic2" style="display:inline-block;"></div>
                                            <input id="pic2" type="file" name="logo" accept="image/jpeg" multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格建议为 640*900px，最大不超过 200K，</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">分享图标</label>
                                    <div class="am-u-sm-12">
                <?php
                if ($result['sharelogo']):
                  ?>
                  <a href="/qwtywma/images/cfg/<?=$result['sharelogo']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwtywma/images/cfg/<?=$result['sharelogo']?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传分享图标</button>
                                        <div id="file-pic3" style="display:inline-block;"></div>
                                            <input id="pic3" type="file" name="sharelogo" accept="image/jpeg" multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格建议为 200*200px，最大不超过 200k，</small>

                                    </div>
                                </div>
                                <hr>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p>分享后下发微信红包</p>
                            </div>
                        </div>
                         <div class="am-form-group">
                                <label for="menu" class="am-u-sm-12 am-form-label">单个红包最小金额，（单位：分，最少100分）</label>
                                <div class="am-u-sm-12">
                                <input type="text" class="form-control" id="logoname" name="cus[leastmoney]" value="<?=$config["leastmoney"]?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="menu" class="am-u-sm-12 am-form-label">单个红包最小金额，（单位：分，最大20000分）</label>
                                <div class="am-u-sm-12">
                                <input type="text" class="form-control" id="logoname" name="cus[mostmoney]" value="<?=$config["mostmoney"]?>">
                                </div>
                            </div>
                        <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p>关注后才能领取红包</p>
                            </div>
                        </div>
                        <div class="am-form-group">
                            <div class="actions am-u-sm-12">
                                <ul class="actions-btn">
                                    <li id="switch-on-2" class="green <?=$config['ifattention'] >0 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off-2" class="red <?=$config['ifattention'] ==0 ? 'red-on' : ''?>">关闭</li>
                                    <input type="hidden" name="cus[ifattention]" id="show2" value="<?=$config['ifattention']?>">
                                </ul>
                            </div>
                        </div>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-danger">保存个性化配置</button>
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
                        <div class="tpl-form-body tpl-amazeui-form">
                        <div class="am-form am-form-horizontal">
                                    <?php if ($success['ok']!=null):?>
                        <div class="am-u-sm-12">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 配置保存成功! </p>
                </div>
            </div>
            </div>
                        <?php elseif($success['ok'] =='file'):?>
                        <div class="am-u-sm-12">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 文件更新成功! </p>
                </div>
            </div>
            </div>
                        <?php endif?>
                        <?php if($config==null):?>
                        <div class="am-u-sm-12">
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                  <a href="download/<?=$bid?>" class="am-btn am-btn-danger">下载红包素材</a>
                </div>
                </div>
                </div>
                        <?php else:?>
                            <?php if($left==1):?>
                        <div class="am-u-sm-12">
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                  <a href="pre_generate/<?=$bid?>" class="am-btn am-btn-danger">生成二维码红包</a>
                </div>
                </div>
                </div>
                            <?php else:?>
                        <div class="am-u-sm-12">
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                  <a class="am-btn am-btn-danger">二维码已经获取</a><small>(7天后才能再次获取)</small>
                </div>
                </div>
                </div>

                            <?php endif;?>
                <?php if($result['cron']->id):?>
                <?php if($result['cron']->state==0):?>
                        <div class="am-u-sm-12">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 二维码正在生成中，请稍后下载！ </p>
                </div>
            </div>
            </div>
                <?php else:?>
                    <?php if($result['cron']->has_down==1):?>
                        <div class="am-u-sm-12">
                        <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                          <a href="#" class="am-btn am-btn-danger">最新二维码已经下载了</a>
                        </div>
                        </div>
                        </div>
                    <?php else:?>
                        <div class="am-u-sm-12">
                        <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                          <a href="download_csv" class="am-btn am-btn-danger">下载最新红包二维码</a>
                        </div>
                        </div>
                        </div>
                    <?php endif?>

                <?php endif?>
                <?php endif?>
                        <div class="am-u-sm-12">
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                  <a href="<?php echo URL::site('qwtywma/download/'.$buy_id)?>" class="am-btn am-btn-danger">下载红包素材</a>
                  </div>
                  </div>
                  </div>
              <?php endif?>
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
$('#switch-on-2').on('click', function() {
    $('#switch-on-2').addClass('green-on');
    $('#switch-off-2').removeClass('red-on');
    $('#show2').val(1);
})
$('#switch-off-2').on('click', function() {
    $('#switch-on-2').removeClass('green-on');
    $('#switch-off-2').addClass('red-on');
    $('#show2').val(0);
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
