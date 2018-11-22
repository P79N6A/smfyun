
    <link rel="stylesheet" href="/qwt/assets/css/simditor.css">
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
    #datetimepicker{
      width: 160px;
      text-align: center;
      margin-top: 5px;
    }
    label{
        text-align: left !important;
    }
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                <?=$result['title']?>
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">预约宝</a></li>
                <li>新建模板消息群发</li>
                <li class="am-active"><?=$result['title']?></li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            <?=$result['title']?>
                        </div>
                </div>
                <div class="am-u-sm-12 am-u-md-12">
                        <div class="tpl-form-body tpl-amazeui-form">
                            <form class="am-form am-form-horizontal" method="post" enctype="multipart/form-data">
<?php if ($result['error']):?>
            <div class="tpl-content-scope">
                <div class="note note-danger">
                    <p> <?=$result['error']?> </p>
                </div>
            </div>
          <?php endif?>
<?php if ($ok > 0):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 保存成功！ </p>
                </div>
            </div>
          <?php endif?>

                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">模板消息标题</label>
                                    <div class="am-u-sm-12">
          <input type="text" maxlength="50" class="form-control" id="title" name="data[title]" value="<?=$order->title?>" placeholder="请输入模板消息标题" value="">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">模板消息详细内容</label>
                                    <div class="am-u-sm-12">
          <input type="text" maxlength="100" class="form-control" id="content" name="data[content]" placeholder="请输入模板消息详细内容" value="<?=$order->content?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">点击模板消息跳转到 </label>
                                    <div class="am-u-sm-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-1" onclick="change(1)" class="switch-type green <?=$order->type==1||!$order->type ? 'green-on' : ''?>">跳转到指定的活动链接地址</li>
                                    <li id="switch-2" onclick="change(2)" class="switch-type green <?=$order->type==2 ? 'green-on' : ''?>">有赞优惠券/优惠码</li>
                                    <li id="switch-3" onclick="change(3)" class="switch-type green <?=$order->type==3 ? 'green-on' : ''?>">有赞赠品</li>
                                    <li id="switch-4" onclick="change(4)" class="switch-type green <?=$order->type==4 ? 'green-on' : ''?>">跳转到微信小程序</li>
            <input type="hidden" id="jumpto" name="ordertype" value="<?=$order->type?$order->type:'1'?>" >
                        </ul>
                            </div>
                            </div>
                            </div>
                                <div class="am-form-group typebox" id="link" <?=($order->type==1||!$order->type)?'':'style="display:none"'?>>
                                    <label id="explain" for="user-name" class="am-u-sm-12 am-form-label">跳转到指定的活动链接地址</label>
                                    <div class="am-u-sm-12">
            <input type="text" maxlength="128" class="form-control url" id="url" name="data[url]" placeholder="请输入活动链接地址(可不填)" <?=$order->type==1||$action1==1?'value='.$order->url:'style="display:none"'?>>
                                    </div>
                                </div>
                                <div class="am-form-group typebox" id="xiaochengxu" <?=($order->type==4)?'':'style="display:none"'?>>
                                    <label id="explain" for="user-name" class="am-u-sm-12 am-form-label">小程序appid（该小程序appid必须与发模板消息的公众号是绑定关联关系）</label>
                                    <div class="am-u-sm-12">
            <input type="text" maxlength="50" class="form-control url" id="xcxappid" name="data[xcxappid]" placeholder="请输入小程序appid" <?=$order->type==4?'value='.$order->appid:''?>>
                                    </div>
                                    <label id="explain" for="user-name" class="am-u-sm-12 am-form-label">所需跳转到小程序的具体页面路径，支持带参数,（示例pages/index/index）</label>
                                    <div class="am-u-sm-12">
            <input type="text" maxlength="100" class="form-control url" id="xcxurl" name="data[xcxurl]" placeholder="请输入小程序的具体页面路径" <?=$order->type==4?'value='.$order->url:''?>>
                                    </div>
                                </div>
                                <div class="am-form-group typebox" id="yzcoupon" <?=($order->type==2)?'':'style="display:none"'?>>
                                    <label for="user-phone" class="am-u-sm-12 am-form-label">有赞优惠码/优惠码</label>
                                    <div class="am-u-sm-12">
                                        <select id="yzcode" name="yzcode" data-am-selected="{searchBox: 1}">
         <?php if($yzcoupons):?>
          <?php foreach ($yzcoupons as $yzcoupon):?>
          <option <?=$order->url==$yzcoupon['group_id']?"selected":""?> value="<?=$yzcoupon['group_id']?>"><?=$yzcoupon['title']?></option>
          <?php endforeach; ?>
           <?php endif;?>
          </select>
                                    </div>
                                </div>
                                <div class="am-form-group typebox" id="yzprize" <?=($order->type==3)?'':'style="display:none"'?>>
                                    <label for="user-phone" class="am-u-sm-12 am-form-label">有赞赠品</label>
                                    <div class="am-u-sm-12">
                                        <select id="yzgift" name="yzgift" data-am-selected="{searchBox: 1}">
         <?php if($yzgifts):?>
          <?php foreach ($yzgifts as $yzgift):?>
          <option <?=$order->url==$yzgift['present_id']?"selected":""?> value="<?=$yzgift['present_id']?>"><?=$yzgift['title']?></option>
         <?php endforeach; ?>
           <?php endif;?>
                                        </select>
                                    </div>
                                </div>
                                <?php if(Model::factory('select_experience')->dopinion($bid,'yyb')):?>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">发送用户</label>
                                    <div class="am-u-sm-12">
                            <div class="switch-box">
                                <ul class="actions-btn">
                                    <li id="switch-all" onclick="change1(1)" class="green switch-flag <?=$order->flag==0? 'green-on' : ''?>">全部</li>
                                    <li id="switch-log" onclick="change1(2)" class="green switch-flag <?=$order->flag == 1 ? 'green-on' : ''?>">已订阅</li>
                                    <input id="sendto" type="hidden" name="orderflag" id="sendtarget" value="<?=$order->flag?>">
                                </ul>
                            </div>
                            </div>
                          </div>
                          <div class="am-form-group typebox1" id="yyfz" <?=($order->flag==1)?'':'style="display:none"'?>>
                                    <label for="user-phone" class="am-u-sm-12 am-form-label">选择预约分组</label>
                                    <div class="am-u-sm-12">
                                        <select id="appointment" name="appointment" data-am-selected="{searchBox: 1}">
                             <?php if($appointments):?>
                              <?php foreach ($appointments as $appointment):?>
                              <option <?=$order->aid==$appointment->id?"selected":""?> value="<?=$appointment->id?>"><?=$appointment->name?></option>
                             <?php endforeach; ?>
                               <?php endif;?>
                                        </select>
                                    </div>
                                </div>
                              <?php else:?>

                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">发送用户</label>
                                    <div class="am-u-sm-12">
                            <div class="switch-box">
                                <ul class="actions-btn">
                                    <li id="switch-all" onclick="change1(1)" class="green switch-flag">全部</li>
                                    <li id="switch-log" onclick="change1(2)" class="green switch-flag green-on">已订阅</li>
                                    <input id="sendto" type="hidden" name="orderflag" id="sendtarget" value="1">
                                </ul>
                            </div>
                            </div>
                          </div>
                          <div class="am-form-group typebox1" id="yyfz">
                                    <label for="user-phone" class="am-u-sm-12 am-form-label">选择预约分组</label>
                                    <div class="am-u-sm-12">
                                        <select id="appointment" name="appointment" data-am-selected="{searchBox: 1}">
                             <?php if($appointments):?>
                              <?php foreach ($appointments as $appointment):?>
                              <option <?=$order->aid==$appointment->id?"selected":""?> value="<?=$appointment->id?>"><?=$appointment->name?></option>
                             <?php endforeach; ?>
                               <?php endif;?>
                                        </select>
                                    </div>
                                </div>
                              <?php endif?>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">发送方式</label>
                                    <div class="am-u-sm-12">
                            <div class="switch-box">
                                <ul class="actions-btn">
                                    <li id="switch-now" onclick="change2(1)" class="green switch-way <?=!isset($order->way)||$order->way == 1 ? 'green-on' : ''?>">立即发送</li>
                                    <li id="switch-time" onclick="change2(2)" class="green switch-way <?=isset($order->way)&&$order->way==0? 'green-on' : ''?>">指定时间发送</li>
                                    <input id="sendway" type="hidden" name="orderway" id="sendway" value="<?=!isset($order->way)||$order->way == 1?'1':'0'?>">
                                </ul>
                            </div>
                            </div>
                </div>
                                <div class="am-form-group typebox2" id="sendtime" <?=(isset($order->way)&&$order->way==0)?'':'style="display:none"'?>>
                                    <label for="user-name" class="am-u-sm-12 am-form-label">模板消息发送时间(立即发送可不设置)</label>
                                    <div class="datetimepickerbox am-u-sm-12">
  <input id="datetimepicker" size="16" type="text" name="data[expiretime]" size="16" value="<?=date("Y-m-d H:i:s",$order->time?$order->time:time())?>" class="am-form-field" readonly>
                </div>
                        </div>
                        <div class="am-u-sm-12" style="padding:0">
                        <hr>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-success"><i class="fa fa-edit"></i>保存</button>
                        </div>
                </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <script src="/qwt/assets/js/module.min.js"></script>
    <script src="/qwt/assets/js/uploader.min.js"></script>
    <script src="/qwt/assets/js/hotkeys.min.js"></script>
    <script src="/qwt/assets/js/simditor.min.js"></script>
<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
    <script type="text/javascript">
    $('#datetimepicker').datetimepicker({
      format: "yyyy-mm-dd hh:ii",
      language: "zh-CN",
      minView: "0",
      autoclose: true,
      pickerPosition:'top-right'
});
    </script>
<script>
    function change(i){
        $('.typebox').hide();
      if(i=='1'){
              $('.switch-type').removeClass('green-on');
              $('#switch-1').addClass('green-on');
        $('#link').show();
        $('#jumpto').val(1);
          }
      if(i=='2'){
              $('.switch-type').removeClass('green-on');
              $('#switch-2').addClass('green-on');
        $('#yzcoupon').show();
        $('#jumpto').val(2);
          }
      if(i=='3'){
              $('.switch-type').removeClass('green-on');
              $('#switch-3').addClass('green-on');
        $('#yzprize').show();
        $('#jumpto').val(3);
          }
      if(i=='4'){
              $('.switch-type').removeClass('green-on');
              $('#switch-4').addClass('green-on');
        $('#xiaochengxu').show();
        $('#jumpto').val(4);
          }
      }
      function change1(i){
        $('.typebox1').hide();
      if(i=='1'){
             $('.switch-flag').removeClass('green-on');
              $('#switch-all').addClass('green-on');
        $('#sendto').val(0);
          }
      if(i=='2'){
              $('.switch-flag').removeClass('green-on');
              $('#switch-log').addClass('green-on');
              $('#yyfz').show();
              $('#sendto').val(1);
          }
      }
      function change2(i){
        $('.typebox2').hide();
      if(i=='1'){
             $('.switch-way').removeClass('green-on');
              $('#switch-now').addClass('green-on');
              $('#sendway').val(1);
          }
      if(i=='2'){
              $('.switch-way').removeClass('green-on');
              $('#switch-time').addClass('green-on');
              $('#sendtime').show();
              $('#sendway').val(0);
          }
      }
    // $('#switch-log').click(function(){
    //   $('#switch-log').addClass('green-on');
    //   $('#switch-all').removeClass('green-on');
    //   $('#sendto').val(1);
    // })
    // $('#switch-all').click(function(){
    //   $('#switch-log').removeClass('green-on');
    //   $('#switch-all').addClass('green-on');
    //   $('#sendto').val(0);
    // })
    // $('#switch-now').click(function(){
    //   $('#switch-now').addClass('green-on');
    //   $('#switch-time').removeClass('green-on');
    //   $('#sendway').val(1);
    // })
    // $('#switch-time').click(function(){
    //   $('#switch-now').removeClass('green-on');
    //   $('#switch-time').addClass('green-on');
    //   $('#sendway').val(0);
    // })
</script>


