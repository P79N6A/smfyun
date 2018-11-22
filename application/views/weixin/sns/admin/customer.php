
<style>
.label {font-size: 14px}
</style>

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
// if ($result['fuser']) $title = $result['fuser']->nickname.'的下线';
// if ($result['ffuser']) $title=$result['ffuser']->nickname.'的二级';
// if ($result['id'] || $result['s']) $title = '搜索结果';
// if ($result['ticket']) $title = '已生成海报';
?>

<section class="content-header">
  <h1>
    用户
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/snsa/qrcodes">用户明细</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">


  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title"><?=$title?>：共 <?=$result['countall']?> 个用户</h3>
              <div class="box-tools">
              <form method="get" name="qrcodesform">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">

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
                  <td>状态</td>
                 <th>头像</th>
                  <th>昵称</th>
                  <!-- <th>OpenID</th> -->
                 <!--  <th nowrap=""><a href="/snsa/qrcodes?sort=money" title="按总收益排序">总收益</a></th>
                  <th nowrap=""><a href="/snsa/qrcodes?sort=score" title="按收益排序">未提现收益</a></th>
                  <th nowrap=""><a href="/snsa/qrcodes?sort=paid" title="按订单收入排序">订单收入</a></th> -->
                  <th>加入时间</th>
                  <th>性别</th>
                  <th>是否是团长</th>
                  <th>是否抽奖</th>
                  <th>操作</th>
                </tr>

                <?php
                foreach ($result['qrcodes'] as $v):
                ?>

                <tr>
                  <!-- <td id="lock<?//=$v->id?>"><?//=$v->id?></td> -->
                  <td id="lock">
                  <?php 
                  if ($v->locked==0){
                    echo '<span class="label label-success">正常</span>';
                  }else{
                    echo '<span class="label label-danger">锁定中</span>';
                  }
                  ?>
                  </td>
                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->nickname?></td>
                  <td><?=date('Y－m－d',$v->jointime)?></td>
                  <td><?=$sex[$v->sex]?></td>
                  <td id="lock">
                  <?php 
                  if ($v->flag>0){//为团长
                    $is_award=$v->oid1;
                    echo '<span class="label label-success">是</span>';
                  }else{
                    $is_award=$v->oid2;
                    echo '<span class="label label-danger">否</span>';
                  }
                  ?>
                  </td>
                  <td id="lock">
                   <?php 
                  if ($is_award>0){//
                    $is_award=$v->oid1;
                    echo '<span class="label label-success">是</span>';
                  }else{
                    echo '<span class="label label-danger">否</span>';
                  }
                  ?>
                  </td>
                  <td nowrap="">
                  <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>"  data-name="<?=$v->nickname?>" data-lock="<?=$v->locked?>">
                      <span>修改状态</span> <i class="fa fa-edit"></i>
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
        <h4 class="modal-title">修改状态</h4>
      </div>
      <div class="modal-body">&nbsp;</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">修改状态</button>
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
  var lock = button.data('lock')
  var form = "";
  form += '<div class="form-group"><label for="flock">用户状态：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock0" value="0"'+ (lock==0 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">正常</span></label> <label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock1" value="1"'+ (lock==1 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">锁定</label> ';
  // form += '<label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock3" value="3"'+ (lock==3 ? ' checked' : '') +'><span class="label label-warning" style="font-size:14px">白名单</label> <label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock4" value="4"'+ (lock==4 ? ' checked' : '') +'><span class="label label-info" style="font-size:14px">隐身用户</label>'
  form += '</div> <p class="help-block">1、锁定后不能参与活动<br /> </div>';
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';
  var modal = $(this);
  modal.find('.modal-title').text(name);
  modal.find('.modal-body').html(form);
})
</script>
