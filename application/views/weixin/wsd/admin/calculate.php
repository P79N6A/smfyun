
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
    个人团队奖励结算账单
    <small></small>
  </h1>

  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
    <li><a href="/wsda/qrcodes">个人团队奖励结算账单</a></li>
    <li class="active"><?=$title?></li>
  </ol>
</section>



<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <a href="<?=$_SERVER['PATH_INFO']?>?export=xls" class="btn btn-success pull-right" style="margin-right:10px;margin-bottom:10px"> <i class="fa fa-file-excel-o"></i> &nbsp; <span>导出个人团队奖励结算账单</span></a>
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
                  <input type="text" name="s" class="form-control input-sm pull-right" placeholder="按昵称，注册手机号，支付宝账号搜索" value="<?=htmlspecialchars($result['s'])?>">

                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default" type="submit"><i class="fa fa-search"></i></button>
                  </div>
                </div>
                </form>
              </div>
            </div><!-- /.box-header -->
            <form name="select" method="post">
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover nowrap">
                <tbody>
                <tr>
                  <!-- <th>ID</th> -->
                  <th><input type="checkbox" id='select_all'>全选</th>
                  <th>头像</th>
                  <th>微信昵称</th>
                  <th>姓名</th>
                  <th>电话</th>
                  <th>支付宝账号</th>
                  <th>当月团队奖励</th>
                  <th>当月团队可结算奖励</th>
                  <th>当月团队待结算奖励</th>
                  <th>当月个人团队奖励</th>
                  <th>当月个人可结算团队奖励</th>
                  <th>当月个人待结算团队奖励</th>
                  <!-- <th>当月销售利润</th>
                  <th>当月可结算销售利润</th>
                  <th>当月待结算销售利润</th> -->
                  <th>已结算个人团队奖励</th>
                  <th>上级代理</th>
                  <th>是否结算</th>
                  <th>操作</th>
                </tr>
                <?php
                foreach ($result['qrcodes'] as $k=>$v):
                   $groups=ORM::factory('wsd_group')->where('bid','=',$v->bid)->where('qid','=',$v->id)->find_all();
                    //echo $qr_num.'<br>';客户数
                    // $month=date('Y-m',time());
                     //echo $month.'<br>';
                    // $daytype='%Y-%m-%d';
                    $nawtime=time();
                    $monthtype='%Y-%m';
                    // $day=date('Y-m-d',time());
                     //echo $day.'<br>';
                    $typearry[0]=2;
                    $typearry[1]=3;
                    $score=ORM::factory('wsd_score')->where('bid','=',$v->bid)->where('qid','=',$v->id)->where('bz','=',$month)->where('type','IN',$typearry)->find();
                    if($score->id){
                      $flag=1;
                    }else{
                      $flag=0;
                    }
                    $month_tmoney=0;
                    $month_pmoney=0;
                    $monthjs_tmoney=0;
                    $monthjs_pmoney=0;
                    foreach ($groups as $group) {
                        if($group->bottom){
                        $bottom1='('.$group->id.','.$group->bottom.')';
                        }else{
                            $bottom1='('.$group->id.')';
                        }
                        // echo 'bottom1'.$bottom1.'<br>';
                        $month_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney1 from wsd_trades where bid=$v->bid and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                        $month_tmoney1=$month_tmoney1[0]['month_tmoney1'];
                        $monthjs_tmoney1=DB::query(Database::SELECT,"SELECT SUM(payment) as monthjs_tmoney1 from wsd_trades where bid=$v->bid and out_time < $nawtime and deletedd = 0 and `gid` in $bottom1 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                        $monthjs_tmoney1=$monthjs_tmoney1[0]['monthjs_tmoney1'];
                        //echo  $month_tmoney.'<br>';
                        $skujs=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money1','<=',$monthjs_tmoney1)->where('money2','>',$monthjs_tmoney1)->find();
                        if(!$skujs->id){
                            $fskujs=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money2','>=',$monthjs_tmoney1)->find();
                            if(!$fskujs->id){
                               $scalejs=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                            }else{
                                $scalejs=0;
                            }
                        }else{
                            $scalejs=$skujs->scale;
                        }
                        $sku=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_tmoney1)->where('money2','>',$month_tmoney1)->find();
                          if(!$sku->id){
                              $fsku=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money2','>=',$month_tmoney1)->find();
                              if(!$fsku->id){
                                 $scale=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                              }else{
                                  $scale=0;
                              }
                          }else{
                              $scale=$sku->scale;
                          }
                          // echo '团队:'.$month_tmoney1.':::::scale::::'.$scale.'<br>';
                        $month_tmoney+=$month_tmoney1*$scale/100;//团队奖励
                        $monthjs_tmoney+=$monthjs_tmoney1*$scalejs/100;//可结算团队奖励
                          // echo  $month_tmoney.'<br>';
                          // echo $group->id."<br>";
                        $child_groups=ORM::factory('wsd_group')->where('bid','=',$v->bid)->where('fgid','=',$group->id)->find_all();
                        $child_moneys=0;
                        $childjs_moneys=0;
                        foreach ($child_groups as $child_group) {
                              if($child_group->bottom){
                                   $bottom2='('.$child_group->id.','.$child_group->bottom.')';
                                }else{
                                      $bottom2='('.$child_group->id.')';
                                }

                              // echo 'bottom2:::'.$bottom2."<br>";
                              $month_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as month_tmoney from wsd_trades where bid=$v->bid and deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                              $monthjs_ltmoney=DB::query(Database::SELECT,"SELECT SUM(payment) as monthjs_tmoney from wsd_trades where bid=$v->bid and out_time < $nawtime and  deletedd = 0 and  `gid` in $bottom2 and FROM_UNIXTIME(`int_time`, '$monthtype')='$month' ")->execute()->as_array();
                              $month_ltmoney=$month_ltmoney[0]['month_tmoney'];
                              $monthjs_ltmoney=$monthjs_ltmoney[0]['monthjs_tmoney'];
                              //echo  'month_ltmoney'.$month_ltmoney.'<br>';
                              $sku=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money1','<=',$month_ltmoney)->where('money2','>',$month_ltmoney)->find();
                               $skujs=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money1','<=',$monthjs_ltmoney)->where('money2','>',$monthjs_ltmoney)->find();
                              if(!$skujs->id){
                                  $fskujs=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money2','>=',$monthjs_ltmoney)->find();
                                  if(!$fskujs->id){
                                     $scalejs=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                                  }else{
                                      $scalejs=0;
                                  }
                              }else{
                                  $scalejs=$skujs->scale;
                              }
                              if(!$sku->id){
                                  $fsku=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->where('money2','>=',$month_ltmoney)->find();
                                  if(!$fsku->id){
                                     $scale=ORM::factory('wsd_sku')->where('bid','=',$v->bid)->order_by('money2','DESC')->find()->scale;
                                  }else{
                                      $scale=0;
                                  }
                              }else{
                                  $scale=$sku->scale;
                              }
                              // echo $month_ltmoney.':::::::'.$scale.'<br>';
                              $child_money= $month_ltmoney*$scale/100;
                              $child_moneys+=$child_money;
                              $childjs_money= $monthjs_ltmoney*$scalejs/100;
                              $childjs_moneys+=$childjs_money;
                        }
                        // echo  '儿子的：'.$month_tmoney.'-'.$child_moneys.'<br>';
                        $month_pmoney+=$month_tmoney-$child_moneys;
                        $monthjs_pmoney+=$monthjs_tmoney-$childjs_moneys;
                        //echo  $month_pmoney.'<br>';当月个人团队奖励
                    }
                    // $month_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as month_pxmoney from wsd_scores where bid=$v->bid and qid = $v->id and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                    // $month_pxmoney=$month_pxmoney[0]['month_pxmoney'];
                    // $monthjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthjs_pxmoney from wsd_scores where bid=$v->bid and qid = $v->id and paydate < $nawtime and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                    // $monthjs_pxmoney=$monthjs_pxmoney[0]['monthjs_pxmoney'];
                    // $monthdjs_pxmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthdjs_pxmoney from wsd_scores where bid=$v->bid and qid = $v->id and paydate >= $nawtime and score > 0 and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                    // $monthdjs_pxmoney=$monthdjs_pxmoney[0]['monthdjs_pxmoney'];
                    // //当月销售利润
                    // //echo  $month_pxmoney.'<br>';
                    // $all_money=$month_pmoney+$month_pxmoney;
                    // $alljs_money=$monthjs_pmoney+$monthjs_pxmoney;
                    //累计销售利润
                    //已结算团队奖励
                    $monthyjs_pmoney=DB::query(Database::SELECT,"SELECT SUM(score) as monthyjs_pmoney from wsd_scores where bid=$v->bid and qid = $v->id and type IN (2,3) and FROM_UNIXTIME(`lastupdate`, '$monthtype')='$month' ")->execute()->as_array();
                    $monthyjs_pmoney=$monthyjs_pmoney[0]['monthyjs_pmoney'];
                    $fname = '';
                    if($v->tid){
                      $fname = '自主购买';
                    }
                    if($v->code){
                      $fname = '来自邀请码：'.$v->code;
                    }
                    $nickname=ORM::factory('wsd_qrcode')->where('bid','=',$v->bid)->where('openid','=',$v->fopenid)->where('lv','=',1)->find()->nickname;
                    if($nickname){
                      $fname = $nickname;
                    }
                ?>
                <tr>
                  <td><input class="select_id" name="select[<?=$k?>]" value="<?=$v->id?>|<?=$_GET['data']['begin']?>|<?=$monthjs_pmoney?>" type="checkbox"></td>
                  <td><img src="<?=$v->headimgurl?>" width="32" height="32" title="<?=$v->openid?>"></td>
                  <td><a href="/wsda/history_scores?qid=<?=$v->id?>"><?=$v->nickname?></td>
                  <td><?=$v->name?></td>
                  <td><?=$v->tel?></td>
                  <td><?=$v->alipay_name?></td>
                  <td><?=number_format($month_tmoney,2)?></td>
                  <td><?=number_format($monthjs_tmoney,2)?></td>
                  <td><?=number_format($month_tmoney-$monthjs_tmoney,2)?></td>
                  <td><?=number_format($month_pmoney,2)?></td>
                  <td><?=number_format($monthjs_pmoney,2)?></td>
                  <td><?=number_format($month_pmoney-$monthjs_pmoney,2)?></td>
                  <!-- <td><?=$month_pxmoney?$month_pxmoney:0?></td>
                  <td><?=$monthjs_pxmoney?$monthjs_pxmoney:0?></td>
                  <td><?=$monthdjs_pxmoney?$monthdjs_pxmoney:0?></td> -->
                  <td><a href="/wsda/history_scores?flag=jl&qid=<?=$v->id?>"><?=number_format(-$monthyjs_pmoney,2)?></td>
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
                  <a href="#" data-toggle="modal" data-target="#actionModel" data-id="<?=$v->id?>" data-lv="<?=$v->lv?>" data-name="<?=$v->nickname?>" data-tel='<?=$v->tel?>' data-uname='<?=$v->name?>' data-money='<?=$monthjs_pmoney?>' data-bz='<?=$v->bz?>'>
                      <span>结算</span> <i class="fa fa-edit"></i>
                    </a>
                  <?php endif;?>
                  </td>
                </tr>

                <?php endforeach;?>
              </tbody></table>
              <button type="submit" class="btn btn-success">点此结算选中代理奖励</button>
            </div><!-- /.box-body -->
            </form>
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
  var bz = button.data('bz')
  var lv = button.data('lv')
  var time = "<?=$_GET['data']['begin']?>";
  var money = button.data('money')?button.data('money'):0;
  var account_type = "<?=$config['money_type']==1?'企业付款':'手动结算'?>"
  form='';
  form += '<div class="form-group"><label for="flock">'+time+'月</label><br>'+'<label for="flock">当月个人可结算团队奖励：'+money+'元</label>';
  form += '<div class="form-group"><label for="flock">结算方式：</label><div class="radio"><label class="checkbox-inline"><input type="radio" name="form[type]" id="flock0" value="1"  ><span class="label label-success" style="font-size:14px">微信企业付款</span></label><label class="checkbox-inline"><input type="radio" name="form[type]" id="flock3" value="2" checked ><span class="label label-danger" style="font-size:14px">手动转账</label></div></div>';
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
$('#select_all').click(function() {
  if($('#select_all').is(':checked')==true){//全选
    $('.select_id').prop("checked",true);
  }else{//非全选
    $('.select_id').removeAttr("checked",false);
  }
});
</script>
