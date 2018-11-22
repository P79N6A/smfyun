
<style>
.label {font-size: 14px}
.search-bar{
  padding-left: 10px;font-size: 14px;color: #666666;padding-bottom: 10px;
}
.mask{
  position: fixed;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  display: -webkit-box;
  -webkit-box-pack: center;
  -webkit-box-align: center;
  z-index: 10000;
  background-color: rgba(0, 0, 0, .7);
  -webkit-animation: fadeIn .6s ease;
  color: rgba(255,255,255,.7);
}
.take{
  position: fixed;
  background-color: #fff;
  width: 500px;
  height: 150px;
  top: 30%;
  left: 50%;
  margin-left: -250px;
  color: #000;
  border-radius: 5px;
}
.take-hd,.take-ft{
  width: 100%;
  text-align: center;
  margin-top: 16px;
}
.take-hd{
  display: inline-block;
  text-align: left;
  margin-left: 40px;
}
.close{
  margin-top: 5px;
  margin-right: 10px;
  position: absolute;
  top: 0;
  right: 0;
}
.confirm-btn{
  background-color: #00a65a;
  width: 100px;
  color: #fff;
  border-radius: 3px;
  border: 1px solid #008d4c;
  padding: 5px
}
.take-tt{
    margin-top: 10px;
    margin-left: 10px;
    font-size: 16px;
    font-weight: bold;
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
    <li><a href="/qfxa/qrcodes">用户明细</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>
<div class="mask mask_group" style="display:none">
  <form method="post" action="/qfxa/export_data_groups">
  <div class="take take_group">
    <div class="take-tt">
      <span>导出所有分组情况</span>
    </div>
    <div class="close">X</div>
    <div class="take-hd">
      <span style="width:70px;line-height:35px;">选择起始时间：</span>
      <div style="display:inline-block">
      <input type="text"  class="form-control formdatetime1" style="width:100px;background-color: #fff;border-radius: 7px;" name="data[begin]" value="<?=$_GET['data']['begin']?>" readonly="">
      </div>
      <span style="width:70px;line-height:35px;">选择终止时间：</span>
      <div style="display:inline-block">
      <input type="text"  class="form-control formdatetime2" style="width:100px;background-color: #fff;border-radius: 7px;" name="data[over]" value="<?=$_GET['data']['over']?>" readonly="">
      </div>
    </div>
    <div class="take-ft">
      <button class="confirm-btn" type="submit">确定</button>
    </div>
  </div>
  </form>
</div>
<div class="mask mask_user" style="display:none">
<form method="post" action="/qfxa/export_data_users">
  <div class="take take_user">
    <div class="take-tt"><span>导出所有用户情况</span></div>
    <div class="close">X</div>
    <div class="take-hd">
      <span style="width:70px;line-height:35px;">选择起始时间：</span>
      <div style="display:inline-block">
      <input type="text"  class="form-control formdatetime1" style="width:100px;background-color: #fff;border-radius: 7px;" name="data[begin]" value="<?=$_GET['data']['begin']?>" readonly="">
      </div>
      <span style="width:70px;line-height:35px;">选择终止时间：</span>
      <div style="display:inline-block">
      <input type="text"  class="form-control formdatetime2" style="width:100px;background-color: #fff;border-radius: 7px;" name="data[over]" value="<?=$_GET['data']['over']?>" readonly="">
      </div>
    </div>
    <div class="take-ft">
      <button class="confirm-btn" type="submit">确定</button>
    </div>
  </div>
</form>
</div>


<!-- Main content -->
<section class="content">

  <div class="row">
    <div class="col-xs-12">
      <a id="take_user" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-list"></i> &nbsp; <span>导出所有用户情况</span></a>
      <a id="take_group" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-list"></i> &nbsp; <span>导出所有分组情况</span></a>
    </div>
  </div>

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
                    <option value="1" <?=$_GET['type']==1?'selected':''?>>正常</option>
                    <option value="3" <?=$_GET['type']==3?'selected':''?>>已取消</option>
                  </select>
                  分组：
                  <select id='group'>
                  <option value='all' <?=!$_GET['group']?'selected':''?>>全部</option>
                  <?php if($gnum>0):?>
                    <?php foreach ($group as $key => $v):?>
                        <option value="<?=$v->id?>" <?=$_GET['group']==$v->id?'selected':''?>><?=$v->name?></option>
                    <?php endforeach?>
                  <?php endif?>
                  </select>

            </div>

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tbody>
                <tr>
                  <!-- <th>ID</th> -->
                  <th>状态：
                  </th>
                  <th>分组：
                  </th>
                  <th>头像</th>
                  <th>昵称</th>
                  <!-- <th>OpenID</th> -->
                  <th nowrap=""><a href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?><?=$_SERVER["QUERY_STRING"]?'&':'?'?>sort=money" title="按总收益排序">总收益</a></th>
                  <th nowrap=""><a href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?><?=$_SERVER["QUERY_STRING"]?'&':'?'?>sort=score" title="按收益排序">未提现收益</a></th>
                  <th nowrap=""><a href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?><?=$_SERVER["QUERY_STRING"]?'&':'?'?>sort=paid" title="按订单收入排序">订单收入</a></th>
                  <th nowrap=""><a href="http://<?=$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?><?=$_SERVER["QUERY_STRING"]?'&':'?'?>sort=fans_num" title="按客户数量排序">客户</a></th>
                  <th>分销商等级</th>
                  <th>姓名</th>
                  <th>电话</th>
                  <th>店铺地址</th>
                  <th>性别</th>
                  <th>备注</th>
                  <th>海报生成</th>
                  <th>操作</th>
                </tr>

                <?php
                foreach ($result['qrcodes'] as $v):
                  $count2 = ORM::factory('qfx_qrcode')->where('bid', '=', $v->bid)->where('fopenid', '=', $v->openid)->where('subscribe','=',1)->count_all();
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
                  $cksum = md5($v->openid.$config['appsecret'].date('Y-m'));
                  $url = '/qfx/index/'. $v->bid .'?url=home&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  $url2 = '/qfx/index/'. $v->bid .'?url=customer&cksum='. $cksum .'&openid='. base64_encode($v->openid);
                  ?>
                  </td>
                  <td><?=$v->groups->name?></td>
                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <!-- <td><a href='/qfxa/qrcodes_detail/<?=$v->id?>'><?=$v->nickname?></a></td> -->
                  <td><?=$v->nickname?></td>
                  <td><?=$v->money?></td>

                  <td id="score<?=$v->id?>"><a href="<?=$url?>" target="_blank" title="查看用户前台"><?=$v->score?></a></td>
                  <td><?=$v->paid?></td>
                  <td><a href="<?=$url2?>" title="查看下线"><?=$count2?></a></td>
                  <td><?=$v->skus->name?$v->skus->name:$config['title1']?></td>
                  <td><?=$v->name?></td>
                  <td><?=$v->tel?></td>
                  <td><?=$v->shop?></td>
                  <td><?=$sex[$v->sex]?></td>
                  <td><?=$v->bz?></td>
                  <td><?=$v->ticket ? '是' : '否'?></td>
                  <td nowrap="">
                  <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lv="<?=$v->lv?>" data-name="<?=$v->nickname?>" data-tel='<?=$v->tel?>' data-uname='<?=$v->name?>' data-bz='<?=$v->bz?>'>
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
  window.location.href = 'http://<?=$_SERVER["HTTP_HOST"]?>/qfxa/qrcodes_m?type='+$('#type').val()+'&group='+$('#group').val();
})
$(document).on('change','#group',function(){
  console.log($('#group').val());
  window.location.href = 'http://<?=$_SERVER["HTTP_HOST"]?>/qfxa/qrcodes_m?group='+$('#group').val()+'&type='+$('#type').val();
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
  var form = '<div class="form-group"><label for="fscore">收益增减（正数为增加、负数为减少）：</label><input class="form-control" id="fscore" name="form[score]" max="999" type="number" style="width:150px"><div class="form-group"><label for="fscore">姓名：</label><input class="form-control" id="fscore" name="form[name]" max="" type="text" style="width:150px" value='+uname+'><div class="form-group"><label for="fscore">电话：</label><input class="form-control" id="fscore" name="form[tel]" max="" type="number" style="width:150px" value='+tel+'><div class="form-group"><label for="fscore">备注：</label><input class="form-control" id="fscore" name="form[bz]" type="text" style="width:150px" value='+bz+'>'+gselect
  form += '<div class="form-group"><label for="flock">用户状态：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[lv]" id="flock0" value="1"'+ (lv==1 ? ' checked' : '') +'><span class="label label-success" style="font-size:14px">正常</span></label><label class="checkbox-inline"><input type="radio" name="form[lv]" id="flock3" value="3"'+ (lv==3 ? ' checked' : '') +'><span class="label label-danger" style="font-size:14px">取消分销商资格</label></div></div>'
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';

  var modal = $(this)
  modal.find('.modal-title').text(name)
  modal.find('.modal-body').html(form)
})

$(document).on('click','#take_user',function(){
    $(".mask_user").fadeIn(500);
});
$(document).on('click','#take_group',function(){
    $(".mask_group").fadeIn(500);
});
$(document).on('click','.close',function(){
    $(".mask_user").fadeOut(500);
    $(".mask_group").fadeOut(500);
});
$(function () {
  $(".formdatetime1").datetimepicker({
    format: "yyyy-mm-dd",
    language: "zh-CN",
    autoclose: true,
    minView:'month',
    todayBtn:true,
    startDate: "<?=$duringtime['begin']?>",
    endDate: "<?=$duringtime['over']?>",

  });

  $(".formdatetime2").datetimepicker({
    format: "yyyy-mm-dd",
    language: "zh-CN",
    autoclose: true,
    minView:'month',
    todayBtn:true,
    startDate: "<?=$duringtime['begin']?>",
    endDate: "<?=$duringtime['over']?>",
  });
})
</script>
