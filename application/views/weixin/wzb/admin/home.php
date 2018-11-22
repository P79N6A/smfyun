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
            <!-- <li id="cfg_menu_li"><a href="#cfg_menu" data-toggle="tab">菜单配置</a></li> -->
            <li id="cfg_text_li"><a href="#cfg_text" data-toggle="tab">个性化配置</a></li>
            <li id="cfg_live_li"><a href="#cfg_live" data-toggle="tab">用户观看直播地址</a></li>
            <li id="cfg_time_li"><a href="#cfg_time" data-toggle="tab">直播预告播放时间（可选功能）</a></li>
            <!-- <li id="cfg_account_li"><a href="#cfg_account" data-toggle="tab">密码修改</a></li> -->
          </ul>

          <?php
          if ($_POST['cfg']) $active = 'wx';
          if (!$_POST || $_POST['yz']) $active = 'yz';
          if ($_POST['menu']) $active = 'menu';
          if ($_POST['text']) $active = 'text';
          if ($_POST['time']) $active = 'time';
          if (isset($_POST['password'])) $active = 'account';
          if($actives)$active = 'wx';
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

                <!-- <div class="callout callout-success">
                  <h4>有赞插件配置信息</h4>
                  <p><b>插件 url：</b> <u><?='http://'.$_SERVER["HTTP_HOST"].'/api/wzb/'.$_SESSION['wzba']['user'];?></u></p>
                  <p><b>插件 token：</b> <u>weizhibo2017</u></p>
                  <p><a href="http://koudaitong.com/apps/third?plugin_name=second" target="_blank">点击这里</a> 进入有赞插件页面设置</p>
                </div> -->

               <!--  <?php if ($result['ok'] > 0):?>
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
                      <input type="text" class="form-control" id="scene_id" placeholder="输入带参数二维码的 scene_id" name="cfg[scene_id_wzb]" value="<?=(int)$config['scene_id_wzb']?>">
                    </div> -->
                    <p class="alert alert-danger alert-dismissable">注意：有赞商户请点击有赞一键授权，以保证正常使用。</p>
                    <?php if($oauth==1):?>
                  <div class="lab4">
                    <a href='/wzba/oauth'><button type="button" class="btn btn-warning">点击一键授权有赞</button></a>
                  </div>
                  <br>
                  <?else:?>
                  <div class="lab4">
                    <a href='/wzba/oauth'><button type="button" class="btn btn-warning">点击重新授权有赞</button></a>
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

                <?php if ($result['ok2'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>微信配置保存成功!</div>
                <?php endif?>
                <?php if ($result['err2']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err2']?></div>
                <?php endif?>
                <!-- form start -->
                <form role="form" method="post" enctype="multipart/form-data">
                  <div class="box-body">

                    <!-- <div class="form-group">
                      <label for="name">微信公众号名称</label>
                      <input type="text" class="form-control" id="name" placeholder="输入公众号名称" maxlength="20" name="cfg[name]" value="<?=$config['name']?>">
                    </div> -->
                    <?php if($user->refresh_token):?>
                    <a href='https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=wxd0b3a6ff48335255&pre_auth_code=<?=$pre_auth_code?>&redirect_uri=http://<?=$_SERVER["HTTP_HOST"]?>/wzba/home?wx=1'><button type="button" class="btn btn-success">点击重新授权</button></a>
                  <?php else:?>
                    <a href='https://mp.weixin.qq.com/cgi-bin/componentloginpage?component_appid=wxd0b3a6ff48335255&pre_auth_code=<?=$pre_auth_code?>&redirect_uri=http://<?=$_SERVER["HTTP_HOST"]?>/wzba/home?wx=1'><button type="button" class="btn btn-warning">点击一键授权</button></a>
                  <?php endif?>
                    <!-- <div class="form-group">
                      <label for="appid">微信公众号App Id（填写后不可修改）</label>
                      <input type="text" class="form-control" id="appid" placeholder="输入 App Id" maxlength="18" name="cfg[appid]" value="<?=$config['appid']?>"<?php if($config['appid']) echo 'readonly=""'?>>
                    </div>

                    <div class="form-group">
                      <label for="appsecret">微信公众号App Secret</label>
                      <input type="text" class="form-control" id="appsecret" placeholder="输入 App Secret" maxlength="32" name="cfg[appsecret]" value="<?=$config['appsecret']?>">
                    </div> -->
                      <br>
                      <br>
                      <div class="alert alert-danger alert-dismissable"><i class="icon fa fa-check"></i>以下选项是需要用到微信发送红包功能的必填项</div>
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

            <!-- <div class="tab-pane" id="cfg_menu">

                <?php if ($result['ok2'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>菜单配置保存成功!</div>
                <?php endif?>

                <?php if ($result['err2']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err2']?></div>
                <?php endif?>


                <form role="form" method="post" class="form-horizontal2">
                  <div class="box-body">

                    <div class="row">
                    <div class="col-lg-3 col-sm-3">
                      <div class="form-group">
                        <label for="menu0">1.「生成海报」对应的菜单 KEY</label>
                        <input type="text" class="form-control" id="menu0" placeholder="生成海报" maxlength="10" name="menu[key_wzbqrcode]" value="<?=$config['key_wzbqrcode']?>">
                      </div>
                    </div>
                    </div>

                    <div class="row">
                    <div class="col-lg-8">
                      <div class="form-group">
                        <label for="menu1">2.「资产查询」对应的菜单 KEY</label>
                        <input type="text" class="form-control" id="menu1" placeholder="资产查询" maxlength="10" name="menu[key_wzbscore]" value="<?=$config['key_wzbscore']?>">
                      </div>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-lg-8">
                      <div class="form-group">
                        <label for="menu2">3.「申请分销」对应的菜单网址（需要在 mp 后台设置 Oauth 网页回调域名为 <?=$_SERVER["HTTP_HOST"]?>）</label>
                        <input readonly="" type="text" class="form-control" id="menu2" maxlength="10" value="<?='http://'.$_SERVER["HTTP_HOST"].'/wzb/index_oauth/'.$_SESSION['wzba']['bid'].'/form'?>">
                      </div>
                    </div>
                    </div>

                    <div class="alert alert-danger">以下为高级选项，如果不清楚如何设置，请保持为空。</div>

                    <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <label for="menu10">预留自定义 KEY</label>
                          <input type="text" class="form-control" id="menu10" placeholder="自定义 KEY1" maxlength="10" name="menu[key_c1_wzb]" value="<?=$config['key_c1_wzb']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <label for="v10">点击后回复文字（使用 \n 换行）</label>
                          <input type="text" class="form-control" id="v10" placeholder="点击自定义 KEY1 后的回复文字" maxlength="200" name="menu[value_c1_wzb]" value="<?=$config['value_c1_wzb']?>">
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <input type="text" class="form-control" id="menu11" placeholder="自定义 KEY2" maxlength="10" name="menu[key_c2_wzb]" value="<?=$config['key_c2_wzb']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" id="v11" placeholder="点击自定义 KEY2 后的回复文字" maxlength="200" name="menu[value_c2_wzb]" value="<?=$config['value_c2_wzb']?>">
                        </div>
                      </div>
                    </div>

                   <div class="row">
                      <div class="col-lg-3 col-sm-3">
                        <div class="form-group">
                          <input type="text" class="form-control" id="menu12" placeholder="自定义 KEY3" maxlength="10" name="menu[key_c3_wzb]" value="<?=$config['key_c3_wzb']?>">
                        </div>
                      </div>
                     <div class="col-lg-9 col-sm-9">
                        <div class="form-group">
                          <input type="text" class="form-control" id="v12" placeholder="点击自定义 KEY3 后的回复文字" maxlength="200" name="menu[value_c3_wzb]" value="<?=$config['value_c3_wzb']?>">
                        </div>
                      </div>
                    </div>

                  </div>

                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">保存菜单配置</button>
                  </div>
                </form>
            </div> -->

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
                      <label for="push">有赞店铺主页地址（必填）</label>
                      <input type="text" class="form-control" id="shop_url" name='text[shop_url]'  value="<?=$config['shop_url']?>">
                    </div>
                  <!-- <div class="alert alert-danger">海报设置</div> -->
                    <p class="alert alert-danger alert-dismissable">直播界面自定义</p>
                    <div class="form-group">
                      <label for="push">直播标题自定义</label>
                      <input type="text" class="form-control" id="title" name='text[title]'  value="<?=$config['title']?>">
                    </div>
                    <div class="form-group">
                      <label for="push">直播昵称自定义</label>
                      <input type="text" class="form-control" id="name" name='text[name]'  value="<?=$config['name']?$config['name']:$user->name?>">
                    </div>
                    <div class="form-group">
                      <label for="push">直播在线人数增加</label>
                      <input type="number" class="form-control" id="num" name='text[num]'  value="<?=$config['num']?>">
                    </div>
                    <?php
                    if ($result['tpl']):
                    ?>
                    <!-- <div class="form-group">
                      <a href="/wzba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/wzba/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" width="200" title="点击查看原图"></a>
                    </div> -->
                    <?php endif?>

                    <!-- <div class="form-group">
                      <label for="pic">直播封面图片</label>
                      <input type="file" class="form-control" id="pic" name="pic" accept="image/jpeg">
                      <p class="help-block">只能为 JPEG 格式，规格建议为 640*1136px，最大不超过 300K</p>
                    </div> -->
                    <?php
                    //默认头像
                    if ($result['tplhead']):
                    ?>
                    <div class="form-group">
                      <a href="/wzba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/wzba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
                    </div>
                    <?php endif?>

                    <div class="form-group">
                      <label for="pic2">自定义直播头像（可选）</label>
                      <input type="file" class="form-control" id="pic2" name="pic2" accept="image/jpeg">
                      <p class="help-block">只能为 JPEG 格式，正方形，最大不超过 100K。</p>
                    </div>
                    <p class="alert alert-danger alert-dismissable">直播分享自定义</p>
                    <?php
                    //默认头像
                    if ($result['tplshare']):
                    ?>
                    <div class="form-group">
                      <a href="/wzba/images/cfg/<?=$result['tplshare']?>.v<?=time()?>.jpg" target="_blank"><img class="img-thumbnail" src="/wzba/images/cfg/<?=$result['tplshare']?>.v<?=time()?>.jpg" width="100" title="点击查看原图"></a>
                    </div>
                    <?php endif?>

                    <div class="form-group">
                      <label for="pic3">自定义分享图标（可选）</label>
                      <input type="file" class="form-control" id="pic3" name="pic3" accept="image/jpeg">
                      <p class="help-block">只能为 JPEG 格式，正方形，最大不超过 100K。</p>
                    </div>

                    <div class="form-group">
                      <label for="push">直播分享到朋友圈标题自定义</label>
                      <input type="text" class="form-control" id="title" name='text[wsptitle]'  value="<?=$config['wsptitle']?>">
                    </div>
                    <div class="form-group">
                      <label for="push">直播分享给朋友标题自定义</label>
                      <input type="text" class="form-control" id="title" name='text[wstitle]'  value="<?=$config['wstitle']?>">
                    </div>
                    <div class="form-group">
                      <label for="push">直播分享给朋友内容自定义</label>
                      <input type="text" class="form-control" id="title" name='text[wsdesc]'  value="<?=$config['wsdesc']?>">
                    </div>
                    <p class="alert alert-danger alert-dismissable">模板消息自定义</p>
                    <div class="form-group">
                      <label for="push">模板消息ID（开启直播后给已订阅用户发送模板消息，所在行业 IT科技 互联网|电子商务 ；模板消息标题：参与成功通知；模板编号：OPENTM407568708）</label>
                      <input type="text" class="form-control" id="title" name='text[starttpl]'  value="<?=$config['starttpl']?>">
                    </div>
                    <div class="form-group">
                      <label for="push">模板消息直播标题自定义</label>
                      <input type="text" class="form-control" id="title" name='text[tpl_top]'  value="<?=$config['tpl_top']?>">
                    </div>
                    <div class="form-group">
                      <label for="push">模板消息活动名称自定义</label>
                      <input type="text" class="form-control" id="title" name='text[tpl_content]'  value="<?=$config['tpl_content']?>">
                    </div>
                    <div class="form-group">
                      <label for="push">模板消息底部消息自定义</label>
                      <input type="text" class="form-control" id="title" name='text[tpl_bottom]'  value="<?=$config['tpl_bottom']?>">
                    </div>

                    <!-- <div class="form-group">
                      <label for="push">直播推流地址</label>
                      <input type="text" class="form-control" id="pushurl" readonly="true" value="rtmp://video-center.alivecdn.com/AppName/">
                    </div>
                    <div class="form-group">
                      <label for="push">直播推流秘钥</label>
                      <input type="text" class="form-control" id="pushsecret" readonly="true" value="<?=$_SESSION['wzba']['sid']?>?vhost=live.smfyun.com">
                    </div>
                    <div class="form-group">
                      <label for="get">直播拉流地址</label>
                      <input type="text" class="form-control" id="get" readonly="true" value="http://live.smfyun.com/AppName/<?=$_SESSION['wzba']['sid']?>.m3u8">
                    </div> -->
                  </div><!-- /.box-body -->

                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">更新个性化配置</button>
                  </div>
                </form>
            </div>
            <div class="tab-pane" id="cfg_live">
                <p class="alert alert-danger alert-dismissable">用户观看及订阅直播地址</p>
                    <div class="form-group">
                      <label for="menu2">用户观看直播地址</label>
                      <input readonly="" type="text" class="form-control" id="menu2" value="<?='http://'.$_SERVER["HTTP_HOST"].'/wzb/index_oauth/'.$_SESSION['wzba']['bid'].'/live'?>">
                    </div>
                    <div class="form-group">
                      <label for="menu3">用户订阅直播地址</label>
                      <input readonly="" type="text" class="form-control" id="menu3" value="<?='http://'.$_SERVER["HTTP_HOST"].'/wzb/index_oauth/'.$_SESSION['wzba']['bid'].'/sub'?>">
                    </div>
            </div>
            <div class="tab-pane" id="cfg_time">
                <p class="alert alert-danger alert-dismissable">未开播时，将开播时间显示在直播间页面上（可选功能）</p>
                <form method="post">

                    <div class="form-group">
                      <label for="announce">是否开启直播间直播预告功能</label>
                      <div class="radio">
                        <label class="checkbox-inline"  onclick="showfunc()">
                          <input type="radio" name="time[timesiwtch]" id="pshow1" value="1" <?=$config['timesiwtch']==1?"checked":""?>>
                          <span class="label label-success"  style="font-size:14px">开启</span>
                        </label>
                        <label class="checkbox-inline" onclick="hidefunc()">
                          <input type="radio" name="time[timesiwtch]" id="pshow2" value="0" <?=$config['timesiwtch']==0?"checked":""?>>
                          <span class="label label-danger"  style="font-size:14px">关闭</span>
                        </label>
                      </div>
                    </div>
                    <div id="timepickerbox" class="form-group" <?=$config['timesiwtch']==1?'':'style="display:none;"'?>>
                      <label for="menu2">请选中开播时间（精确到分）</label>
                      <div class="timepicker">
                        <input type="text" class="form-control pull-left formdatetime" name="time[time]" value="<?=$config['time']?>" readonly="" style="width:auto;">
                        <div class="input-group-addon" style="height:34px;width:34px;background-color:#fff;"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">保存</button>
                  </div>
                </form>
            </div>
            <div class="tab-pane" id="cfg_account">

                <?php if ($result['ok4'] > 0):
                $_SESSION['wzba'] = null;
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
<script type="text/javascript">
    $(document).ready(function(){
      alert('公告：神码云直播商户端APP安卓版已更新，为保证正常使用，请在神码云直播-商户端app下载，重新安装app；ios客户端已下架，如需使用ios客户端，添加微信smfyun2016，迁移到新版应用平台使用。');
    })
function showfunc(){
  $('#timepickerbox').fadeIn(500);
}
function hidefunc(){
  $('#timepickerbox').fadeOut(500);
}
$(function () {
  $(".formdatetime").datetimepicker({
    format: "yyyy-mm-dd hh:ii",
    language: "zh-CN",
    minView: "0",
    autoclose: true
  });
})
</script>
