
  <div class="tpl-page-container tpl-page-header-fixed">
        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                直播分析
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">神码云直播</a></li>
                <li class="am-active">直播分析</li>
            </ol>
            <div class="tpl-portlet-components">
                    <div class="tpl-portlet">
                        <div class="tpl-portlet-title">
                        <div class="am-u-sm-12 am-u-md-9">
                    <div class="caption font-green bold">
                        共 <?=$result['countall']?> 次直播
                    </div>
                    </div>

                        </div>

                        <div class="am-tabs tpl-index-tabs" data-am-tabs>
                            <div class="am-tabs-bd">
                                <div class="am-tab-panel am-fade am-in am-active">
                                    <div id="wrapperA" class="wrapper">
                            <form class="am-form" name="ordersform" method="get">
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-text-nowrap am-table am-table-striped am-table-hover table-main">
                                    <thead><tr>
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
                                    </thead>
                                    <tbody>
                <?php
                foreach ($result['lives'] as $v):
                    $time = date('Y-m-d H:i:s',$v->start_time);
                    $end_time = date('Y-m-d H:i:s',$v->end_time);
                    if($v->end_time){
                      $sql = DB::query(Database::SELECT,"SELECT count(id) as CT FROM qwt_wzbtrades where bid=$bid and pay_time>'$time and' and pay_time<'$end_time'");
                    }else{
                      $sql = DB::query(Database::SELECT,"SELECT count(id) as CT FROM qwt_wzbtrades where bid=$bid and pay_time>'$time'");
                    }
                    $num = $sql->execute()->as_array();
                    $order_count =  $num[0]['CT'];//订单数量

                    $sql = DB::query(Database::SELECT,"SELECT count(id) as CT FROM qwt_wzbtrades where bid=$bid and pay_time>'$time'");
                    $num = $sql->execute()->as_array();
                    $tonow_order_count =  $num[0]['CT'];//订单数量

                    if($v->end_time){
                      $sql = DB::query(Database::SELECT,"SELECT count(distinct(qid)) as CT FROM qwt_wzbtrades where bid=$bid and pay_time>'$time' and pay_time<'$end_time'");
                    }else{
                      $sql = DB::query(Database::SELECT,"SELECT count(distinct(qid)) as CT FROM qwt_wzbtrades where bid=$bid and pay_time>'$time'");
                    }
                    $num = $sql->execute()->as_array();
                    $num_count =  $num[0]['CT'];//下单人数

                    $sql = DB::query(Database::SELECT,"SELECT count(distinct(qid)) as CT FROM qwt_wzbtrades where bid=$bid and pay_time>'$time'");
                    $num = $sql->execute()->as_array();
                    $tonow_num_count =  $num[0]['CT'];//下单人数

                    if($v->end_time){
                      $sql = DB::query(Database::SELECT,"SELECT sum(payment) as CT FROM qwt_wzbtrades where bid=$bid and pay_time>'$time' and pay_time<'$end_time'");
                    }else{
                      $sql = DB::query(Database::SELECT,"SELECT sum(payment) as CT FROM qwt_wzbtrades where bid=$bid and pay_time>'$time'");
                    }
                    $num = $sql->execute()->as_array();
                    $money_count =  $num[0]['CT'];//下单总金额

                    $sql = DB::query(Database::SELECT,"SELECT sum(payment) as CT FROM qwt_wzbtrades where bid=$bid and pay_time>'$time'");
                    $num = $sql->execute()->as_array();
                    $tonow_money_count =  $num[0]['CT'];//下单总金额
                ?>
                <tr>
                  <td><a href='/qwtwzba/history_trades/<?=$v->id?>'><?=$v->id?></a></td>
                  <td><?=date('Y-m-d H:i:s',$v->start_time)?></td>
                  <td><?=$v->end_time>0?date('Y-m-d H:i:s',$v->end_time):'尚未结束'?></td>
                  <td><a href='/qwtwzba/qrcodes?start_time=<?=$v->start_time?>&end_time=<?=$v->end_time?>'><?=$v->uv?></a></td>
                  <td><a href='/qwtwzba/qrcodes?start_time=<?=$v->start_time?>&end_time=<?=$v->end_time?>'><?=$v->pv?></a></td>
                  <!-- <td><?=$v->uv?></td> -->
                  <!-- <td><?=$v->pv?></td> -->
                  <td><?=$v->max_num?></td>
                  <td><a href='/qwtwzba/history_trades/<?=$v->id?>'><?=(int)$num_count?></a>/<a href='/qwtwzba/history_trades/<?=$v->id?>?now=1'><?=(int)$tonow_num_count?></a></td>
                  <td><a href='/qwtwzba/history_trades/<?=$v->id?>'><?=(int)$order_count?></a>/<a href='/qwtwzba/history_trades/<?=$v->id?>?now=1'><?=(int)$tonow_order_count?></a></td>
                  <td><a href='/qwtwzba/history_trades/<?=$v->id?>'><?=number_format($money_count,2)?></a>/<a href='/qwtwzba/history_trades/<?=$v->id?>?now=1'><?=number_format($tonow_money_count,2)?></a></td>
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

