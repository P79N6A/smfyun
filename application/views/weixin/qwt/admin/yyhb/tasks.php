<style type="text/css">
  th, td{
    white-space: nowrap;
  }
</style>
    <div class="tpl-page-container tpl-page-header-fixed">

        <div class="tpl-content-wrapper">
            <div class="tpl-content-page-title">
                活动管理
            </div>
            <ol class="am-breadcrumb">
                <li><a href="#" class="am-icon-home">语音红包</a></li>
                <li><a href="#">活动管理</a></li>
                <li class="am-active">新建活动</li>
            </ol>
            <form class="am-form" method="get">
            <div class="tpl-portlet-components">
                <div class="portlet-title">
                        <div class="am-u-sm-12 am-u-md-6">
                    <div class="caption font-green bold">
                        共 <?=$result['countall']?> 次活动
                    </div>
                    </div>
                        <div class="am-u-sm-12 am-u-md-3">
                        <a href="/qwtyyhba/tasks/add" class="am-btn am-btn-default am-btn-success"><span class="am-icon-plus"></span> 添加新活动</a>
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
                    <div class="am-form-group">
                      <label class="am-u-sm12" style="color:red;font-weight:100;">（用于投放的活动链接：http://yingyong.smfyun.com/smfyun/user_snsapi_userinfo/<?=$bid?>/yyhb/yyhb）</label>
                      </div>
                        <div class="am-u-sm-12" style="overflow:scroll">
                                <table class="am-table am-table-striped am-table-hover table-main">
                                    <thead>
                <tr>
                  <th>活动名称</th>
                  <th>活动有效期</th>
                  <th>点击查看活动领取明细</th>
                  <th>新增粉丝</th>
                  <th>活动参与次数</th>
                  <th>活动中奖数量</th>
                  <th>UV</th>
                  <th>PV</th>
                  <th>奖品总数量</th>
                  <th>活动状态</th>
                  <th>上架时间</th>
                  <th>操作</th>
                </tr>
                                    </thead>
                                    <tbody>
                <?php foreach ($result['tasks'] as $key=>$task):?>
                <tr>
                  <?php
                      $person_num=DB::query(Database::SELECT,"SELECT COUNT(distinct qid) as person_num from qwt_yyhbrecords where tid = $task->id")->execute()->as_array();
                      $person_num=$person_num[0]['person_num'];
                      $join_num=ORM::factory('qwt_yyhbrecord')->where('bid','=',$bid)->where('tid','=',$task->id)->count_all();
                      $prize_num=ORM::factory('qwt_yyhbrecord')->where('bid','=',$bid)->where('tid','=',$task->id)->where('flag','=',1)->count_all();
                      // $join_num=DB::query(Database::SELECT,"SELECT SUM(jointime) as join_num from qwt_yyhbrecords where tid = $task->id")->execute()->as_array();
                      // $join_num=$join_num[0]['join_num'];
                      // $prize_num=DB::query(Database::SELECT,"SELECT SUM(redtime) as prize_num from qwt_yyhbrecords where tid = $task->id")->execute()->as_array();
                      // $prize_num=$prize_num[0]['prize_num'];
                      $all_num=DB::query(Database::SELECT,"SELECT SUM(stock) as all_num from qwt_yyhbskus where tid = $task->id")->execute()->as_array();
                      $all_num=$all_num[0]['all_num'];
                      //$rank=ORM::factory('qwt_yyhbsku')->where('bid','=',$bid)->where('tid','=',$task->id)->count_all();
                      $pv=ORM::factory('qwt_yyhbuv')->where('bid','=',$bid)->where('tid','=',$task->id)->count_all();
                      $uv=DB::query(Database::SELECT,"SELECT count(distinct qid) as num from qwt_yyhbuvs where bid = $bid and tid = $task->id")->execute()->as_array();


                  ?>
                  <td><?=$task->name?></td>
                  <td><?=date('Y-m-d H:i',$task->endtime)?></td>
                  <td><a href="/qwtyyhba/items_num/<?=$task->id?>"><span class="label label-warning">点击查看</span></a></td>
                  <td><?=$person_num?$person_num:0?></td>
                  <td><?=$join_num?></td>
                  <td><?=$prize_num?></td>
                  <td><?=$uv[0]['num']?></td>
                  <td><?=$pv?></td>
                  <td><?=$all_num?$all_num:0?></td>
                  <td>
                  <?php
                  if ($task->flag==0)
                    echo '<span class="label label-danger">已失效</span>';
                  elseif($task->endtime && $task->endtime < time())
                    echo '<span class="label label-danger">已过期</span>';
                  elseif($task->begintime&&$task->begintime>time())
                    echo '<span class="label label-danger">未开始</span>';
                  else
                    echo '<span class="label label-success">正常</span>';
                  ?>
                  </td>
                  <td><?=date('Y-m-d H:i', $task->begintime)?></td>
                  <td>
                  <a style="background-color:#fff;" class='edit am-btn am-btn-default am-btn-xs am-text-secondary' href="/qwtyyhba/tasks/edit/<?=$task->id?>"><span class="am-icon-pencil-square-o"></span> 修改</a>
                  <?php if ($task->flag==1):?>
                  <?php if($task->begintime&&$task->begintime>time()):?>
                  <a style="background-color:#fff;" class='trash am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$task->id?>"><span class="am-icon-trash"></span> 删除</a>
                  <?php else:?>
                  <a style="background-color:#fff;" class='delete am-btn am-btn-default am-btn-xs am-text-secondary' data-id="<?=$task->id?>"><span class="am-icon-calendar-times-o"></span> 终止</a>
                  <?php endif?>
                  <?php endif?>
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
      var tid = $(this).data('id');
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
      window.location.href = "/qwtyyhba/tasks/edit/"+tid+"?DELETE=1";
    })
  })
    $('.trash').click(function(){
      var tid = $(this).data('id');
  swal({
    title: "确认要删除吗？",
    text: "该操作不可恢复！",
    type: "warning",
    showCancelButton: true,
    confirmButtonColor: '#DD6B55',
    cancelButtonText: '取消',
    confirmButtonText: '确认删除',
    closeOnConfirm: false
    },
    function(){
      window.location.href = "/qwtyyhba/tasks/edit/"+tid+"?TRASH=1";
    })
  })
    </script>
