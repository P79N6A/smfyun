<style>
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
          <li id="cfg_wx_li"><a href="#cfg_wx" data-toggle="tab">微信参数</a></li>
          <li id="cfg_menu_li"><a href="#cfg_menu" data-toggle="tab">菜单配置</a></li>
          <!-- <li id="cfg_text_li"><a href="#cfg_text" data-toggle="tab">个性化配置</a></li>
          <li id="cfg_zero_li"><a href="#cfg_zero" data-toggle="tab">积分清零</a></li>
          <li id="cfg_area_li"><a href="#cfg_area" data-toggle="tab">选择可参与地区</a></li> -->
          <li id="cfg_account_li"><a href="#cfg_account" data-toggle="tab">密码修改</a></li>
        </ul>

        <?php
        if (!$_POST||$_POST['cfg']) $active = 'wx';
        if ($_POST['menu']) $active = 'menu';
        if ($_POST['text']) $active = 'text';
        if ($_POST['area']) $active = 'area';
        if (isset($_POST['password'])) $active = 'account';
        ?>

        <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
        </script>

        <div class="tab-content">



          <div class="tab-pane" id="cfg_wx">

            <?php if ($result['ok'] > 0):?>
              <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>微信配置保存成功!</div>
            <?php endif?>

            <!-- form start -->
            <form role="form" method="post" enctype='multipart/form-data'>
              <div class="box-body">
                <div class="form-group">
                <label for="appid">微信一键授权:</label><br>
                <?php if($user->refresh_token):?>
                  <a href='https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=wxd0b3a6ff48335255&pre_auth_code=<?=$pre_auth_code?>&redirect_uri=http://<?=$_SERVER["HTTP_HOST"]?>/whba/home'><button type="button" class="btn btn-warning">点击重新授权</button></a>
                <?php else:?>
                  <a href='https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=wxd0b3a6ff48335255&pre_auth_code=<?=$pre_auth_code?>&redirect_uri=http://<?=$_SERVER["HTTP_HOST"]?>/whba/home'><button type="button" class="btn btn-warning">点击一键授权</button></a>
                <?php endif?>
                </div>
                <div class="form-group">
                  <label for="name">公众号名称</label>
                  <input type="text" class="form-control" id="name" placeholder="输入公众号名称" maxlength="20" name="cfg[name]" value="<?=$config['name']?>">
                </div>

                <!-- <div class="form-group">
                  <label for="appid">公众号App Id（填写后不可修改）</label>
                  <input type="text" class="form-control" id="appid" placeholder="输入 App Id" maxlength="18" name="cfg[appid]" value="<?=$config['appid']?>"<?php if($config['appid']) echo 'readonly=""'?>>
                </div>

                <div class="form-group">
                  <label for="appsecret">公众号App Secret</label>
                  <input type="text" class="form-control" id="appsecret" placeholder="输入 App Secret" maxlength="32" name="cfg[appsecret]" value="<?=$config['appsecret']?>">
                </div> -->
                <p class="alert alert-danger alert-dismissable">以下为高级选项，如果需要添加微信红包的奖品类型，请填写，不需要保持为空。</p>
                <div class="form-group">
                  <label for="mchid">支付商户号</label>
                  <input type="text" class="form-control" id="mchid" placeholder="输入支付商户号" maxlength="32" name="cfg[mchid]" value="<?=$config['mchid']?>">
                </div>
                <div class="form-group">
                  <label for="apikey">API 秘钥</label>
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
               <!--  <div class="form-group">
                      <label for="key">微信CA证书rootca.pem<?php if($result['rootca_file_exists']) echo ' <span class="label label-warning">已上传</span>'?></label>
                      <input type="file" class="form-control" id="rootca" name="rootca">
                </div> -->

              </div><!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-success">保存微信配置</button>
              </div>
            </form>
          </div>
          <div class="tab-pane" id="cfg_menu">

            <?php if ($result['ok2'] > 0):?>
              <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>菜单配置保存成功!
              <?php if($result['menu']==1):?>
                菜单已生效!
              <?php endif?>
              <?php if($result['menu']==0):?>
                菜单未生效,请按照顺序填写菜单！
              <?php endif?>
              </div>
            <?php endif?>


            <!-- form start -->
            <form role="form" method="post" class="form-horizontal2">
              <div class="box-body">
                <div class="alert alert-danger">以下为高级选项，如果不清楚如何设置，请保持为空。</div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu10">一级菜单名称</label>
                      <input type="text" class="form-control" id="menu10" placeholder="一级菜单名称" maxlength="4" name="menu[key_a0]" value="<?=$config['key_a0']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <label for="v10">回复文本或链接(设定了二级菜单后，一级菜单的回复将失效)</label>
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_a0]" value="<?=$config['value_a0']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu10">二级菜单名称</label>
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_a1]" value="<?=$config['key_a1']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <label for="v10">回复内容（仅支持文本和链接）</label>
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_a1]" value="<?=$config['value_a1']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_a2]" value="<?=$config['key_a2']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_a2]" value="<?=$config['value_a2']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_a3]" value="<?=$config['key_a3']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_a3]" value="<?=$config['value_a3']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_a4]" value="<?=$config['key_a4']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_a4]" value="<?=$config['value_a4']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_a5]" value="<?=$config['key_a5']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_a5]" value="<?=$config['value_a5']?>">
                    </div>
                  </div>
                </div>





                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu10">一级菜单名称</label>
                      <input type="text" class="form-control" id="menu10" placeholder="一级菜单名称" maxlength="4" name="menu[key_b0]" value="<?=$config['key_b0']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <label for="v10">回复文本或链接(设定了二级菜单后，一级菜单的回复将失效)</label>
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_b0]" value="<?=$config['value_b0']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu10">二级菜单名称</label>
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_b1]" value="<?=$config['key_b1']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <label for="v10">回复内容（仅支持文本和链接）</label>
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_b1]" value="<?=$config['value_b1']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_b2]" value="<?=$config['key_b2']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_b2]" value="<?=$config['value_b2']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_b3]" value="<?=$config['key_b3']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_b3]" value="<?=$config['value_b3']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_b4]" value="<?=$config['key_b4']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_b4]" value="<?=$config['value_b4']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_b5]" value="<?=$config['key_b5']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_b5]" value="<?=$config['value_b5']?>">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu10">一级菜单名称</label>
                      <input type="text" class="form-control" id="menu10" placeholder="一级菜单名称" maxlength="4" name="menu[key_c0]" value="<?=$config['key_c0']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <label for="v10">回复文本或链接(设定了二级菜单后，一级菜单的回复将失效)</label>
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_c0]" value="<?=$config['value_c0']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu10">二级菜单名称</label>
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_c1]" value="<?=$config['key_c1']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <label for="v10">回复内容（仅支持文本和链接）</label>
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_c1]" value="<?=$config['value_c1']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_c2]" value="<?=$config['key_c2']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_c2]" value="<?=$config['value_c2']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_c3]" value="<?=$config['key_c3']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_c3]" value="<?=$config['value_c3']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_c4]" value="<?=$config['key_c4']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_c4]" value="<?=$config['value_c4']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="4" name="menu[key_c5]" value="<?=$config['key_c5']?>">
                    </div>
                  </div>
                  <div class="col-lg-9 col-sm-9">
                    <div class="form-group">
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）" maxlength="200" name="menu[value_c5]" value="<?=$config['value_c5']?>">
                    </div>
                  </div>
                </div>
              </div><!-- /.box-body -->

              <div class="box-footer">
                <button type="submit" class="btn btn-success">保存菜单配置</button>
              </div>
            </form>
          </div>


            <!-- 2015.12.16增加积分清零部分 -->

              <!-- 2015.12.16增加积分清零部分 -->

              <!-- 2015.12.21增加指定地区用户参与部分 -->

<div class="tab-pane" id="cfg_account">

  <?php if ($result['ok4'] > 0):
  $_SESSION['whba'] = null;
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

