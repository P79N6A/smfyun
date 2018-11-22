<style>
.nav-tabs-custom>.nav-tabs>li.active {
  border-top-color: #00a65a;
}
.nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: #00a65a;
  }
  .reduce,.add{
    font-size: 14px;
    position: relative;
    bottom: 10px;
  }
  .add{
    margin-left: 20px;
    margin-right: 30px;
  }
  .loc{
    margin-top: 5px;
    margin-bottom: 5px;
  }
.box-header .buyflow{
  padding: 8px;
  border-radius: 8px;
  border: 1px solid #dedede;
  width: 100%;
  font-size: 18px;
}
.label {font-size: 14px}
th, td{
  text-align: center;
}
input{
  padding: 10px 6px;
  line-height: 10px;
}
</style>
<link rel="stylesheet" href="css/bootstrap.min.css">

<script src="https://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<section class="content-header">
  <h1>
    流量中心
    <small>流量购买，消耗明细</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">流量中心</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">

      <div class="nav-tabs-custom">

          <ul class="nav nav-tabs">
            <li id="cfg_yz_li"><a data-toggle="tab">购买流量</a></li>
            <li id="cfg_wx_li" class="active"><a data-toggle="tab">流量消耗记录</a></li>
          </ul>

          <script>
    $(document).on('click','#cfg_yz_li',function(){
      window.location.href = '/wzba/flowcenter';
    });
          </script>

          <div class="tab-content">

            <div class="tab-pane active" id="cfg_wx">


  <div class="row">
    <div class="col-xs-12">
            <div class="box-header">
              <h3 class="box-title">概览：共 <?=$result['countall']?> 次直播</h3>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <th>直播ID</th>
                  <th>直播开始时间</th>
                  <th>直播结束时间</th>
                  <th>消耗流量</th>
                  <th>剩余流量</th>
                  </tr>
                <?php
                foreach ($result['lives'] as $v):
                    $sql = DB::query(Database::SELECT,"SELECT sum(data) as CT FROM wzb_lives where bid=$bid and id<=$v->id");
                    $sum = $sql->execute()->as_array();
                    $use_sum =  $sum[0]['CT'];//订单数量
                    // $time = date('Y-m-d H:i:s',$v->start_time);
                    // $end_time = date('Y-m-d H:i:s',$v->end_time);
                    // if($v->end_time){
                    //   $sql = DB::query(Database::SELECT,"SELECT count(id) as CT FROM wzb_trades where bid=$bid and pay_time>'$time and' and pay_time<'$end_time'");
                    // }else{
                    //   $sql = DB::query(Database::SELECT,"SELECT count(id) as CT FROM wzb_trades where bid=$bid and pay_time>'$time'");
                    // }
                    // $num = $sql->execute()->as_array();
                    // $order_count =  $num[0]['CT'];//订单数量

                    // $sql = DB::query(Database::SELECT,"SELECT count(id) as CT FROM wzb_trades where bid=$bid and pay_time>'$time'");
                    // $num = $sql->execute()->as_array();
                    // $tonow_order_count =  $num[0]['CT'];//订单数量

                    // if($v->end_time){
                    //   $sql = DB::query(Database::SELECT,"SELECT count(distinct(qid)) as CT FROM wzb_trades where bid=$bid and pay_time>'$time' and pay_time<'$end_time'");
                    // }else{
                    //   $sql = DB::query(Database::SELECT,"SELECT count(distinct(qid)) as CT FROM wzb_trades where bid=$bid and pay_time>'$time'");
                    // }
                    // $num = $sql->execute()->as_array();
                    // $num_count =  $num[0]['CT'];//下单人数

                    // $sql = DB::query(Database::SELECT,"SELECT count(distinct(qid)) as CT FROM wzb_trades where bid=$bid and pay_time>'$time'");
                    // $num = $sql->execute()->as_array();
                    // $tonow_num_count =  $num[0]['CT'];//下单人数

                    // if($v->end_time){
                    //   $sql = DB::query(Database::SELECT,"SELECT sum(payment) as CT FROM wzb_trades where bid=$bid and pay_time>'$time' and pay_time<'$end_time'");
                    // }else{
                    //   $sql = DB::query(Database::SELECT,"SELECT sum(payment) as CT FROM wzb_trades where bid=$bid and pay_time>'$time'");
                    // }
                    // $num = $sql->execute()->as_array();
                    // $money_count =  $num[0]['CT'];//下单总金额

                    // $sql = DB::query(Database::SELECT,"SELECT sum(payment) as CT FROM wzb_trades where bid=$bid and pay_time>'$time'");
                    // $num = $sql->execute()->as_array();
                    // $tonow_money_count =  $num[0]['CT'];//下单总金额
                ?>
                <tr>
                  <td><?=$v->id?></td>
                  <td><?=date('Y-m-d H:i:s',$v->start_time)?></td>
                  <td><?=$v->end_time>0?date('Y-m-d H:i:s',$v->end_time):'尚未结束'?></td>
                  <td><?=number_format($v->data/(1024*1024*1024),2)?>GB</td>
                  <td><?=number_format($all-number_format($use_sum/(1024*1024*1024),2),2)?>GB</td>
                </tr>

                <?php endforeach;?>
              </tbody></table>

            </div><!-- /.box-body -->
              <div class="box-footer clearfix">
                <?=$pages?>
              </div>
          </div>

    </div>
    <!-- /.content -->
            </div>


          </div>
      </div>

    </div><!--/.col (left) -->

</section><!-- /.content -->
