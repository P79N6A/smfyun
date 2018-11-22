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
      case 'yhm':
        echo '卡密';
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
        echo '赠品';
        break;
      case 'coupon':
        echo '卡券';
        break;
      case 'kmi':
        echo '可购买该商品的用户标签';
        break;
      case 'yzcoupon':
        echo '优惠券';
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
     <?php if($item['key']=='yhm'):?>
    <!-- <div class="row"> -->
        <!-- <div class="col-xs-12"> -->
          <a href="/rwba/items?value1=<?=$item['value']?>&export=csv" class="btn btn-success pull-right" style="margin-right:50px;margin-bottom:10px;margin-top: 2%;"> <i class="fa fa-plus"></i> &nbsp; <span>导出卡密发送记录</span></a>
        <!-- </div> -->
    <!-- </div> -->
      <?php endif?>
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
                <?php if ($result['error']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['error']?></div>
                <?php endif?>
                  <input type="hidden" name="edit[key]" value="<?=$item['key']?>">

                  <span><?=convert($item['key'])?>名称：</span><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="edit[km_content]" placeholder="输入微信红包名称，如：‘恭喜发财’" value="<?=$item['km_content']?>" style="width:17%;"><br>

                   <?php
                    $hello = explode('&',$item['value']);
                   ?>
                  <?php if($item['key']=='hongbao'):?>
                    <span><?=convert2($item['key'])?>：</span><input type="text" class="form-control" id="" placeholder=""  name="edit[value]" value="<?=$item['value']?>" ><br>
                 <?php endif?>
                 <?php if($item['key']=='coupon'):?>
                    <span><?=convert2($item['key'])?>：</span><!-- <input type="text" class="form-control" id="" placeholder=""  name="edit[value]" value="$item['value']" ><br> -->
                    <select name="edit[coupon]"  class="form-control yzcode">
                    <?php if($wxcards):?>
                    <?php foreach ($wxcards as $wxcard):?>
                    <option <?=$item['value']==$wxcard['id']?"selected":""?> value="<?=$wxcard['id']?>"><?=$wxcard['title']?></option>
                    <?php endforeach; ?>
                     <?php endif;?>
                    </select>
                    <br>
                 <?php endif?>
                 <?php if($item['key']=='yzcoupon'):?>
                    <span><?=convert2($item['key'])?>：</span><!-- <input type="text" class="form-control" id="" placeholder=""  name="edit[value]" value="$item['value']" ><br> -->
                    <select name="edit[yzcoupon]"  class="form-control yzcode">
                    <?php if($yzcoupons):?>
                    <?php foreach ($yzcoupons as $yzcoupon):?>
                    <option <?=$item['value']==$yzcoupon['group_id']?"selected":""?> value="<?=$yzcoupon['group_id']?>"><?=$yzcoupon['title']?></option>
                    <?php endforeach; ?>
                     <?php endif;?>
                    </select>
                    <br>
                 <?php endif?>
                 <?php if($item['key']=='gift'):?>
                    <span><?=convert2($item['key'])?>：</span><!--<input type="text" class="form-control" id="" placeholder=""  name="edit[value]" value="$item['value']" ><br> -->
                     <select name="edit[gift]"  class="form-control yzcode">
                    <?php if($yzgifts):?>
                    <?php foreach ($yzgifts as $yzgift):?>
                    <option <?=$item['value']==$yzgift['present_id']?"selected":""?> value="<?=$yzgift['present_id']?>"><?=$yzgift['title']?></option>
                    <?php endforeach; ?>
                     <?php endif;?>
                    </select>
                    <br>
                 <?php endif?>
                 <?php if($item['key']=='kmi'):?>
                    <span><?=convert2($item['key'])?>：</span><input type="text" class="form-control" id="" placeholder=""  name="edit[value]" value="<?=$hello[0]?>" ><br>
                    <span>特权商品链接</span><input type="text" class="form-control" id="" placeholder=""  name="edit[url]" value="<?=$hello[1]?>" ><br>

                <?php endif?>
                 <?php if($item['key']=='yhm'):?>
                    <p class="help-block">点此上传excel文件（不可超过2MB）</p>
                      <input type="file" class="form-control" id="pic" name="pic" accept="text/csv/xls">
                      <br>
                      <label for="startdate">下发奖品时推送的内容（仅支持加入文本和超链接，不要换行！）：</label>
                      <textarea class="textarea4" name="edit[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?=$item['km_text']?$item['km_text']:'亲您的卡号为「%a」卡密为「%b」验证码为「%c」，请注意查收!'?></textarea>

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












