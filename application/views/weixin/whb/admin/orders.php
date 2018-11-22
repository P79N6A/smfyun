<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    已发送记录
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">已发送记录</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">


  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=count($result['order'])?> 条</h3>
            </div><!-- /.box-header -->
            <form method="get" name="ordersform">
            <ul class="nav nav-tabs">

              <li class="pull-right">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="红包状态搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </li>

            </ul>
          </form>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                <th>微信头像</th>
                <th>微信昵称</th>
                <th>电话</th>
                <th>红包金额</th>
                <th>红包状态</th>
                </tr>
                <?php foreach ($result['order'] as $key=>$order):
                  ?>
                <tr>
                  <td><img src="<?=$order->user->headimgurl?>" width="32" height="32" title="<?=$order->user->openid?>"></td>
                  <td><?=$order->user->nickname?></td>
                  <td><?=$order->user->tel?></td>
                  <td><?=$order->money/100?></td>
                  <td id="lock<?=$order->id?>">
                  <?php
                  if ($order->status == 'SENT')
                    echo '<span class="label label-warning">已发放待领取</span>';
                  if ($order->status == 'RECEIVED')
                    echo '<span class="label label-danger">已领取</span>';
                  if ($order->status == 'REFUND')
                    echo '<span class="label label-success">过期未领取</span>';
                  if ($order->status == 'FAILED')
                    echo '<span class="label label-primary">发放失败</span>';
                  ?>
                  </td>

                </tr>

                <?php endforeach;?>
              </tbody></table>
            </div><!-- /.box-body -->
          </div>
          <div class="box-footer clearfix">
                <?=$pages?>
              </div>
    </div>
  </div>

</section><!-- /.content -->
