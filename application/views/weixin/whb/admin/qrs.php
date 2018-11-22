<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    二维码管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">二维码管理</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <a href="/whba/qrs/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新的二维码</span></a>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=count($result['qrs'])?> 个二维码</h3>
              <div class="box-tools">
                <div class="input-group" style="width: 150px;">
                  <input type="text" name="table_search" class="form-control input-sm pull-right" placeholder="搜索">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>名称</th>
                  <th>兑换数量</th>
                  <th>剩余数量</th>
                  <th>金额（元）</th>
                  <th>状态</th>
                  <th>二维码地址</th>
                  <th>上架时间</th>
                  <th>操作</th>
                </tr>

                <?php foreach ($result['qrs'] as $key=>$qr):?>

                <tr>
                  <td><?=$qr->name?></td>
                  <td><a href="/whba/qrcodes?qr_id=<?=$qr->id?>"><?=ORM::factory('whb_qrcode')->where('bid','=',$bid)->where('from_qr','=',$qr->id)->where('status','=',1)->count_all()?></a></td>
                  <td><?=$qr->stock?></td>
                  <td><?=$qr->minprice/100?>~<?=$qr->maxprice/100?></td>
                  <td>
                  <?php
                  if ($qr->endtime && $qr->endtime < time())
                    echo '<span class="label label-danger">已过期</span>';
                  else if ($qr->starttime && $qr->starttime > time())
                    echo '<span class="label label-warning">未开始</span>';
                  else
                    echo '<span class="label label-success">正常</span>';
                  ?>
                  </td>
                  <td><a target="_blank" href="<?=$qr->qrurl?>">点此查看</a></td>
                  <td><?=date('Y-m-d H:i', $qr->lastupdate)?></td>
                  <td><a href="/whba/qrs/edit/<?=$qr->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
                </tr>

                <?php endforeach;?>
              </tbody></table>
            </div><!-- /.box-body -->

            <div class="box-footer clearfix">
              <ul class="pagination pagination-sm no-margin pull-right">
                <li><a href="#">«</a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">»</a></li>
              </ul>
            </div>

          </div>

    </div>
  </div>

</section><!-- /.content -->
