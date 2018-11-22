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
    <li><a href="/dkla/myhexiao/<?=$bid?>">我的核销记录</a></li>
  </ol>
</section>

<!-- Main content -->
<section class="content">


  <div class="row">
    <div class="col-lg-12">

    <?php if ($result['ok']):?>
      <div class="alert alert-success alert-dismissable"><i class="icon fa fa-check"></i><?=$result['ok']?></div>
    <?php endif?>

      <div class="nav-tabs-custom">
          <form method="get" name="ordersform">
            <ul class="nav nav-tabs" style="padding:5px 10px;border-top:3px solid #00a65a;border-radius:3px">
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
                  <th>核销时间</th>
                </tr>
                <?php
                foreach ($result['orders'] as $v):
                ?>
                <tr>
                  <td><img src="<?=$v->user->headimgurl?>" width="32" height="32" title="<?=$v->user->openid?>"></td>
                  <td><?=$v->user->nickname?></td>
                  <td><?=$v->tel?></td>
                  <td><?=date('m-d H:i', $v->createdtime)?></td>
                  <td><?=$v->item->name?></td>
                  <td><?=date('m-d H:i', $v->tag_time)?></td>
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




