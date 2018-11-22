<style type="text/css">
  .tag{
    display: inline-block;
    border:1px solid #CAC1C1;
    padding:5px;
    margin-left: 10px;
    border-radius: 5px;
    margin-top: 5px;
  }
  .tactive{
    background-color: rgb(255, 232, 148);
  }
</style>
<section class="content-header">
  <h1>
    兑换记录
    <small><?=$desc?></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/qda/orders">兑换记录</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <a href="<?=$_SERVER['PATH_INFO']?>?qid=<?=$result['qid']?>&amp;export=csv&tag=<?=$activetype?>" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出全部未处理订单</span></a>
    </div>
  </div>


  <div class="row">
    <div class="col-lg-12">

    <?php if ($result['ok']):?>
      <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i><?=$result['ok']?></div>
    <?php endif?>

      <div class="nav-tabs-custom">

          <form method="get" name="ordersform">
            <ul class="nav nav-tabs">
              <li class="pull-right">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="模糊搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </li>
            </ul>
          </form>
          <div class="tab-pane active" id="orders">
            <div class="table-responsive">
            <form method="post" method="post">
            <table class="table table-striped">
              <tbody>
                <tr>
                <th>收货人</th>
                <th>手机</th>
                <th>收货地址</th>
                <th>备注</th>
                <th>时间</th>
                <th>品名</th>
                <th nowrap="">积分</th>
              </tr>

                <?php foreach ($result['orders'] as $order):?>

                <tr>
                  <td nowrap=""><?=$order->name?></td>
                  <td nowrap=""><?=$order->tel?></td>
                  <td><?=$order->address?></td>
                  <td><?=$order->memo?></td>
                  <td nowrap=""><?=date('m-d H:i', $order->lastupdate)?></td>
                  <td><?=$order->item->name?></td>
                  <td><?=$order->score?></td>
                </tr>
                <?php endforeach;?>
                <input type="hidden" name="action" value="oneship">
              </tbody>
              </table>
              </form>
              </div>
              <div class="box-footer clearfix">
                <?=$pages?>
              </div>
          </div><!-- tab-pane -->
          </div><!-- tab-content -->

      </div><!-- nav-tabs-custom -->
    </div>
  </div>

</section><!-- /.content -->



