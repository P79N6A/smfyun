<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    商户管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">商户管理</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
<form method="get" name="loginsform">

  <div class="row">
    <div class="col-xs-12">
      <a href="/wzbb/logins/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新账号</span></a>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=$result['countall']?> 个商户/剩余可开账号数量<?=$number?></h3>
              <div class="box-tools">
                <div class="input-group" style="width: 150px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>ID</th>
                  <th>商户名</th>
                  <th>登录名</th>
                  <th>密码</th>
                  <th>销售来源</th>
                  <th>有效期</th>
                  <th>剩余流量</th>
                  <th>购买记录</th>
                  <th>最后登录</th>
                  <th>操作</th>
                </tr>

                <?php
                foreach ($result['logins'] as $login):
                  //按账号统计
                  $biz_name = ORM::factory('qwt_wzbcfg')->where('bid', '=', $login->id)->where('key', '=', 'name')->find()->value;
                  $endname='';
                  $beginname='';
                  $aname=ORM::factory('qwt_wzbadmin')->where('id','=',$login->faid)->find()->name;
                  if($login->faid==$login->fadmin){
                    $endname="直销";
                  }else{
                    $beginname=ORM::factory('qwt_wzbadmin')->where('id','=',$login->fadmin)->find()->name.'所属';
                    $endname="销售";
                  }
                  $count_all = ORM::factory('qwt_wzbqrcode')->where('bid', '=', $login->id)->count_all();
                ?>

                <tr>
                  <td><?=$login->id?></td>
                  <td><?=$biz_name?></td>
                  <td><?=$login->user?></td>
                  <td><?=$login->pass?></td>
                  <td><?=$beginname.$aname.$endname?></td>
                  <td>
                  <?php
                  $sql = DB::query(Database::SELECT,"SELECT sum(data) as CT FROM qwt_wzblives where bid=$login->id");
                  $num = $sql->execute()->as_array();
                  $use =  $num[0]['CT'];
                  $all=$login->stream_data;
                  $dif=ceil((strtotime($login->expiretime)-time())/86400);
                  if ($login->expiretime && strtotime($login->expiretime) < time())
                    echo '<span class="label label-danger">已到期</span>';
                  else
                    echo '<span class="label label-success">剩余时间'.$dif.'天</span>';
                  ?>
                  </td>
                  <td><?=number_format($all-number_format($use/(1024*1024*1024),2),2)?>G</td>
                   <td><a href="/wzbb/sales?bid=<?=$login->id?>"><span>点击查看</span> <i class="fa fa-edit"></i></a></td>
                  <td><?=date('m-d H:i', $login->lastlogin)?></td>
                  <td><a href="/wzbb/logins/edit/<?=$login->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
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

</form>
</section><!-- /.content -->
