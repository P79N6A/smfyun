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
    <li><a href="/qwtwfba/qrcodes">用户明细</a></li>
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
                  <th><a href="/qwtwfba/qrcodes?sort=score&amp;s=<?=$result['s']?>&amp;lock=<?=$result['lock']?>" title="按积分排序">现有积分</a></th>
                  <th>兑换数量</th>
                  <th>性别</th>
                  <th>直接粉丝</th>
                  <th>间接粉丝</th>
                  <th><a href="/qwtwfba/qrcodes?sort=jointime&amp;s=<?=$result['s']?>&amp;lock=<?=$result['lock']?>" title="按关注时间排序">关注时间</a></th>
                  <th>是否有生成海报？</th>
                  <th>来源用户</th>
                  <th>操作</th>
                </tr>

                <?php
                foreach ($result['qrcodes'] as $v):
                  $count2 = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($v->bid))->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->count_all();
                  $firstchild=DB::query(Database::SELECT,"SELECT openid FROM qwt_wfbqrcodes WHERE fopenid='$v->openid'")->execute()->as_array();
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
                  $count3 = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($v->bid))->where('bid', '=', $v->bid)->where('fopenid', 'IN',$tempid)->count_all();
                  $fuser='';
                  if($v->fopenid){
                      $fuser = ORM::factory('qwt_wfbqrcode','',Model::factory('select_qwtorm')->selectorm($v->bid))->where('bid', '=', $v->bid)->where('openid', '=', $v->fopenid)->find();
                  }
                  $count4 =ORM::factory('qwt_wfborder')->where('bid','=',$v->bid)->where('qid','=',$v->id)->count_all();
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
                  <?php
                  $cksum = md5($v->openid.$config['appsecret'].date('Y-m-d'));
                  $url = '/qwtwfb/index/'. $v->bid .'?url=score&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  ?>
                  <td id="score<?=$v->id?>"><a href="<?=$url?>" target="_blank" title="查看用户前台"><?=$v->score?></a></td>
                  <td id="score1<?=$v->id?>"><a href="/wfba/orders?qid=<?=$v->id?>" title="查看兑换明细"><?=$count4?></a></td>
                  <td><?=$sex[$v->sex]?></td>
                  <td><a href="/qwtwfba/qrcodes?fopenid=<?=$v->openid?>" title="查看推荐明细"><?=$count2?></a></td>
                  <td><a href="/qwtwfba/qrcodes?ffopenid=<?=$v->openid?>" title="查看推荐明细"><?=$count3?></a></td>
                  <td><?=date('Y-m-d H:i', $v->jointime)?></td>
                  <td><?=$v->ticket ? '是' : '否'?></td>
                  <td><a href="/qwtwfba/qrcodes?id=<?=$fuser->id?>"><?=$fuser->nickname?></a></td>
                  <td nowrap="">
                    <a class='edit' href="#myModal" data-toggle="modal" data-id="<?=$v->id?>" data-lock="<?=$v->lock?>" data-name="<?=$v->nickname?>">
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
            <label for="fscore">积分增减（正数为增加、负数为减少）：
            </label>
            <input class="form-control" id="fscore" name="form[score]" max="999" style="width:150px" type="number"></div>
            <div class="form-group">
                <label for="flock">用户状态：</label>
                <div style="margin-left:-20px">
                    <label class="checkbox-inline">
                        <input class='checkedit' name="form[lock]" id="flock0" value="0" checked="" type="radio">
                        <span class="label label-success" style="font-size:14px">正常</span>
                    </label>
                    <label class="checkbox-inline" style="margin-left:-15px">
                        <input class='checkedit' name="form[lock]" id="flock1" value="1" type="radio"><span class="label label-danger" style="font-size:14px">已锁定</span></label>
                     <label class="checkbox-inline" style="margin-left:-15px">
                        <input class='checkedit' name="form[lock]" id="flock3" value="3" type="radio">
                        <span class="label label-warning" style="font-size:14px">白名单</span>
                    </label>
                    <label class="checkbox-inline" style="margin-left:-15px"><input class='checkedit' name="form[lock]" id="flock4" value="4" type="radio"><span class="label label-info" style="font-size:14px">隐身用户</span></label></div> <p class="help-block">1、加入白名单后不会自动锁定<br>2、隐身用户不会出现在积分排行中</p> </div><input class='edithidden' name="form[id]" value='' type="hidden"></div>
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

