<style>
.nav-tabs-custom>.nav-tabs>li.active {
  border-top-color: #00a65a;
}
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
</style>

<section class="content-header">
  <h1>
    营销模块
    <small><?=$desc?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">营销模块</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">
          <?php
          if ($_POST['cfg']) $active = 'wx';
          if (!$_POST || $_POST['yz']) $active = 'yz';
          if ($_POST['menu']) $active = 'menu';
          if ($_POST['text']) $active = 'text';
          if (isset($_POST['password'])) $active = 'account';
          ?>

          <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
          </script>

          <div class="tab-content">

            <div class="tab-pane active" id="cfg_text">

                <?php if ($result['ok3'] > 0):?>
                  <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>保存成功!</div>
                <?php endif?>

                <?php if ($result['err3']):?>
                  <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-warning"></i><?=$result['err3']?></div>
                <?php endif?>

                <!-- form start -->
                <form role="form" method="post" enctype="multipart/form-data">
                  <div class="box-body">
                    <div class="form-group">
                      <label for="coupon">是否开启首次进入直播间赠送优惠券功能</label>
                      <div class="radio">
                        <label class="checkbox-inline"  onclick="showfunc()">
                          <input type="radio" name="text[coupon]" id="pshow1" value="1" <?=$config['coupon'] == 1 ? ' checked=""' : ''?>>
                          <span class="label label-success"  style="font-size:14px">开启</span>
                        </label>
                        <label class="checkbox-inline" onclick="hidefunc()">
                          <input type="radio" name="text[coupon]" id="pshow2" value="0" <?=$config['coupon'] == 0 ||!$config['coupon']? ' checked=""' : ''?>>
                          <span class="label label-danger"  style="font-size:14px">关闭</span>
                        </label>
                      </div>
                    </div>
                    <div class="form-group coupon">
                      <label for="coupon">选择优惠券</label>
                      <select name="text[couponid]">
                      <?php if($coupon['response']['coupons']){?>
                        <?php foreach ($coupon['response']['coupons'] as $coupon):?>
                            <option <?=$coupon['group_id']==$config['couponid']?'selected':''?> value="<?=$coupon['group_id']?>"><?=$coupon['title']?></option>
                        <?php endforeach;?>
                      <?php }?>
                      </select>
                    </div>
                  </div><!-- /.box-body -->

                  <div class="box-footer">
                    <button type="submit" class="btn btn-success">保存</button>
                  </div>
                </form>
            </div>
          </div>
      </div>

    </div><!--/.col (left) -->

</section><!-- /.content -->
<script type="text/javascript">
function showfunc(){
  $('.coupon').fadeIn(500);
}
function hidefunc(){
  $('.coupon').fadeOut(500);
}
</script>
