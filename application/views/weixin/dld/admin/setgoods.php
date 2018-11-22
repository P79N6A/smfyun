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
    <li><a href="/dlda/qrcodes">分销商品管理</a></li>
    <li class="active"><?//=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">
  <!-- <div class="row">
    <div class="col-xs-12">
      <a href="<?=$_SERVER['PATH_INFO']?>?qid=<?=$result['qid']?>&amp;export=csv&tag=<?=$activetype?>" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出全部商品信息</span></a>
    </div>
  </div> -->
<form method="get" name="qrcodesform">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共<? if($result['countall']) echo $result['countall'];else echo 0;?> 种出售的商品</h3>
         <div class="box-tools">
         <a href="/dlda/setgood1s?refresh=1" class="btn btn-success" style="position:absolute;right:270px;height:30px;line-height:18px;"><i class="fa fa-refresh"></i>刷新商品列表</a>
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按商品名称搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div><!-- /.box-header -->
            <?php
            $config['kaiguan_needpay']=ORM::factory('dld_cfg')->where('bid','=',$bid)->where('key','=','kaiguan_needpay')->find()->value;
            ?>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>缩略图</th>
                  <th>商品名称</th>
                  <th>零售价(元)</th>
                  <th>库存</th>
                  <th>代理哆上的销量</th>
                  <th>是否允许分享</th>
                  <th>操作</th>
                </tr>
                <?php foreach ($result['goods'] as $v){
                   $num_iid=$v->num_iid;
                  $soldednum=DB::query(database::SELECT,"select sum(temp.num)as tonum from (SELECT dld_orders.* FROM `dld_trades`,dld_orders WHERE dld_orders.tid=dld_trades.tid and dld_trades.status!='TRADE_CLOSED' and dld_trades.status!='TRADE_CLOSED_BY_USER' and dld_trades.status!='NO_REFUND' and dld_orders.goodid=$num_iid) as temp where temp.bid=$bid")->execute()->as_array();
                   	//var_dump($soldednum);
                ?>
                <tr>
                  <td style="border-top:2px solid #e5e5e5;"><img src="<?=$v->pic?>" width="32" height="32"></td>
                  <td style="border-top:2px solid #e5e5e5; width:30%;word-wrap:break-word;word-break:break-all;"><?=$v->title?></td>
                  <td style="border-top:2px solid #e5e5e5;"><?=$v->price?></td>
                  <td style="border-top:2px solid #e5e5e5;"><?=$v->num?></td>
                  <td style="border-top:2px solid #e5e5e5;"><?=empty($soldednum[0]['tonum'])?0:$soldednum[0]['tonum']?><?//=$v['sold_num']?></td>
               	  <td style="border-top:2px solid #e5e5e5;">
                  <?php
                  if ($v->status == 0)
                    echo '<span class="label label-warning">不允许</span>';
                  if ($v->status == 1)
                    echo '<span class="label label-danger">允许</span>';
                  ?>
                  </td>
                  <td style="border-top:2px solid #e5e5e5;" nowrap="">
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-num_id="<?=$v->num_iid?>" data-price="<?=$v->price?>"  data-name="<?=$v->title?>" data-money="<?=$v->money?>"  data-status="<?=$v->status?>" data-type="<?=$v->type?>" >
                      <span>修改</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                  <input type="hidden" value='<?=$num_iid?>'>
                </tr>

                <?php
                  if ($v->type==1) {
                    $skus = ORM::factory('dld_goodsku')->where('bid','=',$bid)->where('item_id','=',$v->num_iid)->find_all();
                    foreach ($skus as $sku){
                      echo '<tr>
                              <td></td>
                              <td>'.$sku->title.'</td>
                              <td>'.$sku->price.' </td>
                              <td>'.$sku->num.'</td>
                              <td></td>
                              <td></td>
                              <td></td>
                            </tr>';
                    }
                  }
                }?>
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
        <h4 class="modal-title">确认修改</h4>
      </div>
      <div class="modal-body">&nbsp;</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">确认修改</button>
      </div>
    </div><!-- /.modal-content -->
    </form>
  </div><!-- /.modal-dialog -->
</div>

<script>
<?php
$arr = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
?>
window.skus = Array();
window.smoney = Array();
window.suite = Array();
<?php foreach ($result['goods'] as $v) {
    if($v->type==1){
      $skus2 = ORM::factory('dld_goodsku')->where('bid','=',$bid)->where('item_id','=',$v->num_iid)->find_all();
      echo "skus[\"".$v->num_iid."\"]= Array();";
      foreach ($skus2 as $key=>$sku2) {
        echo "skus[\"".$sku2->item_id."\"][".$key."]= Array();";
        echo "skus[\"".$sku2->item_id."\"][".$key."][\"price\"] = \"".$sku2->price."\";";
        echo "skus[\"".$sku2->item_id."\"][".$key."][\"moeny\"] = \"".$sku2->money."\";";
        echo "skus[\"".$sku2->item_id."\"][".$key."][\"title\"] = \"".$sku2->title."\";";
        echo "skus[\"".$sku2->item_id."\"][".$key."][\"skuid\"] = \"".$sku2->id."\";";
        echo "skus[\"".$sku2->item_id."\"][".$key."][\"suite\"] = Array();";
        foreach ($result['suite'] as $k3=>$v3) {
          $sku = ORM::factory('dld_goodsku')->where('id','=',$sku2->id)->find();
          $value = ORM::factory('dld_smoney')->where('bid','=',$bid)->where('sid','=',$v3->id)->where('sku_id','=',$sku->sku_id)->find();
          echo "skus[\"".$sku2->item_id."\"][".$key."][\"suite\"][".$v3->id."] = Array();";
          echo "skus[\"".$sku2->item_id."\"][".$key."][\"suite\"][".$v3->id."][\"money\"] = \"".$value->money."\";";
          $value->id = (string)$value->id?$value->id:$arr[$key].$arr[$k3];
          echo "skus[\"".$sku2->item_id."\"][".$key."][\"suite\"][".$v3->id."][\"smoneyid\"] = \"".$value->id."\";";
          echo "skus[\"".$sku2->item_id."\"][".$key."][\"suite\"][".$v3->id."][\"name\"] = \"".$value->suite->name."\";";
          echo "skus[\"".$sku2->item_id."\"][".$key."][\"suite\"][".$v3->id."][\"skuid\"] = \"".$sku2->id."\";";
        }
      }
    }else{
      echo "skus[\"".$v->num_iid."\"]= Array();";
      echo "skus[\"".$v->num_iid."\"][0]= Array();";
      echo "skus[\"".$v->num_iid."\"][0][\"suite\"]= Array();";
        foreach ($result['suite'] as $k3=>$v3) {
          $value = ORM::factory('dld_smoney')->where('bid','=',$bid)->where('sid','=',$v3->id)->where('item_id','=',$v->num_iid)->find();
          echo "skus[\"".$v->num_iid."\"][0][\"suite\"][".$v3->id."] = Array();";
          echo "skus[\"".$v->num_iid."\"][0][\"suite\"][".$v3->id."][\"money\"] = \"".$value->money."\";";
          $value->id = (string)$value->id?$value->id:$arr[$key].$arr[$k3];
          echo "skus[\"".$v->num_iid."\"][0][\"suite\"][".$v3->id."][\"smoneyid\"] = \"".$value->id."\";";
          echo "skus[\"".$v->num_iid."\"][0][\"suite\"][".$v3->id."][\"name\"] = \"".$value->suite->name."\";";
        }
    }
}?>
<?php foreach ($result['suite'] as $k1=>$v1) {
   echo "suite[".$v1->id."] = Array();";
   echo "suite[".$v1->id."]['name'] = \"".$v1->name."\";";
}?>
console.log(skus);
$('#actionModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
  var id = button.data('num_id').toString();
  var name = button.data('name');
  var price=button.data('price');
  var money=button.data('money');
  var status=button.data('status');
  var information=name;
  var type = button.data('type');
  console.log(typeof(type)+type);
  console.log(typeof(id)+id);
  console.log(suite);
  if(type == 1){
    var form = '';
    // console.log(skus[id]);
    // console.log(skus[id]['price']);
    // // console.log(skus[id][0]["price"]);
    // // console.log(skus[id][1]["price"]);
    // // console.log(skus[id][0]["title"]);

      for(i=0;skus[id][i];i++){
        console.log(skus[id][i]["price"]);
        console.log(skus[id][i]["moeny"]);
        console.log(skus[id][i]["title"]);
        var smoney = '';
        $.each(skus[id][i]["suite"], function(index, value, array){
          if(value){
            smoney = smoney + '<br><br><label for="fscore">'+suite[index]['name']+'拿货价（元）：</label><input class="form-control" id="fscore" name="form[smoney]['+value['smoneyid']+'][money]" max="999" type="number" step="0.01" style="width:150px; display:inline-block;" value="'+value['money']+'"><input name="form[smoney]['+value['smoneyid']+'][skuid]" type="hidden" value="'+value['skuid']+'"><input name="form[smoney]['+value['smoneyid']+'][suiteid]" type="hidden" value="'+index+'">';
          }
        });
        // for (a =1; skus[id][i]["suite"][a]; a++) {
        //   smoney = smoney + '<br><br><label for="fscore">'+suite[a-1]['name']+'拿货价（元）：</label><input class="form-control" id="fscore" name="form[smoney]['+skus[id][i]['suite'][a]['smoneyid']+'][money]" max="999" type="number" step="0.01" style="width:150px; display:inline-block;" value="'+skus[id][i]['suite'][a]['money']+'"><input name="form[smoney]['+skus[id][i]['suite'][a]['smoneyid']+'][skuid]" type="hidden" value="'+skus[id][i]['suite'][a]['skuid']+'"><input name="form[smoney]['+skus[id][i]['suite'][a]['smoneyid']+'][suiteid]" type="hidden" value="'+a+'">';
        // };
        form+= '<div class="form-group"><label for="fscore">规格：'+skus[id][i]['title']+'</label><br><label for="fscore">零售价（元）：'+skus[id][i]['price']+'</label><br><label for="fscore">普通拿货价（元）：</label><input class="form-control" id="fscore" name="form[money]['+skus[id][i]['skuid']+']" max="999" type="number" step="0.01" style="width:150px; display:inline-block;" value="'+skus[id][i]['moeny']+'">'+smoney+'</div>'+'<hr>';
      }
      // form+='<div class="form-group"><label for="fscore">拿货价（元）</label><input class="form-control" id="fscore" name="form[money]" max="999" type="number" step="0.01" style="width:150px" value="'+money+'"></div>';
     form += '<div class="form-group"><label for="flock">商品审核（审核通过后用户可以分享）：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[status]" id="status0" value="0" '+ (status==0 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">不通过</span></label> <label class="checkbox-inline"><input type="radio" name="form[status]" id="flock1" value="1"'+ (status==1 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">通过</div></div>';
      form += '<input type="hidden" name="form[num_iid]" value="'+ id +'">';
      form += '<input type="hidden" name="form[type]" value="'+ type +'">';
      form += '<input type="hidden" name="form[title]" value="'+ name +'">';
  }else{
    var form = '';
    var smoney = '';
    $.each(skus[id][0]["suite"], function(index, value, array) {
      // console.log(skus[id][0]["suite"][index]);
      console.log(index);
      console.log(value);
      if(value){
        smoney = smoney + '<label for="fscore">'+suite[index]['name']+'拿货价（元）：</label><input class="form-control" id="fscore" name="form[smoney]['+value['smoneyid']+'][money]" max="999" type="number" step="0.01" style="width:150px; display:inline-block;" value="'+value['money']+'"><br><br><input name="form[smoney]['+value['smoneyid']+'][suiteid]" type="hidden" value="'+index+'">';
      }
    });
    // for (var i = 1; skus[id][0]["suite"][i]; i++) {
    //   console.log(skus[id][0]["suite"][i]);
    //   smoney = smoney + '<label for="fscore">'+suite[i-1]['name']+'拿货价（元）：</label><input class="form-control" id="fscore" name="form[smoney]['+skus[id][0]['suite'][i]['smoneyid']+'][money]" max="999" type="number" step="0.01" style="width:150px; display:inline-block;" value="'+skus[id][0]['suite'][i]['money']+'"><br><br><input name="form[smoney]['+skus[id][0]['suite'][i]['smoneyid']+'][suiteid]" type="hidden" value="'+i+'">';
    // };
      form+='<div class="form-group"><label for="fscore">普通拿货价（元）</label><input class="form-control" id="fscore" name="form[money]" max="999" type="number" step="0.01" style="width:150px" value="'+money+'"></div>';
      form = form+smoney;
     form += '<div class="form-group"><label for="flock">商品审核（审核通过后用户可以分享）：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[status]" id="status0" value="0" '+ (status==0 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">不通过</span></label> <label class="checkbox-inline"><input type="radio" name="form[status]" id="flock1" value="1"'+ (status==1 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">通过</div></div>';
      form += '<input type="hidden" name="form[num_iid]" value="'+ id +'">';
      form += '<input type="hidden" name="form[title]" value="'+ name +'">';
  }
  var modal = $(this);
  modal.find('.modal-title').text(information);
  modal.find('.modal-body').html(form);
})
</script>
