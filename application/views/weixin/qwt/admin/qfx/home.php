<style type="text/css">
  .tpl-form-line-form .am-form-label{
    text-align: left !important;
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
                个性化设置
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">全员分销</a></li>
                <li class="am-active">个性化设置</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>海报设置</span>
                            </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab2">
                                    <div id="wrapperB" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g tpl-amazeui-form">
                        <div class="tpl-form-body tpl-form-line">
                            <form method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                                    <?php if ($result['ok3'] > 0):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 个性化信息更新成功! </p>
                </div>
            </div>
          <?php endif?>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">二维码海报背景图</label>
                                    <div class="am-u-sm-12">
                <?php
                if ($result['tpl']):
                  ?>
                  <a href="/qwtqfxa/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwtqfxa/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传二维码海报图片</button>
<div id="file-pic" style="display:inline-block;"></div>
                                            <input id="pic" type="file" name="pic" accept="image/jpeg" multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格建议为 640*900px，最大不超过 400K，<a href="/qfx/tpl.zip" target="_blank">点击这里</a> 下载 PSD 模板自行设计。</small>

                                    </div>
                                </div>
            <hr>
                                <div class="am-form-group">
                                    <label for="pic2" class="am-u-sm-12 am-form-label">默认头像</label>
                                    <div class="am-u-sm-12">
              <?php
                    //默认头像
              if ($result['tplhead']):
                ?><a href="/qwtqfxa/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img class="avator" src="/qwtqfxa/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" alt="">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传默认头像</button>
<div id="file-pic2" style="display:inline-block;"></div>
                                            <input id="pic2"  type="file" name="pic2" accept="image/jpeg" multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，正方形，最大不超过 100K。</small>

                                    </div>
                                </div>
                                <hr>
            <div class="am-form-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="ql" class="am-form-label">二维码左边距（px）</label>
                  <input type="number" id="ql" name="px[px_qrcode_left]" value="<?=intval($config['px_qrcode_left'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="qt" class="am-form-label">二维码上边距（px）</label>
                  <input type="number" id="qt" name="px[px_qrcode_top]" value="<?=floatval($config['px_qrcode_top'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="qw" class="am-form-label">二维码高宽（px）</label>
                  <input type="number" id="qw" name="px[px_qrcode_width]" value="<?=intval($config['px_qrcode_width'])?>">
                                    </div>
                                </div>
                </div>
            </div>
            <div class="am-form-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="hl" class="am-form-label">头像左边距（px）</label>
                        <input type="number" id="hl" name="px[px_head_left]" value="<?=intval($config['px_head_left'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="ht" class="am-form-label">头像上边距（px）</label>
                        <input type="number" id="ht" name="px[px_head_top]" value="<?=floatval($config['px_head_top'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="hw" class="am-form-label">头像高宽（px）</label>
                        <input type="number" id="hw" name="px[px_head_width]" value="<?=intval($config['px_head_width'])?>">
                                    </div>
                                </div>
                </div>
            </div>
            <hr>
                                <div class="am-form-group">
                                    <label for="qw" class="am-u-sm-12 am-form-label">海报发送关键词</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" iid="qw" name="px[keyword]" value="<?=$config['keyword']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="ticker_lifetime" class="am-u-sm-12 am-form-label">海报有效期（单位：天，最长30天）</label>
                                    <div class="am-u-sm-12">
                                        <input type="number" min="1" max="30" class="tpl-form-input" id="ticket_lifetime" name="text[ticket_lifetime]" value="<?=intval($config['ticket_lifetime'])?>">
                                        <small class="warning-text">（特别注意：海报有效期首次设置成功后，后续修改，时间只能改短不能改长，例如首次设置是10天，后续修改只能小于10天）</small>
                                    </div>
                                </div>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p>佣金及提现设置 </p>
                </div>
            </div>
            <div class="am-form-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="title1" class="am-form-label">普通分销商名称</label>
                  <input type="text" id="title1" name="text[title1]" value="<?=trim($config['title1'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="title2" class="am-form-label">用户名称</label>
                  <input type="text" id="title2" name="text[title2]" value="<?=trim($config['title2'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="title5" class="am-form-label">自定义收益名称</label>
                  <input type="text" id="title5" name="text[title5]" value="<?=trim($config['title5'])?>">
                                    </div>
                                </div>
                </div>
            </div>
            <div class="am-form-group">
                <div class="am-u-sm-6">
                                <div class="am-form-group">
                                    <div>
                                    <label for="money1" class="am-form-label">佣金比例（%）</label>
                  <input type="number" id="money1" name="text[money1]" value="<?=intval($config['money1'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-6">
                                <div class="am-form-group">
                                    <div>
                                    <label for="money_out" class="am-form-label">最小提现金额（分）</label>
                  <input type="number" id="money_out" name="text[money_out]" value="<?=intval($config['money_out'])?>">
                                    </div>
                                </div>
                </div>
            </div>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p>消息设置 </p>
                </div>
            </div>
                                <div class="am-form-group">
                                    <label for="qfx_url" class="am-u-sm-12 am-form-label">常见问题说明网址 </label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="qfx_url" name="text[qfx_url]" placeholder="http://" value="<?=$config['qfx_url']?>">
                                        <small class="warning-text">建议设置为常见问题的微杂志</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="qfx_desc" class="am-u-sm-12 am-form-label">扫码后推送文案</label>
                                    <div class="am-u-sm-12">
                                        <textarea class="" id="qfx_desc" name="text[qfx_desc]" style="height:150px;"><?=$config['qfx_desc']?></textarea>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="qfx_money_desc" class="am-u-sm-12 am-form-label">活动说明文案</label>
                                    <div class="am-u-sm-12">
                                        <textarea class="" id="qfx_money_desc" name="text[qfx_money_desc]" style="height:150px;"><?=htmlspecialchars($config['qfx_money_desc'])?></textarea>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="send" class="am-form-label">海报生成前发送消息：{TIME} 会被替换成海报过期时间 </label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="send" name="text[text_qfxsend]" placeholder="您的专属海报生成中，请稍等..." value="<?=htmlspecialchars($config['text_qfxsend'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal" class="am-form-label">新客户加入模板消息ID（行业：IT科技 - 互联网|电子商务，模板标题「任务审核成功通知」模板编号：OPENTM207173802） </label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="goal" name="text[msg_new_friend]" placeholder="" value="<?=$config['msg_new_friend']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal" class="am-form-label">审核通过消息模板（行业：IT科技 - 互联网|电子商务，模板标题「审核结果通知」模板编号：OPENTM405884804） </label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="goal" name="text[msg_success_tpl]" placeholder="" value="<?=$config['msg_success_tpl']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal" class="am-form-label">收益通知消息模板（行业：IT科技 - 互联网|电子商务，模板标题「收益发放通知」模板编号：OPENTM207809222） </label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="goal" name="text[msg_score_tpl]" placeholder="" value="<?=$config['msg_score_tpl']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-form-label">账户余额通知消息模板（行业：IT科技 - 互联网|电子商务，模板标题「账户余额变动通知」模板编号：OPENTM205454780） </label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="goal2" name="text[msg_money_tpl]" placeholder="" value="<?=$config['msg_money_tpl']?>">
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

<?php if($hasover==1):?>
<script type="text/javascript">
  swal({
    title: "全员分销已过期",
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
           window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/product/7";
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
