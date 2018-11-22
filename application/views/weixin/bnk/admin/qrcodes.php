
<style>
.label {font-size: 14px}
</style>


<section class="content-header">
  <h1>
    用户管理
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active"><a href="/wzba/qrcodes">用户明细</a></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">


  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title"><?=$title?>：共 <?=$result['countall']?> 个用户</h3>
              <div class="box-tools">
              <form method="get" name="qrcodesform">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">

                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                </form>
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>微信头像</th>
                  <th>微信昵称</th>
                  <th>发出的红包个数</th>
                  <th>发出的红包金额</th>
                  <th>收到的红包个数</th>
                  <th>收到的红包金额</th>
                  <th>账户余额</th>
                </tr>

                <?php
                foreach ($result['qrcodes'] as $v):
                  $qid=$v->id;
                  $hb_send=ORM::factory('bnk_order')->where('qid','=',$v->id)->count_all();
                  $money_send=DB::query(Database::SELECT,"SELECT SUM(money) as money_send from bnk_orders where qid = $qid")->execute()->as_array();
                  $money_send=$money_send[0]['money_send'];
                  $hb_receive=ORM::factory('bnk_trade')->where('qid','=',$v->id)->count_all();
                  $money_sum=DB::query(Database::SELECT,"SELECT SUM(money) as money_sum from bnk_trades where qid =$qid")->execute()->as_array();
                  $money_sum=$money_sum[0]['money_sum'];
                ?>
                <tr>
                  <td><img src="<?=$v->avatarUrl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->nickName?></td>
                  <td><a href="/bnka/analyze?qid=<?=$v->id?>" title="查看红包发送记录"><?=$hb_send?></a></td>
                  <td><?=$money_send?$money_send:0?></td>
                  <td><a href="/bnka/lottery_history?qid=<?=$v->id?>" title="查看红包领取记录"><?=$hb_receive?></a></td>
                  <td><?=$money_sum?$money_sum:0?></td>
                  <td><a href="/bnka/buymentrecord?qid=<?=$v->id?>" title="查看收支明细"><?=$v->score?></a></td>
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
