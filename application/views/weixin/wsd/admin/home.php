<style>
.nav-tabs-custom>.nav-tabs>li.active {
  border-top-color: #00a65a;
}
.nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: #00a65a;
  }
  .reduce,.add{
    font-size: 14px;
    position: relative;
    bottom: 10px;
  }
  .add{
    margin-left: 20px;
    margin-right: 30px;
  }
  .loc{
    margin-top: 5px;
    margin-bottom: 5px;
  }
</style>

<section class="content-header">
  <h1>
    基础设置
    <small>核心参数配置</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">基础设置</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">

          <ul class="nav nav-tabs">
            <li id="cfg_yz_li"><a href="#cfg_yz" data-toggle="tab">绑定有赞</a></li>
            <li id="cfg_wx_li"><a href="#cfg_wx" data-toggle="tab">微信参数</a></li>
            <li id="cfg_menu_li"><a href="#cfg_menu" data-toggle="tab">菜单配置</a></li>
            <li id="cfg_text_li"><a href="#cfg_text" data-toggle="tab">公告设置</a></li>
            <li id="cfg_surl_li"><a href="#cfg_surl" data-toggle="tab">有赞店铺地址设置</a></li>
            <li id="cfg_account_li"><a href="#cfg_account" data-toggle="tab">密码修改</a></li>
          </ul>

          <?php
          if ($_POST['cfg']) $active = 'wx';
          if (!$_POST || $_POST['yz']) $active = 'yz';
          if ($_POST['menu']) $active = 'menu';
          if ($_POST['text']) $active = 'text';
          if ($_POST['shopurl']) $active = 'surl';
          if (isset($_POST['password'])) $active = 'account';
          ?>

          <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
          </script>

          <div class="tab-content">

            <div class="tab-pane" id="cfg_yz">

                <!-- <div class="callout callout-danger">
                  <p>首次使用，请 <a href="https://wap.koudaitong.com/v2/showcase/feature?alias=j2avl2px" target="_blank">点击这里</a> 查看订单宝系统使用说明书</p>
                </div> -->

               <!--  <div class="callout callout-success">
                  <h4>有赞插件配置信息</h4>
                  <p><b>插件 url：</b> <u><?='http://'.$_SERVER["HTTP_HOST"].'/api/wsd/'.$_SESSION['wsda']['user'];?></u></p>
                  <p><b>插件 token：</b> <u>quanfenxiao2016</u></p>
                  <p><a href="http://koudaitong.com/apps/third?plugin_name=second" target="_blank">点击这里</a> 进入有赞插件页面设置</p>
                </div> -->

                <!-- <?php if ($result['ok'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>有赞配置保存成功!</div>
                <?php endif?> -->

                <!-- form start -->
                <form role="form" method="post">
                  <div class="box-body">

                    <!-- <div class="form-group">
                      <label for="youzan_appid">有赞 AppID（应用ID）</label>
                      <input type="text" class="form-control" id="youzan_appid" placeholder="输入有赞 AppID" maxlength="18" name="cfg[youzan_appid]" value="<?=$config['youzan_appid']?>">
                    </div>

                    <div class="form-group">
                      <label for="youzan_appsecret">有赞 AppSecert（应用密钥）</label>
                      <input type="text" class="form-control" id="youzan_appsecret" placeholder="输入有赞 AppSecert" maxlength="32" name="cfg[youzan_appsecret]" value="<?=$config['youzan_appsecret']?>">
                    </div> -->

                    <!-- <div class="form-group">
                      <label for="scene_id">有赞后台「单个二维码扫描」接口中的 scene_id</label>
                      <input type="text" class="form-control" id="scene_id" placeholder="输入带参数二维码的 scene_id" name="cfg[scene_id_wsd]" value="<?=$config['scene_id_wsd']?>">
                    </div> -->
                    <p class="alert alert-danger alert-dismissable">注意：请务必点击有赞一键授权，否则功能将无法使用</p>
                    <?php if($oauth==1):?>
                  <div class="lab4">
                    <a href='/wsda/oauth'><button type="button" class="btn btn-warning">点击一键授权有赞</button></a>
                  </div>
                  <br>
                  <?else:?>
                  <div class="lab4">
                    <a href='/wsda/oauth'><button type="button" class="btn btn-warning">点击重新授权有赞</button></a>
                  </div>
                  <?endif?>

                  </div><!-- /.box-body -->

                  <!-- <div class="box-footer">
                    <input type="hidden" name="yz" value="1">
                    <button type="submit" class="btn btn-success">保存有赞配置</button>
                  </div> -->
                </form>
            </div>
            <div class="tab-pane" id="cfg_wx">

                <?php if ($result['ok'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>微信配置保存成功!</div>
                <?php endif?>

                <?php if ($result['err1']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err1']?></div>
                <?php endif?>

                <!-- form start -->
                <form role="form" method="post" enctype="multipart/form-data">
                  <div class="box-body">

                    <div class="form-group">
                      <label for="name">微信公众号名称</label>
                      <input type="text" class="form-control" id="name" placeholder="输入公众号名称" maxlength="20" name="cfg[name]" value="<?=$config['name']?>">
                    </div>

                    <div class="form-group">
                      <label for="appid">微信公众号App Id（填写后不可修改）</label>
                      <input type="text" class="form-control" id="appid" placeholder="输入 App Id" maxlength="18" name="cfg[appid]" value="<?=$config['appid']?>"<?php if($config['appid']) echo 'readonly=""'?>>
                    </div>

                    <div class="form-group">
                      <label for="appsecret">微信公众号App Secret</label>
                      <input type="text" class="form-control" id="appsecret" placeholder="输入 App Secret" maxlength="32" name="cfg[appsecret]" value="<?=$config['appsecret']?>">
                    </div>

                    <div class="form-group">
                      <label for="partnerid">微信公众号商户号（partnerid）</label>
                      <input type="number" class="form-control" id="partnerid" placeholder="输入 partnerid" maxlength="32" name="cfg[partnerid]" value="<?=$config['partnerid']?>">
                    </div>

                    <div class="form-group">
                      <label for="partnerkey">微信公众号商户密钥（partnerkey）</label>
                      <input type="text" class="form-control" id="partnerkey" placeholder="输入 partnerkey" maxlength="32" name="cfg[partnerkey]" value="<?=$config['partnerkey']?>">
                    </div>

                    <div class="form-group">
                      <label for="cert">微信支付证书 apiclient_cert.pem<?php if($result['cert_file_exists']) echo ' <span class="label label-warning">已上传</span>'?></label>
                      <input type="file" class="form-control" id="cert" name="cert">
                    </div>

                    <div class="form-group">
                      <label for="key">微信支付证书 apiclient_key.pem<?php if($result['key_file_exists']) echo ' <span class="label label-warning">已上传</span>'?></label>
                      <input type="file" class="form-control" id="key" name="key">
                    </div>

                  </div>

                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">保存微信配置</button>
                  </div>
                </form>
            </div>

            <div class="tab-pane" id="cfg_menu">

                <?php if ($result['ok2'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>菜单配置保存成功!</div>
                <?php endif?>

                <?php if ($result['err2']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err2']?></div>
                <?php endif?>

                <!-- form start -->
                <form role="form" method="post" class="form-horizontal2">
                  <div class="box-body">

                  <!--   <div class="row">
                    <div class="col-lg-3 col-sm-3">
                      <div class="form-group">
                        <label for="menu0">1.「生成海报」对应的菜单 KEY</label>
                        <input type="text" class="form-control" id="menu0" placeholder="生成海报" maxlength="10" name="menu[key_wsdqrcode]" value="<?=$config['key_wsdqrcode']?>">
                      </div>
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-lg-8">
                      <div class="form-group">
                        <label for="menu1">2.「资产查询」对应的菜单 KEY</label>
                        <input type="text" class="form-control" id="menu1" placeholder="资产查询" maxlength="10" name="menu[key_wsdscore]" value="<?=$config['key_wsdscore']?>">
                      </div>
                    </div>
                    </div> -->
                    <div class="row">
                    <div class="col-lg-8">
                      <div class="form-group">
                        <label for="menu2">1.「个人中心」对应的菜单网址（需要在 mp 后台设置 Oauth 网页回调域名为 <?=$_SERVER["HTTP_HOST"]?>）</label>
                        <input readonly="" type="text" class="form-control" id="menu2" maxlength="10" value="<?='http://'.$_SERVER["HTTP_HOST"].'/wsd/index_oauth/'.$_SESSION['wsda']['bid'].'/memberpage'?>">
                      </div>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-lg-8">
                      <div class="form-group">
                        <label for="menu2">2.「代理申请」对应的菜单网址（需要在 mp 后台设置 Oauth 网页回调域名为 <?=$_SERVER["HTTP_HOST"]?>）</label>
                        <input readonly="" type="text" class="form-control" id="menu2" maxlength="10" value="<?='http://'.$_SERVER["HTTP_HOST"].'/wsd/index_oauth/'.$_SESSION['wsda']['bid'].'/form'?>">
                      </div>
                    </div>
                    </div>

                    <!-- <div class="alert alert-danger">以下为高级选项，如果不清楚如何设置，请保持为空。</div>

                    <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <label for="menu10">预留自定义 KEY</label>
                          <input type="text" class="form-control" id="menu10" placeholder="自定义 KEY1" maxlength="10" name="menu[key_c1_wsd]" value="<?=$config['key_c1_wsd']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <label for="v10">点击后回复文字（使用 \n 换行）</label>
                          <input type="text" class="form-control" id="v10" placeholder="点击自定义 KEY1 后的回复文字" maxlength="200" name="menu[value_c1_wsd]" value="<?=$config['value_c1_wsd']?>">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <input type="text" class="form-control" id="menu11" placeholder="自定义 KEY2" maxlength="10" name="menu[key_c2_wsd]" value="<?=$config['key_c2_wsd']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" id="v11" placeholder="点击自定义 KEY2 后的回复文字" maxlength="200" name="menu[value_c2_wsd]" value="<?=$config['value_c2_wsd']?>">
                        </div>
                      </div>
                    </div>

                   <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <input type="text" class="form-control" id="menu12" placeholder="自定义 KEY3" maxlength="10" name="menu[key_c3_wsd]" value="<?=$config['key_c3_wsd']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" id="v12" placeholder="点击自定义 KEY3 后的回复文字" maxlength="200" name="menu[value_c3_wsd]" value="<?=$config['value_c3_wsd']?>">
                        </div>
                      </div>
                    </div> -->

                  </div><!-- /.box-body -->

                  <!-- <div class="box-footer">
                    <button type="submit" class="btn btn-success">保存菜单配置</button>
                  </div> -->
                </form>
            </div>

            <div class="tab-pane" id="cfg_text">

                <?php if ($result['ok3'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>个性化信息更新成功!</div>
                <?php endif?>

                <?php if ($result['err3']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err3']?></div>
                <?php endif?>

                <!-- form start -->
                <form role="form" method="post" enctype="multipart/form-data">
                  <div class="box-body">

                    <div class="form-group">
                      <label for="wsd_desc">公告文字：</label>
                      <input class="form-control" id="wsd_desc" name="text[desc]" value="<?=$config['desc']?>"></input>
                    </div>

                    <div class="form-group">
                      <label for="wsd_url">公告链接网址（没有则不设置）：</label>
                      <input type="text" class="form-control" id="wsd_url" name="text[wsd_url]" placeholder="http://" value="<?=$config['wsd_url']?>">
                    </div>


                  </div><!-- /.box-body -->

                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">更新公告设置</button>
                  </div>
                </form>
            </div>
            <div class="tab-pane" id="cfg_surl">

                <?php if ($result['ok5'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>配置保存成功！</div>
                <?php endif?>

                <form role="form" method="post">
                  <div class="box-body">

                  <div class="form-group">
                      <label for="surl">有赞店铺地址设置</label>
                      <input type="text" class="form-control" id="surl" placeholder="http://"  name="shopurl" value="<?=$config['shopurl']?>">
                  </div>

                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">保存</button>
                  </div>
                  </div>
                </form>
            </div>
            <div class="tab-pane" id="cfg_account">

                <?php if ($result['ok4'] > 0):
                $_SESSION['wsda'] = null;
                ?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>新密码已生效，请重新登录</div>
                <?php endif?>

                <?php if ($result['err4']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err4']?></div>
                <?php endif?>

                <form role="form" method="post">
                  <div class="box-body">

                  <div class="form-group">
                      <label for="password">旧密码</label>
                      <input type="password" class="form-control" id="password" placeholder="请输入旧密码" maxlength="16" name="password">
                  </div>

                  <div class="form-group">
                      <label for="newpassword">新密码</label>
                      <input type="password" class="form-control" id="newpassword" placeholder="请输入新密码" maxlength="16" name="newpassword">
                  </div>

                  <div class="form-group">
                      <label for="newpassword2">重复新密码</label>
                      <input type="password" class="form-control" id="newpassword2" placeholder="请再次输入新密码" maxlength="16" name="newpassword2">
                  </div>

                  <div class="box-footer">
                    <input type="hidden" name="yz" value="1">
                    <button type="submit" class="btn btn-success">修改登录密码</button>
                  </div>
                </form>
            </div>

          </div>
      </div>

    </div><!--/.col (left) -->

</section><!-- /.content -->

