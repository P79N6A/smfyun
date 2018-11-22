<?php
 function convert($key){
    switch ($key) {
      case 'hongbao':
        echo '微信红包';
        break;
      case 'gift':
        echo '赠品';
        break;
      case 'coupon':
        echo '卡券';
        break;
      case 'kmi':
        echo '特权商品';
        break;
      case 'yzcoupon':
        echo '优惠券';
        break;
      case 'freedom':
        echo '文本消息';
        break;
      default:
        # code...
        break;
    }
 }
 function convert2($key){
    switch ($key) {
      case 'hongbao':
        echo '微信红包金额(单位:分，最低100分)';
        break;
      case 'gift':
        echo '赠品id';
        break;
      case 'coupon':
        echo '卡券id';
        break;
      case 'kmi':
        echo '可购买该商品的用户标签';
        break;
      case 'yzcoupon':
        echo '优惠券id';
        break;
      default:
        # code...
        break;
    }
 }
?>
<style>
  .nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: #00a65a;
  }
  .reduce,.add{
    font-size: 14px;
    position: relative;
    bottom: 10px;
  }
  .add{
    margin-left: 20px;
    margin-right: 30px;
  }
  .loc{
    margin-top: 5px;
    margin-bottom: 5px;
  }
  .inputtxt{
    width:5%;
  }
</style>

<section class="content-header">
  <h1>
    奖品修改
    <small>核心参数配置</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">基础设置</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
            <form role="form" method="post" enctype="multipart/form-data">
                <div class="box-body textSet" style="background-color:white;">

                  <script type="text/javascript">
                    $(function () {
                      $(".formdatetime").datetimepicker({
                        format: "yyyy-mm-dd hh:ii",
                        language: "zh-CN",
                        minView: "0",
                        autoclose: true
                      });
                      var editor = new Simditor({
                      textarea: $('.textarea'),
                      toolbar: ['link']
                      });
                    });
                  </script>
                  <input type="hidden" name="edit[key]" value="<?=$item['key']?>">

                  <span><?=convert($item['key'])?>名称：</span><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="edit[km_content]" placeholder="输入微信红包名称，如：‘恭喜发财’" value="<?=$item['km_content']?>" style="width:17%;"><br>

                   <?php
                    $hello = explode('&',$item['value']);
                   ?>
                 <span><?=convert2($item['key'])?>：</span><input type="text" class="form-control" id="" placeholder=""  name="edit[value]" value="<?=$hello[0]?>" ><br>
                 <?php if($item['key']=='kmi'):?>
                    <span>特权商品链接</span><input type="text" class="form-control" id="" placeholder=""  name="edit[url]" value="<?=$hello[1]?>" ><br>

                <?php endif?>


                </div>
              <div class="box-footer">
                <button type="submit" class="btn btn-success">保存配置</button>
                <button type="submit" class="btn btn-danger" style="margin-left: 60px;"><a href="#" style="color:#fff;" id="delete" data-toggle="modal" data-target="#deleteModel"><span>删除</span></a></button>
              </div>

              <div class="modal modal-danger" id="deleteModel">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
                      <h4 class="modal-title">确认要删除吗？该操作不可恢复！</h4>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-outline" data-dismiss="modal">取消</button>
                      <a href="/rwba/items_delete/<?=$item['id']?>" class="btn btn-outline">确认删除</a>
                    </div>

                  </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
              </div><!-- /.modal -->

            </form>
          </div>
  </div>
</section>












