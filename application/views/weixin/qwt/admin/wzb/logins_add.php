
<section class="content-header">
  <h1>
    账号管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/qfxa/items">账号管理</a></li>
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

        <div class="col-lg-3 col-sm-6">
        <div class="form-group">
          <label for="startdate">账号到期时间</label>
          <div class="input-group">
            <input type="text" class="form-control pull-right formdatetime" name="data[expiretime]" value="<?=$_POST['data']['expiretime']?>" readonly="">
            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
          </div>
        </div>
        </div>
        
      <!--   <div class="col-lg-5">
          <div class="form-group">
          <label for="user">规格</label>
        <div class="radio">
          <label class="checkbox-inline">
            <input type="radio" name="data[guige]" id="rsync1" value="1">
            <span class="label label-success"  style="font-size:14px">包月(500元送100G流量)</span>
          </label>
          <label class="checkbox-inline">
            <input type="radio" name="data[guige]" id="rsync0" value="2">
            <span class="label label-danger"  style="font-size:14px">包年(5000元送1000G流量)</span>
          </label>
          </div>
        </div>
        </div> -->
      </div>
      <div class="row">
        <div class="col-lg-5">
          <div class="form-group">
          <label for="user">用户名（登录用）</label>
          <input type="text" maxlength="20" class="form-control" id="user" name="data[user]" placeholder="输入登录用户名" value="<?=htmlspecialchars($_POST['data']['user'])?>">
          <input type="hidden" id="fadmin" name="data[fadmin]" value=<?=$biz->id?> >
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-5">
          <div class="form-group">
            <label for="pass">密码（为空则为不修改密码）</label>
            <input type="text" maxlength="20" class="form-control" id="pass" name="pass" placeholder="输入登录密码">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-5">
          <div class="form-group">
            <label for="name">商户名称（备注用，前台不会显示）</label>
            <input type="text" maxlength="20" class="form-control" id="stock" name="data[name]" placeholder="输入商户名称" value="<?=trim($_POST['data']['name'])?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-5">
          <div class="form-group">
            <label for="name">赠送流量（单位：GB）</label>
            <input type="number" maxlength="20" class="form-control" id="stock" name="data[stream_data]" placeholder="" value="<?=trim($_POST['data']['stream_data'])?>">
          </div>
        </div>
      </div>
      <label class="goaltitle">选择经销商:</label>
      <select name="data[faid]" class="input-group goalb">
        <option value="<?=$biz->id?>" >直销</option>
        <?php foreach ($admins as $admin): ?>
          <option value="<?=$admin->id?>"  <?=$_POST['data']['faid']==$admin->id?"selected":"" ?> ><?=$admin->name?></option>
        <?php endforeach ?>
       </select>
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
        <a href="/qfxa/items/edit/<?=$result['item']['id']?>?DELETE=1" class="btn btn-outline">确认删除</a>
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

