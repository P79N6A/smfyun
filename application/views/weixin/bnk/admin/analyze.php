<style>
.label {font-size: 14px}
th, td{
  text-align: center;
}
</style>

<section class="content-header">
  <h1>
  发送记录
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active"><a href="/wzba/analyze">发送记录</a></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">


  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=$result['countall']?> 条记录</h3>
              <form method="get" name="qrcodesform">
              <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
              </form>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                <th>红包ID</th>
                <th>状态</th>
                <th><a href="/bnka/analyze?sort=ts_num" title="按投诉人数排序">投诉</a></th>
                <th>发起的用户</th>
                <th>已领取红包个数/总个数</th>
                <th>已领取红包金额/总金额</th>
                <th>上传的头像</th>
                <th>操作</th>
                  </tr>
                <?php
                foreach ($result['order'] as $v):
                  // $used_money=DB::query(Database::SELECT,'SELECT SUM money as used_money from bnk_trades where bid =$v->bid and oid =$v->id')->execute()->as_array();
                  // $used_money=$used_money[0]['used_money'];
                ?>
                <tr>
                <td><?=$v->id?></td>
                <td>
                  <?php
                  if($v->status==1){
                      echo '<span class="label label-success">红包领取完毕</span>';
                  }elseif ($v->status==2) {
                    echo '<span class="label label-warning">红包活动进行中</span>';
                  }elseif ($v->status==3) {
                    echo '<span class="label label-danger">红包活动已过期</span>';
                  }elseif ($v->status==4) {
                    echo '<span class="label label-info">已手动结束</span>';
                  }
                  ?>
                </td>
                <td><?=$v->ts_num?></td>
                <td><a href="/bnka/qrcodes?qid=<?=$v->qrcode->id?>" title="查看红包发起人"><?=$v->qrcode->nickName?></a></td>
                <td><?=$v->used_num?>/<?=$v->num?></td>
                <td><?=$v->used_money?>/<?=$v->money?></td>
                <td><img src="/bnka/images/order/<?=$v->id?>.v<?=time()?>.jpg" width='40' height='40'></td>
                <?php if($v->status==2):?>
                  <td nowrap="">
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lock="<?=$v->status?>" data-name="<?=$v->qrcode->nickName?>">
                      <span>修改</span> <i class="fa fa-edit"></i>
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
</section><!-- /.content -->
</section><!-- /.content -->
<div class="modal" id="actionModel">
  <div class="modal-dialog">
    <form id="shipform" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">修改</h4>
      </div>
      <div class="modal-body">&nbsp;</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">修改</button>
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
  var status = button.data('lock')
  var form = '<div class="form-group"><label for="flock">是否结束活动：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock0" value="2"'+ (status==2 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">否</span></label> <label class="checkbox-inline"><input type="radio" name="form[status]" id="flock1" value="4"'+ (status==4 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">是</label> '
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';
  var modal = $(this)
  modal.find('.modal-title').text(name)
  modal.find('.modal-body').html(form)
})
</script>
