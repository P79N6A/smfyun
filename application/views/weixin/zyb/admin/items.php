 <?php
function convert($type,$url=''){
 if(!$type){
   if(substr($url, 0, 4) == 'http')$type=0;//虚拟
   if(substr($url, 0, 4) != 'http')$type=1;//卡券
   if($url='')$type=2;
 }
 switch ($type) {
   case 0:
     echo '虚拟奖品';
     break;
   case 1:
     echo '微信卡券';
     break;
   case 2:
     echo '实物';
     break;
   case 3:
     echo '话费流量';
     break;
   case 4:
     echo '微信红包';
     break;
   case 5:
     echo '有赞赠品';
     break;
   default:
     # code...
     break;
 }
}
?>
<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    奖品管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">奖品管理</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <a href="/wfba/items/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新奖品</span></a>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=count($result['items'])?> 件产品</h3>
              <div class="box-tools">
                <div class="input-group" style="width: 150px;">
                  <input type="text" name="table_search" class="form-control input-sm pull-right" placeholder="搜索">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>排序</th>
                  <th>品名</th>
                  <th>类型</th>
                  <th>兑换数量</th>
                  <th>剩余数量</th>
                  <th>限购</th>
                  <th>价格</th>
                  <th>积分</th>
                  <th>状态</th>
                  <th>上架时间</th>
                  <th>操作</th>
                </tr>

                <?php foreach ($result['items'] as $key=>$item):?>

                <tr>
                  <td><?=$item->pri?></td>
                  <td><?=$item->name?></td>
                  <td><?=convert($item->type,$item->url)?></td>
                  <td><?=$convert[$key]?></td>
                  <td><?=$item->stock?></td>
                  <td><?=$item->limit?></td>
                  <td><?=$item->price?></td>
                  <td><?=$item->score?></td>
                  <td>
                  <?php
                  if ($item->endtime && strtotime($item->endtime) < time())
                    echo '<span class="label label-danger">已过期</span>';
                  else if ($item->show == 0)
                    echo '<span class="label label-warning">隐藏</span>';
                  else
                    echo '<span class="label label-success">正常</span>';
                  ?>
                  </td>
                  <td><?=date('Y-m-d H:i', $item->lastupdate)?></td>
                  <td><a href="/wfba/items/edit/<?=$item->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
                </tr>

                <?php endforeach;?>
              </tbody></table>
            </div><!-- /.box-body -->

            <div class="box-footer clearfix">
              <ul class="pagination pagination-sm no-margin pull-right">
                <li><a href="#">«</a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">»</a></li>
              </ul>
            </div>

          </div>

    </div>
  </div>

</section><!-- /.content -->
