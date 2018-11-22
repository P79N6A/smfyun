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
                <li><a href="#" class="am-icon-home">任务宝订阅号版</a></li>
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
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 海报设置</p>
                </div>
            </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">二维码海报背景图 <span class="tpl-form-line-small-title">Qrcode Images</span></label>
                                    <div class="am-u-sm-12">
                <?php
                if ($result['tpl']):
                  ?>
                  <a href="/qwtrwda/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwtrwda/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
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
                                        只能为 JPEG 格式，规格建议为 640*900px，最大不超过 400K，<a href="/wfb/tpl.zip" target="_blank">点击这里</a> 下载 PSD 模板自行设计。</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="pic2" class="am-u-sm-12 am-form-label">默认头像（获取头像失败时会用改头像，可选） <span class="tpl-form-line-small-title">Avator</span></label>
                                    <div class="am-u-sm-12">
              <?php
                    //默认头像
              if ($result['tplhead']):
                ?><a href="/qwtrwda/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img class="avator" src="/qwtrwda/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" alt="">
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
                                <div class="am-form-group">
                                    <label for="ticker_lifetime" class="am-u-sm-12 am-form-label">海报发送关键词</label>
                                    <div class="am-u-sm-12">
                <input type="text" class="form-control" id="qw" name="px[keyword]" value="<?=$config['keyword']?>">
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
                                        <p> 消息设置</p>
                                    </div>
                                </div>
                                <!--  <div class="am-form-group">
                                  <label for="mgtpl" class="am-u-sm-12 am-form-label">模板消息ID（行业：IT科技 - 互联网|电子商务，模板标题「任务审核成功通知」模板编号：OPENTM207173802）</label>
                                  <div class="am-u-sm-12">
                                  <input type="text" class="form-control" id="mgtpl" name="text[mgtpl]" value='<?=$config['mgtpl']?>'>
                                  </div>
                                </div> -->
                                <div class="am-form-group">
                                    <label for="text_follow_url" class="am-u-sm-12 am-form-label">引导用户快速关注的页面网址</label>
                                    <div class="am-u-sm-12">
              <input type="text" class="tpl-form-input" class="form-control" id="subhref" name="text[subhref]" placeholder="http://" value="<?=$config['subhref']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="text_follow_url" class="am-u-sm-12 am-form-label">首次关注推送网址</label>
                                    <div class="am-u-sm-12">
              <input type="text" class="tpl-form-input" class="form-control" id="text_follow_url" name="text[text_follow_url]" placeholder="http://" value="<?=$config['text_follow_url']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal3" class="am-u-sm-12 am-form-label">扫码关注后自动回复文案</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" id="goal3" name="text[text_goal3]"  value="<?=$config['text_goal3']?htmlspecialchars(str_replace("\n", '\n',$config['text_goal3'])):'恭喜您成为了「%s」的支持者~'?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="send" class="am-u-sm-12 am-form-label">海报生成前发送消息</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" id="send" name="text[text_send]" placeholder="您的专属海报生成中，请稍等..." value="<?=htmlspecialchars(str_replace("\n", '\n',$config['text_send']))?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal" class="am-u-sm-12 am-form-label">获得粉丝奖励文案</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" id="goal" name="text[text_goal]" placeholder="您的朋友「%s」成为了您的支持者！您离完成任务更近一步，继续努力哦。" value="<?=htmlspecialchars(str_replace("\n", '\n',$config['text_goal']))?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">最终完成任务奖励文案</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" id="goal2" name="text[text_goal2]" placeholder="恭喜您已经全部完成了「%s」任务，继续留意我们下次任务哦。" value="<?=htmlspecialchars(str_replace("\n", '\n',$config['text_goal2']))?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal4" class="am-u-sm-12 am-form-label">重复扫描相同二维码回复文案</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="form-control" id="goal4" name="text[text_goal4]" value="<?=$config['text_goal4']?htmlspecialchars(str_replace("\n", '\n',$config['text_goal4'])):'您已经是「%s」的支持者了，不用再扫了哦'?>">
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
    title: "任务宝订阅号版已过期",
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
           window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/product/25";
      }
  })
</script>
<?endif?>
