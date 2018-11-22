
<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    代理商管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">代理商管理</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
<form method="get" name="loginsform">

  <div class="row">
    <div class="col-xs-12">
      <a href="/wzbb/admins/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新代理商</span></a>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=$result['countall']?> 个代理商</h3>
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
                  <th>登录名</th>
                  <th>密码</th>
                  <th>代理商名称</th>
                  <th>状态</th>
                  <th>订单记录</th>
                  <th>最后登录</th>
                  <th>操作</th>
                </tr>
                <?php
                foreach ($result['logins'] as $login):
                ?>
                <tr>
                  <td><?=$login->id?></td>
                  <td><?=$login->user?></td>
                  <td><?=$login->pass?></td>
                  <td><?=$login->name?></td>
                  <td>
                  <?php
                  if ($login->flag==1)
                    echo '<span class="label label-success">正常</span>';
                  else
                    echo '<span class="label label-danger">锁定</span>';
                  ?>
                  </td>
                  <td><a href="/wzbb/sales?aid=<?=$login->id?>"><span>点击查看</span> <i class="fa fa-edit"></i></a></td>
                  <td><?=date('m-d H:i', $login->lastlogin)?></td>
                  <td><a href="/wzbb/admins/edit/<?=$login->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
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
