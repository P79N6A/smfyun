<style>
.label {font-size: 14px}
.button{
  border-radius: 4px;
  padding: 4px 8px;
  border-width: 1px;
  border-style: solid;
  margin-right: 4px;
  color: #fff;
}
.recover{
     background-color: #00d9ff;
    border-color: blue;
}
.edit{
      background-color: #00ce00;
    border-color: green;
}
.delete{
      border-color: red;
    background-color: #ff8686;
}
.send{
    background-color: #c7c700;
    border-color: #868600;
}
.cancel{
      background: #fff;
    color: #7b0000;
    border-color: #670000;
}
</style>
<section class="content-header">
  <h1>
    品牌库
    <small><?=$desc?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">品牌库</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <a href="/dgba/item_add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加品牌</span></a>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=count($result['items'])?> 件品牌</h3>
              <div class="box-tools">
               <form method="get" name="qrcodesform">
                <div class="input-group" style="width: 150px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按品牌名称搜索" value="<?=htmlspecialchars($result['s'])?>" >
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                </form>
              </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>品牌名称</th>
                  <th>备注</th>
                  <th>录入时间</th>
                  <th>操作</th>
                </tr>
                <?php foreach ($result['items'] as $key=>$item):?>
                <tr>
                  <td><?=$item->name?></td>
                  <td><?=$item->remark?></td>
                  <td><?=date('Y-m-d H:i:s',$item->createdtime)?></td>
                  <td nowrap="true">
                    <a href="/dgba/item_edit/<?=$item->id?>" class='button edit'>
                      <span>修改</span><i class='fa fa-edit'></i>
                    </a>
                    <a class='delete button' data-id="<?=$item->id?>">
                      <span>删除</span><i class='fa fa-trash'></i>
                    </a>
                  </td>
                </tr>
                <?php endforeach;?>
              </tbody></table>
            </div><!-- /.box-body -->
            <div class="box-footer clearfix">
                <?=$pages?>
            </div>
          </div>
    </div>
  </div>
</section><!-- /.content -->
<script src="/qwt/assets/js/sweetalert.min.js"></script>
<link rel="stylesheet" href="/qwt/assets/css/sweetalert.css">
<script type="text/javascript">
    $('.delete').click(function(){
      var id = $(this).data('id');
  swal({
    title: "确认要删除吗？",
    text: "该操作不可恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认删除',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/dgba/items?delete="+id;
    })
  })
</script>

