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
    账单结算记录
    <small></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/dlda/qrcodes">账单结算记录</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出账单结算记录</span></a>
    </div>
  </div>
<form method="get" name="qrcodesform">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title"></h3>
              <div class="box-tools">
              <form method="post" name="qrcodesform" style="width:360px;">
                <select name='s[type]'>
                  <option value="all">全部</option>
                  <option value="1">个人团队奖励结算</option>
                  <option value="2">销售利润结算</option>
                </select>
                <div class="input-group" style="width: 250px;float:right;">
                  <input type="text" name="s[text]" class="form-control input-sm pull-right" placeholder="按昵称，注册手机号，支付宝账号搜索" value="<?=htmlspecialchars($_POST['s']['text'])?>">

                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                </form>
              </div>
            </div><!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <th>头像</th>
                  <th>微信昵称</th>
                  <th>电话</th>
                  <th>支付宝账号</th>
                  <th>金额</th>
                  <th>账单时间</th>
                  <th>结算时间</th>
                  <th>结算类型</th>
                  <th>账单类型</th>
                </tr>

                <?php
                foreach ($result['scores'] as $v):
                ?>
                <tr>
                  <td><img src="<?=$v->qrcode->headimgurl?>" width="32" height="32" title="<?=$v->id?>"></td>
                  <td><?=$v->qrcode->nickname?></td>
                  <td><?=$v->qrcode->tel?></td>
                  <th><?=$v->qrcode->alipay_name?></th>
                  <td><?=-$v->score?>元</td>
                  <td><?=$v->bz?$v->bz:'销售利润灵活结算'?></td>
                  <td><?=date('Y-m-d H:i:s',$v->lastupdate)?></td>
                  <td id="lock1<?=$v->id?>">
                  <?php
                  if ($v->type == 2||$v->type == 5)
                    echo '<span class="label label-success">手动企业付款</span>';
                  if ($v->type == 3||$v->type == 6)
                    echo '<span class="label label-warning">手动转账</span>';
                  ?>
                  </td>
                  <td id="lock2<?=$v->id?>">
                  <?php
                  if ($v->type == 2||$v->type == 3)
                    echo '<span class="label label-success">个人团队奖励</span>';
                  if ($v->type == 5||$v->type == 6)
                    echo '<span class="label label-warning">个人销售利润</span>';
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
