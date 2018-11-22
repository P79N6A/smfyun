<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    仓库中的商品
    <small><?//=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/myba/qrcodes">分销商品管理</a></li>
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
              <h3 class="box-title">共<? if($result['total_results']) echo $result['total_results'];else echo 0;?> 种出售的商品</h3>
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
            $config['kaiguan_needpay']=ORM::factory('myb_cfg')->where('bid','=',$bid)->where('key','=','kaiguan_needpay')->find()->value;
            ?>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>缩略图</th>
                  <th>商品名称</th>
                  <th>价格</th>
                  <th>库存</th>
                  <th>订单宝上的销量</th>
                  <th>自购佣金比例</th>
                  <th>一级佣金比例</th>
                  <th>二级佣金比例</th>
                  <?php if($bid==19):?>
                  <th>是否允许分享</th>
                  <?endif?>
                  <?php if($config['kaiguan_needpay']==1):?>
                  <th>三级佣金比例</th>
                  <?endif?>
                  <th>操作</th>
                </tr>

                <?php
                if($result['items']==null)
                  $result['items']=array();
                foreach ($result['items'] as $v)
                {
                  $mon=ORM::factory('myb_setgood')->where('goodid','=',$v['num_iid'])->where('bid','=',$bid)->find();
                   if($mon->id)
                   {
                   	$money0=$mon->money0;
                   	$money1=$mon->money1;
                   	$money2=$mon->money2;
                    $money3=$mon->money3;
                    $status=$mon->status;
                   }
                   else
                   {
                   	$monn = ORM::factory('myb_cfg')->getCfg($bid,1);
                   	$money0=$monn['money0'];
                   	$money1=$monn['money1'];
                   	$money2=$monn['money2'];
                    $money3=$monn['money3'];
                    $status=$monn['status'];
                   }
                   $goodid=$v['num_iid'];
                  $soldednum=DB::query(database::SELECT,"select sum(temp.num)as tonum from (SELECT myb_orders.* FROM `myb_trades`,myb_orders WHERE myb_orders.tid=myb_trades.tid and myb_trades.status!='TRADE_CLOSED' and myb_trades.status!='TRADE_CLOSED_BY_USER' and myb_trades.status!='NO_REFUND' and myb_orders.goodid=$goodid) as temp where temp.bid=$bid")->execute()->as_array();
                   	//var_dump($soldednum);
                ?>

                <tr>
                  <td><img src="<?=$v['pic_url']?>" width="32" height="32"></td>
                  <td style=" width:30%;word-wrap:break-word;word-break:break-all;"><?=$v['title']?></td>

                  <td><?=$v['price']?></td>
                  <td><?=$v['num']?></td>
                  <td><?=empty($soldednum[0]['tonum'])?0:$soldednum[0]['tonum']?><?//=$v['sold_num']?></td>

                  <td><?=$money0?></td>
               	  <td><?=$money1?></td>
                  <td><?=$money2?></td>
                  <?php if($config['kaiguan_needpay']==1):?><td><?=$money3?></td><?endif?>
                  <?php if($bid==19):?>
                  <td>
                  <?php
                  if ($status == 0)
                    echo '<span class="label label-warning">不允许</span>';
                  if ($status == 1)
                    echo '<span class="label label-danger">允许</span>';
                  ?>
                  </td>
                  <?endif?>
                  <td nowrap="">
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-num_id="<?=$v['num_iid']?>" data-price="<?=$v['price']?>" data-name="<?=$v['title']?>" data-pic="<?=$v['pic_url']?>" data-url="<?=$v['detail_url']?>" data-money0="<?=$money0?>" data-money1="<?=$money1?>" data-money2="<?=$money2?>" data-money3="<?=$money3?>" data-status="<?=$status?>">
                      <span>修改佣金</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
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
        <button type="submit" class="btn btn-primary">修改佣金比例</button>
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
  var pic= button.data('pic');
  var url=  button.data('url');
  var price=button.data('price');
  var money0=button.data('money0');
  var money1=button.data('money1');
  var money2=button.data('money2');
  var money3=button.data('money3');
  var status=button.data('status');
  var information=name+"  (价格:"+price+"元)";


  var form = '<div class="form-group"><label for="fscore">自购佣金比例（相对价格的百分比）</label><input class="form-control" id="fscore" name="form[money0]" max="999" type="number" style="width:150px" value="'+money0+'"></div>';
  form+='<div class="form-group"><label for="fscore">一级佣金比例（相对价格的百分比）</label><input class="form-control" id="fscore" name="form[money1]" max="999" type="number" style="width:150px" value="'+money1+'"></div>';
  form+='<div class="form-group"><label for="fscore">二级佣金比例（相对价格的百分比）</label><input class="form-control" id="fscore" name="form[money2]" max="999" type="number" style="width:150px" value="'+money2+'"></div>';
  <?php if($config['kaiguan_needpay']==1):?>
  form+='<div class="form-group"><label for="fscore">三级佣金比例（相对价格的百分比）</label><input class="form-control" id="fscore" name="form[money3]" max="999" type="number" style="width:150px" value="'+money3+'"></div>';
  <?endif?>
  <?php if($bid==19):?>
 form += '<div class="form-group"><label for="flock">商品审核（审核通过后用户可以分享）：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[status]" id="status0" value="0" '+ (status==0 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">不通过</span></label> <label class="checkbox-inline"><input type="radio" name="form[status]" id="flock1" value="1"'+ (status==1 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">通过</div></div>';
 <?endif?>
  form += '<input type="hidden" name="form[num_iid]" value="'+ id +'">';
  form += '<input type="hidden" name="form[title]" value="'+ name +'">';
  form += '<input type="hidden" name="form[pic]" value="'+ pic +'">';
  form += '<input type="hidden" name="form[price]" value="'+ price +'">';
  form += '<input type="hidden" name="form[url]" value="'+ url +'">';
  var modal = $(this);
  modal.find('.modal-title').text(information);
  modal.find('.modal-body').html(form);
})
</script>
