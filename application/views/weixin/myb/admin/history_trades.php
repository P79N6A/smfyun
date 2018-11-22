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
    订单记录
    <small></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/myba/qrcodes">订单记录</a></li>
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
                  <th>性别</th>
                  <th>商品名称</a></th>
                  <th>购买数量</a></th>

                  <th>购买金额</a></th>
                  <th>购买时间</th>
                  <th>上线</th>
                  <th>需结算的佣金</th>
                </tr>

                <?php
                foreach ($result['trades'] as $v):
                //   $count2 = ORM::factory('myb_qrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->count_all();
                //   $count3 = ORM::factory('myb_score')->where('bid', '=', $v->bid)->where('qid', '=', $v->id)->where('type', 'IN', array(3,8))->count_all();
                //   $fuser = ORM::factory('myb_qrcode')->where('bid', '=', $v->bid)->where('openid', '=', $v->fopenid)->find();
                  //$information=ORM::factory('myb_qrcode',array('id'=>$v->qid,'bid'=>$v->bid))->find();
                $information=ORM::factory('myb_qrcode')->where('id','=',$v->qid)->find();
                $fid = ORM::factory('myb_score')->where('tid', '=', $v->id)->where('type', '=', 2)->find();
                $fuser = ORM::factory('myb_qrcode')->where('id', '=', $fid->qid)->find();

                ?>

                <tr>
                  <td><img src="<?=$information->headimgurl?>" width="32" height="32" title="<?=$information->openid?>"></td>
                  <td><?=$information->nickname?></td>
                  <td><?=$sex[$information->sex]?></td>
                  <td><?=$v->title?></td>
                  <td ><?=$v->num?></a></td>

                  <td><?=$v->payment?></td>
                  <td><?=$v->pay_time?></td>
                  <td><a href="/myba/qrcodes?id=<?=$fuser->id?>"><?=$fuser->nickname?></a></td>
                  <td><?=$v->money0+$v->money1+$v->money2+$v->money3?></td>
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
