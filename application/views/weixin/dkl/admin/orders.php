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
    核销记录
    <small><?=$desc?></small>
  </h1>

<?php
if ($result['qrcode']) $title = $result['qrcode']->nickname . '的兑换明细 / ';

if ($result['status'] == 0) $title .= '未处理';
if ($result['status'] == 1) $title .= '已处理';
if ($result['status'] == 1){
  $activetype = 'done';
}
?>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/dkla/orders">兑换记录</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <a href="<?=$_SERVER['PATH_INFO']?>?qid=<?=$result['qid']?>&amp;export=csv&tag=<?=$activetype?>" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出全部<?=$title?>订单</span></a>
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
              <li id="orders<?=$result['status']?>" class="<?=$result['status'] == 0 ? 'active' : ''?>"><a href="/dkla/orders?qid=<?=$result['qid']?>">未核销用户</a>
              </li>
              <li id="orders<?=$result['status']?>" class="<?=$result['status'] == 1 ? 'active' : ''?>"><a href="/dkla/orders/done?qid=<?=$result['qid']?>">已核销用户</a></li>

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
          <div class="tab-pane active" id="orders<?=$result['status']?>">
            <div class="table-responsive">
            <form method="post" method="post">
            <table class="table table-striped">
              <tbody>
                <tr>
                <th>头像</th>
                <th>昵称</th>
                <th>电话</th>
                <th>兑换时间</th>
                <th>兑换产品</th>
                <?php if ($result['status'] == 1):?>
                <th>核销标签</th>
                <th>核销时间</th>
                <?php endif?>
              </tr>
                <?php foreach ($result['orders'] as $order):?>
                <tr>
                  <td><img src="<?=$order->user->headimgurl?>" width="32" height="32" title="<?=$order->user->openid?>"></td>
                  <td>
                    <a href="/dkla/qrcodes?id=<?=$order->user->id?>"><?=$order->user->nickname?></a>
                  </td>
                  <td nowrap=""><?=$order->tel?></td>
                  <td nowrap=""><?=date('m-d H:i', $order->createdtime)?></td>
                  <td><?=$order->item->name?></td>
                 <?php if ($result['status'] == 1):?>
                <td><?=$order->veri->tag?></td>
                <td><?=$order->tag_time?date('m-d H:i', $order->tag_time):''?></td>
                <?php endif?>
                </tr>
                <?php endforeach;?>
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



