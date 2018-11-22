<script src="http://cdn.bootcss.com/jquery/2.0.0/jquery.min.js"></script>
<style>
.label {font-size: 14px}
</style>

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
if ($result['fuser']) $title = $result['fuser']->nickname.'的直接粉丝';
if ($result['ffuser']) $title = $result['ffuser']->nickname.'的间接粉丝';
if ($result['id'] || $result['s']) $title = '搜索结果';
if ($result['ticket']) $title = '已生成海报';
?>

<section class="wrapper" style="width:85%;float:right;background:white">

  <h3>
    参与用户
    <small><?=$desc?></small>
  </h3>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/qwtrwba/qrcodes">用户明细</a></li>
    <li class="active"><?=$title?></li>
  </ol>
<form method="get" name="qrcodesform">

  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title"><?=$title?>：共 <?=$result['countall']?> 个用户</h3>
              <div class="box-tools">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>状态</th>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>任务数量</a></th>
                  <th>奖品数量</th>
                  <th>性别</th>
                  <th>直接粉丝</th>
                  <th><a href="/qwtrwba/qrcodes?sort=jointime&amp;s=<?=$result['s']?>&amp;lock=<?=$result['lock']?>" title="按关注时间排序">关注时间</a></th>
                  <th>是否有生成海报？</th>
                  <th>来源用户</th>
                  <th>来源活动</th>
                  <th>操作</th>
                </tr>

               <?php
                foreach ($result['qrcodes'] as $v):
                  $count2 = ORM::factory('qwt_rwbqrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->count_all();
                  $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_rwbqrcodes WHERE fopenid='$v->openid'")->execute()->as_array();
                  $tempid=array();
                    if($firstchild[0]['openid']==null)
                    {
                      $tempid=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
                    }
                    else
                    {
                      for($i=0;$firstchild[$i];$i++)
                      {
                        $tempid[$i]=$firstchild[$i]['openid'];
                      }
                    }
                  $count3 = ORM::factory('qwt_rwbqrcode')->where('bid', '=', $v->bid)->where('fopenid', 'IN',$tempid)->count_all();
                  $fuser = ORM::factory('qwt_rwbqrcode')->where('bid', '=', $v->bid)->where('openid', '=', $v->fopenid)->find();
                  $task_name=ORM::factory('qwt_rwbrecord')->where('bid','=',$v->bid)->where('fqid','!=','')->where('fqid','=',$fuser->id)->where('qid','=',$v->id)->find()->task->name;
                  $count4 =ORM::factory('qwt_rwborder')->where('bid','=',$v->bid)->where('qid','=',$v->id)->count_all();
                  $sql = DB::query(Database::SELECT,"SELECT `tid`, count(*) AS t_num from qwt_rwborders where `qid` = $v->id  group by tid ");
                  $result2=$sql->execute()->as_array();
                ?>

                <tr>
                  <td id="lock<?=$v->id?>">
                  <?php
                  if ($v->lock == 3)
                    echo '<span class="label label-warning">白名单</span>';
                  elseif ($v->lock == 4)
                    echo '<span class="label label-info">隐身用户</span>';
                  elseif ($v->lock == 1)
                    echo '<span class="label label-danger">已锁定</span>';
                  else
                    echo '<span class="label label-success">正常</span>';
                  ?>
                  </td>

                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->nickname?></td>
                  <td ><?=$result2[0]['t_num']?$result2[0]['t_num']:0?></td>

                  <td id="score1<?=$v->id?>"><a href="/rwba/orders?qid=<?=$v->id?>" title="查看奖品明细"><?=$count4?></a></td>
                  <td><?=$sex[$v->sex]?></td>
                  <td><a href="/qwtrwba/qrcodes?fopenid=<?=$v->openid?>" title="查看推荐明细"><?=$count2?></a></td>

                  <td><?=date('Y-m-d H:i', $v->jointime)?></td>
                  <td id="subscribe<?=$v->id?>">
                    <?php
                    if($v->subscribe==0)
                      echo '<span class="label label-danger">已跑路</span>';
                    else
                      echo '<span class="label label-success">关注</span>';
                    ?>
                  </td>
                  <td><?=$v->ticket ? '是' : '否'?></td>
                  <td><a href="/qwtrwba/qrcodes?id=<?=$fuser->id?>"><?=$fuser->nickname?></a></td>
                  <td><?=$task_name?></td>
                  <td nowrap="">
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lock="<?=$v->lock?>" data-name="<?=$v->nickname?>">
                      <span>修改用户</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
                </tr>
                  <script type="text/javascript">
                    $('.edit').click(function(){
                      $(".edithidden").attr("value",$(this).data('id'));
                      $(".nickname").text($(this).data('name'));
                      $('.checkedit').removeAttr("checked");
                      var i = $(this).data('lock');
                      $('#flock'+i).prop("checked","checked");
                    })
                </script>
                <?php endforeach;?>
              </tbody></table>
            </div><!-- /.box-body -->

              <div class="box-footer clearfix">
                <?=$pages?>
              </div>

            </div>

          </div>

    </div>
  </div>

</form>
</section><!-- /.content -->
 <!-- Modal -->
                            <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title nickname">Edit Media Gallery</h4>
                                        </div>

<div class="modal-body row">

    <div class="col-md-9 img-modal">


            <div class="modal-content">

      <div class="modal-body">
      <form method="post" >
        <div class="form-group">
            <label for="flock">用户状态：</label>
            <div style="margin-left:-20px">
                <label class="checkbox-inline">
                    <input class='checkedit' name="form[lock]" id="flock0" value="0" checked="" type="radio">
                    <span class="label label-success" style="font-size:14px">正常</span>
                </label>
                <label class="checkbox-inline" style="margin-left:-15px">
                    <input class='checkedit' name="form[lock]" id="flock1" value="1" type="radio"><span class="label label-danger" style="font-size:14px">已锁定</span></label>
            <div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">修改用户</button>
      </div>
      </form>
    </div>
     <div class="col-md-7">
    </div>

</div>

            </div>
                                </div>
                            </div>
                            <!-- modal -->

