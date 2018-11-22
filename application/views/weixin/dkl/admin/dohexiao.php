<style>
.label {font-size: 14px}
</style>
<section class="content-header">
  <h1>
    我要核销
    <small><?=$desc?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/dkla/dohexiaos/<?=$bid?>">我要核销</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>
<section class="content">
<form method="get" name="qrcodesform">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=$result['countall']?> 个用户</h3>
              <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="请输入电话号码搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>姓名</th>
                  <th>电话号码</th>
                  <th>兑换产品</th>
                  <th>兑换时间</th>
                  <th>核销状态</th>
                  <th>操作</th>
                </tr>
                <?php
                foreach ($result['orders'] as $v):
                ?>
                <tr>
                  <td><img src="<?=$v->user->headimgurl?>" width="32" height="32" title="<?=$v->user->openid?>"></td>
                  <td><?=$v->user->nickname?></td>
                  <?php
                  $cksum = md5($v->user->openid.$config['appsecret'].date('Y-m-d'));
                  $url = '/dkl/index/'. $v->bid .'?url=score&cksum='. $cksum .'&openid='. base64_encode($v->user->openid);
                  ?>
                  <td><?=$v->name?></td>
                  <td><?=$v->tel?></td>
                  <td><?=$v->item->name?></td>
                  <td><?=date('m-d H:i', $v->createdtime)?></td>
                  <td id="subscribe<?=$v->id?>">
                    <?php
                    if($v->status==0)
                      echo '<span class="label label-danger">未核销</span>';
                    elseif($v->status==1)
                      echo '<span class="label label-success">已核销</span>';
                    ?>
                  </td>
                  <?php if($v->status!=1):?>
                  <td nowrap="">
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-name="<?=$v->user->nickname?>">
                      <span>核销</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                <?php endif;?>
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
  </div>
</form>
</section><!-- /.content -->
<div class="modal" id="actionModel">
  <div class="modal-dialog">
    <form id="shipform" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">核销</h4>
      </div>
      <div class="modal-body">&nbsp;</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">核销</button>
      </div>
    </div><!-- /.modal-content -->
    </form>
  </div><!-- /.modal-dialog -->
</div>
<script>
$('#actionModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var id = button.data('id')
  var name = button.data('name')
  var form = '</div> <p class="help-block">确认后无法修改</p> </div>'
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';
  var modal = $(this)
  modal.find('.modal-body').html(form)
})
</script>
