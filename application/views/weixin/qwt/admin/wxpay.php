<style type="text/css">
  label{
    text-align: left !important;
  }
    .am-badge{
        background-color: green;
    }
</style>
<section>

  <div class="tpl-page-container tpl-page-header-fixed" style="margin-left:0;">
    <div class="tpl-content-wrapper">
      <div class="tpl-content-page-title">
        微信支付
      </div>
      <ol class="am-breadcrumb">
        <li><a href="#" class="am-icon-home">绑定我们</a></li>
        <li class="am-active">微信支付</li>
      </ol>
      <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
        <div class="tpl-portlet">
    <?php if ($result['ok'] > 0):?>
      <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>微信配置保存成功!</div>
    <?php endif?>
          <div class="am-tabs tpl-index-tabs" data-am-tabs>
            <div class="am-tabs-bd">
              <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                <div id="wrapperA" class="wrapper">
                  <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                      <div class="am-u-sm-12">
                        <form method="post" enctype='multipart/form-data' class="am-form am-form-horizontal">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 购买的应用需使用现金红包、企业付款到零钱功能，或者实物奖品需设置所需付款金额，请按照以下步骤操作；无需使用，可不操作。 </p>
                </div>
            </div>
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">商户名（建议为公众号名称或品牌名称；不超过8个字）</label>
                            <div class="am-u-sm-12">
                              <input class="userinfo" type="text" name ="userinfo[name]" id="user-name" value="<?=$userinfo->name?>">
                            </div>
                          </div>
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">微信支付商户号</label>
                            <div class="am-u-sm-12">
          <input type="text" class="form-control" id="mchid" placeholder="输入支付商户号" maxlength="32" name="cfg[mchid]" value="<?=$config['mchid']?>">
                            </div>
                          </div>
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">API 密钥</label>
                            <div class="am-u-sm-12">
          <input type="text" class="form-control" id="apikey" placeholder="输入 API秘钥" maxlength="32" name="cfg[apikey]" value="<?=$config['apikey']?>">
                            </div>
                          </div>
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">微信支付证书 apiclient_cert.pem <span class="tpl-form-line-small-title"><?php if($result['cert_file_exists']) echo ' <span class="label label-warning">已上传</span>'?></span></label>
                            <div class="am-u-sm-12">
                            <div class="am-form-group am-form-file">
                              <button type="button" class="am-btn am-btn-danger am-btn-sm">
                                <i class="am-icon-cloud-upload"></i> 选择要上传的文件</button>
                            <div id="cert-list" style="display:inline-block"></div>
                              <input name="cert" id="cert" type="file" multiple>
                            </div>
                            </div>
                          </div>
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">微信支付证书 apiclient_key.pem <span class="tpl-form-line-small-title"><?php if($result['key_file_exists']) echo ' <span class="label label-warning">已上传</span>'?></span></label>
                            <div class="am-u-sm-12">
                            <div class="am-form-group am-form-file">
                              <button type="button" class="am-btn am-btn-danger am-btn-sm">
                                <i class="am-icon-cloud-upload"></i> 选择要上传的文件</button>
                            <div id="key-list" style="display:inline-block"></div>
                              <input name="key" id="key" type="file" multiple>
                            </div>
                            </div>
                          </div>
                          <hr>
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                              <button type="submit" class="am-btn am-btn-primary">保存</button>
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
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
                            <script>
                              $(function() {
                                $('#cert').on('change', function() {
                                  var fileNames = '';
                                  $.each(this.files, function() {
                                    fileNames += '<span class="am-badge">' + this.name + '</span> ';
                                  });
                                  $('#cert-list').html(fileNames);
                                });
                              });
                              $(function() {
                                $('#key').on('change', function() {
                                  var fileNames = '';
                                  $.each(this.files, function() {
                                    fileNames += '<span class="am-badge">' + this.name + '</span> ';
                                  });
                                  $('#key-list').html(fileNames);
                                });
                              });
                            </script>
</section>
