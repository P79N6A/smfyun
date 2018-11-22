<section class="content-header">
  <h1>
    添加返还规则
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/flba/skus">返还规则管理</a></li>
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
  <form role="form" method="post" enctype="multipart/form-data" onsubmit="return check(this.form)">
    <div class="box-body">

      <div class="row">
        <div class="col-lg-3 col-sm-5">
        <div class="form-group">
          <label for="pri">规则名称</label>
          <input type="text" class="form-control" id="pri" name="data[name]" placeholder="" value="<?=$_POST['data']['name']?>">
        </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-3 col-sm-5">
        <div class="form-group">
          <label for="pri">返还次数</label>
          <input type="number" class="form-control" id="pri" name="data[times]" placeholder="" value="<?=intval($_POST['data']['times'])?>" min='1'>
        </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-3 col-sm-5">
        <div class="form-group">
          <label for="pri">单笔订单金额起始区间</label>
          <input type="number" class="form-control" id="pri" name="data[start]" placeholder="" value="<?=intval($_POST['data']['start'])?>" min='1'>
        </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-3 col-sm-5">
        <div class="form-group">
          <label for="pri">单笔订单金额截止区间</label>
          <input type="number" class="form-control" id="pri" name="data[end]" placeholder="" value="<?=intval($_POST['data']['end'])?>" min='1'>
        </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-3 col-sm-5">
        <div class="form-group">
          <label for="pri">订单返还比例(%)</label>
          <input type="number" class="form-control" id="pri" name="data[scale]" placeholder="" value="<?=intval($_POST['data']['scale'])?>" max='100' min='1'>
        </div>
        </div>
      </div>

    <div class="box-footer">
      <button type="submit" class="btn btn-success"><i class="fa fa-edit"></i> <?=$result['title']?></button>
      <?php if ($result['action'] == 'edit'):?>
      <a href="#" class="btn btn-danger" style="margin-left:10px" id="delete" data-toggle="modal" data-target="#deleteModel"><i class="fa fa-remove"></i> <span>删除该奖品</span></a>
      <?php endif?>
    </div>
  </form>
</div>

</div>
</div>

</section><!-- /.content1 -->

<div class="modal modal-danger" id="deleteModel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">确认要删除吗？该操作不可恢复！</h4>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-dismiss="modal">取消</button>
        <a href="/flba/skus/edit/<?=$result['sku']['id']?>?DELETE=1" class="btn btn-outline">确认删除</a>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
