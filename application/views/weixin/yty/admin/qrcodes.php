
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
    用户
    <small><?=$desc?></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/ytya/qrcodes">经销商审核</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>


<!-- Main content -->
<section class="content">


  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title"><?=$title?>：共 <?=$result['countall']?> 个用户</h3>
              <div class="box-tools">
              <form method="get" name="qrcodesform">
                <div class="input-group" style="width: 250px;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">

                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                </form>
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <th>是否S级经销商</th>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>姓名</th>
                  <th>电话</th>
                  <th>身份证号</th>
                  <th>地址</th>
                  <th>结算账号</th>
                  <th>申请的经销商等级</th>
                  <th>所属经销商</th>
                  <th>需缴纳的代理金</th>
                  <th>审核进度</th>
                  <th>操作</th>
                </tr>

                <?php
                foreach ($result['qrcodes'] as $v):
                  $count2 = ORM::factory('yty_qrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->count_all();
                  $fopenid=$v->fopenid;
                  $fuser=ORM::factory('yty_qrcode')->where('bid','=',$v->bid)->where('openid','=', $fopenid)->find();
                  $fname=$fuser->nickname;
                ?>

                <tr>
                 <td id="lock<?=$v->id?>">
                  <?php
                  if ($v->s == 1){
                    echo '<span class="label label-success">是</span>';
                  }else {
                    echo '<span class="label label-warning">否</span>';
                  }
                  $cksum = md5($v->openid.$config['appsecret'].date('Y-m'));
                  $url = '/yty/index/'. $v->bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  $url2 = '/yty/index/'. $v->bid .'?url=customer&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  ?>
                  </td>
                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->nickname?></td>
                  <td><?=$v->agent->name?></td>
                  <td><?=$v->agent->tel?></td>
                  <td><?=$v->agent->id_card?></td>
                  <td><?=$v->agent->address?></td>
                  <td><?=$v->agent->account?></td>
                  <td><?=$v->agent->skus->name?></td>
                  <td><?=$fname?></td>
                  <td><?=$v->agent->money?></td>
                  <td id="lock<?=$v->id?>">
                  <?php
                  if ($v->lv == 1)
                    echo '<span class="label label-success">正常</span>';
                  if ($v->lv == 2)
                    echo '<span class="label label-warning">待审核</span>';
                  if ($v->lv == 3)
                    echo '<span class="label label-danger">已取消</span>';
                  $cksum = md5($v->openid.$config['appsecret'].date('Y-m'));
                  $url = '/yty/index/'. $v->bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  $url2 = '/yty/index/'. $v->bid .'?url=customer&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  ?>
                  </td>
                  <td nowrap="">
                  <?php if($v->s==1) :?>
                  <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lv="<?=$v->lv?>" data-name="<?=$v->nickname?>" data-tel='<?=$v->agent->tel?>' data-uname='<?=$v->agent->name?>' data-card ='<?=$v->agent->id_card?>' data-address ='<?=$v->agent->address?>'>
                      <span>修改用户</span> <i class="fa fa-edit"></i>
                    </a>
                  <?php endif; ?>
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
</section><!-- /.content -->
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
  var uname = button.data('uname')
  var tel = button.data('tel')
  var card = button.data('card')
  var address = button.data('address')
  var lv = button.data('lv')
  var form = '<div class="form-group"><label for="fscore">姓名：</label><input class="form-control" id="fscore" name="form[name]" max="999" type="text" style="width:150px" value='+uname+'><div class="form-group"><label for="fscore">电话：</label><input class="form-control" id="fscore" name="form[tel]" max="" type="number" style="width:150px" value='+tel+'><div class="form-group"><label for="fscore">身份证号：</label><input class="form-control" id="fscore" name="form[id_card]" type="text" style="width:250px" value='+card+'><div class="form-group"><label for="fscore">地址：</label><input class="form-control" id="fscore" name="form[address]" type="text" style="width:250px" value='+address+'>'
  form += '<div class="form-group"><label for="flock">用户状态：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[lv]" id="flock0" value="1"'+ (lv==1 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">通过</span></label><label class="checkbox-inline"><input type="radio" name="form[lv]" id="flock3" value="3"'+ (lv==3 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">不通过</label></div></div>'
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';
  var modal = $(this)
  modal.find('.modal-title').text(name)
  modal.find('.modal-body').html(form)
})
</script>
