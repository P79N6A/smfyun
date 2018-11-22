<?php
if(!$_POST['data']['starttime']){
  $starttime = time();
}else{
  $starttime = $_POST['data']['starttime'];
}
if(!$_POST['data']['endtime']){
  $endtime = time();
}else{
  $endtime = $_POST['data']['endtime'];
}
?>
<section class="content-header">
  <h1>
    二维码管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/whba/qrs">二维码管理</a></li>
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
  <form role="form" method="post" enctype="multipart/form-data" >
    <div class="box-body">

      <div class="row">
        <div class="col-lg-3 col-sm-6">
        <div class="form-group">
          <label for="startdate">开始时间（为空则不限制）</label>
          <div class="input-group">
            <input type="text" class="form-control pull-right formdatetime" name="data[starttime]" value="<?=date('Y-m-d H:i:s',$starttime)?>" readonly="">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
          </div>
        </div>
        </div>
        <div class="col-lg-3 col-sm-6">
        <div class="form-group">
          <label for="startdate">截止时间（为空则不限制）</label>
          <div class="input-group">
            <input type="text" class="form-control pull-right formdatetime" name="data[endtime]" value="<?=date('Y-m-d H:i:s',$endtime)?>" readonly="">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
          </div>
        </div>
        </div>

      </div>

      <div class="form-group">
        <label for="name">名称</label>
        <input type="text" class="form-control" id="name" name="data[name]" placeholder="输入产品名称" value="<?=htmlspecialchars($_POST['data']['name'])?>">
      </div>


      <div class="row">

        <div class="col-lg-2 col-sm-4">
        <div class="form-group">
          <label for="stock">剩余数量</label>
          <input type="number" class="form-control" id="stock" name="data[stock]" placeholder="输入库存数量" value="<?=intval($_POST['data']['stock'])?>">
        </div>
        </div>

        <div class="col-lg-3 col-sm-4">
        <div class="form-group">
          <label for="price">最小红包金额（单位:分，最少100分）</label>
          <input type="number" class="form-control" id="price" name="data[minprice]" placeholder="" value="<?=$_POST['data']['minprice']?>">
        </div>
        </div>
        <div class="col-lg-4 col-sm-4">
        <div class="form-group">
          <label for="price">最大红包金额（单位:分，最大20000分）</label>
          <input type="number" class="form-control" id="price" name="data[maxprice]" placeholder="" value="<?=$_POST['data']['maxprice']?>">
        </div>
        </div>



      </div>


    <div class="box-footer">
      <button type="submit" class="btn btn-success"><i class="fa fa-edit"></i> <?=$result['title']?></button>
      <?php if ($result['action'] == 'edit'):?>
      <a href="#" class="btn btn-danger" style="margin-left:10px" id="delete" data-toggle="modal" data-target="#deleteModel"><i class="fa fa-remove"></i> <span>删除该二维码</span></a>
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
        <a href="/whba/qrs/edit/<?=$result['qr']['id']?>?DELETE=1" class="btn btn-outline">确认删除</a>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
$(function () {
  $(".formdatetime").datetimepicker({
    format: "yyyy-mm-dd hh:ii",
    language: "zh-CN",
    minView: "0",
    autoclose: true
  });
});
</script>


