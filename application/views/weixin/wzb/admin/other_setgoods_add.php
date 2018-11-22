<section class="content-header">
  <h1>
    其他商品管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/wzba/items">其他商品管理</a></li>
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


      <div class="form-group">
        <label for="show">是否上架？</label>
        <div class="radio">
          <label class="checkbox-inline" checked="">
            <input type="radio" name="data[status]" id="show1" value="1"<?=$_POST['data']['status'] == 1 || !$_POST['data']['status'] ? ' checked=""' : ''?>>
            <span class="label label-success" style="font-size:14px">是</span>
          </label>
          <label class="checkbox-inline">
            <input type="radio" name="data[status]" id="show0" value="0"<?=$_POST['data']['status'] === "0" ? ' checked=""' : ''?>>
            <span class="label label-danger" style="font-size:14px">否
          </label>
        </div>
      </div>


      <div class="row">
        <div class="col-lg-3 col-sm-5">
        <div class="form-group">
          <label for="pri">优先级（数字越大越靠前）</label>
          <input type="number" class="form-control" id="priority" name="data[priority]" placeholder="展示优先级" value="<?=intval($_POST['data']['priority'])?>">
        </div>
        </div>
      </div>

      <div class="form-group">
        <label for="name">商品名称</label>
        <input type="text" class="form-control" id="title" name="data[title]" placeholder="输入商品名称" value="<?=htmlspecialchars($_POST['data']['title'])?>">
      </div>
      <div class="row">

        <div class="col-lg-2 col-sm-4">
        <div class="form-group">
          <label for="price">商品价格</label>
          <input type="number" class="form-control" id="price" name="data[price]" placeholder="市场价" value="<?=floatval($_POST['data']['price'])?>">
        </div>
        </div>
      </div>

      <div class="form-group">

        <?php if ($result['action'] == 'edit' && $result['item']['db_pic']):?>
          <div class="form-group"><img class="img-thumbnail" src="/wzba/dbimages/setgood/<?=$result['item']['id']?>.v<?=$result['item']['time']?>.jpg" width="100"></div>
          <label for="pic">商品图片（重新上传会覆盖原照片）</label>
        <?php else:?>
          <label for="pic">商品图片</label>
        <?php endif?>

          <input type="file" id="pic" name="pic" accept="image/jpeg" class="form-control">
          <input type="hidden" name="MAX_FILE_SIZE" value="102400" />
          <p class="help-block">JPEG 格式，规格为正方形，推荐 600*600px，最大不超过 200K</p>

      </div>
      <div class="row">

        <div class="col-lg-8 col-sm-8">
        <div class="form-group">
          <label for="price">商品购买地址</label>
          <input type="url" class="form-control" id="url" name="data[url]" placeholder="" value="<?=$_POST['data']['url']?>">
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
        <a href="/wzba/other_setgood/edit/<?=$result['item']['id']?>?DELETE=1" class="btn btn-outline">确认删除</a>
      </div>

    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


