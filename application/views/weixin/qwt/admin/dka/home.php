
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
    <style type="text/css">
    .am-badge{
        background-color: green;
    }
    .am-selected-content{
        max-height: 180px;
        overflow: scroll;
    }
    .switch-content{
        overflow: hidden !important;
    }
    label{
        text-align: left !important;
    }
    .hide{
        height: 0;
        overflow: hidden;
    }
    #datetimepicker1{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    #datetimepicker2{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    .search-btn1{
        display: inline-block;
        background-color: white;
    border-radius: 5px;
    border: 1px solid #e5e5e5;
    color: black;
    border-top-left-radius: 5px !important;
    border-bottom-left-radius: 5px !important;
    }
    .inputtxt{
        width: 50px !important;
        display: inline-block !important;
    }
    .switch-content-1,.switch-content-2{
  -webkit-transition: all 0.5s ease;
  -moz-transition: all 0.5s ease;
  -ms-transition: all 0.5s ease;
  -o-transition: all 0.5s ease;
  transition: all 0.5s ease;
    }
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">


        <div class="tpl-content-wrapper">
            <div id="name1" class="tpl-content-page-title">
                个性化设置
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">打卡宝</a></li>
                <li class="am-active">个性化设置</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <ul class="am-tabs-nav am-nav am-nav-tabs" style="left:0;">
                                <!-- <li id="tab1-bar"><a href="#tab1">菜单配置</a></li> -->
                                <li id="tab2-bar"><a href="#tab2">邀请卡配置</a></li>
                                <li id="tab3-bar"><a href="#tab3">打卡设置</a></li>
                                <!-- <li id="tab4-bar"><a href="#tab4">积分清零</a></li> -->
                                <!-- <li id="tab5-bar"><a href="#tab5">批量打标签</a></li> -->
                                <?php if($bid==1):?>
                                <li id="tab6-bar"><a href="#tab6">积分抽奖设置</a></li>
                              <?php endif?>
                            </ul>

                            <div class="am-tabs-bd"><!--
                                <div class="am-tab-panel am-fade" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
                            <form role="form" method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                                    <?php if ($result['ok2']>0):?>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 菜单配置保存成功！</p>
                            </div>
                        </div>
                      <?php endif?>
                                    <?php if ($result['err2']>0):?>
                    <div class="tpl-content-scope">
                            <div class="note note-danger">
                                <p> <?=$result['err2']?></p>
                            </div>
                        </div>
                      <?php endif?>
                                <div class="am-form-group">
                                    <label for="menu4" class="am-u-sm-12 am-form-label">1.「我要打卡」对应的菜单 KEY</label>
                                    <div class="am-u-sm-12">
                  <input type="text" class="form-control" id="menu1" placeholder="我要打卡" maxlength="20" name="menu[key_dka]" value="<?=$config['key_dka']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu4" class="am-u-sm-12 am-form-label">2.「积分查询」对应的菜单 KEY</label>
                                    <div class="am-u-sm-12">
                  <input type="text" class="form-control" id="menu3" placeholder="积分查询" maxlength="20" name="menu[key_score]" value="<?=$config['key_score']?>">
                                    </div>
                                </div>
                    <div class="tpl-content-scope">
                            <div class="note note-danger">
                                <p> 以下为高级选项，如果不清楚如何设置，请保持为空。</p>
                            </div>
                        </div>
                                <div class="am-form-group">
                                    <label for="menu4" class="am-u-sm-3 am-form-label">预留自定义 KEY</label>
                                    <label for="menu4" class="am-u-sm-9 am-form-label">点击后回复文字（使用 \n 换行）</label>
                                    </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-3">
                      <input type="text" class="form-control" id="menu10" placeholder="自定义 KEY1" maxlength="20" name="menu[key_c1]" value="<?=$config['key_c1']?>">
                                    </div>
                                    <div class="am-u-sm-9">
                      <input type="text" class="form-control" id="v10" placeholder="点击自定义 KEY1 后的回复文字" maxlength="200" name="menu[value_c1]" value="<?=$config['value_c1']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-3">
                      <input type="text" class="form-control" id="menu11" placeholder="自定义 KEY1" maxlength="20" name="menu[key_c2]" value="<?=$config['key_c2']?>">
                                    </div>
                                    <div class="am-u-sm-9">
                      <input type="text" class="form-control" id="v11" placeholder="点击自定义 KEY1 后的回复文字" maxlength="200" name="menu[value_c2]" value="<?=$config['value_c2']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-3">
                      <input type="text" class="form-control" id="menu12" placeholder="自定义 KEY1" maxlength="20" name="menu[key_c2]" value="<?=$config['key_c2']?>">
                                    </div>
                                    <div class="am-u-sm-9">
                      <input type="text" class="form-control" id="v12" placeholder="点击自定义 KEY1 后的回复文字" maxlength="200" name="menu[value_c3]" value="<?=$config['value_c3']?>">
                                    </div>
                                </div>
                                <hr>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-danger">保存菜单配置</button>
                        </div>
                </div>
                </form>
                </div>
                </div>
                </div>
                </div>
                </div>
 -->
                                <div class="am-tab-panel am-fade" id="tab2">
                                    <div id="wrapperB" class="wrapper">
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
                            <form role="form" method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                                    <?php if ($result['ok3']>0):?>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 邀请卡设置保存成功！</p>
                            </div>
                        </div>
                      <?php endif?>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 邀请卡设置</p>
                            </div>
                        </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">邀请卡背景图</label>
                                    <div class="am-u-sm-12">
                <?php
                if ($result['tpl']):
                  ?>
                  <a href="/qwtdkaa/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwtdkaa/images/cfg/<?=$result['tpl']?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传邀请卡背景图</button>
                                        <div id="file-pic" style="display:inline-block;"></div>
                                            <input id="pic" type="file" name="pic" accept="image/jpeg" multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格建议为 640*900px，最大不超过 400K，<a href="/dka/tpl.zip" target="_blank">点击这里</a> 下载 PSD 模板自行设计</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">默认头像（获取头像失败时会用该头像，可选）</label>
                                    <div class="am-u-sm-12">
              <?php
                    //默认头像
              if ($result['tplhead']):
                ?>
                  <a href="/qwtdkaa/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwtdkaa/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传默认头像</button>
                                        <div id="file-pic2" style="display:inline-block;"></div>
                                            <input id="pic2" type="file" name="pic2" accept="image/jpeg" multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，正方形，最大不超过 100K。</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">海报有效期（单位：天，最长 30 天）</label>
                                    <div class="am-u-sm-12">
                  <input type="number" min="1" max="30" class="form-control" id="ticket_lifetime" name="text[ticket_lifetime]" value="<?=intval($config['ticket_lifetime'])?>">
                                    </div>
                                </div>
          <?php if($_SESSION['dkaa']['admin'] >= 1):?>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 积分规则设置</p>
                            </div>
                        </div>
            <div class="am-form-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal0" class="am-form-label">首次关注奖励积分</label>
                  <input type="number" class="form-control" id="goal00" name="text[goal00]" value="<?=intval($config['goal00'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal" class="am-form-label">直接推荐奖励积分</label>
                  <input type="number" class="form-control" id="goal01" name="text[goal01]" value="<?=floatval($config['goal01'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal2" class="am-form-label">间接推荐奖励积分</label>
                  <input type="number" class="form-control" id="goal02" name="text[goal02]" value="<?=intval($config['goal02'])?>">
                                    </div>
                                </div>
                </div>
            </div>
          <?php endif ?>
            <?php if($bid==1):?>
                <div class="am-u-sm-6">
                                <div class="am-form-group">
                                    <div>
                                    <label for="rank" class="am-form-label">积分名称自定义（为空则不修改，最长为两个字）</label>
                                        <input type="text" id="rank" name="text[score]"  maxlength='2' value="<?=$config['score']?>" placeholder="输入积分名称">
                                    </div>
                                </div>
                </div>
              <?php endif?>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 文案设置</p>
                            </div>
                        </div>

                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">首次关注推送网址</label>
                                    <div class="am-u-sm-12">
              <input type="text" class="form-control" id="text_follow_url" name="text[text_follow_url]" placeholder="http://" value="<?=$config['text_follow_url']?>">
                                        <small>
                                        建议设置为活动攻略的微杂志。</small>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">邀请卡生成前发送消息</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="form-control" id="send" name="text[text_send]" placeholder="正在为您生成邀请卡，请稍后..." value="<?=htmlspecialchars($config['text_send'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">直接邀请提示文案：</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="form-control" id="goal" name="text[text_goal]" placeholder="您的小伙伴「%s」已接受邀请，加入神码浮云早起计划…" value="<?=htmlspecialchars($config['text_goal'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">间接邀请提示文案：</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="form-control" id="goal2" name="text[text_goal2]" placeholder="您又获得一个小伙伴「%s」加入早起计划…" value="<?=htmlspecialchars($config['text_goal2'])?>">
                                    </div>
                                </div>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 风险控制选项（直接粉丝超过多少，间接粉丝少于多少则锁定，锁定后不能兑换没有新增积分；单个用户兑换奖品次数上限）</p>
                </div>
            </div>
            <div class="am-from-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="risk_level1" class="am-form-label">推荐多少直接粉丝</label>
                        <input type="number" class="form-control" id="risk_level1" name="px[risk_level1]" value="<?=intval($config['risk_level1'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="risk_level2" class="am-form-label">少于多少个间接粉丝</label>
                        <input type="number" class="form-control" id="risk_level2" name="px[risk_level2]" value="<?=floatval($config['risk_level2'])?>">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="day_limit" class="am-form-label">单个用户兑换奖品次数上限（0为不限）</label>
                        <input type="number" class="form-control" id="day_limit" name="px[day_limit]" value="<?=floatval($config['day_limit'])?>">
                                    </div>
                                </div>
                </div>
            </div>
                                <div class="am-form-group">
                                    <label for="risk" class="am-u-sm-12 am-form-label">锁定用户文案</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="form-control" id="risk" name="text[text_risk]" placeholder='您的账号存在安全风险，暂时无法兑换奖品。' value="<?=htmlspecialchars($config['text_risk'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">保存邀请卡设置</button>
                                    </div>
                                </div>
            </form>
                </div>
                </div>
                </div>
                </div>
                </div>
                                <div class="am-tab-panel am-fade" id="tab3">
                                    <div id="wrapperC" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g">
                        <div class="tpl-form-body tpl-amazeui-form">
                            <form role="form" method="post" class="am-form am-form-horizontal" enctype='multipart/form-data' onsubmit='return checktime()'>
                        <div class="am-u-sm-12">
                                    <?php if ($result['ok5']>0):?>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 打卡页面配置保存成功！</p>
                            </div>
                        </div>
                      <?php endif?>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 打卡页面背景图</p>
                            </div>
                        </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">打卡页面背景图</label>
                                    <div class="am-u-sm-12">
      <?php if ($result['dka']):?>
                  <a href="/qwtdkaa/images/cfg/<?=$result['dka']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwtdkaa/images/cfg/<?=$result['dka']?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传打卡页面背景图</button>
                                        <div id="file-pic3" style="display:inline-block;"></div>
                                        <input type="file" id="pic3" name="pic3" accept="image/jpg">
                                        </div>
                                        <small>
                                        只能为 JPG 格式，规格建议为640*350px，最大不超过 400K</small>

                                    </div>
                                </div>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 填写活动说明</p>
                            </div>
                        </div>
                                <div class="am-form-group">
                                    <label for="qwt_fxbmoney_desc" class="am-u-sm-12 am-form-label">活动说明文案</label>
                                    <div class="am-u-sm-12">
        <textarea class="form-control textarea" rows="5" name='dka[explain]'><?php if(!$config['explain']) {echo htmlspecialchars('积分奖励规则：<br>
1、每天5:00~15:00打卡，固定奖励2积分<br>
2、连续第10天打卡，当天奖励10。连续打卡超过10天，每天固定奖励5分，打卡中断后，再次打卡奖励积分重新从2分开始计算，总积分累积。<br>
3、每日打卡，通过摇一摇、砸金蛋的形式也可以获取积分；<br>
4、好友通过你分享的邀请卡扫码关注公众号，每天打卡，你能获得相应的积分奖励；其他用户通过好友分享的邀请卡扫码关注公众号，每天打卡，你也能获得相应的积分奖励；<br>');}else{echo htmlspecialchars($config['explain']);}?></textarea>
                                    </div>
                                </div>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 模板消息设置</p>
                            </div>
                        </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">打卡提醒模板消息ID（IT科技 - 互联网|电子商务「考勤打卡提醒」模板编号：OPENTM413234390）:</label>
                                    <div class="am-u-sm-12">
        <input type="text" class="form-control" name='dka[tplid]' id="name" placeholder="请输入模版消息ID" value="<?=$config['tplid']?>" >
                                    </div>
                                </div>
                                <div class="am-form-group">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 砸金蛋设置</p>
                            </div>
                        </div>
                        </div>
                                <div class="am-form-group">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="actions">
                                <ul class="actions-btn">
                                    <li id="switch-on-1" class="green <?=$config['eggst'] == 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off-1" class="red <?=$config['eggst'] == 0 ? 'red-on' : ''?>">关闭</li>
                                    <input type="hidden" name="dka[eggst]" id="show5" value="<?=$config['eggst'] == 1 ? 1 : 0?>">
                                </ul>
                            </div>
                </div>
                </div>
                                <div class="am-form-group">
                        <div class="am-u-sm-12 switch-content-1 <?=$config['eggst'] ==0 ? 'hide' : ''?>" style="padding:0;">
                                <div class="am-form-group">
          <span class="spantxt">1、金蛋的积分范围：</span>
          <input class="inputtxt" type="text" name="dka[eggstart]" value="<?php if(!$config['eggstart']){echo 1;}else{echo $config['eggstart'];}?>">
          <span class="spantxt">分到</span>
          <input class="inputtxt" type="text" name="dka[eggend]" value="<?php if(!$config['eggend']){echo 5;}else{echo $config['eggend'];}?>">
          <span class="spantxt">分；</span><br><br>
          <span class="spantxt">2、砸中金蛋的概率：</span>
          <input class="inputtxt" type="text" name="dka[eggchance]" value="<?php if(!$config['eggchance']){echo 10;}else{echo $config['eggchance'];}?>" onchange="if(!/(^0$)|(^100$)|(^\d{1,2}$)/.test(value)){value='';alert('请参照下面的格式输入哦!');}" />
          <span class="spantxt">％；</span>
          <p class="help-block">请填写0到100之间的正整数，参照这样的格式：78%</p>
                                </div>
                                </div>
                                </div>
                                <div class="am-form-group">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 摇一摇设置</p>
                            </div>
                        </div>
                        </div>
                                <div class="am-form-group">
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="actions">
                                <ul class="actions-btn">
                                    <li id="switch-on-2" class="green <?=$config['shakest'] == 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off-2" class="red <?=$config['shakest'] == 0 ? 'red-on' : ''?>">关闭</li>
                                    <input type="hidden" name="dka[shakest]" id="show6" value="<?=$config['shakest'] == 1 ? 1 : 0?>">
                                </ul>
                            </div>
                </div>
                </div>
                                <div class="am-form-group">
                        <div class="am-u-sm-12 switch-content-2 <?=$config['shakest'] ==0 ? 'hide' : ''?>" style="padding:0;">
                                <div class="am-form-group">
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
                                </div>
                                </div>
                                <div class="am-form-group">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 打卡积分设置</p>
                            </div>
                        </div>
                        </div>
                                <div class="am-form-group">
                        <div class="am-u-sm-12">
                                <div class="am-form-group">

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
          <span class="spantxt">点签到积分减半；</span><br><br>
          <label for="name">请选择可打卡时间段：</label>
          <input type="text" name='dka[start]' class="a inputtxt" value="<?php if(!$config['start']){echo 0;}else{echo $config['start'];}?>">:00&nbsp到&nbsp
          <input type="text" name='dka[end]' class="a2 inputtxt" value="<?php if(!$config['end']){echo 10;}else{echo $config['end'];}?>">:00
          <!-- <input type="button" value="保存" class="b"><br> -->
          <p class="help-block">请输入0——24之间的正整数，参照这样的格式：5:00 到 8:00</p>
                                </div>
                                </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">好友打卡奖励的积分</label>
                                    <div class="am-u-sm-12">
                  <input type="number" class="form-control" id="goal" name="dka[goal]" value="<?=floatval($config['goal'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">好友的好友打卡奖励的积分</label>
                                    <div class="am-u-sm-12">
                  <input type="number" class="form-control" id="goal2" name="dka[goal2]" value="<?=intval($config['goal2'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div>
                                    <label for="rank" class="am-u-sm-12 am-form-label">积分排行榜显示数量</label>
                                    <div class="am-u-sm-12">
                  <input type="number" class="form-control" id="rank" name="dka[rank]" value="<?=intval($config['rank'])?>">
                                </div>
                                    </div>
                                </div>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-danger">保存</button>
                        </div>
                </div>
            </div>
            </form>
                </div>
                </div>
                </div>
                </div>
                </div>
                                <!-- <div class="am-tab-panel am-fade" id="tab4">
                                    <div id="wrapperD" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g">
                        <div class="tpl-form-body tpl-amazeui-form">
                            <form role="form" method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                        <div class="am-u-sm-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p>您确定将所有的用户积分清零？</p>
                            </div>
                        </div>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 仅清空用户积分，用户关系保留，请商户谨慎处理。</p>
                                <p>注意：积分清零后，兑换中心-总积分归零，粉丝数保留；我的积分之前的积分记录删除。</p>
                            </div>
                        </div>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                  <a class="am-btn am-btn-danger" id="delete" data-toggle="modal" data-target="#deleteModel">清空积分</a>
                        </div>
                </div>
                        </div>
                        </form>
                        </div>
                        </div>
                        </div>
                        </div>
                        </div> -->
                                <div class="am-tab-panel am-fade" id="tab5">
                                    <div id="wrapperE" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g">
                        <div class="tpl-form-body tpl-amazeui-form">
                            <form role="form" method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                        <div class="am-u-sm-12">
          <?php if ($result['ok6'] > 0):?>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p>保存成功,前往有赞后台可以管理标签</p>
                            </div>
                        </div>
          <?php endif?>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">标签名称(填写后不可修改)：</label>
                                    <div class="am-u-sm-12">
                <input class="name3 form-control" type="text" name='tag[tag_name]' value="<?=$config['tag_name']?>" <?php if($config['tag_name']) echo 'readonly=""'?>>
                                    </div>
                                </div>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-danger">保存</button>
                        </div>
                </div>
                        </div>
                        </form>
                        </div>
                        </div>
                        </div>
                        </div>
                        </div>
<?php if($bid==1):?>
                                <div class="am-tab-panel am-fade" id="tab6">
                                    <div id="wrapperF" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g">
                        <div class="tpl-form-body tpl-amazeui-form">
                            <form role="form" method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                        <div class="am-u-sm-12">
  <?php if ($result['ok7'] > 0):?>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p>保存成功</p>
                            </div>
                        </div>
                      <?php endif?>
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="actions">
                                <ul class="actions-btn">
                                    <li id="switch-on-3" class="green <?=$config['pstatus'] == 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off-3" class="red <?=$config['pstatus'] == 0 ? 'red-on' : ''?>">关闭</li>
                                    <input type="hidden" name="draw[pstatus]" id="pshow1" value="<?=$config['pstatus'] == 1 ? 1 : 0?>">
                                </ul>
                            </div>
                </div>
                        <div class="am-u-sm-12 switch-content-3 <?=$config['pstatus'] ==0 ? 'hide' : ''?>" style="padding:0;">
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">活动截止时间（为空则不限制）</label>
                                    <div class="am-u-sm-12">
          <input id="datetimepicker1" type="text" class="form-control pull-right formdatetime" name="draw[drawtime]" value="<?=$config['drawtime']?>" readonly="">
                                    </div>
                                </div>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p>奖品设置</p>
                            </div>
                        </div>
            <div class="am-from-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal0" class="am-form-label">一等奖</label>
          <select name="prize[0][iid]" data-am-selected="{searchBox: 1}">
            <?php foreach($result['items'] as $item):?>
              <option value="<?=$item->id?>" <?php foreach($result['prizes'] as $award){if($award->iid == $item->id && $award->type == 1) echo "selected=\"selected\"";}?>><?=$item->name?></option>
            <?php endforeach;?>
          </select>
          <input type="hidden" name='prize[1][type]' value="1">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal" class="am-form-label">中奖概率（%）：</label>
          <input type="text" name="prize[1][pro]" onkeyup="value=value.replace(/[^0-9.]/g,'')" <?php foreach($result['prizes'] as $award){if($award->type == 1) echo "value=\"$award->probability\"";}?>/>
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal2" class="am-form-label">库存：</label>
          <input type="number" name="prize[1][stock]" id="stock2" <?php foreach($result['prizes'] as $award){if($award->type == 2) echo "value=\"$award->stock\"";}?>/>
                                    </div>
                                </div>
                </div>
            </div>
            <div class="am-from-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal0" class="am-form-label">二等奖</label>
          <select name="prize[1][iid]" data-am-selected="{searchBox: 1}">
            <?php foreach($result['items'] as $item):?>
            <option value="<?=$item->id?>" <?php foreach($result['prizes'] as $award){if($award->iid == $item->id && $award->type == 2) echo "selected=\"selected\"";}?>><?=$item->name?></option>
            <?php endforeach;?>
          </select>
          <input type="hidden" name='prize[1][type]' value="2">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal" class="am-form-label">中奖概率（%）：</label>
          <input type="text" name="prize[1][pro]" onkeyup="value=value.replace(/[^0-9.]/g,'')" <?php foreach($result['prizes'] as $award){if($award->type == 2) echo "value=\"$award->probability\"";}?>/>
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal2" class="am-form-label">库存：</label>
          <input type="number" name="prize[1][stock]" id="stock2" <?php foreach($result['prizes'] as $award){if($award->type == 2) echo "value=\"$award->stock\"";}?>/>
                                    </div>
                                </div>
                </div>
            </div>

            <div class="am-from-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal0" class="am-form-label">三等奖</label>
          <select name="prize[2][iid]" data-am-selected="{searchBox: 1}">
            <?php foreach($result['items'] as $item):?>
            <option value="<?=$item->id?>" <?php foreach($result['prizes'] as $award){if($award->iid == $item->id && $award->type == 3) echo "selected=\"selected\"";}?>><?=$item->name?></option>
            <?php endforeach;?>
          </select>
          <input type="hidden" name='prize[2][type]' value="3">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal" class="am-form-label">中奖概率（%）：</label>
          <input type="text" name="prize[2][pro]" onkeyup="value=value.replace(/[^0-9.]/g,'')" <?php foreach($result['prizes'] as $award){if($award->type == 3) echo "value=\"$award->probability\"";}?>/>
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal2" class="am-form-label">库存：</label>
          <input type="number" name="prize[2][stock]" id="stock3" <?php foreach($result['prizes'] as $award){if($award->type == 3) echo "value=\"$award->stock\"";}?> />
                                    </div>
                                </div>
                </div>
            </div>
            <div class="am-from-group">
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal0" class="am-form-label">四等奖</label>
          <select name="prize[3][iid]" data-am-selected="{searchBox: 1}">
            <?php foreach($result['items'] as $item):?>
            <option value="<?=$item->id?>"  <?php foreach($result['prizes'] as $award){if($award->iid == $item->id && $award->type == 4) echo "selected=\"selected\"";}?>><?=$item->name?></option>
            <?php endforeach;?>
          </select>
          <input type="hidden" name='prize[3][type]' value="4">
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal" class="am-form-label">中奖概率（%）：</label>
          <input type="text" name="prize[3][pro]" onkeyup="value=value.replace(/[^0-9.]/g,'')" <?php foreach($result['prizes'] as $award){if($award->type == 4) echo "value=\"$award->probability\"";}?>/>
                                    </div>
                                </div>
                </div>
                <div class="am-u-sm-4">
                                <div class="am-form-group">
                                    <div>
                                    <label for="goal2" class="am-form-label">库存：</label>
          <input type="number" name="prize[3][stock]" id="stock4" <?php foreach($result['prizes'] as $award){if($award->type == 4) echo "value=\"$award->stock\"";}?> />
                                    </div>
                                </div>
                </div>
            </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">填写活动说明：</label>
                                    <div class="am-u-sm-12">
      <textarea class="form-control textarea2" rows="5" name='draw[drawexplain]'><?php if(!$config['drawexplain']) {echo '抽奖活动说明：
消耗积分即可获得抽奖机会。
';}else{echo htmlspecialchars($config['drawexplain']);}?></textarea>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">每人每天抽奖次数限制:(次)</label>
                                    <div class="am-u-sm-12">
        <input type="number" class="form-control" id="limitTime" name="draw[limitTime]" value="<?=floatval($config['limitTime'])?>" />
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">每次抽奖消耗的积分:(分)</label>
                                    <div class="am-u-sm-12">
      <input type="number" class="form-control" id="killScore" name="draw[killScore]" value="<?=floatval($config['killScore'])?>" />
                                    </div>
                                </div>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-danger">保存</button>
                        </div>
                </div>
                        </div>
                        </div>
                        </form>
                        </div>
                        </div>
                        </div>
                        </div>
                </div>
            <?php endif?>
                </div>
                </div>
                </div>
                </div>
<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>

        <?php

        if ($_POST['menu']||!$_POST) $active = '1';
        if ($_POST['text']) $active = '2';
        if ($_POST['dka']) $active = '3';
        if ($_POST['tag']) $active = '5';
        if ($_POST['draw']) $active = '6';
        ?>

        <script>
          $(function () {
            $('#tab<?=$active?>-bar,#tab<?=$active?>').addClass('am-active');
            $('#tab<?=$active?>').addClass('am-in');
          });
        </script>
    <script type="text/javascript">
    $('#datetimepicker1').datetimepicker({
  language:  'zh-CN',
  format: 'yyyy-mm-dd',
  startView: 'month',
  minView: 'month'
});
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
// document.querySelector('#delete').onclick = function(){
//     swal({
//         title: "您确认吗？",
//         text: "确认要清空用户积分吗？该操作不可恢复！",
//         type: "warning",
//         showCancelButton: true,
//         confirmButtonColor: '#DD6B55',
//         confirmButtonText: '确认清空',
//         cancelButtonText: '取消',
//         closeOnConfirm: false
//     },
//     function(){
//         window.location.href = "<?=URL::site("qwtdkaa/empty")?>?DELETE=1";
//     });
// };
</script>
<script type="text/javascript">
$('#switch-on-1').on('click', function() {
    $('#switch-on-1').addClass('green-on');
    $('#switch-off-1').removeClass('red-on');
    $('.switch-content-1').removeClass('hide');
    $('#show5').val(1);
})
$('#switch-off-1').on('click', function() {
    $('#switch-on-1').removeClass('green-on');
    $('#switch-off-1').addClass('red-on');
    $('.switch-content-1').addClass('hide');
    $('#show5').val(0);
})
$('#switch-on-2').on('click', function() {
    $('#switch-on-2').addClass('green-on');
    $('#switch-off-2').removeClass('red-on');
    $('.switch-content-2').removeClass('hide');
    $('#show6').val(1);
})
$('#switch-off-2').on('click', function() {
    $('#switch-on-2').removeClass('green-on');
    $('#switch-off-2').addClass('red-on');
    $('.switch-content-2').addClass('hide');
    $('#show6').val(0);
})
$('#switch-on-3').on('click', function() {
    $('#switch-on-3').addClass('green-on');
    $('#switch-off-3').removeClass('red-on');
    $('.switch-content-3').removeClass('hide');
    $('#pshow1').val(1);
})
$('#switch-off-3').on('click', function() {
    $('#switch-on-3').removeClass('green-on');
    $('#switch-off-3').addClass('red-on');
    $('.switch-content-3').addClass('hide');
    $('#pshow1').val(0);
})
  $(function() {
    $('#pic').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic').html(fileNames);
    });
  });
  $(function() {
    $('#pic2').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic2').html(fileNames);
    });
  });
  $(function() {
    $('#pic3').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic3').html(fileNames);
    });
  });
</script>
<?php if($hasover==1):?>
<script type="text/javascript">
  swal({
    title: "打卡宝已过期",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: "#DD6B55",
    confirmButtonText: "前往续费",
    cancelButtonText: "取消",
    closeOnConfirm: false,
    closeOnCancel: true,
      },
    function(isConfirm){
      if (isConfirm) {
           window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/product/13";
      }
  })
<?php if($result['err3']):?>
$(document).ready(function(){
    swal({
        title: "失败",
        text: "<?=$result['err3']?>",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "我知道了",
        closeOnConfirm: true,
    })
})
<?php endif?>
<?php if($result['err2']):?>
$(document).ready(function(){
    swal({
        title: "失败",
        text: "<?=$result['err2']?>",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "我知道了",
        closeOnConfirm: true,
    })
})
<?php endif?>
<?php if($result['err5']):?>
$(document).ready(function(){
    swal({
        title: "失败",
        text: "<?=$result['err5']?>",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "我知道了",
        closeOnConfirm: true,
    })
})
<?php endif?>
</script>
<?endif?>
