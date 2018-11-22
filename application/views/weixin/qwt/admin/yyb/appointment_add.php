
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
                <li>新建预约分组</li>
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
<?php if ($ok > 0):?>
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p> 保存成功！ </p>
                </div>
            </div>
          <?php endif?>
                                <div class="am-form-group">
                                    <label for="user-name" class="am-u-sm-12 am-form-label">预约分组名称</label>
                                    <div class="am-u-sm-12">
          <input type="text" maxlength="50" class="form-control" id="title" name="data[name]"  placeholder="请输入预约分组名称" value="">
                                    </div>
                                </div>
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


