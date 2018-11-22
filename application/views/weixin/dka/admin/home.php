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
  .inputtxt{
    width:5%;
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
          <li id="cfg_text_li"><a href="#cfg_text" data-toggle="tab">邀请卡配置</a></li>
          <li id="cfg_dk_li"><a href="#cfg_dk" data-toggle="tab">打卡设置</a></li>
          <li id="cfg_zero_li"><a href="#cfg_zero" data-toggle="tab">积分清零</a></li>
          <!-- <li id="cfg_area_li"><a href="#cfg_area" data-toggle="tab">选择可参与地区</a></li> -->
          <li id="cfg_account_li"><a href="#cfg_account" data-toggle="tab">密码修改</a></li>
          <li id="cfg_lab_li"><a href="#cfg_lab" data-toggle="tab">批量打标签</a></li>
          <?php if($bid==1):?><li id="cfg_draw_li"><a href="#cfg_draw" data-toggle="tab">积分抽奖设置</a></li><?php endif?>
        </ul>
        <?php
        if ($_POST['cfg']) $active = 'wx';
        if (!$_POST || $_POST['yz']) $active = 'yz';

        if ($_POST['menu']) $active = 'menu';
        if ($_POST['text']) $active = 'text';
        if ($_POST['area']) $active = 'area';
        if ($_POST['dka']) $active = 'dk';
        if ($_POST['tag']) $active = 'lab';
        if ($_POST['draw']) $active = 'draw';
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
              <p>首次使用，请 <a href="https://wap.koudaitong.com/v2/feature/7dfd2tz7" target="_blank">点击这里</a> 查看打卡宝系统使用说明书</p>
            </div> -->

            <div class="callout callout-success">
              <h4>有赞插件配置信息</h4>
              <p><b>插件 token：</b> <u>dakabao</u></p>
              <p><b>插件 url：</b> <u><?='http://'.$_SERVER["HTTP_HOST"].'/api/dka/'.$_SESSION['dkaa']['user'];?></u></p>
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
                  <input type="text" class="form-control" id="scene_id" placeholder="输入带参数二维码的 scene_id"  name="cfg[scene_id]" value="<?=$config['scene_id']?>">
                </div>
                <p class="alert alert-danger alert-dismissable">注意：如果需要使用批量打标签、有赞赠品、请务必点击有赞一键授权，否则以上功能将无法使用</p>
                <?php if($oauth==1):?>
                  <div class="lab4">
                    <a href='/dkaa/oauth'><button type="button" class="btn btn-warning">点击一键授权有赞</button></a>
                  </div>
                  <br>
                  <?else:?>
                  <div class="lab4">
                    <a href='/dkaa/oauth'><button type="button" class="btn btn-warning">点击重新授权有赞</button></a>
                  </div>
                  <?endif?>
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
                <p class="alert alert-danger alert-dismissable">以下为高级选项，如果需要添加微信红包的奖品类型，请填写，不需要保持为空。</p>
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
                      <label for="menu4">1.「我要打卡」对应的菜单 KEY</label>
                      <input type="text" class="form-control" id="menu1" placeholder="我要打卡" maxlength="20" name="menu[key_dka]" value="<?=$config['key_dka']?>">
                    </div>
                  </div>
                </div>
                <!-- <div class="row">
                    <div class="col-lg-8">
                      <div class="form-group">
                        <label for="menu1">2.「我的收益」对应的菜单 KEY（不需要设置回调域名）</label>
                        <input type="text" class="form-control" id="menu2" placeholder="我的收益" maxlength="10" name="menu[key_fxbscore]" value="<?=$config['key_fxbscore']?>">
                      </div>
                    </div>
                </div> -->
                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu1">2.「积分查询」对应的菜单 KEY</label>
                      <input type="text" class="form-control" id="menu3" placeholder="积分查询" maxlength="20" name="menu[key_score]" value="<?=$config['key_score']?>">
                    </div>
                  </div>
                </div>
                <div class="alert alert-danger">以下为高级选项，如果不清楚如何设置，请保持为空。</div>

                <div class="row">
                  <div class="col-lg-3 col-sm-3">
                    <div class="form-group">
                      <label for="menu10">预留自定义 KEY</label>
                      <input type="text" class="form-control" id="menu10" placeholder="自定义 KEY1" maxlength="20" name="menu[key_c1]" value="<?=$config['key_c1']?>">
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
                      <input type="text" class="form-control" id="menu11" placeholder="自定义 KEY2" maxlength="20" name="menu[key_c2]" value="<?=$config['key_c2']?>">
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
                      <input type="text" class="form-control" id="menu12" placeholder="自定义 KEY3" maxlength="20" name="menu[key_c3]" value="<?=$config['key_c3']?>">
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
                  <a href="/dkaa/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/dkaa/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" width="200" title="点击查看原图"></a>
                </div>
              <?php endif?>

              <div class="form-group">
                <label for="pic">邀请卡背景图</label>
                <input type="file" class="form-control" id="pic" name="pic" accept="image/jpeg">
                <p class="help-block">只能为 JPEG 格式，规格建议为 640*900px，最大不超过 300K，<a href="/dka/tpl.zip" target="_blank">点击这里</a> 下载 PSD 模板自行设计。</p>
              </div>

              <?php
                    //默认头像
              if ($result['tplhead']):
                ?>
              <div class="form-group">
                <a href="/dkaa/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/dkaa/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
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
              <p class="help-block">建议设置为活动攻略的微杂志。</p>
            </div>

          <?php if($_SESSION['dkaa']['admin'] >= 1):?>
            <div class="row">

              <div class="col-lg-2 col-sm-4">
                <div class="form-group">
                  <label for="goal0">首次关注奖励积分</label>
                  <input type="number" class="form-control" id="goal00" name="text[goal00]" value="<?=intval($config['goal00'])?>">
                </div>
              </div>

              <div class="col-lg-2 col-sm-4">
                <div class="form-group">
                  <label for="goal">直接推荐奖励积分</label>
                  <input type="number" class="form-control" id="goal01" name="text[goal01]" value="<?=floatval($config['goal01'])?>">
                </div>
              </div>

              <div class="col-lg-2 col-sm-4">
                <div class="form-group">
                  <label for="goal2">间接推荐奖励积分</label>
                  <input type="number" class="form-control" id="goal02" name="text[goal02]" value="<?=intval($config['goal02'])?>">
                </div>
              </div>

            </div>
          <?php endif ?>
            <div class="row">

              <div class="col-lg-3 col-sm-6">
                <div class="form-group">
                  <label for="rank">积分排行榜显示数量</label>
                  <input type="number" class="form-control" id="rank" name="text[rank]" value="<?=intval($config['rank'])?>">
                </div>
              </div>
            <?php if($bid==1):?>
             <div class="col-lg-4 col-sm-6">
                <div class="form-group">
                  <label for="rank">积分名称自定义（为空则不修改,最长两个字）</label>
                  <input type="text" class="form-control" id="rank" name="text[scorename]"  maxlength='2'  value="<?=$config['scorename']?>">
                </div>
              </div>
          <?php endif;?>
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

                  <div class="form-group">
                    <label for="send">邀请卡生成前发送消息</label>
                    <input type="text" class="form-control" id="send" name="text[text_send]" placeholder="正在为您生成邀请卡，请稍后..." value="<?=$config['text_send']?>">
                  </div>

                  <div class="form-group">
                    <label for="goal">直接邀请提示文案：</label>
                    <input type="text" class="form-control" id="goal" name="text[text_goal]" placeholder="您的小伙伴「%s」已接受邀请，加入神码浮云早起计划…" value="<?=$config['text_goal']?>">
                  </div>

                  <div class="form-group">
                    <label for="goal2">间接邀请提示文案：</label>
                    <input type="text" class="form-control" id="goal2" name="text[text_goal2]" placeholder="您又获得一个小伙伴「%s」加入早起计划…" value="<?=$config['text_goal2']?>">
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
                          <a href="<?php echo URL::site("dkaa/empty")?>?DELETE=1" class="btn btn-outline">确认清空</a>
                        </div>
                      </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                  </div><!-- /.modal -->
                </div>
              </div>
              <!-- 2015.12.16增加积分清零部分 -->

              <!-- 2015.12.21增加指定地区用户参与部分 -->

              <!-- <div class="tab-pane" id="cfg_area">
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

                      var h2=document.getElementById("hide");
                      h2.style.display="none";
                    }

                    function show(){

                      var h2=document.getElementById("hide");
                      h2.style.display="block";
                    }
                  </script>



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
                      1 、本次活动可参与的地区范围：
                      2 、活动时间：
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

                <script src="http://cdn.jfb.smfyun.com/wdy/plugins/citySelect/jquery.cityselect.js"></script>
                <script src="/dka/plugins/citySelect/city.min1.js"></script>
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
</div> -->
<!-- 2015.12.21增加指定地区用户参与部分 完-->


<!-- 2016.1.26增加打卡页面配置 -->

<div class="tab-pane" id="cfg_dk">
  <!-- form start -->
  <?php if ($result['ok5'] > 0):?>
    <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>保存成功!</div>
  <?php endif?>
  <?php if ($result['err5']):?>
    <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err5']?></div>
  <?php endif?>
  <form role="form" method="post" enctype="multipart/form-data" onsubmit='return checktime()'>
    <div class="box-body">
      <?php if ($result['dka']):?>
        <div class="form-group">
          <a href="/dkaa/images/cfg/<?=$result['dka']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/dkaa/images/cfg/<?=$result['dka']?>.v<?=time()?>.jpg" width="200" title="点击查看原图"></a>
        </div>
      <?php endif?>
      <div class="form-group">
        <label for="pic3">打卡页面背景图</label>
        <input type="file" class="form-control" id="pic3" name="pic3" accept="image/jpg">
        <p class="help-block">只能为 JPG 格式，规格建议为640*350px，最大不超过 300K<!-- <a href="/dka/bg.jpg.zip" target="_blank">点击这里</a> 下载 PSD 模板自行设计。</p> -->
      </div>

      <div class="form-group">
        <label for="name">填写活动说明：</label>
        <textarea class="form-control textarea" rows="5" name='dka[explain]'><?php if(!$config['explain']) {echo htmlspecialchars('积分奖励规则：<br>
1、每天5:00~15:00打卡，固定奖励2积分<br>
2、连续第10天打卡，当天奖励10。连续打卡超过10天，每天固定奖励5分，打卡中断后，再次打卡奖励积分重新从2分开始计算，总积分累积。<br>
3、每日打卡，通过摇一摇、砸金蛋的形式也可以获取积分；<br>
4、好友通过你分享的邀请卡扫码关注公众号，每天打卡，你能获得相应的积分奖励；其他用户通过好友分享的邀请卡扫码关注公众号，每天打卡，你也能获得相应的积分奖励；<br>');}else{echo htmlspecialchars($config['explain']);}?></textarea>
      </div>
      <label for="name">模板消息设置:</label><br><br>
      <div class="form-group">
        <label for="name">打卡提醒模板消息ID（IT科技-互联网|电子商务「任务处理结果提醒」模板编号：OPENTM200815730）:</label>
        <input type="text" class="form-control" name='dka[tplid]' id="name" placeholder="请输入模版消息ID" value="<?=$config['tplid']?>" >
      </div>
      <!-- <div class="form-group">
          <label for="goal">收益通知消息模板（「收益通知」模板编号：OPENTM206596805）</label>
          <input type="text" class="form-control" id="goal" name="dka[msg_score_tpl]" placeholder="" value="<?=$config['msg_score_tpl']?>">
        </div>

        <div class="form-group">
          <label for="goal2">账户余额通知消息模板（「账户余额通知」模板编号：OPENTM204526957）</label>
          <input type="text" class="form-control" id="goal2" name="dka[msg_money_tpl]" placeholder="" value="<?=$config['msg_money_tpl']?>">
        </div> -->
      <div class="form-group" id="zjd">
        <label for="name">砸金蛋设置：</label>
          <div class="radio">
                      <label class="checkbox-inline"  onclick="show3()">
                        <input type="radio" name="dka[eggst]" id="show5" value="1" <?=$config['eggst'] == 1 ? ' checked=""' : ''?>>
                        <span class="label label-success"  style="font-size:14px">开启</span>
                      </label>
                      <label class="checkbox-inline" onclick="hide3()">
                        <input type="radio" name="dka[eggst]" id="hide5" value="0" <?=$config['eggst'] == 0 ? ' checked=""' : ''?>>
                        <span class="label label-danger"  style="font-size:14px">关闭</span>
                      </label>
          </div>
        <span id="zjdHide">
          <span class="spantxt">1、金蛋的积分范围：</span>
          <input class="inputtxt" type="text" name="dka[eggstart]" value="<?php if(!$config['eggstart']){echo 1;}else{echo $config['eggstart'];}?>">
          <span class="spantxt">分到</span>
          <input class="inputtxt" type="text" name="dka[eggend]" value="<?php if(!$config['eggend']){echo 5;}else{echo $config['eggend'];}?>">
          <span class="spantxt">分；</span><br><br>
          <span class="spantxt">2、砸中金蛋的概率：</span>
          <input class="inputtxt" type="text" name="dka[eggchance]" value="<?php if(!$config['eggchance']){echo 10;}else{echo $config['eggchance'];}?>" onchange="if(!/(^0$)|(^100$)|(^\d{1,2}$)/.test(value)){value='';alert('请参照下面的格式输入哦!');}" />
          <span class="spantxt">％；</span>
          <p class="help-block">请填写0到100之间的正整数，参照这样的格式：78%</p>
        </span>
      </div>

      <div class="form-group" id="yyy">
        <label for="name">摇一摇设置：</label>
        <div class="radio">
                      <label class="checkbox-inline"  onclick="show4()">
                        <input type="radio" name="dka[shakest]" id="show6" value="1" <?=$config['shakest'] == 1 ? ' checked=""' : ''?>>
                        <span class="label label-success"  style="font-size:14px">开启</span>
                      </label>
                      <label class="checkbox-inline" onclick="hide4()">
                        <input type="radio" name="dka[shakest]" id="hide6" value="0" <?=$config['shakest'] == 0 ? ' checked=""' : ''?>>
                        <span class="label label-danger"  style="font-size:14px">关闭</span>
                      </label>
        </div>
        <div id="yyyHide">
          <span class="spantxt">1、摇一摇的积分范围：</span>
          <input class="inputtxt" type="text" name="dka[shakestart]" value="<?php if(!$config['shakestart']){echo 1;}else{echo $config['shakestart'];}?>">
          <span class="spantxt">分到</span>
          <input class="inputtxt" type="text" name="dka[shakeend]" value="<?php if(!$config['shakeend']){echo 5;}else{echo $config['shakeend'];}?>">
          <span class="spantxt">分；</span><br><br>
          <span class="spantxt">2、摇中的概率：</span>
          <input class="inputtxt" type="text" name="dka[shakechance]" value="<?php if(!$config['shakechance']){echo 10;}else{echo $config['shakechance'];}?>" onchange="if(!/(^0$)|(^100$)|(^\d{1,2}$)/.test(value)){value='';alert('请参照下面的格式输入哦!');}" />
          <span class="spantxt">％；</span>
          <p class="help-block">请填写0到100之间的正整数，参照这样的格式：78%</p>
        </div>
      </div><br>

          <script>
                $(document).ready(function(){
                 var status = $('#show5').attr('checked');
                 var yyyStatus=$('#show6').attr('checked');
                 if(status=='checked'){
                  $('#zjdHide').show();
                }
                if(status==undefined){
                  $('#zjdHide').hide();
                }
                if(yyyStatus=='checked'){
                  $('#yyyHide').show();
                }
                if(yyyStatus==undefined){
                  $('#yyyHide').hide();
                }
              })
          </script>

          <script type="text/javascript">
                  function hide3(){
                      var h3=document.getElementById("zjdHide");
                      h3.style.display="none";
                    }

                  function show3(){
                      var h3=document.getElementById("zjdHide");
                      h3.style.display="block";
                    }

                  function hide4(){
                      var h4=document.getElementById("yyyHide");
                      h4.style.display="none";
                    }

                  function show4(){
                      var h4=document.getElementById("yyyHide");
                      h4.style.display="block";
                    }
          </script>

        <div class="form-group" id="bor">
          <label for="name">打卡积分设置：</label><br>

          <span class="spantxt">1、基础每天签到</span>
          <input class="inputtxt" type="text" name='dka[basic_point]' value="<?php if(!$config['basic_point']){echo 1;}else{echo $config['basic_point'];}?>">
          <span class="spantxt">积分；</span><br><br>

          <span class="spantxt">2、连续签到</span>
          <input class="inputtxt" type="text" name='dka[con_day]' value="<?php if(!$config['con_day']){echo 10;}else{echo $config['con_day'];}?>">
          <span class="spantxt">天后奖励</span>
          <input class="inputtxt" type="text" name='dka[reward]' value="<?php if(!$config['reward']){echo 5;}else{echo $config['reward'];}?>">
          <span class="spantxt">分；</span>
          <span class="spantxt">继续连续签到每天可获得</span>
          <input class="inputtxt" type="text" name='dka[add_point]' value="<?php if(!$config['add_point']){echo 2;}else{echo $config['add_point'];}?>">
          <span class="spantxt">分；</span><br><br>

          <span class="spantxt">3、</span>
          除<input class="inputtxt" type="text" name="dka[nstart]" value="<?php if(!$config['nstart']){echo 8;}else{echo $config['nstart'];}?>">
          <span class="spantxt">点到</span>
          <input class="inputtxt" type="text" name="dka[nend]" value="<?php if(!$config['nend']){echo 10;}else{echo $config['nend'];}?>">
          <span class="spantxt">点签到积分减半；</span>
        </div>



        <!-- 打卡时间 -->
        <script>

        function checktime(){
          var v = $('.a').val();
            var v2=$('.a2').val();
            if(v==''||v2==''){
              alert("请按照示例中的时间格式输入打卡时间段！");
              return false;
            }else{
              var reg=/^(([0]{1}[0-9]{1})|([1]{1}[0-9]{1})|([2]{1}[0-4]{1})|[0-9])$/gi;
              if(reg.test(v)){
                return true;
              }else{
                alert('请按照示例中的时间格式输入打卡时间段！');
                return false;
              }
          }
        }
      </script>
  <style>
    .a{
      width:40px;
    }
    .a2{
      width:40px;
    }
  </style>
        <div class="form-group">
          <label for="name">请选择可打卡时间段：</label>
          <input type="text" name='dka[start]' class="a" value="<?php if(!$config['start']){echo 0;}else{echo $config['start'];}?>">:00&nbsp到&nbsp
          <input type="text" name='dka[end]' class="a2" value="<?php if(!$config['end']){echo 10;}else{echo $config['end'];}?>">:00
          <!-- <input type="button" value="保存" class="b"><br> -->
          <p class="help-block">请输入0——24之间的正整数，参照这样的格式：5:00 到 8:00</p>
        </div>
        <!-- 打卡时间结束 -->


        <div class="row">

              <!-- <div class="col-lg-2 col-sm-4">
                <div class="form-group">
                  <label for="goal0">首次关注奖励积分</label>
                  <input type="number" class="form-control" id="goal0" name="dka[goal0]" value="<?=intval($config['goal0'])?>">
                </div>
              </div> -->

              <div class="col-lg-2 col-sm-4">
                <div class="form-group">
                  <label for="goal">好友打卡奖励的积分</label>
                  <input type="number" class="form-control" id="goal" name="dka[goal]" value="<?=floatval($config['goal'])?>">
                </div>
              </div>

              <div class="col-lg-3 col-sm-4">
                <div class="form-group">
                  <label for="goal2">好友的好友打卡奖励的积分</label>
                  <input type="number" class="form-control" id="goal2" name="dka[goal2]" value="<?=intval($config['goal2'])?>">
                </div>
              </div>

        </div>
        <!-- <label for="name">收益设置：</label><br><br>
        <div class="row">
          <div class="col-lg-2 col-sm-4">
            <div class="form-group">
              <label for="money_out">最小提现金额（分）</label>
              <input type="number" class="form-control" id="money_out" name="dka[money_out]" value="<?=intval($config['money_out'])?>">
            </div>
          </div>

          <div class="col-lg-3 col-sm-4">
            <div class="form-group">
              <label for="money_out_buy">提现需消费（分）</label>
              <input type="number" class="form-control" id="money_out_buy" name="dka[money_out_buy]" value="<?=intval($config['money_out_buy'])?>">
            </div>
          </div>
        </div>
        <div class="row">

          <div class="col-lg-2 col-sm-4">
            <div class="form-group">
              <label for="money0">自购返利比例（%）</label>
              <input type="number" class="form-control" id="money0" name="dka[money0]" value="<?=intval($config['money0'])?>">
            </div>
          </div>

          <div class="col-lg-2 col-sm-4">
            <div class="form-group">
              <label for="money1">一级佣金比例（%）</label>
              <input type="number" class="form-control" id="money1" name="dka[money1]" value="<?=intval($config['money1'])?>">
            </div>
          </div>

          <div class="col-lg-2 col-sm-4">
            <div class="form-group">
              <label for="money2">二级佣金比例（%）</label>
              <input type="number" class="form-control" id="money2" name="dka[money2]" value="<?=intval($config['money2'])?>">
            </div>
          </div>

        </div>

        <div class="form-group">
          <label for="fxb_money_desc">提现说明文案</label>
          <textarea class="form-control" id="fxb_money_desc" name="dka[fxb_money_desc]" style="height:150px"><?=htmlspecialchars($config['fxb_money_desc'])?></textarea>
        </div> -->

        <div class="box-footer">
          <button type="submit" class="btn btn-success">保存</button>
        </div>
      </div>
    </div>
  </form>
  <!-- 2016.1.26增加打卡页面配置  完 -->
  <!-- 批量打标签 开始 -->
        <style>
          .laball{
            margin-left: 3px;
            margin-bottom: 13px;
          }
          .laball1{
            margin-left: 3px;
            margin-bottom: 13px;
          }
          .lab3{
            width: 100%;
            /*margin-top:15px;*/
            display:inline-block;
            top:70px;
            margin-bottom:20px;
          }
          #lab4{
            margin-top:10px;
          }
          .add1,.reduce1{
            font-size:14px;
            cursor: pointer;
          }

        </style>
        <br>

          <div class="box3 tab-pane" id="cfg_lab">
          <?php if ($result['ok6'] > 0):?>
            <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>保存成功,前往有赞后台可以管理标签</div>
          <?php endif?>
          <form role="form" method="post" >
            <div class="btn3">
              <!-- <span class="label label-success add1"  >增加</span>
              <span class="label label-danger reduce1">减少</span> -->
            </div>


            <div class="laball">
              <div class="lab3">
                <span>标签名称(填写后不可修改)：</span>
                <input class="name3 form-control" type="text" name='tag[tag_name]' value="<?=$config['tag_name']?>" <?php if($config['tag_name']) echo 'readonly=""'?>>
              </div>
              <br>
              <button type="submit" class="btn btn-success">保存</button>
              <div class="lab3" id="lab4">
                <!-- <span>自动打标签条件：累计积分达到</span> -->
                <!-- <input class="num3" type="text" onkeypress="return (/[\d.]/.test(String.fromCharCode(event.keyCode)))">分
                <p class="help-block" style="font-size:14px;">累计积分只能输入数字哦～</p> -->
              </div>
            </div>
            </form>
          </div>


<script>
  $(document).on('click','.reduce1',function(){
    if(parseInt($('.laball1').length)==0){
          alert('不能再减少');
    }else{
            $(".laball1").last().remove();
          }
  });
  $(document).on('click','.add1',function(){
    console.log(1);
    $(".laball").append(
          '<div class=\"laball1\">'+
            '<div class=\"lab3\">'+
              '<span>'+'标签名称：'+'</span>'+
              '<input class=\"name3\" type=\"text\">'+
            '</div>'+
            '<br>'+
            '<div class=\"lab3\" id=\"lab4\">'+
              '<span>'+'自动打标签条件：累计积分达到'+'</span>'+
              '<input class=\"num3\" type=\"text\" onkeydown=\"onlyNum();\" style=\"ime-mode:Disabled\">'+'分'+
            '</div>'+
          '</div>'
      );
  })
</script>

<script type="text/javascript">
  $(function () {
  var editor = new Simditor({
    textarea: $('.textarea'),
    toolbar: ['title','bold','italic','underline','strikethrough','color','ol','ul','blockquote','table','link','image','hr','indent','outdent','alignment']
  });
  var editor2 = new Simditor({
    textarea: $('.textarea2'),
    toolbar: ['title','bold','italic','underline','strikethrough','color','ol','ul','blockquote','table','link','image','hr','indent','outdent','alignment']
  });
});
</script>
<script language=javascript>
  function onlyNum()
      {
      if(!(event.keyCode==46)&&!(event.keyCode==8)&&!(event.keyCode==37)&&!(event.keyCode==39))
      if(!((event.keyCode>=48&&event.keyCode<=57)||(event.keyCode>=96&&event.keyCode<=105)))
      event.returnValue=false;
  }
</script>
<?php if($bid==1):?>
<!-- 批量打标签 结束 -->
<!-- 大转盘 -->
<div class="tab-pane" id="cfg_draw">
  <?php if ($result['ok7'] > 0):?>
  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>保存成功</div>
  <?php endif?>
<form role="form" method="post" >
<div class="box-body">
    <label for="show" style="font-size:16px;">是否开启本功能？</label>
        <div class="radio">
          <label class="checkbox-inline"  onclick="showfunc()">
            <input type="radio" name="draw[pstatus]" id="pshow1" value="1" <?=$config['pstatus'] == 1 ? ' checked=""' : ''?>>
            <span class="label label-success"  style="font-size:14px">开启</span>
          </label>
          <label class="checkbox-inline" onclick="hidefunc()">
            <input type="radio" name="draw[pstatus]" id="pshow2" value="0" <?=$config['pstatus'] == 0 ||!$config['pstatus']? ' checked=""' : ''?>>
            <span class="label label-danger"  style="font-size:14px">关闭</span>
          </label>
        </div>

<script type="text/javascript">
  function hidefunc(){

  var h2=document.getElementById("phide");
  h2.style.display="none";
}

function showfunc(){

  var h2=document.getElementById("phide");
  h2.style.display="block";
}
$(document).ready(function(){
 var status = $('#pshow1').attr('checked');
 if(status=='checked'){
  $('#phide').show();
  }else{
    $('#phide').hide();
  }
})
</script>
    <div id="phide">
    <div class="row">
    <div class="col-lg-3 col-sm-6">
      <div class="form-group">
        <label for="startdate">活动截止时间（为空则不限制）</label>
        <div class="input-group">
          <input type="text" class="form-control pull-right formdatetime" name="draw[drawtime]" value="<?=$config['drawtime']?>" readonly="">
          <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
        </div>
      </div>
    </div>
    </div>
    <script type="text/javascript">
        $(function(){
          $(".formdatetime").datetimepicker({
            format: "yyyy-mm-dd hh:ii",
            language: "zh-CN",
            minView: "0",
            autoclose: true
          });
        });
    </script>
    <div class="form-group">
      <lable>奖品设置：</lable>
        <div class="prizeSet">
          <label id="prize">一等奖</label>
          <select name="prize[0][iid]">
            <?php foreach($result['items'] as $item):?>
              <option value="<?=$item->id?>" <?php foreach($result['prizes'] as $award){if($award->iid == $item->id && $award->type == 1) echo "selected=\"selected\"";}?>><?=$item->name?></option>
            <?php endforeach;?>
          </select>
          <input type="hidden" name='prize[0][type]' value="1">
          <label id="prize">中奖概率：</label>
          <input type="text" name="prize[0][pro]" onkeyup="value=value.replace(/[^0-9.]/g,'')" <?php foreach($result['prizes'] as $award){if($award->type == 1) echo "value=\"$award->probability\"";}?>/>
          <label id="prize">(%)</label>
          <label id="prize">库存：</label>
          <input type="number" name="prize[0][stock]" id="stock1" <?php foreach($result['prizes'] as $award){if($award->type == 1) echo "value=\"$award->stock\"";}?>/>
        </div>

        <div class="prizeSet">
          <label id="prize">二等奖</label>
          <select name="prize[1][iid]">
            <?php foreach($result['items'] as $item):?>
            <option value="<?=$item->id?>" <?php foreach($result['prizes'] as $award){if($award->iid == $item->id && $award->type == 2) echo "selected=\"selected\"";}?>><?=$item->name?></option>
            <?php endforeach;?>
          </select>
          <input type="hidden" name='prize[1][type]' value="2">
          <label id="prize">中奖概率：</label>
          <input type="text" name="prize[1][pro]" onkeyup="value=value.replace(/[^0-9.]/g,'')" <?php foreach($result['prizes'] as $award){if($award->type == 2) echo "value=\"$award->probability\"";}?>/>
          <label id="prize">(%)</label>
          <label id="prize">库存：</label>
          <input type="number" name="prize[1][stock]" id="stock2" <?php foreach($result['prizes'] as $award){if($award->type == 2) echo "value=\"$award->stock\"";}?>/>
        </div>

        <div class="prizeSet">
          <label id="prize">三等奖</label>
          <select name="prize[2][iid]">
            <?php foreach($result['items'] as $item):?>
            <option value="<?=$item->id?>" <?php foreach($result['prizes'] as $award){if($award->iid == $item->id && $award->type == 3) echo "selected=\"selected\"";}?>><?=$item->name?></option>
            <?php endforeach;?>
          </select>
          <input type="hidden" name='prize[2][type]' value="3">
          <label id="prize">中奖概率：</label>
          <input type="text" name="prize[2][pro]" onkeyup="value=value.replace(/[^0-9.]/g,'')" <?php foreach($result['prizes'] as $award){if($award->type == 3) echo "value=\"$award->probability\"";}?>/>
          <label id="prize">(%)</label>
          <label id="prize">库存：</label>
          <input type="number" name="prize[2][stock]" id="stock3" <?php foreach($result['prizes'] as $award){if($award->type == 3) echo "value=\"$award->stock\"";}?> />
        </div>

        <div class="prizeSet">
          <label id="prize">四等奖</label>
          <select name="prize[3][iid]">
            <?php foreach($result['items'] as $item):?>
            <option value="<?=$item->id?>"  <?php foreach($result['prizes'] as $award){if($award->iid == $item->id && $award->type == 4) echo "selected=\"selected\"";}?>><?=$item->name?></option>
            <?php endforeach;?>
          </select>
          <input type="hidden" name='prize[3][type]' value="4">
          <label id="prize">中奖概率：</label>
          <input type="text" name="prize[3][pro]" onkeyup="value=value.replace(/[^0-9.]/g,'')" <?php foreach($result['prizes'] as $award){if($award->type == 4) echo "value=\"$award->probability\"";}?>/>
          <label id="prize">(%)</label>
          <label id="prize">库存：</label>
          <input type="number" name="prize[3][stock]" id="stock4" <?php foreach($result['prizes'] as $award){if($award->type == 4) echo "value=\"$award->stock\"";}?> />
        </div>
    </div>

    <div class="form-group">
      <label for="name">填写活动说明：</label>
      <textarea class="form-control textarea2" rows="5" name='draw[drawexplain]'><?php if(!$config['drawexplain']) {echo '抽奖活动说明：
消耗积分即可获得抽奖机会。
';}else{echo htmlspecialchars($config['drawexplain']);}?></textarea>
    </div>
    <div class="form-group">
        <label for="limitTime">每人每天抽奖次数限制:(次)</label>
        <input type="number" class="form-control" id="limitTime" name="draw[limitTime]" value="<?=floatval($config['limitTime'])?>" />
    </div>

    <div class="form-group">
      <label for="killScore">每次抽奖消耗的积分:(分)</label>
      <input type="number" class="form-control" id="killScore" name="draw[killScore]" value="<?=floatval($config['killScore'])?>" />
    </div>
    </div>
    <div class="box-footer">
          <button type="submit" class="btn btn-success">保存</button>
    </div>
</div><!-- box-body -->
</form>
</div><!-- tab-pane -->
<?php endif?>
<!-- 大转盘end -->
  <div class="tab-pane" id="cfg_account">

    <?php if ($result['ok4'] > 0):
    $_SESSION['dkaa'] = null;
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

</div>

</div><!--/.col (left) -->

</section><!-- /.content -->

<!--       <div class="box-footer">
        <input type="hidden" name="yz" value="1">
        <button type="submit" class="btn btn-success">修改登录密码</button>
      </div> -->
    </form>
  </div>

</div>
</div>

</div><!--/.col (left) -->

</section><!-- /.content -->

