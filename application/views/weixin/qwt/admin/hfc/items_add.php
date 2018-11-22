
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
    label{
        text-align: left !important;
    }
    .content{
    padding: 10px;
    border-radius: 10px;
    border: 2px solid rgba(129,180,0,.5);
}
    .am-badge{
        background-color: green;
    }
.title{
    font-size: 16px;
    line-height: 16px;
    color: orange;
    margin-top: -18px;
    background: #fff;
    padding: 0 5px;
    width: 60px;
    text-align: center;
}
    #datetimepicker{
      width: 160px;
      text-align: center;
      margin-top: 5px;
    }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                <?=$result['action']?>
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">充值拼团</a></li>
                <li>商品管理</li>
                <li class="am-active"><?=$result['action']?></li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span><?=$result['action']?></span>
                            </div>
                        </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-active am-fade am-in" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block" style="overflow:-webkit-paged-x">

                    <div class="am-g tpl-amazeui-form">
                        <div class="tpl-form-body">
                            <form method="post" class="am-form am-form-horizontal" enctype='multipart/form-data' onsubmit="return toValid()">
                                    <?php if ($result['err3']):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p><span class="label label-danger">注意:</span> <?=$result['err3']?> </p>
                </div>
            </div>
          <?php endif?>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">商品名称</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input titleinput" id="goal2" name="form[name]" value="<?=$item->name?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">商品优先级</label>
                                    <div class="am-u-sm-12">
                    <input type="number" class="tpl-form-input titleinput" id="goal2" name="form[pri]" value="<?=$item->pri?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">商品图片</label>
                                    <div class="am-u-sm-12">
                <?php
                if ($item->id):
                  ?>
                  <a href="/qwthfca/images/item/<?=$item->id?>.v<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="/qwthfca/images/item/<?=$item->id?>.v<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                          <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传商品图片</button>
                                        <div id="file-pic" style="display:inline-block;"></div>
                                            <input id="pic" type="file" name="pic" accept="image/jpeg" multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，最大不超过 400K。</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">商品原价</label>
                                    <div class="am-u-sm-12">
                    <input type="number" step="0.01" class="tpl-form-input titleinput" id="goal2" name="form[old_price]" value="<?=$item->old_price?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">商品团购价</label>
                                    <div class="am-u-sm-12">
                    <input type="number" step="0.01" class="tpl-form-input titleinput" id="goal2" name="form[price]" value="<?=$item->price?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">商品描述</label>
                                    <div class="am-u-sm-12 content-box">
                                    <?php if ($result['action']=='添加新商品'):?>
            <div class="am-form-group content">
            <div class="title">第1张</div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">描述图1</label>
                                    <div class="am-u-sm-12">
                                       <!--  <a href="http://<?=$_SERVER['HTTP_HOST']?>/qwthfca/images/desc/<?=$v->id?>?<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="http://<?=$_SERVER['HTTP_HOST']?>/qwthfca/images/desc/<?=$v->id?>?<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a> -->
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传描述图</button>
<div class="picname" id="file-pic1" style="display:inline-block;"></div>
                                            <input class="picinput" id="pic1" type="file" name="pic1" accept="image/jpeg" multiple>
                                        </div>
                                        <small>只能为 JPEG 格式，最大不超过 400K。</small>
                                    </div>
                                </div>
                                <input type="hidden" name="pri[1]" value="1">
                                </div>
                            <?php else:?>
                                <?php foreach ($imgs as $k => $v):?>
            <div class="am-form-group content">
            <div class="title">第<?=$v->pri?>张</div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">描述图<?=$v->pri?></label>
                                    <div class="am-u-sm-12">
                                        <a href="http://<?=$_SERVER['HTTP_HOST']?>/qwthfca/images/desc/<?=$v->id?>?<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="http://<?=$_SERVER['HTTP_HOST']?>/qwthfca/images/desc/<?=$v->id?>?<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传描述图</button>
<div class="picname" id="file-pic<?=$v->pri?>" style="display:inline-block;"></div>
                                            <input class="picinput" id="pic<?=$v->pri?>" type="file" name="pic<?=$v->pri?>" accept="image/jpeg" multiple>
                                        </div>
                                        <small>只能为 JPEG 格式，最大不超过 400K。</small>
                                    </div>
                                </div>
                                <input type="hidden" name="pri[<?=$v->pri?>]" value="<?=$v->pri?>">
                                </div>
                            <?php endforeach?>
                        <?php endif?>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="button" class="add am-btn am-btn-primary tpl-btn-bg-color-success ">添加一张描述图片</button>
                                        <button type="button" class="delete am-btn am-btn-primary tpl-btn-bg-color-success ">删除最后一张描述图片</button>
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">几人成团</label>
                                    <div class="am-u-sm-12">
                    <input type="number" class="tpl-form-input titleinput" id="goal2" name="form[groupnum]" value="<?=$item->groupnum?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">团购时间设置</label>
                        <div class="am-u-sm-12 am-u-md-12">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-on" class="green <?=$item->timeouttype?'':'green-on'?>">设置固定截止时间</li>
                                    <li id="switch-off" class="red <?=$item->timeouttype?'red-on':''?>">设置团购发起后有效天数</li>
                        <input type="hidden" name="form[timeouttype]" id="show0" value="<?=$item->timeouttype?1:0?>">
                                </ul>
                            </div>
                </div>
                </div>
                                <div class="am-form-group limittime" <?=$item->timeouttype?"style='display:none'":""?> >
                                    <label for="user-name" class="am-u-sm-12 am-form-label">设置截止时间</label>
                                    <div class="datetimepickerbox am-u-sm-12">
  <input id="datetimepicker" size="16" type="text" name="form[timeout0]" size="16" value="<?=$item->timeout?date('Y-m-d H:i:s',$item->timeout):''?>" readonly="" class="am-form-field" readonly>
                </div>
                                </div>
                                <div class="am-form-group limitday" <?=$item->timeouttype?"":"style='display:none'"?>>
                                    <label for="goal2" class="am-u-sm-12 am-form-label">设置有效天数</label>
                                    <div class="am-u-sm-12">
                    <input type="number" class="tpl-form-input" id="goal2" name="form[timeout1]" value="<?=$item->timeout/86400?>">
                                    </div>
                                </div>
                                <hr>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success "><?=$result['action']?></button>
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



        </div>

<script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.js"></script>
<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
<script type="text/javascript" src="https://unpkg.com/wangeditor@3.1.0/release/wangEditor.min.js"></script>
<script type="text/javascript">
function toValid () {
    var pass = 1;
    $('.titleinput').each(function(){
        if ($(this).val()=='') {
            pass = 0;
        }
    });
    if (pass==0) {
        alert('请填写完整');
        return false;
    }else{
        return true;
    }
}
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
    $('.picinput').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $(this).parent().children('.picname').html(fileNames);
    });
  });
  $('#switch-on').click(function(){
    $('#switch-on').addClass('green-on');
    $('#switch-off').removeClass('red-on');
    $('#show0').val(0);
    $('.limittime').show();
    $('.limitday').hide();
  })
  $('#switch-off').click(function(){
    $('#switch-on').removeClass('green-on');
    $('#switch-off').addClass('red-on');
    $('#show0').val(1);
    $('.limittime').hide();
    $('.limitday').show();
  })
    $('#datetimepicker').datetimepicker({
  format: 'yyyy-mm-dd hh:ii'
});
    function check(){
        var i = $('.content-box').children().length;
            $('.add').attr('disabled',false);
            $('.delete').attr('disabled',false);
        if (i==1) {
            $('.delete').attr('disabled',true);
        }else{
        };
    }
        $('.add').click(function(){
        var i = $('.content-box').children().length;
        var a = i+1;
            $('.content-box').append(
                '<div class="am-form-group content">'+
            '<div class="title">第'+a+'张</div>'+
                                '<div class="am-form-group">'+
                                    '<label for="pic" class="am-u-sm-12 am-form-label">描述图'+a+'</label>'+
                                    '<div class="am-u-sm-12">'+
                                        '<div class="am-form-group am-form-file">'+
                                            '<button type="button" class="am-btn am-btn-danger am-btn-sm"><i class="am-icon-cloud-upload"></i> 上传描述图</button>'+
                                            '<div class="picname" id="file-pic'+a+'" style="display:inline-block;margin-left:5px;"></div>'+
                                            '<input class="picinput" type="file" id="pic'+a+'" name="pic'+a+'" accept="image/jpeg" multiple>'+
                                        '</div>'+
                                        '<small>只能为 JPEG 格式，最大不超过 400K。</small>'+
                                        '</div>'+
                                '</div>'+
                                '<input type="hidden" name="pri['+a+']" value="'+a+'">'+
                                '</div>'
            );
            check();
  $(function() {
    $('.picinput').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $(this).parent().children('.picname').html(fileNames);
    });
  });
        });
        $('.delete').click(function(){
            $('.content-box').children('.content:last').remove();
            check();
        })
</script>
