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
    同步有赞积分
    
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">同步有赞积分</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">

        
        <?php
        if ($_POST['cfg']) $active = 'wx';
        if (!$_POST || $_POST['rsync']) $active = 'rsync';

        
        if ($_POST['rsync']) $active = 'rsync';
        
        ?>

        <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
        </script>

        <div class="tab-content">
        <!-- 有赞积分同步 -->
        <div class="tab-pane" id="cfg_rsync">
          <?php if ($result['ok7'] > 0):?>
            <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>开启成功!</div>
          <?php endif?>
          <?php if ($result['error7'] ==7):?>
            <div class="alert alert-warning alert-dismissable"><i class="icon fa fa-check"></i>请在绑定有赞处点击一键授权</div>
          <?php endif?>
          <div>
            公告：由于接口政策的原因，为保证功能的正常使用，有赞积分同步功能已暂停使用。<br>
          </div>
          <!-- <form role="form" method="post">
            <div class="form-group">
              <label for="show" style="font-size:16px;">是否开启本功能？开启有赞积分同步功能之后，不能关闭。
                注意：因为有赞积分只支持整数，积分宝和有赞积分兑换比例默认为1比1，请权衡好积分宝和有赞的积分奖励规则。</label>
                <div class="radio">
                  <label class="checkbox-inline">
                    <input type="radio" name="rsync[switch]" id="rsync1" value="1" <?=$config['switch'] == 1 ? ' checked=""' : ''?>>
                    <span class="label label-success"  style="font-size:14px">开启</span>
                  </label>
                  <label class="checkbox-inline">
                    <input <?=$config['switch'] == 1 ? ' disabled' : ''?> type="radio" name="rsync[switch]" id="rsync0" value="0" <?=$config['switch'] === "0" ||!$config['switch']? ' checked=""' : ''?>>
                    <span class="label label-danger"  style="font-size:14px">关闭</span>
                  </label>
                </div>
              </div>
              <button type="submit" class="btn btn-success">保存配置</button>
            </form> -->
          </div>
          <!-- 有赞积分同步end -->


        </div>
      </div>

    </div><!--/.col (left) -->

  </section><!-- /.content -->

