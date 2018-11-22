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
    提现记录
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/ytya/history_withdrawals">提现记录</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>

<?php
if(!$config['pay']){
  $config['pay']==1;
}
?>
<!-- Main content -->
<section class="content">
<form method="get" name="qrcodesform">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">

            <div class="box-header">
              <h3 class="box-title"><?=$title?> : 共 <?=$result['countall']?> 条记录&nbsp&nbsp&nbsp账户状态: <?=$config['pay']==1? '正常':'余额不足'?></h3>
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
                  <th>头像</th>
                  <th>昵称</th>
                  <th>等级</th>
                  <th>姓名</th>
                  <th>电话</th>
                  <th>提现金额</th>
                  <th>提现时间</th>
                </tr>
                <?php
                foreach ($result['withdrawals'] as $v):
                $information= ORM::factory('yty_qrcode')->where('id', '=', $v->qid)->find();
                  $cksum = md5($information->openid.$config['appsecret'].date('Y-m'));
                  $url = '/yty/index/'. $v->bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($information->openid);
                  $url2 = '/yty/index/'. $v->bid .'?url=customer&cksum='. $cksum .'&openid='. base64_encode($information->openid);
                ?>
                <tr>
                  <td><img src="<?=$information->headimgurl?>" width="32" height="32" title="<?=$information->openid?>"></td>
                  <td><a href="<?=$url?>"><?=$information->nickname?></td>
                  <td><?=$information->agent->skus->name?></td>
                  <td><?=$information->agent->name?></td>
                  <td><?=$information->agent->tel?></td>
                  <td><?=number_format(-($v->score),2)?></td>
                  <td ><?=date("Y-m-d H:i:s",$v->lastupdate)?></a></td>
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
