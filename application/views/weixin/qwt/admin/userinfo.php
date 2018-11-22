<style type="text/css">
  label{
    text-align: left !important;
  }
</style>
<section>

  <div class="tpl-page-container tpl-page-header-fixed" style="margin-left:0;">
    <div class="tpl-content-wrapper">
      <div class="tpl-content-page-title">
        账户信息
      </div>
      <ol class="am-breadcrumb">
        <li><a href="#" class="am-icon-home">会员中心</a></li>
        <li class="am-active">账户信息</li>
      </ol>
      <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
        <div class="tpl-portlet">
          <div class="tpl-portlet-title">
            <div class="tpl-caption font-green ">
              <span>账户信息</span>
            </div>
          </div>
          <div class="am-tabs tpl-index-tabs" data-am-tabs>
            <div class="am-tabs-bd">
              <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                <div id="wrapperA" class="wrapper">
                  <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                      <div class="am-u-sm-12">
                        <form method="post" class="am-form am-form-horizontal" onsubmit="return check()">
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">登录账号</label>
                            <div class="am-u-sm-12">
                              <input type="text" name="userinfo[user]" id="user-name" value="<?=$userinfo->user?>" readonly="true">
                            </div>
                          </div>
                          <div class="am-form-group">
                            <label for="user-name password" class="am-u-sm-12 am-form-label">密码</label>
                            <div class="am-u-sm-12">
                              <input class="userinfo" type="text" name="userinfo[pass]" id="user-pass" value="<?=$userinfo->pass?>" readonly="true">
                            </div>
                          </div>
                          <div class="am-form-group passwordconfirm" style="display:none;">
                            <label for="user-name" class="am-u-sm-12 am-form-label">确认新密码 <span class="passwordwarning" style="color:red;display:none;"> 两次输入的密码不一致！</span></label>
                            <div class="am-u-sm-12">
                              <input class="userinfo" type="password" id="user-passconfirm" value="<?=$userinfo->pass?>" readonly="true">
                            </div>
                          </div>
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">公众号名称</label>
                            <div class="am-u-sm-12">
                              <input type="text" name="userinfo[weixin_name]" id="user-name" value="<?=$userinfo->weixin_name?>" readonly="true">
                            </div>
                          </div>
                          <div class="am-form-group">
                            <label for="user-name" class="am-u-sm-12 am-form-label">邀请码</label>
                            <div class="am-u-sm-12">
                              <input type="text" id="user-name" value="<?=$userinfo->code?>" readonly="true">
                            </div>
                          </div>
                          <hr>
                          <div class="am-form-group">
                            <div class="am-u-sm-9 am-u-sm-push-3 edit_frame">
                              <button type="button" id='editbtn' class="am-btn am-btn-primary">修改账户信息</button>
                            </div>
                            <div class="am-u-sm-9 am-u-sm-push-3 btn_frame" style="display:none;">
                              <button type="submit" class="am-btn am-btn-primary">保存账户信息</button>
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
</section>
    <script src="/qwt/assets/js/amazeui.min.js"></script>
    <script src="/qwt/assets/js/app.js"></script>
    <script type="text/javascript">
    $("#editbtn").click(function() {
      $('.userinfo').removeAttr("readonly");
      $('.edit_frame').css({
        "display": 'none'
      });
      $('.btn_frame').css({
        "display": 'block'
      })
    });
    // $('#user-pass').focus(function(){
    //   $('.passwordconfirm').show();
    // })
    // $('#user-passconfirm').change(function(){
    //   var  a=$('#user-pass').val()
    //   var  b=$('#user-passconfirm').val()
    //   if (a==b) {
    //     $('.passwordwarning').hide();
    //   }else{
    //     $('.passwordwarning').show();
    //   };
    // })
    // function check(){
    //   var  a=$('#user-pass').val()
    //   var  b=$('#user-passconfirm').val()
    //   if (a==b) {
    //     return true;
    //   }else{
    //     alert('两次输入的密码不一致！密码修改失败。');
    //     return false;
    //   }
    //     ;
    // }
    </script>
