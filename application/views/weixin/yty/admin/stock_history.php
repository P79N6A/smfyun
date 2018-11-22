<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    补货记录
    <small><?//=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/ytya/qrcodes">补货记录</a></li>
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
              <h3 class="box-title">共<? if($result['total_results']) echo $result['total_results'];else echo 0;?> 条补货记录</h3>
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
            $config['kaiguan_needpay']=ORM::factory('yty_cfg')->where('bid','=',$bid)->where('key','=','kaiguan_needpay')->find()->value;
              $sku_num=ORM::factory('yty_sku')->where('bid','=',$bid)->count_all();
            ?>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>是否s级经销商</th>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>受理经销商</th>
                  <th>金额</th>
                </tr>
                <?php
                if($result['stocks']==null)
                  $result['stocks']=array();
                foreach ($result['stocks'] as $v)
                {
                ?>
                <tr>
                  <td><?=$v->fqid? '否':'是'?></td>
                  <td><img src="<?=$v->qrcode->headimgurl?>" width="32" height="32"></td>
                  <td><?=$v->qrcode->nickname?></td>
                  <td><?=$v->fqrcode->nickname?></td>
                  <td><?=$v->money?></td>
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
  var id = button.data('num_id');
  var name = button.data('name');
  var price=button.data('price');
  var money0=button.data('money0');
  var money1=button.data('money1');
  var money2=button.data('money2');
  var money3=button.data('money3');
  var money4=button.data('money4');
  var status=button.data('status');
  var pic=button.data('pic');
  var url=button.data('url');
  var information=name+"  (价格:"+price+"元)";
  var form = '';
  form+='<div class="form-group"><label for="fscore">一级经销商进货价</label><input class="form-control" id="fscore" name="form[money0]" max="999" type="number" style="width:150px" value="'+money0+'"></div>';
  <?php if($sku_num>=2): ?>
  form+='<div class="form-group"><label for="fscore">二级经销商进货价</label><input class="form-control" id="fscore" name="form[money1]" max="999" type="number" style="width:150px" value="'+money1+'"></div>';
  <?php endif?>
  <?php if($sku_num>=3): ?>
  form+='<div class="form-group"><label for="fscore">三级经销商进货价</label><input class="form-control" id="fscore" name="form[money2]" max="999" type="number" style="width:150px" value="'+money2+'"></div>';
  <?php endif?>
  <?php if($sku_num>=4): ?>
  form+='<div class="form-group"><label for="fscore">四级经销商进货价</label><input class="form-control" id="fscore" name="form[money3]" max="999" type="number" style="width:150px" value="'+money3+'"></div>';
  <?php endif?>
  <?php if($sku_num>=5): ?>
  form+='<div class="form-group"><label for="fscore">五级经销商进货价</label><input class="form-control" id="fscore" name="form[money4]" max="999" type="number" style="width:150px" value="'+money4+'"></div>';
  <?php endif?>
  form += '<div class="form-group"><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock0" value="1"><span class="label label-success" style="font-size:14px">是</span></label><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock3" value="0"><span class="label label-danger" style="font-size:14px">否</label></div></div>';
 // form += '<div class="form-group"><label for="flock">用户状态（加入白名单后不会再自动锁定）：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock0" value="0"'+ (lock==0 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">正常</span></label> <label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock1" value="1"'+ (lock==1 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">已锁定</label> <label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock3" value="3"'+ (lock==3 ? ' checked' : '') +'><span class="label label-warning" style="font-size:14px">白名单</label></div></div>';
  form += '<input type="hidden" name="form[num_iid]" value="'+ id +'">';
  form += '<input type="hidden" name="form[title]" value="'+ name +'">';
  form += '<input type="hidden" name="form[pic]" value="'+ pic +'">';
  form += '<input type="hidden" name="form[url]" value="'+ url +'">';
  form += '<input type="hidden" name="form[price]" value="'+ price +'">';
  var modal = $(this);
  modal.find('.modal-title').text(information);
  modal.find('.modal-body').html(form);
})
</script>
