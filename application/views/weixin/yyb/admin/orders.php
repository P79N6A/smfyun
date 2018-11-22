<style>
.label {font-size: 14px}
.clone{
  color: #72afd2;
}
</style>

<section class="content-header">
  <h1>
    预约任务列表
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">预约任务列表</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
<form method="get" name="loginsform">

  <div class="row">
    <div class="col-xs-12">
      <a href="/yyba/orders_add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>新建群发</span></a>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=$countall?> 个预约任务</h3>
              <div class="box-tools">
                <div class="input-group" style="width: 150px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="搜索" value="">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>新建时间</th>
                  <th>模板消息群发标题</th>
                  <th>模板消息群发时间</th>
                  <th>模板消息跳转类型</th>
                  <th>状态（是否发送）</th>
                  <th>发送用户数</th>
                  <th>失败条数</th>
                  <th>发送用户</th>
                  <th>发送方式</th>
                  <th>修改（发送后不可修改）</th>
                  <!-- <th>模板消息预览</th> -->
                </tr>
                <?php
                foreach ($result['orders'] as $orders):
                ?>
                <tr>
                  <td><?=date('Y-m-d H:i:s',$orders->lastupdate)?></td>
                  <td><?=$orders->title?></td>
                  <td><?=date('Y-m-d H:i:s',$orders->time)?></td>
                  <td id="type<?=$orders->id?>">
                  <?php
                  if ($orders->type == 1&&$orders->url)
                    echo '<span class="label label-warning">链接</span>';
                  elseif ($orders->type == 2)
                    echo '<span class="label label-info">优惠券优惠</span>';
                  elseif ($orders->type == 3)
                    echo '<span class="label label-danger">有赞赠品</span>';
                  if ($orders->type == 1&&!$orders->url)
                     echo '<span class="label label-success">无</span>';
                  ?>
                  </td>
                  <td>
                    <?php 
                    if($orders->state==1){
                      echo '<span class="label label-success">发送完成</span>';
                    }elseif ($orders->state==0) {
                        if($orders->start==1){
                            $sending=ORM::factory('yyb_qrcode')->where('bid','=',$orders->bid)->count_all()-$orders->has_send+50000;
                            echo '<span class="label label-success">发送中(约'.ceil($sending/10000).'分钟后发送完毕)</span>';
                        }else{
                          if($orders->way == 0&&$orders->time>time()){
                            echo '<span class="label label-warning">未发送(未到发送时间)</span>';
                          }elseif ($orders->way == 1||$orders->time<=time()) {
                            $odernum=ORM::factory('yyb_order')->where('bid','!=',$orders->bid)->where('state','=',0)->count_all();
                            if($odernum>0){
                                $dlorders=ORM::factory('yyb_order')->where('bid','!=',$orders->bid)->where('state','=',0)->find_all();
                                foreach ($dlorders as $k => $dlorder) {
                                    $bids[$k]= $dlorder->bid;
                                }
                                $countnum=ORM::factory('yyb_qrcode')->where('bid','IN',$bids)->count_all();
                                echo '<span class="label label-warning">队列中,约'.ceil($countnum/10000).'分钟后开始发送</span>';
                            }else{
                                echo '<span class="label label-warning">未发送(五分钟后如还是此状态请联系管理员)</span>';
                            }
                        }
                      }
                    }
                  ?>
                  </td>
                  <td><?=$orders->state==1?$orders->all_user:''?></td>
                  <td><?=$orders->state==1?ORM::factory('yyb_item')->where('bid','=',$orders->bid)->where('oid','=',$orders->id)->count_all():''?></td>
                   <td id="flag<?=$orders->id?>">
                  <?php
                  if ($orders->flag == 1)
                    echo '<span class="label label-warning">已订阅</span>';
                  elseif ($orders->flag == 0)
                    echo '<span class="label label-info">全部</span>';
                  ?>
                  </td>
                  <td id="way<?=$orders->id?>">
                  <?php
                  if ($orders->way == 1)
                    echo '<span class="label label-warning">立即发送</span>';
                  elseif ($orders->way == 0)
                    echo '<span class="label label-info">定时发送</span>';
                  ?>
                  </td>
                  <td><?=$orders->state==1?'':'<a href="/yyba/orders_edit/'.$orders->id.'"><span>修改</span> <i class="fa fa-edit"></i></a>'?></td>
                  <!-- <td><a href="/yyba/play/<?=$orders->id?>"><span>预览</span> <i class="fa fa-edit"></i></a></td> -->
                </tr>
              <?php endforeach?>
              </tbody></table>
            </div><!-- /.box-body -->
          </div>

    </div>
  </div>
</form>
<div class="box-footer clearfix">
        <?=$pages?>
</div>
</section><!-- /.content -->
