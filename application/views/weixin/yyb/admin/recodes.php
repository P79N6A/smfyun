<style>
.label {font-size: 14px}
.clone{
  color: #72afd2;
}
</style>

<section class="content-header">
  <h1>
    发送失败记录
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">发送失败记录</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
<form method="get" name="loginsform">

  <div class="row">
    <div class="col-xs-12">

    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=$countall?> 条消息发送记录</h3>
              <div class="box-tools">
                <!--<div class="input-group" style="width: 150px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="搜索" value="">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>-->
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <th>预约项目标题</th>
                  <th>头像</th>
                  <th>昵称</th>
                  <!-- <th>性别</th> -->
                  <th>发送状态</th>
                  <th>原因</th>
                  <th>发送时间</th>
                </tr>
                <?php
                foreach ($result['user'] as $user):
                  $reason=$user->reason;
                  $result_reason=explode(" ", $user->reason);
                  if($result_reason[1]=='require'&&$result_reason[2]=='subscribe'&&$result_reason[3]=='hint:') $reason='用户取消关注';
                ?>
                <tr>
                  <td><?=$user->order->title?></td>
                  <td><img style="height:32px;width:32px;"src="<?=$user->qrcode->headimgurl?>"></td>
                  <td><?=$user->qrcode->nickname?></td>
                  <!-- <td><?=$user->qrcode->sex?></td> -->
                  <td><?=$user->state==1?"<span class='label label-success'>已发送</span>":"<span class='label label-warning'>发送失败</span>"?></td>

                  <td><?=$reason?></td>
                  <td><?=date("Y-m-d h:i:s",$user->lastupdate)?></td>
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
