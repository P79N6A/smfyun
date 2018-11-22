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
    取消关注扣除积分

  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">取消关注扣除积分</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">

        <?php
        if ($_POST['cfg']) $active = 'wx';
        if (!$_POST || $_POST['cancle']) $active = 'cancle';




        ?>

        <script>
          $(function () {
            $('#cfg_<?=$active?>,#cfg_<?=$active?>_li').addClass('active');
          });
        </script>

        <div class="tab-content">


        <div class="tab-pane" id="cfg_cancle">
        <?php if ($result['ok8'] > 0):?>
              <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>保存成功</div>
            <?php endif?>
          <form role="form" method="post">
            <div class="form-group">
              <label for="show" style="font-size:16px;">1、粉丝取消关注公众号后，该粉丝的首次关注奖励积分，该粉丝上线、上线的上线获得的积分奖励，全部扣除；该粉丝重新关注公众号后，已扣除的积分不会恢复。<br>

2、用户点击菜单进入积分相关页面，会自动筛选下线、下线的下线是否有取消关注，有的话按照上述流程扣除积分；<br>
3、获取用户基本信息的微信接口调用量每天有上限，如果活动参与人数多，不建议开启本功能，很容易造成接口调用量达到上限，影响活动的正常进行；<br>
4、本功能开启后，会增加客服沟通压力，请谨慎考虑后再开启；</label>
              <div class="radio">
                <label class="checkbox-inline">
                  <input type="radio" name="cancle[btnn]" value="1" id="open" <?=$config['btnn'] == 1 ? ' checked=""' : ''?>>
                  <span class="label label-success" style="font-size:14px">开启</span>
                </label>
                <label class="checkbox-inline">
                  <input type="radio" name="cancle[btnn]" value="0" id="close" <?=$config['btnn'] == 0 ?'checked=""' : ''?>>
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

