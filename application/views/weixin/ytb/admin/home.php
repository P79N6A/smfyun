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

                <div class="callout callout-success">
                  <h4>有赞插件配置信息</h4>
                  <p><b>插件 url：</b> <u><?='http://'.$_SERVER["HTTP_HOST"].'/api/ytb/'.$_SESSION['ytba']['user'];?></u></p>
                  <p><b>插件 token：</b> <u>yutangbao2016</u></p>
                  <p><a href="http://koudaitong.com/apps/third?plugin_name=second" target="_blank">点击这里</a> 进入有赞插件页面设置</p>
                </div>

                <?php if ($result['ok'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>有赞配置保存成功!</div>
                <?php endif?>

                <!-- form start -->
                <form role="form" method="post">
                  <div class="box-body">

                    <!--<div class="form-group">
                      <label for="youzan_appid">有赞 AppID（应用ID）</label>
                      <input type="text" class="form-control" id="youzan_appid" placeholder="输入有赞 AppID" maxlength="18" name="cfg[youzan_appid]" value="<?=$config['youzan_appid']?>">
                    </div>

                    <div class="form-group">
                      <label for="youzan_appsecret">有赞 AppSecert（应用密钥）</label>
                      <input type="text" class="form-control" id="youzan_appsecret" placeholder="输入有赞 AppSecert" maxlength="32" name="cfg[youzan_appsecret]" value="<?=$config['youzan_appsecret']?>">
                    </div>-->
                    <div class="form-group">
                      <label for="scene_id">有赞后台「单个二维码扫描」接口中的 scene_id</label>
                      <input type="text" class="form-control" id="scene_id" placeholder="输入带参数二维码的 scene_id" name="cfg[scene_id]" value="<?=$config['scene_id']?>">
                    </div>
                    <p class="alert alert-danger alert-dismissable">注意：请务必点击有赞一键授权，否则功能将无法使用</p>
                    <?php if($oauth==1):?>
                  <div class="lab4">
                    <a href='/ytba/oauth'><button type="button" class="btn btn-warning">点击一键授权有赞</button></a>
                  </div>
                  <br>
                  <?else:?>
                  <div class="lab4">
                    <a href='/ytba/oauth'><button type="button" class="btn btn-warning">点击重新授权有赞</button></a>
                  </div>
                  <?endif?>

                  </div><!-- /.box-body -->

                  <div class="box-footer">
                    <input type="hidden" name="yz" value="1">
                    <button type="submit" class="btn btn-success">保存有赞配置</button>
                  </div>
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
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>菜单配置保存成功!</div>
                <?php endif?>

                <?php if ($result['err2']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err2']?></div>
                <?php endif?>

                <!-- form start -->
                <form role="form" method="post" class="form-horizontal2">
                  <div class="box-body">
                  <div class="row">
                    <div class="col-lg-8">
                      <div class="form-group">
                        <label for="menu0">1.「生成海报」对应的菜单 KEY</label>
                        <input type="text" class="form-control" id="menu0" placeholder="生成海报" maxlength="10" name="menu[key_ytbqrcode]" value="<?=$config['key_ytbqrcode']?>">
                      </div>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-lg-8">
                      <div class="form-group">
                        <label for="menu1">1.「我的<?=$config['score_name']?>」对应的菜单 KEY</label>
                        <input type="text" class="form-control" id="menu1" placeholder="资产明细" maxlength="10" name="menu[key_ytbscore]" value="<?=$config['key_ytbscore']?>">
                      </div>
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-lg-8">
                      <div class="form-group">
                        <label for="menu2">2.「<?=$config['score_name']?>兑换」对应的菜单 KEY</label>
                        <input type="text" class="form-control" id="menu2" name="menu[key_ytbitem]" value="<?=$config['key_ytbitem']?>">
                      </div>
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-lg-8">
                      <div class="form-group">
                        <label for="menu2">3.「分享商品」对应的菜单网址（需要在 mp 后台设置 Oauth 网页回调域名为 <?=$_SERVER["HTTP_HOST"]?>）</label>
                        <input type="text" class="form-control" id="menu2" value="<?='http://'.$_SERVER["HTTP_HOST"].'/ytb/storefuop/'.$_SESSION['ytba']['bid']?>" readonly=''>
                      </div>
                    </div>
                    </div>

                    <div class="alert alert-danger">以下为高级选项，如果不清楚如何设置，请保持为空。</div>

                    <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <label for="menu10">预留自定义 KEY</label>
                          <input type="text" class="form-control" id="menu10" placeholder="自定义 KEY1" maxlength="10" name="menu[key_c1_ytb]" value="<?=$config['key_c1_ytb']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <label for="v10">点击后回复文字（使用 \n 换行）</label>
                          <input type="text" class="form-control" id="v10" placeholder="点击自定义 KEY1 后的回复文字" maxlength="200" name="menu[value_c1_ytb]" value="<?=$config['value_c1_ytb']?>">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <input type="text" class="form-control" id="menu11" placeholder="自定义 KEY2" maxlength="10" name="menu[key_c2_ytb]" value="<?=$config['key_c2_ytb']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" id="v11" placeholder="点击自定义 KEY2 后的回复文字" maxlength="200" name="menu[value_c2_ytb]" value="<?=$config['value_c2_ytb']?>">
                        </div>
                      </div>
                    </div>

                   <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <input type="text" class="form-control" id="menu12" placeholder="自定义 KEY3" maxlength="10" name="menu[key_c3_ytb]" value="<?=$config['key_c3_ytb']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" id="v12" placeholder="点击自定义 KEY3 后的回复文字" maxlength="200" name="menu[value_c3_ytb]" value="<?=$config['value_c3_ytb']?>">
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
                      <a href="/ytba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/ytba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" width="200" title="点击查看原图"></a>
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
                      <a href="/ytba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/ytba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
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
                        <input type="number" min="1" max="30" class="form-control" id="ticket_lifetime" name="text[ticket_lifetime]" value="<?=intval($config['ticket_lifetime'])?>">
                      </div>
                      </div>

                    </div>
                  <div class="alert alert-danger">邀请关注奖励设置</div>
                    <div class="row">
                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title1">每邀请关注人数</label>
                          <input type="number" class="form-control" id="title1" name="text[rw_num]" value="<?=trim($config['rw_num'])?>">
                        </div>
                      </div>

                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title2">有赞优惠券ID</label>
                          <input type="number" class="form-control" id="title2" name="text[rw_value]" value="<?=trim($config['rw_value'])?>">
                        </div>
                      </div>
                    </div>
                    <div class="alert alert-danger">积分奖励设置（按照成功交易订单金额的百分比）</div>
                      <div class="row">

                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title1">积分名称自定义</label>
                          <input type="text" class="form-control" id="title1" name="text[score_name]" value="<?=trim($config['score_name'])?>">
                        </div>
                      </div>
                      </div>
                     <div class="form-group">
                      <label style="color:red;max-width:1000px;width:1000px;margin-bottom: 10px;padding-left:10px">少塘主</label>
                    </div>

                    <div class="row">

                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title1">自购&直接推荐者购买积分%</label>
                          <input type="number" class="form-control" id="title1" name="text[score_shao1]" value="<?=trim($config['score_shao1'])?>">
                        </div>
                      </div>

                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title2">间接推荐者购买积分%</label>
                          <input type="number" class="form-control" id="title2" name="text[score_shao2]" value="<?=trim($config['score_shao2'])?>">
                        </div>
                      </div>

                    </div>
                     <div class="form-group">
                      <label style="color:red;max-width:1000px;width:1000px;margin-bottom: 10px;padding-left:10px">二塘主</label>
                    </div>

                    <div class="row">

                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title1">自购&直接推荐者购买积分%</label>
                          <input type="number" class="form-control" id="title1" name="text[score_er1]" value="<?=trim($config['score_er1'])?>">
                        </div>
                      </div>

                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title2">间接推荐者购买积分%</label>
                          <input type="number" class="form-control" id="title2" name="text[score_er2]" value="<?=trim($config['score_er2'])?>">
                        </div>
                      </div>

                    </div>
                     <div class="form-group">
                      <label style="color:red;max-width:1000px;width:1000px;margin-bottom: 10px;padding-left:10px">大塘主</label>
                    </div>

                    <div class="row">

                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title1">自购&直接推荐者购买积分%</label>
                          <input type="number" class="form-control" id="title1" name="text[score_da1]" value="<?=trim($config['score_da1'])?>">
                        </div>
                      </div>

                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title2">间接推荐者购买积分%</label>
                          <input type="number" class="form-control" id="title2" name="text[score_da2]" value="<?=trim($config['score_da2'])?>">
                        </div>
                      </div>

                    </div>
                     <div class="alert alert-danger">等级设置</div>
                    <div class="row">
                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title1">大塘主名称自定义</label>
                          <input type="text" class="form-control" id="title1" name="text[first]" value="<?=$config['first']?>" maxlength='4'>
                        </div>
                      </div>
                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title1">二塘主名称自定义</label>
                          <input type="text" class="form-control" id="title1" name="text[second]" value="<?=$config['second']?>" maxlength='4'>
                        </div>
                      </div>
                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title1">少塘主名称自定义</label>
                          <input type="text" class="form-control" id="title1" name="text[third]" value="<?=$config['third']?>" maxlength='4'>
                        </div>
                      </div>
                      </div>
                        <div class="form-group">
                      <label style="color:red;max-width:1000px;width:1000px;margin-bottom: 10px;padding-left:10px">二塘主升级条件</label>
                    </div>

                    <div class="row">

                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title1">累计直接推荐人数</label>
                          <input type="number" class="form-control" id="title1" name="text[pool_num1]" value="<?=trim($config['pool_num1'])?>">
                        </div>
                      </div>

                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title2">累计积分</label>
                          <input type="number" class="form-control" id="title2" name="text[pool_score1]" value="<?=trim($config['pool_score1'])?>">
                        </div>
                      </div>

                    </div>
                        <div class="form-group">
                      <label style="color:red;max-width:1000px;width:1000px;margin-bottom: 10px;padding-left:10px">大塘主升级条件</label>
                    </div>

                    <div class="row">

                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title1">累计直接推荐人数</label>
                          <input type="number" class="form-control" id="title1" name="text[pool_num2]" value="<?=trim($config['pool_num2'])?>">
                        </div>
                      </div>

                      <div class="col-lg-3 col-sm-4">
                        <div class="form-group">
                          <label for="title2">累计积分</label>
                          <input type="number" class="form-control" id="title2" name="text[pool_score2]" value="<?=trim($config['pool_score2'])?>">
                        </div>
                      </div>

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
                      <label for="ytb_url">常见问题说明网址</label>
                      <input type="text" class="form-control" id="ytb_url" name="text[ytb_url]" placeholder="http://" value="<?=$config['ytb_url']?>">
                      <p class="help-block">建议设置为常见问题的微杂志</p>
                    </div>
                    <div class="form-group">
                      <label for="ytb_money_desc">活动说明文案</label>
                      <textarea class="form-control" id="ytb_money_desc" name="text[ytb_money_desc]" style="height:150px"><?=htmlspecialchars($config['ytb_money_desc'])?></textarea>
                    </div>
                    <div class="form-group">
                      <label for="goal">发放通知消息模板（行业：IT科技 - 互联网|电子商务，模板标题「任务处理通知」模板编号：OPENTM200605630)</label>
                      <input type="text" class="form-control" id="goal" name="text[coupontpl]" placeholder="" value="<?=$config['coupontpl']?>">
                    </div>
                    <div class="form-group">
                        <label for="goal3">扫码关注后自动回复文案</label>
                        <input type="text" class="form-control" id="goal3" name="text[text_goal3]"  value="<?=$config['text_goal3']?$config['text_goal3']:'恭喜您成为了「%s」的支持者~'?>">
                      </div>

                    <div class="form-group">
                      <label for="goal4">重复扫描相同二维码回复文案</label>
                      <input type="text" class="form-control" id="goal4" name="text[text_goal4]" value="<?=$config['text_goal4']?$config['text_goal4']:'您已经是「%s」的支持者了，不用再扫了哦'?>">
                    </div>
                    <div class="form-group">
                      <label for="send">海报生成前发送消息</label>
                      <input type="text" class="form-control" id="send" name="text[text_send]" placeholder="您的专属海报生成中，请稍等..." value="<?=$config['text_send']?>">
                    </div>

                    <div class="form-group">
                      <label for="text_follow_url">首次关注推送网址</label>
                      <input type="text" class="form-control" id="text_follow_url" name="text[text_follow_url]" placeholder="http://" value="<?=$config['text_follow_url']?>">
                      <p class="help-block">建议设置为活动攻略的微杂志，示例网址：<a href="http://kdt.im/znDoi9NAE" target="_blank">http://kdt.im/znDoi9NAE</a></p>
                    </div>
                     <div class="form-group">
                      <label for="goal">自购文案自定义</label>
                      <input type="text" class="form-control" id="goal" name="text[shopping1]" placeholder="" value="<?=$config['shopping1']?>">
                    </div>
                     <div class="form-group">
                      <label for="goal">直接推荐者购买文案自定义</label>
                      <input type="text" class="form-control" id="goal" name="text[shopping2]" placeholder="" value="<?=$config['shopping2']?>">
                    </div>
                     <div class="form-group">
                      <label for="goal">间接推荐者购买文案自定义</label>
                      <input type="text" class="form-control" id="goal" name="text[shopping3]" placeholder="" value="<?=$config['shopping3']?>">
                    </div>
                     <div class="form-group">
                      <label for="goal">升级文案自定义</label>
                      <input type="text" class="form-control" id="goal" name="text[lv_shao]" placeholder="" value="<?=$config['lv_shao']?>">
                    </div>
                  </div><!-- /.box-body -->

                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">更新个性化配置</button>
                  </div>
                </form>
            </div>

            <div class="tab-pane" id="cfg_account">

                <?php if ($result['ok4'] > 0):
                $_SESSION['ytba'] = null;
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

