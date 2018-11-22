<style>
.label {font-size: 14px}
</style>

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
// if ($result['fuser']) $title = $result['fuser']->nickname.'的下线';
 if ($result['s']) $title = '搜索结果';
// if ($result['ticket']) $title = '已生成海报';
?>

<section class="content-header">
  <h1>
    订单记录
    <small></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/ytya/history_trades">订单记录</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">
<form method="get" name="qrcodesform">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">

            <div class="box-header">
              <h3 class="box-title"><?=$title?>: 共 <?=$result['countall']?> 条记录</h3>
              <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按订单编号搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <th>头像</th>
                  <th>昵称</th>
                  <th>订单名称</th>
                  <th>订单编号</th>
                  <th>订单金额</th>
                  <th>订单状态</th>
                  <th>经销商</th>
                  <th>需结算的收益金额</a></th>
                  <th>需扣除经销商进货额</a></th>
                  <th>操作</th>
                </tr>

                <?php
                foreach ($result['trades'] as $v):
                //$user=$v->qrcode;
                //$fuser=ORM::factory('yty_qrcode')->where('bid','=',$bid)->where('openid','=',$user->fopenid)->find();
                //   $count2 = ORM::factory('yty_qrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->count_all();
                //   $count3 = ORM::factory('yty_score')->where('bid', '=', $v->bid)->where('qid', '=', $v->id)->where('type', 'IN', array(3,8))->count_all();
                //   $fuser = ORM::factory('yty_qrcode')->where('bid', '=', $v->bid)->where('openid', '=', $v->fopenid)->find();
                  //$information=ORM::factory('yty_qrcode',array('id'=>$v->qid,'bid'=>$v->bid))->find();
                $information=ORM::factory('yty_qrcode')->where('id','=',$v->qid)->find();
                $fid = ORM::factory('yty_score')->where('tid', '=', $v->id)->where('type', '=', 2)->find();
                $fuser = ORM::factory('yty_qrcode')->where('id', '=', $fid->qid)->find();
                ?>
                <tr>
                  <td><img src="<?=$information->headimgurl?>" width="32" height="32" title="<?=$information->openid?>"></td>
                  <td><?=$information->nickname?></td>
                  <td><?=$v->title?></td>
                  <td ><?=$v->tid?></a></td>
                  <td><?=$v->payment?></td>
                  <td id="lock<?=$v->id?>">
                  <?php
                  if ($v->status == 'WAIT_SELLER_SEND_GOODS')
                    echo '<span class="label label-warning">已付款</span>';
                  if ($v->status == 'WAIT_BUYER_CONFIRM_GOODS')
                    echo '<span class="label label-danger">已发货</span>';
                  if ($v->status == 'TRADE_BUYER_SIGNED')
                    echo '<span class="label label-success">已签收</span>';
                  if ($v->status == 'TRADE_CLOSED')
                    echo '<span class="label label-primary">已退款</span>';
                  ?>
                  </td>
                  <td><a href="/ytya/qrcodes_m?id=<?=$fuser->id?>"><?=$fuser->nickname?></td>
                  <td><?=$v->money?></td>
                  <td><?=$v->money1?></td>
                  <td nowrap="">
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-title="<?=$v->title?>" data-tid="<?=$v->tid?>" data-id="<?=$v->id?>">
                      <span>处理</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                </tr>
                <?php endforeach;?>
              </tbody>
            </table>
          </div><!-- /.box-body -->
              <div class="box-footer clearfix">
                <?=$pages?>
              </div>
            </div>
          </div>
    </div>
</form>
</section><!-- /.content -->
<div class="modal" id="actionModel">
  <div class="modal-dialog">
    <form id="shipform" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">退款订单处理</h4>
      </div>
      <div class="modal-body">&nbsp;</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">确定</button>
      </div>
    </div><!-- /.modal-content -->
    </form>
  </div><!-- /.modal-dialog -->
</div>

<script>
$('#actionModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
  var id = button.data('id');
  var title = button.data('title');
  var tid = button.data('tid');
  var information=title+"  (订单号:"+tid+")";
  var form = '';
  form += '<div class="form-group"><label for="flock">是否处理七天后退款订单：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock0" value="1"><span class="label label-success" style="font-size:14px">是</span></label><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock3" value="0"><span class="label label-danger" style="font-size:14px">否</label></div></div>';
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';
  var modal = $(this);
  modal.find('.modal-title').text(information);
  modal.find('.modal-body').html(form);
})
</script>
