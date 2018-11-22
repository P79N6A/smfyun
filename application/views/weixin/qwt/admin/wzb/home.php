
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
    <style type="text/css">
    .am-selected-content{
        max-height: 180px;
        overflow: scroll;
    }
    .switch-content{
        overflow: hidden !important;
    }
    .am-badge{
        background-color: green;
    }
    label{
        text-align: left !important;
    }
    .hide{height: 0;}
    #datetimepicker1{
        display: inline-block;
    width: 250px;
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
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">


        <div class="tpl-content-wrapper">
            <div id="name1" class="tpl-content-page-title">
                个性化设置
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">神码云直播</a></li>
                <li>基础设置</li>
                <li id="name2" class="am-active">个性化设置</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <ul class="am-tabs-nav am-nav am-nav-tabs" style="left:0;">
                                <li id="tab1-bar" class="am-active"><a href="#tab1">个性化设置</a></li>
                                <li id="tab2-bar"><a href="#tab2">用户观看直播地址</a></li>
                                <li id="tab3-bar"><a href="#tab3">直播预告播放时间（可选功能）</a></li>
                            </ul>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
                            <form role="form" method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                                    <?php if ($result['ok3']>0):?>
                    <div class="tpl-content-scope">
                            <div class="note note-info" style="color:green">
                                <p> 个性化设置更新成功！</p>
                            </div>
                        </div>
                      <?php endif?>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">店铺主页地址（必填）</label>
                                    <div class="am-u-sm-12">
                  <input type="text" class="form-control" id="shop_url" name='text[shop_url]'  value="<?=$config['shop_url']?>">
                                    </div>
                                </div>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 直播界面自定义</p>
                            </div>
                        </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">直播标题自定义</label>
                                    <div class="am-u-sm-12">
                  <input type="text" class="form-control" id="title" name='text[title]'  value="<?=$config['title']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">直播昵称自定义</label>
                                    <div class="am-u-sm-12">
                  <input type="text" class="form-control" id="name" name='text[name]'  value="<?=$config['name']?$config['name']:$user->name?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">直播在线人数增加</label>
                                    <div class="am-u-sm-12">
                  <input type="number" class="form-control" id="num" name='text[num]'  value="<?=$config['num']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">自定义直播头像</label>
                                    <div class="am-u-sm-12">
                    <?php
                    //默认头像
                    if ($result['tplhead']):
                    ?>
                  <a href="/qwtwzba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwtwzba/images/cfg/<?=$result['tplhead']?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传自定义直播头像</button>
<div id="file-pic" style="display:inline-block;"></div>
                                            <input id="pic" type="file" name="pic2" accept="image/jpeg" multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格建议为 640*900px，最大不超过 100K。</small>

                                    </div>
                                </div>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 直播分享自定义</p>
                            </div>
                        </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">自定义分享图标（可选）</label>
                                    <div class="am-u-sm-12">
                    <?php
                    //默认头像
                    if ($result['tplshare']):
                    ?>
                  <a href="/qwtwzba/images/cfg/<?=$result['tplshare']?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwtwzba/images/cfg/<?=$result['tplshare']?>.v<?=time()?>.jpg" alt="" width=" 100"title="点击查看原图">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传自定义分享图标</button>
<div id="file-pic2" style="display:inline-block;"></div>
                                            <input id="pic2" type="file" name="pic3" accept="image/jpeg" multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格建议为 640*900px，最大不超过 100K。</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">直播分享到朋友圈标题自定义</label>
                                    <div class="am-u-sm-12">
                      <input type="text" class="form-control" id="title" name='text[wsptitle]'  value="<?=$config['wsptitle']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">直播分享给朋友标题自定义</label>
                                    <div class="am-u-sm-12">
                      <input type="text" class="form-control" id="title" name='text[wstitle]'  value="<?=$config['wstitle']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">直播分享给朋友内容自定义</label>
                                    <div class="am-u-sm-12">
                      <input type="text" class="form-control" id="title" name='text[wsdesc]'  value="<?=$config['wsdesc']?>">
                                    </div>
                                </div>
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 模板消息自定义</p>
                            </div>
                        </div>

                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">模板消息ID（开启直播后给已订阅用户发送模板消息，所在行业 IT科技 互联网|电子商务 ；模板消息标题：参与成功通知；模板编号：OPENTM407568708）</label>
                                    <div class="am-u-sm-12">
                      <input type="text" class="form-control" id="title" name='text[starttpl]'  value="<?=$config['starttpl']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">模板消息直播标题自定义</label>
                                    <div class="am-u-sm-12">
                      <input type="text" class="form-control" id="title" name='text[tpl_top]'  value="<?=$config['tpl_top']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">模板消息活动名称自定义</label>
                                    <div class="am-u-sm-12">
                      <input type="text" class="form-control" id="title" name='text[tpl_content]'  value="<?=$config['tpl_content']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">模板消息底部消息自定义</label>
                                    <div class="am-u-sm-12">
                      <input type="text" class="form-control" id="title" name='text[tpl_bottom]'  value="<?=$config['tpl_bottom']?>">
                                    </div>
                                </div>
                                <hr>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-danger">保存个性化配置</button>
                        </div>
                </div>
                </form>
                </div>
                </div>
                </div>
                </div>
                </div>

                                <div class="am-tab-panel am-fade" id="tab2">
                                    <div id="wrapperB" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g">
                        <div class="tpl-form-body tpl-amazeui-form">
                        <div class="am-form am-form-horizontal">
                        <div class="am-u-sm-12">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 用户观看直播地址 </p>
                </div>
            </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">用户观看直播地址</label>
                                    <div class="am-u-sm-12">
                      <input readonly="" type="text" class="form-control" id="menu2" value="<?='http://'.$_SERVER["HTTP_HOST"].'/smfyun/user_snsapi_userinfo/'.$_SESSION['qwta']['bid'].'/wzb/live'?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">用户订阅直播地址</label>
                                    <div class="am-u-sm-12">
                      <input readonly="" type="text" class="form-control" id="menu2" value="<?='http://'.$_SERVER["HTTP_HOST"].'/smfyun/user_snsapi_base/'.$_SESSION['qwta']['bid'].'/wzb/sub'?>">
                                    </div>
                                </div>
            </div>
                </div>
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
                            <form role="form" method="post" class="am-form am-form-horizontal" enctype='multipart/form-data'>
                        <div class="am-u-sm-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 未开播时，将开播时间显示在直播间页面上（可选功能） </p>
                            </div>
                        </div>
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="actions">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="<?=$config['timesiwtch'] >0 ? 'green-on' : 'green'?>">开启</li>
                                    <li id="switch-off" class="<?=$config['timesiwtch'] ==0 ? 'red-on' : 'red'?>">关闭</li>
                                    <input type="hidden" name="time[timesiwtch]" id="show1" value="<?=$config['timesiwtch']?>">
                                </ul>
                            </div>
                </div>
                        <div class="am-u-sm-12 switch-content <?=$config['timesiwtch'] ==0 ? 'hide' : ''?>" style="padding:0;">
                                <div class="am-form-group">
                                    <label for="menu" class="am-u-sm-12 am-form-label">请选中开播时间（精确到分）</label>
                                    <div class="am-u-sm-12">
  <input name="time[time]" value="<?=$config['time']?>" id="datetimepicker1" size="16" type="text" class="am-form-field" readonly>
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
                </div>
                </div>
                </div>
                </div>
                </div>
<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
    <script type="text/javascript">

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
    $('#datetimepicker1').datetimepicker({
  language:  'zh-CN',
  format: 'yyyy-mm-dd hh:ii',
  startView: 'month',
});
$('#tab1-bar').on('click', function() {
    $('#name1').text('个性化设置');
    $('#name2').text('个性化设置');
})
$('#tab2-bar').on('click', function() {
    $('#name1').text('用户观看直播地址');
    $('#name2').text('用户观看直播地址');
})
$('#tab3-bar').on('click', function() {
    $('#name1').text('直播预告播放时间（可选功能）');
    $('#name2').text('直播预告播放时间（可选功能）');})
$('#switch-on').on('click', function() {
    $('#switch-on').addClass('green-on');
    $('#switch-off').removeClass('red-on');
    $('.switch-content').removeClass('hide');
    $('#show1').val(1);
})
$('#switch-off').on('click', function() {
    $('#switch-on').removeClass('green-on');
    $('#switch-off').addClass('red-on');
    $('.switch-content').addClass('hide');
    $('#show1').val(0);
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
    </script>

