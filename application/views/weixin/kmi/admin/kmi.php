<!-- 卡密配置 -->
<?php
  $active = 'hb';
  if ($_POST['honbao']) $active = 'hb';
  if ($_POST['coupon']) $active = 'kq';
  if ($_POST['gift']) $active = 'zp';
  if ($_POST['kmi']) $active = 'km';
  if ($_POST['yhq']) $active = 'yhq';
  if ($_POST['freedom']) $active = 'freedom';
?>
<script>
  $(function () {
    $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
  });
</script>
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
  label{
    display: block;
  }
</style>

<section class="content-header">
  <h1>
    奖品配置
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
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li id="cfg_hb_li"><a href="#cfg_hb" data-toggle="tab">微信红包</a></li>
          <li id="cfg_zp_li"><a href="#cfg_zp" data-toggle="tab">有赞赠品</a></li>
          <li id="cfg_kq_li"><a href="#cfg_kq" data-toggle="tab">微信卡券</a></li>
          <li id="cfg_km_li"><a href="#cfg_km" data-toggle="tab">卡密</a></li>
          <li id="cfg_yhq_li"><a href="#cfg_yhq" data-toggle="tab">有赞优惠券</a></li>
          <li id="cfg_freedom_li"><a href="#cfg_freedom" data-toggle="tab">自定义回复文本</a></li>
        </ul>
        </script>
        <style type="text/css">
            .textSet>span{
              font-size: 15px;
              font-weight: 900;
            }
        </style>
        <div class="tab-content">
          <div class="tab-pane" id="cfg_hb">
            <form role="form" method="post">
            	<div class="box-body textSet">
                <div class="row">
                  <div class="col-lg-3 col-sm-6">
                  <div class="form-group">
                    <label for="startdate">有效期（为空则不限制）</label>
                    <div class="input-group">
                      <input type="text" class="form-control pull-right formdatetime" name="hongbao[enddate]" value="" readonly="">
                      <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                  </div>
                </div>

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
                    var editor = new Simditor({
                      textarea: $('.textarea2'),
                      toolbar: ['link']
                    });
                    var editor = new Simditor({
                      textarea: $('.textarea3'),
                      toolbar: ['link']
                    });
                    var editor = new Simditor({
                      textarea: $('.textarea4'),
                      toolbar: ['link']
                    });
                    var editor = new Simditor({
                      textarea: $('.textarea5'),
                      toolbar: ['link']
                    });
                    var editor = new Simditor({
                      textarea: $('.textarea6'),
                      toolbar: ['link']
                    });
                  });
                </script>
                <?php if ($result['error']['hongbao']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['error']['hongbao']?></div>
                <?php endif?>

                <label for="startdate">微信红包名称：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="hongbao[km_content]" placeholder="输入微信红包名称，如：‘恭喜发财’" value="" style="width:17%;"><br>
                <label for="startdate">微信红包数量：</label><input type="number" class="form-control" id="" placeholder="" maxlength="50" name="hongbao[km_num]" value="" style="width:17%;"><br>
                <label for="startdate">微信红包限购：</label><input type="number" class="form-control" id="" placeholder="" maxlength="50" name="hongbao[km_limit]" value="0" style="width:17%;"><br>
                <label for="startdate">微信红包金额(单位：分，最低100分)：</label><input type="number" class="form-control" id="" placeholder="" maxlength="50" name="hongbao[value]" value="" style="width:17%;"><br>
                <label for="startdate">下发奖品时推送的内容（仅支持加入文本和超链接，不要换行！）：</label><!-- <input type="text" class="form-control" id="" placeholder="" maxlength="50" name="hongbao[km_text]" value="恭喜发财，红包已发送，请查收!" style="width:100%;"> -->
                <textarea class="textarea" name="hongbao[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">恭喜发财，红包已发送，请查收!</textarea>

                </div>

              <div class="box-footer">
                <button type="submit" class="btn btn-success">保存红包配置</button>
              </div>
            </form>
          </div>
          <div class="tab-pane" id="cfg_kq">
            <form role="form" method="post" enctype="multipart/form-data">
            	<div class="box-body textSet">

                <div class="row">
                  <div class="col-lg-3 col-sm-6">
                  <div class="form-group">
                    <label for="startdate">有效期（为空则不限制）</label>
                    <div class="input-group">
                      <input type="text" class="form-control pull-right formdatetime" name="coupon[enddate]" value="" readonly="">
                      <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                  </div>
                </div>
                <?php if ($result['error']['coupon']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['error']['coupon']?></div>
                <?php endif?>
                  <label for="startdate">卡券名称：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="coupon[km_content]" placeholder="输入卡劵备注" value="" style="width:17%;"><br>
                  <label for="startdate">卡券数量：</label><input type="number" class="form-control" id="" placeholder="" maxlength="50" name="coupon[km_num]" value="" style="width:17%;"><br>
                  <label for="startdate">卡券限购：</label><input type="number" class="form-control" id="" placeholder="" maxlength="50" name="coupon[km_limit]" value="0" style="width:17%;"><br>
                  <label for="startdate">卡券：</label>
                  <select name="coupon[value]" class="form-control">
                  <?php if($wxcards):?>
                  <?php foreach ($wxcards as $wxcard):?>
                  <option <?=$_POST['data']['url']==$wxcard['id']?"selected":""?> value="<?=$wxcard['id']?>"><?=$wxcard['title']?></option>
                  <?php endforeach; ?>
                   <?php endif;?>
                  </select>
                  <br>
                  <label for="startdate">下发奖品时推送的内容（仅支持加入文本和超链接，不要换行！）：</label><!-- <input type="text" class="form-control" id="" placeholder="" maxlength="50" name="coupon[km_text]" value="赶紧点击链接「%s」领取卡券吧" style="width:100%;"> -->
                  <textarea class="textarea2" name="coupon[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">赶紧点击链接「%s」领取卡券吧</textarea>

		          </div>

              <div class="box-footer">
                <button type="submit" class="btn btn-success">保存卡券配置</button>
              </div>
            </form>
          </div>
          <div class="tab-pane" id="cfg_zp">
	          <form role="form" method="post">
                  <div class="box-body textSet">

                    <div class="row">
                    <div class="col-lg-3 col-sm-6">
                    <div class="form-group">
                      <label for="startdate">有效期（为空则不限制）</label>
                      <div class="input-group">
                        <input type="text" class="form-control pull-right formdatetime" name="gift[enddate]" value="" readonly="">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                    </div>
                  </div>
                      <?php if ($result['error']['gift']):?>
                        <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['error']['gift']?></div>
                      <?php endif?>
                      <label for="startdate">赠品名称：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="gift[km_content]" placeholder="输入赠品备注" value="" style="width:17%;"><br>
                      <label for="startdate">赠品数量：</label><input type="number" class="form-control" id="" placeholder="" maxlength="50" name="gift[km_num]" value="" style="width:17%;"><br>
                      <label for="startdate">赠品限购：</label><input type="number" class="form-control" id="" placeholder="" maxlength="50" name="gift[km_limit]" value="0" style="width:17%;"><br>
                      <label for="startdate">赠品：</label>
                       <select name="gift[value]"  class="form-control" >
                        <?php if($yzgifts):?>
                        <?php foreach ($yzgifts as $yzgift):?>
                        <option <?=$_POST['data']['value1']==$yzgift['present_id']?"selected":""?> value="<?=$yzgift['present_id']?>"><?=$yzgift['title']?></option>
                        <?php endforeach; ?>
                         <?php endif;?>
                        </select>
                      <br>
                      <label for="startdate">下发奖品时推送的内容（仅支持加入文本和超链接，不要换行！）：</label><!-- <input type="text" class="form-control" id="" placeholder="" maxlength="50" name="gift[km_text]" value="赠品已下发，请查收" style="width:100%;"> -->
                      <textarea class="textarea3" name="gift[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">赠品已下发，请查收</textarea>

                  </div>
                <div class="box-footer">
                  <button type="submit" class="btn btn-success">保存赠品配置</button>
                </div>
	           </form>
          </div>
          <div class="tab-pane" id="cfg_km">
	          <form role="form" method="post" enctype='multipart/form-data'>
	          	<div class="box-body textSet">
                   <div class="row">
                    <div class="col-lg-3 col-sm-6">
                    <div class="form-group">
                      <label for="startdate">有效期（为空则不限制）</label>
                      <div class="input-group">
                        <input type="text" class="form-control pull-right formdatetime" name="kmi[enddate]" value="" readonly="">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                    </div>
                  </div>
                      <?php if ($result['error']['kmi']):?>
                        <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['error']['kmi']?></div>
                      <?php endif?>
                      <label for="startdate">卡密名称：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="kmi[km_content]" placeholder="" value="" style="width:17%;"><br>
                      <label for="startdate">卡密数量：</label><input type="number" class="form-control" id="" placeholder="" maxlength="50" name="kmi[km_num]" value="" style="width:17%;"><br>
                      <label for="startdate">卡密限购：</label><input type="number" class="form-control" id="" placeholder="" maxlength="50" name="kmi[km_limit]" value="0" style="width:17%;"><br>
                      <p class="help-block">点此上传excel文件（不可超过2MB）</p>
                      <input type="file" class="form-control" id="pic" name="pic" accept="text/csv/xls">
                      <br>
                      <label for="startdate">下发奖品时推送的内容（仅支持加入文本和超链接，不要换行！）：</label><!-- <input type="text" class="form-control" id="" placeholder="" maxlength="50" name="kmi[km_text]" value="亲您的卡号为「%a」卡密为「%b」验证码为「%c」，请注意查收!" style="width:100%;"> -->
                      <textarea class="textarea4" name="kmi[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">亲您的卡号为「%a」卡密为「%b」验证码为「%c」，请注意查收!</textarea>

                  </div>
                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">保存卡密配置</button>
                  </div>
	           </form>
          </div>
          <div class="tab-pane" id="cfg_yhq">
            <form role="form" method="post">
              <div class="box-body textSet">
                   <div class="row">
                    <div class="col-lg-3 col-sm-6">
                    <div class="form-group">
                      <label for="startdate">有效期（为空则不限制）</label>
                      <div class="input-group">
                        <input type="text" class="form-control pull-right formdatetime" name="yhq[enddate]" value="" readonly="">
                        <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                    </div>
                  </div>
                      <?php if ($result['error']['yhq']):?>
                        <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['error']['yhq']?></div>
                      <?php endif?>
                      <label for="startdate">优惠券名称：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="yhq[km_content]" placeholder="" value="" style="width:17%;"><br>
                      <label for="startdate">优惠券数量：</label><input type="number" class="form-control" id="" placeholder="" maxlength="50" name="yhq[km_num]" value="" style="width:17%;"><br>
                      <label for="startdate">优惠券限购：</label><input type="number" class="form-control" id="" placeholder="" maxlength="50" name="yhq[km_limit]" value="0" style="width:17%;"><br>
                      <label for="startdate">优惠券：</label>
                      <select name="yhq[value]"  class="form-control" >
                      <?php if($yzcoupons):?>
                      <?php foreach ($yzcoupons as $yzcoupon):?>
                      <option <?=$_POST['data']['value1']==$yzcoupon['group_id']?"selected":""?> value="<?=$yzcoupon['group_id']?>"><?=$yzcoupon['title']?></option>
                      <?php endforeach; ?>
                       <?php endif;?>
                      </select>
                      <br>
                      <label for="startdate">下发奖品时推送的内容（仅支持加入文本和超链接，不要换行！）：</label><!-- <input type="text" class="form-control" id="" placeholder="" maxlength="50" name="yhq[km_text]" value="优惠券已下发到您账户，请查收" style="width:100%;"> -->
                      <textarea class="textarea5" name="yhq[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">优惠券已下发到您账户，请查收!</textarea><br>

                  </div>
                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">保存优惠券配置</button>
                  </div>
             </form>
          </div>
          <div class="tab-pane" id="cfg_freedom">
            <form role="form" method="post">
                <label for="startdate">文本消息名称：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="freedom[km_content]" placeholder="" value="" style="width:17%;"><br>
                <label for="startdate">个性化文本消息（仅支持加入文本和超链接，不要换行！）：</label><!-- <input type="text" class="form-control" id="" placeholder="" maxlength="50" name="freedom[km_text]" value="感谢小主在我家店铺购买" style="width:100%;"> --><textarea class="textarea6" name="freedom[km_text]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">感谢小主在我家店铺购买!</textarea><br>

                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">保存文本配置</button>
                  </div>
             </form>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


