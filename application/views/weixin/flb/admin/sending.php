<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    待发送审核
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">待发送审核</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=count($result['sending'])?> 条</h3>
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
                <th>原因(发送失败)</th>
                <th>审核</th>
               </tr>
                <?php foreach ($result['sending'] as $key=>$sending):
                $money=ORM::factory('flb_detail')->where('bid','=',$bid)->where('oid','=',$sending->oid)->select(array('SUM("money")', 'moneys'))->find()->moneys;
                ?>
                <tr>
                  <td><img src="<?=$sending->order->user->headimgurl?>" width="32" height="32" title="<?=$sending->order->user->openid?>"></td>
                  <td><?=$sending->order->user->nickname?></td>
                  <td><?=$sending->order->receiver_name?></td>
                  <td><?=$sending->order->tel?></td>
                  <td><?=$sending->order->adress?></td>
                  <td><?=$sending->order->title?></td>
                  <td><?=$sending->order->price?></td>
                  <td><?=$money?></td>
                  <td><?=$sending->num?></td>
                  <td><?=$sending->money1!=0?$sending->money1:$sending->money?></td>
                  <td><?=$sending->log?></td>
                   <td nowrap="">
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$sending->id?>">
                      <span>点击审核</span> <i class="fa fa-edit"></i>
                    </a>
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
<div class="modal" id="actionModel">
  <div class="modal-dialog">
    <form id="shipform" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">是否发送</h4>
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
  var button = $(event.relatedTarget)
  var id = button.data('id')
  var form = ''
  form += '<div class="form-group"><label for="flock">是否发送：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock0" value="1"><span class="label label-success" style="font-size:14px">发送</span></label><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock3" value="0"><span class="label label-danger" style="font-size:14px">不发送</label></div></div>'
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';

  var modal = $(this)
  modal.find('.modal-title').text(name)
  modal.find('.modal-body').html(form)
})
</script>
