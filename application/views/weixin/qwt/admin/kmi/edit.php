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
        echo '卡密';
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
            <form role="form" method="post" enctype="multipart/form-data">
                <div class="box-body textSet" style="background-color:white;">
                  <?php if($prize['key']!='freedom'):?>
                  <div class="row">
                    <div class="col-lg-3 col-sm-6">
                    <div class="form-group">
                      <label for="startdate">有效期（为空则不限制）</label>
                      <div class="input-group">
                        <input type="text" class="form-control pull-right formdatetime" name="edit[enddate]" value="<?=$prize['enddate']?date('Y-m-d H:i:s',$prize['enddate']):''?>" readonly="">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                    </div>
                  </div>
                  <?endif?>
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
                  <input type="hidden" name="edit[key]" value="<?=$prize['key']?>">

                  <span><?=convert($prize['key'])?>名称：</span><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="edit[km_content]" placeholder="输入微信红包名称，如：‘恭喜发财’" value="<?=$prize['km_content']?>" style="width:17%;"><br>
                  <?php if($prize['key']!='freedom'):?>
                      <?php if($prize['key']=='kmi'):?>
                      <span><?=convert($prize['key'])?>数量（正数为增加、负数为减少）：</span><input type="number" class="form-control" id="" placeholder=""  name="edit[km_num]" value="<?=ORM::factory('kmi_km')->where('bid','=',$prize['bid'])->where('startdate','=',$prize['value'])->count_all()?>" style="width:17%;"><br>
                      <?php else:?>
                      <span><?=convert($prize['key'])?>数量（正数为增加、负数为减少）：</span><input type="number" class="form-control" id="" placeholder=""  name="edit[km_num]" value="<?=$prize['km_num']?>" style="width:17%;"><br>
                      <?endif?>
                  <?endif?>
                  <?php if($prize['key']!='freedom'):?>
                  <span><?=convert($prize['key'])?>限购：</span><input type="number" class="form-control" id="" placeholder=""  name="edit[km_limit]" value="<?=$prize['km_limit']?>" style="width:17%;"><br>
                  <?endif ?>
                  <?php if($prize['key']=='hongbao'):?>
                    <span><?=convert2($prize['key'])?>：</span><input type="text" class="form-control" id="" placeholder=""  name="edit[value]" value="<?=$prize['value']?>" ><br>
                  <? endif?>
                  <?php if($prize['key']=='coupon'):?>
                    <span><?=convert2($prize['key'])?>：</span>
                    <select name="edit[coupon]"  class="form-control yzcode">
                    <?php if($wxcards):?>
                    <?php foreach ($wxcards as $wxcard):?>
                    <option <?=$prize['value']==$wxcard['id']?"selected":""?> value="<?=$wxcard['id']?>"><?=$wxcard['title']?></option>
                    <?php endforeach; ?>
                     <?php endif;?>
                    </select>
                    <br>
                  <? endif?>
                   <?php if($prize['key']=='yzcoupon'):?>
                    <span><?=convert2($prize['key'])?>：</span>
                    <select name="edit[yzcoupon]"  class="form-control yzcode">
                    <?php if($yzcoupons):?>
                    <?php foreach ($yzcoupons as $yzcoupon):?>
                    <option <?=$prize['value']==$yzcoupon['group_id']?"selected":""?> value="<?=$yzcoupon['group_id']?>"><?=$yzcoupon['title']?></option>
                    <?php endforeach; ?>
                     <?php endif;?>
                    </select>
                    <br>
                  <? endif?>
                   <?php if($prize['key']=='gift'):?>
                    <span><?=convert2($prize['key'])?>：</span>
                    <select name="edit[gift]"  class="form-control yzcode">
                    <?php if($yzgifts):?>
                    <?php foreach ($yzgifts as $yzgift):?>
                    <option <?=$prize['value']==$yzgift['present_id']?"selected":""?> value="<?=$yzgift['present_id']?>"><?=$yzgift['title']?></option>
                    <?php endforeach; ?>
                     <?php endif;?>
                    </select>
                    <br>
                  <? endif?>
                  <?php if($prize['key']=='kmi'):?>
                    <span>上传追加新的excel文件：</span>
                    <input type="file" class="form-control" id="pic" name="pic" accept="text/csv/xls">
                      <p class="help-block">点此上传文件</p>
                  <?php endif?>
                  <?php if($prize['key']=='freedom'):?>
                  <span>个性化文本消息（仅支持加入文本和超链接，不要换行！）:</span>
                  <?php else:?>
                  <span>下发奖品时推送的内容（仅支持加入文本和超链接，不要换行！）:</span>
                  <?endif?>
                  <!-- <input type="text" class="form-control" id="" placeholder="" maxlength="50" name="edit[km_text]" value="<?=$prize['km_text']?>"> -->
                  <textarea class="textarea" name="edit[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?=$prize['km_text']?></textarea>

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
                      <a href="/kmia/prizes_delete/<?=$prize['id']?>" class="btn btn-outline">确认删除</a>
                    </div>

                  </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
              </div><!-- /.modal -->

            </form>
          </div>
  </div>
</section>












