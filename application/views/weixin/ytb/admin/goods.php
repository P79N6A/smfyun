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
    商品审核
    <small><?=$desc?></small>
  </h1>


  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/ytba/setgoods">商品审核</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">




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
                <th>图片</th>
                <th>名称</th>
                <th>价格</th>
                <th>库存</th>
                <th>是否展示在推荐页</th>
                <th>审核</th>

              </tr>
                <?php foreach ($result['goods'] as $good):

                ?>
                <tr>

                  <td><img src="<?=$good->pic?>" width="32" height="32" title="<?=$good->id?>"></td>
                  <td><?=$good->name?></td>
                  <td><?=$good->price?></td>
                  <td><?=$good->num?></td>
                  <td>
                  <?php
                  if ($good->status == 0)
                    echo '<span class="label label-warning">不允许</span>';
                  if ($good->status == 1)
                    echo '<span class="label label-danger">允许</span>';
                  ?>
                  </td>
                  <td nowrap="">
                  <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$good->id?>"  data-name="<?=$good->name?>" >
                      <span>审核商品</span> <i class="fa fa-edit"></i>
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
<div class="modal" id="actionModel">
  <div class="modal-dialog">
    <form id="shipform" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">是否审核通过该商品</h4>
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
  form += '<div class="form-group"><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock0" value="1"><span class="label label-success" style="font-size:14px">是</span></label><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock3" value="0"><span class="label label-danger" style="font-size:14px">否</label></div></div>'
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';

  var modal = $(this)
  //modal.find('.modal-title').text(name)
  modal.find('.modal-body').html(form)
})
</script>


