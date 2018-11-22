<!-- 卡密配置 -->
<link rel="stylesheet" href="/qwt/assets/css/simditor.css">
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
    .am-badge{
        background-color: green;
    }
    #datetimepicker{
      width: 160px;
      text-align: center;
      margin-top: 5px;
    }
    .typebox{
        overflow: visible;
    }
    .simditor-icon{
      margin-top: 12px;
      display: block !important;
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
            <li><a href="#" class="am-icon-home">自动发卡工具</a></li>
            <li class="am-active"><?=$result['title']?></li>
        </ol>
        <div class="tpl-portlet-components" style="overflow:-webkit-paged-x;">
            <div class="portlet-title">
                <div class="caption font-green bold">
                    <?=$result['title']?>
                </div>
            </div>
            <div class="am-u-sm-12 am-u-md-12 tpl-amazeui-form">
                <div class="tpl-form-body tpl-form-line">
                    <form class="am-form  am-form-horizontal" method="post" enctype="multipart/form-data">
                           <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-12 am-form-label">卡密名称
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="text" class="form-control" name="kmi[km_content]" readonly="readonly" value="<?=$prize->km_content?>">
                                </div>
                            </div>
                             <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-12 am-form-label">卡密内容(1)
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="text" class="form-control" name="kmi[password1]" placeholder="输入卡密内容" value="<?=$_POST['kmi']['password1']?>">
                                </div>
                            </div>
                             <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-12 am-form-label">卡密内容(2)
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="text" class="form-control" name="kmi[password2]" placeholder="输入卡密内容" value="<?=$_POST['kmi']['password2']?>">
                                </div>
                            </div>
                             <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-12 am-form-label">卡密内容(3)
                                </label>
                                <div class="am-u-sm-12">
                                    <input type="text" class="form-control" name="kmi[password3]" placeholder="输入卡密内容" value="<?=$_POST['kmi']['password3']?>">
                                </div>
                            </div>
                        </div>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                                    <button type="submit" class="am-btn am-btn-success"><i class="fa fa-edit"></i>提交</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/qwt/assets/js/module.min.js"></script>
<script src="/qwt/assets/js/uploader.min.js"></script>
<script src="/qwt/assets/js/hotkeys.min.js"></script>
<script src="/qwt/assets/js/simditor.min.js"></script>
<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
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
</script>





