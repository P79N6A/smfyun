<div class="page-heading">
    <h3>
        微信支付
    </h3>
</div>
<!-- page heading end-->

<!--body wrapper start-->
<div class="wrapper">
<div class="row">
<div class="col-sm-12">
<section class="panel">
    <header class="panel-heading">
        <p class="alert alert-danger alert-dismissable">以下为高级选项，如果需要添加微信红包的奖品类型，请填写，不需要保持为空。</p>
    </header>
    <div class="panel-body">
    <?php if ($result['ok'] > 0):?>
      <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>微信配置保存成功!</div>
    <?php endif?>
    <form role="form" method="post" enctype='multipart/form-data'>
        <div class="form-group">
          <label class="col-sm-2 col-sm-2" for="mchid">支付商户号</label>
          <input type="text" class="form-control" id="mchid" placeholder="输入支付商户号" maxlength="32" name="cfg[mchid]" value="<?=$config['mchid']?>">
        </div>
        <div class="form-group">
          <label class="col-sm-2 col-sm-2" for="apikey">API 秘钥</label>
          <input type="text" class="form-control" id="apikey" placeholder="输入 API秘钥" maxlength="32" name="cfg[apikey]" value="<?=$config['apikey']?>">
        </div>

        <div class="form-group">
              <label for="cert">微信支付证书 apiclient_cert.pem<?php if($result['cert_file_exists']) echo ' <span class="label label-warning">已上传</span>'?></label>
              <input type="file" class="form-control" id="cert" name="cert">
            </div>

        <div class="form-group">
              <label for="key">微信支付证书 apiclient_key.pem<?php if($result['key_file_exists']) echo ' <span class="label label-warning">已上传</span>'?></label>
              <input type="file" class="form-control" id="key" name="key">
        </div>
         <div class="box-footer">
            <button type="submit" class="btn btn-success">保存微信配置</button>
          </div>
    </form>
    </div>
</section>
