<?php
error_reporting(E_ALL^E_NOTICE^E_WARNING);
?>
<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    仓库中的商品
    <small><?//=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/wzba/qrcodes">店铺商品管理</a></li>
    <li class="active"><?//=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">
<form method="get" name="qrcodesform">

  <div class="row">
    <div class="col-xs-12">
      <a href="/wzba/setgoods1?refresh=1" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-refresh"></i> &nbsp; <span>刷新商品列表</span></a>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共<? if($result['countall']) echo $result['countall'];else echo 0;?> 种出售的商品</h3>
                 <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>缩略图</th>
                  <th>优先级</th>
                  <th>商品名称</th>
                  <th>价格</th>
                  <th>库存</th>
                  <th>是否上架</th>
                  <th>操作</th>
                </tr>
                <?php foreach ($result['goods'] as $k => $v):?>
                <tr>
                  <td><img src="<?=$v->pic?>" width="32" height="32"></td>
                  <td><?=$v->status?$v->priority:0?></td>
                  <td style=" width:30%;word-wrap:break-word;word-break:break-all;"><?=$v->title?></td>

                  <td><?=$v->price?></td>
                  <td><?=$v->num?></td>
                  <td><?=$v->status?'<span class="label label-success">是</span>':'<span class="label label-danger">否</span>'?></td>
                  <td nowrap="">
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-num_id="<?=$v->goodid?>" data-price="<?=$v->price?>" data-name="<?=$v->title?>" data-priority="<?=$v->priority?>" data-status="<?=$v->status?>" data-pic="<?=$v->pic?>" data-url="<?=$v->url?>" data-price="<?=$v->price?>">
                      <span>修改</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                  <input type="hidden" value='<?=$goodid?>'>
                </tr>
              <?php endforeach?>
              </tbody></table>
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
        <h4 class="modal-title">是否上架直播</h4>
      </div>
      <div class="modal-body">&nbsp;</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">确认修改</button>
      </div>
    </div><!-- /.modal-content -->
    </form>
  </div><!-- /.modal-dialog -->
</div>

<script>
$('#actionModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
  var id = button.data('num_id');
  var name = button.data('name');
  var price=button.data('price');
  var priority=button.data('priority');
  var status=button.data('status');
  var url=button.data('url');
  var price=button.data('price');
  var pic=button.data('pic');
  var information=name+"  (价格:"+price+"元)";


  var form = '';
  form+='<div class="form-group"><label for="fscore">直播商品展示优先级（数字越大越靠前）</label><input class="form-control" id="fscore" name="form[priority]" max="999" type="number" style="width:150px" value="'+priority+'"></div><div class="form-group"><label for="fscore">是否上架直播</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock0" value="1"'+ (status==1 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">是</span></label><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock3" value="0" '+ (status==0 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">否</label></div></div>';
 // form += '<div class="form-group"><label for="flock">用户状态（加入白名单后不会再自动锁定）：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock0" value="0"'+ (lock==0 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">正常</span></label> <label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock1" value="1"'+ (lock==1 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">已锁定</label> <label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock3" value="3"'+ (lock==3 ? ' checked' : '') +'><span class="label label-warning" style="font-size:14px">白名单</label></div></div>';
  form += '<input type="hidden" name="form[num_iid]" value="'+ id +'">';
  form += '<input type="hidden" name="form[title]" value="'+ name +'">';
  form += '<input type="hidden" name="form[url]" value="'+ url +'">';
  form += '<input type="hidden" name="form[price]" value="'+ price +'">';
  form += '<input type="hidden" name="form[pic]" value="'+ pic +'">';
  var modal = $(this);
  modal.find('.modal-title').text(information);
  modal.find('.modal-body').html(form);
})
</script>
