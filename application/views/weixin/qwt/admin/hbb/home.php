

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
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">


        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                个性化设置
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">口令红包</a></li>
                <li id="name1" class="am-active">个性化设置</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <ul class="am-tabs-nav am-nav am-nav-tabs" style="left:0;">
                                <li id="tab1-bar" class="am-active"><a href="#tab1">个性化设置</a></li>
                                <li id="tab2-bar"><a href="#tab2">口令获取、红包素材下载</a></li>
                            </ul>

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
                                <p> 发放规则设置</p>
                            </div>
                        </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">单个用户最大的领取数量（最大为10）</label>
                                    <div class="am-u-sm-12">
                                    <input type="number" class="form-control" id="moneyMin" name="cus[ct]" value="<?=$config["ct"]?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">单个红包最小金额</label>
                                    <div class="am-u-sm-12">
                  <input type="number" class="form-control" id="moneyMin" name="cus[moneyMin]" placeholder="moneyMin" value="<?=$config["moneyMin"]?>">
                                        <small>单位：分，最少100分</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">单个红包最大金额</label>
                                    <div class="am-u-sm-12">
                  <input type="number" class="form-control" id="money" name="cus[money]" placeholder="money" value="<?=$config["money"]?>">
                                        <small>单位：分，最大20000分</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">单个红包领取概率</label>
                                    <div class="am-u-sm-12">
                  <input type="number" class="form-control" id="rate" name="cus[rate]" placeholder="rate" value="<?=$config["rate"]?>">
                                        <small>填入0~100的整数，比如填写99，即领取概率为99%。建议领取概率不低于90%</small>
                                    </div>
                                </div>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 文案设置</p>
                            </div>
                        </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">口令兑换成功自动回复文案</label>
                                    <div class="am-u-sm-12">
                  <input type="text" class="form-control" id="success" name="cus[success]" placeholder="" value="<?=htmlspecialchars($config["success"])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">口令红包领取失败自动回复文案</label>
                                    <div class="am-u-sm-12">
                  <input type="text" class="form-control" id="rate" name="cus[success2]" placeholder="rate" value="<?=htmlspecialchars($config["success2"])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">本人已经领取过自动回复文案</label>
                                    <div class="am-u-sm-12">
                  <input type="text" class="form-control" id="got" name="cus[got]" placeholder="" value="<?=htmlspecialchars($config["got"])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">红包口令已经被兑换过自动回复文案</label>
                                    <div class="am-u-sm-12">
                  <input type="text" class="form-control" id="payed" name="cus[payed]" placeholder="" value="<?=htmlspecialchars($config["payed"])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">口令输入错误自动回复文案</label>
                                    <div class="am-u-sm-12">
                  <input type="text" class="form-control" id="hack" name="cus[hack]" placeholder="" value="<?=htmlspecialchars($config["hack"])?>">
                                    </div>
                                </div>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 是否开启裂变？</p>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="actions">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$config['split'] >0 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off" class="red <?=$config['split'] ==0 ? 'red-on' : ''?>">关闭</li>
                                    <input type="hidden" name="cus[split]" id="show1" value="<?=$config['split']?>">
                                </ul>
                            </div>
                </div>
                        <div class="am-u-sm-12 switch-content <?=$config['split'] >0 ? '' : 'hide'?>" style="padding:0;">
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">裂变口令个数</label>
                                    <div class="am-u-sm-12">
                    <?if($config['split_count']>0):?>
                    <input type="text" class="form-control" id="split_count" name="cus[split_count]" value=<?=$config['split_count']?>>
                    <?else:?>
                    <input type="text" class="form-control" id="split_count" name="cus[split_count]" placeholder='裂变个数'>
                    <?endif;?>
                                        <small>小于10的整数</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">裂变发送文案</label>
                                    <div class="am-u-sm-12">
                      <input type="text" class="form-control" id="hack" name="cus[splits_txt]" placeholder="" value="<?=htmlspecialchars($config["splits_txt"])?>">
                                    </div>
                                </div>
                                </div>
                                <hr>
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
                  <a href="pre_generate/<?=$bid?>" class="am-btn am-btn-danger">生成口令红包</a>
                </div>
                </div>
                </div>
                            <?php else:?>
                        <div class="am-u-sm-12">
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                  <a class="am-btn am-btn-danger">口令已经获取</a><small>(7天后才能再次获取)</small>
                </div>
                </div>
                </div>

                            <?php endif;?>
                <?php if($result['cron']->id):?>
                <?php if($result['cron']->state==0):?>
                        <div class="am-u-sm-12">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 口令正在生成中，请稍后下载！ </p>
                </div>
            </div>
            </div>
                <?php else:?>
                    <?php if($result['cron']->has_down==1):?>
                        <div class="am-u-sm-12">
                        <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                          <a href="#" class="am-btn am-btn-danger">最新口令已经下载了</a>
                        </div>
                        </div>
                        </div>
                    <?php else:?>
                        <div class="am-u-sm-12">
                        <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                          <a href="download_csv" class="am-btn am-btn-danger">下载最新红包口令csv</a>
                        </div>
                        </div>
                        </div>
                    <?php endif?>

                <?php endif?>
                <?php endif?>
                        <div class="am-u-sm-12">
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                  <a href="<?php echo URL::site('qwthbba/download/'.$buy_id)?>" class="am-btn am-btn-danger">下载红包素材</a>
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
    $('#name1').text('口令获取、红包素材下载');
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
    </script>
<?php if($hasover==1):?>
<script type="text/javascript">
  swal({
    title: "口令红包已过期",
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
