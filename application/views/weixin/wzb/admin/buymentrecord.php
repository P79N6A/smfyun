<style>
.label {font-size: 14px}
th, td{
  text-align: center;
}
</style>

<?php

$title = '概览';
?>

<section class="content-header">
  <h1>
    购买记录
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/wzba/analyze">购买记录</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">


  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title"><?=$title?>：共 <?=$result['countall']?> 次购买</h3>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <th>订单编号</th>
                  <th>类型</th>
                  <th>名称</th>
                  <th>购买时间</th>
                  <th>付款金额<th>
                  </tr>
                  <?php 
                  function type($type){
                    switch ($type) {
                      case 'month':
                        echo '续费包月';
                        break;
                      case 'year':
                        echo '续费包年';
                        break;  
                      case 'stream':
                        echo '流量';
                        break;                                          
                    }
                  }
                  ?>
                  <?php foreach ($result['orders'] as $k => $v):?>
                    <tr>
                      <td><?=$v->tid?></td>
                      <td><?=type($v->type)?></td>
                      <td><?=$v->title?></td>
                      <td><?=date('Y-m-d H:i:s',$v->time)?></td>
                      <td><?=$v->price?></td>
                    </tr>                    
                  <?php endforeach?>
              </tbody></table>
            </div><!-- /.box-body -->

              <div class="box-footer clearfix">
                <?=$pages?>
              </div>

            </div>

          </div>

    </div>
</section><!-- /.content -->
</section><!-- /.content -->
