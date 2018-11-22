
<style>
.label {font-size: 14px}
</style>
<section class="wrapper" style="width:85%;float:right;">
 <div wclass="wrapper">
      <div class="row">
          <div class="page-heading">
            <h3>
                任务管理
                <small><?=$desc?></small>
              </h3>
         </div>
<ul class="breadcrumb" style="margin-left:15px">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li class="active">任务管理</li>
</ul>


                <div class="col-sm-12">
                <section class="panel">
                <header class="panel-heading">
                    共 <?=count($result['items'])?> 件产品
                    <span class="tools pull-right">
                        <a href="/qwtrwba/tasks/add" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px;height:40px"> <i class="fa fa-plus"></i> &nbsp; <span>添加新任务</span></a>
                     </span>
                </header>
                 <div class="input-group" style="width: 150px;margin-left:15px">
                  <input type="text" name="table_search" class="form-control input-sm pull-right" placeholder="搜索">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                <div class="panel-body">
                <div class="adv-table editable-table ">
                <div class="clearfix">
                <table class="table table-striped table-hover table-bordered">
                <thead>
                <tr>
                  <th>任务名称</th>
                  <th>任务截止时间</th>
                  <th>点击查看任务奖品发放详情</th>
                  <th>任务参与人数</th>
                  <th>任务级数</th>
                  <th>任务状态</th>
                  <th>上架时间</th>
                  <th>操作</th>
                </tr>
                </thead>
                <?php foreach ($result['tasks'] as $key=>$task):?>
                <tbody>
                <tr>
                  <?php
                      $num=ORM::factory('qwt_rwbrecord')->where('bid','=',$bid)->where('tid','=',$task->id)->count_all();
                      $rank=ORM::factory('qwt_rwbsku')->where('bid','=',$bid)->where('tid','=',$task->id)->count_all();


                  ?>
                  <td><?=$task->name?></td>
                  <td><?=date('Y-m-d H:i',$task->endtime)?></td>
                  <td><a href="/qwtrwba/items_num/<?=$task->id?>"><span class="label label-warning">点击查看</span></a></td>
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
                  <td><a href="/qwtrwba/tasks/edit/<?=$task->id?>"><span>修改</span> <i class="fa fa-edit"></i></a></td>
                </tr>

                <?php endforeach;?>
              </tbody></table>
                </div>
            <div class="box-footer clearfix">
                <?=$pages?>
            </div>
                </div>
                </section>
                </div>
                </div>
        </div>
        <!--body wrapper end-->

</section><!-- /.content -->
