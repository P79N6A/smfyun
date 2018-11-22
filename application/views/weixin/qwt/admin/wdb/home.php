
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
                个性化设置
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">积分宝订阅号版</a></li>
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
                                <div class="am-tab-panel am-active am-fade am-in" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g tpl-amazeui-form">
                        <div class="tpl-form-body">
                            <form method="post" class="am-form am-form-horizontal" enctype='multipart/form-data' onsubmit="return check()">
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
                  <a href="/qwtwdba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwtwdba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
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
                                        只能为 JPEG 格式，规格建议为 640*900px，最大不超过 400K，<a href="/wdb/tpl.zip" target="_blank">点击这里</a> 下载 PSD 模板自行设计。</small>

                                    </div>
                                </div>
                                <hr>
                                <div class="am-form-group">
                                    <label for="pic2" class="am-u-sm-12 am-form-label">默认头像</label>
                                    <div class="am-u-sm-12">
              <?php
                    //默认头像
              if ($result['tplhead']):
                ?><a href="/qwtwdba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img class="avator" src="/qwtwdba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" alt="">
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
                                <div class="am-form-group">
                                    <label for="keyword" class="am-u-sm-12 am-form-label">海报发送关键词</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="keyword" name="text[keyword]" value="<?=$config['keyword']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="ticker_lifetime" class="am-u-sm-12 am-form-label">海报有效期（单位：天，最长30天）</label>
                                    <div class="am-u-sm-12">
                                        <input type="number" min="1" max="30" class="tpl-form-input" id="ticket_lifetime" name="text[ticket_lifetime]" value="<?=intval($config['ticket_lifetime'])?>">
                                        <small class="warning-text">（特别注意：海报有效期首次设置成功后，后续修改，时间只能改短不能改长，例如首次设置是10天，后续修改只能小于10天）</small>
                                    </div>
                                </div>
            </div>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 积分规则设置</p>
                </div>
            </div>
            <div class="am-from-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal0" class="am-form-label">首次关注奖励积分</label>
                                        <input type="number" id="goal0" name="text[goal0]" value="<?=intval($config['goal0'])?>" placeholder="首次关注奖励积分">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal" class="am-form-label">直接推荐奖励积分</label>
                                        <input type="number" id="goal" name="text[goal]" value="<?=floatval($config['goal'])?>" placeholder="直接推荐奖励积分">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal2" class="am-form-label">间接推荐奖励积分</label>
                                        <input type="number" id="goal2" name="text[goal2]" value="<?=intval($config['goal2'])?>" placeholder="间接推荐奖励积分">
                                    </div>
                                </div>
                </div>
            </div>
            <div class="am-form-group">
                <div class="am-u-sm-6">
                                <div class="am-form-group">
                                    <div>
                                    <label for="rank" class="am-form-label">积分排行榜显示数量</label>
                                        <input type="number" id="rank" name="text[rank]" value="<?=intval($config['rank'])?>" placeholder="输入积分排行榜显示数量">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-6">
                                <div class="am-form-group">
                                    <div>
                                    <label for="rank" class="am-form-label">积分名称自定义（为空则不修改，最长为两个字）</label>
                                        <input type="text" id="rank" name="text[score]"  maxlength='2' value="<?=$config['score']?>" placeholder="输入积分名称">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-12">
                                        <small class="warning-text">请核算后谨慎设置积分奖励规则，避免积分兑换奖品的门槛过低，造成不必要的损失，特别是在奖品设置时，单个奖品兑换所需的积分一定要高于首次关注奖励积分。因为运营操作不当，积分兑换门槛设置过低导致的损失，我方不予承担。</small>
                                        </div>
            </div>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 文案设置</p>
                </div>
            </div>
                                <div class="am-form-group">
                                    <label for="text_follow_url" class="am-u-sm-12 am-form-label">引导用户快速关注的页面网址 <span class="tpl-form-line-small-title">Lead Site</span></label>
                                    <div class="am-u-sm-12">
              <input type="text" class="tpl-form-input" class="form-control" id="subhref" name="text[subhref]" placeholder="http://" value="<?=$config['subhref']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="text_follow_url" class="am-u-sm-12 am-form-label">活动说明网址 </label>
                                    <div class="am-u-sm-12">
              <input type="text" class="tpl-form-input" class="form-control" id="text_follow_url" name="text[text_follow_url]" placeholder="http://" value="<?=$config['text_follow_url']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal3" class="am-u-sm-12 am-form-label">扫码关注点击验证后自动回复文案</label>
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
                                    <label for="goal" class="am-u-sm-12 am-form-label">直接积分奖励文案</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" id="goal" name="text[text_goal]" placeholder="您的朋友「%s」成为了您的支持者！您已获得了相应的积分奖励，请注意查收。" value="<?=htmlspecialchars(str_replace("\n", '\n',$config['text_goal']))?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">间接积分奖励文案</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" id="goal2" name="text[text_goal2]" placeholder="您的朋友「%s」又获得了一个新的支持者！" value="<?=htmlspecialchars(str_replace("\n", '\n',$config['text_goal2']))?>">
                                    </div>
                                </div>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 风险控制选项（直接粉丝超过多少，间接粉丝少于多少则锁定，锁定后不能兑换没有新增积分；单个用户兑换奖品次数上限）</p>
                </div>
            </div>
            <div class="am-from-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="risk_level1" class="am-form-label">推荐多少直接粉丝</label>
                        <input type="number" id="risk_level1" name="px[risk_level1]" value="<?=intval($config['risk_level1'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="risk_level2" class="am-form-label">少于多少个间接粉丝</label>
                        <input type="number" id="risk_level2" name="px[risk_level2]" value="<?=intval($config['risk_level2'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="day_limit" class="am-form-label">单个用户兑换奖品次数上限（0为不限）</label>
                        <input type="number" id="day_limit" name="px[day_limit]" value="<?=floatval($config['day_limit'])?>">
                                    </div>
                                </div>
                </div>
            </div>
                                <div class="am-form-group">
                                    <label for="risk" class="am-u-sm-12 am-form-label">锁定用户文案</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" id="risk" name="text[text_risk]" placeholder='您的账号存在安全风险，暂时无法兑换奖品。' value="<?=htmlspecialchars($config['text_risk'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">保存个性化设置</button>
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
function check(){
    if ($('#subhref').val()==''){
        swal({
            title: "失败",
            text: "引导用户快速关注的页面网址必填，请填写完整后重试",
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "我知道了",
            closeOnConfirm: true,
        })
        return false;
    }else{
        return true;
    }
}

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
    title: "积分宝订阅号已过期",
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
           window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/product/2";
      }
  })
</script>
<?endif?>
