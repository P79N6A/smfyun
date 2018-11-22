
<section class="content-header">
  <h1>
    添加代理商
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/qfxa/admins">代理商管理</a></li>
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
          <label for="user">用户名（登录用）</label>
          <input type="text" maxlength="20" class="form-control" id="user" name="data[user]" placeholder="输入登录用户名" value="<?=htmlspecialchars($_POST['data']['user'])?>">
          <input type="hidden" id="fadmin" name="data[fadmin]" value=<?=$biz->id?> >
          <input type="hidden" id="admin" name="data[admin]" value=<?=$biz->admin-1?> >
           <input type="hidden" id="flag" name="data[flag]" value=1 >
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
            <label for="name">代理商名称（显示用）</label>
            <input type="text" maxlength="20" class="form-control" id="stock" name="data[name]" placeholder="输入商户名称" value="<?=trim($_POST['data']['name'])?>">
          </div>
        </div>
      </div>
      <?php if ($result['action'] == 'add'):?>
       <div class="row">
        <div class="col-lg-5">
          <div class="form-group">
            <label for="pass">可开账号数量</label>
            <input type="number" maxlength="20" class="form-control" id="number" name="data[number]" >
          </div>
        </div>
      </div>
      <?php endif?>
      <?php if ($result['action'] == 'edit'):
      $all=ORM::factory('wzb_login')->where('fadmin','=',$chbiz->id)->count_all();
      $number=$chbiz->number-$all;
      ?>
       <div class="row">
        <div class="col-lg-5">
          <div class="form-group">
            <label for="pass">剩余可开账号数量</label>
            <input type="number" maxlength="20" class="form-control" id="number" name="data[number]" value="<?=$number?>" >
          </div>
        </div>
      </div>
      <?php endif?>
    </div>
    <div class="box-footer">
      <button type="submit" class="btn btn-success"><i class="fa fa-edit"></i> <?=$result['title']?></button>
      <?php if ($result['action'] == 'edit'):?>
        <a href="#" class="btn btn-danger" style="margin-left:10px" id="delete" data-toggle="modal" data-target="#deleteModel"><i class="fa fa-remove"></i> <span>删除该代理商</span></a>
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

