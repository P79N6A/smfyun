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
    提现模式

  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">提现模式</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">


        <div class="tab-content">
        <!-- 有赞积分同步 -->
        <div class="tab-pane active" id="cfg_rsync">
          <?php if ($result['ok'] > 0):?>
            <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i>修改成功!</div>
          <?php endif?>
          <!-- <!-- <div>
            公告：<br>
            1、有赞积分同步功能暂时无法使用；<br>
            2、之前已经开通本功能的商户，本功能继续有效不受影响；<br>
            3、有赞近期会调整积分接口的规则，调整完毕后，有赞积分同步功能会重新在后台上线；<br>
          </div> -->
          <form role="form" method="post">
            <div class="form-group">
              <label for="show" style="font-size:16px;">自主提现自动发款，余额不足时请切换成申请提现</label>
                <div class="radio">
                  <label class="checkbox-inline">
                    <input type="radio" name="rsync[send_self]" id="rsync1" value="1" <?=$config['send_self'] == 1 ||!$config['send_self']? ' checked=""' : ''?>>
                    <span class="label label-success"  style="font-size:14px">自主提现</span>
                  </label>
                  <label class="checkbox-inline">
                    <input type="radio" name="rsync[send_self]" id="rsync2" value="2" <?=$config['send_self'] ==2 ? ' checked=""' : ''?>>
                    <span class="label label-danger"  style="font-size:14px">申请提现</span>
                  </label>
                </div>
              </div>
              <button type="submit" class="btn btn-success">保存</button>
            </form>
          </div>
          <!-- 有赞积分同步end -->


        </div>
      </div>

    </div><!--/.col (left) -->

  </section><!-- /.content -->

