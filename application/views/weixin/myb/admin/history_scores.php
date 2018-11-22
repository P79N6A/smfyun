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
// if ($result['ticket']) $title = '已生成海报';
?>

<section class="content-header">
  <h1>
    结算记录
    <small></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/myba/qrcodes">结算记录</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出全部结算记录</span></a>
    </div>
  </div>
<form method="get" name="qrcodesform">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <th>头像</th>
                  <th>微信昵称</th>
                  <th>金额</th>
                  <th>账单时间</th>
                  <th>结算时间</a></th>
                  <th>结算类型</a></th>
                </tr>

                <?php
                foreach ($result['scores'] as $v):
                ?>
                <tr>
                  <td><img src="<?=$v->qrcode->headimgurl?>" width="32" height="32" title="<?=$v->id?>"></td>
                  <td><?=$v->qrcode->nickname?></td>
                  <td><?=-$v->score?></td>
                  <td><?=$v->bz?></td>
                  <td><?=date('Y-m-d H:i:s',$v->lastupdate)?></td>
                  <td><?=$v->getTypeName($v->type)?></td>
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
