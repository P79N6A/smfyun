<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    代理分组
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">代理分组设置</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <a href="/wsda/group/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新的代理分组</span></a>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=count($result['group'])?> 组</h3>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>组ID</th>
                  <th>分组名称</th>
                  <th>当前分组代理人数</th>
                  <th>创建时间</th>
                  <th>操作</th>
                </tr>

                <?php foreach ($result['group'] as $key=>$group):?>
                  <?php $sum = ORM::factory('wsd_qrcode')->where('bid','=',$bid)->where('group_id','=',$group->id)->count_all()?>
                <tr>
                  <td><?=$group->id?></td>
                  <td><?=$group->name?></td>
                  <td><a href="/wsda/qrcodes_m/?group=<?=$group->id?>"><?=$sum?></a></td>
                  <td><?=date('Y-m-d H:i:s',$group->lastupdate)?></td>
                  <td><a href="/wsda/group/edit/<?=$group->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
                </tr>

                <?php endforeach;?>
              </tbody></table>
            </div><!-- /.box-body -->
          </div>

    </div>
  </div>

</section><!-- /.content -->
