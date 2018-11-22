
<style>
.label {font-size: 14px}
</style>

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
if ($result['fuser']) $title = $result['fuser']->nickname.'的下线';
if ($result['ffuser']) $title=$result['ffuser']->nickname.'的二级';
if ($result['id'] || $result['s']) $title = '搜索结果';
?>

<section class="content-header">
  <h1>
    参与用户
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/ytba/qrcodes">用户明细</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">
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
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <th>头像</th>
                  <th>昵称</th>
                  <th nowrap=""><a href="/ytba/qrcodes?sort=lv" title="按等级排序">等级</a></th>
                  <th nowrap=""><a href="/ytba/qrcodes?sort=all_score" title="按累计积分排序">累计积分</a></th>
                  <th nowrap=""><a href="/ytba/qrcodes?sort=score" title="按现有积分排序">现有积分</a></th>
                  <th>待结算积分</th>
                  <th>性别</th>
                  <th>下线</th>
                  <th>二级</th>
                  <th><a href="/ytba/qrcodes?sort=jointime" title="按关注时间排序">关注时间</a></th>
                  <th>上线</th>
                  <th>操作</th>
                </tr>

                <?php
                foreach ($result['qrcodes'] as $v):
                  $score_sum1=ORM::factory('ytb_trade')->where('status','!=','TRADE_BUYER_SIGNED')->where('qid','=',$v->id)->select(array('SUM("score1")', 'score1s'))->find()->score1s;
                  $firstchild=DB::query(Database::SELECT,"SELECT * FROM ytb_qrcodes Where `bid` = $v->bid and `fopenid`='$v->openid' ")->execute()->as_array();
                  $tempid=array();
                  $tempids=('!!!');
                  if($firstchild[0]['openid']==null){
                    $tempid=array('0' =>'!!!');
                    $tempids=('!!!');//没有二级时 匹配一个不存在的；
                  }else{
                    for($i=0;$firstchild[$i];$i++){
                      $tempid[$i]=$firstchild[$i]['id'];
                      //$tempids[$i]=$firstchild[$i]['openid'];
                      $tempids=$tempids."'".$firstchild[$i]['openid']."',";
                    }
                    //$the_uname ="uname in(".$uname."'')";
                  }
                  $the_uname ="fopenid in(".$tempids."'')";
                  $score_sum2=ORM::factory('ytb_trade')->where('status','!=','TRADE_BUYER_SIGNED')->where('qid','in',$tempid)->select(array('SUM("score2")', 'score2s'))->find()->score2s;
                  //$lastchild=DB::query(Database::SELECT,"SELECT  * from ytb_qrcode where  FIND_IN_SET(fopenid,(select openid  from ytb_qrcode where  `bid` = $v->bid and `fopenid`='$v->openid' )")->execute()->as_array();
                  $lastchild=DB::query(Database::SELECT,"SELECT * FROM ytb_qrcodes Where `bid` = $v->bid and ".$the_uname." ")->execute()->as_array();
                  $tempid1=array();
                  if($lastchild[0]['openid']==null){
                    $tempid1=array('0' =>'!!!');//没有二级时 匹配一个不存在的；
                  }else{
                    for($i=0;$lastchild[$i];$i++){
                      $tempid1[$i]=$lastchild[$i]['id'];
                    }
                  }
                  $score_sum3=ORM::factory('ytb_trade')->where('status','!=','TRADE_BUYER_SIGNED')->where('qid','in',$tempid1)->select(array('SUM("score3")', 'score3s'))->find()->score3s;
                  $score_sum=$score_sum1+$score_sum2+$score_sum3;

                  $count2 = ORM::factory('ytb_qrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->count_all();
                  //$count3 = ORM::factory('ytb_score')->where('bid', '=', $v->bid)->where('qid', '=', $v->id)->where('type', 'IN', array(3,8))->count_all();
                  $firstchild=DB::query(Database::SELECT,"SELECT openid FROM ytb_qrcodes WHERE fopenid='$v->openid'")->execute()->as_array();
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
                  if($config['kaiguan_needpay']==1){
                      $tempdata = ORM::factory('ytb_qrcode')->where('bid', '=', $v->bid)->where('fopenid', 'IN',$tempid)->find_all();
                      // var_dump($tempdata);
                      $i=0;
                      $resid=array();
                      foreach ($tempdata as $res) {
                        $resid[$i]=$res->openid;
                        $i++;
                      }
                  }
                  //var_dump($resid);
                  if($resid[0]==null) $resid=array('0' =>'!!!');//没有三级时 匹配一个不存在的；
                  $count3 = ORM::factory('ytb_qrcode')->where('bid', '=', $v->bid)->where('fopenid', 'IN',$tempid)->count_all();
                  $count4 = ORM::factory('ytb_qrcode')->where('bid', '=', $v->bid)->where('fopenid', 'IN',$resid)->count_all();
                  $fuser = ORM::factory('ytb_qrcode')->where('bid', '=', $v->bid)->where('openid', '=', $v->fopenid)->find();
                ?>

                <tr>
                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->nickname?></td>
                  <td id="lv<?=$v->lv?>">
                  <?php
                  if($v->lv == 0)
                    echo '<span class="label label-success">'.$config['pool_name'].'</span>';
                  elseif($v->lv == 1)
                    echo '<span class="label label-danger">'.$config['third'].'</span>';
                  elseif($v->lv == 2)
                    echo '<span class="label label-warning">'.$config['second'].'</span>';
                  elseif($v->lv == 3)
                    echo '<span class="label label-primary">'.$config['first'].'</span>';
                  $cksum = md5($v->openid.$config['appsecret'].date('Y-m'));
                  $url = '/ytb/index/'. $v->bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  ?>
                  </td>
                  <td><?=$v->all_score?></td>
                  <td id="score<?=$v->id?>"><a href="<?=$url?>" target="_blank" title="查看用户前台"><?=$v->score?></a></td>
                  <td><?=$score_sum?></td>
                  <td><?=$sex[$v->sex]?></td>
                  <td><a href="/ytba/qrcodes?fopenid=<?=$v->openid?>" title="查看下线"><?=$count2?></a></td>
                  <td><a href="/ytba/qrcodes?ffopenid=<?=$v->openid?>" title="查看二级"><?=$count3?></a></td>
                  <td><?=date('y-m-d H:i', $v->jointime)?></td>
                  <td><a href="/ytba/qrcodes?id=<?=$fuser->id?>"><?=$fuser->nickname?></a></td>
                  <td nowrap="">
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lv="<?=$v->lv?>" data-name="<?=$v->nickname?>">
                      <span>修改用户</span> <i class="fa fa-edit"></i>
                    </a>
                  </td>
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

<div class="modal" id="actionModel">
  <div class="modal-dialog">
    <form id="shipform" method="post">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <h4 class="modal-title">修改用户</h4>
      </div>
      <div class="modal-body">&nbsp;</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">修改用户</button>
      </div>
    </div><!-- /.modal-content -->
    </form>
  </div><!-- /.modal-dialog -->
</div>

<script>
$('#actionModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var id = button.data('id')
  var name = button.data('name')
  var lv = button.data('lv')
  var first = "<?=$config['first']?>";
  var second = "<?=$config['second']?>";
  var third = "<?=$config['third']?>";
  var pool_name = "<?=$config['pool_name']?>";

  var form = '<div class="form-group"><label for="all_score">累计积分（正数为增加、负数为减少）：</label><input class="form-control" id="all_score" name="form[all_score]"  type="number" style="width:150px"><label for="score">现有积分（正数为增加、负数为减少。注意：修改现有积分累计积分也会相应改变）：</label><input class="form-control" id="score" name="form[score]"  type="number" style="width:150px"></div>'
  form += '<div class="form-group"><label for="flock">用户等级（注意：修改用户等级时请对应修改用户累计积分!!）：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[lv]" id="flock1" value="1"'+ (lv==1 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">'+third+'</label> <label class="checkbox-inline"><input type="radio" name="form[lv]" id="flock2" value="2"'+ (lv==2 ? ' checked' : '') +'><span class="label label-warning" style="font-size:14px">'+second+'</label><label class="checkbox-inline"><input type="radio" name="form[lv]" id="flock3" value="3"'+ (lv==3 ? ' checked' : '') +'><span class="label label-primary" style="font-size:14px">'+first+'</label></div></div>'
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';

  var modal = $(this)
  modal.find('.modal-title').text(name)
  modal.find('.modal-body').html(form)
})
</script>
