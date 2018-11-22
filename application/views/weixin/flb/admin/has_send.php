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
              <h3 class="box-title">共 <?=count($result['has_send'])?> 条</h3>
            </div><!-- /.box-header -->
            <form method="get" name="ordersform">
            <ul class="nav nav-tabs">

              <li class="pull-right">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="姓名搜索" value="<?=htmlspecialchars($result['s'])?>">
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
                <th>姓名</th>
                <th>电话</th>
                <th>地址</th>
                <th>订单名称</th>
                <th>订单金额(元)</th>
                <th>返还总额(元)</th>
                <th>第几次返还</th>
                <th>本次返还金额(元)</th>
                <th>红包状态</th>
                <th>日期</th>
                </tr>
                <?php foreach ($result['has_send'] as $key=>$has_send):
                  $money=ORM::factory('flb_detail')->where('bid','=',$bid)->where('oid','=',$has_send->oid)->select(array('SUM("money")', 'moneys'))->find()->moneys;
                  $data=date('Y-m-d',$has_send->lastupdate);
                  ?>
                <tr>
                  <td><img src="<?=$has_send->order->user->headimgurl?>" width="32" height="32" title="<?=$has_send->order->user->openid?>"></td>
                  <td><?=$has_send->order->user->nickname?></td>
                  <td><?=$has_send->order->receiver_name?></td>
                  <td><?=$has_send->order->tel?></td>
                  <td><?=$has_send->order->adress?></td>
                  <td><?=$has_send->order->title?></td>
                  <td><?=$has_send->order->price?></td>
                  <td><?=$money?></td>
                  <td><?=$has_send->num?></td>
                  <td><?=$has_send->money?></td>
                  <td id="lock<?=$has_send->id?>">
                  <?php
                  if ($has_send->status == 'SENT')
                    echo '<span class="label label-warning">已发放待领取</span>';
                  if ($has_send->status == 'RECEIVED')
                    echo '<span class="label label-danger">已领取</span>';
                  if ($has_send->status == 'REFUND')
                    echo '<span class="label label-success">过期未领取</span>';
                  if ($has_send->status == 'FAILED')
                    echo '<span class="label label-primary">发放失败</span>';
                  ?>
                  </td>
                  <td><?=$data?></td>
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
