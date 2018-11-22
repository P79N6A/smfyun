
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                任务管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">任务宝订阅号版</a></li>
                <li><a href="#">任务设置</a></li>
                <li class="am-active">任务管理</li>
            </ol>
            <form class="am-form" method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=$result['countall']?> 件产品
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtrwda/tasks/add" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 添加新任务</a>
                        </div>

                        <div class="am-u-sm-12 am-u-md-3">
                            <div class="am-input-group am-input-group-sm">
                  <input type="text" name="s" class="am-form-field form-control input-sm pull-right" placeholder="搜索">
                                <span class="am-input-group-btn">
            <button class="am-btn  am-btn-default am-btn-success tpl-am-btn-success am-icon-search" type="submit"></button>
          </span>
                            </div>
                        </div>


                </div>
                <div class="tpl-block">
                    <div class="am-g">
                        <div class="am-u-sm-12">
                                <table class="am-table am-table-striped am-table-hover table-main">
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
                                    <tbody>
                <?php foreach ($result['tasks'] as $key=>$task):?>
                <tr>
                  <?php
                      $num=ORM::factory('qwt_rwdrecord')->where('bid','=',$bid)->where('tid','=',$task->id)->count_all();
                      $rank=ORM::factory('qwt_rwdsku')->where('bid','=',$bid)->where('tid','=',$task->id)->count_all();


                  ?>
                  <td><?=$task->name?></td>
                  <td><?=date('Y-m-d H:i',$task->endtime)?></td>
                  <td><a href="/qwtrwda/items_num/<?=$task->id?>"><span class="label label-warning">点击查看</span></a></td>
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
                  <td nowrap="">
                  <a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwtrwda/tasks/edit/<?=$task->id?>"><span class="am-icon-pencil-square-o"></span> 修改</a>
                  <?php if($task->endtime&&$task->endtime > time()&&$task->begintime!=$task->endtime):?>
                  <a style="background-color:#fff;" class='delete am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$task->id?>">
                  <span>终止该任务</span> <i class="am-icon-times"></i></a>
                <?php endif;?>
                  </td>
                </tr>

                <?php endforeach;?>
                                    </tbody>
                                </table>
                            <div class="am-u-lg-12">
                                <div class="am-cf">

                                    <div class="am-fr">
                                        <ul class="am-pagination tpl-pagination">
                                        <?=$pages?>
                                        </ul>
                                    </div>
                                </div>
                                <hr>
                            </div>

                        </div>

                    </div>
                </div>
                <div class="tpl-alert"></div>
            </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
      $('.delete').click(function(){
      var id = $(this).data('id');
  swal({
    title: "确认要终止吗？",
    text: "该操作不可恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认终止',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwtrwda/tasks?tid="+id+"&DELETE=1";
    })
  })
    </script>
