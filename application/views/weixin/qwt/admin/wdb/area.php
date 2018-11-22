

    <style type="text/css">
    .am-selected-content{
        max-height: 180px;
        overflow: scroll;
    }
    .switch-content{
        overflow: hidden !important;
    }
    .hide{height: 0;}
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">


        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                选择可参与地区
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">积分宝订阅号版</a></li>
                <li>可选功能</li>
                <li class="am-active">选择可参与地区</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="caption font-green bold">
                            选择可参与地区
                        </div>
                </div>
                <?php if ($result['ok5'] > 0):?>
                <div class="am-u-sm-12 am-u-md-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 保存成功！</p>
                            </div>
                        </div>
                </div>
                <?php endif?>
                <form role="form" method="post" onsubmit="return toVaild()">
                <div class="am-u-sm-12 am-u-md-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 是否开启本功能？</p>
                            </div>
                        </div>
                </div>
                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="actions">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$config['status'] == 1 ? 'green-on' : ''?>">开启</li>
                                    <li id="switch-off" class="red <?=$config['status'] == 1 ? '' : 'red-on'?>">关闭</li>
                        <input type="hidden" name="area[status]" id="show0" value="<?=$config['status']?>">
                                </ul>
                            </div>
                </div>
                        <div class="am-u-sm-12 switch-content <?=$config['status'] == 1 ? '' : 'hide'?>" style="padding:0;">
                    <hr>
                        <div class="am-u-sm-12">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 请选择可参与活动的地区：</p>
                            </div>
                        </div>
                </div>
                        <div class="am-u-sm-12">
                <div class="am-form-group">
                            <div class="am-btn-toolbar">
                                <div class="am-btn-group am-btn-group-xs">
                                    <button type="button" class="am-btn am-btn-default am-btn-success add"><span class="am-icon-plus"></span> 新增</button>
                                    <button type="button" class="am-btn am-btn-default am-btn-danger reduce"><span class="am-icon-trash-o"></span> 删除</button>
                                </div>
                            </div>
                </div>
                </div>
                <div class="am-u-sm-12" id="area">
                      <?php if ($config['count']){
                        $num = $config['count'];
                        for ($i=1; $i <=$num ; $i++) {
                         echo '
                        <div class=\'am-u-sm-12 loc\' id=\'city'.$i.'\'>
                <div class=\'am-form-group\'>
                          <select class=\'prov\' name=\'area[pro'.$i.']\'></select>
                          <select class=\'city\' name=\'area[city'.$i.']\' disabled="disabled"></select>
                          <select class=\'dist\' name=\'area[dis'.$i.']\' disabled="disabled"></select>
                        </div>
                        </div>
                        ';
                      }
                    }
                    ?>
                    <?php if (!$config['count']):?>
                        <div class="am-u-sm-12 loc" id="city1">
                <div class="am-form-group">
                          <select class="prov" name="area[pro1]"></select>
                          <select class="city" name="area[city1]" disabled="disabled"></select>
                          <select class="dist" name="area[dis1]" disabled="disabled"></select>
                        </div>
                        </div>
                    <?php endif?>
                    </div>
                  <input id='count' name="area[count]" style="display:none" value='<?=$config['count']?>'>
                        <div class="am-u-sm-12">
                <div class="am-form-group">
                    <div class="tpl-content-scope">
                            <div class="note note-info">
                                <p> 对不符合活动地区的用户提示文案：</p>
                            </div>
                        </div>
                </div>
                </div>
                        <div class="am-u-sm-12">
                <div class="am-form-group">
                    <input name='area[reply]' type="text" style="width:100%;height:40px;font-size: 14px; line-height: 18px; border: 1px solid #dddddd;" value='<?php if($config['reply']){echo $config['reply'];}else{echo '不好意思，您不在本次活动的参与地区，不要灰心哦，请继续关注我们的公众号，有更多惊喜等着你呢！';}?>'>
                </div>
                </div>
                </div>
                <script src="http://cdn.jfb.smfyun.com/wdy/plugins/citySelect/jquery.cityselect.js"></script>
                <script src="/wdy/plugins/citySelect/city.min1.js"></script>
                <script type="text/javascript">

<?php if($result['error']):?>
$(document).ready(function(){
    swal({
        title: "失败",
        text: "<?=$result['error']?>",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "我知道了",
        closeOnConfirm: true,
    })
})
<?php endif?>

                  function toVaild(){
                    var type = $('#show0').val();
                    if (type==1) {
                        num = parseInt($('.prov').length);
                        $('#count').val(num);
                        var isn = $('.prov:last').val();
                        if(!isn==''||$('.guanbi').is(':checked')){
                          return true;
                        }else{
                          alert('请至少填写省');
                          return false;
                        }
                    }else{
                        return true;
                    }
                  }
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
                    $("#area").append(
'<div class=\'am-u-sm-12 loc\' id=\'city'+num+'\'>'+
                '<div class=\'am-form-group\'>'+
                          '<select class=\'prov\' name=\'area[pro'+num+']\'></select>'+
                          '<select class=\'city\' name=\'area[city'+num+']\' disabled="disabled"></select>'+
                          '<select class=\'dist\' name=\'area[dis'+num+']\' disabled="disabled"></select>'+
                        '</div>'+
                        '</div>');
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
                        <div class="am-u-sm-12" style="padding:0">
                        <hr>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-danger">保存设置</button>
                        </div>
                </div>
                </div>
                </form>
            </div>
        </div>

    </div>

    <script type="text/javascript">

$('#switch-on').on('click', function() {
    $('#switch-on').addClass('green-on');
    $('#switch-off').removeClass('red-on');
    $('.switch-content').removeClass('hide');
    $('#show0').val(1);
})
$('#switch-off').on('click', function() {
    $('#switch-on').removeClass('green-on');
    $('#switch-off').addClass('red-on');
    $('.switch-content').addClass('hide');
    $('#show0').val(0);
})
    </script>




