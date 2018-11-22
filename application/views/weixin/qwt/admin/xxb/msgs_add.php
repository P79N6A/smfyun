
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
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                发送内容设置
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">消息宝</a></li>
                <li>发送内容设置</li>
                <li class="am-active">发送内容编辑</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>发送内容设置</span>
                            </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-active am-fade am-in" id="tab1">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block">

                    <div class="am-g tpl-amazeui-form">
                        <div class="tpl-form-body">
                            <form method="post" class="am-form am-form-horizontal" enctype='multipart/form-data' onsubmit="return toValid()">
                                    <?php if ($result['ok3'] > 0):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 发送内容更新成功! </p>
                </div>
            </div>
          <?php endif?>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">选择下发消息类型</label>
                        <div class="am-u-sm-12 am-u-md-12" style="float:left">
                            <div class="actions" style="float:left">
                                <ul class="actions-btn">
                                    <li id="switch-off" class="red <?=$item->type == 1 ? '' : 'red-on'?>">图文消息</li>
                                    <li id="switch-on" class="green <?=$item->type == 1 ? 'green-on' : ''?>">小程序卡片</li>
                        <input type="hidden" name="form[type]" id="show0" value="<?=$item->type?>">
                                </ul>
                            </div>
                </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">发送内容名称</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input titleinput" id="goal2" name="form[title]" value="<?=$item->name?>">
                                    </div>
                                </div>
                                <div class="typebox typexcx" <?=$item->type == 1 ? '' : 'style="display:none"'?>>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">小程序卡片标题</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input xcxinput" name="xcx[title]" value="<?=$item->title?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="ticker_lifetime" class="am-u-sm-12 am-form-label">小程序AppID</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input xcxinput" name="xcx[appid]" value="<?=$item->appid?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">点击小程序卡片跳转页面路径（选填，如不填写则默认跳转至首页）</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" name="xcx[path]" value="<?=$item->path?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">小程序卡片预览图</label>
                                    <div class="am-u-sm-12">
                                    <?php if ($item->id):?>
                                        <a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxxba/images/item/<?=$item->id?>?<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="http://<?=$_SERVER['HTTP_HOST']?>/qwtxxba/images/item/<?=$item->id?>?<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                        <?php endif?>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传预览图</button>
<div id="file-pic" style="display:inline-block;"></div>
                                            <input id="pic" type="file" name="picxcx" accept="image/jpeg" multiple>

                                        </div>
                                        <small>最大300KB，建议为JPG格式，高宽比为高:宽=4:5，建议尺寸为692px*552px</small>

                                    </div>
                                </div>
                                </div>
                                <div class="typebox typemsg" <?=$item->type == 1 ? 'style="display:none"' : ''?>>
            <div class="content-box">
          <?php if ($result['action'] == 'add'):?>
            <div class="am-form-group content" style="border:0;padding:0;">
                <div class="tpl-content-scope">
                    <div class="note note-info">
                        <p> 消息封面设置</p>
                    </div>
                </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">封面图</label>
                                    <div class="am-u-sm-12">
                                        <div class="am-form-group am-form-file">
                  <!-- <a href="'http://'.$_SERVER['HTTP_HOST'].'/qwtxxba/images/msg/'.$v->id.'?'.time().'.jpg'" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="'http://'.$_SERVER['HTTP_HOST'].'/qwtxxba/images/msg/'.$v->id.'?'.time().'.jpg'" alt="" title="点击查看原图">
                                            </div>
                                            </a> -->
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传封面图片</button>
<div id="file-pic0" style="display:inline-block;"></div>
                                            <input id="pic0" type="file" name="pic0" accept="image/jpeg" multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格建议为 900*500px，最大不超过 400K</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">封面标题</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" id="goal2" name="text[0][title]">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">封面跳转链接</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" id="goal2" name="text[0][url]">
                                    </div>
                                </div>
                                <input type="hidden" name="text[0][mid]" value="">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 次级消息设置（可选项，最多7个）</p>
                </div>
            </div>
            </div>
                            <?php else:?>
                                <?php foreach ($msgs as $k => $v):?>
                                <?php if ($k==0):?>
            <div class="am-form-group content" style="border:0;padding:0;">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 消息封面设置</p>
                </div>
            </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">封面图</label>
                                    <div class="am-u-sm-12">
                                        <a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxxba/images/msg/<?=$v->id?>?<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="http://<?=$_SERVER['HTTP_HOST']?>/qwtxxba/images/msg/<?=$v->id?>?<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传封面图片</button>
<div id="file-pic0" style="display:inline-block;"></div>
                                            <input id="pic0" type="file" name="pic<?=$k?>" accept="image/jpeg" multiple>

                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格建议为 900*500px，最大不超过 400K</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">封面标题</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input msginput" name="text[<?=$k?>][title]" value="<?=$v->title?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">封面跳转链接</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input msginput" name="text[<?=$k?>][url]" value="<?=$v->url?>">
                                    </div>
                                </div>
                                <input type="hidden" name="text[<?=$k?>][mid]" value="<?=$v->id?>">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 次级消息设置（可选项，最多7个）</p>
                </div>
            </div>
            </div>
        <?php else:?>
            <div class="am-form-group content">
            <div class="title">第<?=$k?>栏</div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">预览图<?=$k?></label>
                                    <div class="am-u-sm-12">
                                        <a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtxxba/images/msg/<?=$v->id?>?<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="http://<?=$_SERVER['HTTP_HOST']?>/qwtxxba/images/msg/<?=$v->id?>?<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传预览图</button>
<div id="file-pic<?=$k?>" style="display:inline-block;"></div>
                                            <input id="pic<?=$k?>" type="file" name="pic<?=$k?>" accept="image/jpeg" multiple>
                                        </div>
                                        <small>
                                        只能为 JPEG 格式，规格建议为 200*200px，最大不超过 400K。</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">标题<?=$k?></label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input msginput" id="goal2" name="text[<?=$k?>][title]" value="<?=$v->title?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">跳转链接<?=$k?></label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input msginput" id="goal2" name="text[<?=$k?>][url]" value="<?=$v->url?>">
                                    </div>
                                </div>
                                <input type="hidden" name="text[<?=$k?>][mid]" value="<?=$v->id?>">
                                </div>
                            <?php endif?>
                        <?php endforeach?>
                    <?php endif?>
                                </div>

                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="button" class="add am-btn am-btn-primary tpl-btn-bg-color-success ">添加一栏</button>
                                        <button type="button" class="delete am-btn am-btn-primary tpl-btn-bg-color-success ">删除最后一栏</button>
                                    </div>
                                </div>
                                </div>
                                <hr>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">保存消息设置</button>
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
    function toValid(){
        var flag = 0;
        var type = $('#show0').val();
        console.log(type);
        if (type==1) {
            console.log('2');
            $(".xcxinput").each(function(){
            　　if($(this).val() == "") {
                flag = 1;
                };
            });
        }else{
            console.log('1');
            $(".msginput").each(function(){
            　　if($(this).val() == "") {
                flag = 1;
                };
            });
        }
        if ($('.titleinput').val()=='') {
            flag = 1;
        };
        if(flag==1){
            alert('请填写完整');
            return false;
        }else{
            return true;
            // return false;
        }
    }
    $(document).on('change','input',function(){
        check();
    })
    function check(){
        var i = $('.content-box').children().length;
            $('.add').attr('disabled',false);
            $('.delete').attr('disabled',false);
        if (i==1) {
            $('.delete').attr('disabled',true);
        }else{
        };
        if (i==8) {
            $('.add').attr('disabled',true);
        }else{
        };
        $(".msginput").each(function(){
        　　if($(this).val() == "") {
            console.log(1);
            $('.add').attr('disabled',true);
        　　}else{
            console.log(2);
        }
        });
        console.log(0);
    }
    $(document).ready(function(){
        check();
    });
        $('.add').click(function(){
        var i = $('.content-box').children().length;
        var a = i;
            $('.content-box').append(
                '<div class="am-form-group content">'+
            '<div class="title">第'+a+'栏</div>'+
                                '<div class="am-form-group">'+
                                    '<label for="pic" class="am-u-sm-12 am-form-label">预览图'+a+'</label>'+
                                    '<div class="am-u-sm-12">'+
                                        '<div class="am-form-group am-form-file">'+
                                            '<button type="button" class="am-btn am-btn-danger am-btn-sm"><i class="am-icon-cloud-upload"></i> 上传预览图</button>'+
                                            '<div id="file-pic'+a+'" style="display:inline-block;"></div>'+
                                            '<input type="file" id="pic'+a+'" name="pic'+a+'" accept="image/jpeg" multiple>'+
                                        '</div>'+
                                        '<small>只能为 JPEG 格式，规格建议为 200*200px，最大不超过 400K。</small>'+
                                        '</div>'+
                                '</div>'+
                                '<div class="am-form-group">'+
                                    '<label for="goal2" class="am-u-sm-12 am-form-label">标题'+a+'</label>'+
                                    '<div class="am-u-sm-12">'+
                    '<input type="text" class="tpl-form-input msginput" name="text['+a+'][title]">'+
                                    '</div>'+
                                '</div>'+
                                '<div class="am-form-group">'+
                                    '<label for="goal2" class="am-u-sm-12 am-form-label">跳转链接'+a+'</label>'+
                                    '<div class="am-u-sm-12">'+
                    '<input type="text" class="tpl-form-input msginput" name="text['+a+'][url]">'+
                                    '</div>'+
                                '</div>'+
                                '<input type="hidden" name="text['+a+'][mid]" value="">'+
                                '</div>'
            );
            check();
        });
        $('.delete').click(function(){
            $('.content-box').children('.content:last').remove();
            check();
        })
    $('body').on('change', '#pic', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic').html(fileNames);
    });
    $('body').on('change', '#pic0', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic0').html(fileNames);
    });
    $('body').on('change', '#pic1', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic1').html(fileNames);
    });
    $('body').on('change', '#pic2', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic2').html(fileNames);
    });
    $('body').on('change', '#pic3', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic3').html(fileNames);
    });
    $('body').on('change', '#pic4', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic4').html(fileNames);
    });
    $('body').on('change', '#pic5', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic5').html(fileNames);
    });
    $('body').on('change', '#pic6', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic6').html(fileNames);
    });
    $('body').on('change', '#pic7', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic7').html(fileNames);
    });
    $('#switch-on').click(function(){
        $('#switch-on').addClass('green-on');
        $('#switch-off').removeClass('red-on');
        $('.typebox').hide();
        $('.typexcx').show();
        $('#show0').val(1);
    })
    $('#switch-off').click(function(){
        $('#switch-on').removeClass('green-on');
        $('#switch-off').addClass('red-on');
        $('.typebox').hide();
        $('.typemsg').show();
        $('#show0').val(0);
    })
</script>
