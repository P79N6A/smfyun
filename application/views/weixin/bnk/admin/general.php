
<style>
.label {font-size: 14px}
</style>


<section class="content-header">
  <h1>
    概况
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active"><a href="/wzba/qrcodes">概况</a></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">


  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>充值的红包个数/金额</th>
                  <th>累计服务费</th>
                  <th>发出的红包个数/金额</th>
                  <th>累计已提现</th>
                  <th>账户余额</th>
                </tr>
                <?php
                  $cz_num=ORM::factory('bnk_score')->where('type','=',1)->count_all();
                  $cz_money=DB::query(Database::SELECT,"SELECT SUM(score) as cz_money from bnk_scores where  type =1")->execute()->as_array();
                  $cz_money=$cz_money[0]['cz_money'];
                  $tx_money=DB::query(Database::SELECT,"SELECT SUM(score) as tx_money from bnk_scores where  type =5")->execute()->as_array();
                  $tx_money=$tx_money[0]['tx_money'];
                  $sum_fee=DB::query(Database::SELECT,"SELECT SUM(used_fee) as sum_fee from bnk_orders")->execute()->as_array();
                  $sum_fee=$sum_fee[0]['sum_fee'];
                  $hb_send=ORM::factory('bnk_order')->count_all();
                  $money_send=DB::query(Database::SELECT,"SELECT SUM(money) as money_send from bnk_orders")->execute()->as_array();
                  $money_send=$money_send[0]['money_send'];
                  $hb_receive=ORM::factory('bnk_trade')->count_all();
                  $money_sum=DB::query(Database::SELECT,"SELECT SUM(money) as money_send from bnk_trades")->execute()->as_array();
                  $money_sum=$money_sum[0]['money_sum'];
                  $last_money=DB::query(Database::SELECT,"SELECT SUM(score) as last_money from bnk_scores ")->execute()->as_array();
                  $last_money=$last_money[0]['last_money'];
                ?>
                <tr>
                  <td><a href="/bnka/buymentrecord?type=1" title="查看充值记录"><?=$cz_num?>/<?=$cz_money?$cz_money:0?></a></td>
                  <td><?=$sum_fee?$sum_fee:0?></td>
                  <td><a href="/bnka/lottery_history" title="查看红包发送记录"><?=$hb_send?>/<?=$money_send?$money_send:0?></td>
                  <td><a href="/bnka/buymentrecord?type=1" title="查看提现记录"><?=$tx_money?$tx_money:0?></a></td>
                  <td><a href="/bnka/buymentrecord" title="查看收支明细"><?=$last_money?$last_money:0?></a></td>
                </tr>
              </tbody></table>
            </div><!-- /.box-body -->

              <div class="box-footer clearfix">
                <?=$pages?>
              </div>

            </div>

          </div>

    </div>
</section><!-- /.content -->
