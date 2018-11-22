<!-- 卡密记录 -->
<?php
    function convert($a){
        switch ($a) {
            case 0:
            echo '未处理';
                break;
            case 1:
            echo "已处理";
            default:
            echo '';
                break;
        }
    }
?>
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
    领取记录
    <small><?=$desc?></small>
  </h1>


  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/rwba/orders">领取记录</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <a href="<?=$_SERVER['PATH_INFO']?>?qid=<?=$result['qid']?>&amp;export=csv&tag=<?=$activetype?>" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出奖品发送记录</span></a>
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
          <div class="tab-pane active" id="orders<?=$result['status']?>">

            <div class="table-responsive">
            <form method="post" method="post">
            <table class="table table-striped">
              <tbody>
              <tr>
                <th>头像</th>
                <th>昵称</th>
                <th>活动名称</th>
                <th>奖品名称</th>
                <th>时间</th>
                <th>发送状态</th>
                <th>原因</th>
              </tr>

                <?php foreach ($result['orders'] as $order):

                ?>
                <tr>
                  <td><img src="<?=$order->user->headimgurl?>" width="32" height="32" title="<?=$order->user->openid?>"></td>
                  <td>
                    <a href="/rwba/qrcodes?id=<?=$order->user->id?>"><?=$order->name?></a>
                  </td>
                  <td><?=$order->task_name?></td>
                  <td><?=$order->item_name?></td>
                  <td><?=date('m-d H:i',$order->lastupdate)?></td>
                  <th><?=convert($order->state)?></th>
                  <td><?=$order->log?></td>
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


