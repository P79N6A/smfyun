
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
if ($result['ticket']) $title = '已生成海报';
?>

<section class="content-header">
  <h1>
    参与用户
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/fxba/qrcodes">用户明细</a></li>
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
                  <!-- <th>ID</th> -->
                  <th>状态</th>
                  <th>头像</th>
                  <th>昵称</th>
                  <!-- <th>OpenID</th> -->
                  <th nowrap=""><a href="/fxba/qrcodes?sort=money" title="按总收益排序">总收益</a></th>
                  <th nowrap=""><a href="/fxba/qrcodes?sort=score" title="按收益排序">未提现收益</a></th>
                  <th nowrap=""><a href="/fxba/qrcodes?sort=paid" title="按订单收入排序">订单收入</a></th>
                  <th nowrap=""><a href="/fxba/qrcodes?sort=shscore" title="按现有积分排序">现有积分</a></th>
                  <th>性别</th>
                  <th>下线</th>
                  <th>二级</th>
                  <?if($config['kaiguan_needpay']==1):?>
                  <th>三级</th>
                  <?endif;?>
                  <th><a href="/fxba/qrcodes?sort=jointime" title="按关注时间排序">关注时间</a></th>
                  <th>有海报？</th>
                  <th>上线</th>
                  <th>操作</th>
                </tr>

                <?php
                foreach ($result['qrcodes'] as $v):
                  $count2 = ORM::factory('fxb_qrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->count_all();
                  //$count3 = ORM::factory('fxb_score')->where('bid', '=', $v->bid)->where('qid', '=', $v->id)->where('type', 'IN', array(3,8))->count_all();
                  $firstchild=DB::query(Database::SELECT,"SELECT openid FROM fxb_qrcodes WHERE fopenid='$v->openid'")->execute()->as_array();
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
                      $tempdata = ORM::factory('fxb_qrcode')->where('bid', '=', $v->bid)->where('fopenid', 'IN',$tempid)->find_all();
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
                  $count3 = ORM::factory('fxb_qrcode')->where('bid', '=', $v->bid)->where('fopenid', 'IN',$tempid)->count_all();
                  $count4 = ORM::factory('fxb_qrcode')->where('bid', '=', $v->bid)->where('fopenid', 'IN',$resid)->count_all();
                  $fuser = ORM::factory('fxb_qrcode')->where('bid', '=', $v->bid)->where('openid', '=', $v->fopenid)->find();
                ?>

                <tr>
                  <!-- <td><?=$v->id?></td> -->
                  <td id="lock<?=$v->id?>">
                  <?php
                  if ($v->lock > 1)
                    echo '<span class="label label-warning">白名单</span>';
                  elseif ($v->lock == 1)
                    echo '<span class="label label-danger">已锁定</span>';
                  else
                    echo '<span class="label label-success">正常</span>';

                  $cksum = md5($v->openid.$config['appsecret'].date('Y-m'));
                  $url = '/fxb/index/'. $v->bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  ?>
                  </td>

                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->nickname?></td>

                  <td><?=$v->money?></td>

                  <td id="score<?=$v->id?>"><a href="<?=$url?>" target="_blank" title="查看用户前台"><?=$v->score?></a></td>
                  <td><?=$v->paid?></td>
                  <td id="score<?=$v->id?>"><a href="<?=$url?>" target="_blank" title="查看用户前台"><?=$v->shscore?></a></td>
                  <td><?=$sex[$v->sex]?></td>
                  <td><a href="/fxba/qrcodes?fopenid=<?=$v->openid?>" title="查看下线"><?=$count2?></a></td>
                  <td><a href="/fxba/qrcodes?ffopenid=<?=$v->openid?>" title="查看二级"><?=$count3?></a></td>
                  <?if($config['kaiguan_needpay']==1):?>
                  <td><a href="/fxba/qrcodes?fffopenid=<?=$v->openid?>" title="查看三级"><?=$count4?></a></td>
                  <?endif?>
                  <td><?=date('y-m-d H:i', $v->jointime)?></td>
                  <td><?=$v->ticket ? '是' : '否'?></td>
                  <td><a href="/fxba/qrcodes?id=<?=$fuser->id?>"><?=$fuser->nickname?></a></td>
                  <td nowrap="">
                    <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lock="<?=$v->lock?>" data-name="<?=$v->nickname?>">
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
  var lock = button.data('lock')
  var form = '<div class="form-group"><label for="fscore">收益增减（正数为增加、负数为减少）：</label><input class="form-control" id="fscore" name="form[score]" max="999" type="number" style="width:150px"><label for="fscore">积分增减（正数为增加、负数为减少）：</label><input class="form-control" id="shcore" name="form[shscore]" max="999" type="number" style="width:150px"></div>'
  form += '<div class="form-group"><label for="flock">用户状态（加入白名单后不会再自动锁定）：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock0" value="0"'+ (lock==0 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">正常</span></label> <label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock1" value="1"'+ (lock==1 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">已锁定</label> <label class="checkbox-inline"><input type="radio" name="form[lock]" id="flock3" value="3"'+ (lock==3 ? ' checked' : '') +'><span class="label label-warning" style="font-size:14px">白名单</label></div></div>'
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';

  var modal = $(this)
  modal.find('.modal-title').text(name)
  modal.find('.modal-body').html(form)
})
</script>
