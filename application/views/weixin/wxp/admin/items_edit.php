
<section class="content-header">
  <h1>
    红包规则管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/wxpa/items">红包规则管理</a></li>
    <li class="active"><?=$result['title']?></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

<?php if ($result['error']):?>
  <div class="alert alert-danger alert-dismissable"><?=$result['error']?></div>
<?php endif?>

<div class="row">
<div class="col-lg-12">

<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title"><?=$result['title']?></h3>
  </div><!-- /.box-header -->
  <!-- form start -->
  <form role="form" method="post" enctype="multipart/form-data">
    <div class="box-body">

      <div class="row">
        <div class="col-lg-5">
          <div class="form-group">
          <label for="user">发送规则名称</label>
          <input type="text" maxlength="20" class="form-control" id="user" name="data[name]" placeholder="" value="<?=$result['item']->name?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-5">
          <div class="form-group">
            <label for="money">发送金额最小值（单位：分，最少100分）</label>
            <input type="number" min='100' maxlength="20" class="form-control" id="money" name="data[moneymin]" value="<?=$result['item']->moneymin?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-5">
          <div class="form-group">
            <label for="money">发送金额最大值（单位：分，最少100分）</label>
            <input type="number" min='100' maxlength="20" class="form-control" id="money" name="data[money]" value="<?=$result['item']->money?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-5">
          <div class="form-group">
            <label for="money">领取概率（最小值为0，最大值为100）</label>
            <input type="number" min='0' max='100' maxlength="20" class="form-control" id="rate" name="data[rate]" value="<?=$result['item']->rate?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-5">
          <div class="form-group">
            <label for="name">红包数量</label>
            <input disabled="true" type="number" maxlength="20" class="form-control" id="num" name="data[num]" placeholder="" value="<?=$result['item']->num?>">
          </div>
        </div>
      </div>
      <input type="hidden" name='data[id]' value="<?=$result['item']->id?>">

    </div><!-- /.box-body -->

    <div class="box-footer">
      <button type="submit" class="btn btn-success"><i class="fa fa-edit"></i> <?=$result['title']?></button>

      <?php if ($result['action'] == 'edit'):?>
        <!-- <a href="#" class="btn btn-danger" style="margin-left:10px" id="delete" data-toggle="modal" data-target="#deleteModel"><i class="fa fa-remove"></i> <span>删除该奖品</span></a> -->
      <?php endif?>

    </div>
  </form>
</div>

</div>
</div>

</section><!-- /.content -->

<div class="modal modal-danger" id="deleteModel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">确认要删除吗？该操作不可恢复！</h4>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-dismiss="modal">取消</button>
        <a href="/wxpa/items/edit/<?=$result['item']['id']?>?DELETE=1" class="btn btn-outline">确认删除</a>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
$(function () {
  $(".formdatetime").datetimepicker({
    format: "yyyy-mm-dd",
    language: "zh-CN",
    autoclose: true,
    minView:'month',
    todayBtn:true
  });

});
</script>

