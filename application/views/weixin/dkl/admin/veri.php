<style>
.label {font-size: 14px}
</style>
<section class="content-header">
  <h1>
    核销员管理
    <small><?=$desc?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">核销员管理</li>
  </ol>
</section>
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <a href="/dkla/veri_add" class="btn btn-success pull-left" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加核销员</span></a>
    </div>
  </div>
 <?php if ($result['err']):?>
  <div class="alert alert-danger alert-dismissable"><?=$result['err']?></div>
 <?php endif?>
  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=count($result['veris'])?> 个核销员</h3>
              <!-- <div class="box-tools">
                <div class="input-group" style="width: 150px;">
                  <input type="text" name="table_search" class="form-control input-sm pull-right" placeholder="搜索">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div> -->
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>排序</th>
                  <th>核销员名称</th>
                  <th>核销标签</th>
                  <th>总核销数量</th>
                  <th>核销员状态</th>
                  <th>核销权限修改</th>
                </tr>
                <?php foreach ($result['veris'] as $key=>$veri):
                $num=ORM::factory('dkl_order')->where('bid','=',$bid)->where('status','=',1)->where('vid','=',$veri->id)->count_all();
                ?>
                <tr>
                  <td><?=$key+1?></td>
                  <td><?=$veri->name?></td>
                  <td><?=$veri->tag?></td>
                  <td><?=$num?></td>
                  <td>
                  <?php
                  if ($veri->flag==1)
                    echo '<span class="label label-success">正常</span>';
                  else
                    echo '<span class="label label-danger">锁定</span>';
                  ?>
                  </td>
                  <td><a href="#" id="delete" data-toggle="modal" data-id=<?=$veri->id?> data-name=<?=$veri->name?> data-tag=<?=$veri->tag?> data-tel=<?=$veri->tel?> data-flag=<?=$veri->flag?> data-target="#deleteModel"><span>修改</span></a></td>
                </tr>
                <?php endforeach;?>
              </tbody></table>
            </div>
              <div class="box-footer clearfix">
                <?=$pages?>
              </div>
          </div>
    </div>
  </div>
</section>
<div class="modal" id="deleteModel">
  <div class="modal-dialog">
  <form id="shipform" method="post">
    <div class="modal-content">
      <div class="modal-header">
      <h4 class="modal-title">核销权限修改</h4>
      </div>
      <div class="modal-body">
      <div class="modal-id">
      </div>
      <!-- <div class="radio">
            <label class="checkbox-inline">
              <input type="radio" name="form[switch]" id="rsync1" value="1" >
              <span class="label label-success"  style="font-size:14px">开启</span>
            </label>
            <label class="checkbox-inline">
              <input type="radio" name="form[switch]" id="rsync0" value="0" checked>
              <span class="label label-danger"  style="font-size:14px">关闭</span>
            </label>
        </div> -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">修改用户</button>
      </div>
  </div>
  </form>
</div>
</div>
<script>
$('#deleteModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var id = button.data('id')
  var name = button.data('name')
  var tag = button.data('tag')
  var tel = button.data('tel')
  var flag = button.data('flag')
  var modal = $(this)
  var form = '<input type="hidden" name="form[id]" value="'+ id +'">'
  form += '<div class="form-group"><label for="fscore">核销员名称：</label><input class="form-control" id="fscore" name="form[name]" value="'+name+'" type="text" style="width:150px"></div>'
   form += '<div class="form-group"><label for="fscore">核销员手机号：</label><input class="form-control" id="fscore" name="form[tel]" value="'+tel+'" type="text" style="width:150px"></div>'
    form += '<div class="form-group"><label for="fscore">核销员标签：</label><input class="form-control" id="fscore" name="form[tag]" value="'+tag+'"  type="text" style="width:150px"></div>'
    form += '<div class="radio"><label class="checkbox-inline"><input type="radio" name="form[switch]" id="rsync1" '+(flag==1?"checked":"")+' value="1" ><span class="label label-success"  style="font-size:14px">开启</span></label><label class="checkbox-inline"><input type="radio" '+(flag==0?"checked":"")+'name="form[switch]" id="rsync0" value="0"><span class="label label-danger"  style="font-size:14px">关闭</span></label></div>'
  modal.find('.modal-id').html(form);
})
</script>
