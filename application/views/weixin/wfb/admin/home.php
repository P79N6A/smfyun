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
          <li id="cfg_text_li"><a href="#cfg_text" data-toggle="tab">个性化配置</a></li>
          <li id="cfg_zero_li"><a href="#cfg_zero" data-toggle="tab">积分清零</a></li>
          <li id="cfg_area_li"><a href="#cfg_area" data-toggle="tab">选择可参与地区</a></li>
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
                  <a href='https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=wxc520face24b8f175&pre_auth_code=<?=$pre_auth_code?>&redirect_uri=http://<?=$_SERVER["HTTP_HOST"]?>/wfba/home'><button type="button" class="btn btn-warning">点击重新授权</button></a>
                <?php else:?>
                  <a href='https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=wxc520face24b8f175&pre_auth_code=<?=$pre_auth_code?>&redirect_uri=http://<?=$_SERVER["HTTP_HOST"]?>/wfba/home'><button type="button" class="btn btn-warning">点击一键授权</button></a>
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
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label class='menu' for="menu">一级菜单名称</label>
                      <input type="text" class="form-control" id="menu"  maxlength="4" name="menu[key_menu]" value="<?=$config['key_menu']?>">
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu0">1.「生成海报」自定义名称</label>
                      <input type="text" class="form-control" id="menu0"  maxlength="4" name="menu[key_qrcode]" value="<?=$config['key_qrcode']?>">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu1">2.「积分查询」自定义名称</label>
                      <input type="text" class="form-control" id="menu1"  maxlength="4" name="menu[key_score]" value="<?=$config['key_score']?>">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu2">3.「积分兑换」自定义名称</label>
                      <input type="text" class="form-control" id="menu2"  maxlength="4" name="menu[key_item]" value="<?=$config['key_item']?>">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu3">4.「积分排行」自定义名称</label>
                      <input type="text" class="form-control" id="menu3"  maxlength="4" name="menu[key_top]" value="<?=$config['key_top']?>">
                    </div>
                  </div>
                </div>
                <div class="alert alert-danger">以下为高级选项，如果不清楚如何设置，请保持为空。</div>
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
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）"  name="menu[value_b0]" value="<?=$config['value_b0']?>">
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
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）"  name="menu[value_b1]" value="<?=$config['value_b1']?>">
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
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）"  name="menu[value_b2]" value="<?=$config['value_b2']?>">
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
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）"  name="menu[value_b3]" value="<?=$config['value_b3']?>">
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
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）"  name="menu[value_b4]" value="<?=$config['value_b4']?>">
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
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）"  name="menu[value_b5]" value="<?=$config['value_b5']?>">
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
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）"  name="menu[value_c0]" value="<?=$config['value_c0']?>">
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
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）"  name="menu[value_c1]" value="<?=$config['value_c1']?>">
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
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）"  name="menu[value_c2]" value="<?=$config['value_c2']?>">
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
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）"  name="menu[value_c3]" value="<?=$config['value_c3']?>">
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
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）"  name="menu[value_c4]" value="<?=$config['value_c4']?>">
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
                      <input type="text" class="form-control" id="v10" placeholder="请填写文本消息或者链接（直接在浏览器地址栏复制链接填写即可）"  name="menu[value_c5]" value="<?=$config['value_c5']?>">
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

                <?php
                if ($result['tpl']):
                  ?>
                <div class="form-group">
                  <a href="/wfba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/wfba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" width="200" title="点击查看原图"></a>
                </div>
              <?php endif?>

              <div class="form-group">
                <label for="pic">二维码海报背景图</label>
                <input type="file" class="form-control" id="pic" name="pic" accept="image/jpeg">
                <p class="help-block">只能为 JPEG 格式，规格建议为 640*900px，最大不超过 300K，<a href="/wfb/tpl.zip" target="_blank">点击这里</a> 下载 PSD 模板自行设计。</p>
              </div>

              <?php
                    //默认头像
              if ($result['tplhead']):
                ?>
              <div class="form-group">
                <a href="/wfba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/wfba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
              </div>
            <?php endif?>

            <div class="form-group">
              <label for="pic2">默认头像（获取头像失败时会用该头像，可选）</label>
              <input type="file" class="form-control" id="pic2" name="pic2" accept="image/jpeg">
              <p class="help-block">只能为 JPEG 格式，正方形，最大不超过 100K。</p>
            </div>

            <div class="form-group">
              <label for="text_follow_url">首次关注推送网址</label>
              <input type="text" class="form-control" id="text_follow_url" name="text[text_follow_url]" placeholder="http://" value="<?=$config['text_follow_url']?>">
              <p class="help-block">建议设置为活动攻略的微杂志</p>
            </div>

            <div class="row">

              <div class="col-lg-2 col-sm-4">
                <div class="form-group">
                  <label for="goal0">首次关注奖励积分</label>
                  <input type="number" class="form-control" id="goal0" name="text[goal0]" value="<?=intval($config['goal0'])?>">
                </div>
              </div>

              <div class="col-lg-2 col-sm-4">
                <div class="form-group">
                  <label for="goal">直接推荐奖励积分</label>
                  <input type="number" class="form-control" id="goal" name="text[goal]" value="<?=floatval($config['goal'])?>">
                </div>
              </div>

              <div class="col-lg-2 col-sm-4">
                <div class="form-group">
                  <label for="goal2">间接推荐奖励积分</label>
                  <input type="number" class="form-control" id="goal2" name="text[goal2]" value="<?=intval($config['goal2'])?>">
                </div>
              </div>
              <label style="color:red;max-width:1000px;width:1000px;margin-bottom: 10px;padding-left:10px">注意：请核算后谨慎设置积分奖励规则，避免积分兑换奖品的门槛过低，造成不必要的损失，特别是在奖品设置时，单个奖品兑换所需的积分一定要低于首次关注奖励积分。因为运营操作不当，积分兑换门槛设置过低导致的损失，我方不予承担。</label>
            </div>

            <div class="row">

              <div class="col-lg-3 col-sm-6">
                <div class="form-group">
                  <label for="rank">积分排行榜显示数量</label>
                  <input type="number" class="form-control" id="rank" name="text[rank]" value="<?=intval($config['rank'])?>">
                </div>
              </div>
              <div class="col-lg-4 col-sm-6">
                <div class="form-group">
                  <label for="rank">积分名称自定义（为空则不修改,最长两个字）</label>
                  <input type="text" class="form-control" id="rank" name="text[scorename]"  maxlength='2' value="<?=$config['scorename']?>">
                </div>
              </div>
            </div>



            <div class="alert alert-danger">以下为高级选项，如果不清楚如何设置，建议采用默认值。</div>

            <div class="row">

              <div class="col-lg-3 col-sm-6">
                <div class="form-group">
                  <label for="ticket_lifetime">海报有效期（单位：天，最长 30 天）</label><label for="ticket_lifetime" style="color:red;max-width:1000px;width:1000px;">（特别注意：海报有效期首次设置成功后，后续修改，时间只能改短不能改长，例如首次设置是10天，后续修改只能小于10天）</label>
                  <input type="number" min="1" max="30" class="form-control" id="ticket_lifetime" name="text[ticket_lifetime]" value="<?=intval($config['ticket_lifetime'])?>">
                </div>
              </div>

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

                      <!--
                      <div class="col-lg-2 col-sm-3">
                      <div class="form-group">
                        <label for="qh">高度</label>
                        <input type="number" class="form-control" id="qh" name="px[px_qrcode_height]" value="<?=intval($config['px_qrcode_height'])?>">
                      </div>
                      </div>
                    -->

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

                      <!--
                      <div class="col-lg-2 col-sm-3">
                      <div class="form-group">
                        <label for="hh">高度</label>
                        <input type="number" class="form-control" id="hh" name="px[px_head_height]" value="<?=intval($config['px_head_height'])?>">
                      </div>
                      </div>
                    -->

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
                    <label for="goal">直接积分奖励文案</label>
                    <input type="text" class="form-control" id="goal" name="text[text_goal]" placeholder="您的朋友「%s」成为了您的支持者！您已获得了相应的积分奖励，请注意查收。" value="<?=$config['text_goal']?>">
                  </div>

                  <div class="form-group">
                    <label for="goal2">间接积分奖励文案</label>
                    <input type="text" class="form-control" id="goal2" name="text[text_goal2]" placeholder="您的朋友「%s」又获得了一个新的支持者！" value="<?=$config['text_goal2']?>">
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

                </div><!-- /.box-body -->

                <div class="box-footer">
                  <button type="submit" class="btn btn-success">更新个性化信息</button>
                </div>
              </form>
            </div>

            <!-- 2015.12.16增加积分清零部分 -->
            <div class="tab-pane" id="cfg_zero">
              <span style="font-size:20px;">您确定将所有用户的积分清零？<span>
                <p class="help-block" style="font-size:14px;">仅清空用户积分，用户关系保留，请商户谨慎处理。</p>
                <p class="help-block" style="font-size:14px;">注意：积分清零后，兑换中心-总积分归零，粉丝数保留；我的积分之前的积分记录删除。</p>
                <div class="radio">
                  <a href="#" class="btn btn-danger" style="margin-left:10px" id="delete" data-toggle="modal" data-target="#deleteModel"><i class="fa fa-remove"></i> <span>清空积分</span></a>
                  <div class="modal modal-danger" id="deleteModel">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                          <h4 class="modal-title">确认要清空用户积分吗？该操作不可恢复！</h4>
                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-outline" data-dismiss="modal">取消</button>
                          <a href="<?php echo URL::site("wfba/empty")?>?DELETE=1" class="btn btn-outline">确认清空</a>
                        </div>
                      </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                  </div><!-- /.modal -->
                </div>
              </div>
              <!-- 2015.12.16增加积分清零部分 -->

              <!-- 2015.12.21增加指定地区用户参与部分 -->

              <div class="tab-pane" id="cfg_area">
                <?php if ($result['ok5'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>保存成功!</div>
                <?php endif?>
                <form role="form" method="post" onsubmit="return toVaild()">
                  <div class="form-group">
                    <label for="show" style="font-size:16px;">是否开启本功能？</label>
                    <div class="radio">
                      <label class="checkbox-inline"  onclick="show()">
                        <input type="radio" name="area[status]" id="show1" value="1" <?=$config['status'] == 1 ? ' checked=""' : ''?>>
                        <span class="label label-success"  style="font-size:14px">开启</span>
                      </label>
                      <label class="checkbox-inline" onclick="hide()">
                        <input type="radio" name="area[status]" id="show0" value="0" <?=$config['status'] === "0" ||!$config['status']? ' checked=""' : ''?>>
                        <span class="label label-danger"  style="font-size:14px">关闭</span>
                      </label>
                    </div>

                    <script type="text/javascript">
                      function hide(){
                      // var h=document.getElementById("hidee");
                      // h.style.visibility="hidden";
                      var h2=document.getElementById("hide");
                      h2.style.display="none";
                    }

                    function show(){
                      // var h=document.getElementById("hidee");
                      // h.style.visibility="visible";
                      var h2=document.getElementById("hide");
                      h2.style.display="block";
                    }
                  </script>


                  <!-- 地区选择代码 -->
                  <br>
                  <div id="hide">
                    <label id="lab" for="show" style="font-size:16px;">请选择可参与活动的地区：
                      <br>
                      <br>
                      <span class="label label-success add"  >添加</span>
                      <span class='label label-danger reduce'>减少</span>

                      <?php if ($config['count']){
                        $num = $config['count'];
                        for ($i=1; $i <=$num ; $i++) {
                         echo '
                         <div class=\'loc\' id=\'city'.$i.'\'>
                          <select class=\'prov\' name=\'area[pro'.$i.']\'></select>
                          <select class=\'city\' name=\'area[city'.$i.']\' disabled="disabled"></select>
                          <select class=\'dist\' name=\'area[dis'.$i.']\' disabled="disabled"></select>
                        </div>
                        ';
                      }
                    }
                    ?>
                    <?php if (!$config['count']):?>
                      <div class="loc" id="city1" class="loc">
                        <select class="prov" name="area[pro1]"></select>
                        <select class="city" name="area[city1]" disabled="disabled"></select>
                        <select class="dist" name="area[dis1]" disabled="disabled"></select>
                      </div>

                    <?php endif?>
                  </label>
                  <input id='count' name="area[count]" style="display:none" value='<?=$config['count']?>'>


                  <br><br>
                  <div class="form-group">
                    <label for="desc">页面中对不符合活动地区的用户提示文案</label><br>
                    <input name='area[reply]' type="text" style="width:100%;height:40px;font-size: 14px; line-height: 18px; border: 1px solid #dddddd;" value='<?php if($config['reply']){echo $config['reply'];}else{echo '不好意思，您不在本次活动的参与地区，不要灰心哦，请继续关注我们的公众号，有更多惊喜等着你呢！';}?>'>
                  </div>
                  <div class="form-group">
                    <label for="desc">页面中符合活动地区的用户提示文案</label><br>
                    <input name='area[isreply]' type="text" style="width:100%;height:40px;font-size: 14px; line-height: 18px; border: 1px solid #dddddd;" value='<?php if($config['isreply']){echo $config['isreply'];}else{echo '亲~恭喜您获得参与本次活动的机会，请返回到公众号对话框，点击【生成海报】菜单，获得属于你的专属海报！';}?>'>
                  </div>
                  <div class="form-group">
                    <label for="desc">微信中对不符合活动地区的用户提示文案</label><br>
                    <input name='area[replyfront]' type="text" style="width:50%;height:40px;float:left;font-size: 14px; line-height: 18px; border: 1px solid #dddddd;" value='<?php if($config['replyfront']){echo $config['replyfront'];}else{echo '您好，本次活动可参与的地区为：';}?>'>
                    <div style="float:left;font-size: 14px;width:20%;text-align:center; line-height: 36px;">点击查看是否在活动范围内</div>
                    <input name='area[replyend]' type="text" style="width:29.6%;height:40px;float:left;font-size: 14px; line-height: 18px; border: 1px solid #dddddd;" value='<?php if($config['replyend']){echo $config['replyend'];}else{echo '，如果您不在本次活动的范围内，请关注公众号的消息，有更多福利等着你哦！';}?>'>
                  </div><br><br>
                  <div class="form-group">
                    <label for="desc">活动说明</label>
                    <textarea class="textarea" wrap="virtual" id="" name="area[info]" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?php if($config['info']){echo $config['info'];}else{echo '
                      1 、本次活动可参与的地区范围：<br>
                      2 、活动时间：<br>
                      3 、活动注意事项：';}?></textarea>
                    </div>

                  </div>
                </div>
                <br>

                <script type="text/javascript">
                  function toVaild(){
                    num = parseInt($('.prov').length);
                    $('#count').val(num);
                    var isn = $('.prov:last').val();
                    if(!isn==''){
                      return true;
                    }else{
                      alert('请至少填写省');
                      return false;
                    }
                  }
                </script>
                <!-- // <script src="/wfb/plugins/jQuery/jquery.js"></script> -->
                <script src="http://cdn.jfb.smfyun.com/wdy/plugins/citySelect/jquery.cityselect.js"></script>
                <script src="/wfb/plugins/citySelect/city.min1.js"></script>
                <script type="text/javascript">
                  $("#city1").citySelect({
                    prov:'',
                    city:'',
                    dist:'',
                    required:false
                  });
                  <?php
                  if ($config['count']){
                    $num = $config['count'];
                    for ($i=1; $i <=$num ; $i++) {
                     echo '
                     $(function(){
                      $(\'#city'.$i.'\').citySelect({
                        prov:\''.$config['pro'.$i].'\',
                        city:\''.$config['city'.$i].'\',
                        dist:\''.$config['dis'.$i].'\',
                        required:false
                      });
                    })';
                  }
                }
                ?>

                $(document).on('click','.add',function(){
                  var isn = $('.prov:last').val();
                  if(!isn==''){
                    window.num = parseInt($('.prov').length);
                    num = num+1;
                    $('#count').val(num);
                    $('.add').attr('count',num);
                    $("#lab").append("<div class=\"loc\" id=\"city"+num+"\">"+
                      "<select class=\"prov\" name=\"area[pro"+num+"]\"></select>"+
                      "<select class=\"city\" name=\"area[city"+num+"]\" disabled=\"disabled\"></select>"+
                      "<select class=\"dist\" name=\"area[dis"+num+"]\" disabled=\"disabled\"></select>"+
                      "</div>");
                  }else{
                    alert('请至少填写省');
                  }
                  $("#city"+num).citySelect({
                    prov:'',
                    city:'',
                    dist:'',
                    required:false
                  });
                })
                $(document).on('click','.reduce',function(){
                  if(parseInt($('.prov').length)==1){
                    alert('不能再减少');
                  }else{
                    $('.loc').last().remove();
                  }
                })
              </script>


              <!-- 开启与关闭 -->
              <script>
                $(document).ready(function(){
                 var status = $('#show1').attr('checked');
                 if(status=='checked'){
                  $('#hide').show();
                }
                if(status==undefined){
                  $('#hide').hide();
                }
              })
            </script>

            <div class="form-group">
              <label for="show">
                <div class="box-footer">
                  <button id="sub" type="submit" class="btn btn-success">保存配置</button>
                </div>
              </label>

            </div>


          </form>
        </div>

        <!-- 2015.12.21增加指定地区用户参与部分 完-->
<script language=javascript>
  function onlyNum()
      {
      if(!(event.keyCode==46)&&!(event.keyCode==8)&&!(event.keyCode==37)&&!(event.keyCode==39))
      if(!((event.keyCode>=48&&event.keyCode<=57)||(event.keyCode>=96&&event.keyCode<=105)))
      event.returnValue=false;
  }
  $(function () {
  var editor = new Simditor({
    textarea: $('.textarea'),
    toolbar: ['title','bold','italic','underline','strikethrough','color','ol','ul','blockquote','table','link','image','hr','indent','outdent','alignment']
  });
})
</script>

<!-- 批量打标签 结束 -->


<div class="tab-pane" id="cfg_account">

  <?php if ($result['ok4'] > 0):
  $_SESSION['wfba'] = null;
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

