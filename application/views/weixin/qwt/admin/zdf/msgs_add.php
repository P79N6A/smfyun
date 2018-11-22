
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
                <?=$result['action']?>
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">公众号自动下发小程序卡片工具</a></li>
                <li>小程序卡片管理</li>
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
                <div class="tpl-block">

                    <div class="am-g tpl-amazeui-form">
                        <div class="tpl-form-body">
                            <form method="post" class="am-form am-form-horizontal" enctype='multipart/form-data' onsubmit="return toValid()">
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">小程序名称</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" id="goal2" name="text[name]" value="<?=$msg->name?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">小程序卡片标题</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" name="text[title]" value="<?=$msg->title?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="ticker_lifetime" class="am-u-sm-12 am-form-label">小程序AppID</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="tpl-form-input" name="text[appid]" value="<?=$msg->appid?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">点击小程序卡片跳转页面路径（选填，如不填写则默认跳转至首页）</label>
                                    <div class="am-u-sm-12">
                    <input type="text" class="tpl-form-input" name="text[path]" value="<?=$msg->path?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="pic" class="am-u-sm-12 am-form-label">小程序卡片预览图<?=$result['action']=='修改小程序'?'（不上传则不修改）':'（不上传预览图将无法正常使用）'?></label>
                                    <div class="am-u-sm-12">
                                        <a href="http://<?=$_SERVER['HTTP_HOST']?>/qwtzdfa/images/msg/<?=$v->id?>?<?=time()?>.jpg" target="_blank">
                                            <div class="tpl-form-file-img">
                                                <img src="http://<?=$_SERVER['HTTP_HOST']?>/qwtzdfa/images/msg/<?=$msg->id?>?<?=time()?>.jpg" alt="" title="点击查看原图">
                                            </div>
                                            </a>
                                        <div class="am-form-group am-form-file">
                                            <button type="button" class="am-btn am-btn-danger am-btn-sm">
    <i class="am-icon-cloud-upload"></i> 上传预览图</button>
<div id="file-pic0" style="display:inline-block;"></div>
                                            <input id="pic0" type="file" name="pic" accept="image/jpeg" multiple>

                                        </div>
                                        <small>最大300KB，建议为JPG格式，高宽比为高:宽=4:5，建议尺寸为692px*552px</small>

                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary tpl-btn-bg-color-success ">保存</button>
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
    function toValid(){
        var flag = 0;
        $(":text").each(function(){
        　　if($(this).val() == "") {
            flag = flag + 1;
            };
        });
        if(flag > 1){
            alert('除跳转页路径外均为必填，请填写完整！');
            return false;
        }else{
            return true;
        }
    }
  $(function() {
    $('#pic0').on('change', function() {
      var fileNames = '';
      $.each(this.files, function() {
        fileNames += '<span class="am-badge">' + this.name + ' √ </span> ';
      });
      $('#file-pic0').html(fileNames);
    });
  });
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
</script>
