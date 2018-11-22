<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    补货申请
    <small><?//=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/ytya/stock">补货申请</a></li>
    <li class="active"><?//=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">
<form method="get" name="qrcodesform">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共<? if($result['total_results']) echo $result['total_results'];else echo 0;?> 条补货申请</h3>
         <!--      <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div> -->
            </div><!-- /.box-header -->
            <?php

            ?>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>是否s级经销商</th>
                  <th>经销商等级</th>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>受理经销商</th>
                  <th>补货金额</th>
                  <th>操作</th>
                </tr>
                <?php
                if($result['stocks']==null)
                  $result['stocks']=array();
                foreach ($result['stocks'] as $v)
                {
                ?>
                <tr>
                  <td><?=$v->fqid? '否':'是'?></td>
                  <td><?=$v->qrcode->agent->skus->name?></td>
                  <td><img src="<?=$v->qrcode->headimgurl?>" width="32" height="32"></td>
                  <td><?=$v->qrcode->nickname?></td>
                  <td><?=$v->fqrcode->nickname?></td>
               	  <td><?=$v->money?></td>
                  <?php if(!$v->fqid):?>
                  <td nowrap="">
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-name="<?=$v->qrcode->nickname?>" data-money="<?=$v->money?>">
                      <span>审核</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                <?php endif;?>
                  <input type="hidden" value='<?=$goodid?>'>
                </tr>

                <?php }?>
              </tbody></table>
            </div><!-- /.box-body -->

              <div class="box-footer clearfix">
                <?=$pages?>
              </div>

            </div>

          </div>

    </div>

</form>
</section><!-- /.content -->

<div class="modal" id="actionModel">
  <div class="modal-dialog">
    <form id="shipform" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">修改佣金比例</h4>
      </div>
      <div class="modal-body">&nbsp;</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">确定</button>
      </div>
    </div><!-- /.modal-content -->
    </form>
  </div><!-- /.modal-dialog -->
</div>

<script>
$('#actionModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
  var id = button.data('id');
  var name=button.data('name');
  var money=button.data('money');
  var information=name+"  (补货金额:"+money+"元)";
  var form = '';
  form += '<div class="form-group"><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock0" value="1"><span class="label label-success" style="font-size:14px">是</span></label><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock3" value="0"><span class="label label-danger" style="font-size:14px">否</label></div></div>';
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';
  var modal = $(this);
  modal.find('.modal-title').text(information);
  modal.find('.modal-body').html(form);
})
</script>
