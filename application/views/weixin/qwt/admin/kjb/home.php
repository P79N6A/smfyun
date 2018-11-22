<style type="text/css">
    label{
        text-align: left !important;
    }
    .am-badge{
        background-color: green;
    }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                店铺信息设置
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">砍价宝</a></li>
                <li class="am-active">店铺信息设置</li>
            </ol>


                <div class="am-u-md-6 am-u-sm-12 row-mb" style="width:100%">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                            <div class="tpl-caption font-green ">
                                <span>店铺信息设置</span>
                            </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="tab2">
                                    <div id="wrapperB" class="wrapper">
                <div class="tpl-block tpl-amazeui-form">

                    <div class="am-g">
                        <div class="tpl-form-body am-form-horizontal">
                            <form method="post" class="am-form " enctype='multipart/form-data'>
                                <!-- <div class="am-form-group">
                                    <label for="shopname" class="am-u-sm-12 am-form-label">店铺名称</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="form-control" id="shopname" name="shop[name]" value="<?=$_POST['shop']['name']?>">
                                    </div>
                                </div> -->
                                <div class="am-form-group">
                                    <label for="shoptel" class="am-u-sm-12 am-form-label">客服电话</label>
                                    <div class="am-u-sm-12">
                                        <input type="number" class="form-control" id="shoptel" name="shop[tel]" value="<?=$_POST['shop']['tel']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="shopurl" class="am-u-sm-12 am-form-label">店铺链接</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="form-control" id="shopurl" name="shop[url]" value="<?=$_POST['shop']['url']?>">
                                    </div>
                                </div>
                                <div class="am-form-group">
                                    <label for="shoptitle" class="am-u-sm-12 am-form-label">砍价活动页面标题</label>
                                    <div class="am-u-sm-12">
                                        <input type="text" class="form-control" id="shoptitle" name="shop[title]" value="<?=$_POST['shop']['title']?>">
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
$(document).ready(function(){
<?php if($hasover==1):?>
  swal({
    title: "砍价宝已过期",
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
           window.location.href = "http://<?=$_SERVER['HTTP_HOST']?>/qwta/product/24";
      }
  })
<?endif?>
<?php if ($result['ok3']):?>
    swal({
        title: "成功",
        text: "店铺信息保存成功",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "确定",
        closeOnConfirm: true,
    })
<?php endif?>
<?php if ($result['err3']):?>
    swal({
        title: "失败",
        text: "<?=$result['err3']?>",
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "我知道了",
        closeOnConfirm: true,
    })
<?php endif?>
})
</script>
