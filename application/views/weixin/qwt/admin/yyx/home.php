

    <style type="text/css">
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
    .hide{height: 0;}
    </style>
    <div class="tpl-page-container tpl-page-header-fixed">


        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                数据自定义
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">数据大屏幕</a></li>
                <li class="am-active">数据自定义</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">

                            <div class="tpl-caption font-green ">
                                <span>数据自定义</span>
                            </div>
                </div>
                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active">
                                    <div id="wrapperA" class="wrapper">
                <div class="tpl-block ">
                    <div class="am-g tpl-amazeui-form">
                        <div class="am-u-sm-12">
            <form role="form" method="post" class="am-form am-form-horizontal" enctype="multipart/form-data">
                                    <?php if ($result['ok3']>0):?>
                    <div class="tpl-content-scope">
                            <div class="note note-info" style="color:green">
                                <p> 数据自定义更新成功!</p>
                            </div>
                        </div>
                      <?php endif?>
                                <!-- <div class="am-form-group">
                                    <label for="goal0" class="am-u-sm-12 am-form-label">本月销售目标</label>
                                    <div class="am-u-sm-12">
                  <input type="number" class="form-control" id="goal0" name="text[goal]" value="<?=intval($config['goal'])?>">
                                    </div>
                                </div> -->
                                <!-- <div class="am-form-group">
                                    <label for="goal" class="am-u-sm-12 am-form-label">本月累计销售额增加(为零则不增加)</label>
                                    <div class="am-u-sm-12">
                  <input type="number" class="form-control" id="goal" name="text[goal1]" value="<?=floatval($config['goal1'])?>">
                                    </div>
                                </div> -->
                                <div class="am-form-group">
                                    <label for="goal2" class="am-u-sm-12 am-form-label">今日交易额增加(为零则恢复原始数据)</label>
                                    <div class="am-u-sm-12">
                  <input type="number" class="form-control" id="goal2" name="text[goal2]" value="<?=intval($config['goal2'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal3" class="am-u-sm-12 am-form-label">昨日交易额增加(为零则恢复原始数据)</label>
                                    <div class="am-u-sm-12">
                  <input type="number" class="form-control" id="goal3" name="text[goal3]" value="<?=intval($config['goal3'])?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="goal4" class="am-u-sm-12 am-form-label">累计交易额(为零则恢复原始数据)</label>
                                    <div class="am-u-sm-12">
                  <input type="number" class="form-control" id="goal4" name="text[goal4]" value="<?=intval($config['goal4'])?>">
                                    </div>
                                </div>
                                <hr>
                <div class="am-form-group">
                        <div class="am-u-sm-9 am-u-sm-push-3">
                            <button type="submit" class="am-btn am-btn-danger">保存数据自定义</button>
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

<?php if($hasover==1):?>
<script type="text/javascript">
  swal({
    title: "数据大屏幕已过期",
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
           window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/product/16";
      }
  })
</script>
<?endif?>
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
</script>
