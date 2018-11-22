<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    经销商等级设置
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">经销商等级设置</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <a href="/ytya/skus/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>新建经销商等级</span></a>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=count($result['skus'])?> 条(请按照由高到低顺序新建经销商等级，新建后不允许删除)</h3>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>经销商等级名称</th>
                  <th>经销商等级优先度</th>
                  <th>所需的代理金（单位：元）</th>
                  <th>等级对应的经销商进货贸易折扣</th>
                  <th>是否允许提现</th>
                  <th>操作</th>
                </tr>
                <?php foreach ($result['skus'] as $key=>$sku):?>
                <tr>
                  <td><?=$sku->name?></td>
                  <td><?=$sku->lv?></td>
                  <td><?=$sku->money?></td>
                  <td><?=$sku->scale?>%</td>
                   <td id="status">
                  <?php
                  if ($sku->status== 1){
                    echo '<span class="label label-success">是</span>';
                  }else{
                    echo '<span class="label label-danger">否</span>';
                  }
                  ?>
                  </td>
                  <td><a href="/ytya/skus/edit/<?=$sku->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
                </tr>
                <?php endforeach;?>
              </tbody></table>
            </div><!-- /.box-body -->
          </div>

    </div>
  </div>

</section><!-- /.content -->
