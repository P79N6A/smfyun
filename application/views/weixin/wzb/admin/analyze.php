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
    直播
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/wzba/analyze">直播分析</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">


  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title"><?=$title?>：共 <?=$result['countall']?> 次直播</h3>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <th>直播ID</th>
                  <th>直播开始时间</th>
                  <th>直播结束时间</th>
                  <th>UV</th>
                  <th>PV</th>
                  <th>有效在线峰值人数</th>
                  <th>下单人数<br>(直播期间/直播开始至今)</th>
                  <th>订单数量<br>(直播期间/直播开始至今)</th>
                  <th>付款总金额<br>(直播期间/直播开始至今)</th>
                  </tr>
                <?php
                foreach ($result['lives'] as $v):
                    $time = date('Y-m-d H:i:s',$v->start_time);
                    $end_time = date('Y-m-d H:i:s',$v->end_time);
                    if($v->end_time){
                      $sql = DB::query(Database::SELECT,"SELECT count(id) as CT FROM wzb_trades where bid=$bid and pay_time>'$time and' and pay_time<'$end_time'");
                    }else{
                      $sql = DB::query(Database::SELECT,"SELECT count(id) as CT FROM wzb_trades where bid=$bid and pay_time>'$time'");
                    }
                    $num = $sql->execute()->as_array();
                    $order_count =  $num[0]['CT'];//订单数量

                    $sql = DB::query(Database::SELECT,"SELECT count(id) as CT FROM wzb_trades where bid=$bid and pay_time>'$time'");
                    $num = $sql->execute()->as_array();
                    $tonow_order_count =  $num[0]['CT'];//订单数量

                    if($v->end_time){
                      $sql = DB::query(Database::SELECT,"SELECT count(distinct(qid)) as CT FROM wzb_trades where bid=$bid and pay_time>'$time' and pay_time<'$end_time'");
                    }else{
                      $sql = DB::query(Database::SELECT,"SELECT count(distinct(qid)) as CT FROM wzb_trades where bid=$bid and pay_time>'$time'");
                    }
                    $num = $sql->execute()->as_array();
                    $num_count =  $num[0]['CT'];//下单人数

                    $sql = DB::query(Database::SELECT,"SELECT count(distinct(qid)) as CT FROM wzb_trades where bid=$bid and pay_time>'$time'");
                    $num = $sql->execute()->as_array();
                    $tonow_num_count =  $num[0]['CT'];//下单人数

                    if($v->end_time){
                      $sql = DB::query(Database::SELECT,"SELECT sum(payment) as CT FROM wzb_trades where bid=$bid and pay_time>'$time' and pay_time<'$end_time'");
                    }else{
                      $sql = DB::query(Database::SELECT,"SELECT sum(payment) as CT FROM wzb_trades where bid=$bid and pay_time>'$time'");
                    }
                    $num = $sql->execute()->as_array();
                    $money_count =  $num[0]['CT'];//下单总金额

                    $sql = DB::query(Database::SELECT,"SELECT sum(payment) as CT FROM wzb_trades where bid=$bid and pay_time>'$time'");
                    $num = $sql->execute()->as_array();
                    $tonow_money_count =  $num[0]['CT'];//下单总金额
                ?>
                <tr>
                  <td><a href='/wzba/history_trades/<?=$v->id?>'><?=$v->id?></a></td>
                  <td><?=date('Y-m-d H:i:s',$v->start_time)?></td>
                  <td><?=$v->end_time>0?date('Y-m-d H:i:s',$v->end_time):'尚未结束'?></td>
                  <td><a href='/wzba/qrcodes?start_time=<?=$v->start_time?>&end_time=<?=$v->end_time?>'><?=$v->uv?></a></td>
                  <td><a href='/wzba/qrcodes?start_time=<?=$v->start_time?>&end_time=<?=$v->end_time?>'><?=$v->pv?></a></td>
                  <!-- <td><?=$v->uv?></td> -->
                  <!-- <td><?=$v->pv?></td> -->
                  <td><?=$v->max_num?></td>
                  <td><a href='/wzba/history_trades/<?=$v->id?>'><?=(int)$num_count?></a>/<a href='/wzba/history_trades/<?=$v->id?>?now=1'><?=(int)$tonow_num_count?></a></td>
                  <td><a href='/wzba/history_trades/<?=$v->id?>'><?=(int)$order_count?></a>/<a href='/wzba/history_trades/<?=$v->id?>?now=1'><?=(int)$tonow_order_count?></a></td>
                  <td><a href='/wzba/history_trades/<?=$v->id?>'><?=number_format($money_count,2)?></a>/<a href='/wzba/history_trades/<?=$v->id?>?now=1'><?=number_format($tonow_money_count,2)?></a></td>
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
</section><!-- /.content -->
