<style type="text/css">
  .tag{
    display: inline-block;
    border:1px solid #CAC1C1;
    padding:5px;
    margin-left: 10px;
    border-radius: 5px;
    margin-top: 5px;
  }
  .tactive{
    background-color: rgb(255, 232, 148);
  }
</style>

<section class="content-header">
  <h1>
    订单记录
    <small><?=$desc?></small>
  </h1>


  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/flba/orders">领取记录</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

 <!-- <div class="row">
    <div class="col-xs-12">
      <a href="<?=$_SERVER['PATH_INFO']?>?qid=<?=$result['qid']?>&amp;export=csv&tag=<?=$activetype?>" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出奖品发送记录</span></a>
    </div>
  </div>-->


  <div class="row">
    <div class="col-lg-12">

    <?php if ($result['ok']):?>
      <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i><?=$result['ok']?></div>
    <?php endif?>

      <div class="nav-tabs-custom">

          <form method="get" name="ordersform">
            <ul class="nav nav-tabs">

              <li class="pull-right">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="模糊搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </li>

            </ul>
          </form>
          <div class="tab-pane active" id="orders<?=$result['status']?>">

            <div class="table-responsive">
            <form method="post" method="post">
            <table class="table table-striped">
              <tbody>
              <tr>
                <th>微信头像</th>
                <th>微信昵称</th>
                <th>姓名</th>
                <th>电话</th>
                <th>地址</th>
                <th>订单名称</th>
                <th>订单金额(元)</th>
                <th>订单状态</th>
                <th>返还总额(元)</th>
                <th>已返还金额(元)</th>
                <th>已返还次数</th>
                <th>待返还金额(元)</th>
                <th>待返还次数</th>
                <th>日期</th>
                <th>操作</th>
              </tr>
                <?php foreach ($result['orders'] as $order):
                  $money=ORM::factory('flb_detail')->where('bid','=',$bid)->where('oid','=',$order->id)->select(array('SUM("money")', 'moneys'))->find()->moneys;
                  $paid=ORM::factory('flb_detail')->where('bid','=',$bid)->where('oid','=',$order->id)->where('state','=',1)->select(array('SUM("money")', 'money'))->find()->money;
                  $pay=ORM::factory('flb_detail')->where('bid','=',$bid)->where('oid','=',$order->id)->where('state','=',0)->select(array('SUM("money")', 'money1'))->find()->money1;
                  $has_time=ORM::factory('flb_detail')->where('bid','=',$bid)->where('oid','=',$order->id)->where('state','=',0)->count_all();
                  $user_time=ORM::factory('flb_detail')->where('bid','=',$bid)->where('oid','=',$order->id)->where('state','=',1)->count_all();
                ?>
                <tr>

                  <td><img src="<?=$order->user->headimgurl?>" width="32" height="32" title="<?=$order->user->openid?>"></td>
                  <td><?=$order->user->nickname?></td>
                  <td><?=$order->receiver_name?></td>
                  <td><?=$order->tel?></td>
                  <td><?=$order->adress?></td>
                  <td><?=$order->title?></td>
                  <td><?=$order->price?></td>
                  <td id="lock<?=$order->id?>">
                  <?php
                  if ($order->status == 'WAIT_SELLER_SEND_GOODS')
                    echo '<span class="label label-warning">已付款</span>';
                  if ($order->status == 'WAIT_BUYER_CONFIRM_GOODS')
                    echo '<span class="label label-danger">已发货</span>';
                  if ($order->status == 'TRADE_BUYER_SIGNED')
                    echo '<span class="label label-success">已签收</span>';
                  if ($order->status == 'TRADE_CLOSED')
                    echo '<span class="label label-primary">已退款</span>';
                  ?>
                  </td>
                  <td><?=$money?></td>
                  <td><?=$paid?></td>
                  <td><?=$user_time?></td>
                  <td><?=$pay?></td>
                  <td><?=$has_time?></td>
                  <td><?=date('Y-m-d',$order->time)?></td>
                  <td nowrap="">
                  <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$order->id?>" data-tid="<?=$order->tid?>">
                      <span>删除订单</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                </tr>
                <?php endforeach;?>
                <input type="hidden" name="action" value="oneship">
              </tbody>
              </table>
              </form>
              </div>

              <div class="box-footer clearfix">
                <?=$pages?>
              </div>

          </div><!-- tab-pane -->
          </div><!-- tab-content -->

      </div><!-- nav-tabs-custom -->
    </div>
  </div>
</section><!-- /.content -->
<div class="modal modal-danger" id="actionModel">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
      <h4 class="modal-title">确认删除该订单吗?</h4>
      <h5 class="modal-title tid"></h5>
    </div>
      <div class="modal-footer">
      <form method="post" class="posttid">
      </form>
      </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div>
<script>
$('#actionModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var id = button.data('id')
  var tid = button.data('tid')
  var form = '订单号：'+tid;
  var posttid = '<button type="button" class="btn btn-outline" data-dismiss="modal">取消</button><button type="submit" class="btn btn-outline">确认删除</button><input type="hidden" name="deltid" value="'+id+'" />';
  var modal = $(this);
  modal.find('.tid').html(form)
  modal.find('.posttid').html(posttid)
})
</script>


