<style type="text/css">
    label{
        text-align: left !important;
    }
</style>

    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
              盖楼设置
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">微信盖楼</a></li>
                <li class="am-active">盖楼设置</li>
            </ol>
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                    <div class="caption font-green bold">
                        <span class="am-icon-code"></span> 盖楼设置
                    </div>


                </div>
                <div class="tpl-block ">

                    <div class="am-g tpl-amazeui-form">


                        <div class="am-u-sm-12 am-u-md-9">
                            <form class="am-form am-form-horizontal" method="post">
                        <?php if ($success =='text' ):?>
                            <div class="tpl-content-scope">
                              <div class="note note-info">
                                <p> 配置保存成功!</p>
                              </div>
                            </div>
                        <?php endif?>
                                <div class="am-form-group">
                                    <label for="keyword" class="am-u-sm-12 am-form-label">盖楼关键字</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" id="keyword" name="text[keyword]" placeholder="keyword" value="<?=$config['keyword']?>">
                                    </div>
                                </div>

                                <div class="am-form-group">
                                    <label for="word" class="am-u-sm-12 am-form-label">盖楼未中奖回复文案</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" id="word" name="text[fword]" placeholder="" value="<?=htmlspecialchars($config['fword'])?>">
                                    </div>
                                </div>

                                <div class="am-form-group">
                                    <label for="time" class="am-u-sm-12 am-form-label">单个用户每日盖楼次数上限(0表示不限次数)</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" id="times" name="text[times]" placeholder="" value="<?=$config['times']?>">
                                    </div>
                                </div>


                                <div class="am-form-group">
                                    <div class="am-u-sm-9 am-u-sm-push-3">
                                        <button type="submit" class="am-btn am-btn-primary">保存盖楼设置</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>










        </div>

    </div>
<?php if($hasover==1):?>
<script type="text/javascript">
  swal({
    title: "盖楼已过期",
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
           window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/product/6";
      }
  })
</script>
<?endif?>
