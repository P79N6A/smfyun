<section>

  <div class="tpl-page-container tpl-page-header-fixed" style="margin-left:0;">
    <div class="tpl-content-wrapper">
      <div class="tpl-content-page-title">
        有赞一键授权
      </div>
      <ol class="am-breadcrumb">
        <li><a href="#" class="am-icon-home">绑定我们</a></li>
        <li class="am-active">有赞一键授权</li>
      </ol>
      <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
        <div class="tpl-portlet">
          <div class="tpl-portlet-title">
            <div class="tpl-caption font-green ">
              <span>有赞一键授权</span>
            </div>
          </div>
          <div class="am-tabs tpl-index-tabs" data-am-tabs>
            <div class="am-tabs-bd">
              <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                  <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                      <div class="am-u-sm-12">
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3">
                 <?php if($oauth==1):?>
                  <a href='/qwta/yzoauth?yzoauth=1'>
                  <button type="button" class="am-btn am-btn-primary">点击一键授权</button></a>
                <?php else:?>
                  <a href='/qwta/yzoauth?yzoauth=1'>
                  <button type="button" class="am-btn am-btn-warning"><?=$user->yzexpires_in<time()?'（您已经授权成功，如果遇到接口异常问题，）':''?>点击重新授权</button></a>
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
  </div>
</section>
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
