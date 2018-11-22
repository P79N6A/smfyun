<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    分销商等级设置
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">分销商等级设置</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <a href="/qfxa/skus/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新的分销商等级</span></a>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=count($result['skus'])?> 条</h3>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>分销商等级</th>
                  <th>分销商等级名称</th>
                  <th>达到多少金额自动到达该级别（元）</th>
                  <th>该级别下的返还比例</th>
                  <th>操作</th>
                </tr>

                <?php foreach ($result['skus'] as $key=>$sku):?>

                <tr>
                  <td><?=$sku->lv?></td>
                  <td><?=$sku->name?></td>
                  <td><?=$sku->money?></td>
                  <td><?=$sku->scale?>%</td>
                  <td><a href="/qfxa/skus/edit/<?=$sku->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
                </tr>

                <?php endforeach;?>
              </tbody></table>
            </div><!-- /.box-body -->
          </div>

    </div>
  </div>

</section><!-- /.content -->
