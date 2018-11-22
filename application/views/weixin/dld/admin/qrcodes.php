
<style>
.label {font-size: 14px}
.search-bar{
  padding-left: 10px;font-size: 14px;color: #666666;padding-bottom: 10px;
}
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
    <li><a href="/dlda/qrcodes">用户明细</a></li>
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
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称，姓名，电话，备注，地址搜索" value="<?=htmlspecialchars($result['s'])?>">

                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                </form>
              </div>
            </div><!-- /.box-header -->
            <div class="search-bar">
                状态：
                  <select id='type'>
                    <option value="all" <?=!$_GET['type']?'selected':''?>>全部</option>
                    <option value="2" <?=$_GET['type']==2?'selected':''?>>待审核</option>
                    <option value="4" <?=$_GET['type']==4?'selected':''?>>已驳回</option>
                  </select>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody><tr>
                  <!-- <th>ID</th> -->
                  <th>状态</th>
                  <th>头像</th>
                  <th>昵称</th>
                  <th>姓名</th>
                  <th>电话</th>
                  <th>店铺地址</th>
                  <th>性别</th>
                  <th>备注</th>
                  <th>操作</th>
                </tr>

                <?php
                foreach ($result['qrcodes'] as $v):
                  $count2 = ORM::factory('dld_qrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->count_all();
                ?>

                <tr>
                  <!-- <td><?=$v->id?></td> -->
                  <td id="lock<?=$v->id?>">
                  <?php
                  if ($v->lv == 1)
                    echo '<span class="label label-success">正常</span>';
                  if ($v->lv == 2)
                    echo '<span class="label label-warning">待审核</span>';
                  if ($v->lv == 3)
                    echo '<span class="label label-danger">已取消</span>';
                  if ($v->lv == 4)
                    echo '<span class="label label-primary">被驳回</span>';
                  $cksum = md5($v->openid.$config['appsecret'].date('Y-m'));
                  $url = '/dld/index/'. $v->bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  $url2 = '/dld/index/'. $v->bid .'?url=customer&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  ?>
                  </td>

                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><?=$v->nickname?></td>
                  <td><?=$v->name?></td>
                  <td><?=$v->tel?></td>
                  <td><?=$v->shop?></td>
                  <td><?=$sex[$v->sex]?></td>
                  <td><?=$v->bz?></td>
                  <td nowrap="">
                  <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lv="<?=$v->lv?>" data-name="<?=$v->nickname?>" data-tel='<?=$v->tel?>' data-bz='<?=$v->bz?>' data-uname='<?=$v->name?>'>
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
$(document).on('change','#type',function(){
  console.log($('#type').val());
  window.location.href = 'http://<?=$_SERVER["HTTP_HOST"]?>/dlda/qrcodes?type='+$('#type').val();
})
$(document).ready(function() {
  <?php if($gnum>0):?>
    // gselect = '<label for="fscore">选择分组：</label>';
    select = '';
    <?php foreach ($group as $key => $v):?>
        select = select+ "<option value=<?=$v->id?>><?=$v->name?></option>";
    <?php endforeach?>
    gselect = '<br><label for="fscore">选择分组：</label><select name=\"form[groupid]\">'+select+'</select>';
  <?php else:?>
    gselect = '';
  <?php endif?>
});
$('#actionModel').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget)
  var id = button.data('id')
  var name = button.data('name')
  var uname = button.data('uname')
  var tel = button.data('tel')
  var bz = button.data('bz')
  var lv = button.data('lv')

  var form = '<div class="form-group"><label for="fscore">姓名：</label><input class="form-control" id="fscore" name="form[name]" max="999" type="text" style="width:150px" value='+uname+'><div class="form-group"><label for="fscore">电话：</label><input class="form-control" id="fscore" name="form[tel]" max="" type="number" style="width:150px" value='+tel+'><div class="form-group"><label for="fscore">备注：</label><input class="form-control" id="fscore" name="form[bz]" type="text" style="width:150px" value='+bz+'>'+gselect
  form += '<div class="form-group"><label for="flock">用户状态：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[lv]" id="flock0" value="1"'+ (lv==1 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">通过</span></label><label class="checkbox-inline"><input type="radio" name="form[lv]" id="flock3" value="4"'+ (lv==4 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">不通过</label></div></div>'
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';

  var modal = $(this)
  modal.find('.modal-title').text(name)
  modal.find('.modal-body').html(form)
})
</script>
