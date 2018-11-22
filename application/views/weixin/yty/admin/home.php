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

                <!-- <div class="callout callout-danger">
                  <p>首次使用，请 <a href="https://wap.koudaitong.com/v2/showcase/feature?alias=j2avl2px" target="_blank">点击这里</a> 查看订单宝系统使用说明书</p>
                </div> -->

                <div class="callout callout-success">
                  <h4>有赞插件配置信息</h4>
                  <p><b>插件 url：</b> <u><?='http://'.$_SERVER["HTTP_HOST"].'/api/yty/'.$_SESSION['ytya']['user'];?></u></p>
                  <p><b>插件 token：</b> <u>yuntianyou2017</u></p>
                  <p><a href="http://koudaitong.com/apps/third?plugin_name=second" target="_blank">点击这里</a> 进入有赞插件页面设置</p>
                </div>

                <?php if ($result['ok'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>有赞配置保存成功!</div>
                <?php endif?>

                <!-- form start -->
                <form role="form" method="post">
                  <div class="box-body">
                    <p class="alert alert-danger alert-dismissable">注意：请务必点击有赞一键授权，否则功能将无法使用</p>
                    <?php if($oauth==1):?>
                  <div class="lab4">
                    <a href='/ytya/oauth'><button type="button" class="btn btn-warning">点击一键授权有赞</button></a>
                  </div>
                  <br>
                  <?else:?>
                  <div class="lab4">
                    <a href='/ytya/oauth'><button type="button" class="btn btn-warning">点击重新授权有赞</button></a>
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
                        <label for="menu1">1.「经销商个人中心」对应的菜单 KEY</label>
                        <input type="text" class="form-control" id="menu1" placeholder="资产查询" maxlength="10" name="menu[key_ytyscore]" value="<?=$config['key_ytyscore']?>">
                      </div>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-lg-8">
                      <div class="form-group">
                        <label for="menu2">2.「S级经销商申请」对应的网址（需要在 mp 后台设置 Oauth 网页回调域名为 <?=$_SERVER["HTTP_HOST"]?>）</label>
                        <input readonly="" type="text" class="form-control" id="menu2" maxlength="10" value="<?='http://'.$_SERVER["HTTP_HOST"].'/yty/index_oauth/'.$_SESSION['ytya']['bid'].'/'.base64_encode('sjifenxiaoshang').'/form'?>">
                      </div>
                    </div>
                    </div>

                    <div class="alert alert-danger">以下为高级选项，如果不清楚如何设置，请保持为空。</div>

                    <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <label for="menu10">预留自定义 KEY</label>
                          <input type="text" class="form-control" id="menu10" placeholder="自定义 KEY1" maxlength="10" name="menu[key_c1_yty]" value="<?=$config['key_c1_yty']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <label for="v10">点击后回复文字（使用 \n 换行）</label>
                          <input type="text" class="form-control" id="v10" placeholder="点击自定义 KEY1 后的回复文字" maxlength="200" name="menu[value_c1_yty]" value="<?=$config['value_c1_yty']?>">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <input type="text" class="form-control" id="menu11" placeholder="自定义 KEY2" maxlength="10" name="menu[key_c2_yty]" value="<?=$config['key_c2_yty']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" id="v11" placeholder="点击自定义 KEY2 后的回复文字" maxlength="200" name="menu[value_c2_yty]" value="<?=$config['value_c2_yty']?>">
                        </div>
                      </div>
                    </div>

                   <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <input type="text" class="form-control" id="menu12" placeholder="自定义 KEY3" maxlength="10" name="menu[key_c3_yty]" value="<?=$config['key_c3_yty']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" id="v12" placeholder="点击自定义 KEY3 后的回复文字" maxlength="200" name="menu[value_c3_yty]" value="<?=$config['value_c3_yty']?>">
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
                    //默认头像
                    if ($result['tpl']):
                  ?>
                <div class="form-group">
                  <a href="/ytya/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/ytya/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
                </div>
              <?php endif?>
                <div class="form-group">
                  <label for="pic">公众号二维码</label>
                  <input type="file" class="form-control" id="pic" name="pic" accept="image/jpeg">
                  <p class="help-block">只能为 JPEG 格式，正方形，最大不超过 100K。</p>
                </div>
                <?php
                if ($result['tplhead']):
                  ?>
                <div class="form-group">
                  <a href="/ytya/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/ytya/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
                </div>
              <?php endif?>
                <div class="form-group">
                  <label for="pic2">品牌logo（分享商品list时会用到）</label>
                  <input type="file" class="form-control" id="pic2" name="pic2" accept="image/jpeg">
                  <p class="help-block">只能为 JPEG 格式，正方形，最大不超过 100K。</p>
                </div>
                    <div class="alert alert-danger">佣金及提现设置</div>

                    <div class="row">
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
                          <label for="money1">升级后奖励给上级分销商占本人目前经销商等级所需的代理金的比重（%）</label>
                          <input type="number" class="form-control" id="money1" name="text[money0]" value="<?=intval($config['money0'])?>">
                        </div>
                      </div>
                      <div class="col-lg-2 col-sm-4">
                        <div class="form-group">
                          <label for="money1">升级后每次进货额奖励给上级经销商的比重（%）</label>
                          <input type="number" class="form-control" id="money1" name="text[money1]" value="<?=intval($config['money1'])?>">
                        </div>
                      </div>
                      <div class="col-lg-2 col-sm-4">
                        <div class="form-group">
                          <label for="money_out">最小提现金额（分）</label>
                          <input type="number" class="form-control" id="money_out" name="text[money_out]" value="<?=intval($config['money_out'])?>">
                        </div>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="list_text">分享商品list文案自定义</label>
                      <input type="text" class="form-control" id="list_text" name="text[list_text]"  value="<?=$config['list_text']?>">
                    </div>
                    <div class="form-group">
                      <label for="dp_url">店铺链接</label>
                      <input type="text" class="form-control" id="dp_url" name="text[dp_url]"  value="<?=$config['dp_url']?>">
                    </div>
                    <div class="form-group">
                      <label for="team_title">邀请代理时分享的标题</label>
                      <input type="text" class="form-control" id="team_title" name="text[team_title]"  value="<?=$config['team_title']?>">
                    </div>
                    <div class="form-group">
                      <label for="team_desc">邀请代理时分享的内容</label>
                      <input type="text" class="form-control" id="team_desc" name="text[team_desc]"  value="<?=$config['team_desc']?>">
                    </div>
                    <div class="alert alert-danger">消息设置</div>
                    <div class="form-group">
                      <label for="goal">进货额余额变更通知（行业：IT科技 - 互联网|电子商务，模板标题「变更通知」模板编号：OPENTM403182052）</label>
                      <input type="text" class="form-control" id="goal" name="text[money_arrived_tpl]" placeholder="" value="<?=$config['money_arrived_tpl']?>">
                    </div>
                    <div class="form-group">
                      <label for="goal">待处理任务消息模板（行业：IT科技 - 互联网|电子商务，模板标题「任务处理通知」模板编号：OPENTM200605630）</label>
                      <input type="text" class="form-control" id="goal" name="text[task_deal_tpl]" placeholder="" value="<?=$config['task_deal_tpl']?>">
                    </div>
                    <div class="form-group">
                      <label for="goal">审核通过消息模板（行业：IT科技 - 互联网|电子商务，模板标题「审核结果通知」模板编号：OPENTM405884804）</label>
                      <input type="text" class="form-control" id="goal" name="text[msg_success_tpl]" placeholder="" value="<?=$config['msg_success_tpl']?>">
                    </div>
                    <div class="form-group">
                      <label for="goal">收益通知消息模板（行业：金融业 - 证券|基金|理财|信托，模板标题「账户收益通知」模板编号：OPENTM405980080）</label>
                      <input type="text" class="form-control" id="goal" name="text[msg_score_tpl]" placeholder="" value="<?=$config['msg_score_tpl']?>">
                    </div>
                    <div class="form-group">
                      <label for="goal2">账户余额通知消息模板（行业：IT科技 - 互联网|电子商务，模板标题「账户余额变动通知」模板编号：OPENTM205454780）</label>
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
                $_SESSION['ytya'] = null;
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

