 <?php
function convert1($type,$url=''){
 if(!$type){

   if($url='')$type=2;
 }
 switch ($type) {
   case 0:
     echo '虚拟奖品';
     break;
   case 2:
     echo '实物';
     break;
   case 3:
     echo '话费流量';
    break;
   case 4:
     echo "红包";
   default:
     # code...
     break;
 }
}
?>
<style>
.label {font-size: 14px}
</style>

<section class="wrapper" style="width:85%;float:right;">
 <div class="wrapper">
      <div class="row">
          <div class="page-heading">
            <h3>
                奖品管理
                <small><?=$desc?></small>
              </h3>
        </div>
<ul class="breadcrumb" style="margin-left:15px">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">奖品管理</li>
</ul>




<!--body wrapper start-->

                <div class="col-sm-12">
                <section class="panel">
                <header class="panel-heading">
                    共 <?=count($result['items'])?> 件产品
                    <span class="tools pull-right">
                        <a href="/qwtwdba/items/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px;height:40px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新奖品</span></a>
                     </span>
                </header>
                 <div class="input-group" style="width: 150px;margin-left:15px">
                  <input type="text" name="table_search" class="form-control input-sm pull-right" placeholder="搜索">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                <div class="panel-body">
                <div class="adv-table editable-table ">
                <div class="clearfix">



                <table class="table table-striped table-hover table-bordered" id="editable-sample">
                <thead>
                <tr>
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
                </thead>
                <tbody>
                <?php foreach ($result['items'] as $key=>$item):?>

                <tr>
                  <td><?=$item->pri?></td>
                  <td><?=$item->name?></td>
                  <td><?=convert1($item->type,$item->url)?></td>
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
                  <td><a href="/qwtwdba/items/edit/<?=$item->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
                </tr>

                <?php endforeach;?>
                </tbody>
                </table>
                </div>
                <div class="box-footer clearfix">
              <ul class="pagination pagination-sm no-margin pull-right">
                <li><a href="#">«</a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">»</a></li>
              </ul>
            </div>
                </div>
                </section>
                </div>
                </div>
        </div>
        <!--body wrapper end-->

</section><!-- /.content -->
