<script src="http://cdn.bootcss.com/jquery/2.0.1/jquery.js"></script>
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
<section class="wrapper" style="width:85%;float:right;background:#eff0f4">
  <h3>
    兑换记录
    <small><?=$desc?></small>
  </h3>

<?php
if ($result['qrcode']) $title = $result['qrcode']->nickname . '的兑换明细 / ';

if ($result['status'] == 0) $title .= '未处理';
if ($result['status'] == 1) $title .= '已处理';
if ($result['status'] == 1){
  $activetype = 'done';
}
?>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/qwtwdba/orders">兑换记录</a></li>
    <li class="active"><?=$title?></li>
  </ol>


<!-- Main content -->

<div class="wrapper" style="background:white">
  <div class="row">
    <div class="col-md-12">
      <a href="#" data-toggle="modal" data-target="#shipModel" class="csv btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-shopping-cart"></i> &nbsp; <span>实物奖品批量发货</span></a>
      <a href="<?=$_SERVER['PATH_INFO']?>?qid=<?=$result['qid']?>&amp;export=csv&tag=<?=$activetype?>" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出全部未处理订单</span></a>

    </div>
    <li class="pull-right" style="list-style:none;margin-right:25px;margin-bottom:10px">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="模糊搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </li>
  </div>


  <div class="row">
    <div class="col-lg-12">

    <?php if ($result['ok']):?>
      <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i><?=$result['ok']?></div>
    <?php endif?>

      <div class="nav-tabs-custom">

          <form method="get" name="ordersform">
            <header class="panel-heading custom-tab dark-tab">
            <ul class="nav nav-tabs">
              <li id="orders<?=$result['status']?>" class="<?=$result['status'] == 0 ? 'active' : ''?>"><a href="/qwtwdba/orders?qid=<?=$result['qid']?>">未处理订单</a>
              </li>
              <li id="orders<?=$result['status']?>" class="<?=$result['status'] == 1 ? 'active' : ''?>"><a href="/qwtwdba/orders/done?qid=<?=$result['qid']?>">已处理订单</a></li>



            </ul>
          </header>
          </form>
          <?php if ($result['status'] == 0 ):?>
          <div style="clear:both">
          <span tag='total' class='tag <?=$activetype == 'total' ? 'tactive' : ''?>'><a href="/qwtwdba/orders?qid=<?=$result['qid']?>&type=all">全部</a></span>
          <span tag='object' class='tag <?=$activetype == 'object' ? 'tactive' : ''?>'><a href="/qwtwdba/orders?qid=<?=$result['qid']?>&type=object">实物</a></span>
          <span tag='fare' class='tag <?=$activetype == 'fare' ? 'tactive' : ''?>'><a href="/qwtwdba/orders?qid=<?=$result['qid']?>&type=fare">话费/充值</a></span>
        </div>
          <div class="tab-content">
          <?php endif?>

          <div class="tab-pane active" id="orders<?=$result['status']?>">



            <div class="table-responsive">
            <form method="post" method="post">
            <table class="table table-striped">
              <tbody>
                <tr>
                <th>头像</th>
                <th>昵称</th>
                <th>收货人</th>
                <th>手机</th>
                <th>收货地址</th>
                <th>金额</th>
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
                    <a href="/qwtwdba/qrcodes?id=<?=$order->user->id?>"><?=$order->user->nickname?></a>
                  </td>
                  <td nowrap=""><?=$order->name?></td>
                  <td nowrap=""><?=$order->tel?></td>
                  <td><?=$order->city.' '.$order->address?></td>
                  <th><?=$order->item->price?></th>
                  <td><?=$order->memo?></td>
                  <td nowrap=""><?=date('m-d H:i', $order->createdtime)?></td>
                  <td><?=$order->item->name?></td>
                  <td><?=$order->score?></td>
                  <td nowrap="">
                    <?php if ($result['status'] == 0):?>
                    <a href="#myModal" class="edit" data-toggle="modal"  data-openid="<?=$order->user->openid?>" data-name="<?=$order->item->name?>" data-id="<?=$order->id?>" data-linkman="<?=$order->name?>" data-tag="<?=$order->type?>" data-tel="<?=$order->tel?>" data-addr="<?=$order->city.' '.$order->address?>" >
                      <span>处理</span> <i class="fa fa-check-square"></i>
                    </a>
                    <input type="hidden" name="id[]" value="<?=$order->id?>">
                    <?php endif?>
                  </td>
                </tr>

                <?php endforeach;?>
                <script type="text/javascript">
                    $('.edit').click(function(){
                      if($(this).data('addr')==' '){
                        $(".openid").text($(this).data('openid'));
                        $('.cp').hide();
                        $('#id').val($(this).data('id'));
                        $(".man").text($(this).data('linkman'));
                        $('.addresstel').html('<strong>手机号:<span class="addr">'+$(this).data('tel')+'</span></strong>');
                      }else{
                        $('.cp').show();
                        $('#id').val($(this).data('id'));
                        $(".openid").text($(this).data('openid'));
                        $(".addr").text($(this).data('addr'));
                        $(".man").text($(this).data('linkman'));
                      }
                    })
                </script>
                <input type="hidden" name="action" value="oneship">

              </tbody>
              </table>
              <?php if ($result['status'] == 0 && $order->id):?>
              <button type="submit" class="btn btn-success pull-right">一键处理本页订单</button>
              <?php endif?>
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
        <!-- <input type="hidden" name="id" id="id"> -->

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
<!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">姓名</h4>
                </div>

<div class="modal-body row">
<form method="post" >
    <div class="col-md-9 img-modal">
      <p class="mtop10"><strong>openID:<span class="openid"></span></strong></p>
      <p ><strong>收货人:</strong><span class="man"></span> </p>
      <p class='addresstel'><strong>收货地址:<span class="addr"></span></strong></p>
      <p class='cp'><strong>快递公司:</strong>
      <span class='cp'><input  value="<?=$_SESSION['qwtwdba']['shiptype']?>" class="form-control" name="shiptype"></span>
      </p>
      <p class='cp'><strong>快递单号:</strong>
      <span class='cp'><input  value="<?=$_SESSION['qwtwdba']['shipcode']?>" class="form-control" name="shipcode"></span>
      </p>
    </div>
    <input type="hidden" name="action" value="ship">
    <input type="hidden" name="id" id="id">
<div class="col-md-7">

<button class="btn btn-danger btn-sm" type="button" data-dismiss="modal">取消</button>
<button class="btn btn-success btn-sm" type="submit">处理该订单</button>

</div>
</form>
</div>

</div>
</div>
</div>
<!-- modal -->
<script>
$(document).ready(function(){
  var type = '<?=$activetype?>';
  switch (type)
  {
    case "total":
    var str = '全部未';
    break;
    case "object":
    var str = '实物奖品未';
    break;
    case "fare":
    var str = '话费/充值未';
    break;
    case "code":
    var str = '优惠码未';
    break;
    case "done":
    var str = '全部已';
    break;
  }
  $('.row').children().children().eq(1).children('span').html('导出'+str+'处理订单');
      if(type!=='object'){
        $('.csv').css('display','none');
      }
})

$('#actionModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var id = button.data('id')
  var name = button.data('name')
  var man = button.data('linkman')
  var addr = button.data('addr')
  var tag = button.data('tag')
  var tel = button.data('tel')
  var form = '<div class="form-group"><label for="shiptype">快递公司：</label><input name="shiptype" maxlength="6" type="text" value="<?=$_SESSION['qwtwdba']['shiptype']?>"></div>'
  form += '<div class="form-group"><label for="shiptype">快递单号：</label><input name="shipcode" maxlength="20" type="text" value="<?=$_SESSION['qwtwdba']['shipcode']?>"></div>'

  $('#id').val(id)

  var modal = $(this)
  modal.find('.modal-title').text(name)
  if(tag==3){
    modal.find('.modal-body').html('<div class="form-group"><label>OpenID：</label>'+ button.data('openid') + '</div><div class="form-group"><label>收货人：</label>'+ man +'</div><div class="form-group"><label>手机号：</label>'+ tel + '</div>' )
  //modal.find('form').attr('action', '/wdba/orders/ship/' + id + '?p=<?=$_GET['p']?>')
  }else{
    modal.find('.modal-body').html('<div class="form-group"><label>OpenID：</label>'+ button.data('openid') + '</div><div class="form-group"><label>收货人：</label>'+ man +'</div><div class="form-group"><label>收货地址：</label>'+ addr + '</div>' + form)
  //modal.find('form').attr('action', '/wdba/orders/ship/' + id + '?p=<?=$_GET['p']?>')
  }

})
</script>
