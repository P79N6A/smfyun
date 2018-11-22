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
</style>

<section class="content-header">
  <h1>
    红包发送审核

  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">红包发送审核</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">
      <?php
        if ($_POST['cfg']) $active = 'wx';
        if (!$_POST || $_POST['hb_check']) $active = 'hb_check';
        ?>

        <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
        </script>
        <div class="tab-content">
        <div class="tab-pane" id="cfg_hb_check">
        <?php if ($result['ok8'] > 0):?>
              <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>保存成功</div>
            <?php endif?>
          <form role="form" method="post">
            <div class="form-group">
              <label for="show" style="font-size:16px;">此功能开启后，用户兑换奖品时，可以在后台奖品兑换界面对进行【兑换红包】的用户进行审核，审核成功之后再发送红包</label>
              <div class="radio">
                <label class="checkbox-inline">
                  <input type="radio" name="hb_check[hb_check]" value="1" id="open" <?=$config['hb_check'] == 1 ? ' checked=""' : ''?>>
                  <span class="label label-success" style="font-size:14px">开启</span>
                </label>
                <label class="checkbox-inline">
                  <input type="radio" name="hb_check[hb_check]" value="0" id="close" <?=$config['hb_check'] == 0 ?'checked=""' : ''?>>
                  <span class="label label-danger" style="font-size:14px">关闭</span>
                </label>
              </div>
            </div><br>
            <button type="submit" class="btn btn-success" id="sub">保存配置</button>
          </form>
        </div>
        </div>
      </div>
    </div><!--/.col (left) -->

  </section><!-- /.content -->

