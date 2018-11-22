<style>
.nav-tabs-custom>.nav-tabs>li.active {
  border-top-color: #00a65a;
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
            <li id="cfg_account_li"><a href="#cfg_account" data-toggle="tab">密码修改</a></li>
          </ul>

          <?php
          if ($_POST['cfg']) $active = 'wx';
          if (!$_POST || $_POST['yz']) $active = 'yz';

          if ($_POST['menu']) $active = 'menu';
          if ($_POST['text']) $active = 'text';
          if (isset($_POST['password'])) $active = 'account';
          ?>

          <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
          </script>

          <div class="tab-content">

            <div class="tab-pane" id="cfg_yz">

                <div class="callout callout-danger">
                  <p>首次使用，请 <a href="http://shop1128733.koudaitong.com/v2/feature/nbo89d92" target="_blank">点击这里</a> 查看积分宝系统使用说明书</p>
                </div>

                <div class="callout callout-success">
                  <h4>有赞插件配置信息</h4>
                  <p><b>插件 url：</b> <u><?='http://'.$_SERVER["HTTP_HOST"].'/api/mdt/'.$_SESSION['mdta']['user'];?></u></p>
                  <p><b>插件 token：</b> <u>jifenbao2015</u></p>
                  <p><a href="http://koudaitong.com/apps/third" target="_blank">点击这里</a> 进入有赞插件页面设置</p>
                </div>

                <?php if ($result['ok'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>有赞配置保存成功!</div>
                <?php endif?>

                <!-- form start -->
                <form role="form" method="post">
                  <div class="box-body">
                    <div class="form-group">
                      <label for="scene_id">有赞后台「单个二维码扫描」接口中的 scene_id</label>
                      <input type="text" class="form-control" id="scene_id" placeholder="输入带参数二维码的 scene_id" maxlength="4" name="cfg[scene_id]" value="<?=(int)$config['scene_id']?>">
                    </div>
                  </div><!-- /.box-body -->

                  <div class="box-footer">
                    <input type="hidden" name="yz" value="1">
                    <button type="submit" class="btn btn-success">保存有赞配置</button>
                  </div>
                </form>
            </div>

            <div class="tab-pane" id="cfg_wx">

                <?php if ($result['ok'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>微信配置保存成功!</div>
                <?php endif?>

                <!-- form start -->
                <form role="form" method="post">
                  <div class="box-body">
                    <div class="form-group">
                      <label for="name">公众号名称</label>
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
                  </div><!-- /.box-body -->

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
                    <div class="col-lg-3 col-sm-3">
                      <div class="form-group">
                        <label for="menu0">1.「生成海报」对应的菜单 KEY</label>
                        <input type="text" class="form-control" id="menu0" placeholder="生成海报" maxlength="10" name="menu[key_qrcode]" value="<?=$config['key_qrcode']?>">
                      </div>
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-lg-3 col-sm-3">
                      <div class="form-group">
                        <label for="menu1">2.「积分查询」对应的菜单 KEY</label>
                        <input type="text" class="form-control" id="menu1" placeholder="积分查询" maxlength="10" name="menu[key_score]" value="<?=$config['key_score']?>">
                      </div>
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-lg-3 col-sm-3">
                      <div class="form-group">
                        <label for="menu2">3.「积分兑换」对应的菜单 KEY</label>
                        <input type="text" class="form-control" id="menu2" placeholder="积分兑换" maxlength="10" name="menu[key_item]" value="<?=$config['key_item']?>">
                      </div>
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-lg-3 col-sm-3">
                      <div class="form-group">
                        <label for="menu3">4.「积分排行」对应的菜单 KEY</label>
                        <input type="text" class="form-control" id="menu3" placeholder="积分排行" maxlength="10" name="menu[key_top]" value="<?=$config['key_top']?>">
                      </div>
                    </div>
                    </div>

                    <div class="alert alert-danger">以下为高级选项，如果不清楚如何设置，请保持为空。</div>

                    <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <label for="menu10">预留自定义 KEY</label>
                          <input type="text" class="form-control" id="menu10" placeholder="自定义 KEY1" maxlength="10" name="menu[key_c1]" value="<?=$config['key_c1']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <label for="v10">点击后回复文字（使用 \n 换行）</label>
                          <input type="text" class="form-control" id="v10" placeholder="点击自定义 KEY1 后的回复文字" maxlength="200" name="menu[value_c1]" value="<?=$config['value_c1']?>">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <input type="text" class="form-control" id="menu11" placeholder="自定义 KEY2" maxlength="10" name="menu[key_c2]" value="<?=$config['key_c2']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" id="v11" placeholder="点击自定义 KEY2 后的回复文字" maxlength="200" name="menu[value_c2]" value="<?=$config['value_c2']?>">
                        </div>
                      </div>
                    </div>

                   <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <input type="text" class="form-control" id="menu12" placeholder="自定义 KEY3" maxlength="10" name="menu[key_c3]" value="<?=$config['key_c3']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" id="v12" placeholder="点击自定义 KEY3 后的回复文字" maxlength="200" name="menu[value_c3]" value="<?=$config['value_c3']?>">
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
                      <a href="/mdta/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/mdta/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" width="200" title="点击查看原图"></a>
                    </div>
                    <?php endif?>

                    <div class="form-group">
                      <label for="pic">二维码海报背景图</label>
                      <input type="file" class="form-control" id="pic" name="pic" accept="image/jpeg">
                      <p class="help-block">只能为 JPEG 格式，规格建议为 640*900px，最大不超过 300K，<a href="/mdt/tpl.zip" target="_blank">点击这里</a> 下载 PSD 模板自行设计。</p>
                    </div>

                    <?php
                    //默认头像
                    if ($result['tplhead']):
                    ?>
                    <div class="form-group">
                      <a href="/mdta/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/mdta/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
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
                      <p class="help-block">建议设置为活动攻略的微杂志，示例网址：<a href="http://kdt.im/znDoi9NAE" target="_blank">http://kdt.im/znDoi9NAE</a></p>
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

                    </div>

                    <div class="row">

                      <div class="col-lg-3 col-sm-6">
                      <div class="form-group">
                        <label for="rank">积分排行榜显示数量</label>
                        <input type="number" class="form-control" id="rank" name="text[rank]" value="<?=intval($config['rank'])?>">
                      </div>
                      </div>

                    </div>

                    <div class="alert alert-danger">以下为高级选项，如果不清楚如何设置，建议采用默认值。</div>

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

            <div class="tab-pane" id="cfg_account">

                <?php if ($result['ok4'] > 0):
                $_SESSION['mdta'] = null;
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

