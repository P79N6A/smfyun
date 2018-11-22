<style type="text/css">
  .am-form .am-form-label{
    text-align: left !important;
  }
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
                <li><a href="#" class="am-icon-home">订单宝</a></li>
                <li class="am-active">基础设置</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 am-form-group-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>基础设置</span>
                            </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab2">
                                    <div id="wrapperB" class="wrapper">
                <div class="tpl-block tpl-amazeui-form">

                    <div class="am-g">
                        <div class="tpl-form-body tpl-form-line">
                            <form method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
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
                  <a href="/qwtfxba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwtfxba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
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
                                        只能为 JPEG 格式，规格建议为 640*900px，最大不超过 400K，<a href="/fxb/tpl.zip" target="_blank">点击这里</a> 下载 PSD 模板自行设计。</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="pic2" class="am-u-sm-12 am-form-label">默认头像（获取头像失败时会用改头像，可选） <span class="tpl-form-line-small-title">Avator</span></label>
                                    <div class="am-u-sm-12">
              <?php
                    //默认头像
              if ($result['tplhead']):
                ?><a href="/qwtfxba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img class="avator" src="/qwtfxba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" alt="">
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
                                    <label for="ticker_lifetime" class="am-u-sm-12 am-form-label">海报有效期（单位：天，最长30天） <span class="tpl-form-line-small-title">Validity</span></label>
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
                                    <label for="user-name" class="am-u-sm-12 am-form-label">三级收益开关：
                                        <small class="warning-text">注意：按照微信最新的规则，三级收益开关请不要开启，否则会导致封号和封禁微信支付。</small></label>
                                    <div class="am-u-sm-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-1" class="switch-type green <?=$config['kaiguan_needpay'] == 1 ? 'green-on' : ''?>">开</li>
                                    <li id="switch-4" class="switch-type red <?=$config['kaiguan_needpay'] == 1 ? '' : 'red-on'?>">关</li>
                                </ul>
                                <input type="hidden" name="text[kaiguan_needpay]" value="<?=$config['kaiguan_needpay'] == 1 ? 1 : 0?>" id="flock">
                            </div>
                            </div>
                </div>

            <div class="am-form-group">
                <div class="am-u-sm-12">
                                <div class="am-form-group">
                                    <div>
                                    <label for="money0" class="am-form-label">自购返利比例（%）</label>
                  <input type="number" id="money0" name="text[money0]" value="<?=intval($config['money0'])?>">
                                    </div>
                                </div>
                </div>
            </div>
            <div class="am-form-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="title1" class="am-form-label">一级名称</label>
                  <input type="text" id="title1" name="text[title1]" value="<?=trim($config['title1'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="title2" class="am-form-label">二级名称</label>
                  <input type="text" id="title2" name="text[title2]" value="<?=trim($config['title2'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4 level3" <?=$config['kaiguan_needpay'] == 1 ? '' : 'style="display:none;"'?>>
                                <div class="am-form-group">
                                    <div>
                                    <label for="title3" class="am-form-label">三级名称</label>
                  <input type="text" id="title3" name="text[title3]" value="<?=trim($config['title3'])?>">
                                    </div>
                                </div>
                </div>
            </div>
            <div class="am-form-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="money1" class="am-form-label">一级佣金比例（%）</label>
                  <input type="number" id="money1" name="text[money1]" value="<?=intval($config['money1'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="money2" class="am-form-label">二级佣金比例（%）</label>
                  <input type="number" id="money2" name="text[money2]" value="<?=intval($config['money2'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4 level3" <?=$config['kaiguan_needpay'] == 1 ? '' : 'style="display:none;"'?>>
                                <div class="am-form-group">
                                    <div>
                                    <label for="money3" class="am-form-label">三级佣金比例（%）</label>
                  <input type="number" id="money3" name="text[money3]" value="<?=intval($config['money3'])?>">
                                    </div>
                                </div>
                </div>
            </div>
            <div class="am-form-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="money_out" class="am-form-label">最小提现金额（分）</label>
                  <input type="number" id="money_out" name="text[money_out]" value="<?=intval($config['money_out'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="money_out_buy" class="am-form-label">提现需消费（分）</label>
                  <input type="number" id="money_out_buy" name="text[money_out_buy]" value="<?=intval($config['money_out_buy'])?>">
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


                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">未购买用户生成海报？</label>
                                    <div class="am-u-sm-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-2" class="switch-type green <?=$config['haibao_needpay'] == 0 || !$config['haibao_needpay'] ? 'green-on' : ''?>">允许</li>
                                    <li id="switch-3" class="switch-type red <?=$config['haibao_needpay'] === "1" ? 'red-on' : ''?>">不允许</li>
                                </ul>
                                <input type="hidden" name="text[haibao_needpay]" value="<?=$config['haibao_needpay'] == 0 || !$config['haibao_needpay'] ? 0 : 1?>" id="flock2">
                            </div>
                            </div>
                </div>

            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p>积分设置 </p>
                </div>
                </div>
                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label warning-text">注意：请核算后谨慎设置积分奖励规则，避免积分兑换奖品的门槛过低，造成不必要的损失，特别是在奖品设置时，单个奖品兑换所需的积分一定要高于首次扫码积分。因为运营操作不当，积分兑换门槛设置过低导致的损失，我方不予承担。</label>
            </div>
            <div class="am-form-group">
                <div class="am-u-sm-3">
                                <div class="am-form-group">
                                    <div>
                                    <label for="money_init" class="am-form-label">首次关注积分</label>
                  <input type="number" id="money_init" name="text[money_init]" value="<?=intval($config['money_init'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-3">
                                <div class="am-form-group">
                                    <div>
                                    <label for="money_scan1" class="am-form-label">一级扫码积分</label>
                  <input type="number" id="money_scan1" name="text[money_scan1]" value="<?=intval($config['money_scan1'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-3">
                                <div class="am-form-group">
                                    <div>
                                    <label for="money_scan2" class="am-form-label">二级扫码积分</label>
                  <input type="number" id="money_scan2" name="text[money_scan2]" value="<?=intval($config['money_scan2'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-3">
                                <div class="am-form-group">
                                    <div>
                                    <label for="scorename" class="am-form-label">自定义积分名称</label>
                  <input type="text" id="scorename" name="text[scorename]" value="<?=trim($config['scorename'])?>">
                                    </div>
                                </div>
                </div>
            </div>
            <div class="tpl-content-scope">
                <div class="note note-danger">
                    <p>风险控制：超过以下警戒值的用户，会被锁定不能领取奖品。具体说明请参见说明书，建议谨慎设置。 </p>
                </div>
                </div>
            <div class="am-form-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="risk_level1" class="am-form-label">推荐多少直接粉丝？</label>
                  <input type="number" id="risk_level1" name="px[risk_level1]" value="<?=intval($config['risk_level1'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="risk_level2" class="am-form-label">少于多少个间接粉丝？</label>
                  <input type="number" id="risk_level2" name="px[risk_level2]" value="<?=floatval($config['risk_level2'])?>">
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
                                    <label for="risk" class="am-u-sm-12 am-form-label">锁定用户提示文案 </label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="risk" name="text[text_risk]" placeholder='您的账号存在安全风险，暂时无法兑换奖品。' value="<?=htmlspecialchars($config['text_risk'])?>">
                                    </div>
                                </div>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p>消息设置 </p>
                </div>
                </div>
                                <div class="am-form-group">
                                    <label for="qwt_fxburl" class="am-u-sm-12 am-form-label">常见问题说明网址 </label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="qwt_fxburl" name="text[qwt_fxburl]" placeholder="http://" value="<?=$config['qwt_fxburl']?>">
                                        <small class="warning-text">建议设置为常见问题的微杂志</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="fxb_desc" class="am-u-sm-12 am-form-label">扫码后推送文案</label>
                                    <div class="am-u-sm-12">
                                        <textarea class="" id="qwt_fxbdesc" name="text[qwt_fxbdesc]" style="height:150px;"><?=htmlspecialchars(str_replace("\n", '\n',$config['qwt_fxbdesc']))?></textarea>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="qwt_fxbmoney_desc" class="am-u-sm-12 am-form-label">活动说明文案</label>
                                    <div class="am-u-sm-12">
                                        <textarea class="" id="qwt_fxbmoney_desc" name="text[qwt_fxbmoney_desc]" style="height:150px;"><?=htmlspecialchars($config['qwt_fxbmoney_desc'])?></textarea>
                                    </div>
                                </div>

                                <div class="am-form-group">
                                    <label for="send" class="am-form-label am-u-sm-12">海报生成前发送消息：{TIME} 会被替换成海报过期时间 </label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="send" name="text[text_fxbsend]" placeholder="您的专属海报生成中，请稍等..." value="<?=htmlspecialchars(str_replace("\n", '\n',$config['text_fxbsend']))?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal" class="am-form-label am-u-sm-12">收益通知消息模板（行业：IT科技 - 互联网|电子商务，模板标题「收益发放通知」模板编号：OPENTM207809222） </label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" id="goal" name="text[msg_score_tpl]" placeholder="" value="<?=$config['msg_score_tpl']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-form-label am-u-sm-12">账户余额通知消息模板（行业：IT科技 - 互联网|电子商务，模板标题「账户余额变动通知」模板编号：OPENTM205454780） </label>
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
                    $(document).ready(function() {
                      // var sanji=<?=$config['kaiguan_needpay']?>;
                      var sanji = 1
                      if(sanji!=1){
                        $('#level3').hide();
                      }
                    });
    $('#switch-1').click(function(){
      $(this).addClass('green-on');
      $('#switch-4').removeClass('red-on');
      $('#flock').val(1);
                        $('.level3').show();
      })
    $('#switch-4').click(function(){
      $(this).addClass('red-on');
      $('#switch-1').removeClass('green-on');
      $('#flock').val(0);
                        $('.level3').hide();
      })
    $('#switch-2').click(function(){
      $(this).addClass('green-on');
      $('#switch-3').removeClass('red-on');
      $('#flock2').val(0);
      })
    $('#switch-3').click(function(){
      $(this).addClass('red-on');
      $('#switch-2').removeClass('green-on');
      $('#flock').val(1);
      })
                    </script>
<?php if($hasover==1):?>
<script type="text/javascript">
  swal({
    title: "订单宝已过期",
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
           window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/product/8";
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

<?php if($result['error7']):?>
$(document).ready(function(){
    swal({
        title: "失败",
        text: "您还没有进行有赞授权，请先前往【绑定我们】-【有赞一键授权】进行授权",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "立即前往",
        closeOnConfirm: false,
        showCancelButton: true,
        cancelButtonText: "取消",
        closeOnCancel: true,
    },
    function(isConfirm){
      if (isConfirm) {
                window.location.href='http://<?=$_SERVER["HTTP_HOST"]?>/qwta/yzoauth';
      }
  })
})
<?php endif?>
</script>
<?endif?>
