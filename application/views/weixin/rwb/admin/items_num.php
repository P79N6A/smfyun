
<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    <?=$result['tid_name']?>|奖品发送情况
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active"></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=$result['countall']?> 级别</h3>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>任务级别</th>
                  <th>对应奖品名称</th>
                  <th>奖品发放数量</th>
                </tr>
                <?php foreach ($result['items_num'] as $k=>$v):
                  $count = ORM::factory('rwb_order')->where('bid','=',$bid)->where('iid','=',$v->item->id)->count_all();
                ?>
                <tr>
                  <td><?=$k+1?></td>
                  <td><?=$v->item->km_content?></td>
                  <td><?=$count?></td>
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
