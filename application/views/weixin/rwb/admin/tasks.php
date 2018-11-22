
<style>
.label {font-size: 14px}
</style>

<section class="content-header">
  <h1>
    任务管理
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">任务管理</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <a href="/rwba/tasks/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新任务</span></a>
    </div>
  </div>

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title">共 <?=$result['countall']?> 个任务</h3>
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
                  <th>任务名称</th>
                  <th>任务截止时间</th>
                  <th>点击查看任务奖品发放详情</th>
                  <th>任务参与人数</th>
                  <th>任务级数</th>
                  <th>任务状态</th>
                  <th>上架时间</th>
                  <th>操作</th>
                </tr>
                <?php foreach ($result['tasks'] as $key=>$task):?>

                <tr>
                  <?php
                      $num=ORM::factory('rwb_record')->where('bid','=',$bid)->where('tid','=',$task->id)->count_all();
                      $rank=ORM::factory('rwb_sku')->where('bid','=',$bid)->where('tid','=',$task->id)->count_all();


                  ?>
                  <td><?=$task->name?></td>
                  <td><?=date('Y-m-d H:i',$task->endtime)?></td>
                  <td><a href="/rwba/items_num/<?=$task->id?>"><span class="label label-warning">点击查看</span></a></td>
                  <td><?=$num?></td>
                  <td><?=$rank?></td>
                  <td>
                  <?php
                  if ($task->endtime && $task->endtime < time())
                    echo '<span class="label label-danger">已过期</span>';
                  elseif($task->endtime && $task->begintime==$task->endtime)
                    echo '<span class="label label-danger">已过期</span>';
                  elseif($task->begintime&&$task->begintime>time())
                    echo '<span class="label label-danger">未开始</span>';
                  else
                    echo '<span class="label label-success">正常</span>';
                  ?>
                  </td>
                  <td><?=date('Y-m-d H:i', $task->lastupdate)?></td>
                  <td><a href="/rwba/tasks/edit/<?=$task->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
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

</section><!-- /.content -->
