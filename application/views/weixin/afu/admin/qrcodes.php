
<style>
.label {font-size: 14px}
</style>

<?php
function convert($a='')
{
  switch ($a) {
    case 'm':
      echo "男";
      break;
    case 'w':
      echo "女";
      break;
    default:
      echo "未知";
      break;
  }
}
$title = '概览';
?>

<section class="content-header">
  <h1>
    参与用户
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/yyxa/qrcodes">用户明细</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>



<section class="content">
<form method="get" name="qrcodesform">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title"><?=$title?>：共 <?=$result['countall']?> 个用户</h3>
              <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>有赞积分</th>
                  <th>订单数量</th>
                  <th>成交金额</th>
                  <th>性别</th>
                </tr>

                <?php
                foreach ($result['qrcodes'] as $v):

                ?>

                <tr>
                  </td>
                  <td><img src="<?=$v->avatar?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->nick?></td>
                  <td><?=$v->points?></td>
                  <td><?=$v->traded_num?></td>
                  <td><?=$v->traded_money?></td>
                  <td><?=convert($v->sex)?></td>
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
  </div>

</form>
</section><!-- /.content -->


