
<section class="content-header">
  <h1>
    兑换记录
    <small><?=$desc?></small>
  </h1>

<?php
if ($result['qrcode']) $title = $result['qrcode']->nickname . '的兑换明细 / ';

if ($result['status'] == 0) $title .= '未处理';
if ($result['status'] == 1) $title .= '已处理';
?>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/mdta/orders">兑换记录</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
<form method="get" name="ordersform">

  <div class="row">
    <div class="col-xs-12">
      <a href="#" data-toggle="modal" data-target="#shipModel" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-shopping-cart"></i> &nbsp; <span>批量发货</span></a>
      <a href="<?=$_SERVER['PATH_INFO']?>?qid=<?=$result['qid']?>&amp;export=csv" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出 CSV</span></a>
    </div>
  </div>


  <div class="row">
    <div class="col-lg-12">

    <?php if ($result['ok']):?>
      <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i><?=$result['ok']?></div>
    <?php endif?>

      <div class="nav-tabs-custom">

          <ul class="nav nav-tabs">
            <li id="orders<?=$result['status']?>" class="<?=$result['status'] == 0 ? 'active' : ''?>"><a href="/mdta/orders?qid=<?=$result['qid']?>">未处理订单</a></li>
            <li id="orders<?=$result['status']?>" class="<?=$result['status'] == 1 ? 'active' : ''?>"><a href="/mdta/orders/done?qid=<?=$result['qid']?>">已处理订单</a></li>

            <li class="pull-right">
              <div class="input-group" style="width: 250px;">
                <input type="text" name="s" class="form-control input-sm pull-right" placeholder="模糊搜索" value="<?=htmlspecialchars($result['s'])?>">
                <div class="input-group-btn">
                  <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                </div>
              </div>
            </li>

          </ul>

          <div class="tab-content">
          <div class="tab-pane active" id="orders<?=$result['status']?>">

            <div class="table-responsive">
            <table class="table table-striped">
              <tbody><tr>
                <th>头像</th>
                <th>昵称</th>
                <th>收货人</th>
                <th>手机</th>
                <th>收货地址</th>
                <th>备注</th>
                <th>时间</th>
                <th>品名</th>
                <th nowrap="">积分</th>
                <th>操作</th>
              </tr>

                <?php foreach ($result['orders'] as $order):?>

                <tr>
                  <td><img src="<?=$order->user->headimgurl?>" width="32" height="32" title="<?=$order->user->openid?>"></td>
                  <td>
                    <a href="/mdta/qrcodes?id=<?=$order->user->id?>"><?=$order->user->nickname?></a>
                  </td>
                  <td nowrap=""><?=$order->name?></td>
                  <td nowrap=""><?=$order->tel?></td>
                  <td><?=$order->city.' '.$order->address?></td>
                  <td><?=$order->memo?></td>
                  <td nowrap=""><?=date('m-d H:i', $order->createdtime)?></td>
                  <td><?=$order->item->name?></td>
                  <td><?=$order->score?></td>
                  <td nowrap="">
                    <?php if ($result['status'] == 0):?>
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-openid="<?=$order->user->openid?>" data-name="<?=$order->item->name?>" data-id="<?=$order->id?>" data-linkman="<?=$order->name?>" data-addr="<?=$order->city.' '.$order->address?>">
                      <span>处理</span> <i class="fa fa-check-square"></i>
                    </a>
                    <?php endif?>
                  </td>
                </tr>

                <?php endforeach;?>
              </tbody>
              </table>
              </div>

              <div class="box-footer clearfix">
                <?=$pages?>
              </div>

          </div><!-- tab-pane -->
          </div><!-- tab-content -->

      </div><!-- nav-tabs-custom -->
    </div>
  </div>

</form>
</section><!-- /.content -->

<div class="modal" id="actionModel">
  <div class="modal-dialog">
    <form id="shipform" method="post" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">Modal Default</h4>
      </div>
      <div class="modal-body">
        <p>One fine body…</p>
      </div>
      <div class="modal-footer">
        <input type="hidden" name="action" value="ship">
        <input type="hidden" name="id" id="id">

        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">处理该订单</button>
      </div>
    </div><!-- /.modal-content -->
    </form>
  </div><!-- /.modal-dialog -->
</div>

<div class="modal" id="shipModel">
  <div class="modal-dialog">
    <form role="form" method="post" enctype="multipart/form-data">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">批量发货步骤说明</h4>
      </div>
      <div class="modal-body">
        <ol>
          <li>下载待发货订单 CSV 文件</li>
          <li>发货后将物流信息补充到 CSV 文件的最后两列中</li>
          <li>上传 CSV 文件，完成批量发货</li>
        </ol>

        <input type="file" id="csv" name="csv" accept="text/csv" class="form-control">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">上传 CSV 文件</button>
      </div>
    </div><!-- /.modal-content -->
    </form>
  </div><!-- /.modal-dialog -->
</div>

<script>
$('#actionModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var id = button.data('id')
  var name = button.data('name')
  var man = button.data('linkman')
  var addr = button.data('addr')
  var form = '<div class="form-group"><label for="shiptype">快递公司：</label><input name="shiptype" maxlength="6" type="text" value="<?=$_SESSION['mdta']['shiptype']?>"></div>'
  form += '<div class="form-group"><label for="shiptype">快递单号：</label><input name="shipcode" maxlength="20" type="text" value="<?=$_SESSION['mdta']['shipcode']?>"></div>'

  $('#id').val(id)

  var modal = $(this)
  modal.find('.modal-title').text(name)
  modal.find('.modal-body').html('<div class="form-group"><label>OpenID：</label>'+ button.data('openid') + '</div><div class="form-group"><label>收货人：</label>'+ man +'</div><div class="form-group"><label>收货地址：</label>'+ addr + '</div>' + form)
  //modal.find('form').attr('action', '/mdta/orders/ship/' + id + '?p=<?=$_GET['p']?>')
})
</script>
