<style type="text/css">
    label{
        text-align: left !important;
    }
</style>
<div class="tpl-page-container tpl-page-header-fixed">
    <div class="tpl-content-wrapper">
        <div class="tpl-content-page-title">
            分销商等级设置
        </div>
        <ol class="am-breadcrumb">
            <li><a href="#" class="am-icon-home">全员分销</a></li>
            <li>分销商设置</li>
            <li>分销商等级管理</li>
            <li class="am-active"><?=$result['title']?></li>
        </ol>
        <div class="tpl-portlet-components" style="overflow:-webkit-paged-x;">
            <div class="portlet-title">
                <div class="caption font-green bold">
                    <?=$result['title']?>
                </div>
            </div>
            <div class="am-u-sm-12 am-u-md-12">
                <div class="tpl-form-body tpl-amazeui-form">
                    <form class="am-form am-form-horizontal" method="post" enctype="multipart/form-data" onsubmit="return check(this.form)">
                        <div class="row">

                            <div class="am-form-group">
                                <label for="pri" class="am-u-sm-12 am-form-label">分销商等级
                                    <span class="tpl-form-line-small-title">Level</span>
                                </label>
                                <div class="am-u-sm-12">
          <input type="number" class="form-control" id="pri" name="data[lv]" placeholder="" value="<?=intval($_POST['data']['lv'])?>">
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-12 am-form-label">分销商对应名称
                                    <span class="tpl-form-line-small-title">Name</span>
                                </label>
                                <div class="am-u-sm-12">
          <input type="text" class="form-control" id="pri" name="data[name]" placeholder="" value="<?=$_POST['data']['name']?>" >
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-12 am-form-label">达到多少金额自动到达该级别（单位：元）
                                    <span class="tpl-form-line-small-title">Cost</span>
                                </label>
                                <div class="am-u-sm-12">
          <input type="number" class="form-control" id="pri" name="data[money]" placeholder="" value="<?=intval($_POST['data']['money'])?>" min='1'>
                                </div>
                            </div>
                            <div class="am-form-group">
                                <label for="user-name" class="am-u-sm-12 am-form-label">该级别下的返还比例（%）
                                    <span class="tpl-form-line-small-title">Rate</span>
                                </label>
                                <div class="am-u-sm-12">
          <input type="number" class="form-control" id="pri" name="data[scale]" placeholder="" value="<?=intval($_POST['data']['scale'])?>" min='1' max='99'>
                                </div>
                            </div>
                        </div>
                        <div class="am-u-sm-12" style="padding:0">
                                <hr>
                            <div class="am-form-group">
                                <div class="am-u-sm-9 am-u-sm-push-3">
                                    <button type="submit" class="am-btn am-btn-success"><i class="fa fa-edit"></i>保存</button>
                                    <?php if ($result['action'] == 'edit'):?>
                                    <a class="am-btn am-btn-danger" id="delete">
                                        <i class="fa fa-remove"></i>
                                        <span>删除该等级</span>
                                    </a>
                                    <?php endif?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $('#delete').click(function(){
  swal({
    title: "确认要删除吗？",
    text: "该操作不可恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认删除',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwtqfxa/skus/edit/<?=$result['sku']['id']?>?DELETE=1";
    })
  })

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
