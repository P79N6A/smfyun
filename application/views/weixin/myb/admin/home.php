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
            <li id="cfg_text_li"><a href="#cfg_text" data-toggle="tab">个性化配置</a></li>
            <!--<li id="cfg_switch_li"><a href="#cfg_switch" data-toggle="tab">有赞积分同步</a></li>-->
            <li id="cfg_account_li"><a href="#cfg_account" data-toggle="tab">密码修改</a></li>
          </ul>

          <?php
          if ($_POST['cfg']) $active = 'wx';
          if (!$_POST || $_POST['yz']) $active = 'yz';
          if ($_POST['menu']) $active = 'menu';
          if ($_POST['text']) $active = 'text';
          if ($_POST['rsync']) $active = 'switch';
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
                  <p><b>插件 url：</b> <u><?='http://'.$_SERVER["HTTP_HOST"].'/api/myb/'.$_SESSION['myba']['user'];?></u></p>
                  <p><b>插件 token：</b> <u>fenxiaobao2015</u></p>
                  <p><a href="http://koudaitong.com/apps/third?plugin_name=second" target="_blank">点击这里</a> 进入有赞插件页面设置</p>
                </div> -->

                <?php if ($result['ok'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>有赞配置保存成功!</div>
                <?php endif?>

                <!-- form start -->
                <form role="form" method="post">
                  <div class="box-body">
                    <!-- <div class="form-group">
                      <label for="scene_id">有赞后台「单个二维码扫描」接口中的 scene_id</label>
                      <input type="text" class="form-control" id="scene_id" placeholder="输入带参数二维码的 scene_id"  name="cfg[scene_id_myb]" value="<?=$config['scene_id_myb']?>">
                    </div> -->
                    <p class="alert alert-danger alert-dismissable">注意：请务必点击有赞一键授权，否则功能将无法使用</p>
                    <?php if($oauth==1):?>
                  <div class="lab4">
                    <a href='/myba/oauth'><button type="button" class="btn btn-warning">点击一键授权有赞</button></a>
                  </div>
                  <br>
                  <?else:?>
                  <div class="lab4">
                    <a href='/myba/oauth'><button type="button" class="btn btn-warning">点击重新授权有赞</button></a>
                  </div>
                  <?endif?>

                  </div><!-- /.box-body -->

                  <!-- <div class="box-footer">
                    <input type="hidden" name="yz" value="1">
                    <button type="submit" class="btn btn-success">保存有赞配置</button>
                  </div> -->
                </form>
            </div>
            <div class="tab-pane" id="cfg_switch">
              <?php if ($result['ok7'] > 0):?>
                <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>开启成功!</div>
              <?php endif?>
              <?php if ($result['error7'] ==7):?>
                <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-check"></i>请在绑定有赞处点击一键授权</div>
              <?php endif?>
              <form role="form" method="post">
                <div class="form-group">
                  <label for="show" style="font-size:16px;">是否开启本功能？开启有赞积分同步功能之后，不能关闭。
                    注意：因为有赞积分只支持整数，积分宝和有赞积分兑换比例默认为1比1，请权衡好积分宝和有赞的积分奖励规则。</label>
                    <div class="radio">
                      <label class="checkbox-inline">
                        <input type="radio" name="rsync[switch]" id="rsync1" value="1" <?=$config['switch'] == 1 ? ' checked=""' : ''?>>
                        <span class="label label-success"  style="font-size:14px">开启</span>
                      </label>
                      <label class="checkbox-inline">
                        <input <?=$config['switch'] == 1 ? ' disabled' : ''?> type="radio" name="rsync[switch]" id="rsync0" value="0" <?=$config['switch'] === "0" ||!$config['switch']? ' checked=""' : ''?>>
                        <span class="label label-danger"  style="font-size:14px">关闭</span>
                      </label>
                    </div>
                  </div>
                  <button type="submit" class="btn btn-success">保存配置</button>
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

                      <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label class='menu' for="menu">一级菜单名称</label>
                      <input type="text" class="form-control" id="menu"  maxlength="10" name="menu[key_menu]" value="<?=$config['key_menu']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu0">1.「生成海报」自定义名称</label>
                      <input type="text" class="form-control" id="menu0"  maxlength="10" name="menu[key_qrcode]" value="<?=$config['key_qrcode']?>">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu1">2.「积分查询」自定义名称</label>
                      <input type="text" class="form-control" id="menu1"  maxlength="10" name="menu[key_score]" value="<?=$config['key_score']?>">
                    </div>
                  </div>
                </div>

                <div class="row">
                <div class="col-lg-8">
                  <div class="form-group">
                    <label for="menu2">3.「我的收益」对应的菜单网址（需要在 mp 后台设置 Oauth 网页回调域名为 <?=$_SERVER["HTTP_HOST"]?>）</label>
                    <input type="text" class="form-control" id="menu2" maxlength="10" value="<?='http://'.$_SERVER["HTTP_HOST"].'/myb/index_oauth/'.$_SESSION['myba']['bid'].'/home'?>">
                  </div>
                </div>
                </div>
                    <div class="alert alert-danger">以下为高级选项，如果不清楚如何设置，请保持为空。</div>

                     <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu10">一级菜单名称</label>
                      <input type="text" class="form-control" id="menu10" placeholder="一级菜单名称" maxlength="10" name="menu[key_b0]" value="<?=$config['key_b0']?>">
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
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="10" name="menu[key_b1]" value="<?=$config['key_b1']?>">
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
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="10" name="menu[key_b2]" value="<?=$config['key_b2']?>">
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
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="10" name="menu[key_b3]" value="<?=$config['key_b3']?>">
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
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="10" name="menu[key_b4]" value="<?=$config['key_b4']?>">
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
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="10" name="menu[key_b5]" value="<?=$config['key_b5']?>">
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
                      <input type="text" class="form-control" id="menu10" placeholder="一级菜单名称" maxlength="10" name="menu[key_c0]" value="<?=$config['key_c0']?>">
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
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="10" name="menu[key_c1]" value="<?=$config['key_c1']?>">
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
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="10" name="menu[key_c2]" value="<?=$config['key_c2']?>">
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
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="10" name="menu[key_c3]" value="<?=$config['key_c3']?>">
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
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="10" name="menu[key_c4]" value="<?=$config['key_c4']?>">
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
                      <input type="text" class="form-control" id="menu10" placeholder="二级菜单名称" maxlength="10" name="menu[key_c5]" value="<?=$config['key_c5']?>">
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
                  <div class="alert alert-danger">海报设置</div>
                    <?php
                    if ($result['tpl']):
                    ?>
                    <div class="form-group">
                      <a href="/myba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/myba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" width="200" title="点击查看原图"></a>
                    </div>
                    <?php endif?>

                    <div class="form-group">
                      <label for="pic">二维码海报背景图</label>
                      <input type="file" class="form-control" id="pic" name="pic" accept="image/jpeg">
                      <p class="help-block">只能为 JPEG 格式，规格建议为 640*900px，最大不超过 300K，<a href="/wdy/tpl.zip" target="_blank">点击这里</a> 下载 PSD 模板自行设计。</p>
                    </div>

                    <?php
                    //默认头像
                    if ($result['tplhead']):
                    ?>
                    <div class="form-group">
                      <a href="/myba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/myba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
                    </div>
                    <?php endif?>

                    <div class="form-group">
                      <label for="pic2">默认头像（获取头像失败时会用该头像，可选）</label>
                      <input type="file" class="form-control" id="pic2" name="pic2" accept="image/jpeg">
                      <p class="help-block">只能为 JPEG 格式，正方形，最大不超过 100K。</p>
                    </div>


                    <div class="row">

                      <div class="col-lg-2 col-sm-3">
                      <div class="form-group">
                        <label for="ql">二维码左边距（px）</label>
                        <input type="number" class="form-control" id="ql" name="px[px_qrcode_left]" value="<?=intval($config['px_qrcode_left'])?>">
                      </div>
                      </div>

                      <div class="col-lg-2 col-sm-3">
                      <div class="form-group">
                        <label for="qt">上边距</label>
                        <input type="number" class="form-control" id="qt" name="px[px_qrcode_top]" value="<?=floatval($config['px_qrcode_top'])?>">
                      </div>
                      </div>

                      <div class="col-lg-2 col-sm-3">
                      <div class="form-group">
                        <label for="qw">二维码高宽</label>
                        <input type="number" class="form-control" id="qw" name="px[px_qrcode_width]" value="<?=intval($config['px_qrcode_width'])?>">
                      </div>
                      </div>

                    </div>

                    <div class="row">

                      <div class="col-lg-2 col-sm-3">
                      <div class="form-group">
                        <label for="hl">头像左边距（px）</label>
                        <input type="number" class="form-control" id="hl" name="px[px_head_left]" value="<?=intval($config['px_head_left'])?>">
                      </div>
                      </div>

                      <div class="col-lg-2 col-sm-3">
                      <div class="form-group">
                        <label for="ht">上边距</label>
                        <input type="number" class="form-control" id="ht" name="px[px_head_top]" value="<?=floatval($config['px_head_top'])?>">
                      </div>
                      </div>

                      <div class="col-lg-2 col-sm-3">
                      <div class="form-group">
                        <label for="hw">头像高宽</label>
                        <input type="number" class="form-control" id="hw" name="px[px_head_width]" value="<?=intval($config['px_head_width'])?>">
                      </div>
                      </div>


                    </div>
                    <div class="row">

                      <div class="col-lg-6 col-sm-6">
                      <div class="form-group">
                        <label for="ticket_lifetime">海报有效期（单位：天，最长 30 天）</label>
                        <input type="number" min="1" max="30" class="form-control" id="ticket_lifetime" name="text[myb_ticket_lifetime]" value="<?=intval($config['myb_ticket_lifetime'])?>">
                      </div>
                      </div>

                    </div>
                    <!--<div class="row">

                       <div class="col-lg-2 col-sm-4">
                        <div class="form-group">
                          <label for="title3">总收益名称</label>
                          <input type="text" class="form-control" id="title3" name="text[title3]" value="<?=trim($config['title3'])?>">
                        </div>
                      </div>

                      <div class="col-lg-2 col-sm-4">
                        <div class="form-group">
                          <label for="title4">当前收益名称</label>
                          <input type="text" class="form-control" id="title4" name="text[title4]" value="<?=trim($config['title4'])?>">
                        </div>
                      </div>

                    </div>-->
                    <div class="alert alert-danger">佣金及提现设置</div>

            <!--          <div class="form-group">
                      <label for="myb_kaiguan">三级收益开关</label>
                      <label style="color:red;max-width:1000px;width:1000px;margin-bottom: 10px;padding-left:10px">注意：按照微信最新的规则，三级收益开关请不要开启，否则会导致封号和封禁微信支付。</label>
                      <div class="radio">
                        <label class="checkbox-inline"><input type="radio" name="text[kaiguan_needpay]" id="show0" value="1"<?=$config['kaiguan_needpay'] == 1 || !$config['kaiguan_needpay'] ? ' checked=""' : ''?> onclick='show()'><span class="label label-success" style="font-size:14px">开</span></label>
                        <label class="checkbox-inline"><input type="radio" name="text[kaiguan_needpay]" id="show1" value="0"<?=$config['kaiguan_needpay'] == 0 ? ' checked=""' : ''?> onclick='hide()'><span class="label label-danger" style="font-size:14px">关</label>
                      </div>
                    </div> -->

                    <div class="row">

                      <div class="col-lg-2 col-sm-4">
                        <div class="form-group">
                          <label for="title1">一级名称</label>
                          <input type="text" class="form-control" id="title1" name="text[title1]" value="<?=trim($config['title1'])?>">
                        </div>
                      </div>

                      <div class="col-lg-2 col-sm-4">
                        <div class="form-group">
                          <label for="title2">二级名称</label>
                          <input type="text" class="form-control" id="title2" name="text[title2]" value="<?=trim($config['title2'])?>">
                        </div>
                      </div>

                      <div class="col-lg-2 col-sm-4" id='nprecent3'>
                        <div class="form-group">
                          <label for="title3">三级名称</label>
                          <input type="text" class="form-control" id="title3" name="text[titlen3]" value="<?=trim($config['titlen3'])?>">
                        </div>
                      </div>
                    </div>

                    <div class="row">

                      <div class="col-lg-2 col-sm-4">
                        <div class="form-group">
                          <label for="money_out">最小提现金额（分）</label>
                          <input type="number" class="form-control" id="money_out" name="text[money_out]" value="<?=intval($config['money_out'])?>">
                        </div>
                      </div>

                      <div class="col-lg-2 col-sm-4">
                        <div class="form-group">
                          <label for="money_out_buy">提现需消费（分）</label>
                          <input type="number" class="form-control" id="money_out_buy" name="text[money_out_buy]" value="<?=intval($config['money_out_buy'])?>">
                        </div>
                      </div>

                      <div class="col-lg-2 col-sm-4">
                        <div class="form-group">
                          <label for="title5">自定义收益名称</label>
                          <input type="text" class="form-control" id="title5" name="text[title5]" value="<?=trim($config['title5'])?>">
                        </div>
                      </div>

                    </div>


                    <div class="row">

                      <div class="col-lg-2 col-sm-4">
                        <div class="form-group">
                          <label for="money0">自购返利比例（%）</label>
                          <input type="number" class="form-control" id="money0" name="text[money0]" value="<?=intval($config['money0'])?>">
                        </div>
                      </div>

                      <div class="col-lg-2 col-sm-4">
                        <div class="form-group">
                          <label for="money1">一级佣金比例（%）</label>
                          <input type="number" class="form-control" id="money1" name="text[money1]" value="<?=intval($config['money1'])?>">
                        </div>
                      </div>

                      <div class="col-lg-2 col-sm-4">
                        <div class="form-group">
                          <label for="money2">二级佣金比例（%）</label>
                          <input type="number" class="form-control" id="money2" name="text[money2]" value="<?=intval($config['money2'])?>">
                        </div>
                      </div>

                        <div class="col-lg-2 col-sm-4" id='precent3'>
                          <div class="form-group">
                            <label for="money2">三级佣金比例（%）</label>
                            <input type="number" class="form-control" id="money3" name="text[money3]" value="<?=intval($config['money3'])?>">
                          </div>
                        </div>

                    </div>


                    <div class="form-group">
                      <label for="myb_haibao">未购买用户生成海报？</label>
                      <div class="radio">
                        <label class="checkbox-inline"><input type="radio" name="text[haibao_needpay]" id="show0" value="0"<?=$config['haibao_needpay'] == 0 || !$config['haibao_needpay'] ? ' checked=""' : ''?>><span class="label label-success" style="font-size:14px">允许</span></label>
                        <label class="checkbox-inline"><input type="radio" name="text[haibao_needpay]" id="show1" value="1"<?=$config['haibao_needpay'] === "1" ? ' checked=""' : ''?>><span class="label label-danger" style="font-size:14px">不允许</label>
                      </div>
                    </div>
                          <div class="alert alert-danger">积分设置</div>
                          <label style="color:red;max-width:1000px;width:1000px;margin-bottom: 10px;padding-left:10px">注意：请核算后谨慎设置积分奖励规则，避免积分兑换奖品的门槛过低，造成不必要的损失，特别是在奖品设置时，单个奖品兑换所需的积分一定要高于首次扫码积分。因为运营操作不当，积分兑换门槛设置过低导致的损失，我方不予承担。</label>
                    <div class="row">

                      <div class="col-lg-2 col-sm-3">
                        <div class="form-group">
                          <label for="money_init">首次关注积分</label>
                          <input type="number" class="form-control" id="money_init" name="text[money_init]" value="<?=intval($config['money_init'])?>">
                        </div>
                      </div>

                      <div class="col-lg-2 col-sm-3">
                        <div class="form-group">
                          <label for="money_scan1">一级扫码积分</label>
                          <input type="number" class="form-control" id="money_scan1" name="text[money_scan1]" value="<?=intval($config['money_scan1'])?>">
                        </div>
                      </div>

                      <div class="col-lg-2 col-sm-3">
                        <div class="form-group">
                          <label for="money_scan2">二级扫码积分</label>
                          <input type="number" class="form-control" id="money_scan2" name="text[money_scan2]" value="<?=intval($config['money_scan2'])?>">
                        </div>
                      </div>

                      <div class="col-lg-2 col-sm-3" >
                        <div class="form-group">
                          <label for="scorename">自定义积分名称</label>
                          <input type="text" class="form-control" id="scorename" name="text[scorename]" value="<?=trim($config['scorename'])?>">
                        </div>
                      </div>

                    </div>
                <div class="alert alert-warning">风险控制：超过以下警戒值的用户，会被锁定不能领取奖品。具体说明请参见说明书，建议谨慎设置。</div>

                  <div class="row">

                    <div class="col-lg-2 col-sm-3">
                      <div class="form-group">
                        <label for="risk_level1">推荐多少直接粉丝？</label>
                        <input type="number" class="form-control" id="risk_level1" name="px[risk_level1]" value="<?=intval($config['risk_level1'])?>">
                      </div>
                    </div>

                    <div class="col-lg-2 col-sm-3">
                      <div class="form-group">
                        <label for="risk_level2">少于多少个间接粉丝？</label>
                        <input type="number" class="form-control" id="risk_level2" name="px[risk_level2]" value="<?=floatval($config['risk_level2'])?>">
                      </div>
                    </div>
                    <div class="col-lg-4 col-sm-4">
                      <div class="form-group">
                        <label for="day_limit">单个用户兑换奖品次数上限（0为不限）</label>
                        <input type="number" class="form-control" id="day_limit" name="px[day_limit]" value="<?=floatval($config['day_limit'])?>">
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="risk">锁定用户提示文案</label>
                    <input type="text" class="form-control" id="risk" name="text[text_risk]" placeholder='您的账号存在安全风险，暂时无法兑换奖品。' value="<?=$config['text_risk']?>">
                  </div>

                    <script type="text/javascript">
                    $(document).ready(function() {
                      var sanji=<?=$config['kaiguan_needpay']?>;
                      if(sanji!=1){
                        $('#precent3').hide();
                        $('#sprecent3').hide();
                        $('#nprecent3').hide();
                      }
                    });
                        function show(){
                          $('#precent3').show();
                          $('#sprecent3').show();
                          $('#nprecent3').show();
                        }
                        function hide(){
                          $('#precent3').hide();
                          $('#sprecent3').hide();
                          $('#nprecent3').hide();
                        }
                    </script>
                    <div class="alert alert-danger">消息设置</div>

                    <div class="form-group">
                      <label for="myb_url">常见问题说明网址</label>
                      <input type="text" class="form-control" id="myb_url" name="text[myb_url]" placeholder="http://" value="<?=$config['myb_url']?>">
                      <p class="help-block">建议设置为常见问题的微杂志</p>
                    </div>

                    <div class="form-group">
                      <label for="myb_desc">扫码后推送文案</label>
                      <textarea class="form-control" id="myb_desc" name="text[myb_desc]"><?=$config['myb_desc']?></textarea>
                    </div>

                    <div class="form-group">
                      <label for="myb_money_desc">活动说明文案</label>
                      <textarea class="form-control" id="myb_money_desc" name="text[myb_money_desc]" style="height:150px"><?=htmlspecialchars($config['myb_money_desc'])?></textarea>
                    </div>





                    <div class="form-group">
                      <label for="send">海报生成前发送消息：{TIME} 会被替换成海报过期时间</label>
                      <input type="text" class="form-control" id="send" name="text[text_mybsend]" placeholder="您的专属海报生成中，请稍等..." value="<?=htmlspecialchars($config['text_mybsend'])?>">
                    </div>

                    <div class="form-group">
                      <label for="goal">收益通知消息模板（「收益通知」模板编号：OPENTM206596805）</label>
                      <input type="text" class="form-control" id="goal" name="text[msg_score_tpl]" placeholder="" value="<?=$config['msg_score_tpl']?>">
                    </div>

                    <div class="form-group">
                      <label for="goal2">账户余额通知消息模板（「账户余额通知」模板编号：OPENTM204526957）</label>
                      <input type="text" class="form-control" id="goal2" name="text[msg_money_tpl]" placeholder="" value="<?=$config['msg_money_tpl']?>">
                    </div>

                  </div><!-- /.box-body -->

                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">更新个性化配置</button>
                  </div>
                </form>
            </div>

            <div class="tab-pane" id="cfg_account">

                <?php if ($result['ok4'] > 0):
                $_SESSION['myba'] = null;
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

