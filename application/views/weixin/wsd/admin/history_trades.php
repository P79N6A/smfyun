<style>
.label {font-size: 14px}
th{
  text-align: center;
}
td{
  text-align: center;
}
</style>

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
// if ($result['fuser']) $title = $result['fuser']->nickname.'的下线';
 if ($result['s']) $title = '搜索结果';
// if ($result['ticket']) $title = '已生成海报';
?>

<section class="content-header">
  <h1>
    订单记录
    <small></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/wsda/qrcodes">订单记录</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出全部订单记录</span></a>
    </div>
  </div>
<form method="get" name="qrcodesform">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">

            <div class="box-header">
              <h3 class="box-title"><?=$title?>: 共 <?=$result['countall']?> 条记录</h3>
              <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <th>订单名称</th>
                  <th>时间</th>
                  <th>金额</th>
                  <th>客户昵称</a></th>
                  <th>头像</th>
                  <th>姓名</th>
                  <th>收货地址</th>
                  <th>收货电话</th>
                  <th>备注</th>
                  <th>所属代理</a></th>
                  <th>需扣除的销售利润</th>
                  <th>订单状态</th>
                </tr>

                <?php
                foreach ($result['trades'] as $v):
                $information=ORM::factory('wsd_qrcode')->where('id','=',$v->qid)->find();
                $fuser = ORM::factory('wsd_qrcode')->where('bid', '=', $v->bid)->where('openid', '=', $v->fopenid)->where('lv','=',1)->find();
                ?>
                <tr>
                  <td><?=$v->title?></td>
                  <td><?=$v->pay_time?></td>
                  <td><?=$v->money?></td>
                  <td><?=$information->nickname?></td>
                  <td><img src="<?=$information->headimgurl?>" width="32" height="32"></td>
                  <td><?=$v->receiver_name?></td>
                  <td><?=$v->receiver_state.$v->receiver_city.$v->receiver_district.$v->receiver_address?></td>
                  <td><?=$v->receiver_mobile?></td>
                  <td><?=$v->buyer_message?></td>
                  <td><?=$fuser->nickname?></td>
                  <td><?=$v->money1?></td>
                  <td id="lock<?=$v->id?>">
                  <?php
                  if ($v->status == 'WAIT_SELLER_SEND_GOODS')
                    echo '<span class="label label-warning">已付款</span>';
                  if ($v->status == 'WAIT_BUYER_CONFIRM_GOODS')
                    echo '<span class="label label-danger">已发货</span>';
                  if ($v->status == 'TRADE_BUYER_SIGNED')
                    echo '<span class="label label-success">已签收</span>';
                  if ($v->status == 'TRADE_CLOSED')
                    echo '<span class="label label-primary">已退款</span>';
                  if ($v->status == 'TRADE_CLOSED_BY_USER')
                    echo '<span class="label label-primary">订单已取消</span>';
                  ?>
                  </td>
                </tr>
                <?php endforeach;?>
              </tbody>
            </table>
          </div><!-- /.box-body -->

              <div class="box-footer clearfix">
                <?=$pages?>
              </div>

            </div>

          </div>

    </div>
</form>
</section><!-- /.content -->
