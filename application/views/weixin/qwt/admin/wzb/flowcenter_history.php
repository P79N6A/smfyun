
<link rel="stylesheet" href="/qwt/assets/css/amazeui.datetimepicker.css"/>
<style type="text/css">
    #datetimepicker1{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    #datetimepicker2{
        display: inline-block;
    width: 150px;
    text-align: center;
    border: 1px solid #e5e5e5;
    border-radius: 5px;
    height: 38px;
    }
    .search-btn1{
        display: inline-block;
        background-color: white;
    border-radius: 5px;
    border: 1px solid #e5e5e5;
    color: black;
    border-top-left-radius: 5px !important;
    border-bottom-left-radius: 5px !important;
    }
</style>

  <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                流量消耗记录
            </div>
            <ol class="am-breadcrumb">
                <li><a class="am-icon-home">神码云直播</a></li>
                <li><a>会员中心</a></li>
                <li><a>直播流量中心</a></li>
                <li class="am-active">流量消耗记录</li>
            </ol>
            <div class="tpl-portlet-components">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <ul class="am-nav am-nav-tabs" style="left:0;">
              <li><a href="/qwtwzba/flowcenter">购买流量</a></li>
              <li class="am-active"><a>流量消耗记录</a></li>
                            </ul>

                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active" id="orders<?=$result['status']?>">
                                    <div id="wrapperA" class="wrapper">
                            <form class="am-form" name="ordersform" method="get">
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12 am-u-md-9">
            <div class="tpl-content-scope">
                <div class="note note-info">
                    <p class="warning-text"> 概览：共 <?=$result['countall']?> 次直播</p>
                </div>
            </div>
</div>
                        </div>
                    </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead><tr>
                  <!-- <th>ID</th> -->
                  <th>直播ID</th>
                  <th>直播开始时间</th>
                  <th>直播结束时间</th>
                  <th>消耗流量</th>
                  <th>剩余流量</th>
                  </tr>
                                    </thead>
                                    <tbody>
                <?php
                foreach ($result['lives'] as $v):
                    $sql = DB::query(Database::SELECT,"SELECT sum(data) as CT FROM qwt_wzblives where bid=$bid and id<=$v->id");
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
                                    </tbody>
                                </table>
                                <div class="am-cf">

                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination">
                                        <?=$pages?>
                                        </ul>
                                    </div>
                                </div>
                                <hr>

                        </div>

                    </div>
                </div>
                            </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>










        </div>

<script src="/qwt/assets/js/amazeui.datetimepicker.min.js"></script>
    <script type="text/javascript">
    $('#datetimepicker1').datetimepicker({
  language:  'zh-CN',
  format: 'yyyy-mm-dd',
  startView: 'month',
  minView: 'month'
});
    $('#datetimepicker2').datetimepicker({
  language:  'zh-CN',
  format: 'yyyy-mm-dd',
  startView: 'month',
  minView: 'month'
});
    </script>

