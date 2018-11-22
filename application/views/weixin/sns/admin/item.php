
<style>
.label {font-size: 14px}
</style>

<?php
// $sex[0] = '未知';
// $sex[1] = '男';
// $sex[2] = '女';

 $title = '概览';
 if ($result['id'] || $result['s']) $title = '搜索结果';
?>

<section class="content-header">
  <h1>
    奖品
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/snsa/qrcodes">奖品明细</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">


  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title"><?=$title?>：共 <?=$result['countall']?> 种奖品</h3>
              <div class="box-tools">
              <form method="get" name="qrcodesform">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按奖品名搜索" value="<?=htmlspecialchars($result['s'])?>">

                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                </form>
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <td>奖品名</td>
                 <th>总库存</th>
                  <th>剩余量</th>
                  <th>操作</th>
                </tr>

                <?php
                foreach ($result['items'] as $v):
                ?>

                <tr>
                  <td><?=$v->name?></td>
                  <td><?=$v->num?></td>
                  <td><?=$v->residue?></td>
                  <td nowrap="">
                  <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>"  data-name="<?=$v->name?>" data-num="<?=$v->num?>" data-residue="<?=$v->residue?>"
                      <span>修改库存</span> <i class="fa fa-edit"></i>
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
<div class="modal" id="actionModel">
  <div class="modal-dialog">
    <form id="shipform" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">修改库存</h4>
      </div>
      <div class="modal-body">&nbsp;</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">修改库存</button>
      </div>
    </div><!-- /.modal-content -->
    </form>
  </div><!-- /.modal-dialog -->
</div>

<script>
$('#actionModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
  var id = button.data('id');
  var name = button.data('name');
  var num = button.data('num');
  var residue=button.data('residue');
  var form = "";
  form+='<div class="form-group"><label for="fscore">总库存</label>'+num+'</div>';
  form+='<div class="form-group"><label for="fscore">剩余量</label><input class="form-control" id="fscore" name="form[residue]" type="number" step="1" style="width:150px" value='+residue+'></div>';
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';
  var modal = $(this);
  modal.find('.modal-title').text(name);
  modal.find('.modal-body').html(form);
})
</script>
