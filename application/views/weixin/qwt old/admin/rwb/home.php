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
                <div class="alert alert-danger">海报设置</div>
                <?php
                if ($result['tpl']):
                  ?>
                <div class="form-group">
                  <a href="/qwtrwba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/qwtrwba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" width="200" title="点击查看原图"></a>
                </div>
              <?php endif?>

              <div class="form-group">
                <label for="pic">二维码海报背景图</label>
                <input type="file" class="form-control" id="pic" name="pic" accept="image/jpeg">
                <p class="help-block">只能为 JPEG 格式，规格建议为 640*900px，最大不超过 300K，<a href="/qwtrwb/tpl.zip" target="_blank">点击这里</a> 下载 PSD 模板自行设计。</p>
              </div>

              <?php
                    //默认头像
              if ($result['tplhead']):
                ?>
              <div class="form-group">
                <a href="/qwtrwba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/qwtrwba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
              </div>
            <?php endif?>

            <div class="form-group">
              <label for="pic2">默认头像（获取头像失败时会用该头像，可选）</label>
              <input type="file" class="form-control" id="pic2" name="pic2" accept="image/jpeg">
              <p class="help-block">只能为 JPEG 格式，正方形，最大不超过 100K。</p>
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

              <div class="col-lg-4 col-sm-6">
                <div class="form-group">
                  <label for="ticket_lifetime">海报有效期（单位：天，最长 30 天）</label><label for="ticket_lifetime" style="color:red;max-width:1000px;width:1000px;">（特别注意：海报有效期首次设置成功后，后续修改，时间只能改短不能改长，例如首次设置是10天，后续修改只能小于10天）</label>
                  <input type="number" min="1" max="30" class="form-control" id="ticket_lifetime" name="text[ticket_lifetime]" value="<?=intval($config['ticket_lifetime'])?>">
                </div>
              </div>

            </div>



            <div class="alert alert-danger">消息设置</div>

             <div class="form-group">
              <label for="text_follow_url">首次关注推送网址</label>
              <input type="text" class="form-control" id="text_follow_url" name="text[text_follow_url]" placeholder="http://" value="<?=$config['text_follow_url']?>">
              <p class="help-block">建议设置为活动攻略的微杂志，示例网址：<a href="http://kdt.im/znDoi9NAE" target="_blank">http://kdt.im/znDoi9NAE</a></p>
            </div>
            <!--<div class="row">
              <div class="col-lg-7 col-sm-7">
                <div class="form-group">
                  <label for="rank">支付多少可以生成海报 单位：分（0表示不限,务必一键授权给有赞）</label>
                  <input type="number" class="form-control" id="rank" name="text[needpay]"  maxlength='4' value="<?=intval($config['needpay'])?>">
                </div>
              </div>
            </div>-->

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
                    <label for="goal">获得粉丝奖励文案</label>
                    <input type="text" class="form-control" id="goal" name="text[text_goal]" placeholder="您的朋友「%s」成为了您的支持者！您离完成任务更近一步，继续努力哦。" value="<?=$config['text_goal']?>">
                  </div>
                  <div class="form-group">
                    <label for="goal2">最终完成任务奖励文案</label>
                    <input type="text" class="form-control" id="goal2" name="text[text_goal2]" placeholder="恭喜您已经全部完成了「%s」任务，继续留意我们下次任务哦。" value="<?=$config['text_goal2']?>">
                  </div>
                </div><!-- /.box-body -->
                <div class="box-footer">
                  <button type="submit" class="btn btn-success">更新个性化信息</button>
                </div>

                </div><!-- /.box-body -->


              </form>
        </div>


                </div>
            </div>
        </div>
        <!--body wrapper end-->
<!-- Main content -->


</section><!-- /.content -->

