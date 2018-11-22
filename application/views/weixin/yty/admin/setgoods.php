<link rel="stylesheet" type="text/css" href="/wdy/plugins/simditor-2.2.4/styles/simditor.css" />
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
    <li><a href="/ytya/setgoods">经销商品管理</a></li>
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
              <h3 class="box-title">共<? if($result['countall']) echo $result['countall'];else echo 0;?> 种出售的商品</h3>
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
              $name1=ORM::factory('yty_sku')->where('bid','=',$bid)->where('order','=',1)->find()->name;
              $name2=ORM::factory('yty_sku')->where('bid','=',$bid)->where('order','=',2)->find()->name;
              $name3=ORM::factory('yty_sku')->where('bid','=',$bid)->where('order','=',3)->find()->name;
              $name4=ORM::factory('yty_sku')->where('bid','=',$bid)->where('order','=',4)->find()->name;
              $name5=ORM::factory('yty_sku')->where('bid','=',$bid)->where('order','=',5)->find()->name;

            ?>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>缩略图</th>
                  <th>商品名称</th>
                  <th>价格</th>
                  <th><?=$name1?>进货价</th>
                  <?php if($sku_num>=2): ?>
                  <th><?=$name2?>进货价</th>
                  <?endif ?>
                  <?php if($sku_num>=3): ?>
                  <th><?=$name3?>进货价</th>
                  <?endif ?>
                  <?php if($sku_num>=4): ?>
                  <th><?=$name4?>进货价</th>
                  <?endif ?>
                  <?php if($sku_num>=5): ?>
                  <th><?=$name5?>进货价</th>
                  <?endif ?>
                  <th>是否参与分销</th>
                  <th>当月销量/当月销售额</th>
                  <th>总销量/总销售额</th>
                  <th>操作</th>
                </tr>
                <?php
                foreach ($result['setgoods'] as $v):

                   $desc=htmlspecialchars($v->information);
                   $time=strtotime(date('ym',time()));
                   $goodid=$v->goodid;
                   $orders=DB::query(Database::SELECT,"select sum(num) as all_sold from yty_orders where bid = $bid and goodid = $goodid")->execute()->as_array();
                   $all_sold = $orders[0]['all_sold'];
                   $orders=DB::query(Database::SELECT,"select sum(price) as all_money from yty_orders where bid = $bid and goodid = $goodid")->execute()->as_array();
                   $all_money=$orders[0]['all_money'];
                   $orders=DB::query(Database::SELECT,"select sum(num) as month_sold from yty_orders where bid = $bid and goodid = $goodid and lastupdate >= $time")->execute()->as_array();
                   $month_sold=$orders[0]['month_sold'];
                   $orders=DB::query(Database::SELECT,"select sum(price) as month_money from yty_orders where bid = $bid and goodid = $goodid and lastupdate >= $time ")->execute()->as_array();
                   $month_money=$orders[0]['month_money'];
                  $soldednum=DB::query(database::SELECT,"select sum(temp.num)as tonum from (SELECT yty_orders.* FROM `yty_trades`,yty_orders WHERE yty_orders.tid=yty_trades.tid and yty_trades.status!='TRADE_CLOSED' and yty_trades.status!='TRADE_CLOSED_BY_USER' and yty_trades.status!='NO_REFUND' and yty_orders.goodid=$goodid) as temp where temp.bid=$bid")->execute()->as_array();
                   	//var_dump($soldednum);
                ?>
                <tr>
                </div>
                <?php if(!$v->pic):?>
                  <td><img src="<?=$v->picurl?>" width="32" height="32"></td>
                <?endif?>
                <?php if($v->pic):?>
                  <td><img src="/ytya/images/setgood/<?=$v->id?>.v<?=time()?>.jpg" width="32" height="32"></td>
                <?endif?>
                  <td style=" width:30%;word-wrap:break-word;word-break:break-all;"><?=$v->title?></td>
                  <td><?=$v->price?></td>
               	  <td><?=$v->money0?$v->money0:0.00?></td>
                   <?php if($sku_num>=2): ?>
                  <td><?=$v->money1?$v->money1:0.00?></td>
                  <?endif ?>
                   <?php if($sku_num>=3): ?>
                  <td><?=$v->money2?$v->money2:0.00?></td>
                  <?endif ?>
                   <?php if($sku_num>=4): ?>
                  <td><?=$v->money3?$v->money3:0.00?></td>
                  <?endif ?>
                   <?php if($sku_num>=5): ?>
                  <td><?=$v->money4?$v->money4:0.00?></td>
                  <?endif ?>
                  <td id="lock">
                  <?php
                  if ($v->status== 1){
                    echo '<span class="label label-success">是</span>';
                  }else{
                    echo '<span class="label label-danger">否</span>';
                  }
                  ?>
                  </td>
                  <?php
                  if(!$month_sold)
                    $month_sold=0.00;
                  if(!$month_money)
                    $month_money=0.00;
                  if(!$all_money)
                    $all_money=0.00;
                  if(!$all_sold)
                    $all_sold=0.00;
                  ?>
                  <td><?=number_format($month_sold,2)?>/<?=number_format($month_money,2)?></td>
                  <td><?=number_format($all_sold,2)?>/<?=number_format($all_money,2)?></td>
                  <td nowrap="">
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-num_id="<?=$v->goodid?>" data-price="<?=$v->price?>" data-name="<?=$v->title?>" data-money0="<?=$v->money0?>" data-money1="<?=$v->money1?>" data-money2="<?=$v->money2?>" data-money3="<?=$v->money3?>" data-money4="<?=$v->money4?>" data-picurl="<?=$v->picurl?>" data-url="<?=$v->url?>" data-price="<?=$v->price?>" data-status="<?=$v->status?>" data-desc="<?=$desc?>" data-name1="<?=$name1?>" data-name2="<?=$name2?>" data-name3="<?=$name3?>" data-name4="<?=$name4?>" data-name5="<?=$name5?>" >
                      <span>修改</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                  <input type="hidden" value='<?=$goodid?>'>
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

</form>
</section><!-- /.content -->

<div class="modal" id="actionModel">
  <div class="modal-dialog">
    <form id="shipform" method="post" enctype="multipart/form-data">
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
  var desc=button.data('desc');
  var money0=button.data('money0');
  var money1=button.data('money1');
  var money2=button.data('money2');
  var money3=button.data('money3');
  var money4=button.data('money4');
  var status=button.data('status');
  var picurl=button.data('picurl');
  var name1=button.data('name1');
  var name2=button.data('name2');
  var name3=button.data('name3');
  var name4=button.data('name4');
  var name5=button.data('name5');
  var url=button.data('url');
  var information=name+"  (价格:"+price+"元)";
  var form = '';

  form+='<div class="form-group"><label for="fscore">商品名称</label><input class="form-control" id="fscore" name="form[title]" max="999" type="text" style="width:80%" value="'+name+'"></div>';
  // form+='<div class="form-group"><label for="fscore">商品描述</label><input class="form-control" id="fscore" name="form[desc]" max="999" type="text" style="width:80%" value="'+desc+'"></div>';

  form+='<div class="form-group"><label for="desc">详细介绍</label><textarea class="textarea" id="desc" name="form[desc]" placeholder="" style="width: 100%; height: 100px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">'+desc+'</textarea></div>';
  form+='<div class="form-group"><label for="fscore">缩略图</label><input class="form-control" id="fscore" name="goodpic" type="file" style="width:100%"></div>';
  form+='<div class="form-group"><label for="fscore">'+name1+'进货价</label><input class="form-control" id="fscore" name="form[money0]" max="999" type="number" step="0.01" style="width:150px" value="'+money0+'"></div>';
  <?php if($sku_num>=2): ?>
  form+='<div class="form-group"><label for="fscore">'+name2+'进货价</label><input class="form-control" id="fscore" name="form[money1]" max="999" type="number" step="0.01" style="width:150px" value="'+money1+'"></div>';
  <?php endif?>
  <?php if($sku_num>=3): ?>
  form+='<div class="form-group"><label for="fscore">'+name3+'进货价</label><input class="form-control" id="fscore" name="form[money2]" max="999" type="number" step="0.01" style="width:150px" value="'+money2+'"></div>';
  <?php endif?>
  <?php if($sku_num>=4): ?>
  form+='<div class="form-group"><label for="fscore">'+name4+'进货价</label><input class="form-control" id="fscore" name="form[money3]" max="999" type="number" step="0.01" style="width:150px" value="'+money3+'"></div>';
  <?php endif?>
  <?php if($sku_num>=5): ?>
  form+='<div class="form-group"><label for="fscore">'+name5+'进货价</label><input class="form-control" id="fscore" name="form[money4]" max="999" type="number" step="0.01" style="width:150px" value="'+money4+'"></div>';
  <?php endif?>
  form += '<div class="form-group"><label for="fscore"> 是否参与分销</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock0" value="1"'+ (status==1 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">是</span></label><label class="checkbox-inline"><input type="radio" name="form[status]" id="flock3" value="0" '+ (status==0 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">否</label></div></div>';
 // form += '<div class="form-group"><label for="flock">用户状态（加入白名单后不会再自动锁定）：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock0" value="0"'+ (lock==0 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">正常</span></label> <label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock1" value="1"'+ (lock==1 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">已锁定</label> <label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock3" value="3"'+ (lock==3 ? ' checked' : '') +'><span class="label label-warning" style="font-size:14px">白名单</label></div></div>';
  form += '<input type="hidden" name="form[num_iid]" value="'+ id +'">';
  // form += '<input type="hidden" name="form[title]" value="'+ name +'">';
  form += '<input type="hidden" name="form[picurl]" value="'+picurl +'">';
  form += '<input type="hidden" name="form[url]" value="'+ url +'">';
  form += '<input type="hidden" name="form[price]" value="'+ price +'">';
  var modal = $(this);
  modal.find('.modal-title').text(information);
  modal.find('.modal-body').html(form);
  $(function () {
  var editor = new Simditor({
    textarea: $('.textarea'),
    toolbar: ['title','bold','italic','underline','strikethrough','color','ol','ul','blockquote','table','link','image','hr','indent','outdent','alignment']
  });
});

});

</script>

