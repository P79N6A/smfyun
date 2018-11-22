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
<section class="wrapper" style="width:85%;float:right;background:#eff0f4">
  <h3>
    基础设置
    <small>核心参数配置</small>
  </h3>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">基础设置</li>
  </ol>

<!--body wrapper start-->
        <div class="wrapper" style="background:#eff0f4">
            <div class="row">
                <div class="col-md-14">
                    <section class="panel">
                        <header class="panel-heading custom-tab dark-tab">
                            <ul class="nav nav-tabs">
                                <li class="" id="cfg_menu_li">
                                    <a href="#cfg_menu" data-toggle="tab">菜单配置</a>
                                </li>
                                <li class="" id="cfg_text_li">
                                    <a href="#cfg_text" data-toggle="tab">个性化配置</a>
                                </li>
                            </ul>
                            <?php

        if (!$_POST||$_POST['menu']) $active = 'menu';
        if ($_POST['text']) $active = 'text';
        ?>
        <script src="http://cdn.bootcss.com/jquery/2.0.1/jquery.js"></script>
        <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
        </script>

                        </header>
                        <div class="panel-body">
                            <div class="tab-content">
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
                      <input type="text" class="form-control" id="menu0"  maxlength="4" name="menu[key_qrcode]" placeholder='生成海报' value="<?=$config['key_qrcode']?>">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu1">2.「积分查询」自定义名称</label>
                      <input type="text" class="form-control" id="menu1"  maxlength="4" name="menu[key_score]" placeholder='积分查询' value="<?=$config['key_score']?>">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu2">3.「积分兑换」自定义名称</label>
                      <input type="text" class="form-control" id="menu2"  maxlength="4" name="menu[key_item]" placeholder='积分兑换' value="<?=$config['key_item']?>">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu3">4.「积分排行」自定义名称</label>
                      <input type="text" class="form-control" id="menu3"  maxlength="4" name="menu[key_top]" placeholder='积分排行' value="<?=$config['key_top']?>">
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
                  <a href="/qwtwfba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/qwtwfba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" width="200" title="点击查看原图"></a>
                </div>
              <?php endif?>

              <div class="form-group">
                <label for="pic">二维码海报背景图</label>
                <input type="file" class="form-control" id="pic" name="pic" accept="image/jpeg">
                <p class="help-block">只能为 JPEG 格式，规格建议为 640*900px，最大不超过 300K，<a href="/qwtwfb/tpl.zip" target="_blank">点击这里</a> 下载 PSD 模板自行设计。</p>
              </div>

              <?php
                    //默认头像
              if ($result['tplhead']):
                ?>
              <div class="form-group">
                <a href="/qwtwfba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/qwtwfba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
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
              <label style="color:red;max-width:1000px;width:1000px;margin-bottom: 10px;padding-left:10px">注意：请核算后谨慎设置积分奖励规则，避免积分兑换奖品的门槛过低，造成不必要的损失，特别是在奖品设置时，单个奖品兑换所需的积分一定要低于首次关注奖励积分。因为运营操作不当，积分兑换门槛设置过低导致的损失，我方不予承担。</label>
            </div>

            <div class="row">

              <div class="col-lg-3 col-sm-6">
                <div class="form-group">
                  <label for="rank">积分排行榜显示数量</label>
                  <input type="number" class="form-control" id="rank" name="text[rank]" value="<?=intval($config['rank'])?>">
                </div>
              </div>
              <div class="col-lg-5 col-sm-6">
                <div class="form-group">
                  <label for="rank">积分名称自定义（为空则不修改,最长两个字）</label>
                  <input type="text" class="form-control" id="rank" name="text[score]"  maxlength='2' value="<?=$config['score']?>">
                </div>
              </div>
            </div>

            <div class="alert alert-danger">以下为高级选项，如果不清楚如何设置，建议采用默认值。</div>

            <div class="row">

              <div class="col-lg-4 col-sm-6">
                <div class="form-group">
                  <label for="ticket_lifetime">海报有效期（单位：天，最长 30 天）</label><label for="ticket_lifetime" style="color:red;max-width:1000px;width:1000px;">（特别注意：海报有效期首次设置成功后，后续修改，时间只能改短不能改长，例如首次设置是10天，后续修改只能小于10天）</label>
                  <input type="number" min="1" max="30" class="form-control" id="ticket_lifetime" name="text[ticket_lifetime]" value="<?=intval($config['ticket_lifetime'])?>">
                </div>
              </div>

            </div>

            <div class="row">

              <div class="col-lg-3 col-sm-3">
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
                    <label for="goal3">扫码关注点击验证后自动回复文案</label>
                    <input type="text" class="form-control" id="goal3" name="text[text_goal3]"  value="<?=$config['text_goal3']?$config['text_goal3']:'恭喜您成为了「%s」的支持者~'?>">
                  </div>

                  <!-- <div class="form-group">
                    <label for="goal4">重复扫描相同二维码回复文案</label>
                    <input type="text" class="form-control" id="goal4" name="text[text_goal4]" value="<?=$config['text_goal4']?$config['text_goal4']:'您已经是「%s」的支持者了，不用再扫了哦'?>">
                  </div> -->
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

                    <div class="col-lg-3 col-sm-3">
                      <div class="form-group">
                        <label for="risk_level1">推荐多少直接粉丝？</label>
                        <input type="number" class="form-control" id="risk_level1" name="px[risk_level1]" value="<?=intval($config['risk_level1'])?>">
                      </div>
                    </div>

                    <div class="col-lg-4 col-sm-4">
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


                </div>
            </div>
        </div>
        <!--body wrapper end-->
<!-- Main content -->


</section><!-- /.content -->

