<style>
.label {font-size: 14px}
</style>

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
// if ($result['fuser']) $title = $result['fuser']->nickname.'的下线';
 if ($result['s']) $title = '搜索结果';
 if ($status==0) $title = '总体';
 if ($status==1) $title = '虚拟';
 if ($status==2) $title = '实物';

?>

<section class="content-header">
  <h1>
    领取记录
    <small></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/snsa/qrcodes">领取记录</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">
<form method="get" name="qrcodesform">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">

            <div class="box-header">
              <h3 class="box-title"><?=$title?>: 共 <?=$result['countall']?> 条记录</h3>
              <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称精确搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div><!-- /.box-header -->
                      <form method="get" name="ordersform">
            <ul class="nav nav-tabs">
              <li id="orders<?=$status?>" class="<?=$status== 0 ? 'active' : ''?>"><a href="/snsa/order">整体数据</a></li>
              <li id="orders<?=$status?>" class="<?=$status == 1? 'active' : ''?>"><a href="/snsa/order?type=1">虚拟奖品</a></li>
              <li id="orders<?=$status?>" class="<?=$status == 2? 'active' : ''?>"><a href="/snsa/order?type=2">实物奖品</a></li>
            </ul>
          </form>

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <th>头像</th>
                  <th>昵称</th>
                  <th>性别</th>
                  <th>奖品名称</a></th>
                 <!--  <th>购买数量</a></th> -->

                <!--   <th>购买金额</a></th> -->
                  <th>获取时间</th>
                  <th>是否发放成功</th>
                  <!-- <th>需结算的佣金</th> -->
                </tr>

                <?php
                foreach ($result['orders'] as $v):
                $uinformation=ORM::factory('sns_qrcode')->where('id','=',$v->qid)->find();
                $ginformaint = ORM::factory('sns_item')->where('id', '=', $v->goodid)->find();

                ?>

                <tr>
                  <td><img src="<?=$uinformation->headimgurl?>" width="32" height="32" title="<?=$uinformation->openid?>"></td>
                  <td><?=$uinformation->nickname?></td>
                  <td><?=$sex[$uinformation->sex]?></td>
                  <td><?=$ginformaint->name?></td>
                  <td ><?=date('Y-m-d h:i',$v->lastupdate)?></a></td>

                  </td>
                  <td id="lock">
                   <?php 
                  if ($v->flag==1){//
                    echo '<span class="label label-success">是</span>';
                  }else{
                    echo '<span class="label label-danger">否</span>';
                  }
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
