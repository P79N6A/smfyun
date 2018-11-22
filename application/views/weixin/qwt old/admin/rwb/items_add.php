<!-- 卡密配置 -->
<?php
  $active = 'hb';
  if ($_POST['honbao']) $active = 'hb';
  if ($_POST['coupon']) $active = 'kq';
  if ($_POST['gift']) $active = 'zp';
  if ($_POST['kmi']) $active = 'km';
  if ($_POST['yhq']) $active = 'yhq';
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
  .tab-content{
    padding:20px;
  }
</style>
<section class="wrapper" style="width:85%;float:right;">
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
      <div class="nav-tabs-custom" style="background:white">
        <ul class="nav nav-tabs custom-tab dark-tab">
          <li id="cfg_hb_li active"><a href="#cfg_hb" data-toggle="tab">微信红包</a></li>
          <li id="cfg_zp_li"><a href="#cfg_zp" data-toggle="tab">有赞赠品</a></li>
          <li id="cfg_kq_li"><a href="#cfg_kq" data-toggle="tab">微信卡券</a></li>
          <li id="cfg_km_li"><a href="#cfg_km" data-toggle="tab">特权商品</a></li>
          <li id="cfg_yhq_li"><a href="#cfg_yhq" data-toggle="tab">有赞优惠券</a></li>
        </ul>
        </script>
        <style type="text/css">
            .textSet>span{
              font-size: 15px;
              font-weight: 900;
            }
        </style>
        <div class="tab-content">
          <div class="tab-pane active" id="cfg_hb">
            <form role="form" method="post">
              <div class="box-body textSet">


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
                <div class="form-group">
                <label for="startdate">微信红包名称：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="hongbao[km_content]" placeholder="输入微信红包名称，如：‘恭喜发财’" value="" style="width:17%;"><br>
                </div>
                <div class="form-group">
                <label for="startdate">微信红包金额(单位：分，最低100分)：</label><input type="number" class="form-control" id="" placeholder="" maxlength="50" name="hongbao[value]" value="" style="width:17%;"><br>
                </div>
              </div>
              <div class="box-footer">
                <button type="submit" class="btn btn-success">保存红包配置</button>
              </div>
            </form>
          </div>
          <div class="tab-pane" id="cfg_kq">
            <form role="form" method="post" enctype="multipart/form-data">
              <div class="box-body textSet">


                <?php if ($result['error']['coupon']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['error']['coupon']?></div>
                <?php endif?>
                  <div class="form-group">
                  <label for="startdate">卡券名称：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="coupon[km_content]" placeholder="输入卡劵备注" value="" style="width:17%;"><br>
                  </div>
                  <div class="form-group">
                  <label for="startdate">卡券ID：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="coupon[value]" value="" style="width:17%;"><br>
                  </div>


              </div>

              <div class="box-footer">
                <button type="submit" class="btn btn-success">保存卡券配置</button>
              </div>
            </form>
          </div>
          <div class="tab-pane" id="cfg_zp">
            <form role="form" method="post">
                  <div class="box-body textSet">

                      <?php if ($result['error']['gift']):?>
                        <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['error']['gift']?></div>
                      <?php endif?>
                      <div class="form-group">
                      <label for="startdate">赠品名称：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="gift[km_content]" placeholder="输入赠品备注" value="" style="width:17%;"><br>
                      </div>
                      <div class="form-group">
                      <label for="startdate">赠品ID：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="gift[value]" value="" style="width:17%;"><br>
                      </div>


                  </div>
                <div class="box-footer">
                  <button type="submit" class="btn btn-success">保存赠品配置</button>
                </div>
             </form>
          </div>
          <div class="tab-pane" id="cfg_km">
            <form role="form" method="post" enctype='multipart/form-data'>
              <div class="box-body textSet">

                      <?php if ($result['error']['rwb']):?>
                        <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['error']['rwb']?></div>
                      <?php endif?>
                      <div class="form-group">
                      <label for="startdate">特权商品名称：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="kmi[km_content]" placeholder="" value="" style="width:17%;"><br>
                      </div>
                      <div class="form-group">
                      <label for="startdate">可购买该商品的用户标签：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="kmi[value]" value="" style="width:17%;"><br>
                      </div>
                      <div class="form-group">
                      <label for="startdate">特权商品链接：</label><input type="text" class="form-control" id="" placeholder=""  name="kmi[url]" value="" style="width:17%;"><br>
                      </div>


                  </div>
                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">保存特权商品配置</button>
                  </div>
             </form>
          </div>
          <div class="tab-pane" id="cfg_yhq">
            <form role="form" method="post">
              <div class="box-body textSet">

                      <?php if ($result['error']['yhq']):?>
                        <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['error']['yhq']?></div>
                      <?php endif?>
                      <div class="form-group">
                      <label for="startdate">优惠券名称：</label><input type="text" class="form-control" id="" placeholder="" maxlength="50" name="yhq[km_content]" placeholder="" value="" style="width:17%;"><br>
                      </div>
                      <div class="form-group">
                      <label for="startdate">优惠券id：</label><input type="text" class="form-control" id="" placeholder=""  name="yhq[value]" value="" style="width:100%;"><br>
                      </div>

                  </div>
                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">保存优惠券配置</button>
                  </div>
             </form>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
</section>
