
<style>
.label {font-size: 14px}
th,td{
  white-space: nowrap;
}
</style>

<?php
$sex[0] = '未知';
$sex[1] = '男';
$sex[2] = '女';

$title = '概览';
// if ($result['fuser']) $title = $result['fuser']->nickname.'的下线';
 if ($result['s']) $title = '搜索结果';
// if ($result['ticket']) $title = '已生成海报';
?>

<section class="content-header">
  <h1>
    结算账单
    <small></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/myba/qrcodes">结算账单</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>



<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出全部结算账单</span></a>
    </div>
  </div>


  <div class="row">
    <div class="col-xs-12">
        <div class="box box-success">
            <div class="box-header">
              <h3 class="box-title"><?=$title?>：共 <?=$result['countall']?> 个代理</h3>
              <div class="box-tools">
              <form method="get" name="qrcodesform" style="width:360px;">
                <input type="text"  class="form-control pull-left formdatetime1" style="width:100px;float:left;background-color: #fff;border-radius: 7px;" name="data[begin]" value="<?=$_GET['data']['begin']?>" readonly="">
                <div class="input-group" style="width: 250px;float:right;">
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称搜索" value="<?=htmlspecialchars($result['s'])?>">

                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                </form>
              </div>
            </div><!-- /.box-header -->

            <div class="box-body table-responsive no-padding">
              <table class="table table-hover nowrap">
                <tbody>
                <tr>
                  <th>头像</th>
                  <th>微信昵称</th>
                  <th>姓名</th>
                  <th>电话</th>
                  <th>支付宝账号</th>
                  <th>当月收益</th>
                  <th>当月可结算收益</th>
                  <th>当月待结算收益</th>
                  <th>累计收益</th>
                  <th>累计可结算收益收益</th>
                  <th>上线</th>
                  <th>是否结算</th>
                  <th>操作</th>
                </tr>
                <?php
                foreach ($result['qrcodes'] as $v):
                    $nawtime=time();
                    $monthtype='%Y-%m';
                    $score=ORM::factory('myb_score')->where('bid','=',$v->bid)->where('qid','=',$v->id)->where('bz','=',$month)->find();
                    if($score->id){
                      $flag=1;
                    }else{
                      $flag=0;
                    }
                    $month_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as month_pxmoney from myb_scores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                    $month_pxmoney=$month_pxmoney[0]['month_pxmoney'];
                    $monthjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthjs_pxmoney from myb_scores where bid=$v->bid and qid = $v->id and paydate < $nawtime and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                    $monthjs_pxmoney=$monthjs_pxmoney[0]['monthjs_pxmoney'];
                    $monthdjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthdjs_pxmoney from myb_scores where bid=$v->bid and qid = $v->id and paydate >= $nawtime and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                    $monthdjs_pxmoney=$monthdjs_pxmoney[0]['monthdjs_pxmoney'];
                    $monthlj_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthlj_pxmoney from myb_scores where bid=$v->bid and qid = $v->id and score > 0 ")->execute()->as_array();
                    $monthlj_pxmoney=$monthlj_pxmoney[0]['monthlj_pxmoney'];
                    $monthljjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthljjs_pxmoney from myb_scores where bid=$v->bid and qid = $v->id and paydate < $nawtime and score > 0  ")->execute()->as_array();
                    $monthljjs_pxmoney=$monthljjs_pxmoney[0]['monthljjs_pxmoney'];
                    //累计销售利润
                    $fname=ORM::factory('myb_qrcode')->where('bid','=',$v->bid)->where('openid','=',$v->fopenid)->find()->nickname;
                ?>
                <tr>
                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><a href="/myba/history_scores?qid=<?=$v->id?>"><?=$v->nickname?></td>
                  <td><?=$v->name?></td>
                  <td><?=$v->tel?></td>
                  <td><?=$v->alipay_name?></td>
                  <td><?=$month_pxmoney?$month_pxmoney:0?></td>
                  <td><?=$monthjs_pxmoney?$monthjs_pxmoney:0?></td>
                  <td><?=$monthdjs_pxmoney?$monthdjs_pxmoney:0?></td>
                  <td><?=$monthlj_pxmoney?$monthlj_pxmoney:0?></td>
                  <td><?=$monthljjs_pxmoney?$monthljjs_pxmoney:0?></td>
                  <td><?=$fname?></td>
                  <td id="lock<?=$v->id?>">
                  <?php
                  if ($flag == 1)
                    echo '<span class="label label-success">已结算</span>';
                  if ($flag == 0)
                    echo '<span class="label label-warning">未结算</span>';
                  ?>
                  </td>
                  <td nowrap="">
                  <?php if($flag==0):?>
                  <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-name="<?=$v->nickname?>" data-tel='<?=$v->tel?>' data-uname='<?=$v->name?>' data-money='<?=$monthjs_pxmoney?>' >
                      <span>结算</span> <i class="fa fa-edit"></i>
                    </a>
                  <?php endif;?>
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
        <h4 class="modal-title">确定</h4>
      </div>
      <div class="modal-body">&nbsp;</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">取消</button>
        <button type="submit" class="btn btn-primary">确定</button>
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
  var time = "<?=$_GET['data']['begin']?>";
  var money = button.data('money');
  var account_type = "<?=$config['money_type']==1?'企业付款':'手动结算'?>"
  form='';
  form += '<div class="form-group"><label for="flock">'+time+'月</label><br>'+'<label for="flock">当月可结算收益：'+money+'元</label>';
  form += '<div class="form-group"><label for="flock">结算方式：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[type]" id="flock0" value="1"  ><span class="label label-success" style="font-size:14px">微信企业付款</span></label><label class="checkbox-inline"><input type="radio" name="form[type]" id="flock3" value="2" checked><span class="label label-danger" style="font-size:14px">手动转账</label></div></div>';
  // form+='<div class="form-group"><label for="flock">注意事项:如果此代理商该月账单已手动结算，则该月账单自动结算时会pass掉该代理商</label>';
  form += '<input type="hidden" name="form[id]" value="'+ id +'">';
  form += '<input type="hidden" name="form[time]" value="'+ time +'">'
  form += '<input type="hidden" name="form[money]" value="'+ money +'">'
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
    format: "yyyy-mm",
    language: "zh-CN",
    autoclose: true,
    startView:3,
    minView:3,
    todayBtn:false,
    startDate: "<?=$duringtime['begin']?>",
    <?php $time=strtotime("+1 Months");?>
    endDate: "<?=date("Y-m",$time)?>",
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
