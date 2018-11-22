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
      <a href="/qda/items/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新奖品</span></a>
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
                  <th>品名</th>
                  <th>兑换数量</th>
                  <th>剩余数量</th>
                  <th>价格</th>
                  <th>领取资格</th>
                  <th>上架时间</th>
                  <th>操作</th>
                </tr>

                <?php foreach ($result['items'] as $key=>$item):
                 $num1=ORM::factory('qd_order')->where('iid','=',$item->id)->count_all();
                 $num2=$item->stock->$num1;
                ?>

                <tr>
                  <td><?=$item->name?></td>
                  <td><?=$num1?></td>
                  <td><?=$num2?></td>
                  <td><?=$item->price?></td>
                  <td><?=$item->score?></td>
                  <td><?=date('Y-m-d H:i', $item->lastupdate)?></td>
                  <td><a href="/qda/items/edit/<?=$item->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
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
