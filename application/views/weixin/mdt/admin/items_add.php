
<section class="content-header">
  <h1>
    商品管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/mdta/items">团购商品管理</a></li>
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

      <div class="form-group">
        <label for="show">商品类别</label>
        <div class="radio">
          <label class="checkbox-inline"><input type="radio" name="data[tid]" id="type1" value="1"<?=$_POST['data']['tid'] == 1 || !$_POST['data']['tid'] ? ' checked=""' : ''?>><span class="label label-success" style="font-size:14px">聚划算</span></label>
          <label class="checkbox-inline"><input type="radio" name="data[tid]" id="type2" value="2"<?=$_POST['data']['tid'] == 2 ? ' checked=""' : ''?>><span class="label label-success" style="font-size:14px">9.9 包邮</label>
          <label class="checkbox-inline"><input type="radio" name="data[tid]" id="type3" value="3"<?=$_POST['data']['tid'] == 3 ? ' checked=""' : ''?>><span class="label label-success" style="font-size:14px">1 元秒杀</label>
        </div>
      </div>

      <div class="row">

        <div class="col-lg-3 col-sm-6">
        <div class="form-group">
          <label for="starttime">商品上架时间</label>
          <div class="input-group">
            <input type="text" class="form-control pull-right formdatetime" name="data[starttime]" value="<?=$_POST['data']['starttime'] ? date('Y-m-d H:i:s', $_POST['data']['starttime']) : ''?>" readonly="">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
          </div>
        </div>
        </div>

      </div>

      <div class="row">

        <div class="col-lg-3 col-sm-6">
        <div class="form-group">
          <label for="endtime">商品下架时间</label>
          <div class="input-group">
            <input type="text" class="form-control pull-right formdatetime" name="data[endtime]" value="<?=$_POST['data']['endtime'] ? date('Y-m-d H:i:s', $_POST['data']['endtime']) : ''?>" readonly="">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
          </div>
        </div>
        </div>

      </div>

      <!--
      <div class="form-group">
        <label for="show">是否在列表中显示？</label>
        <div class="radio">
          <label class="checkbox-inline"><input type="radio" name="data[show]" id="show1" value="1"<?=$_POST['data']['show'] == 1 || !$_POST['data']['show'] ? ' checked=""' : ''?>><span class="label label-success" style="font-size:14px">显示</span></label>
          <label class="checkbox-inline"><input type="radio" name="data[show]" id="show0" value="0"<?=$_POST['data']['show'] === "0" ? ' checked=""' : ''?>><span class="label label-danger" style="font-size:14px">隐藏</label>
        </div>
      </div>
      -->

      <!--
      <div class="row">
        <div class="col-lg-3 col-sm-5">
        <div class="form-group">
          <label for="pri">优先级（数字越大越靠前）</label>
          <input type="number" class="form-control" id="pri" name="data[pri]" placeholder="展示优先级" value="<?=intval($_POST['data']['pri'])?>">
        </div>
        </div>
      </div>
      -->

      <div class="form-group">
        <label for="name">商品名称</label>
        <input type="text" class="form-control" id="name" name="data[name]" placeholder="输入商品名称" value="<?=htmlspecialchars($_POST['data']['name'])?>">
      </div>

      <div class="form-group">

        <?php if ($result['action'] == 'edit' && $result['item']['pic']):?>
          <div class="form-group"><img class="img-thumbnail" src="/mdta/images/item/<?=$result['item']['id']?>.v<?=$result['item']['lastupdate']?>.jpg" width="100"></div>
          <label for="pic">商品图片（重新上传会覆盖原照片）</label>
        <?php else:?>
          <label for="pic">商品图片</label>
        <?php endif?>

          <input type="file" id="pic" name="pic" accept="image/jpeg" class="form-control">
          <input type="hidden" name="MAX_FILE_SIZE" value="102400" />
          <p class="help-block">JPEG 格式，规格为正方形，推荐 600*600px，最大不超过 200K</p>
      </div>

      <div class="form-group">
        <label for="url">有赞微商品购买链接</label>
        <input type="url" class="form-control" id="url" name="data[url]" placeholder="http://" value="<?=htmlspecialchars($_POST['data']['url'])?>">
      </div>

      <div class="row">

        <div class="col-lg-2 col-sm-4">
        <div class="form-group">
          <label for="price">商品团购价</label>
          <input type="text" class="form-control" id="price" name="data[price]" placeholder="团购价" value="<?=floatval($_POST['data']['price'])?>">
        </div>
        </div>

        <div class="col-lg-2 col-sm-4">
        <div class="form-group">
          <label for="price2">市场价</label>
          <input type="text" class="form-control" id="price2" name="data[price2]" placeholder="市场价" value="<?=intval($_POST['data']['price2'])?>">
        </div>
        </div>

      </div>

      <!--
      <div class="form-group">
        <label for="desc">详细介绍</label>
        <textarea class="textarea" id="desc" name="data[desc]" placeholder="" style="width: 100%; height: 200px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"><?=htmlspecialchars($_POST['data']['desc'])?></textarea>
      </div>
      -->

    </div><!-- /.box-body -->

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
        <a href="/mdta/items/edit/<?=$result['item']['id']?>?DELETE=1" class="btn btn-outline">确认删除</a>
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

